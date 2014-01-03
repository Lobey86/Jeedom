
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


cmd.changeType = function(_el, _subType) {
    var selSubType = '<select style="width : 100px;margin-top : 5px;" class="cmdAttr form-control" l1key="subType">';
    var type = _el.value();
    switch (type) {
        case 'info' :
            selSubType += '<option value="numeric">Numérique</option>';
            selSubType += '<option value="binary">Binaire</option>';
            selSubType += '<option value="string">Autre</option>';
            _el.closest('.cmd').find('.cmdAttr[l1key=eventOnly]').parent().show();
            _el.closest('.cmd').find('.cmdAttr[l1key=isHistorized]').parent().show();
            break;
        case 'action' :
            selSubType += '<option value="other">Défaut</option>';
            selSubType += '<option value="slider">Slider</option>';
            selSubType += '<option value="message">Message</option>';
            selSubType += '<option value="color">Couleur</option>';
            _el.closest('.cmd').find('.cmdAttr[l1key=eventOnly]').parent().hide();
            _el.closest('.cmd').find('.cmdAttr[l1key=isHistorized]').parent().hide();
            break;
    }
    selSubType += '</select>';
    _el.closest('.cmd').find('.subType').empty();
    _el.closest('.cmd').find('.subType').append(selSubType);
    if (isset(_subType)) {
        _el.closest('.cmd').find('.cmdAttr[l1key=subType]').value(_subType);
    }
}

cmd.availableType = function() {
    var selType = '<select style="width : 100px; margin-bottom : 3px;" class="cmdAttr form-control" l1key="type">';
    selType += '<option value="info">Info</option>';
    selType += '<option value="action">Action</option>';
    selType += '</select>';
    return selType;
}

cmd.getSelectModal = function(_options, callback) {
    if ($("#mod_insertCmdValue").length == 0) {
        $('body').append('<div id="mod_insertCmdValue" title="Sélectionner la commande" ></div>');

        $("#mod_insertCmdValue").dialog({
            autoOpen: false,
            modal: true,
            height: 250,
            width: 800
        });
    }
    if (!isset(_options)) {
        _options = {};
    }
    $("#mod_insertCmdValue").dialog('option', 'buttons', {
        "Annuler": function() {
            $(this).dialog("close");
        },
        "Valider": function() {
            var retour = {};
            retour.human = mod_insertCmdValue_getValue();
            if ($.trim(retour) != '') {
                callback(retour);
            }
            $(this).dialog('close');
        }
    });
    $('#mod_insertCmdValue').load('index.php?v=d&modal=cmd.human.insert&cmd_type=' + init(_options.type, 'all')).dialog('open');
}