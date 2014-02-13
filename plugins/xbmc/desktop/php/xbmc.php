<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}

global $listCmdXBMC;

include_file('core', 'xbmc', 'config', 'xbmc');
sendVarToJS('eqType', 'xbmc');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des equipements XBMC 
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('xbmc') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>Général</legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nom de l'équipement XBMC</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom de l'équipement XBMC"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >Objet parent</label>
                    <div class="col-lg-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
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
                    <label class="col-lg-2 control-label" >Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isEnable" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >Visible</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isVisible" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Adresse</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addr" placeholder="Adresse ou IP de XBMC avec le port"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nom d'utilisateur</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="login" placeholder="Nom d'utilisateur"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Mot de passe</label>
                    <div class="col-lg-3">
                        <input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password" placeholder="Mot de passe"/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> Ajouter une commande XBMC</a><br/><br/>
        <div class="alert alert-info">
            Sous type : <br/>
            - Slider : mettre #slider# pour recupérer la valeur<br/>
            - Color : mettre #color# pour recupérer la valeur<br/>
            - Message : mettre #title# et #message#
        </div>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 200px;">Nom</th>
                    <th style="width: 100px;">Type</th>
                    <th>Parametre(s)</th>
                    <th style="width: 100px;"></th>
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

<div class="modal fade" id="md_addPreConfigCmdXbmc">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter une commande prédefinie</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="div_addPreConfigCmdXbmcError"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="in_addPreConfigCmdXbmcName">Fonctions</label>
                            <div class="col-lg-8">
                                <select class="form-control" id="sel_addPreConfigCmdXbmc">
                                    <?php
                                    foreach ($listCmdXBMC as $key => $cmdXbmc) {
                                        echo "<option value='" . $key . "'>" . $cmdXbmc['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <div class="alert alert-success">
                    <center><h4>Version 
                            <?php
                            foreach ($listCmdXBMC as $key => $cmdXbmc) {
                                echo '<span class="json_cmd ' . $key . ' hide" style="display : none;" >' . json_encode($cmdXbmc) . '</span>';
                                echo '<span class="version ' . $key . '" style="display : none;">' . $cmdXbmc['version'] . '</span>';
                            }
                            ?>
                        </h4></center>
                </div>
                <div class="alert alert-info">
                    <center><h4>Description</h4></center>
                    <?php
                    foreach ($listCmdXBMC as $key => $cmdXbmc) {
                        echo '<span class="description ' . $key . '" style="display : none;">' . $cmdXbmc['description'] . '</span>';
                    }
                    ?>
                </div>
                <div class="alert alert-danger">
                    <center><h4>Pré-requis</h4></center>
                    <?php
                    foreach ($listCmdXBMC as $key => $cmdXbmc) {
                        echo '<span class="required ' . $key . '" style="display : none;">' . $cmdXbmc['required'] . '</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addPreConfigCmdXbmcSave"><i class="fa fa-check-circle"></i> Ajouter</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'xbmc', 'js', 'xbmc'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
