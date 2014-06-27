
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

    $('#div_tree').on('select_node.jstree', function(node, selected) {
        if (selected.node.a_attr.class == 'infoObject') {
            displayObject(selected.node.a_attr['data-object_id']);
        }
        if (selected.node.a_attr.class == 'infoEqLogic') {
            displayEqLogic(selected.node.a_attr['data-eqlogic_id']);
        }
        if (selected.node.a_attr.class == 'infoCmd') {
            displayCmd(selected.node.a_attr['data-cmd_id']);
        }
    });
    $('#div_tree').jstree();


    $("#bt_displayConfig").on('click', function(event) {
        $.hideAlert();
        saveConfiguration($('#display_configuration'));
    });

    $('.bt_resetColor').on('click', function() {
        jeedom.getConfiguration({
            key: $(this).attr('data-l1key'),
            default: 1,
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(data) {
                $('.configKey[data-l1key="' + $(this).attr('data-l1key') + '"]').value(data);
            }
        });
    });
});


function saveConfiguration(_el) {
    jeedom.config.save({
        configuration: _el.getValues('.configKey')[0],
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function() {
            $('#div_alert').showAlert({message: '{{Sauvegarde effetuée}}', level: 'success'});
            modifyWithoutSave = false;
        }
    });
}

/***************************Commandes****************************/
function displayCmd(_cmd_id) {
    jeedom.cmd.byId(_cmd_id, function(data) {
        $('#div_displayInfo').empty();
        var div = '<div class="row">';
        div += '<div class="col-lg-6" >';
        div += '<form class="form-horizontal">';
        div += '<fieldset>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{ID}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="id"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Nom}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="name"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Type}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="type"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Sous-type}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="subType"></span>';
        div += '</div>';
        div += '</div>';

        div += '</fieldset>';
        div += '</form>';
        div += '</div>';
        div += '<div class="col-lg-6" >';


        div += '<form class="form-horizontal">';
        div += '<fieldset>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Unité}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="unite"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Memcache}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary tooltips" data-l1key="cache" data-l2key="enable" title="{{Actif}}"></span> ';
        div += '<span class="label label-default tooltips" title="{{Durée du cache}}"><span class="cmdAttr" data-l1key="cache" data-l2key="lifetime"></span> {{seconde(s)}}</span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Historisé}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="isHistorized"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Evenement seulement}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="cmdAttr label label-primary" data-l1key="eventOnly"></span>';
        div += '</div>';
        div += '</div>';

        div += '</fieldset>';
        div += '</form>';
        div += '</div>';
        div += '</div>';


        div += '<div>';
        div += '<legend>{{Configuration}}</legend>';
        div += '<form class="form-horizontal">';
        div += '<fieldset>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-2 control-label">{{Dashboard widget}}</label>';
        div += '<div class="col-lg-3">';
        div += '<select class="form-control cmdAttr" data-l1key="template" data-l2key="dashboard">';
        for (var i in cmd_widgetDashboard[data.type][data.subType]) {
            div += '<option>' + cmd_widgetDashboard[data.type][data.subType][i].name + '</option>';
        }
        div += '</select>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-2 control-label">{{Mobile widget}}</label>';
        div += '<div class="col-lg-3">';
        div += '<select class="form-control cmdAttr" data-l1key="template" data-l2key="mobile">';
        for (var i in cmd_widgetMobile[data.type][data.subType]) {
            div += '<option>' + cmd_widgetMobile[data.type][data.subType][i].name + '</option>';
        }
        div += '</select>';
        div += '</div>';
        div += '</div>';

        div += '</fieldset>';
        div += '</form>';
        div += '<a class="btn btn-success" id="saveCmd"><i class="fa fa-check-circle"></i> {{Enregistrer}}</a>';

        $('#div_displayInfo').html(div);
        $('#div_displayInfo').setValues(data, '.cmdAttr');
        $('#saveCmd').off().on('click', function() {
            var cmd = $('#div_displayInfo').getValues('.cmdAttr');
            jeedom.cmd.save(cmd[0], function() {
                $('#div_alert').showAlert({message: '{{Enregistrement réussi}}', level: 'success'});
            });
        });
    });
}

/***********************Objet***************************/
function displayObject(_object_id) {
    jeedom.object.byId({
        id: _object_id,
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            $('#div_displayInfo').empty();
            var div = '<div class="row">';
            div += '<form class="form-horizontal">';
            div += '<fieldset>';

            div += '<div class="form-group">';
            div += '<label class="col-lg-2 control-label">{{ID}}</label>';
            div += '<div class="col-lg-4">';
            div += '<span class="objectAttr label label-primary" data-l1key="id"></span>';
            div += '</div>';
            div += '</div>';

            div += '<div class="form-group">';
            div += '<label class="col-lg-2 control-label">{{Nom}}</label>';
            div += '<div class="col-lg-4">';
            div += '<span class="objectAttr label label-primary" data-l1key="name"></span>';
            div += '</div>';
            div += '</div>';

            div += '<div class="form-group">';
            div += '<label class="col-lg-2 control-label">{{Visible}}</label>';
            div += '<div class="col-lg-4">';
            div += '<span class="objectAttr label label-primary" data-l1key="isVisible"></span>';
            div += '</div>';
            div += '</div>';

            div += '</fieldset>';
            div += '</form>';
            div += '</div>';
            $('#div_displayInfo').html(div);
            $('#div_displayInfo').setValues(data, '.objectAttr');

        }
    });
}

/***********************EqLogic***************************/
function displayEqLogic(_eqLogic_id) {
    $.hideAlert();

    jeedom.eqLogic.byId(_eqLogic_id, function(data) {
        $('#div_displayInfo').empty();
        var div = '<div class="row">';
        div += '<div class="col-lg-6" >';
        div += '<form class="form-horizontal">';
        div += '<fieldset>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{ID}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="id"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Nom}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="name"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Logical ID}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="logicalId"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Object ID}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="object_id"></span>';
        div += '</div>';
        div += '</div>';

        div += '</fieldset>';
        div += '</form>';
        div += '</div>';
        div += '<div class="col-lg-6" >';
        div += '<form class="form-horizontal">';
        div += '<fieldset>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Type}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="eqType_name"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Activer}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="isEnable"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Visible}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="isVisible"></span>';
        div += '</div>';
        div += '</div>';

        div += '<div class="form-group">';
        div += '<label class="col-lg-4 control-label">{{Tentative échouée}}</label>';
        div += '<div class="col-lg-4">';
        div += '<span class="eqLogicAttr label label-primary" data-l1key="status" data-l2key="numberTryWithoutSuccess"></span>';
        div += '</div>';
        div += '</div>';

        div += '</fieldset>';
        div += '</form>';
        div += '</div>';
        div += '</div>';
        $('#div_displayInfo').html(div);
        $('#div_displayInfo').setValues(data, '.eqLogicAttr');
    });
}