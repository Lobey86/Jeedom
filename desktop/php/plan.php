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
        <a class="btn btn-warning btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_editPlanHeader"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-danger btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_removePlanHeader"><i class="fa fa-minus-circle"></i></a>
        <span class="editMode" style="display: none;">
            <input  id="bt_uploadImage" type="file" name="file" style="display: inline-block;">
        </span>
        <span class="editMode" style="display: none;">
            Grille : <input class="form-control ingrid input-sm planHeaderAttr" data-l1key='configuration' data-l2key="gridX" id="in_gridX" style="width: 50px;display: inline-block;"/> x <input class="form-control ingrid input-sm planHeaderAttr" id="in_gridY" data-l1key='configuration' data-l2key='gridY' style="width: 50px;display: inline-block;"/>
        </span>
        <a class="btn btn-warning pull-right btn-sm" style="margin-bottom: 3px;" id="bt_editPlan" data-mode="0"><i class="fa fa-pencil"></i> {{Mode édition}}</a>
        <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addGraph"><i class="fa fa-plus-circle"></i> {{Ajouter Graph}}</a>
        <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addLink"><i class="fa fa-plus-circle"></i> {{Ajouter lien}}</a>
        <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addScenario"><i class="fa fa-plus-circle"></i> {{Ajouter scénario}}</a>
        <a class="btn btn-info pull-right btn-sm editMode" style="margin-bottom: 3px;display: none;" id="bt_addEqLogic"><i class="fa fa-plus-circle"></i> {{Ajouter équipement}}</a>
    <?php } ?>
</div>
<div id="div_displayObject" style="min-height: 500px; min-width: 750px;position: relative;">
    <?php
    if (is_object($planHeader) && $planHeader->getImage('type') != '') {
        $size = $planHeader->getImage('size')
        ?>
        <img src="data:image/<?php echo $planHeader->getImage('type') ?>;base64,<?php echo $planHeader->getImage('data') ?>" data-sixe_y="<?php echo $size[1] ?>" data-sixe_x="<?php echo $size[0] ?>">
    <?php } ?>
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
<?php include_file('desktop', 'plan', 'js'); ?>