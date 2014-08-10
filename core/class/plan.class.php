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

class plan {
    /*     * *************************Attributs****************************** */

    private $id;
    private $object_id;
    private $link_type;
    private $link_id;
    private $position;
    private $display;
    private $css;

    /*     * ***********************Methode static*************************** */

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM plan
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byObjectId($_object_id) {
        $values = array(
            'object_id' => $_object_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM plan
                WHERE object_id=:object_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byLinkTypeLinkId($_link_type, $_link_id) {
        $values = array(
            'link_type' => $_link_type,
            'link_id' => $_link_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM plan
                WHERE link_type=:link_type
                    AND link_id=:link_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byLinkTypeLinkIdObjectId($_link_type, $_link_id, $_object_id) {
        $values = array(
            'link_type' => $_link_type,
            'link_id' => $_link_id,
            'object_id' => $_object_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM plan
                WHERE link_type=:link_type
                    AND link_id=:link_id
                    AND object_id=:object_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM plan';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        DB::save($this);
    }

    public function remove() {
        DB::remove($this);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getObject_id() {
        return $this->object_id;
    }

    public function getLink_type() {
        return $this->link_type;
    }

    public function getLink_id() {
        return $this->link_id;
    }

    public function getPosition($_key, $_default) {
        return utils::getJsonAttr($this->position, $_key, $_default);
    }

    public function getDisplay($_key, $_default) {
        return utils::getJsonAttr($this->display, $_key, $_default);
    }

    public function getCss($_key, $_default) {
        return utils::getJsonAttr($this->css, $_key, $_default);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setObject_id($object_id) {
        $this->object_id = $object_id;
    }

    public function setLink_type($link_type) {
        $this->link_type = $link_type;
    }

    public function setLink_id($link_id) {
        $this->link_id = $link_id;
    }

    public function setPosition($_key, $_value) {
        $this->position = utils::setJsonAttr($this->position, $_key, $_value);
    }

    public function setDisplay($_key, $_value) {
        $this->display = utils::setJsonAttr($this->display, $_key, $_value);
    }

    public function setCss($_key, $_value) {
        $this->css = utils::setJsonAttr($this->css, $_key, $_value);
    }

}

?>
