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

define('PUSHINGBOXADDR', 'http://api.pushingbox.com/pushingbox');

class pushingBox extends eqLogic {
    
}

class pushingBoxCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */
    public function preSave() {
        if ($this->getConfiguration('devid') == '') {
            throw new Exception('DevId ne peut etre vide');
        }
    }

    public function execute($_options = null) {
        if ($_options === null) {
            throw new Exception('[PushingBox] Les options de la fonction ne peuvent etre null');
        }

        if ($_options['message'] == '' && $_options['title'] == '') {
            throw new Exception('[PushingBox] Le message et le sujet ne peuvent être vide');
        }

        if ($_options['title'] == '') {
            $_options['title'] = '[Jeedom] - Notification';
        }
        $request = new com_http(PUSHINGBOXADDR . '?devid=' . $this->getConfiguration('devid') . '&title=' . urlencode($_options['title']) . '&message=' . urlencode($_options['message']));
        return $request->exec(6000, 0, false);
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>