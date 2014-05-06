<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}


?>
<a class="btn btn-default pull-right" id="bt_checkAllUpdate"><i class="fa fa-refresh"></i> Verifier les mises à jour</a><br/><br/>
<table class="table table-condensed table-bordered tablesorter" id="table_update" style="margin-top: 5px;">
    <thead>
        <tr>
            <th></th>
            <th>{{Type}}</th>
            <th>{{Nom}}</th>
            <th>{{Version actuel}}</th>
            <th>{{Version disponible}}</th>
            <th>{{Status}}</th>
            <th data-sorter="false" data-filter="false">{{Action}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php include_file('desktop', 'update', 'js'); ?>