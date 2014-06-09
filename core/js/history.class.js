
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


jeedom.history = function() {
};

jeedom.history.chart = [];


jeedom.history.drawChart = function(_cmd_id, _el, _dateRange, _option) {
    if ($.type(_dateRange) == 'object') {
        _dateRange = json_encode(_dateRange);
    }
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
                var message = '{{Il n\'existe encore aucun historique pour cette commande :}} ' + data.result.history_name;
                if (init(data.result.dateStart) != '') {
                    if (init(data.result.dateEnd) != '') {
                        message += ' {{du}} ' + data.result.dateStart + ' {{au}} ' + data.result.dateEnd;
                    } else {
                        message += ' {{à partir de}} ' + data.result.dateStart;
                    }
                } else {
                    if (init(data.result.dateEnd) != '') {
                        message += ' {{jusqu\'au}} ' + data.result.dateEnd;
                    }
                }
                $('#div_alert').showAlert({message: message, level: 'danger'});
                return;
            }
            if (isset(jeedom.history.chart[_el]) && isset(jeedom.history.chart[_el].cmd[intval(_cmd_id)])) {
                jeedom.history.chart[_el].cmd[intval(_cmd_id)] = null;
            }
            _option = init(_option, {});
            _option.graphType = init(_option.graphType, 'line');
            if (isset(jeedom.history.chart[_el])) {
                _option.graphColor = init(_option.graphColor, Highcharts.getOptions().colors[init(jeedom.history.chart[_el].color, 0)]);
            } else {
                _option.graphColor = init(_option.graphColor, Highcharts.getOptions().colors[0]);
            }

            if (!isset(_option.graphStep) && isset(data.result.cmd) && data.result.cmd.subType == 'binary') {
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
                dataGrouping: {
                    enabled: false
                },
                type: _option.graphType,
                id: intval(_cmd_id),
                cursor: 'pointer',
                name: (isset(_option.name)) ? _option.name : data.result.history_name,
                data: data.result.data,
                color: _option.graphColor,
                stack: _option.graphStack,
                step: _option.graphStep,
                yAxis: _option.graphScale,
                tooltip: {
                    valueDecimals: 2
                },
                point: {
                    events: {
                        click: function(event) {
                            if (!$.mobile) {
                                var id = this.series.userOptions.id;
                                var datetime = Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x);
                                var value = this.y;
                                bootbox.prompt("{{Edition de la série :}} <b>" + this.series.name + "</b> {{et du point de}} <b>" + datetime + "</b> ({{valeur :}} <b>" + value + "</b>) ? {{Ne rien mettre pour supprimer la valeur}}", function(result) {
                                    if (result !== null) {
                                        jeedom.history.changeHistoryPoint(id, datetime, result);
                                    }
                                });
                            }
                        }
                    }
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

            if (!isset(jeedom.history.chart[_el])) {
                jeedom.history.chart[_el] = {};
                jeedom.history.chart[_el].cmd = new Array();
                jeedom.history.chart[_el].color = 0;

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

                jeedom.history.chart[_el].chart = new Highcharts.StockChart({
                    chart: {
                        zoomType: 'x',
                        renderTo: _el
                    },
                    plotOptions: {
                        series: {
                            stacking: 'normal',
                            connectNulls: true,
                            dataGrouping: {
                                enable: false
                            }
                        }
                    },
                    credits: {
                        text: '',
                        href: '',
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
                            maxPadding: 0.25,
                            showLastLabel: true,
                        }, {
                            opposite: true,
                            format: '{value}',
                            showEmpty: false,
                            gridLineWidth: 0,
                            maxPadding: 0.25,
                            labels: {
                                align: 'left',
                                x: -5
                            }
                        }],
                    xAxis: {
                        type: 'datetime',
                        ordinal: false,
                        // plotBands: jeedom.history.generatePlotBand(data.result.data[0][0], data.result.data[data.result.data.length - 1][0])
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
                jeedom.history.chart[_el].chart.addSeries(series);
                var extremeAxisX = jeedom.history.chart[_el].chart.xAxis[0].getExtremes();
                jeedom.history.chart[_el].chart.xAxis[0].setExtremes(extremeAxisX.dataMax - (86400000 * 7), extremeAxisX.dataMax);
            }
            var yaxis = jeedom.history.chart[_el].chart.yAxis[0].getExtremes();
            jeedom.history.chart[_el].chart.yAxis[0].setExtremes(yaxis.dataMin, yaxis.dataMax);
            jeedom.history.chart[_el].cmd[intval(_cmd_id)] = {option: _option, dateRange: _dateRange};
            jeedom.history.chart[_el].color++;
            if (jeedom.history.chart[_el].color > 9) {
                jeedom.history.chart[_el].color = 0;
            }
        }
    });
}

jeedom.history.generatePlotBand = function(_startTime, _endTime) {
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

jeedom.history.refreshGraph = function(_cmd_id) {
    var serie = null;
    for (var i in jeedom.history.chart) {
        serie = jeedom.history.chart[i].chart.get(intval(_cmd_id));
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

jeedom.history.changeHistoryPoint = function(_cmd_id, _datetime, _value) {
    $.ajax({// fonction permettant de faire de l'ajax
        type: "POST", // methode de transmission des données au fichier php
        url: "core/ajax/cmd.ajax.php", // url du fichier php
        data: {
            action: "changeHistoryPoint",
            cmd_id: _cmd_id,
            datetime: _datetime,
            value: _value
        },
        dataType: 'json',
        error: function(request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function(data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{La valeur a été éditée avec succès}}', level: 'success'});
            var serie = null;
            for (var i in jeedom.history.chart) {
                serie = jeedom.history.chart[i].chart.get(intval(_cmd_id));
                if (serie != null && serie != undefined) {
                    serie.remove();
                    serie = null;
                    jeedom.history.drawChart(_cmd_id, i, jeedom.history.chart[i].cmd[intval(_cmd_id)].dateRange, jeedom.history.chart[i].cmd[intval(_cmd_id)].option);
                }
            }
        }
    });
}