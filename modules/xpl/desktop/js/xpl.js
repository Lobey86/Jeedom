
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
    $('#table_cmd tbody').delegate('tr .cmdAttr[l1key=configuration][l2key=xPLtypeCmd]', 'change', function() {
        changexPLTypeCmd($(this));
    });

    $('#table_cmd tbody').delegate('tr .cmdAttr[l1key=configuration][l2key=xPLschema]', 'change', function() {
        changexPLTypeCmd($(this));
    });

    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
});


function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }

    var selxPlschema = '<select class="cmdAttr form-control" l1key="configuration" l2key="xPLschema" style="width : 150px;">';
    selxPlschema += '<option value="control.basic">Control.basic</option>';
    selxPlschema += '<option value="sensor.basic">Sensor.basic</option>';
    selxPlschema += '</select>';

    var typeXmdxPL = '<select class="cmdAttr form-control" l1key="configuration" l2key="xPLtypeCmd" style="width : 150px;margin-top : 5px;">';
    typeXmdxPL += '<option value="XPL-CMND">XPL-CMND</option>';
    //typeXmdxPL += '<option value="XPL-STAT">XPL-STAT</option>';
    typeXmdxPL += '<option value="XPL-TRIG">XPL-TRIG</option>';
    typeXmdxPL += '</select>';

    var tr = '<tr class="cmd" cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control" l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control" l1key="name" value="' + init(_cmd.name) + '"></td>';
    tr += '<td>' + selxPlschema + typeXmdxPL + '</td>';
    tr += '<td class="xPLbody">';
    tr += '<textarea style="height : 100px;" class="cmdAttr form-control" l1key="configuration" l2key="xPLbody">' + init(_cmd.configuration.xPLbody) + '</textarea>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" l1key="isHistorized" /> Historiser<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr" l1key="eventOnly" /> Evenement seulement<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr" l1key="cache" l2key="enable" checked /> Autoriser memcache</span>';
    tr += '</td>';
    tr += '<td><input class="cmdAttr  form-control" l1key="unite" style="width : 100px;"></td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" action="test"><i class="fa fa-rss"></i> Tester</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction" action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    $('#table_cmd tbody tr:last .cmdAttr[l1key=configuration][l2key=xPLschema]').trigger('change');
}

function changexPLTypeCmd(_el, _xPLbody) {
    var tr = _el.closest('tr');
    tr.find('.cmdAttr[l1key=isHistorized]').show();
    tr.find('.cmdAttr[l1key=cache][l2key=enable]').parent().show();
    tr.find('.cmdAttr[l1key=eventOnly]').parent().show();
    switch (_el.value()) {
        case 'XPL-CMND' :
            tr.find('.test_xpl').show();
            tr.find('.eventOnly').parent().hide();
            break;
        case 'XPL-STAT' :
            tr.find('.test_xpl').hide();
            break;
        case 'XPL-TRIG' :
            tr.find('.eventOnly').prop('checked', true);
            tr.find('.test_xpl').hide();
            break;
    }
    updatexPLbody(tr.find('.cmdAttr[l1key=configuration][l2key=xPLschema]'), _xPLbody);
}

function updatexPLbody(_el, _xPLbody) {
    if (!isset(_xPLbody)) {
        var xPLschema = _el.value();
        var xPltypeCmd = _el.parent().find('.cmdAttr[l1key=configuration][l2key=xPLtypeCmd]').value();
        var tr = _el.closest('tr');
        tr.find('.cmdAttr[l1key=configuration][l2key=xPLbody]').value(getxPLbody(xPLschema, xPltypeCmd));
    }
}

function getxPLbody(_xPLschema, _xPltypeCmd) {
    var body = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/xpl/core/ajax/xpl.ajax.php", // url du fichier php
        data: {
            action: "getxPLbody",
            xPLschema: _xPLschema,
            xPLtypeCmd: _xPltypeCmd
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            body = $.trim(data.result);
        }
    });
    return body;
}