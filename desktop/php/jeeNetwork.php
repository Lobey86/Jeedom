<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_jeeNetwork" class="nav nav-list bs-sidenav">
                <a id="bt_addJeeNetwork" class="btn btn-default" style="width : 100%;margin-top : 5px;margin-bottom: 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter un Jeedom}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (jeeNetwork::all() as $jeeNetwork) {
                    echo '<li class="cursor li_jeeNetwork" data-jeeNetwork_id="' . $jeeNetwork->getId() . '"><a>' . $jeeNetwork->getName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 jeeNetwork" style="display: none;" id="div_conf">
        <form class="form-horizontal">
            <fieldset>
                <legend>{{Général}}</legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nom du Jeedom exclave}}</label>
                    <div class="col-lg-3">
                        <input class="form-control jeeNetworkAttr" type="text" data-l1key="id" style="display : none;"/>
                        <input class="form-control jeeNetworkAttr" type="text" data-l1key="name" placeholder="Nom du Jeedom exclave"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{IP}}</label>
                    <div class="col-lg-3">
                        <input class="form-control jeeNetworkAttr" type="text" data-l1key="ip" placeholder="IP"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Clef API}}</label>
                    <div class="col-lg-3">
                        <input class="form-control jeeNetworkAttr" type="text" data-l1key="apikey" placeholder="Clef API"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Statut}}</label>
                    <div class="col-lg-3">
                        <span class="label label-default jeeNetworkAttr" type="text" data-l1key="status" ></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Version de Jeedom}}</label>
                    <div class="col-lg-3">
                        <span class="label label-default jeeNetworkAttr" type="text" data-l1key="configuration" data-l2key="version" ></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nombre de mise à jour}}</label>
                    <div class="col-lg-3">
                        <span class="label label-default jeeNetworkAttr" type="text" data-l1key="configuration" data-l2key="nbUpdate" ></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nombre de message}}</label>
                    <div class="col-lg-3">
                        <span class="label label-default jeeNetworkAttr" type="text" data-l1key="configuration" data-l2key="nbMessage" ></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Plugin}}</label>
                    <div class="col-lg-10" id="div_pluginList">

                    </div>
                </div>
            </fieldset>
        </form>
        <hr/>
        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-success" id="bt_saveJeeNetwork"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                    <a class="btn btn-danger" id="bt_removeJeeNetwork"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                </div>

            </fieldset>
        </form>
    </div>
</div>

<?php include_file('desktop', 'jeeNetwork', 'js'); ?>