<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

include_file('core', 'macro', 'class', 'macro');

$allType = eqLogic::allType();
$sel_type = '<select class=\'sel-type form-control\'>';
foreach ($allType as $type) {
    if ($type['type'] != 'macro' && $type['type'] != 'mail') {
        $sel_type .= '<option>' . $type['type'] . '</option>';
    }
}
$sel_type .= '</select>';

sendVarToJS('select_id', init('id', '-1'));
sendVarToJS('sel_type', $sel_type);
?>

<div class="row">
    <div class="col-lg-2 bs-sidebar">
        <ul id="ul_macro" class="nav nav-list bs-sidenav fixnav">
            <li class="nav-header">Liste des equipements macro
                <i class="fa fa-plus-circle pull-right cursor" id="bt_addMacro" style="font-size: 1.5em;margin-bottom: 5px;"></i>
            </li>
            <li class="filter" style="margin-bottom: 5px;"><input class="form-control" class="filter form-control" placeholder="Rechercher" style="width: 100%"/></li>
            <?php
            foreach (eqLogic::byType('macro') as $eqLogic) {
                echo '<li class="cursor li_eqLogic" eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
            }
            ?>
        </ul>
    </div>
    <div class="col-lg-10" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;" id="div_conf">
        <form class="form-horizontal">
            <fieldset>
                <legend>Générale</legend>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Nom de la macro</label>
                    <div class="col-lg-3">
                        <input class="form-control" type="text"  id="in_name" placeholder="Nom de la macro"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" >Objet parent</label>
                    <div class="col-lg-3">
                        <select class="form-control" id="sel_object">
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
                    <label class="col-lg-3 control-label" >Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox"  class="form-control" id="in_enable" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" >Visible</label>
                    <div class="col-lg-1">
                        <input type="checkbox"  class="form-control" id="in_visible" checked/>
                    </div>
                </div>

                <a class="btn btn-success btn-sm" id="bt_addCommand"><i class="fa fa-plus-circle"></i> Ajouter une macro commande</a><br/><br/>

                <ul class="nav nav-tabs" id="ul_cmdOfMacro">

                </ul>
                <div class="tab-content" id="div_cmdOfMacro">

                </div>

                <div class="form-actions">
                    <a class="btn btn-success" id="bt_saveMacro"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                    <a class="btn btn-danger" id="bt_removeMacro"><i class="fa fa-minus-circle"></i> Supprimer</a>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<div class="modal fade" id="md_addMacro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter une macro</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addMacroAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom de la macro</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="text"  id="in_addMacroName" size="16" placeholder="Nom de la macro"/>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addMacroSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="md_addCmdToMacro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter une macro</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addCmdToMacroAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom de la commande</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="text"  id="in_addCmdToMacroName" size="16" placeholder="Nom de la commande"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="sel_addMacroType">Type</label>
                            <div class="col-lg-8">
                                <select class="form-control" id="sel_addMacroType">
                                    <option value="other">Action</option>
                                    <option value="slider">Slider</option>
                                    <option value="color">Couleur</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i>Annuler</a>
                <a class="btn btn-success" id="bt_addCmdToMacroSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'macro', 'js', 'macro'); ?>