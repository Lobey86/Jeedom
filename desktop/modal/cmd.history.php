<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div class="md_history" title="Historique">
    <center><div id="div_historyChart"></div></center>
    <select class="pull-right sel_chartType form-control" data-cmd_id="#id#" style="width: 200px;display: inline-block;">
        <option value="line"> {{Ligne}} </option>
        <option value="area"> {{Aire}} </option>
        <option value="column">{{Colonne}}</option>
    </select>
    <span class="pull-right">Variation : <input type="checkbox" data-cmd_id="#id#" class="cb_derive" /></span>


    <script>
        $('.sel_chartType[data-cmd_id=#id#]').on('change', function() {
            $('#md_modal').dialog({title: "{{Historique}}"});
            $("#md_modal").load('index.php?v=d&modal=cmd.history&id=<?php echo init('id') ?>&graphType=' + $('.sel_chartType[data-cmd_id=#id#]').value() + '&derive=' + $('.cb_derive[data-cmd_id=#id#]').value()).dialog('open');
        });
        $('.cb_derive[data-cmd_id=#id#]').on('change', function() {
            $('#md_modal').dialog({title: "{{Historique}}"});
            $("#md_modal").load('index.php?v=d&modal=cmd.history&id=<?php echo init('id') ?>&graphType=' + $('.sel_chartType[data-cmd_id=#id#]').value() + '&derive=' + $('.cb_derive[data-cmd_id=#id#]').value()).dialog('open');
        });
        $('#div_historyChart').css('position', 'relative').css('width', '100%');
        delete jeedom.history.chart['div_historyChart'];
        jeedom.history.drawChart({
            cmd_id: "<?php echo init('id') ?>",
            el: 'div_historyChart',
            daterange: 'all',
            option: {
                graphType: "<?php echo init('graphType', 'line') ?>",
                derive: "<?php echo init('derive', '0') ?>"
            }
        });
    </script>
</div>





