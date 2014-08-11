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

/*****************************PLAN HEADER***********************************/
$('#bt_addPlanHeader').on('click', function() {
    bootbox.prompt("Nom du plan ?", function(result) {
        if (result !== null) {
            jeedom.plan.saveHeader({
                planHeader: {name: result},
                error: function(error) {
                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                },
                success: function(data) {
                    window.location.replace('index.php?v=d&p=plan&plan_id=' + data.id);
                }
            });
        }
    });
});

$('#bt_removePlanHeader').on('click', function() {
    bootbox.confirm('{{Etes-vous sûr de vouloir supprimer ce plan ?', function(result) {
        if (result) {
            jeedom.plan.removeHeader({
                id: planHeader_id,
                error: function(error) {
                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                },
                success: function() {
                    window.location.replace('index.php?v=d&p=plan');
                }
            });
        }
    });
});

$('#bt_editPlanHeader').on('click', function() {
    bootbox.prompt("Nom du plan ?", function(result) {
        if (result !== null) {
            jeedom.plan.saveHeader({
                planHeader: {name: result, id: planHeader_id},
                error: function(error) {
                    $('#div_alert').showAlert({message: error.message, level: 'danger'});
                },
                success: function(data) {
                    window.location.replace('index.php?v=d&p=plan&plan_id=' + data.id);
                }
            });
        }
    });
});

$('#bt_uploadImage').fileupload({
    url: 'core/ajax/plan.ajax.php?action=uploadImage&id=' + planHeader_id,
    dataType: 'json',
    done: function(e, data) {
        if (data.result.state != 'ok') {
            $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
            return;
        }
        $('#div_alert').showAlert({message: '{{Fichier(s) ajouté(s) avec succes}}', level: 'success'});
        window.location.reload();
    }
});

$('#sel_planHeader').on('change', function() {
    window.location.replace('index.php?v=d&p=plan&plan_id=' + $(this).value());
});

/*****************************PLAN***********************************/
$('#bt_addEqLogic').on('click', function() {
    jeedom.eqLogic.getSelectModal({}, function(data) {
        addEqLogic(data.id);
    });
});

displayPlan();

$(window).resize(function() {
    displayPlan();
});

$('#div_displayObject').delegate('.eqLogic-widget', 'dblclick', function() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('#md_modal').dialog({title: "{{Configuration du plan}}"});
        $('#md_modal').load('index.php?v=d&modal=plan.configure&link_type=eqLogic&link_id=' + $(this).attr('data-eqLogic_id') + '&planHeader_id=' + planHeader_id).dialog('open');
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

function displayPlan() {
    if (planHeader_id != -1) {
        jeedom.plan.byPlanHeader({
            id: planHeader_id,
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
    }
}

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
            plan.planHeader_id = planHeader_id;
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