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

class widget {
    /*     * *************************Attributs****************************** */

    private $type = 'none';
    private $subtype = 'none';
    private $name;
    private $path;
    private $content = '';
    private $version = 'dashboard';

    /*     * ***********************Methode static*************************** */

    public static function listWidget($_version) {
        $path = dirname(__FILE__) . '/../template/' . $_version;
        $files = ls($path, 'cmd.*', false, array('files', 'quiet'));
        $return = array();
        foreach ($files as $file) {
            $informations = explode('.', $file);
            $pathfile = $path . '/' . $file;
            $widget = new self();
            $widget->setType($informations[1]);
            $widget->setSubtype($informations[2]);
            $widget->setName($informations[3]);
            $widget->setVersion($_version);
            $widget->setContent(file_get_contents($pathfile));
            $widget->setPath($pathfile);
            $return[] = $widget;
        }
        return $return;
    }

    public static function byPath($_pathfile) {
        if (!file_exists($_pathfile)) {
            throw new Exception('Chemin jusqu\'au widget non trouvé : ' . $_pathfile);
        }
        $path_parts = pathinfo($_pathfile);
        $informations = explode('.', $path_parts['basename']);
        $widget = new self();
        $widget->setType($informations[1]);
        $widget->setSubtype($informations[2]);
        $widget->setName($informations[3]);
        $folder = explode('/', $path_parts['dirname']);
        $widget->setVersion($folder[count($folder) - 1]);
        $widget->setContent(file_get_contents($_pathfile));
        $widget->setPath($_pathfile);
        return $widget;
    }

    /*     * *********************Methode d'instance************************* */

    public function getHumanName() {
        return $this->getType() . '.' . $this->getSubtype() . '.' . $this->getName();
    }

    public function remove() {
        $allowWritePath = config::byKey('allowWriteDir', 'widget');
        if (!hadFileRight($allowWritePath, $this->getPath())) {
            throw new Exception('Vous n\'etez pas autoriser à écrire : ' . $this->generatePath());
        }
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }
    }

    public function save() {
        if (trim($this->getName()) == '') {
            throw new Exception('Le nom du widget ne peut etre vide');
        }
        $allowWritePath = config::byKey('allowWriteDir', 'widget');
        if (!hadFileRight($allowWritePath, $this->generatePath())) {
            throw new Exception('Vous n\'etez pas autoriser à écrire : ' . $this->generatePath());
        }
        if (!is_writable($this->generatePath())) {
            //throw new Exception('Fichier/dossier inaccessible en écriture : ' . $this->generatePath());
        }
        file_put_contents($this->generatePath(), $this->getContent());
        if (realpath($this->getPath()) != realpath($this->generatePath())) {
            if (file_exists($this->getPath())) {
                unlink($this->getPath());
            }
        }
        $this->setPath($this->generatePath());
        return true;
    }

    public function generatePath() {
        $pathfile = dirname(__FILE__) . '/../template/';
        $pathfile .= $this->getVersion() . '/';
        $pathfile .= 'cmd.' . $this->getType() . '.' . $this->getSubtype() . '.' . $this->getName() . '.html';
        return $pathfile;
    }

    public function displayExemple() {
        $cmds = cmd::byTypeSubType($this->getType(), $this->getSubtype());
        if (count($cmds) < 1) {
            return 'Il n\'y a aucune commande de type : ' . $this->getType() . ' et de sous-type : ' . $this->getSubtype();
        }
        foreach ($cmds as $cmd) {
            $cmd->setTemplate($this->getVersion(), $this->getName());
            $html = $cmd->toHtml($this->getVersion());
            if (trim($html) != '') {
                return $html;
            }
        }
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getSubtype() {
        return $this->subtype;
    }

    public function setSubtype($subtype) {
        $this->subtype = $subtype;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

}

?>
