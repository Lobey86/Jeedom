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

class chatHistory {
    /*     * *************************Attributs****************************** */

    private $id;
    private $from;
    private $to;
    private $message;
    private $datetime;

    /*     * ***********************Methode static*************************** */

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM chatHistory';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function allByUserId($_user_id,$_otherUserId) {
        $value = array(
            'user_id' => $_user_id,
            'otherUserId' => $_otherUserId,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM (SELECT ' . DB::buildField(__CLASS__) . ' FROM chatHistory
                WHERE (`to`=:user_id AND `from`=:otherUserId)
                OR (`from`=:user_id AND `to`=:otherUserId)
                    ORDER BY `datetime` DESC
                    LIMIT 10) as hc ORDER BY `datetime` ASC';
        return DB::Prepare($sql, $value, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function getChatUserHistory($_user_id, $_otherUserId) {
        $return = array();
        $chatHistory_list = self::allByUserId($_user_id, $_otherUserId);
        foreach ($chatHistory_list as $chatHistory) {
            $info_message = array(
                'UserFromId' => $chatHistory->getFrom(),
                'OtherUserId' => $chatHistory->getTo(),
                'Message' => $chatHistory->getMessage(),
            );
            $return[] = $info_message;
        }
        return $return;
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

    public function getFrom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

}

?>
