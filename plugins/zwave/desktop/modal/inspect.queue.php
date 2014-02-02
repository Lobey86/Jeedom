<?php
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

if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>
<div id='div_inspectQueueAlert' style="display: none;"></div>
<span class='pull-right'>Rafraichis à : <span id='span_inspectQueueRefreshTIme' class='label label-primary' style="font-size: 1.2em;"></span></span><br/><br/>
<table id="table_zwaveQueue" class="table table-bordered table-condensed tablesorter">
    <thead>
        <tr>
            <th>Nombre d'envoi(s)</th>
            <th>Timeout</th>
            <th>Logical ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script>
    updateZwaveQueue();
    initTableSorter();

    function updateZwaveQueue() {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
            data: {
                action: "inspectQueue",
            },
            dataType: 'json',
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_inspectQueueAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_inspectQueueAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#table_zwaveQueue tbody').empty();
                var tr = '';
                for (var i in data.result) {
                    tr += '<tr>';
                    tr += '<td>';
                    tr += data.result[i].sendCount;
                    tr += '</td>';
                    tr += '<td>';
                    tr += data.result[i].timeout;
                    tr += '</td>';
                    tr += '<td>';
                    tr += data.result[i].id;
                    tr += '</td>';
                    tr += '<td>';
                    tr += data.result[i].name;
                    tr += '</td>';
                    tr += '<td>';
                    tr += data.result[i].description;
                    tr += '</td>';
                    tr += '<td>';
                    tr += data.result[i].status;
                    tr += '</td>';
                    tr += '</tr>';
                }
                $('#table_zwaveQueue tbody').append(tr);
                $('#table_zwaveQueue').trigger('update');
                var date = new Date();
                var hour = date.getHours();
                var minute = date.getMinutes();
                var seconde = date.getSeconds();
                var horloge = (hour < 10) ? '0' + hour : hour;
                horloge += ':';
                horloge += (minute < 10) ? '0' + minute : minute;
                horloge += ':';
                horloge += (seconde < 10) ? '0' + seconde : seconde;
                $('#span_inspectQueueRefreshTIme').text(horloge);
                if ($('#table_zwaveQueue').is(':visible')) {
                    setTimeout(updateZwaveQueue, 1000);
                }
            }
        });

    }
</script>
