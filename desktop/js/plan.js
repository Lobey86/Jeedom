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
            if (data[i].plan.link_type == 'eqLogic') {
                displayEqLogic(data[i].plan.link_id, data[i].html, data[i].plan);
            }
        }
    },
});


$('#bt_addEqLogic').on('click', function() {
    jeedom.eqLogic.getSelectModal({}, function(data) {
        addEqLogic(data.id);
    });
});

$('#div_displayObject').delegate('.eqLogic-widget', 'dblclick', function() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('#md_modal').dialog({title: "{{Configuration du plan}}"});
        $('#md_modal').load('index.php?v=d&modal=plan.configure&link_type=eqLogic&link_id=' + $(this).attr('data-eqLogic_id') + '&object_id=' + $('.li_object.active').attr('data-object_id')).dialog('open');
    }
});

$('#bt_editPlan').on('click', function() {
    if ($(this).attr('data-mode') == '0') {
        $('.eqLogic-widget').draggable({
            stop: function(event, ui) {
                savePlan();
            }
        });
        $('.editMode').show();
        $(this).html('<i class="fa fa-pencil"></i> {{Quitter le mode édition}}');
        $(this).attr('data-mode', '1');
    } else {
        $('.eqLogic-widget').draggable("destroy");
        $('.editMode').hide();
        $(this).html('<i class="fa fa-pencil"></i> {{Mode édition}}');
        $(this).attr('data-mode', '0');
    }

});

function savePlan() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        var parent = {
            height: $('#div_displayObject img').height(),
            width: $('#div_displayObject img').width(),
        };
        var plans = [];
        $('.eqLogic-widget').each(function() {
            var plan = {};
            plan.position = {};
            plan.link_type = 'eqLogic';
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
            global: false,
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function() {
            },
        });
    }
}


function addEqLogic(_id, _plan) {
    jeedom.eqLogic.toHtml({
        id: _id,
        version: 'dashboard',
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            displayEqLogic(_id, data.html, _plan);
            savePlan();
        }
    })
}

function displayEqLogic(_id, _html, _plan) {
    _plan = init(_plan, {});
    _plan.position = init(_plan.position, {});
    _plan.css = init(_plan.css, {});
    $('.eqLogic-widget[data-eqLogic_id=' + _id + ']').remove();
    var parent = {
        height: $('#div_displayObject img').height(),
        width: $('#div_displayObject img').width(),
    };
    var html = $(_html);
    html.css('position', 'absolute');
    html.css('top', init(_plan.position.top, '0') * parent.height / init(_plan.css.zoom, 0.65) / 100);
    html.css('left', init(_plan.position.left, '0') * parent.width / init(_plan.css.zoom, 0.65) / 100);
    html.css('zoom', init(_plan.css.zoom, 0.65));
    for (var key in _plan.css) {
        if (_plan.css[key] != '') {
            html.css(key, _plan.css[key]);
        }
    }
    $('#div_displayObject').append(html);
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('.eqLogic-widget').draggable({
            stop: function(event, ui) {
                savePlan();
            }
        });
    }
}