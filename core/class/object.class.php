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

class object {
    /*     * *************************Attributs****************************** */

    private $id;
    private $name;
    private $father_id = null;
    private $isVisible = 1;

    /*     * ***********************Methode static*************************** */

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM object
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM object
                ORDER BY father_id,name';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function rootObject($_all = false) {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM object
                WHERE father_id IS NULL
                ORDER BY name';
        if ($_all === false) {
            $sql .= ' LIMIT 1';
            return DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
        }
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function buildTree($_object = null) {
        $return = array();
        if ($_object == null) {
            $object_list = self::rootObject(true);
        } else {
            if (is_object($_object)) {
                $object_list = $_object->getChild();
            } else {
                return array();
            }
        }
        foreach ($object_list as $object) {
            if (is_object($object)) {
                $return[] = $object;
                $childs = self::buildTree($object);
                if (count($childs) > 0) {
                    $return = array_merge($return, $childs);
                }
            }
        }
        return $return;
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        $internalEvent = new internalEvent();
        if ($this->getId() == '') {
            $internalEvent->setEvent('create::object');
        } else {
            $internalEvent->setEvent('update::object');
        }
        DB::save($this);
        $internalEvent->setOptions('id', $this->getId());
        $internalEvent->save();
        return true;
    }

    public function getChild() {
        $values = array(
            'id' => $this->id
        );
        $sql = 'SELECT id,name,father_id,isVisible
                FROM object
                WHERE father_id=:id
                    AND isVisible=1
                ORDER BY name';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public function getChilds() {
        $return = array();
        foreach ($this->getChild() as $child) {
            $return[] = $child;
            $return = array_merge($return, $child->getChilds());
        }
        return $return;
    }

    public function getEqLogic($_onlyEnable = true) {
        return eqLogic::byObjectId($this->getId(), $_onlyEnable);
    }

    public function getScenario($_onlyEnable = true) {
        return scenario::byObjectId($this->getId(), $_onlyEnable);
    }

    public function preRemove() {
        dataStore::removeByTypeLinkId('object', $this->getId());
    }

    public function remove() {
        $internalEvent = new internalEvent();
        $internalEvent->setEvent('remove::object');
        $internalEvent->setOptions('id', $this->getId());
        DB::remove($this);
        $internalEvent->save();
    }

    public function getFather() {
        return self::byId($this->getFather_id());
    }

    public function parentNumber() {
        $father = $this->getFather();
        if (!is_object($father)) {
            return 0;
        }
        $fatherNumber = 0;
        while (true) {
            $fatherNumber++;
            $father = $father->getFather();
            if (!is_object($father)) {
                return $fatherNumber;
            }
            if ($fatherNumber > 50) {
                throw new Exception(__('Erreur boucle dans les relation entre objects', __FILE__));
            }
        }
    }

    public function getHumanName() {
        return $this->name;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getFather_id() {
        return $this->father_id;
    }

    public function getIsVisible() {
        return $this->isVisible;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setFather_id($father_id = null) {
        $this->father_id = ($father_id == '') ? null : $father_id;
    }

    public function setIsVisible($isVisible) {
        $this->isVisible = $isVisible;
    }

}

?>
