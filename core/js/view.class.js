
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


jeedom.view = function() {
};

jeedom.view.cache = Array();

jeedom.view.all = function() {
    if (isset(jeedom.view.cache.all)) {
        return jeedom.view.cache.all;
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
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
    jeedom.view.cache.all = result;
    return result;
}

jeedom.view.prefetch = function(_id, _version, _async) {
    if (!isset(jeedom.view.cache.html)) {
        jeedom.view.cache.html = Array();
    }
    if (!isset(jeedom.view.cache.html[_id])) {
        jeedom.view.toHtml(_id, _version, false, false, init(_async, true));
    }

}

jeedom.view.toHtml = function(_id, _version, _allowCache, _globalAjax, _async) {
    if (!isset(jeedom.view.cache.html)) {
        jeedom.view.cache.html = Array();
    }
    if (init(_allowCache, false) == true && isset(jeedom.view.cache.html[_id])) {
        return jeedom.view.cache.html[_id];
    }
    var result = {html: '', scenario: [], cmd: [], eqLogic: []};
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "getView",
            id: ($.isArray(_id)) ? json_encode(_id) : _id,
            version: _version,
        },
        dataType: 'json',
        async: init(_async, false),
        global: init(_globalAjax, true),
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (_id == 'all' || $.isArray(_id)) {
                for (var i in data.result) {
                    jeedom.view.cache.html[i] = jeedom.view.handleViewAjax(data.result[i]);
                }
            } else {
                result = jeedom.view.handleViewAjax(data.result);
                jeedom.view.cache.html[_id] = result;
            }
        }
    });
    return result;
}

jeedom.view.handleViewAjax = function(_view) {
    var result = {html: '', scenario: [], cmd: [], eqLogic: []};
    for (var i in _jeedom.view.viewZone) {
        var viewZone = _jeedom.view.viewZone[i];
        result.html += '<div>';
        result.html += '<legend style="color : #716b7a">' + viewZone.name + '</legend>';
        var div_id = 'div_viewZone' + viewZone.id;
        /*         * *****************viewZone widget***************** */
        if (viewZone.type == 'widget') {
            result.html += '<div id="' + div_id + '" class="eqLogicZone">';
            for (var j in viewZone.viewData) {
                var viewData = viewZone.viewData[j];
                result.html += viewData.html;
                result[viewData.type].push(viewData.id);
            }
            result.html += '</div>';
        }
        /*         * *****************viewZone graph***************** */
        if (viewZone.type == 'graph') {
            result.html += '<div id="' + div_id + '" class="chartContainer">';
            result.html += '<script>';
            for (var j in viewZone.viewData) {
                var viewData = viewZone.viewData[j];
                var configuration = json_encode(viewData.configuration);
                result.html += 'drawChart(' + viewData.link_id + ',"' + div_id + '","' + viewZone.configuration.dateRange + ' ",jQuery.parseJSON("' + configuration.replace(/\"/g, "\\\"") + '"));';
            }
            result.html += '</script>';
            result.html += '</div>';
        }
        result.html += '</div>';
    }
    return result;
}