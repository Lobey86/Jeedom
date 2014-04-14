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

class scenarioExpression {
    /*     * *************************Attributs****************************** */

    private $id;
    private $scenarioSubElement_id;
    private $type;
    private $subtype;
    private $expression;
    private $options;
    private $order;
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

    public static function byscenarioSubElementId($_scenarioSubElementId) {
        $values = array(
            'scenarioSubElement_id' => $_scenarioSubElementId
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM ' . __CLASS__ . ' 
                WHERE scenarioSubElement_id=:scenarioSubElement_id
                ORDER BY `order`';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function getExpressionOptions($_expression, $_options) {
        $return = array('html' => '');
        $cmd = cmd::byId(str_replace('#', '', cmd::humanReadableToCmd($_expression)));
        if (is_object($cmd)) {
            $return['html'] = trim($cmd->toHtml('scenario', $_options));
        } else {
            try {
                $return['html'] = getTemplate('core', 'scenario', $_expression . '.default');
                if (is_json($_options)) {
                    $_options = json_decode($_options, true);
                }
                foreach ($_options as $key => $value) {
                    $replace['#' . $key . '#'] = $value;
                }
                if (!isset($replace['#id#'])) {
                    $replace['#id#'] = rand();
                }
                $return['html'] = template_replace(cmd::cmdToHumanReadable($replace), $return['html']);
            } catch (Exception $e) {
                
            }
        }
        $replace = array('#uid#' => 'exp' . rand());
        $return['html'] = template_replace($replace, $return['html']);
        return $return;
    }

    public static function setTags($_expression) {
        $_expression = cmd::cmdToValue($_expression);
        $replace = array(
            '#heure#' => date('H'),
            '#minute#' => date('i'),
            '#jour#' => date('d'),
            '#mois#' => date('m'),
            '#annee#' => date('Y'),
            '#time#' => date('Hi'),
            '#date#' => date('md'),
            '#semaine#' => date('W'),
            '#sjour#' => convertDayEnToFr(date('l')),
        );
        preg_match_all("/#rand\[([0-9]*)\-([0-9]*)\]#/", $_expression, $matches);
        if (isset($matches[1]) && isset($matches[2]) && isset($matches[1][0]) && isset($matches[2][0])) {
            $replace['#rand[' . $matches[1][0] . '-' . $matches[2][0] . ']#'] = rand($matches[1][0], $matches[2][0]);
        }
        preg_match_all("/#var\[(.*?)]#/", $_expression, $matches);
        if (isset($matches[1]) && isset($matches[1][0])) {
            $variable = explode('-', $matches[1][0]);
            $default = '';
            if (count($variable) == 2) {
                $default = $variable[1];
            }
            $variable = $variable[0];
            $dataStore = dataStore::byTypeLinkIdKey('scenario', -1, $variable);
            if (is_object($dataStore)) {
                $replace['#var[' . $matches[1][0] . ']#'] = $dataStore->getValue($default);
            }
        }
        return str_replace(array_keys($replace), array_values($replace), $_expression);
    }

    /*     * *********************Methode d'instance************************* */

    public function execute(&$scenario) {
        $message = '';
        try {
            if ($this->getType() == 'element') {
                $element = scenarioElement::byId($this->getExpression());
                if (is_object($element)) {
                    $this->setLog(__('Exécution d\'un bloc élément : ',__FILE__) . $this->getExpression());
                    return $element->execute($scenario);
                }
            }
            $options = $this->getOptions();
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $options[$key] = self::setTags($value);
                }
            }
            if ($this->getType() == 'action') {
                switch ($this->getExpression()) {
                    case 'sleep':
                        $this->setLog('Pause de ' . $options['duration'] . ' seconde(s)');
                        return sleep($options['duration']);
                        break;
                    case 'scenario':
                        $actionScenario = scenario::byId($this->getOptions('scenario_id'));
                        if (!is_object($actionScenario)) {
                            throw new Exception(__('Action sur scénario impossible. Scénario introuvable vérifier l\'id : ',__FILE__) . $this->getOptions('scenario_id'));
                        }
                        switch ($this->getOptions('action')) {
                            case 'start':
                                $this->setLog(__('Lancement du scénario : ',__FILE__) . $actionScenario->getName());
                                $actionScenario->launch();
                                break;
                            case 'stop':
                                $this->setLog(__('Arrêt forcer du scénario : ',__FILE__) . $actionScenario->getName());
                                $actionScenario->stop();
                                break;
                            case 'deactivate':
                                $this->setLog(__('Désactivation du scénario : ',__FILE__) . $actionScenario->getName());
                                $actionScenario->setIsActive(0);
                                $actionScenario->save();
                                break;
                            case 'activate':
                                $this->setLog(__('Activation du scénario : ',__FILE__) . $actionScenario->getName());
                                $actionScenario->setIsActive(1);
                                $actionScenario->save();
                                break;
                        }
                        return;
                        break;
                    case 'var':
                        $value = self::setTags($this->getOptions('value'));
                        $message = __('Affectation de la variable ',__FILE__) . $this->getOptions('name') . __(' à [',__FILE__) . $value . '] = ';
                        try {
                            $test = new evaluate();
                            $result = $test->Evaluer($value);
                            if (is_string($result)) { //Alors la valeur n'est pas un calcul
                                $result = $value;
                            }
                        } catch (Exception $e) {
                            $result = $value;
                        }
                        $message .= $result;
                        $this->setLog($message);
                        $scenario->setData($this->getOptions('name'), $result);
                        return;
                        break;
                    default:
                        $cmd = cmd::byId(str_replace('#', '', $this->getExpression()));
                        if (is_object($cmd)) {
                            if (count($options) != 0) {
                                $this->setLog(__('Exécution de la commande ',__FILE__) . $cmd->getHumanName() . __(" avec comme option(s) : \n",__FILE__) . print_r($options, true));
                            } else {
                                $this->setLog(__('Exécution de la commande ',__FILE__) . $cmd->getHumanName());
                            }
                            return $cmd->execCmd($options);
                        }
                        $this->setLog(__('[Erreur] Aucune commande trouvée pour ',__FILE__) . $this->getExpression());
                        return;
                        break;
                }
            }
            if ($this->getType() == 'condition') {
                $test = new evaluate();
                $expression = self::setTags($this->getExpression());
                $message = __('Evaluation de la condition : [',__FILE__) . $expression . '] = ';
                $result = $test->Evaluer($expression);
                if (is_bool($result)) {
                    if ($result) {
                        $message .= __('Vrai',__FILE__);
                    } else {
                        $message .= __('Faux',__FILE__);
                    }
                } else {
                    $message .= $result;
                }
                $this->setLog($message);
                return $result;
            }
            if ($this->getType() == 'code') {
                $this->setLog(__('Exécution d\'un bloc code',__FILE__));
                return eval($this->getExpression());
            }
        } catch (Exception $e) {
            $this->setLog($message . $e->getMessage());
            //throw $e;
        }
    }

    public function save() {
        DB::save($this);
    }

    public function remove() {
        if ($this->getType() == 'element') {
            $element = scenarioElement::byId($this->getExpression());
            if (is_object($element)) {
                $element->remove();
            }
        }
        DB::remove($this);
    }

    public function copy($_scenarioSubElement_id) {
        $expressionCopy = clone $this;
        $expressionCopy->setId('');
        $expressionCopy->setScenarioSubElement_id($_scenarioSubElement_id);
        $expressionCopy->save();
        if ($expressionCopy->getType() == 'element') {
            $element = scenarioElement::byId($expressionCopy->getExpression());
            if (is_object($element)) {
                $expressionCopy->setExpression($element->copy());
                $expressionCopy->save();
            }
        }
        return $expressionCopy->getId();
    }

    public function clearLog() {
        $this->setLog('');
        if ($this->getType() == 'element') {
            $element = scenarioElement::byId($this->getExpression());
            if (is_object($element)) {
                $element->clearLog();
            }
        }
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getScenarioSubElement_id() {
        return $this->scenarioSubElement_id;
    }

    public function setScenarioSubElement_id($scenarioSubElement_id) {
        $this->scenarioSubElement_id = $scenarioSubElement_id;
    }

    public function getSubtype() {
        return $this->subtype;
    }

    public function setSubtype($subtype) {
        $this->subtype = $subtype;
    }

    public function getExpression() {
        return $this->expression;
    }

    public function setExpression($expression) {
        $this->expression = cmd::humanReadableToCmd($expression);
    }

    public function getOptions($_key = '', $_default = '') {
        return utils::getJsonAttr($this->options, $_key, $_default);
    }

    public function setOptions($_key, $_value) {
        $_value = cmd::humanReadableToCmd($_value);
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
            $this->log = '[' . date('Y-m-d H:i:s') . '][EXPRESSION] ' . $log;
        }
        $this->save();
    }

}

?>
