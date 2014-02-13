<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'geoloc');
?>
<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des équipements Géoloc
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('geoloc') as $eqLogic) {
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
                            <label class="col-lg-4 control-label" >Objet parent</label>
                            <div class="col-lg-8">
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
                    </fieldset> 
                </form>
            </div>
            <div class="col-lg-6">

            </div>
        </div>

        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> Commandes</a><br/><br/>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 200px;">Nom</th>
                    <th style="width: 200px;">Type</th>
                    <th>Options</th>
                    <th style="width: 100px;">Paramètres</th>
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

<?php include_file('desktop', 'geoloc', 'js', 'geoloc'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>