<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

foreach (scenario::all() as $scenario) {
    if ($scenario->getIsVisible() == 1) {
        echo $scenario->toHtml('mobile');
    }
}
?>
