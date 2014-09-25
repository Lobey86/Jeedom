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
if (trim(config::byKey('api')) == '') {
    echo 'Vous n\'avez aucune clef API de configurer, veuillez d\'abord en générer une (Page Générale -> Administration -> Configuration';
    log::add('jeeEvent', 'error', 'Vous n\'avez aucune clef API de configurer, veuillez d\'abord en générer une (Page Générale -> Administration -> Configuration');
    die();
}
if ((init('apikey') != '' || init('api') != '') && init('type') != '') {
    try {
        if (config::byKey('api') != init('apikey') && config::byKey('api') != init('api')) {
            connection::failed();
            throw new Exception('Clef API non valide, vous n\'etes pas autorisé à effectuer cette action (jeeApi). Demande venant de :' . getClientIp() . 'Clef API : ' . init('apikey') . init('api') . ' != ' . config::byKey('api'));
        }
        connection::success('api');
        $type = init('type');
        if ($type == 'cmd') {
            $cmd = cmd::byId(init('id'));
            if (!is_object($cmd)) {
                throw new Exception('Aucune commande correspondant à l\'id : ' . init('id'));
            }
            log::add('api', 'debug', 'Exécution de : ' . $cmd->getHumanName());
            echo $cmd->execCmd($_REQUEST);
        } else if ($type == 'interact') {
            echo interactQuery::tryToReply(init('query'));
        } else if ($type == 'scenario') {
            $scenario = scenario::byId(init('id'));
            if (!is_object($scenario)) {
                throw new Exception('Aucun scénario correspondant à l\'id : ' . init('id'));
            }
            switch (init('action')) {
                case 'start':
                    log::add('api', 'debug', 'Start scénario de : ' . $scenario->getHumanName());
                    $scenario->launch(false, __('Lancement provoque par un appel api ', __FILE__));
                    break;
                case 'stop':
                    log::add('api', 'debug', 'Stop scénario de : ' . $scenario->getHumanName());
                    $scenario->stop();
                    break;
                case 'deactivate':
                    log::add('api', 'debug', 'Activation scénario de : ' . $scenario->getHumanName());
                    $scenario->setIsActive(0);
                    $scenario->save();
                    break;
                case 'activate':
                    log::add('api', 'debug', 'Désactivation scénario de : ' . $scenario->getHumanName());
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


        if (isset($params['apikey']) || isset($params['api'])) {
            if (config::byKey('api') == '' || (config::byKey('api') != $params['apikey'] && config::byKey('api') != $params['api'])) {
                if (config::byKey('market::jeedom_apikey') == '' || config::byKey('market::jeedom_apikey') != $params['apikey'] || $_SERVER['REMOTE_ADDR'] != '94.23.188.164') {
                    connection::failed();
                    throw new Exception('Clef API invalide', -32001);
                }
            }
        } else if (isset($params['username']) && isset($params['password'])) {
            $user = user::connect($params['username'], $params['password']);
            if (!is_object($user) || $user->getRights('admin') != 1) {
                connection::failed();
                throw new Exception('Nom d\'utilisateur ou mot de passe invalide', -32001);
            }
        } else {
            connection::failed();
            throw new Exception('Aucune clef API ou nom d\'utilisateur', -32001);
        }

        connection::success('api');

        if ($params['plugin'] != '') {
            include_file('core', $params['plugin'], 'api', $params['plugin']);
        } else {
            /*             * ***********************Ping********************************* */
            if ($jsonrpc->getMethod() == 'ping') {
                $jsonrpc->makeSuccess('pong');
            }

            /*             * ***********************Get API Key********************************* */
            if ($jsonrpc->getMethod() == 'getApiKey' && config::byKey('market::jeedom_apikey') == $params['apikey']) {
                market::validateTicket($params['ticket']);
                $jsonrpc->makeSuccess(config::byKey('api'));
            }

            /*             * ***********************Version********************************* */
            if ($jsonrpc->getMethod() == 'version') {
                $jsonrpc->makeSuccess(getVersion('jeedom'));
            }

            /*             * ************************Plugin*************************** */
            if ($jsonrpc->getMethod() == 'plugin::listPlugin') {
                $activateOnly = (isset($params['activateOnly']) && $params['activateOnly'] == 1) ? true : false;
                $orderByCaterogy = (isset($params['orderByCaterogy']) && $params['orderByCaterogy'] == 1) ? true : false;
                $jsonrpc->makeSuccess(utils::o2a(plugin::listPlugin($activateOnly, $orderByCaterogy)));
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

            if ($jsonrpc->getMethod() == 'eqLogic::byType') {
                $jsonrpc->makeSuccess(utils::o2a(eqLogic::byType($params['type'])));
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

            if ($jsonrpc->getMethod() == 'eqLogic::save') {
                $typeEqLogic = $params['eqType_name'];
                $typeCmd = $typeEqLogic . 'Cmd';
                if ($typeEqLogic == '' || !class_exists($typeEqLogic) || !class_exists($typeCmd)) {
                    throw new Exception(__('Type incorrect (classe commande inexistante)', __FILE__) . $typeCmd);
                }
                $eqLogic = null;
                if (isset($params['id'])) {
                    $eqLogic = $typeEqLogic::byId($params['id']);
                }
                if (!is_object($eqLogic)) {
                    $eqLogic = new $typeEqLogic();
                    $eqLogic->setEqType_name($params['eqType_name']);
                }
                if (method_exists($eqLogic, 'preAjax')) {
                    $eqLogic->preAjax();
                }
                utils::a2o($eqLogic, jeedom::fromHumanReadable($params));
                $eqLogic->save();
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
                $jsonrpc->makeSuccess(utils::o2a(scenario::all()));
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
                throw new Exception('La paramètre "state" ne peut être vide et doit avoir pour valuer [run,stop,enable;disable]');
            }


            /*             * ************************JeeNetwork*************************** */
            if ($jsonrpc->getMethod() == 'jeeNetwork::handshake') {
                if (config::byKey('jeeNetwork::mode') != 'slave') {
                    throw new Exception('Impossible d\'ajouter une box jeedom non esclave à un reseau Jeedom');
                }
                $auiKey = config::byKey('auiKey');
                if ($uiaKey == '') {
                    $auiKey = config::genKey(255);
                    config::save('auiKey', $auiKey);
                }
                $return = array(
                    'mode' => config::byKey('jeeNetwork::mode'),
                    'nbUpdate' => update::nbNeedUpdate(),
                    'version' => getVersion('jeedom'),
                    'nbMessage' => message::nbMessage(),
                    'auiKey' => $auiKey
                );
                foreach (plugin::listPlugin(true) as $plugin) {
                    if ($plugin->getAllowRemote() == 1) {
                        $return['plugin'][] = $plugin->getId();
                    }
                }
                $address = (isset($params['address']) && $params['address'] != '') ? $params['address'] : getClientIp();
                config::save('jeeNetwork::master::ip', $address);
                config::save('jeeNetwork::master::apikey', $params['apikey_master']);
                if (config::byKey('internalAddr') == '') {
                    config::save('internalAddr', $params['slave_ip']);
                }
                $jsonrpc->makeSuccess($return);
            }

            if ($jsonrpc->getMethod() == 'jeeNetwork::reload') {
                foreach (plugin::listPlugin(true) as $plugin) {
                    try {
                        $plugin->launch('slaveReload');
                    } catch (Exception $ex) {
                        
                    }
                }
                $jsonrpc->makeSuccess('ok');
            }

            if ($jsonrpc->getMethod() == 'jeeNetwork::halt') {
                jeedom::haltSystem();
                $jsonrpc->makeSuccess('ok');
            }

            if ($jsonrpc->getMethod() == 'jeeNetwork::reboot') {
                jeedom::rebootSystem();
                $jsonrpc->makeSuccess('ok');
            }

            if ($jsonrpc->getMethod() == 'jeeNetwork::update') {
                jeedom::update('', 0);
                $jsonrpc->makeSuccess('ok');
            }

            if ($jsonrpc->getMethod() == 'jeeNetwork::checkUpdate') {
                update::checkAllUpdate();
                $jsonrpc->makeSuccess('ok');
            }

            /*             * ************************Log*************************** */
            if ($jsonrpc->getMethod() == 'log::get') {
                $jsonrpc->makeSuccess(log::get($params['log'], $params['start'], $params['nbLine']));
            }

            if ($jsonrpc->getMethod() == 'log::list') {
                $jsonrpc->makeSuccess(log::liste());
            }

            if ($jsonrpc->getMethod() == 'log::empty') {
                $jsonrpc->makeSuccess(log::clear($params['log']));
            }

            if ($jsonrpc->getMethod() == 'log::remove') {
                $jsonrpc->makeSuccess(log::remove($params['log']));
            }

            /*             * ************************Messages*************************** */
            if ($jsonrpc->getMethod() == 'message::removeAll') {
                $jsonrpc->makeSuccess(message::removeAll());
            }

            if ($jsonrpc->getMethod() == 'message::all') {
                $jsonrpc->makeSuccess(utils::o2a(message::all()));
            }

            /*             * ************************Interact*************************** */
            if ($jsonrpc->getMethod() == 'interact::tryToReply') {
                $jsonrpc->makeSuccess(interactQuery::tryToReply(init('query')));
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
