<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

include_file('3rdparty', 'jquery.sew/jquery.sew', 'css');

include_file('3rdparty', 'codemirror/lib/codemirror', 'js');
include_file('3rdparty', 'codemirror/lib/codemirror', 'css');
include_file('3rdparty', 'codemirror/addon/edit/matchbrackets', 'js');
include_file('3rdparty', 'codemirror/mode/htmlmixed/htmlmixed', 'js');
include_file('3rdparty', 'codemirror/mode/clike/clike', 'js');
include_file('3rdparty', 'codemirror/mode/php/php', 'js');
?>


<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_scenario" class="nav nav-list bs-sidenav">   
                <center>
                    <?php
                    if (config::byKey('enableScenario') == 0) {
                        echo '<a class="btn btn-sm btn-default expertModeVisible" id="bt_changeAllScenarioState" data-state="1" style="width : 49%;min-width : 127px;margin-top : 3px;" title="{{Activer le système de scénario}}"><i class="fa fa-check" style="color : green;font-size : 1.5em;"></i> {{Act. scénarios}}</a>';
                    } else {
                        echo '<a class="btn btn-sm btn-default expertModeVisible" id="bt_changeAllScenarioState" data-state="0" style="width : 49%;min-width : 127px;margin-top : 3px;" title="{{Desactiver le système de scénario}}"><i class="fa fa-times" style="color : red;font-size : 1.5em;"></i> {{Désac. scénarios}}</a>';
                    }
                    ?>
                    <a class="btn btn-default btn-sm tooltips expertModeVisible" id="bt_displayScenarioVariable" title="{{Voir toutes les variables de scénario}}" style="width : 49%;min-width : 127px;margin-top : 3px;"><i class="fa fa fa-eye" style="font-size : 1.5em;"></i> {{Voir variables}}</a>
                </center>
                <a class="btn btn-default" id="bt_addScenario" style="width : 100%;margin-top : 5px;margin-bottom: 5px;"><i class="fa fa-plus-circle cursor" ></i> Nouveau scénario</a>
                <li class="filter"> 
                    <select style="width: 100%;margin-bottom: 5px;" id="sel_group" class="form-control input-sm">
                        <option value=''>{{Tous}}</option>
                        <?php
                        foreach (scenario::listGroup() as $group) {
                            if ($group['group'] != '') {
                                if (init('group') == $group['group'] && init('group') != '') {
                                    echo '<option selected>' . $group['group'] . '</option>';
                                } else {
                                    echo '<option>' . $group['group'] . '</option>';
                                }
                            }
                        }
                        ?>            
                    </select></li>
                 <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>

                <?php
                foreach (scenario::all(init('group')) as $scenario) {
                    echo '<li class="cursor li_scenario" id="scenario' . $scenario->getId() . '" data-scenario_id="' . $scenario->getId() . '">';
                    echo '<a>';
                    echo $scenario->getHumanName();
                    echo '<span class="pull-right">';
                    echo $scenario->getIcon();
                    echo '</span>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10" id="div_editScenario" style="display: none; border-left: solid 1px #EEE; padding-left: 25px;">
        
        <legend style="height: 35px;">{{Scénario}}
            <span class="expertModeVisible">(ID : <span class="scenarioAttr" data-l1key="id" ></span>)</span>
            <a class="btn btn-default btn-xs pull-right" id="bt_copyScenario"><i class="fa fa-copy"></i> {{Dupliquer}}</a>
            <a class="btn btn-default btn-xs pull-right" id="bt_logScenario"><i class="fa fa-file-text-o"></i> {{Log}}</a>
            <a class="btn btn-danger btn-xs pull-right" id="bt_stopScenario"><i class="fa fa-stop"></i> {{Arrêter}}</a>
        </legend>
        <div class="row">
            <div class="col-lg-3">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-6 control-label" >{{Nom du scénario}}</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr input-sm" data-l1key="name" type="text" placeholder="{{Nom du scénario}}"/>
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-lg-6 control-label" >{{Nom à afficher}}</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr input-sm tooltips" title="{{Ne rien mettre pour laisser le nom par default}}" data-l1key="display" data-l2key="name" type="text" placeholder="{{Nom à afficher}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label" >{{Groupe}}</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr input-sm" data-l1key="group" type="text" placeholder="{{Groupe du scénario}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label">{{Actif}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="scenarioAttr" data-l1key="isActive">
                            </div>
                            <label class="col-lg-3 control-label">{{Visible}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="scenarioAttr" data-l1key="isVisible">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label" >{{Objet parent}}</label>
                            <div class="col-lg-6">
                                <select class="scenarioAttr form-control input-sm" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group expertModeVisible">
                            <label class="col-lg-6 control-label">{{Timeout secondes (0 = illimité)}}</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr input-sm" data-l1key="timeout">
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col-lg-5">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" >{{Mode du scénario}}</label>
                        <div class="col-lg-3">
                            <select class="form-control scenarioAttr input-sm" data-l1key="mode">
                                <option value="provoke">{{Provoqué}}</option>
                                <option value="schedule">{{Programmé}}</option>
                                <option value="all">{{Les deux}}</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <a class="btn btn-default" id="bt_addTrigger"><i class="fa fa-plus-square"></i> {{Déclencheur}}</a>
                            <a class="btn btn-default" id="bt_addSchedule"><i class="fa fa-plus-square"></i> {{Programmation}}</a>
                        </div>
                    </div>
                    <div class="scheduleDisplay" style="display: none;">
                        <div class="form-group">
                            <label class="col-lg-3 control-label" >{{Précédent}}</label>
                            <div class="col-lg-3" ><span class="scenarioAttr label label-primary" data-l1key="forecast" data-l2key="prevDate" data-l3key="date"></span></div>
                            <label class="col-lg-3 control-label" >{{Prochain}}</label>
                            <div class="col-lg-3"><span class="scenarioAttr label label-success" data-l1key="forecast" data-l2key="nextDate" data-l3key="date"></span></div> 
                        </div>
                        <div class="scheduleMode"></div>
                    </div>
                    <div class="provokeMode provokeDisplay" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="col-lg-3">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-6 control-label" for="span_lastCheck">{{Dernier lancement}}</label>
                        <div class="col-lg-6">
                            <div><span id="span_lastLaunch" class="label label-info" style="position: relative; top: 4px;"></span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-6 control-label" for="span_ongoing">{{Etat}}</label>
                        <div class="col-lg-6">
                            <div><span id="span_ongoing" class="label" style="position: relative; top: 4px;"></span></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <textarea class="form-control scenarioAttr" data-l1key="description" placeholder="Description"></textarea><br/>

        <div id="div_scenarioElement" class="element"></div>


        <div class="form-actions">
            <a class="btn btn-warning tooltips" id="bt_testScenario" title='{{Veuillez sauvegarder avant de tester. Ceci peut ne pas aboutir.}}'><i class="fa fa-gamepad"></i> Exécuter</a>
            <a class="btn btn-danger" id="bt_delScenario"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
            <a class="btn btn-success" id="bt_saveScenario"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
        </div>

    </div>
</div>

<div class="modal fade" id="md_copyScenario">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>{{Dupliquer le scénario}}</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_copyScenarioAlert"></div>
                <center>
                    <input class="form-control" type="text"  id="in_copyScenarioName" size="16" placeholder="{{Nom du scénario}}"/><br/><br/>
                </center>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> {{Annuler}}</a>
                <a class="btn btn-success" id="bt_copyScenarioSave"><i class="fa fa-check-circle"></i> {{Enregistrer}}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="md_addElement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>{{Ajouter élément}}</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addElementAlert"></div>
                <center>
                    <select id="in_addElementType" class="form-control">
                        <option value="if">{{Si/Alors/Sinon}}</option>
                        <option value="action">{{Action}}</option>
                        <option value="for">{{Boucle}}</option>
                        <option value="code">{{Code}}</option>
                    </select>
                </center>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> {{Annuler}}</a>
                <a class="btn btn-success" id="bt_addElementSave"><i class="fa fa-check-circle"></i> {{Enregistrer}}</a>
            </div>
        </div>
    </div>
</div>

<?php
include_file('desktop', 'scenario', 'js');
include_file('3rdparty', 'jquery.sew/jquery.caretposition', 'js');
include_file('3rdparty', 'jquery.sew/jquery.sew.min', 'js');
?>