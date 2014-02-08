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