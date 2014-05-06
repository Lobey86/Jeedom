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

class update {
    /*     * *************************Attributs****************************** */

    private $id;
    private $logical_id;
    private $name;
    private $localDatetime;
    private $remoteDatetime;
    private $status;
    private $configuration;

    /*     * ***********************Methode static*************************** */

    public static function byId($_id) {
        $values = array(
            'id' => $_id,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM udpate 
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byLogicalId($_logicalId) {
        $values = array(
            'logicalId' => $_logicalId,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM update 
                WHERE logicalId=:logicalId';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     *
     * @return array de tous les utilisateurs 
     */
    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . ' 
                FROM update';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        return DB::save($this);
    }

    public function remove() {
        return DB::remove($this);
    }

    public function refresh() {
        DB::refresh($this);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getLogical_id() {
        return $this->logical_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getLocalDatetime() {
        return $this->localDatetime;
    }

    public function getRemoteDatetime() {
        return $this->remoteDatetime;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getConfiguration($_key = '', $_default = '') {
        return utils::getJsonAttr($this->configuration, $_key, $_default);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setLogical_id($logical_id) {
        $this->logical_id = $logical_id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setLocalDatetime($localDatetime) {
        $this->localDatetime = $localDatetime;
    }

    public function setRemoteDatetime($remoteDatetime) {
        $this->remoteDatetime = $remoteDatetime;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setConfiguration($_key, $_value) {
        $this->configuration = utils::setJsonAttr($this->configuration, $_key, $_value);
    }

}

?>
