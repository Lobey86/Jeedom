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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('core', 'xpl.core', 'class', 'xpl');
include_file('core', 'xpl', 'config', 'xpl');

class xpl extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function deamon() {
        $xplinstance = XPLInstance::getXPLInstance();
        $eventReturn = $xplinstance->doEvents();
        if ($eventReturn == 1) {
            xPL::proccessMessageEvent($xplinstance->getMessage());
        }
        $xplinstance->detach();
    }

    public static function newDeviceFromxPLNetwork($_logicalId) {
        $list_xPl = self::byLogicalId($_logicalId);
        if (is_object($list_xPl[0])) {
            foreach ($list_xPl as $xPl) {
                $xPl->setIsEnable(1);
                $xPl->save();
            }
        } else {
            if (!is_object($xPl)) {
                $xPL = new xpl();
                $xPL->setName($_logicalId);
                $xPL->setLogicalId($_logicalId);
                $xPL->setObject_id(null);
                $xPL->setIsEnable(1);
                $xPL->setIsVisible(1);
                $xPl->save();
            }
        }
    }

    public static function removedDeviceFromxPLNetwork($_logicalId) {
        $list_xPl = self::byLogicalId($_logicalId);
        if (is_object($list_xPl[0])) {
            foreach ($list_xPl as $xPl) {
                $xPl->setEnable(0);
            }
        }
    }

    public static function proccessMessageEvent($_message) {
        switch ($_message->messageSchemeIdentifier()) {
            case 'sensor.basic':
                require_once dirname(__FILE__) . '/schema/sensor.basic.class.php';
                $list_event = basicSensor::parserMessage($_message);
                break;

            default:
                break;
        }

        if (is_array($list_event)) {
            foreach ($list_event as $event) {
                $cmd = xPLCmd::byId($event['cmd_id']);
                if ($cmd->getType() == 'info') {
                    cache::set('xpl' . $cmd->getId(), $event['value']);
                }
                $cmd->event($event['value']);
            }
        }
        return;
    }

    /*     * *********************Methode d'instance************************* */


    /*     * **********************Getteur Setteur*************************** */
}

class xPLCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('xPLbody') == '') {
            throw new Exception('xPL Body ne peut etre vide');
        }
        if ($this->getConfiguration('xPLtypeCmd') == '') {
            throw new Exception('xPL type commande ne peut etre vide');
        }
        if ($this->getConfiguration('xPLschema') == '') {
            throw new Exception('Le schema xPL ne peut etre vide');
        }
    }

    public function execute($_options = null) {
        $xPLinstance = XPLInstance::getXPLInstance(false);
        $source = $xPLinstance->getThisDevice()->deviceName();
        $_target = $this->getEqLogic()->getLogicalId();

        if ($_options != null) {
            switch ($this->getType()) {
                case 'action' :
                    switch ($this->getSubType()) {
                        case 'slider':
                            $body = str_replace('#slider#', $_options['slider'], $this->getConfiguration('xPLbody'));
                            break;
                        case 'color':
                            $body = str_replace('#color#', $_options['color'], $this->getConfiguration('xPLbody'));
                            break;
                        case 'message':
                            $replace = array('#title#', '#message#');
                            $replaceBy = array($_options['title'], $_options['message']);
                            if ($_options['message'] == '' || $_options['title'] == '') {
                                throw new Exception('[xPL] Le message et le sujet ne peuvent être vide');
                            }
                            $body = str_replace($replace, $replaceBy, $this->getConfiguration('xPLbody'));
                            break;
                    }
                    break;
            }
        }

        $message = '';
        switch ($this->getConfiguration('xPLtypeCmd')) {
            case 'XPL-CMND':
                $message .= "xpl-cmnd\n";
                break;
            case 'XPL-STAT':
                $message .= "xpl-stat\n";
                break;
            case 'XPL-TRIG':
                $message .= "xpl-trig\n";
                break;
            default:
                return "";
        }
        $message .= "{\n";
        $message .= "hop=1\n";
        $message .= sprintf("source=%s\n", $source);
        $message .= sprintf("target=%s\n", $_target);
        $message .= "}\n";
        $message .= $this->getConfiguration('xPLschema') . "\n";
        $message .= "{\n";
        $message .= $body;
        $message .= "}\n";
        $xPLinstance->sendPlainTextMessage($message);

        if ($this->getType() == 'info') {
            $mc = cache::byKey('xpl' . $this->getId());
            return $mc->getValue();
        }
        return '';
    }

    public function getItem($_key) {
        $lines = explode("\n", $this->getConfiguration('xPLbody'));
        for ($row = 0, $sLines = count($lines); $row < $sLines; $row++) {
            list($name, $value) = explode('=', $lines[$row]);
            if ($name == $_key) {
                return $value;
            }
        }
        return false;
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
