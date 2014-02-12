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
        throw new Exception('401 Unauthorized');
    }

    if (init('action') == 'all') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        ajax::success(utils::o2a(user::all()));
    }

    if (init('action') == 'save') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $users = json_decode(init('users'), true);
        foreach ($users as $user_json) {
            $user = user::byId($user_json['id']);
            if (!is_object($user)) {
                if (config::byKey('ldap::enable') == '1') {
                    throw new Exception('Vous devez desactiver l\'authentification LDAP pour pouvoir editer un utilisateur');
                }
                $user = new user();
            }
            self::a2o($user, $user_json);
            if (isset($user_json['password'])) {
                if (config::byKey('ldap::enable') == '1') {
                    throw new Exception('Vous devez desactiver l\'authentification LDAP pour pouvoir editer un utilisateur');
                }
                $user->setPassword(sha1($user_json['password']));
            }
            $user->save();
        }
        ajax::success();
    }

    if (init('action') == 'delUser') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        if (config::byKey('ldap::enable') == '1') {
            throw new Exception('Vous devez desactiver l\'authentification LDAP pour pouvoir supprimer un utilisateur');
        }
        $user = user::byId(init('id'));
        if (!is_object($user)) {
            throw new Exception('User id inconnu');
        }
        $user->remove();
        ajax::success();
    }

    if (init('action') == 'saveProfil') {
        $values = json_decode(init('value'), true);
        foreach ($values as $value) {
            $_SESSION['user']->setOptions($value['key'], $value['value']);
        }
        $_SESSION['user']->save();
        ajax::success();
    }

    if (init('action') == 'testLdapConneciton') {
        if (!isConnect('admin')) {
            throw new Exception('401 Unauthorized');
        }
        $connection = user::connectToLDAP();
        if ($connection === false) {
            throw new Exception();
        }
        ajax::success();
    }

    throw new Exception('Aucune methode correspondante à : ' . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
