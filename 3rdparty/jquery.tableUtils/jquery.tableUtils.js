(function($) {
    $.initTableFilter = function() {
        $("body").delegate("ul li input.filter", 'keyup', function() {
            $(this).closest('ul').ulFilter();
        });
    };


    $.fn.ulFilter = function() {
        var ul = $(this);
        var li = $(this).find('li:not(.filter):not(.nav-header):first');
        var find = 'li.filter input.filter';
        delete inputs;
        var inputs = new Array();
        ul.find(find).each(function(i) {
            var filterOn = '';
            if ($(this).is(':visible')) {
                var value = $(this).value();
                var filterOn = $(this).attr('filterOn');
            }
            if (filterOn != '' && value != '') {
                var infoInput = new Array();
                infoInput[0] = filterOn;
                infoInput[1] = value.toLowerCase();
                inputs.push(infoInput);
            }
        });
        var searchText = 1;
        var showLi = true;
        while (li.html() != undefined) {
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
            } else {
                li.hide();
            }
            li = li.next();
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