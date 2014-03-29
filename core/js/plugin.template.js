
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
            if ('function' == typeof(saveEqLogic)) {
                eqLogic = saveEqLogic(eqLogic);
            }
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
        bootbox.prompt("Nom de l'équipement ?", function(result) {
            if (result !== null) {
                eqLogic.save(eqType, [{name: result}]);
            }
        });
    });

    /**************************CMD*********************************************/
    $('.cmdAction[data-action=add]').on('click', function() {
        addCmdToTable();
        $('.cmd:last .cmdAttr[data-l1key=type]').trigger('change');
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=type]', 'change', function() {
        cmd.changeType($(this).closest('.cmd'));
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=subType]', 'change', function() {
        cmd.changeSubType($(this).closest('.cmd'));
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=eventOnly]', 'change', function() {
        if ($(this).value() == 1) {
            $(this).closest('.cmd').find('.cmdAttr[data-l1key=cache][data-l2key=lifetime]').hide();
            $(this).closest('.cmd').find('.cmdAttr[data-l1key=cache][data-l2key=lifetime]').addClass('hide');
        } else {
            $(this).closest('.cmd').find('.cmdAttr[data-l1key=cache][data-l2key=lifetime]').show();
            $(this).closest('.cmd').find('.cmdAttr[data-l1key=cache][data-l2key=lifetime]').removeClass('hide');
        }
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

    if (is_numeric(getUrlVars('id'))) {
        if ($('#ul_eqLogic .li_eqLogic[data-eqLogic_id=' + getUrlVars('id') + ']').length != 0) {
            $('#ul_eqLogic .li_eqLogic[data-eqLogic_id=' + getUrlVars('id') + ']').click();
        } else {
            $('#ul_eqLogic .li_eqLogic:first').click();
        }
    } else {
        $('#ul_eqLogic .li_eqLogic:first').click();
    }

    $('body').delegate('.cmdAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $('body').delegate('.eqLogicAttr', 'change', function() {
        modifyWithoutSave = true;
    });
});