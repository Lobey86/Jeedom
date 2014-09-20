<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('3rdparty', 'jquery.fileupload/jquery.ui.widget', 'js');
include_file('3rdparty', 'jquery.fileupload/jquery.iframe-transport', 'js');
include_file('3rdparty', 'jquery.fileupload/jquery.fileupload', 'js');
$planHeader = planHeader::byId(init('id'));
$planHeaders = planHeader::all();

if (init('plan_id') == '') {
    $planHeader = planHeader::byId($_SESSION['user']->getOptions('defaultDashboardPlan'));
} else {
    $planHeader = planHeader::byId(init('plan_id'));
}
if (!is_object($planHeader) && count($planHeaders) > 0) {
    $planHeader = $planHeaders[0];
}
if (is_object($planHeader)) {
    sendVarToJS('planHeader_id', $planHeader->getId());
} else {
    sendVarToJS('planHeader_id', -1);
}
?>
<div id="div_planHeader">
    <select class="form-control input-sm" style="width: 200px;display: inline-block" id="sel_planHeader">
        <?php
        foreach (planHeader::all() as $planHeader_select) {
            if ($planHeader_select->getId() == $planHeader->getId()) {
                echo '<option selected value="' . $planHeader_select->getId() . '">' . $planHeader_select->getName() . '</option>';
            } else {
                echo '<option value="' . $planHeader_select->getId() . '">' . $planHeader_select->getName() . '</option>';
            }
        }
        ?>
    </select>
    <?php if (isConnect('admin')) { ?>
        <a class="btn btn-success btn-sm" style="margin-bottom: 3px;" id="bt_addPlanHeader"><i class="fa fa-plus-circle"></i></a>
        <a class="btn btn-default btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_configurePlanHeader"><i class="fa fa-cogs"></i></a>
        <a class="btn btn-warning pull-right btn-sm" style="margin-bottom: 3px;" id="bt_editPlan" data-mode="0"><i class="fa fa-pencil"></i> {{Mode édition}}</a>
        <?php if (is_object($planHeader)) { ?>
            <a class="btn btn-success pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_savePlan" data-mode="0"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
            <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addGraph"><i class="fa fa-plus-circle"></i> {{Ajouter Graph}}</a>
            <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addLink"><i class="fa fa-plus-circle"></i> {{Ajouter lien}}</a>
            <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addScenario"><i class="fa fa-plus-circle"></i> {{Ajouter scénario}}</a>
            <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addEqLogic"><i class="fa fa-plus-circle"></i> {{Ajouter équipement}}</a>
            <?php
        }
    }
    ?>
</div>
<div id="div_displayObject" style="position: relative;">
    <?php
    if (is_object($planHeader) && $planHeader->getImage('type') != '') {
        echo $planHeader->displayImage();
    }
    ?>
</div>

<div class="modal fade" id="md_selectLink">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{{Selectionnez un lien}}</h4>
            </div>
            <div class="modal-body">
                <select class="form-control linkType">
                    <option value="plan">Plan</option>
                    <option value="view">Vue</option>
                </select>
                <br/>
                <div class="linkplan linkOption">
                    <select class="form-control linkId">
                        <?php
                        foreach (planHeader::all() as $planHeader_select) {
                            if ($planHeader_select->getId() != $planHeader->getId()) {
                                echo '<option value="' . $planHeader_select->getId() . '">' . $planHeader_select->getName() . '</option>';
                            }
                        }
                        ?>   
                    </select>
                </div>
                <div class="linkview linkOption" style="display: none;">
                    <select class="form-control linkId">
                        <?php
                        foreach (view::all() as $views) {
                            echo '<option value="' . $views->getId() . '">' . $views->getName() . '</option>';
                        }
                        ?>   
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{Annuler}}</button>
                <button type="button" class="btn btn-primary validate">{{Valider}}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->




<div id="md_addViewData" title="Ajouter widget/graph">
    <table id="table_addViewDataHidden" style="display: none;">
        <tbody></tbody>
    </table>
    <table class="table table-condensed table-bordered table-striped" id="table_addViewData">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 150px;">{{Type}}</th>
                <th style="width: 150px;">{{Objet}}</th>
                <th style="width: 150px;">{{Nom}}</th>
                <th>Affichage</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (cmd::all() as $cmd) {
                $eqLogic = $cmd->getEqLogic();
                if (!is_object($eqLogic)) {
                    continue;
                }
                if ($eqLogic->getIsVisible() == 1 && $cmd->getIsHistorized() == 1) {
                    $object = $cmd->getEqLogic()->getObject();
                    echo '<tr data-link_id="' . $cmd->getId() . '" data-type="graph" data-viewDataType="cmd">';
                    echo '<td>';
                    echo '<input type="checkbox" class="enable" />';
                    echo '<input class="graphDataOption" data-l1key="link_id" value="' . $cmd->getId() . '" hidden/>';
                    echo '</td>';
                    echo '<td class="type">';
                    echo 'Commande';
                    echo '<input class="graphDataOption" data-l1key="type" value="cmd" hidden/>';
                    echo '</td>';
                    echo '<td class="object_name">';
                    if (is_object($object)) {
                        echo $object->getName();
                    }
                    echo '</td>';
                    echo '<td class="name">';
                    echo '[' . $eqLogic->getName() . '][';
                    echo $cmd->getName() . ']';
                    echo '</td>';
                    echo '<td class="display">';
                    echo '<div class="option">';
                    echo '<form class="form-inline">';
                    echo '<div class="form-group">';
                    echo '<label>Couleur :</label> <select class="graphDataOption form-control" data-l1key="configuration" data-l2key="graphColor" style="width : 110px;background-color:#4572A7;color:white;">';
                    echo '<option value="#4572A7" style="background-color:#4572A7;color:white;">{{Bleu}}</option>';
                    echo '<option value="#AA4643" style="background-color:#AA4643;color:white;">{{Rouge}}</option>';
                    echo '<option value="#89A54E" style="background-color:#89A54E;color:white;">{{Vert}}</option>';
                    echo '<option value="#80699B" style="background-color:#80699B;color:white;">{{Violet}}</option>';
                    echo '<option value="#00FFFF" style="background-color:#00FFFF;color:white;">{{Bleu ciel}}</option>';
                    echo '<option value="#DB843D" style="background-color:#DB843D;color:white;">{{Orange}}</option>';
                    echo '<option value="#FFFF00" style="background-color:#FFFF00;color:white;">{{Jaune}}</option>';
                    echo '<option value="#FE2E9A" style="background-color:#FE2E9A;color:white;">{{Rose}}</option>';
                    echo '<option value="#000000" style="background-color:#000000;color:white;">{{Noir}}</option>';
                    echo '<option value="#3D96AE" style="background-color:#3D96AE;color:white;">{{Vert/Bleu}}</option>';
                    echo '</select> ';
                    echo '</div> ';
                    echo '<div class="form-group">';
                    echo ' <label>Type :</label> <select class="graphDataOption form-control" data-l1key="configuration" data-l2key="graphType" style="width : 100px;">';
                    echo '<option value="line">{{Ligne}}</option>';
                    echo '<option value="area">{{Aire}}</option>';
                    echo '<option value="column">{{Colonne}}</option>';
                    echo '</select> ';
                    echo '</div> ';
                    echo '<div class="form-group">';
                    echo '';
                    echo ' <label>Escalier : <input type="checkbox" class="graphDataOption" data-l1key="configuration" data-l2key="graphStep">';
                    echo '</label>';
                    echo ' <label>Empiler : <input type="checkbox" class="graphDataOption" data-l1key="configuration" data-l2key="graphStack">';
                    echo '</label>';
                    echo ' <label>Variation : <input type="checkbox" class="graphDataOption" data-l1key="configuration" data-l2key="derive">';
                    echo '</label>';
                    echo ' <label>Echelle :</label> <select class="graphDataOption form-control" data-l1key="configuration" data-l2key="graphScale" style="width : 60px;">';
                    echo '<option value="0">0</option>';
                    echo '<option value="1">1</option>';
                    echo '</select>';

                    echo '</div>';
                    echo '</form>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>



<?php include_file('desktop', 'plan', 'js'); ?>