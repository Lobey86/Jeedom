
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
    $("#bt_addVirtualInfo").on('click', function(event) {
        var _cmd = {type: 'info'};
        addCmdToTable(_cmd);
    });

    $("#bt_addVirtualAction").on('click', function(event) {
        var _cmd = {type: 'action'};
        addCmdToTable(_cmd);
    });

    $('#table_cmd tbody').delegate('tr .remove', 'click', function(event) {
        $(this).closest('tr').remove();
    });

    $('#table_cmd tbody').delegate('tr .remove', 'click', function(event) {
        $(this).closest('tr').remove();
    });

    $("#table_cmd").delegate(".listEquipementInfo", 'click', function() {
        var el = $(this);
        cmd.getSelectModal({type: 'info'}, function(result) {
            var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=calcul]');
            calcul.value(calcul.value() + ' ' + result.human);
        });
    });

    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
});

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    if (init(_cmd.type) == 'info') {
        var disabled = (init(_cmd.configuration.virtualAction) == '1') ? 'disabled' : '';
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="Nom"></td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
        tr += '<td><textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="calcul" style="height : 33px;" ' + disabled + ' placeholder="Calcul"></textarea>';
        tr += '<a class="btn btn-default cursor listEquipementInfo" style="margin-top : 5px;"><i class="fa fa-list-alt "></i> Rechercher équipement</a></td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="unite" style="width : 90px;" placeholder="Unite"></td>';
        tr += '<td>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized"/> Historiser<br/></span>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> Afficher<br/></span>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="eventOnly"' + disabled + '/> Evénement seulement<br/></span>';
        tr += '<input style="width : 150px;" class="tooltips cmdAttr form-control input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="Lifetime cache" title="Lifetime cache">';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> Tester</a>';
        }
        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
        tr += '</tr>';
        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    }

    if (init(_cmd.type) == 'action') {
        var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="Nom"></td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '<input class="cmdAttr" data-l1key="configuration" data-l2key="virtualAction" value="1" style="display:none;" >';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="infoName" placeholder="Nom information" style="margin-bottom : 5px;">';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="value"placeholder="Valeur"></td>';
        tr += '<td></td>';
        tr += '<td>';
        tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/> Afficher<br/></span>';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> Tester</a>';
        }
        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
        tr += '</tr>';

        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    }
}
