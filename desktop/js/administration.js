
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
    $("#admin_tab").delegate("a", 'click', function(event) {
        $(this).tab('show');
        $.hideAlert();
    })

    if (tab != '') {
        $('#admin_tab a[href=#' + tab + ']').click();
    }

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

    $("#bt_genKeyAPI").on('click', function(event) {
        $.hideAlert();
        genKeyAPI();
    });

    $("#bt_nodeJsKey").on('click', function(event) {
        $.hideAlert();
        genNodeJsKey();
    });

    $("#bt_flushMemcache").on('click', function(event) {
        $.hideAlert();
        flushMemcache();
    });

    $("#bt_saveGeneraleConfig").on('click', function(event) {
        $.hideAlert();
        saveConvertColor();
        saveConfiguration($('#config'));
    });

    $("#bt_saveUpdate").on('click', function(event) {
        $.hideAlert();
        saveConfiguration($('#update'));
    });

    $("#bt_saveBackup").on('click', function(event) {
        $.hideAlert();
        saveConfiguration($('#backup'));
    });

    $("#bt_saveUser").on('click', function(event) {
        var users = $('#table_user tbody tr').getValues('.userAttr');
        saveUser(users);
    });

    $(".bt_updateJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('Etez-vous sûr de vouloir mettre à jour Jeedom ? Une fois lancée cette opération ne peut etre annulée', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                $.ajax({
                    type: 'POST',
                    url: 'core/ajax/jeedom.ajax.php',
                    data: {
                        action: 'update',
                        mode: el.attr('data-mode')
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) {
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        getJeedomLog(1, 'update');
                    }
                });
            }
        });
    });

    $("#bt_backupJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('Etez-vous sûr de vouloir faire une sauvegarde de Jeedom ? Une fois lancée cette opération ne peut etre annulée', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                $.ajax({
                    type: 'POST',
                    url: 'core/ajax/jeedom.ajax.php',
                    data: {
                        action: 'backup',
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) {
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        getJeedomLog(1, 'backup');
                    }
                });
            }
        });
    });

    $("#bt_restoreJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('Etez-vous sûr de vouloir restaurer Jeedom avec <b>' + $('#sel_restoreBackup option:selected').text() + '</b> ? Une fois lancée cette opération ne peut etre annulée', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                $.ajax({
                    type: 'POST',
                    url: 'core/ajax/jeedom.ajax.php',
                    data: {
                        action: 'restore',
                        backup: $('#sel_restoreBackup').value(),
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) {
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        getJeedomLog(1, 'restore');
                    }
                });
            }
        });
    });

    $("#bt_removeBackup").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('Etez-vous sûr de vouloir supprimer la sauvegarde <b>' + $('#sel_restoreBackup option:selected').text() + '</b> ?', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                $.ajax({
                    type: 'POST',
                    url: 'core/ajax/jeedom.ajax.php',
                    data: {
                        action: 'removeBackup',
                        backup: $('#sel_restoreBackup').value(),
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) {
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        updateListBackup();
                        $('#div_alert').showAlert({message: 'Sauvegarde supprimé avec succès', level: 'success'});
                    }
                });
            }
        });
    });

    $('#bt_downloadBackup').on('click', function() {
        window.open('backup/' + $('#sel_restoreBackup option:selected').text(), "_blank", null);
    });

    $("#bt_restoreCloudJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('Etez-vous sûr de vouloir restaurer Jeedom avec la sauvergarde Cloud <b>' + $('#sel_restoreCloudBackup option:selected').text() + '</b> ? Une fois lancée cette opération ne peut etre annulée', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                $.ajax({
                    type: 'POST',
                    url: 'core/ajax/jeedom.ajax.php',
                    data: {
                        action: 'restoreCloud',
                        backup: $('#sel_restoreCloudBackup').value(),
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) {
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        getJeedomLog(1, 'restore');
                    }
                });
            }
        });
    });

    $("#bt_testLdapConnection").on('click', function(event) {
        $.hideAlert();
        $.ajax({
            type: 'POST',
            url: 'core/ajax/user.ajax.php',
            data: {
                action: 'testLdapConneciton',
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) {
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: 'Connection échoué : ' + data.result, level: 'danger'});
                    return;
                }
                $('#div_alert').showAlert({message: 'Connection réussie', level: 'success'});
            }
        });
        return false;
    });

    $("#table_user").delegate(".del_user", 'click', function(event) {
        $.hideAlert();
        var user = {id: $(this).closest('tr').find('.userAttr[data-l1key=id]').value()};
        bootbox.confirm('Etez-vous sûr de vouloir supprimer cet utilisateur ?', function(result) {
            if (result) {
                delUser(user);
            }
        });
    });

    $("#table_user").delegate(".change_mdp_user", 'click', function(event) {
        $.hideAlert();
        var user = {id: $(this).closest('tr').find('.userAttr[data-l1key=id]').value(), login: $(this).closest('tr').find('.userAttr[data-l1key=login]').value()};
        bootbox.prompt("Quel est le nouveau mot de passe", function(result) {
            if (result !== null) {
                user.password = result;
                addEditUser(user);
            }
        });
    });


    $('#bt_addColorConvert').on('click', function() {
        addConvertColor();
    });

    printConvertColor();

    loadConfiguration($('body'));
    updateListBackup();
});
/********************Log************************/

function getJeedomLog(_autoUpdate, _log) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/log.ajax.php',
        data: {
            action: 'get',
            logfile: _log,
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var log = '';
            for (var i in data.result.reverse()) {
                log += data.result[i][2];
                if ($.trim(data.result[i][2]) == '[END ' + _log.toUpperCase() + ' SUCCESS]') {
                    $('#div_alert').showAlert({message: 'L\'opération est réussie', level: 'success'});
                    _autoUpdate = 0;
                }
                if ($.trim(data.result[i][2]) == '[END ' + _log.toUpperCase() + ' ERROR]') {
                    $('#div_alert').showAlert({message: 'L\'opération a échoué', level: 'danger'});
                    _autoUpdate = 0;
                }
            }
            $('#pre_' + _log + 'Info').text(log);
            if (init(_autoUpdate, 0) == 1) {
                setTimeout(function() {
                    getJeedomLog(_autoUpdate, _log)
                }, 1000);
            } else {
                $('#bt_' + _log + 'Jeedom .fa-refresh').hide();
                $('.bt_' + _log + 'Jeedom .fa-refresh').hide();
                updateListBackup();
            }
        }
    });
}

function updateListBackup() {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'listBackup',
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var options = '';
            for (var i in data.result) {
                options += '<option value="' + i + '">' + data.result[i] + '</option>';
            }
            $('#sel_restoreBackup').html(options);
        }
    });
}


/********************Utilisateurs************************/

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
                    ligne += '<a class="btn btn-xs btn-danger pull-right del_user"><i class="fa fa-trash-o"></i> Supprimer</a>';
                    ligne += '<a class="btn btn-xs btn-warning pull-right change_mdp_user"><i class="fa fa-pencil"></i> Changer le mot de passe</a>';
                }
                ligne += '</td>';
                ligne += '<td>';
                ligne += '<input type="checkbox" class="userAttr" data-l1key="rights" data-l2key="admin"/> Admin';
                ligne += '</td>';
                ligne += '</tr>';
                $('#table_user tbody').append(ligne);
                $('#table_user tbody tr:last').setValues(data.result[i], '.userAttr');
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
            $('#div_alert').showAlert({message: 'L\'utilisateur a bien été supprimé', level: 'success'});
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
            $('#div_alert').showAlert({message: 'Sauvegarde effetuée', level: 'success'});
        }
    });
}

/********************Administation************************/

function genKeyAPI() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "genKeyAPI"
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
            $('#in_keyAPI').value(data.result);
        }
    });
}

function genNodeJsKey() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "genNodeJsKey"
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
            $('#in_nodeJsKey').value(data.result);
        }
    });
}

function flushMemcache() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/mc.ajax.php", // url du fichier php
        data: {
            action: "flush"
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
            $('#div_alert').showAlert({message: 'Cache vidé', level: 'success'});
        }
    });
}

function saveConfiguration(_el) {
    try {
        var configuration = _el.getValues('.configKey');
        configuration = configuration[0];
    } catch (e) {
        $('#div_alert').showAlert({message: e, level: 'danger'});
        return false;
    }
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
            $('#div_alert').showAlert({message: 'Sauvegarde effetuée', level: 'success'});
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
        }
    });
}


/********************Convertion************************/
function printConvertColor() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "getKey",
            key: 'convertColor'
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

            $('#table_convertColor tbody').empty();
            for (var color in data.result) {
                addConvertColor(color, data.result[color]);
            }
        }
    });
}

function addConvertColor(_color, _html) {
    var tr = '<tr>';
    tr += '<td>';
    tr += '<input class="color form-control input-sm" value="' + init(_color) + '"/>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="html form-control input-sm" value="' + init(_html) + '" />';
    tr += '<div class="colorpicker" style="display : none"></div>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_convertColor tbody').append(tr);
    var input = $('#table_convertColor tbody tr:last input.html');
    var div = input.closest('td').find('.colorpicker');
    div.farbtastic(input);

    input.focus(function() {
        if (!$(this).parent().find('.colorpicker').is(':visible')) {
            $(this).parent().find('.colorpicker').show();
        }
    });

    input.blur(function() {
        if ($(this).parent().find('.colorpicker').is(':visible')) {
            $(this).parent().find('.colorpicker').hide();
        }
    });

    div.on('mouseup', function() {
        div.closest('td').find('input.html').value($.farbtastic($(this)).color);
    });
}

function saveConvertColor() {
    var value = {};
    var colors = {};
    $('#table_convertColor tbody tr').each(function() {
        colors[$(this).find('.color').value()] = $(this).find('.html').value();
    });
    value.convertColor = colors;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: 'addKey',
            value: json_encode(value)
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
        }
    });
}