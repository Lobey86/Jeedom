<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('eqType', 'sarah');
sendVarToJS('dontRemoveCmd', '1');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des equipements S.A.R.A.H 
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('sarah') as $eqLogic) {
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
                    <label class="col-lg-2 control-label">Nom de l'équipement S.A.R.A.H</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom de l'équipement S.A.R.A.H"/>
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
                    <label class="col-lg-2 control-label" >Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isEnable" checked/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-2 control-label">Adresse du nodeJS</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="addrSrv" placeholder="xxx.xxx.xxx.xxx:8080"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Adresse du TTS</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr configuration form-control" data-l1key="configuration" data-l2key="addrSrvTts" placeholder="xxx.xxx.xxx.xxx:8888"/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-warning" id="bt_syncInteract"><i class="fa fa-exchange"></i> Synchroniser</a>
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php include_file('desktop', 'sarah', 'js', 'sarah'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>