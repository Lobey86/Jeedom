<?php
if (!isConnect()) {
    throw new Exception('401 - Unauthorized access to page');
}
if (init('object_id') == '') {
    $_GET['object_id'] = $_SESSION['user']->getOptions('defaultDashboardObject', 'global');
}
$object = object::byId(init('object_id'));
if (!is_object($object)) {
    $object = object::rootObject();
}
if (!is_object($object)) {
    throw new Exception('Aucun objet racine trouvé');
}
?>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_object" class="nav nav-list bs-sidenav">
                <li class="nav-header">Liste objects </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="form-control" class="filter form-control" placeholder="Rechercher" style="width: 100%"/></li>
                <?php
                if (init('object_id') == 'global') {
                    echo '<li class="cursor li_object active"><a href="index.php?v=d&p=dashboard&object_id=global">Global</a></li>';
                } else {
                    echo '<li class="cursor li_object"><a href="index.php?v=d&p=dashboard&object_id=global">Global</a></li>';
                }
                $allObject = object::all();
                foreach ($allObject as $object_li) {
                    if ($object_li->getId() == init('object_id')) {
                        echo '<li class="cursor li_object active"><a href="index.php?v=d&p=dashboard&object_id=' . $object_li->getId() . '">' . $object_li->getName() . '</a></li>';
                    } else {
                        echo '<li class="cursor li_object"><a href="index.php?v=d&p=dashboard&object_id=' . $object_li->getId() . '">' . $object_li->getName() . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <?php
        echo '<div object_id="' . $object->getId() . '">';
        echo '<legend>' . $object->getName() . '</legend>';
        foreach ($object->getEqLogic() as $eqLogic) {
            if ($eqLogic->getIsVisible() == '1') {
                echo $eqLogic->toHtml('dashboard');
            }
        }
        foreach ($object->getChilds() as $child) {
            if (count($child->getEqLogic()) > 0) {
                $margin = 40 * $child->parentNumber();
                echo '<div object_id="' . $child->getId() . '" style="margin-left : ' . $margin . 'px;">';
                echo '<legend>' . $child->getName() . '</legend>';
                foreach ($child->getEqLogic() as $eqLogic) {
                    if ($eqLogic->getIsVisible() == '1') {
                        echo $eqLogic->toHtml('dashboard');
                    }
                }
                echo '</div>';
            }
        }
        echo '</div>';

        if (init('object_id') == 'global') {
            foreach ($object->getChilds() as $child) {
                if (count($child->getEqLogic()) > 0) {
                    $margin = 40 * $child->parentNumber();
                    echo '<div object_id="' . $child->getId() . '" style="margin-left : ' . $margin . 'px;">';
                    echo '<legend>' . $child->getName() . '</legend>';
                    foreach ($child->getEqLogic() as $eqLogic) {
                        if ($eqLogic->getIsVisible() == '1') {
                            echo $eqLogic->toHtml('dashboard');
                        }
                    }
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    <div class="col-lg-2">
        <legend>Scénarios</legend>
        <?php
        foreach (scenario::all() as $scenario) {
            echo $scenario->toHtml('dashboard');
        }
        ?>
    </div>     
</div>