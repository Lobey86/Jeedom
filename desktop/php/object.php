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
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                $allObject = object::buildTree();
                foreach ($allObject as $object) {
                    $margin = 15 * $object->parentNumber();
                    echo '<li class="cursor li_object" data-object_id="' . $object->getId() . '"><a style="position:relative;left:' . $margin . 'px;">' . $object->getName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 object" style="display: none;" id="div_conf">
        <form class="form-horizontal">
            <fieldset>
                <legend>Général</legend>
                <div class="form-group">
                    <label class="col-lg-1 control-label">Nom de l'objet</label>
                    <div class="col-lg-3">
                        <input class="form-control objectAttr" type="text" data-l1key="id" style="display : none;"/>
                        <input class="form-control objectAttr" type="text" data-l1key="name" placeholder="Nom de l'objet"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-1 control-label">Père</label>
                    <div class="col-lg-3">
                        <select class="form-control objectAttr" data-l1key="father_id">
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
                    <label class="col-lg-1 control-label">Visible</label>
                    <div class="col-lg-1">
                        <input class="form-control objectAttr" type="checkbox"  data-l1key="is_visible" checked/>
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

<?php include_file("desktop", "object", "js"); ?>