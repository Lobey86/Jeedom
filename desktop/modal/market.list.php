<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('3rdparty', 'jquery.lazyload/jquery.lazyload', 'js');
include_file('3rdparty', 'bootstrap.rating/bootstrap.rating', 'js');

$markets = market::byStatusAndType('stable', init('type'));
if (config::byKey('market::showToValidateMarket') == 1) {
    $markets = array_merge($markets, market::byStatusAndType('beta', init('type')));
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
            <th style="width: 100px;">{{Note}}</th>
            <th>{{Téléchargé}}</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($markets as $market) {
            $rating = $market->getRating();
            echo '<tr data-market_id="' . $market->getId() . '" data-market_type="' . $market->getType() . '" class="cursor" style="height:70px;">';
            if ($market->getStatus('stable') == 1 && $market->getImg('stable')) {
                $urlPath = config::byKey('market::address') . '/' . $market->getImg('stable');
            } else {
                if ($market->getImg('beta')) {
                    $urlPath = config::byKey('market::address') . '/' . $market->getImg('beta');
                }
            }
            echo '<td><center><img src="core/img/no_image.gif" data-original="' . $urlPath . '"  class="lazy" height="70" /></center></td>';
            echo '<td>' . $market->getCategorie() . '</td>';
            echo '<td>' . $market->getName() . '</td>';
            echo '<td>' . $market->getDescription() . '</td>';
            echo '<td>' . $market->getStatus() . '</td>';
            echo '<td><center><input type="number" class="rating" data-max="5" data-empty-value="0" data-min="1" value="' . $market->getRating() . '" data-disabled="1" /></center></td>';
            echo '<td><center>' . $market->getDownloaded() . '</center></td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<script>
    $(function() {
        $("img.lazy").lazyload({
            event: "sporty"
        });
        $("img.lazy").trigger("sporty");
        initTableSorter();


        $('#table_market tbody tr').on('click', function() {
            $('#md_modal2').dialog({title: "{{Market Jeedom}}"});
            $('#md_modal2').load('index.php?v=d&modal=market.display&type=' + $(this).attr('data-market_type') + '&id=' + $(this).attr('data-market_id')).dialog('open');
        });
    });
</script>