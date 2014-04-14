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
echo "[START UPDATE]\n";
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

$update = false;
$backup_ok = false;
try {
    require_once dirname(__FILE__) . '/../core/php/core.inc.php';
    echo __("***************Installation/Mise à jour de Jeedom " . getVersion('jeedom') . "***************\n", __FILE__);


    try {
        $curentVersion = config::byKey('version');
        if ($curentVersion != '') {
            $update = true;
        }
    } catch (Exception $e) {
        
    }
    if (isset($_GET['v'])) {
        $update = true;
    }

    if ($update) {
        if (config::byKey('update::backupBefore') == 1) {
            try {
                jeedom::backup();
            } catch (Exception $e) {
                if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                    throw $e;
                } else {
                    echo __('***ERREUR*** ', __FILE__) . $e->getMessage();
                }
            }
            $backup_ok = true;
        }
        if (isset($_GET['mode']) && $_GET['mode'] == 'force') {
            echo __("/!\ Mise à jour en mode forcée /!\ \n", __FILE__);
        }
        jeedom::stop();
        if (!isset($_GET['v'])) {
            try {
                echo __("Verification des mises à jour (git pull)\n", __FILE__);
                $repo = getGitRepo();
                if (isset($_GET['mode']) && $_GET['mode'] == 'force') {
                    echo __("Reset du dépot git (mise à jour forcée)\n", __FILE__);
                    echo $repo->run('reset --hard HEAD');
                }
                echo $repo->pull(config::byKey('git::remote'), config::byKey('git::branch'));
            } catch (Exception $e) {
                if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                    throw $e;
                } else {
                    echo __('***ERREUR*** ', __FILE__) . $e->getMessage();
                }
            }
        }
        @include dirname(__FILE__) . '/../core/config/version.config.php';

        if (version_compare(getVersion('jeedom'), $curentVersion, '=') && !isset($_GET['v'])) {
            jeedom::start();
            echo __("***************Jeedom est à jour en version ", __FILE__) . getVersion('jeedom') . "***************\n";
            echo "[END UPDATE SUCCESS]\n";
            exit();
        }
        if (isset($_GET['v'])) {
            echo __("La mise à jour ", __FILE__) . $_GET['v'] . __(" va être reapliquée. Voulez vous continuer  ? [o/N] ", __FILE__);
            if (trim(fgets(STDIN)) !== 'o' ) {
                echo __("Mise à jour forcee de Jeedom est annulée\n", __FILE__);
                jeedom::start();
                echo "[END UPDATE SUCCESS]\n";
                exit(0);
            }
            $updateSql = dirname(__FILE__) . '/update/' . $_GET['v'] . '.sql';
            if (file_exists($updateSql)) {
                try {
                    echo __("Mise a jour BDD en version : ", __FILE__) . $_GET['v'] . "\n";
                    $sql = file_get_contents($updateSql);
                    DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
                    echo "OK\n";
                } catch (Exception $e) {
                    if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                        throw $e;
                    } else {
                        echo __('***ERREUR*** ', __FILE__) . $e->getMessage();
                    }
                }
            }
            $updateScript = dirname(__FILE__) . '/update/' . $_GET['v'] . '.php';
            if (file_exists($updateScript)) {
                try {
                    echo __("Mise à jour systeme en version : ", __FILE__) . $_GET['v'] . "\n";
                    require_once $updateScript;
                    echo __("OK\n", __FILE__);
                } catch (Exception $e) {
                    if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                        throw $e;
                    } else {
                        echo __('***ERREUR*** ', __FILE__) . $e->getMessage();
                    }
                }
            }
        } else {
            while (version_compare(getVersion('jeedom'), $curentVersion, '>')) {
                $nextVersion = incrementVersion($curentVersion);
                $updateSql = dirname(__FILE__) . '/update/' . $nextVersion . '.sql';
                if (file_exists($updateSql)) {
                    try {
                        echo __("Mise à jour BDD en version : ", __FILE__) . $nextVersion . "\n";
                        $sql = file_get_contents($updateSql);
                        DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
                        echo "OK\n";
                    } catch (Exception $e) {
                        if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                            throw $e;
                        } else {
                            echo '***ERREUR*** ' . $e->getMessage();
                        }
                    }
                }
                $updateScript = dirname(__FILE__) . '/update/' . $nextVersion . '.php';
                if (file_exists($updateScript)) {
                    try {
                        echo __("Mise à jour systeme en version : ", __FILE__) . $nextVersion . "\n";
                        require_once $updateScript;
                        echo __("OK\n", __FILE__);
                    } catch (Exception $e) {
                        if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
                            throw $e;
                        } else {
                            echo __('***ERREUR*** ', __FILE__) . $e->getMessage();
                        }
                    }
                }
                $curentVersion = $nextVersion;
            }
        }
        jeedom::start();
        echo __("***************Jeedom est à jour en version ", __FILE__) . getVersion('jeedom') . "***************\n";
    } else {
        if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
            echo __("Jeedom va être installé voulez vous continuer ? [o/N] ", __FILE__);
            if (trim(fgets(STDIN)) !== 'o') {
                echo __("Installation de Jeedom est annulée\n", __FILE__);
                echo "[END UPDATE SUCCESS]\n";
                exit(0);
            }
        }
        echo __("\nInstallation de Jeedom ", __FILE__) . getVersion('jeedom') . "\n";
        $sql = file_get_contents(dirname(__FILE__) . '/install.sql');
        echo __("Installation de la base de données...", __FILE__);
        DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
        echo __("OK\n", __FILE__);
        echo __("Post installe...\n", __FILE__);
        nodejs::updateKey();
        config::save('api', config::genKey());
        echo __("Ajout des taches cron\n", __FILE__);
        $cron = new cron();
        $cron->setClass('history');
        $cron->setFunction('historize');
        $cron->setSchedule('*/5 * * * * *');
        $cron->setTimeout(5);
        $cron->save();
        $cron = new cron();
        $cron->setClass('scenario');
        $cron->setFunction('check');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(5);
        $cron->save();
        $cron = new cron();
        $cron->setClass('cmd');
        $cron->setFunction('collect');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(5);
        $cron->save();
        $cron = new cron();
        $cron->setClass('plugin');
        $cron->setFunction('cron');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(5);
        $cron->save();
        $cron = new cron();
        $cron->setClass('history');
        $cron->setFunction('archive');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(20);
        $cron->save();
        $cron = new cron();
        $cron->setClass('jeedom');
        $cron->setFunction('cron');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(60);
        $cron->save();
        $cron = new cron();
        $cron->setClass('jeedom');
        $cron->setFunction('persist');
        $cron->setSchedule('* * * * * *');
        $cron->setTimeout(5);
        $cron->save();

        echo __("Ajout de l\'utilisateur (admin,admin)\n", __FILE__);
        $user = new user();
        $user->setLogin('admin');
        $user->setPassword(sha1('admin'));
        $user->setRights('admin', 1);
        $user->save();

        if (!isset($_GET['mode']) || $_GET['mode'] != 'force') {
            echo __("Jeedom est-il installé sur un Rasberry PI ? [o/N] ", __FILE__);
            if (trim(fgets(STDIN)) === 'o') {
                config::save('cronSleepTime', 60);
                $logLevel = array('info' => 0, 'debug' => 0, 'event' => 0, 'error' => 1);
            } else {
                $logLevel = array('info' => 1, 'debug' => 0, 'event' => 1, 'error' => 1);
            }
        } else {
            config::save('cronSleepTime', 60);
            $logLevel = array('info' => 0, 'debug' => 0, 'event' => 0, 'error' => 1);
        }
        config::save('logLevel', $logLevel);
        echo "OK\n";
    }

    config::save('version', getVersion('jeedom'));
} catch (Exception $e) {
    if ($update) {
        if ($backup_ok) {
            jeedom::restore();
        }
        jeedom::start();
    }
    echo __('Erreur durant l\'installation : ', __FILE__) . $e->getMessage();
    echo __('Détails : ', __FILE__) . print_r($e->getTrace());
    echo "[END UPDATE ERROR]\n";
    throw $e;
}
echo "[END UPDATE SUCCESS]\n";

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
