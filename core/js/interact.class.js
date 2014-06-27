
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


jeedom.interact = function() {
};

jeedom.interact.remove = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/interact.ajax.php", // url du fichier php
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
            if ('function' == typeof (_params.success)) {
                _params.success();
            }
        }
    });
}


jeedom.interact.get = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/interact.ajax.php", // url du fichier php
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
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.interact.save = function(_params) {
    $.hideAlert();
    $.ajax({
        type: 'POST',
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: 'save',
            interact: json_encode(_params.interact),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}