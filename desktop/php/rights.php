<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$pages = array(
    'administration' => array(
        'title' => 'Administration',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'backup' => array(
        'title' => 'Utilisateur',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'cron' => array(
        'title' => 'Moteur de tache',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'display' => array(
        'title' => 'Affichage',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'interact' => array(
        'title' => 'Interaction',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'jeeNetwork' => array(
        'title' => 'Reseaux jeedom',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'log' => array(
        'title' => 'Log',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'message' => array(
        'title' => 'Message',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'object' => array(
        'title' => 'Objet',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'plan' => array(
        'title' => 'Design',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'plugin' => array(
        'title' => 'Plugin',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'scenario' => array(
        'title' => 'Scenario',
        'view' => array('title' => 'Voiro'),
        'edit' => array('title' => 'Editer')
    ),
    'security' => array(
        'title' => 'Sécurité',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'timeline' => array(
        'title' => 'Timeline',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'update' => array(
        'title' => 'Mise à jour',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'user' => array(
        'title' => 'Utilisateur',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
    'view' => array(
        'title' => 'Vue',
        'view' => array('title' => 'Voir'),
        'edit' => array('title' => 'Editer')
    ),
);

include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
?>
<a class="btn btn-success pull-right"><i class="fa fa-floppy-o"></i> Sauvegarder</a>
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
                    <td style="width: 100px;">{{Droits}}</td>
                    <td style="width: 250px;">{{Nom}}</td>
                    <td>{{Description}}</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($pages as $kpage => $page) {
                    echo '<tr class="rights">';
                    echo '<td>';
                    foreach ($page as $kright => $right) {
                        if ($kright != 'title' && $kright != 'title') {
                            echo '<input type="checkbox" data-l1key="' . $kpage . $kright . '"  checked />' . $right['title'] . '<br/>';
                        }
                    }
                    echo '</td>';
                    echo '<td>';
                    echo $page['title'];
                    echo '</td>';
                    echo '<td>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
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