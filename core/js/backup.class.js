
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

jeedom.backup.backup = function(_callback) {
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback();
            }
        }
    });
};


jeedom.backup.restoreLocal = function(_backup, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'restore',
            backup: _backup,
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
                _callback();
            }
        }
    });
};

jeedom.backup.remove = function(_backup, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'removeBackup',
            backup: _backup,
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
                _callback();
            }
        }
    });
};

jeedom.backup.restoreCloud = function(_backup, _callback) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/jeedom.ajax.php',
        data: {
            action: 'restoreCloud',
            backup: _backup,
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
                _callback();
            }
        }
    });
};

jeedom.backup.list = function(_callback) {
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
};