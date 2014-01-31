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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'syncEqLogicWithRazberry') {
        zwave::syncEqLogicWithRazberry();
        ajax::success();
    }

    if (init('action') == 'changeIncludeState') {
        zwave::changeIncludeState(init('state'));
        ajax::success();
    }

    if (init('action') == 'getCommandClassInfo') {
        ajax::success(zwave::getCommandClassInfo(init('class')));
    }

    if (init('action') == 'getPluginInfo') {
        $eqLogic = zwave::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('Razberry plugin non trouvé : ' . init('id'));
        }
        ajax::success($eqLogic->getInfo());
    }

    if (init('action') == 'getDeviceConfiguration') {
        $eqLogic = zwave::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('Razberry plugin non trouvé : ' . init('id'));
        }
        ajax::success($eqLogic->getDeviceConfiguration(init('forceRefresh', false)));
    }

    if (init('action') == 'setDeviceConfiguration') {
        $eqLogic = zwave::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('Razberry plugin non trouvé : ' . init('id'));
        }
        ajax::success($eqLogic->setDeviceConfiguration(json_decode(init('configurations'), true)));
    }

    if (init('action') == 'inspectQueue') {
        ajax::success(zwave::inspectQueue());
    }

    if (init('action') == 'getRoutingTable') {
        ajax::success(zwave::getRoutingTable());
    }

    if (init('action') == 'updateRoute') {
        ajax::success(zwave::updateRoute());
    }

    throw new Exception('Aucune methode correspondante');
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
