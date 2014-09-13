
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


printJeeNetwork();


$("#bt_addJeeNetwork").on('click', function() {
    addJeeNetwork({});
});

$("#bt_save").on('click', function() {
    jeedom.jeeNetwork.save({
        crons: $('#table_jeeNetwork tbody tr').getValues('.jeeNetworkAttr'),
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: printCron
    });
});

$("#table_jeeNetwork").delegate(".remove", 'click', function() {
    $(this).closest('tr').remove();
});

$('body').delegate('.jeeNetworkAttr', 'change', function() {
    modifyWithoutSave = true;
});

function printJeeNetwork() {
    jeedom.jeeNetwork.all({
        success: function(data) {
            $('#table_jeeNetwork tbody').empty();
            for (var i in data.jeeNetworks) {
                addJeeNetwork(data.jeeNetworks[i]);
            }
            $("#table_cron").trigger("update");
            modifyWithoutSave = false;
        }
    });
}

function addJeeNetwork(_jeeNetwork) {
    $.hideAlert();
    var tr = '<tr id="' + init(_jeeNetwork.id) + '">';
    tr += '<td><input class="form-control jeeNetworkAttr" data-l1key="id" /></td>';
    tr += '<td><input class="form-control jeeNetworkAttr" data-l1key="ip" /></td>';
    tr += '<td><input class="jeeNetworkAttr form-control" data-l1key="apikey" /></td>';
    tr += '<td></td>';
    tr += '</tr>';
    $('#table_cron').append(tr);
    $('#table_cron tbody tr:last').setValues(_cron, '.cronAttr');
}
