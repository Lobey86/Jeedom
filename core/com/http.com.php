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

    /*     * ********************Functions static********************* */

    function __construct($_url) {
        $this->url = $_url;
    }

    /*     * ************* Functions ************************************ */

    function exec($_timeout = 2, $_maxRetry = 3, $_logErrorIfNoResponse = true) {
        $retry = true;
        $nbRetry = 1;
        while ($retry) {
            $retry = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $_timeout);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                if ($nbRetry <= $_maxRetry) {
                    $nbRetry++;
                    $retry = true;
                    sleep(1);
                } else {
                    if ($_logErrorIfNoResponse) {
                        log::add('http.com', 'error', __('Erreur curl : ', __FILE__) . curl_error($ch) . __(' sur la commande ', __FILE__) . $this->url . __(' après ', __FILE__) . $nbRetry . __(' relance(s)', __FILE__));
                    }
                    throw new Exception(__('Echec de la requete http : ', __FILE__) . $this->url, 404);
                }
            }
            curl_close($ch);
        }
        log::add('http.com', 'Debug', __('Url : ', __FILE__) . $this->url . __("\nReponse : ", __FILE__) . $response);
        return $response;
    }

}

?>
