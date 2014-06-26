
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


jeedom.update = function() {
};


jeedom.update.doAll = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'updateAll',
            level: _params.level,
            mode: _params.mode
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
                _params.success();
            }
        }
    });
}

jeedom.update.changeState = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'changeState',
            id: _params.id,
            state: _params.state
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
                _params.success();
            }
        }
    });
}

jeedom.update.do = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'update',
            id: _params.id
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
                _params.success();
            }
        }
    });
}

jeedom.update.remove = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'remove',
            id: _params.id
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
                _params.success();
            }
        }
    });
}

jeedom.update.checkAll = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'checkAllUpdate'
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
                _params.success();
            }
        }
    });
}


jeedom.update.get = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'all'
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