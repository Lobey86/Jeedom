<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
if (init('object_id') == '') {
    $_GET['object_id'] = $_SESSION['user']->getOptions('defaultDashboardObject', 'global');
}
$objects = object::byId(init('object_id'));
if (!is_object($objects)) {
    $objects = object::rootObject(true);
}
if (!is_array($objects)) {
    $objects = array($objects);
}
if (!is_array($objects)) {
    throw new Exception('{{Aucun objet racine trouvé}}');
}
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_object" class="nav nav-list bs-sidenav">
                <li class="nav-header">{{Liste objects}} </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                if (init('object_id') == 'global') {
                    echo '<li class="cursor li_object active"><a href="index.php?v=d&p=dashboard&object_id=global&category=' . init('category', 'all') . '">{{Global}}</a></li>';
                } else {
                    echo '<li class="cursor li_object"><a href="index.php?v=d&p=dashboard&object_id=global&category=' . init('category', 'all') . '">{{Global}}</a></li>';
                }
                $allObject = object::buildTree();
                foreach ($allObject as $object_li) {
                    $margin = 15 * $object_li->parentNumber();
                    if ($object_li->getId() == init('object_id')) {
                        echo '<li class="cursor li_object active" ><a href="index.php?v=d&p=dashboard&object_id=' . $object_li->getId() . '&category=' . init('category', 'all') . '" style="position:relative;left:' . $margin . 'px;">' . $object_li->getName() . '</a></li>';
                    } else {
                        echo '<li class="cursor li_object" ><a href="index.php?v=d&p=dashboard&object_id=' . $object_li->getId() . '&category=' . init('category', 'all') . '" style="position:relative;left:' . $margin . 'px;">' . $object_li->getName() . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div style="position: fixed;width: 100%;z-index: 1029;top : 51px;left : 35%">
            <div class="btn-group tooltips" title="{{Filtre sur les catégories d'équipement}}">
                <?php
                if (init('category', 'all') == 'all') {
                    echo '<a type="button" href="index.php?v=d&p=dashboard&object_id=' . init('object_id') . '&category=all" class="btn btn-primary categoryAction">{{Tous}}</a>';
                } else {
                    echo '<a type="button" href="index.php?v=d&p=dashboard&object_id=' . init('object_id') . '&category=all" class="btn btn-default categoryAction">{{Tous}}</a>';
                }
                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                    if (init('category', 'all') == $key) {
                        echo '<a type="button" href="index.php?v=d&p=dashboard&object_id=' . init('object_id') . '&category=' . $key . '" class="btn btn-primary categoryAction" data-l1key="' . $key . '">{{' . $value['name'] . '}}</a>';
                    } else {
                        echo '<a type="button" href="index.php?v=d&p=dashboard&object_id=' . init('object_id') . '&category=' . $key . '" class="btn btn-default categoryAction" data-l1key="' . $key . '">{{' . $value['name'] . '}}</a>';
                    }
                }
                ?>
            </div>
        </div>
        <div style="height: 10px;width: 100%;"></div>

        <?php
        foreach ($objects as $object) {
            echo '<div object_id="' . $object->getId() . '">';
            echo '<legend>' . $object->getName() . '</legend>';
            foreach ($object->getEqLogic() as $eqLogic) {
                if ($eqLogic->getIsVisible() == '1' && (init('category', 'all') == 'all' || $eqLogic->getCategory(init('category')) == 1)) {
                    echo $eqLogic->toHtml('dashboard');
                }
            }
            foreach (object::buildTree($object) as $child) {
                $margin = 40 * $child->parentNumber();
                echo '<div object_id="' . $child->getId() . '" style="margin-left : ' . $margin . 'px;">';
                echo '<legend>' . $child->getName() . '</legend>';
                foreach ($child->getEqLogic() as $eqLogic) {
                    if ($eqLogic->getIsVisible() == '1' && (init('category', 'all') == 'all' || $eqLogic->getCategory(init('category')) == 1)) {
                        echo $eqLogic->toHtml('dashboard');
                    }
                }
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
    <div class="col-lg-2">
        <legend>{{Scénarios}}</legend>
        <?php
        if (init('object_id') == 'global') {
            foreach (scenario::byObjectId(null, false) as $scenario) {
                if ($scenario->getIsVisible() == 1) {
                    echo $scenario->toHtml('dashboard');
                }
            }
        }
        foreach ($objects as $object) {
            foreach ($object->getScenario(false) as $scenario) {
                if ($scenario->getIsVisible() == 1) {
                    echo $scenario->toHtml('dashboard');
                }
            }
            foreach ($object->getChilds() as $child) {
                foreach ($child->getScenario(false) as $scenario) {
                    if ($scenario->getIsVisible() == 1) {
                        echo $scenario->toHtml('dashboard');
                    }
                }
            }
        }
        ?>
    </div>     
</div>
