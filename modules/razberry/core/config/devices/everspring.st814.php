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

global $deviceConfiguration;

$deviceConfiguration = array(
    'everspring.st814' => array(
        'name' => 'Everspring ST814',
        'vendor' => 'Everspring',
        'manufacturerId' => 96,
        'manufacturerProductType' => 6,
        'manufacturerProductId' => 1,
        'commands' => array(
            array('name' => 'Température', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '°C', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[1].val')
            ),
            array('name' => 'Humidité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '%', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[5].val')
            ),
        ),
        'parameters' => array(
            '1' => array(
                'name' => 'Basic Level set',
                'description' => 'Set basic set value to be on or off',
                'default' => '99',
                'type' => 'input',
                'min' => '0',
                'max' => '99',
            ),
            '2' => array(
                'name' => 'Temperature trigger ON',
                'description' => 'Temperature level when a ON command is sent out',
                'default' => '99',
                'type' => 'input',
                'min' => '0',
                'max' => '99',
                'unite' => '°C',
            ),
            '3' => array(
                'name' => 'Temperature trigger OFF',
                'description' => 'Temperature level when a OFF command is sent out',
                'default' => '99',
                'type' => 'input',
                'min' => '0',
                'max' => '99',
                'unite' => '°C',
            ),
            '4' => array(
                'name' => 'Humidity trigger ON',
                'description' => 'Humidity level when a ON command is sent out',
                'default' => '99',
                'type' => 'input',
                'min' => '0',
                'max' => '99',
                'unite' => '%',
            ),
            '5' => array(
                'name' => 'Humidity trigger OFF',
                'description' => 'Temperture level when a OFF command is sent out',
                'default' => '99',
                'type' => 'input',
                'min' => '0',
                'max' => '99',
                'unite' => '%',
            ),
            '6' => array(
                'name' => 'Auto report time',
                'description' => 'Sets the time interval when sensor report is sent',
                'default' => '0',
                'type' => 'input',
                'min' => '0',
                'max' => '1439',
                'unite' => 'min'
            ),
            '7' => array(
                'name' => 'Auto report Temperature',
                'description' => 'Sets the temperature change causing a sensor report',
                'default' => '0',
                'type' => 'input',
                'min' => '0',
                'max' => '70',
                'unite' => '°C',
            ),
            '8' => array(
                'name' => 'Auto report Humidity',
                'description' => 'Sets the humidity change causing a sensor report',
                'default' => '0',
                'type' => 'input',
                'min' => '0',
                'max' => '70',
                'unite' => '%',
            ),
        )
    ),
);
?>
