
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
    $(".li_macro").on('click', function(event) {
        $.hideAlert();
        //resetAllValue();
        $('#div_conf').show();
        $('.li_macro').removeClass('active');
        $(this).addClass('active');
        printMacro($(this).attr('macroEq_id'));
        return false;
    });

    $("#bt_addMacro").on('click', function(event) {
        $.hideAlert();
        $('#in_addMacroName').value('');
        $('#md_addMacro').modal('show');
        return false;
    });

    $("#bt_addMacroSave").on('click', function(event) {
        addMacro();
        return false;
    });

    $("#bt_addCommand").on('click', function(event) {
        $('#in_addCmdToMacroName').value('');
        $('#sel_addMacroType').value('action');
        $('#md_addCmdToMacro').modal('show');
        return false;
    });

    $("#bt_addCmdToMacroSave").on('click', function(event) {
        addCmdToMacro();
        return false;
    });

    $("#bt_saveMacro").on('click', function(event) {
        saveMacro();
        return false;
    });

    $("#bt_removeMacro").on('click', function(event) {
        if ($('.li_macro.active').attr('macroEq_id') != undefined) {
            $.hideAlert();
            bootbox.confirm('Etez-vous sûr de vouloir supprimer la macro <span style="font-weight: bold ;">' + $('.li_macro.active').attr('name') + '</span> ?', function(result) {
                if (result) {
                    removeMacro($('.li_macro.active').attr('macroEq_id'));
                }
            });
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner une macro', level: 'danger'});
        }
        return false;
    });

    $("#div_cmdOfMacro").delegate(".addCmdLineToMacroCmd", 'click', function(event) {
        addCmdLineToMacroCmd($(this).parent().find('table tbody'));
        return false;
    });

    $("#div_cmdOfMacro").delegate(".removeTabMacro", 'click', function(event) {
        removeTabMacro($(this).parent().attr('cmd_id'));
        return false;
    });

    $("#div_cmdOfMacro").delegate(".remove", 'click', function(event) {
        $(this).closest('tr').remove();
    });


    if (select_id != -1) {
        if ($('#ul_macro .li_macro[macroEq_id=' + select_id + ']').length != 0) {
            $('#ul_macro .li_macro[macroEq_id=' + select_id + ']').click();
        } else {
            $('#ul_macro .li_macro:first').click();
        }
    } else {
        $('#ul_macro .li_macro:first').click();
    }
});

function removeMacro(_macroEq_id) {
    $.hideAlert();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "removeMacro",
            id: _macroEq_id
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
            window.location.replace('index.php?v=d&m=macro&p=index');
        }
    });
}

function printMacro(_macroEq_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "getMacro",
            macroEq_id: _macroEq_id
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
            emptyTabCommand();
            $('#in_name').value(data.result.name);
            $('#sel_object').value(data.result.object_id);
            $('#in_visible').prop('checked', (data.result.isVisible == 1) ? true : false);
            $('#in_enable').prop('checked', (data.result.isEnable == 1) ? true : false);

            for (var i in data.result.cmd) {
                addTabCommand(data.result.cmd[i].id, data.result.cmd[i].name, data.result.cmd[i].subType);
                for (var j in data.result.cmd[i].cmdToExecute) {
                    var eqType_name = data.result.cmd[i].cmdToExecute[j].eqType_name;
                    var eqLogic_id = data.result.cmd[i].cmdToExecute[j].eqLogic_id;
                    var execute_command_id = data.result.cmd[i].cmdToExecute[j].execute_command_id;
                    var option = data.result.cmd[i].cmdToExecute[j].option;
                    addCmdLineToMacroCmd($('#cmd' + data.result.cmd[i].id + ' table tbody'), eqType_name, eqLogic_id, execute_command_id, option);
                }
            }
        }
    });

}


function addCmdToMacro() {
    $.hideAlert();
    try {
        var name = $('#in_addCmdToMacroName').value();
        if (name == '') {
            throw('Le nom de la commande ne peut être vide');
        }
        var macroEq_id = $('.li_macro.active').attr('macroEq_id');
    } catch (e) {
        $('#div_alert').showAlert({message: e, level: 'danger'});
        return false;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "addCmdToMacro",
            name: name,
            macroEq_id: macroEq_id,
            type: $('#sel_addMacroType').value()
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_addCmdToMacroAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_addCmdToMacroAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (isNaN(data.result)) {
                $('#div_addCmdToMacroAlert').showAlert({message: data.result, level: 'danger'});
            } else {
                addTabCommand(data.result, name, $('#sel_addMacroType').value());
                $('#md_addCmdToMacro').modal('hide');
                $('#ul_cmdOfMacro a:last').click();
            }
        }
    });
}

function addMacro() {
    $.hideAlert();
    var name = $('#in_addMacroName').value();
    if (name == '') {
        $('#div_addMacroAlert').showAlert({message: 'Le nom de la macro ne peut être vide', level: 'danger'});
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "addMacro",
            name: name
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_addMacroAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_addMacroAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.replace('index.php?v=d&m=macro&p=index&id=' + data.result.id);
        }
    });
}

function addTabCommand(_id, _name, _subType) {
    if (_subType == 'other') {
        var subType = 'Action';
    }
    if (_subType == 'slider') {
        var subType = 'Slider';
    }

    if (_subType == 'color') {
        var subType = 'Color';
    }

    var li = '<li><a href="#cmd' + _id + '" data-toggle="tab">' + _name + '</a></li>';
    $('#ul_cmdOfMacro').append(li);
    var tab = '<div class="tab-pane" id="cmd' + _id + '" cmd_id="' + _id + '">';
    tab += '<br/>Type : <span class="label label-info typeCmd" subtype="' + _subType + '">' + subType + '</span>';
    tab += '<a class="removeTabMacro pull-right btn btn-danger btn-sm"><i class="fa fa-minus-circle"></i> Supprimer la commande</a>';
    tab += '<br/>';
    tab += '<br/>';
    tab += '<a class="addCmdLineToMacroCmd btn btn-success btn-sm"><i class="fa fa-plus-circle"></i> Ajouter une commande</a>';
    tab += '<br/>';
    tab += '<br/>';
    tab += '<table class="table table-condensed table-bordered">';
    tab += '<table class="table table-condensed table-bordered">';
    tab += '<thead>';
    tab += '<tr>';
    tab += '<th>Type</th><th>Nom équipement</th><th>Commande</th><th>Option</th><th></th>';
    tab += '</tr>';
    tab += '</thead>';
    tab += '<tbody></tbody>';
    tab += '</table>';
    tab += '</div>';
    $('#div_cmdOfMacro').append(tab);

    $('#ul_cmdOfMacro a:first').tab('show');
}

function removeTabMacro(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "removeCmdToMacro",
            id: _id
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
            $('#cmd' + _id).remove();
            $('a[href="#cmd' + _id + '"]').parent().remove();
            $('#ul_cmdOfMacro a:first').click();
        }
    });
}

function emptyTabCommand() {
    $('#ul_cmdOfMacro').empty();
    $('#div_cmdOfMacro').empty();
}


function addCmdLineToMacroCmd(_tbody, _eqType_name, _eqLogic_id, _execute_command_id, _option) {

    var line = '<tr>';
    line += '<td class="type">';
    line += sel_type;
    line += '</td>';
    line += '<td class="equipement">';
    line += '</td>';
    line += '<td class="cmd">';
    line += '</td>';
    line += '<td class="option">';
    line += '</td>';
    line += '<td>';
    line += '<i class="fa fa-minus remove"></i>';
    line += '</td>';
    line += '</tr>';
    _tbody.append(line);

    _tbody.find('tr:last .type select').unbind().undelegate();
    _tbody.find('tr:last .type select').change(function() {
        changeType($(this));
    });

    if (isset(_eqType_name)) {
        _tbody.find('tr:last .type select').value(_eqType_name);
    }
    changeType(_tbody.find('tr:last .type select'), _eqLogic_id, _execute_command_id, _option);
}


function changeType(_select, _eqLogic_id, _execute_command_id, _option) {
    var type = _select.value();
    var subTypeCmd = _select.closest('tr').find('.typeCmd').attr('subtype');
    if (subTypeCmd == 'other') {
        subTypeCmd = '';
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "listByTypeAndCmdType",
            type: type,
            typeCmd: 'action',
            subTypeCmd: subTypeCmd
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
            _select.closest('tr').find('.equipement').empty();
            var selecteqLogic = '<select class="form-control">';
            for (var i in data.result) {
                selecteqLogic += '<option value="' + data.result[i].eqLogic.id + '">[' + data.result[i].object.name + '] ' + data.result[i].eqLogic.name + '</option>';
            }
            selecteqLogic += '</select>';
            _select.closest('tr').find('.equipement').append(selecteqLogic);
            _select.closest('tr').find('.equipement select').unbind().undelegate();
            _select.closest('tr').find('.equipement select').change(function() {
                changeeqLogic($(this));
            });

            if (isset(_eqLogic_id)) {
                _select.closest('tr').find('.equipement select').value(_eqLogic_id);
            }
            changeeqLogic(_select.closest('tr').find('.equipement select'), _execute_command_id, _option);
        }
    });
}


function changeeqLogic(_select, _execute_command_id, _option) {
    var eqLogic_id = _select.value();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "byEqLogic",
            eqLogic_id: eqLogic_id
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
            var subTypeCmd = _select.closest('tr').find('.typeCmd').attr('subtype');
            _select.closest('tr').find('.cmd').empty();
            var selectCmd = '<select class="form-control">';
            for (var i in data.result) {
                if (data.result[i].type == 'action') {
                    if (subTypeCmd == 'slider' || subTypeCmd == 'color') {
                        if (data.result[i].subType == subTypeCmd) {
                            selectCmd += '<option value="' + data.result[i].id + '" subtype="' + data.result[i].subType + '">' + data.result[i].name + '</option>';
                        }
                    } else {
                        selectCmd += '<option value="' + data.result[i].id + '" subtype="' + data.result[i].subType + '">' + data.result[i].name + '</option>';
                    }

                }
            }
            selectCmd += '</select>';
            _select.closest('tr').find('.cmd').append(selectCmd);
            if (isset(_execute_command_id)) {
                _select.closest('tr').find('.cmd select').value(_execute_command_id);
            }

            if (subTypeCmd != 'slider' && subTypeCmd != 'color') {
                _select.closest('tr').find('.cmd select').unbind().undelegate();
                _select.closest('tr').find('.cmd select').change(function() {
                    changeCmd($(this));
                });
                changeCmd(_select.closest('tr').find('.cmd select'), _option);
            }
        }
    });
}

function changeCmd(_select, _option) {
    _select.closest('tr').find('.option').empty();
    var cmd_id = _select.find('option:selected').value();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: 'actionToHtml',
            version: 'scenario',
            id: cmd_id,
            option: _option
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
            _select.closest('tr').find('.option').append(data.result);
        }
    });
}


function saveMacro() {
    $.hideAlert();
    var name = $('#in_name').value();
    if (name == '') {
        $('#div_alert').showAlert({message: 'Le nom de la macro ne peut vide', level: 'danger'});
        return;
    }
    var isVisible = $('#in_visible').value();
    var isEnable = $('#in_enable').value();

    var list_div_tab = $('.tab-pane');
    var cmd = new Array();
    for (i = 0; i < list_div_tab.length; i++) {
        var tab = new Object();
        var div_tab = list_div_tab.eq(i);
        tab['id'] = div_tab.attr('cmd_id');
        tab['cmd_execute'] = new Array();
        var tr = div_tab.find('table tbody tr:first');
        while (tr.html() != undefined) {
            var cmd_execute = new Object();
            cmd_execute['cmd_id'] = tr.find('.cmd select').value();
            var option = new Object();
            tr.find('.action_option').each(function() {
                if ($(this).attr('type') == 'checkbox') {
                    option[$(this).attr('key')] = $(this).value();
                } else {
                    var value = $(this).value();
                    var insertValue = value.match(/#(.*?)#/g);
                    for (var i in insertValue) {
                        var insertValuePara = insertValue[i].match(/\[(.*?)\]/g);
                        if (insertValuePara != null && insertValuePara.length == 3) {
                            var type = insertValuePara[0].replace('[', '').replace(']', '');
                            var eqLogic_name = insertValuePara[1].replace('[', '').replace(']', '');
                            var cmd_name = insertValuePara[2].replace('[', '').replace(']', '');
                            var insertValueCmdId = cmdIdByTypeEqLogicNameCmdName(type, eqLogic_name, cmd_name);
                            if (insertValueCmdId !== false) {
                                value = value.replace(insertValue[i], '#' + insertValueCmdId + '#');
                            }
                        }
                    }
                    option[$(this).attr('key')] = value;
                }
            });
            cmd_execute['option'] = json_encode(option);
            tab['cmd_execute'].push(cmd_execute);
            tr = tr.next();
        }
        cmd.push(tab);
    }

    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "modules/macro/core/ajax/macro.ajax.php", // url du fichier php
        data: {
            action: "saveMacro",
            id: $('.li_macro.active').attr('macroEq_id'),
            name: name,
            object_id: $('#sel_object').value(),
            isVisible: isVisible,
            isEnable: isEnable,
            cmd: json_encode(cmd)
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
            $('#div_alert').showAlert({message: 'Macro sauvegardé', level: 'success'});
        }
    });
}