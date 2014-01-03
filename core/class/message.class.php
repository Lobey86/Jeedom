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

class message {
    /*     * *************************Attributs****************************** */

    private $id;
    private $date;
    private $logicalId;
    private $module;
    private $message;
    private $action;

    /*     * ***********************Methode static*************************** */

    public static function add($_type, $_message, $_action = '', $_logicalId = '') {
        $message = new message();
        $message->setModule($_type);
        $message->setMessage($_message);
        $message->setAction($_action);
        $message->setDate(date('Y-m-d H:i:m'));
        $message->setLogicalId($_logicalId);
        $message->save();
        @nodejs::pushNotification('Message de ' . $_type, $_message, 'message');
    }

    public static function removeAll($_module = '') {
        $values = array();
        if ($_module != '') {
            $values['module'] = $_module;
            $sql = 'DELETE FROM message
                    WHERE module=:module';
        } else {
            $sql = 'DELETE FROM message';
        }
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
    }

    public static function nbMessage() {
        $sql = 'SELECT count(*) 
                FROM message';
        $count = DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
        return $count['count(*)'];
    }

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM message
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byModuleLogicalId($_module,$_logicalId) {
        $values = array(
            'logicalId' => $_logicalId,
            'module' => $_module
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM message
                WHERE logicalId=:logicalId
                    AND module=:module';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byModule($_module) {
        $values = array(
            'module' => $_module
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM message
                WHERE module=:module
                ORDER BY date DESC';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function listModule() {
        $sql = 'SELECT DISTINCT(module)
                FROM message';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM message
                ORDER BY date DESC';
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

    public function getDate() {
        return $this->date;
    }

    public function getModule() {
        return $this->module;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getAction() {
        return $this->action;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getLogicalId() {
        return $this->logicalId;
    }

    public function setLogicalId($logicalId) {
        $this->logicalId = $logicalId;
    }

}

?>
