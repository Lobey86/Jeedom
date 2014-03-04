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

if (php_sapi_name() != 'cli' || isset($_SERVER['REQUEST_METHOD']) || !isset($_SERVER['argc'])) {
    header("Status: 404 Not Found");
    header('HTTP/1.0 404 Not Found');
    $_SERVER['REDIRECT_STATUS'] = 404;
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}
echo "[START RESTORE]\n";
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

try {
    require_once dirname(__FILE__) . '/../core/php/core.inc.php';
    echo "***************Lancement de la restauration de Jeedom***************\n";
    global $CONFIG;
    global $BACKUP_FILE;
    if (isset($BACKUP_FILE)) {
        $_GET['backup'] = $BACKUP_FILE;
    }
    if (!isset($_GET['backup']) || $_GET['backup'] == '') {
        if (substr(config::byKey('backup::path'), 0, 1) != '/') {
            $backup_dir = dirname(__FILE__) . '/../' . config::byKey('backup::path');
        } else {
            $backup_dir = config::byKey('backup::path');
        }
        if (!file_exists($backup_dir)) {
            mkdir($backup_dir, 0770, true);
        }
        $backup = scandir($backup_dir, SCANDIR_SORT_DESCENDING);
        $backup = $backup_dir . '/' . $backup[0];
    } else {
        $backup = $_GET['backup'];
    }
    if (substr($backup, 0, 1) != '/') {
        $backup = dirname(__FILE__) . '/../' . $backup;
    }

    if (!file_exists($backup)) {
        throw new Exception('Backup non trouvé : ' . $backup);
    }

    echo "Restauration de Jeedom avec le fichier : " . $backup . "\n";


    $tmp = dirname(__FILE__) . '/../tmp/backup';
    rrmdir($tmp);
    if (!file_exists($tmp)) {
        mkdir($tmp, 0770, true);
    }
    echo "Décompression du backup : ";
    system('cd ' . $tmp . '; tar xfz ' . $backup . ' ');
    echo "OK\n";

    jeedom::stop();
    echo "Reastauration de la base de données : ";
    system("mysql --user=" . $CONFIG['db']['username'] . " --password=" . $CONFIG['db']['password'] . " " . $CONFIG['db']['dbname'] . "  < " . $tmp . "/DB_backup.sql");
    echo "OK\n";

    echo "Reastauration des fichiers : ";
    rcopy($tmp, dirname(__FILE__) . '/..', false);
    echo "OK\n";

    jeedom::start();
    echo "***************Fin de la restoration de Jeedom***************\n";
    echo "[END RESTORE SUCCESS]\n";
} catch (Exception $e) {
    jeedom::start();
    echo 'Erreur durant le backup : ' . $e->getMessage();
    echo 'Détails : ' . print_r($e->getTrace());
    echo "[END RESTORE ERROR]\n";
    throw $e;
}
?>
