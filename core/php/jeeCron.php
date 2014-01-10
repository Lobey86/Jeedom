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

if (config::byKey('api') != init('api')) {
    echo 'Clef API invalide, vous n\'etez pas autorisé à effectuer cette action';
    log::add('cron', 'Error', 'Clef API invalide, vous n\'etez pas autorisé à effectuer cette action');
    die();
}

if (init('cron_id') != '') {
    $datetime = date('Y-m-d H:i:s');
    $cron = cron::byId(init('cron_id'));
    if (!is_object($cron)) {
        echo 'Cron job non trouvé : ' . init('cron_id');
        log::add('cron', 'Error', 'Cron job non trouvé : ' . init('cron_id'));
        die();
    }
    if ($cron->getPID() != '' && $cron->getPID() != 0) {
        echo 'Le cron : ' . $cron->getClass() . '::' . $cron->getFunction() . '() est en cours';
        log::add('cron', 'Error', 'Le cron : ' . $cron->getClass() . '::' . $cron->getFunction() . '() est en cours');
        die();
    }
    log::add('cron', 'info', 'Lancement de ' . $cron->getClass() . '::' . $cron->getFunction() . '() avec le PID : ' . getmypid());
    $cron->setState('run');
    $cron->setDuration('0s');
    $cron->setPID(getmypid());
    $cron->setServer(gethostname());
    $cron->setLastRun($datetime);
    $cron->save();
    if ($cron->getClass() != '') {
        $class = $cron->getClass();
        $function = $cron->getFunction();
        if (method_exists($class, $function)) {
            if ($cron->getDeamon() == 0) {
                $class::$function();
            } else {
                while (true) {
                    $class::$function();
                    sleep(config::byKey('deamonsSleepTime'));
                    if ((strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)) / 60 >= $cron->getTimeout()) {
                        die();
                    }
                }
            }
        } else {
            $cron->setState('Not found');
            $cron->setPID('');
            $cron->setServer('');
            $cron->save();
            log::add('cron', 'error', '[Erreur] Non trouvée ' . $cron->getClass() . '::' . $cron->getFunction() . '()');
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
                    sleep(config::byKey('deamonsSleepTime'));
                    if ((strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)) / 60 >= $cron->getTimeout()) {
                        die();
                    }
                }
            }
        } else {
            $cron->setState('Not found');
            $cron->setPID('');
            $cron->setServer('');
            $cron->save();
            log::add('cron', 'error', '[Erreur] Non trouvée ' . $cron->getClass() . '::' . $cron->getFunction() . '()');
            die();
        }
    }
    $cron->setState('stop');
    $cron->setPID('');
    $cron->setServer('');
    $cron->setDuration(convertDuration(strtotime(date('Y-m-d H:i:s')) - strtotime($datetime)));
    $cron->save();
    log::add('cron', 'info', 'Fin de ' . $cron->getClass() . '::' . $cron->getFunction() . '()');
    die();
} else {
    $retry = 0;
    while (true) {
        $retry++;
        if ($retry > 10) {
            echo "Il y a deja un jeeCron qui tourne : " . cron::getPidFile() . "\n";
            log::add('cron', 'error', '[' . getmypid() . '] Lancement de Jeecron annulé car il y a deja un en cours : ' . cron::getPidFile());
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
        foreach (cron::all() as $cron) {
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
                    log::add('cron', 'error', '[Timeout] "' . $cron->getClass() . '::' . $cron->getFunction() . '()"');
                } else {
                    log::add('cron', 'info', 'Arrêt/relance du demon : "' . $cron->getClass() . '::' . $cron->getFunction() . '()"');
                }
                $cron->stop();
            }
            try {
                switch ($cron->getState()) {
                    case 'run':
                        if ($cron->getServer() == gethostname()) {
                            $cron->setDuration(convertDuration(strtotime($datetime) - strtotime($cron->getLastRun())));
                            $cron->save();
                        }
                        break;
                    case 'starting':
                        $cmd = 'php ' . dirname(__FILE__) . '/jeeCron.php';
                        $cmd.= ' api=' . init('api');
                        $cmd.= ' cron_id=' . $cron->getId();
                        if (exec('ps ax | grep "' . $cmd . '" | wc -l') < 3) {
                            shell_exec('nohup ' . $cmd . ' >> ' . log::getPathToLog('cron') . ' 2>&1 &');
                        }
                        break;
                    case 'stoping':
                        if ($cron->getServer() == gethostname()) {
                            log::add('cron', 'info', 'Arret de ' . $cron->getClass() . '::' . $cron->getFunction() . '()');
                            exec('kill ' . $cron->getPID());
                            sleep(3);
                            if ($cron->running()) {
                                exec('kill -9 ' . $cron->getPID());
                                sleep(5);
                            }
                            if ($cron->running()) {
                                $cron->setState('error');
                                $cron->setServer('');
                                $cron->setPID('');
                                $cron->save();
                                log::add('cron', 'error', '[Erreur] ' . $cron->getClass() . '::' . $cron->getFunction() . '() : Impossible d\'arreter la tache');
                            } else {
                                $cron->setState('stop');
                                $cron->setDuration(-1);
                                $cron->setPID('');
                                $cron->setServer('');
                                $cron->save();
                            }
                        }
                        break;
                }
            } catch (Exception $e) {
                $cron->setState('error');
                $cron->setPID('');
                $cron->setServer('');
                $cron->setDuration(-1);
                $cron->save();
                echo '[Erreur] ' . $cron->getClass() . '::' . $cron->getFunction() . '() : ' . $e->getMessage() . ' : ' . $e->getTraceAsString();
                log::add('cron', 'error', '[Erreur] ' . $cron->getClass() . '::' . $cron->getFunction() . '() : ' . $e->getMessage() . ' : ' . $e->getTraceAsString());
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
