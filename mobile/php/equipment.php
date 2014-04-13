<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ul_object ui-icon-alt">';
foreach (object::all() as $object) {
    $rightPanel .= '<li class="li_object"><a href="index.php?v=m&p=equipment&object_id=' . $object->getId() . '" object_id="' . $object->getId() . '" father_id="' . $object->getFather_id() . '">' . $object->getName() . '</a></li>';
}
$rightPanel .= '</ul>';


sendVarToJS('object_id', init('object_id', -1));
if (init('object_id') != '') {
    $object = object::byId(init('object_id'));
    if (!is_object($object)) {
        throw new Exception('{{Object ID non trouvé}}');
    }
}
if (init('object_id') != '') {
    echo '<div id="div_equipmentList">';
    foreach ($object->getEqLogic() as $eqLogic) {
        if ($eqLogic->getIsVisible() == '1') {
            echo $eqLogic->toHtml('mobile');
        }
    }

    echo '</div>';
}
include_file('mobile', 'equipement', 'js');
?>
