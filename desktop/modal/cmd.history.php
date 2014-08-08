<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div class="md_history" title="Historique">
    <center><div id="div_historyChart"></div></center>
    <select class="pull-right sel_chartType form-control" data-cmd_id="#id#" style="size: 120px;">
        <option value="line"> {{Ligne}} </option>
        <option value="area"> {{Aire}} </option>
        <option value="column">{{Colonne}}</option>
    </select>

    <script>
        $('.sel_chartType[data-cmd_id=#id#]').on('change', function() {
            $('#md_modal').dialog({title: "{{Historique}}"});
            $("#md_modal").load('index.php?v=d&modal=cmd.history&id=<?php echo init('id') ?>&graphType='+$(this).value()).dialog('open');
        });
        $('#div_historyChart').css('position', 'relative').css('width', '100%');
        delete jeedom.history.chart['div_historyChart'];
        jeedom.history.drawChart({cmd_id: "<?php echo init('id') ?>", el: 'div_historyChart', daterange: 'all', option: {graphType: "<?php echo init('graphType', 'line') ?>"}});
    </script>
</div>





