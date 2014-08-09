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

    public static function searchExpression($_expression) {
        $values = array(
            'expression' => '%' . $_expression . '%'
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM ' . __CLASS__ . ' 
                WHERE expression LIKE :expression';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ALL, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function byElement($_element_id) {
        $values = array(
            'expression' => $_element_id
        );
        $sql = 'SELECT ' . DB::buildField(__CLASS__) . '  
                FROM ' . __CLASS__ . ' 
                WHERE expression=:expression
                    AND `type`= "element"';
        return DB::Prepare($sql, $values, DB::FETCH_TYPE_ROW, PDO::FETCH_CLASS, __CLASS__);
    }

    public static function getExpressionOptions($_expression, $_options) {
        $startLoadTime = getmicrotime();
        $cmd = cmd::byId(str_replace('#', '', cmd::humanReadableToCmd($_expression)));
        if (is_object($cmd)) {
            $return['html'] = trim($cmd->toHtml('scenario', $_options));
        } else {
            try {
                $return['html'] = getTemplate('core', 'scenario', $_expression . '.default');
                if (is_json($_options)) {
                    $_options = json_decode($_options, true);
                }
                if (is_array($_options) && count($_options) > 0) {
                    foreach ($_options as $key => $value) {
                        $replace['#' . $key . '#'] = $value;
                    }
                }
                if (!isset($replace['#id#'])) {
                    $replace['#id#'] = rand();
                }
                $return['html'] = template_replace(cmd::cmdToHumanReadable($replace), $return['html']);
            } catch (Exception $e) {
                
            }
        }
        $replace = array('#uid#' => 'exp' . mt_rand());
        $return['html'] = translate::exec(template_replace($replace, $return['html']), 'core/template/scenario/' . $_expression . '.default');
        return $return;
    }

    /*     * ********************Fonction utiliser dans le calcule des conditions********************************* */

    public static function rand($_min, $_max) {
        return rand($_min, $_max);
    }

    public static function variable($_name, $_default = '') {
        $dataStore = dataStore::byTypeLinkIdKey('scenario', -1, trim($_name));
        if (is_object($dataStore)) {
            return $dataStore->getValue($_default);
        }
        return -1;
    }

    public static function scenario($_scenario) {
        $scenario = scenario::byId(str_replace(array('scenario', '#'), '', trim($_scenario)));
        $state = $scenario->getState();
        if ($scenario->getIsActive() == 0) {
            return -1;
        }
        switch ($state) {
            case 'stop':
                return 0;
            case 'in progress':
                return 1;
        }
        return -2;
    }

    public static function tendance($_cmd_id, $_period = '1 hour', $_threshold = '') {
        $cmd = cmd::byId(trim(str_replace('#', '', $_cmd_id)));
        if (!is_object($cmd)) {
            return null;
        }
        if ($cmd->getIsHistorized() == 0) {
            return null;
        }
        $endTime = date('Y-m-d H:i:s');
        $startTime = date('Y-m-d H:i:s', strtotime('-' . $_period . '' . $endTime));
        $tendance = $cmd->getTendance($startTime, $endTime);
        if ($_threshold != '') {
            $maxThreshold = $_threshold;
            $minThreshold = -$_threshold;
        } else {
            $maxThreshold = config::byKey('historyCalculTendanceThresholddMax');
            $minThreshold = config::byKey('historyCalculTendanceThresholddMin');
        }
        if ($tendance > $maxThreshold) {
            return 1;
        }
        if ($tendance < $minThreshold) {
            return -1;
        }
        return 0;
    }

    public static function setTags($_expression) {
        $replace = array(
            '#heure#' => date('H'),
            '#minute#' => date('i'),
            '#jour#' => date('d'),
            '#mois#' => date('m'),
            '#annee#' => date('Y'),
            '#time#' => date('Hi'),
            '#timestamp#' => time(),
            '#date#' => date('md'),
            '#semaine#' => date('W'),
            '#sjour#' => convertDayEnToFr(date('l')),
        );
        $_expression = str_replace(array_keys($replace), array_values($replace), $_expression);
        $replace = array();

        preg_match_all("/([a-z][a-z]*?)\((.*?)\)/", $_expression, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $function = $match[1];
            $arguments = explode(',', $match[2]);
            if (method_exists(__CLASS__, $function)) {
                $result = call_user_func_array(__CLASS__ . "::" . $function, $arguments);
                $replace[$match[0]] = $result;
            }
        }
        $_expression = str_replace(array_keys($replace), array_values($replace), $_expression);
        return cmd::cmdToValue($_expression);
    }

    /*     * *********************Methode d'instance************************* */

    public function execute(&$scenario) {
        $message = '';
        try {
            if ($this->getType() == 'element') {
                $element = scenarioElement::byId($this->getExpression());
                if (is_object($element)) {
                    $this->setLog(__('Exécution d\'un bloc élément : ', __FILE__) . $this->getExpression());
                    return $element->execute($scenario);
                }
            }
            $options = $this->getOptions();
            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    $options[$key] = str_replace('"', '', self::setTags($value));
                }
            }
            if ($this->getType() == 'action') {
                if ($this->getExpression() == 'icon') {
                    $this->setLog(__('Changement de l\'icone du scénario', __FILE__));
                    $scenario->setDisplay('icon', $options['icon']);
                    $scenario->save();
                    return;
                } else if ($this->getExpression() == 'sleep') {
                    if (isset($options['duration'])) {
                        try {
                            $test = new evaluate();
                            $options['duration'] = $test->Evaluer($options['duration']);
                        } catch (Exception $e) {
                            
                        }
                        if (is_numeric($options['duration']) && $options['duration'] > 0) {
                            $this->setLog(__('Pause de ', __FILE__) . $options['duration'] . __(' seconde(s)', __FILE__));
                            return sleep(intval($options['duration']));
                        }
                    }
                    $this->setLog(__('Aucune durée trouvée pour l\'action sleep ou la durée n\'est pas valide : ', __FILE__) . $options['duration']);
                    return;
                } else if ($this->getExpression() == 'stop') {
                    $this->setLog(__('Arret du scénario', __FILE__));
                    $scenario->setState('stop');
                    $scenario->setPID('');
                    $scenario->save();
                    die();
                } else if ($this->getExpression() == 'scenario') {
                    $actionScenario = scenario::byId($this->getOptions('scenario_id'));
                    if (!is_object($actionScenario)) {
                        throw new Exception(__('Action sur scénario impossible. Scénario introuvable vérifier l\'id : ', __FILE__) . $this->getOptions('scenario_id'));
                    }
                    switch ($this->getOptions('action')) {
                        case 'start':
                            $this->setLog(__('Lancement du scénario : ', __FILE__) . $actionScenario->getName());
                            $actionScenario->launch(false, __('Lancement provoque par le scenario  : ', __FILE__) . $scenario->getHumanName());
                            break;
                        case 'stop':
                            $this->setLog(__('Arrêt forcer du scénario : ', __FILE__) . $actionScenario->getName());
                            $actionScenario->stop();
                            break;
                        case 'deactivate':
                            $this->setLog(__('Désactivation du scénario : ', __FILE__) . $actionScenario->getName());
                            $actionScenario->setIsActive(0);
                            $actionScenario->save();
                            break;
                        case 'activate':
                            $this->setLog(__('Activation du scénario : ', __FILE__) . $actionScenario->getName());
                            $actionScenario->setIsActive(1);
                            $actionScenario->save();
                            break;
                    }
                    return;
                } else if ($this->getExpression() == 'variable') {
                    $value = self::setTags($this->getOptions('value'));
                    $message = __('Affectation de la variable ', __FILE__) . $this->getOptions('name') . __(' à [', __FILE__) . $value . '] = ';
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
                } else {
                    $cmd = cmd::byId(str_replace('#', '', $this->getExpression()));
                    if (is_object($cmd)) {
                        if (count($options) != 0) {
                            $this->setLog(__('Exécution de la commande ', __FILE__) . $cmd->getHumanName() . __(" avec comme option(s) : \n", __FILE__) . print_r($options, true));
                        } else {
                            $this->setLog(__('Exécution de la commande ', __FILE__) . $cmd->getHumanName());
                        }
                        return $cmd->execCmd($options);
                    }
                    $this->setLog(__('[Erreur] Aucune commande trouvée pour ', __FILE__) . $this->getExpression());
                    return;
                }
            } else if ($this->getType() == 'condition') {
                $test = new evaluate();
                $expression = self::setTags($this->getExpression());
                $message = __('Evaluation de la condition : [', __FILE__) . $expression . '] = ';
                $result = $test->Evaluer($expression);
                if (is_bool($result)) {
                    if ($result) {
                        $message .= __('Vrai', __FILE__);
                    } else {
                        $message .= __('Faux', __FILE__);
                    }
                } else {
                    $message .= $result;
                }
                $this->setLog($message);
                return $result;
            }
            if ($this->getType() == 'code') {
                $this->setLog(__('Exécution d\'un bloc code', __FILE__));
                return eval($this->getExpression());
            }
        } catch (Exception $e) {
            $this->setLog($message . $e->getMessage());
        }
    }

    public function save() {
        DB::save($this);
    }

    public function remove() {
        DB::remove($this);
    }

    public function getAllId() {
        $return = array(
            'element' => array(),
            'subelement' => array(),
            'expression' => array($this->getId()),
        );
        $result = array(
            'element' => array(),
            'subelement' => array(),
            'expression' => array(),
        );
        if ($this->getType() == 'element') {
            $element = scenarioElement::byId($this->getExpression());
            if (is_object($element)) {
                $result = $element->getAllId();
            }
        }
        $return['element'] = array_merge($return['element'], $result['element']);
        $return['subelement'] = array_merge($return['subelement'], $result['subelement']);
        $return['expression'] = array_merge($return['expression'], $result['expression']);
        return $return;
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

    public function getSubElement() {
        return scenarioSubElement::byId($this->getScenarioSubElement_id());
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
        $this->expression = jeedom::fromHumanReadable($expression);
    }

    public function getOptions($_key = '', $_default = '') {
        return utils::getJsonAttr($this->options, $_key, $_default);
    }

    public function setOptions($_key, $_value) {
        $_value = jeedom::fromHumanReadable($_value);
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
