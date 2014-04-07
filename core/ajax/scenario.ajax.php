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

    if (init('action') == 'addScenario') {
        $scenario = new scenario();
        $scenario->setName(init('name'));
        $scenario->setIsActive(1);
        $scenario->save();
        ajax::success(array('id' => $scenario->getId()));
    }

    if (init('action') == 'changeState') {
        $scenario = scenario::byId(init('id'));
        if (!is_object($scenario)) {
            throw new Exception('Scénario ID inconnu : ' . init('id'));
        }
        switch (init('state')) {
            case 'start':
                $scenario->launch(init('force', false));
                break;
            case 'stop':
                $scenario->stop();
                break;
            case 'deactivate':
                $scenario->setIsActive(0);
                $scenario->save();
                break;
            case 'activate':
                $scenario->setIsActive(1);
                $scenario->save();
                break;
        }
        ajax::success();
    }

    if (init('action') == 'listScenarioHtml') {
        $return = array();
        foreach (scenario::all() as $scenario) {
            $return[] = $scenario->toHtml(init('version'));
        }
        ajax::success($return);
    }

    if (init('action') == 'all') {
        ajax::success(utils::o2a(scenario::all()));
    }

    if (init('action') == 'toHtml') {
        $scenario = scenario::byId(init('id'));
        if (is_object($scenario)) {
            ajax::success($scenario->toHtml(init('version')));
        }
        ajax::success();
    }

    if (init('action') == 'removeScenario') {
        $scenario = scenario::byId(init('id'));
        if (!is_object($scenario)) {
            throw new Exception('Scénario ID inconnu');
        }
        $scenario->remove();
        ajax::success();
    }

    if (init('action') == 'copyScenario') {
        $scenario = scenario::byId(init('id'));
        if (!is_object($scenario)) {
            throw new Exception('Scénario ID inconnu');
        }
        ajax::success(utils::o2a($scenario->copy(init('name'))));
    }

    if (init('action') == 'getScenario') {
        $scenario = scenario::byId(init('id'));
        if (!is_object($scenario)) {
            throw new Exception('Scénario ID inconnu');
        }
        $return = utils::o2a($scenario);
        $return['trigger'] = cmd::cmdToHumanReadable($return['trigger']);
        $return['forecast'] = $scenario->calculateScheduleDate();
        $return['elements'] = array();
        foreach ($scenario->getElement() as $element) {
            $return['elements'][] = $element->getAjaxElement();
        }

        ajax::success($return);
    }

    if (init('action') == 'saveScenario') {
        $scenario_ajax = json_decode(init('scenario'), true);
        if (isset($scenario_ajax['id'])) {
            $scenario_db = scenario::byId($scenario_ajax['id']);
        }
        if (!isset($scenario_db) || !is_object($scenario_db)) {
            throw new Exception('Scénario inconnue verifié l\'id : ' . $scenario_ajax['id']);
        }
        utils::a2o($scenario_db, $scenario_ajax);
        $scenario_db->save();
        $scenario_element_list = array();
        foreach ($scenario_ajax['elements'] as $element_ajax) {
            $scenario_element_list[] = scenarioElement::saveAjaxElement($element_ajax);
        }
        $scenario_db->setScenarioElement($scenario_element_list);
        $scenario_db->save();
        ajax::success();
    }

    if (init('action') == 'actionToHtml') {
        ajax::success(scenarioExpression::getExpressionOptions(init('expression'), init('option')));
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
