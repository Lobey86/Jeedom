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

class virtual extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function event() {
        $cmd = virtualCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
        $value = init('value');
        $virtualCmd = virtualCmd::byId($cmd->getConfiguration('infoId'));
        if (is_object($virtualCmd)) {
            if ($virtualCmd->getEqLogic()->getEqType_name() != 'virtual') {
                throw new Exception('La cible de la commande virtuel n\'est pas un équipement de type virtuel');
            }
            if ($this->getSubType() != 'slider' && $this->getSubType() != 'color') {
                $value = $this->getConfiguration('value');
            }
            $virtualCmd->setConfiguration('value', $value);
            $virtualCmd->save();
        } else {
            $cmd->setConfiguration('value', $value);
            $cmd->save();
        }
        $cmd->event($value);
    }

    /*     * *********************Methode d'instance************************* */


    /*     * **********************Getteur Setteur*************************** */
}

class virtualCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getType() == 'action') {
            if ($this->getConfiguration('infoName') == '') {
                throw new Exception('Le nom de la commande info ne peut etre vide');
            }
            $actionInfo = virtualCmd::byTypeEqLogicNameCmdName('virtual', $this->getEqLogic()->getName(), $this->getConfiguration('infoName'));
            if (!is_object($actionInfo)) {
                $actionInfo = new virtualCmd();
                $actionInfo->setType('info');
                $actionInfo->setSubType('string');
                $actionInfo->setCache('enable', 0);
            }
            $actionInfo->setEventOnly(0);
            $actionInfo->setConfiguration('virtualAction', 1);
            $actionInfo->setName($this->getConfiguration('infoName'));
            $actionInfo->setEqLogic_id($this->getEqLogic_id());
            $actionInfo->setConfiguration('value', $this->getConfiguration('value'));
            $actionInfo->save();
            $this->setConfiguration('infoId', $actionInfo->getId());
        } else {
            $this->setConfiguration('calcul', cmd::humanReadableToCmd($this->getConfiguration('calcul')));
        }
    }

    public function execute($_options = null) {
        switch ($this->getType()) {
            case 'info':
                if ($this->getConfiguration('virtualAction', 0) == '0') {
                    $calcul = cmd::cmdToValue($this->getConfiguration('calcul'));
                    $test = new evaluate();
                    $result = $test->Evaluer($calcul);
                    if ($this->getSubType() == 'binary') {
                        if ($result) {
                            return 1;
                        } else {
                            return 0;
                        }
                    }
                    if (is_numeric($result)) {
                        return number_format($result, 2);
                    } else {
                        return $result;
                    }
                } else {
                    return $this->getConfiguration('value');
                }
                break;

            case 'action':
                $virtualCmd = virtualCmd::byId($this->getConfiguration('infoId'));
                if (!is_object($virtualCmd)) {
                    throw new Exception('Virtual info commande non trouvé, verifier ID');
                }
                if ($virtualCmd->getEqLogic()->getEqType_name() != 'virtual') {
                    throw new Exception('La cible de la commande virtuel n\'est pas un équipement de type virtuel');
                }
                if ($this->getSubType() == 'slider') {
                    $value = $_options['slider'];
                } else if ($this->getSubType() == 'color') {
                    $value = $_options['color'];
                } else {
                    $value = $this->getConfiguration('value');
                }
                $virtualCmd->setConfiguration('value', $value);
                $virtualCmd->save();
                $virtualCmd->event($value);
                break;
        }
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>
