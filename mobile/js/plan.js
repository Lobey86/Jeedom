function initPlan(_planHeader_id) {
    jeedom.plan.getHeader({
        id: _planHeader_id,
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            $('#div_displayObject').empty().append(data.image);
            var img = $('#div_displayObject img');
            var height = $(window).height() - $('#pageTitle').height();
            var width = $(window).width();
            if (init(data.configuration.sizeX) != '' && init(data.configuration.sizeY) != '') {
                if (init(data.configuration.maxSizeAllow) == 1 && (height > data.configuration.sizeY || width > data.configuration.sizeX)) {
                    height = data.configuration.sizeY;
                    width = data.configuration.sizeX;
                }
                if (init(data.configuration.minSizeAllow) == 1 && (height < data.configuration.sizeY || width < data.configuration.sizeX)) {
                    height = data.configuration.sizeY;
                    width = data.configuration.sizeX;
                }
                if (width / height != data.configuration.sizeX / data.configuration.sizeY) {
                    var cHeight = width / (data.configuration.sizeX / data.configuration.sizeY);
                    if (height < cHeight) {
                        width = height * (data.configuration.sizeX / data.configuration.sizeY);
                    } else {
                        height = cHeight;
                    }
                }
            }
            var size_x = img.attr('data-sixe_x');
            var size_y = img.attr('data-sixe_y');
            var ratio = size_x / size_y;
            $('#div_displayObject').height(height);
            $('#div_displayObject').width(width);
            var rWidth = width;
            var rHeight = width / ratio;
            if (rHeight > height) {
                rHeight = height;
                rWidth = height * ratio;
            }
            $('#div_displayObject img').height(rHeight);
            $('#div_displayObject img').width(rWidth);

            $('.eqLogic-widget,.scenario-widget,.plan-link-widget,.view-link-widget,.graph-widget').remove();


            jeedom.plan.byPlanHeader({
                id: _planHeader_id,
                version: 'mobile',
                error: function(error) {
                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                },
                success: function(data) {
                    for (var i in data) {
                        if (data[i].plan.link_type == 'graph') {
                            addGraph(data[i].plan);
                        } else {
                            displayObject(data[i].plan.link_type, data[i].plan.link_id, data[i].html, data[i].plan);
                        }
                    }
                    setTileSize('.eqLogic');
                },
            });
        }
    });

    $(window).on("orientationchange", function(event) {
        initPlan(_planHeader_id)
    });
}


function getZoomLevel(_el) {
    var zoom = _el.css('zoom');
    if (zoom == undefined) {
        return 1;
    }
    return zoom;
}


function displayObject(_type, _id, _html, _plan) {
    for (var i in jeedom.history.chart) {
        delete jeedom.history.chart[i];
    }
    _plan = init(_plan, {});
    _plan.position = init(_plan.position, {});
    _plan.css = init(_plan.css, {});
    if (_type == 'eqLogic') {
        var defaultZoom = 0.65;
        $('.eqLogic-widget[data-eqLogic_id=' + _id + ']').remove();
    }
    if (_type == 'scenario') {
        var defaultZoom = 1;
        $('.scenario-widget[data-scenario_id=' + _id + ']').remove();
    }
    if (_type == 'view') {
        var defaultZoom = 1;
        $('.view-link-widget[data-link_id=' + _id + ']').remove();
    }
    if (_type == 'plan') {
        var defaultZoom = 1;
        $('.plan-link-widget[data-link_id=' + _id + ']').remove();
    }
    if (_type == 'graph') {
        var defaultZoom = 1;
        $('.graph-widget[data-graph_id=' + _id + ']').remove();
    }
    var parent = {
        height: $('#div_displayObject img').height(),
        width: $('#div_displayObject img').width(),
    };
    var html = $(_html);
    $('#div_displayObject').append(html);

    for (var key in _plan.css) {
        if (_plan.css[key] != '') {
            html.css(key, _plan.css[key]);
        }
    }
    html.css('position', 'absolute');
    html.css('zoom', init(_plan.css.zoom, defaultZoom));
    html.css('-moz-transform', 'scale(' + init(_plan.css.zoom, defaultZoom) + ',' + init(_plan.css.zoom, defaultZoom) + ')');
    var position = {
        top: init(_plan.position.top, '10') * parent.height / 100,
        left: init(_plan.position.left, '10') * parent.width / 100,
    };
    if (html.css('zoom') != undefined) {
        html.css('top', position.top / init(_plan.css.zoom, defaultZoom));
        html.css('left', position.left / init(_plan.css.zoom, defaultZoom));
    } else {
        html.css('top', position.top - html.height() * 0.52 * (1 - init(_plan.css.zoom, defaultZoom)));
        html.css('left', position.left - html.width() * 0.52 * (1 - init(_plan.css.zoom, defaultZoom)));
    }

    if (_type == 'eqLogic') {
        if (isset(_plan.display) && isset(_plan.display.cmd)) {
            for (var id in _plan.display.cmd) {
                if (_plan.display.cmd[id] == 1) {
                    $('.cmd[data-cmd_id=' + id + ']').remove();
                }
            }
        }
        if (isset(_plan.display) && (isset(_plan.display.name) && _plan.display.name == 1)) {
            html.find('.widget-name').remove();
        }
    }
    html.trigger('create');
}

function addGraph(_plan) {
    _plan = init(_plan, {});
    _plan.display = init(_plan.display, {});
    _plan.link_id = init(_plan.link_id, Math.round(Math.random() * 99999999) + 9999);
    var options = init(_plan.display.graph, '[]');
    var html = '<div class="graph-widget" data-graph_id="' + _plan.link_id + '" style="width : ' + init(_plan.display.width, 400) + 'px;height : ' + init(_plan.display.height, 200) + 'px;background-color : white;border : solid 1px black;">';
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        html += '<i class="fa fa-cogs pull-right editMode configureGraph" style="margin-right : 5px;margin-top : 5px;"></i>';
    } else {
        html += '<i class="fa fa-cogs pull-right editMode configureGraph" style="margin-right : 5px;margin-top : 5px;display:none;"></i>';
    }
    html += '<span class="graphOptions" style="display:none;">' + json_encode(init(_plan.display.graph, '[]')) + '</span>';
    html += '<div class="graph" id="graph' + _plan.link_id + '" style="width : 100%;height : 90%;"></div>';
    html += '</div>';
    displayObject('graph', _plan.link_id, html, _plan);

    for (var i in options) {
        if (init(options[i].link_id) != '') {
            jeedom.history.drawChart({
                cmd_id: options[i].link_id,
                el: 'graph' + _plan.link_id,
                dateRange: init(_plan.display.dateRange, '7 days'),
                option: init(options[i].configuration, {}),
                global: false,
            });
        }
    }
}

