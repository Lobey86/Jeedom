<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'sms');
sendVarToJS('dontRemoveCmd', '1');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des equipements SMS
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('sms') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>Générale</legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nom de l'équipement SMS</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom de l'équipement GSM" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isEnable" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >Port du périphérique</label>
                    <div class="col-lg-2">
                        <input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port" placeholder="/dev/ttyUSB0"/>
                    </div>
                </div><div class="form-group">
                    <label class="col-lg-2 control-label" >Code pin</label>
                    <div class="col-lg-2">
                        <input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="pin"/>
                    </div>
                </div>

            </fieldset> 
        </form>

        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> Ajouter d'un numéro</a><br/><br/>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Numéro</th>
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

<?php include_file('desktop', 'sms', 'js', 'sms'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>