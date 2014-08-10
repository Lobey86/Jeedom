/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

jeedom.plan.byObject({
    object_id: $('.li_object.active').attr('data-object_id'),
    error: function(error) {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function(data) {
        for (var i in data) {
            if (data[i].link_type == 'widget') {
                addEqLogic(data[i].link_id, data[i]);
            }
        }
    },
});


$('#bt_addEqLogic').on('click', function() {
    jeedom.eqLogic.getSelectModal({}, function(data) {
        addEqLogic(data.id);
    });
});

$('#bt_savePlan').on('click', function() {
    var parent = {
        height: $('#div_displayObject img').height(),
        width: $('#div_displayObject img').width(),
    };
    var plans = [];
    $('.eqLogic-widget').each(function() {
        var plan = {};
        plan.position = {};
        plan.link_type = 'widget';
        plan.link_id = $(this).attr('data-eqLogic_id');
        plan.object_id = $('.li_object.active').attr('data-object_id');
        var zoom = $(this).css('zoom');
        $(this).css('zoom', '100%');
        var position = $(this).position();
        $(this).css('zoom', zoom);
        plan.position.top = ((position.top * zoom) / parent.height) * 100;
        plan.position.left = ((position.left * zoom) / parent.width) * 100;
        plans.push(plan);
    });
    jeedom.plan.save({
        plans: plans,
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function() {
            $('#div_alert').showAlert({message: 'Plan sauvegardé avec succès', level: 'success'});
        },
    });
});


function addEqLogic(_id, _plan) {
    _plan = init(_plan, {});
    _plan.position = init(_plan.position, {});
    _plan.css = init(_plan.css, {});
    jeedom.eqLogic.toHtml({
        id: _id,
        version: 'dashboard',
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            var parent = {
                height: $('#div_displayObject img').height(),
                width: $('#div_displayObject img').width(),
            };
            var html = $(data.html);
            html.css('position', 'absolute');
            html.css('top', init(_plan.position.top, '0') * parent.height / init(_plan.css.zoom, 0.65) / 100);
            html.css('left', init(_plan.position.left, '0') * parent.width / init(_plan.css.zoom, 0.65) / 100);
            html.css('zoom', init(_plan.css.zoom, 0.65));
            for (var key in _plan.css) {
                html.css(key, _plan.css[key]);
            }
            html.draggable({
                start: startFix,
                drag: function(event, ui) {
                    dragFix(event, ui, $(this).css('zoom'))
                }
            });
            $('#div_displayObject').append(html);
        }
    })
}

function startFix(event, ui) {
    ui.position.left = 40;
    ui.position.top = -40;
}

function dragFix(event, ui, zoomScale) {
    var changeLeft = ui.position.left - ui.originalPosition.left; // find change in left
    var newLeft = ui.originalPosition.left + changeLeft / zoomScale; // adjust new left by our zoomScale

    var changeTop = ui.position.top - ui.originalPosition.top; // find change in top
    var newTop = ui.originalPosition.top + changeTop / zoomScale; // adjust new top by our zoomScale

    ui.position.left = newLeft;
    ui.position.top = newTop;
}