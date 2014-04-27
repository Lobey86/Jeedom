

function initLocalPage() {
    var objects = object.all();
    var li = '';
    for (var i in objects) {
        li += '<li><a href="#" class="link" data-page="equipment" data-title="' + objects[i].name + '" data-option="' + objects[i].id + '">' + objects[i].name + '</a></li>'
    }
    $('#ul_objectList').empty().append(li).listview("refresh");

    var views = view.all();
    var li = '';
    for (var i in views) {
        li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '</a></li>'
    }
    $('#ul_viewList').empty().append(li).listview("refresh");

    refreshMessageNumber();
}

