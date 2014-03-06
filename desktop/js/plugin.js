
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
    $(".li_plugin").on('click', function(event) {
        $.hideAlert();
        $('.li_plugin').removeClass('active');
        $(this).addClass('active');
        printPlugin($(this).attr('data-plugin_id'), $(this).attr('data-pluginPath'));
        return false;
    });

    $("#span_plugin_toggleState").delegate(".togglePlugin", 'click', function(event) {
        togglePlugin($(this).attr('data-plugin_id'), $(this).attr('data-state'));
    });

    if (is_numeric(getUrlVars('id'))) {
        if ($('#ul_plugin .li_plugin[data-plugin_id=' + getUrlVars('id') + ']').length != 0) {
            $('#ul_plugin .li_plugin[data-plugin_id=' + getUrlVars('id') + ']').click();
        } else {
            $('#ul_plugin .li_plugin:first').click();
        }
    } else {
        $('#ul_plugin .li_plugin:first').click();
    }

    $("#bt_savePluginConfig").on('click', function(event) {
        savePluginConfig();
        return false;
    });

    $('#bt_displayMarket').on('click', function() {
        $('#md_modal').dialog({title: "Market Jeedom"});
        $('#md_modal').load('index.php?v=d&modal=market.list&type=plugin').dialog('open');
    });

    $('body').delegate('.viewOnMarket', 'click', function() {
        $('#md_modal2').dialog({title: "Market Jeedom Display"});
        $('#md_modal2').load('index.php?v=d&modal=market.display&type=plugin&logicalId=' + $(this).attr('data-market_logicalId')).dialog('open');
    });
    
    $('body').delegate('.sendOnMarket', 'click', function() {
        $('#md_modal2').dialog({title: "Envoyer sur le market"});
        $('#md_modal2').load('index.php?v=d&modal=market.send&type=plugin&logicalId=' + $(this).attr('data-market_logicalId')).dialog('open');
    });
    
    $('body').delegate('.configKey', 'change', function() {
        modifyWithoutSave = true;
    });
});

function togglePlugin(_id, _state) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/plugin.ajax.php", // url du fichier php
        data: {
            action: "togglePlugin",
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
            window.location.replace('index.php?v=d&p=plugin&id=' + _id);
        }
    });
}

function savePluginConfig() {
    try {
        var configuration = $('#div_plugin_configuration').getValues('.configKey');
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
            plugin: $('.li_plugin.active').attr('data-plugin_id')
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
            modifyWithoutSave = false;
        }
    });
}

function loadPluginConfig() {
    try {
        var configuration = $('#div_plugin_configuration').getValues('.configKey');
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
            plugin: $('.li_plugin.active').attr('data-plugin_id')
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
            $('#div_plugin_configuration').setValues(data.result, '.configKey');
            modifyWithoutSave = false;
        }
    });
}

function printPlugin(_id, _pluginPath) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/plugin.ajax.php", // url du fichier php
        data: {
            action: "getPluginConf",
            id: _id,
            pluginPath: _pluginPath
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

            $('#span_plugin_id').html(data.result.id);
            $('#span_plugin_name').html(data.result.name);
            $('#span_plugin_author').html(data.result.author);
            $('#span_plugin_description').html(data.result.description);
            $('#span_plugin_licence').html(data.result.licence);
            $('#span_plugin_installation').html(data.result.installation);


            $('#span_plugin_market').empty();
            if (data.result.status.market == 1) {
                $('#span_plugin_market').append('<a class="btn btn-default btn-xs viewOnMarket" data-market_logicalId="' + data.result.id + '" style="margin-right : 5px;"><i class="fa fa-cloud-download"></i> Voir sur le market</a>')
            }
            
            if (data.result.status.market_owner == 1) {
                $('#span_plugin_market').append('<a class="btn btn-warning btn-xs sendOnMarket" data-market_logicalId="' + data.result.id + '"><i class="fa fa-cloud-upload"></i> Envoyer sur le market</a>')
            }

            if (data.result.checkVersion != -1) {
                $('#span_plugin_require').html('<span>' + data.result.require + '</span>');
            } else {
                $('#span_plugin_require').html('<span class="label label-danger">' + data.result.require + '</span>');
            }
            $('#span_plugin_version').html(data.result.version);

            $('#span_plugin_toggleState').empty();
            if (data.result.checkVersion != -1) {
                if (data.result.activate == 1) {
                    var btn = '<a class="btn btn-danger btn-xs togglePlugin" data-state="0" data-plugin_id="' + data.result.id + '" style="margin : 5px;"><i class="fa fa-times"></i> Désactiver</a>';
                } else {
                    var btn = '<a class="btn btn-success btn-xs togglePlugin" data-state="1" data-plugin_id="' + data.result.id + '" style="margin : 5px;"><i class="fa fa-check"></i> Activer</a>';
                }
                $('#span_plugin_toggleState').html(btn);
            }

            $('#div_plugin_configuration').empty();
            if (data.result.checkVersion != -1) {
                if (data.result.configurationPath != '' && data.result.activate == 1) {
                    $('#div_plugin_configuration').load(data.result.configurationPath, function() {
                        loadPluginConfig();
                        $('#div_plugin_configuration').parent().show();
                    });
                } else {
                    $('#div_plugin_configuration').parent().hide();
                }
            } else {
                $('#div_plugin_configuration').parent().hide();
            }
            $('#div_confPlugin').show();
            modifyWithoutSave = false;
        }
    });
}

