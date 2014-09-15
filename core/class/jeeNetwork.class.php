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
    private $status;

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

    public static function changeMode($_mode) {
        switch ($_mode) {
            case 'master':
                if (config::byKey('jeeNetwork::mode') != 'master') {
                    $cron = new cron();
                    $cron->setClass('history');
                    $cron->setFunction('historize');
                    $cron->setSchedule('*/5 * * * * *');
                    $cron->setTimeout(5);
                    $cron->save();
                    $cron = new cron();
                    $cron->setClass('scenario');
                    $cron->setFunction('check');
                    $cron->setSchedule('* * * * * *');
                    $cron->setTimeout(5);
                    $cron->save();
                    $cron = new cron();
                    $cron->setClass('cmd');
                    $cron->setFunction('collect');
                    $cron->setSchedule('*/5 * * * * *');
                    $cron->setTimeout(5);
                    $cron->save();
                    $cron = new cron();
                    $cron->setClass('history');
                    $cron->setFunction('archive');
                    $cron->setSchedule('00 * * * * *');
                    $cron->setTimeout(20);
                    $cron->save();
                    config::save('jeeNetwork::mode', 'master');
                }
                break;
            case 'slave':
                if (config::byKey('jeeNetwork::mode') != 'slave') {
                    foreach (eqLogic::all() as $eqLogic) {
                        $eqLogic->remove();
                    }
                    foreach (object::all() as $object) {
                        $object->remove();
                    }
                    foreach (update::all() as $update) {
                        switch ($update->getType()) {
                            case 'core':
                                break;
                            case 'plugin':
                                $plugin = plugin::byId($update->getLogicalId());
                                if (is_object($plugin) && $plugin->getAllowRemote() != 1) {
                                    $update->deleteObjet();
                                }
                                break;
                            default :
                                $plugin = plugin::byId($update->getType());
                                if (is_object($plugin) && $plugin->getAllowRemote() != 1) {
                                    $update->deleteObjet();
                                }
                                break;
                        }
                    }
                    foreach (view::all() as $view) {
                        $view->remove();
                    }
                    foreach (plan::all() as $plan) {
                        $plan->remove();
                    }
                    foreach (scenario::all() as $scenario) {
                        $scenario->remove();
                    }
                    foreach (listener::all() as $listener) {
                        $listener->remove();
                    }
                    $cron = cron::byClassAndFunction('history', 'historize');
                    if (is_object($cron)) {
                        $cron->remove();
                    }
                    $cron = cron::byClassAndFunction('scenario', 'check');
                    if (is_object($cron)) {
                        $cron->remove();
                    }
                    $cron = cron::byClassAndFunction('cmd', 'collect');
                    if (is_object($cron)) {
                        $cron->remove();
                    }
                    $cron = cron::byClassAndFunction('history', 'archive');
                    if (is_object($cron)) {
                        $cron->remove();
                    }
                    config::save('jeeNetwork::mode', 'slave');
                }
                break;
        }
    }

    public static function pull() {
        foreach (self::all() as $jeeNetwork) {
            if ($jeeNetwork->getStatus() == 'ok') {
                try {
                    $jeeNetwork->handshake();
                    $jeeNetwork->save();
                } catch (Exception $e) {
                    log::add('jeeNetwork', 'error', $e->getMessage());
                }
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        if ($this->getIp() == '') {
            throw new Exception('L\'adresse IP ne peut etre vide');
        }
        if ($this->getApikey() == '') {
            throw new Exception('La clef API ne peut etre vide');
        }
        $this->handshake();
    }

    public function save() {
        return DB::save($this);
    }

    public function remove() {
        return DB::remove($this);
    }

    public function handshake($_reload = false) {
        $jsonrpc = $this->getJsonRpc();
        $params = array(
            'apikey_master' => config::byKey('api'),
            'address' => config::byKey('internalAddr'),
            'slave_ip' => $this->getRealIp(),
            'reload' => $_reload
        );
        if ($jsonrpc->sendRequest('jeeNetwork::handshake', $params)) {
            $result = $jsonrpc->getResult();
            $this->setStatus('ok');
            $this->setPlugin($result['plugin']);
            $this->setConfiguration('nbUpdate', $result['nbUpdate']);
            $this->setConfiguration('version', $result['version']);
            $this->setConfiguration('auiKey', $result['auiKey']);
            $this->setConfiguration('lastCommunication', date('Y-m-d H:i:s'));
            if ($this->getConfiguration('nbMessage') != $result['nbMessage'] && $result['nbMessage'] > 0) {
                log::add('jeeNetwork', 'error', __('Le jeedom esclave : ', __FILE__) . $this->getName() . __(' à de nouveau message : ', __FILE__) . $result['nbMessage']);
            }
            $this->setConfiguration('nbMessage', $result['nbMessage']);
        } else {
            $this->setStatus('erreur');
            throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
        }
    }

    public function reload() {
        $jsonrpc = $this->getJsonRpc();
        if (!$jsonrpc->sendRequest('jeeNetwork::reload', array())) {
            throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
        }
    }

    public function halt() {
        $jsonrpc = $this->getJsonRpc();
        if (!$jsonrpc->sendRequest('jeeNetwork::halt', array())) {
            throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
        }
    }

    public function reboot() {
        $jsonrpc = $this->getJsonRpc();
        if (!$jsonrpc->sendRequest('jeeNetwork::reboot', array())) {
            throw new Exception($jsonrpc->getError(), $jsonrpc->getErrorCode());
        }
    }

    public function getJsonRpc() {
        if ($this->getIp() == '') {
            throw new Exception(__('Aucune addresse IP de renseignée pour : ', __FILE__) . $this->getName());
        }
        return new jsonrpcClient($this->getIp() . '/core/api/jeeApi.php', $this->getApikey());
    }

    public function getRealIp() {
        $ip = $this->getIp();
        $pos = strpos($ip, '/');
        if ($pos > 0) {
            $ip = substr($ip, 0, $pos);
        }
        return $ip;
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

    public function getPlugin() {
        return json_decode($this->plugin, true);
    }

    public function setPlugin($plugins) {
        $this->plugin = json_encode($plugins, JSON_UNESCAPED_UNICODE);
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

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

}
