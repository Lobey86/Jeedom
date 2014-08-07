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

if ((init('apikey') != '' || init('api') != '') && init('type') != '') {
    try {
        if (config::byKey('api') != init('apikey') && config::byKey('api') != init('api')) {
            connection::failed();
            throw new Exception('Clef API non valide, vous n\'etes pas autorisé à effectuer cette action (jeeApi)');
        }
        connection::success('api');
        $type = init('type');
        if ($type == 'cmd') {
            $cmd = cmd::byId(init('id'));
            if (!is_object($cmd)) {
                throw new Exception('Aucune commande correspondant à l\'id : ' . init('id'));
            }
            echo $cmd->execCmd($_REQUEST);
        } else if ($type == 'scenario') {
            $scenario = scenario::byId(init('id'));
            if (!is_object($scenario)) {
                throw new Exception('Aucun scénario correspondant à l\'id : ' . init('id'));
            }
            switch (init('action')) {
                case 'start':
                    $scenario->launch(false, __('Lancement provoque par un appel api ', __FILE__));
                    break;
                case 'stop':
                    $scenario->stop();
                    break;
                case 'deactivate':
                    $scenario->setIsActive(0);
                    $scenario->save();
                    break;
                case 'activate':
                    $scenario->setIsActive(1);
                    $scenario->save();
                    break;
                default :
                    throw new Exception('Action non trouvée ou invalide [start,stop,deactivate,activate]');
            }
            echo 'ok';
        } else {
            if (class_exists($type)) {
                if (method_exists($type, 'event')) {
                    log::add('api', 'info', 'Appels de ' . $type . '::event()');
                    $type::event();
                } else {
                    throw new Exception('Aucune methode correspondante : ' . $type . '::event()');
                }
            } else {
                throw new Exception('Aucune plugin correspondant : ' . $type);
            }
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
            throw new Exception('Requete invalide. Jsonrpc version invalide : ' . $jsonrpc->getJsonrpc(), -32001);
        }

        $params = $jsonrpc->getParams();

        if (config::byKey('api') != $params['apikey'] && config::byKey('api') != $params['api']) {
            connection::failed();
            throw new Exception('Clef API invalide', -32001);
        }
        connection::success('api');

        if ($params['plugin'] != '') {
            include_file('core', $params['plugin'], 'api', $params['plugin']);
        } else {
            /*             * ***********************Ping********************************* */
            if ($jsonrpc->getMethod() == 'ping') {
                $jsonrpc->makeSuccess('pong');
            }

            /*             * ***********************Version********************************* */
            if ($jsonrpc->getMethod() == 'version') {
                $jsonrpc->makeSuccess(getVersion('jeedom'));
            }

            /*             * ************************Object*************************** */
            if ($jsonrpc->getMethod() == 'object::all') {
                $jsonrpc->makeSuccess(utils::o2a(object::all()));
            }

            if ($jsonrpc->getMethod() == 'object::byId') {
                $object = object::byId($params['id']);
                if (!is_object($object)) {
                    throw new Exception('Objet introuvable : ' . $params['id'], -32601);
                }
                $jsonrpc->makeSuccess(utils::o2a($object));
            }

            /*             * ************************Equipement*************************** */
            if ($jsonrpc->getMethod() == 'eqLogic::all') {
                $jsonrpc->makeSuccess(utils::o2a(eqLogic::all()));
            }

            if ($jsonrpc->getMethod() == 'eqLogic::byObjectId') {
                $jsonrpc->makeSuccess(utils::o2a(eqLogic::byObjectId($params['object_id'])));
            }

            if ($jsonrpc->getMethod() == 'eqLogic::byId') {
                $eqLogic = eqLogic::byId($params['id']);
                if (!is_object($eqLogic)) {
                    throw new Exception('EqLogic introuvable : ' . $params['id'], -32602);
                }
                $jsonrpc->makeSuccess(utils::o2a($eqLogic));
            }

            /*             * ************************Commande*************************** */
            if ($jsonrpc->getMethod() == 'cmd::all') {
                $jsonrpc->makeSuccess(utils::o2a(cmd::all()));
            }

            if ($jsonrpc->getMethod() == 'cmd::byEqLogicId') {
                $jsonrpc->makeSuccess(utils::o2a(cmd::byEqLogicId($params['eqLogic_id'])));
            }

            if ($jsonrpc->getMethod() == 'cmd::byId') {
                $cmd = cmd::byId($params['id']);
                if (!is_object($cmd)) {
                    throw new Exception('Cmd introuvable : ' . $params['id'], -32701);
                }
                $jsonrpc->makeSuccess(utils::o2a($cmd));
            }

            if ($jsonrpc->getMethod() == 'cmd::execCmd') {
                $cmd = cmd::byId($params['id']);
                if (!is_object($cmd)) {
                    throw new Exception('Cmd introuvable : ' . $params['id'], -32702);
                }
                $jsonrpc->makeSuccess($cmd->execCmd($params['options']));
            }

            if ($jsonrpc->getMethod() == 'cmd::getStatistique') {
                $cmd = cmd::byId($params['id']);
                if (!is_object($cmd)) {
                    throw new Exception('Cmd introuvable : ' . $params['id'], -32702);
                }
                $jsonrpc->makeSuccess($cmd->getStatistique($params['startTime'], $params['endTime']));
            }

            if ($jsonrpc->getMethod() == 'cmd::getTendance') {
                $cmd = cmd::byId($params['id']);
                if (!is_object($cmd)) {
                    throw new Exception('Cmd introuvable : ' . $params['id'], -32702);
                }
                $jsonrpc->makeSuccess($cmd->getTendance($params['startTime'], $params['endTime']));
            }

            if ($jsonrpc->getMethod() == 'cmd::getHistory') {
                $cmd = cmd::byId($params['id']);
                if (!is_object($cmd)) {
                    throw new Exception('Cmd introuvable : ' . $params['id'], -32702);
                }
                $jsonrpc->makeSuccess($cmd->getHistory($params['startTime'], $params['endTime']));
            }

            /*             * ************************Scénario*************************** */
            if ($jsonrpc->getMethod() == 'scenario::all') {
                $jsonrpc->makeSuccess(scenario::all());
            }

            if ($jsonrpc->getMethod() == 'scenario::byId') {
                $scenario = scenario::byId($params['id']);
                if (!is_object($scenario)) {
                    throw new Exception('Scenario introuvable : ' . $params['id'], -32703);
                }
                $jsonrpc->makeSuccess(utils::o2a($scenario));
            }

            if ($jsonrpc->getMethod() == 'scenario::changeSate') {
                $scenario = cmd::byId($params['id']);
                if (!is_object($scenario)) {
                    throw new Exception('Scenario introuvable : ' . $params['id'], -32702);
                }
                if ($params['state'] == 'stop') {
                    $jsonrpc->makeSuccess($scenario->stop());
                }
                if ($params['state'] == 'run') {
                    $jsonrpc->makeSuccess($scenario->launch(false, __('Scenario lance sur appels API', __FILE__)));
                }
                if ($params['state'] == 'enable') {
                    $scenario->setIsActive(1);
                    $jsonrpc->makeSuccess($scenario->save());
                }
                if ($params['state'] == 'disable') {
                    $scenario->setIsActive(0);
                    $jsonrpc->makeSuccess($scenario->save());
                }
                throw new Exception('La paramètre "state" ne peut etre vide et doit avoir pour valuer [run,stop,enable;disable]');
            }

            /*             * ************************************************************************ */
        }
        throw new Exception('Aucune méthode correspondante : ' . $jsonrpc->getMethod(), -32500);
        /*         * *********Catch exeption*************** */
    } catch (Exception $e) {
        $message = $e->getMessage();
        $jsonrpc = new jsonrpc(init('request'));
        $errorCode = (is_numeric($e->getCode())) ? -32000 - $e->getCode() : -32599;
        $jsonrpc->makeError($errorCode, $message);
    }
}
?>
