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
jeedom.nodeJs  = {state : -1};
jeedom.chat  = {state : false};

jeedom.init = function() {
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
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "/nodeJS/socket.io/socket.io.js", // url du fichier php
            data: {},
            dataType: 'script',
            statusCode: {
                200: function() {
                    $("head").append("<script type='text/javascript' src='/nodeJS/socket.io/socket.io.js'></script>");
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
                        notify('Node JS erreur', 'Erreur d\'authentification sur node JS, clef invalide', 'gritter-red');
                        $('.span_nodeJsState').removeClass('green').addClass('red');
                        jeedom.nodeJs.state = false;
                    });
                    socket.on('eventCmd', function(cmd_id) {
                        refreshCmdValue(cmd_id);
                    });
                    socket.on('eventScenario', function(scenario_id) {
                        refreshScenarioValue(scenario_id);
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
                }
            }
        });
    } else {
        $('.span_nodeJsState').removeClass('red').addClass('grey');
        jeedom.nodeJs.state = null;
    }
}

jeedom.getConfiguration = function(_key) {
    if (!isset(jeedom.cache.getConfiguration)) {
        jeedom.cache.getConfiguration = Array();
    }
    if (isset(jeedom.cache.getConfiguration[_key])) {
        return jeedom.cache.getConfiguration[_key];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/jeedom.ajax.php", // url du fichier php
        data: {
            action: "getConfiguration",
            key: _key
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
    jeedom.cache.getConfiguration[_key] = result;
    return result;
}