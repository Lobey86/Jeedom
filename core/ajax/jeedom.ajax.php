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

    if (init('action') == 'getInfoApplication') {
        if (!isConnect()) {
            throw new Exception(__('401 - Accès non autorisé', __FILE__), -1234);
        }
        $return = array();
        $return['user_id'] = $_SESSION['user']->getId();
        $return['nodeJsKey'] = config::byKey('nodeJsKey');
        $return['userProfils'] = $_SESSION['user']->getOptions();
        $return['plugins'] = array();
        foreach (plugin::listPlugin(true) as $plugin) {
            if ($plugin->getMobile() != '') {
                $return['plugins'][] = utils::o2a($plugin);
            }
        }
        ajax::success($return);
    }

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__), -1234);
    }

    if (init('action') == 'update') {
        jeedom::update();
        ajax::success();
    }

    if (init('action') == 'clearDate') {
        $cache = cache::byKey('jeedom::lastDate');
        if ($cache->getValue() != '') {
            $cache->remove();
        }
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
        ajax::success(jeedom::getConfiguration(init('key'), init('default')));
    }

    if (init('action') == 'flushcache') {
        cache::flush();
        cache::set('jeedom::startOK', 1, 0);
        ajax::success();
    }

    if (init('action') == 'backupupload') {
        $uploaddir = dirname(__FILE__) . '/../../backup';
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
        if (!file_exists($uploaddir)) {
            throw new Exception(__('Répertoire d\'upload non trouvé : ', __FILE__) . $uploaddir);
        }
        if (!isset($_FILES['file'])) {
            throw new Exception(__('Aucun fichier trouvé. Vérifié parametre PHP (post size limit)', __FILE__));
        }
        if (filesize($_FILES['file']['tmp_name']) > 30000000) {
            throw new Exception(__('Le fichier est trop gros (miximum 30mo)', __FILE__));
        }
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . '/' . $_FILES['file']['name'])) {
            throw new Exception(__('Impossible de déplacer le fichier temporaire', __FILE__));
        }
        ajax::success();
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
