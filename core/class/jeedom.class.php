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

class jeedom {
    /*     * *************************Attributs****************************** */

    private static $jeedomConfiguration;

    /*     * ***********************Methode static*************************** */

    public static function stop() {
        try {
            echo "Désactivation de toutes les tâches";
            config::save('enableCron', 0);
            foreach (cron::all() as $cron) {
                if ($cron->running()) {
                    $cron->halt();
                    echo '.';
                }
            }
            echo " OK\n";
        } catch (Exception $e) {
            if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                throw $e;
            } else {
                echo '***ERREUR*** ' . $e->getMessage();
            }
        }
        /*         * **********Arret des crons********************* */

        try {
            if (cron::jeeCronRun()) {
                echo "Arret du cron master ";
                exec('kill ' . cron::getPidFile());
                while (cron::jeeCronRun()) {
                    echo '.';
                    sleep(2);
                }
                echo " OK\n";
            }
        } catch (Exception $e) {
            if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                throw $e;
            } else {
                echo '***ERREUR*** ' . $e->getMessage();
            }
        }


        /*         * *********Arret des scénarios**************** */
        try {
            echo "Désactivation de tous les scénarios";
            config::save('enableScenario', 0);
            foreach (scenario::all() as $scenario) {
                $scenario->stop();
                echo '.';
            }
            echo " OK\n";
        } catch (Exception $e) {
            if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                throw $e;
            } else {
                echo '***ERREUR*** ' . $e->getMessage();
            }
        }
    }

    public static function start() {
        try {
            /*             * *********Réactivation des scénarios**************** */
            echo "Réactivation des scénarios : ";
            config::save('enableScenario', 1);
            echo "OK\n";
            /*             * *********Réactivation des tâches**************** */
            echo "Réactivation des tâches : ";
            config::save('enableCron', 1);
            echo "OK\n";
        } catch (Exception $e) {
            if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                throw $e;
            } else {
                echo '***ERREUR*** ' . $e->getMessage();
            }
        }
    }

    public static function getUsbMapping($_name = '') {
        $cache = cache::byKey('jeedom::usbMapping');
        if (!is_json($cache->getValue()) || $_name == '') {
            $usbMapping = array();
            foreach (ls('/dev/', 'ttyUSB*') as $usb) {
                $vendor = '';
                $model = '';
                foreach (explode("\n", shell_exec('udevadm info --name=/dev/' . $usb . ' --query=all')) as $line) {
                    if (strpos($line, 'E: ID_MODEL=') !== false) {
                        $model = trim(str_replace(array('E: ID_MODEL=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_VENDOR=') !== false) {
                        $vendor = trim(str_replace(array('E: ID_VENDOR=', '"'), '', $line));
                    }
                }
                if ($vendor == '' && $model == '') {
                    $usbMapping['/dev/' . $usb] = '/dev/' . $usb;
                } else {
                    $name = trim($vendor . ' ' . $model);
                    $number = 2;
                    while (isset($usbMapping[$name])) {
                        $name = trim($vendor . ' ' . $model . ' ' . $number);
                        $number++;
                    }
                    $usbMapping[$name] = '/dev/' . $usb;
                }
            }
            cache::set('jeedom::usbMapping', json_encode($usbMapping), 0);
        } else {
            $usbMapping = json_decode($cache->getValue(), true);
        }
        if ($_name != '') {
            if (isset($usbMapping[$_name])) {
                return $usbMapping[$_name];
            }
            $usbMapping = array();
            foreach (ls('/dev/', 'ttyUSB*') as $usb) {
                $vendor = '';
                $model = '';
                foreach (explode("\n", shell_exec('udevadm info --name=/dev/' . $usb . ' --query=all')) as $line) {
                    if (strpos($line, 'E: ID_MODEL=') !== false) {
                        $model = trim(str_replace(array('E: ID_MODEL=', '"'), '', $line));
                    }
                    if (strpos($line, 'E: ID_VENDOR=') !== false) {
                        $vendor = trim(str_replace(array('E: ID_VENDOR=', '"'), '', $line));
                    }
                }
                if ($vendor == '' && $model == '') {
                    $usbMapping['/dev/' . $usb] = '/dev/' . $usb;
                } else {
                    $name = trim($vendor . ' ' . $model);
                    $number = 2;
                    while (isset($usbMapping[$name])) {
                        $name = trim($vendor . ' ' . $model . ' ' . $number);
                        $number++;
                    }
                    $usbMapping[$name] = '/dev/' . $usb;
                }
            }
            cache::set('jeedom::usbMapping', json_encode($usbMapping), 0);
            if (isset($usbMapping[$_name])) {
                return $usbMapping[$_name];
            }
            return '';
        }
        return $usbMapping;
    }

    public static function persist() {
        
    }

    public static function backup($_background = false) {
        if ($_background) {
            log::clear('backup');
            $cmd = 'nice -20 php ' . dirname(__FILE__) . '/../../install/backup.php';
            $cmd.= ' >> ' . log::getPathToLog('backup') . ' 2>&1 &';
            exec($cmd);
        } else {
            require_once dirname(__FILE__) . '/../../install/backup.php';
        }
    }

    public static function listBackup() {
        if (substr(config::byKey('backup::path'), 0, 1) != '/') {
            $backup_dir = dirname(__FILE__) . '/../../' . config::byKey('backup::path');
        } else {
            $backup_dir = config::byKey('backup::path');
        }
        $backups = ls($backup_dir, '*.tar.gz', false, array('files', 'quiet', 'datetime_asc'));
        $return = array();
        foreach ($backups as $backup) {
            $return[$backup_dir . '/' . $backup] = $backup;
        }
        return $return;
    }

    public static function removeBackup($_backup) {
        if (file_exists($_backup)) {
            unlink($_backup);
        } else {
            throw new Exception('Impossible de trouver le fichier : ' . $_backup);
        }
    }

    public static function restore($_backup = '', $_background = false) {
        if ($_background) {
            log::clear('restore');
            $cmd = 'php ' . dirname(__FILE__) . '/../../install/restore.php backup=' . $_backup;
            $cmd.= ' >> ' . log::getPathToLog('restore') . ' 2>&1 &';
            exec($cmd);
        } else {
            global $BACKUP_FILE;
            $BACKUP_FILE = $_backup;
            require_once dirname(__FILE__) . '/../../install/restore.php';
        }
    }

    public static function update($_mode = '', $_level = -1) {
        log::clear('update');
        $cmd = 'php ' . dirname(__FILE__) . '/../../install/install.php mode=' . $_mode . ' level=' . $_level;
        $cmd.= ' >> ' . log::getPathToLog('update') . ' 2>&1 &';
        exec($cmd);
    }

    public static function getConfiguration($_key, $_default = false) {
        if (!is_array(self::$jeedomConfiguration)) {
            self::$jeedomConfiguration = array();
        }
        if (!$_default && isset(self::$jeedomConfiguration[$_key])) {
            return self::$jeedomConfiguration[$_key];
        }
        $keys = explode(':', $_key);
        global $JEEDOM_INTERNAL_CONFIG;
        $result = $JEEDOM_INTERNAL_CONFIG;
        foreach ($keys as $key) {
            if (isset($result[$key])) {
                $result = $result[$key];
            }
        }
        if ($_default) {
            return $result;
        }
        self::$jeedomConfiguration[$_key] = self::checkValueInconfiguration($_key, $result);
        return self::$jeedomConfiguration[$_key];
    }

    private static function checkValueInconfiguration($_key, $_value) {
        if (!is_array(self::$jeedomConfiguration)) {
            self::$jeedomConfiguration = array();
        }
        if (isset(self::$jeedomConfiguration[$_key])) {
            return self::$jeedomConfiguration[$_key];
        }
        if (is_array($_value)) {
            foreach ($_value as $key => $value) {
                $_value[$key] = self::checkValueInconfiguration($_key . ':' . $key, $value);
            }
            self::$jeedomConfiguration[$_key] = $_value;
            return $_value;
        } else {
            $config = config::byKey($_key);
            return ($config == '') ? $_value : $config;
        }
    }

    public static function whatDoYouKnow($_object = null) {
        $result = array();
        if (is_object($_object)) {
            $objects = array($_object);
        } else {
            $objects = object::all();
        }
        foreach ($objects as $object) {
            foreach ($object->getEqLogic() as $eqLogic) {
                if ($eqLogic->getIsEnable() == 1) {
                    foreach ($eqLogic->getCmd() as $cmd) {
                        if ($cmd->getIsVisible() == 1 && $cmd->getType() == 'info') {
                            try {
                                $value = $cmd->execCmd();
                                if (!isset($result[$object->getId()])) {
                                    $result[$object->getId()] = array();
                                    $result[$object->getId()]['name'] = $object->getName();
                                    $result[$object->getId()]['eqLogic'] = array();
                                }
                                if (!isset($result[$object->getId()]['eqLogic'][$eqLogic->getId()])) {
                                    $result[$object->getId()]['eqLogic'][$eqLogic->getId()] = array();
                                    $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['name'] = $eqLogic->getName();
                                    $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['cmd'] = array();
                                }

                                $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['cmd'][$cmd->getId()] = array();
                                $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['cmd'][$cmd->getId()]['name'] = $cmd->getName();
                                $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['cmd'][$cmd->getId()]['unite'] = $cmd->getUnite();
                                $result[$object->getId()]['eqLogic'][$eqLogic->getId()]['cmd'][$cmd->getId()]['value'] = $value;
                            } catch (Exception $exc) {
                                
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public static function needUpdate($_refresh = false) {
        $return = array();
        $return['currentVersion'] = market::getJeedomCurrentVersion($_refresh);
        $return['version'] = getVersion('jeedom');
        if (version_compare($return['currentVersion'], $return['version'], '>')) {
            $return['needUpdate'] = true;
        } else {
            $return['needUpdate'] = false;
        }
        return $return;
    }

    public static function isStarted() {
        if (extension_loaded('memcached')) {
            $cache = cache::byKey('start');
            if ($cache->getValue() == 'ok') {
                return true;
            }
        } else {
            $sql = "SELECT `value` FROM `start` WHERE `key`='start'";
            $result = DB::Prepare($sql, array());
            if (count($result) > 0 && $result['value'] == 'ok') {
                return true;
            }
        }
        return false;
    }

    public static function isDateOk() {
        $cache = cache::byKey('jeedom::lastDate');
        $lastDate = strtotime($cache->getValue());
        if ($lastDate == '' || $lastDate === false) {
            cache::set('jeedom::lastDate', date('Y-m-d H:00:00'), 0);
            message::removeAll('core', 'dateCheckFailed');
            return true;
        }
        if ($lastDate == strtotime(date('Y-m-d H:00:00'))) {
            message::removeAll('core', 'dateCheckFailed');
            return true;
        }
        if (($lastDate + 7200) > strtotime(date('Y-m-d H:00:00')) && ($lastDate - 3600) < strtotime(date('Y-m-d H:00:00'))) {
            cache::set('jeedom::lastDate', date('Y-m-d H:00:00'), 0);
            message::removeAll('core', 'dateCheckFailed');
            return true;
        }
        $ntptime = strtotime(getNtpTime());
        if ($ntptime !== false && ($ntptime + 3600) > strtotime('now') && ($ntptime - 3600) < strtotime('now')) {
            cache::set('jeedom::lastDate', date('Y-m-d H:00:00'), 0);
            message::removeAll('core', 'dateCheckFailed');
            return true;
        }
        log::add('core', 'error', __('La date systeme (', __FILE__) . date('Y-m-d H:00:00') . __(') est anterieur à la derniere date (', __FILE__) . $lastDate . __(')enregistrer. Tous les lancements des scénarios sont interrompu jusqu\'à correction.', __FILE__), 'dateCheckFailed');
        return false;
    }

    public static function event($_event) {
        scenario::check($_event);
    }

    public static function cron() {
        if (!self::isStarted()) {
            if (extension_loaded('memcached') && method_exists('cache', 'load')) {
                cache::load();
                cache::set('start', 'ok');
            }
            $cache = cache::byKey('jeedom::usbMapping');
            $cache->remove();
            jeedom::start();
            plugin::start();
            internalEvent::start();
            if (!extension_loaded('memcached')) {
                DB::Prepare("INSERT INTO `start` (`key` ,`value`) VALUES ('start',  'ok')", array());
            }
            self::event('start');
            log::add('core', 'info', 'Démarrage de Jeedom OK');
        }
        plugin::cron();
        interactDef::cron();
        eqLogic::checkAlive();
        connection::cron();
        try {
            $c = new Cron\CronExpression(config::byKey('persist::cron'), new Cron\FieldFactory);
            if ($c->isDue()) {
                if (method_exists('cache', 'persist')) {
                    cache::persist();
                }
            }
        } catch (Exception $e) {
            log::add('log', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression(config::byKey('log::chunck'), new Cron\FieldFactory);
            if ($c->isDue()) {
                log::chunk();
            }
        } catch (Exception $e) {
            log::add('log', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression(config::byKey('update::check'), new Cron\FieldFactory);
            if ($c->isDue()) {
                update::checkAllUpdate();
                $nbUpdate = update::nbNeedUpdate();
                if ($nbUpdate > 0) {
                    message::add('update', 'De nouvelles mise à  jour sont disponible (' . $nbUpdate . ')');
                }
            }
        } catch (Exception $e) {
            log::add('update', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression(config::byKey('backup::cron'), new Cron\FieldFactory);
            if ($c->isDue()) {
                jeedom::backup();
            }
        } catch (Exception $e) {
            log::add('backup', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression('50 23 * * *', new Cron\FieldFactory);
            if ($c->isDue()) {
                scenario::cleanTable();
            }
        } catch (Exception $e) {
            log::add('scenario', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression('00 * * * *', new Cron\FieldFactory);
            if ($c->isDue()) {
                self::isDateOk();
            }
        } catch (Exception $e) {
            log::add('scenario', 'error', $e->getMessage());
        }
        try {
            $c = new Cron\CronExpression(config::byKey('jeeNetwork::pull'), new Cron\FieldFactory);
            if ($c->isDue()) {
                jeeNetwork::pull();
            }
        } catch (Exception $e) {
            log::add('jeeNetwork', 'error', $e->getMessage());
        }
        if (config::byKey('market::allowDNS') == 1) {
            try {
                $c = new Cron\CronExpression('*/10 * * * *', new Cron\FieldFactory);
                if ($c->isDue()) {
                    market::updateIp();
                }
            } catch (Exception $e) {
                log::add('market', 'error', $e->getMessage());
            }
        }
    }

    public static function checkOngoingThread($_cmd) {
        return shell_exec('ps ax | grep "' . $_cmd . '$" | grep -v "grep" | wc -l');
    }

    public static function retrievePidThread($_cmd) {
        return shell_exec('ps ax | grep "' . $_cmd . '$" | grep -v "grep" | awk "{print $1}"');
    }

    public static function getHardwareKey() {
        $cache = cache::byKey('jeedom::hwkey');
        if ($cache->getValue(0) == 0) {
            //$key = shell_exec('cat /proc/cpuinfo');
            $key = shell_exec("/sbin/ifconfig eth0 | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'");
            $hwkey = sha1($key);
            cache::set('jeedom::hwkey', $hwkey, 86400);
            return $hwkey;
        }
        return $cache->getValue();
    }

    public static function versionAlias($_version) {
        $alias = array(
            'mview' => 'mobile',
            'dview' => 'dashboard',
        );
        return (isset($alias[$_version])) ? $alias[$_version] : $_version;
    }

    public static function toHumanReadable($_input) {
        return scenario::toHumanReadable(eqLogic::toHumanReadable(cmd::cmdToHumanReadable($_input)));
    }

    public static function fromHumanReadable($_input) {
        return scenario::fromHumanReadable(eqLogic::fromHumanReadable(cmd::humanReadableToCmd($_input)));
    }

    public static function evaluateExpression($_input) {
        $test = new evaluate();
        return $test->Evaluer(cmd::cmdToValue($_input));
    }

    public static function haltSystem() {
        exec('sudo halt');
    }

    public static function rebootSystem() {
        exec('sudo reboot');
    }

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}

?>
