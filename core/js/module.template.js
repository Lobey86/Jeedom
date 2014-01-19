
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
    $(".li_eqLogic").on('click', function() {
        $('.eqLogic').show();
        $('.li_eqLogic').removeClass('active');
        $(this).addClass('active');
        eqLogic.print(eqType, $(this).attr('eqLogic_id'));
        return false;
    });

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Sauvegarde effectué avec succès', level: 'success'});
    }

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Suppression effectué avec succès', level: 'success'});
    }

    /**************************EqLogic*********************************************/

    $('.eqLogicAction[action=save]').on('click', function() {
        var eqLogics = [];
        $('.eqLogic').each(function() {
            var eqLogic = $(this).getValues('.eqLogicAttr');
            eqLogic = eqLogic[0];
            eqLogic.cmd = $(this).find('.cmd').getValues('.cmdAttr');
            eqLogics.push(eqLogic);
        });
        eqLogic.save(eqType, eqLogics);
        return false;
    });

    $('.eqLogicAction[action=remove]').on('click', function() {
        if ($('.li_eqLogic.active').attr('eqLogic_id') != undefined) {
            bootbox.confirm('Etes-vous sûr de vouloir supprimer l\'équipement ' + eqType + ' <span style="font-weight: bold ;">' + $('.li_eqLogic.active a:first').text() + '</span> ?', function(result) {
                if (result) {
                    eqLogic.remove(eqType);
                }
            });
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un ' + eqType, level: 'danger'});
        }
    });


    $('.eqLogicAction[action=add]').on('click', function() {
        $.hideAlert();
        $('#md_addEqLogic .eqLogicAttr').value('');
        $('#md_addEqLogic').modal('show');
    });

    $("#md_addEqLogic .eqLogicAction[action=newAdd]").on('click', function() {
        eqLogic.save(eqType, $('#md_addEqLogic').getValues('.eqLogicAttr'));
    });

    /**************************CMD*********************************************/
    $('.cmdAction[action=add]').on('click', function() {
        addCmdToTable();
    });

    $('body').delegate('.cmd .cmdAttr[l1key=type]', 'change', function() {
        cmd.changeType($(this).closest('.cmd'));
    });
    
    $('body').delegate('.cmd .cmdAttr[l1key=subType]', 'change', function() {
        cmd.changeSubType($(this).closest('.cmd'));
    });

    $('body').delegate('.cmd .cmdAction[action=remove]', 'click', function() {
        $(this).closest('tr').remove();
    });

    $('body').delegate('.cmd .cmdAction[action=test]', 'click', function() {
        $.hideAlert();
        if ($('.eqLogicAttr[l1key=isEnable]').is(':checked')) {
            var id = $(this).closest('.cmd').attr('cmd_id');
            cmd_test(id);
        } else {
            $('#div_alert').showAlert({message: 'Veuillez activer l\'équipement avant de tester une de ses commandes', level: 'warning'});
        }
    });

    if (select_id != -1) {
        if ($('#ul_eqLogic .li_eqLogic[eqLogic_id=' + select_id + ']').length != 0) {
            $('#ul_eqLogic .li_eqLogic[eqLogic_id=' + select_id + ']').click();
        } else {
            $('#ul_eqLogic .li_eqLogic:first').click();
        }
    } else {
        $('#ul_eqLogic .li_eqLogic:first').click();
    }
});