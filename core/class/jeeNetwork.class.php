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

class jeeNetwork {
    /*     * *************************Attributs****************************** */

    private $id;
    private $ip;
    private $apikey;
    private $plugin;
    private $configuration;
    private $name;

    /*     * ***********************Methode static*************************** */

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM jeeNetwork';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM jeeNetwork
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byPlugin($_plugin) {
        $values = array(
            'plugin' => '%' . $_plugin . '%'
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM jeeNetwork
                WHERE plugin LIKE :plugin';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        return DB::save($this);
    }

    public function remove() {
        return DB::remove($this);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getIp() {
        return $this->ip;
    }

    public function getApikey() {
        return $this->apikey;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function setApikey($apikey) {
        $this->apikey = $apikey;
    }

    public function getPlugin($_key = '', $_default = '') {
        return utils::getJsonAttr($this->plugin, $_key, $_default);
    }

    public function setPlugin($_key, $_value) {
        $this->plugin = utils::setJsonAttr($this->plugin, $_key, $_value);
    }

    public function getConfiguration($_key = '', $_default = '') {
        return utils::getJsonAttr($this->configuration, $_key, $_default);
    }

    public function setConfiguration($_key, $_value) {
        $this->configuration = utils::setJsonAttr($this->configuration, $_key, $_value);
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}
