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
    echo "***************Installation/Mise a jour de Jeedom " . VERSION . "***************\n";
    $update = false;
    $curentVersion = config::byKey('version');
    if ($curentVersion != '') {
        $update = true;
    }

    if ($update) {
        stopActivities();

        if (!isset($_GET['v'])) {
            echo "Verification des mises a jour (git pull)\n";
            $repo = getGitRepo();
            echo $repo->pull(config::byKey('git::remote'), config::byKey('git::branch'));
        }
        if (version_compare(VERSION, $curentVersion, '=') && !isset($_GET['v'])) {
            echo "Jeedom est installe et en derniere version : " . VERSION . "\n";
            startActivities();
            exit();
        }
        if (isset($_GET['v'])) {
            echo "La mise à jour " . $_GET['v'] . " va etre reapliquee. Voulez vous continuer  ? [o/N] ";
            if (trim(fgets(STDIN)) !== 'o') {
                echo "Mise a jour forcee de Jeedom est annulee\n";
                startActivities();
                exit(0);
            }
            $updateSql = dirname(__FILE__) . '/update/' . $_GET['v'] . '.sql';
            if (file_exists($updateSql)) {
                echo "Mise a jour BDD en version : " . $_GET['v'] . "\n";
                $sql = file_get_contents($updateSql);
                DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
                echo "OK\n";
            }
            $updateScript = dirname(__FILE__) . '/update/' . $_GET['v'] . '.php';
            if (file_exists($updateScript)) {
                echo "Mise a jour systeme en version : " . $_GET['v'] . "\n";
                require_once $updateScript;
                echo "OK\n";
            }
        } else {
            while (version_compare(VERSION, $curentVersion, '>')) {
                $nextVersion = incrementVersion($curentVersion);
                $updateSql = dirname(__FILE__) . '/update/' . $nextVersion . '.sql';
                if (file_exists($updateSql)) {
                    echo "Mise à jour BDD en version : " . $nextVersion . "\n";
                    $sql = file_get_contents($updateSql);
                    DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
                    echo "OK\n";
                }
                $updateScript = dirname(__FILE__) . '/update/' . $nextVersion . '.php';
                if (file_exists($updateScript)) {
                    echo "Mise à jour systeme en version : " . $nextVersion . "\n";
                    require_once $updateScript;
                    echo "OK\n";
                }
                $curentVersion = $nextVersion;
            }
            echo "Fin de la mise à jour de Jeedom\n";
        }
        startActivities();
    } else {
        echo "Jeedom va etre installe voulez vous continuer ? [o/N] ";
        if (trim(fgets(STDIN)) !== 'o') {
            exit(0);
        }
        echo "\nInstallation de Jeedom " . VERSION . "\n";
        $sql = file_get_contents(dirname(__FILE__) . '/install.sql');
        echo "Installation de la base de données...";
        DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
        echo "OK\n";
        echo "Post installe...\n";
        nodejs::updateKey();
        config::save('api', config::genKey());
        echo "Ajout des taches cron\n";
        $cron = new cron();
        $cron->setClass('history');
        $cron->setFunction('historize');
        $cron->setSchedule('*/5 * * * * *');
        $cron->save();
        $cron = new cron();
        $cron->setClass('scenario');
        $cron->setFunction('check');
        $cron->setSchedule('* * * * * *');
        $cron->save();
        $cron = new cron();
        $cron->setClass('cmd');
        $cron->setFunction('collect');
        $cron->setSchedule('* * * * * *');
        $cron->save();
        $cron = new cron();
        $cron->setFunction('cronModule');
        $cron->setSchedule('* * * * * *');
        $cron->save();
        $cron = new cron();
        $cron->setClass('history');
        $cron->setFunction('archive');
        $cron->setSchedule('* * * * * *');
        $cron->save();
        $cron = new cron();
        $cron->setFunction('cronCore');
        $cron->setSchedule('* * * * * *');
        $cron->save();

        echo "Ajout de l\'utilisateur (admin,admin)\n";
        $user = new user();
        $user->setLogin('admin');
        $user->setPassword(sha1('admin'));
        $user->save();
        echo "Jeedom est-il installé sur un Rasberry PI ? [o/N] ";
        if (trim(fgets(STDIN)) === 'o') {
            config::save('cronSleepTime', 60);
            $logLevel = array('info' => 0, 'debug' => 0, 'event' => 0, 'error' => 1);
        } else {
            $logLevel = array('info' => 1, 'debug' => 0, 'event' => 1, 'error' => 1);
        }
        config::save('logLevel', $logLevel);
        echo "OK\n";
    }

    config::save('version', VERSION);
} catch (Exception $e) {
    echo 'Erreur durant l\'installation : ' . $e->getMessage();
    echo 'Detail : ' . print_r($e->getTrace());
}

function incrementVersion($_version) {
    $version = explode('.', $_version);
    if ($version[2] < 99) {
        $version[2]++;
    } else {
        if ($version[1] < 99) {
            $version[1]++;
            $version[2] = 0;
        } else {
            $version[0]++;
            $version[1] = 0;
            $version[2] = 0;
        }
    }
    $returnVersion = '';
    for ($j = 0, $sVersion = count($version); $j < $sVersion; $j++) {
        $returnVersion .= $version[$j] . '.';
    }
    return trim($returnVersion, '.');
}

function stopActivities() {
    /*     * **********Arret des crons********************* */
    echo "Desactivation de toutes les taches";
    config::save('enableCron', 0);
    foreach (cron::all() as $cron) {
        if ($cron->running()) {
            $cron->halt();
            echo '.';
        }
    }
    echo " OK\n";
    echo "Attente de l'arret du cron master ";
    while (cron::jeeCronRun()) {
        echo '.';
        sleep(2);
    }
    echo " OK\n";
    /*     * *********Arret des scénarios**************** */
    echo "Desactivation de tout les scenarios";
    config::save('enableScenario', 0);
    foreach (scenario::all() as $scenario) {
        $scenario->stop();
        echo '.';
    }
    echo " OK\n";
}

function startActivities() {
    /*     * *********Réactivation des scénarios**************** */
    echo "Recuperation des mises a jour OK\n";
    echo "Reactivation des scenarios : ";
    config::save('enableScenario', 1);
    echo "OK\n";
    /*     * *********Réactivation des tâches**************** */
    echo "Reactivation des taches : ";
    config::save('enableCron', 1);
    echo "OK\n";
}