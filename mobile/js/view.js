function initView(_view_id) {
    var views = view.all();
    var li = ' <ul data-role="listview">';
    for (var i in views) {
        li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '</a></li>'
    }
    li += '</ul>';
    panel(li);
    if (isset(_view_id) && is_numeric(_view_id)) {
        CORE_chart = [];
        var html = view.toHtml(_view_id, 'mobile', true);
        for (var i in jeedom.workflow.eqLogic) {
            if (jeedom.workflow.eqLogic[i]) {
                if ($.inArray(i, html.eqLogic) >= 0) {
                    var html = view.toHtml(_view_id, 'mobile');
                    jeedom.workflow.eqLogic[i] = false;
                    break;
                }
            }
        }
        $('#div_displayView').empty().html(html.html).trigger('create');
        setTimeout(function() {
            if (deviceInfo.type == 'phone') {
                $('.chartContainer').width((deviceInfo.width  - 50));
            } else {
                $('.chartContainer').width(((deviceInfo.width  / 2) - 50));
            }
            setTileSize('.eqLogic');
            setTileSize('.scenario');
            $('.eqLogicZone').masonry();
        }, 1);
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