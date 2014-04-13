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


if (!isset($_GET['v'])) {
    $useragent = $_SERVER["HTTP_USER_AGENT"];
    echo $useragent;
    if (stristr($useragent, "Android") || strpos($useragent, "iPod") || strpos($useragent, "iPhone") || strpos($useragent, "Mobile") || strpos($useragent, "WebOS") || strpos($useragent, "mobile") || strpos($useragent, "hp-tablet")) {
        header("location: index.php?v=m");
    } else {
        header("location: index.php?v=d");
    }
} else {
    require_once dirname(__FILE__) . "/core/php/core.inc.php";
    if ($_GET['v'] == "m") {
        include_file('mobile', 'index', 'php');
    } elseif ($_GET['v'] == "d") {
        if (isset($_GET['modal'])) {
            include_file('core', 'authentification', 'php');
            try {
                if (!isConnect()) {
                    throw new Exception('{{401 - Accès non autorisé}}');
                }
                include_file('desktop', init('modal'), 'modal', init('plugin'));
            } catch (Exception $e) {
                ob_end_clean(); //Clean pile after expetion (to prevent no-traduction)
                echo '<div class="alert alert-danger div_alert">';
                echo displayExeption($e);
                echo '</div>';
            }
        } else {
            include_file('desktop', 'index', 'php');
        }
    } else {
        echo "Erreur veuillez contacter l'administrateur";
    }
}
?>
