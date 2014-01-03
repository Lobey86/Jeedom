
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

function jeedomChatAdapter(options) {
    this.defaults = {
// put your options here
    };
    this.opts = $.extend({}, this.defaults, options);
}

jeedomChatAdapter.prototype = {
    init: function(chat, done) {
        if (isset(socket) && socket != null) {
            socket.on('newChatMessage', function(userFromId, userDestId, message) {
                chat.client.sendMessage({UserFromId: userFromId, Message: message});
            });
            socket.on('refreshUserList', function(_connectUserList) {
                if (_connectUserList == null) {
                    $.ajax({// fonction permettant de faire de l'ajax
                        type: "POST", // methode de transmission des données au fichier php
                        url: "core/ajax/chat.ajax.php", // url du fichier php
                        data: {
                            action: "getUserList",
                        },
                        dataType: 'json',
                        error: function(request, status, error) {
                            handleAjaxError(request, status, error);
                        },
                        global: false,
                        success: function(data) { // si l'appel a bien fonctionné
                            if (data.state != 'ok') {
                                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                                return;
                            }
                            chat.client.usersListChanged(data.result);
                        }
                    });
                }
            });
        }

        var _this = this;

        _this.server = {
            sendMessage: function(userDestId, messageText) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "sendMessage",
                        message: messageText,
                        userDestId: userDestId,
                        userFromId: chat.opts.user.Id
                    },
                    dataType: 'json',
                    global: false,
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }

                    }
                });
            },
            sendTypingSignal: function(otherUserId, done) {

            },
            getMessageHistory: function(otherUserId, done) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "getUserHistory",
                        otherUserId: otherUserId,
                    },
                    dataType: 'json',
                    global: false,
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        done(data.result);
                    }
                });
            },
            getUserInfo: function(user_id, done) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "getUserInfo",
                        user_id: user_id,
                    },
                    dataType: 'json',
                    global: false,
                    async : false,
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        done(data.result);
                    }
                });
            },
            getUsersList: function(done) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "getUserList",
                    },
                    dataType: 'json',
                    global: false,
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error);
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        done(data.result);
                    }
                });
            },
        };
        done();
    }
}


