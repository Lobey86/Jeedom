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

    if (!isConnect()) {
        throw new Exception('401 Unauthorized');
    }

    include_file('core', 'macro', 'class','macro');

    if (init('action') == 'addMacro') {
        $macro = new macro();
        $macro->setName(init('name'));
        $macro->setEqType_name('macro');
        $macro->save();
        if (file_exists(dirname(__FILE__) . '/img/defaut.png')) {
            eqLogic::saveImage($macro->getId(), file_get_contents(dirname(__FILE__) . '/img/defaut.png'));
        }
        ajax::success(array('id' => $macro->getId()));
    }

    if (init('action') == 'saveMacro') {
        $macro = macro::byId(init('id'));
        if (!is_object($macro)) {
            throw new Exception('EqReal inconnu verifié l\'id');
        }
        $macro->setName(init('name'));
        $macro->setObject_id(init('object_id'));
        $macro->setIsVisible(init('isVisible'));
        $macro->setIsEnable(init('isEnable'));
        $macro->save();

        $list_cmd_to_save = json_decode(init('cmd'), true);
        $list_cmd = macroCmd::byEqLogicId($macro->getId());

        for ($i = 0, $sCmd = count($list_cmd); $i < $sCmd; $i++) {
            foreach ($list_cmd_to_save as $cmd) {
                if (isset($list_cmd[$i]) && $cmd['id'] == $list_cmd[$i]->getId()) {
                    unset($list_cmd[$i]);
                }
            }
        }
        foreach ($list_cmd as $cmdToRemove) {
            $cmdToRemove->remove();
        }
        foreach ($list_cmd_to_save as $cmd) {
            macroExecution::emptyCmd($cmd['id']);
            foreach ($cmd['cmd_execute'] as $cmdToExecute) {
                $macroExecution = new macroExecution();
                $macroExecution->setMacroCmd_id($cmd['id']);
                $macroExecution->setExecute_cmd_id($cmdToExecute['cmd_id']);
                $macroExecution->setOption($cmdToExecute['option']);
                $macroExecution->setOrder();
                $macroExecution->save();
            }
        }
        ajax::success();
    }

    if (init('action') == 'addCmdToMacro') {
        $macroCmd = macroCmd::byId(init('id'));
        if (!is_object($macroCmd)) {
            $macroCmd = new macroCmd();
        }
        $macroCmd->setEqLogic_id(init('macroEq_id'));
        $macroCmd->setName(init('name'));
        $macroCmd->setType('action');
        $macroCmd->setSubType(init('type'));
        $macroCmd->save();
        ajax::success($macroCmd->getId());
    }

    if (init('action') == 'removeCmdToMacro') {
        $macroCmd = macroCmd::byId(init('id'));
        if (!is_object($macroCmd)) {
            throw new Exception('Macro commande id inconnu');
        }
        $macroCmd->remove();
        ajax::success();
    }

    if (init('action') == 'removeMacro') {
        $macro = macro::byId(init('id'));
        if (!is_object($macro)) {
            throw new Exception('Macro id inconnu');
        }
        $macro->remove();
        ajax::success();
    }


    if (init('action') == 'getMacro') {
        $macro = macro::byId(init('macroEq_id'));
        if (!is_object($macro)) {
            throw new Exception('Macro id inconu');
        }
        $return = utils::o2a($macro);
        $return['cmd'] = array();
        foreach ($macro->getCmd() as $cmd) {
            $info_cmd = array();
            $info_cmd['id'] = $cmd->getId();
            $info_cmd['name'] = $cmd->getName();
            $info_cmd['type'] = $cmd->getType();
            $info_cmd['subType'] = $cmd->getSubType();
            $info_cmd['cmdToExecute'] = array();
            foreach ($cmd->getCmdToExecute() as $cmdToExecute) {
                $info_cmdToExecute = array();
                $info_cmdToExecute['execute_command_id'] = $cmdToExecute->getExecute_cmd_id();
                $info_cmdToExecute['order'] = $cmdToExecute->getOrder();
                $info_cmdToExecute['option'] = $cmdToExecute->getOption();
                $cmdInfo = cmd::byId($cmdToExecute->getExecute_cmd_id());
                if (is_object($cmdInfo)) {
                    $info_cmdToExecute['eqLogic_id'] = $cmdInfo->getEqLogic_id();
                    $info_cmdToExecute['eqType_name'] = $cmdInfo->getEqType_name();
                    $info_cmd['cmdToExecute'][] = $info_cmdToExecute;
                }
            }
            $return['cmd'][] = $info_cmd;
        }
        ajax::success($return);
    }


    throw new Exception('Aucune methode correspondante');
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
