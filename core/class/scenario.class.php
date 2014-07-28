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
    private $hlogs;
    private $display;
    private $description;
    private $_internalEvent = 0;
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

    public static function byElement($_element_id) {
        $values = array(
            'element_id' => '%' . $_element_id . '%'
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                    FROM scenario
                    WHERE `scenarioElement` LIKE :element_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byObjectId($_object_id, $_onlyEnable = true, $_onlyVisible = false) {
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
        if ($_onlyVisible) {
            $sql .= ' AND isVisible = 1';
        }
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function check($_event_id = null) {
        $message = '';
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
                    $message = __('Scenario lance automatiquement sur evenement venant de : ', __FILE__) . $cmd->getHumanName();
                    if (is_object($cmd)) {
                        log::add('scenario', 'info', __('Evènement venant de ', __FILE__) . $cmd->getHumanName() . ' (' . $cmd->getId() . __(') vérification du/des scénario(s) : ', __FILE__) . $scenario_list);
                    } else {
                        return;
                    }
                } else {
                    $message = __('Scenario lance sur evenement : #', __FILE__) . $_event_id . '#';
                    log::add('scenario', 'info', __('Evènement : #', __FILE__) . $_event_id . __('# vérification du/des scénario(s) : ', __FILE__) . $scenario_list);
                }
            }
        } else {
            $message = __('Scenario lance automatiquement sur programmation', __FILE__);
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
                $scenario_->launch(false, $message);
            } catch (Exception $e) {
                log::add('scenario', 'error', $e->getMessage());
            }
        }
        return true;
    }

    public static function cleanTable() {
        $element_ids = array();
        $subelement_ids = array();
        $expression_ids = array();
        $ids = array(
            'element' => array(),
            'subelement' => array(),
            'expression' => array(),
        );
        foreach (scenario::all() as $scenario) {
            foreach ($scenario->getElement() as $element) {
                $result = $element->getAllId();
                $ids['element'] = array_merge($ids['element'], $result['element']);
                $ids['subelement'] = array_merge($ids['subelement'], $result['subelement']);
                $ids['expression'] = array_merge($ids['expression'], $result['expression']);
            }
        }

        $sql = 'DELETE FROM scenarioExpression WHERE id NOT IN (-1';
        foreach ($ids['expression'] as $expression_id) {
            $sql .= ',' . $expression_id;
        }
        $sql .= ')';
        DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);

        $sql = 'DELETE FROM scenarioSubElement WHERE id NOT IN (-1';
        foreach ($ids['subelement'] as $subelement_id) {
            $sql .= ',' . $subelement_id;
        }
        $sql .= ')';
        DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);

        $sql = 'DELETE FROM scenarioElement WHERE id NOT IN (-1';
        foreach ($ids['element'] as $element_id) {
            $sql .= ',' . $element_id;
        }
        $sql .= ')';
        DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function byObjectNameGroupNameScenarioName($_object_name, $_group_name, $_scenario_name) {
        $values = array(
            'scenario_name' => html_entity_decode($_scenario_name),
        );

        if ($_object_name == __('Aucun', __FILE__)) {
            if ($_group_name == __('Aucun', __FILE__)) {
                $sql = 'SELECT ' . DB::buildField(__CLASS__, 's') . '
                        FROM scenario s
                        WHERE s.name=:scenario_name
                            AND `group` IS NULL
                            AND s.object_id IS NULL';
            } else {
                $values['group_name'] = $_group_name;
                $sql = 'SELECT ' . DB::buildField(__CLASS__, 's') . '
                        FROM scenario s
                        WHERE s.name=:scenario_name
                            AND s.object_id IS NULL
                            AND `group`=:group_name';
            }
        } else {
            $values['object_name'] = $_object_name;
            if ($_group_name == __('Aucun', __FILE__)) {
                $sql = 'SELECT ' . DB::buildField(__CLASS__, 's') . '
                        FROM scenario s
                        INNER JOIN object ob ON s.object_id=ob.id
                        WHERE s.name=:scenario_name
                            AND ob.name=:object_name
                            AND `group` IS NULL';
            } else {
                $values['group_name'] = $_group_name;
                $sql = 'SELECT ' . DB::buildField(__CLASS__, 's') . '
                        FROM scenario s
                        INNER JOIN object ob ON s.object_id=ob.id
                        WHERE s.name=:scenario_name
                            AND ob.name=:object_name
                            AND `group`=:group_name';
            }
        }
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function toHumanReadable($_input) {
        if (is_object($_input)) {
            $reflections = array();
            $uuid = spl_object_hash($_input);
            if (!isset($reflections[$uuid])) {
                $reflections[$uuid] = new ReflectionClass($_input);
            }
            $reflection = $reflections[$uuid];
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($_input);
                $property->setValue($_input, self::toHumanReadable($value));
                $property->setAccessible(false);
            }
            return $_input;
        }
        if (is_array($_input)) {
            foreach ($_input as $key => $value) {
                $_input[$key] = self::toHumanReadable($value);
            }
            return $_input;
        }
        $text = $_input;
        preg_match_all("/#scenario([0-9]*)#/", $text, $matches);
        foreach ($matches[1] as $scenario_id) {
            if (is_numeric($scenario_id)) {
                $scenario = self::byId($scenario_id);
                if (is_object($scenario)) {
                    $text = str_replace('#scenario' . $scenario_id . '#', '#' . $scenario->getHumanName(true) . '#', $text);
                }
            }
        }
        return $text;
    }

    public static function fromHumanReadable($_input) {
        $isJson = false;
        if (is_json($_input)) {
            $isJson = true;
            $_input = json_decode($_input, true);
        }
        if (is_object($_input)) {
            $reflections = array();
            $uuid = spl_object_hash($_input);
            if (!isset($reflections[$uuid])) {
                $reflections[$uuid] = new ReflectionClass($_input);
            }
            $reflection = $reflections[$uuid];
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($_input);
                $property->setValue($_input, self::fromHumanReadable($value));
                $property->setAccessible(false);
            }
            return $_input;
        }
        if (is_array($_input)) {
            foreach ($_input as $key => $value) {
                $_input[$key] = self::fromHumanReadable($value);
            }
            if ($isJson) {
                return json_encode($_input, JSON_UNESCAPED_UNICODE);
            }
            return $_input;
        }
        $text = $_input;

        preg_match_all("/#\[(.*?)\]\[(.*?)\]\[(.*?)\]#/", $text, $matches);

        if (count($matches) == 4) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                if (isset($matches[1][$i]) && isset($matches[2][$i]) && isset($matches[3][$i])) {
                    $scenario = self::byObjectNameGroupNameScenarioName($matches[1][$i], $matches[2][$i], $matches[3][$i]);
                    if (is_object($scenario)) {
                        $text = str_replace($matches[0][$i], '#scenario' . $scenario->getId() . '#', $text);
                    }
                }
            }
        }

        return $text;
    }

    public static function byUsedCommand($_cmd_id) {
        $return = self::byTrigger($_cmd_id);
        $expressions = scenarioExpression::searchExpression('#' . $_cmd_id . '#');

        foreach ($expressions as $expression) {
            $scenarios[] = $expression->getSubElement()->getElement()->getScenario();
        }

        foreach ($scenarios as $scenario) {
            if (is_object($scenario)) {
                $find = false;
                foreach ($return as $existScenario) {
                    if ($scenario->getId() == $existScenario->getId()) {
                        $find = true;
                        break;
                    }
                }
                if (!$find) {
                    $return[] = $scenario;
                }
            }
        }
        return $return;
    }

    /*     * *********************Methode d'instance************************* */

    public function launch($_force = false, $_message = '') {
        if (config::byKey('enableScenario') == 1) {
            $cmd = 'nohup php ' . dirname(__FILE__) . '/../../core/php/jeeScenario.php ';
            $cmd.= ' scenario_id=' . $this->getId();
            $cmd.= ' force=' . $_force;
            $cmd.= ' message=' . escapeshellarg($_message);
            $cmd.= ' >> ' . log::getPathToLog('scenario') . ' 2>&1 &';
            shell_exec($cmd);
            return true;
        }
        return false;
    }

    public function execute($_message = '') {
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('launch::scenario');
        $internalEvent->setOptions('id', $this->getId());
        $internalEvent->save();
        $this->clearLog();
        $this->setDisplay('icon', '');
        $initialState = $this->getState();
        $this->setLog(__('Début exécution du scénario : ', __FILE__) . $this->getHumanName() . '. ' . $_message);
        $this->setState('in progress');
        $this->setLastLaunch(date('Y-m-d H:i:s'));
        $this->save();
        foreach ($this->getElement() as $element) {
            $element->execute($this, $initialState);
        }
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('stop::scenario');
        $internalEvent->setOptions('id', $this->getId());
        $internalEvent->save();
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
        $logs = $this->getHlogs();
        if (is_array($logs)) {
            if (count($logs) > 5) {
                array_pop($logs);
            }
            array_unshift($logs, $this->getConsolidateLog());
            $this->setHlogs($logs);
        } else {
            $this->setHlogs(array($this->getConsolidateLog()));
        }
        $this->setLog('');
        foreach ($this->getElement() as $element) {
            $element->clearLog();
        }
    }

    public function toHtml($_version) {
        $_version = jeedom::versionAlias($_version);
        $replace = array(
            '#id#' => $this->getId(),
            '#state#' => $this->getState(),
            '#isActive#' => $this->getIsActive(),
            '#name#' => ($this->getDisplay('name') != '') ? $this->getDisplay('name') : $this->getHumanName(),
            '#icon#' => $this->getIcon(),
            '#lastLaunch#' => $this->getLastLaunch(),
            '#scenarioLink#' => $this->getLinkToConfiguration(),
        );
        if (!isset(self::$_templateArray)) {
            self::$_templateArray = array();
        }
        if (!isset(self::$_templateArray[$_version])) {
            self::$_templateArray[$_version] = getTemplate('core', $_version, 'scenario');
        }
        return template_replace($replace, self::$_templateArray[$_version]);
    }

    public function getIcon() {
        if ($this->getIsActive() == 1) {
            switch ($this->getState()) {
                case 'in progress':
                    return '<i class="fa fa-spinner fa-spin"></i>';
                case 'error':
                    return '<i class="fa fa-exclamation-triangle"></i>';
                default:
                    if ($this->getDisplay('icon') != '') {
                        return $this->getDisplay('icon');
                    }
                    return '<i class="fa fa-check"></i>';
            }
        } else {
            return '<i class="fa fa-times"></i>';
        }
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=scenario&id=' . $this->getId();
    }

    public function preSave() {
        if ($this->getTimeout() == '' || !is_numeric($this->getTimeout())) {
            $this->setTimeout(0);
        }
        if ($this->getName() == '') {
            throw new Exception('Le nom du scénario ne peut être vide');
        }
    }

    public function save() {
        if (($this->getMode() == 'schedule' || $this->getMode() == 'all') && $this->getSchedule() == '') {
            throw new Exception(__('Le scénario est de type programmé mais la programmation est vide', __FILE__));
        }
        if ($this->getLastLaunch() == '' && ($this->getMode() == 'schedule' || $this->getMode() == 'all')) {
            $calculateScheduleDate = $this->calculateScheduleDate();
            $this->setLastLaunch($calculateScheduleDate['prevDate']);
        }
        if ($this->getInternalEvent() == 1) {
            $internalEvent = new internalEvent();
            if ($this->getId() == '') {
                $internalEvent->setEvent('create::scenario');
            } else {
                $internalEvent->setEvent('update::scenario');
            }
        }
        DB::save($this);
        if (isset($internalEvent)) {
            $internalEvent->setOptions('id', $this->getId());
            $internalEvent->save();
        }
        @nodejs::pushUpdate('eventScenario', $this->getId());
    }

    public function refresh() {
        DB::refresh($this);
    }

    public function remove() {
        viewData::removeByTypeLinkId('scenario', $this->getId());
        dataStore::removeByTypeLinkId('scenario', $this->getId());
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('remove::scenario');
        $internalEvent->setOptions('id', $this->getId());
        foreach ($this->getElement() as $element) {
            $element->remove();
        }
        DB::remove($this);
        $internalEvent->save();
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
        } else {
            try {
                $c = new Cron\CronExpression($this->getSchedule(), new Cron\FieldFactory);
                $calculatedDate['prevDate'] = $c->getPreviousRunDate();
                $calculatedDate['nextDate'] = $c->getNextRunDate();
            } catch (Exception $exc) {
                //echo $exc->getTraceAsString();
            }
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
                    $diff = round(abs((strtotime('now') - strtotime($prev)) / 60));
                    if ($lastCheck <= $prev && $diff <= config::byKey('maxCatchAllow') || config::byKey('maxCatchAllow') == -1) {
                        if ($diff > 3) {
                            log::add('scenario', 'error', __('Retard lancement prévu à ', __FILE__) . $prev->format('Y-m-d H:i:s') . __(' dernier lancement à ', __FILE__) . $lastCheck->format('Y-m-d H:i:s') . __('. Retard de : ', __FILE__) . $diff . ' min : ' . $this->getName() . __('. Rattrapage en cours...', __FILE__));
                        }
                        return true;
                    }
                } catch (Exception $exc) {
                    log::add('scenario', 'error', __('Expression cron non valide : ', __FILE__) . $schedule);
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
                $diff = round(abs((strtotime('now') - $prev->getTimestamp()) / 60));
                if ($lastCheck < $prev && $diff <= config::byKey('maxCatchAllow') || config::byKey('maxCatchAllow') == -1) {
                    if ($diff > 3) {
                        log::add('scenario', 'error', __('Retard lancement prévu à ', __FILE__) . $prev->format('Y-m-d H:i:s') . __(' dernier lancement à ', __FILE__) . $lastCheck->format('Y-m-d H:i:s') . __('. Retard de : ', __FILE__) . $diff . ' min: ' . $this->getName() . __('. Rattrapage en cours...', __FILE__));
                    }
                    return true;
                }
            } catch (Exception $exc) {
                log::add('scenario', 'error', __('Expression cron non valide : ', __FILE__) . $this->getSchedule());
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
                throw new Exception(__('Impossible d\'arreter le scénario : ', __FILE__) . $this->getHumanName() . __('. PID : ', __FILE__) . $this->getPID());
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

    public function getHumanName($_complete = false) {
        $return = '';
        if (is_numeric($this->getObject_id())) {
            $return .= '[' . $this->getObject()->getName() . ']';
        } else {
            if ($_complete) {
                $return .= '[' . __('Aucun', __FILE__) . ']';
            }
        }
        if ($this->getGroup() != '') {
            $return .= '[' . $this->getGroup() . ']';
        } else {
            if ($_complete) {
                $return .= '[' . __('Aucun', __FILE__) . ']';
            }
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
        if ($name != $this->getName()) {
            $this->setInternalEvent(1);
        }
        $this->name = $name;
    }

    public function setIsActive($isActive) {
        if ($isActive != $this->getIsActive()) {
            $this->setInternalEvent(1);
        }
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
            $schedule = json_encode($schedule, JSON_UNESCAPED_UNICODE);
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
            $scenarioElement = json_encode($scenarioElement, JSON_UNESCAPED_UNICODE);
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
            $trigger = json_encode($trigger, JSON_UNESCAPED_UNICODE);
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

    public function getHlogs() {
        if (is_json($this->hlogs)) {
            return json_decode($this->hlogs, true);
        }
        return $this->hlogs;
    }

    public function setHlogs($hlogs) {
        if (is_array($hlogs)) {
            $this->hlogs = json_encode($hlogs);
        } else {
            $this->hlogs = $hlogs;
        }
    }

    public function getInternalEvent() {
        return $this->_internalEvent;
    }

    public function setInternalEvent($_internalEvent) {
        $this->_internalEvent = $_internalEvent;
    }

    public function getDisplay($_key = '', $_default = '') {
        return utils::getJsonAttr($this->display, $_key, $_default);
    }

    public function setDisplay($_key, $_value) {
        $this->display = utils::setJsonAttr($this->display, $_key, $_value);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

}

?>