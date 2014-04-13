
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

$(function() {

    $('body').one('nodeJsConnect', function() {
        $.chat({
            user: {
                Id: user_id,
                Name: user_login,
                ProfilePictureUrl: 'core/img/noPicture.gif'
            },
            typingText: ' {{en train d\'écrire...}}',
            titleText: 'Jeedom chat',
            emptyRoomText: "{{Il n'y a personne}}",
            adapter: new jeedomChatAdapter()
        });


        socket.on('refreshUserList', function(_connectUserList) {
            if (_connectUserList != null) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "refreshConnectUser",
                        connectUserList: json_encode(_connectUserList),
                    },
                    global: false,
                    dataType: 'json',
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
            }
        });
    });
});