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

class jeenodeReal extends eqReal {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function liste() {
        $sql = 'SELECT id,logicalId,name,type
                FROM eqReal
                WHERE cat=\'jeenode\'
                ORDER BY type DESC, name';
        return DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
    }

    /*     * *********************Methode d'instance************************* */

    public function updateLastCommunication() {
        $this->setConfiguration('lastCommunication', date('Y-m-d H:i'));
        $this->save();
    }

    public function getHttpHeader() {
        if ($this->type == 'master') {
            $masterIp = $this->getConfiguration('IP');
            $node = $this->logicalId;
        } else {
            $jeenodeMaster = jeenodeReal::byLogicalId($this->getConfiguration('masterId'), 'jeenode');
            if (count($jeenodeMaster) != 1) {
                log::add('jeenode', 'error', 'Jeenode id : ' . $this->getConfiguration('masterId') . ' n\'est pas unique');
                return;
            } else {
                $jeenodeMaster = $jeenodeMaster[0];
            }
            $masterIp = $jeenodeMaster->getConfiguration('IP');
            $node = $this->logicalId;
        }
        return 'http://' . $masterIp . '/?n=' . $node;
    }

    public function getUptime() {
        $request = new com_http($this->getHttpHeader() . '&p=0&t=u&m=?');
        return $request->exec(100, 0);
    }

    public function getFreeRam() {
        $request = new com_http($this->getHttpHeader() . '&p=0&t=r&m=?');
        return $request->exec(100, 0);
    }

    public function getBat() {
        $request = new com_http($this->getHttpHeader() . '&p=0&t=b&m=?');
        return $request->exec(100, 0);
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getPortNumber($_portNumber) {
        return jeenode::byNodeIdAndPortNumber($this->id, $_portNumber);
    }

}

class jeenode extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function byNodeIdAndPortNumber($_eqReal_id, $_portNumber) {
        $values = array(
            'eqReal_id' => $_eqReal_id,
            'portNumber' => $_portNumber,
        );
        $sql = 'SELECT id
                FROM eqLogic
                WHERE eqReal_id=:eqReal_id
                    AND logicalId=:portNumber';
        $eqLogic_id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW);
        return self::byId($eqLogic_id['id']);
    }

    public static function event() {
        $nodeId = init('n');
        $port = init('p');
        if (is_numeric(init('t'))) {
            $type = chr(init('t'));
        } else {
            $type = init('t');
        }
        $value = init('v');
        $jeenodeReal = jeenodeReal::byLogicalId($nodeId, 'jeenode');
        if (count($jeenodeReal) == 0) {
            log::add('jeenode', 'error', 'Jeenode id : ' . $nodeId . ' inconnue');
            return;
        }
        if (count($jeenodeReal) > 1) {
            log::add('jeenode', 'error', 'Jeenode id : ' . $nodeId . ' n\'est pas unique');
            return;
        }

        $jeenodeReal = $jeenodeReal[0];

        if (is_object($jeenodeReal)) {
            if ($type == 'b') {
                $linkToEquipement = 'index.php?v=d&p=jeenode&id=';
                $linkToEquipement.= $jeenodeReal->getId();
                $message = 'L\'équipement <a href="' . $linkToEquipement . '">' . $jeenodeReal->getName();
                $message .= '</a> à une batterie faible';
                message::add('jeenode', $message);
                return false;
            } else {
                $jeenode = $jeenodeReal->getPortNumber($port);
                if (is_object($jeenode)) {
                    $cmd = $jeenode->getCmdByType($type);
                    if (is_object($cmd)) {
                        $jeenodeReal->updateLastCommunication();
                        $cmd->event($value);
                    }
                }
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=jeenode&m=jeenode&id=' . $this->getEqReal_id();
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getCmdByType($_typeCmd) {
        return jeenodeCmd::byEqLogicIdAndCmdType($this->id, $_typeCmd);
    }

    public function getCmdById($_cmdId) {
        return jeenodeCmd::byId($_cmdId);
    }

}

class jeenodeCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function byEqLogicIdAndCmdType($_eqLogic_Id, $_typeCmd) {
        $values = array(
            'eqLogic_id' => $_eqLogic_Id
        );
        $sql = 'SELECT id
                FROM cmd
                WHERE eqLogic_id=:eqLogic_id';
        $list_cmd_id = DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL);
        foreach ($list_cmd_id as $cmd_id) {
            $cmd = self::byId($cmd_id['id']);
            if ($cmd->getConfiguration('type') == $_typeCmd) {
                return $cmd;
            }
        }
        return false;
    }

    private static function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        return $rgb; // returns an array with the rgb values
    }

    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
        $jeenode = $this->getEqLogic();
        $jeenodeReal = $jeenode->getEqReal();
        $mode = $this->getConfiguration('mode');
        $typeCmd = $this->getConfiguration('type');
        $port = $jeenode->getLogicalId();

        if ($jeenode->getLogicalId() == 'I2C') {
            if ($typeCmd == 'c' && $_options != null) {
                $rgb = self::hex2rgb($_options['color']);
                $request = new com_http($jeenodeReal->getHttpHeader() . '&t=' . $typeCmd . '&m=' . $mode . '&p=5&v=' . $rgb[0] . '&v1=' . $rgb[1] . '&v2=' . $rgb[2]);
                return $request->exec();
            }
        } else {
            $value = '';
            if ($typeCmd == 'p' && $_options != null) {
                $value = ($_options['slider'] / 100) * 255;
            }
            if ($_options === null && $this->getConfiguration('value') != '') {
                $value = $this->getConfiguration('value');
            }
            $request = new com_http($jeenodeReal->getHttpHeader() . '&t=' . $typeCmd . '&m=' . $mode . '&p=' . $port . '&v=' . $value);
            $result = $request->exec();
            if ($result == 'NR') {
                throw new Exception('Erreur lors de la recuperation d\'information ' . $jeenode->getName() . ' (' . $jeenodeReal->getName() . '). Retour jeenode : ' . $result);
            }
            if ($this->getType() == 'info' && $this->getConfiguration('calcul') != '') {
                $returnValue = intval($result);
                $calcul = str_replace('#V#', $returnValue, $this->getConfiguration('calcul'));
                $test = new evaluate();
                $result = $test->Evaluer($calcul);
            }
            $jeenodeReal->updateLastCommunication();
            if ($this->getType() == 'action' && $_options != null && $result != $value) {
                throw new Exception('Erreur lors de l\'éxécution de l\'action sur ' . $jeenode->getName() . ' (' . $jeenodeReal->getName() . '). Retour jeenode : ' . $result);
            }
            return $result;
        }
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
