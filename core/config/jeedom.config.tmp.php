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

global $JEEDOM_INTERNAL_CONFIG;
$JEEDOM_INTERNAL_CONFIG = array(
    'eqLogic' => array(
        'category' => array(
            'heating' => array('name' => 'Chauffage', 'color' => '#F8E6E0'),
            'security' => array('name' => 'Sécurité', 'color' => '#CEE3F6'),
            'energy' => array('name' => 'Energie', 'color' => '#CEF6CE'),
            'light' => array('name' => 'Lumière', 'color' => '#F7F8E0'),
            'automatism' => array('name' => 'Automatisme', 'color' => '#F781D8'),
        ),
    ),
    'cmd' => array(
        'type' => array(
            'info' => array(
                'name' => 'Info',
                'subtype' => array(
                    'numeric' => array('name' => 'Numérique',
                        'configuration' => array(
                            'minValue' => array('visible' => true),
                            'maxValue' => array('visible' => true)),
                        'unite' => array('visible' => true),
                        'eventOnly' => array('visible' => true),
                        'isHistorized' => array('visible' => true),
                        'cache' => array(
                            'lifetime' => array('visible' => true),
                            'enable' => array('visible' => true)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                    'binary' => array('name' => 'Binaire',
                        'configuration' => array(
                            'minValue' => array('visible' => false),
                            'maxValue' => array('visible' => false)),
                        'unite' => array('visible' => false),
                        'eventOnly' => array('visible' => true),
                        'isHistorized' => array('visible' => true),
                        'cache' => array(
                            'lifetime' => array('visible' => true),
                            'enable' => array('visible' => true)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => true),
                        ),
                    ),
                    'string' => array('name' => 'Autre',
                        'configuration' => array(
                            'minValue' => array('visible' => false),
                            'maxValue' => array('visible' => false)),
                        'unite' => array('visible' => true),
                        'eventOnly' => array('visible' => true),
                        'isHistorized' => array('visible' => false),
                        'cache' => array(
                            'lifetime' => array('visible' => true),
                            'enable' => array('visible' => true)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                )
            ),
            'action' => array(
                'name' => 'Action',
                'subtype' => array(
                    'other' => array('name' => 'Défaut',
                        'configuration' => array(
                            'minValue' => array('visible' => false),
                            'maxValue' => array('visible' => false)),
                        'unite' => array('visible' => false),
                        'eventOnly' => array('visible' => false),
                        'isHistorized' => array('visible' => false),
                        'cache' => array(
                            'lifetime' => array('visible' => false),
                            'enable' => array('visible' => false)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                    'slider' => array('name' => 'Slider',
                        'configuration' => array(
                            'minValue' => array('visible' => true),
                            'maxValue' => array('visible' => true)),
                        'unite' => array('visible' => false),
                        'eventOnly' => array('visible' => false),
                        'isHistorized' => array('visible' => false),
                        'cache' => array(
                            'lifetime' => array('visible' => false),
                            'enable' => array('visible' => false)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                    'message' => array('name' => 'Message',
                        'configuration' => array(
                            'minValue' => array('visible' => false),
                            'maxValue' => array('visible' => false)),
                        'unite' => array('visible' => false),
                        'eventOnly' => array('visible' => false),
                        'isHistorized' => array('visible' => false),
                        'cache' => array(
                            'lifetime' => array('visible' => false),
                            'enable' => array('visible' => false)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                    'color' => array('name' => 'Couleur',
                        'configuration' => array(
                            'minValue' => array('visible' => false),
                            'maxValue' => array('visible' => false)),
                        'unite' => array('visible' => false),
                        'eventOnly' => array('visible' => false),
                        'isHistorized' => array('visible' => false),
                        'cache' => array(
                            'lifetime' => array('visible' => false),
                            'enable' => array('visible' => false)
                        ),
                        'display' => array(
                            'invertBinary' => array('visible' => false),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
?>