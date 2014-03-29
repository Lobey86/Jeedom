$(document).on('pagecontainershow', function() {

    $("#bt_clearMessage").on('click', function(event) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/message.ajax.php", // url du fichier php
            data: {
                action: "clearMessage",
                plugin: plugin
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('.ui-page-active #div_alert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('.ui-page-active #div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                window.location.reload();
            }
        });
    });


    $("#table_message").delegate(".removeMessage", 'click', function(event) {
        var tr = $(this).closest('tr');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/message.ajax.php", // url du fichier php
            data: {
                action: "removeMessage",
                id: tr.attr('data-message_id'),
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('.ui-page-active #div_alert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('.ui-page-active #div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                tr.remove();
            }
        });
    });
});


function removeMessage(_id) {

}