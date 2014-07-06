<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<table id="table_timeline" class="table table-bordered table-condensed tablesorter" >
    <thead>
        <tr>
            <th style="width: 200px;">{{Date}}</th>
            <th style="width: 200px;">{{Evenement}}</th>
            <th>{{Valeur}}</th>
        </tr>
    </thead>
    <tbody> 
        <?php
        foreach (internalEvent::all() as $internalEvent) {
            echo '<tr>';
            echo '<td>';
            echo $internalEvent->getDatetime();
            echo '</td>';
            echo '<td>';
            echo $internalEvent->getEvent();
            echo '</td>';
            echo '<td>';
            echo '<table class="table table-bordered table-condensed" style="margin-bottom : 0px;">';
            echo '<thead>';
            echo '<tr>';
            foreach ($internalEvent->getOptions() as $key => $value) {
                echo '<th>';
                echo $key;
                echo '</th>';
                if ($key == 'id') {
                    echo '<th>{{Nom}}</th>';
                }
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            echo '<tr>';
            foreach ($internalEvent->getOptions() as $key => $value) {
                echo '<td>';
                echo $value;
                echo '</td>';
                if ($key == 'id') {
                    echo '<td>';
                    if (strpos($internalEvent->getEvent(), 'cmd') !== false) {
                        $cmd = cmd::byId($value);
                        if (is_object($cmd)) {
                            echo $cmd->getHumanName();
                        }
                    }
                    echo '</td>';
                }
            }
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
