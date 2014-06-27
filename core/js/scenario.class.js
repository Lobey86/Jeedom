
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

jeedom.scenario.all = function(_params) {
    var paramsRequired = [];
    var paramsSpecifics = {
        pre_success: function(data) {
            jeedom.scenario.cache.all = data.result;
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
    if (isset(jeedom.scenario.cache.all)) {
        params.success(jeedom.scenario.cache.all);
        return;
    }
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'all',
    };
    $.ajax(paramsAJAX);
}

jeedom.scenario.toHtml = function(_params) {
    var paramsRequired = ['id', 'version'];
    var paramsSpecifics = {
        pre_success: function(data) {
            if (_params.id == 'all' || $.isArray(_params.id)) {
                for (var i in data.result) {
                    jeedom.scenario.cache.html[i] = data.result[i];
                }
            } else {
                if (isset(jeedom) && isset(jeedom.workflow) && isset(jeedom.workflow.scenario) && jeedom.workflow.scenario[_params.id]) {
                    jeedom.workflow.object[_params.id] = false;
                }
                jeedom.scenario.cache.html[_params.id] = data.result;
            }
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
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'toHtml',
        id: ($.isArray(_params.id)) ? json_encode(_params.id) : _params.id,
        version: _params.version
    };
    $.ajax(paramsAJAX);
}


jeedom.scenario.changeState = function(_params) {
    var paramsRequired = ['id', 'state'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'changeState',
        id: _params.id,
        state: _params.state
    };
    $.ajax(paramsAJAX);
}


jeedom.scenario.refreshValue = function(_params) {
    if ($('.scenario[data-scenario_id=' + _params.id + ']').html() != undefined) {
        var version = $('.scenario[data-scenario_id=' + _params.id + ']').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/scenario.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _params.id,
                version: _params.version || version
            },
            dataType: 'json',
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    _params.error({message: data.result, code: 0});
                    return;
                }
                $('.scenario[data-scenario_id=' + _params.id + ']').replaceWith(data.result);
                if ($.mobile) {
                    $('.scenario[data-scenario_id=' + _params.id + ']').trigger("create");
                    setTileSize('.scenario');
                }
            }
        });
    }
};


jeedom.scenario.copy = function(_params) {
    var paramsRequired = ['id', 'name'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'copy',
        id: _params.id,
        name: _params.name
    };
    $.ajax(paramsAJAX);
};


jeedom.scenario.get = function(_params) {
    var paramsRequired = ['id'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'get',
        id: _params.id
    };
    $.ajax(paramsAJAX);
};

jeedom.scenario.save = function(_params) {
    var paramsRequired = ['scenario'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'save',
        scenario: json_encode(_params.scenario)
    };
    $.ajax(paramsAJAX);
};

jeedom.scenario.remove = function(_params) {
    var paramsRequired = ['id'];
    var paramsSpecifics = {};
    try {
        jeedom.private.checkParamsRequired(_params || {}, paramsRequired);
    } catch (e) {
        (_params.error || paramsSpecifics.error || jeedom.private.default_params.error)(e);
        return;
    }
    var params = $.extend({}, jeedom.private.default_params, paramsSpecifics, _params || {});
    var paramsAJAX = jeedom.private.getParamsAJAX(params);
    paramsAJAX.url = 'core/ajax/scenario.ajax.php';
    paramsAJAX.data = {
        action: 'remove',
        id: _params.id
    };
    $.ajax(paramsAJAX);
};