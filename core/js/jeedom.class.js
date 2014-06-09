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

jeedom.cache = [];
jeedom.nodeJs = {state: -1};
jeedom.display = {};
jeedom.workflow = {object: [], eqLogic: [], cmd: [], scenario: [], nextrun: 0, delay: 1500};


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
                        jeedom.cmd.refreshValue(_options.cmd_id);
                        jeedom.workflow.cmd[_options.cmd_id] = true;
                        jeedom.workflow.eqLogic[_options.eqLogic_id] = true;
                        jeedom.workflow.object[_options.object_id] = true;
                        jeedom.scheduleWorkflow();
                    });
                    socket.on('eventScenario', function(scenario_id) {
                        jeedom.scenario.refreshValue(scenario_id);
                        jeedom.workflow.scenario[scenario_id] = true;
                        jeedom.scheduleWorkflow();
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

jeedom.scheduleWorkflow = function() {
    var nextrun = ((new Date()).getTime()) + jeedom.workflow.delay;
    if (nextrun > jeedom.workflow.nextrun) {
        if (nextrun < (jeedom.workflow.nextrun + jeedom.workflow.delay)) {
            jeedom.workflow.nextrun += jeedom.workflow.delay;
            var timeout = (new Date()).getTime() - jeedom.workflow.nextrun;
            if (timeout < 1) {
                timeout = jeedom.workflow.delay;
            }
            setTimeout(function() {
                jeedom.processWorkflow();
            }, timeout);
        } else {
            jeedom.workflow.nextrun = nextrun + jeedom.workflow.delay;
            setTimeout(function() {
                jeedom.processWorkflow();
            }, jeedom.workflow.delay);
        }
    }
}

jeedom.processWorkflow = function() {
    var list_object = [];
    for (var i in jeedom.workflow.object) {
        if (jeedom.workflow.object[i]) {
            list_object.push(i);
            jeedom.workflow.object[i] = false;
        }
    }

    var list_view = [];
    for (var i in jeedom.workflow.eqLogic) {
        if (jeedom.workflow.eqLogic[i]) {
            if (isset(jeedom.view) && isset(jeedom.view.cache) && isset(jeedom.view.cache.html)) {
                for (var j in jeedom.view.cache.html) {
                    if ($.inArray(j, list_view) < 0 && $.inArray(i, jeedom.view.cache.html[j].eqLogic) >= 0) {
                        list_view.push(j);
                    }
                }
            }
            jeedom.workflow.eqLogic[i] = false;
        }
    }

    for (var i in jeedom.workflow.scenario) {
        if (jeedom.workflow.scenario[i]) {
            if (isset(jeedom.view) && isset(jeedom.view.cache) && isset(jeedom.view.cache.html)) {
                for (var j in jeedom.view.cache.html) {
                    if ($.inArray(j, list_view) < 0 && $.inArray(i, jeedom.view.cache.html[j].scenario) >= 0) {
                        list_view.push(j);
                    }
                }
            }
            jeedom.workflow.scenario[i] = false;
        }
    }
    for (var i in jeedom.workflow.cmd) {
        if (jeedom.workflow.cmd[i]) {
            jeedom.workflow.cmd[i] = false;
        }
    }

    if (list_object.length > 0 && isset(jeedom.object) && isset(jeedom.object.cache) && isset(jeedom.object.cache.html)) {
        jeedom.object.prefetch(list_object, jeedom.display.version);
    }
    if (list_view.length > 0 && isset(jeedom.view) && isset(jeedom.view.cache) && isset(jeedom.view.cache.html)) {
        jeedom.view.prefetch(list_view, jeedom.display.version);
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