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
var noBootstrapTooltips = true;
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
        $('#div_alert').showAlert({message: '{{Fichier(s) ajouté(s) avec succès}}', level: 'success'});
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

$('#bt_addScenario').on('click', function() {
    jeedom.scenario.getSelectModal({}, function(data) {
        addScenario(data.id);
    });
});

$('#bt_addLink').on('click', function() {
    $('#md_selectLink').modal('show');
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

$('#div_displayObject').delegate('.scenario-widget', 'dblclick', function() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('#md_modal').dialog({title: "{{Configuration du plan}}"});
        $('#md_modal').load('index.php?v=d&modal=plan.configure&link_type=scenario&link_id=' + $(this).attr('data-scenario_id') + '&planHeader_id=' + planHeader_id).dialog('open');
    }
});

$('#div_displayObject').delegate('.plan-link-widget', 'dblclick', function() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('#md_modal').dialog({title: "{{Configuration du plan}}"});
        $('#md_modal').load('index.php?v=d&modal=plan.configure&link_type=plan&link_id=' + $(this).attr('data-link_id') + '&planHeader_id=' + planHeader_id).dialog('open');
    }
});

$('#div_displayObject').delegate('.view-link-widget', 'dblclick', function() {
    if ($('#bt_editPlan').attr('data-mode') == "1") {
        $('#md_modal').dialog({title: "{{Configuration du plan}}"});
        $('#md_modal').load('index.php?v=d&modal=plan.configure&link_type=view&link_id=' + $(this).attr('data-link_id') + '&planHeader_id=' + planHeader_id).dialog('open');
    }
});

$('#bt_editPlan').on('click', function() {
    if ($(this).attr('data-mode') == '0') {
        initDraggable(1);
        $('.editMode').show();
        $(this).html('<i class="fa fa-pencil"></i> {{Quitter le mode édition}}');

        $(this).attr('data-mode', '1');
    } else {
        initDraggable(0);
        $('.editMode').hide();
        $(this).html('<i class="fa fa-pencil"></i> {{Mode édition}}');

        $(this).attr('data-mode', '0');
    }
});



function initDraggable(_state) {
    var offset = {};
    $('.eqLogic-widget').draggable({
        start: function(evt, ui) {
            offset.top = ui.offset.top;
            offset.left = ui.offset.left;
        },
        drag: function(evt, ui) {
            ui.position.top = Math.round(ui.position.top / $(this).css('zoom')) - Math.round(offset.top * $(this).css('zoom'));
            ui.position.left = Math.round(ui.position.left / $(this).css('zoom')) - Math.round(offset.left * $(this).css('zoom'));
        },
        stop: function(event, ui) {
            savePlan();
        }
    });
    $('.scenario-widget').draggable({
        stop: function(event, ui) {
            savePlan();
        }
    });
    $('.plan-link-widget').draggable({
        stop: function(event, ui) {
            savePlan();
        }
    });
    $('.view-link-widget').draggable({
        stop: function(event, ui) {
            savePlan();
        }
    });
    $('#div_displayObject a').each(function() {
        $(this).attr('data-href', $(this).attr('href'));
        $(this).attr('href', '#')
    });
    if (_state != 1 && _state != '1') {
        $('.eqLogic-widget').draggable("destroy");
        $('.scenario-widget').draggable("destroy");
        $('#div_displayObject a').each(function() {
            $(this).attr('href', $(this).attr('data-href'));
            $(this).attr('data-href', '#')
        });
    }
}

function displayPlan() {
    var img = $('#div_displayObject img');
    var size_x = img.attr('data-sixe_x');
    var size_y = img.attr('data-sixe_y');
    var ratio = size_x / size_y;
    var height = $(window).height() - $('header').height() - $('#div_planHeader').height() - 45;
    var width = $(window).width() - 22;
    if (height < 500) {
        height = 500;
    }
    if (width < 750) {
        width = 750;
    }
    $('#div_displayObject').height(height);
    $('#div_displayObject').width(width);
    var rWidth = width;
    var rHeight = width / ratio;
    if (rHeight > height) {
        rHeight = height;
        rWidth = height * ratio;
    }

    $('#div_displayObject img').height(rHeight);
    $('#div_displayObject img').width(rWidth);


    if (planHeader_id != -1) {
        jeedom.plan.byPlanHeader({
            id: planHeader_id,
            error: function(error) {
                $('#div_alert').showAlert({message: error.message, level: 'danger'});
            },
            success: function(data) {
                for (var i in data) {
                    console.log(data[i].plan.link_type);
                    displayObject(data[i].plan.link_type, data[i].plan.link_id, data[i].html, data[i].plan);
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
            plan.position.top = (((position.top * zoom)) / parent.height) * 100;
            plan.position.left = (((position.left * zoom)) / parent.width) * 100;
            plans.push(plan);
        });
        $('.scenario-widget').each(function() {
            var plan = {};
            plan.position = {};
            plan.link_type = 'scenario';
            plan.link_id = $(this).attr('data-scenario_id');
            plan.planHeader_id = planHeader_id;
            var zoom = $(this).css('zoom');
            $(this).css('zoom', '100%');
            var position = $(this).position();
            $(this).css('zoom', zoom);
            plan.position.top = ((position.top * zoom) / parent.height) * 100;
            plan.position.left = ((position.left * zoom) / parent.width) * 100;
            plans.push(plan);
        });
        $('.plan-link-widget').each(function() {
            var plan = {};
            plan.position = {};
            plan.link_type = 'plan';
            plan.link_id = $(this).attr('data-link_id');
            plan.planHeader_id = planHeader_id;
            var position = $(this).position();
            plan.position.top = ((position.top) / parent.height) * 100;
            plan.position.left = ((position.left) / parent.width) * 100;
            plans.push(plan);
        });
        $('.view-link-widget').each(function() {
            var plan = {};
            plan.position = {};
            plan.link_type = 'view';
            plan.link_id = $(this).attr('data-link_id');
            plan.planHeader_id = planHeader_id;
            var position = $(this).position();
            plan.position.top = ((position.top) / parent.height) * 100;
            plan.position.left = ((position.left) / parent.width) * 100;
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


function displayObject(_type, _id, _html, _plan) {
    _plan = init(_plan, {});
    _plan.position = init(_plan.position, {});
    _plan.css = init(_plan.css, {});
    if (_type == 'eqLogic') {
        var defaultZoom = 0.65;
        $('.eqLogic-widget[data-eqLogic_id=' + _id + ']').remove();
    }
    if (_type == 'scenario') {
        var defaultZoom = 1;
        $('.scenario-widget[data-scenario_id=' + _id + ']').remove();
    }
    if (_type == 'view') {
        var defaultZoom = 1;
        $('.view-link-widget[data-link_id=' + _id + ']').remove();
    }
    if (_type == 'plan') {
        var defaultZoom = 1;
        $('.plan-link-widget[data-link_id=' + _id + ']').remove();
    }
    var parent = {
        height: $('#div_displayObject img').height(),
        width: $('#div_displayObject img').width(),
    };
    var html = $(_html);
    html.css('position', 'absolute');
    html.css('top', init(_plan.position.top, '10') * parent.height / init(_plan.css.zoom, defaultZoom) / 100);
    html.css('left', init(_plan.position.left, '10') * parent.width / init(_plan.css.zoom, defaultZoom) / 100);
    html.css('zoom', init(_plan.css.zoom, defaultZoom));
    for (var key in _plan.css) {
        if (_plan.css[key] != '') {
            html.css(key, _plan.css[key]);
        }
    }
    $('#div_displayObject').append(html);

    if (_type == 'eqLogic') {
        if (isset(_plan.display) && isset(_plan.display.cmd)) {
            for (var id in _plan.display.cmd) {
                if (_plan.display.cmd[id] == 1) {
                    $('.cmd[data-cmd_id=' + id + ']').remove();
                }
            }
        }
    }
    initDraggable($('#bt_editPlan').attr('data-mode') == "1");
}

/***************************EqLogic**************************************/
function addEqLogic(_id, _plan) {
    jeedom.eqLogic.toHtml({
        id: _id,
        version: 'dashboard',
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            displayObject('eqLogic', _id, data.html, _plan);
            savePlan();
        }
    })
}



/***************************Scenario**************************************/
function addScenario(_id, _plan) {
    jeedom.scenario.toHtml({
        id: _id,
        version: 'dashboard',
        error: function(error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function(data) {
            displayObject('scenario', _id, data, _plan);
            savePlan();
        }
    })
}

/**********************************LINK************************************/
$('#md_selectLink .linkType').on('change', function() {
    $('#md_selectLink .linkOption').hide();
    $('#md_selectLink .link' + $(this).value()).show();
});

$('#md_selectLink .validate').on('click', function() {
    var link = {};
    link.type = $('#md_selectLink .linkType').value();
    link.id = $('#md_selectLink .link' + link.type + ' .linkId').value();
    link.name = $('#md_selectLink .link' + link.type + ' .linkId option:selected').text();
    $('#md_selectLink').modal('hide');
    addLink(link);
});

function addLink(_link, _plan) {
    var link = '';
    var label = '';
    if (_link.type == 'plan') {
        link = 'index.php?v=d&p=plan&plan_id=' + _link.id;
        label = 'label-success';
    }
    if (_link.type == 'view') {
        link = 'index.php?v=d&p=view&view_id=' + _link.id;
        label = 'label-primary';
    }
    var html = '<span class="' + _link.type + '-link-widget label ' + label + '" data-link_id="' + _link.id + '" >';
    html += '<a href="' + link + '" style="color:white;text-decoration:none;font-size : 1.5em;">';
    html += _link.name;
    html += '</a>';
    html += '</span>';
    displayObject(_link.type, _link.id, html, _plan)
    savePlan();
}
