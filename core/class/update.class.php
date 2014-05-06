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
    private $type;
    private $logicalId;
    private $name;
    private $localVersion;
    private $remoteVersion;
    private $status;
    private $configuration;

    /*     * ***********************Methode static*************************** */

    public static function checkAllUpdate() {
        foreach (self::all() as $update) {
            if ($update->getState() !== 'hold') {
                $update->checkUpdate();
            }
        }
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM `update` 
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byLogicalId($_logicalId) {
        $values = array(
            'logicalId' => $_logicalId,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM `update`
                WHERE logicalId=:logicalId';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byTypeAndLogicalId($_type, $_logicalId) {
        $values = array(
            'logicalId' => $_logicalId,
            'type' => $_type,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM `update`
                WHERE logicalId=:logicalId
                    AND type=:type';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     *
     * @return array de tous les utilisateurs 
     */
    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . ' 
                FROM `update`';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    /*     * *********************Methode d'instance************************* */

    public function checkUpdate() {
        try {
            $market = market::byLogicalId($this->getLogicalId());
            $this->setRemoteVersion($market->getDatetime());
            $market_info = market::getInfo($market->getLogicalId());
            $this->setStatus($market_info['status']);
            $this->save();
        } catch (Exception $ex) {
            
        }
    }

    public function preSave() {
        if ($this->getLogicalId() == '') {
            throw new Exception(__('Le logical ID ne peut etre vide', __FILE__));
        }
        if ($this->getLocalVersion() == '') {
            throw new Exception(__('La version locale ne peut etre vide', __FILE__));
        }
        if ($this->getName() == '') {
            $this->setName($this->getLogicalId());
        }
    }

    public function save() {
        return DB::save($this);
    }

    public function remove() {
        return DB::remove($this);
    }

    public function update() {
        
    }

    public function delete() {
        
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
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

    public function setName($name) {
        $this->name = $name;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setConfiguration($_key, $_value) {
        $this->configuration = utils::setJsonAttr($this->configuration, $_key, $_value);
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getLocalVersion() {
        return $this->localVersion;
    }

    public function getRemoteVersion() {
        return $this->remoteVersion;
    }

    public function setLocalVersion($localVersion) {
        $this->localVersion = $localVersion;
    }

    public function setRemoteVersion($remoteVersion) {
        $this->remoteVersion = $remoteVersion;
    }

    public function getLogicalId() {
        return $this->logicalId;
    }

    public function setLogicalId($logicalId) {
        $this->logicalId = $logicalId;
    }

}

?>
