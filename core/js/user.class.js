
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


jeedom.user = function() {
};
jeedom.user.connectCheck = 0;

jeedom.user.all = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "all"
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
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.user.remove = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success();
            }
        }
    });
}

jeedom.user.save = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "save",
            users: json_encode(_params.users)
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
            if ('function' == typeof (_params.success)) {
                _params.success();
            }
        }
    });
}


jeedom.user.saveProfils = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "saveProfils",
            user: json_encode(_params.profils)
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
            if ('function' == typeof (_params.success)) {
                _params.success();
            }
        }
    });
}

jeedom.user.get = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "get",
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
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
};

jeedom.user.logByKey = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "login",
            key: _params.key
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                localStorage.setItem("deviceKey", '');
                if ('function' == typeof (_params.success)) {
                    _params.success(false);
                }
            } else {
                localStorage.setItem("deviceKey", data.result.deviceKey);
                if ('function' == typeof (_params.success)) {
                    _params.success(true);
                }
            }
        }
    });
};

jeedom.user.isConnect = function(_params) {
    var unix = Math.round(+new Date() / 1000);
    if (unix > (jeedom.user.connectCheck + 300)) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/user.ajax.php", // url du fichier php
            data: {
                action: "isConnect",
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_alert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    if ('function' == typeof (_params.success)) {
                        _params.success(false);
                        return;
                    }
                }
                jeedom.user.connectCheck = Math.round(+new Date() / 1000);
                if ('function' == typeof (_params.success)) {
                    _params.success(true);
                }
            }
        });
    } else {
        if ('function' == typeof (_params.success)) {
            _params.success(true);
        }
    }
}

