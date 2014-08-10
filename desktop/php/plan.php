<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file('3rdparty', 'jquery.masonry/jquery.masonry', 'js');
if (init('object_id') == '') {
    $object = object::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
} else {
    $object = object::byId(init('object_id'));
}
if (!is_object($object)) {
    $object = object::rootObject();
}
if (!is_object($object)) {
    throw new Exception('{{Aucun objet racine trouvé. Pour en créer un, allez dans Générale -> Objet.<br/> Si vous ne savez pas quoi faire ou que c\'est la premiere fois que vous utilisez Jeedom n\'hésitez pas a consulter cette <a href="http://jeedom.fr/premier_pas.php" target="_blank">page</a>}}');
}
$child_object = object::buildTree($object);
$parentNumber = array();
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <center>
            <?php
            if (init('category', 'all') == 'all') {
                echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=all" class="btn btn-primary btn-sm categoryAction" style="margin-bottom: 5px;margin-right: 3px;">{{Tous}}</a>';
            } else {
                echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=all" class="btn btn-default btn-sm categoryAction" style="margin-bottom: 5px;margin-right: 3px;">{{Tous}}</a>';
            }
            foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                if (init('category', 'all') == $key) {
                    echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=' . $key . '" class="btn btn-primary btn-sm categoryAction" data-l1key="' . $key . '" style="margin-bottom: 5px;margin-right: 3px;">{{' . $value['name'] . '}}</a>';
                } else {
                    echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=' . $key . '" class="btn btn-default btn-sm categoryAction" data-l1key="' . $key . '" style="margin-bottom: 5px;margin-right: 3px;">{{' . $value['name'] . '}}</a>';
                }
            }
            if (init('category', 'all') == 'other') {
                echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=other" class="btn btn-primary btn-sm categoryAction" style="margin-bottom: 5px;margin-right: 3px;">{{Autre}}</a>';
            } else {
                echo '<a href="index.php?v=d&p=plan&object_id=' . init('object_id') . '&category=other" class="btn btn-default btn-sm categoryAction" style="margin-bottom: 5px;margin-right: 3px;">{{Autre}}</a>';
            }
            ?>
        </center>
        <div class="bs-sidebar">
            <ul id="ul_object" class="nav nav-list bs-sidenav">
                <li class="nav-header">{{Liste objets}} </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                $allObject = object::buildTree(null, true);
                foreach ($allObject as $object_li) {
                    $parentNumber[$object_li->getId()] = $object_li->parentNumber();
                    $margin = 15 * $parentNumber[$object_li->getId()];
                    if ($object_li->getId() == $object->getId()) {
                        echo '<li class="cursor li_object active" data-object_id="'.$object_li->getId().'" ><a href="index.php?v=d&p=plan&object_id=' . $object_li->getId() . '&category=' . init('category', 'all') . '" style="position:relative;left:' . $margin . 'px;">' . $object_li->getDisplay('icon') . ' ' . $object_li->getName() . '</a></li>';
                    } else {
                        echo '<li class="cursor li_object" data-object_id="'.$object_li->getId().'" ><a href="index.php?v=d&p=plan&object_id=' . $object_li->getId() . '&category=' . init('category', 'all') . '" style="position:relative;left:' . $margin . 'px;">' . $object_li->getDisplay('icon') . ' ' . $object_li->getName() . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10">
        <a class="btn btn-success pull-right btn-xs cursor" style="margin-bottom: 3px;" id="bt_savePlan"><i class="fa fa-check"></i> {{Enregistrer}}</a>
        <a class="btn btn-warning pull-right btn-xs cursor" style="margin-bottom: 3px;"><i class="fa fa-pencil"></i> {{Editer}}</a>
        <a class="btn btn-info pull-right btn-xs cursor" style="margin-bottom: 3px;"><i class="fa fa-plus-circle"></i> {{Ajouter scénario}}</a>
        <a class="btn btn-info pull-right btn-xs cursor" style="margin-bottom: 3px;" id="bt_addEqLogic"><i class="fa fa-plus-circle"></i> {{Ajouter équipement}}</a>
        <div id="div_displayObject">
            <img src="data:image/<?php echo $object->getImage('type') ?>;base64,<?php echo $object->getImage('data') ?>" class="img-responsive">
        </div>
    </div>
</div>

<?php include_file('desktop', 'plan', 'js'); ?>