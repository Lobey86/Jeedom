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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'getScriptContent') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception('Aucune fichier trouvé : ' . $path);
        }
        if (!is_readable($path)) {
            throw new Exception('Impossible de lire : ' . $path);
        }
        if (is_dir($path)) {
            throw new Exception('Impossible de lire un dossier : ' . $path);
        }
        $allowReadPath = config::byKey('allowReadDir', 'script');
        $allowReadPath[] = config::byKey('userScriptDir', 'script');
        if (!hadFileRight($allowReadPath, $path)) {
            throw new Exception('Vous n\'etez pas autoriser à lire : ' . $path);
        }
        $pathinfo = pathinfo($path);
        $return = array(
            'content' => file_get_contents($path),
            'extension' => $pathinfo['extension']
        );
        ajax::success($return);
    }

    if (init('action') == 'saveScriptContent') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception('Aucune fichier trouvé : ' . $path);
        }
        if (!is_writable($path)) {
            throw new Exception('Impossible d\'écrire dans : ' . $path);
        }
        if (is_dir($path)) {
            throw new Exception('Impossible d\'écrire un dossier : ' . $path);
        }
        $allowWritePath = config::byKey('allowWriteDir', 'script');
        $allowWritePath[] = config::byKey('userScriptDir', 'script');
        if (!hadFileRight($allowWritePath, $path)) {
            throw new Exception('Vous n\'etez pas autoriser à écrire : ' . $path);
        }
        file_put_contents($path, init('content'));
        ajax::success();
    }

    if (init('action') == 'removeScript') {
        $path = init('path');
        if (!file_exists($path)) {
            throw new Exception('Aucune fichier trouvé : ' . $path);
        }
        if (!is_writable($path)) {
            throw new Exception('Impossible d\'écrire dans : ' . $path);
        }
        if (is_dir($path)) {
            throw new Exception('Impossible de supprimer un dossier : ' . $path);
        }
        $allowRemovePath = config::byKey('allowRemoveDir', 'script');
        $allowRemovePath[] = config::byKey('userScriptDir', 'script');
        if (!hadFileRight($allowRemovePath, $path)) {
            throw new Exception('Vous n\'etez pas autoriser supprimer : ' . $path);
        }
        unlink($path);
        ajax::success();
    }


    if (init('action') == 'addUserScript') {
        $path = config::byKey('userScriptDir', 'script') . '/' . init('name');
        if (strpos($path, '/') !== 0 || strpos($path, '\\') !== 0) {
            $path = getRootPath() . '/' . $path;
        }
        if (!touch($path)) {
            throw new Exception('Impossible d\'écrire dans : ' . $path);
        }
        ajax::success($path);
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
