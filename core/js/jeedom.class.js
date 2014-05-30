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


function jeedom() {
}

jeedom.cache = Array();
jeedom.nodeJs = {state: -1};
jeedom.display = {};

jeedom.init = function() {
    jeedom.display.version = 'desktop';
    if ($.mobile) {
        jeedom.display.version = 'mobile';
    }

    socket = null;
    Highcharts.setOptions({
        lang: {
            months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            shortMonths: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
        }
    });
    if (nodeJsKey != '') {
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            if (options.dataType == 'script' || originalOptions.dataType == 'script') {
                options.cache = true;
            }
        });
        $.getScript("/nodeJS/socket.io/socket.io.js")
                .done(function(script, textStatus) {
                    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                        if (options.dataType == 'script' || originalOptions.dataType == 'script') {
                            options.cache = false;
                        }
                    });
                    socket = io.connect();

                    socket.on('error', function(reason) {
                        console.log('Unable to connect Socket.IO', reason);
                    });
                    socket.on('connect', function() {
                        socket.emit('authentification', nodeJsKey, user_id);
                        $('.span_nodeJsState').removeClass('red').addClass('green');
                        jeedom.nodeJs.state = true;
                        $('body').trigger('nodeJsConnect');
                    });
                    socket.on('authentification_failed', function() {
                        notify('Node JS erreur', '{{Erreur d\'authentification sur node JS, clef invalide}}', 'gritter-red');
                        $('.span_nodeJsState').removeClass('green').addClass('red');
                        jeedom.nodeJs.state = false;
                    });
                    socket.on('eventCmd', function(_options) {
                        _options = json_decode(_options);
                        refreshCmdValue(_options.cmd_id);
                        if (isset(object) && isset(object.cache) && isset(object.cache.html) && isset(object.cache.html[_options.object_id])) {
                            object.prefetch(_options.object_id, jeedom.display.version, true);
                        }
                        if (isset(view) && isset(view.cache) && isset(view.cache.html)) {
                            for (var i in view.cache.html) {
                                if ($.inArray(_options.eqLogic_id, view.cache.html[i].eqLogic) >= 0) {
                                    view.prefetch(i, jeedom.display.version, true);
                                }
                            }
                        }
                    });
                    socket.on('eventScenario', function(scenario_id) {
                        refreshScenarioValue(scenario_id);
                        if (isset(view) && isset(view.cache) && isset(view.cache.html)) {
                            for (var i in view.cache.html) {
                                if ($.inArray(scenario_id, view.cache.html[i].scenario) >= 0) {
                                    view.prefetch(i, jeedom.display.version, true);
                                }
                            }
                        }
                    });
                    socket.on('eventHistory', function(cmd_id) {
                        refreshGraph(cmd_id);
                    });
                    socket.on('notify', function(title, text, category) {
                        var theme = '';
                        switch (init(category)) {
                            case 'event' :
                                if (init(userProfils.notifyEvent) == 'none') {
                                    return;
                                } else {
                                    theme = userProfils.notifyEvent;
                                }
                                break;
                            case 'scenario' :
                                if (init(userProfils.notifyLaunchScenario) == 'none') {
                                    return;
                                } else {
                                    theme = userProfils.notifyLaunchScenario;
                                }
                                break;
                            case 'message' :
                                if (init(userProfils.notifyNewMessage) == 'none') {
                                    return;
                                } else {
                                    theme = userProfils.notifyNewMessage;
                                }
                                refreshMessageNumber();
                                break;
                        }
                        notify(title, text, theme);
                    });
                })
                .fail(function(jqxhr, settings, exception) {
                    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                        if (options.dataType == 'script' || originalOptions.dataType == 'script') {
                            options.cache = false;
                        }
                    });
                });
    } else {
        $('.span_nodeJsState').removeClass('red').addClass('grey');
        jeedom.nodeJs.state = null;
    }
}

jeedom.getConfiguration = function(_key, _default) {
    if (!isset(jeedom.cache.getConfiguration)) {
        jeedom.cache.getConfiguration = Array();
    }
    if (init(_default, 0) == 0 && isset(jeedom.cache.getConfiguration[_key])) {
        return jeedom.cache.getConfiguration[_key];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/jeedom.ajax.php", // url du fichier php
        data: {
            action: "getConfiguration",
            key: _key,
            default: init(_default, 0)
        },
        dataType: 'json',
        async: false,
        cache: true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            result = data.result;
        }
    });
    if (init(_default, 0) == 0) {
        jeedom.cache.getConfiguration[_key] = result;
    }
    return result;
}