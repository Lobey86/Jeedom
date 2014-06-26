
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

jeedom.view.all = function(_callback) {
    if (isset(jeedom.view.cache.all) && 'function' == typeof (_callback)) {
        _callback(jeedom.view.cache.all);
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
            if ('function' == typeof (_callback)) {
                _callback(jeedom.view.cache.all);
                return;
            }
        }
    });
}

jeedom.view.prefetch = function(_id, _version) {
    if (_version == 'mobile') {
        _version = 'mview';
    }
    if (_version == 'dashboard') {
        _version = 'dview';
    }
    if (!isset(jeedom.view.cache.html)) {
        jeedom.view.cache.html = Array();
    }
    if (!isset(jeedom.view.cache.html[_id])) {
        jeedom.view.toHtml(_id, _version, false, false);
    }

}

jeedom.view.toHtml = function(_id, _version, _useCache, _globalAjax, _callback) {
    if (_version == 'mobile') {
        _version = 'mview';
    }
    if (_version == 'dashboard') {
        _version = 'dview';
    }
    if (init(_useCache, false) == true && isset(jeedom.view.cache.html[_id]) && 'function' == typeof (_callback)) {
        _callback(jeedom.view.cache.html[_id]);
        return;
    }

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "get",
            id: ($.isArray(_id)) ? json_encode(_id) : _id,
            version: _version,
        },
        dataType: 'json',
        global: init(_globalAjax, true),
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var result = {html: '', scenario: [], cmd: [], eqLogic: []};
            if (_id == 'all' || $.isArray(_id)) {
                for (var i in data.result) {
                    jeedom.view.cache.html[i] = jeedom.view.handleViewAjax(data.result[i]);
                }
            } else {
                result = jeedom.view.handleViewAjax(data.result);
                jeedom.view.cache.html[_id] = result;
            }
            if ('function' == typeof (_callback)) {
                _callback(result);
            }
        }
    });
}

jeedom.view.handleViewAjax = function(_view) {
    var result = {html: '', scenario: [], cmd: [], eqLogic: []};
    for (var i in _view.viewZone) {
        var viewZone = _view.viewZone[i];
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


jeedom.view.remove = function(_id, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'remove',
            id: _id,
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}


jeedom.view.save = function(_view_id, viewZones, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'save',
            view_id: _view_id,
            viewZones: json_encode(viewZones),
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.view.get = function(_view_id, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'get',
            id: _view_id,
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}