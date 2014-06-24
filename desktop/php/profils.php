<?php
if (!isConnect()) {
    throw new Exception('{{Error 401 Unauthorized');
}

$notifyTheme = array(
    'info' => '{{Bleu}}',
    'error' => '{{Rouge}}',
    'success' => '{{Vert}}',
    'warning' => '{{Jaune}}',
);

$homePage = array(
    'core::dashboard' => '{{Dashboard}}',
    'core::view' => '{{Vue}}',
);
foreach (plugin::listPlugin() as $pluginList) {
    if ($pluginList->isActive() == 1 && $pluginList->getDisplay() != '') {
        $homePage[$pluginList->getId() . '::' . $pluginList->getDisplay()] = $pluginList->getName();
    }
}
?>
<legend>{{Profil}}</legend>

<div class="panel-group" id="accordionConfiguration">
    <input style="display: none;" class="userAttr form-control" data-l1key="id" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionConfiguration" href="#config_notification">
                    {{Notifications}}
                </a>
            </h3>
        </div>
        <div id="config_notification" class="panel-collapse collapse in">
            <div class="panel-body">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Notifier des évenements}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="notifyEvent">
                                    <?php
                                    foreach ($notifyTheme as $key => $value) {
                                        echo "<option value='$key'>$value</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Notifier du lancement des scénarios}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="notifyLaunchScenario">
                                    <?php
                                    foreach ($notifyTheme as $key => $value) {
                                        echo "<option value='$key'>$value</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Notifier nouveau message}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="notifyNewMessage">
                                    <?php
                                    foreach ($notifyTheme as $key => $value) {
                                        echo "<option value='$key'>$value</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionConfiguration" href="#config_interface">
                    Interface
                </a>
            </h3>
        </div>
        <div id="config_interface" class="panel-collapse collapse">
            <div class="panel-body">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Page d'accueil}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="homePage">
                                    <?php
                                    foreach ($homePage as $key => $value) {
                                        echo "<option value='$key'>$value</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Vue par défault(desktop)}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="defaultDesktopView">
                                    <?php
                                    foreach (view::all() as $view) {
                                        echo "<option value='" . $view->getId() . "'>" . $view->getName() . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Objet par défault(desktop)}}</label>
                            <div class="col-lg-3">
                                <select class="userAttr form-control" data-l1key="options" data-l2key="defaultDashboardObject">
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo "<option value='" . $object->getId() . "'>" . $object->getName() . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionConfiguration" href="#config_other">
                    {{Autre}}
                </a>
            </h3>
        </div>
        <div id="config_other" class="panel-collapse collapse">
            <div class="panel-body">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Mot de passe}}</label>
                            <div class="col-lg-3">
                                <input type="password" class="userAttr form-control" data-l1key="password" />
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-lg-3 control-label">{{Retapez le mot de passe}}</label>
                            <div class="col-lg-3">
                                <input type="password" class="form-control" id="in_passwordCheck" />
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    <br/> 
    <div class="form-actions">
        <a class="btn btn-success" id="bt_saveProfils"><i class="fa fa-check-circle icon-white"></i> {{Sauvegarder}}</a>
    </div>
</div>
<?php include_file("desktop", "profils", "js"); ?>