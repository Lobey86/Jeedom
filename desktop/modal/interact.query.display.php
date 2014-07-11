<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
if (init('interactDef_id') == '') {
    throw new Exception('{{Interact Def ID ne peut etre vide}}');
}

$interactQueries = interactQuery::byInteractDefId(init('interactDef_id'));
if (count($interactQueries) == 0) {
    throw new Exception('{{Aucune phrase trouvée}}');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
?>

<div style="display: none;" id="md_displayInteractQueryAlert"></div>

<table class="table table-bordered table-condensed tablesorter" id="table_interactQuery">
    <thead>
        <tr>
            <th>{{Phrase}}</th>
            <th>{{Commande}}</th>
            <th>{{Action}}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($interactQueries as $interactQuery) {
            $trClass = ($interactQuery->getEnable() == 1) ? 'success' : 'danger';
            echo '<tr class="' . $trClass . '" data-interactQuery_id="' . $interactQuery->getId() . '">';
            echo '<td>' . $interactQuery->getQuery() . '</td>';
            echo '<td>';
            if ($interactQuery->getLink_type() == 'cmd') {
                echo str_replace('#', '', cmd::cmdToHumanReadable('#' . $interactQuery->getLink_id() . '#'));
            }
            echo '</td>';
            echo '<td>';
            if ($interactQuery->getEnable() == 1) {
                echo '<a class="btn btn-danger btn-xs changeEnable" data-state="0" style="color : white;">{{Désactiver}}</a>';
            } else {
                echo '<a class="btn btn-success btn-xs changeEnable" data-state="1" style="color : white;">{{Activer}}</a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<script>
    initTableSorter();
    
    $('#table_interactQuery .changeEnable').on('click', function() {
        var tr = $(this).closest('tr');
        var btn = $(this);
        $.ajax({
            type: 'POST',
            url: "core/ajax/interact.ajax.php", // url du fichier php
            data: {
                action: 'changeState',
                id: tr.attr('data-interactQuery_id'),
                enable: btn.attr('data-state'),
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error,$('#md_displayInteractQueryAlert'));
            },
            success: function(data) {
                if (data.state != 'ok') {
                    $('#md_displayInteractQueryAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                if (btn.attr('data-state') == 1) {
                    tr.removeClass('danger').addClass('success');
                    btn.attr('data-state', 0);
                    btn.removeClass('btn-success').addClass('btn-danger');
                    btn.text('{{Désactiver}}');
                } else {
                    tr.removeClass('success').addClass('danger');
                    btn.attr('data-state', 1);
                    btn.removeClass('btn-danger').addClass('btn-success');
                    btn.text('{{Activer}}');
                }
            }
        });
    });

</script>