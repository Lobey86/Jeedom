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
    'everspring.sm103' => array(
        'name' => 'Everspring SM103',
        'vendor' => 'Everspring',
        'manufacturerId' => 96,
        'manufacturerProductType' => 13,
        'manufacturerProductId' => 1,
        'commands' => array(
            array('name' => 'Etat', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1, 'isHistorized' => 1, 'eventOnly' => 1,
                'configuration' => array('class' => '0x30', 'value' => 'data[1].level')
            ),
        ),
        'parameters' => array(
            '1' => array(
                'name' => 'Configuring the Phase Level of ON Command',
                'description' => 'The Configuration parameter that can be used to adjust the phase level of ON command is transmitted. This parameter can be configured with the value of 0 through 127. Value 0: Set Device OFF(0x00) Value 1-99: Set Device On (1-99) Value 100-127: Set Device On to the last phase (0xFF) Note: 0xFF means the device will be on to the last phase that the device was turned off.',
                'default' => '1',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '127',
            ),
            '2' => array(
                'name' => 'Configuring the OFF Delay',
                'description' => 'The Configuration parameter that can be used to adjust the amount of delay before the OFF command is transmitted. This parameter can be configured with the value of 1 through 127, where 1 means 1 second delay and 127 means 127 seconds of delay.',
                'default' => '1',
                'type' => 'input',
                'unite' => '',
                'min' => '1',
                'max' => '127',
            ),
            '3' => array(
                'name' => 'Enabling/Disabling Power Saving Function',
                'description' => 'When the magnet is parted or closed from the Detector for 10 seconds, the SM103 will enter the power saving mode. This parameter can be configured with the value of 0 through 127, where 0 means power saving being enabled and others mean power saving being disabled.',
                'default' => '127',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '127',
            ),
        ),
    ),
);

