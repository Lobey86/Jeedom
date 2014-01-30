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

    if (!isConnect()) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'genKeyAPI') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        config::save('api', config::genKey());
        ajax::success(config::byKey('api'));
    }

    if (init('action') == 'getKey') {
        $keys = init('key');
        if ($keys == '') {
            throw new Exception('Aucune clef demandée');
        }
        if (is_json($keys)) {
            $keys = json_decode($keys, true);
            $return = array();
            foreach ($keys as $key => $value) {
                $return[$key] = config::byKey($key, init('module'));
            }
            ajax::success($return);
        } else {
            ajax::success(config::byKey(init('key')));
        }
    }

    if (init('action') == 'genNodeJsKey') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        nodejs::updateKey();
        ajax::success(config::byKey('nodeJsKey'));
    }

    if (init('action') == 'clearLog') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        log::clear(init('logfile'));
        ajax::success();
    }

    if (init('action') == 'removeLog') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        log::remove(init('logfile'));
        ajax::success();
    }

    if (init('action') == 'addKey') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $values = json_decode(init('value'), true);
        foreach ($values as $key => $value) {
            config::save($key, $value, init('module', 'core'));
        }
        ajax::success();
    }


    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
