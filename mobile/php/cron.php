<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt">';
$rightPanel .= '<li data-role="list-divider">Action</li>';
$rightPanel .= '<li><a id="bt_refreshCron" href="#"><i class="fa fa-refresh"></i> Rafraîchir</a></li>';
$rightPanel .= '</ul>';
?>

<table data-role="table" id="table_cron" data-mode="columntoggle" class="ui-responsive table-stroke">
    <thead>
        <tr>
            <th data-priority="1">#</th>
            <th data-priority="6">{{Actif}}</th>
            <th data-priority="5">{{Serveur}}</th>
            <th data-priority="4">{{PID}}</th>
            <th data-priority="6">{{Demon}}</th>
            <th data-priority="1">{{Classe}}</th>
            <th data-priority="1">{{Fonction}}</th>
            <th data-priority="3">{{Dernier lancement}}</th>
            <th data-priority="2">{{Durée}}</th>
            <th data-priority="1">{{Satut}}</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>


<?php

include_file('mobile', 'cron', 'js');
?>

