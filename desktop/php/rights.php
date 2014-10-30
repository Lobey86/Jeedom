<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<select class="form-control pull-right" style="display: inline-block; width: 200px;">
    <?php
    foreach (user::all() as $user) {
        echo '<option value="' . $user->getId() . '">' . $user->getLogin() . '</option>';
    }
    ?>
</select>




<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#general" role="tab" data-toggle="tab">{{Générale}}</a></li>
    <li role="presentation"><a href="#eqLogic" role="tab" data-toggle="tab">{{Plugins/Equipements}}</a></li>
    <li role="presentation"><a href="#scenario" role="tab" data-toggle="tab">{{Scénarios}}</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <br/>
        <table class="table table-bordered table-condensed tablesorter" >
            <thead>
                <tr>
                    <td>{{Nom}}</td>
                    <td>{{Droits}}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span style="display: none;" ></span>Voir page administration</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div role="tabpanel" class="tab-pane" id="eqLogic">
        <thead>
            <tr>
                <td>{{Nom}}</td>
                <td>{{Droits}}</td>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </div>
    <div role="tabpanel" class="tab-pane" id="scenario">
        <thead>
            <tr>
                <td>{{Nom}}</td>
                <td>{{Droits}}</td>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </div>
</div>