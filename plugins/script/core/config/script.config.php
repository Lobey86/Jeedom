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

global $listScript;
$listScript = array(
    0 => array(
        "name" => "isConnected",
        "path" => cleanPath(dirname(__FILE__) . "/../ressources/isConnected.sh"),
        "argv" => " [type] [adresse]",
        "requestType" => "script",
        "type" => "info",
        "subType" => "binary",
        "description" => "Script permettant de savoir si un périphérique est présent sur le reseaux local (par sa mac ou son ip)",
        "use" => "[chemin du script]/isConnected.sh [type] [adresse]",
        "version" => "0.1",
        "required" => "# Il faut ajouter les droits à apache (www-data) d'éxécuter la commande arp-scan<br/>
            # Dans un terminal :<br/>
            # sudo apt-get install arp-scan #installation du paquet permetant de scanner le réseaux<br/>
            # sudo visudo -s<br/>
            # Ajouter la ligne :  <br/>
            # www-data ALL=NOPASSWD: /usr/bin/arp-scan",
    ),
    1 => array(
        "name" => "hasInternet",
        "path" => cleanPath(dirname(__FILE__) . "/../ressources/hasInternet.php"),
        "argv" => " api=#API",
        "requestType" => "script",
        "type" => "info",
        "subType" => "binary",
        "description" => "Script qui renvoit 1 si il a pu pinguer 192.168.1.1 (à modifier dans le script si votre routeur à une adresse differente), 8.8.8.8 et google.fr",
        "use" => "[IMPORTANT] #API sera automatiquement remplacé par la cef api de jeedom, ne pas modifier",
        "version" => "0.1",
        "required" => "",
    ),
    2 => array(
        "name" => "neufBoxApi",
        "path" => cleanPath(dirname(__FILE__) . "/../ressources/neufBoxApi.php"),
        "argv" => " api=#API method=#method",
        "requestType" => "script",
        "type" => "info",
        "subType" => "value",
        "description" => "Script d'interface pour l'API de la neufbox",
        "use" => "#API sera automatiquement remplacé par la cef api de jeedom, ne pas modifier<br/>
                  #method est à remplacé par la methode voulu",
        "version" => "0.1",
        "required" => "",
    ),
);
?>
