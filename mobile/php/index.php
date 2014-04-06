<?php
require_once dirname(__FILE__) . "/../../core/php/core.inc.php";
include_file('core', 'authentification', 'php');
include_file("core", "pageDescriptor", "config");
global $PAGE_DESCRIPTOR_MOBILE;
$page = 'connection';
if (isConnect()) {
    if (init('p') == '' || init('p') == 'connection') {
        $page = 'home';
    } else {
        $page = init('p');
    }
}
if (isset($PAGE_DESCRIPTOR_MOBILE[$page])) {
    $title = $PAGE_DESCRIPTOR_MOBILE[$page]['title'];
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
<html  manifest="site.manifest">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" /> 
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="apple-touch-icon" sizes="128x128" href="core/img/jeedom_128x128.png" />
        <link rel="apple-touch-startup-image" sizes="256x256" href="core/img/jeedom_256x256.png" />
        <link rel="apple-touch-icon-precomposed" sizes="256x256" href="core/img/jeedom_256x256.png">
        <link rel="shortcut icon" sizes="128x128" href="core/img/jeedom_128x128.png">
        <link rel="apple-touch-icon" href="core/img/jeedom_128x128.png" />
        <link rel="apple-touch-icon-precomposed" href="core/img/jeedom_256x256.png">

        <!-- iPhone SPLASHSCREEN-->
        <link href="core/img/jeedom_320x460.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
        <!-- iPhone (Retina) SPLASHSCREEN-->
        <link href="core/img/jeedom_640x920.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
        <!-- iPad (portrait) SPLASHSCREEN-->
        <link href="core/img/jeedom_768x1004.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
        <!-- iPad (landscape) SPLASHSCREEN-->
        <link href="core/img/jeedom_748x1024.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
        <!-- iPad (Retina, portrait) SPLASHSCREEN-->
        <link href="core/img/jeedom_1536x2008.png" media="(device-width: 1536px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
        <!-- iPad (Retina, landscape) SPLASHSCREEN-->
        <link href="core/img/jeedom_2048x1496.png" media="(device-width: 1536px)  and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">


        <title>Jeedom</title> 
        <?php
        include_file('3rdparty', 'jquery.mobile/jquery.mobile', 'css');
        include_file('3rdparty', 'font-awesome/css/font-awesome.min', 'css');
        include_file('mobile', 'commun', 'css');
        include_file('core', 'core', 'css');
        include_file('3rdparty', 'jquery.loading/jquery.loading', 'css');
        include_file('3rdparty', 'jquery/jquery.min', 'js');
        include_file('3rdparty', 'php.js/php.min', 'js');
        include_file('3rdparty', 'jquery.mobile/jquery.mobile.min', 'js');
        include_file('3rdparty', 'highstock/highstock', 'js');
        include_file('3rdparty', 'highstock/highcharts-more', 'js');
        include_file('3rdparty', 'jquery.include/jquery.include', 'js');
        include_file('core', 'js.inc', 'php');
        ?>

    </head> 
    <body> 
        <div data-role="page" class="type-interior" id="div_container" data-title="Jeedom">
            <div data-role="header" data-theme="a" data-position="fixed">
                <!-- <a href="#" data-icon="back" data-rel="back" title="Retour" data-iconpos="notext">Retour</a> -->
                <h1 style="margin: 0 10px;">
                    <img src="core/img/jeedom_ico.png" height="17" width="18" style="position: relative; top : 3px;"/>eedom
                    <span class="horloge"><?php echo date('H:i:s'); ?></span>
                </h1>
                <a href="#leftpanel" class="bt_leftpanel" data-icon="bars" data-iconpos="notext">Menu</a>

                <a href="#rightpanel" class="bt_rightpanel" data-icon="gear" data-iconpos="notext">Options</a>
            </div><!-- /header -->
            <div data-role="content" id='pagecontainer'>
                <a href="#div_alert" data-rel="popup" data-position-to="window"></a>
                <div data-role="popup" id="div_alert"></div>
                <?php
                if (!isConnect()) {
                    include_file('mobile', 'connection', 'php');
                } else {
                    sendVarToJS('userProfils', $_SESSION['user']->getOptions());
                    sendVarToJS('user_id', $_SESSION['user']->getId());
                    sendVarToJS('user_login', $_SESSION['user']->getLogin());
                    sendVarToJS('nodeJsKey', config::byKey('nodeJsKey'));
                    sendVarToJS('otherUserId', init('otherUserId '));
                    include_file('core', 'core', 'js');
                    try {
                        if (isset($PAGE_DESCRIPTOR_MOBILE[$page])) {
                            include_file('mobile', $PAGE_DESCRIPTOR_MOBILE[$page]['pageName'], 'php');
                        } else if (isset($plugin) && is_object($plugin)) {
                            include_file('plugins', $page, 'php');
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
                }
                ?>
                <div id="div_loading" style="position: absolute; top: 45%;left: 45%;display: none">
                    <img src="3rdparty/jquery.mobile/images/ajax-loader.gif" />
                </div>
            </div><!-- /content -->
            <div data-role="footer" data-theme="a" data-position="fixed">
                <span style="margin-left: 0px;">&copy; Jeedom (v<?php echo getVersion('jeedom') ?>) <?php echo date('Y') ?> </span>
                <span style="float: right;">Node JS <span class="span_nodeJsState binary red"></span></span>
            </div><!-- /footer -->

            <div data-role="panel" id="leftpanel" data-position="left" data-display="push" data-theme="b" data-position-fixed="true" data-animate="false" class="ui-icon-alt">
                <ul data-role="listview">
                    <li><a href="index.php?v=m&p=home" data-theme="a"><i class="fa fa-home"></i> Accueil</a></li>
                    <li><a href="index.php?v=m&p=equipment" data-theme="a"><i class="fa fa fa-tachometer" ></i> Equipements </a></li>
                    <li><a href="index.php?v=m&p=scenario" data-theme="a"><i class="fa fa-cogs"></i> Scénario</a></li>
                    <li><a href="index.php?v=m&p=view" data-theme="a"><i class="fa fa-picture-o"></i> Vues</a></li>
                    <?php if (config::byKey('enableChat') == 1 && config::byKey('enableNodeJs') == 1) { ?>
                        <li><a href="index.php?v=m&p=chat" data-theme="a"><i class="fa fa-comment-o"></i> Chat</a></li>
                    <?php } ?>
                    <li><a href="index.php?v=m&p=message" data-theme="a"><i class="fa fa-envelope"></i> <span id="span_nbMessage"><?php echo message::nbMessage(); ?></span> Message(s)</a></li>
                    <li><a href="index.php?v=m&p=log" data-theme="a"><i class="fa fa-file-o"></i> Log</a></li>
                    <?php if (isConnect('admin')) { ?>
                        <li><a href="index.php?v=m&p=cron" data-theme="a"><i class="fa fa-tasks"></i> Cron</a></li>
                    <?php } ?>
                    <li><a href="index.php?v=m&logout=1" data-theme="a"><i class="fa fa-sign-out"></i> Se deconnecter</a></li>
                </ul>
            </div>
            <?php if (isset($rightPanel)) { ?>
                <div data-role="panel" id="rightpanel" class='rightpanel' data-position="right" data-display="push" data-dismissible="false" data-animate="false" data-position-fixed="true" data-theme="a" class="ui-icon-alt" >
                    <?php echo $rightPanel; ?>
                </div>
            <?php } ?>
        </div><!-- /page -->
        <?php
        include_file('3rdparty', 'jquery.value/jquery.value', 'js');
        include_file('3rdparty', 'jquery.alert/jquery.alert', 'js');
        include_file('3rdparty', 'jquery.loading/jquery.loading', 'js');
        include_file('mobile', 'utils', 'js');
        ?>
        <script>
            var clientDatetime = new Date();
            var clientServerDiffDatetime = (<?php echo strtotime(date('Y-m-d H:i:s')); ?> * 1000) - clientDatetime.getTime();
        </script>
    </body>
</html>

