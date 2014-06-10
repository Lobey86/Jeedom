
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
    $("#security_tab").delegate('a', 'click', function(event) {
        $(this).tab('show');
        $.hideAlert();
    });

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Suppression effectuée avec succès}}', level: 'success'});
    }

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Sauvegarde effectuée avec succès}}', level: 'success'});
    }

    $('#bt_saveSecurityConfig').on('click', function() {
        var configuration = $('#config').getValues('.configKey');
        jeedom.config.save(configuration[0], 'core', function() {
            var configuration = $('#config').getValues('.configKey');
            jeedom.config.load(configuration[0], 'core', function(data) {
                $('#config').setValues(data, '.configKey');
                modifyWithoutSave = false;
                $('#div_alert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            });
        });
    });

    $('#table_security').delegate('.remove', 'click', function() {
        var tr = $(this).closest('tr');
        bootbox.confirm("{{Etês-vous sur de vouloir supprimer cette connection ? Si l\'IP :}} " + tr.find('.ip').text() + " {{était banni celle-ci ne le sera plus}}", function(result) {
            if (result) {
                remove(tr.attr('data-id'));
            }
        });
    });

    $('#table_security').delegate('.ban', 'click', function() {
        var tr = $(this).closest('tr');
        bootbox.confirm("{{Etês-vous sur de vouloir bannir cette IP  :}} " + tr.find('.ip').text() + " ?", function(result) {
            if (result) {
                ban(tr.attr('data-id'));
            }
        });
    });

    var configuration = $('#config').getValues('.configKey');
    jeedom.config.load(configuration[0], 'core', function(data) {
        $('#config').setValues(data, '.configKey');
    });
});

function remove(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/connection.ajax.php", // url du fichier php
        data: {
            action: "remove",
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
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=security&removeSuccessFull=1');
        }
    });
}

function ban(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/connection.ajax.php", // url du fichier php
        data: {
            action: "ban",
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
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=security&saveSuccessFull=1');
        }
    });
}