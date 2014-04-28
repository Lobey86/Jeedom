function initEquipment(_object_id) {
    $('#div_displayEquipement').masonry('destroy');
    var objects = object.all();
    var li = ' <ul data-role="listview">';
    for (var i in objects) {
        li += '<li><a href="#" class="link" data-page="equipment" data-title="' + objects[i].name + '" data-option="' + objects[i].id + '">' + objects[i].name + '</a></li>'
    }
    li += '</ul>';
    panel(li);
    if (isset(_object_id) && is_numeric(_object_id)) {
        var html = object.toHtml(_object_id, 'mobile');
        $('#div_displayEquipement').empty().html(html).trigger('create');

        var bSize = 150;
        if (!isTablet) {
            bSize = (screen.width / 2) - 28;
        }

        $('.eqLogic').each(function() {
            var width = $(this).width();
            var height = $(this).height();
            if (width < bSize) {
                $(this).width((bSize));
            } else {
                if (width > bSize && width < (bSize * 2 + 12)) {
                    $(this).width((bSize * 2 + 12));
                }
            }
            if (height < bSize) {
                $(this).height((bSize));
            } else {
                if (height > bSize && height < (bSize * 2 + 12)) {
                    $(this).height((bSize * 2 + 12));
                }
            }
        });
        $('#div_displayEquipement').masonry();
    } else {
        $('#panel_right').panel('open');
    }

    $(window).off().on("orientationchange", function(event) {
        $('#div_displayEquipement').masonry();
    });
}