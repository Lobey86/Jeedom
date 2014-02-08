
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

object.getEqLogic = function(_object_id, _cmd_eqTypeName) {
    if (!isset(object.cache.getEqLogic)) {
        object.cache.getEqLogic = Array();
    }
    if (isset(object.cache.getEqLogic[_object_id + _cmd_eqTypeName])) {
        return object.cache.getEqLogic[_object_id + _cmd_eqTypeName];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "listByObjectAndCmdType",
            object_id: _object_id,
            typeCmd: _cmd_eqTypeName
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
    object.cache.getEqLogic[_object_id + _cmd_eqTypeName] = result;
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