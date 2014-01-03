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
    require_once(dirname(__FILE__) . '/../php/core.inc.php');
    include_file('core', 'authentification', 'php');

    if (!isConnect(true)) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'save') {
        utils::processJsonObject('cron', init('crons'));
        ajax::success();
    }

    if (init('action') == 'remove') {
        $cron = cron::byId(init('id'));
        if (!is_object($cron)) {
            throw new Exception('Cron id inconnu');
        }
        $cron->remove();
        ajax::success();
    }

    if (init('action') == 'all') {
        $results = array();
        $results['crons'] = utils::o2a(cron::all());
        $results['nbCronRun'] = cron::nbCronRun();
        $results['nbProcess'] = cron::nbProcess();
        $results['nbMasterCronRun'] = (cron::jeeCronRun()) ? 1 : 0;
        $results['loadAvg'] = cron::loadAvg();
        ajax::success($results);
    }

    if (init('action') == 'start') {
        $cron = cron::byId(init('id'));
        if (!is_object($cron)) {
            throw new Exception('Cron id inconnu');
        }
        $cron->start();
        ajax::success();
    }

    if (init('action') == 'stop') {
        $cron = cron::byId(init('id'));
        if (!is_object($cron)) {
            throw new Exception('Cron id inconnu');
        }
        $cron->stop();
        ajax::success();
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));

    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
