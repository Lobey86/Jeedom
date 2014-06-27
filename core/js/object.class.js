
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


jeedom.object = function() {
};

jeedom.object.cache = Array();

if (!isset(jeedom.object.cache.html)) {
    jeedom.object.cache.html = Array();
}

if (!isset(jeedom.object.cache.getEqLogic)) {
    jeedom.object.cache.getEqLogic = Array();
}

if (!isset(jeedom.object.cache.html)) {
    jeedom.object.cache.html = Array();
}

if (!isset(jeedom.object.cache.byId)) {
    jeedom.object.cache.byId = Array();
}

jeedom.object.getEqLogic = function(_params) {
    if (isset(jeedom.object.cache.getEqLogic[_params.id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.object.cache.getEqLogic[_params.id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "listByObject",
            object_id: _params.id,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            jeedom.object.cache.getEqLogic[_params.id] = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.object.cache.getEqLogic[_params.id]);
            }
        }
    });
};

jeedom.object.all = function(_params) {
    if (isset(jeedom.object.cache.all) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.object.cache.all);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "all",
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            jeedom.object.cache.all = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.object.cache.all);
            }
        }
    });
};

jeedom.object.prefetch = function(_params) {
    if (!isset(jeedom.object.cache.html[_params.id])) {
        jeedom.object.toHtml({id: _params.id, version: _params.version, useCache: false, globalAjax: false});
    }
};

jeedom.object.toHtml = function(_params) {
    if (init(_params.useCache, false) == true && isset(jeedom.object.cache.html[_params.id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.object.cache.html[_params.id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: ($.isArray(_params.id)) ? json_encode(_params.id) : _params.id,
            version: _params.version || 'dashboard',
        },
        dataType: 'json',
        global: _params.globalAjax || true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if (_params.id == 'all' || $.isArray(_params.id)) {
                for (var i in data.result) {
                    jeedom.object.cache.html[i] = data.result[i];
                }
            } else {
                if (isset(jeedom) && isset(jeedom.workflow) && isset(jeedom.workflow.object) && jeedom.workflow.object[_params.id]) {
                    jeedom.workflow.object[_params.id] = false;
                }
                jeedom.object.cache.html[_params.id] = data.result;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
};

jeedom.object.remove = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "remove",
            id: _params.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if (isset(jeedom.object.cache.all)) {
                delete jeedom.object.cache.all;
            }
            if (isset(jeedom.object.cache.html[_params.id])) {
                delete jeedom.object.cache.html[_params.id];
            }
            if (isset(jeedom.object.cache.getEqLogic[_params.id])) {
                delete jeedom.object.cache.getEqLogic[_params.id];
            }
            if ('function' == typeof (_params.success)) {
                _params.success();
            }
        }
    });
};

jeedom.object.save = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "save",
            object: json_encode(_params.object),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if (isset(jeedom.object.cache.all)) {
                delete jeedom.object.cache.all;
            }
            if (isset(jeedom.object.cache.html[data.result.id])) {
                delete jeedom.object.cache.html[data.result.id];
            }
            if (isset(jeedom.object.cache.getEqLogic[data.result.id])) {
                delete jeedom.object.cache.getEqLogic[data.result.id];
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
};


jeedom.object.byId = function(_params) {
    if (isset(jeedom.object.cache.byId[_params.id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.object.cache.byId[_params.id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _params.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            jeedom.object.cache.byId[_params.id] = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.object.cache.byId[_params.id]);
            }
        }
    });
};

jeedom.object.setOrder = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "setOrder",
            objects: json_encode(_params.objects)
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
};