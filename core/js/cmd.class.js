
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

jeedom.cmd = function() {
};

jeedom.cmd.cache = Array();

if (!isset(jeedom.cmd.cache.byId)) {
    jeedom.cmd.cache.byId = Array();
}

jeedom.cmd.execute = function(_params) {
    var eqLogic = $('.cmd[data-cmd_id=' + _params.id + ']').closest('.eqLogic');
    eqLogic.find('.statusCmd').empty().append('<i class="fa fa-spinner fa-spin"></i>');
    if (init(_params.value) != '' && (is_array(_params.value) || is_object(_params.value))) {
        _params.value = json_encode(_params.value);
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "execCmd",
            id: _params.id,
            cache: init(_params.cache, 1),
            value: _params.value || ''
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                if ('function' == typeof (_params.error)) {
                    _params.success(data.error);
                } else {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                }
                eqLogic.find('.statusCmd').empty().append('<i class="fa fa-times"></i>');
                setTimeout(function() {
                    eqLogic.find('.statusCmd').empty();
                }, 3000);
                return;
            }
            if (init(_params.notify, true)) {
                eqLogic.find('.statusCmd').empty().append('<i class="fa fa-rss"></i>');
                setTimeout(function() {
                    eqLogic.find('.statusCmd').empty();
                }, 3000);
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
};


jeedom.cmd.test = function(_params) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "getCmd",
            id: _params.id,
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
            var result = data.result;
            switch (result.type) {
                case 'info' :
                    jeedom.cmd.execute({
                        id: _params.id,
                        cache: 0,
                        notify: false,
                        success: function(result) {
                            alert(result);
                        }
                    });
                    break;
                case 'action' :
                    switch (result.subType) {
                        case 'other' :
                            jeedom.cmd.execute({id: _params.id, cache: 0});
                            break;
                        case 'slider' :
                            jeedom.cmd.execute({id: _params.id, value: {slider: 50}, cache: 0});
                            break;
                        case 'color' :
                            jeedom.cmd.execute({id: _params.id, value: {color: '#fff000'}, cache: 0});
                            break;
                        case 'message' :
                            jeedom.cmd.execute({id: _params.id, value: {title: '{{[Jeedom] Message de test}}', message: '{{Ceci est un test de message pour la commande}} ' + result.name}, cache: 0});
                            break;
                    }
                    break;
            }
        }
    });
};


jeedom.cmd.refreshValue = function(_params) {
    var cmd = $('.cmd[data-cmd_id=' + _params.id + ']');
    if (cmd.html() != undefined && cmd.closest('.eqLogic').attr('data-version') != undefined) {
        var version = cmd.closest('.eqLogic').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/cmd.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _params.id,
                version: _params.version || version,
            },
            dataType: 'json',
            cache: true,
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                cmd.replaceWith(data.result.html);
                initTooltips();
                if ($.mobile) {
                    $('.cmd[data-cmd_id=' + _params.id + ']').trigger("create");
                } else {
                    positionEqLogic($('.cmd[data-cmd_id=' + _params.id + ']').closest('.eqLogic').attr('data-eqLogic_id'), true);
                }
            }
        });
    }
};


jeedom.cmd.save = function(_params) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "save",
            cmd: json_encode(_params.cmd)
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (isset(jeedom.cmd.cache.byId[data.result.id])) {
                delete jeedom.cmd.cache.byId[data.result.id];
            }
            if ('function' == typeof (_params.success)) {
                _params.success(data.result);
            }
        }
    });
}


jeedom.cmd.byId = function(_params) {
    if (isset(jeedom.cmd.cache.byId[_params.id]) && 'function' == typeof (_params.success)) {
        _params.success(jeedom.cmd.cache.byId[_params.id]);
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
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
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            jeedom.cmd.cache.byId[_params.id] = data.result;
            if ('function' == typeof (_params.success)) {
                _params.success(jeedom.cmd.cache.byId[_params.id]);
            }
        }
    });
}



jeedom.cmd.changeType = function(_cmd, _subType) {
    var selSubType = '<select style="width : 120px;margin-top : 5px;" class="cmdAttr form-control input-sm" data-l1key="subType">';
    var type = _cmd.find('.cmdAttr[data-l1key=type]').value();
    jeedom.getConfiguration({
        key: 'cmd:type:' + type + ':subtype',
        default: 0,
        error: function(error) {
            _params.error(error);
        },
        success: function(subType) {
            for (var i in subType) {
                selSubType += '<option value="' + i + '">' + subType[i].name + '</option>';
            }
            selSubType += '</select>';
            _cmd.find('.subType').empty();
            _cmd.find('.subType').append(selSubType);
            if (isset(_subType)) {
                _cmd.find('.cmdAttr[data-l1key=subType]').value(_subType);
                modifyWithoutSave = false;
            }
            jeedom.cmd.changeSubType(_cmd);
            if ('function' == typeof (initExpertMode)) {
                initExpertMode();
            }
        }
    });
};

jeedom.cmd.changeSubType = function(_cmd) {
    jeedom.getConfiguration({
        key: 'cmd:type:' + _cmd.find('.cmdAttr[data-l1key=type]').value() + ':subtype:' + _cmd.find('.cmdAttr[data-l1key=subType]').value(),
        default: 0,
        error: function(error) {
            _params.error(error);
        },
        success: function(subtype) {
            for (var i in subtype) {
                if (isset(subtype[i].visible)) {
                    var el = _cmd.find('.cmdAttr[data-l1key=' + i + ']');
                    if (el.attr('type') == 'checkbox' && el.parent().is('span')) {
                        el = el.parent();
                    }
                    if (subtype[i].visible) {
                        el.show();
                        el.removeClass('hide');
                    } else {
                        el.hide();
                        el.addClass('hide');
                    }
                } else {
                    for (var j in subtype[i]) {
                        var el = _cmd.find('.cmdAttr[data-l1key=' + i + '][data-l2key=' + j + ']');
                        if (el.attr('type') == 'checkbox' && el.parent().is('span')) {
                            el = el.parent();
                        }
                        if (isset(subtype[i][j].visible)) {
                            if (subtype[i][j].visible) {
                                el.show();
                                el.removeClass('hide');
                            } else {
                                el.hide();
                                el.addClass('hide');
                            }
                        }
                    }
                }
            }
            if (_cmd.find('.cmdAttr[data-l1key=subType]').value() == 'slider' || _cmd.find('.cmdAttr[data-l1key=subType]').value() == 'color') {
                _cmd.find('.cmdAttr[data-l1key=value]').show();
            }
            if ('function' == typeof (initExpertMode)) {
                initExpertMode();
            }
        }
    });
};

jeedom.cmd.availableType = function() {
    var selType = '<select style="width : 120px; margin-bottom : 3px;" class="cmdAttr form-control input-sm" data-l1key="type">';
    selType += '<option value="info">{{Info}}</option>';
    selType += '<option value="action">{{Action}}</option>';
    selType += '</select>';
    return selType;
};

jeedom.cmd.getSelectModal = function(_options, _callback) {
    if (!isset(_options)) {
        _options = {};
    }
    if ($("#mod_insertCmdValue").length == 0) {
        $('body').append('<div id="mod_insertCmdValue" title="{{Sélectionner la commande}}" ></div>');

        $("#mod_insertCmdValue").dialog({
            autoOpen: false,
            modal: true,
            height: 250,
            width: 800
        });
        jQuery.ajaxSetup({async: false});
        $('#mod_insertCmdValue').load('index.php?v=d&modal=cmd.human.insert');
        jQuery.ajaxSetup({async: true});
    }
    mod_insertCmd.setOptions(_options);
    $("#mod_insertCmdValue").dialog('option', 'buttons', {
        "Annuler": function() {
            $(this).dialog("close");
        },
        "Valider": function() {
            var retour = {};
            retour.human = mod_insertCmd.getValue();
            if ($.trim(retour) != '' && 'function' == typeof (_callback)) {
                _callback(retour);
            }
            $(this).dialog('close');
        }
    });
    $('#mod_insertCmdValue').dialog('open');
};


jeedom.cmd.displayActionOption = function(_expression, _options, _callback) {
    var html = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: 'actionToHtml',
            version: 'scenario',
            expression: _expression,
            option: json_encode(_options)
        },
        dataType: 'json',
        async: ('function' == typeof (_callback)),
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (data.result.html != '') {
                html += '<div class="alert alert-info" style="margin : 0px; padding : 3px;">';
                html += data.result.html;
                html += '</div>';
            }
            if ('function' == typeof (_callback)) {
                _callback(html);
                return;
            }
        }
    });
    return html;
};
