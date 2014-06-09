
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

$(function() {

    printUsers();

    $("#bt_addUser").on('click', function(event) {
        $.hideAlert();
        $('#in_newUserLogin').value('');
        $('#in_newUserMdp').value('');
        $('#md_newUser').modal('show');
    });

    $("#bt_newUserSave").on('click', function(event) {
        $.hideAlert();
        var user = [{login: $('#in_newUserLogin').value(), password: $('#in_newUserMdp').value()}];
        saveUser(user);
        $('#md_newUser').modal('hide');
    });

    $("#bt_saveUser").on('click', function(event) {
        var users = $('#table_user tbody tr').getValues('.userAttr');
        saveUser(users);
    });

    $("#table_user").delegate(".del_user", 'click', function(event) {
        $.hideAlert();
        var user = {id: $(this).closest('tr').find('.userAttr[data-l1key=id]').value()};
        bootbox.confirm('{{Etes-vous sûr de vouloir supprimer cet utilisateur ?}}', function(result) {
            if (result) {
                delUser(user);
            }
        });
    });

    $("#table_user").delegate(".change_mdp_user", 'click', function(event) {
        $.hideAlert();
        var user = {id: $(this).closest('tr').find('.userAttr[data-l1key=id]').value(), login: $(this).closest('tr').find('.userAttr[data-l1key=login]').value()};
        bootbox.prompt("{{Quel est le nouveau mot de passe ?}}", function(result) {
            if (result !== null) {
                user.password = result;
                addEditUser(user);
            }
        });
    });

    loadConfiguration($('body'));

    $('body').delegate('.userAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $('body').delegate('.configKey', 'change', function() {
        modifyWithoutSave = true;
    });
});

function printUsers() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "all"
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }

            $('#table_user tbody').empty();
            for (var i in data.result) {
                var ligne = '<tr><td class="login">';
                ligne += '<span class="userAttr" data-l1key="id" style="display : none;"/>';
                ligne += '<span class="userAttr" data-l1key="login" />';
                ligne += '</td>';
                ligne += '<td>';
                if (ldapEnable != '1') {
                    ligne += '<a class="btn btn-xs btn-danger pull-right del_user"><i class="fa fa-trash-o"></i> {{Supprimer}}</a>';
                    ligne += '<a class="btn btn-xs btn-warning pull-right change_mdp_user"><i class="fa fa-pencil"></i> {{Changer le mot de passe}}</a>';
                }
                ligne += '</td>';
                ligne += '<td>';
                ligne += '<input type="checkbox" class="userAttr" data-l1key="rights" data-l2key="admin"/> Admin';
                ligne += '</td>';
                ligne += '</tr>';
                $('#table_user tbody').append(ligne);
                $('#table_user tbody tr:last').setValues(data.result[i], '.userAttr');
                modifyWithoutSave = false;
            }
        }
    });
}


function delUser(_user) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "delUser",
            id: _user.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            printUsers();
            $.hideLoading();
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{L\'utilisateur a bien été supprimé}}', level: 'success'});
        }
    });
}

function saveUser(_users) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "save",
            users: json_encode(_users)
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            printUsers();
            $.hideLoading();
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Sauvegarde effetuée}}', level: 'success'});
            modifyWithoutSave = false;
        }
    });
}

function saveConfiguration(_el) {
    var configuration = _el.getValues('.configKey');
    configuration = configuration[0];
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "addKey",
            value: json_encode(configuration)
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Sauvegarde effetuée}}', level: 'success'});
            modifyWithoutSave = false;
            loadConfiguration(_el);
        }
    });
}

function loadConfiguration(_el) {
    var configuration = _el.getValues('.configKey');
    configuration = configuration[0];
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "getKey",
            plugin: 'core',
            key: json_encode(configuration)
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            _el.setValues(data.result, '.configKey');
            modifyWithoutSave = false;
        }
    });
}