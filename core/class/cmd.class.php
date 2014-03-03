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

class cmd {
    /*     * *************************Attributs****************************** */

    protected $id;
    protected $name;
    protected $order;
    protected $type;
    protected $subType;
    protected $eqLogic_id;
    protected $isHistorized = 0;
    protected $unite = '';
    protected $cache;
    protected $eventOnly = 0;
    protected $configuration;
    protected $template;
    protected $display;
    protected $collect = 0;
    protected $_collectDate = '';
    protected $value = null;
    protected $isVisible = 1;
    protected $_internalEvent = 0;

    /*     * ***********************Methode static*************************** */

    private static function getClass($_id) {
        if (get_called_class() != __CLASS__) {
            return get_called_class();
        }
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT el.eqType_name, el.isEnable
                FROM cmd c
                    INNER JOIN eqLogic el ON c.eqLogic_id=el.id
                WHERE c.id=:id';
        $result = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        $eqTyme_name = $result['eqType_name'];
        if ($result['isEnable'] == 0) {
            try {
                if ($eqTyme_name != '') {
                    $plugin = new plugin($eqTyme_name);
                }
                if (!is_object($plugin) || $plugin->isActive() == 0) {
                    return __CLASS__;
                }
            } catch (Exception $e) {
                return __CLASS__;
            }
        }
        if (class_exists($eqTyme_name)) {
            if (method_exists($eqTyme_name, 'getClassCmd')) {
                return $eqTyme_name::getClassCmd();
            }
        }
        if (class_exists($eqTyme_name . 'Cmd')) {
            return $eqTyme_name . 'Cmd';
        }
        return __CLASS__;
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM cmd
                WHERE id=:id';

        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, self::getClass($_id));
    }

    public static function all() {
        $sql = 'SELECT id
                FROM cmd
                ORDER BY id';
        $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function allHistoryCmd($_notEventOnly = false) {
        $sql = 'SELECT id
                FROM cmd
                WHERE isHistorized=1
                    AND type=\'info\'';
        if ($_notEventOnly) {
            $sql .= ' AND eventOnly=0';
        }
        $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byEqLogicId($_eqLogic_id) {
        $values = array(
            'eqLogic_id' => $_eqLogic_id
        );
        $sql = 'SELECT id
                FROM cmd
                WHERE eqLogic_id=:eqLogic_id
                ORDER BY `order`';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byValue($_value) {
        $values = array(
            'value' => $_value
        );
        $sql = 'SELECT id
                FROM cmd
                WHERE value=:value
                ORDER BY `order`';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byTypeEqLogicNameCmdName($_eqType_name, $_eqLogic_name, $_cmd_name) {
        $values = array(
            'eqType_name' => $_eqType_name,
            'eqLogic_name' => $_eqLogic_name,
            'cmd_name' => $_cmd_name,
        );
        $sql = 'SELECT c.id
                FROM cmd c
                    INNER JOIN eqLogic el ON c.eqLogic_id=el.id
                WHERE c.name=:cmd_name
                    AND el.name=:eqLogic_name
                    AND el.eqType_name=:eqType_name';
        $id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($id['id']);
    }

    public static function byEqLogicIdCmdName($_eqLogic_id, $_cmd_name) {
        $values = array(
            'eqLogic_id' => $_eqLogic_id,
            'cmd_name' => $_cmd_name,
        );
        $sql = 'SELECT c.id
                FROM cmd c
                WHERE c.name=:cmd_name
                    AND c.eqLogic_id=:eqLogic_id';
        $id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($id['id']);
    }

    public static function byObjectNameEqLogicNameCmdName($_object_name, $_eqLogic_name, $_cmd_name) {
        $values = array(
            'eqLogic_name' => $_eqLogic_name,
            'cmd_name' => html_entity_decode($_cmd_name),
        );

        if ($_object_name == 'Aucun') {
            $sql = 'SELECT c.id
                    FROM cmd c
                        INNER JOIN eqLogic el ON c.eqLogic_id=el.id
                    WHERE c.name=:cmd_name
                        AND el.name=:eqLogic_name
                        AND el.object_id IS NULL';
        } else {
            $values['object_name'] = $_object_name;
            $sql = 'SELECT c.id
                    FROM cmd c
                        INNER JOIN eqLogic el ON c.eqLogic_id=el.id
                        INNER JOIN object ob ON el.object_id=ob.id
                    WHERE c.name=:cmd_name
                        AND el.name=:eqLogic_name
                        AND ob.name=:object_name';
        }
        $id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($id['id']);
    }

    public static function byObjectNameCmdName($_object_name, $_cmd_name) {
        $values = array(
            'object_name' => $_object_name,
            'cmd_name' => $_cmd_name,
        );
        $sql = 'SELECT c.id
                FROM cmd c
                    INNER JOIN eqLogic el ON c.eqLogic_id=el.id
                    INNER JOIN object ob ON el.object_id=ob.id
                WHERE c.name=:cmd_name
                    AND ob.name=:object_name';
        $id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($id['id']);
    }

    public static function byTypeSubType($_type, $_subType = '') {
        $values = array(
            'type' => $_type,
        );
        $sql = 'SELECT c.id
                FROM cmd c
                WHERE c.type=:type';
        if ($_subType != '') {
            $values['subtype'] = $_subType;
            $sql .= ' AND c.subtype=:subtype';
        }
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function collect() {
        $sql = 'SELECT id
                FROM cmd
                WHERE collect=1
                    AND eventOnly=0
                ORDER BY eqLogic_id';
        $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
        $cmd = null;
        foreach ($results as $result) {
            $cmd = self::byId($result['id']);
            if (is_object($cmd)) {
                if ($cmd->getEqLogic()->getIsEnable() == 1) {
                    $cmd->execCmd(null, 1, false);
                    log::add('collect', 'info', 'la commande : ' . $cmd->getHumanName() . ' est collectée');
                    nodejs::pushUpdate('eventCmd', $cmd->getId());
                    foreach (self::byValue($cmd->getId()) as $cmd_link) {
                        nodejs::pushUpdate('eventCmd', $cmd_link->getId());
                    }
                }
            }
        }
    }

    public static function cmdToHumanReadable($_input) {
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
                $property->setValue($_input, self::cmdToHumanReadable($value));
                $property->setAccessible(false);
            }
            return $_input;
        }
        if (is_array($_input)) {
            foreach ($_input as $key => $value) {
                $_input[$key] = self::cmdToHumanReadable($value);
            }
            return $_input;
        }
        $text = $_input;
        preg_match_all("/#([0-9]*)#/", $text, $matches);
        foreach ($matches[1] as $cmd_id) {
            if (is_numeric($cmd_id)) {
                $cmd = self::byId($cmd_id);
                if (is_object($cmd)) {
                    $text = str_replace('#' . $cmd_id . '#', '#' . $cmd->getHumanName() . '#', $text);
                }
            }
        }
        return $text;
    }

    public static function humanReadableToCmd($_input) {
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
                $property->setValue($_input, self::humanReadableToCmd($value));
                $property->setAccessible(false);
            }
            return $_input;
        }
        if (is_array($_input)) {
            foreach ($_input as $key => $value) {
                $_input[$key] = self::humanReadableToCmd($value);
            }
            if ($isJson) {
                return json_encode($_input);
            }
            return $_input;
        }
        $text = $_input;

        preg_match_all("/#\[(.*?)\]\[(.*?)\]\[(.*?)\]#/", $text, $matches);
        if (count($matches) == 4) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                if (isset($matches[1][$i]) && isset($matches[2][$i]) && isset($matches[3][$i])) {
                    $cmd = self::byObjectNameEqLogicNameCmdName($matches[1][$i], $matches[2][$i], $matches[3][$i]);
                    if (is_object($cmd)) {
                        $text = str_replace($matches[0][$i], '#' . $cmd->getId() . '#', $text);
                    }
                }
            }
        }

        return $text;
    }

    public static function cmdToValue($_input) {
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
                $property->setValue($_input, self::cmdToValue($value));
                $property->setAccessible(false);
            }
            return $_input;
        }
        if (is_array($_input)) {
            foreach ($_input as $key => $value) {
                $_input[$key] = self::cmdToValue($value);
            }
            return $_input;
        }
        $text = $_input;
        preg_match_all("/#([0-9]*)#/", $text, $matches);
        foreach ($matches[1] as $cmd_id) {
            if (is_numeric($cmd_id)) {
                $cmd = self::byId($cmd_id);
                if ($cmd->getType() == 'info') {
                    $cmd_value = $cmd->execCmd();
                    if ($cmd->getSubtype() == "string") {
                        $cmd_value = '"' . $cmd_value . '"';
                    }
                    $text = str_replace('#' . $cmd_id . '#', $cmd_value, $text);
                }
            }
        }
        return $text;
    }

    public static function allType() {
        $sql = 'SELECT distinct(type) as type
                FROM cmd';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function allSubType($_type = '') {
        $values = array();
        $sql = 'SELECT distinct(subType) as subtype';
        if ($_type != '') {
            $values['type'] = $_type;
            $sql .= ' WHERE type=:type';
        }
        $sql .= ' FROM cmd';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
    }

    public static function allUnite() {
        $sql = 'SELECT distinct(unite) as unite
                FROM cmd';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function convertColor($_color) {
        $colors = config::byKey('convertColor');
        if (isset($colors[$_color])) {
            return $colors[$_color];
        }
        throw new Exception('Impossible de traduire la couleur en code hexadecimal : ' . $_color);
    }

    public static function availableWidget($_version) {
        $path = dirname(__FILE__) . '/../template/' . $_version;
        $files = ls($path, 'cmd.*', false, array('files', 'quiet'));
        $return = array();
        foreach ($files as $file) {
            $informations = explode('.', $file);
            if (!isset($return[$informations[1]])) {
                $return[$informations[1]] = array();
            }
            if (!isset($return[$informations[1]][$informations[2]])) {
                $return[$informations[1]][$informations[2]] = array();
            }
            $return[$informations[1]][$informations[2]][] = array('name' => $informations[3]);
        }
        foreach (plugin::listPlugin(true) as $plugin) {
            $path = dirname(__FILE__) . '/../../plugins/' . $plugin->getId() . '/core/template/' . $_version;
            $files = ls($path, 'cmd.*', false, array('files', 'quiet'));
            foreach ($files as $file) {
                $informations = explode('.', $file);
                if (!isset($return[$informations[1]])) {
                    $return[$informations[1]] = array();
                }
                if (!isset($return[$informations[1]][$informations[2]])) {
                    $return[$informations[1]][$informations[2]] = array();
                }
                $return[$informations[1]][$informations[2]][] = array('name' => $informations[3]);
            }
        }
        return $return;
    }

    /*     * *********************Methode d'instance************************* */

    public function getLastValue() {
        return $this->getConfiguration('lastCmdValue', null);
    }

    public function dontRemoveCmd() {
        return false;
    }

    public function getTableName() {
        return 'cmd';
    }

    public function save() {
        if ($this->getName() == '') {
            throw new Exception('Le nom de la commande ne peut etre vide : ' . print_r($this, true));
        }
        if ($this->getType() == '') {
            throw new Exception('Le type de la commande ne peut etre vide : ' . print_r($this, true));
        }
        if ($this->getSubType() == '') {
            throw new Exception('Le sous-type de la commande ne peut etre vide : ' . print_r($this, true));
        }
        if ($this->getInternalEvent() == 1) {
            $internalEvent = new internalEvent();
            if ($this->getId() == '') {
                $internalEvent->setEvent('create::cmd');
            } else {
                $internalEvent->setEvent('update::cmd');
            }
        }
        DB::save($this);
        if (isset($internalEvent)) {
            $internalEvent->setOptions('id', $this->getId());
            $internalEvent->save();
        }
        return true;
    }

    public function remove() {
        viewData::removeByTypeLinkId('cmd', $this->getId());
        dataStore::removeByTypeLinkId('cmd', $this->getId());
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('remove::cmd');
        $internalEvent->setOptions('id', $this->getId());
        DB::remove($this);
        $internalEvent->save();
    }

    public function execute($_options = array()) {
        return false;
    }

    /**
     * 
     * @param type $_options
     * @param type $cache 0 = ignorer le cache , 1 = mode normale, 2 = cache utilisé meme si expiré (puis marqué à recollecter)
     * @return command result
     * @throws Exception
     */
    public function execCmd($_options = null, $cache = 1, $_sendNodeJsEvent = true) {
        if ($this->getEqLogic()->getIsEnable() != 1) {
            throw new Exception('Cette équipement est désactivé');
        }
        if ($this->getEventOnly() && $cache == 0) {
            $cache = 1;
        }
        if ($this->getType() == 'info' && ($cache != 0)) {
            $mc = cache::byKey('cmd' . $this->getId());
            if ($mc->getValue() !== '') {
                $this->setCollectDate($mc->getOptions('collectDate', $mc->getDatetime()));
                if (!$mc->hasExpired() || $cache == 2) {
                    if ($mc->hasExpired()) {
                        $this->setCollect(1);
                        $this->save();
                        log::add('collect', 'info', 'la commande : ' . $this->getHumanName() . ' est marquée à collecter');
                    }
                    return $mc->getValue();
                }
            }
            if ($this->getEventOnly() == 1) {
                return null;
            }
        }

        $eqLogic = $this->getEqLogic();
        $type = $eqLogic->getEqType_name();
        try {
            if ($_options !== null && $_options !== '') {
                $options = self::cmdToValue($_options);
                if (is_json($_options)) {
                    $options = json_decode($_options, true);
                }
            } else {
                $options = null;
            }
            if (isset($options['color'])) {
                $options['color'] = str_replace('"', '', $options['color']);
            }
            if ($this->getSubType() == 'color' && isset($options['color']) && substr($options['color'], 0, 1) != '#') {
                $options['color'] = cmd::convertColor($options['color']);
            }
            $value = $this->execute($options);
        } catch (Exception $e) {
            //Si impossible de contacter l'équipement
            $numberTryWithoutSuccess = $eqLogic->getStatus('numberTryWithoutSuccess', 0);
            $eqLogic->setStatus('numberTryWithoutSuccess', $numberTryWithoutSuccess);
            if ($numberTryWithoutSuccess >= config::byKey('numberOfTryBeforeEqLogicDisable')) {
                $message = 'Désactivation de <a href="' . $eqLogic->getLinkToConfiguration() . '">' . $eqLogic->getName();
                $message .= ($eqLogic->getEqReal_id() != '') ? ' (' . $eqLogic->getEqReal()->getName() . ') ' : '';
                $message .= '</a> car il n\'a pas répondu ou mal répondu lors des 3 derniers essais';
                $action = '<a class="bt_changeIsEnable cursor" data-eqLogic_id="' . $this->getEqLogic_id() . '" data-isEnable="1">Ré-activer</a>';
                message::add($type, $message, $action);
                $eqLogic->setIsEnable(0);
                $eqLogic->save();
            }
            log::add($type, 'error', 'Erreur sur ' . $eqLogic->getName() . ' : ' . $e->getMessage());
            throw $e;
        }
        if (strpos($value, 'error') === false) {
            $eqLogic->setStatus('numberTryWithoutSuccess', 0);
            $eqLogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
        }
        if ($this->getType() == 'info' && $this->getSubType() == 'binary') {
            if ((is_numeric(intval($value)) && intval($value) > 1) || $value || $value == 1) {
                $value = 1;
            } else {
                $value = 0;
            }
        }
        if ($this->getType() == 'info' && $value !== false) {
            if ($this->getCollectDate() == '') {
                cache::set('cmd' . $this->getId(), $value, $this->getCacheLifetime());
            } else {
                cache::set('cmd' . $this->getId(), $value, $this->getCacheLifetime(), array('collectDate' => $this->getCollectDate()));
            }
        }

        if ($this->getType() == 'action' && $options !== null) {
            if (isset($options['slider'])) {
                $this->setConfiguration('lastCmdValue', $options['slider']);
                $this->save();
            }
            if (isset($options['color'])) {
                $this->setConfiguration('lastCmdValue', $options['color']);
                $this->save();
            }
        }
        if ($this->getType() == 'info') {
            if ($this->getCollectDate() == '') {
                $this->setCollectDate(date('Y-m-d H:i:s'));
            }
            $this->setCollect(0);
            $this->save();
            if ($_sendNodeJsEvent) {
                nodejs::pushUpdate('eventCmd', $this->getId());
                foreach (self::byValue($this->getId()) as $cmd) {
                    nodejs::pushUpdate('eventCmd', $cmd->getId());
                }
            }
        }
        return $value;
    }

    public function toHtml($_version = 'dashboard', $options = '') {
        $html = '';
        $template_name = 'cmd.' . $this->getType() . '.' . $this->getSubType();
        $template_name .= '.' . $this->getTemplate($_version, 'default');
        $template = '';
        try {
            $template = getTemplate('core', $_version, $template_name);
        } catch (Exception $e) {
            if ($template == '') {
                foreach (plugin::listPlugin(true) as $plugin) {
                    try {
                        $template = getTemplate('core', $_version, $template_name, $plugin->getId());
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
        if ($template == '') {
            $template_name = 'cmd.' . $this->getType() . '.' . $this->getSubType() . '.default';
            $template = getTemplate('core', $_version, $template_name);
        }
        $replace = array(
            '#id#' => $this->getId(),
            '#name#' => $this->getName(),
        );
        $replace['#history#'] = '';
        $replace['#displayHistory#'] = 'display : none;';
        switch ($this->getType()) {
            case "info":
                $replace['#unite#'] = ($this->getUnite() != '') ? $this->getUnite() : '';
                $replace['#minValue#'] = $this->getConfiguration('minValue', 0);
                $replace['#maxValue#'] = $this->getConfiguration('maxValue', 100);
                $replace['#state#'] = '';
                $replace['#tendance#'] = '';
                try {
                    $value = $this->execCmd(null, 2);
                    if ($value === null) {
                        return template_replace($replace, $template);
                    }
                    if ($this->getSubType() == 'binary' && $this->getDisplay('invertBinary') == 1) {
                        if ($value == 1) {
                            $value = 0;
                        } else {
                            $value = 1;
                        }
                    }
                    $replace['#state#'] = $value;
                    $replace['#collectDate#'] = $this->getCollectDate();
                    switch ($this->getSubType()) {
                        case "binary" :
                            $replace['#state#'] = $value;
                            break;
                        case "string" :
                            $replace['#state#'] = $value;
                            break;
                        case "numeric" :
                            if ($this->getIsHistorized()) {
                                $replace['#displayHistory#'] = '';
                                $historyStatistique = $this->getStatistique(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . config::byKey('historyCalculPeriod') . ' hour')), date('Y-m-d H:i:s'));
                                $replace['#averageHistoryValue#'] = round($historyStatistique['avg'], 1) . $this->getUnite();
                                $replace['#minHistoryValue#'] = round($historyStatistique['min'], 1) . $this->getUnite();
                                $replace['#maxHistoryValue#'] = round($historyStatistique['max'], 1) . $this->getUnite();
                                $tendance = $this->getTendance(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -' . config::byKey('historyCalculTendance') . ' hour')), date('Y-m-d H:i:s'));
                                $replace['#tendance#'] = 'fa fa-minus';
                                if ($tendance > config::byKey('historyCalculTendanceThresholddMax')) {
                                    $replace['#tendance#'] = 'fa fa-arrow-up';
                                }
                                if ($tendance < config::byKey('historyCalculTendanceThresholddMin')) {
                                    $replace['#tendance#'] = 'fa fa-arrow-down';
                                }
                            }
                            break;
                    }
                } catch (Exception $e) {
                    
                }
                if ($this->getIsHistorized() == 1) {
                    $replace['#history#'] = 'history cursor';
                    $html .= template_replace($replace, getTemplate('core', $_version, 'cmd.info.history.default'));
                }
                $html .= template_replace($replace, $template);
                break;
            case "action":
                $cmdValue = $this->getCmdValue();
                if (is_object($cmdValue) && $cmdValue->getType() == 'info') {
                    $replace['#state#'] = $cmdValue->execCmd(null, 2);
                } else {
                    if ($this->getLastValue() != null) {
                        $replace['#state#'] = $this->getLastValue();
                    } else {
                        $replace['#state#'] = '';
                    }
                }
                $replace['#minValue#'] = $this->getConfiguration('minValue', 0);
                $replace['#maxValue#'] = $this->getConfiguration('maxValue', 100);
                $html .= template_replace($replace, $template);
                if (trim($html) == '') {
                    return $html;
                }

                if ($options != '') {
                    $options = self::cmdToHumanReadable($options);
                    if (is_json($options)) {
                        $options = json_decode($options, true);
                    }
                    if (is_array($options)) {
                        foreach ($options as $key => $value) {
                            $replace['#' . $key . '#'] = $value;
                        }
                        $html = template_replace($replace, $html);
                    }
                }
                break;
        }
        return $html;
    }

    public function event($_value) {
        $eqLogic = $this->getEqLogic();
        if (is_object($eqLogic)) {
            if ($eqLogic->getIsEnable() == 1) {
                if ($this->getType() == 'info' && $this->getSubType() == 'binary' && is_numeric(intval($_value)) && intval($_value) > 1) {
                    $_value = 1;
                }
                if (strpos($_value, 'error') === false) {
                    $eqLogic->setStatus('numberTryWithoutSuccess', 0);
                    $eqLogic->setStatus('lastCommunication', date('Y-m-d H:i:s'));
                    $this->addHistoryValue($_value);
                }
                $message = 'Message venant de ' . $this->getHumanName() . ' : ' . $_value;
                log::add($eqLogic->getEqType_name(), 'Event', $message . ' / cache lifetime => ' . $this->getCacheLifetime());
                cache::set('cmd' . $this->getId(), $_value, $this->getCacheLifetime());
                if ($this->getCollect() == 1) {
                    $this->setCollect(0);
                    $this->save();
                }
                nodejs::pushUpdate('eventCmd', $this->getId());
                foreach (self::byValue($this->getId()) as $cmd) {
                    nodejs::pushUpdate('eventCmd', $cmd->getId());
                }
                $internalEvent = new internalEvent();
                $internalEvent->setEvent('event::cmd');
                $internalEvent->setOptions('id', $this->getId());
                $internalEvent->setOptions('value', $_value);
                $internalEvent->save();
                scenario::check($this->getId());
            }
        } else {
            log::add('core', 'Error', 'Impossible de trouver l\'équipement correspondant à l\'id ' . $this->getEqLogic_id() . ' ou équipement désactivé. Evènement sur commande : ' . print_r($this, true));
        }
    }

    public function emptyHistory() {
        return history::emptyHistory($this->getId());
    }

    public function addHistoryValue($_value) {
        if ($this->getIsHistorized() == 1) {
            if (($this->getConfiguration('maxValue') === '' || $_value <= $this->getConfiguration('maxValue')) && ($this->getConfiguration('minValue') === '' || $_value >= $this->getConfiguration('minValue', $_value))) {
                $hitory = new history();
                $hitory->setCmd_id($this->getId());
                $hitory->setValue($_value);
                return $hitory->save();
            }
        }
        return false;
    }

    public function getStatistique($_startTime, $_endTime) {
        return history::getStatistique($this->getId(), $_startTime, $_endTime);
    }

    public function getTendance($_startTime, $_endTime) {
        return history::getTendance($this->getId(), $_startTime, $_endTime);
    }

    public function getCacheLifetime() {
        if ($this->getEventOnly() == 1) {
            return 0;
        }
        if ($this->getCache('enable') == 0 && $this->getCache('lifetime') == '') {
            return 5;
        }
        $lifetime = $this->getCache('lifetime', config::byKey('lifeTimeMemCache'));
        return ($lifetime < 5) ? 5 : $lifetime;
    }

    public function getCmdValue() {
        if (is_numeric($this->getValue())) {
            return self::byId($this->getValue());
        }
        return false;
    }

    public function getHumanName() {
        $name = '';
        $eqLogic = $this->getEqLogic();
        if (is_object($eqLogic)) {
            $name .= $eqLogic->getHumanName();
        }
        $name .= '[' . $this->getName() . ']';
        return $name;
    }

    public function getHistory($_dateStart = null, $_dateEnd = null) {
        return history::all($this->id, $_dateStart, $_dateEnd);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getSubType() {
        return $this->subType;
    }

    public function getEqType_name() {
        return eqLogic::byId($this->eqLogic_id)->getEqType_name();
    }

    public function getEqLogic_id() {
        return $this->eqLogic_id;
    }

    public function getIsHistorized() {
        return $this->isHistorized;
    }

    public function getUnite() {
        return $this->unite;
    }

    public function getEqLogic() {
        return eqLogic::byId($this->eqLogic_id);
    }

    public function getEventOnly() {
        return $this->eventOnly;
    }

    public function setId($id = '') {
        $this->setInternalEvent(1);
        $this->id = $id;
    }

    public function setName($name) {
        $this->setInternalEvent(1);
        $this->name = $name;
    }

    public function setType($type) {
        $this->setInternalEvent(1);
        $this->type = $type;
    }

    public function setSubType($subType) {
        $this->setInternalEvent(1);
        $this->subType = $subType;
    }

    public function setEqLogic_id($eqLogic_id) {
        $this->setInternalEvent(1);
        $this->eqLogic_id = $eqLogic_id;
    }

    public function setIsHistorized($isHistorized) {
        $this->setInternalEvent(1);
        $this->isHistorized = $isHistorized;
    }

    public function setUnite($unite) {
        $this->setInternalEvent(1);
        $this->unite = $unite;
    }

    public function setEventOnly($eventOnly) {
        $this->setInternalEvent(1);
        $this->eventOnly = $eventOnly;
    }

    public function getCache($_key = '', $_default = '') {
        return utils::getJsonAttr($this->cache, $_key, $_default);
    }

    public function setCache($_key, $_value) {
        $this->cache = utils::setJsonAttr($this->cache, $_key, $_value);
    }

    public function getTemplate($_key = '', $_default = '') {
        return utils::getJsonAttr($this->template, $_key, $_default);
    }

    public function setTemplate($_key, $_value) {
        $this->template = utils::setJsonAttr($this->template, $_key, $_value);
    }

    public function getConfiguration($_key = '', $_default = '') {
        return utils::getJsonAttr($this->configuration, $_key, $_default);
    }

    public function setConfiguration($_key, $_value) {
        $this->configuration = utils::setJsonAttr($this->configuration, $_key, $_value);
    }

    public function getDisplay($_key = '', $_default = '') {
        return utils::getJsonAttr($this->display, $_key, $_default);
    }

    public function setDisplay($_key, $_value) {
        $this->display = utils::setJsonAttr($this->display, $_key, $_value);
    }

    public function getCollect() {
        return $this->collect;
    }

    public function setCollect($collect) {
        $this->collect = $collect;
    }

    public function getCollectDate() {
        return $this->_collectDate;
    }

    public function setCollectDate($_collectDate) {
        $this->_collectDate = $_collectDate;
    }

    public function getValue() {
        if (!is_numeric($this->value)) {
            return null;
        }
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getIsVisible() {
        return $this->isVisible;
    }

    public function setIsVisible($isVisible) {
        $this->isVisible = $isVisible;
    }

    public function getInternalEvent() {
        return $this->_internalEvent;
    }

    public function setInternalEvent($_internalEvent) {
        $this->_internalEvent = $_internalEvent;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

}

?>
