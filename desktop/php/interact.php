<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$sel_eqType = '<select class=\'interactDefAttr tooltips form-control input-sm\' title=\'{{Limiter aux équipement de type}}\' data-l1key=\'filtres\' data-l2key=\'plugin\' style=\'margin-top : 5px;\'>';
$sel_eqType .= '<option value=\'all\' >{{Tous}}</option>';
foreach (eqLogic::allType() as $type) {
    $sel_eqType .= '<option value=\'' . $type['type'] . '\' >' . $type['type'] . '</option>';
}
$sel_eqType .= '</select>';
sendVarToJS('sel_eqType', $sel_eqType);


$sel_unite = '<select class=\'interactDefAttr tooltips form-control input-sm\' data-l1key=\'filtres\' data-l2key=\'cmd_unite\' title=\'{{Limiter aux commandes ayant pour unité}}\'>';
$sel_unite .= '<option value=\'all\' >{{Toutes}}</option>';
foreach (cmd::allUnite() as $unite) {
    $sel_unite .= '<option value=\'' . $unite['unite'] . '\' >' . $unite['unite'] . '</option>';
}
$sel_unite .= '</select>';
sendVarToJS('sel_unite', $sel_unite);
?>


<a class="btn btn-success pull-right" id="bt_save"><i class="fa fa-check-circle"></i> {{Enregistrer}}</a>
<a class="btn btn-default pull-right" id="bt_addSarahDef"><i class="fa fa-plus-circle"></i> {{Ajouter}}</a>
<br/><br/><br/>
<table class="table table-bordered table-condensed table-striped" id="table_interactDef">
    <thead>
        <tr>
            <th style="width : 350px;">{{Filtre}}</th>
            <th>{{Phrase}}</th>
            <th style="width : 360px;">{{Type}}</th>
            <th style="width : 100px;">{{Personne}}</th>
            <th style="width : 110px;">{{Action}}</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<?php include_file('desktop', 'interact', 'js'); ?>
