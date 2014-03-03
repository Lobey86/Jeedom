<?php
require_once dirname(__FILE__) . "/../../core/php/core.inc.php";
include_file('core', 'authentification', 'php');
$startLoadTime = getmicrotime();
include_file("core", "pageDescriptor", "config");
global $PAGE_DESCRIPTOR_DESKTOP;
if (isConnect() && init('p') == '') {
    redirect('index.php?v=d&p=' . $_SESSION['user']->getOptions('homePage', 'dashboard'));
}
$page = 'Connexion';
if (isConnect() && init('p') != '') {
    $page = init('p');
}
if (isset($PAGE_DESCRIPTOR_DESKTOP[$page])) {
    $title = $PAGE_DESCRIPTOR_DESKTOP[$page]['title'];
} else {
    $title = $page;
}
$plugin = init('m');
if ($plugin != '') {
    $plugin = new plugin($plugin);
    if (is_object($plugin)) {
        $title = $plugin->getName();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Jeedom - <?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <META HTTP-EQUIV="Pragma" CONTENT="private">
        <META HTTP-EQUIV="Cache-Control" CONTENT="private, max-age=5400, pre-check=5400">
        <META HTTP-EQUIV="Expires" CONTENT="<?php echo date(DATE_RFC822, strtotime("1 day")); ?>">

        <!-- Le styles -->
        <?php
        include_file('3rdparty', 'bootstrap/css/bootstrap.min', 'css');
        ?>
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }
        </style>

        <?php
        include_file('3rdparty', 'font-awesome/css/font-awesome', 'css');
        include_file('desktop', 'commun', 'css');
        include_file('core', 'core', 'css');
        include_file('3rdparty', 'jquery.gritter/jquery.gritter', 'css');
        include_file('3rdparty', 'jquery.ui/jquery-ui-bootstrap/jquery-ui', 'css');
        if (config::byKey('enableChat') == 1 && config::byKey('enableNodeJs') == 1) {
            include_file('3rdparty', 'jquery.chatjs/jquery.chatjs', 'css');
        }
        include_file('3rdparty', 'jquery.loading/jquery.loading', 'css');
        include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
        include_file('3rdparty', 'jquery/jquery.min', 'js');
        include_file('3rdparty', 'php.js/php.min', 'js');
        ?>
    </head>

    <body>
        <?php
        if (!isConnect()) {
            require_once dirname(__FILE__) . "/connection.php";
        } else {
            sendVarToJS('userProfils', $_SESSION['user']->getOptions());
            sendVarToJS('user_id', $_SESSION['user']->getId());
            sendVarToJS('user_login', $_SESSION['user']->getLogin());
            if (config::byKey('enableNodeJs') == 1) {
                sendVarToJS('nodeJsKey', config::byKey('nodeJsKey'));
            } else {
                sendVarToJS('nodeJsKey', '');
            }
            ?>
            <div id="wrap">
                <header class="navbar navbar-fixed-top navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="index.php?v=d&p=<?php echo $_SESSION['user']->getOptions('homePage', 'dashboard'); ?>" style="font-size: 1.7em;">
                                <img src="core/img/jeedom_ico.png" height="19" width="20"/>eedom
                            </a>
                            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <nav class="navbar-collapse collapse">
                            <ul class="nav navbar-nav">
                                <li class="dropdown cursor">
                                    <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-home"></i> Accueil <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="index.php?v=d&p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                        <li><a href="index.php?v=d&p=view"><i class="fa fa-picture-o"></i> Vue</a></li>
                                    </ul>
                                </li>
                                <li><a href="index.php?v=d&p=history"><i class="fa fa-bar-chart-o"></i> Historique</a></li>
                                <?php if (isConnect('admin')) { ?>
                                    <li class="dropdown cursor">
                                        <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-qrcode"></i> Général <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="index.php?v=d&p=administration"><i class="fa fa-wrench"></i> Administration</a></li>
                                            <li><a href="index.php?v=d&p=interact"><i class="fa fa-comments-o"></i> Interaction</a></li>
                                            <li class='expertModeHidden'><a href="index.php?v=d&p=display"><i class="fa fa-th"></i> Affichage</a></li>
                                            <li class="expertModeHidden"><a href="index.php?v=d&p=cron"><i class="fa fa-tasks"></i> Moteur de taches</a></li>
                                            <li><a href="index.php?v=d&p=object"><i class="fa fa-picture-o"></i> Objet</a></li>
                                            <li><a href="index.php?v=d&p=plugin"><i class="fa fa-tags"></i> Plugins</a></li>
                                            <li class='expertModeHidden'><a href="index.php?v=d&p=log"><i class="fa fa-file-o"></i> Log</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <li><a href="index.php?v=d&p=scenario"><i class="fa fa-cogs"></i> Scénario</a></li>
                                <?php if (isConnect('admin')) { ?>
                                    <li class="dropdown cursor">
                                        <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-tasks"></i> Plugins <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <?php
                                            foreach (plugin::listPlugin() as $pluginList) {
                                                if ($pluginList->isActive() == 1) {
                                                    echo '<li><a href="index.php?v=d&m=' . $pluginList->getId() . '&p=' . $pluginList->getIndex() . '"><i class="' . $pluginList->getIcon() . '"></i> ' . $pluginList->getName() . '</a></li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                <?php } ?>
                            </ul>

                            <ul class="nav navbar-nav navbar-right">
                                <?php $displayMessage = (message::nbMessage() > 0) ? '' : 'display : none;'; ?>
                                <li><a href="index.php?v=d&p=message">
                                        <span class="label label-danger" id="span_nbMessage" style="<?php echo $displayMessage; ?>">
                                            <i class="fa fa-envelope"></i> <?php echo message::nbMessage(); ?> message(s)
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-clock-o"></i> <span id="horloge"><?php echo date('H:i:s'); ?></span>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-user"></i> <?php echo $_SESSION['user']->getLogin(); ?>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="index.php?v=d&p=profils"><i class="fa fa-briefcase"></i> Profil</a></li>
                                        <?php
                                        if ($_SESSION['user']->getOptions('expertMode') == 1) {
                                            echo '<li class="cursor"><a id="bt_expertMode" state="1"><i class="fa fa-check-square-o"></i> Mode expert</a></li>';
                                        } else {
                                            echo '<li class="cursor"><a id="bt_expertMode" state="0"><i class="fa fa-square-o"></i> Mode expert</a></li>';
                                        }
                                        ?>
                                        <li class="divider"></li>
                                        <li><a href="core/php/authentification.php?logout"><i class="fa fa-sign-out"></i> Se déconnecter</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="bt_pageHelp cursor"
                                    <?php
                                    echo 'data-name="' . init('p') . '"';
                                    if (isset($plugin) && is_object($plugin)) {
                                        echo 'data-plugin="' . $plugin->getId() . '"';
                                    }
                                    ?>accesskey="">
                                        <i class="fa fa-question-circle" ></i>
                                    </a>
                                </li>
                            </ul>
                        </nav><!--/.nav-collapse -->
                    </div>
                </header>
                <main class="container-fluid" id="div_mainContainer">
                    <ol class="breadcrumb">
                        <?php
                        if (isset($PAGE_DESCRIPTOR_DESKTOP[$page])) {
                            echo '<li class="active">' . $PAGE_DESCRIPTOR_DESKTOP[$page]['title'] . '</li>';
                        } else if (isset($plugin) && is_object($plugin)) {
                            echo '<li class="active">' . $plugin->getName() . ' : ' . $plugin->getDescription() . '</li>';
                        }
                        ?>
                    </ol>

                    <div style="display: none;width : 100%" id="div_alert"></div>
                    <?php
                    try {
                        if (isset($PAGE_DESCRIPTOR_DESKTOP[$page])) {
                            include_file('desktop', $PAGE_DESCRIPTOR_DESKTOP[$page]['pageName'], 'php');
                        } else if (isset($plugin) && is_object($plugin)) {
                            $status = $plugin->status();
                            if ($status['status'] == 'update') {
                                echo '<div class="row">';
                                echo '<div class="col-lg-2"></div>';
                                echo '<div class="col-lg-10">';
                                echo '<div class="alert alert-warning">Une nouvelle <a class="cursor bt_pluginUpdate" data-logicalId="' . $plugin->getId() . '">mise à jour</a> existe pour ' . $plugin->getName() . '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            include_file('desktop', $page, 'php', $plugin->getId());
                        } else {
                            echo '<div class="alert alert-danger div_alert">';
                            echo '404 - Request page not found';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger div_alert">';
                        echo displayExeption($e);
                        echo '</div>';
                    }
                    ?>
                    <div id="md_modal"></div>
                    <div id="md_modal2"></div>
                    <div id="md_pageHelp"></div>
                </main>
            </div>
        <?php } ?>
        <?php
        include_file('core', 'core', 'js');
        include_file('3rdparty', 'bootstrap/bootstrap.min', 'js');
        include_file('3rdparty', 'jquery.ui/jquery-ui.min', 'js');
        if (isConnect()) {
            include_file('core', 'js.inc', 'php');
            include_file('3rdparty', 'jquery.value/jquery.value', 'js');
            include_file('3rdparty', 'jquery.alert/jquery.alert', 'js');
            include_file('3rdparty', 'jquery.loading/jquery.loading', 'js');
            include_file('3rdparty', 'jquery.include/jquery.include', 'js');
            include_file('3rdparty', 'bootbox/bootbox.min', 'js');
            include_file('3rdparty', 'highstock/highstock', 'js');
            include_file('3rdparty', 'highstock/highcharts-more', 'js');
            include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
            include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
            include_file('desktop', 'utils', 'js');
            include_file('3rdparty', 'jquery.gritter/jquery.gritter.min', 'js');
            if (config::byKey('enableChat') == 1 && config::byKey('enableNodeJs') == 1) {
                include_file('core', 'chatAdapter', 'js');
                include_file('3rdparty', 'jquery.chatjs/jquery.chatjs', 'js');
                include_file('3rdparty', 'jquery.chatjs/jquery.autosize.min', 'js');
                include_file("desktop", "chat", "js");
            }
        }
        ?>
        <footer>
            <span class="pull-left">Node JS <span id="span_nodeJsState" class="binary red tooltips"></span> - </span>
            <span class="pull-left">&copy; <a id="bt_jeedomAbout" class="cursor">Jeedom</a> (v<?php echo getVersion('jeedom') ?> 
                <?php
                $version = jeedom::needUpdate();
                if ($version['needUpdate']) {
                    echo '<span class="label label-danger">Mise à jour disponible</span>';
                }
                echo ')';
                echo date('Y');
                $pageLoadTime = round(getmicrotime() - $startLoadTime, 3);
                echo ' - Page générée en ' . $pageLoadTime . 's';
                ?>
            </span>
        </footer>
        <script>
            var clientDatetime = new Date();
            var clientServerDiffDatetime = (<?php echo strtotime(date('Y-m-d H:i:s')); ?> * 1000) - clientDatetime.getTime();
        </script>
    </body>
</html>

