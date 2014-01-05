
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
    printSarahDef();

    $("#md_queries").dialog({
        autoOpen: false,
        modal: true,
        height: (jQuery(window).height() - 150),
        width: 1000
    });

    $('#table_interactDef tbody').delegate('.displayInteracQuery', 'click', function() {
        var tr = $(this).closest('tr');
        $('#md_queries').load('index.php?v=d&modal=interact.query.display&interactDef_id=' + tr.find('.interactDefAttr[l1key=id]').value()).dialog('open');
    });

    $("#bt_addSarahDef").on('click', function() {
        addSarahDefToTable({});
    });

    $("#bt_save").on('click', function() {
        saveSarahDef();
    });

    $("#table_interactDef").delegate(".remove", 'click', function() {
        $(this).closest('tr').remove();
    });

    $("#table_interactDef").delegate(".listEquipementInfo", 'click', function() {
        var el = $(this);
        cmd.getSelectModal({type: 'all'}, function(result) {
            el.closest('tr').find('.interactDefAttr[l1key=link_id]').value(result.human);
        });
    });

    $("#table_interactDef tbody").sortable();
});

function saveSarahDef() {
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
            $('#div_alert').showAlert({message: 'Sauvegarde réussie', level: 'success'});
            printSarahDef();
        }
    });
}


function printSarahDef() {
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
                addSarahDefToTable(data.result[i]);
            }
        }
    });
}

function addSarahDefToTable(_interactDef) {
    if (!isset(_interactDef)) {
        _interactDef = {};
    }
    var tr = '<tr>';
    tr += '<td>';
    tr += '<input class="interactDefAttr" l1key="id" style="display : none;"/>';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-4">';
    tr += '<select class="interactDefAttr tooltips form-control input-sm" l1key="filtres" l2key="cmd_type" title="Limiter aux commande de type">';
    tr += '<option value="info">Info</option>';
    tr += '<option value="action">Action</option>';
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_subtype;
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_unite;
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_object;
    tr += '</div>';
    tr += '<div class="col-lg-4">';
    tr += sel_eqType;
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-6 has-warning">';
    tr += '<input class="interactDefAttr form-control input-sm" l1key="query" placeholder="Demande" />';
    tr += '</div>';
    tr += '<div class="col-lg-6 has-success">';
    tr += '<input class="interactDefAttr form-control input-sm" l1key="reply" placeholder="Réponse"/>';
    tr += '</div>';
    tr += '<div class="col-lg-6">';
    tr += '<input class="interactDefAttr form-control input-sm tooltips" l1key="options" l2key="convertBinary" placeholder="Conversion binaire : faux|vrai" title="Convertir les commandes binaire" style="margin-top : 5px;" />';
    tr += '</div>';
    tr += '<div class="col-lg-6">';
    tr += '<input class="interactDefAttr form-control input-sm tooltips" l1key="options" l2key="synonymes" placeholder="Synonyne" title="Remplace les mots par leur synonyme lors de la generation des commandes" style="margin-top : 5px;" />';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<div class="form-group">';
    tr += '<div class="col-lg-12">';
    tr += '<select class="interactDefAttr form-control input-sm" l1key="link_type">';
    tr += '<option value="cmd">Commande</option>';
    tr += '</select>';
    tr += '</div>';
    tr += '<div class="col-lg-9">';
    tr += '<input class="interactDefAttr form-control input-sm" l1key="link_id" style="margin-top : 5px;"/>';
    tr += '</div>';
    tr += '<div class="col-lg-3">';
    tr += '<a class="form-control btn btn-default cursor listEquipementInfo input-sm" style="margin-top : 5px;"><i class="fa fa-list-alt "></i></a></td>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="interactDefAttr form-control input-sm" l1key="person"/>';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="displayInteracQuery cursor">';
    tr += '<span class="label label-success interactDefAttr tooltips" l1key="nbEnableInteractQuery" title="Nombre de requetes active"></span> / ';
    tr += '<span class="label label-default interactDefAttr tooltips" l1key="nbInteractQuery" title="Nombre de requetes générées"></span>';
    tr += '</span>';
    tr += '<i class="fa fa-minus-circle remove pull-right cursor"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_interactDef tbody').append(tr);
    $('#table_interactDef tbody tr:last').setValues(_interactDef, '.interactDefAttr');
    //activateTooltips();
}