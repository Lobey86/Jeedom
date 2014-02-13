<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
global $listScript;
include_file('core', 'script', 'config', 'script');
include_file('3rdparty', 'jquery.fileTree/jqueryFileTree', 'css');
include_file('3rdparty', 'codemirror/lib/codemirror', 'js');
include_file('3rdparty', 'codemirror/lib/codemirror', 'css');
include_file('3rdparty', 'codemirror/addon/edit/matchbrackets', 'js');
include_file('3rdparty', 'codemirror/mode/htmlmixed/htmlmixed', 'js');
include_file('3rdparty', 'codemirror/mode/clike/clike', 'js');
include_file('3rdparty', 'codemirror/mode/php/php', 'js');
include_file('3rdparty', 'codemirror/mode/shell/shell', 'js');
include_file('3rdparty', 'codemirror/mode/python/python', 'js');
include_file('3rdparty', 'codemirror/mode/ruby/ruby', 'js');
include_file('3rdparty', 'codemirror/mode/perl/perl', 'js');

sendVarToJS('eqType', 'script');
sendVarToJS('userScriptDir', getRootPath() . '/' . config::byKey('userScriptDir', 'script'));
?>


<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des scripts
                    <i class="fa fa-plus-circle pull-right cursor eqLogicAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('script') as $eqLogic) {
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
                    <label class="col-lg-3 control-label">Nom de l'équipement script</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom de l'équipement script"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label" >Objet parent</label>
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
                    <label class="col-lg-3 control-label">Catégorie</label>
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
                    <label class="col-lg-3 control-label">Activer</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isEnable" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Visible</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr form-control" data-l1key="isVisible" checked/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <legend>Script</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> Ajouter une commande script</a><br/><br/>
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
                    <th style="width: 70px;">Type script</th>
                    <th style="width: 70px;">Type</th>
                    <th>Requête</th>
                    <th style="width: 110px;">Divers</th>
                    <th style="width: 170px;">Paramètres</th>
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


<div class="modal fade" id="md_addPreConfigScript">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un script shell prédefinie</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none;" id="div_addPreConfigError"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="in_addPreConfigName">Script</label>
                            <div class="col-lg-8">
                                <select class="form-control" id="sel_addPreConfigScript">
                                    <?php
                                    foreach ($listScript as $key => $script) {
                                        echo '<option value="' . $key . '" data-path="' . $script['path'] . '" data-argv="' . $script['argv'] . '" data-type="' . $script['type'] . '" data-subType="' . $script['subType'] . '" data-requestType="' . $script['requestType'] . '">' . $script['name'] . '</option>';
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
                            foreach ($listScript as $key => $script) {
                                echo '<span class="version ' . $key . '" style="display : none;">' . $script['version'] . ' - ' . $script['requestType'] . '</span>';
                            }
                            ?>
                        </h4></center>
                </div>
                <div class="alert alert-info">
                    <center><h4>Description</h4></center>
                    <?php
                    foreach ($listScript as $key => $script) {
                        echo '<span class="description ' . $key . '" style="display : none;">' . $script['description'] . '</span>';
                    }
                    ?>
                </div>
                <div class="alert">
                    <center><h4>Utilisation</h4></center>
                    <?php
                    foreach ($listScript as $key => $script) {
                        echo '<span class="use ' . $key . '" style="display : none;">' . $script['use'] . '</span>';
                    }
                    ?>
                </div>
                <div class="alert alert-danger">
                    <center><h4>Pré-requis</h4></center>
                    <?php
                    foreach ($listScript as $key => $script) {
                        echo '<span class="required ' . $key . '" style="display : none;">' . $script['required'] . '</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addPreConfigSave"><i class="fa fa-check-circle"></i> Ajouter</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="md_addEqLogic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un équipement script</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addEqLogicAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom de l'équipement script</label>
                            <div class="col-lg-8">
                                <input class="form-control eqLogicAttr" data-l1key="name" type="text" placeholder="Nom de l'équipement script"/>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success eqLogicAction" data-action="newAdd"><i class="fa fa-check-circle icon-white"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<div id="md_browseScriptFile" title="Parcourir...">
    <div style="display: none;" id="div_browseScriptFileAlert"></div>
    <div id="div_browseScriptFileTree"></div>
</div>

<div id="md_editScriptFile" title="Editer...">
    <div style="display: none;" id="div_editScriptFileAlert"></div>
    <textarea id="ta_editScriptFile" class="form-control" style="height: 100%;"></textarea>
</div>

<?php include_file('3rdparty', 'jquery.fileTree/jquery.easing.1.3', 'js'); ?>
<?php include_file('3rdparty', 'jquery.fileTree/jqueryFileTree', 'js'); ?>
<?php include_file('desktop', 'script', 'js', 'script'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
