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
require_once dirname(__FILE__) . '/../../core/php/core.inc.php';

class scenarioElement {
    /*     * *************************Attributs****************************** */

    private $id;
    private $name;
    private $type;
    private $options;
    private $order = 0;
    private $log;

    /*     * ***********************Methode static*************************** */

    public static function byId($_id) {
        $values = array(
            'id' => $_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM ' . __CLASS__ . ' 
                WHERE id=:id';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function saveAjaxElement($element_ajax) {
        if (isset($element_ajax['id']) && $element_ajax['id'] != '') {
            $element_db = scenarioElement::byId($element_ajax['id']);
        } else {
            $element_db = new scenarioElement();
        }
        if (!isset($element_db) || !is_object($element_db)) {
            throw new Exception(__('Elément inconnue verifié l\'id : ', __FILE__) . $element_ajax['id']);
        }
        utils::a2o($element_db, $element_ajax);
        $element_db->save();
        $subElement_order = 0;
        $subElement_list = $element_db->getSubElement();
        $enable_subElement = array();
        foreach ($element_ajax['subElements'] as $subElement_ajax) {
            if (isset($subElement_ajax['id']) && $subElement_ajax['id'] != '') {
                $subElement_db = scenarioSubElement::byId($subElement_ajax['id']);
            } else {
                $subElement_db = new scenarioSubElement();
            }
            if (!isset($subElement_db) || !is_object($subElement_db)) {
                throw new Exception(__('Elément inconnu vérifié l\'id : ', __FILE__) . $subElement_ajax['id']);
            }
            utils::a2o($subElement_db, $subElement_ajax);
            $subElement_db->setScenarioElement_id($element_db->getId());
            $subElement_db->setOrder($subElement_order);
            $subElement_db->save();
            $subElement_order++;
            $enable_subElement[$subElement_db->getId()] = true;

            $expression_list = $subElement_db->getExpression();
            $expression_order = 0;
            $enable_expression = array();
            foreach ($subElement_ajax['expressions'] as &$expression_ajax) {
                if (isset($expression_ajax['scenarioSubElement_id']) && $expression_ajax['scenarioSubElement_id'] != $subElement_db->getId() && isset($expression_ajax['id']) && $expression_ajax['id'] != '') {
                    $expression_ajax['id'] = '';
                }
                if (isset($expression_ajax['id']) && $expression_ajax['id'] != '') {
                    $expression_db = scenarioExpression::byId($expression_ajax['id']);
                } else {
                    $expression_db = new scenarioExpression();
                }
                if (!isset($expression_db) || !is_object($expression_db)) {
                    throw new Exception(__('Expression inconnue vérifié l\'id : ', __FILE__) . $expression_ajax['id']);
                }
                utils::a2o($expression_db, $expression_ajax);
                $expression_db->setScenarioSubElement_id($subElement_db->getId());
                if ($expression_db->getType() == 'element') {
                    $expression_db->setExpression(self::saveAjaxElement($expression_ajax['element']));
                }
                $expression_db->setOrder($expression_order);
                $expression_db->save();
                $expression_order++;
                $enable_expression[$expression_db->getId()] = true;
            }
            foreach ($expression_list as $expresssion) {
                if (!isset($enable_expression[$expresssion->getId()])) {
                    $expresssion->remove();
                }
            }
        }
        foreach ($subElement_list as $subElement) {
            if (!isset($enable_subElement[$subElement->getId()])) {
                $subElement->remove();
            }
        }


        return $element_db->getId();
    }

    /*     * *********************Methode d'instance************************* */

    public function save() {
        DB::save($this);
    }

    public function remove() {
        foreach ($this->getSubElement() as $subelement) {
            $subelement->remove();
        }
        DB::remove($this);
    }

    public function execute(&$_scenario) {
        $this->setLog(__('Exécution de l\'élément : ', __FILE__) . $this->getType());
        if ($this->getType() == 'if') {
            if ($this->getSubElement('if')->execute($_scenario)) {
                return $this->getSubElement('then')->execute($_scenario);
            }
            return $this->getSubElement('else')->execute($_scenario);
        } else if ($this->getType() == 'action') {
            return $this->getSubElement('action')->execute($_scenario);
        } else if ($this->getType() == 'code') {
            return $this->getSubElement('code')->execute($_scenario);
        } else if ($this->getType() == 'for') {
            $for = $this->getSubElement('for');
            $limits = $for->getExpression();
            $limits = scenarioExpression::setTags($limits[0]->getExpression());
            if (!is_numeric($limits)) {
                $this->setLog(__('[ERREUR] La condition pour une boucle doit être un numérique : ', __FILE__) . $limits);
                throw new Exception(__('La condition pour une boucle doit être un numérique : ', __FILE__) . $limits);
            }
            $return = false;
            for ($i = 1; $i <= $limits; $i++) {
                $return = $this->getSubElement('do')->execute($_scenario);
            }
            return $return;
        }
    }

    public function getSubElement($_type = '') {
        return scenarioSubElement::byScenarioElementId($this->getId(), $_type);
    }

    public function getAjaxElement() {
        $return = utils::o2a($this);
        $return['subElements'] = array();
        foreach ($this->getSubElement() as $subElement) {
            $subElement_ajax = utils::o2a($subElement);
            $subElement_ajax['expressions'] = array();
            foreach ($subElement->getExpression() as $expression) {
                $expression_ajax = utils::o2a($expression);
                if ($expression->getType() == 'element') {
                    $element = self::byId($expression->getExpression());
                    if (is_object($element)) {
                        $expression_ajax['element'] = $element->getAjaxElement();
                    }
                }
                $expression_ajax['expression'] = jeedom::toHumanReadable($expression_ajax['expression']);
                $subElement_ajax['expressions'][] = $expression_ajax;
            }
            $return['subElements'][] = $subElement_ajax;
        }
        return $return;
    }

    public function getAllId() {
        $return = array(
            'element' => array($this->getId()),
            'subelement' => array(),
            'expression' => array(),
        );
        foreach ($this->getSubElement() as $subelement) {
            $result = $subelement->getAllId();
            $return['element'] = array_merge($return['element'], $result['element']);
            $return['subelement'] = array_merge($return['subelement'], $result['subelement']);
            $return['expression'] = array_merge($return['expression'], $result['expression']);
        }
        return $return;
    }

    public function getConsolidateLog() {
        $return = '';
        $log = $this->getLog();
        if (trim($log) != '') {
            $return .= $log . "\n";
        }
        foreach ($this->getSubElement() as $subElement) {
            $log = $subElement->getLog();
            if (trim($log) != '') {
                $logs = explode("\n", trim($log));
                foreach ($logs as $log) {
                    $return .= "\t" . $log . "\n";
                }
            }
            foreach ($subElement->getExpression() as $expression) {
                $log = $expression->getLog();
                if (trim($log) != '') {
                    $logs = explode("\n", trim($log));
                    foreach ($logs as $log) {
                        $return .= "\t\t" . $log . "\n";
                    }
                }
                if ($expression->getType() == 'element') {
                    $element = self::byId($expression->getExpression());
                    if (is_object($element)) {
                        $log = $element->getConsolidateLog();
                        if (trim($log) != '') {
                            $logs = explode("\n", trim($log));
                            foreach ($logs as $log) {
                                $return .= "\t\t" . $log . "\n";
                            }
                        }
                    }
                }
            }
        }
        return $return;
    }

    public function copy() {
        $elementCopy = clone $this;
        $elementCopy->setId('');
        $elementCopy->save();
        foreach ($this->getSubElement() as $subelement) {
            $subelement->copy($elementCopy->getId());
        }
        return $elementCopy->getId();
    }

    public function clearLog() {
        $this->setLog('');
        foreach ($this->getSubElement() as $subelement) {
            $subelement->clearLog();
        }
    }

    public function getScenario() {
        $scenario = scenario::byElement($this->getId());
        if (is_object($scenario)) {
            return $scenario;
        }
        $expression = scenarioExpression::byElement($this->getId());
        if (is_object($expression)) {
            return $expression->getSubElement()->getElement()->getScenario();
        }
        return null;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getOptions($_key = '', $_default = '') {
        return utils::getJsonAttr($this->options, $_key, $_default);
    }

    public function setOptions($_key, $_value) {
        $this->options = utils::setJsonAttr($this->options, $_key, $_value);
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

    public function getLog() {
        return $this->log;
    }

    public function setLog($log) {
        if ($log == '') {
            $this->log = '';
        } else {
            $this->log = '[' . date('Y-m-d H:i:s') . '][ELEMENT] ' . $log;
        }
        $this->save();
    }

}

?>
