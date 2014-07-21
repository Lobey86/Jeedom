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
    private $value = '';
    private $lifetime = 1;
    private $datetime;
    private $options = null;
    private $_hasExpired = -1;

    /*     * ***********************Methode static*************************** */

    public static function byKey($_key, $_noRemove = false) {
        $values = array(
            'key' => $_key
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM cache
                WHERE `key`=:key';
        $cache = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
        if (!is_object($cache)) {
            $cache = new self();
            $cache->setKey($_key);
            $cache->setDatetime(date('Y-m-d H:i:s'));
        } else {
            if (!$_noRemove && $cache->hasExpired()) {
                $cache->remove();
            }
        }
        return $cache;
    }

    public static function search($_search, $_noRemove = false) {
        $values = array(
            'key' => '%' . $_search . '%'
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM cache
                WHERE `key` LIKE :key';
        $caches = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
        if (!$_noRemove) {
            foreach ($caches as $cache) {
                if ($cache->hasExpired()) {
                    $cache->remove();
                }
            }
        }
        return $caches;
    }

    public static function flush() {
        $sql = 'TRUNCATE TABLE cache';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
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
        return $cache->save();
    }

    public static function persist() {
        DB::Prepare('TRUNCATE TABLE `cache_persist`', array(), DB::FETCH_TYPE_ROW);
        DB::Prepare('REPLACE `cache_persist` SELECT * FROM `cache`', array(), DB::FETCH_TYPE_ROW);
        return true;
    }

    public static function restore() {
        DB::Prepare('TRUNCATE TABLE `cache`', array(), DB::FETCH_TYPE_ROW);
        DB::Prepare('REPLACE `cache` SELECT * FROM `cache_persist`', array(), DB::FETCH_TYPE_ROW);
        return true;
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        $options = $this->getOptions();
        if (is_array($options) || is_object($options)) {
            $options = json_encode($options, JSON_UNESCAPED_UNICODE);
        }
        $values = array(
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'datetime' => date('Y-m-d H:i:s'),
            'lifetime' => $this->getLifetime(),
            'options' => $options
        );
        $sql = 'REPLACE cache
                 SET `key`=:key,
                     `value`=:value,
                     `datetime`=:datetime,
                     `lifetime`=:lifetime,
                     `options`=:options';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
    }

    public function remove() {
        DB::remove($this);
    }

    public function hasExpired() {
        if($this->_hasExpired != -1){
            return $this->_hasExpired;
        }
        if (trim($this->getValue()) === '' || $this->getValue() === false) {
            $this->_hasExpired = true;
            return true;
        }
        if ($this->getLifetime() != 0 && (strtotime($this->getDatetime()) + $this->getLifetime()) < strtotime('now')) {
            $this->_hasExpired = true;
            return true;
        }
        $this->_hasExpired = false;
        return false;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getValue($_default = '') {
        return (trim($this->value) === '') ? $_default : $this->value;
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
