function initView(_view_id) {
    jeedom.view.all(function(views) {
        var li = ' <ul data-role="listview">';
        for (var i in views) {
            li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '</a></li>'
        }
        li += '</ul>';
        panel(li);
    });
    if (isset(_view_id) && is_numeric(_view_id)) {
        jeedom.history.chart = [];
        jeedom.view.toHtml(_view_id, 'mview', true, true, function(html) {
            var alreadyDisplay = false;
            for (var i in jeedom.workflow.eqLogic) {
                if (jeedom.workflow.eqLogic[i]) {
                    if ($.inArray(i, html.eqLogic) >= 0) {
                        alreadyDisplay = true;
                        jeedom.view.toHtml(_view_id, 'mview', false, true, function(html) {
                            jeedom.workflow.eqLogic[i] = false;
                            displayView(html);
                        });
                        break;
                    }
                }
            }
            if (!alreadyDisplay) {
                displayView(html);
            }
        });
    } else {
        $('#panel_right').panel('open');
    }

    $(window).on("orientationchange", function(event) {
        if (deviceInfo.type == 'phone') {
            $('.chartContainer').width((deviceInfo.width - 50));
        } else {
            $('.chartContainer').width(((deviceInfo.width / 2) - 50));
        }
        setTileSize('.eqLogic');
        setTileSize('.scenario');
        $('.eqLogicZone').masonry();
    });
}

function displayView(html) {
    $('#div_displayView').empty().html(html.html).trigger('create');
    if (deviceInfo.type == 'phone') {
        $('.chartContainer').width((deviceInfo.width - 50));
    } else {
        $('.chartContainer').width(((deviceInfo.width / 2) - 50));
    }
    setTileSize('.eqLogic');
    setTileSize('.scenario');
    $('.eqLogicZone').masonry();
}