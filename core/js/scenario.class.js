
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


jeedom.scenario = function() {
};

jeedom.scenario.cache = Array();

if (!isset(jeedom.scenario.cache.html)) {
    jeedom.scenario.cache.html = Array();
}

jeedom.scenario.all = function(_callback) {
    if (isset(jeedom.scenario.cache.all) && 'function' == typeof (_callback)) {
        _callback(jeedom.scenario.cache.all);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "all",
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
            jeedom.scenario.cache.all = data.result;
            if ('function' == typeof (_callback)) {
                _callback(jeedom.scenario.cache.all);
            }
        }
    });
}

jeedom.scenario.toHtml = function(_id, _version, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: ($.isArray(_id)) ? json_encode(_id) : _id,
            version: _version
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (_id == 'all' || $.isArray(_id)) {
                for (var i in data.result) {
                    jeedom.scenario.cache.html[i] = data.result[i];
                }
            } else {
                if (isset(jeedom) && isset(jeedom.workflow) && isset(jeedom.workflow.scenario) && jeedom.workflow.scenario[_id]) {
                    jeedom.workflow.object[_id] = false;
                }
                jeedom.scenario.cache.html[_id] = data.result;
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}


jeedom.scenario.changeState = function(_id, _state, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "changeState",
            id: _id,
            state: _state
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}


jeedom.scenario.refreshValue = function(_scenario_id) {
    if ($('.scenario[data-scenario_id=' + _scenario_id + ']').html() != undefined) {
        var version = $('.scenario[data-scenario_id=' + _scenario_id + ']').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/scenario.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _scenario_id,
                version: version
            },
            dataType: 'json',
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('.scenario[data-scenario_id=' + _scenario_id + ']').replaceWith(data.result);
                if ($.mobile) {
                    $('.scenario[data-scenario_id=' + _scenario_id + ']').trigger("create");
                    setTileSize('.scenario');
                }
            }
        });
    }
};


jeedom.scenario.copy = function(_id, _name, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "copy",
            id: _id,
            name: _name
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_copyScenarioAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_copyScenarioAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
};


jeedom.scenario.get = function(_id, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "get",
            id: _id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
};

jeedom.scenario.save = function(_scenario, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "save",
            scenario: json_encode(_scenario),
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
};

jeedom.scenario.remove = function(_id, _callback) {
   $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "remove",
            id: _id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback();
            }
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=scenario');
        }
    });
};