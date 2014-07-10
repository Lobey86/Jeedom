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

/* ------------------------------------------------------------ Inclusions */
require_once dirname(__FILE__) . '/../../core/php/core.inc.php';

class com_http {
    /*     * ***********************Attributs************************* */

    private $url;
    private $username;
    private $password;
    private $logError = true;
    private $ping = false;
    private $noSslCheck = true;
    private $sleepTime = 500000;

    /*     * ********************Functions static********************* */

    function __construct($_url, $_username = '', $_password = '') {
        $this->url = $_url;
        $this->username = $_username;
        $this->password = $_password;
    }

    /*     * ************* Functions ************************************ */

    function exec($_timeout = 2, $_maxRetry = 3) {
        if ($this->getPing()) {
            $url = parse_url($this->url);
            $host = $url['host'];
            if (!ip2long($host)) {
                $timeout = config::byKey('http::ping_timeout');
                exec("timeout $timeout ping -n -c 1 -W 2 $host", $output, $retval);
                if ($retval != 0) {
                    throw new Exception(__('Impossible de résoudre le DNS : ', __FILE__) . $host . __('. Pas d\'internet ?', __FILE__));
                }
            }
        }
        $nbRetry = 0;
        while ($nbRetry < $_maxRetry) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            if ($this->getNoSslCheck()) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $_timeout);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            if ($this->username != '') {
                curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            }
            $response = curl_exec($ch);
            $nbRetry++;
            if (curl_errno($ch) && $nbRetry < $_maxRetry) {
                curl_close($ch);
                usleep($this->getSleepTime());
            } else {
                $nbRetry = $_maxRetry + 1;
            }
        }
        if (curl_errno($ch)) {
            $curl_error = curl_error($ch);
            if ($this->getLogError()) {
                log::add('http.com', 'error', __('Erreur curl : ', __FILE__) . $curl_error . __(' sur la commande ', __FILE__) . $this->url . __(' après ', __FILE__) . $nbRetry . __(' relance(s)', __FILE__));
            }
            curl_close($ch);
            throw new Exception(__('Echec de la requete http : ', __FILE__) . $this->url . ' Curl error : ' . $curl_error, 404);
        }
        curl_close($ch);
        log::add('http.com', 'Debug', __('Url : ', __FILE__) . $this->url . __("\nReponse : ", __FILE__) . $response);
        return $response;
    }

    public function getPing() {
        return $this->ping;
    }

    public function getNoSslCheck() {
        return $this->noSslCheck;
    }

    public function setPing($ping) {
        $this->ping = $ping;
    }

    public function setNoSslCheck($noSslCHeck) {
        $this->noSslCheck = $noSslCHeck;
    }

    public function getLogError() {
        return $this->logError;
    }

    public function setLogError($logError) {
        $this->logError = $logError;
    }

    public function getSleepTime() {
        return $this->sleepTime;
    }

    public function setSleepTime($sleepTime) {
        $this->sleepTime = $sleepTime * 1000000;
    }

}

?>
