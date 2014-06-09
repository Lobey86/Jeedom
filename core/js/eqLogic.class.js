
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

jeedom.eqLogic.save = function(_type, _eqLogics, _callback) {
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
            if (isset(jeedom.eqLogic.cache.byId[data.result.id])) {
                delete jeedom.eqLogic.cache.byId[data.result.id];
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.eqLogic.remove = function(_type, _eqLogic_Id, _callback) {
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
            if (isset(jeedom.eqLogic.cache.byId[_eqLogic_Id])) {
                delete jeedom.eqLogic.cache.byId[_eqLogic_Id];
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.eqLogic.print = function(_type, _eqLogic_id, _callback) {
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.eqLogic.toHtml = function(_id, _version, _callback) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "toHtml",
            id: _id,
            version: _version
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
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.eqLogic.getCmd = function(_eqLogic_id) {
    if (isset(jeedom.eqLogic.cache.getCmd[_eqLogic_id])) {
        return jeedom.eqLogic.cache.getCmd[_eqLogic_id];
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
    jeedom.eqLogic.cache.getCmd[_eqLogic_id] = result;
    return result;
}


jeedom.eqLogic.byId = function(_eqLogic_id, _callback) {
    if (isset(jeedom.eqLogic.cache.byId[_eqLogic_id])) {
        if ('function' == typeof (_callback)) {
            _callback(jeedom.eqLogic.cache.byId[_eqLogic_id]);
        }
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/eqLogic.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _eqLogic_id
        },
        dataType: 'json',
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
            jeedom.eqLogic.cache.byId[_eqLogic_id] = data.result;
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
}

jeedom.eqLogic.builSelectCmd = function(_eqLogic_id, _filter) {
    if (!isset(_filter)) {
        _filter = {};
    }
    var cmds = jeedom.eqLogic.getCmd(_eqLogic_id);
    var result = '';
    for (var i in cmds) {
        if ((init(_filter.type, 'all') == 'all' || cmds[i].type == _filter.type) &&
                (init(_filter.subtype, 'all') == 'all' || cmds[i].subType == _filter.subtype)) {
            result += '<option value="' + cmds[i].id + '" >' + cmds[i].name + '</option>';
        }
    }
    return result;
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