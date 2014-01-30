<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
sendVarToJS('select_id', init('id', '-1'));
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_object" class="nav nav-list bs-sidenav">
                <li class="nav-header">Liste objects 
                    <i class="fa fa-plus-circle pull-right cursor" id="bt_addObject" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="form-control" class="filter form-control" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                $allObject = object::all();
                foreach ($allObject as $object) {
                    echo '<li class="cursor li_object" object_id="' . $object->getId() . '" name="' . $object->getName() . '"><a>' . $object->getName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;" id="div_conf">
        <form class="form-horizontal">
            <fieldset>
                <legend>Général</legend>
                <div class="form-group">
                    <label class="col-lg-1 control-label" for="in_name">Nom de l'objet</label>
                    <div class="col-lg-3">
                        <input class="form-control" type="text"  id="in_name" size="16" placeholder="Nom de l'objet"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-1 control-label" for="in_name">Père</label>
                    <div class="col-lg-3">
                        <select class="form-control" id="sel_father">
                            <option value="">Aucun</option>
                            <?php
                            foreach ($allObject as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-1 control-label" >Visible</label>
                    <div class="col-lg-1">
                        <input class="form-control" type="checkbox"  id="in_visible" size="16" checked/>
                    </div>
                </div>
            </fieldset>
        </form>
        <hr/>
        <div id="div_objectImage">


        </div>
        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-success" id="bt_saveObject"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                    <a class="btn btn-danger" id="bt_removeObject"><i class="fa fa-minus-circle"></i> Supprimer</a>
                </div>

            </fieldset>
        </form>
    </div>
</div>




<div class="modal fade" id="md_addObject">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un objet</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addObjetAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="in_addJeenodeName">Nom de l'objet</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="text"  id="in_addObjectName" size="16" placeholder="Nom de l'objet"/>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-primary" id="bt_addObjetSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<?php include_file("desktop", "object", "js"); ?>