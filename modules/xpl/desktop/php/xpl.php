<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

include_file('core', 'xpl', 'config', 'xpl');
include_file('core', 'xpl', 'class', 'xpl');

sendVarToJS('select_id', init('id', '-1'));
sendVarToJS('eqType', 'xpl');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des equipements xPL 
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="form-control" class="filter form-control" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('xpl') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <?php
        $cron = cron::byId(config::byKey('xPLDeamonCronId', 'xPL'));
        if (is_object($cron) && $cron->getState() != 'run') {
            echo '<div class="alert alert-danger" >Attention le démon xPL n\'est pas en marche. Vérifier pourquoi </div>';
        }
        ?>
        <form class="form-horizontal">
            <fieldset>
                <legend>Général</legend>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Nom de l'équipement xPL</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" l1key="name" placeholder="Nom de l'équipement xPL"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Nom logique de l'équipement xPL</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" l1key="logicalId"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" >Objet parent</label>
                    <div class="col-lg-3">
                        <select id="sel_object" class="eqLogicAttr form-control" l1key="object_id">
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Visible</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" l1key="isVisible" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" l1key="isEnable" checked/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction" action="add"><i class="fa fa-plus-circle"></i> Ajouter une commande xPL</a><br/><br/>
        <div class="alert alert-info">
            Sous type : <br/>
            - Slider : mettre #slider# pour recupérer la valeur<br/>
            - Color : mettre #color# pour recupérer la valeur<br/>
            - Message : mettre #title# et #message#
        </div>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 150px;">Nom</th>
                    <th style="width: 150px;">Schema</th>
                    <th>Body</th>
                    <th style="width: 110px;">Type</th>
                    <th style="width: 200px;">Parameters</th>
                    <th style="width: 100px;">Unite</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
                    <a class="btn btn-success eqLogicAction" action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<div class="modal fade" id="md_addEqLogic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un équipement xPL</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addEqLogicAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom de l'équipement xPL</label>
                            <div class="col-lg-8">
                                <input class="form-control eqLogicAttr" l1key="name" type="text" placeholder="Nom de l'équipement xPL"/>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success eqLogicAction" action="newAdd"><i class="fa fa-check-circle icon-white"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'xpl', 'js', 'xpl'); ?>
<?php include_file('core', 'module.template', 'js'); ?>