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
        $('.eqLogic').each(function() {
            var width = $(this).width();
            var height = $(this).height();
            if (width > 150 && width < 312) {
                $(this).width(312);
            }
            if (height > 150 && height < 312) {
                $(this).height(312);
            }
        });
        setTimeout(function() {
            $('#div_displayEquipement').masonry();
        }, 100);
    } else {
        $('#panel_right').panel('open');
    }

    $(window).off().on("orientationchange", function(event) {
        $('#div_displayEquipement').masonry();
    });
}