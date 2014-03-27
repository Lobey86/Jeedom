<?php

if (!isConnect()) {
    include_file('mobile', '401', 'php');
    die();
}

foreach (scenario::all() as $scenario) {
    if ($scenario->getIsVisible() == 1) {
        echo $scenario->toHtml('mobile');
    }
}
?>
