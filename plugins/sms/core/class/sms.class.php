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
require_once dirname(__FILE__) . '/../../core/php/sms.inc.php';

class sms extends eqLogic {
    /*     * *************************Attributs****************************** */

    private $_pinOk = false;
    private static $_serial;

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function getSerial() {
        if (!isset(self::$_serial)) {
            $serial = new phpSerial();
            $serial->deviceSet($this->getConfiguration('port'));
            $serial->confBaudRate(460800);
            $serial->confParity('none');
            $serial->confCharacterLength(8);
            self::$_serial = $serial;
        }
        return self::$_serial;
    }

    private function readPort($returnBufffer = false) {
        $out = null;
        list($last, $buffer) = $this->getSerial()->readPort();
        if ($returnBufffer) {
            $out = $buffer;
        } else {
            $out = strtoupper($last);
        }
        return $out;
    }

    private function sendMessage($msg) {
        $this->getSerial()->sendMessage($msg);
    }

    private function deviceOpen() {
        $this->getSerial()->deviceOpen();
    }

    private function deviceClose() {
        $this->getSerial()->deviceClose();
    }

    public function deleteSms($id) {
        $this->deviceOpen();
        $this->sendMessage("AT+CMGD={$id}\r");
        $out = $this->readPort();
        $this->deviceClose();
        if ($out == 'OK') {
            return true;
        }
        return false;
    }

    public function sendSMS($_phoneNumber, $_message) {
        if ($this->checkPin()) {
            $_message = substr($_message, 0, 160);
            $this->deviceOpen();
            $this->sendMessage(chr(26));
            $this->sendMessage("AT+CMGF=1\r");
            $this->sendMessage("AT+CMGS=\"{$_phoneNumber}\"\r");
            $this->sendMessage("{$_message}" . chr(26));
            $out = $this->readPort();
            $this->deviceClose();
            if ($out == 'OK') {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("Please insert the PIN");
        }
    }

    private function checkPin() {
        if ($this->getPinOk()) {
            return true;
        }
        $this->deviceOpen();
        $this->sendMessage("AT+CPIN?\r");
        $out = $this->readPort();
        $this->deviceClose();
        if ($out == "+CPIN: SIM PIN") {
            $pin = $this->getConfiguration('pin');
            if (is_null($pin) || $pin == '' || !is_numeric($pin)) {
                $this->setIsEnable(false);
                $this->save();
                throw new Exception("PIN erreur : vide ou non numérique");
            }
            $this->deviceOpen();
            $this->sendMessage("AT+CPIN={$pin}\r");
            $out = $this->readPort();
            $this->deviceClose();
            sleep(10);
        }

        switch ($out) {
            case "+CPIN: READY":
            case "OK":
                $this->setPinOk(true);
                return true;
                break;
        }
        $this->setIsEnable(false);
        $this->save();
        throw new Exception("PIN ERROR ({$out})");
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getPinOk() {
        return $this->_pinOk;
    }

    public function setPinOk($_pinOk) {
        $this->_pinOk = $_pinOk;
    }

}

class smsCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
        $eqLogic = $this->getEqLogic();
        if (!isset($_options['title']) || !isset($_options['message'])) {
            throw new Exception("Le titre ou le message ne peuvent être tous les deux vide");
        }
        $message = '';
        $sep = '';
        if (isset($_options['title'])) {
            $message = $_options['title'];
            $sep = "\n";
        }
        if (isset($_options['message'])) {
            $message .= $sep . $_options['message'];
        }
        $eqLogic->sendSMS($this->getConfiguration('phoneNumber'), $message);
    }

}
