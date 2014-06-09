
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
        jeedom.eqLogic.print(eqType, $(this).attr('data-eqLogic_id'), function(data) {
            $('body .eqLogicAttr').value('');
            $('body').setValues(data, '.eqLogicAttr');
            if ('function' == typeof (printEqLogic)) {
                printEqLogic(data);
            }
            if ('function' == typeof (addCmdToTable)) {
                $('.cmd').remove();
                for (var i in data.cmd) {
                    addCmdToTable(data.cmd[i]);
                    if ($('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').value() == 'slider' || $('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').value() == 'color') {
                        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=value]').show();
                    }
                }
            }
            initTooltips();
            initExpertMode();
            $.hideLoading();
            modifyWithoutSave = false;
        });
        return false;
    });

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Sauvegarde effectuée avec succès}}', level: 'success'});
    }

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: '{{Suppression effectuée avec succès}}', level: 'success'});
    }

    /**************************EqLogic*********************************************/

    $('.eqLogicAction[data-action=save]').on('click', function() {
        var eqLogics = [];
        $('.eqLogic').each(function() {
            var eqLogic = $(this).getValues('.eqLogicAttr');
            eqLogic = eqLogic[0];
            if ('function' == typeof (saveEqLogic)) {
                eqLogic = saveEqLogic(eqLogic);
            }
            eqLogic.cmd = $(this).find('.cmd').getValues('.cmdAttr');
            eqLogics.push(eqLogic);
        });
        jeedom.eqLogic.save(eqType, eqLogics, function(_data) {
            var vars = getUrlVars();
            var url = 'index.php?';
            for (var i in vars) {
                if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
                    url += i + '=' + vars[i].replace('#', '') + '&';
                }
            }
            modifyWithoutSave = false;
            url += 'id=' + _data.id + '&saveSuccessFull=1';
            window.location.href = url;
        });
        return false;
    });

    $('.eqLogicAction[data-action=remove]').on('click', function() {
        if ($('.li_eqLogic.active').attr('data-eqLogic_id') != undefined) {
            bootbox.confirm('{{Etes-vous sûr de vouloir supprimer l\'équipement}} ' + eqType + ' <b>' + $('.li_eqLogic.active a:first').text() + '</b> ?', function(result) {
                if (result) {
                    jeedom.eqLogic.remove(eqType, $('.li_jeedom.eqLogic.active').attr('data-eqLogic_id'), function() {
                        var vars = getUrlVars();
                        var url = 'index.php?';
                        for (var i in vars) {
                            if (i != 'id' && i != 'removeSuccessFull' && i != 'saveSuccessFull') {
                                url += i + '=' + vars[i].replace('#', '') + '&';
                            }
                        }
                        modifyWithoutSave = false;
                        url += 'removeSuccessFull=1';
                        window.location.href = url;
                    });
                }
            });
        } else {
            $('#div_alert').showAlert({message: '{{Veuillez d\'abord sélectionner un}} ' + eqType, level: 'danger'});
        }
    });


    $('.eqLogicAction[data-action=add]').on('click', function() {
        bootbox.prompt("{{Nom de l'équipement ?}}", function(result) {
            if (result !== null) {
                jeedom.eqLogic.save(eqType, [{name: result}], function(_data) {
                    var vars = getUrlVars();
                    var url = 'index.php?';
                    for (var i in vars) {
                        if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
                            url += i + '=' + vars[i].replace('#', '') + '&';
                        }
                    }
                    modifyWithoutSave = false;
                    url += 'id=' + _data.id + '&saveSuccessFull=1';
                    window.location.href = url;
                });
            }
        });
    });

    /**************************CMD*********************************************/
    $('.cmdAction[data-action=add]').on('click', function() {
        addCmdToTable();
        $('.cmd:last .cmdAttr[data-l1key=type]').trigger('change');
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=type]', 'change', function() {
        jeedom.cmd.changeType($(this).closest('.cmd'));
    });

    $('body').delegate('.cmd .cmdAction[data-l1key=chooseIcon]', 'click', function() {
        var cmd = $(this).closest('.cmd');
        chooseIcon(function(_icon) {
            cmd.find('.cmdAttr[data-l1key=display][data-l2key=icon]').empty().append(_icon);
        });
    });

    $('body').delegate('.cmd .cmdAttr[data-l1key=subType]', 'change', function() {
        jeedom.cmd.changeSubType($(this).closest('.cmd'));
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
            jeedom.cmd.test(id);
        } else {
            $('#div_alert').showAlert({message: '{{Veuillez activer l\'équipement avant de tester une de ses commandes}}', level: 'warning'});
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