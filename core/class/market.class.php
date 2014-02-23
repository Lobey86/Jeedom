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

class market {
    /*     * *************************Attributs****************************** */

    private $id;
    private $name;
    private $type;
    private $datetime;
    private $description;
    private $categorie;
    private $changelog;
    private $version;
    private $user_id;
    private $downloaded;
    private $status;
    private $author;
    private $logicalId;
    private $api_author;

    /*     * ***********************Methode static*************************** */

    private static function construct($_arrayMarket) {
        $market = new market();
        if (!isset($_arrayMarket['id'])) {
            return;
        }
        $market->setId($_arrayMarket['id']);
        $market->setName($_arrayMarket['name']);
        $market->setType($_arrayMarket['type']);
        $market->setDatetime($_arrayMarket['datetime']);
        $market->setDescription($_arrayMarket['description']);
        $market->setDownloaded($_arrayMarket['downloaded']);
        $market->setUser_id($_arrayMarket['user_id']);
        $market->setVersion($_arrayMarket['version']);
        $market->setCategorie($_arrayMarket['categorie']);
        $market->setStatus($_arrayMarket['status']);
        $market->setAuthor($_arrayMarket['author']);
        $market->setChangelog($_arrayMarket['changelog']);
        $market->setLogicalId($_arrayMarket['logicalId']);
        if (!isset($_arrayMarket['api_author'])) {
            $_arrayMarket['api_author'] = null;
        }
        $market->setApi_author($_arrayMarket['api_author']);
        return $market;
    }

    public static function byId($_id) {
        $market = market::getJsonRpc();
        if ($market->sendRequest('market::byId', array('id' => $_id))) {
            return self::construct($market->getResult());
        } else {
            throw new Exception($market->getError());
        }
    }

    public static function byLogicalId($_logicalId) {
        $market = market::getJsonRpc();
        if ($market->sendRequest('market::byLogicalId', array('logicalId' => $_logicalId))) {
            return self::construct($market->getResult());
        } else {
            throw new Exception($market->getError());
        }
    }

    public static function byMe() {
        $market = market::getJsonRpc();
        if ($market->sendRequest('market::byAuthor', array())) {
            $return = array();
            foreach ($market->getResult() as $result) {
                $return[] = self::construct($result);
            }
            return $return;
        } else {
            throw new Exception($market->getError());
        }
    }

    public static function byStatusAndType($_status, $_type) {
        $market = market::getJsonRpc();
        if ($market->sendRequest('market::byStatusAndType', array('status' => $_status, 'type' => $_type))) {
            $return = array();
            foreach ($market->getResult() as $result) {
                $return[] = self::construct($result);
            }
            return $return;
        } else {
            throw new Exception($market->getError());
        }
    }

    public static function byStatus($_status) {
        $market = market::getJsonRpc();
        if ($market->sendRequest('market::byStatus', array('status' => $_status))) {
            $return = array();
            foreach ($market->getResult() as $result) {
                $return[] = self::construct($result);
            }
            return $return;
        } else {
            throw new Exception($market->getError());
        }
    }

    public static function getJsonRpc() {
        return new jsonrpcClient(config::byKey('market::address') . '/core/api/api.php', config::byKey('market::apikey'));
    }

    public static function getInfo($_logicalId) {
        $return = array();
        if ($_logicalId == '') {
            $return['market'] = 0;
            $return['market_owner'] = 0;
            $return['status'] = 'ok';
            return $return;
        }

        if (config::byKey('market::apikey') !== '') {
            $return['market_owner'] = 1;
        } else {
            $return['market_owner'] = 0;
        }
        $return['market'] = 0;
        $updateDateTime = config::byKey('installVersionDate', $_logicalId);

        try {
            $market = market::byLogicalId($_logicalId);

            if (!is_object($market)) {
                $return['status'] = 'depreciated';
            } else {
                $return['market'] = 1;
                if ($market->getApi_author() == config::byKey('market::apikey')) {
                    $return['market_owner'] = 1;
                } else {
                    $return['market_owner'] = 0;
                }
            }
            if ($market->getStatus() == 'Refusé') {
                $return['status'] = 'depreciated';
            }
            if ($market->getStatus() == 'A valider') {
                $return['status'] = 'ok';
            }
            if ($market->getStatus() == 'Validé') {
                if ($updateDateTime < $market->getDatetime()) {
                    $return['status'] = 'update';
                } else {
                    $return['status'] = 'ok';
                }
            }
        } catch (Exception $e) {
            $return['status'] = 'ok';
        }
        return $return;
    }

    /*     * *********************Methode d'instance************************* */

    public function install() {
        $tmp = dirname(__FILE__) . '/../../tmp/' . $this->getLogicalId() . '.zip';

        $url = config::byKey('market::address') . "/core/php/downloadFile.php?id=" . $this->getId();
        file_put_contents($tmp, fopen($url, 'r'));
        if (!file_exists($tmp)) {
            throw new Exception('Impossible de télécharger le fichier depuis : ' . $url);
        }
        switch ($this->getType()) {
            case 'plugin' :
                $cibDir = dirname(__FILE__) . '/../../plugins/' . $this->getLogicalId();
                if (!file_exists($cibDir) && !mkdir($cibDir, 0775, true)) {
                    throw new Exception('Impossible de créer le dossier  : ' . $cibDir . '. Problème de droits ?');
                }
                $zip = new ZipArchive;
                if ($zip->open($tmp) === TRUE) {
                    $zip->extractTo($cibDir . '/');
                    $zip->close();
                    try {
                        $plugin = new plugin($this->getLogicalId());
                    } catch (Exception $e) {
                        $this->remove();
                        throw new Exception('Impossible d\'installer le plugin. Le nom du plugin est différent de l\'ID ou le plugin n\'est pas correctement formé. Veuillez contacter l\'auteur.');
                    }
                    if (config::byKey('installVersionDate', $this->getLogicalId()) != '') {
                        if (is_object($plugin) && $plugin->isActive()) {
                            $plugin->setIsEnable(1);
                        }
                    }
                } else {
                    throw new Exception('Impossible de décompresser le zip : ' . $tmp);
                }


                break;
            default :
                $type = $this->getType();
                if (class_exists($type) && method_exists($type, 'getFromMarket')) {
                    $tmp = $type::getFromMarket($this, $tmp);
                }
                break;
        }
        config::save('installVersionDate', $this->getDatetime(), $this->getLogicalId());
    }

    public function remove() {
        switch ($this->getType()) {
            case 'plugin' :
                $cibDir = dirname(__FILE__) . '/../../plugins/' . $this->getLogicalId();
                if (file_exists($cibDir)) {
                    rrmdir($cibDir);
                }
                break;
            default :
                $type = $this->getType();
                if (class_exists($type) && method_exists(${type}, 'getFromMarket')) {
                    $tmp = $type::removeFromMarket($this);
                }
                break;
        }

        config::remove('installVersionDate', $this->getLogicalId());
    }

    public function save() {
        $market = market::getJsonRpc();
        $params = utils::o2a($this);
        switch ($this->getType()) {
            case 'plugin' :
                $cibDir = realpath(dirname(__FILE__) . '/../../plugins/' . $this->getLogicalId());
                $tmp = dirname(__FILE__) . '/../../tmp/' . $this->getLogicalId() . '.zip';
                if (!create_zip($cibDir, $tmp)) {
                    throw new Exception('Echec de création du zip');
                }
                break;
            default :
                $type = $this->getType();
                if (class_exists($type) && method_exists(${type}, 'shareOnMarket')) {
                    $tmp = $type::shareOnMarket($this);
                }
                break;
        }
        if (!file_exists($tmp)) {
            throw new Exception('Impossible de trouver le fichier à envoyer : ' . $tmp);
        }
        $file = array(
            'file' => '@' . realpath($tmp)
        );
        if (!$market->sendRequest('market::save', $params, 30, $file)) {
            throw new Exception($market->getError());
        }
        config::save('installVersionDate', date('Y-m-d H:i:s'), $this->getLogicalId());
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function getDownloaded() {
        return $this->downloaded;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function setDownloaded($downloaded) {
        $this->downloaded = $downloaded;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getChangelog() {
        return $this->changelog;
    }

    public function setChangelog($changelog) {
        $this->changelog = $changelog;
    }

    public function getLogicalId() {
        return $this->logicalId;
    }

    public function setLogicalId($logicalId) {
        $this->logicalId = $logicalId;
    }

    public function getApi_author() {
        return $this->api_author;
    }

    public function setApi_author($api_author) {
        $this->api_author = $api_author;
    }

}

?>
