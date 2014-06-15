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
            'heating' => array('name' => 'Chauffage', 'color' => '#2980b9', 'mcolor' => '#2980b9','cmdColor' => '#3498db','mcmdColor' => '#3498db'),
            'security' => array('name' => 'Sécurité', 'color' => '#8e44ad', 'mcolor' => '#8e44ad','cmdColor' => '#9b59b6','mcmdColor' => '#9b59b6'),
            'energy' => array('name' => 'Energie', 'color' => '#27ae60', 'mcolor' => '#27ae60','cmdColor' => '#2ecc71','mcmdColor' => '#2ecc71'),
            'light' => array('name' => 'Lumière', 'color' => '#f39c12', 'mcolor' => '#f39c12','cmdColor' => '#f1c40f','mcmdColor' => '#f1c40f'),
            'automatism' => array('name' => 'Automatisme', 'color' => '#808080', 'mcolor' => '#808080','cmdColor' => '#c2beb8','mcmdColor' => '#c2beb8'),
        ),
    ),
    'plugin' => array(
        'category' => array(
            'security' => array('name' => 'Sécurité', 'icon' => 'fa-lock'),
            'automation protocol' => array('name' => 'Protocole domotique', 'icon' => 'fa-rss'),
            'programming' => array('name' => 'Programmation', 'icon' => 'fa-code'),
            'Panel' => array('name' => 'Panel', 'icon' => 'fa-thumb-tack'),
            'organization' => array('name' => 'Organisation', 'icon' => 'fa-calendar'),
            'weather' => array('name' => 'Météo', 'icon' => 'fa-sun-o'),
            'communication' => array('name' => 'Communication', 'icon' => 'fa-comment-o'),
            'multimedia' => array('name' => 'Multimedia', 'icon' => 'fa-comment-o'),
            'wellness' => array('name' => 'Bien-être', 'icon' => 'fa-user'),
            'jeedomBox' => array('name' => 'Jeedom Box', 'icon' => 'fa-dropbox'),            
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