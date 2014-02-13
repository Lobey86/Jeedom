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

class pincode extends eqLogic {

    public function preUpdate() {
        if ($this->getConfiguration('code') == '') {
            throw new Exception('Le code ne peut etre vide.');
        }
        if ($this->getConfiguration('cmd_name') == '') {
            throw new Exception('Le nom de la commande info ne peut etre vide : ' . $this->getConfiguration('cmd_name'));
        }
        if ($this->getConfiguration('cmd_subtype') == '') {
            throw new Exception('Le sous-type de la commande info ne peut etre vide : ' . $this->getConfiguration('cmd_subtype'));
        }
    }

    public function postUpdate() {
        $cmd_info = $this->getCmdInfo();
        if (!is_object($cmd_info)) {
            $cmd_info = new pincodeCmd();
            $cmd_info->setEqLogic_id($this->getId());
            $cmd_info->setType('info');
            $cmd_info->setEventOnly(1);
        }
        $cmd_info->setName($this->getConfiguration('cmd_name'));
        $cmd_info->setSubType($this->getConfiguration('cmd_subtype'));
        $cmd_info->setUnite($this->getConfiguration('cmd_unite'));
        $cmd_info->setIsVisible($this->getConfiguration('cmd_isVisible'));
        $cmd_info->save();
    }

    public function getCmdInfo() {
        foreach ($this->getCmd() as $cmd) {
            if ($cmd->getType() == 'info') {
                return $cmd;
            }
        }
        return null;
    }

    public function toHtml($_version = 'dashboard') {
        $info = '';
        $action = '';
        if ($this->getIsEnable()) {
            foreach ($this->getCmd() as $cmd) {
                if ($cmd->getIsVisible() == 1) {
                    if ($cmd->getType() == 'action') {
                        $action.=$cmd->toHtml($_version);
                    }
                    if ($cmd->getType() == 'info') {
                        $info.=$cmd->toHtml($_version);
                    }
                }
            }
        }
        $object = $this->getObject();

        $replace = array(
            '#id#' => $this->getId(),
            '#info#' => (isset($info)) ? $info : '',
            '#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
            '#eqLink#' => $this->getLinkToConfiguration(),
            '#action#' => (isset($action)) ? $action : '',
            '#object_name#' => (is_object($object)) ? $object->getName() . ' - ' : '',
            '#background_color#' => $this->getBackgroundColor(),
        );
        $html = template_replace($replace, getTemplate('core', $_version, 'eqLogic', 'pincode'));
        return $html;
    }

}

class pincodeCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function dontRemoveCmd() {
        if ($this->getType() == 'info') {
            return true;
        }
        return false;
    }

    public function toHtml($_version = 'dashboard', $options = '') {
        if ($this->getType() == 'info') {
            return parent::toHtml($_version, $options);
        }
        $replace = array(
            '#id#' => $this->getId(),
            '#name#' => $this->getName(),
        );
        $html = template_replace($replace, getTemplate('core', $_version, 'cmd', 'pincode'));

        return $html;
    }

    public function execute($_options = null) {
        if ($_options == null || $_options['code'] == '') {
            throw new Exception('Le code ne peut être vide');
        }
        $eqLogic = $this->getEqLogic();
        if ($_options['code'] == $eqLogic->getConfiguration('code')) {
            $cmd_info = $eqLogic->getCmdInfo();
            if (!is_object($cmd_info)) {
                throw new Exception('La commande info à affecter est introuvable : ' . $this->getConfiguration('cmd_name'));
            }
            $cmd_info->event($this->getConfiguration('value'));
        } else {
            throw new Exception('Le code est invalide : ' . $_options['code']);
        }
    }

}
