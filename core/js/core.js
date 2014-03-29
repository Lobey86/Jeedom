
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


var CORE_chart = [];
var socket = null;
$(function() {
    if (!$.mobile) {
        jeedom.init();
    }
});


$(document).on('pagecontainershow', function() {
    if ($.mobile) {
        if (jeedom.nodeJs.state === null) {
            $('.span_nodeJsState').removeClass('red').addClass('grey');
        }
        if (jeedom.nodeJs.state === true) {
            setTimeout(function() {
                $('body').trigger('nodeJsConnect');
            }, 500);
            $('.span_nodeJsState').removeClass('red').addClass('green');
        }
        if (jeedom.nodeJs.state === false) {
            $('.span_nodeJsState').removeClass('green').addClass('red');
        }
        if (jeedom.nodeJs.state == -1) {
            jeedom.init();
        }
    }
});



function execCmd(_id, _value, _cache) {
    if (init(_value) != '' && (is_array(_value) || is_object(_value))) {
        _value = json_encode(_value);
    }
    var retour;
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "execCmd",
            id: _id,
            cache: init(_cache, 1),
            value: _value
        },
        dataType: 'json',
        async: false,
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                notify('Commande', data.result, 'gritter-red');
                return;
            }
            notify('Commande', 'La commande a été executée avec succès', 'gritter-green', true);
            retour = data.result;
        }
    });
    return retour;
}

function changeScenarioState(_id, _state) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/scenario.ajax.php", // url du fichier php
        data: {
            action: "changeState",
            id: _id,
            state: _state
        },
        dataType: 'json',
        async: false,
        global: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                notify('Commande', data.result, 'gritter-red')
                return;
            }
            notify('Scénario', 'Mise à jour de l\état du scénario réussi', 'gritter-green', true);
        }
    });
}

function cmd_test(_id) {
    $.showLoading();
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "getCmd",
            id: _id,
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var result = data.result;
            switch (result.type) {
                case 'info' :
                    alert(execCmd(_id, '', 0));
                    break;
                case 'action' :
                    switch (result.subType) {
                        case 'other' :
                            execCmd(_id, '', 0);
                            break;
                        case 'slider' :
                            var slider = new Object();
                            slider['slider'] = 50;
                            execCmd(_id, slider, 0);
                            break;
                        case 'color' :
                            var color = new Object();
                            color['color'] = '#fff000';
                            execCmd(_id, color, 0);
                            break;
                        case 'message' :
                            var message = new Object();
                            message['title'] = '[Jeedom] Message de test';
                            message['message'] = 'Ceci est un test de message pour la commande ' + result.name;
                            execCmd(_id, message, 0);
                            break;
                    }
                    break;
            }
        }
    });
}

function getTemplate(_folder, _version, _filename, _replace) {
    if (_folder == 'core') {
        var path = _folder + '/template/' + _version + '/' + _filename;
    } else {
        var path = 'plugins/' + _folder + '/desktop/template/' + _version + '/' + _filename;
    }
    var template = '';
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: path, // url du fichier php
        async: false,
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné
            if (isset(_replace) && _replace != null) {
                for (i in _replace) {
                    var reg = new RegExp(i, "g");
                    data = data.replace(reg, _replace[i]);
                }
            }
            template = data;
        }
    });
    return template;
}

function getConfigValue(_el) {
    var config = new Object();
    config['key'] = _el.attr('key');
    config['value'] = _el.value();
    return config;
}

function handleAjaxError(_request, _status, _error, _div_alert) {
    $.hideLoading();
    var div_alert = init(_div_alert, $('#div_alert'));
    if (_request.status != '0') {
        if (init(_request.responseText, '') != '') {
            div_alert.showAlert({message: _request.responseText, level: 'danger'});
        } else {
            div_alert.showAlert({message: _request.status + ' : ' + _error, level: 'danger'});
        }
    }
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

function getUrlVars(_key) {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars[hash[0]] = hash[1];
        if (isset(_key) && _key == hash[0]) {
            return hash[1];
        }
    }
    if (isset(_key)) {
        return false;
    }
    return vars;
}


function refreshScenarioValue(_scenario_id) {
    if ($('.scenario[data-scenario_id=' + _scenario_id + ']').html() != undefined) {
        var version = $('.scenario[data-scenario_id=' + _scenario_id + ']').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/scenario.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _scenario_id,
                version: version
            },
            dataType: 'json',
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('.scenario[data-scenario_id=' + _scenario_id + ']').replaceWith(data.result);
                if ($.mobile) {
                    $('.scenario[data-scenario_id=' + _scenario_id + ']').trigger("create");
                }
            }
        });
    }
}

function refreshCmdValue(_cmd_id) {
    if ($('.cmd[data-cmd_id=' + _cmd_id + ']').html() != undefined && $('.cmd[data-cmd_id=' + _cmd_id + ']').closest('.eqLogic').attr('data-version') != undefined) {
        var version = $('.cmd[data-cmd_id=' + _cmd_id + ']').closest('.eqLogic').attr('data-version');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/cmd.ajax.php", // url du fichier php
            data: {
                action: "toHtml",
                id: _cmd_id,
                version: version,
            },
            dataType: 'json',
            cache: true,
            global: false,
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('.cmd[data-cmd_id=' + _cmd_id + ']').replaceWith(data.result.html);

                activateTooltips();
                if ($.mobile) {
                    $('.cmd[data-cmd_id=' + _cmd_id + ']').trigger("create");
                }
            }
        });
    }
}


function activateTooltips() {
    if ($.mobile) {

    } else {
        $('.tooltips').tooltip({
            animation: true,
            html: true,
            placement: 'bottom'
        });
    }
}

function generatePlotBand(_startTime, _endTime) {
    var plotBands = [];
    var pas = 43200000;
    var offset = 14400000; //Debut du jour - 4 (soit 20h)
    _startTime = (Math.floor(_startTime / 86400000) * 86400000) - offset;
    while (_startTime < _endTime) {
        var plotBand = {};
        plotBand.color = '#E6E6E6';
        plotBand.from = _startTime;
        plotBand.to = _startTime + pas;
        plotBands.push(plotBand);
        _startTime += 2 * pas;
    }
    return plotBands;
}

function refreshGraph(_cmd_id) {
    var serie = null;
    for (var i in CORE_chart) {
        serie = CORE_chart[i].chart.get(intval(_cmd_id));
        if (serie != null && serie != undefined) {
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // methode de transmission des données au fichier php
                url: "core/ajax/cmd.ajax.php", // url du fichier php
                data: {
                    action: "getHistory",
                    id: _cmd_id
                },
                dataType: 'json',
                global: false,
                async: false,
                error: function(request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function(data) {
                    if (data.state != 'ok') {
                        $('#div_alert').showAlert({message: data.result, level: 'danger'});
                        return;
                    }
                    serie.addPoint(data.result.data[data.result.data.length - 1], true, true);
                }
            });
            serie = null;
        }
    }
}

function drawChart(_cmd_id, _el, _dateRange, _option) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "getHistory",
            id: _cmd_id,
            dateRange: init(_dateRange),
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) { // si l'appel a bien fonctionné 
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            if (data.result.data.length < 1) {
                $('#div_alert').showAlert({message: 'Il n\'existe encore aucun historique pour cette commande : ' + data.result.history_name, level: 'danger'});
                return;
            }
            _option = init(_option, {});
            _option.graphType = init(_option.graphType, 'line');
            if (isset(CORE_chart[_el])) {
                _option.graphColor = init(_option.graphColor, Highcharts.getOptions().colors[init(CORE_chart[_el].color, 0)]);
            } else {
                _option.graphColor = init(_option.graphColor, Highcharts.getOptions().colors[0]);
            }

            if (!isset(_option.graphStep) && data.result.cmd.subType == 'binary') {
                _option.graphStep = true;
            } else {
                _option.graphStep = (_option.graphStep == 1) ? true : false;
            }
            if (_option.graphStack == undefined || _option.graphStack == null || _option.graphStack == 0) {
                _option.graphStack = Math.floor(Math.random() * 10000 + 2);
            } else {
                _option.graphStack = 1;
            }
            if (_option.graphScale == undefined) {
                _option.graphScale = 0;
            } else {
                _option.graphScale = intval(_option.graphScale);
            }

            var series = {
                type: _option.graphType,
                id: intval(_cmd_id),
                name: data.result.history_name,
                data: data.result.data,
                color: _option.graphColor,
                stack: _option.graphStack,
                step: _option.graphStep,
                yAxis: _option.graphScale,
                tooltip: {
                    valueDecimals: 2
                }
            };

            if (!$.mobile) {
                var legend = {
                    enabled: true,
                    borderColor: 'black',
                    borderWidth: 2,
                    shadow: true
                };
            } else {
                var legend = {};
            }

            var maxDatetime = new Date().getTime();

            if (!isset(CORE_chart[_el])) {
                CORE_chart[_el] = {};
                CORE_chart[_el].cmd = new Array();
                CORE_chart[_el].color = 0;

                var dateRange = 3;
                switch (_dateRange) {
                    case '30 min' :
                        dateRange = 0
                        break;
                    case '1 hour' :
                        dateRange = 1
                        break;
                    case '1 day' :
                        dateRange = 2
                        break;
                    case '7 days' :
                        dateRange = 3
                        break;
                    case '1 month' :
                        dateRange = 4
                        break;
                    case '1 year' :
                        dateRange = 5
                        break;
                    case 'all' :
                        dateRange = 6
                        break;
                }

                CORE_chart[_el].chart = new Highcharts.StockChart({
                    chart: {
                        zoomType: 'x',
                        renderTo: _el
                    },
                    plotOptions: {
                        series: {
                            stacking: 'normal',
                            dataGrouping: {
                                enable: false
                            }
                        }
                    },
                    credits: {
                        text: 'Copyright Jeedom',
                        href: 'http://jeedom.fr',
                    },
                    navigator: {
                        enabled: false
                    },
                    rangeSelector: {
                        buttons: [{
                                type: 'minute',
                                count: 30,
                                text: '30m'
                            }, {
                                type: 'hour',
                                count: 1,
                                text: 'H'
                            }, {
                                type: 'day',
                                count: 1,
                                text: 'J'
                            }, {
                                type: 'week',
                                count: 1,
                                text: 'S'
                            }, {
                                type: 'month',
                                count: 1,
                                text: 'M'
                            }, {
                                type: 'year',
                                count: 1,
                                text: 'A'
                            }, {
                                type: 'all',
                                count: 1,
                                text: 'Tous'
                            }],
                        selected: dateRange,
                        inputEnabled: false
                    },
                    legend: legend,
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                        valueDecimals: 2,
                    },
                    yAxis: [{
                            format: '{value}',
                            showEmpty: false,
                            showLastLabel: true,
                            labels: {
                                align: 'right',
                                x: -5
                            }
                        }, {
                            opposite: true,
                            format: '{value}',
                            showEmpty: false,
                            gridLineWidth: 0,
                            labels: {
                                align: 'left',
                                x: -5
                            }
                        }],
                    xAxis: {
                        type: 'datetime',
                        ordinal: false,
                        max: maxDatetime,
                        plotBands: generatePlotBand(data.result.data[0][0], data.result.data[data.result.data.length - 1][0])
                    },
                    scrollbar: {
                        barBackgroundColor: 'gray',
                        barBorderRadius: 7,
                        barBorderWidth: 0,
                        buttonBackgroundColor: 'gray',
                        buttonBorderWidth: 0,
                        buttonBorderRadius: 7,
                        trackBackgroundColor: 'none', trackBorderWidth: 1,
                        trackBorderRadius: 8,
                        trackBorderColor: '#CCC'
                    },
                    series: [series]
                });
            } else {
                CORE_chart[_el].chart.addSeries(series);
                var extremeAxisX = CORE_chart[_el].chart.xAxis[0].getExtremes();
                CORE_chart[_el].chart.xAxis[0].setExtremes(extremeAxisX.dataMax - (86400000 * 7), extremeAxisX.dataMax);
            }

            CORE_chart[_el].cmd[intval(_cmd_id)] = {option: _option};
            CORE_chart[_el].color++;
            if (CORE_chart[_el].color > 9) {
                CORE_chart[_el].color = 0;
            }
        }
    });
}
