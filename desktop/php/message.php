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

        <table class="table table-condensed table-bordered" id="table_message" style="margin-top: 5px;">
            <thead>
                <tr>
                    <th></th><th>Date et heure</th><th>Module</th><th>Description</th><th>Action<i class="fa fa-filter pull-right showFilter"></i></th>
                </tr>
                <tr class="filter" style="display: none">
                    <td></td>
                    <td class="datetime"><input class="form-control" style="width: 100px;" class="filter" filterOn="datetime" /></td>
                    <td class="module"><input class="form-control" style="width: 100px;" class="filter" filterOn="module" /></td>
                    <td class="description"><input class="form-control" style="width: 300px;" class="filter" filterOn="description" /></td>
                    <td class="action"><input class="form-control" style="width: 100px;" class="filter" filterOn="action" /></td>
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
