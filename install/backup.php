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
echo "[START BACKUP]\n";
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
    echo "***************Lancement du backup de Jeedom***************\n";
    global $CONFIG;
    $tmp = dirname(__FILE__) . '/../tmp/backup';
    if (!file_exists($tmp)) {
        mkdir($tmp, 0770, true);
    }
    if (substr(config::byKey('backup::path'), 0, 1) != '/') {
        $backup_dir = dirname(__FILE__) . '/../' . config::byKey('backup::path');
    } else {
        $backup_dir = config::byKey('backup::path');
    }
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0770, true);
    }
    $bakcup_name = 'backup-' . date("d-m-Y-H\hi") . '.tar.gz';

    echo 'Backup des fichiers : ';
    rcopy(dirname(__FILE__) . '/..', $tmp, true, array('tmp', 'backup', 'log'));
    echo "OK\n";

    echo 'Backup de la base de données : ';
    system("mysqldump --host=" . $CONFIG['db']['host'] . " --user=" . $CONFIG['db']['username'] . " --password=" . $CONFIG['db']['password'] . " " . $CONFIG['db']['dbname'] . "  > " . $tmp . "/DB_backup.sql");
    echo "OK\n";

    echo 'Création de l\'archive : ';
    system('cd ' . $tmp . '; tar cfz ' . $backup_dir . '/' . $bakcup_name . ' * > /dev/null 2>&1');
    echo "OK\n";

    echo 'Nettoyage des anciens backup : ';
    system('find ' . $backup_dir . ' -mtime +' . config::byKey('backup::keepDays') . ' -print | xargs -r rm');
    echo "OK\n";

    if (config::byKey('backup::cloudUpload') == 1) {
        echo 'Envoie de la sauvegarde dans le cloud : ';
        try {
            market::sendBackup($backup_dir . '/' . $bakcup_name);
        } catch (Exception $e) {
            log::add('backup', 'error', $e->getMessage());
            echo '/!\ ' . $e->getMessage() . ' /!\\';
        }
        echo "OK\n";
    }

    echo "***************Fin du backup de Jeedom***************\n";
    echo "[END BACKUP SUCCESS]\n";
} catch (Exception $e) {
    echo 'Erreur durant le backup : ' . $e->getMessage();
    echo 'Détails : ' . print_r($e->getTrace());
    echo "[END BACKUP ERROR]\n";
    throw $e;
}
?>
