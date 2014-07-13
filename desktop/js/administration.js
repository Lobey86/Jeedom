
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

$("#bt_clearJeedomLastDate").on('click', function(event) {
    $.hideAlert();
    clearJeedomDate();
});


$("#bt_saveGeneraleConfig").on('click', function(event) {
    $.hideAlert();
    saveConvertColor();
    jeedom.config.save({
        configuration: $('#config').getValues('.configKey')[0],
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function() {
            jeedom.config.load({
                configuration: $('#config').getValues('.configKey')[0],
                error: function(error) {
                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                },
                success: function(data) {
                    $('#config').setValues(data, '.configKey');
                    modifyWithoutSave = false;
                    $('#div_alert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
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
                $('#div_alert').showAlert({message: '{{Connexion échoué :}} ' + data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Connexion réussie}}', level: 'success'});
        }
    });
    return false;
});

$('#bt_addColorConvert').on('click', function() {
    addConvertColor();
});

printConvertColor();

$.showLoading();
jeedom.config.load({
    configuration: $('#config').getValues('.configKey')[0],
    error: function(error) {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function(data) {
        $('#config').setValues(data, '.configKey');
        modifyWithoutSave = false;
    }
});
$('body').delegate('.configKey', 'change', function() {
    modifyWithoutSave = true;
});

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

function clearJeedomDate() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/jeedom.ajax.php", // url du fichier php
        data: {
            action: "clearDate"
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
            $('#in_jeedomLastDate').value('');
        }
    });
}


function flushMemcache() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/jeedom.ajax.php", // url du fichier php
        data: {
            action: "flushcache"
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
            $('#div_alert').showAlert({message: '{{Cache vidé}}', level: 'success'});
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
            modifyWithoutSave = false;
        }
    });
}

function addConvertColor(_color, _html) {
    var tr = '<tr>';
    tr += '<td>';
    tr += '<input class="color form-control input-sm" value="' + init(_color) + '"/>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input type="color" class="html form-control input-sm" value="' + init(_html) + '" />';
    tr += '</td>';
    tr += '</tr>';
    $('#table_convertColor tbody').append(tr);
    modifyWithoutSave = true;
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
            modifyWithoutSave = false;
        }
    });
}