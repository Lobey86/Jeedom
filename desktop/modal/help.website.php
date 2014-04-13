<?php

if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$url = 'http://jeedom.fr/' . init('page');



$ch = curl_init();
curl_setopt_array($ch, array
    (
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HEADER => FALSE,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_MAXREDIRS => 10,
));
$response = curl_exec($ch);
curl_close($request);

if (curl_errno($ch) || strpos($response,'404 Not Found') !== false) {
    echo '<div class="alert alert-danger" id="primary">{{Aucune aide n\'éxiste pour le moment sur cette page}}</div>';
} else {
    echo str_replace('<img src="', '<img src="http://jeedom.fr/', $response);
}
?>


