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

require_once dirname(__FILE__) . '/utils.inc.php';
include_file('core', 'common', 'config');
include_file('core', 'version', 'config');
include_file('core', 'module', 'class');
include_file('core', 'config', 'class');
include_file('core', 'DB', 'class');

function jeedomCoreAutoload($classname) {
    try {
        include_file('core', $classname, 'class');
    } catch (Exception $e) {
        
    }
}

function jeedomComAutoload($classname) {
    try {
        include_file('core', substr($classname, 4), 'com');
    } catch (Exception $e) {
        
    }
}

function jeedomModuleAutoload($classname) {
    try {
        try {
            $module = new module($classname);
        } catch (Exception $e) {
            if (!is_object($module) || $module->getId() == '') {
                if (strpos($classname, 'Real') !== false) {
                    error_log(substr($classname, 0, -4));
                    $module = new module(substr($classname, 0, -4));
                }
                if (strpos($classname, 'Cmd') !== false) {
                    $module = new module(substr($classname, 0, -3));
                }
            }
        }
        if (is_object($module) && $module->getId() != '') {
            if ($module->isActive() == 1) {
                $include = $module->getInclude();
                include_file('core', $include['file'], $include['type'], $module->getId());
            }
        }
    } catch (Exception $e) {
        
    }
}

function jeedom3rdPartyAutoload($classname) {
    try {
        if ($classname == 'Cron\CronExpression') {
            include_file('3rdparty', 'cron-expression/cron.inc', 'php');
        }
    } catch (Exception $e) {
        
    }
}

spl_autoload_register('jeedomCoreAutoload', true, true);
spl_autoload_register('jeedomComAutoload', true, true);
spl_autoload_register('jeedomModuleAutoload', true, true);
spl_autoload_register('jeedom3rdPartyAutoload', true, true);
?>
