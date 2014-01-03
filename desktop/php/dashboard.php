<?php
if (!isConnect()) {
    throw new Exception('401 - Unauthorized access to page');
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
    <div class="col-lg-10">
        <?php
        echo '<div object_id="' . $object->getId() . '">';
        echo '<legend>' . $object->getName() . '</legend>';
        foreach ($object->getEqLogic() as $eqLogic) {
            if ($eqLogic->getIsVisible() == '1') {
                echo $eqLogic->toHtml('dashboard');
            }
        }
        echo '</div>';
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