
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


jeedom.backup = function() {
};

jeedom.backup.backup = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'backup',
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
};


jeedom.backup.restoreLocal = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'restore',
            backup: _params.backup,
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
};

jeedom.backup.remove = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'removeBackup',
            backup: _params.backup,
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
};

jeedom.backup.restoreCloud = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'restoreCloud',
            backup: _params.backup,
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
};

jeedom.backup.list = function(_params) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'listBackup',
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
};