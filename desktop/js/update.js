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
    printUpdate();

    $('#bt_checkAllUpdate').on('click', function() {
        checkAllUpdate();
    });

    $('#table_update').delegate('.changeState', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        var state = $(this).attr('data-state');
        bootbox.confirm('{{Etez vous sur de vouloir changer l\'état de l\'objet ?}}', function(result) {
            if (result) {
                changeStateUpdate(id, state);
            }
        });

    });

    $('#table_update').delegate('.update', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        bootbox.confirm('{{Etez vous sur de vouloir mettre a jour cet objet ?}}', function(result) {
            if (result) {
                updateUpdate(id);
            }
        });
    });
});

function changeStateUpdate(_id, _state) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'changeState',
            id: _id,
            state: _state
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
            printUpdate();
        }
    });
}

function updateUpdate(_id) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'update',
            id: _id
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
            printUpdate();
        }
    });
}

function checkAllUpdate() {
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            printUpdate();
        }
    });
}

function printUpdate() {
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
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#table_update tbody').empty();
            for (var i in data.result) {
                addUpdate(data.result[i]);
            }
        }
    });
}

function addUpdate(_update) {
    $.hideAlert();
    var tr = '<tr data-id="' + init(_update.id) + '">';
    tr += '<td class="enable"><center>';
    tr += '<input type="checkbox" checked/>';
    tr += '</center></td>';
    tr += '<td><span class="updateAttr" data-l1key="type"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="name"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="localVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="remoteVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="status"></span></td>';
    tr += '<td>';
    if (_update.type != 'core') {
        if (_update.status != 'hold') {
            tr += '<a class="btn btn-warning btn-xs pull-right changeState" data-state="hold"><i class="fa fa-lock"></i> Bloquer</a>';
        } else {
            tr += '<a class="btn btn-success btn-xs pull-right changeState" data-state="unhold"><i class="fa fa-unlock"></i> Débloquer</a>';
        }
    }
    if (_update.status == 'update') {
        tr += '<a class="btn btn-info btn-xs pull-right update"><i class="fa fa-refresh"></i> Mettre à jour</a>';
    }
    tr += '</td>';
    tr += '</tr>';
    $('#table_update').append(tr);
    $('#table_update tbody tr:last').setValues(_update, '.updateAttr');
}