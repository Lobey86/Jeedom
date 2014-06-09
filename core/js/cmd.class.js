
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

jeedom.cmd.getSuggestColor = function(_id) {
    var eqLogic = $(".cmd[data-cmd_id=" + _id + "]").closest('.eqLogic');
    if (count(eqLogic) > 0 && eqLogic != undefined) {
        var vcolor = 'cmdColor';
        if (eqLogic.attr('data-version') == 'mobile') {
            vcolor = 'mcmdColor';
        }
        return jeedom.getConfiguration('eqLogic:category:' + eqLogic.attr('data-category') + ':' + vcolor);
    }
    return '#000000';
};


jeedom.cmd.changeType = function(_cmd, _subType) {
    var selSubType = '<select style="width : 120px;margin-top : 5px;" class="cmdAttr form-control input-sm" data-l1key="subType">';
    var type = _cmd.find('.cmdAttr[data-l1key=type]').value();
    switch (type) {
        case 'info' :
            var subType = jeedom.getConfiguration('cmd:type:info:subtype');
            for (var i in subType) {
                selSubType += '<option value="' + i + '">' + subType[i].name + '</option>';
            }
            break;
        case 'action' :
            var subType = jeedom.getConfiguration('cmd:type:action:subtype');
            for (var i in subType) {
                selSubType += '<option value="' + i + '">' + subType[i].name + '</option>';
            }
            break;
    }
    selSubType += '</select>';
    _cmd.find('.subType').empty();
    _cmd.find('.subType').append(selSubType);
    if (isset(_subType)) {
        _cmd.find('.cmdAttr[data-l1key=subType]').value(_subType);
    }
    jeedom.cmd.changeSubType(_cmd);
};

jeedom.cmd.changeSubType = function(_cmd) {
    var subtype = jeedom.getConfiguration('cmd:type:' + _cmd.find('.cmdAttr[data-l1key=type]').value() + ':subtype:' + _cmd.find('.cmdAttr[data-l1key=subType]').value());
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
};

jeedom.cmd.availableType = function() {
    var selType = '<select style="width : 120px; margin-bottom : 3px;" class="cmdAttr form-control input-sm" data-l1key="type">';
    selType += '<option value="info">{{Info}}</option>';
    selType += '<option value="action">{{Action}}</option>';
    selType += '</select>';
    return selType;
};

jeedom.cmd.getSelectModal = function(_options, callback) {
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
            if ($.trim(retour) != '') {
                callback(retour);
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
        async: false,
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
        }
    });
    return html;
};



jeedom.cmd.execute = function(_id, _value, _cache, _notify, _callback) {
    var eqLogic = $('.cmd[data-cmd_id=' + _id + ']').closest('.eqLogic');
    eqLogic.find('.statusCmd').empty().append('<i class="fa fa-spinner fa-spin"></i>');
    if (init(_value) != '' && (is_array(_value) || is_object(_value))) {
        _value = json_encode(_value);
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "execCmd",
            id: _id,
            cache: init(_cache, 1),
            value: _value
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                eqLogic.find('.statusCmd').empty().append('<i class="fa fa-times"></i>');
                setTimeout(function() {
                    eqLogic.find('.statusCmd').empty();
                }, 3000);
                return;
            }
            if (init(_notify, true)) {
                eqLogic.find('.statusCmd').empty().append('<i class="fa fa-rss"></i>');
                setTimeout(function() {
                    eqLogic.find('.statusCmd').empty();
                }, 3000);
            }
            if ('function' == typeof (_callback)) {
                _callback(data.result);
            }
        }
    });
};


jeedom.cmd.test = function(_id) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "getCmd",
            id: _id,
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
                    jeedom.cmd.execute(_id, '', 0, false, function(result) {
                        alert(result);
                    });
                    break;
                case 'action' :
                    switch (result.subType) {
                        case 'other' :
                            jeedom.cmd.execute(_id, '', 0);
                            break;
                        case 'slider' :
                            var slider = new Object();
                            slider['slider'] = 50;
                            jeedom.cmd.execute(_id, slider, 0);
                            break;
                        case 'color' :
                            var color = new Object();
                            color['color'] = '#fff000';
                            jeedom.cmd.execute(_id, color, 0);
                            break;
                        case 'message' :
                            var message = new Object();
                            message['title'] = '{{[Jeedom] Message de test}}';
                            message['message'] = '{{Ceci est un test de message pour la commande}} ' + result.name;
                            jeedom.cmd.execute(_id, message, 0);
                            break;
                    }
                    break;
            }
        }
    });
};


jeedom.cmd.refreshValue = function(_cmd_id) {
    if ($('.cmd[data-cmd_id=' + _cmd_id + ']').html() != undefined && $('.cmd[data-cmd_id=' + _cmd_id + ']').closest('.eqLogic').attr('data-version') != undefined) {
        var version = $('.cmd[data-cmd_id=' + _cmd_id + ']').closest('.eqLogic').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/cmd.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _cmd_id,
                version: version,
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
                $('.cmd[data-cmd_id=' + _cmd_id + ']').replaceWith(data.result.html);
                initTooltips();
                if ($.mobile) {
                    $('.cmd[data-cmd_id=' + _cmd_id + ']').trigger("create");
                } else {
                    positionEqLogic($('.cmd[data-cmd_id=' + _cmd_id + ']').closest('.eqLogic').attr('data-eqLogic_id'), true);
                }
            }
        });
    }
};


