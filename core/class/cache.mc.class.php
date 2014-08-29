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

class cache {
    /*     * *************************Attributs****************************** */

    private $key;
    private $value = null;
    private $lifetime = 1;
    private $datetime;
    private $options = null;
    private $_hasExpired = -1;
    private static $_cache = array();
    private $connection;
    private static $sharedInstance;

    /*     * ***********************Methode static*************************** */

    private function __construct() {
        $this->connection = new Memcached();
        $this->connection->addServer('localhost', 11211);
    }

    public static function getConnection() {
        if (!isset(self::$sharedInstance)) {
            self::$sharedInstance = new self();
        }
        return self::$sharedInstance->connection;
    }

    public static function byKey($_key, $_noRemove = false, $_allowFastCache = false) {
        if (isset(self::$_cache[$_key]) && $_allowFastCache) {
            return self::$_cache[$_key]['value'];
        }
        $result = self::getConnection()->get($_key);
        if ($result == false) {
            $cache = new self();
            $cache->setKey($_key);
            $cache->setDatetime(date('Y-m-d H:i:s'));
            $cache->_hasExpired = true;
        } else {
            $cache = self::buildCacheObject($result);
        }
        self::$_cache[$_key] = array('value' => $cache, 'datetime' => strtotime('now'));
        return $cache;
    }

    public static function search($_search, $_noRemove = false) {
        $return = array();
        $keys = self::getConnection()->getAllKeys();
        foreach ($keys as $key) {
            if (strpos($key, $_search) !== false) {
                $return[] = self::byKey($key);
            }
        }
        return $return;
    }

    public static function flush() {
        $keys = self::getConnection()->getAllKeys();
        foreach ($keys as $key) {
            if ($key != 'start') {
                self::getConnection()->delete($key);
            }
        }
    }

    public static function buildCacheObject($_value) {
        $_value = json_decode($_value, true);
        $cache = new self();
        $cache->setKey($_value['key']);
        $cache->setDatetime($_value['datetime']);
        $cache->setLifetime($_value['lifetime']);
        $cache->setValue($_value['value']);
        $cache->options = $_value['options'];
        return $cache;
    }

    public static function set($_key, $_value, $_lifetime = 60, $_options = null) {
        if ($_lifetime < 0) {
            $_lifetime = 0;
        }
        $cache = new self();
        $cache->setKey($_key);
        $cache->setValue($_value);
        $cache->setLifetime($_lifetime);
        if ($_options != null) {
            foreach ($_options as $key => $value) {
                $cache->setOptions($key, $value);
            }
        }
        self::$_cache[$_key] = array('value' => $cache, 'datetime' => strtotime('now'));
        return $cache->save();
    }

    public static function load() {
        $sql = 'SELECT *
                FROM cache';
        $caches = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
        foreach ($caches as $cache) {
            self::set($cache['key'], $cache['value'], $cache['lifetime'], $cache['options']);
        }
    }

    public static function persist() {
        DB::Prepare('TRUNCATE TABLE `cache`', array());
        $sql = 'REPLACE cache
                 SET `key`=:key,
                     `value`=:value,
                     `datetime`=:datetime,
                     `lifetime`=:lifetime,
                     `options`=:options';
        $keys = self::getConnection()->getAllKeys();
        foreach ($keys as $key) {
            $cache = self::byKey($key);
            if ($cache->getKey() != '') {
                $values = array(
                    'key' => $cache->getKey(),
                    'value' => $cache->getValue(),
                    'datetime' => $cache->getDatetime(),
                    'lifetime' => $cache->getLifetime(),
                    'options' => $cache->getOptions()
                );
                DB::Prepare($sql, $values);
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        $values = array(
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'datetime' => date('Y-m-d H:i:s'),
            'lifetime' => $this->getLifetime(),
            'options' => $this->getOptions()
        );
        self::getConnection()->set($this->getKey(), json_encode($values));
    }

    public function remove() {
        if (isset(self::$_cache[$_key])) {
            unset(self::$_cache[$_key]);
        }
        self::getConnection()->delete($this->getKey());
    }

    public function hasExpired() {
        if ($this->_hasExpired != -1) {
            return $this->_hasExpired;
        }
        if ($this->getLifetime() == 0) {
            $this->_hasExpired = false;
            return false;
        }
        if ($this->value === null || trim($this->value) === '') {
            $this->_hasExpired = true;
            return true;
        }
        if ((strtotime($this->getDatetime()) + $this->getLifetime()) < strtotime('now')) {
            $this->_hasExpired = true;
            return true;
        }
        $this->_hasExpired = false;
        return false;
    }

    public function invalid() {
        if (!$this->hasExpired()) {
            $this->setLifetime(1);
            $this->save();
        }
        return true;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getValue($_default = '') {
        return ($this->value === null || trim($this->value) === '') ? $_default : $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getLifetime() {
        return $this->lifetime;
    }

    public function setLifetime($lifetime) {
        $this->lifetime = $lifetime;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

    public function getOptions($_key = '', $_default = '') {
        return utils::getJsonAttr($this->options, $_key, $_default);
    }

    public function setOptions($_key, $_value) {
        $this->options = utils::setJsonAttr($this->options, $_key, $_value);
    }

}

?>
