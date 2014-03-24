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

class nodejs {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    public static function pushNotification($_title, $_text, $_category = '') {
        $url = self::baseUrl() . 'type=notify&category=' . $_category . '&title=' . urlencode($_title) . '&text=' . urlencode($_text);
        try {
            self::send($url);
        } catch (Exception $e) {
            
        }
        return true;
    }

    public static function pushUpdate($_event, $_option) {
        if (is_object($_option) || is_array($_option)) {
            $_option = json_encode($_option);
        }
        $url = self::baseUrl() . 'type=' . urlencode($_event) . '&options=' . urlencode($_option);
        try {
            self::send($url);
        } catch (Exception $e) {
            
        }
    }

    public static function pushChatMessage($_userFromId, $_userDestId, $_message) {
        $url = self::baseUrl() . 'type=newChatMessage&userFromId=' . urlencode($_userFromId) . '&userDestId=' . urlencode($_userDestId) . '&message=' . urlencode($_message);
        try {
            self::send($url);
        } catch (Exception $e) {
            
        }
    }

    public static function updateKey() {
        config::save('nodeJsKey', config::genKey());
    }

    private static function baseUrl() {
        if (config::byKey('nodeJsKey') == '') {
            config::save('nodeJsKey', config::genKey());
        }
        return '127.0.0.1:' . config::byKey('nodeJsInternalPort') . '?key=' . urlencode(config::byKey('nodeJsKey')) . '&';
    }

    private static function send($_url) {
        if (config::byKey('enableNodeJs') == 1) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $_url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_HEADER, false);
            curl_setopt($c, CURLOPT_TIMEOUT, 1);
            $output = curl_exec($c);
            if ($output === false) {
                throw new Exception(curl_error($c));
            }
            curl_close($c);
        }
        return true;
    }

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}

?>
