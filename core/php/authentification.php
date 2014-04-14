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

require_once dirname(__FILE__) . '/core.inc.php';

ini_set('session.gc_maxlifetime', 24 * 3600);
ini_set('session.use_cookies', 1);
if (isset($_COOKIE['sess_id'])) {
    session_id($_COOKIE['sess_id']);
}
@session_start();
setcookie('sess_id', session_id(), time() + 24 * 3600, "/", '', false, true);

if (ini_get('register_globals') == '1') {
    echo __('Vous devriez mettre <b>register_globals</b> à <b>Off</b><br/>', __FILE__);
}

if (isConnect() && (!isset($_SESSION['userHash']) || getUserHash() != $_SESSION['userHash'])) {
    session_destroy();
    setcookie('sess_id', session_id(), 0, "/", '', false, true);
    $getParams = '';
    unset($_GET['auth']);
    foreach ($_GET AS $var => $value) {
        $getParams.= $var . '=' . $value . '&';
    }
    if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
        header('Location:../../index.php?' . $getParams);
    } else {
        header('Location:index.php?' . $getParams);
    }
}

if (init('login') != '' && init('login') != '') {
    login(init('login'), init('mdp'));
}

if (init('logout') == 1) {
    logout();
}

/* * **************************Definition des function************************** */

function login($_login, $_password) {
    $user = user::connect($_login, $_password);
    if (is_object($user)) {
        $_SESSION['user'] = $user;
        $_SESSION['userHash'] = getUserHash();
        log::add('connection', 'info', __('Connexion de l\'utilisateur : ', __FILE__) . $_login);
        $getParams = '';
        unset($_GET['auth']);
        foreach ($_GET AS $var => $value) {
            $getParams.= $var . '=' . $value . '&';
        }
        if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
            header('Location:../../index.php?' . trim($getParams, '&'));
        } else {
            header('Location:index.php?' . trim($getParams, '&'));
        }
        return;
    }
    sleep(5);
    if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
        header('Location:../../index.php?v=' . $_GET['v'] . '&error=1');
    } else {
        header('Location:index.php?v=' . $_GET['v'] . '&error=1');
    }
    return;
}

function logout() {
    setcookie('sess_id', '', time() - 3600, "/", '', false, true);
    $_SESSION['user'] == null;
    session_unset();
    session_destroy();
    return;
}

function isConnect($_right = '') {
    if (isset($_SESSION['user']) && is_object($_SESSION['user'])) {
        if ($_SESSION['user']->is_Connected()) {
            if ($_right != '') {
                return ($_SESSION['user']->getRights($_right) == 1) ? true : false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getUserHash() {
    $hash = getClientIp() . $_SERVER["HTTP_USER_AGENT"];
    if (isConnect()) {
        $hash .= $_SESSION['user']->getLogin();
        $hash .= $_SESSION['user']->getId();
        $hash .= $_SESSION['user']->getHash();
    }
    return sha1($hash);
}

?>
