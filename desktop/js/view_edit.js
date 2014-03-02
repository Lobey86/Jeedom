
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
    $("#md_addViewData").dialog({
        autoOpen: false,
        modal: true,
        height: (jQuery(window).height() - 150),
        width: (jQuery(window).width() - 450)
    });

    $(".li_view").on('click', function(event) {
        $.hideAlert();
        $(".li_view").removeClass('active');
        $(this).addClass('active');
        printView($(this).attr('data-view_id'));
        return false;
    });

    $('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]').on('change', function() {
        setColorSelect($(this).closest('select'));
    });

    $("#bt_addView").on('click', function(event) {
        $.hideAlert();
        $('#in_addViewName').value('');
        $('#in_addViewId').value('');
        $('#md_addView').modal('show');
        return false;
    });

    $("#bt_editView").on('click', function(event) {
        $.hideAlert();
        $('#in_addViewName').value($('.li_view.active a').text());
        $('#in_addViewId').value($('.li_view.active').attr('data-view_id'));
        $('#md_addView').modal('show');
        return false;
    });

    $("#bt_addViewSave").on('click', function(event) {
        editView();
        return;
    });

    $('#bt_saveView').on('click', function(event) {
        saveView($(".li_view.active").attr('data-view_id'));
        return;
    });

    $("#bt_removeView").on('click', function(event) {
        $.hideAlert();
        bootbox.confirm('Etez-vous sûr de vouloir supprimer la vue <span style="font-weight: bold ;">' + $(".li_view.active a").text() + '</span> ?', function(result) {
            if (result) {
                removeView($(".li_view.active").attr('data-view_id'));
            }
        });
    });

    if (is_numeric(getUrlVars('id'))) {
        if ($('#ul_view .li_view[data-view_id=' + getUrlVars('id') + ']').length != 0) {
            $('#ul_view .li_view[data-view_id=' + getUrlVars('id') + ']').click();
        } else {
            $('#ul_view .li_view:first').click();
        }
    } else {
        $('#ul_view .li_view:first').click();
    }

    $("#div_viewZones").sortable({axis: "y", cursor: "move", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

    $('.enable').on('click', function() {
        var selectTr = $(this).closest('tr');
        if ($(this).value() == 1) {
            selectTr.find('div.option').show();
            if (selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]').length) {
                var color = selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]').value();
                var colorChange = true;
                var colorNumberChange = 0;
                while (colorChange) {
                    colorChange = false;
                    $('#table_addViewData tbody tr').each(function() {
                        if ($(this).find('.enable').value() == 1 && color == $(this).closest('tr').find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]').value()) {
                            color = selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor] option[value=' + color + ']').next().value();
                            colorChange = true;
                            colorNumberChange++;
                        }
                    });
                    if (colorNumberChange > selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor] option').length) {
                        return;
                    }
                }
                selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor] option[value=' + color + ']').prop('selected', true);
                setColorSelect(selectTr.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]'));
            }
        } else {
            selectTr.find('div.option').hide();
        }
    });

    /*****************************viewZone****************************************/
    $('#bt_addviewZone').on('click', function() {
        $('#in_addEditviewZoneEmplacement').val('');
        $('#in_addEditviewZoneName').val('');
        $('#sel_addEditviewZoneType').prop('disabled', false);
        $('#md_addEditviewZone').modal('show');
    });

    $('#bt_addEditviewZoneSave').on('click', function() {
        if ($.trim($('#in_addEditviewZoneName').val()) != '') {
            var viewZone = {name: $('#in_addEditviewZoneName').value(), emplacement: $('#in_addEditviewZoneEmplacement').value(), type: $('#sel_addEditviewZoneType').value()};
            addEditviewZone(viewZone);
            $('#md_addEditviewZone').modal('hide');
        } else {
            alert('div_addEditviewZoneError', 'Le nom de la viewZone ne peut être vide')
        }
    });

    $('#div_viewZones').delegate('.bt_removeviewZone', 'click', function() {
        $(this).closest('.viewZone').remove();
    });

    $('#div_viewZones').delegate('.bt_editviewZone', 'click', function() {
        $('#md_addEditviewZone').modal('show');
        $('#in_addEditviewZoneName').val($(this).closest('.viewZone').find('.viewZoneAttr[data-l1key=name]').text());
        $('#sel_addEditviewZoneType').val($(this).closest('.viewZone').find('.viewZoneAttr[data-l1key=type]').val());
        $('#sel_addEditviewZoneType').prop('disabled', true);
        $('#in_addEditviewZoneEmplacement').val($(this).closest('.viewZone').attr('id'));
    });

    /*****************************DATA****************************************/

    $('#div_viewZones').delegate('.bt_addViewData', 'click', function() {
        $('#table_addViewData .filter').value('');
        var viewZone = $(this).closest('.viewZone');
        $('#table_addViewData tbody tr .enable').prop('checked', false);
        var type = viewZone.find('.viewZoneAttr[data-l1key=type]').value();
        if (type == 'graph') {
            $('#table_addViewDataHidden tbody').append($('#table_addViewData tr[data-type=widget]'));
            $('#table_addViewData tbody').append($('#table_addViewDataHidden tr[data-type=graph]'));
        }
        if (type == 'widget') {
            $('#table_addViewDataHidden tbody').append($('#table_addViewData tr[data-type=graph]'));
            $('#table_addViewData tbody').append($('#table_addViewDataHidden tr[data-type=widget]'));
        }
        $('#table_addViewData tbody tr div.option').hide();


        var viewDatas = [];
        viewZone.find('span.viewData').each(function() {
            viewDatas.push($(this));
        });
        for (var i = (viewDatas.length - 1); i >= 0; i--) {
            var viewData = $('#table_addViewData tbody tr[data-viewDataType=' + viewDatas[i].find('.viewDataAttr[data-l1key=type]').value() + '][data-link_id=' + viewDatas[i].find('.viewDataAttr[data-l1key=link_id]').value() + ']');
            if (viewData != null) {
                viewData.find('.enable').value(1);
                viewData.find('.option').show();
                viewDatas[i].find('.viewDataAttr').each(function() {
                    viewData.find('.viewDataOption[data-l1key=' + $(this).attr('data-l1key') + '][data-l2key=' + $(this).attr('data-l2key') + ']').value($(this).value());
                });
                setColorSelect(viewData.find('.viewDataOption[data-l1key=configuration][data-l2key=graphColor]'));
                $('#table_addViewData tbody').prepend(viewData);
            }
        }

        $("#md_addViewData").dialog('option', 'buttons', {
            "Annuler": function() {
                $(this).dialog("close");
            },
            "Valider": function() {
                var span = '';
                var tr = $('#table_addViewData tbody tr:first');
                while (tr.attr('data-link_id') != undefined) {
                    if (tr.find('.enable').is(':checked')) {
                        var viewData = tr.getValues('.viewDataOption');
                        viewData = viewData[0];
                        viewData.link_id = tr.attr('data-link_id');
                        viewData.name = '';
                        if (tr.find('.object_name').text() != '') {
                            viewData.name += '[' + tr.find('.object_name').text() + ']';
                        } else {
                            if (tr.find('.type').text() == 'Scénario') {
                                viewData.name += '[Scénario]';
                            }
                        }
                        viewData.name += '[' + tr.find('.name').text() + ']';
                        span += addServiceToviewZone(viewData);
                    }
                    tr = tr.next();
                }
                viewZone.find('span.viewData').remove();
                viewZone.find('.div_viewData').append(span);
                $(this).dialog('close');
            }
        });
        $("#md_addViewData").dialog('open');
    });

    $('#div_viewZones').delegate('.bt_removeViewData', 'click', function() {
        $(this).closest('span').remove();
    });

});

function setColorSelect(_select) {
    _select.css('background-color', _select.find('option:selected').val());
}

function editView() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "editView",
            name: $('#in_addViewName').value(),
            id: $('#in_addViewId').value(),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_addViewAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_addViewAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if ($('#in_addViewId').value() != '') {
                $('.li_view.active a').text($('#in_addViewName').value());
            } else {
                window.location.replace('index.php?v=d&p=view_edit&id=' + data.result.id);
            }
        }
    });
}


function printView(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "getView",
            id: _id,
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
            $('#div_viewZones').empty();
            var result = data.result;
            for (var i in result.viewZone) {
                var viewZone = result.viewZone[i];
                addEditviewZone(viewZone);
                for (var j in viewZone.viewData) {
                    var viewData = viewZone.viewData[j];
                    var span = addServiceToviewZone(viewData);
                    $('#div_viewZones .viewZone:last .div_viewData').append(span);
                }

            }
        }
    });
}

function addEditviewZone(_viewZone) {
    if (!isset(_viewZone.configuration)) {
        _viewZone.configuration = {};
    }
    if (init(_viewZone.emplacement) == '') {
        var id = $('#div_viewZones .viewZone').length;
        var div = '<div id="viewZone' + id + '" class="viewZone" data-toggle="tab">';
        div += '<legend style="height: 35px;"><span class="viewZoneAttr" data-l1key="name">' + init(_viewZone.name) + '</span>';
        div += '<a class="btn btn-danger btn-xs pull-right bt_removeviewZone"><i class="fa fa-trash-o"></i> Supprimer</a>';
        div += ' <a class="btn btn-warning btn-xs pull-right bt_editviewZone"><i class="fa fa-pencil"></i> Editer</a>';
        div += '<a class="btn btn-primary btn-xs pull-right bt_addViewData"><i class="fa fa-plus-circle"></i> Ajouter/Editer ' + init(_viewZone.type, 'widget') + '</a>';

        if (init(_viewZone.type, 'widget') == 'graph') {
            div += '<select class="pull-right viewZoneAttr form-control input-sm" data-l1key="configuration" data-l2key="dateRange" style="width : 200px;">';
            if (init(_viewZone.configuration.dateRange) == "30 min") {
                div += '<option value="30 min" selected>30min</option>';
            } else {
                div += '<option value="30 min">30min</option>';
            }
            if (init(_viewZone.configuration.dateRange) == "1 day") {
                div += '<option value="1 day" selected>Jour</option>';
            } else {
                div += '<option value="1 day">Jour</option>';
            }
            if (init(_viewZone.configuration.dateRange, '7 days') == "7 days") {
                div += '<option value="7 days" selected>Semaine</option>';
            } else {
                div += '<option value="7 days">Semaine</option>';
            }
            if (init(_viewZone.configuration.dateRange) == "1 month") {
                div += '<option value="1 month" selected>Mois</option>';
            } else {
                div += '<option value="1 month">Mois</option>';
            }
            if (init(_viewZone.configuration.dateRange) == "1 year") {
                div += '<option value="1 year" selected>Années</option>';
            } else {
                div += '<option value="1 year">Années</option>';
            }
            if (init(_viewZone.configuration.dateRange) == "all") {
                div += '<option value="all" selected>Tous</option>';
            } else {
                div += '<option value="all">Tous</option>';
            }
            div += '</select>';
        }

        div += '</legend>';
        div += '<input style="display : none;" class="viewZoneAttr" data-l1key="type" value="' + init(_viewZone.type) + '">';
        div += '<div class="div_viewData"></div>';
        div += '</div>';
        $('#div_viewZones').append(div);
        $('#viewZone' + id + ' .div_viewData').sortable({axis: "x", cursor: "move", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
    } else {
        $('#' + _viewZone.emplacement).find('.viewZoneAttr[data-l1key=name]').text(_viewZone.name);
    }
}

function addServiceToviewZone(_viewData) {
    if (!isset(_viewData.configuration) || _viewData.configuration == '') {
        _viewData.configuration = {};
    }
    var span = '<span class="label label-default viewData cursor" style="background-color : ' + init(_viewData.configuration.graphColor) + '; font-size : 1.1em;margin:4px;">';
    span += '<i class="fa fa-trash-o cursor bt_removeViewData"></i> ';
    span += init(_viewData.name);
    span += '<input class="viewDataAttr" data-l1key="link_id" value="' + init(_viewData.link_id) + '" style="display  : none;"/>';
    span += '<input class="viewDataAttr" data-l1key="type" value="' + init(_viewData.type) + '" style="display  : none;"/>';
    for (var i in _viewData.configuration) {
        span += '<input class="viewDataAttr" data-l1key="configuration" data-l2key="' + i + '" value="' + init(_viewData.configuration[i]) + '" style="display  : none;"/>';
    }
    span += '</span>';
    return span;
}

function removeView(_id) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'removeView',
            id: _id,
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
            window.location.reload();
        }
    });
}


function saveView(_view_id) {
    $.hideAlert();
    var viewZones = [];
    $('.viewZone').each(function() {
        viewZoneInfo = {};
        var viewZoneInfo = $(this).getValues('.viewZoneAttr');
        viewZoneInfo = viewZoneInfo[0];
        viewZoneInfo.viewData = $(this).find('span.viewData').getValues('.viewDataAttr');
        viewZones.push(viewZoneInfo);
    });

    $.ajax({
        type: 'POST',
        url: 'core/ajax/view.ajax.php',
        data: {
            action: 'saveView',
            view_id: _view_id,
            viewZones: json_encode(viewZones),
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
            $('#div_alert').showAlert({message: 'Modification enregistré', level: 'success'});
        }
    });
}