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
    require_once(dirname(__FILE__) . '/../../core/php/core.inc.php');
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'update') {
        jeedom::update();
        ajax::success();
    }

    if (init('action') == 'backup') {
        jeedom::backup(true);
        ajax::success();
    }

    if (init('action') == 'restore') {
        jeedom::restore(init('backup'), true);
        ajax::success();
    }
    
      if (init('action') == 'restoreCloud') {
          market::retoreBackup(init('backup'));
        ajax::success();
    }

    if (init('action') == 'getUpdateLog') {
        ajax::success(log::get('update', 0, 3000));
    }

    if (init('action') == 'getBackupLog') {
        ajax::success(log::get('backup', 0, 3000));
    }

    if (init('action') == 'getRestoreLog') {
        ajax::success(log::get('restore', 0, 3000));
    }

    if (init('action') == 'removeBackup') {
        jeedom::removeBackup(init('backup'));
        ajax::success();
    }

    if (init('action') == 'listBackup') {
        ajax::success(jeedom::listBackup());
    }

    if (init('action') == 'getConfiguration') {
        ajax::success(jeedom::getConfiguration(init('key')));
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
