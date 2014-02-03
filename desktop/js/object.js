
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
    $(".li_object").on('click', function(event) {
        $.hideAlert();
        $('#div_conf').show();
        $('.li_object').removeClass('active');
        $(this).addClass('active');
        printObject($(this).attr('data-object_id'));
        return false;
    });

    $("#bt_addObject").on('click', function(event) {
        $.hideAlert();
        $('#in_addObjectName').value('');
        $('#md_addObject').modal('show');
        return false;
    });

    $("#bt_addObjetSave").on('click', function(event) {
        addObject();
        return false;
    });

    $("#bt_saveObject").on('click', function(event) {
        if ($('.li_object.active').attr('data-object_id') != undefined) {
            saveObject();
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un objet', level: 'danger'});
        }
        return false;
    });

    $("#bt_removeObject").on('click', function(event) {
        if ($('.li_object.active').attr('data-object_id') != undefined) {
            $.hideAlert();
            bootbox.confirm('Etez-vous sûr de vouloir supprimer l\'objet <span style="font-weight: bold ;">' + $('.li_object.active a').text() + '</span> ?', function(result) {
                if (result) {
                    removeObject($('.li_object.active').attr('data-object_id'));
                }
            });
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un objet', level: 'danger'});
        }
        return false;
    });

    if (select_id != -1) {
        if ($('#ul_object .li_object[data-object_id=' + select_id + ']').length != 0) {
            $('#ul_object .li_object[data-object_id=' + select_id + ']').click();
        } else {
            $('#ul_object .li_object:first').click();
        }
    } else {
        $('#ul_object .li_object:first').click();
    }
});


function removeObject(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "removeObject",
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
            window.location.replace('index.php?v=d&p=object');
        }
    });
}

function printObject(_object_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "byId",
            id: _object_id
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
            $('.objectAttr[data-l1key=father_id] option').show();
            $('.object').setValues(data.result, '.objectAttr');
            $('.objectAttr[data-l1key=father_id] option[value=' + _object_id + ']').hide();
        }
    });
}

function addObject() {
    var name = $('#in_addObjectName').value();
    if (name == '') {
        $('#div_addObjetAlert').showAlert({message: 'Le nom de l\'objet ne peut être vide', level: 'danger'});
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "saveObject",
            name: name,
            isVisible: 1
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
            window.location.replace('index.php?v=d&p=object&id=' + data.result.id);
        }
    });
}


function  saveObject() {
    var object = $('.object').getValues('.objectAttr');
    object = object[0];
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/object.ajax.php", // url du fichier php
        data: {
            action: "saveObject",
            object : json_encode(object),
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
            $('#div_alert').showAlert({message: 'Objet sauvegardé', level: 'success'});
        }
    });

}