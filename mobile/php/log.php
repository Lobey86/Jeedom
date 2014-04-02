<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}

$nbLinePerPage = 50;

$page = init('page', 1);
$logfile = init('logfile', 'core');
$list_logfile = array();
$dir = opendir('log/');
while ($file = readdir($dir)) {
    if ($file != '.' && $file != '..') {
        $list_logfile[] = $file;
    }
}
if ($logfile == '') {
    $logfile = $list_logfile[0];
}
sendVarToJS('logfile', $logfile);
global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt">';
$rightPanel .= '<li data-role="list-divider">Action</li>';
$rightPanel .= '<li><a id="bt_clearLog" href="#"><i class="fa fa-trash-o"></i> Vider</a></li>';
$rightPanel .= '<li><a id="bt_removeLog" href="#"><i class="fa fa-times"></i> Supprimer</a></li>';
$rightPanel .= '</ul>';
$rightPanel .= '<br/>';
$rightPanel .= '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt">';
$rightPanel .= '<li data-role="list-divider">Logfile</li>';

foreach ($list_logfile as $file) {
    $rightPanel .= '<li><a href="index.php?v=m&p=log&logfile=' . $file . '">' . $file . '</a></li>';
}
$rightPanel .= '</ul>';



$nbLine = log::nbLine($logfile);
$nbPage = ceil($nbLine / $nbLinePerPage);
$firstLine = $nbLine - $nbLinePerPage * $page;
if ($firstLine < 0) {
    $nbLinePerPage+=$firstLine;
    $firstLine = 0;
}
$log = log::get($logfile, $firstLine, $nbLinePerPage);

if (isset($log[0][0]) && $log[0][0] == '') {
    unset($log[0]);
}
?>

<select id="sel_page" data-mini="true" data-inline="true">
    <?php
    for ($i = 1; $i <= $nbPage; $i++) {
        if ($i == $page) {
            echo '<option value="' . $i . '" selected>' . $i . '</option>';
        } else {
            echo '<option value="' . $i . '">' . $i . '</option>';
        }
    }
    ?>
</select>

<span style="font-size: 1.8em;">
    <?php echo $logfile; ?>
</span>

<table data-role="table" id="table_log" data-mode="columntoggle" class="ui-responsive table-stroke">
    <thead>
        <tr>
            <th data-priority="1">Date</th>
            <th data-priority="3">Type</th>
            <th data-priority="1">Message</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($log !== false) {
            foreach ($log as $ligne) {
                $class = '';
                if (strtolower($ligne[1]) == 'error') {
                    $class = 'alert alert-danger';
                }
                if (strtolower($ligne[1]) == 'event') {
                    $class = 'alert alert-success';
                }
                if (strtolower($ligne[1]) == 'connexion' || strtolower($ligne[1]) == 'info') {
                    $class = 'alert alert-info';
                }
                if (strtolower($ligne[1]) == 'debug') {
                    $class = 'alert alert-warning';
                }
                echo '<tr class="' . $class . '">';
                echo '<td class="datetime">' . $ligne[0] . '</td>';
                echo '<td class="type">' . $ligne[1] . '</td>';
                echo '<td class="message">' . $ligne[2] . '</td>';
                echo '</tr>';
            }
        }
        ?>
    </tbody>
</table>

<script>
    $(function() {
        $('#sel_page').on('change', function() {
            var page = $(this).value();
            window.location = 'index.php?v=m&p=log&page=' + page + '&logfile=' + logfile;
        });



        $("#bt_clearLog").on('click', function(event) {
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // methode de transmission des données au fichier php
                url: "core/ajax/log.ajax.php", // url du fichier php
                data: {
                    action: "clear",
                    logfile: logfile
                },
                dataType: 'json',
                error: function(request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function(data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    } else {
                        window.location.reload();
                    }
                }
            });
        });

        $("#bt_removeLog").on('click', function(event) {
            $.ajax({// fonction permettant de faire de l'ajax
                type: "POST", // methode de transmission des données au fichier php
                url: "core/ajax/log.ajax.php", // url du fichier php
                data: {
                    action: "remove",
                    logfile: logfile
                },
                dataType: 'json',
                error: function(request, status, error) {
                    handleAjaxError(request, status, error);
                },
                success: function(data) { // si l'appel a bien fonctionné
                    if (data.state != 'ok') {
                        $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    } else {
                        window.location.href = 'index.php?v=m&p=log';
                    }
                }
            });
        });
    });
</script>