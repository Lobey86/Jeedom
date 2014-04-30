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

    $('#bt_logout').on('click', function() {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/user.ajax.php", // url du fichier php
            data: {
                action: "logout",
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_alert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                localStorage.setItem("deviceKey", '');
                initApplication();
            }
        });
    });
});

function initExpertMode() {
    if (expertMode == 1) {
        $('.expertModeDisable').attr('disabled', true);
        $('.expertModeHidden').show();
    } else {
        $('.expertModeDisable').attr('disabled', false);
        $('.expertModeHidden').hide();
    }
}


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
                    if (localStorage.getItem("deviceKey") != '' && localStorage.getItem("deviceKey") != undefined && localStorage.getItem("deviceKey") != null) {
                        autoLogin(localStorage.getItem("deviceKey"));
                    } else {
                        modal('index.php?v=m&modal=login');
                    }
                } else {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                }
                return;
            } else {
                /*************Initialisation environement********************/
                nodeJsKey = data.result.nodeJsKey;
                user_id = data.result.user_id;
                expertMode = data.result.expertMode;
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
    $('.ui-popup').popup('close');
    $('#page').empty();
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
        var functionName = _page.substring(_page.indexOf('p=') + 2);
        if (functionName.indexOf('&') > 0) {
            functionName = functionName.substring(0, functionName.indexOf('&'));
        }
        functionName = 'init' + functionName.charAt(0).toUpperCase() + functionName.substring(1).toLowerCase();
        if ('function' == typeof (window[functionName])) {
            if (isset(_option)) {
                window[functionName](_option);
            } else {
                window[functionName](_option);
            }
        }
        initExpertMode();
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
        $('#panel_right').empty().trigger('create');
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

function autoLogin(_key) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/user.ajax.php", // url du fichier php
        data: {
            action: "login",
            key: _key
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error, $('#div_alert'));
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                localStorage.setItem("deviceKey", '');
                initApplication();
                return;
            }
            localStorage.setItem("deviceKey", data.result.deviceKey);
            initApplication();
        }
    });
}

function getDeviceType() {
    var result = {};
    result.type = 'dekstop';
    if (navigator.userAgent.match(/(android)/gi)) {
        result.type = 'phone';
        if (screen.width > 899 || screen.height > 899) {
            result.type = 'tablet';
        }
    }
    if (navigator.userAgent.match(/(phone)/gi)) {
        result.type = 'phone';
    }
    if (navigator.userAgent.match(/(Iphone)/gi)) {
        result.type = 'phone';
    }
    if (navigator.userAgent.match(/(Ipad)/gi)) {
        result.type = 'tablet';
    }
    result.bSize = 150;
    if (result.type == 'phone') {
        result.bSize = (screen.width / 2) - 28;
        if (result.bSize > 176) {
            result.bSize = 176;
        }
        if (result.bSize < 120) {
            result.bSize = 120;
        }
    }
    result.bSize2 = result.bSize * 2 + 12;
    return result;
}

function setTileSize(_filter, _fixWidthSize, _fixHeightSize) {
    var deviceInfo = getDeviceType();
    $(_filter).each(function() {
        var width = $(this).width();
        var height = $(this).height();
        if (init(_fixWidthSize, '') != '') {
            $(this).width((deviceInfo.bSize + 6) * _fixWidthSize);
        } else {
            if (width <= deviceInfo.bSize) {
                $(this).width(deviceInfo.bSize);
            } else {
                $(this).width(deviceInfo.bSize2);
            }
        }
        if (init(_fixHeightSize, '') != '') {
            $(this).width((deviceInfo.bSize + 6) * _fixHeightSize);
        } else {
            if (height <= deviceInfo.bSize) {
                $(this).height(deviceInfo.bSize);
            } else {
                $(this).height(deviceInfo.bSize2);
            }
        }
        var verticalAlign = $(this).find('.vertical-align');
        height = $(this).height();
        var vAlignHeight = verticalAlign.height();
        verticalAlign.css('position', 'relative');
        verticalAlign.css('top', (((height / 2) - (vAlignHeight / 2)) - 20) + 'px');
    });
}