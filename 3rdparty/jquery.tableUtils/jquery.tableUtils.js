(function($) {
    $.initTableFilter = function() {
        $("body").delegate("ul li input.filter", 'keyup', function() {
            $(this).closest('ul').tableFilter();
        });
    };


    $.fn.tableFilter = function() {
        var table = $(this);
        var type = 'table';
        var tr = $(this).find('tbody tr:first');
        var find = 'thead tr.filter input.filter';
        var findLineFilter = 'thead .lineFilter';
        if ($(this).is('ul')) {
            type = 'ul';
            var li = $(this).find(':nth-child(3)');
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
        var searchText = 1;
        if (type == 'table') {
            var showTr = true;
            while (tr.html() != undefined) {
                if (!tr.hasClass('noShow')) {
                    showTr = true;
                    for (var i = 0; i < inputs.length; i++) {
                        searchText = tr.find('.' + inputs[i][0]).text().toLowerCase().stripAccents().indexOf(inputs[i][1].stripAccents());
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
        }

        if (type == 'ul') {
            var showLi = true;
            while (li.html() != undefined) {
                if (!li.hasClass('noShow')) {
                    showLi = true;
                    for (var i = 0; i < inputs.length; i++) {
                        searchText = li.find('a').text().toLowerCase().stripAccents().indexOf(inputs[i][1].stripAccents());
                        if (searchText < 0) {
                            showLi = false;
                            break;
                        }
                    }
                    if (showLi) {
                        li.show();
                        nbShowLine++;
                    } else {
                        li.hide();
                        nbHideLine++;
                    }
                } else {
                    li.hide();
                }
                li = li.next();
            }
        }
        table.find(findLineFilter).remove();
        return this;
    };


    $.fn.hideShowColumns = function(_showColumns) {
        for (var i in _showColumns) {
            if (_showColumns[i] == 1) {
                $('#sel_columns option[value=' + i + ']').prop('selected', true);
                var index = $(this).find('thead th').index($(this).find('thead th.' + i)) + 1;
                if (index >= 0) {
                    $(this).find('thead tr td:nth-child(' + index + ')').show();
                }
                $(this).find('.' + i).show();
            } else {
                $('#sel_columns option[value=' + i + ']').prop('selected', false);
                var index = $(this).find('thead th').index($(this).find('thead th.' + i)) + 1;
                if (index >= 0) {
                    $(this).find('thead tr td:nth-child(' + index + ')').hide();
                }
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