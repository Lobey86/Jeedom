function initView(_view_id) {
    jeedom.view.all({
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(views) {
            var li = ' <ul data-role="listview">';
            for (var i in views) {
                li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '</a></li>'
            }
            li += '</ul>';
            panel(li);
        }
    });
    if (isset(_view_id) && is_numeric(_view_id)) {
        jeedom.history.chart = [];
        jeedom.view.toHtml({
            id: _view_id,
            version: 'mobile',
            useCache: true,
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(html) {
                var alreadyDisplay = false;
                for (var i in jeedom.workflow.eqLogic) {
                    if (jeedom.workflow.eqLogic[i]) {
                        if ($.inArray(i, html.eqLogic) >= 0) {
                            alreadyDisplay = true;
                            jeedom.view.toHtml({
                                id: _view_id,
                                version: 'mobile',
                                useCache: false,
                                error: function(error) {
                                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                                },
                                success: function(html) {
                                    jeedom.workflow.eqLogic[i] = false;
                                    displayView(html);
                                }
                            });
                            break;
                        }
                    }
                }
                if (!alreadyDisplay) {
                    displayView(html);
                }
            }});
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