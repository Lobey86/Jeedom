<?php
if (!isConnect()) {
    throw new Exception('Error 401 Unauthorized');
}
$selectModule = init('module');
if ($selectModule != '') {
    $listMessage = message::byModule($selectModule);
} else {
    $listMessage = message::all();
}
?>
<div class="row">
    <div class="col-lg-12">
        <a class="btn btn-danger pull-right" id="bt_clearMessage"><i class="fa fa-trash-o icon-white"></i> Vider</a>
        <select id="sel_module" class="form-control" style="width: 200px;">
            <option value="" selected>Tout</option>
            <?php
            foreach (message::listModule() as $module) {
                if ($selectModule == $module['module']) {
                    echo '<option value="' . $module['module'] . '" selected>' . $module['module'] . '</option>';
                } else {
                    echo '<option value="' . $module['module'] . '">' . $module['module'] . '</option>';
                }
            }
            ?>
        </select>

        <table class="table table-condensed table-bordered tablesorter" id="table_message" style="margin-top: 5px;">
            <thead>
                <tr>
                    <th data-sorter="false" data-filter="false"></th><th>Date et heure</th><th>Module</th><th>Description</th><th data-sorter="false" data-filter="false">Action</th>
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
                    echo '<td class="message_action">' . $message->getAction() . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_file('desktop', 'message', 'js'); ?>
