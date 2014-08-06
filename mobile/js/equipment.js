function initEquipment(_object_id) {
    jeedom.object.all({
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(objects) {
            var li = ' <ul data-role="listview">';
            for (var i in objects) {
                if (objects[i].isVisible == 1) {
                    var icon = '';
                    if (isset(objects[i].display) && isset(objects[i].display.icon)) {
                        icon = objects[i].display.icon;
                    }
                    li += '<li></span><a href="#" class="link" data-page="equipment" data-title="' + icon.replace(/\"/g, "\'") + ' ' + objects[i].name + '" data-option="' + objects[i].id + '"><span>' + icon + '</span> ' + objects[i].name + '</a></li>';
                }
            }
            li += '</ul>';
            panel(li);
        }
    });

    if (isset(_object_id) && is_numeric(_object_id)) {
        jeedom.object.toHtml({
            id: _object_id,
            version: 'mobile',
            useCache: !jeedom.workflow.object[_object_id],
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(html) {
                $('#div_displayEquipement').empty().html(html).trigger('create');
                setTileSize('.eqLogic');
                $('#div_displayEquipement').masonry();
            }
        });
    } else {
        $('#panel_right').panel('open');
    }

    $(window).on("orientationchange", function(event) {
        setTileSize('.eqLogic');
        $('#div_displayEquipement').masonry();
    });

    $("body:not(.eqLogic)").off("swipeleft").on("swipeleft", function() {
        jeedom.object.all({
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(objects) {
                modal(false);
                panel(false);
                var icon = '';
                for (var i in objects) {
                    if (_object_id == objects[i].id && isset(objects[parseInt(i) + 1])) {
                        if (isset(objects[parseInt(i) + 1].display) && isset(objects[parseInt(i) + 1].display.icon)) {
                            icon = objects[parseInt(i) + 1].display.icon;
                        }
                        page('equipment', icon.replace(/\"/g, "\'") + ' ' + objects[parseInt(i) + 1].name, objects[parseInt(i) + 1].id);
                        return;
                    }
                }
                if (isset(objects[0].display) && isset(objects[0].display.icon)) {
                    icon = objects[0].display.icon;
                }
                page('equipment', icon.replace(/\"/g, "\'") + ' ' + objects[0].name, objects[0].id);
                return;
            }
        });
    });

    $("body:not(.eqLogic)").off("swiperight").on("swiperight", function() {
        jeedom.object.all({
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(objects) {
                modal(false);
                panel(false);
                var icon = '';
                var previous = null;
                for (var i in objects) {
                    if (_object_id == objects[i].id && previous != null) {
                        break;
                    }
                    previous = objects[i];
                }
                if (isset(previous.display) && isset(previous.display.icon)) {
                    icon = previous.display.icon;
                }
                page('equipment', icon.replace(/\"/g, "\'") + ' ' + previous.name, previous.id);
                return;
            }
        });
    });
}