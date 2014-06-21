<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div class="row row-overflow">
    <div class="col-lg-3 bs-sidebar">
        <ul id="ul_history" class="nav nav-list bs-sidenav">
            <li class="nav-header">{{Historique}}</li>
            <li class="filter"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" /></li>
            <?php
            foreach (cmd::allHistoryCmd() as $cmd) {
                echo '<li class="cursor li_history" data-cmd_id="' . $cmd->getId() . '"><a class="history">' . $cmd->getHumanName() . '<i class="fa fa-trash-o remove pull-right"></i></a></li>';
            }
            ?>
        </ul>
    </div>

    <div class="col-lg-9" style="border-left: solid 1px #EEE; padding-left: 25px;height: 600px;">
        <select class="form-control pull-right" id="sel_chartType" style="width: 200px;">
            <option value="line">{{Ligne}}</option>
            <option value="areaspline">{{Areaspline}}</option>
            <option value="column">{{Barre}}</option>
        </select>
        <div id="div_graph" style="margin-top: 50px;"></div>
    </div>
</div>

<?php include_file("desktop", "history", "js"); ?>
