
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
    var selSubType = '<select style="width : 120px;margin-top : 5px;" class="cmdAttr form-control" l1key="subType">';
    var type = _cmd.find('.cmdAttr[l1key=type]').value();
    switch (type) {
        case 'info' :
            selSubType += '<option value="numeric">Numérique</option>';
            selSubType += '<option value="binary">Binaire</option>';
            selSubType += '<option value="string">Autre</option>';
            _cmd.find('.cmdAttr[l1key=eventOnly]').show();
            _cmd.find('.cmdAttr[l1key=isHistorized]').parent().show();
            _cmd.find('.cmdAttr[l1key=cache][l2key=enable]').parent().show();
            break;
        case 'action' :
            selSubType += '<option value="other">Défaut</option>';
            selSubType += '<option value="slider">Slider</option>';
            selSubType += '<option value="message">Message</option>';
            selSubType += '<option value="color">Couleur</option>';
            _cmd.find('.cmdAttr[l1key=eventOnly]').parent().hide();
            _cmd.find('.cmdAttr[l1key=isHistorized]').parent().hide();
            _cmd.find('.cmdAttr[l1key=cache][l2key=enable]').parent().hide();
            break;
    }
    selSubType += '</select>';
    _cmd.find('.subType').empty();
    _cmd.find('.subType').append(selSubType);
    if (isset(_subType)) {
        _cmd.find('.cmdAttr[l1key=subType]').value(_subType);
    }
    _cmd.find('.cmdAttr[l1key=subType]').trigger('change');
}

cmd.changeSubType = function(_cmd) {
    var type = _cmd.find('.cmdAttr[l1key=type]').value();
    var subType = _cmd.find('.cmdAttr[l1key=subType]').value();
    switch (type) {
        case 'info' :
            switch (subType) {
                case 'numeric' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').show();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').show();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').show();
                    _cmd.find('.cmdAttr[l1key=unite]').show();
                    break;
                case 'binary' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').hide();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').hide();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').show();
                    _cmd.find('.cmdAttr[l1key=unite]').hide();
                    break;
                case 'string' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').hide();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').hide();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').show();
                    _cmd.find('.cmdAttr[l1key=unite]').show();
                    break;
            }
            break;
        case 'action' :
            switch (subType) {
                case 'other' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').hide();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').hide();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').hide();
                    _cmd.find('.cmdAttr[l1key=unite]').hide();
                    break;
                case 'slider' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').show();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').show();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').hide();
                    _cmd.find('.cmdAttr[l1key=unite]').hide();
                    break;
                case 'message' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').hide();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').hide();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').hide();
                    _cmd.find('.cmdAttr[l1key=unite]').hide();
                    break;
                case 'color' :
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=minValue]').hide();
                    _cmd.find('.cmdAttr[l1key=configuration][l2key=maxValue]').hide();
                    _cmd.find('.cmdAttr[l1key=cache][l2key=lifetime]').hide();
                    _cmd.find('.cmdAttr[l1key=unite]').hide();
                    break;
            }
            break;
    }
}

cmd.availableType = function() {
    var selType = '<select style="width : 120px; margin-bottom : 3px;" class="cmdAttr form-control" l1key="type">';
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
        jQuery.ajaxSetup({async:false});
        $('#mod_insertCmdValue').load('index.php?v=d&modal=cmd.human.insert');
        jQuery.ajaxSetup({async:true});
    }
    mod_insertCmd.setTypeCmd(init(_options.type, 'all'));
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