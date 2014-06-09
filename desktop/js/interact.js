
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
    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Sauvegarde effectuée avec succès}}', level: 'success'});
    }

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Suppression effectuée avec succès}}', level: 'success'});
    }

    $(".li_interact").on('click', function(event) {
        $('#div_conf').show();
        $('.li_interact').removeClass('active');
        $(this).addClass('active');
        printInteract($(this).attr('data-interact_id'));
        return false;
    });


    if (is_numeric(getUrlVars('id'))) {
        if ($('#ul_interact .li_interact[data-interact_id=' + getUrlVars('id') + ']').length != 0) {
            $('#ul_interact .li_interact[data-interact_id=' + getUrlVars('id') + ']').click();
        } else {
            $('#ul_interact .li_interact:first').click();
        }
    } else {
        $('#ul_interact .li_interact:first').click();
    }

    $('body').delegate('.interactAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $(".interactAttr[data-l1key=link_type]").on('change', function() {
        changeLinkType({link_type: $(this).value()});
    });

    $('.displayInteracQuery').on('click', function() {
        $('#md_modal').dialog({title: "{{Liste des interactions}}"});
        $('#md_modal').load('index.php?v=d&modal=interact.query.display&interactDef_id=' + $('.interactAttr[data-l1key=id]').value()).dialog('open');
    });

    $('body').delegate('.listEquipementInfo', 'click', function() {
        jeedom.cmd.getSelectModal({}, function(result) {
            $('.interactAttr[data-l1key=link_id]').value(result.human);
        });
    });

    $("#bt_saveInteract").on('click', function() {
        var interact = $('.interact').getValues('.interactAttr');
        saveInteract(interact[0]);
    });

    $("#bt_addInteract").on('click', function(event) {
        bootbox.prompt("Demande ?", function(result) {
            if (result !== null) {
                saveInteract({query: result});
            }
        });
    });

    $("#bt_removeInteract").on('click', function() {
        if ($('.li_interact.active').attr('data-interact_id') != undefined) {
            $.hideAlert();
            bootbox.confirm('{{Etes-vous sûr de vouloir supprimer l\'intéraction}} <span style="font-weight: bold ;">' + $('.li_interact.active a').text() + '</span> ?', function(result) {
                if (result) {
                    removeInteract($('.li_interact.active').attr('data-interact_id'));
                }
            });
        } else {
            $('#div_alert').showAlert({message: '{{Veuillez d\'abord sélectionner un objet}}', level: 'danger'});
        }
    });
});

function changeLinkType(_options) {
    $('#linkOption').empty();
    $('.interactAttr[data-l1key=reply]').closest('.form-group').show();
    $('#div_filtre').show();
    $('.interactAttr[data-l1key=options][data-l2key=convertBinary]').closest('.form-group').show();
    $('.interactAttr[data-l1key=options][data-l2key=synonymes]').closest('.form-group').show();
    if (_options.link_type == 'whatDoYouKnow') {
        $('.interactAttr[data-l1key=options][data-l2key=convertBinary]').closest('.form-group').hide();
        $('.interactAttr[data-l1key=options][data-l2key=synonymes]').closest('.form-group').hide();
        $('.interactAttr[data-l1key=reply]').closest('.form-group').hide();
        $('#div_filtre').hide();
    }
    if (_options.link_type == 'cmd') {
        var options = '<div class="form-group">';
        options += '<label class="col-sm-3 control-label">{{Commande}}</label>';
        options += '<div class="col-sm-8">';
        options += '<input class="interactAttr form-control input-sm" data-l1key="link_id" style="margin-top : 5px;"/>';
        options += '</div>';
        options += '<div class="col-sm-1">';
        options += '<a class="form-control btn btn-default cursor listEquipementInfo input-sm" style="margin-top : 5px;"><i class="fa fa-list-alt "></i></a></td>';
        options += '</div>';
        options += '</div>';
        $('#linkOption').append(options);
    }
    if (_options.link_type == 'scenario') {
        var scenarios = jeedom.scenario.all();
        var options = '<div class="form-group">';
        options += '<label class="col-sm-3 control-label">{{Scénario}}</label>';
        options += '<div class="col-sm-9">';
        options += '<select class="interactAttr form-control input-sm" data-l1key="link_id" style="margin-top : 5px;">';
        for (var i in scenarios) {
            options += '<option value="' + scenarios[i].id + '">' + scenarios[i].humanName + '</option>';
        }
        options += '</select>';
        options += '</div>';
        options += '</div>';
        options += '<div class="form-group">';
        options += '<label class="col-sm-3 control-label">{{Action}}</label>';
        options += '<div class="col-sm-9">';
        options += '<select class="interactAttr form-control input-sm" data-l1key="options" data-l2key="scenario_action">';
        options += '<option value="start">{{Start}}</option>';
        options += '<option value="stop">{{Stop}}</option>';
        options += '<option value="activate">{{Activer}}</option>';
        options += '<option value="deactivate">{{Désactiver}}</option>';
        options += '</select>';
         options += '</div>';
        options += '</div>';
        $('#linkOption').append(options);
        $('.interactAttr[data-l1key=options][data-l2key=convertBinary]').closest('.form-group').hide();
        $('.interactAttr[data-l1key=options][data-l2key=synonymes]').closest('.form-group').hide();
        $('.interactAttr[data-l1key=reply]').closest('.form-group').hide();
        $('#div_filtre').hide();
    }
    delete _options.link_type;
    $('.interact').setValues(_options, '.interactAttr');
}

function removeInteract(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: "remove",
            id: _id
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
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=interact&removeSuccessFull=1');
        }
    });
}


function printInteract(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _id
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
            $('.interactAttr').value('');
            $('.interact').setValues(data.result, '.interactAttr');
            modifyWithoutSave = false;
        }
    });
}

function saveInteract(_interact) {
    $.hideAlert();
    $.ajax({
        type: 'POST',
        url: "core/ajax/interact.ajax.php", // url du fichier php
        data: {
            action: 'save',
            interact: json_encode(_interact),
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
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=interact&id=' + data.result.id + '&saveSuccessFull=1');
        }
    });
}