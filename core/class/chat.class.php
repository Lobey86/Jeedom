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

class chat {
    /*     * *************************Attributs****************************** */

    private $id;
    private $user_id;
    private $datetime;

    /*     * ***********************Methode static*************************** */

    public static function all() {
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM chat';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function removeAll() {
        $sql = 'DELETE FROM chat';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byUserId($_user_id) {
        $value = array(
            'user_id' => $_user_id,
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM chat
                WHERE user_id=:user_id';
        return DB::Prepare($sql, $value, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function getUsersList() {
        $return = array();
        $jeedomBot = array(
            'Id' => 0,
            'Name' => 'Jeedom',
            'ProfilePictureUrl' => 'core/img/jeedom_ico_chat.png',
            'Status' => 1,
        );
        $return[] = $jeedomBot;
        foreach (user::all() as $user) {
            $chat = chat::byUserId($user->getId());
            $user_info = array(
                'Id' => $user->getId(),
                'Name' => $user->getLogin(),
                'ProfilePictureUrl' => 'core/img/noPicture.gif',
                'Status' => (is_object($chat)) ? 1 : 0,
            );
            $return[] = $user_info;
        }
        return $return;
    }

    public static function getUserInfo($_user_id) {
        $user_info = array();
        if ($_user_id == 0) {
            $user_info = array(
                'Id' => 0,
                'Name' => 'Jeedom',
                'ProfilePictureUrl' => 'core/img/jeedom_ico_chat.png',
                'Status' => 1,
            );
        } else {
            $user = user::byId($_user_id);
            if (is_object($user)) {
                $chat = chat::byUserId($user->getId());
                $user_info = array(
                    'Id' => $_user_id,
                    'Name' => $user->getLogin(),
                    'ProfilePictureUrl' => 'core/img/noPicture.gif',
                    'Status' => (is_object($chat)) ? 1 : 0,
                );
            } else {
                $user_info = array(
                    'Id' => $_user_id,
                    'Name' => 'Error',
                    'ProfilePictureUrl' => 'core/img/noPicture.gif',
                    'Status' => 0,
                );
            }
        }
        return $user_info;
    }

    public static function sendMessage($_userFromId, $_userDestId, $_message) {
        $chatHistory = new chatHistory();
        $chatHistory->setFrom($_userFromId);
        $chatHistory->setTo($_userDestId);
        $chatHistory->setMessage($_message);
        $chatHistory->setDatetime(date('Y-m-d H:i:s'));
        $chatHistory->save();
        if ($_userDestId == 0) {
            $parameters = array();
            $user = user::byId($_userFromId);
            if (is_object($user)) {
                $parameters['profile'] = $user->getLogin();
            }
            $reply = interactQuery::tryToReply($_message, $parameters);
            $chatHistory = new chatHistory();
            $chatHistory->setFrom($_userDestId);
            $chatHistory->setTo($_userFromId);
            $chatHistory->setMessage($reply);
            $chatHistory->setDatetime(date('Y-m-d H:i:s'));
            $chatHistory->save();
            nodejs::pushChatMessage($_userDestId, $_userFromId, $reply);
        } else {
            nodejs::pushChatMessage($_userFromId, $_userDestId, $_message);
        }
    }

    public static function forceRefreshUserList() {
        nodejs::pushUpdate('refreshUserList', '');
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

    public function getUser_id() {
        return $this->user_id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

}

?>
