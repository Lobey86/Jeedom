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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class macro extends eqLogic {
    
}

class macroCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
        if ($this->getType() == 'action' && $this->getSubType() == 'other') {
            foreach ($this->getCmdToExecute() as $cmdMacro) {
                $cmd = cmd::byId($cmdMacro->getExecute_cmd_id());
                $cmd->execCmd($cmdMacro->getOption());
            }
        }
        if ($this->getType() == 'action' && ($this->getSubType() == 'slider' || $this->getSubType() == 'color')) {
            foreach ($this->getCmdToExecute() as $cmdMacro) {
                $cmd = cmd::byId($cmdMacro->getExecute_cmd_id());
                if (is_object($cmd)) {
                    $cmd->execCmd(json_encode($_options));
                }
            }
        }
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getCmdToExecute() {
        return macroExecution::byId($this->id);
    }

}

class macroExecution {
    /*     * *************************Attributs****************************** */

    private $macroCmd_id;
    private $execute_cmd_id;
    private $order;
    private $option;

    /*     * ***********************Methode static*************************** */

    public static function byId($_macroCmd_id) {
        $values = array(
            'macroCmd_id' => $_macroCmd_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '
                FROM macro
                WHERE macroCmd_id=:macroCmd_id
                ORDER BY `order`';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function emptyCmd($_macroCmd_id) {
        $values = array(
            'macroCmd_id' => $_macroCmd_id
        );
        $sql = 'DELETE FROM macro
                WHERE macroCmd_id=:macroCmd_id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
    }

    /*     * *********************Methode d'instance************************* */

    public function getTableName() {
        return 'macro';
    }

    private function maxOrderCmd() {
        if (!is_numeric($this->macroCmd_id)) {
            throw new Exception('Impossible de calculer l\'ordre veuilez d\'abord fixer l\'id');
        }
        $values = array(
            'macroCmd_id' => $this->macroCmd_id
        );
        $sql = 'SELECT MAX(`order`) as max
                FROM macro
                WHERE macroCmd_id=:macroCmd_id';
        $max = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        if ($max['max'] != null) {
            return $max['max'] + 1;
        } else {
            return 0;
        }
    }

    public function save() {
        $values = array(
            'macroCmd_id' => $this->getMacroCmd_id(),
            'execute_cmd_id' => $this->getExecute_cmd_id(),
            'order' => $this->getOrder(),
            'option' => $this->getOption()
        );
        $sql = 'INSERT INTO macro 
                SET `macroCmd_id`=:macroCmd_id,
                    `execute_cmd_id`=:execute_cmd_id,
                    `order`=:order,
                    `option`=:option';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);

        DB::save($this);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getMacroCmd_id() {
        return $this->macroCmd_id;
    }

    public function setMacroCmd_id($macroCmd_id) {
        $this->macroCmd_id = $macroCmd_id;
    }

    public function getExecute_cmd_id() {
        return $this->execute_cmd_id;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getOption() {
        return $this->option;
    }

    public function setExecute_cmd_id($execute_cmd_id) {
        $this->execute_cmd_id = $execute_cmd_id;
    }

    public function setOrder($order = '') {
        if ($order == '') {
            $this->order = $this->maxOrderCmd();
        } else {
            $this->order = $order;
        }
    }

    public function setOption($option = null) {
        $this->option = $option;
    }

}

?>
