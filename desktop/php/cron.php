<?php
if (!isConnect()) {
    throw new Exception('401 - Unauthorized access to page');
}
?>

<div class="row">
    <div class="col-lg-12">
        Processus Jeecron : <span class="label label-default"><span class="tooltips" id="span_jeecronMasterRuns" title="Nombre de Jeecron master, doit toujours etre inférieur à 2"></span> | <span id="span_jeecronRuns"></span> | <span id="span_nbProcess"></span></span>
        <span style="margin-left: 100px;">Load avg <span class="label label-default"><span id="span_loadAvg1"></span> | <span id="span_loadAvg5"></span> | <span id="span_loadAvg15"></span></span></span>
        <a class="btn btn-success pull-right" id="bt_save"><i class="fa fa-check-circle"></i> Enregistrer</a>
        <a class="btn btn-default pull-right" id="bt_addCron"><i class="fa fa-plus-circle"></i> Ajouter</a>
        <a class="btn btn-default pull-right" id="bt_refreshCron"><i class="fa fa-refresh"></i> Rafraîchir</a>
        <br/><br/><br/>
        <table id="table_cron" class="table table-bordered table-condensed tablesorter" >
            <thead>
                <tr>
                    <th class="" style="width: 50px;" data-sorter="false" data-filter="false"></th>
                    <th class="enable" style="width: 80px;">Actif</th>
                    <th class="server" style="width: 100px;">Serveur</th>
                    <th class="pid" style="width: 100px;">PID</th>
                    <th class="deamons" style="width: 80px;">Démon</th>
                    <th class="class">Classe</th>
                    <th class="function">Fonction</th>
                    <th class="schedule"><i class="fa fa-question-circle cursor getHelpSchedule" style="position: relative; width: 10px;"></i> Programation</th>
                    <th class="timeout" style="width: 150px;">Timeout (min)</th>
                    <th class="lastRun" style="width: 200px;">Dernier lancement</th>
                    <th class="duration" style="width: 80px;">Durée</th>
                    <th class="state" style="width: 80px;">Statut</th>
                    <th class="action" style="width: 50px;" data-sorter="false" data-filter="false"></th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </div>
</div>
<?php include_file('desktop', 'cron', 'js'); ?>