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
include_file('3rdparty', 'phpSerial.class', 'php', 'gsm');

class gsm extends eqLogic {
    
}

class gsmCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
        $eqLogic = $this->getEqLogic();
        $serial = new phpSerial;
        $serial->deviceSet($eqLogic->getConfiguration('port'));
        $serial->deviceOpen();
        $serial->sendMessage('AT+CPIN="0000"' . "\n");
        sleep(2);
        $serial->sendMessage('AT+CMGF=1' . "\n");
        $serial->sendMessage('AT+CSMP=17,167,0,16' . "\n");
        $serial->sendMessage('AT+CMGS="+33627860098"' . "\n");
        $serial->sendMessage('Test');
        $serial->sendMessage(chr(26));
        $serial->deviceClose();
    }

}
