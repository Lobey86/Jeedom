
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


function object() {

}

object.cache = Array();

object.getEqLogic = function(_object_id) {
    if (!isset(object.cache.getEqLogic)) {
        object.cache.getEqLogic = Array();
    }
    if (isset(object.cache.getEqLogic[_object_id])) {
        return object.cache.getEqLogic[_object_id];
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
    object.cache.getEqLogic[_object_id] = result;
    return result;
}

object.all = function() {
    if (isset(object.cache.all)) {
        return object.cache.all;
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
    object.cache.all = result;
    return result;
}

object.prefetch = function(_id, _version, _forced) {
    setTimeout(function() {
        if (!isset(object.cache.html)) {
            object.cache.html = Array();
        }
        if (init(_forced, false) == true || !isset(object.cache.html[_id])) {
            object.cache.html[_id] = object.toHtml(_id, _version, false, false);
        }
    }, 0);
}

object.toHtml = function(_id, _version, _useCache, _globalAjax) {
    if (!isset(object.cache.html)) {
        object.cache.html = Array();
    }
    if (init(_useCache, false) == true && isset(object.cache.html[_id])) {
        return object.cache.html[_id];
    }
    var result = '';
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: _id,
            version: _version
        },
        dataType: 'json',
        async: false,
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
            result = data.result;
        }
    });
    return result;
}