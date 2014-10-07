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

$session_lifetime = config::byKey('session_lifetime', 24);
if (!is_numeric($session_lifetime)) {
    $session_lifetime = 24;
}
ini_set('session.gc_maxlifetime', $session_lifetime * 3600);
ini_set('session.use_cookies', 1);
if (isset($_COOKIE['sess_id'])) {
    session_id($_COOKIE['sess_id']);
}
@session_start();
setcookie('sess_id', session_id(), time() + 24 * 3600, "/", '', false, true);
@session_write_close();


if (!isConnect() && isset($_COOKIE['registerDevice']) && init('v') == 'd') {
    if (loginByKey($_COOKIE['registerDevice'], true)) {
        setcookie('registerDevice', $_COOKIE['registerDevice'], time() + 24 * 3600, "/", '', false, true);
    } else {
        setcookie('registerDevice', '', time() - 3600, "/", '', false, true);
    }
}

if (!isConnect() && config::byKey('sso:allowRemoteUser') == 1) {
    $user = user::byLogin($_SERVER['REMOTE_USER']);
    if (is_object($user)) {
        connection::success($user->getLogin());
        @session_start();
        $_SESSION['user'] = $user;
        $_SESSION['userHash'] = getUserHash();
        @session_write_close();
        log::add('connection', 'info', __('Connexion de l\'utilisateur par REMOTE_USER : ', __FILE__) . $user->getLogin());
    }
}

if (ini_get('register_globals') == '1') {
    echo __('Vous devriez mettre <b>register_globals</b> à <b>Off</b><br/>', __FILE__);
}

if (isConnect() && (!isset($_SESSION['userHash']) || getUserHash() != $_SESSION['userHash'])) {
    logout();
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

if (init('login') != '' && init('mdp') != '') {
    login(init('login'), init('mdp'));
}
if (init('connect') == '1' && (init('mdp') == '' || init('login') == '')) {
    header('Location:../../index.php?v=' . $_GET['v'] . '&p=connection&error=1');
}

if (init('logout') == 1) {
    logout();
}

if (trim(init('auiKey')) != '') {
    if (init('auiKey') == config::byKey('auiKey')) {
        $user = user::byLogin('jeedom_master');
        if (!is_object($user)) {
            $user = user::byLogin('admin');
        }
        if (is_object($user)) {
            connection::success($user->getLogin());
            @session_start();
            $_SESSION['user'] = $user;
            $_SESSION['userHash'] = getUserHash();
            @session_write_close();
            log::add('connection', 'info', __('Connexion par auikey', __FILE__));
            $getParams = '';
            unset($_GET['auth']);
            foreach ($_GET AS $var => $value) {
                $getParams.= $var . '=' . $value . '&';
            }
            if (!$_ajax) {
                if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
                    header('Location:../../index.php?' . trim($getParams, '&'));
                } else {
                    header('Location:index.php?' . trim($getParams, '&'));
                }
            }
        }
    } else {
        log::add('connection', 'info', __('Echec de connection par auikey', __FILE__));
        connection::failed();
        sleep(5);
        if (!$_ajax) {
            if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
                header('Location:../../index.php?v=' . $_GET['v'] . '&error=1');
            } else {
                header('Location:index.php?v=' . $_GET['v'] . '&error=1');
            }
        }
    }
}

/* * **************************Definition des function************************** */

function login($_login, $_password, $_ajax = false) {
    $user = user::connect($_login, $_password);
    if (is_object($user)) {
        connection::success($user->getLogin());
        @session_start();
        $_SESSION['user'] = $user;
        if (init('v') == 'd' && init('registerDevice') == 'on') {
            if ($_SESSION['user']->getOptions('registerDevice') == '') {
                $_SESSION['user']->setOptions('registerDevice', config::genKey(255));
                $_SESSION['user']->save();
            }
            setcookie('registerDevice', $_SESSION['user']->getOptions('registerDevice'), time() + 365 * 24 * 3600, "/", '', false, true);
        }
        $_SESSION['userHash'] = getUserHash();
        @session_write_close();
        log::add('connection', 'info', __('Connexion de l\'utilisateur : ', __FILE__) . $_login);
        $getParams = '';
        unset($_GET['auth']);
        foreach ($_GET AS $var => $value) {
            $getParams.= $var . '=' . $value . '&';
        }
        if (!$_ajax) {
            if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
                header('Location:../../index.php?' . trim($getParams, '&'));
            } else {
                header('Location:index.php?' . trim($getParams, '&'));
            }
        }
        return true;
    }
    connection::failed();
    sleep(5);
    if (!$_ajax) {
        if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
            header('Location:../../index.php?v=' . $_GET['v'] . '&error=1');
        } else {
            header('Location:index.php?v=' . $_GET['v'] . '&error=1');
        }
    }
    return false;
}

function loginByKey($_key, $_ajax = false) {
    $user = user::byKey($_key);
    if (is_object($user)) {
        connection::success($user->getLogin());
        @session_start();
        $_SESSION['user'] = $user;
        $_SESSION['userHash'] = getUserHash();
        @session_write_close();
        log::add('connection', 'info', __('Connexion de l\'utilisateur : ', __FILE__) . $user->getLogin());
        $getParams = '';
        unset($_GET['auth']);
        foreach ($_GET AS $var => $value) {
            $getParams.= $var . '=' . $value . '&';
        }
        if (!$_ajax) {
            if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
                header('Location:../../index.php?' . trim($getParams, '&'));
            } else {
                header('Location:index.php?' . trim($getParams, '&'));
            }
        }
        return true;
    }
    connection::failed();
    sleep(5);
    if (!$_ajax) {
        if (strpos($_SERVER['PHP_SELF'], 'core') || strpos($_SERVER['PHP_SELF'], 'desktop')) {
            header('Location:../../index.php?v=' . $_GET['v'] . '&error=1');
        } else {
            header('Location:index.php?v=' . $_GET['v'] . '&error=1');
        }
    }
    return false;
}

function logout() {
    global $isConnect;
    @session_start();
    setcookie('sess_id', '', time() - 3600, "/", '', false, true);
    setcookie('registerDevice', '', time() - 3600, "/", '', false, true);
    session_unset();
    session_destroy();
    $isConnect = array();
    return;
}

function isConnect($_right = '') {
    if (isset($_SESSION['user']) && is_object($_SESSION['user']) && $_SESSION['user']->is_Connected()) {
        if ($_right != '') {
            return ($_SESSION['user']->getRights($_right) == 1) ? true : false;
        }
        return true;
    }
    return false;
}

function getUserHash() {
    return sha1(getClientIp() . $_SERVER["HTTP_USER_AGENT"]);
}

?>
