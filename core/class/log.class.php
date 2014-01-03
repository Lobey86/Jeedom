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

class log {
    /*     * *************************Attributs****************************** */

    private static $logLevel;

    /*     * ***********************Methode static*************************** */

    /**
     * Ajoute un message dans les log et fait en sorte qu'il n'y
     * ai jamais plus de 1000 lignes
     * @param string $_type type du message à mettre dans les log
     * @param string $_message message à mettre dans les logs
     */
    public static function add($_module, $_type, $_message) {
        $_type = strtolower($_type);
        if (self::isTypeLog($_type)) {
            $_message = str_replace("\n", '<br/>', $_message);
            $_message = str_replace(";", ',', $_message);
            $path = self::getPathToLog($_module);
            $message = date("d-m-Y H:i:s") . ' | ' . $_type . ' | ' . $_message . "\r\n";
            $log = fopen($path, "a+");
            fputs($log, $message);
            fclose($log);
            $log_file = file($path);
            if (count($log_file) > config::byKey('maxLineLog')) {
                $log_file = array_slice($log_file, count($log_file) - config::byKey('maxLineLog'));
                $log_txt = implode("", $log_file);
                $log = fopen($path, "w+");
                fwrite($log, $log_txt);
                fclose($log);
            }
            @chown($path, 'www-data');
            @chgrp($path, 'www-data');
            @chmod($path, 0777);
            if (config::byKey('addMessageForErrorLog') == 1 && $_type == 'error') {
                @message::add($_module, $_message);
            }
        }
    }

    public static function getPathToLog($_module = 'core') {
        return dirname(__FILE__) . '/../../log/' . $_module;
    }

    /**
     * Vide le fichier de log 
     */
    public static function clear($_module) {
        $path = self::getPathToLog($_module);
        $log = fopen($path, "w");
        ftruncate($log, 0);
        fclose($log);
        return true;
    }

    /**
     * Vide le fichier de log 
     */
    public static function remove($_module) {
        $path = self::getPathToLog($_module);
        unlink($path);
        return true;
    }

    /**
     * Renvoi les x derniere ligne du fichier de log
     * @param int $_maxLigne nombre de ligne voulu
     * @return string Ligne du fichier de log
     */
    public static function get($_module = 'core', $_begin, $_nbLines) {
        $page = array();
        $path = self::getPathToLog($_module);
        if (!file_exists($path)) {
            return false;
        }
        $log = new SplFileObject($path);
        if ($log) {
            $log->seek($_begin); //Seek to the begening of lines
            $linesRead = 0;
            while ($log->valid() && $linesRead != $_nbLines) {
                $line = $log->current(); //get current line
                if (count(explode("|", $line)) == 3) {
                    array_unshift($page, array_map('trim', explode("|", $line)));
                } else {
                    $lineread = array();
                    $lineread[0] = '';
                    $lineread[1] = '';
                    $lineread[2] = $line;
                    array_unshift($page, $lineread);
                }
                $log->next(); //go to next line
                $linesRead++;
            }
        }
        return $page;
    }

    public static function nbLine($_module = 'core') {
        $path = self::getPathToLog($_module);
        $log_file = file($path);
        return count($log_file);
    }

    private static function isTypeLog($_type) {
        if (!isset(self::$logLevel) || !is_array(self::$logLevel)) {
            try {
                self::$logLevel = config::byKey('logLevel');
            } catch (Exception $e) {
                self::$logLevel = array();
            }
        }
        if (!isset(self::$logLevel[$_type]) || self::$logLevel[$_type] == 1) {
            return true;
        }
        return false;
    }

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}

?>
