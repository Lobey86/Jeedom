<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

$sel_eqType = '<select class=\'interactDefAttr tooltips form-control input-sm\' title=\'Limiter aux équipement de type\' l1key=\'filtres\' l2key=\'eqType_name\' style=\'margin-top : 5px;\'>';
$sel_eqType .= '<option value=\'all\' >Tous</option>';
foreach (eqLogic::allType() as $type) {
    $sel_eqType .= '<option value=\'' . $type['type'] . '\' >' . $type['type'] . '</option>';
}
$sel_eqType .= '</select>';
sendVarToJS('sel_eqType', $sel_eqType);

$sel_object = '<select class=\'interactDefAttr tooltips form-control input-sm\' l1key=\'filtres\' l2key=\'object_id\' title=\'Limiter aux commandes appartenant à l objet\' style=\'margin-top : 5px;\'>';
$sel_object .= '<option value=\'all\' >Tous</option>';
foreach (object::all() as $object) {
    $sel_object .= '<option value=\'' . $object->getId() . '\' >' . $object->getName() . '</option>';
}
$sel_object .= '</select>';
sendVarToJS('sel_object', $sel_object);

$sel_unite = '<select class=\'interactDefAttr tooltips form-control input-sm\' l1key=\'filtres\' l2key=\'cmd_unite\' title=\'Limiter aux commandes ayant pour unité\'>';
$sel_unite .= '<option value=\'all\' >Toutes</option>';
foreach (cmd::allUnite() as $unite) {
    $sel_unite .= '<option value=\'' . $unite['unite'] . '\' >' . $unite['unite'] . '</option>';
}
$sel_unite .= '</select>';
sendVarToJS('sel_unite', $sel_unite);

$sel_subtype = '<select class=\'interactDefAttr tooltips form-control input-sm\' l1key=\'filtres\' l2key=\'subtype\' title=\'Limiter aux commandes ayant pour sous-type\'>';
$sel_subtype .= '<option value=\'all\' >Tous</option>';
$sel_subtype .= '<option value=\'color\' >Couleur</option>';
$sel_subtype .= '<option value=\'binary\' >Binaire</option>';
$sel_subtype .= '<option value=\'slider\' >Slider</option>';
$sel_subtype .= '<option value=\'numeric\' >Numérique</option>';
$sel_subtype .= '<option value=\'string\' >Autre</option>';
$sel_subtype .= '</select>';
sendVarToJS('sel_subtype', $sel_subtype);
?>


<div class="row">
    <div class="col-lg-12">
        <a class="btn btn-success pull-right" id="bt_save"><i class="fa fa-check-circle"></i> Enregistrer</a>
        <a class="btn btn-default pull-right" id="bt_addSarahDef"><i class="fa fa-plus-circle"></i> Ajouter</a>
        <br/><br/><br/>
        <table class="table table-bordered table-condensed table-striped" id="table_interactDef">
            <thead>
                <tr>
                    <th style="width : 350px;">Filtre</th>
                    <th>Phrase</th>
                    <th style="width : 360px;">Type</th>
                    <th style="width : 100px;">Personne</th>
                    <th style="width : 110px;">Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<div id="md_help" title="Aide sur la configuration des interactions"></div>

<div id="md_queries" title="Requete réel"></div>

<?php include_file('desktop', 'interact', 'js'); ?>
