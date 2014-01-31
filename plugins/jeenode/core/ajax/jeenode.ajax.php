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

    if (init('action') == 'addJeenode') {
        $jeenodeReal = new jeenodeReal();
        $jeenodeReal->setName(init('name'));
        $jeenodeReal->setType(init('type'));
        $jeenodeReal->setCat('jeenode');
        $jeenodeReal->save();
        ajax::success(array('id' => $jeenodeReal->getId()));
    }

    if (init('action') == 'removeJeenode') {
        $jeenodeReal = jeenodeReal::byId(init('id'));
        if (!is_object($jeenodeReal)) {
            throw new Exception('Jennode real inconnu vérifier l\'id');
        }
        $jeenodeReal->remove();
        ajax::success();
    }

    if (init('action') == 'getUptime') {
        $jeenodeReal = jeenodeReal::byId(init('id'));
        if (!is_object($jeenodeReal)) {
            throw new Exception('Jennode real inconnu vérifier l\'id');
        }
        ajax::success($jeenodeReal->getUptime());
    }


    if (init('action') == 'getFreeRam') {
        $jeenodeReal = jeenodeReal::byId(init('id'));
        if (!is_object($jeenodeReal)) {
            throw new Exception('Jennode real inconnu vérifier l\'id');
        }
        ajax::success($jeenodeReal->getFreeRam());
    }

    if (init('action') == 'getBat') {
        $jeenodeReal = jeenodeReal::byId(init('id'));
        if (!is_object($jeenodeReal)) {
            throw new Exception('Jennode real inconnu vérifier l\'id');
        }
        ajax::success($jeenodeReal->getBat());
    }

    if (init('action') == 'saveJeenode') {
        $eqReals_ajax = json_decode(init('eqReals'), true);

        foreach ($eqReals_ajax as $eqReal_ajax) {
            $eqReal_db = jeenodeReal::byId($eqReal_ajax['id']);
            if (!is_object($eqReal_db)) {
                throw new Exception('JeenodeReal inconnu verifié l\'id');
            }
            utils::a2o($eqReal_db, $eqReal_ajax);
            $eqReal_db->save();
            switch ($eqReal_db->getType()) {
                case ('jeenode' || 'roomnode') :
                    $eqLogics_ajax = $eqReal_ajax['eqLogic'];
                    $enable_eqLogic = array();
                    foreach ($eqLogics_ajax as $eqLogic_ajax) {
                        foreach ($eqLogic_ajax['configuration'] as $key => $value) {
                            if ($key == 'portType') {
                                $eqLogic_ajaxType = $value;
                            }
                        }
                        if ($eqLogic_ajaxType != 0) {
                            $eqLogic_db = new jeenode();
                            utils::a2o($eqLogic_db, $eqLogic_ajax);
                            $eqLogic_db->save();

                            $enable_eqLogic[$eqLogic_db->getId()] = true;
                            $enable_cmd = array();
                            foreach ($eqLogic_ajax['cmd'] as $cmd_ajax) {
                                $cmd_db = new jeenodeCmd();
                                $cmd_db->setEqLogic_id($eqLogic_db->getId());
                                utils::a2o($cmd_db, $cmd_ajax);
                                $cmd_db->save();
                                $enable_cmd[$cmd_db->getId()] = true;
                            }

                            //suppression des entrées non modifiées.
                            foreach ($eqLogic_db->getCmd() as $cmd_db) {
                                if (!isset($enable_cmd[$cmd_db->getId()])) {
                                    $cmd_db->remove();
                                }
                            }
                        } else {
                            $eqLogic_db = jeenode::byId($eqLogic_ajax['id']);
                            $eqLogic_db->remove();
                        }
                    }
                    foreach ($eqReal_db->getEqLogic() as $eqLogic_db) {
                        if (!isset($enable_eqLogic[$eqLogic_db->getId()])) {
                            $eqLogic_db->remove();
                        }
                    }
                    break;
            }
        }
        ajax::success();
    }


    if (init('action') == 'getJeenodeConf') {
        $jeenodeReal = jeenodeReal::byId(init('jeenodeRealId'));
        if (!is_object($jeenodeReal)) {
            throw new Exception('JeenodeReal inconnu verifié l\'id');
        }
        $return = utils::o2a($jeenodeReal);
        $return['port'] = array();
        foreach ($jeenodeReal->getEqLogic() as $eqLogic) {
            $portConfiguration = utils::o2a($eqLogic);
            $portConfiguration['cmd'] = utils::o2a($eqLogic->getCmd());
            $return['port'][] = $portConfiguration;
        }
        ajax::success($return);
    }

    throw new Exception('Aucune methode correspondante');
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
