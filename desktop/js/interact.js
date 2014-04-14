
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
    printInteractDef();

    $('#table_interactDef tbody').delegate('.displayInteracQuery', 'click', function() {
        var tr = $(this).closest('tr');
        $('#md_modal').dialog({title: "{{Liste des interactions}}"});
        $('#md_modal').load('index.php?v=d&modal=interact.query.display&interactDef_id=' + tr.find('.interactDefAttr[data-l1key=id]').value()).dialog('open');
    });

    $("#bt_addSarahDef").on('click', function() {
        addInteractDefToTable({});
    });

    $("#bt_save").on('click', function() {
        saveIntercDef();
    });

    $("#table_interactDef").delegate(".remove", 'click', function() {
        $(this).closest('tr').remove();
    });

    $("#table_interactDef").delegate(".listEquipementInfo", 'click', function() {
        var el = $(this);
        cmd.getSelectModal({cmd: {type: 'action'}}, function(result) {
            el.closest('tr').find('.interactDefAttr[data-l1key=link_id]').value(result.human);
        });
    });

    $("#table_interactDef").delegate(".interactDefAttr[data-l1key=link_type]", 'change', function() {
        var el = $(this);
        el.closest('tr').find('.interactDefAttr').show();
        el.closest('tr').find('.listEquipementInfo').show();
        if (el.value() == 'whatDoYouKnow') {
            el.closest('tr').find('.interactDefAttr[data-l1key=link_id]').hide();
            el.closest('tr').find('.interactDefAttr[data-l1key=options][data-l2key=convertBinary]').hide();
            el.closest('tr').find('.interactDefAttr[data-l1key=options][data-l2key=synonymes]').hide();
            el.closest('tr').find('.interactDefAttr[data-l1key=reply]').hide();
            el.closest('tr').find('.listEquipementInfo').hide();
            el.closest('tr').find('.interactDefAttr[data-l1key=filtres]').hide();
        }
    });
    
    $('body').delegate('.interactDefAttr', 'change', function() {
        modifyWithoutSave = true;
    });
});

function saveIntercDef() {
    $.hideAlert();
    var interactDefs = $('#table_interactDef tbody tr').getValues('.interactDefAttr');
    $.ajax({
        type: 'POST',
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: 'save',
            interactDefs: json_encode(interactDefs),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            printInteractDef();
            modifyWithoutSave = false;
        }
    });
}


function printInteractDef() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: "all",
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
            $('#table_interactDef tbody tr').remove();

            for (var i in data.result) {
                addInteractDefToTable(data.result[i]);
            }
            modifyWithoutSave = false;
        }
    });
}

function addInteractDefToTable(_interactDef) {
    if (!isset(_interactDef)) {
        _interactDef = {};
    }
    var tr = '<tr>';
    tr += '<td>';
    tr += '<input class="interactDefAttr hide" data-l1key="id" />';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-4">';
    tr += '<select class="interactDefAttr tooltips form-control input-sm" data-l1key="filtres" data-l2key="cmd_type" title="{{Limiter aux commande de type}}">';
    var types = jeedom.getConfiguration('cmd:type');
    for (var i in types) {
        tr += '<option value="' + i + '">' + types[i].name + '</option>';
    }
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += '<select class=\'interactDefAttr tooltips form-control input-sm\' data-l1key=\'filtres\' data-l2key=\'subtype\' title=\'{{Limiter aux commandes ayant pour sous-type}}\'>';
    tr += '<option value=\'all\' >{{Tous<}}/option>';
    for (var i in types) {
        for (var j in types[i].subtype) {
            tr += '<option value="' + j + '">' + types[i].subtype[j].name + '</option>';
        }
    }
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_unite;
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    var objects = object.all();
    tr += '<select class=\'interactDefAttr tooltips form-control input-sm\' data-l1key=\'filtres\' data-l2key=\'object_id\' title=\'{{Limiter aux commandes appartenant à l objet}}\' style=\'margin-top : 5px;\'>';
    tr += '<option value=\'all\' >{{Tous}}</option>';
    for (var i in objects) {
        tr += '<option value=' + objects[i].id + '>' + objects[i].name + '</option>';
    }
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_eqType;
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-6 has-warning">';
    tr += '<input class="interactDefAttr form-control input-sm" data-l1key="query" placeholder="{{Demande}}" />';
    tr += '</div>';
    tr += '<div class="col-lg-6 has-success">';
    tr += '<input class="interactDefAttr form-control input-sm" data-l1key="reply" placeholder="{{Réponse}}"/>';
    tr += '</div>';
    tr += '<div class="col-lg-6">';
    tr += '<input class="interactDefAttr form-control input-sm tooltips" data-l1key="options" data-l2key="convertBinary" placeholder="{{Conversion binaire : faux|vrai}}" title="{{Convertir les commandes binaire}}" style="margin-top : 5px;" />';
    tr += '</div>';
    tr += '<div class="col-lg-6">';
    tr += '<input class="interactDefAttr form-control input-sm tooltips" data-l1key="options" data-l2key="synonymes" placeholder="{{Synonyne}}" title="{{Remplace les mots par leur synonyme lors de la generation des commandes}}" style="margin-top : 5px;" />';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-12">';
    tr += '<select class="interactDefAttr form-control input-sm" data-l1key="link_type">';
    tr += '<option value="cmd">{{Commande}}</option>';
    tr += '<option value="whatDoYouKnow">{{Que sais tu ?}}</option>';
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-9">';
    tr += '<input class="interactDefAttr form-control input-sm" data-l1key="link_id" style="margin-top : 5px;"/>';
    tr += '</div>';
    tr += '<div class="col-lg-3">';
    tr += '<a class="form-control btn btn-default cursor listEquipementInfo input-sm" style="margin-top : 5px;"><i class="fa fa-list-alt "></i></a></td>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="interactDefAttr form-control input-sm" data-l1key="person"/>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="displayInteracQuery cursor">';
    tr += '<span class="label label-success interactDefAttr tooltips" data-l1key="nbEnableInteractQuery" title="{{Nombre de requetes active}}"></span> / ';
    tr += '<span class="label label-default interactDefAttr tooltips" data-l1key="nbInteractQuery" title="{{Nombre de requetes totales}}"></span>';
    tr += '</span>';
    tr += '<i class="fa fa-minus-circle remove pull-right cursor"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_interactDef tbody').append(tr);
    $('#table_interactDef tbody tr:last').setValues(_interactDef, '.interactDefAttr');
    activateTooltips();
}