<?php
if (!isConnect('admin')) {
    throw new Exception('401 - Unauthorized access to page');
}
include_file('3rdparty', 'codemirror/lib/codemirror', 'js');
include_file('3rdparty', 'codemirror/lib/codemirror', 'css');
include_file('3rdparty', 'codemirror/addon/edit/matchbrackets', 'js');
include_file('3rdparty', 'codemirror/mode/htmlmixed/htmlmixed', 'js');
include_file('3rdparty', 'codemirror/mode/xml/xml', 'js');
include_file('3rdparty', 'codemirror/mode/javascript/javascript', 'js');
?>
<style>
    .CodeMirror {
        border: 1px solid #eee;
        height: auto;
    }
    .CodeMirror-scroll {
        overflow-y: hidden;
        overflow-x: auto;
    }

</style>

<div class="row">
    <div class="col-lg-2">
        <div class="bs-sidebar affix">
            <ul id="ul_widget" class="nav nav-list bs-sidenav fixnav">
                <li class="nav-header">Liste des widgets
                    <i class="fa fa-plus-circle pull-right cursor widgetAction" data-action="add" style="font-size: 1.5em;margin-bottom: 5px;"></i>
                </li>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="Rechercher" style="width: 100%"/></li>
                <li class="nav-header">Dashboard</li>
                <?php
                foreach (widget::listWidget('dashboard') as $widget) {
                    echo '<li class="cursor li_widget" data-path="' . $widget->getPath() . '"><a>' . $widget->getHumanName() . '</a></li>';
                }
                ?>
                <li class="nav-header">Mobile</li>
                <?php
                foreach (widget::listWidget('mobile') as $widget) {
                    echo '<li class="cursor li_widget" data-path="' . $widget->getPath() . '"><a>' . $widget->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 widget" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">

        <div class="row">
            <div class="col-lg-6">
                <legend>Général</legend>
                <form class="form-horizontal">
                    <fieldset>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Nom du widget</label>
                            <div class="col-lg-6">
                                <input type="text" class="widgetAttr form-control" data-l1key="path" style="display : none;" />
                                <input type="text" class="widgetAttr form-control" data-l1key="name" placeholder="Nom du widget"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Version</label>
                            <div class="col-lg-6">
                                <select class="widgetAttr form-control" data-l1key='version'>
                                    <option value='dashboard'>Dashboard</option>
                                    <option value='mobile'>Mobile</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Type</label>
                            <div class="col-lg-6">
                                <select class="widgetAttr form-control" data-l1key='type'>
                                    <option value='none'>Aucun</option>
                                    <?php
                                    foreach (cmd::allType() as $type) {
                                        echo '<option value="' . $type['type'] . '">' . $type['type'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Sous-type</label>
                            <div class="col-lg-6">
                                <select class="widgetAttr form-control" data-l1key='subtype'>
                                    <option value='none'>Aucun</option>
                                    <?php
                                    foreach (cmd::allSubType() as $subtype) {
                                        echo '<option value="' . $subtype['subtype'] . '">' . $subtype['subtype'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="col-lg-6" >
                <legend>Apercu <a class="btn btn-xs btn-default pull-right" id="bt_applyWidget"><i class="fa fa-fire"></i> Appliquer sur des commandes</a></legend>
                <div class="col-lg-6" id='div_widgetResult'></div>
            </div>
        </div>

        <textarea class='form-control widgetAttr' data-l1key='content' id='ta_widgetContent' style='height: 500px;'></textarea>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger widgetAction" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
                    <a class="btn btn-success widgetAction" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php include_file('desktop', 'widget', 'js', 'widget'); ?>