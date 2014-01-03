
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
    $(".li_module").on('click', function(event) {
        $.hideAlert();
        $('.li_module').removeClass('active');
        $(this).addClass('active');
        printModule($(this).attr('id'), $(this).attr('modulePath'));
        return false;
    });

    $("#span_module_toggleState").delegate(".toggleModule", 'click', function(event) {
        toggleModule($(this).attr('module_id'), $(this).attr('state'));
    });

    if (select_id != -1) {
        $('#ul_module .li_module[module_id=' + select_id + ']').click();
    } else {
        $('#ul_module .li_module:first').click();
    }

    $("#bt_saveModuleConfig").on('click', function(event) {
        saveModuleConfig();
        return false;
    });
});

function toggleModule(_id, _state) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/module.ajax.php", // url du fichier php
        data: {
            action: "toggleModule",
            id: _id,
            state: _state
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
            window.location.replace('index.php?v=d&p=module&id=' + _id);
        }
    });
}

function saveModuleConfig() {
    try {
        var configuration = $('#div_module_configuration').getValues('.configKey');
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
            value: json_encode(configuration),
            module: $('.li_module.active').attr('module_id')
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
        }
    });
}

function loadModuleConfig() {
    try {
        var configuration = $('#div_module_configuration').getValues('.configKey');
        configuration = configuration[0];
    } catch (e) {
        $('#div_alert').showAlert({message: e, level: 'danger'});
        return false;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/config.ajax.php", // url du fichier php
        data: {
            action: "getKey",
            key: json_encode(configuration),
            module: $('.li_module.active').attr('module_id')
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
            $('#div_module_configuration').setValues(data.result, '.configKey');
        }
    });
}

function printModule(_id, _modulePath) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/module.ajax.php", // url du fichier php
        data: {
            action: "getModuleConf",
            id: _id,
            modulePath: _modulePath
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

            $('#span_module_id').html(data.result.id);
            $('#span_module_name').html(data.result.name);
            $('#span_module_author').html(data.result.author);
            $('#span_module_description').html(data.result.description);
            $('#span_module_licence').html(data.result.licence);
            $('#span_module_installation').html(data.result.installation);
            if (data.result.checkVersion != -1) {
                $('#span_module_require').html('<span>' + data.result.require + '</span>');
            } else {
                $('#span_module_require').html('<span class="label label-danger">' + data.result.require + '</span>');
            }
            $('#span_module_version').html(data.result.version);

            $('#span_module_toggleState').empty();
            if (data.result.checkVersion != -1) {
                if (data.result.activate == 1) {
                    var btn = '<a class="btn btn-danger btn-xs toggleModule" state="0" module_id="' + data.result.id + '" style="margin : 5px;"><i class="fa fa-times"></i> Désactiver</a>';
                } else {
                    var btn = '<a class="btn btn-success btn-xs toggleModule" state="1" module_id="' + data.result.id + '" style="margin : 5px;"><i class="fa fa-check"></i> Activer</a>';
                }
                $('#span_module_toggleState').html(btn);
            }

            $('#div_module_configuration').empty();
            if (data.result.checkVersion != -1) {
                if (data.result.configurationPath != '' && data.result.activate == 1) {
                    $('#div_module_configuration').load(data.result.configurationPath, function() {
                        loadModuleConfig();
                        $('#div_module_configuration').parent().show();
                    });
                } else {
                    $('#div_module_configuration').parent().hide();
                }
            } else {
                $('#div_module_configuration').parent().hide();
            }
            $('#div_confModule').show();
        }
    });
}

