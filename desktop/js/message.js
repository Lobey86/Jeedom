
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
    $("#sel_plugin").on('change', function() {
        window.location = 'index.php?v=d&p=message&plugin=' + $('#sel_plugin').value();
    });

    $("#bt_clearMessage").on('click', function(event) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/message.ajax.php", // url du fichier php
            data: {
                action: "clearMessage",
                plugin: $('#sel_plugin').value()
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
                window.location.reload();
            }
        });
    });


    $("#table_message").delegate(".removeMessage", 'click', function(event) {
        removeMessage($(this).closest('tr').attr('message_id'))
    });


    $("#table_message").delegate(".bt_changeIsEnable", 'click', function() {
        message_id = $(this).closest('tr').attr('message_id');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/eqLogic.ajax.php", // url du fichier php
            data: {
                action: "setIsEnable",
                id: $(this).attr('eqLogic_id'),
                isEnable: $(this).attr('isEnable')
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
                removeMessage(message_id);
            }
        });
    });
});


function removeMessage(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/message.ajax.php", // url du fichier php
        data: {
            action: "removeMessage",
            id: _id
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
            window.location.reload();
        }
    });
}