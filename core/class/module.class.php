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

class module {
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
    private $include = array();

    /*     * ***********************Methode static*************************** */

    function __construct($_id) {
        if (!file_exists($_id)) {
            $_id = self::getPathById($_id);
            if (!file_exists($_id)) {
                throw new Exception('Module introuvable : ' . $_id);
            }
        }
        $module = @simplexml_load_file($_id);
        if (!is_object($module)) {
            throw new Exception('Module introuvable : ' . $_id);
        }
        $this->id = (string) $module->id;
        $this->name = (string) $module->name;
        $this->description = (string) $module->description;
        $this->icon = (string) $module->icon;
        $this->licence = (string) $module->licence;
        $this->author = (string) $module->author;
        $this->require = (string) $module->require;
        $this->version = (string) $module->version;
        $this->installation = (string) $module->installation;
        $this->category = (string) $module->category;
        $this->filepath = $_id;
        $this->index = (isset($module->index)) ? (string) $module->index : $module->id;
        if (isset($module->include)) {
            $this->include = array(
                'file' => (string) $module->include,
                'type' => (string) $module->include['type']
            );
        } else {
            $this->include = array(
                'file' => $module->id,
                'type' => 'class'
            );
        }
    }

    public static function getPathById($_id) {
        return dirname(__FILE__) . '/../../modules/' . $_id . '/module_info/info.xml';
    }

    public function getPathToConfigurationById() {
        if (file_exists(dirname(__FILE__) . '/../../modules/' . $this->id . '/module_info/configuration.php')) {
            return 'modules/' . $this->id . '/module_info/configuration.php';
        } else {
            return '';
        }
    }

    public static function listModule($_activateOnly = false) {
        if ($_activateOnly) {
            $sql = "SELECT module
                    FROM config
                    WHERE `key`='active'
                    AND `value`='1'";
            $results = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
            foreach ($results as $result) {
                $module = new module($result['module']);
                if ($module != null) {
                    $listModule[] = $module;
                }
            }
        } else {
            $rootModulePath = dirname(__FILE__) . '/../../modules';
            $rootModule = opendir($rootModulePath) or die('Erreur');
            $listModule = array();
            while ($dirModule = @readdir($rootModule)) {
                $pathInfoModule = $rootModulePath . '/' . $dirModule . '/module_info/info.xml';
                if (file_exists($pathInfoModule)) {
                    $module = new module($pathInfoModule);
                    if ($module != null) {
                        $listModule[] = $module;
                    }
                }
            }
        }
        usort($listModule, 'module::orderModule');
        return $listModule;
    }

    public static function orderModule($a, $b) {
        $al = strtolower($a->name);
        $bl = strtolower($b->name);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /*     * *********************Methode d'instance************************* */

    public function isActive() {
        return config::byKey('active', $this->id);
    }

    public function setIsEnable($_state) {
        if (version_compare(VERSION, $this->require) == -1 && $_state == 1) {
            throw new Exception('Votre version de jeedom n\'est pas assez récente pour activer ce module');
        }
        config::save('active', $_state, $this->id);
        foreach (eqLogic::byType($this->id) as $eqLogic) {
            $eqLogic->setIsEnable($_state);
            $eqLogic->save();
        }
        if (file_exists(dirname(__FILE__) . '/../../modules/' . $this->id . '/module_info/install.php')) {
            require_once dirname(__FILE__) . '/../../modules/' . $this->id . '/module_info/install.php';
            ob_start();
            if ($_state == 1) {
                install();
            } else {
                remove();
            }
            $out = ob_get_clean();
            log::add($this->id, 'info', $out);
        }
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
        return $this->description;
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
        return $this->installation;
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

}

?>
