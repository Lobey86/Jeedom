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

class xbmc extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        if ($this->getConfiguration('login') == '') {
            throw new Exception('Le nom d\'utilisateur ne peut etre vide');
        }
        if ($this->getConfiguration('password') == '') {
            throw new Exception('Le mot de passe ne peut etre vide');
        }
        if ($this->getConfiguration('addr') == '') {
            throw new Exception('L\'adresse ne peut etre vide');
        }
    }

}

class xbmcCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('request') == '') {
            throw new Exception('La requete ne peut etre vide');
        }
    }

    public function execute($_options = null) {
        $xbmc = $this->getEqLogic();
        $requestHeader = 'http://' . $xbmc->getConfiguration('login') . ':' . $xbmc->getConfiguration('password') . '@' . $xbmc->getConfiguration('addr');

        if ($this->getConfiguration('parameters') == '') {
            $json = array(
                'jsonrpc' => '2.0',
                'method' => $this->getConfiguration('request'),
                'id' => 1
            );
        } else {
            $parameters = $this->getConfiguration('parameters');
            if ($this->type == 'action' && $_options != null) {
                switch ($this->subType) {
                    case 'message':
                        $parameters = str_replace('#title#', $_options['title'], $parameters);
                        $parameters = str_replace('#message#', $_options['message'], $parameters);
                        break;
                    case 'color':
                        $parameters = str_replace('#color#', $_options['color'], $parameters);
                        break;
                    case 'slider':
                        $parameters = str_replace('#slider#', $_options['slider'], $parameters);
                        break;
                    default:
                        break;
                }
            }

            $json = array(
                'jsonrpc' => '2.0',
                'method' => $this->getConfiguration('request'),
                'params' => json_decode($parameters, true),
                'id' => 1
            );
        }

        $request = json_encode($json);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $requestHeader . "/jsonrpc");
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        if ($response === false) {
            log::add('xbmc', 'Error', 'Erreur curl : ' . curl_error($ch) . ' sur la commande XBMC ' . $this->name);
            throw new Exception('[XBMC] Erreur curl : ' . curl_error($ch) . ' sur la commande XBMC ' . $this->name);
        }
        curl_close($ch);
        return $response;
    }

    /*     * **********************Getteur Setteur*************************** */
}
?>

