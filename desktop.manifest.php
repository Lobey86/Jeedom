<?php
header('Content-type: text/cache-manifest');
require_once dirname(__FILE__) . "/core/php/core.inc.php";
?>
CACHE MANIFEST

# Version <?php echo getVersion('jeedom'); ?>
# <?php echo config::byKey('mobile::manifestKey'); ?>
# 10

CACHE:

NETWORK:
*

FALLBACK:
/ mobile/html/fallback.html
