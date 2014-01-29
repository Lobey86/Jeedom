<?php
if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
include_file('core', 'js.inc', 'php');
?>
<table class="table table-condensed table-bordered" id="table_mod_insertCmdValue_valueEqLogicToMessage">
    <thead>
        <tr>
            <th style="width: 150px;">Object</th>
            <th style="width: 150px;">Equipement</th>
            <th style="width: 150px;">Commande</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="mod_insertCmdValue_object">
                <select class='form-control'>
                    <option value="-1">Aucun</option>
                    <?php
                    foreach (object::all() as $object)
                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                    ?>
                </select>
            </td>
            <td class="mod_insertCmdValue_eqLogic"></td>
            <td class="mod_insertCmdValue_cmd"></td>
        </tr>
    </tbody>
</table> 
<script>
    function mod_insertCmd() {
    }
    mod_insertCmd.typeCmd = 'all';
    $("#table_mod_insertCmdValue_valueEqLogicToMessage").delegate("td.mod_insertCmdValue_object select", 'change', function() {
        mod_insertCmd.changeObjectCmd($('#table_mod_insertCmdValue_valueEqLogicToMessage td.mod_insertCmdValue_object select'), mod_insertCmd.typeCmd);
    });
    mod_insertCmd.setTypeCmd = function(_typeCmd) {
        mod_insertCmd.typeCmd = _typeCmd;
        mod_insertCmd.changeObjectCmd($('#table_mod_insertCmdValue_valueEqLogicToMessage td.mod_insertCmdValue_object select'), mod_insertCmd.typeCmd);
    }

    mod_insertCmd.getValue = function() {
        var object_name = $('#table_mod_insertCmdValue_valueEqLogicToMessage tbody tr:first .mod_insertCmdValue_object select option:selected').html();
        var equipement_name = $('#table_mod_insertCmdValue_valueEqLogicToMessage tbody tr:first .mod_insertCmdValue_eqLogic select option:selected').html();
        var cmd_name = $('#table_mod_insertCmdValue_valueEqLogicToMessage tbody tr:first .mod_insertCmdValue_cmd select option:selected').html();
        if (cmd_name == undefined) {
            return '';
        }
        return '#[' + object_name + '][' + equipement_name + '][' + cmd_name + ']#';
    }

    mod_insertCmd.changeObjectCmd = function(_select, _typeCmd) {
        var eqLogics = object.getEqLogic(_select.value(), _typeCmd);
        _select.closest('tr').find('.mod_insertCmdValue_eqLogic').empty();
        var selectEqLogic = '<select class="form-control">';
        for (var i in eqLogics) {
            selectEqLogic += '<option value="' + eqLogics[i].id + '">' + eqLogics[i].name + '</option>';
        }
        selectEqLogic += '</select>';
        _select.closest('tr').find('.mod_insertCmdValue_eqLogic').append(selectEqLogic);
        _select.closest('tr').find('.mod_insertCmdValue_eqLogic select').change(function() {
            mod_insertCmd.changeEqLogic($(this), _typeCmd);
        });
        mod_insertCmd.changeEqLogic(_select.closest('tr').find('.mod_insertCmdValue_eqLogic select'), _typeCmd);
    }

    mod_insertCmd.changeEqLogic = function(_select, _typeCmd) {
        _select.closest('tr').find('.mod_insertCmdValue_cmd').empty();
        _typeCmd = (_typeCmd == 'all') ? '' : _typeCmd;
        var selectCmd = '<select class="form-control">';
        selectCmd += eqLogic.builSelectCmd(_select.value(), _typeCmd);
        selectCmd += '</select>';
        _select.closest('tr').find('.mod_insertCmdValue_cmd').append(selectCmd);
    }
</script>
