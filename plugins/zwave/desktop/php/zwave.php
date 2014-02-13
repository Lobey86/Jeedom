<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'zwave');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">

                <a class="btn btn-default btn-sm tooltips" id="bt_syncEqLogic" title="Synchroniser équipement avec le Razberry" style="display: inline-block;"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-default btn-sm tooltips changeIncludeState" title="Inclure prériphérique Z-wave" data-state="1" style="display: inline-block;"><i class="fa fa-sign-in fa-rotate-90"></i></a>
                <a class="btn btn-default btn-sm tooltips changeIncludeState" title="Exclure périphérique Z-wave" data-state="0" style="display: inline-block;"><i class="fa fa-sign-out fa-rotate-90"></i></a>
                <a class="btn btn-default btn-sm tooltips expertModeHidden" id="bt_inspectQueue" title="Inspecter la queue Z-wave" style="display: inline-block;"><i class="fa fa-exchange fa-rotate-90"></i></a>
                <a class="btn btn-default btn-sm tooltips expertModeHidden" id="bt_routingTable" title="Afficher la table de routage" style="display: inline-block;"><i class="fa fa-sitemap"></i></a>

                <li class="nav-header">Liste des équipements Z-wave
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('zwave') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <div class="row">
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>Général</legend>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom de l'équipement</label>
                            <div class="col-lg-8">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom de l'équipement"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Node ID</label>
                            <div class="col-lg-8">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="logicalId" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" >Objet parent</label>
                            <div class="col-lg-8">
                                <select class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">Aucun</option>
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Catégorie</label>
                            <div class="col-lg-8">
                                <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Activer</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isEnable" checked/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visible</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isVisible" checked/>
                            </div>
                        </div>
                        <div class="form-group expertModeHidden">
                            <label class="col-lg-4 control-label">Délai autorisé entre 2 messages (min)</label>
                            <div class="col-lg-4">
                                <input class="eqLogicAttr form-control" data-l1key="timeout" />
                            </div>
                        </div>
                        <div class="form-group expertModeHidden">
                            <label class="col-lg-4 control-label">Fréquence de rafraichissement des valeurs</label>
                            <div class="col-lg-4">
                                <input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="refreshDelay"/>
                            </div>
                            <div class="col-lg-1">
                                <i class="fa fa-question-circle cursor getHelpSchedule floatright" ></i>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>Informations</legend>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Equipement</label>
                            <div class="col-lg-5">
                                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="device">
                                    <option value="">Aucun</option>
                                    <?php
                                    foreach (zwave::devicesParameters() as $id => $info) {
                                        echo '<option value="' . $id . '">' . $info['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <a class="btn btn-default" id="bt_configureDevice"><i class="fa fa-wrench"></i></a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Batterie</label>
                            <div class="col-lg-5">
                                <span class="zwaveInfo tooltips label label-default" data-l1key="battery"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Dernière communication</label>
                            <div class="col-lg-5">
                                <span class="zwaveInfo tooltips label label-default" data-l1key="lastReceived"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Etat</label>
                            <div class="col-lg-5">
                                <span class="zwaveInfo tooltips label label-default" data-l1key="state"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Marque</label>
                            <div class="col-lg-5">
                                <span class="zwaveInfo tooltips label label-default" data-l1key="brand"></span>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-lg-2 control-label">Classes</label>
                            <div class="col-lg-5">
                                <a class="btn btn-default" id="bt_showClass"><i class="fa fa-cogs"></i> Voir/Ajouter commandes préconfigurées</a>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
        </div>

        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction expertModeHidden" data-action="add"><i class="fa fa-plus-circle"></i> Commandes</a><br/><br/>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 300px;">Nom</th>
                    <th style="width: 130px;" class="expertModeHidden">Type</th>
                    <th style="width: 100px;" class="expertModeHidden">Instance ID</th>
                    <th style="width: 100px;" class="expertModeHidden">Class</th>
                    <th style="width: 200px;" class="expertModeHidden">Commande</th>
                    <th >Paramètres</th>
                    <th style="width: 100px;">Options</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php include_file('desktop', 'zwave', 'js', 'zwave'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>