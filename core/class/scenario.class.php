<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../core/php/core.inc.php';

class scenario {
    /*     * *************************Attributs****************************** */

    private $id;
    private $name;
    private $isActive = 1;
    private $group = '';
    private $state = 'stop';
    private $lastLaunch = null;
    private $mode;
    private $schedule;
    private $pid;
    private $scenarioElement;
    private $trigger;
    private $log;
    private $timeout = 0;
    private $object_id = null;
    private $isVisible = 1;
    private static $_templateArray;

    /*     * ***********************Methode static*************************** */

    /**
     * Renvoit un object scenario
     * @param int  $_id id du scenario voulu
     * @return scenario object scenario
     */
    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM scenario 
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * Renvoit tous les objects scenario
     * @return [] scenario object scenario
     */
    public static function all($_group = '') {
        if ($_group == '') {
            $sql = 'SELECT ' . DB::buildField(__CLASS__) . ' 
                    FROM scenario 
                    ORDER BY `group`, `name`';
            return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
        } else {
            $values = array(
                'group' => $_group
            );
            $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                    FROM scenario
                    WHERE `group`=:group
                    ORDER BY `group`, `name`';
            return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
        }
    }

    public static function listGroup() {
        $sql = 'SELECT DISTINCT(`group`)
                FROM scenario
                ORDER BY `group`';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function byTrigger($_cmd_id) {
        $values = array(
            'cmd_id' => '%#' . $_cmd_id . '#%'
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                    FROM scenario
                    WHERE `trigger` LIKE :cmd_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byObjectId($_object_id, $_onlyEnable = true) {
        $values = array();
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM scenario';
        if ($_object_id == null) {
            $sql .= ' WHERE object_id IS NULL';
        } else {
            $values['object_id'] = $_object_id;
            $sql .= ' WHERE object_id=:object_id';
        }
        if ($_onlyEnable) {
            $sql .= ' AND isActive = 1';
        }
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function check($_event_id = null) {
        if ($_event_id != null) {
            $scenarios = self::byTrigger($_event_id);
            $scenario_list = '';
            foreach ($scenarios as $key => &$scenario) {
                if ($scenario->getMode() == 'schedule') {
                    unset($scenarios[$key]);
                } else {
                    $scenario_list .= $scenario->getHumanName() . ' ';
                }
            }
            if ($scenario_list != '') {
                if (is_numeric($_event_id)) {
                    $cmd = cmd::byId($_event_id);
                    if (is_object($cmd)) {
                        log::add('scenario', 'info', 'Evènement venant de ' . $cmd->getHumanName() . ' (' . $cmd->getId() . ') vérification du/des scénario(s) : ' . $scenario_list);
                    } else {
                        return;
                    }
                } else {
                    log::add('scenario', 'info', 'Evènement : #' . $_event_id . '# vérification du/des scénario(s) : ' . $scenario_list);
                }
            }
        } else {
            $scenarios = scenario::all();
            foreach ($scenarios as $key => &$scenario) {
                if ($scenario->getState() == 'in progress' && !$scenario->running()) {
                    $scenario->setState('error');
                    $scenario->save();
                }
                if ($scenario->getIsActive() == 1 && $scenario->getState() != 'in progress' && ($scenario->getMode() == 'schedule' || $scenario->getMode() == 'all')) {
                    if (!$scenario->isDue()) {
                        unset($scenarios[$key]);
                    }
                } else {
                    unset($scenarios[$key]);
                }
            }
        }
        if (count($scenarios) == 0) {
            return true;
        }

        foreach ($scenarios as $scenario_) {
            try {
                $scenario_->launch();
            } catch (Exception $e) {
                log::add('scenario', 'error', $e->getMessage());
            }
        }
        return true;
    }

    /*     * *********************Methode d'instance************************* */

    public function launch($_force = false) {
        if (config::byKey('enableScenario') == 1) {
            $cmd = 'nohup php ' . dirname(__FILE__) . '/../../core/php/jeeScenario.php ';
            $cmd.= ' scenario_id=' . $this->getId();
            $cmd.= ' force=' . $_force;
            $cmd.= ' >> ' . log::getPathToLog('scenario') . ' 2>&1 &';
            shell_exec($cmd);
            return true;
        }
        return false;
    }

    public function execute() {
        $this->clearLog();
        $initialState = $this->getState();
        $this->setLog('Début exécution du scénario : ' . $this->getHumanName());
        $this->setState('in progress');
        $this->setLastLaunch(date('Y-m-d H:i:s'));
        $this->save();
        foreach ($this->getElement() as $element) {
            $element->execute($this, $initialState);
        }
        $this->save();
        return true;
    }

    public function copy($_name) {
        $scenarioCopy = clone $this;
        $scenarioCopy->setName($_name);
        $scenarioCopy->setId('');
        $scenario_element_list = array();
        foreach ($this->getElement() as $element) {
            $scenario_element_list[] = $element->copy();
        }
        $scenarioCopy->setScenarioElement($scenario_element_list);
        $scenarioCopy->clearLog();
        $scenarioCopy->save();
        return $scenarioCopy;
    }

    public function clearLog() {
        $this->setLog('');
        foreach ($this->getElement() as $element) {
            $element->clearLog();
        }
    }

    public function toHtml($_version) {
        $replace = array(
            '#id#' => $this->getId(),
            '#state#' => $this->getState(),
            '#isActive#' => $this->getIsActive(),
            '#name#' => $this->getHumanName(),
            '#lastLaunch#' => $this->getLastLaunch(),
            '#scenarioLink#' => $this->getLinkToConfiguration(),
        );
        if (!isset(self::$_templateArray)) {
            self::$_templateArray = array();
        }
        if (!isset(self::$_templateArray[$_version])) {
            self::$_templateArray[$_version] = getTemplate('core', $_version, 'scenario');
        }
        $html = template_replace($replace, self::$_templateArray[$_version]);
        return $html;
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=scenario&id=' . $this->getId();
    }

    public function preSave() {
        if ($this->getTimeout() == '' || !is_numeric($this->getTimeout())) {
            $this->setTimeout(60);
        }
        if ($this->getName() == '') {
            throw new Exception('Le nom du scénario ne peut être vide');
        }
    }

    public function save() {
        if (($this->getMode() == 'schedule' || $this->getMode() == 'all') && $this->getSchedule() == '') {
            throw new Exception('Le scénario est de type programmé mais la programmation est vide');
        }
        if ($this->getLastLaunch() == '' && ($this->getMode() == 'schedule' || $this->getMode() == 'all')) {
            $calculateScheduleDate = $this->calculateScheduleDate();
            $this->setLastLaunch($calculateScheduleDate['prevDate']);
        }
        DB::save($this);
        @nodejs::pushUpdate('eventScenario', $this->getId());
    }

    public function refresh() {
        DB::refresh($this);
    }

    public function remove() {
        viewData::removeByTypeLinkId('scenario', $this->getId());
        dataStore::removeByTypeLinkId('scenario', $this->getId());
        foreach ($this->getElement() as $element) {
            $element->remove();
        }
        DB::remove($this);
    }

    public function removeData($_key, $_private = true) {
        if ($_private) {
            $dataStore = dataStore::byTypeLinkIdKey('scenario', $this->getId(), $_key);
        } else {
            $dataStore = dataStore::byTypeLinkIdKey('scenario', -1, $_key);
        }
        if (is_object($dataStore)) {
            return $dataStore->remove();
        }
        return true;
    }

    public function setData($_key, $_value) {
        $dataStore = new dataStore();
        $dataStore->setType('scenario');
        $dataStore->setKey($_key);
        $dataStore->setValue($_value);
        $dataStore->setLink_id(-1);
        $dataStore->save();
        return true;
    }

    public function getData($_key, $_private = true) {
        if ($_private) {
            $dataStore = dataStore::byTypeLinkIdKey('scenario', $this->getId(), $_key);
        } else {
            $dataStore = dataStore::byTypeLinkIdKey('scenario', -1, $_key);
        }
        if (is_object($dataStore)) {
            return $dataStore->getValue();
        }
        return '';
    }

    public function calculateScheduleDate() {
        $calculatedDate = array('prevDate' => '', 'nextDate' => '');
        if (is_array($this->getSchedule())) {
            $calculatedDate_tmp = array('prevDate' => '', 'nextDate' => '');
            foreach ($this->getSchedule() as $schedule) {
                try {
                    $c = new Cron\CronExpression($schedule, new Cron\FieldFactory);
                    $calculatedDate_tmp['prevDate'] = $c->getPreviousRunDate();
                    $calculatedDate_tmp['nextDate'] = $c->getNextRunDate();
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }
                if ($calculatedDate['prevDate'] == '' || $calculatedDate['prevDate'] < $calculatedDate_tmp['prevDate']) {
                    $calculatedDate['prevDate'] = $calculatedDate_tmp['prevDate'];
                }
                if ($calculatedDate['nextDate'] == '' || $calculatedDate['nextDate'] > $calculatedDate_tmp['nextDate']) {
                    $calculatedDate['nextDate'] = $calculatedDate_tmp['nextDate'];
                }
            }
        }
        try {
            $c = new Cron\CronExpression($this->getSchedule(), new Cron\FieldFactory);
            $calculatedDate['prevDate'] = $c->getPreviousRunDate();
            $calculatedDate['nextDate'] = $c->getNextRunDate();
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
        }
        return $calculatedDate;
    }

    public function isDue() {
        if ($this->getLastLaunch() == '' || $this->getLastLaunch() == '0000-00-00 00:00:00') {
            return true;
        }
        $last = strtotime($this->getLastLaunch());
        $now = time();
        $now = ($now - $now % 60);
        $last = ($last - $last % 60);
        if ($now == $last) {
            return false;
        }

        if (is_array($this->getSchedule())) {
            foreach ($this->getSchedule() as $schedule) {
                try {
                    $c = new Cron\CronExpression($schedule, new Cron\FieldFactory);
                    if ($c->isDue()) {
                        return true;
                    }
                    $lastCheck = new DateTime($this->getLastLaunch());
                    $prev = $c->getPreviousRunDate();
                    if ($lastCheck < $prev) {
                        if ($lastCheck->diff($c->getPreviousRunDate())->format('%i') > 5) {
                            log::add('scenario', 'error', 'Retard lancement prévu à ' . $prev->format('Y-m-d H:i:s') . ' dernier lancement à ' . $lastCheck->format('Y-m-d H:i:s') . '. Retard de : ' . ( $lastCheck->diff($c->getPreviousRunDate())->format('%i min')) . ': ' . $this->getName() . '. Rattrapage en cours...');
                        }
                        return true;
                    }
                } catch (Exception $exc) {
                    log::add('scenario', 'error', 'Expression cron non valide : ' . $schedule);
                    return false;
                }
            }
        } else {
            try {
                $c = new Cron\CronExpression($this->getSchedule(), new Cron\FieldFactory);
                if ($c->isDue()) {
                    return true;
                }
                $lastCheck = new DateTime($this->getLastLaunch());
                $prev = $c->getPreviousRunDate();
                if ($lastCheck < $prev) {
                    if ($lastCheck->diff($c->getPreviousRunDate())->format('%i') > 5) {
                        log::add('scenario', 'error', 'Retard lancement prévu à ' . $prev->format('Y-m-d H:i:s') . ' dernier lancement à ' . $lastCheck->format('Y-m-d H:i:s') . '. Retard de : ' . ( $lastCheck->diff($c->getPreviousRunDate())->format('%i min')) . ': ' . $this->getName() . '. Rattrapage en cours...');
                    }
                    return true;
                }
            } catch (Exception $exc) {
                log::add('scenario', 'error', 'Expression cron non valide : ' . $this->getSchedule());
                return false;
            }
        }


        return false;
    }

    public function running() {
        if ($this->getPID() > 0) {
            exec('ps ' . $this->pid, $pState);
            return (count($pState) >= 2);
        }
        return false;
    }

    public function stop() {
        if ($this->running()) {
            exec('kill ' . $this->getPID());
            $retry = 0;
            while ($this->running() && $retry < 10) {
                sleep(1);
                exec('kill -9 ' . $this->getPID());
                $retry++;
            }
            if ($this->running()) {
                throw new Exception('Impossible d\'arreter le scénario : ' . $this->getHumanName() . '. PID : ' . $this->getPID());
            }
        }
        $this->setState('stop');
        $this->save();
        return true;
    }

    public function getElement() {
        $return = array();
        $elements = $this->getScenarioElement();
        if (is_array($elements)) {
            foreach ($this->getScenarioElement() as $element_id) {
                $element = scenarioElement::byId($element_id);
                if (is_object($element)) {
                    $return[] = $element;
                }
            }
            return $return;
        }
        if ($elements != '') {
            $element = scenarioElement::byId($element_id);
            if (is_object($element)) {
                $return[] = $element;
                return $return;
            }
        }
        return array();
    }

    public function getConsolidateLog() {
        $return = $this->getLog() . "\n";
        foreach ($this->getElement() as $element) {
            $log = $element->getConsolidateLog();
            if (trim($log) != '') {
                $return .= $log . "\n";
            }
        }
        return $return;
    }

    public function getObject() {
        return object::byId($this->object_id);
    }

    public function getHumanName() {
        $return = '';
        if ($this->getGroup() != '') {
            $return .= '[' . $this->getGroup() . ']';
        }
        if (is_numeric($this->getObject_id())) {
            $return .= '[' . $this->getObject()->getName() . ']';
        }
        $return .= '[' . $this->getName() . ']';
        return $return;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getState() {
        return $this->state;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getLastLaunch() {
        return $this->lastLaunch;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    public function setGroup($group) {
        $this->group = $group;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setLastLaunch($lastLaunch) {
        $this->lastLaunch = $lastLaunch;
    }

    public function getType() {
        return $this->type;
    }

    public function getMode() {
        return $this->mode;
    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function getSchedule() {
        if (is_json($this->schedule)) {
            return json_decode($this->schedule, true);
        }
        return $this->schedule;
    }

    public function setSchedule($schedule) {
        if (is_array($schedule)) {
            $schedule = json_encode($schedule);
        }
        $this->schedule = $schedule;
    }

    public function getPID() {
        return $this->pid;
    }

    public function setPID($pid) {
        $this->pid = $pid;
    }

    public function getScenarioElement() {
        if (is_json($this->scenarioElement)) {
            return json_decode($this->scenarioElement, true);
        }
        return $this->scenarioElement;
    }

    public function setScenarioElement($scenarioElement) {
        if (is_array($scenarioElement)) {
            $scenarioElement = json_encode($scenarioElement);
        }
        $this->scenarioElement = $scenarioElement;
    }

    public function getTrigger() {
        if (is_json($this->trigger)) {
            return json_decode($this->trigger, true);
        }
        return $this->trigger;
    }

    public function setTrigger($trigger) {
        if (is_array($trigger)) {
            $trigger = json_encode($trigger);
        }
        $this->trigger = cmd::humanReadableToCmd($trigger);
    }

    public function getLog() {
        return $this->log;
    }

    public function setLog($log) {
        if ($log == '') {
            $this->log = '';
        } else {
            $this->log = '[' . date('Y-m-d H:i:s') . '][SCENARIO] ' . $log;
        }
        $this->save();
    }

    public function getTimeout($_default = '') {
        if ($this->timeout == '' || !is_numeric($this->timeout)) {
            return $_default;
        }
        return $this->timeout;
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    public function getObject_id() {
        return $this->object_id;
    }

    public function getIsVisible() {
        return $this->isVisible;
    }

    public function setObject_id($object_id = null) {
        $this->object_id = (!is_numeric($object_id)) ? null : $object_id;
    }

    public function setIsVisible($isVisible) {
        $this->isVisible = $isVisible;
    }

}

?>