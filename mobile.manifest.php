<?php
header('Content-type: text/cache-manifest');
require_once dirname(__FILE__) . "/core/php/core.inc.php";

$js_file = array(
    '3rdparty/jquery.include/jquery.include.js',
    '3rdparty/highstock/highcharts-more.js',
    '3rdparty/highstock/highstock.js',
    '3rdparty/jquery/jquery.min.js',
    '3rdparty/php.js/php.min.js',
    '3rdparty/jquery.mobile/jquery.mobile.min.js',
    '3rdparty/jquery.value/jquery.value.js',
    '3rdparty/jquery.alert/jquery.alert.js',
    '3rdparty/jquery.loading/jquery.loading.js',
    'core/js/cmd.class.js',
    'core/js/core.js',
    'core/js/eqLogic.class.js',
    'core/js/jeedom.class.js',
    'core/js/object.class.js',
    'core/js/plugin.class.js',
    'core/js/view.class.js',
    'core/js/message.class.js',
    'core/js/scenario.class.js',
    '3rdparty/jquery.masonry/jquery.masonry.js',
    '3rdparty/jquery.farbtastic/farbtastic.js',
);

$other_file = array(
    'mobile/css/farbtastic.css',
    'mobile/css/marker.png',
    'mobile/css/mask.png',
    'mobile/css/wheel.png',
    '3rdparty/jquery.mobile/jquery.mobile.css',
    '3rdparty/font-awesome/css/font-awesome.min.css',
    'core/css/core.css',
    '3rdparty/jquery.loading/jquery.loading.css',
    'mobile/css/commun.css',
    '3rdparty/font-awesome/fonts/fontawesome-webfont.woff?v=4.0.3',
    '3rdparty/jquery.mobile/images/ajax-loader.gif',
);
?>
CACHE MANIFEST

CACHE:
<?php
foreach ($js_file as $file) {
    echo "\n";
    if (file_exists(dirname(__FILE__) . '/' . $file)) {
        echo '#' . md5_file(dirname(__FILE__) . '/' . $file);
        echo "\n";
    }
    echo 'core/php/getJS.php?file=' . $file;
    echo "\n";
}
foreach ($other_file as $file) {
    echo "\n";
    if (file_exists(dirname(__FILE__) . '/' . $file)) {
        echo '#' . md5_file(dirname(__FILE__) . '/' . $file);
        echo "\n";
    }
    echo $file;
    echo "\n";
}
foreach (ls('mobile/js', '*.js') as $file) {
    echo "\n";
    if (file_exists(dirname(__FILE__) . '/mobile/js/' . $file)) {
        echo '#' . md5_file(dirname(__FILE__) . '/mobile/js/' . $file);
        echo "\n";
    }
    echo 'core/php/getJS.php?file=mobile/js/' . $file;
    echo "\n";
}
foreach (ls('mobile/html', '*.html') as $file) {
    echo "\n";
    if (file_exists(dirname(__FILE__) . '/mobile/html/' . $file)) {
        echo '#' . md5_file(dirname(__FILE__) . '/mobile/html/' . $file);
        echo "\n";
    }
    echo 'index.php?v=m&p=' . substr($file, 0, -5);
    echo "\n";
}

foreach (plugin::listPlugin(true) as $plugin) {
    if ($plugin->getMobile() != '') {
        foreach (ls('plugins/' . $plugin->getId() . '/mobile/js', '*.js') as $file) {
            echo "\n";
            if (file_exists(dirname(__FILE__) . '/plugins/' . $plugin->getId() . '/mobile/js/' . $file)) {
                echo '#' . md5_file(dirname(__FILE__) . '/plugins/' . $plugin->getId() . '/mobile/js/' . $file);
                echo "\n";
            }
            echo 'core/php/getJS.php?file=plugins/' . $plugin->getId() . '/mobile/js/' . $file . "\n";
        }
        foreach (ls('plugins/' . $plugin->getId() . '/mobile/html', '*.html') as $file) {
            echo "\n";
            if (file_exists(dirname(__FILE__) . '/plugins/' . $plugin->getId() . '/mobile/html/' . $file)) {
                echo '#' . md5_file(dirname(__FILE__) . '/plugins/' . $plugin->getId() . '/mobile/html/' . $file);
                echo "\n";
            }
            echo 'index.php?v=m&m=' . $plugin->getId() . '&p=' . substr($file, 0, -5) . "\n";
        }
    }
}
?>

NETWORK:
*

FALLBACK:
/ mobile/html/fallback.html