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

    if (init('action') == 'getEqLogicObject') {
        $object = object::byId(init('object_id'));

        if (!is_object($object)) {
            throw new Exception('Objet inconnu verifié l\'id');
        }
        $return = utils::o2a($object);
        $return['eqLogic'] = array();
        foreach ($object->getEqLogic() as $eqLogic) {
            if ($eqLogic->getIsVisible() == '1') {
                $info_eqLogic = array();
                $info_eqLogic['id'] = $eqLogic->getId();
                $info_eqLogic['type'] = $eqLogic->getEqType_name();
                $info_eqLogic['object_id'] = $eqLogic->getObject_id();
                $info_eqLogic['html'] = $eqLogic->toHtml(init('version'));
                $return['eqLogic'][] = $info_eqLogic;
            }
        }
        ajax::success($return);
    }

    if (init('action') == 'byId') {
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('EqLogic inconnu verifié l\'id');
        }
        ajax::success(utils::o2a($eqLogic));
    }

    if (init('action') == 'toHtml') {
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('Eqlogic inconnu verifié l\'id');
        }
        $info_eqLogic = array();
        $info_eqLogic['id'] = $eqLogic->getId();
        $info_eqLogic['type'] = $eqLogic->getEqType_name();
        $info_eqLogic['object_id'] = $eqLogic->getObject_id();
        $info_eqLogic['html'] = $eqLogic->toHtml(init('version'));
        ajax::success($info_eqLogic);
    }

    if (init('action') == 'listByType') {
        ajax::success(utils::a2o(eqLogic::byType(init('type'))));
    }

    if (init('action') == 'listByObjectAndCmdType') {
        $object_id = (init('object_id') != -1) ? init('object_id') : null;
        ajax::success(eqLogic::listByObjectAndCmdType($object_id, init('typeCmd'), init('subTypeCmd')));
    }

    if (init('action') == 'listByObject') {
        $object_id = (init('object_id') != -1) ? init('object_id') : null;
        ajax::success(utils::o2a(eqLogic::byObjectId($object_id)));
    }

    if (init('action') == 'listByTypeAndCmdType') {
        $results = eqLogic::listByTypeAndCmdType(init('type'), init('typeCmd'), init('subTypeCmd'));
        $return = array();
        foreach ($results as $result) {
            $eqLogic = eqLogic::byId($result['id']);
            $info['eqLogic'] = utils::o2a($eqLogic);
            $info['object'] = array('name' => 'Aucun');
            if (is_object($eqLogic)) {
                $object = $eqLogic->getObject();
                if (is_object($object)) {
                    $info['object'] = utils::o2a($eqLogic->getObject());
                }
            }
            $return[] = $info;
        }
        ajax::success($return);
    }

    if (init('action') == 'setIsEnable') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('EqLogic inconnu verifié l\'id');
        }
        $eqLogic->setIsEnable(init('isEnable'));
        $eqLogic->save();
        ajax::success();
    }

    /*     * **************************Gloabl Method******************************** */

    if (init('action') == 'remove') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $eqLogic = eqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('EqLogic inconnu verifié l\'id');
        }
        $eqLogic->remove();
        ajax::success();
    }

    if (init('action') == 'get') {
        $typeEqLogic = init('type');
        if ($typeEqLogic == '' || !class_exists($typeEqLogic)) {
            throw new Exception('Type incorrect (classe équipement inexistante) : ' . $typeEqLogic);
        }
        $eqLogic = $typeEqLogic::byId(init('id'));
        if (!is_object($eqLogic)) {
            throw new Exception('EqLogic inconnu verifié l\'id : ' . init('id'));
        }
        $return = utils::o2a($eqLogic);
        $return['cmd'] = utils::o2a($eqLogic->getCmd());
        ajax::success(eqLogic::toHumanReadable(cmd::cmdToHumanReadable($return)));
    }

    if (init('action') == 'save') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $eqLogicsSave = json_decode(init('eqLogic'), true);

        foreach ($eqLogicsSave as $eqLogicSave) {
            if (!is_array($eqLogicSave)) {
                throw new Exception('Informations recues incorrecte');
            }
            $typeEqLogic = init('type');
            $typeCmd = $typeEqLogic . 'Cmd';
            if ($typeEqLogic == '' || !class_exists($typeEqLogic) || !class_exists($typeCmd)) {
                throw new Exception('Type incorrect (classe commande inexistante)' . $typeCmd);
            }
            $eqLogic = null;
            if (isset($eqLogicSave['id'])) {
                $eqLogic = $typeEqLogic::byId($eqLogicSave['id']);
            }
            if (!is_object($eqLogic)) {
                $eqLogic = new $typeEqLogic();
                $eqLogic->setEqType_name(init('type'));
            }
            if (method_exists($eqLogic, 'preAjax')) {
                $eqLogic->preAjax();
            }
            utils::a2o($eqLogic, eqLogic::fromHumanReadable(cmd::humanReadableToCmd($eqLogicSave)));
            $dbList = $typeCmd::byEqLogicId($eqLogic->getId());
            $eqLogic->save();
            $enableList = array();
            if (isset($eqLogicSave['cmd'])) {
                $cmd_order = 0;
                foreach ($eqLogicSave['cmd'] as $cmd_info) {
                    $cmd = null;
                    if (isset($cmd_info['id'])) {
                        $cmd = $typeCmd::byId($cmd_info['id']);
                    }
                    if (!is_object($cmd)) {
                        $cmd = new $typeCmd();
                    }
                    $cmd->setEqLogic_id($eqLogic->getId());
                    $cmd->setOrder($cmd_order);
                    utils::a2o($cmd, $cmd_info);
                    $cmd->save();
                    $cmd_order++;
                    $enableList[$cmd->getId()] = true;
                }
            }
            //suppression des entrées non innexistante.
            foreach ($dbList as $dbObject) {
                if (!isset($enableList[$dbObject->getId()]) && !$dbObject->dontRemoveCmd()) {
                    $dbObject->remove();
                }
            }
        }
        ajax::success(utils::o2a($eqLogic));
    }


    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
