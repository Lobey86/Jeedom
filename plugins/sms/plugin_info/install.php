<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
if (!isConnect()) {
    die();
}

function install() {
    $cron = new cron();
    $cron->setClass('sms');
    $cron->setFunction('pull');
    $cron->setEnable(1);
    $cron->setDeamon(1);
    $cron->setDeamonSleepTime(30);
    $cron->setSchedule('* * * * *');
    $cron->save();
    config::save('smsDeamonCronId', $cron->getId(), 'sms');
}

function remove() {
    $cron = cron::byId(config::byKey('smsDeamonCronId', 'sms'));
    if (is_object($cron)) {
        $cron->remove();
    }
    config::remove('smsDeamonCronId', 'sms');
}

?>
