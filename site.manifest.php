<?php
header('Content-type: ');
require_once dirname(__FILE__) . "/core/php/core.inc.php";
?>
CACHE MANIFEST

# Version <?php echo getVersion('jeedom'); ?>
# <?php echo config::byKey('mobile::manifestKey'); ?>
# 1


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

<?php
foreach (ls('mobile/js', '*.js') as $file) {
    echo 'core/php/getJS.php?file=mobile/js/' . $file . "\n";
}
foreach (ls('mobile/html', '*.html') as $file) {
    echo 'index.php?v=m&p=' . substr($file, 0, -5) . "\n";
}

foreach (plugin::listPlugin(true) as $plugin) {
    if ($plugin->getMobile() != '') {
        foreach (ls('plugins/' . $plugin->getId() . '/mobile/js', '*.js') as $file) {
            echo 'core/php/getJS.php?file=plugins/' . $plugin->getId() . '/mobile/js/' . $file . "\n";
        }
        foreach (ls('plugins/' . $plugin->getId() . '/mobile/html', '*.html') as $file) {
            echo 'index.php?v=m&m=' . $plugin->getId() . '&p=' . substr($file, 0, -5) . "\n";
        }
    }
}
?>

NETWORK:
*

FALLBACK:
/ mobile/html/fallback.html
