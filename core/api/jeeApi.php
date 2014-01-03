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

require_once dirname(__FILE__) . "/../php/core.inc.php";
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            $_REQUEST[$argList[0]] = $argList[1];
        }
    }
}

if (init('api') != '' && init('type') != '') {
    if (config::byKey('api') != init('api')) {
        throw new Exception('Clef API non valide, vous n\'etez pas autorisé à effectuer cette action (jeeApi)');
    }
    try {
        $type = init('type');
        if (class_exists($type)) {
            if (method_exists($type, 'event')) {
                $type::event();
            } else {
                throw new Exception('Aucune methode correspondante : ' . $type . '::event()');
            }
        } else {
            throw new Exception('Aucune module correspondant : ' . $type);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        log::add('jeeEvent', 'error', $e->getMessage());
    }
    die();
} else {
    try {

        $IP = getClientIp();
        log::add('api', 'info', init('request') . ' - IP :' . $IP);

        $jsonrpc = new jsonrpc(init('request'));

        if (!mySqlIsHere()) {
            throw new Exception('Mysql non lancé', -32001);
        }

        if ($jsonrpc->getJsonrpc() != '2.0') {
            throw new Exception('Requete invalide', -32001);
        }

        $params = $jsonrpc->getParams();

        if (config::byKey('api') != $params['api']) {
            throw new Exception('Clef API invalide', -32001);
        }

        if ($params['module'] != '') {
            include_file('core', $params['module'], 'api', $params['module']);
        } else {
            /*             * ***********************Ping********************************* */
            if ($jsonrpc->getMethod() == 'ping') {
                $jsonrpc->makeSuccess('pong');
            }

            /*             * ***********************Version********************************* */
            if ($jsonrpc->getMethod() == 'version') {
                $jsonrpc->makeSuccess(VERSION);
            }

            /*             * ************************Executer une commande*************************** */
            if ($jsonrpc->getMethod() == 'execCmd') {
                if ($params['cmdId'] == '') {
                    throw new Exception('Commande ID invalide', -32602);
                }
                $cmd = cmd::byId($params['cmdId']);
                if (!is_object($cmd)) {
                    throw new Exception('Commande introuvable', -32603);
                }
                $jsonrpc->makeSuccess($cmd->execCmd($params));
            }

            /*             * ************************************************************************ */
        }
        throw new Exception('Methode non trouvée', -32601);
        /*         * *********Catch exeption*************** */
    } catch (Exception $e) {
        $message = $e->getMessage();
        $jsonrpc = new jsonrpc(init('request'));
        $errorCode = (is_numeric($e->getCode())) ? -32000 - $e->getCode() : -32699;
        $jsonrpc->makeError($errorCode, $message);
    }
}
?>
