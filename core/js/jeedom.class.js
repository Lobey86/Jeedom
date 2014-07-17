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

if (!isset(jeedom.cache.getConfiguration)) {
    jeedom.cache.getConfiguration = Array();
}


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
    if (nodeJsKey != '' && io != null) {
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
            notify('Node JS erreur', '{{Erreur d\'authentification sur node JS, clef invalide}}', 'error');
            $('.span_nodeJsState').removeClass('green').addClass('red');
            jeedom.nodeJs.state = false;
        });
        socket.on('eventCmd', function(_options) {
            _options = json_decode(_options);
            jeedom.cmd.refreshValue({id: _options.cmd_id});
            if ($.mobile) {
                jeedom.workflow.cmd[_options.cmd_id] = true;
                jeedom.workflow.eqLogic[_options.eqLogic_id] = true;
                jeedom.workflow.object[_options.object_id] = true;
                jeedom.scheduleWorkflow();
            }
        });
        socket.on('eventScenario', function(scenario_id) {
            jeedom.scenario.refreshValue({id: scenario_id});
            if ($.mobile) {
                jeedom.workflow.scenario[scenario_id] = true;
                jeedom.scheduleWorkflow();
            }
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
        jeedom.object.prefetch({id: list_object, version: jeedom.display.version});
    }
    if (list_view.length > 0 && isset(jeedom.view) && isset(jeedom.view.cache) && isset(jeedom.view.cache.html)) {
        jeedom.view.prefetch({id: list_view, version: jeedom.display.version});
    }
}

jeedom.getConfiguration = function(_params) {
    var paramsRequired = ['key'];
    var paramsSpecifics = {
        pre_success: function(data) {
            jeedom.cache.getConfiguration[_params.key] = data.result;
            return data;
        }
    };
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    if (isset(jeedom.cache.getConfiguration[params.key])) {
        _params.success(jeedom.cache.getConfiguration[params.key]);
        return;
    }
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/jeedom.ajax.php';
    paramsAJAX.data = {
        action: 'getConfiguration',
        key: _params.key,
        default: init(_params.default, 0)
    };
    $.ajax(paramsAJAX);
};

