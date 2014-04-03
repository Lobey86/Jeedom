<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}

sendVarToJS('select_id', init('id', '-1'));
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_plugin" class="nav nav-list bs-sidenav fixnav">
                <center>
                    <a class="btn btn-success btn-xs tooltips cursor" id="bt_displayMarket" style="display: inline-block;"><i class="fa fa-shopping-cart"></i> Télécharger du market</a>
                </center>
                <li class="nav-header">Liste plugin</li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (plugin::listPlugin() as $plugin) {
                    $status = $plugin->status();
                    echo '<li class="cursor li_plugin" data-pluginPath="' . $plugin->getFilepath() . '" data-plugin_id="' . $plugin->getId() . '"><a>';

                    echo '<i class="' . $plugin->getIcon() . '"></i> ' . $plugin->getName();
                    if ($plugin->isActive() == 1) {
                        if ($status['status'] == 'depreciated') {
                            echo '<i class="fa fa-times pull-right tooltips" title="Plugin non maintenu ou supprimé"></i>';
                        }
                        if ($status['status'] == 'ok') {
                            echo '<i class="fa fa-check pull-right tooltips" title="Plugin à jour"></i>';
                        }
                        if ($status['status'] == 'update') {
                            echo '<i class="fa fa-refresh pull-right tooltips" title="Mise à jour nécessaire"></i>';
                        }
                        echo '<span class="binary green pull-right"></span> ';
                    } else {
                        echo '<span class="binary red pull-right"></span> ';
                    }
                    echo '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10" id="div_confPlugin" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <legend>
            <span id="span_plugin_name" ></span> (<span id="span_plugin_id"></span>)
             <span id="span_plugin_toggleState" class="pull-right"></span>
             <span id="span_plugin_market" class="pull-right"></span>
        </legend>
        <div class="alert alert-info">
            <h5 style="display: inline-block;font-weight: bold;">Description : </h5> <span id="span_plugin_description"></span>
        </div>
        <div class="alert alert-danger">
            <h5 style="display: inline-block;font-weight: bold;">Installation : </h5> <span id="span_plugin_installation"></span>
        </div>
        <div class="alert alert-success">
            <h5 style="display: inline-block;font-weight: bold;">Version plugin : </h5> <span id="span_plugin_version"></span> - 
            <h5 style="display: inline-block;font-weight: bold;">Version Jeedom requis : </h5> <span id="span_plugin_require"></span>
        </div>
        <div class="alert alert-warning">
            <h5 style="display: inline-block;font-weight: bold;">Auteur : </h5> <span id="span_plugin_author"></span> - 
            <h5 style="display: inline-block;font-weight: bold;">Licence : </h5> <span id="span_plugin_licence"></span>
        </div>
        <div>
            <legend>Configuration</legend>
            <div id="div_plugin_configuration"></div>

            <div class="form-actions">
                <a class="btn btn-success" id="bt_savePluginConfig"><i class="fa fa-check-circle icon-white" style="position:relative;left:-5px;top:1px"></i>Sauvegarder</a>
            </div>
        </div>
    </div>
</div>

<?php include_file("desktop", "plugin", "js"); ?>
