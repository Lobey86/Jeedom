function initLocalPage(_view_id) {
    if (isset(_view_id) && is_numeric(_view_id)) {
        CORE_chart = [];
        var html = view.toHtml(_view_id, 'mobile');
        $('#div_displayView').empty().html(html).trigger('create');
    }
    var views = view.all();
    var li = ' <ul data-role="listview">';
    for (var i in views) {
        li += '<li><a href="#" class="link" data-page="view" data-title="' + views[i].name + '" data-option="' + views[i].id + '">' + views[i].name + '<a></li>'
    }
    li += '</ul>';
    panel(li);
}