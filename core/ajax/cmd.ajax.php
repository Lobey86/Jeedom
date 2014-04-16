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

    if (init('action') == 'toHtml') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd inconnu verifié l\'id', __FILE__));
        }
        $info_cmd = array();
        $info_cmd['id'] = $cmd->getId();
        $info_cmd['html'] = $cmd->toHtml(init('version'));
        ajax::success($info_cmd);
    }

    if (init('action') == 'execCmd') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd ID inconnu : ', __FILE__) . init('id'));
        }
        ajax::success($cmd->execCmd(init('value'), init('cache', 1)));
    }

    if (init('action') == 'getByObjectNameEqNameCmdName') {
        $cmd = cmd::byObjectNameEqLogicNameCmdName(init('object_name'), init('eqLogic_name'), init('cmd_name'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd inconnu : ', __FILE__) . init('object_name') . '/' . init('eqLogic_name') . '/' . init('cmd_name'));
        }
        ajax::success($cmd->getId());
    }

    if (init('action') == 'getByObjectNameCmdName') {
        $cmd = cmd::byObjectNameCmdName(init('object_name'), init('cmd_name'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd inconnu : ', __FILE__) . init('object_name') . '/' . init('cmd_name'), 9999);
        }
        ajax::success(utils::o2a($cmd));
    }

    if (init('action') == 'getById') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd inconnu : ', __FILE__) . init('id'), 9999);
        }
        ajax::success(utils::o2a($cmd));
    }

    if (init('action') == 'getHumanCmdName') {
        ajax::success(cmd::cmdToHumanReadable('#' . init('id') . '#'));
    }

    if (init('action') == 'byEqLogic') {
        ajax::success(utils::o2a(cmd::byEqLogicId(init('eqLogic_id'))));
    }

    if (init('action') == 'getCmd') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd ID inconnu : ', __FILE__) . init('id'));
        }
        $return = utils::o2a($cmd);
        $eqLogic = $cmd->getEqLogic();
        $return['eqLogic_name'] = $eqLogic->getName();
        $return['plugin'] = $eqLogic->getEqType_Name();
        if ($eqLogic->getObject_id() > 0) {
            $return['object_name'] = $eqLogic->getObject()->getName();
        }
        ajax::success($return);
    }

    if (init('action') == 'save') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé', __FILE__));
        }
        $cmd_ajax = json_decode(init('cmd'), true);
        $cmd = cmd::byId($cmd_ajax['id']);
        if (!is_object($cmd)) {
            $cmd = new cmd();
        }
        utils::a2o($cmd, $cmd_ajax);
        $cmd->save();
        ajax::success();
    }

    if (init('action') == 'changeHistoryPoint') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé', __FILE__));
        }
        $history = history::byCmdIdDatetime(init('cmd_id'), init('datetime'));
        if (!is_object($history)) {
            throw new Exception(__('Aucun point ne correspond pour l\'historique : ', __FILE__) . init('cmd_id') . ' - ' . init('datetime'));
        }
        $value = (init('value', null) == '') ? null : init('value', null);
        $history->setValue($value);
        $history->save();
        ajax::success();
    }

    if (init('action') == 'getHistory') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd ID inconnu : ', __FILE__) . init('id'));
        }
        $return = array();
        $data = array();
        $dateStart = null;
        $dateEnd = null;
        if (init('dateRange') != '' && init('dateRange') != 'all') {
            $dateEnd = date('Y-m-d H:i:s');
            $dateStart = date('Y-m-d H:i:s', strtotime('- ' . init('dateRange') . ' ' . $dateEnd));
        }
        if (init('dateStart') != '') {
            $dateStart = init('dateStart');
        }
        if (init('dateEnd') != '') {
            $dateEnd = init('dateEnd');
        }
        $return['maxValue'] = '';
        $return['minValue'] = '';
        foreach ($cmd->getHistory($dateStart, $dateEnd) as $history) {
            $info_history = array();
            $info_history[] = floatval(strtotime($history->getDatetime() . " UTC")) * 1000;
            $info_history[] = ($history->getValue() === null ) ? null : floatval($history->getValue());
            if ($history->getValue() > $return['maxValue'] || $return['maxValue'] == '') {
                $return['maxValue'] = $history->getValue();
            }
            if ($history->getValue() < $return['minValue'] || $return['minValue'] == '') {
                $return['minValue'] = $history->getValue();
            }
            $data[] = $info_history;
        }
        $return['cmd_name'] = $cmd->getName();
        $return['history_name'] = $cmd->getHumanName();
        $return['unite'] = $cmd->getUnite();
        $return['data'] = $data;
        $return['cmd'] = utils::o2a($cmd);
        $return['eqLogic'] = utils::o2a($cmd->getEqLogic());
        ajax::success($return);
    }

    if (init('action') == 'emptyHistory') {
        $cmd = cmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception(__('Cmd ID inconnu : ', __FILE__) . init('id'));
        }
        $cmd->emptyHistory();
        ajax::success();
    }


    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
