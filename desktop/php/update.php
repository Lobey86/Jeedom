<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}


?>
<a class="btn btn-default pull-right">Verifier les mises à jour</a><br/><br/>
<table class="table table-condensed table-bordered tablesorter" id="table_update" style="margin-top: 5px;">
    <thead>
        <tr>
            <th>{{Type}}</th>
            <th>{{Nom}}</th>
            <th>{{Version actuel}}</th>
            <th>{{Version disponible}}</th>
            <th>{{Status}}</th>
            <th data-sorter="false" data-filter="false">{{Action}}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach (update::all() as $update) {
            echo '<tr data-message_id="' . $update->getId() . '">';
            echo '<td>' . $update->getType() . '</td>';
            echo '<td>' . $update->getName() . '</td>';
            echo '<td>' . $update->getLocalVersion() . '</td>';
            echo '<td>' . $update->getRemoteVersion() . '</td>';
            echo '<td>' . $update->getStatus() . '</td>';
            echo '<td></td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>