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
        $('#div_displayView').empty().html(html.html).trigger('create');
        setTimeout(function() {
            if (deviceInfo.type == 'phone') {
                $('.chartContainer').width(($('#pagecontainer').width() - 50));
            } else {
                $('.chartContainer').width((($('#pagecontainer').width() / 2) - 50));
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
            $('.chartContainer').width(($('#pagecontainer').width() - 50));
        } else {
            $('.chartContainer').width((($('#pagecontainer').width() / 2) - 50));
        }
        setTileSize('.eqLogic');
        setTileSize('.scenario');
        $('.eqLogicZone').masonry();
    });
}