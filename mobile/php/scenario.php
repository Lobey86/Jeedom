<?php

if (!isConnect()) {
    include_file('mobile', '401', 'php');
    die();
}

foreach (scenario::all() as $scenario) {
    echo $scenario->toHtml('mobile');
}
?>
