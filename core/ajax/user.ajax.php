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
    require_once(dirname(__FILE__) . '/../../core/php/core.inc.php');
    include_file('core', 'authentification', 'php');

    if (!isConnect()) {
        throw new Exception(__('401 - Accès non autorisé',__FILE__));
    }

    if (init('action') == 'all') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        ajax::success(utils::o2a(user::all()));
    }

    if (init('action') == 'save') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        $users = json_decode(init('users'), true);
        foreach ($users as $user_json) {
            $user = user::byId($user_json['id']);
            if (!is_object($user)) {
                if (config::byKey('ldap::enable') == '1') {
                    throw new Exception(__('Vous devez desactiver l\'authentification LDAP pour pouvoir editer un utilisateur',__FILE__));
                }
                $user = new user();
            }
            utils::a2o($user, $user_json);
            if (isset($user_json['password'])) {
                if (config::byKey('ldap::enable') == '1') {
                    throw new Exception(__('Vous devez desactiver l\'authentification LDAP pour pouvoir editer un utilisateur',__FILE__));
                }
                $user->setPassword(sha1($user_json['password']));
            }
            $user->save();
        }
        ajax::success();
    }

    if (init('action') == 'delUser') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        if (config::byKey('ldap::enable') == '1') {
            throw new Exception(__('Vous devez desactiver l\'authentification LDAP pour pouvoir supprimer un utilisateur',__FILE__));
        }
        $user = user::byId(init('id'));
        if (!is_object($user)) {
            throw new Exception('User id inconnu');
        }
        $user->remove();
        ajax::success();
    }

    if (init('action') == 'saveUser') {
        if (!isConnect()) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        $user_json = json_decode(init('user'), true);
        if (isset($user_json['id']) && $user_json['id'] != $_SESSION['user']->getId()) {
            throw new Exception('401 unautorized');
        }
        $login = $_SESSION['user']->getLogin();
        $rights = $_SESSION['user']->getRights();
        $password = $_SESSION['user']->getPassword();
        utils::a2o($_SESSION['user'], $user_json);
        foreach ($rights as $right => $value) {
            $_SESSION['user']->setRights($right, $value);
        }
        $_SESSION['user']->setLogin($login);
        if ($password != $_SESSION['user']->getPassword()) {
            $_SESSION['user']->setPassword(sha1($_SESSION['user']->getPassword()));
        }
        $_SESSION['user']->save();
        ajax::success();
    }

    if (init('action') == 'getUser') {
        if (!isConnect()) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        ajax::success(utils::o2a($_SESSION['user']));
    }

    if (init('action') == 'testLdapConneciton') {
        if (!isConnect('admin')) {
            throw new Exception(__('401 - Accès non autorisé',__FILE__));
        }
        $connection = user::connectToLDAP();
        if ($connection === false) {
            throw new Exception();
        }
        ajax::success();
    }

    throw new Exception(__('Aucune methode correspondante à : ',__FILE__). init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
