var chart = [];
var noChart = [];
var colorChart = [];

function printEqLogicviewZone(_viewZone_id) {
    $.mobile.loading('show', {
        text: 'Chargement...',
        textVisible: true,
    });
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/view.ajax.php", // url du fichier php
        data: {
            action: "getEqLogicviewZone",
            viewZone_id: _viewZone_id,
            version: 'mobile'
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
            var result = data.result;
            $('#div_viewZone' + _viewZone_id).empty();
            var html = '';
            for (var i in result.viewData) {
                html += result.viewData[i].html;
            }
            $('#div_viewZone' + _viewZone_id).append(html);
            $('#div_viewZone' + _viewZone_id).trigger("create");
        }
    })
}
