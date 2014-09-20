<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$plan = plan::byLinkTypeLinkIdPlanHedaerId(init('link_type'), init('link_id'), init('planHeader_id'));
if (!is_object($plan)) {
    throw new Exception('Impossible de trouver le plan');
}
$link = $plan->getLink();
sendVarToJS('id', $plan->getId());
?>
<div id="div_alertPlanConfigure"></div>
<a class='btn btn-success btn-xs pull-right cursor' style="color: white;" id='bt_saveConfigurePlan'><i class="fa fa-check"></i> Sauvegarder</a>
<a class='btn btn-danger  btn-xs pull-right cursor' style="color: white;" id='bt_removeConfigurePlan'><i class="fa fa-times"></i> Supprimer</a>
<form class="form-horizontal">
    <fieldset id="fd_planConfigure">
        <legend>Général</legend>
        <input type="text"  class="planAttr form-control" data-l1key="id" style="display: none;"/>
        <input type="text"  class="planAttr form-control" data-l1key="link_type" style="display: none;"/>
        <input type="text"  class="planAttr form-control" data-l1key="link_id" style="display: none;"/>
        <?php if ($plan->getLink_type() == 'eqLogic' || $plan->getLink_type() == 'scenario') { ?>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Taille du widget}}</label>
                <div class="col-lg-2">
                    <?php
                    if ($plan->getLink_type() == 'eqLogic') {
                        echo '<input type="text" class="planAttr form-control" data-l1key="css" data-l2key="zoom" value="0.65"/>';
                    }
                    if ($plan->getLink_type() == 'scenario') {
                        echo '<input type="text" class="planAttr form-control" data-l1key="css" data-l2key="zoom" value="1"/>';
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Couleur de fond}}</label>
                <div class="col-lg-2">
                    <select class="planAttr form-control" data-l1key="css" data-l2key="background-color">
                        <option value="">Normale</option>
                        <option value="transparent">Transparent</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Couleur des icones et textes}}</label>
                <div class="col-lg-2">
                    <input type="color" class="planAttr form-control" data-l1key="css" data-l2key="color" value="#FFFFFF"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Ne pas afficher le nom de l'équipement}}</label>
                <div class="col-lg-2">
                    <input type="checkbox" class="planAttr" data-l1key="display" data-l2key="name" >
                </div>
            </div>
            <legend>Spécifique</legend>
            <?php
            if ($plan->getLink_type() == 'eqLogic' && is_object($link)) {
                foreach ($link->getCmd() as $cmd) {
                    if ($cmd->getIsVisible() == 1) {
                        echo '<div class="form-group">';
                        echo '<label class="col-lg-4 control-label">{{Ne pas afficher }}' . $cmd->getHumanName() . '</label>';
                        echo '<div class="col-lg-2">';
                        echo '<input type="checkbox" class="planAttr" data-l1key="display" data-l2key="cmd" data-l3key="' . $cmd->getID() . '" />';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
            ?>
        <?php } else if ($plan->getLink_type() == 'graph') { ?>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Période}}</label>
                <div class="col-lg-2">
                    <select class="planAttr form-control" data-l1key="display" data-l2key="dateRange">
                        <option value="30 min">{{30min}}</option>
                        <option value="1 day">{{Jour}}</option>
                        <option value="7 days" selected>{{Semaine}}</option>
                        <option value="1 month">{{Mois}}</option>
                        <option value="1 year">{{Années}}</option>
                        <option value="all">{{Tous}}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Bordure}}</label>
                <div class="col-lg-2">
                    <select class="planAttr form-control" data-l1key="css" data-l2key="border">
                        <option value="solid 1px black">Oui</option>
                        <option value="none">Non</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Afficher la légende}}</label>
                <div class="col-lg-2">
                    <input type="checkbox" checked class="planAttr" data-l1key="display" data-l2key="showLegend" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Afficher le selecteur de période}}</label>
                <div class="col-lg-2">
                    <input type="checkbox" class="planAttr" checked data-l1key="display" data-l2key="showTimeSelector" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Afficher la barre de défilement}}</label>
                <div class="col-lg-2">
                    <input type="checkbox" class="planAttr" checked data-l1key="display" data-l2key="showScrollbar" >
                </div>
            </div>
        <?php } else if ($plan->getLink_type() == 'plan' || $plan->getLink_type() == 'view') { ?>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Nom}}</label>
                <div class="col-lg-2">
                    <input class="planAttr form-control" data-l1key="display" data-l2key="name" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">{{Icône}}</label>
                <div class="col-lg-2">
                    <div class="planAttr" data-l1key="display" data-l2key="icon" ></div>
                </div>
                <div class="col-lg-2">
                    <a class="btn btn-default btn-sm" id="bt_chooseIcon"><i class="fa fa-flag"></i> {{Choisir une icône}}</a>
                </div>
            </div>
        <?php } ?>
    </fieldset>
</form>


<script>
    $('#bt_chooseIcon').on('click', function () {
        chooseIcon(function (_icon) {
            $('.planAttr[data-l1key=display][data-l2key=icon]').empty().append(_icon);
        });
    });

    $('#bt_saveConfigurePlan').on('click', function () {
        save();
    });

    $('#bt_removeConfigurePlan').on('click', function () {
        bootbox.confirm('Etes-vous sûr de vouloir supprimer cet object du plan ?', function (result) {
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
            error: function (request, status, error) {
                handleAjaxError(request, status, error, $('#div_alertPlanConfigure'));
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertPlanConfigure').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#fd_planConfigure').setValues(data.result, '.planAttr');
            }
        });
    }


    function save() {
        jeedom.plan.save({
            plans: $('#fd_planConfigure').getValues('.planAttr'),
            error: function (error) {
                $('#div_alertPlanConfigure').showAlert({message: error.message, level: 'danger'});
            },
            success: function () {
                $('#div_alertPlanConfigure').showAlert({message: 'Plan sauvegardé', level: 'success'});
                displayPlan();
                $('#fd_planConfigure').closest("div.ui-dialog-content").dialog("close");
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
            error: function (request, status, error) {
                handleAjaxError(request, status, error, $('#div_alertPlanConfigure'));
            },
            success: function (data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertPlanConfigure').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_alertPlanConfigure').showAlert({message: 'Plan supprimé', level: 'success'});
                displayPlan();
                $('#fd_planConfigure').closest("div.ui-dialog-content").dialog("close");
            }
        });
    }

</script>