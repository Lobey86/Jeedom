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

    if (init('action') == 'removeObject') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception('Objet inconnu verifié l\'id');
        }
        $object->remove();
        ajax::success();
    }

    if (init('action') == 'byId') {
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception('Objet inconnu verifié l\'id : ' . init('id'));
        }
        ajax::success(utils::o2a($object));
    }

    if (init('action') == 'all') {
        ajax::success(utils::o2a(object::all()));
    }

    if (init('action') == 'saveObject') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $object = new object();
        $object->setId(init('id'));
        $object->setName(init('name'));
        $object->setFather_id(init('father_id', null));
        $object->setIsVisible(init('isVisible'));
        $object->save();
        ajax::success(array('id' => $object->getId()));
    }

    if (init('action') == 'getChild') {
        $object = object::byId(init('id'));
        if (!is_object($object)) {
            throw new Exception('Objet inconnu verifié l\'id');
        }
        $return = utils::o2a($object->getChild());
        ajax::success($return);
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
