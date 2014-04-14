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

try {
    require_once dirname(__FILE__) . '/../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect()) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'sendMessage') {
        chat::sendMessage(init('userFromId'), init('userDestId'), init('message'));
        ajax::success();
    }

    if (init('action') == 'getUserList') {
        ajax::success(chat::getUsersList());
    }

    if (init('action') == 'getUserHistory') {
        ajax::success(chatHistory::getChatUserHistory($_SESSION['user']->getId(), init('otherUserId')));
    }

    if (init('action') == 'refreshConnectUser') {
        chat::removeAll();
        $connectUserList = json_decode(init('connectUserList'), true);
        foreach ($connectUserList as $connectUser) {
            $chat = chat::byUserId($connectUser);
            if (!is_object($chat)) {
                $chat = new chat();
                $chat->setUser_id($connectUser);
            }
            $chat->setDatetime(date('Y-m-d H:i:s'));
            $chat->save();
        }
        chat::forceRefreshUserList();
        ajax::success();
    }

    if (init('action') == 'getUserInfo') {
        ajax::success(chat::getUserInfo(init('user_id')));
    }

    throw new Exception(__('Aucune methode correspondante à : ',__FILE__). init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
