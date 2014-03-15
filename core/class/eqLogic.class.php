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

class eqLogic {
    /*     * *************************Attributs****************************** */

    protected $id;
    protected $name;
    protected $logicalId = '';
    protected $object_id = null;
    protected $eqType_name;
    protected $eqReal_id = null;
    protected $isVisible = 0;
    protected $isEnable = 0;
    protected $configuration;
    protected $specificCapatibilities;
    protected $timeout;
    protected $category;
    protected $_internalEvent = 0;
    protected $_debug = false;

    /*     * ***********************Methode static*************************** */

    private static function getClass($_id) {
        if (get_called_class() != __CLASS__) {
            return get_called_class();
        }
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT eqType_name, isEnable
                FROM eqLogic
                WHERE id=:id';
        $result = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        $eqTyme_name = $result['eqType_name'];
        if ($result['isEnable'] == 0) {
            try {
                $plugin = null;
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
            return $eqTyme_name;
        }
        return __CLASS__;
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM eqLogic
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, self::getClass($_id));
    }

    public static function all() {
        $sql = 'SELECT id
                FROM eqLogic';
        $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byTimeout($_timeout = 0) {
        $values = array(
            'timeout' => $_timeout
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE timeout>:timeout';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byEqRealId($_eqReal_id) {
        $values = array(
            'eqReal_id' => $_eqReal_id
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE eqReal_id=:eqReal_id';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byObjectId($_object_id, $_onlyEnable = true) {
        $values = array();
        $sql = 'SELECT id
                FROM eqLogic';
        if ($_object_id == null) {
            $sql .= ' WHERE object_id IS NULL';
        } else {
            $values['object_id'] = $_object_id;
            $sql .= ' WHERE object_id=:object_id';
        }
        if ($_onlyEnable) {
            $sql .= ' AND isEnable = 1';
        }
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byLogicalId($_logicalId, $_eqType_name) {
        $values = array(
            'logicalId' => $_logicalId,
            'eqType_name' => $_eqType_name
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE logicalId=:logicalId
                    AND eqType_name=:eqType_name';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byType($_eqType_name) {
        $values = array(
            'eqType_name' => $_eqType_name
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE eqType_name=:eqType_name
                ORDER BY name';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function byTypeAndSearhConfiguration($_eqType_name, $_configuration) {
        $values = array(
            'eqType_name' => $_eqType_name,
            'configuration' => '%' . $_configuration . '%'
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE eqType_name=:eqType_name
                    AND configuration LIKE :configuration
                ORDER BY name';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    public static function listByTypeAndCmdType($_eqType_name, $_typeCmd, $subTypeCmd = '') {
        if ($subTypeCmd == '') {
            $values = array(
                'eqType_name' => $_eqType_name,
                'typeCmd' => $_typeCmd
            );
            $sql = 'SELECT DISTINCT(el.id),el.name
                    FROM eqLogic el
                        INNER JOIN cmd c ON c.eqLogic_id=el.id
                    WHERE eqType_name=:eqType_name
                        AND c.type=:typeCmd
                    ORDER BY name';
            return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        } else {
            $values = array(
                'eqType_name' => $_eqType_name,
                'typeCmd' => $_typeCmd,
                'subTypeCmd' => $subTypeCmd
            );
            $sql = 'SELECT DISTINCT(el.id),el.name
                    FROM eqLogic el
                        INNER JOIN cmd c ON c.eqLogic_id=el.id
                    WHERE eqType_name=:eqType_name
                        AND c.type=:typeCmd
                        AND c.subType=:subTypeCmd
                    ORDER BY name';
            return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        }
    }

    public static function listByObjectAndCmdType($_object_id, $_typeCmd, $subTypeCmd = '') {
        $values = array();
        $sql = 'SELECT DISTINCT(el.id),el.name
                FROM eqLogic el
                    INNER JOIN cmd c ON c.eqLogic_id=el.id
                WHERE ';
        if ($_object_id == null) {
            $sql .= ' object_id IS NULL ';
        } elseif ($_object_id != '') {
            $values['object_id'] = $_object_id;
            $sql .= ' object_id=:object_id ';
        }
        if ($subTypeCmd != '') {
            $values['subTypeCmd'] = $subTypeCmd;
            $sql .= ' AND c.subType=:subTypeCmd ';
        }
        if ($_typeCmd != '' && $_typeCmd != 'all') {
            $values['type'] = $_typeCmd;
            $sql .= ' AND c.type=:type ';
        }
        $sql .= ' ORDER BY name ';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
    }

    public static function allType() {
        $sql = 'SELECT distinct(eqType_name) as type
                FROM eqLogic';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function checkAlive() {
        foreach (eqLogic::byTimeout() as $eqLogic) {
            if ($eqLogic->getIsEnable() == 1) {
                $sendReport = false;
                $cmds = $eqLogic->getCmd();
                foreach ($cmds as $cmd) {
                    if ($cmd->getEventOnly() == 1) {
                        $sendReport = true;
                    }
                }
                $logicalId = 'noMessage' . $eqLogic->getId();
                if ($sendReport) {
                    $noReponseTimeLimit = $eqLogic->getTimeout();
                    if (count(message::byPluginLogicalId('core', $logicalId)) == 0) {
                        if ($eqLogic->getStatus('lastCommunication', date('Y-m-d H:i:s')) < date('Y-m-d H:i:s', strtotime('-' . $noReponseTimeLimit . ' minutes' . date('Y-m-d H:i:s')))) {
                            $message = 'Attention <a href="' . $eqLogic->getLinkToConfiguration() . '">' . $eqLogic->getHumanName();
                            $message .= '</a> n\'a pas envoyé de message depuis plus de ' . $noReponseTimeLimit . ' min (vérifier les piles)';
                            message::add('core', $message, '', $logicalId);
                            foreach ($cmds as $cmd) {
                                if ($cmd->getEventOnly() == 1) {
                                    $cmd->event('error::timeout');
                                }
                            }
                        }
                    } else {
                        if ($eqLogic->getStatus('lastCommunication', date('Y-m-d H:i:s')) > date('Y-m-d H:i:s', strtotime('-' . $noReponseTimeLimit . ' minutes' . date('Y-m-d H:i:s')))) {
                            foreach (message::byPluginLogicalId('core', $logicalId) as $message) {
                                $message->remove();
                            }
                        }
                    }
                }
            }
        }
    }

    public static function byObjectNameEqLogicName($_object_name, $_eqLogic_name) {
        $values = array(
            'eqLogic_name' => $_eqLogic_name,
            'object_name' => $_object_name,
        );
        $sql = 'SELECT el.id
                    FROM eqLogic el
                        INNER JOIN object ob ON el.object_id=ob.id
                    WHERE el.name=:eqLogic_name
                        AND ob.name=:object_name';
        $id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($id['id']);
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
        preg_match_all("/#([0-9]*)#/", $text, $matches);
        foreach ($matches[1] as $eqLogic_id) {
            if (is_numeric($eqLogic_id)) {
                $eqLogic = self::byId($eqLogic_id);
                if (is_object($eqLogic)) {
                    $text = str_replace('#' . $eqLogic_id . '#', '#' . $eqLogic->getHumanName() . '#', $text);
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
                return json_encode($_input);
            }
            return $_input;
        }
        $text = $_input;

        preg_match_all("/#\[(.*?)\]\[(.*?)\]#/", $text, $matches);
        if (count($matches) == 3) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                if (isset($matches[1][$i]) && isset($matches[2][$i])) {
                    $eqLogic = self::byObjectNameEqLogicName($matches[1][$i], $matches[2][$i]);
                    if (is_object($eqLogic)) {
                        $text = str_replace($matches[0][$i], '#' . $eqLogic->getId() . '#', $text);
                    }
                }
            }
        }

        return $text;
    }

    /*     * *********************Methode d'instance************************* */

    public function getTableName() {
        return 'eqLogic';
    }

    public function toHtml($_version = 'dashboard') {
        $info = '';
        $action = '';
        if ($this->getIsEnable()) {
            foreach ($this->getCmd() as $cmd) {
                if ($cmd->getIsVisible() == 1) {
                    if ($cmd->getType() == 'action') {
                        $action.=$cmd->toHtml($_version);
                    }
                    if ($cmd->getType() == 'info') {
                        $info.=$cmd->toHtml($_version);
                    }
                }
            }
        }
        $object = $this->getObject();


        $replace = array(
            '#id#' => $this->getId(),
            '#info#' => (isset($info)) ? $info : '',
            '#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
            '#eqLink#' => $this->getLinkToConfiguration(),
            '#action#' => (isset($action)) ? $action : '',
            '#object_name#' => (is_object($object)) ? $object->getName() . ' - ' : '',
            '#background_color#' => $this->getBackgroundColor(),
        );

        $html = template_replace($replace, getTemplate('core', $_version, 'eqLogic'));
        return $html;
    }

    public function getShowOnChild() {
        return false;
    }

    public function remove() {
        foreach ($this->getCmd() as $cmd) {
            $cmd->remove();
        }
        viewData::removeByTypeLinkId('eqLogic', $this->getId());
        dataStore::removeByTypeLinkId('eqLogic', $this->getId());
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('remove::eqLogic');
        $internalEvent->setOptions('id', $this->getId());
        DB::remove($this);
        $internalEvent->save();
    }

    public function save() {
        if ($this->getName() == '') {
            throw new Exception('Le nom de l\'équipement ne peut etre vide');
        }
        if ($this->getInternalEvent() == 1) {
            $internalEvent = new internalEvent();
            if ($this->getId() == '') {
                $internalEvent->setEvent('create::eqLogic');
            } else {
                $internalEvent->setEvent('update::eqLogic');
            }
        }
        DB::save($this);
        if (isset($internalEvent)) {
            $internalEvent->setOptions('id', $this->getId());
            $internalEvent->save();
        }
        return true;
    }

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=' . $this->getEqType_name() . '&m=' . $this->getEqType_name() . '&id=' . $this->getId();
    }

    public function collectInProgress() {
        $values = array(
            'eqLogic_id' => $this->getId()
        );
        $sql = 'SELECT count(*)
                FROM cmd
                WHERE eqLogic_id=:eqLogic_id
                    AND collect=1
                    AND eventOnly=0';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        if ($results['count(*)'] > 0) {
            return true;
        }
        return false;
    }

    public function getHumanName() {
        $name = '';
        $objet = $this->getObject();
        if (is_object($objet)) {
            $name .= '[' . $objet->getName() . ']';
        } else {
            $name .= '[Aucun]';
        }
        $name .= '[' . $this->getName() . ']';
        return $name;
    }

    public function getBackgroundColor() {
        if ($this->getCategory('security', 0) == 1) {
            return jeedom::getConfiguration('eqLogic:category:security:color');
        }
        if ($this->getCategory('heating', 0) == 1) {
            return jeedom::getConfiguration('eqLogic:category:heating:color');
        }
        if ($this->getCategory('energy', 0) == 1) {
            return jeedom::getConfiguration('eqLogic:category:energy:color');
        }
        if ($this->getCategory('light', 0) == 1) {
            return jeedom::getConfiguration('eqLogic:category:light:color');
        }
        return '#F5F5F5';
    }

    public function displayDebug($_message) {
        if ($this->getDebug()) {
            echo $_message . "\n";
        }
    }

    public function batteryStatus($_pourcent) {
        if ($_pourcent >= 20) {
            foreach (message::byPluginLogicalId($this->getEqType_name(), 'lowBattery' . $this->getId()) as $message) {
                $message->remove();
            }
            foreach (message::byPluginLogicalId($this->getEqType_name(), 'noBattery' . $this->getId()) as $message) {
                $message->remove();
            }
        } elseif ($_pourcent > 0) {
            $logicalId = 'lowBattery' . $this->getId();
            if (count(message::byPluginLogicalId($this->getEqType_name(), $logicalId)) == 0) {
                $message = 'Le module ' . $this->getEqType_name() . ' ';
                $object = $this->getObject();
                if (is_object($object)) {
                    $message .= '[' . $object->getName() . ']';
                }
                $message .= $this->getHumanName() . ' à moins de ' . $_pourcent . '% de batterie';
                message::add($this->getEqType_name(), $message, '', $logicalId);
            }
        } else {
            foreach (message::byPluginLogicalId($this->getEqType_name(), 'lowBattery' . $this->getId()) as $message) {
                $message->remove();
            }
            $logicalId = 'noBattery' . $this->getId();
            $message = 'Le module ' . $this->getEqType_name() . ' ';
            $object = $this->getObject();
            if (is_object($object)) {
                $message .= '[' . $object->getName() . ']';
            }
            $message .= $this->getHumanName() . ' a été désactivé car il n\'a plus de batterie';
            $action = '<a class="bt_changeIsEnable cursor" data-eqLogic_id="' . $this->getId() . '" data-isEnable="1">Ré-activer</a>';
            message::add($this->getEqType_name(), $message, $action, $logicalId);
        }
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getLogicalId() {
        return $this->logicalId;
    }

    public function getObject_id() {
        return $this->object_id;
    }

    public function getObject() {
        return object::byId($this->object_id);
    }

    public function getEqType_name() {
        return $this->eqType_name;
    }

    public function getIsVisible() {
        return $this->isVisible;
    }

    public function getIsEnable() {
        return $this->isEnable;
    }

    public function getCmd() {
        return cmd::byEqLogicId($this->id);
    }

    public function getEqReal_id() {
        return $this->eqReal_id;
    }

    public function getEqReal() {
        return eqReal::byId($this->eqReal_id);
    }

    public function setId($id) {
        $this->setInternalEvent(1);
        $this->id = $id;
    }

    public function setName($name) {
        $this->setInternalEvent(1);
        $this->name = $name;
    }

    public function setLogicalId($logicalId) {
        $this->setInternalEvent(1);
        $this->logicalId = $logicalId;
    }

    public function setObject_id($object_id = null) {
        $this->setInternalEvent(1);
        $this->object_id = (!is_numeric($object_id)) ? null : $object_id;
    }

    public function setEqType_name($eqType_name) {
        $this->setInternalEvent(1);
        $this->eqType_name = $eqType_name;
    }

    public function setEqReal_id($eqReal_id) {
        $this->setInternalEvent(1);
        $this->eqReal_id = $eqReal_id;
    }

    public function setIsVisible($isVisible) {
        $this->setInternalEvent(1);
        $this->isVisible = $isVisible;
    }

    public function setIsEnable($_isEnable) {
        $this->setInternalEvent(1);
        $this->isEnable = $_isEnable;
    }

    public function getConfiguration($_key = '', $_default = '') {
        return utils::getJsonAttr($this->configuration, $_key, $_default);
    }

    public function setConfiguration($_key, $_value) {
        $this->configuration = utils::setJsonAttr($this->configuration, $_key, $_value);
    }

    public function getStatus($_key = '', $_default = '') {
        $status = cache::byKey('core::eqLogic' . $this->getId() . '::' . $_key);
        return $status->getValue($_default);
    }

    public function setStatus($_key, $_value) {
        return cache::set('core::eqLogic' . $this->getId() . '::' . $_key, $_value, 0);
    }

    public function getSpecificCapatibilities($_key = '', $_default = '') {
        return utils::getJsonAttr($this->specificCapatibilities, $_key, $_default);
    }

    public function setSpecificCapatibilities($_key, $_value) {
        $this->specificCapatibilities = utils::setJsonAttr($this->specificCapatibilities, $_key, $_value);
    }

    public function getInternalEvent() {
        return $this->_internalEvent;
    }

    public function setInternalEvent($_internalEvent) {
        $this->_internalEvent = $_internalEvent;
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    public function getCategory($_key = '', $_default = '') {
        return utils::getJsonAttr($this->category, $_key, $_default);
    }

    public function setCategory($_key, $_value) {
        $this->category = utils::setJsonAttr($this->category, $_key, $_value);
    }

    public function getDebug() {
        return $this->_debug;
    }

    public function setDebug($_debug) {
        if ($_debug) {
            echo "Mode debug activé\n";
        }
        $this->_debug = $_debug;
    }

}

?>
