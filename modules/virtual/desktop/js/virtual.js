
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
            var calcul = el.closest('tr').find('.cmdAttr[l1key=configuration][l2key=calcul]');
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
        var tr = '<tr class="cmd" cmd_id="' + init(_cmd.id) + '" virtualAction="' + init(_cmd.configuration.virtualAction) + '">';
        tr += '<td>';
        tr += '<span class="cmdAttr" l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control" l1key="name" style="width : 140px;" placeholder="Nom"></td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type" l1key="type" value="info" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
        tr += '<td><textarea class="cmdAttr form-control" l1key="configuration" l2key="calcul" style="height : 33px;" ' + disabled + ' placeholder="Calcul"></textarea>';
        tr += '<a class="form-control btn btn-default cursor listEquipementInfo" style="margin-top : 5px;"><i class="fa fa-list-alt "></i> Rechercher équipement</a></td>';
        tr += '<td><input class="cmdAttr form-control" l1key="unite" style="width : 90px;" placeholder="Unite"></td>';
        tr += '<td>';
        tr += '<span><input type="checkbox" class="cmdAttr" l1key="isHistorized"/> Historiser<br/></span>';
        tr += '<span><input type="checkbox" class="cmdAttr" l1key="isVisible" checked/> Afficher<br/></span>';
        tr += '<span><input type="checkbox" class="cmdAttr" l1key="eventOnly"' + disabled + '/> Evenement seulement<br/></span>';
        tr += '<input style="width : 150px;" class="tooltips cmdAttr form-control" l1key="cache" l2key="lifetime" placeholder="Lifetime cache" title="Lifetime cache">';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" action="test"><i class="fa fa-rss"></i> Tester</a>';
        }
        tr += '<i class="fa fa-minus-circle pull-right cmdAction" action="remove"></i></td>';
        tr += '</tr>';
        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    }

    if (init(_cmd.type) == 'action') {
        var tr = '<tr class="cmd" cmd_id="' + init(_cmd.id) + '">';
        tr += '<td>';
        tr += '<span class="cmdAttr" l1key="id"></span>';
        tr += '</td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control" l1key="name" style="width : 140px;" placeholder="Nom"></td>';
        tr += '<td>';
        tr += '<input class="cmdAttr form-control type" l1key="type" value="action" disabled style="margin-bottom : 5px;" />';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '<input class="cmdAttr" l1key="configuration" l2key="virtualAction" value="1" style="display:none;" >';
        tr += '</td>';
        tr += '<td><input class="cmdAttr form-control" l1key="configuration" l2key="infoName" placeholder="Nom information" style="margin-bottom : 5px;">';
        tr += '<input class="cmdAttr form-control" l1key="configuration" l2key="value"placeholder="Valeur"></td>';
        tr += '<td></td>';
        tr += '<td>';
        tr += '<span><input type="checkbox" class="cmdAttr" l1key="isVisible" checked/> Afficher<br/></span>';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" action="test"><i class="fa fa-rss"></i> Tester</a>';
        }
        tr += '<i class="fa fa-minus-circle pull-right cmdAction" action="remove"></i></td>';
        tr += '</tr>';

        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    }

}


function changeObjectCmd(_select, _typeCmd, _eqLogic_id, _cmd_id, _option) {
    var object_id = _select.value();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "listByObjectAndCmdType",
            object_id: object_id,
            typeCmd: _typeCmd
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

            _select.parent().next('.eqLogic').empty();
            var selecteqLogic = '<select class="form-control">';
            for (var i in data.result) {
                selecteqLogic += '<option value="' + data.result[i].id + '">' + data.result[i].name + '</option>';
            }
            selecteqLogic += '</select>';
            _select.parent().next('.eqLogic').append(selecteqLogic);
            _select.parent().next('.eqLogic').find('select').change(function() {
                changeEqLogic($(this), _typeCmd);
            });

            if (isset(_eqLogic_id)) {
                _select.parent().next('.eqLogic').find('select').value(_eqLogic_id);
            }
            changeEqLogic(_select.parent().next('.eqLogic').find('select'), _typeCmd, _cmd_id, _option);
        }
    });
}

function changeEqLogic(_select, _typeCmd, _cmd_id, _option) {
    var eqLogic_id = _select.value();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "byEqLogic",
            eqLogic_id: eqLogic_id
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
            _select.closest('td').next('.cmd').empty();
            var selectCmd = '<select class="cmd_value form-control">';
            for (var i in data.result) {
                if (data.result[i].type == _typeCmd) {
                    selectCmd += '<option value="' + data.result[i].id + '" subtype="' + data.result[i].subType + '">' + data.result[i].name + '</option>';
                }
            }
            selectCmd += '</select>';
            _select.closest('td').next('.cmd').append(selectCmd);
            if (isset(_cmd_id)) {
                _select.closest('td').next('.cmd').find('select').value(_cmd_id);
            }

            if (_typeCmd == 'action') {
                _select.closest('td').next('.cmd').find('select').change(function() {
                    changeCmd($(this));
                });
                changeCmd(_select.closest('td').next('.cmd').find('select'), _option);
                _select.closest('td').next('.cmd').find('select').addClass('scenarioActionAttr').attr('key', 'cmd_id');
            }
            if (_typeCmd == 'info') {
                _select.closest('td').next('.cmd').find('select').addClass('scenarioConditionAttr').attr('key', 'cmd_id' + _select.closest('td').next('.cmd').attr('number'));
            }
        }
    });
}

function changeCmd(_select, _option) {
    _select.closest('tr').find('.option').empty();

    switch (_select.find('option:selected').attr('subtype')) {
        case "slider" :
            if (isset(_option)) {
                var input = '<input class="form-control" value="' + _option + '" />';
            } else {
                var input = '<input class="form-control" />';
            }
            _select.closest('tr').find('.option').append(input);
            break;
        case "option" :
            if (isset(_option)) {
                var input = '<input class="form-control" value="' + _option + '" />';
            } else {
                var input = '<input class="form-control" />';
            }
            _select.closest('tr').find('.option').append(input);
            break;
        case "color" :
            if (isset(_option)) {
                var input = '<center><input class="form-control" value="' + _option + '" /><div class="colorpicker" style="display : none"></div></center>';
            } else {
                var input = '<center><input class="form-control" name="color" value="#123456"/><div class="colorpicker" style="display : none"></div></center>';
            }
            _select.closest('tr').find('.option').append(input);
            var input = _select.closest('tr').find('.option input');
            var div = _select.closest('tr').find('.option .colorpicker');
            div.farbtastic(input);

            input.focus(function() {
                if (!$(this).parent().find('.colorpicker').is(':visible')) {
                    $(this).parent().find('.colorpicker').show();
                }
            });

            input.blur(function() {
                if ($(this).parent().find('.colorpicker').is(':visible')) {
                    $(this).parent().find('.colorpicker').hide();
                }
            });

            break;

        case "message" :
            var option = json_decode(_option);
            var td = '<input class="form-control" class="title" placeholder="Title" style="width : 88%" value="';
            td += init(option.title);
            td += '"/><br>';
            td += '<textarea class="message" style="margin-top : 5px;width : 85%" placeholder="Message">';
            if (init(option.message) != '') {
                var cmdId = option.message.match(/#(.*?)#/g);
                for (var i in cmdId) {
                    cmdId[i] = cmdId[i].replace(/#/gi, '');
                    $.ajax({// fonction permettant de faire de l'ajax
                        type: "POST", // methode de transmission des données au fichier php
                        url: "core/ajax/cmd.ajax.php", // url du fichier php
                        data: {
                            action: "getCmd",
                            id: cmdId[i]
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
                            if (data != '-1') {
                                option.message = option.message.replace('#' + cmdId[i] + '#', '#[' + data.result.eqType_name + '].[' + data.result.eqLogic_name + '].[' + data.result.name + ']#');
                            }
                        }
                    });
                }
            }
            td += init(option.message);
            td += '</textarea>';
            td += '<i class="fa fa-list-alt listEquipementInfo cursor pull-right" style="margin-top : 5px;">';
            _select.closest('tr').find('.option').append(td);
            break;
    }
}

