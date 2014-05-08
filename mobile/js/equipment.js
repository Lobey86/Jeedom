function initEquipment(_object_id) {
    var objects = object.all();
    var li = ' <ul data-role="listview">';
    for (var i in objects) {
        var icon = '';
        if (isset(objects[i].configuration) && isset(objects[i].configuration.icon)) {
            icon = objects[i].configuration.icon;
        }
        li += '<li><a href="#" class="link" data-page="equipment" data-title="' + objects[i].name + '" data-option="' + objects[i].id + '">' + icon + ' ' + objects[i].name + '</a></li>'
    }
    li += '</ul>';
    panel(li);
    if (isset(_object_id) && is_numeric(_object_id)) {
        var html = object.toHtml(_object_id, 'mobile');
        $('#div_displayEquipement').empty().html(html).trigger('create');
        setTileSize('.eqLogic');
        $('#div_displayEquipement').masonry();
    } else {
        $('#panel_right').panel('open');
    }

    $(window).on("orientationchange", function(event) {
        setTileSize('.eqLogic');
        $('#div_displayEquipement').masonry();
    });
}