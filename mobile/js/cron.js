$(document).on('pagecontainershow',function(){
    printCron();
    $("#bt_refreshCron").on('click', function() {
        printCron();
        $(".rightpanel").panel().panel("close");
    });

    $("#table_cron").delegate(".stop", 'click', function() {
        changeStateCron('stop', $(this).closest('tr').attr('id'));
    });

    $("#table_cron").delegate(".start", 'click', function() {
        changeStateCron('start', $(this).closest('tr').attr('id'));
    });

});

function changeStateCron(_state, _id) {
    $.hideAlert();
    $.ajax({
        type: 'POST',
        url: 'core/ajax/cron.ajax.php',
        data: {
            action: _state,
            id: _id
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
            printCron();
        }
    });
}

function printCron() {
    $.hideAlert();
    $.ajax({
        type: 'POST',
        url: 'core/ajax/cron.ajax.php',
        data: {
            action: 'all'
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
            $('#table_cron tbody').empty();
            for (var i in data.result.crons) {
                addCron(data.result.crons[i]);
            }
            $('#table_cron').table( "refresh" );
        }
    });
}

function addCron(_cron) {
    $.hideAlert();
    var tr = '<tr id="' + init(_cron.id) + '">';
    tr += '<td>';
    if (init(_cron.state) == 'run') {
        tr += '<a class="cursor stop"><i class="fa fa-stop"></i></a>';
    }
    if (init(_cron.state) != '' && init(_cron.state) != 'starting' && init(_cron.state) != 'run' && init(_cron.state) != 'stoping') {
        tr += '<a class="cursor start"><i class="fa fa-play"></i></a>';
    }
    tr += '</td>';
    tr += '<td class="enable"><center>';
    tr += '<input class="cronAttr" data-l1key="id" hidden/>';
    tr += '<input type="checkbox" class="cronAttr" data-l1key="enable" checked disabled/>';
    tr += '</center></td>';
    tr += '<td>';
    tr += init(_cron.server);
    tr += '</td>';
    tr += '<td>';
    tr += init(_cron.pid);
    tr += '</td>';
    tr += '<td class="deamons"><center>';
    tr += '<input type="checkbox" class="cronAttr" data-l1key="deamon" checked disabled/>';
    tr += '</center></td>';
    tr += '<td class="class"><span class="form-control cronAttr" data-l1key="class" ></span></td>';
    tr += '<td class="function"><span class="form-control cronAttr" data-l1key="function" /></span></td>';
    tr += '<td class="lastRun">';
    tr += init(_cron.lastRun);
    tr += '</td>';
    tr += '<td class="duration">';
    tr += '<span >' + init(_cron.duration) + '</span>';
    tr += '</td>';
    tr += '<td class="state">';
    tr += '<span>' + init(_cron.state) + '</span>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cron').append(tr);
    $('#table_cron tbody tr:last').setValues(_cron, '.cronAttr');
}
