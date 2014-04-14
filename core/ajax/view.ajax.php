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
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'editView') {
        $view = view::byId(init('id'));
        if (!is_object($view)) {
            $view = new view();
        }
        $view->setName(init('name'));
        $view->save();
        ajax::success(array('id' => $view->getId()));
    }

    if (init('action') == 'removeView') {
        $view = view::byId(init('id'));
        if (!is_object($view)) {
            throw new Exception(__('Vue non trouvé. Vérifier l\'id', __FILE__));
        }
        $view->remove();
        ajax::success();
    }

    if (init('action') == 'getView') {
        $view = view::byId(init('id'));
        if (!is_object($view)) {
            throw new Exception(__('Vue non trouvé. Vérifier l\'id', __FILE__));
        }

        $return = utils::o2a($view);
        $return['viewZone'] = array();

        foreach ($view->getviewZone() as $viewZone) {
            $viewZone_info = utils::o2a($viewZone);
            $viewZone_info['viewData'] = array();
            foreach ($viewZone->getviewData() as $viewData) {
                $viewData_info = utils::o2a($viewData);
                $viewData_info['name'] = '';

                switch ($viewData->getType()) {
                    case 'cmd':
                        $cmd = $viewData->getLinkObject();
                        if (is_object($cmd)) {
                            $viewData_info['name'] = $cmd->getHumanName();
                        }
                        break;
                    case 'eqLogic':
                        $eqLogic = $viewData->getLinkObject();
                        if (is_object($eqLogic)) {
                            $viewData_info['name'] = $eqLogic->getHumanName();
                        }
                        break;
                    case 'scenario':
                        $scenario = $viewData->getLinkObject();
                        if (is_object($scenario)) {
                            $viewData_info['name'] = __('[Scénario][', __FILE__) . $scenario->getName() . ']';
                        }
                        break;
                }
                $viewZone_info['viewData'][] = $viewData_info;
            }
            $return['viewZone'][] = $viewZone_info;
        }
        ajax::success($return);
    }


    if (init('action') == 'saveView') {
        $view = view::byId(init('view_id'));
        if (!is_object($view)) {
            throw new Exception(__('Vue non trouvé. Vérifier l\'id', __FILE__));
        }
        $view->removeviewZone();
        $viewZones = json_decode(init('viewZones'), true);

        foreach ($viewZones as $viewZone_info) {
            $viewZone = new viewZone();
            $viewZone->setView_id($view->getId());
            utils::a2o($viewZone, $viewZone_info);
            $viewZone->save();
            if (isset($viewZone_info['viewData'])) {
                foreach ($viewZone_info['viewData'] as $viewData_info) {
                    $viewData = new viewData();
                    $viewData->setviewZone_id($viewZone->getId());
                    utils::a2o($viewData, $viewData_info);
                    $viewData->save();
                }
            }
        }
        ajax::success();
    }

    if (init('action') == 'getEqLogicviewZone') {
        $viewZone = viewZone::byId(init('viewZone_id'));
        if (!is_object($viewZone)) {
            throw new Exception(__('Vue non trouvé. Vérifier l\'id', __FILE__));
        }
        $return = utils::o2a($viewZone);
        $return['eqLogic'] = array();
        foreach ($viewZone->getviewData() as $viewData) {
            $infoViewDatat = utils::o2a($viewData->getLinkObject());
            $infoViewDatat['html'] = $viewData->getLinkObject()->toHtml(init('version'));
            $return['viewData'][] = $infoViewDatat;
        }
        ajax::success($return);
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
