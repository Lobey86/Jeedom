/***************Fonction d'initialisation*********************/
$(function() {
    $(document).ajaxStart(function() {
        $.showLoading();
    });
    $(document).ajaxStop(function() {
        $.hideLoading();
    });
    initApplication();

    $('body').delegate('a.link', 'click', function() {
        modal(false);
        panel(false);
        $('#panel_left').panel('close');
        page("index.php?v=m&p=" + $(this).attr('data-page'), $(this).attr('data-title'), $(this).attr('data-option'));
    });
});


function initApplication() {

    modal(false);
    panel(false);
    $('#panel_left').panel('close');
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/jeedom.ajax.php", // url du fichier php
        data: {
            action: "getInfoApplication"
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                if (data.code == -1234) {
                    modal('index.php?v=m&modal=login');
                } else {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                }
                return;
            } else {
                /*************Initialisation environement********************/
                nodeJsKey = data.result.nodeJsKey;
                user_id = data.result.user_id;
                $('#span_version').append(data.result.version);
                $('#span_year').append(data.result.year);
                var include = [
                    'core/php/getJS.php?file=core/js/cmd.class.js',
                    'core/php/getJS.php?file=core/js/eqLogic.class.js',
                    'core/php/getJS.php?file=core/js/jeedom.class.js',
                    'core/php/getJS.php?file=core/js/object.class.js',
                    'core/php/getJS.php?file=core/js/scenario.class.js',
                    'core/php/getJS.php?file=core/js/plugin.class.js',
                    'core/php/getJS.php?file=core/js/message.class.js',
                    'core/php/getJS.php?file=core/js/view.class.js',
                    'core/php/getJS.php?file=core/js/core.js',
                ];
                $.include(include, function() {
                    refreshMessageNumber();
                    page("index.php?v=m&p=home", 'Acceuil');
                });
            }
        }
    });
}

function page(_page, _title, _option) {
    $('#page').empty();

    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
        if (options.dataType == 'script' || originalOptions.dataType == 'script') {
            options.cache = true;
        }
    });

    $('#page').load(_page, function() {
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            if (options.dataType == 'script' || originalOptions.dataType == 'script') {
                options.cache = false;
            }
        });
        if (isset(_title)) {
            $('#pageTitle').empty().append(_title);
        }
        $('#page').trigger('create');
        if (isset(_option)) {
            initLocalPage(_option);
        } else {
            initLocalPage();
        }
    });
    $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
        if (options.dataType == 'script' || originalOptions.dataType == 'script') {
            options.cache = false;
        }
    });
}

function modal(_name) {
    if (_name === false) {
        $('#div_popup').empty();
        $("#div_popup").popup("close");
        $("[data-role=popup]").popup("close");
    } else {
        $('#div_popup').empty();
        $('#div_popup').load(_name, function() {
            $('#div_popup').trigger('create');
            $("#div_popup").popup("open");

        });
    }
}

function panel(_content) {
    if (_content === false) {
        $('#panel_right').empty();
        $('#bt_panel_right').hide();
        $('#panel_right').panel('close');
    } else {
        $('#panel_right').append(_content).trigger('create');
        $('#bt_panel_right').show();
    }
}

function refreshMessageNumber() {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/message.ajax.php", // url du fichier php
        data: {
            action: "nbMessage"
        },
        dataType: 'json',
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('.ui-page-active #div_alert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('.ui-page-active {div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('.span_nbMessage').html(data.result);
        }
    });
}

function notify(_title, _text) {
    if (_title == '' && _text == '') {
        return true;
    }
    $('#div_alert').html("<center><b>" + _title + "</b></center>" + _text).popup("open", {y: 0});
    setTimeout(function() {
        $('#div_alert').popup("close");
    }, 1000)
}