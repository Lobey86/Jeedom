<?php
if (!isConnect('admin')) {
    throw new Exception('Error 401 Unauthorized');
}
include_file('core', 'jeenode', 'class', 'jeenode');
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_jeenode" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des jeenodes
                    <i class="fa fa-plus-circle pull-right cursor" id="bt_addJeenode" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                foreach (jeenodeReal::liste() as $jeenodeReal) {
                    echo '<li class="cursor li_jeenode" data-jeenodeReal_id="' . $jeenodeReal['id'] . '" data-type="' . $jeenodeReal['type'] . '" data-name="' . $jeenodeReal['name'] . '"><a>' . $jeenodeReal['name'] . ' (' . $jeenodeReal['type'] . ')</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqReal" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;" id="div_conf">
        <div id="div_confCommun">
            <div class="row">
                <div class="col-lg-6">
                    <form class="form-horizontal">
                        <fieldset>
                            <legend>Général</legend>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Nom du jeenode</label>
                                <div class="col-lg-3">
                                    <input class="eqRealAttr form-control" data-l1key="name" type="text" placeholder="Nom du jeenode"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Type</label>
                                <div class="col-lg-3">
                                    <input class="eqRealAttr form-control" data-l1key="type" disabled/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Mode</label>
                                <div class="col-lg-3">
                                    <select class="eqRealAttr form-control" data-l1key='configuration' data-l2key='mode'>
                                        <option value="actif">Actif</option>
                                        <option value="passif">Passif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Node ID</label>
                                <div class="col-lg-3">
                                    <input class="eqRealAttr form-control" data-l1key="logicalId" type="text" placeholder="ID du jeenode"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Fréquence</label>
                                <div class="col-lg-3">
                                    <select  class="eqRealAttr form-control" data-l1key="configuration" data-l2key="frequence">
                                        <option value='RF12_433MHZ'>433 MHZ</option>
                                        <option value='RF12_868MHZ' selected>868 MHZ</option>
                                        <option value='RF12_915MHZ'>915 MHZ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-3 control-label">Configuraiton avancée</label>
                                <div class="col-lg-3">
                                    <a class='btn btn-default' id="bt_showAdvanceConfigue">Afficher</a>
                                </div>
                            </div>                            
                        </fieldset>
                    </form>
                </div>
                <div class="col-lg-6" id="div_jeenodeInformation" >
                    <form class="form-horizontal">
                        <fieldset>
                            <legend>Informations Jeenode</legend>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Dernière communication</label>
                                <div class="col-lg-3">
                                    <label class="checkbox">
                                        <span class="label label-info" id="label_lastCommunication"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Uptime</label>
                                <div class="col-lg-3">
                                    <label class="checkbox">
                                        <span class="label label-info" id="label_uptime"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Mémoire libre</label>
                                <div class="col-lg-3">
                                    <label class="checkbox">
                                        <span class="label label-info" id="label_ram"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" style="display: none;">
                                <label class="col-lg-3 control-label">Batterie</label>
                                <div class="col-lg-3">
                                    <label class="checkbox">
                                        <span class="label" id="label_bat"></span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>

            <form class="form-horizontal" id='div_confCommunAvance' style="display: none;">
                <fieldset>
                    <legend>Configuration avancée</legend><br/>
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Debug mode</label>
                                <div class="col-lg-1">
                                    <input type="checkbox" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debug">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Debit port série</label>
                                <div class="col-lg-3">
                                    <select class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debitSerial">
                                        <option value='57600'>57600 Bauds</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Groupe</label>
                                <div class="col-lg-3">
                                    <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="group" placeholder="Groupe du jeenode" notEmpty mustNumber value="212"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Radio sync mode</label>
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debitSerial" placeholder="Mode de syncrhonisation radio" notEmpty mustNumber value="2"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">     
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Retry period</label>
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debitSerial" placeholder="Temps d'attente avant relance (ms)" notEmpty mustNumber value="10"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Retry limit</label>
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debitSerial" placeholder="Nombre d'essai avant abandont" notEmpty mustNumber value="3"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Ack time</label>
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="debitSerial" placeholder="Temps d'attente avant le ack (ms)" notEmpty mustNumber value="10"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

        <div id="div_configurationSpecifiqueType"></div>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-success" id="bt_saveJeenode"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                    <a class="btn btn-danger" id="bt_removeJeenode"><i class="fa fa-minus-circle"></i> Supprimer</a>
                </div>
            </fieldset>
        </form>
    </div>
</div>


<div class="modal fade" id="md_addJeenode">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h3>Ajouter un jeenode</h3>
            </div>
            <div class="modal-body">
                <div style="display: none;" id="div_addJeenodeAlert"></div>
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="in_addJeenodeName">Nom du jeenode</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="text"  id="in_addJeenodeName" size="16" placeholder="Nom du jeenode"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" for="sel_addJeenodeType">Type du jeenode</label>
                            <div class="col-lg-8">
                                <select class="form-control" id="sel_addJeenodeType">
                                    <option value='master'>Master</option>
                                    <option value='jeenode'>Jeenode</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-minus-circle"></i> Annuler</a>
                <a class="btn btn-success" id="bt_addJeenodeSave"><i class="fa fa-check-circle"></i> Enregistrer</a>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'jeenode', 'js', 'jeenode'); ?>