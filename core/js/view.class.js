
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

if (!isset(jeedom.view.cache.html)) {
    jeedom.view.cache.html = Array();
}

jeedom.view.all = function(_params) {
    if (isset(jeedom.view.cache.all) && 'function' == typeof (_callback)) {
        _params.success(jeedom.view.cache.all);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
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
            jeedom.view.cache.all = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.view.cache.all);
                return;
            }
        }
    });
}

jeedom.view.prefetch = function(_params) {
    if (_params.version  == 'mobile') {
        _params.version = 'mview';
    }
    if (_params.version  == 'dashboard') {
        _params.version  = 'dview';
    }
    if (!isset(jeedom.view.cache.html)) {
        jeedom.view.cache.html = Array();
    }
    if (!isset(jeedom.view.cache.html[_params.id])) {
        jeedom.view.toHtml({id: _params.id, version: _params.version, useCache: false, globalAjax: false});
    }

}

jeedom.view.toHtml = function(_params) {
    if (_params.version == 'mobile') {
        _params.version = 'mview';
    }
    if (_params.version == 'dashboard') {
        _params.version = 'dview';
    }
    if (init(_params.useCache, false) == true && isset(jeedom.view.cache.html[_params.id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.view.cache.html[_params.id]);
        return;
    }

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "get",
            id: ($.isArray(_params.id)) ? json_encode(_params.id) : _params.id,
            version: _params.version,
        },
        dataType: 'json',
        global: _params.globalAjax || true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var result = {html: '', scenario: [], cmd: [], eqLogic: []};
            if (_params.id == 'all' || $.isArray(_params.id)) {
                for (var i in data.result) {
                    jeedom.view.cache.html[i] = jeedom.view.handleViewAjax({view: data.result[i]});
                }
            } else {
                result = jeedom.view.handleViewAjax({view: data.result});
                jeedom.view.cache.html[_params.id] = result;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(result);
            }
        }
    });
}

jeedom.view.handleViewAjax = function(_params) {
    var result = {html: '', scenario: [], cmd: [], eqLogic: []};
    for (var i in _params.view.viewZone) {
        var viewZone = _params.view.viewZone[i];
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
                result.html += 'jeedom.history.drawChart({cmd_id : ' + viewData.link_id + ',el : "' + div_id + '",daterange : "' + viewZone.configuration.dateRange + ' ",option : jQuery.parseJSON("' + configuration.replace(/\"/g, "\\\"") + '")});';
            }
            result.html += '</script>';
            result.html += '</div>';
        }
        result.html += '</div>';
    }
    return result;
}


jeedom.view.remove = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'remove',
            id: _params.id,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}


jeedom.view.save = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'save',
            view_id: _params.id,
            viewZones: json_encode(_params.viewZones),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.view.get = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'get',
            id: _params.id,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}