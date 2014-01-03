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

require_once dirname(__FILE__) . "/core.inc.php";

if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

if (config::byKey('api') != init('api')) {
    log::add('scenario', 'Error', 'Problème dans la clef API, vous n\'etez pas autorisé à effectuer cette action');
    die('Problème dans la clef API, vous n\'etez pas autorisé à effectuer cette action');
}

$scenario = scenario::byId(init('scenario_id'));
if (!is_object($scenario)) {
    log::add('scenario', 'info', 'Scenario non trouvé verifier id : ' . init('scenario_id'));
    die('Scenario non trouvé verifier id : ' . init('scenario_id'));
}
set_time_limit($scenario->getTimeout(config::byKey('maxExecTimeScript', 1) * 60));

try {
    if (($scenario->getIsActive() == 1 || init('force') == 1) && $scenario->getState() != 'in progress') {
        $scenario->setPID(getmypid());
        $scenario->save();
        log::add('scenario', 'info', 'Verification du scenario : ' . $scenario->getName() . ' avec le PID : ' . getmypid());
        $scenario->execute();
    } else {
        die('Impossible de lancer le scenario car deja en cours ou inactif');
    }
} catch (Exception $e) {
    log::add('scenario', 'error', 'Scenario  : ' . $scenario->getName() . '. ' . 'Erreur : ' . $e->getMessage());
    $scenario->setState('error');
    $scenario->setPID('');
    $scenario->save();
    die();
}

$scenario->setPID('');
$scenario->save();
?>
