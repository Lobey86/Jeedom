<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

sendVarToJS('select_id', init('id', '-1'));
?>

<div class="row">
    <div class="col-lg-2 bs-sidebar">
        <ul id="ul_module" class="nav nav-list bs-sidenav fixnav">
            <li class="nav-header">Liste module</li>
            <li class="filter" style="margin-bottom: 5px;"><input class="form-control" class="filter form-control" placeholder="Rechercher" style="width: 100%"/></li>
            <?php
            foreach (module::listModule() as $module) {
                echo '<li class="cursor li_module" modulePath="' . $module->getFilepath() . '" module_id="' . $module->getId() . '"><a >';

                echo '<i class="' . $module->getIcon() . '"></i> ' . $module->getName();
                if ($module->isActive() == 1) {
                    echo '<span class="binary green pull-right"></span> ';
                } else {
                    echo '<span class="binary red pull-right"></span> ';
                }
                echo '</a></li>';
            }
            ?>
        </ul>
    </div>
    <div class="col-lg-10" id="div_confModule" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <legend>
            <span id="span_module_toggleState"></span>
            <span id="span_module_name" ></span> (<span id="span_module_id"></span>)
        </legend>
        <div class="alert alert-info">
            <h5 style="display: inline-block">Description : </h5> <span id="span_module_description"></span>
        </div>
        <div class="alert alert-danger">
            <h5 style="display: inline-block">Installation : </h5> <span id="span_module_installation"></span>
        </div>
        <div class="alert alert-success">
            <h5 style="display: inline-block">Version module : </h5> <span id="span_module_version"></span> - 
            <h5 style="display: inline-block">Version Jeedom requis : </h5> <span id="span_module_require"></span>
        </div>
        <div class="alert alert-warning">
            <h5 style="display: inline-block">Auteur : </h5> <span id="span_module_author"></span> - 
            <h5 style="display: inline-block">Licence : </h5> <span id="span_module_licence"></span>
        </div>
        <div>
            <legend>Configuration</legend>
            <div id="div_module_configuration"></div>

            <div class="form-actions">
                <a class="btn btn-success" id="bt_saveModuleConfig"><i class="fa fa-check-circle icon-white" style="position:relative;left:-5px;top:1px"></i>Sauvegarder</a>
            </div>
        </div>
    </div>
</div>

<?php include_file("desktop", "module", "js"); ?>
