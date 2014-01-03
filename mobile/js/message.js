$(function() {

    $("#bt_clearMessage").on('click', function(event) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/message.ajax.php", // url du fichier php
            data: {
                action: "clearMessage",
                module: module
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                    return;
                }
                window.location.reload();
            }
        });
    });


    $("#table_message").delegate(".removeMessage", 'click', function(event) {
        removeMessage($(this).closest('tr').attr('message_id'))
    });
});


function removeMessage(_id) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/message.ajax.php", // url du fichier php
        data: {
            action: "removeMessage",
            id: _id
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message:  data.result,level: 'danger'});
                return;
            }
            window.location.reload();
        }
    });
}