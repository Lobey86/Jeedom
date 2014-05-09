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

    $('#bt_updateAll').on('click', function() {
        bootbox.confirm('{{Etes-vous sur de vouloir tout mettre à jour tous les plugins ?}} ', function(result) {
            if (result) {
                updateAll();
            }
        });
    });

    $('#bt_updateCore').on('click', function() {
        bootbox.confirm('{{Etes-vous sur de vouloir mettre à jour Jeedom ?}} ', function(result) {
            if (result) {
                updateAll('core');
            }
        });
    });

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
                doUpdate(id);
            }
        });
    });

    $('#table_update').delegate('.remove', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        bootbox.confirm('{{Etez vous sur de vouloir supprimer cet objet ?}}', function(result) {
            if (result) {
                removeUpdate(id);
            }
        });
    });

    $('#bt_expertMode').on('click', function() {
        printUpdate();
    });
});

function updateAll(_filter) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'updateAll',
            filter: _filter
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
            getJeedomLog(1, 'update');
        }
    });
}

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

function doUpdate(_id) {
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
            getJeedomLog(1, 'update');
        }
    });
}

function removeUpdate(_id) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/update.ajax.php',
        data: {
            action: 'remove',
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

function getJeedomLog(_autoUpdate, _log) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/log.ajax.php',
        data: {
            action: 'get',
            logfile: _log,
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var log = '';
            var regex = /<br\s*[\/]?>/gi;
            for (var i in data.result.reverse()) {
                log += data.result[i][2].replace(regex, "\n");
                if ($.trim(data.result[i][2].replace(regex, "\n")) == '[END ' + _log.toUpperCase() + ' SUCCESS]') {
                    printUpdate();
                    $('#div_alert').showAlert({message: '{{L\'opération est réussie}}', level: 'success'});
                    _autoUpdate = 0;
                }
                if ($.trim(data.result[i][2].replace(regex, "\n")) == '[END ' + _log.toUpperCase() + ' ERROR]') {
                    printUpdate();
                    $('#div_alert').showAlert({message: '{{L\'opération a échoué}}', level: 'danger'});
                    _autoUpdate = 0;
                }
            }
            $('#pre_' + _log + 'Info').text(log);
            if (init(_autoUpdate, 0) == 1) {
                setTimeout(function() {
                    getJeedomLog(_autoUpdate, _log)
                }, 1000);
            } else {
                $('#bt_' + _log + 'Jeedom .fa-refresh').hide();
                $('.bt_' + _log + 'Jeedom .fa-refresh').hide();
            }
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
            $('#table_update').trigger('update');
        }
    });
}

function addUpdate(_update) {
    $.hideAlert();
    if (_update.status != 'update' && _update.type != 'core') {
        if ($('#bt_expertMode').attr('state') == 0) {
            return;
        }
    }
    var tr = '<tr data-id="' + init(_update.id) + '">';
    tr += '<td><span class="updateAttr" data-l1key="type"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="name"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="localVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="remoteVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="status"></span></td>';
    tr += '<td>';
    if (_update.type != 'core') {
        tr += '<a class="btn btn-danger btn-xs pull-right remove expertModeHidden" data-state="unhold" style="color : white;"><i class="fa fa-trash-o"></i> Supprimer</a>';
        if (_update.status != 'hold') {
            tr += '<a class="btn btn-warning btn-xs pull-right changeState expertModeHidden" data-state="hold" style="color : white;"><i class="fa fa-lock"></i> Bloquer</a>';
        } else {
            tr += '<a class="btn btn-success btn-xs pull-right changeState expertModeHidden" data-state="unhold" style="color : white;"><i class="fa fa-unlock"></i> Débloquer</a>';
        }
    }
    if (_update.status == 'update') {
        tr += '<a class="btn btn-info btn-xs pull-right update" style="color : white;"><i class="fa fa-refresh"></i> Mettre à jour</a>';
    }
    tr += '</td>';
    tr += '</tr>';
    $('#table_update').append(tr);
    $('#table_update tbody tr:last').setValues(_update, '.updateAttr');
}