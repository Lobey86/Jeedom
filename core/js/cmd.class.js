
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


function cmd() {
}


cmd.changeType = function(_cmd, _subType) {
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
    cmd.changeSubType(_cmd);
}

cmd.changeSubType = function(_cmd) {
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
}

cmd.availableType = function() {
    var selType = '<select style="width : 120px; margin-bottom : 3px;" class="cmdAttr form-control input-sm" data-l1key="type">';
    selType += '<option value="info">Info</option>';
    selType += '<option value="action">Action</option>';
    selType += '</select>';
    return selType;
}

cmd.getSelectModal = function(_options, callback) {
    if (!isset(_options)) {
        _options = {};
    }
    if ($("#mod_insertCmdValue").length == 0) {
        $('body').append('<div id="mod_insertCmdValue" title="Sélectionner la commande" ></div>');

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
}