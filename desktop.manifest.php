<?php
header('Content-type: text/cache-manifest');
require_once dirname(__FILE__) . "/core/php/core.inc.php";
?>
CACHE MANIFEST

# Version <?php echo getVersion('jeedom'); ?>
# <?php echo config::byKey('manifestKey'); ?>
# 10
<?php if (!defined('DESKTOP_CACHE') || DESKTOP_CACHE == 1) { ?>
    CACHE:
    core/php/getJS.php?file=3rdparty/jquery.include/jquery.include.js
    core/php/getJS.php?file=3rdparty/highstock/highcharts-more.js
    core/php/getJS.php?file=3rdparty/highstock/highstock.js
    core/php/getJS.php?file=3rdparty/jquery/jquery.min.js
    core/php/getJS.php?file=3rdparty/php.js/php.min.js
    core/php/getJS.php?file=3rdparty/jquery.mobile/jquery.mobile.min.js
    core/php/getJS.php?file=3rdparty/jquery.value/jquery.value.js
    core/php/getJS.php?file=3rdparty/jquery.alert/jquery.alert.js
    core/php/getJS.php?file=3rdparty/jquery.loading/jquery.loading.js
    core/php/getJS.php?file=core/js/cmd.class.js
    core/php/getJS.php?file=core/js/core.js
    core/php/getJS.php?file=core/js/eqLogic.class.js
    core/php/getJS.php?file=core/js/jeedom.class.js
    core/php/getJS.php?file=core/js/object.class.js
    core/php/getJS.php?file=core/js/plugin.class.js
    core/php/getJS.php?file=core/js/view.class.js
    core/php/getJS.php?file=core/js/message.class.js
    core/php/getJS.php?file=core/js/scenario.class.js
    core/php/getJS.php?file=3rdparty/jquery.masonry/jquery.masonry.js
    core/php/getJS.php?file=3rdparty/jquery.farbtastic/farbtastic.js
    mobile/css/farbtastic.css
    mobile/css/marker.png
    mobile/css/mask.png
    mobile/css/wheel.png
    3rdparty/jquery.mobile/jquery.mobile.css
    3rdparty/font-awesome/css/font-awesome.min.css
    core/css/core.css
    3rdparty/jquery.loading/jquery.loading.css
    mobile/css/commun.css
    3rdparty/font-awesome/fonts/fontawesome-webfont.woff?v=4.0.3
    3rdparty/jquery.mobile/images/ajax-loader.gif
    3rdparty/jquery.gritter/jquery.gritter.css
    3rdparty/jquery.ui/jquery-ui-bootstrap/jquery-ui.css
    3rdparty/jquery.tablesorter/theme.bootstrap.css
    desktop/css/commun.css
    core/css/core.css
    core/php/getJS.php?file=3rdparty/bootstrap/bootstrap.min.js
    core/php/getJS.php?file=3rdparty/jquery.ui/jquery-ui.min.js
    core/php/getJS.php?file=3rdparty/jquery.ui/jquery.ui.datepicker.fr.js
    core/php/getJS.php?file=3rdparty/jquery.ui/bootbox/bootbox.min.js
    core/php/getJS.php?file=3rdparty/jquery.tablesorter/jquery.tablesorter.min.js
    core/php/getJS.php?file=3rdparty/jquery.tablesorter/jquery.tablesorter.widgets.min.js
    core/php/getJS.php?file=3rdparty/jquery.tableUtils/jquery.tableUtils.js
    core/php/getJS.php?file=3rdparty/jquery.gritter/jquery.gritter.min.js
    core/php/getJS.php?file=desktop/js/utils.js
    core/php/getJS.php?file=core/js/js.inc.js
    core/php/getJS.php?file=3rdparty/jquery.farbtastic/farbtastic.js
    core/php/getJS.php?file=core/js/js.inc.js
    3rdparty/jquery.farbtastic/farbtastic.css
    core/php/getJS.php?file=3rdparty/jquery.tree/jquery.tree.js
    3rdparty/jquery.tree/themes/default/style.min.css

    core/php/getJS.php?file=3rdparty/codemirror/lib/codemirror.js
    core/php/getJS.php?file=3rdparty/codemirror/addon/edit/matchbrackets.js
    core/php/getJS.php?file=3rdparty/codemirror/mode/htmlmixed/htmlmixed.js
    core/php/getJS.php?file=3rdparty/codemirror/mode/clike/clike.js
    core/php/getJS.php?file=3rdparty/codemirror/mode/php/php.js
    3rdparty/codemirror/lib/codemirror.css
    core/php/getJS.php?file=3rdparty/jquery.sew/jquery.caretposition.js
    core/php/getJS.php?file=3rdparty/jquery.sew/jquery.sew.min.js
    3rdparty/jquery.sew/jquery.sew.css

    <?php
    foreach (ls('desktop/js', '*.js') as $file) {
        echo 'core/php/getJS.php?file=desktop/js/' . $file;
        echo "\n";
    }

    foreach (plugin::listPlugin(true) as $plugin) {
        foreach (ls('plugins/' . $plugin->getId() . '/dekstop/js', '*.js') as $file) {
            echo 'core/php/getJS.php?file=plugins/' . $plugin->getId() . '/desktop/js/' . $file . "\n";
        }
    }
} else {
    ?>
    CACHE: 
<?php } ?>


NETWORK:
*

FALLBACK:
/ desktop/html/fallback.html
