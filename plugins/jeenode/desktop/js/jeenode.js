
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
    $(".li_jeenode").on('click', function(event) {
        $.hideAlert();
        $('#div_conf').show();
        $('.li_jeenode').removeClass('active');
        $(this).addClass('active');
        printJeenode($(this).attr('data-name'), $(this).attr('data-type'), $(this).attr('data-jeenodeReal_id'));
        return false;
    });

    $("#bt_addJeenode").on('click', function(event) {
        $.hideAlert();
        $('#in_addJeenodeName').value('');
        $('#md_addJeenode').modal('show');
        return false;
    });

    $("#bt_addJeenodeSave").on('click', function(event) {
        addJeenode();
        return false;
    });

    $("#bt_showAdvanceConfigue").on('click', function(event) {
        if ($('#div_confCommunAvance').is(':visible')) {
            $(this).text('Afficher');
            $('#div_confCommunAvance').hide();
        } else {
            $(this).text('Masquer');
            $('#div_confCommunAvance').show();
        }
        return false;
    });

    $("#bt_saveJeenode").on('click', function(event) {
        if ($('.li_jeenode.active').attr('data-jeenodeReal_id') != undefined) {
            saveJeenode();
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un jeenode', level: 'danger'});
        }
        return false;
    });

    $("#bt_removeJeenode").on('click', function(event) {
        if ($('.li_jeenode.active').attr('data-jeenodeReal_id') != undefined) {
            $.hideAlert();
            bootbox.confirm('Etez-vous sûr de vouloir supprimer le jeenode <span style="font-weight: bold ;">' + $('.li_jeenode.active').attr('data-name') + '</span> ?', function(result) {
                if (result) {
                    removeJeenode($('.li_jeenode.active').attr('data-jeenodeReal_id'));
                }
            });
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un jeenode', level: 'danger'});
        }
        return false;
    });

    if (select_id != -1) {
        if ($('#ul_jeenode .li_jeenode[data-jeenodeReal_id=' + select_id + ']').length != 0) {
            $('#ul_jeenode .li_jeenode[data-jeenodeReal_id=' + select_id + ']').click();
        } else {
            $('#ul_jeenode .li_jeenode:first').click();
        }
    } else {
        $('#ul_jeenode .li_jeenode:first').click();
    }
});

function printJeenode(_name, _type, _jeenodeRealId) {
    $('.eqRealAttr[data-l1key=name]').value(_name);
    $('.eqRealAttr[data-l1key=type]').value(_type);
    getJeenodeConf(_type, _jeenodeRealId);
}

function getJeenodeConf(_type, _jeenodeRealId) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "getJeenodeConf",
            type: _type,
            jeenodeRealId: _jeenodeRealId
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
            $('#div_configurationSpecifiqueType').empty();
            $('#label_bat').closest('.form-group').hide();
            $('#label_ram').closest('.form-group').hide();
            $('#label_uptime').closest('.form-group').hide();
            $('#label_lastCommunication').closest('.form-group').show();
            $('#label_lastCommunication').text((data.result.configuration.lastCommunication != null && data.result.configuration.lastCommunication != '') ? data.result.configuration.lastCommunication : '');
            $('.eqRealAttr[data-l1key=nodeID]').value((data.result.logicalId != null && data.result.logicalId != '') ? data.result.logicalId : '');

            if (data.result.type == 'master') {
                $('#label_uptime').closest('.form-group').show();
                $('#label_ram').closest('.form-group').show();
                $('#label_lastCommunication').closest('.form-group').hide();
                getInfo(_jeenodeRealId);
                $('#div_configurationSpecifiqueType').html(getTemplate('jeenode', 'master', 'generaleConfiguration.php'));
            }

            if (data.result.type == 'jeenode') {
                var replace = new Array();
                replace['#generaleConfData#'] = json_encode(data.result);
                $('#div_configurationSpecifiqueType').html(getTemplate('jeenode', 'jeenode', 'generaleConfiguration.php', replace));
                if (data.result.configuration.mode == 'actif') {
                    $('#div_configurationSpecifiqueType').find('.sel_portType option[data-active_only=1]').prop('disabled', false);
                    $('#label_ram').closest('.form-group').show();
                    $('#label_uptime').closest('.form-group').show();
                    getInfo(_jeenodeRealId);
                } else {
                    $('#div_configurationSpecifiqueType').find('.sel_portType option[data-active_only=1]').prop('disabled', true);
                }
            }
            $('body').setValues(data.result, '.eqRealAttr');
        }
    });
}


function addJeenode() {
    $.hideAlert();
    var name = $('#in_addJeenodeName').value();
    if (name == '') {
        $('#div_addJeenodeAlert').showAlert({message: 'Le nom du jeenode ne peut être vide', level: 'danger'});
        return;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "addJeenode",
            name: name,
            type: $('#sel_addJeenodeType').value()
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_addJeenodeAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_addJeenodeAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.replace('index.php?v=d&m=jeenode&p=jeenode&id=' + data.result.id);
        }
    });
}

function removeJeenode(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "removeJeenode",
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
            window.location.replace('index.php?v=d&p=jeenode');
        }
    });
}

function saveJeenode() {
    $.hideAlert();
    try {
        var eqReals = [];
        $('.eqReal').each(function() {
            var eqReal = $(this).getValues('.eqRealAttr');
            eqReal = eqReal[0];
            eqReal.id = $('.li_jeenode.active').attr('data-jeenodeReal_id');

            if (isNaN(eqReal.logicalId) || eqReal.logicalId == '') {
                throw('Le node ID ne peut etre vide et doit etre un nombre');
            }
            eqReal.eqLogic = [];
            $(this).find('.eqLogic').each(function() {
                if ($(this).find('.portType').length > 0) {
                    var eqLogic = $(this).getValues('.eqLogicAttr');
                    eqLogic = eqLogic[0];
                    eqLogic.configuration = {};
                    eqLogic.configuration.portType = [];
                    eqLogic['plugin'] = 'jeenode';
                    eqLogic['eqReal_id'] = eqReal.id;
                    $(this).find('.portType').each(function() {
                        eqLogic.configuration.portType.push($(this).attr('data-code'));
                    });
                    eqLogic.cmd = $(this).find('.confSpePort .cmd').getValues('.cmdAttr');
                    eqReal.eqLogic.push(eqLogic);
                }
            });
            eqReals.push(eqReal);
        });
    } catch (e) {
        $('#div_alert').showAlert({message: e, level: 'danger'});
        return false;
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "saveJeenode",
            eqReals: json_encode(eqReals)
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
            $('#div_alert').showAlert({message: 'Jeenode sauvegardé', level: 'success'});
            var li = $('.li_jeenode.active');
            printJeenode(li.attr('data-name'), li.attr('data-type'), li.attr('data-jeenodeReal_id'));
        }
    });
}

function getInfo(_id) {
    $('#label_uptime').text('');
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "getUptime",
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
            if (data.result == 'NR') {
                data.result = 'Aucune reponse';
                $('#label_ram').text(data.result);
                $('#label_ram').addClass('label-info');
                $('#label_ram').removeClass('label-important');
            } else {
                getFreeRam(_id);
            }
            if (data.result == false) {
                $('#label_uptime').text('Error');
                $('#label_uptime').addClass('label-important');
                $('#label_uptime').removeClass('label-info');
            } else {
                $('#label_uptime').text(data.result);
                $('#label_uptime').addClass('label-info');
                $('#label_uptime').removeClass('label-important');
            }
        }
    });
}

function getFreeRam(_id) {
    $('#label_ram').text('');
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/jeenode/core/ajax/jeenode.ajax.php", // url du fichier php
        data: {
            action: "getFreeRam",
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
            if (data.result == false) {
                $('#label_ram').text('Error');
                $('#label_ram').addClass('label-important');
                $('#label_ram').removeClass('label-info');
            } else {
                $('#label_ram').text(data.result + ' octets (' + Math.round(intval(data.result) / 20.48) + ' %)');
                $('#label_ram').addClass('label-info');
                $('#label_ram').removeClass('label-important');
            }

        }
    });
}