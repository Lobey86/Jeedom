<?php
if (!isConnect()) {
    include_file('mobile', '401', 'php');
    die();
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
        throw new Exception('Object ID non trouvé');
    }
}
?>

<h2 style="position: relative; top : -10px;margin-top: 0px;margin-bottom: 0px;text-align: center;">
    <span id="span_objectName"></span>
</h2>


<?php if (init('object_id') != '') { ?>
    <div id="div_equipmentList" style="margin-top : 5px;">
        <?php
        foreach ($object->getEqLogic() as $eqLogic) {
            if ($eqLogic->getIsVisible() == '1') {
                echo $eqLogic->toHtml('mobile');
            }
        }
        ?>
    </div>
<?php } ?>

<?php include_file('mobile', 'equipement', 'js'); ?>
