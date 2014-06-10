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

    $('.bt_updateAll').on('click', function() {
        var level = $(this).attr('data-level');
        var mode = $(this).attr('data-mode');
        bootbox.confirm('{{Etes-vous sur de vouloir faire les mises à jour ?}} ', function(result) {
            if (result) {
                jeedom.update.doAll(mode, level, function() {
                    getJeedomLog(1, 'update');
                });
            }
        });
    });

    $('#bt_checkAllUpdate').on('click', function() {
        jeedom.update.all(function() {
            printUpdate();
        });
    });

    $('#table_update').delegate('.changeState', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        var state = $(this).attr('data-state');
        bootbox.confirm('{{Etez vous sur de vouloir changer l\'état de l\'objet ?}}', function(result) {
            if (result) {
                jeedom.update.changeState(id, state, function() {
                    printUpdate();
                });
            }
        });

    });

    $('#table_update').delegate('.update', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        bootbox.confirm('{{Etez vous sur de vouloir mettre a jour cet objet ?}}', function(result) {
            if (result) {
                jeedom.update.do(id, function() {
                    getJeedomLog(1, 'update');
                });
            }
        });
    });

    $('#table_update').delegate('.remove', 'click', function() {
        var id = $(this).closest('tr').attr('data-id');
        bootbox.confirm('{{Etez vous sur de vouloir supprimer cet objet ?}}', function(result) {
            if (result) {
                jeedom.update.remove(id, function() {
                    printUpdate();
                });
            }
        });
    });

    $('#table_update').delegate('.view', 'click', function() {
        $('#md_modal').dialog({title: "Market"});
        $('#md_modal').load('index.php?v=d&modal=market.display&type=' + $(this).closest('tr').attr('data-type') + '&logicalId=' + encodeURI($(this).closest('tr').attr('data-logicalId'))).dialog('open');
    });

    $('#table_update').delegate('.sendToMarket', 'click', function() {
        $('#md_modal').dialog({title: "Partager sur le market"});
        $('#md_modal').load('index.php?v=d&modal=market.send&type=' + $(this).closest('tr').attr('data-type') + '&logicalId=' + encodeURI($(this).closest('tr').attr('data-logicalId')) + '&name=' + encodeURI($(this).closest('tr').attr('data-logicalId'))).dialog('open');
    });

    $('#bt_expertMode').on('click', function() {
        printUpdate();
    });
});

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
            setTimeout(function() {
                getJeedomLog(_autoUpdate, _log)
            }, 1000);
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
    jeedom.update.get(function(data) {
        $('#table_update tbody').empty();
        for (var i in data) {
            addUpdate(data[i]);
        }
        $('#table_update').trigger('update');
        initTooltips();
    });
}

function addUpdate(_update) {
    $.hideAlert();
    if (_update.status != 'update' && _update.type != 'core') {
        if ($('#bt_expertMode').attr('state') == 0) {
            return;
        }
    }
    var tr = '<tr data-id="' + init(_update.id) + '" data-logicalId="' + init(_update.logicalId) + '" data-type="' + init(_update.type) + '">';
    tr += '<td><span class="updateAttr" data-l1key="type"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="name"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="localVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="remoteVersion"></span></td>';
    tr += '<td><span class="updateAttr" data-l1key="status"></span></td>';
    tr += '<td>';
    if (_update.status == 'update') {
        tr += '<a class="btn btn-info btn-xs pull-right update tooltips" style="color : white;" title="{{Mettre à jour}}"><i class="fa fa-refresh"></i> Metrre à jour</a>';
    }

    if (_update.type != 'core') {
        tr += '<a class="btn btn-danger btn-xs pull-right remove expertModeVisible tooltips" data-state="unhold" style="color : white;" ><i class="fa fa-trash-o"></i> {{Supprimer}}</a>';
        if (_update.status != 'hold') {
            tr += '<a class="btn btn-warning btn-xs pull-right changeState expertModeVisible tooltips" data-state="hold" style="color : white;"><i class="fa fa-lock"></i> {{Bloquer}}</a>';
        } else {
            tr += '<a class="btn btn-success btn-xs pull-right changeState expertModeVisible tooltips" data-state="unhold" style="color : white;"><i class="fa fa-unlock"></i> {{Débloquer}}</a>';
        }
        if (isset(_update.configuration) && isset(_update.configuration.market_owner) && _update.configuration.market_owner == 1) {
            tr += '<a class="btn btn-success btn-xs pull-right sendToMarket tooltips cursor expertModeVisible" style="color : white;" title="{{Envoyer sur le market}}"><i class="fa fa-cloud-upload"></i> {{Partager}}</a>';
        }
        if (isset(_update.configuration) && isset(_update.configuration.market) && _update.configuration.market == 1) {
            tr += '<a class="btn btn-primary btn-xs pull-right view tooltips cursor" style="color : white;"><i class="fa fa-search"></i> {{Voir}}</a>';
        }
    }

    tr += '</td>';
    tr += '</tr>';
    $('#table_update').append(tr);
    $('#table_update tbody tr:last').setValues(_update, '.updateAttr');
}