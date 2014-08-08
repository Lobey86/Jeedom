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



    $("body:not(.eqLogic)").off("swipeleft").on("swipeleft", function() {
        jeedom.view.all({
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(views) {
                modal(false);
                panel(false);
                var icon = '';
                for (var i in views) {
                    if (_view_id == views[i].id && isset(views[parseInt(i) + 1])) {
                        if (isset(views[parseInt(i) + 1].display) && isset(views[parseInt(i) + 1].display.icon)) {
                            icon = views[parseInt(i) + 1].display.icon;
                        }
                        page('view', icon.replace(/\"/g, "\'") + ' ' + views[parseInt(i) + 1].name, views[parseInt(i) + 1].id);
                        return;
                    }
                }
                if (isset(views[0].display) && isset(views[0].display.icon)) {
                    icon = views[0].display.icon;
                }
                page('view', icon.replace(/\"/g, "\'") + ' ' + views[0].name, views[0].id);
                return;
            }
        });
    });

    $("body:not(.eqLogic)").off("swiperight").on("swiperight", function() {
         jeedom.view.all({
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(views) {
                modal(false);
                panel(false);
                var icon = '';
                var previous = null;
                for (var i in views) {
                    if (_view_id == views[i].id && previous != null) {
                        break;
                    }
                    previous = views[i];
                }
                if (isset(previous.display) && isset(previous.display.icon)) {
                    icon = previous.display.icon;
                }
                page('view', icon.replace(/\"/g, "\'") + ' ' + previous.name, previous.id);
                return;
            }
        });
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