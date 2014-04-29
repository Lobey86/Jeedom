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
        if (deviceType == 'phone') {
            bSize = (screen.width / 2) - 28;
            if (bSize > 176) {
                bSize = 176;
            }
            if (bSize < 120) {
                bSize = 120;
            }
        }

        $('.eqLogic').each(function() {
            var width = $(this).width();
            var height = $(this).height();
            if (width <= bSize) {
                $(this).width((bSize));
            } else {
                $(this).width((bSize * 2 + 12));
            }
            if (height <= bSize) {
                $(this).height((bSize));
            } else {
                $(this).height((bSize * 2 + 12));
            }
            var verticalAlign = $(this).find('.vertical-align');
            height = $(this).height();
            var vAlignHeight = verticalAlign.height();
            verticalAlign.css('position', 'relative');
            verticalAlign.css('top', (((height / 2) - (vAlignHeight / 2)) - 20) + 'px');

        });
        $('#div_displayEquipement').masonry();
        $('.vertical-align').each(function() {

        });
    } else {
        $('#panel_right').panel('open');
    }

    $(window).off().on("orientationchange", function(event) {
        $('#div_displayEquipement').masonry();
    });
}