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
    'ezmotion.3in1' => array(
        'name' => 'EZ Motion 3 in 1',
        'vendor' => 'EZ motion',
        'manufacturerId' => 30,
        'manufacturerProductType' => 2,
        'manufacturerProductId' => 1,
        'commands' => array(
            array('name' => 'Luminosité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1,
                'configuration' => array('class' => '0x31', 'instanceId' => 2, 'value' => 'data[3].val')
            ),
            array('name' => 'Température', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1,
                'configuration' => array('class' => '0x31', 'instanceId' => 3, 'value' => 'data[1].val')
            ),
        ),
        'configuration' => array('refreshDelay' => 10),
        'parameters' => array(
            '1' => array(
                'name' => 'Sensibilité',
                'description' => 'Sensitivity sets the amount of motion required for EZMotion to detect motion. A higher value makes it more sensitive and a lower value makes it less sensitive. Note that values above 200 are not recommended when EZMotion is battery operated.Recommended values: 10 = Pet Immune, 100 = Medium sensitivity for hallways, 200 = Highly sensitive for rooms where people are sitting still.',
                'default' => '200',
                'type' => 'input',
                'min' => '0',
                'max' => '255',
            ),
            '2' => array(
                'name' => 'On Time',
                'description' => 'On Time sets the number of minutes that the lights stay on when motion has not been detected. A value of 0 On Time is a special mode where the lights are constantly sent a command to turn them on whenever motion is detected. EZMotion will NOT turn the lights off in this mode. Note that this mode will significantly shorten battery life. Recommended values: 5 min for hallways 20 min for an office environment 60 min for a library or other room where someone may be sitting still for a long time.',
                'default' => '20',
                'type' => 'input',
                'min' => '0',
                'max' => '255',
                'unite' => 'min',
            ),
            '3' => array(
                'name' => 'LED ON/OFF ',
                'description' => 'LED ON/OFF turns the LED on or off. A slight improvement in battery life is obtained by turning the LED off. Setting LED ON/OFF to zero will turn the LED off and 255 turnsit on.',
                'default' => 'On',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Off',
                    ),
                    '255' => array(
                        'name' => 'On',
                    ),
                )
            ),
            '4' => array(
                'name' => 'Light Threshold',
                'description' => 'Light Threshold is the percentage of light in the room above which the lights will not be turned on. Light Threshold is often used in room with a lot of natural daylight. Setting Light Threshold to a value of 50% will cause EZMotion to not turn the lights on when the natural light in the room is already at the 50% value. This feature only prevents the lights from coming on when motion is first detected and the light level in the room is already above Light Threshold. It will not turn the lights off when the amount of natural light in the room increases. It will automatically turn on the lights in a room that has motion in it and that the amount of natural light has dropped be low Light Threshold. A value of 100% turns off this feature. Recommended values: Usually a value between 40% and 60% will prevent th e lights from coming on in a reasonably well light room and will turn them on as it is getting dark. Some experimentation is required with each room to determine the proper setting.',
                'default' => '20',
                'type' => 'input',
                'min' => '0',
                'max' => '100',
                'unite' => '%',
            ),
            '5' => array(
                'name' => 'Stay Awake',
                'description' => 'Setting Stay Awake to a non-zero value will cause EZMotion to always be awake. NOTE: this mode should NOT be used when EZMotion is battery powered! Batteries will only la st a few days in this mode. Stay Awake is NOT set to the factory default (0) when EZMotion is Excluded (reset) from the Z-Wave network. Setting Stay Awake to a non-zero value will cause the Z-Wave Listening Bit to be set. EZMotion will become a routing node in the Z-Wave Mesh-Network when the Listening Bit is set. To properly have EZMotion included in the routing tables, set Stay Awake to a non-zero value, then reset EZMotion (Exclude from the network), then add it back to the network.The new routing information will be used now that the liste ning bit is set.',
                'default' => '0',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '255',
            ),
            '6' => array(
                'name' => 'On Value',
                'description' => 'On Value is the value sent by the Z-Wave BASIC_SET command when motion is detected. A value of 0 will turn the lights off (not recommended). A value between 1 and 100 will set the dim level to between 1% and 100%. A value of 255 will turn the light on',
                'default' => '255',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '100,255',
            ),
            '7' => array(
                'name' => 'TempAdj',
                'description' => 'TempAdj is a twos-complement number that is used to adjust the temperature reading to make it more accurate. The value programmed is in tenths of degree Fahrenheit. The temperature reading can be adjusted up to +12.7F to -12.8F. A value of 1 will adjust the temperature reading by +0.1F. A value of -1 will adjust the temperature by -0.1F. A value of 123 will adjust the temperature by +12.3F. TempAdj is NOT changed when Excluded (reset) from t he Z- Wave network.',
                'default' => 'Factory Cali-brated',
                'type' => 'input',
                'unite' => '',
                'min' => '-127',
                'max' => '+128',
            ),
        )
    ),
);
?>
