<?php

require_once dirname(__FILE__) . '/../../core/php/core.inc.php';

$sql = 'SELECT * 
        FROM config 
        WHERE `key` LIKE "%::installVersionDate%"';
$values = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
foreach ($values as $value) {
    $update = new update();
    $name = explode('::', $value['key']);
    $update->setLogical_id($name[0]);
    $update->setLocalVersion($value['value']);
    $update->setName($name[0]);
    if ($value['plugin'] == 'core') {
        $update->setType('plugin');
    } else {
        $update->setType($value['plugin']);
    }
    $update->save();
}

$sql = 'SELECT * 
        FROM config 
        WHERE `key` = "installVersionDate"';
$values = DB::Prepare($sql, array(), DB::FETCH_TYPE_ALL);
foreach ($values as $value) {
    $update = new update();
    if ($value['plugin'] != '') {
        $update->setLogical_id($value['plugin']);
        $update->setLocalVersion($value['value']);
        $update->setName($value['plugin']);
        $update->setType('plugin');
        $update->save();
    }
}
