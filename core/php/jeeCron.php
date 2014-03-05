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

require_once dirname(__FILE__) . "/core.inc.php";

$startTime = getmicrotime();
if (isset($argv)) {
    foreach ($argv as $arg) {
        $argList = explode('=', $arg);
        if (isset($argList[0]) && isset($argList[1])) {
            $_GET[$argList[0]] = $argList[1];
        }
    }
}

if (init('cron_id') != '') {
    $datetime = date('Y-m-d H:i:s');
    $cron = cron::byId(init('cron_id'));
    if (!is_object($cron)) {
        echo 'Cron job non trouvé : ' . init('cron_id');
        log::add('cron', 'Error', 'Cron job non trouvé : ' . init('cron_id'));
        die();
    }
    if ($cron->getNbRun() > 1) {
        log::add('cron', 'Error', 'Le cron : ' . $cron->getName() . ' est en cours (' . $cron->getNbRun() . ')');
        die('Le cron : ' . $cron->getName() . ' est en cours (' . $cron->getNbRun() . ')');
    }
    log::add('cron', 'info', 'Lancement de ' . $cron->getName() . ' avec le PID : ' . getmypid());
    try {
        $cron->setState('run');
        $cron->setDuration('0s');
        $cron->setPID(getmypid());
        $cron->setServer(gethostname());
        $cron->setLastRun($datetime);
        $cron->save();
        if ($cron->getClass() != '') {
            $class = $cron->getClass();
            $function = $cron->getFunction();
            if (class_exists($class)) {
                if (method_exists($class, $function)) {
                    if ($cron->getDeamon() == 0) {
                        $class::$function();
                    } else {
                        while (true) {
                            $class::$function();
                            sleep($cron->getDeamonSleepTime());
                            if ((strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)) / 60 >= $cron->getTimeout()) {
                                die();
                            }
                        }
                    }
                } else {
                    $cron->setState('Not found');
                    $cron->setPID();
                    $cron->setServer('');
                    $cron->setEnable(0);
                    $cron->save();
                    log::add('cron', 'error', '[Erreur] Fonction non trouvée ' . $cron->getName());
                    die();
                }
            } else {
                $cron->setState('Not found');
                $cron->setPID();
                $cron->setServer('');
                $cron->save();
                log::add('cron', 'error', '[Erreur] Classe non trouvée ' . $cron->getName());
                die();
            }
        } else {
            $function = $cron->getFunction();
            if (function_exists($function)) {
                if ($cron->getDeamon() == 0) {
                    $function();
                } else {
                    while (true) {
                        $function();
                        sleep($cron->getDeamonSleepTime());
                        if ((strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)) / 60 >= $cron->getTimeout()) {
                            die();
                        }
                    }
                }
            } else {
                $cron->setState('Not found');
                $cron->setPID();
                $cron->setServer('');
                $cron->save();
                $cron->setEnable(0);
                log::add('cron', 'error', '[Erreur] Non trouvée ' . $cron->getName());
                die();
            }
        }
        $cron->setState('stop');
        $cron->setPID();
        $cron->setServer('');
        $cron->setDuration(convertDuration(strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)));
        $cron->save();
        log::add('cron', 'info', 'Fin de ' . $cron->getName());
        die();
    } catch (Exception $e) {
        $cron->setState('error');
        $cron->setPID('');
        $cron->setServer('');
        $cron->setDuration(-1);
        $cron->save();
        echo '[Erreur] ' . $cron->getName() . ' : ' . $e->getMessage();
        log::add('cron', 'error', 'Erreur sur ' . $cron->getName() . ' : ' . $e->getMessage());
    }
} else {
    if (config::byKey('enableCron') == 0) {
        die('Tous les crons sont actuellement désactivés');
    }

    $retry = 0;
    while (true) {
        $retry++;
        if ($retry > 20) {
            echo "Il y a deja un jeeCron qui tourne : " . cron::getPidFile() . "\n";
            log::add('cron', 'info', '[' . getmypid() . '] Lancement de Jeecron annulé car il y a deja un en cours : ' . cron::getPidFile());
            die();
        }
        if (cron::jeeCronRun()) {
            sleep(1);
        } else {
            break;
        }
    }
    $sleepTime = config::byKey('cronSleepTime');

    set_time_limit(59);
    cron::setPidFile();
    while (true) {
        if (config::byKey('enableCron') == 0) {
            die('Tous les crons sont actuellement désactivés');
        }
        foreach (cron::all() as $cron) {
            try {
                $cron->refresh();
                $datetime = date('Y-m-d H:i:s');
                if ($cron->getEnable() == 1 && !$cron->running()) {
                    if ($cron->getDeamon() == 0) {
                        if ($cron->isDue()) {
                            $cron->start();
                        }
                    } else {
                        $cron->start();
                    }
                }
                if ($cron->running() && (strtotime($datetime) - strtotime($cron->getLastRun())) / 60 >= $cron->getTimeout()) {
                    if ($cron->getDeamon() == 0) {
                        log::add('cron', 'error', '[Timeout] ' . $cron->getName());
                    } else {
                        log::add('cron', 'info', 'Arrêt/relance du demon : ' . $cron->getName());
                    }
                    $cron->stop();
                }
                switch ($cron->getState()) {
                    case 'run':
                        if ($cron->getServer() == gethostname()) {
                            $cron->setDuration(convertDuration(strtotime($datetime) - strtotime($cron->getLastRun())));
                            $cron->save();
                        }
                        break;
                    case 'starting':
                        $cron->run();
                        break;
                    case 'stoping':
                        $cron->halt();
                        break;
                }
            } catch (Exception $e) {
                $cron->setState('error');
                $cron->setPID('');
                $cron->setServer('');
                $cron->setDuration(-1);
                $cron->save();
                echo '[Erreur] ' . $cron->getName() . ' : ' . $e->getMessage();
                log::add('cron', 'error', '[Erreur] ' . $cron->getName() . ' : ' . $e->getMessage());
            }
        }
        if ($sleepTime > 59) {
            die();
        }
        sleep($sleepTime);
        if (round(getmicrotime() - $startTime, 3) > 59) {
            die();
        }
    }
}
?>
