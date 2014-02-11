
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
    editor = null;

    $(".li_widget").on('click', function(event) {
        $('.widget').show();
        $('.li_widget').removeClass('active');
        $(this).addClass('active');
        printWidget($(this).attr('data-path'));
        return false;
    });

    if (getUrlVars('saveSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Sauvegarde effectué avec succès', level: 'success'});
    }

    if (getUrlVars('removeSuccessFull') == 1) {
        $('#div_alert').showAlert({message: 'Suppression effectué avec succès', level: 'success'});
    }

    $('.widgetAction[data-action=save]').on('click', function() {
        saveWidget();
    });

    $('.widgetAction[data-action=remove]').on('click', function() {
        bootbox.confirm('Etes-vous sûr de vouloir supprimer le widget <span style="font-weight: bold ;">' + $('.widgetAttr[data-l1key=name]').value() + '</span> ?', function(result) {
            if (result) {
                removeWidget($('.widgetAttr[data-l1key=path]').value());
            }
        });
    });

    $('.widgetAction[data-action=add]').on('click', function() {
        $.hideAlert();
        $('#md_addWidget .widgetAttr').value('');
        $('#md_addWidget').modal('show');
    });

    $('#bt_applyWidget').on('click', function() {
        $('#md_modal').dialog({title: "Appliquer widget"});
        $('#md_modal').load('index.php?v=d&plugin=widget&modal=widget.apply&path=' + $('.widgetAttr[data-l1key=path]').value()).dialog('open');
    });

    $("#md_addWidget .widgetAction[data-action=newAdd]").on('click', function() {
        $('#md_addWidget').modal('hide');
        addWidget($('#md_addWidget .widgetAttr[data-l1key=name]').value());
    });


    if (getUrlVars('id') != '') {
        if ($('#ul_widget .li_widget[data-path="' + getUrlVars('id') + '"]').length != 0) {
            $('#ul_widget .li_widget[data-path="' + getUrlVars('id') + '"]').click();
        } else {
            $('#ul_widget .li_widget:first').click();
        }
    } else {
        $('#ul_widget .li_widget:first').click();
    }
});

function printWidget(_path) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/widget/core/ajax/widget.ajax.php", // url du fichier php
        data: {
            action: "get",
            path: _path
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
            $('.widget').setValues(data.result, '.widgetAttr');
            if (editor == null) {
                if (isset(data.result.content)) {
                    $('#ta_script').val(data.result.content);
                }
                setTimeout(function() {
                    editor = CodeMirror.fromTextArea(document.getElementById("ta_widgetContent"), {
                        lineNumbers: true,
                        mode: "text/html",
                        matchBrackets: true,
                        viewportMargin: Infinity
                    });
                }, 1);
            } else {
                if (isset(data.result.content)) {
                    editor.setValue(data.result.content);
                }
            }
            activateTooltips();
            $('#div_widgetResult').empty();
            $('#div_widgetResult').append('<iframe src="index.php?v=d&plugin=widget&modal=widget.result&path=' + data.result.path + '" frameBorder="0"></iframe>');
        }
    });
}

function saveWidget() {
    $.hideAlert();
    var widget = $('.widget').getValues('.widgetAttr');
    widget = widget[0];
    if (editor != null) {
        widget.content = editor.getValue();
    }
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/widget/core/ajax/widget.ajax.php", // url du fichier php
        data: {
            action: "save",
            widget: json_encode(widget),
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
            url += 'id=' + data.result.path + '&saveSuccessFull=1';
            window.location.href = url;
        }
    });
}


function addWidget(_name) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/widget/core/ajax/widget.ajax.php", // url du fichier php
        data: {
            action: "add",
            name: _name,
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
            url += 'id=' + data.result.path + '&saveSuccessFull=1';
            window.location.href = url;
        }
    });
}

function removeWidget(_path) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/widget/core/ajax/widget.ajax.php", // url du fichier php
        data: {
            action: "remove",
            path: _path,
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
                if (i != 'id' && i != 'removeSuccessFull' && i != 'saveSuccessFull') {
                    url += i + '=' + vars[i].replace('#', '') + '&';
                }
            }
            url += 'removeSuccessFull=1';
            window.location.href = url;
        }
    });
}