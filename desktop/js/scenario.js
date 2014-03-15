
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
    editor = [];

    autoCompleteCondition = [
        {val: 'rand[MIN-MAX]#'},
        {val: 'heure#'},
        {val: 'jour#'},
        {val: 'mois#'},
        {val: 'annee#'},
        {val: 'date#'},
        {val: 'time#'},
        {val: 'semaine#'},
        {val: 'sjour#'},
        {val: 'minute#'},
        {val: 'var[mavariable-defaut]#'},
    ];

    autoCompleteAction = ['sleep', 'var', 'scenario'];

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Sauvegarde effectuée avec succès', level: 'success'});
    }

    $(".li_scenario").on('click', function(event) {
        $.hideAlert();
        $(".li_scenario").removeClass('active');
        $(this).addClass('active');
        printScenario($(this).attr('data-scenario_id'));
    });

    $("#bt_changeAllScenarioState").on('click', function() {
        var el = $(this);
        var value = {enableScenario: el.attr('data-state')};
        $.ajax({
            type: 'POST',
            url: 'core/ajax/config.ajax.php',
            data: {
                action: 'addKey',
                value: json_encode(value)
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
                if (el.attr('data-state') == 1) {
                    el.find('i').removeClass('fa-check').addClass('fa-times');
                    el.removeClass('btn-success').addClass('btn-danger').attr('data-state', 0);
                } else {
                    el.find('i').removeClass('fa-times').addClass('fa-check');
                    el.removeClass('btn-danger').addClass('btn-success').attr('data-state', 1);
                }
            }
        });
    });

    $('#sel_group').change(function() {
        window.location.href = 'index.php?v=d&p=scenario&group=' + $(this).value();
    });

    $('#md_addScenario').modal('hide');

    $("#bt_addScenario").on('click', function(event) {
        bootbox.prompt("Nom du scénario ?", function(result) {
            if (result !== null) {
                addScenario(result);
            }
        });
    });

    $("#bt_saveScenario").on('click', function(event) {
        saveScenario();
    });

    $("#bt_delScenario").on('click', function(event) {
        $.hideAlert();
        bootbox.confirm('Etes-vous sûr de vouloir supprimer le scénario <span style="font-weight: bold ;">' + $('.scenarioAttr[data-l1key=name]').value() + '</span> ?', function(result) {
            if (result) {
                removeScenario($('.scenarioAttr[data-l1key=id]').value());
            }
        });
    });

    $("#bt_testScenario").on('click', function() {
        $.hideAlert();
        execScenario($('.scenarioAttr[data-l1key=id]').value());
    });

    $("#bt_copyScenario").on('click', function() {
        bootbox.prompt("Nom du scénario ?", function(result) {
            if (result !== null) {
                copyScenario($('.scenarioAttr[data-l1key=id]').value(), result);
            }
        });
    });

    $("#bt_copyScenarioSave").on('click', function(event) {
        copyScenario($('.scenarioAttr[data-l1key=id]').value(), $('#in_copyScenarioName').value());
    });

    $("#bt_stopScenario").on('click', function(event) {
        stopScenario($('.scenarioAttr[data-l1key=id]').value());
    });

    $('#bt_displayScenarioVariable').on('click', function() {
        $('#md_modal').closest('.ui-dialog').css('z-index', '1030');
        $('#md_modal').dialog({title: "Variable des scénarios"});
        $("#md_modal").load('index.php?v=d&modal=dataStore.management&type=scenario').dialog('open');

    });

    /*******************Element***********************/
    $('body').delegate('.bt_addScenarioElement', 'click', function(event) {
        var elementDiv = $(this).closest('.element');
        var expression = false;
        if ($(this).hasClass('fromSubElement')) {
            elementDiv = $(this).closest('.subElement').find('.expressions').eq(0);
            expression = true;
        }
        $('#md_addElement').modal('show');
        $("#bt_addElementSave").off();
        $("#bt_addElementSave").on('click', function(event) {
            if (expression) {
                elementDiv.append(addExpression({type: 'element', element: {type: $("#in_addElementType").value()}}));
            } else {
                elementDiv.append(addElement({type: $("#in_addElementType").value()}));
            }
            setEditor();
            $('#md_addElement').modal('hide');
        });
    });

    $('body').delegate('.bt_removeElement', 'click', function(event) {
        if ($(this).closest('.expression').length != 0) {
            $(this).closest('.expression').remove();
        } else {
            $(this).closest('.element').remove();
        }
    });

    $('body').delegate('.bt_addAction', 'click', function(event) {
        $(this).closest('.subElement').children('.expressions').append(addExpression({type: 'action'}));
        setAutocomplete();
    });

    $('body').delegate('.bt_removeExpression', 'click', function(event) {
        $(this).closest('.expression').remove();
    });

    $('body').delegate('.bt_selectCmdExpression', 'click', function(event) {
        var expression = $(this).closest('.expression');
        var type = 'info';
        if (expression.find('.expressionAttr[data-l1key=type]').value() == 'action') {
            type = 'action';
        }
        cmd.getSelectModal({cmd: {type: type}}, function(result) {
            if (expression.find('.expressionAttr[data-l1key=type]').value() == 'action') {
                expression.find('.expressionAttr[data-l1key=expression]').value(result.human);
                expression.find('.expressionOptions').html(displayActionOption(expression.find('.expressionAttr[data-l1key=expression]').value(), ''));
            }
            if (expression.find('.expressionAttr[data-l1key=type]').value() == 'condition') {
                expression.find('.expressionAttr[data-l1key=expression]').value(expression.find('.expressionAttr[data-l1key=expression]').value() + ' ' + result.human);
            }
        });
    });

    $('body').delegate('.expression .expressionAttr[data-l1key=expression]', 'focusout', function(event) {
        if ($(this).closest('.expression').find('.expressionAttr[data-l1key=type]').value() == 'action') {
            var expression = $(this).closest('.expression').getValues('.expressionAttr');
            $(this).closest('.expression').find('.expressionOptions').html(displayActionOption($(this).value(), init(expression[0].options)));
        }
    });


    /**************** Scheduler **********************/

    $('.scenarioAttr[data-l1key=mode]').on('change', function() {
        if ($(this).value() == 'schedule' || $(this).value() == 'all') {
            $('.scheduleDisplay').show();
            $('#bt_addSchedule').show();
        } else {
            $('.scheduleDisplay').hide();
            $('#bt_addSchedule').hide();
        }
        if ($(this).value() == 'provoke' || $(this).value() == 'all') {
            $('.provokeDisplay').show();
            $('#bt_addTrigger').show();
        } else {
            $('.provokeDisplay').hide();
            $('#bt_addTrigger').hide();
        }
    });

    $('#bt_addTrigger').on('click', function() {
        addTrigger('');
    });

    $('#bt_addSchedule').on('click', function() {
        addSchedule('');
    });

    $('body').delegate('.bt_removeTrigger', 'click', function(event) {
        $(this).closest('.trigger').remove();
    });

    $('body').delegate('.bt_removeSchedule', 'click', function(event) {
        $(this).closest('.schedule').remove();
    });

    $('body').delegate('.bt_selectTrigger', 'click', function(event) {
        var el = $(this);
        cmd.getSelectModal({cmd: {type: 'info'}}, function(result) {
            el.closest('.trigger').find('.scenarioAttr[data-l1key=trigger]').value(result.human);
        });
    });

    $('body').delegate('.bt_sortable', 'mouseenter', function() {
        $("#div_scenarioElement").sortable({axis: "y", cursor: "move", items: ".expression", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
        $("#div_scenarioElement").sortable("enable");
    });

    $('body').delegate('.bt_sortable', 'mouseout', function() {
        $("#div_scenarioElement").sortable("disable");
    });

    /***********************LOG*****************************/

    $('#bt_logScenario').on('click', function() {
        $('#md_modal').dialog({title: "Log d\'éxécution du scénario"});
        $("#md_modal").load('index.php?v=d&modal=scenario.log.execution&scenario_id=' + $('.scenarioAttr[data-l1key=id]').value()).dialog('open');
    });

    /**************** Initialisation **********************/
    if (is_numeric(getUrlVars('id'))) {
        if ($('#ul_scenario .li_scenario[data-scenario_id=' + getUrlVars('id') + ']').length != 0) {
            $('#ul_scenario .li_scenario[data-scenario_id=' + getUrlVars('id') + ']').click();
        } else {
            $('#ul_scenario .li_scenario:first').click();
        }
    } else {
        $('#ul_scenario .li_scenario:first').click();
    }

    $('body').delegate('.scenarioAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $('body').delegate('.expressionAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $('body').delegate('.elementAttr', 'change', function() {
        modifyWithoutSave = true;
    });

    $('body').delegate('.subElementAttr', 'change', function() {
        modifyWithoutSave = true;
    });
});

function setEditor() {
    $('.expressionAttr[data-l1key=type][value=code]').each(function() {
        var expression = $(this).closest('.expression');
        var code = expression.find('.expressionAttr[data-l1key=expression]');
        if (code.attr('id') == undefined) {
            code.uniqueId();
            var id = code.attr('id');
            setTimeout(function() {
                editor[id] = CodeMirror.fromTextArea(document.getElementById(id), {
                    lineNumbers: true,
                    mode: 'text/x-php',
                    matchBrackets: true
                });
            }, 1);
        }
    });
}

function setAutocomplete() {
    $('.expression').each(function() {
        if ($(this).find('.expressionAttr[data-l1key=type]').value() == 'condition') {
            $(this).find('.expressionAttr[data-l1key=expression]').sew({values: autoCompleteCondition, token: '#'}); // pass in the values
        }
        if ($(this).find('.expressionAttr[data-l1key=type]').value() == 'action') {
            $(this).find('.expressionAttr[data-l1key=expression]').autocomplete({
                source: autoCompleteAction
            });
        }
    });
    $('.autoCompleteCondition').sew({values: autoCompleteCondition, token: '#'})
}

function displayActionOption(_expression, _options) {
    var html = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: 'actionToHtml',
            version: 'scenario',
            expression: _expression,
            option: json_encode(_options)
        },
        dataType: 'json',
        async: false,
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (data.result.html != '') {
                html += '<div class="alert alert-info" style="margin : 0px; padding : 3px;">';
                html += data.result.html;
                html += '</div>';
            }
            setAutocomplete();
        }
    });
    return html;
}


function copyScenario(_scenario_id, _name) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "copyScenario",
            id: _scenario_id,
            name: _name
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_copyScenarioAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_copyScenarioAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.replace('index.php?v=d&p=scenario&id=' + data.result.id);
        }
    });
}

function stopScenario(_scenario_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "stopScenario",
            id: _scenario_id,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_copyScenarioAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            printScenario(_scenario_id);
        }
    });
}

function addScenario(_name) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "addScenario",
            name: _name,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_addScenarioAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_addScenarioAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.replace('index.php?v=d&p=scenario&id=' + data.result.id);
        }
    });
}

function printScenario(_id) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "getScenario",
            id: _id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('input.scenarioAttr').value('');
            $('#table_scenarioCondition tbody').empty();
            $('#table_scenarioAction tbody').empty();
            $('#table_trigger tbody').empty();
            $('.scenarioAttr[data-l1key=object_id] option:first').prop('selected', true);
            $('body').setValues(data.result, '.scenarioAttr');
            $('#span_type').text(data.result.type);
            data.result.lastLaunch = (data.result.lastLaunch == null) ? 'Jamais' : data.result.lastLaunch;
            $('#span_lastLaunch').text(data.result.lastLaunch);


            $('#div_scenarioElement').empty();
            $('#div_scenarioElement').append('<a class="btn btn-default bt_addScenarioElement"><i class="fa fa-plus-circle"></i> Ajouter Elément</a>');
            $('.provokeMode').empty();
            $('.scheduleMode').empty();
            $('.scenarioAttr[data-l1key=mode]').trigger('change');
            for (var i in data.result.schedules) {
                $('#div_schedules').schedule.display(data.result.schedules[i]);
            }
            $('#bt_stopScenario').hide();
            switch (data.result.state) {
                case 'error' :
                    $('#span_ongoing').text('Erreur');
                    $('#span_ongoing').removeClass('label-info label-danger label-success').addClass('label-warning');
                    break;
                case 'on' :
                    $('#span_ongoing').text('Actif');
                    $('#span_ongoing').removeClass('label-info label-danger label-warning').addClass('label-success');
                    break;
                case 'in progress' :
                    $('#span_ongoing').text('En cours');
                    $('#span_ongoing').addClass('label-success');
                    $('#span_ongoing').removeClass('label-success label-danger label-warning').addClass('label-info');
                    $('#bt_stopScenario').show();
                    break;
                case 'stop' :
                    $('#span_ongoing').text('Arrêté');
                    $('#span_ongoing').removeClass('label-info label-success label-warning').addClass('label-danger');
                    break;
            }

            if (data.result.isActive != 1) {
                $('#in_ongoing').text('Inactif');
                $('#in_ongoing').removeClass('label-danger');
                $('#in_ongoing').removeClass('label-success');
            }

            if ($.isArray(data.result.trigger)) {
                for (var i in data.result.trigger) {
                    if (data.result.trigger[i] != '' && data.result.trigger[i] != null) {
                        addTrigger(data.result.trigger[i]);
                    }
                }
            } else {
                if (data.result.trigger != '' && data.result.trigger != null) {
                    addTrigger(data.result.trigger);
                }
            }

            if ($.isArray(data.result.schedule)) {
                for (var i in data.result.schedule) {
                    if (data.result.schedule[i] != '' && data.result.schedule[i] != null) {
                        addSchedule(data.result.schedule[i]);
                    }
                }
            } else {
                if (data.result.schedule != '' && data.result.schedule != null) {
                    addSchedule(data.result.schedule);
                }
            }

            for (var i in data.result.elements) {
                $('#div_scenarioElement').append(addElement(data.result.elements[i]));
            }
            setEditor();
            setAutocomplete();
            $('#div_editScenario').show();
            $.hideLoading();
            modifyWithoutSave = false;
        }
    });
}

function saveScenario() {
    $.hideAlert();
    var scenario = $('body').getValues('.scenarioAttr');
    scenario = scenario[0];
    var elements = [];
    $('#div_scenarioElement').children('.element').each(function() {
        elements.push(getElement($(this)));
    });
    scenario.elements = elements;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "saveScenario",
            scenario: json_encode(scenario),
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
            var vars = getUrlVars();
            var url = 'index.php?';
            for (var i in vars) {
                if (i != 'id' && i != 'saveSuccessFull' && i != 'removeSuccessFull') {
                    url += i + '=' + vars[i].replace('#', '') + '&';
                }
            }
            url += 'id=' + scenario.id + '&saveSuccessFull=1';
            modifyWithoutSave = false;
            window.location.href = url;
        }
    });
}

function removeScenario(_scenario_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "removeScenario",
            id: _scenario_id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $.hideLoading();
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            modifyWithoutSave = false;
            window.location.replace('index.php?v=d&p=scenario');
        }
    });
}

function execScenario(_scenario_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "execScenario",
            id: _scenario_id
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
            $('#div_alert').showAlert({message: 'Exécution réussie', level: 'success'});
        }
    });
}

function addTrigger(_trigger) {
    var div = '<div class="form-group trigger">';
    div += '<label class="col-lg-3 control-label">Evènement</label>';
    div += '<div class="col-lg-7">';
    div += '<input class="scenarioAttr input-sm form-control" data-l1key="trigger" value="' + _trigger + '">';
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<a class="btn btn-default btn-sm cursor bt_selectTrigger"><i class="fa fa-list-alt"></i></a>';
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-minus-circle bt_removeTrigger cursor" style="margin-top : 9px;"></i>';
    div += '</div>';
    div += '</div>';
    $('.provokeMode').append(div);
}

function addSchedule(_schedule) {
    var div = '<div class="form-group schedule">';
    div += '<label class="col-lg-3 control-label">Programmation</label>';
    div += '<div class="col-lg-7">';
    div += '<input class="scenarioAttr input-sm form-control" data-l1key="schedule" value="' + _schedule + '">';
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-question-circle cursor bt_pageHelp floatright" data-name="cronSyntaxe"></i>';
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-minus-circle bt_removeSchedule cursor"></i>';
    div += '</div>';
    div += '</div>';
    $('.scheduleMode').append(div);
}

function addExpression(_expression) {
    if (!isset(_expression.type) || _expression.type == '') {
        return '';
    }
    var retour = '<div class="expression row">';
    retour += '<input class="expressionAttr" data-l1key="id" style="display : none;" value="' + init(_expression.id) + '"/>';
    retour += '<input class="expressionAttr" data-l1key="scenarioSubElement_id" style="display : none;" value="' + init(_expression.scenarioSubElement_id) + '"/>';
    retour += '<input class="expressionAttr" data-l1key="type" style="display : none;" value="' + init(_expression.type) + '"/>';
    switch (_expression.type) {
        case 'condition' :
            if (isset(_expression.expression)) {
                _expression.expression = _expression.expression.replace(/"/g, '&quot;');
            }
            retour += '<div class="col-lg-11">';
            retour += '<input class="expressionAttr form-control input-sm" data-l1key="expression" value="' + init(_expression.expression) + '" style="background-color : #dff0d8;" />';
            retour += '</div>';
            retour += '<div class="col-lg-1">';
            retour += ' <a class="btn btn-default btn-sm cursor bt_selectCmdExpression" cmd_type="info"><i class="fa fa-list-alt"></i></a>';
            retour += '</div>';
            break;
        case 'element' :
            retour += '<div class="col-lg-12">';
            retour += '<i class="fa fa-bars pull-left cursor bt_sortable" style="margin-top : 12px;margin-left : 4px;"></i>';
            retour += addElement(_expression.element, true);
            retour += '</div>';
            break;
        case 'action' :
            retour += '<div class="col-lg-1">';
            retour += '<i class="fa fa-bars pull-left cursor bt_sortable" style="margin-top : 9px;"></i>';
            retour += '<i class="fa fa-minus-circle pull-left cursor bt_removeExpression" style="margin-top : 9px;"></i>';
            retour += '</div>';
            retour += '<div class="col-lg-6">';
            retour += '<input class="expressionAttr form-control input-sm" data-l1key="expression" value="' + init(_expression.expression) + '" style="background-color : #fcf8e3;"/>';
            retour += '</div>';
            retour += '<div class="col-lg-1">';
            retour += ' <a class="btn btn-default btn-sm cursor bt_selectCmdExpression" cmd_type="action"><i class="fa fa-list-alt"></i></a>';
            retour += '</div>';
            retour += '<div class="col-lg-4 expressionOptions">';
            retour += displayActionOption(init(_expression.expression), init(_expression.options));
            retour += '</div>';
            break;
        case 'code' :
            retour += '<div class="col-lg-12">';
            retour += '<i class="fa fa-bars pull-left cursor bt_sortable" style="margin-top : 9px;"></i>';
            retour += '<textarea class="expressionAttr form-control" data-l1key="expression">' + init(_expression.expression) + '</textarea>';
            retour += '</div>';
            break;
    }
    retour += '</div>';
    return retour;
}

function addSubElement(_subElement) {
    if (!isset(_subElement.type) || _subElement.type == '') {
        return '';
    }
    if (!isset(_subElement.options)) {
        _subElement.options = {};
    }
    var retour = '<div class="subElement">';
    retour += '<input class="subElementAttr" data-l1key="id" style="display : none;" value="' + init(_subElement.id) + '"/>';
    retour += '<input class="subElementAttr" data-l1key="scenarioElement_id" style="display : none;" value="' + init(_subElement.scenarioElement_id) + '"/>';
    retour += '<input class="subElementAttr" data-l1key="type" style="display : none;" value="' + init(_subElement.type) + '"/>';
    switch (_subElement.type) {
        case 'if' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="condition"/>';
            retour += '<legend>SI ';
            retour += '<div class="expressions" style="display : inline-block; width : 90%">';
            var expression = {type: 'condition'};
            if (isset(_subElement.expressions) && isset(_subElement.expressions[0])) {
                expression = _subElement.expressions[0];
            }
            retour += addExpression(expression);
            retour += '</div>';
            retour += '</legend>';
            break;
        case 'then' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="action"/>';
            retour += '<legend style="margin-top : 8px;">ALORS';
            retour += '<a class="btn btn-xs btn-default bt_addScenarioElement pull-right fromSubElement"><i class="fa fa-plus-circle"></i> Ajouter élément</a>';
            retour += '<a class="btn btn-xs btn-default bt_addAction pull-right"><i class="fa fa-plus-circle"></i> Ajouter action</a>';
            retour += '</legend>';
            retour += '<div class="expressions">';
            if (isset(_subElement.expressions)) {
                for (var k in _subElement.expressions) {
                    retour += addExpression(_subElement.expressions[k]);
                }
            }
            retour += '</div>';
            break;
        case 'else' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="action"/>';
            retour += '<legend style="margin-top : 8px;">SINON';
            retour += '<a class="btn btn-xs btn-default bt_addScenarioElement pull-right fromSubElement"><i class="fa fa-plus-circle"></i> Ajouter élément</a>';
            retour += '<a class="btn btn-xs btn-default bt_addAction pull-right"><i class="fa fa-plus-circle"></i> Ajouter action</a>';
            retour += '</legend>';
            retour += '<div class="expressions">';
            if (isset(_subElement.expressions)) {
                for (var k in _subElement.expressions) {
                    retour += addExpression(_subElement.expressions[k]);
                }
            }
            retour += '</div>';
            break;
        case 'for' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="condition"/>';
            retour += '<legend style="margin-top : 8px;">DE 1 A ';
            retour += '<div class="expressions" style="display : inline-block; width : 90%">';
            var expression = {type: 'condition'};
            if (isset(_subElement.expressions) && isset(_subElement.expressions[0])) {
                expression = _subElement.expressions[0];
            }
            retour += addExpression(expression);
            retour += '</div>';
            retour += '</legend>';
            break;
        case 'do' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="action"/>';
            retour += '<legend style="margin-top : 8px;">FAIRE';
            retour += '<a class="btn btn-xs btn-default bt_addScenarioElement pull-right fromSubElement"><i class="fa fa-plus-circle"></i> Ajouter élément</a>';
            retour += '<a class="btn btn-xs btn-default bt_addAction pull-right"><i class="fa fa-plus-circle"></i> Ajouter action</a>';
            retour += '</legend>';
            retour += '<div class="expressions">';
            if (isset(_subElement.expressions)) {
                for (var k in _subElement.expressions) {
                    retour += addExpression(_subElement.expressions[k]);
                }
            }
            retour += '</div>';
            break;
        case 'code' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="action"/>';
            retour += '<legend style="margin-top : 8px;">CODE';
            retour += '</legend>';
            retour += '<div class="expressions">';
            var expression = {type: 'code'};
            if (isset(_subElement.expressions) && isset(_subElement.expressions[0])) {
                expression = _subElement.expressions[0];
            }
            retour += addExpression(expression);
            retour += '</div>';
            break;
        case 'action' :
            retour += '<input class="subElementAttr" data-l1key="subtype" style="display : none;" value="action"/>';
            retour += '<legend style="margin-top : 8px;">ACTION';
            retour += '<a class="btn btn-xs btn-default bt_addScenarioElement pull-right fromSubElement"><i class="fa fa-plus-circle"></i> Ajouter élément</a>';
            retour += '<a class="btn btn-xs btn-default bt_addAction pull-right"><i class="fa fa-plus-circle"></i> Ajouter action</a>';
            retour += '</legend>';
            retour += '<div class="expressions">';
            if (isset(_subElement.expressions)) {
                for (var k in _subElement.expressions) {
                    retour += addExpression(_subElement.expressions[k]);
                }
            }
            retour += '</div>';
            break;
    }
    retour += '</div>';
    return retour;
}


function addElement(_element) {
    if (!isset(_element)) {
        return;
    }
    if (!isset(_element.type) || _element.type == '') {
        return '';
    }
    var div = '<div class="element well well-sm" style="margin-top : 8px;border : 2px solid black;">';
    div += '<input class="elementAttr" data-l1key="id" style="display : none;" value="' + init(_element.id) + '"/>';
    div += '<input class="elementAttr" data-l1key="type" style="display : none;" value="' + init(_element.type) + '"/>';
    div += '<i class="fa fa-minus-circle pull-right cursor bt_removeElement"></i>';
    switch (_element.type) {
        case 'if' :
            if (isset(_element.subElements) && isset(_element.subElements)) {
                for (var j in _element.subElements) {
                    div += addSubElement(_element.subElements[j]);
                }
            } else {
                div += addSubElement({type: 'if'});
                div += addSubElement({type: 'then'});
                div += addSubElement({type: 'else'});
            }
            break;
        case 'for' :
            if (isset(_element.subElements) && isset(_element.subElements)) {
                for (var j in _element.subElements) {
                    div += addSubElement(_element.subElements[j]);
                }
            } else {
                div += addSubElement({type: 'for'});
                div += addSubElement({type: 'do'});
            }
            break;
        case 'code' :
            if (isset(_element.subElements) && isset(_element.subElements)) {
                for (var j in _element.subElements) {
                    div += addSubElement(_element.subElements[j]);
                }
            } else {
                div += addSubElement({type: 'code'});
            }
            break;
        case 'action' :
            if (isset(_element.subElements) && isset(_element.subElements)) {
                for (var j in _element.subElements) {
                    div += addSubElement(_element.subElements[j]);
                }
            } else {
                div += addSubElement({type: 'action'});
            }
            break;
    }
    div += '</div>';
    return div;
}

function getElement(_element) {
    var element = _element.getValues('.elementAttr', 1);
    if (element.length == 0) {
        return;
    }
    element = element[0];
    element.subElements = [];

    _element.findAtDepth('.subElement', 2).each(function() {
        var subElement = $(this).getValues('.subElementAttr', 2);
        subElement = subElement[0];
        subElement.expressions = [];
        var expression_dom = $(this).children('.expressions');
        if (expression_dom.length == 0) {
            expression_dom = $(this).children('legend').children('.expressions');
        }
        expression_dom.children('.expression').each(function() {
            var expression = $(this).getValues('.expressionAttr', 3);
            expression = expression[0];
            if (expression.type == 'element') {
                expression.element = getElement($(this).findAtDepth('.element', 2));
            }
            if (subElement.type == 'code') {
                var id = $(this).find('.expressionAttr[data-l1key=expression]').attr('id');
                if (id != undefined && isset(editor[id])) {
                    expression.expression = editor[id].getValue();
                }
            }
            subElement.expressions.push(expression);
        });
        element.subElements.push(subElement);
    });
    return element;
}
