
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
    $('#table_cmd tbody').delegate('.cmdAttr[data-l1key=configuration][data-l2key=mode]', 'change', function() {
        var tr = $(this).closest('tr');
        tr.find('.modeOption').hide();
        tr.find('.modeOption' + '.' + $(this).value()).show();
    });

    optionCmdForDistance = getCmdForDistance();
});

function getCmdForDistance() {
    var select = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/geoloc/core/ajax/geoloc.ajax.php", // url du fichier php
        data: {
            action: "cmdForDistance"
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        async: false,
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            for (var i in data.result) {
                select += '<option value="' + data.result[i].id + '">' + data.result[i].human_name + '</option>';
            }
        }
    });
    return select;
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" ></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="info" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subtype" value="string" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" >';
    tr += '</td>';
    tr += '<td>';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="mode">';
    tr += '<option value="fixe">Fixe</option>';
    tr += '<option value="dynamic">Dynamique</option>';
    tr += '<option value="distance">Distance</option>';
    tr += '</select>';
    tr += '</td>';

    tr += '<td>';
    tr += '<span class="fixe modeOption">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="coordinate" placeholder="Latitude,Longitude" >';
    tr += '</span>';

    tr += '<span class="dynamic modeOption" style="display : none;">';

    tr += '</span>';

    tr += '<span class="distance modeOption" style="display : none;">';
    tr += 'De ';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="from" style="display : inline-block; width : 400px;">';
    tr += optionCmdForDistance;
    tr += '</select>';
    tr += ' à ';
    tr += '<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="to" style="display : inline-block; width : 400px;">';
    tr += optionCmdForDistance;
    tr += '</select>';
    tr += '</span>';

    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="info" style="display : none;">';
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