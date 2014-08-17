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
    <a class="btn btn-success btn-sm" style="margin-bottom: 3px;" id="bt_addPlanHeader"><i class="fa fa-plus-circle"></i></a>
    <a class="btn btn-warning btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_editPlanHeader"><i class="fa fa-pencil"></i></a>
    <a class="btn btn-danger btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_removePlanHeader"><i class="fa fa-minus-circle"></i></a>
    <span class="editMode" style="display: none;">
        <input  id="bt_uploadImage" type="file" name="file" style="display: inline-block;">
    </span>

    <a class="btn btn-warning pull-right btn-sm" style="margin-bottom: 3px;" id="bt_editPlan" data-mode="0"><i class="fa fa-pencil"></i> {{Mode édition}}</a>
    <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addScenario"><i class="fa fa-plus-circle"></i> {{Ajouter scénario}}</a>
    <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addEqLogic"><i class="fa fa-plus-circle"></i> {{Ajouter équipement}}</a>
</div>
<div id="div_displayObject" style="min-height: 500px; min-width: 750px;">
    <?php
    if (is_object($planHeader) && $planHeader->getImage('type') != '') {
        $size = $planHeader->getImage('size')
        ?>
        <center>
            <img src="data:image/<?php echo $planHeader->getImage('type') ?>;base64,<?php echo $planHeader->getImage('data') ?>" data-sixe_y="<?php echo $size[1] ?>" data-sixe_x="<?php echo $size[0] ?>">
        </center>
    <?php } ?>
</div>


<?php include_file('desktop', 'plan', 'js'); ?>