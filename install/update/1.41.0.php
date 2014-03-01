<?php

$cron = new cron();
$cron->setClass('jeedom');
$cron->setFunction('persist');
$cron->setSchedule('* * * * * *');
$cron->save();
