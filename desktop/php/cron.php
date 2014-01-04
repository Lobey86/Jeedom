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
        <a class="btn btn-default pull-right" id="bt_refreshCron"><i class="fa fa-refresh"></i> Rafraichir</a>
        <br/><br/><br/>
        <table id="table_cron" class="table table-bordered table-condensed tablesorter" >
            <thead>
                <tr>
                    <th class=""></th>
                    <th class="enable">Actif</th>
                    <th class="server">Serveur</th>
                    <th class="pid">PID</th>
                    <th class="deamons">Démon</th>
                    <th class="class">Classe</th>
                    <th class="function">Fonction</th>
                    <th class="schedule">Programation <i class="fa fa-question-circle cursor getHelpSchedule floatright" ></i></th>
                    <th class="timeout">Timeout</th>
                    <th class="lastRun">Dernier lancement</th>
                    <th class="duration">Durée</th>
                    <th class="state">Status</th>
                    <th class="action"></th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </div>
</div>
<?php include_file('desktop', 'cron', 'js'); ?>