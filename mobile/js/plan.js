function initPlan(_planHeader_id) {
    jeedom.plan.allHeader({
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(planHeader) {
            var li = ' <ul data-role="listview">';
            for (var i in planHeader) {
                li += '<li><a href="#" class="link" data-page="plan" data-title="' + planHeader[i].name + '" data-option="' + planHeader[i].id + '">' + planHeader[i].name + '</a></li>'
            }
            li += '</ul>';
            panel(li);
        }
    });

    displayPlan(_planHeader_id);

    $(window).on("orientationchange", function(event) {
        initPlan(_planHeader_id)
    });

    $("#bt_fullScreen").on("click", function() {
        if ($("div[data-role=header]").length != 0) {
            $("div[data-role=header]").remove();
            $(this).css('top', '15px');
            $('.ui-content').css('padding', '0');
            displayPlan(_planHeader_id);
        } else {
            window.location.reload();
        }
    });

    $("body:not(.eqLogic)").off("swipeleft");
    $("body:not(.eqLogic)").off("swiperight");
}

function displayPlan(_planHeader_id) {
    jeedom.plan.getHeader({
        id: _planHeader_id,
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            $('#div_displayObject').empty().append(data.image);
            var img = $('#div_displayObject img');
            if ($("div[data-role=header]").length != 0) {
                var height = $(window).height() - $('#pageTitle').height() - 55;
            } else {
                var height = $(window).height();
            }
            var width = $(window).width();
            if (data.configuration != null && init(data.configuration.sizeX) != '' && init(data.configuration.sizeY) != '') {
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
}

function displayObject(_type, _id, _html, _plan) {
    for (var i in jeedom.history.chart) {
        delete jeedom.history.chart[i];
    }
    _plan = init(_plan, {});
    _plan.position = init(_plan.position, {});
    _plan.css = init(_plan.css, {});
    var defaultZoom = 1;
    if (_type == 'eqLogic') {
        defaultZoom = 0.65;
        $('.eqLogic-widget[data-eqLogic_id=' + _id + ']').remove();
    }
    if (_type == 'scenario') {
        $('.scenario-widget[data-scenario_id=' + _id + ']').remove();
    }
    if (_type == 'view') {
        $('.view-link-widget[data-link_id=' + _id + ']').remove();
    }
    if (_type == 'plan') {
        $('.plan-link-widget[data-link_id=' + _id + ']').remove();
    }
    if (_type == 'graph') {
        $('.graph-widget[data-graph_id=' + _id + ']').remove();
    }
    var parent = {
        height: $('#div_displayObject').height(),
        width: $('#div_displayObject').width(),
    };
    var html = $(_html);
    $('#div_displayObject').append(html);

    for (var key in _plan.css) {
        if (_plan.css[key] != '' && key != 'zoom') {
            html.css(key, _plan.css[key]);
        }
        if (key == 'color') {
            html.find('.btn.btn-default').css("cssText", key + ': ' + _plan.css[key] + ' !important;border-color : ' + _plan.css[key] + ' !important');
            html.find('tspan').css('fill', _plan.css[key]);
            html.find('span').css(key, _plan.css[key]);
        }
    }
    html.css('position', 'absolute');
    html.css('transform-origin', '0 0');
    html.css('transform', 'scale(' + init(_plan.css.zoom, defaultZoom) + ')');
    html.css('-webkit-transform-origin', '0 0');
    html.css('-webkit-transform', 'scale(' + init(_plan.css.zoom, defaultZoom) + ')');
    html.css('-moz-transform-origin', '0 0');
    html.css('-moz-transform', 'scale(' + init(_plan.css.zoom, defaultZoom) + ')');
    var position = {
        top: init(_plan.position.top, '10') * parent.height / 100,
        left: init(_plan.position.left, '10') * parent.width / 100,
    };
    html.css('top', position.top);
    html.css('left', position.left);
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

