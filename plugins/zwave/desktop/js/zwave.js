
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
    $(".li_eqLogic").on('click', function() {
        printPluginInfo($(this).attr('data-eqLogic_id'));
        return false;
    });

    $('#bt_syncEqLogic').on('click', function() {
        syncEqLogicWithRazberry();
    });
    $('.changeIncludeState').on('click', function() {
        changeIncludeState($(this).attr('data-state'));
    });

    $('#bt_showClass').on('click', function() {
        $('#md_modal').dialog({title: "Classe du périphérique"});
        $('#md_modal').load('index.php?v=d&plugin=zwave&modal=show.class&id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
    });

    $('#bt_configureDevice').on('click', function() {
        $('#md_modal').dialog({title: "Configuration du péréphérique"});
        $('#md_modal').load('index.php?v=d&plugin=zwave&modal=configure.device&id=' + $('.eqLogicAttr[data-l1key=id]').value()).dialog('open');
    });

    $('#bt_inspectQueue').on('click', function() {
        $('#md_modal').dialog({title: "Queue Z-wave"});
        $('#md_modal').load('index.php?v=d&plugin=zwave&modal=inspect.queue').dialog('open');
    });

    $('#bt_routingTable').on('click', function() {
        $('#md_modal').dialog({title: "Table de routage"});
        $('#md_modal').load('index.php?v=d&plugin=zwave&modal=routing.table').dialog('open');
    });

    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});


    $('body').delegate(".getHelpSchedule", 'click', function() {
        showHelpModal("cronSyntaxe");
    });

    /**********************Node js requests *****************************/
    $('body').one('nodeJsConnect', function() {
        socket.on('zwave::controller.data.controllerState', function(_options) {
            if (_options == 1) {
                $('.changeIncludeState[data-state=1]').removeClass('btn-default').addClass('btn-success');
            }
            if (_options == 5) {
                $('.changeIncludeState[data-state=0]').removeClass('btn-default').addClass('btn-danger');
            }
            if (_options == 0) {
                $('.changeIncludeState').addClass('btn-default').removeClass('btn-success btn-danger');
            }
        });
    });
});

function printPluginInfo(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
        data: {
            action: "getPluginInfo",
            id: _id,
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
            $('.zwaveInfo').value('');
            for (var i in data.result) {
                var value = data.result[i]['value'];
                if (isset(data.result[i]['unite'])) {
                    value += ' ' + data.result[i]['unite'];
                }
                $('.zwaveInfo[data-l1key=' + i + ']').value(value);
                $('.zwaveInfo[data-l1key=' + i + ']').attr('title', data.result[i]['datetime']);
            }
        }
    });
}


function syncEqLogicWithRazberry() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
        data: {
            action: "syncEqLogicWithRazberry",
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

function changeIncludeState(_state) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
        data: {
            action: "changeIncludeState",
            state: _state,
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
        }
    });
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }

    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" >';
    tr += '<select class="cmdAttr form-control tooltips input-sm" data-l1key="value" style="display : none;margin-top : 5px;" title="La valeur de la commande vaut par defaut la commande">';
    tr += eqLogic.builSelectCmd($(".li_eqLogic.active").attr('data-eqLogic_id'), 'info');
    tr += '</select>';
    tr += '</td>';
    tr += '<td class="expertModeHidden">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td class="expertModeHidden"><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="instanceId" value="0"></td>';
    tr += '<td class="expertModeHidden"><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="class" ></td>';
    tr += '<td class="expertModeHidden"><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value" ></td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" /> Historiser<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> Afficher<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr expertModeHidden" data-l1key="eventOnly" /> Evénement<br/></span>';
    tr += '<input style="width : 150px;" class="tooltips cmdAttr form-control expertModeHidden input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="Lifetime cache" title="Lifetime cache">';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control tooltips input-sm" data-l1key="unite"  style="width : 100px;" placeholder="Unité" title="Unité">';
    tr += '<input class="tooltips cmdAttr form-control input-sm expertModeHidden" data-l1key="configuration" data-l2key="minValue" placeholder="Min" title="Min"> ';
    tr += '<input class="tooltips cmdAttr form-control input-sm expertModeHidden" data-l1key="configuration" data-l2key="maxValue" placeholder="Max" title="Max" style="margin-top : 5px;">';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> Tester</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    cmd.changeType($('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]'), init(_cmd.subType));
}