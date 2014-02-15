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

    const ALL = "ALL";
    const UNREAD = "REC UNREAD";

    /*     * ***********************Methode static*************************** */

    public static function cleanSMS($_message) {
        $caracteres = array(
            'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
            'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
            'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
            'Œ' => 'oe', 'œ' => 'oe',
            '$' => 's');
        $_message = strtr($_message, $caracteres);
        $_message = preg_replace('#[^A-Za-z0-9 \n\.\'=\*:]+#', '', $_message);
        return $_message;
    }

    public static function pull() {
        foreach (sms::byType('sms') as $eqLogic) {
            $cmds = $eqLogic->getCmd();
            foreach ($eqLogic->readInbox() as $message) {
                $eqLogic->deleteSms($message['id']);
                $autorized = false;
                foreach ($cmds as $cmd) {
                    $formatedPhoneNumber = '+33' . substr($cmd->getConfiguration('phonenumber'), 1);
                    if ($cmd->getConfiguration('phonenumber') == $message['phonenumber'] || $formatedPhoneNumber == $message['phonenumber']) {
                        $autorized = true;
                        break;
                    }
                }
                if ($autorized) {
                    $reply = interactQuery::tryToReply(trim($message['message']), array());
                    $eqLogic->sendSMS($message['phonenumber'], self::cleanSMS($reply));
                }
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function getSerial() {
        if (!isset(self::$_serial)) {
            $this->displayDebug('Création de l\'interface série sur le port : ' . $this->getConfiguration('port'));
            $serial = new phpSerial();
            $serial->deviceSet($this->getConfiguration('port'));
            $serial->confBaudRate(9600);
            $serial->confParity('none');
            $serial->confStopBits(1);
            $serial->confCharacterLength(8);
            $serial->confFlowControl('none');
            $serial->sendParameters("-ignbrk -hupcl -onlcr -echo -echok -echoctl -echoke");
            $serial->setValidOutputs(array(
                'OK',
                'ERROR',
                '+CPIN: SIM PIN',
                '+CPIN: READY',
                '>',
                'COMMAND NOT SUPPORT',
                '+CMS ERROR: 305'
            ));
            self::$_serial = $serial;
            $this->cleanDevice();
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
        $this->displayDebug('Envoie message sur port série : ' . $msg);
        $this->getSerial()->sendMessage($msg);
    }

    private function deviceOpen() {
        $this->getSerial()->deviceOpen();
    }

    private function deviceClose() {
        $this->getSerial()->deviceClose();
    }

    private function cleanDevice() {
        $this->deviceOpen();
        $this->sendMessage(chr(26));
        $this->deviceClose();
        $this->deviceOpen();
        $this->sendMessage("AT\r");
        $this->readPort();
        $this->deviceClose();
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

    public function readInbox($mode = self::ALL) {
        $inbox = $return = array();
        if ($this->checkPin()) {
            $this->deviceOpen();
            $this->sendMessage("AT+CMGF=1\r");
            $out = $this->readPort();
            if ($out == 'OK') {
                $this->sendMessage("AT+CMGL=\"{$mode}\"\r");
                $inbox = $this->readPort(true);
                $this->deviceClose();
            } else {
                $this->deviceClose();
            }
            if (count($inbox) > 2) {
                array_pop($inbox);
                array_pop($inbox);
                $arr = explode("+CMGL:", implode("\n", $inbox));
                for ($i = 1; $i < count($arr); $i++) {
                    $arrItem = explode("\n", $arr[$i], 2);
                    $headArr = explode(",", $arrItem[0]);
                    $fromTlfn = str_replace('"', null, $headArr[2]);
                    $id = $headArr[0];
                    $date = $headArr[4];
                    $hour = $headArr[5];
                    $txt = $arrItem[1];
                    $return[] = array('id' => trim($id), 'phonenumber' => $fromTlfn, 'message' => $txt, 'date' => $date, 'hour' => $hour);
                }
            }
            return $return;
        } else {
            throw new Exception("Please insert the PIN");
        }
    }

    public function sendSMS($_phoneNumber, $_message) {
        $_message = self::cleanSMS($_message);
        if (strlen($_message) > 160) {
            $lines = explode("\n", $_message);
            $message = '';
            $sepLine = '';
            foreach ($lines as $line) {
                if (strlen($line) > 160) {
                    $words = explode(' ', $line);
                    $sepWord = '';
                    foreach ($words as $word) {
                        if (strlen($message . $sepWord . $word) > 160) {
                            self::sendSMS($_phoneNumber, $message);
                            $message = $word;
                        } else {
                            $message .=$sepWord . $word;
                        }
                        $sepWord = ' ';
                    }
                } else {
                    if (strlen($message . $sepLine . $line) > 160) {
                        self::sendSMS($_phoneNumber, $message);
                        $message = $line;
                    } else {
                        $message .= $sepLine . $line;
                    }
                    $sepLine = "\n";
                }
            }
            if ($message != '') {
                self::sendSMS($_phoneNumber, $message);
            }
        } else {
            if ($this->checkPin()) {
                $_message = substr($_message, 0, 160);
                $this->deviceOpen();
                $this->sendMessage("AT+CMGF=1\r");
                $out = $this->readPort();
                if ($out == 'OK') {
                    $this->sendMessage("AT+CMGS=\"{$_phoneNumber}\"\r");
                    $this->sendMessage("{$_message}" . chr(26));
                    $out = $this->readPort();
                    $this->deviceClose();
                }
                if ($out == 'OK') {
                    return true;
                } else {
                    $this->deviceOpen();
                    $this->sendMessage(chr(26));
                    $out = $this->readPort();
                    $this->deviceClose();
                    return false;
                }
            } else {
                throw new Exception("Please insert the PIN");
            }
        }
    }

    private function checkPin() {
        $this->displayDebug('Vérification du code pin');
        if ($this->getPinOk()) {
            $this->displayDebug('Code pin OK');
            return true;
        }
        $this->displayDebug('Demande au modem');
        $this->deviceOpen();
        $this->sendMessage("AT+CPIN?\r");
        $out = $this->readPort();
        $this->deviceClose();
        $this->displayDebug('Resultat : ' . $out);
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
            sleep(45);
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
        //$eqLogic->setDebug(true);
        if (!isset($_options['title']) && !isset($_options['message'])) {
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
        $eqLogic->sendSMS($this->getConfiguration('phonenumber'), $message);
    }

}
