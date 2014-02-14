<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>


<table id="table_market" class="table table-bordered table-condensed tablesorter">
    <thead>
        <tr>
            <th>Type</th>
            <th>ID</th>
            <th>Nom</th>
            <th>Auteur</th>
            <th>Catégorie</th>
            <th>Description</th>
            <th>Status</th>
            <th>Nombre de téléchargement</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach (market::byStatusAndType('Validé', 'plugin') as $market) {
            echo '<tr data-market_id="' . $market->getId() . '" class="cursor">';
            echo '<td>' . $market->getType() . '</td>';
            echo '<td>' . $market->getLogicalId() . '</td>';
            echo '<td>' . $market->getName() . '</td>';
            echo '<td>' . $market->getAuthor() . '</td>';
            echo '<td>' . $market->getCategorie() . '</td>';
            echo '<td>' . $market->getDescription() . '</td>';
            echo '<td>' . $market->getStatus() . '</td>';
            echo '<td>' . $market->getDownloaded() . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>


<script>

    initTableSorter();
    $('#table_market tbody tr').on('click', function() {
        $('#md_modal2').dialog({title: "Market Jeedom Display"});
        $('#md_modal2').load('index.php?v=d&modal=market.display&id=' + $(this).attr('data-market_id')).dialog('open');
    });
</script>