
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
    $("#bt_saveBackup").on('click', function(event) {
        $.hideAlert();
        var configuration = $('#backup').getValues('.configKey');
        jeedom.config.save(configuration[0], 'core', function() {
            var configuration = $('#backup').getValues('.configKey');
            jeedom.config.load(configuration[0], 'core', function(data) {
                $('#backup').setValues(data, '.configKey');
                modifyWithoutSave = false;
                $('#div_alert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            });
        });
    });

    $("#bt_saveUser").on('click', function(event) {
        var users = $('#table_user tbody tr').getValues('.userAttr');
        saveUser(users);
    });

    $("#bt_backupJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('{{Etes-vous sûr de vouloir faire une sauvegarde de Jeedom ? Une fois lancée cette opération ne peut être annulée}}', function(result) {
            if (result) {

                el.find('.fa-refresh').show();
                jeedom.backup.backup(function() {
                    getJeedomLog(1, 'backup');
                });
            }
        });
    });

    $("#bt_restoreJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('{{Etes-vous sûr de vouloir restaurer Jeedom avec}} <b>' + $('#sel_restoreBackup option:selected').text() + '</b> ? {{Une fois lancée cette opération ne peut être annulée}}', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                jeedom.backup.restoreLocal($('#sel_restoreBackup').value(), function() {
                    getJeedomLog(1, 'restore');
                });
            }
        });
    });

    $("#bt_removeBackup").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('{{Etes-vous sûr de vouloir supprimer la sauvegarde}} <b>' + $('#sel_restoreBackup option:selected').text() + '</b> ?', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                jeedom.backup.remove($('#sel_restoreBackup').value(), function() {
                    updateListBackup();
                    $('#div_alert').showAlert({message: '{{Sauvegarde supprimé avec succès}}', level: 'success'});
                });
            }
        });
    });

    $('#bt_downloadBackup').on('click', function() {
        window.open('core/php/downloadFile.php?pathfile=backup/' + $('#sel_restoreBackup option:selected').text(), "_blank", null);
    });

    $("#bt_restoreCloudJeedom").on('click', function(event) {
        var el = $(this);
        bootbox.confirm('{{Etes-vous sûr de vouloir restaurer Jeedom avec la sauvergarde Cloud}} <b>' + $('#sel_restoreCloudBackup option:selected').text() + '</b> ? {{Une fois lancée cette opération ne peut être annulée}}', function(result) {
            if (result) {
                el.find('.fa-refresh').show();
                jeedom.backup.restoreCloud($('#sel_restoreCloudBackup').value(), function() {
                    getJeedomLog(1, 'restore');
                });
            }
        });
    });

    var configuration = $('#backup').getValues('.configKey');
    jeedom.config.load(configuration[0], 'core', function(data) {
        $('#backup').setValues(data, '.configKey');
    });
    updateListBackup();

    $('body').delegate('.configKey', 'change', function() {
        modifyWithoutSave = true;
    });
});
/********************Log************************/

function getJeedomLog(_autoUpdate, _log) {
    $.ajax({
        type: 'POST',
        url: 'core/ajax/log.ajax.php',
        data: {
            action: 'get',
            logfile: _log,
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var log = '';
            for (var i in data.result.reverse()) {
                log += data.result[i][2];
                if ($.trim(data.result[i][2]) == '[END ' + _log.toUpperCase() + ' SUCCESS]') {
                    $('#div_alert').showAlert({message: '{{L\'opération est réussie}}', level: 'success'});
                    _autoUpdate = 0;
                }
                if ($.trim(data.result[i][2]) == '[END ' + _log.toUpperCase() + ' ERROR]') {
                    $('#div_alert').showAlert({message: '{{L\'opération a échoué}}', level: 'danger'});
                    _autoUpdate = 0;
                }
            }
            $('#pre_' + _log + 'Info').text(log);
            if (init(_autoUpdate, 0) == 1) {
                setTimeout(function() {
                    getJeedomLog(_autoUpdate, _log)
                }, 1000);
            } else {
                $('#bt_' + _log + 'Jeedom .fa-refresh').hide();
                $('.bt_' + _log + 'Jeedom .fa-refresh').hide();
                updateListBackup();
            }
        }
    });
}

function updateListBackup() {
    jeedom.backup.list(function(data) {
        var options = '';
        for (var i in data) {
            options += '<option value="' + i + '">' + data[i] + '</option>';
        }
        $('#sel_restoreBackup').html(options);
    });
}