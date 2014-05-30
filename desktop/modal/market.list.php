<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('3rdparty', 'jquery.lazyload/jquery.lazyload', 'js');

$markets = market::byStatusAndType('Validé', init('type'));
if (config::byKey('market::showToValidateMarket') == 1) {
    $markets = array_merge($markets, market::byStatusAndType('A valider', init('type')));
} else {
    if (config::byKey('market::apikey') != '') {
        foreach (market::byMe() as $myMarket) {
            if ($myMarket->getStatus() != 'Validé' && $myMarket->getType() == init('type')) {
                $markets[] = $myMarket;
            }
        }
    }
}
?>


<table id="table_market" class="table table-bordered table-condensed tablesorter" data-sortlist="[[2,0]]">
    <thead>
        <tr>
            <th data-sorter="false"></th>
            <th>{{Catégorie}}</th>
            <th>{{Nom}}</th>
            <th>{{Description}}</th>
            <th>{{Statut}}</th>
            <th>{{Auteur}}</th>
            <th>{{Téléchargé}}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($markets as $market) {
            echo '<tr data-market_id="' . $market->getId() . '" data-market_type="' . $market->getType() . '" class="cursor">';
            echo '<td><img src="core/img/no_image.gif" data-original="' . config::byKey('market::address') . '/market/' . $market->getType() . '/' . $market->getLogicalId() . '.jpg"  class="img-responsive lazy" width="70" height="70" /></td>';
            echo '<td>' . $market->getCategorie() . '</td>';
            echo '<td>' . $market->getName() . '</td>';
            echo '<td>' . $market->getDescription() . '</td>';
            echo '<td>' . $market->getStatus() . '</td>';
            echo '<td>' . $market->getAuthor() . '</td>';
            echo '<td>' . $market->getDownloaded() . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<script>
    $(function() {
        $("img.lazy").lazyload({
            event : "sporty"
        });
         $("img.lazy").trigger("sporty");
        initTableSorter();


        $('#table_market tbody tr').on('click', function() {
            $('#md_modal2').dialog({title: "{{Market Jeedom}}"});
            $('#md_modal2').load('index.php?v=d&modal=market.display&type=' + $(this).attr('data-market_type') + '&id=' + $(this).attr('data-market_id')).dialog('open');
        });
    });
</script>