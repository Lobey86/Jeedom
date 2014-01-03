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

class eqReal {
    /*     * *************************Attributs****************************** */

    protected $id;
    protected $logicalId = '';
    protected $name;
    protected $type;
    protected $cat;
    protected $configuration;
    protected $_internalEvent = 0;

    /*     * ***********************Methode static*************************** */

    public function getTableName() {
        return 'eqReal';
    }

    private static function getClass($_id) {
        if (get_called_class() != __CLASS__) {
            return get_called_class();
        }
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT eqType_name
                FROM eqLogic
                WHERE eqReal_id=:id';
        $result = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        $eqTyme_name = $result['eqType_name'];
        if ($eqTyme_name != '' && class_exists($eqTyme_name . 'Real')) {
            return $eqTyme_name . 'Real';
        }
        return __CLASS__;
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM eqReal
                WHERE id=:id';
        $class = self::getClass($_id);
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, $class);
    }

    public static function byLogicalId($_logicalId, $_cat) {
        $values = array(
            'logicalId' => $_logicalId,
            'cat' => $_cat
        );
        $sql = 'SELECT id
                FROM eqReal
                WHERE logicalId=:logicalId
                    AND cat=:cat';
        $results = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        $return = array();
        foreach ($results as $result) {
            $return[] = self::byId($result['id']);
        }
        return $return;
    }

    /*     * *********************Methode d'instance************************* */

    public function remove() {
        foreach ($this->getEqLogic() as $eqLogic) {
            $eqLogic->remove();
        }
        dataStore::removeByTypeLinkId('eqReal', $this->getId());
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('remove::eqReal');
        $internalEvent->setOptions('id', $this->getId());
        DB::remove($this);
        $internalEvent->save();
    }

    public function save() {
        if ($this->getName() == '') {
            throw new Exception('Le nom de l\'équipement réel ne peut etre vide');
        }
        if ($this->getInternalEvent() == 1) {
            $internalEvent = new internalEvent();
            if ($this->getId() == '') {
                $internalEvent->setEvent('create::eqReal');
            } else {
                $internalEvent->setEvent('update::eqReal');
            }
        }
        DB::save($this);
        if (isset($internalEvent)) {
            $internalEvent->setOptions('id', $this->getId());
            $internalEvent->save();
        }
        return true;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getEqLogic() {
        return eqLogic::byEqRealId($this->id);
    }

    public function getId() {
        return $this->id;
    }

    public function getLogicalId() {
        return $this->logicalId;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getCat() {
        return $this->cat;
    }

    public function setId($id) {
        $this->setInternalEvent(1);
        $this->id = $id;
    }

    public function setLogicalId($logicalId) {
        $this->setInternalEvent(1);
        $this->logicalId = $logicalId;
    }

    public function setName($name) {
        $this->setInternalEvent(1);
        $this->name = $name;
    }

    public function setType($type) {
        $this->setInternalEvent(1);
        $this->type = $type;
    }

    public function setCat($cat) {
        $this->setInternalEvent(1);
        $this->cat = $cat;
    }

    public function getConfiguration($_name = '', $_default = '') {
        if ($this->configuration == '') {
            return $_default;
        }
        if (is_json($this->configuration)) {
            if ($_name == '') {
                return json_decode($this->configuration);
            }
            $configuration = json_decode($this->configuration, true);
            return (isset($configuration[$_name])) ? $configuration[$_name] : $_default;
        }
        return $_default;
    }

    public function setConfiguration($_name, $_key) {
        if ($this->configuration == '' || !is_json($this->configuration)) {
            $this->configuration = json_encode(array($_name => $_key));
        } else {
            $configuration = json_decode($this->configuration, true);
            $configuration[$_name] = $_key;
            $this->configuration = json_encode($configuration);
        }
    }

    public function getInternalEvent() {
        return $this->_internalEvent;
    }

    public function setInternalEvent($_internalEvent) {
        $this->_internalEvent = $_internalEvent;
    }

}

?>
