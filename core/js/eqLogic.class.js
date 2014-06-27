
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


jeedom.eqLogic = function() {
};

jeedom.eqLogic.cache = Array();

if (!isset(jeedom.eqLogic.cache.getCmd)) {
    jeedom.eqLogic.cache.getCmd = Array();
}

if (!isset(jeedom.eqLogic.cache.byId)) {
    jeedom.eqLogic.cache.byId = Array();
}

jeedom.eqLogic.save = function(_params) {
    $.hideAlert();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "save",
            type: _params.type,
            eqLogic: json_encode(_params.eqLogics),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if (isset(jeedom.eqLogic.cache.byId[data.result.id])) {
                delete jeedom.eqLogic.cache.byId[data.result.id];
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.eqLogic.remove = function(_params) {
    $.hideAlert();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "remove",
            type: _params.type,
            id: _params.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if (isset(jeedom.eqLogic.cache.byId[_params.eqLogic_Id])) {
                delete jeedom.eqLogic.cache.byId[_params.eqLogic_Id];
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.eqLogic.print = function(_params) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "get",
            type: _params.type,
            id: _params.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.eqLogic.toHtml = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: _params.id,
            version: _params.version
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}

jeedom.eqLogic.getCmd = function(_params) {
    if (isset(jeedom.eqLogic.cache.getCmd[_params.eqLogic_id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.eqLogic.cache.getCmd[_params.eqLogic_id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "byEqLogic",
            eqLogic_id: _params.id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            jeedom.eqLogic.cache.getCmd[_params.eqLogic_id] = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.eqLogic.cache.getCmd[_params.eqLogic_id]);
            }
        }
    });
}


jeedom.eqLogic.byId = function(_params) {
    if (isset(jeedom.eqLogic.cache.byId[_params.eqLogic_id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.eqLogic.cache.byId[_params.eqLogic_id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _params.id
        },
        dataType: 'json',
        cache: true,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                _params.error({message: data.result, code: 0});
                return;
            }
            jeedom.eqLogic.cache.byId[_params.eqLogic_id] = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.eqLogic.cache.byId[_params.eqLogic_id]);
            }
        }
    });
}

jeedom.eqLogic.builSelectCmd = function(_params) {
    if (!isset(_params.filter)) {
        _params.filter = {};
    }
    jeedom.eqLogic.getCmd({
        id: _params.id,
        success: function(cmds) {
            var result = '';
            for (var i in cmds) {
                if ((init(_params.filter.type, 'all') == 'all' || cmds[i].type == _params.filter.type) &&
                        (init(_params.filter.subtype, 'all') == 'all' || cmds[i].subType == _params.filter.subtype)) {
                    result += '<option value="' + cmds[i].id + '" >' + cmds[i].name + '</option>';
                }
            }
            if ('function' == typeof (_params.success)) {
                _params.success(result);
            }
        }
    });
}

jeedom.eqLogic.getSelectModal = function(_options, callback) {
    if (!isset(_options)) {
        _options = {};
    }
    if ($("#mod_insertEqLogicValue").length == 0) {
        $('body').append('<div id="mod_insertEqLogicValue" title="{{Sélectionner la commande}}" ></div>');

        $("#mod_insertEqLogicValue").dialog({
            autoOpen: false,
            modal: true,
            height: 250,
            width: 800
        });
        jQuery.ajaxSetup({async: false});
        $('#mod_insertEqLogicValue').load('index.php?v=d&modal=eqLogic.human.insert');
        jQuery.ajaxSetup({async: true});
    }
    mod_insertEqLogic.setOptions(_options);
    $("#mod_insertEqLogicValue").dialog('option', 'buttons', {
        "Annuler": function() {
            $(this).dialog("close");
        },
        "Valider": function() {
            var retour = {};
            retour.human = mod_insertEqLogic.getValue();
            if ($.trim(retour) != '') {
                callback(retour);
            }
            $(this).dialog('close');
        }
    });
    $('#mod_insertEqLogicValue').dialog('open');
};