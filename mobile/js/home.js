function initHome() {
    var objects = object.all();
    var li = '';
    for (var i in objects) {
        if (objects[i].isVisible == 1) {
            var icon = '';
            if (isset(objects[i].display) && isset(objects[i].display.icon)) {
                icon = objects[i].display.icon;
            }
            li += '<li></span><a href="#" class="link" data-page="equipment" data-title="' + icon.replace(/\"/g, "\'") + ' ' + objects[i].name + '" data-option="' + objects[i].id + '"><span>' + icon + '</span> ' + objects[i].name + '</a></li>'
        }
    }
    $('#ul_objectList').empty().append(li).listview("refresh");

    var views = view.all();
    var li = '';
    for (var i in views) {
        li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '</a></li>'
    }
    $('#ul_viewList').empty().append(li).listview("refresh");

    if (plugins.length > 0) {
        var li = '';
        for (var i in plugins) {
            li += '<li><a href="#" class="link" data-page="' + plugins[i].mobile + '" data-plugin="' + plugins[i].id + '" data-title="' + plugins[i].name + '">' + plugins[i].name + '</a></li>'
        }
        $('#ul_pluginList').empty().append(li).listview("refresh");
    } else {
        $('#bt_listPlugin').hide();
    }
    refreshMessageNumber();
}

