
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

jeedom.object.getEqLogic = function(_object_id) {
    if (!isset(jeedom.object.cache.getEqLogic)) {
        jeedom.object.cache.getEqLogic = Array();
    }
    if (isset(jeedom.object.cache.getEqLogic[_object_id])) {
        return jeedom.object.cache.getEqLogic[_object_id];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "listByObject",
            object_id: _object_id,
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            result = data.result;
        }
    });
    jeedom.object.cache.getEqLogic[_object_id] = result;
    return result;
};

jeedom.object.all = function() {
    if (isset(jeedom.object.cache.all)) {
        return jeedom.object.cache.all;
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "all",
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            result = data.result;
        }
    });
    jeedom.object.cache.all = result;
    return result;
};

jeedom.object.prefetch = function(_id, _version, _async) {
    if (!isset(jeedom.object.cache.html)) {
        jeedom.object.cache.html = Array();
    }
    if (!isset(jeedom.object.cache.html[_id])) {
        jeedom.object.toHtml(_id, _version, false, false, init(_async, true));
    }
};

jeedom.object.toHtml = function(_id, _version, _useCache, _globalAjax, _async) {
    if (!isset(jeedom.object.cache.html)) {
        jeedom.object.cache.html = Array();
    }
    if (init(_useCache, false) == true && isset(jeedom.object.cache.html[_id])) {
        return jeedom.object.cache.html[_id];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: ($.isArray(_id)) ? json_encode(_id) : _id,
            version: _version
        },
        dataType: 'json',
        async: init(_async, false),
        global: init(_globalAjax, true),
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
                    jeedom.object.cache.html[i] = data.result[i];
                }
            } else {
                if (isset(jeedom) && isset(jeedom.workflow) && isset(jeedom.workflow.object) && jeedom.workflow.object[_id]) {
                    jeedom.workflow.object[_id] = false;
                }
                jeedom.object.cache.html[_id] = result;
            }
            result = data.result;
        }
    });
    return result;
};

jeedom.object.remove = function(_id, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback();
            }
        }
    });
};

jeedom.object.save = function(_object, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "save",
            object: json_encode(_object),
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


jeedom.object.byId = function(_id, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _id
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

jeedom.object.setOrder = function(_objects, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "setOrder",
            objects: json_encode(_objects)
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