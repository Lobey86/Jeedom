
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
        eqLogic.print(eqType, $(this).attr('data-eqLogic_id'));
        return false;
    });

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Sauvegarde effectuée avec succès', level: 'success'});
    }

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Suppression effectuée avec succès', level: 'success'});
    }

    /**************************EqLogic*********************************************/

    $('.eqLogicAction[data-action=save]').on('click', function() {
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

    $('.eqLogicAction[data-action=remove]').on('click', function() {
        if ($('.li_eqLogic.active').attr('data-eqLogic_id') != undefined) {
            bootbox.confirm('Etes-vous sûr de vouloir supprimer l\'équipement ' + eqType + ' <span style="font-weight: bold ;">' + $('.li_eqLogic.active a:first').text() + '</span> ?', function(result) {
                if (result) {
                    eqLogic.remove(eqType);
                }
            });
        } else {
            $('#div_alert').showAlert({message: 'Veuillez d\'abord sélectionner un ' + eqType, level: 'danger'});
        }
    });


    $('.eqLogicAction[data-action=add]').on('click', function() {
        $.hideAlert();
        $('#md_addEqLogic .eqLogicAttr').value('');
        $('#md_addEqLogic').modal('show');
    });

    $("#md_addEqLogic .eqLogicAction[data-action=newAdd]").on('click', function() {
        eqLogic.save(eqType, $('#md_addEqLogic').getValues('.eqLogicAttr'));
    });

    /**************************CMD*********************************************/
    $('.cmdAction[data-action=add]').on('click', function() {
        addCmdToTable();
        $('.cmd .cmdAttr[data-l1key=type]').trigger('change');
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=type]', 'change', function() {
        cmd.changeType($(this).closest('.cmd'));
    });
    
    $('body').delegate('.cmd .cmdAttr[data-l1key=subType]', 'change', function() {
        cmd.changeSubType($(this).closest('.cmd'));
    });

    $('body').delegate('.cmd .cmdAction[data-action=remove]', 'click', function() {
        $(this).closest('tr').remove();
    });

    $('body').delegate('.cmd .cmdAction[data-action=test]', 'click', function() {
        $.hideAlert();
        if ($('.eqLogicAttr[data-l1key=isEnable]').is(':checked')) {
            var id = $(this).closest('.cmd').attr('data-cmd_id');
            cmd_test(id);
        } else {
            $('#div_alert').showAlert({message: 'Veuillez activer l\'équipement avant de tester une de ses commandes', level: 'warning'});
        }
    });

    if (select_id != -1) {
        if ($('#ul_eqLogic .li_eqLogic[data-eqLogic_id=' + select_id + ']').length != 0) {
            $('#ul_eqLogic .li_eqLogic[data-eqLogic_id=' + select_id + ']').click();
        } else {
            $('#ul_eqLogic .li_eqLogic:first').click();
        }
    } else {
        $('#ul_eqLogic .li_eqLogic:first').click();
    }
});