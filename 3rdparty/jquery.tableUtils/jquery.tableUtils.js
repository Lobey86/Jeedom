
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


(function($) {
    $.initTableFilter = function() {
        $("body").delegate("table input.filter", 'keyup', function() {
            $(this).closest('table').tableFilter();
        });
        $("body").delegate("ul li input.filter", 'keyup', function() {
            $(this).closest('ul').tableFilter();
        });

        //Hide/show filter
        $("body").delegate("table thead .showFilter", 'click', function() {
            if ($(this).closest('thead').find('tr.filter').is(':visible')) {
                $(this).closest('thead').find('tr.filter').hide();
            } else {
                $(this).closest('thead').find('tr.filter').show();
                $(this).closest('thead').find('tr.filter').find('input.filter:first').focus();
            }
        });
        $("body").delegate("ul li .showFilter", 'click', function() {
            if ($(this).closest('ul').find('li.filter').is(':visible')) {
                $(this).closest('ul').find('li.filter').hide();
            } else {
                $(this).closest('ul').find('li.filter').show();
                $(this).closest('ul').find('li.filter input.filter:first').focus();
            }
        });

        //Hide/show tbody
        $("body").delegate('.hideTable', 'click', function() {
            if ($(this).closest('tbody').is(':visible')) {
                $(this).closest('tbody').hide();
                $(this).removeClass('icon-circle-arrow-up');
                $(this).addClass('icon-circle-arrow-down');
            } else {
                $(this).closest('tbody').show();
                $(this).removeClass('icon-circle-arrow-down');
                $(this).addClass('icon-circle-arrow-up');
            }
        });
    };


    $.fn.tableFilter = function() {
        table = $(this);
        type = 'table';
        var tr = $(this).find('tbody tr:first');
        var find = 'thead tr.filter input.filter';
        var findLineFilter = 'thead .lineFilter';
        if ($(this).is('ul')) {
            type = 'ul';
            tr = $(this).find(':nth-child(3)');
            find = 'li.filter input.filter';
            findLineFilter = 'li .lineFilter';
        }

        delete inputs;
        var inputs = new Array();
        table.find(find).each(function(i) {
            var filterOn = '';
            if (type == 'table' && $(this).closest("thead").hasClass('header-copy')) {
                if (!$(this).closest("thead").hasClass('hide')) {
                    var value = $(this).value();
                    var filterOn = $(this).attr('filterOn');
                }
            } else {
                if ($(this).is(':visible')) {
                    var value = $(this).value();
                    var filterOn = $(this).attr('filterOn');
                }
            }
            if (filterOn != '') {
                if (type == 'table') {
                    table.find(find + '[filterOn=' + filterOn + ']').each(function(i) {
                        if ($(this).closest("thead").hasClass('header-copy')) {
                            if ($(this).closest("thead").hasClass('hide')) {
                                $(this).value(value);
                            }
                        } else {
                            if ($(this).find('thead.header-copy').html() != undefined && !table.find('thead.header-copy').hasClass('hide')) {
                                $(this).value(value);
                            }
                        }
                    });
                }
                if (value != '') {
                    var infoInput = new Array();
                    infoInput[0] = filterOn;
                    infoInput[1] = value.toLowerCase();
                    inputs.push(infoInput);
                }
            }
        });
        var nbShowLine = 0;
        var nbHideLine = 0;
        while (tr.html() != undefined) {
            if (!tr.hasClass('noShow')) {
                var showTr = true;
                for (var i = 0; i < inputs.length; i++) {
                    if (type == 'table') {
                        var searchText = tr.find('.' + inputs[i][0]).text().toLowerCase().stripAccents().indexOf(inputs[i][1].stripAccents());
                    }
                    if (type == 'ul') {
                        var searchText = tr.find('a').text().toLowerCase().indexOf(inputs[i][1]);
                    }
                    if (searchText < 0) {
                        showTr = false;
                        break;
                    }
                }

                if (showTr) {
                    tr.show();
                    nbShowLine++;
                } else {
                    tr.hide();
                    nbHideLine++;
                }
            } else {
                tr.hide();
            }
            tr = tr.next();
        }
        table.find(findLineFilter).remove();
        if (nbHideLine != 0) {
            if (type == 'table') {
                var filterLine = '<tr class="alert alert-success lineFilter"><td  colspan="99"><center>' + nbShowLine + ' line(s) found according to filter</center></td></tr>';
                table.find('thead').append(filterLine);
            } else if (type == 'ul') {
                var filterLine = '<li class="alert alert-success lineFilter"><center>' + nbShowLine + ' line(s) found according to filter</center></li>';
                table.find('li.filter').append(filterLine);
            }
        }
        return this;
    };


    $.fn.hideShowColumns = function(_showColumns) {
        for (var i in _showColumns) {
            if (_showColumns[i] == 1) {
                $('#sel_columns option[value=' + i + ']').prop('selected', true);
                $(this).find('.' + i).show();
            } else {
                $('#sel_columns option[value=' + i + ']').prop('selected', false);
                $(this).find('.' + i).hide();
            }
        }
        return this;
    };
    
      String.prototype.stripAccents = function() {
        var in_chrs = 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
                out_chrs = 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY',
                transl = {};
        eval('var chars_rgx = /[' + in_chrs + ']/g');
        for (var i = 0; i < in_chrs.length; i++) {
            transl[in_chrs.charAt(i)] = out_chrs.charAt(i);
        }
        return this.replace(chars_rgx, function(match) {
            return transl[match];
        });
    };
})(jQuery);