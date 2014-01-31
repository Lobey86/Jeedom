<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

include_file('3rdparty', 'jquery.sew/jquery.sew', 'css');

include_file('3rdparty', 'codemirror/lib/codemirror', 'js');
include_file('3rdparty', 'codemirror/lib/codemirror', 'css');
include_file('3rdparty', 'codemirror/addon/edit/matchbrackets', 'js');
include_file('3rdparty', 'codemirror/mode/htmlmixed/htmlmixed', 'js');
include_file('3rdparty', 'codemirror/mode/clike/clike', 'js');
include_file('3rdparty', 'codemirror/mode/php/php', 'js');

sendVarToJS('select_id', init('id', '-1'));
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_scenario" class="nav nav-list bs-sidenav fixnav">                  
                <?php
                if (config::byKey('enableScenario') == 0) {
                    echo '<a class="btn btn-sm btn-success" id="bt_changeAllScenarioState" state="1" style="display: inline-block;" title="Activer/Désactiver le système de scénario"><i class="fa fa-check"></i></a>';
                } else {
                    echo '<a class="btn btn-sm btn-danger" id="bt_changeAllScenarioState" state="0" style="display: inline-block;" title="Activer/Désactiver le système de scénario"><i class="fa fa-times"></i> </a>';
                }
                ?>
                <a class="btn btn-default btn-sm tooltips" id="bt_displayScenarioVariable" title="Voir toutes les variables de scénario" style="display: inline-block;"><i class="fa fa fa-eye"></i></a>
                <li class="nav-header">Liste scénarios 
                    <i class="fa fa-plus-circle pull-right cursor" id="bt_addScenario" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li> 
                    <select style="width: 100%;margin-bottom: 5px;" id="sel_group" class="form-control">
                        <option value=''>Tous</option>
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
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control" placeholder="Rechercher"/></li>

                <?php
                foreach (scenario::all(init('group')) as $scenario) {
                    $group = '';
                    if ($scenario->getGroup() != '') {
                        $group = '[' . $scenario->getGroup() . '] ';
                    }
                    if ($scenario->getIsActive() == 1) {
                        switch ($scenario->getState()) {
                            case 'on':
                                $state = 'green';
                                break;
                            case 'in progress':
                                $state = 'blue';
                                break;
                            case 'error':
                                $state = 'orange';
                                break;
                            default:
                                $state = 'red';
                                break;
                        }
                    } else {
                        $state = 'grey';
                    }
                    echo '<li class="cursor li_scenario" id="scenario' . $scenario->getId() . '" scenario_id="' . $scenario->getId() . '">';
                    echo '<a> <span class="binary ' . $state . ' pull-right binary" style="width : 15px;"></span>' . $group . $scenario->getName() . '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10" id="div_editScenario" style="display: none; border-left: solid 1px #EEE; padding-left: 25px;">
        <input class="scenarioAttr" l1key="id" hidden/>
        <legend style="height: 35px;">Scénario
            <a class="btn btn-default btn-xs pull-right" id="bt_copyScenario"><i class="fa fa-copy"></i> Dupliquer</a>
            <a class="btn btn-default btn-xs pull-right" id="bt_logScenario"><i class="fa fa-file-text-o"></i> Log</a>
            <a class="btn btn-danger btn-xs pull-right" id="bt_stopScenario"><i class="fa fa-stop"></i> Arrêter</a>
        </legend>
        <div class="row">
            <div class="col-lg-3">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-6 control-label" >Nom du scénario</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr" l1key="name" type="text" placeholder="Nom du scénario"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label" >Groupe</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr" l1key="group" type="text" placeholder="Groupe du scénario"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-6 control-label">Actif</label>
                            <div class="col-lg-6">
                                <input type="checkbox" class="form-control scenarioAttr" l1key="isActive">
                            </div>
                        </div>
                        <div class="form-group expertModeHidden">
                            <label class="col-lg-6 control-label">Timeout secondes (0 = illimité)</label>
                            <div class="col-lg-6">
                                <input class="form-control scenarioAttr" l1key="timeout">
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col-lg-5">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" >Mode du scénario</label>
                        <div class="col-lg-3">
                            <select class="form-control scenarioAttr" l1key="mode">
                                <option value="provoke">Provoqué</option>
                                <option value="schedule">Programmé</option>
                                <option value="all">Les deux</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <a class="btn btn-default" id="bt_addTrigger"><i class="fa fa-plus-square"></i> Evènement</a>
                            <a class="btn btn-default" id="bt_addSchedule"><i class="fa fa-plus-square"></i> Programmation</a>
                        </div>
                    </div>
                    <div class="scheduleDisplay" style="display: none;">
                        <div class="form-group">
                            <label class="col-lg-3 control-label" >Précédent</label>
                            <div class="col-lg-3" ><span class="scenarioAttr label label-primary" l1key="forecast" l2key="prevDate" l3key="date"></span></div>
                            <label class="col-lg-3 control-label" >Prochain</label>
                            <div class="col-lg-3"><span class="scenarioAttr label label-success" l1key="forecast" l2key="nextDate" l3key="date"></span></div> 
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
                        <label class="col-lg-6 control-label" for="span_lastCheck">Dernièr lancement</label>
                        <div class="col-lg-6">
                            <div><span id="span_lastLaunch" class="label label-info" style="position: relative; top: 4px;"></span></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-6 control-label" for="span_ongoing">Etat</label>
                        <div class="col-lg-6">
                            <div><span id="span_ongoing" class="label" style="position: relative; top: 4px;"></span></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <div id="div_scenarioElement" class="element">

        </div>


        <div class="form-actions">
            <a class="btn btn-warning tooltips" id="bt_testScenario" title='Veuillez sauvegarder avant de tester. Ceci peut ne pas aboutir.'><i class="fa fa-gamepad"></i> Exécuter</a>
            <a class="btn btn-danger" id="bt_delScenario"><i class="fa fa-minus-circle"></i> Supprimer</a>
            <a class="btn btn-success" id="bt_saveScenario"><i class="fa fa-check-circle"></i> Sauvegarder</a>
        </div>

    </div>
</div>

<div class="modal fade" id="md_addScenario">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un scénario</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addScenarioAlert"></div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-lg-4 control-label" for="inputEmail">Nom</label>
                        <div class="col-lg-8">
                            <input class="form-control" type="text"  id="in_addScenarioName" size="16" placeholder="Nom du scénario"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addScenarioSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="md_copyScenario">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Dupliquer le scénario</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_copyScenarioAlert"></div>
                <center>
                    <input class="form-control" type="text"  id="in_copyScenarioName" size="16" placeholder="Nom du scénario"/><br/><br/>
                </center>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_copyScenarioSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="md_addElement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter élément</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addElementAlert"></div>
                <center>
                    <select id="in_addElementType" class="form-control">
                        <option value="if">Si/Alors/Sinon</option>
                        <option value="action">Action</option>
                        <option value="for">Boucle</option>
                        <option value="code">Code</option>
                    </select>
                </center>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addElementSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<?php
include_file("desktop", "scenario", "js");
include_file('3rdparty', 'jquery.sew/jquery.caretposition', 'js');
include_file('3rdparty', 'jquery.sew/jquery.sew.min', 'js');
?>