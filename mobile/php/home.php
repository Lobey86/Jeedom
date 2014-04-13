<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div class="ui-grid-a">
    <div class="ui-block-a">
        <center>
            <a href="#equipmentMenu" data-rel="popup" data-transition="slideup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-a" style="margin: 5px;">
                <i class="fa fa fa-tachometer" style="font-size: 6em;"></i><br/>{{Equipements}}
            </a>
            <div data-role="popup" id="equipmentMenu" data-theme="b">
                <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">{{Fermer}}</a>
                <ul data-role="listview" data-inset="true" style="min-width:210px;">
                    <li data-role="list-divider">{{Selectionner l'object}}</li>
                    <?php
                    foreach (object::all() as $object) {
                        echo '<li class="li_object"><a href="index.php?v=m&p=equipment&object_id=' . $object->getId() . '" object_id="' . $object->getId() . '" father_id="' . $object->getFather_id() . '">' . $object->getName() . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </center>
    </div>

    <div class="ui-block-b">
        <center>
            <a href="index.php?v=m&p=scenario" data-role="button" data-theme="a" style="margin: 5px;">
                <i class="fa fa-cogs" style="font-size: 6em;"></i><br/>{{Scénario}}
            </a>
        </center>
    </div>



    <div class="ui-block-a">
        <center>
            <a href="#viewMenu" data-rel="popup" data-transition="slideup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-a" style="margin: 5px;">
                <i class="fa fa-picture-o" style="font-size: 6em;"></i><br/>{{Vues}}
            </a>
            <div data-role="popup" id="viewMenu" data-theme="b">
                <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">{{Fermer}}</a>
                <ul data-role="listview" data-inset="true" style="min-width:210px;">
                    <li data-role="list-divider">{{Selectionner la vue}}</li>
                    <?php
                    foreach (view::all() as $view) {
                        echo '<li><a href="index.php?v=m&p=view&id=' . $view->getId() . '">' . $view->getName() . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </center>
    </div>

    <div class="ui-block-b">
        <center>
            <a href="index.php?v=m&p=message" data-role="button" data-theme="a" style="margin: 5px;">
                <i class="fa fa-envelope-o" style="font-size: 6em;"></i><br/><?php echo message::nbMessage(); ?> {{Message(s)}}
            </a>
        </center>
    </div>

    <?php if (config::byKey('enableChat') == 1 && config::byKey('enableNodeJs') == 1) { ?>
        <div class="ui-block-a">
            <center>
                <a href="index.php?v=m&p=chat" data-role="button" data-theme="a" style="margin: 5px;">
                    <i class="fa fa-comment-o" style="font-size: 6em;"></i><br/> {{Chat}}
                </a>
            </center>
        </div>
    <?php } ?>
</div>