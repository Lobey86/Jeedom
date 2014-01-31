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
    $backup = dirname(__FILE__) . '/../backup';
    if (!file_exists($backup)) {
        mkdir($backup, 0770, true);
    }
    echo 'Backup des fichiers : ';
    rcopy(dirname(__FILE__) . '/..', $tmp);
    echo "OK\n";
    echo 'Backup de la base de données : ';
    system("mysqldump --host=" . $CONFIG['db']['host'] . " --user=" . $CONFIG['db']['username'] . " --password=" . $CONFIG['db']['password'] . " " . $CONFIG['db']['dbname'] . "  > " . $tmp . "/DB-" . date("d-m-Y-H\hi") . ".sql");
    echo "OK\n";

    echo 'Création de l\'archive : ';
    system('tar cfz ' . $backup . '/backup-' . date("d-m-Y-H\hi") . '.tar.gz ' . $tmp);
    echo "OK\n";
    echo 'Nettoyage des anciens backup : ';
    system('find ' . $backup . ' -mtime +' . config::byKey('backup::keepDays') . ' -print | xargs -r rm');
    echo "OK\n";
    echo "***************Fin du backup de Jeedom***************\n";
} catch (Exception $e) {
    echo 'Erreur durant le backup : ' . $e->getMessage();
    echo 'Détails : ' . print_r($e->getTrace());
}

function rcopy($src, $dst) {
    if (file_exists($dst)) {
        rrmdir($dst);
    }
    if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != ".." && $file != "tmp" && $file != "backup") {
                rcopy("$src/$file", "$dst/$file");
            }
        }
    } else if (file_exists($src)) {
        copy($src, $dst);
    }
}

// removes files and non-empty directories
function rrmdir($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rrmdir("$dir/$file");
            }
        }
        rmdir($dir);
    } else if (file_exists($dir)) {
        unlink($dir);
    }
}

?>
