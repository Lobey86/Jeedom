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

global $listClassCommand;
$listClassCommand = array(
    '0x25' => array(
        'name' => 'COMMAND_CLASS_SWITCH_BINARY',
        'description' => '',
        'commands' => array(
            array('name' => 'On', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'value' => 'Set(255)')
            ),
            array('name' => 'Off', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'value' => 'Set(0)')
            ),
             array('name' => 'Etat', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'value' => 'data.level')
            ),
        )
    ),
    '0x26' => array(
        'name' => 'COMMAND_CLASS_SWITCH_MULTILEVEL',
        'description' => '',
        'commands' => array(
            array('name' => 'On', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'Set(99)')
            ),
            array('name' => 'Off', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'Set(0)')
            ),
            array('name' => 'Intensité', 'type' => 'action', 'subtype' => 'slider', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'Set(#slider#)')
            ),
            array('name' => 'Etat', 'type' => 'info', 'subtype' => 'numeric', 'unite' => '%', 'isVisible' => 0, 'eventOnly' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'data.level')
            ),
            array('name' => 'Couleur', 'type' => 'action', 'subtype' => 'color', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => '#color#')
            ),
        )
    ),
    '0x30' => array(
        'name' => 'COMMAND_CLASS_SENSOR_BINARY',
        'description' => '',
        'commands' => array(
            array('name' => 'Présence', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1, 'isHistorized' => 1, 'eventOnly' => 1,
                'configuration' => array('class' => '0x30', 'value' => 'data[1].level')
            ),
        )
    ),
    '0x31' => array(
        'name' => 'COMMAND_CLASS_SENSOR_MULTILEVEL',
        'description' => '',
        'commands' => array(
            array('name' => 'Température', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '°C', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[1].val')
            ),
            array('name' => 'Luminosité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => 'Lux', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[3].val', 'maxValue' => 1000, 'minValue' => 0)
            ),
            array('name' => 'Puissance', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => 'W', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[4].val', 'maxValue' => 2500, 'minValue' => 0)
            ),
            array('name' => 'Humidité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '%', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[5].val')
            ),
        )
    ),
    '0x32' => array(
        'name' => 'COMMAND_CLASS_METER',
        'description' => '',
        'commands' => array(
            array('name' => 'Consommation', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0, 'unite' => 'kWh', 'eventOnly' => 1,
                'configuration' => array('class' => '0x32', 'value' => 'data[0].val')
            ),
            array('name' => 'Reset', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1, 'isHistorized' => 0, 'unite' => '',
                'configuration' => array('class' => '0x32', 'value' => 'Reset()')
            ),
        )
    ),
    '0x80' => array(
        'name' => 'COMMAND_CLASS_BATTERY',
        'description' => '',
        'commands' => array(
            array('name' => 'Batterie', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 0, 'isHistorized' => 0, 'unite' => '%',
                'configuration' => array('class' => '0x80', 'value' => 'data.last')
            ),
        )
    ),
);

