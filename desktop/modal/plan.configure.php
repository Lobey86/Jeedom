<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$plan = plan::byLinkTypeLinkIdPlanHedaerId(init('link_type'), init('link_id'), init('planHeader_id'));
if (!is_object($plan)) {
    throw new Exception('Impossible de trouver le plan');
}
sendVarToJS('id', $plan->getId());
?>
<div id="div_alertPlanConfigure"></div>
<a class='btn btn-success btn-xs pull-right cursor' style="color: white;" id='bt_saveConfigurePlan'><i class="fa fa-check"></i> Sauvegarder</a>
<a class='btn btn-danger  btn-xs pull-right cursor' style="color: white;" id='bt_removeConfigurePlan'><i class="fa fa-times"></i> Supprimer</a>
<form class="form-horizontal">
    <fieldset id="fd_planConfigure">
        <div class="form-group">
            <label class="col-lg-2 control-label">{{Taille du widget}}</label>
            <div class="col-lg-2">
                <input type="text"  class="planAttr form-control" data-l1key="id" style="display: none;"/>
                <input type="text"  class="planAttr form-control" data-l1key="link_type" style="display: none;"/>
                <input type="text"  class="planAttr form-control" data-l1key="link_id" style="display: none;"/>
                <input type="text" class="planAttr form-control" data-l1key="css" data-l2key="zoom" value="0.65"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">{{Couleur de fond}}</label>
            <div class="col-lg-2">
                <select class="planAttr form-control" data-l1key="css" data-l2key="background-color">
                    <option value="">Normale</option>
                    <option value="transparent">Transparent</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">{{Couleur des icones et textes}}</label>
            <div class="col-lg-2">
                <input type="color" class="planAttr form-control" data-l1key="css" data-l2key="color" value="#FFFFFF"/>
            </div>
        </div>
    </fieldset>
</form>


<script>

    $('#bt_saveConfigurePlan').on('click', function() {
        save();
    });

    $('#bt_removeConfigurePlan').on('click', function() {
        bootbox.confirm('Etes-vous sûr de vouloir supprimer cet object du plan ?', function(result) {
            if (result) {
                remove();
            }
        });
    });

    if (isset(id) && id != '') {
        load(id);
    }

    function load(_id) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/plan.ajax.php", // url du fichier php
            data: {
                action: "get",
                id: _id
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_alertPlanConfigure'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertPlanConfigure').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#fd_planConfigure').setValues(data.result, '.planAttr');
                if ($(".planAttr[data-l1key=link_type]").value() == 'eqLogic') {
                    addEqLogic(data.result.link_id, data.result);
                }
            }
        });
    }


    function save() {
        jeedom.plan.save({
            plans: $('#fd_planConfigure').getValues('.planAttr'),
            error: function(error) {
                $('#div_alertPlanConfigure').showAlert({message: error.message, level: 'danger'});
            },
            success: function() {
                $('#div_alertPlanConfigure').showAlert({message: 'Plan sauvegardé', level: 'success'});
                load(id);
            },
        });
    }

    function remove() {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/plan.ajax.php", // url du fichier php
            data: {
                action: "remove",
                id: $(".planAttr[data-l1key=id]").value()
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_alertPlanConfigure'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertPlanConfigure').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alertPlanConfigure').showAlert({message: 'Plan supprimé', level: 'success'});
                if ($(".planAttr[data-l1key=link_type]").value() == 'eqLogic') {
                    $('.eqLogic-widget[data-eqLogic_id=' + $(".planAttr[data-l1key=link_id]").value() + ']').remove();
                }
            }
        });
    }

</script>