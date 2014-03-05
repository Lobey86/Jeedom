
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


function eqLogic() {
}

eqLogic.cache = Array();

eqLogic.save = function(_type, _eqLogics) {
    $.hideAlert();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "save",
            type: _type,
            eqLogic: json_encode(_eqLogics),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                if ($('#md_addEqLogic').is(':visible')) {
                    $('#div_addEqLogicAlert').showAlert({message: data.result, level: 'danger'});
                } else {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                }
                return;
            }
            var vars = getUrlVars();
            var url = 'index.php?';
            for (var i in vars) {
                if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
                    url += i + '=' + vars[i].replace('#', '') + '&';
                }
            }
            modifyWithoutSave = false;
            url += 'id=' + data.result.id + '&saveSuccessFull=1';
            window.location.href = url;
        }
    });
}

eqLogic.remove = function(_type, _eqLogic_Id) {
    $.hideAlert();
    if (!isset(_eqLogic_Id)) {
        _eqLogic_Id = $('.li_eqLogic.active').attr('data-eqLogic_id');
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "remove",
            type: _type,
            id: _eqLogic_Id
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
            var vars = getUrlVars();
            var url = 'index.php?';
            for (var i in vars) {
                if (i != 'id' && i != 'removeSuccessFull' && i != 'saveSuccessFull') {
                    url += i + '=' + vars[i].replace('#', '') + '&';
                }
            }
            modifyWithoutSave = false;
            url += 'removeSuccessFull=1';
            window.location.href = url;
        }
    });
}

eqLogic.print = function(_type, _eqLogic_id) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "get",
            type: _type,
            id: _eqLogic_id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('body .eqLogicAttr').value('');
            $('body').setValues(data.result, '.eqLogicAttr');

            if ('function' == typeof(printEqLogic)) {
                printEqLogic(data.result);
            }

            if ('function' == typeof(addCmdToTable)) {
                $('.cmd').remove();
                for (var i in data.result.cmd) {
                    addCmdToTable(data.result.cmd[i]);
                    if ($('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').value() == 'slider' || $('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').value() == 'color') {
                        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=value]').show();
                    }
                }
            }
            activateTooltips();
            initExpertMode();
            $.hideLoading();
            modifyWithoutSave = false;
        }
    });
}

eqLogic.getCmd = function(_eqLogic_id) {
    if (!isset(eqLogic.cache.getCmd)) {
        eqLogic.cache.getCmd = Array();
    }
    if (isset(eqLogic.cache.getCmd[_eqLogic_id])) {
        return eqLogic.cache.getCmd[_eqLogic_id];
    }
    var result = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "byEqLogic",
            eqLogic_id: _eqLogic_id
        },
        dataType: 'json',
        async: false,
        cache: true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            result = data.result;
        }
    });
    eqLogic.cache.getCmd[_eqLogic_id] = result;
    return result;
}

eqLogic.builSelectCmd = function(_eqLogic_id, _filter) {
    if (!isset(_filter)) {
        _filter = {};
    }
    var cmds = eqLogic.getCmd(_eqLogic_id);
    var result = '';
    for (var i in cmds) {
        if ((init(_filter.type, 'all') == 'all' || cmds[i].type == _filter.type) &&
                (init(_filter.subtype, 'all') == 'all' || cmds[i].subType == _filter.subtype)) {
            result += '<option value="' + cmds[i].id + '" >' + cmds[i].name + '</option>';
        }
    }
    return result;
}
