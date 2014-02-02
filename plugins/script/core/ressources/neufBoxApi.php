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

require_once dirname(__FILE__) . "/checkApiKey.php";

foreach ($argv as $arg) {
    $argList = explode('=', $arg);
    if ($argList[0] == 'method' && !isset($_GET['method'])) {
        $_GET['method'] = $argList[1];
    }
}

$method = $_GET['method'];


$xml = new DomDocument();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.1/api/?method=auth.getToken");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$auth_getToken = curl_exec($ch);
curl_close($ch);

$xml->loadXML($auth_getToken);
$token = $xml->getElementsByTagName('auth')->item(0)->getAttribute('token');


# gestion du username
$username = "admin";
$username_hash = hash("sha256", $username);
$username_hmac = hash_hmac("sha256", $username_hash, $token);

# gestion du mot de passe
$password = "pr1sv9web8";
$password_hash = hash("sha256", $password);
$password_hmac = hash_hmac("sha256", $password_hash, $token);

# hash final
$hash = $username_hmac . $password_hmac;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.1/api/?method=auth.checkToken&token=$token&hash=$hash");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$token = curl_exec($ch);
curl_close($ch);
$xml->loadXML($token);
$token = $xml->getElementsByTagName('auth')->item(0)->getAttribute('token');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.1/api/?method=$method&token=$token");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$info = curl_exec($ch);
curl_close($ch);
echo $info;
?>
