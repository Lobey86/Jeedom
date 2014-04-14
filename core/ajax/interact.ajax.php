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
    require_once dirname(__FILE__) . '/../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'all') {
        $results = utils::o2a(interactDef::all());
        foreach ($results as &$result) {
            $result['nbInteractQuery'] = count(interactQuery::byInteractDefId($result['id']));
            $result['nbEnableInteractQuery'] = count(interactQuery::byInteractDefId($result['id'], true));
            if ($result['link_type'] == 'cmd' && $result['link_id'] != '') {
                $cmd = cmd::byId($result['link_id']);
                if (is_object($cmd)) {
                    $result['link_id'] = cmd::cmdToHumanReadable('#' . $cmd->getId() . '#');
                } else {
                    if ($result['link_id'] == 0) {
                        $result['link_id'] = '';
                    }
                }
            }
        }
        ajax::success($results);
    }

    if (init('action') == 'save') {
        $interactDefs_ajax = json_decode(init('interactDefs'), true);
        $position = 0;
        foreach ($interactDefs_ajax as &$interactDef_ajax) {
            if ($interactDef_ajax['link_type'] == 'cmd') {
                $interactDef_ajax['link_id'] = str_replace('#', '', cmd::humanReadableToCmd($interactDef_ajax['link_id']));
            }
            $interactDef_ajax['position'] = $position;
            $position++;
        }
        utils::processJsonObject('interactDef', array_reverse($interactDefs_ajax));
        ajax::success();
    }

    if (init('action') == 'changeState') {
        $interactQuery = interactQuery::byId(init('id'));
        if (!is_object($interactQuery)) {
            throw new Exception(__('InteractQuery ID inconnu', __FILE__));
        }
        $interactQuery->setEnable(init('enable'));
        $interactQuery->save();
        ajax::success();
    }

    if (init('action') == 'sendChatMessage') {
        ajax::success(interactQuery::tryToReply(init('message'), null));
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
