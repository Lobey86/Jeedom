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
        throw new Exception(__('401 - Accès non autorisé',__FILE__));
    }

    if (init('action') == 'removeObject') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception(__('Objet inconnu verifié l\'id', __FILE__));
        }
        $object->remove();
        ajax::success();
    }

    if (init('action') == 'byId') {
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception(__('Objet inconnu verifié l\'id : ', __FILE__) . init('id'));
        }
        ajax::success(utils::o2a($object));
    }

    if (init('action') == 'all') {
        ajax::success(utils::o2a(object::all()));
    }

    if (init('action') == 'saveObject') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        $object_json = json_decode(init('object'), true);
        $object = object::byId($object_json['id']);
        if (!is_object($object)) {
            $object = new object();
        }
        utils::a2o($object, $object_json);
        $object->save();
        ajax::success(utils::o2a($object));
    }

    if (init('action') == 'getChild') {
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception(__('Objet inconnu verifié l\'id', __FILE__));
        }
        $return = utils::o2a($object->getChild());
        ajax::success($return);
    }

    throw new Exception(__('Aucune methode correspondante à : ',__FILE__). init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
