<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}
$module = init('module');

if ($module != '') {
    $listMessage = message::byModule($module);
} else {
    $listMessage = message::all();
}
sendVarToJS('module', $module);
global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt">';
$rightPanel .= '<li data-role="list-divider">Action</li>';
$rightPanel .= '<li><a id="bt_clearMessage" href="#"><i class="fa fa-trash-o"></i> Vider</a></li>';
$rightPanel .= '</ul>';
$rightPanel .= '<br/><br/><br/>';
$rightPanel .= '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt">';
$rightPanel .= '<li data-role="list-divider">Logfile</li>';
$rightPanel .= '<li><a href="index.php?v=m&p=message" data-ajax="false">Tout</a></li>';

foreach (message::listModule() as $module) {
    $rightPanel .= '<li><a href="index.php?v=m&p=message&module=' . $module['module'] . '" data-ajax="false">' . $module['module'] . '</a></li>';
}
$rightPanel .= '</ul>';
?>

<h2 style="position: relative; top : -10px;margin-top: 0px;margin-bottom: 0px;text-align: center;">
    <?php echo init('module', 'Tous'); ?>
</h2>


<table data-role="table" id="table_message" data-mode="columntoggle" class="ui-responsive table-stroke">
    <thead>
        <tr>
            <th data-priority="1"></th>
            <th data-priority="2">Date</th>
            <th data-priority="3">Module</th>
            <th data-priority="1">Description</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($listMessage as $message) {
            echo '<tr message_id="' . $message->getId() . '">';
            echo '<td><center><i class="fa fa-trash-o cursor removeMessage"></i></center></td>';
            echo '<td class="datetime">' . $message->getDate() . '</td>';
            echo '<td class="module">' . $message->getModule() . '</td>';
            echo '<td class="message">' . $message->getMessage() . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<?php include_file('mobile', 'message', 'js'); ?>