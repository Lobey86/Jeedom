
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


    $("#md_browseScriptFile").dialog({
        autoOpen: false,
        modal: true,
        height: (jQuery(window).height() - 150),
    });

    $("#table_cmd tbody").delegate(".browseScriptFile", 'click', function(event) {
        var tr = $(this).closest('tr');
        $("#md_browseScriptFile").dialog('open');
        $('#div_browseScriptFileTree').fileTree({
            root: '/',
            script: '3rdparty/jquery.fileTree/jqueryFileTree.php',
            folderEvent: 'click'
        }, function(file) {
            $("#md_browseScriptFile").dialog('close');
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').text(file);
        });
    });

    $("#md_editScriptFile").dialog({
        autoOpen: false,
        modal: true,
        height: (jQuery(window).height() - 150),
        width: (jQuery(window).width() - 150)
    });

    $("#table_cmd tbody").delegate(".editScriptFile", 'click', function(event) {
        var tr = $(this).closest('tr');
        $('#ta_editScriptFile').text('');
        var path = tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val();
        if (path.indexOf(' ') > 0) {
            path = path.substr(0, path.indexOf(' '));
        }
        var data = loadScriptFile(path);
        if (data === false) {
            return;
        }
        if (editor == null) {
            setTimeout(function() {
                editor = CodeMirror.fromTextArea(document.getElementById("ta_editScriptFile"), {
                    lineNumbers: true,
                    mode: data.mode,
                    matchBrackets: true
                });
            }, 1);
        } else {
            editor.setOption("mode", data.mode);
        }

        $('#ta_editScriptFile').val(data.content);
        $("#md_editScriptFile").dialog('option', 'buttons', {
            "Annuler": function() {
                $(this).dialog("close");
            },
            "Enregistrer": function() {
                if (saveScriptFile(path, editor.getValue())) {
                    $(this).dialog("close");
                }
            }
        });
        $("#md_editScriptFile").dialog('open');
    });


    $("#table_cmd tbody").delegate(".newScriptFile", 'click', function(event) {
        var tr = $(this).closest('tr');
        $('#md_newUserScript').modal('show');
        $('#bt_addUserNewScript').undelegate().unbind();
        $("#bt_addUserNewScript").on('click', function(event) {
            var path = addUserScript($('#in_newUserScriptName').value());
            if (path !== false) {
                tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val(path);
                $('#md_newUserScript').modal('hide');
                tr.find('.editScriptFile').click();
            }
        });
    });

    $("#table_cmd tbody").delegate(".removeScriptFile", 'click', function(event) {
        var tr = $(this).closest('tr');
        var path = tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val();
        if (path.indexOf(' ') > 0) {
            path = path.substr(0, path.indexOf(' '));
        }

        $.hideAlert();
        bootbox.confirm('Etes-vous sûr de vouloir supprimer le script : <span style="font-weight: bold ;">' + path + '</span> ?', function(result) {
            if (result) {
                tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').val('');
            }
        });
    });


    $("#table_cmd tbody").delegate(".listScript", 'click', function(event) {
        $('.description').hide();
        $('.use').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $('#sel_addPreConfigScript').value()).show();
        $('.use.' + $('#sel_addPreConfigScript').value()).show();
        $('.version.' + $('#sel_addPreConfigScript').value()).show();
        $('.required.' + $('#sel_addPreConfigScript').value()).show();
        $('#md_addPreConfigScript').modal('show');
        $('#bt_addPreConfigSave').undelegate().unbind();
        var tr = $(this).closest('tr');
        $("#bt_addPreConfigSave").on('click', function(event) {
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=request]').value($('#sel_addPreConfigScript option:selected').attr('data-path') + $('#sel_addPreConfigScript option:selected').attr('data-argv'));
            tr.find('.cmdAttr[data-l1key=type]').value($('#sel_addPreConfigScript option:selected').attr('data-type'));
            tr.find('.cmdAttr[data-l1key=configuration][data-l2key=requestType]').value($('#sel_addPreConfigScript option:selected').attr('data-requestType'));
            cmd.changeType(tr.find('.type select'), $('#sel_addPreConfigScript option:selected').attr('data-subType'));
            $('#md_addPreConfigScript').modal('hide');
        });
    });

    $("#sel_addPreConfigScript").on('change', function(event) {
        $('.description').hide();
        $('.use').hide();
        $('.version').hide();
        $('.required').hide();
        $('.description.' + $(this).value()).show();
        $('.use.' + $(this).value()).show();
        $('.version.' + $(this).value()).show();
        $('.required.' + $(this).value()).show();
    });
    
    $("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
});


function loadScriptFile(_path) {
    $.hideAlert();
    var result = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/script/core/ajax/script.ajax.php", // url du fichier php
        data: {
            action: "getScriptContent",
            path: _path,
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return false;
            }
            result = data.result;
            switch (result.extension) {
                case 'php' :
                    result.mode = 'text/x-php';
                    break;
                case 'sh' :
                    result.mode = 'shell';
                    break;
                case 'pl' :
                    result.mode = 'text/x-php';
                    break;
                case 'py' :
                    result.mode = 'pyhton';
                    break;
                case 'rb' :
                    result.mode = 'text/x-ruby';
                    break;
                default :
                    result.mode = 'text/x-php';
                    break;
            }
        }
    });
    return result;
}

function saveScriptFile(_path, _content) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/script/core/ajax/script.ajax.php", // url du fichier php
        data: {
            action: "saveScriptContent",
            path: _path,
            content: _content,
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_editScriptFileAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_editScriptFileAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            success = true;
            $('#div_alert').showAlert({message: 'Script sauvegardé', level: 'success'});
        }
    });
    return success;
}

function addUserScript(_name) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/script/core/ajax/script.ajax.php", // url du fichier php
        data: {
            action: "addUserScript",
            name: _name,
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_newUserScriptAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_newUserScriptAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            success = data.result;
        }
    });
    return success;
}

function removeScript(_path) {
    $.hideAlert();
    var success = false;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "plugins/script/core/ajax/script.ajax.php", // url du fichier php
        data: {
            action: "removeScript",
            path: _path,
        },
        dataType: 'json',
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_newUserScriptAlert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_newUserScriptAlert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: 'Script supprimé', level: 'success'});
            success = true;
        }
    });
    return success;
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    var selRequestType = '<select style="width : 90px;" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="requestType">';
    selRequestType += '<option value="script">Script</option>';
    selRequestType += '<option value="http">Http</option>';
    selRequestType += '</select>';

    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td><input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id"  style="display : none;"></td>';
    tr += '<td class="requestType" type="' + init(_cmd.configuration.requestType) + '" >' + selRequestType + '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td><textarea style="height : 95px;" class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="request"></textarea>';

    tr += '<div class="form-group">';
    tr += '<div class="col-lg-3">';
    tr += '<a class="btn btn-default listScript cursor form-control input-sm" style="margin-top : 5px;"><i class="fa fa-list-alt"></i> Script prédefini</a>';
    tr += '</div>';
    tr += '<div class="col-lg-2">';
    tr += '<a class="btn btn-default browseScriptFile cursor form-control input-sm" style="margin-top : 5px;"><i class="fa fa-folder-open"></i> Parcourir</a>';
    tr += '</div>';
    tr += '<div class="col-lg-2">';
    tr += '<a class="btn btn-default editScriptFile cursor form-control input-sm" style="margin-top : 5px;"><i class="fa fa-edit"></i> Editer</a>';
    tr += '</div>';
    tr += '<div class="col-lg-2">';
    tr += '<a class="btn btn-default newScriptFile cursor form-control input-sm" style="margin-top : 5px;"><i class="fa fa-file-o"></i> Nouveau</a>';
    tr += '</div>';
    tr += '<div class="col-lg-2">';
    tr += '<a class="btn btn-default removeScriptFile cursor form-control input-sm" style="margin-top : 5px;"><i class="fa fa-trash-o"></i> Supprimer</a>';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control tooltips input-sm" data-l1key="unite"  style="width : 100px;" placeholder="Unité" title="Unité">';
    tr += '<input style="width : 100px;" class="tooltips cmdAttr form-control input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="Lifetime cache" title="Lifetime cache">';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="Min" title="Min"> ';
    tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="Max" title="Max">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" /> Historiser<br/></span>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="cache" data-l2key="enable" checked /> Autoriser memcache</span>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> Tester</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';

    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');

    if (isset(_cmd.configuration.requestType)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=requestType]').value(init(_cmd.configuration.requestType));
    }

    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    cmd.changeType($('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]'), init(_cmd.subType));
    activateTooltips();
}