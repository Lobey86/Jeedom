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

class plugin {
    /*     * *************************Attributs****************************** */

    private $id;
    private $name;
    private $description;
    private $licence;
    private $installation;
    private $author;
    private $require;
    private $version;
    private $category;
    private $filepath;
    private $icon;
    private $index;
    private $display;
    private $include = array();

    /*     * ***********************Methode static*************************** */

    function __construct($_id) {
        if (!file_exists($_id)) {
            $_id = self::getPathById($_id);
            if (!file_exists($_id)) {
                throw new Exception('Plugin introuvable : ' . $_id);
            }
        }
        $plugin = @simplexml_load_file($_id);
        if (!is_object($plugin)) {
            throw new Exception('Plugin introuvable : ' . $_id);
        }
        $this->id = (string) $plugin->id;
        $this->name = (string) $plugin->name;
        $this->description = (string) $plugin->description;
        $this->icon = (string) $plugin->icon;
        $this->licence = (string) $plugin->licence;
        $this->author = (string) $plugin->author;
        $this->require = (string) $plugin->require;
        $this->version = (string) $plugin->version;
        $this->installation = (string) $plugin->installation;
        $this->category = (string) $plugin->category;
        $this->filepath = $_id;
        $this->index = (isset($plugin->index)) ? (string) $plugin->index : $plugin->id;
        $this->display = (isset($plugin->display)) ? (string) $plugin->display : '';
        if (isset($plugin->include)) {
            $this->include = array(
                'file' => (string) $plugin->include,
                'type' => (string) $plugin->include['type']
            );
        } else {
            $this->include = array(
                'file' => $plugin->id,
                'type' => 'class'
            );
        }
    }

    public static function getPathById($_id) {
        return dirname(__FILE__) . '/../../plugins/' . $_id . '/plugin_info/info.xml';
    }

    public function getPathToConfigurationById() {
        if (file_exists(dirname(__FILE__) . '/../../plugins/' . $this->id . '/plugin_info/configuration.php')) {
            return 'plugins/' . $this->id . '/plugin_info/configuration.php';
        } else {
            return '';
        }
    }

    public static function listPlugin($_activateOnly = false) {
        if ($_activateOnly) {
            $sql = "SELECT plugin
                    FROM config
                    WHERE `key`='active'
                    AND `value`='1'";
            $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
            foreach ($results as $result) {
                $plugin = new plugin($result['plugin']);
                if ($plugin != null) {
                    $listPlugin[] = $plugin;
                }
            }
        } else {
            $rootPluginPath = dirname(__FILE__) . '/../../plugins';
            $rootPlugin = opendir($rootPluginPath) or die('Erreur');
            $listPlugin = array();
            while ($dirPlugin = @readdir($rootPlugin)) {
                $pathInfoPlugin = $rootPluginPath . '/' . $dirPlugin . '/plugin_info/info.xml';
                if (file_exists($pathInfoPlugin)) {
                    $plugin = new plugin($pathInfoPlugin);
                    if ($plugin != null) {
                        $listPlugin[] = $plugin;
                    }
                }
            }
        }
        usort($listPlugin, 'plugin::orderPlugin');
        return $listPlugin;
    }

    public static function orderPlugin($a, $b) {
        $al = strtolower($a->name);
        $bl = strtolower($b->name);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    public static function cron() {
        foreach (self::listPlugin(true) as $plugin) {
            $plugin_id = $plugin->getId();
            if (method_exists($plugin_id, 'cron')) {
                $plugin->launch('cron');
            }
        }
    }

    public static function start() {
        foreach (self::listPlugin(true) as $plugin) {
            $plugin_id = $plugin->getId();
            if (method_exists($plugin_id, 'start')) {
                $plugin->launch('start');
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function isActive() {
        return config::byKey('active', $this->id);
    }

    public function setIsEnable($_state) {
        if (version_compare(getVersion('jeedom'), $this->require) == -1 && $_state == 1) {
            throw new Exception('Votre version de jeedom n\'est pas assez récente pour activer ce plugin');
        }
        $alreadyActive = config::byKey('active', $this->id, 0);
        config::save('active', $_state, $this->id);
        if ($_state == 0) {
            foreach (eqLogic::byType($this->id) as $eqLogic) {
                $eqLogic->setIsEnable($_state);
                $eqLogic->setIsVisible($_state);
                $eqLogic->save();
            }
        }
        try {
            if (file_exists(dirname(__FILE__) . '/../../plugins/' . $this->id . '/plugin_info/install.php')) {
                require_once dirname(__FILE__) . '/../../plugins/' . $this->id . '/plugin_info/install.php';
                ob_start();
                if ($_state == 1) {
                    install();
                } else {
                    remove();
                }
                $out = ob_get_clean();
                log::add($this->id, 'info', $out);
            }
        } catch (Exception $e) {
            config::save('active', $alreadyActive, $this->id);
            throw $e;
        }

        if ($alreadyActive == 0) {
            $this->start();
        }
        return true;
    }

    public function status() {
        $return = array();
        $return['market_owner'] = 1;
        $return['market'] = 0;
        $updateDateTime = config::byKey('installVersionDate', $this->getId());

        try {
            $market = market::byLogicalId($this->getId());

            if (!is_object($market)) {
                $return['status'] = 'depreciated';
            } else {
                $return['market'] = 1;
                if ($market->getApi_author() == config::byKey('market::apikey')) {
                    $return['market_owner'] = 1;
                } else {
                    $return['market_owner'] = 0;
                }
            }
            if ($market->getStatus() == 'Refusé') {
                $return['status'] = 'depreciated';
            }
            if ($market->getStatus() == 'A valider') {
                $return['status'] = 'ok';
            }
            if ($market->getStatus() == 'Validé') {
                if ($updateDateTime < $market->getDatetime()) {
                    $return['status'] = 'update';
                } else {
                    $return['status'] = 'ok';
                }
            }
        } catch (Exception $e) {
            $return['status'] = 'ok';
        }

        if (!$this->isActive()) {
            $return['status'] = 'disable';
        }
        return $return;
    }

    public function launch($_function) {
        if ($_function == '') {
            throw new Exception('La fonction à lancer ne peut etre vide');
        }
        if (!class_exists($this->getId()) || !method_exists($this->getId(), $_function)) {
            throw new Exception('Il n\'existe aucune méthode : ' . $this->getId() . '::' . $_function . '()');
        }
        $cmd = 'php ' . dirname(__FILE__) . '/../../core/php/jeePlugin.php ';
        $cmd.= ' plugin_id=' . $this->getId();
        $cmd.= ' function=' . $_function;
        if (jeedom::checkOngoingThread($cmd) > 0) {
            return true;
        }
        shell_exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('plugin') . ' 2>&1 &');
        return true;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return nl2br($this->description);
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getRequire() {
        return $this->require;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getLicence() {
        return $this->licence;
    }

    public function getFilepath() {
        return $this->filepath;
    }

    public function getInstallation() {
        return nl2br($this->installation);
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getInclude() {
        return $this->include;
    }

    public function getDisplay() {
        return $this->display;
    }

    public function setDisplay($display) {
        $this->display = $display;
    }

}

?>
