<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

include_file('3rdparty', 'jquery.tree/themes/default/style.min', 'css');
include_file('3rdparty', 'jquery.tree/jquery.tree', 'js');

sendVarToJS('cmd_widgetDashboard', cmd::availableWidget('dashboard'));
sendVarToJS('cmd_widgetMobile', cmd::availableWidget('mobile'));
?>
<div class="row">
    <div class="col-lg-4" >
        <legend>{{Arbre des commandes}}</legend>
        <div id='div_tree'>
            <ul id='ul_rootTree'>
                <?php if (count(eqLogic::byObjectId(null)) > 0) { ?>
                    <li>
                        <a>{{Sans objet}}</a>
                        <ul>
                            <?php
                            foreach (eqLogic::byObjectId(null) as $eqLogic) {
                                echo '<li>';
                                echo '<a>' . $eqLogic->getName() . '</a>';
                                echo '<ul>';
                                foreach ($eqLogic->getCmd() as $cmd) {
                                    echo '<li>';
                                    echo '<a class="cmd" data-cmd_id="' . $cmd->getId() . '">' . $cmd->getName() . '</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php
                foreach (object::all() as $object) {
                    echo '<li>';
                    echo '<a class="infoObject" data-object_id="' . $object->getId() . '">' . $object->getName() . '</a>';
                    echo '<ul>';
                    foreach ($object->getEqLogic() as $eqLogic) {
                        echo '<li>';
                        echo '<a class="infoEqLogic" data-eqLogic_id="' . $eqLogic->getId() . '">' . $eqLogic->getName() . '</a>';
                        echo '<ul>';
                        foreach ($eqLogic->getCmd() as $cmd) {
                            echo '<li>';
                            echo '<a class="infoCmd" data-cmd_id="' . $cmd->getId() . '">' . $cmd->getName() . '</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-8">
        <legend>{{Informations}}</legend>
        <div id='div_displayInfo'></div>
    </div>
</div>

<?php include_file('desktop', 'display', 'js'); ?>