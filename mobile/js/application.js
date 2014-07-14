/***************Fonction d'initialisation*********************/
$(function() {
    $.mobile.orientationChangeEnabled = false;

    $(window).on("orientationchange", function(event) {
        deviceInfo = getDeviceType();
    });

    initApplication();

    $('body').delegate('a.link', 'click', function() {
        modal(false);
        panel(false);
        page($(this).attr('data-page'), $(this).attr('data-title'), $(this).attr('data-option'), $(this).attr('data-plugin'));
    });

    var webappCache = window.applicationCache;
    webappCache.addEventListener("updateready", updateCache, false);
    webappCache.update();

    function updateCache() {
        webappCache.swapCache();
        if (confirm("Une nouvelle version de Jeedom vient d'etre installée. Voulez-vous rafraichir pour l'utiliser maintenant ?")) {
            window.location.reload();
        }
    }
});

function initExpertMode() {
    if (expertMode == 1) {
        $('.expertModeDisable').attr('disabled', true);
        $('.expertModeVisible').show();
    } else {
        $('.expertModeDisable').attr('disabled', false);
        $('.expertModeVisible').hide();
    }
}


function initApplication(_reinit) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: 'POST', // methode de transmission des données au fichier php
        url: 'core/ajax/jeedom.ajax.php', // url du fichier php
        data: {
            action: 'getInfoApplication'
        },
        dataType: 'json',
        error: function(request, status, error) {
            if (confirm('Erreur de communication.Etes-vous connecté à internet? Voulez-vous ressayer ?')) {
                window.location.reload();
            }
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                modal(false);
                panel(false);
                if (data.code == -1234) {
                    if (localStorage.getItem("deviceKey") != '' && localStorage.getItem("deviceKey") != undefined && localStorage.getItem("deviceKey") != null) {
                        jeedom.user.logByKey({
                            key: localStorage.getItem("deviceKey"),
                            success: function() {
                                initApplication();
                            }
                        });
                        $.hideLoading();
                    } else {
                        $.hideLoading();
                        page('connection', 'Connexion');
                    }
                    return;
                } else {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                }
                return;
            } else {
                if (init(_reinit, false) == false) {
                    modal(false);
                    panel(false);
                    /*************Initialisation environement********************/
                    nodeJsKey = data.result.nodeJsKey;
                    user_id = data.result.user_id;
                    plugins = data.result.plugins;
                    deviceInfo = getDeviceType();
                    userProfils = data.result.userProfils;
                    expertMode = userProfils.expertMode;
                    $.get("core/php/icon.inc.php", function(data) {
                        $("head").append(data);
                        var include = [
                            'core/js/core.js',
                        ];
                        $.showLoading();
                        $.include(include, function() {
                            jeedom.object.prefetch({id: 'all', version: 'mobile'});
                            jeedom.view.prefetch({id: 'all', version: 'mobile'});
                            page("home", 'Accueil');
                            $.hideLoading();
                        });
                    });
                }
            }
        }
    });
}

function page(_page, _title, _option, _plugin) {
    $('.ui-popup').popup('close');
    $('#page').empty();
    if (isset(_title)) {
        $('#pageTitle').empty().append(_title);
    }
    if (_page == 'connection') {
        var page = 'index.php?v=m&p=' + _page;
        $('#page').load(page, function() {
            $('#page').trigger('create');
        });
        return;
    }
    jeedom.user.isConnect({
        success: function(result) {
            if (!result) {
                initApplication(true);
                return;
            }
            var page = 'index.php?v=m&p=' + _page;
            if (init(_plugin) != '') {
                page += '&m=' + _plugin;
            }
            $('#page').load(page, function() {
                $('#page').trigger('create');
                var functionName = '';
                if (init(_plugin) != '') {
                    functionName = 'init' + _plugin.charAt(0).toUpperCase() + _plugin.substring(1).toLowerCase() + _page.charAt(0).toUpperCase() + _page.substring(1).toLowerCase();
                } else {
                    functionName = 'init' + _page.charAt(0).toUpperCase() + _page.substring(1).toLowerCase();
                }
                if ('function' == typeof (window[functionName])) {
                    if (init(_option) != '') {
                        window[functionName](_option);
                    } else {
                        window[functionName]();
                    }
                }
            });
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
        $('#panel_right').empty().trigger('create');
        $('#bt_panel_right').hide();
        $('#panel_right').panel('close');
    } else {
        $('#panel_right').append(_content).trigger('create');
        $('#bt_panel_right').show();
    }
}

function refreshMessageNumber() {
    jeedom.message.number({
        success: function(_number) {
            $('.span_nbMessage').html(_number);
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

function getDeviceType() {
    var result = {};
    result.type = 'dekstop';
    result.width = $('#pagecontainer').width();
    if (navigator.userAgent.match(/(android)/gi)) {
        result.width = screen.width;
        result.type = 'phone';
        if ($('#pagecontainer').width() > 899) {
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
        var ori = window.orientation;
        if (ori == 90 || ori == -90) {
            result.bSize = (result.width / 3) - 30;
        } else {
            result.bSize = (result.width / 2) - 30;
        }
    }
    return result;
}

function setTileSize(_filter) {
    $(_filter).each(function() {
        $(this).width(deviceInfo.bSize);
    });
}

function init(_value, _default) {
    if (!isset(_default)) {
        _default = '';
    }
    if (!isset(_value)) {
        return _default;
    }
    return _value;
}