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
if (init('id') == '') {
    throw new Exception('EqLogic ID ne peut etre vide');
}
$eqLogic = eqLogic::byId(init('id'));
if (!is_object($eqLogic)) {
    throw new Exception('EqLogic non trouvé');
}
global $listClassCommand;
include_file('core', 'class.command', 'config', 'zwave');
?>
<div id='div_showClassAlert' style="display: none;"></div>
<div class="row">
    <div class="col-lg-2">
        <table id="table_class" class="table table-bordered table-condensed tablesorter">
            <thead>
                <tr>
                    <th>Classes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($eqLogic->getAvailableCommandClass() as $commandClasses) {
                    echo '<tr data-commandClass="' . $commandClasses . '" class="cursor">';
                    echo '<td>';
                    echo $commandClasses;
                    if (count($listClassCommand[$commandClasses]) > 0) {
                        echo '<span class="label label-success pull-right">' . count($listClassCommand[$commandClasses]) . '<span>';
                    } else {
                        echo '<span class="label label-default pull-right">' . count($listClassCommand[$commandClasses]) . '<span>';
                    }

                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="col-lg-10" id='div_showClassInformations'>
        <form class="form-horizontal">
            <fieldset>
                <legend>Informations <a class='pull-right btn btn-success btn-xs' style="color : white;" id='bt_addClassCommand'>Ajouter</a></legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nom</label>
                    <div class="col-lg-8">
                        <span class='showClassAttr label label-primary' data-l1key='name'></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Description</label>
                    <div class="col-lg-8">
                        <div class='showClassAttr label label-info' data-l1key='description'></div>
                    </div>
                </div>
            </fieldset>
        </form>
        <table id="table_classCommands" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th><input type='checkbox' id='cb_selectAllCommand' /></th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Commande</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>


<script>

    $('#table_class tbody tr').on('click', function() {
        $('#table_class tbody tr').removeClass('active');
        $(this).addClass('active');
        syncEqLogicWithRazberry($(this).attr('data-commandClass'));
    });

    $('#bt_addClassCommand').on('click', function() {
        $('#table_classCommands tbody tr').each(function() {
            if ($(this).find('.classCommandAttr[data-l1key=enable]').prop('checked')) {
                addCmdToTable(json_decode($(this).find('.classCommandAttr[data-l1key=json_cmd]').value()));
            }
        });
        $('#div_showClassAlert').showAlert({message: 'Commandes ajoutées', level: 'success'});
    });

    $('#cb_selectAllCommand').on('click', function() {
        $('#table_classCommands tbody tr .classCommandAttr[data-l1key=enable]').prop('checked', $(this).prop('checked'));
    });

    function syncEqLogicWithRazberry(_commandClass) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
            data: {
                action: "getCommandClassInfo",
                class: _commandClass
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_showClassAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_showClassAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_showClassInformations').setValues(data.result, '.showClassAttr');
                $('#table_classCommands tbody').empty();

                for (var i in data.result.commands) {
                    data.result.commands[i].json_cmd = json_encode(data.result.commands[i]);
                    var tr = '';
                    tr += '<tr>';
                    tr += '<td>';
                    tr += '<input style="display: none;" class="classCommandAttr" data-l1key="json_cmd"/>';
                    tr += '<input type="checkbox" class="classCommandAttr" data-l1key="enable"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<span class="classCommandAttr label label-default" data-l1key="name"></span> ';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<span class="classCommandAttr label label-default" data-l1key="type"></span> ';
                    tr += '<span class="classCommandAttr label label-default" data-l1key="subtype"></span> ';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<span class="classCommandAttr label label-default" data-l1key="configuration" data-l2key="value" ></span> ';
                    tr += '</td>';
                    tr += '</tr>';
                    $('#table_classCommands tbody').append(tr);
                    $('#table_classCommands tbody tr:last').setValues(data.result.commands[i], '.classCommandAttr');
                }
            }
        });
    }



</script>   