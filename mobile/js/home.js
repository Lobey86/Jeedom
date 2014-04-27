

function initLocalPage() {
    var objects = object.all();
    var li = '';
    for (var i in objects) {
        li += '<li><a href="#" class="link" data-page="equipment" data-title="' + objects[i].name + '" data-option="' + objects[i].id + '">' + objects[i].name + '<a></li>'
    }
    $('#ul_objectList').empty().append(li).listview("refresh");
    refreshMessageNumber();
}

