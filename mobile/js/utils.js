$(document).on('pagecontainershow', function() {

    /*********************Gestion de l'heure********************************/
    setInterval(function() {
        var date = new Date();
        date.setTime(date.getTime() + clientServerDiffDatetime);
        var hour = date.getHours();
        var minute = date.getMinutes();
        var seconde = date.getSeconds();
        var horloge = (hour < 10) ? '0' + hour : hour;
        horloge += ':';
        horloge += (minute < 10) ? '0' + minute : minute;
        horloge += ':';
        horloge += (seconde < 10) ? '0' + seconde : seconde;
        $('.horloge').text(horloge);
    }, 1000);

    // Ajax Loading Screen
    $(document).ajaxStart(function() {
        $.showLoading();
    });
    $(document).ajaxStop(function() {
        $.hideLoading();
    });


    if ($('.ui-page-active .rightpanel').length > 0) {
        if (window.innerWidth > 800) {
            setTimeout(function() {
                $(".rightpanel").panel("open")
            }, 10);
        }
    } else {
        $('.ui-page-active .bt_rightpanel').remove();
        if (window.innerWidth > 800) {
            setTimeout(function() {
                $(".leftpanel").panel("open")
            }, 10);
        }
    }
});


(function(win) {
    var doc = win.document;
    if (!win.navigator.standalone && !location.hash && win.addEventListener) {
        win.scrollTo(0, 1);
        var scrollTop = 1,
                getScrollTop = function() {
                    return win.pageYOffset || doc.compatMode === "CSS1Compat" && doc.documentElement.scrollTop || doc.body.scrollTop || 0;
                },
                bodycheck = setInterval(function() {
                    if (doc.body) {
                        clearInterval(bodycheck);
                        scrollTop = getScrollTop();
                        win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                    }
                }, 15);
        win.addEventListener("load", function() {
            setTimeout(function() {
                if (getScrollTop() < 20) {
                    win.scrollTo(0, scrollTop === 1 ? 0 : 1);
                }
            }, 0);
        }, false);
    }
})(this);

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
            $('.ui-page-active #span_nbMessage').html(data.result);
            $(document).ajaxStart(function() {
                $.mobile.loading('show', {
                    text: 'Chargement...',
                    textVisible: true,
                });
            });
        }
    });
}

function notify(_title, _text) {
    if (_title == '' && _text == '') {
        return true;
    }
    $('.ui-page-active #div_alert').html("<center><b>" + _title + "</b></center>" + _text).popup("open", {y: 0});
    setTimeout(function() {
        $('.ui-page-active #div_alert').popup("close");
    }, 1000)
}
