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
        ),
    ),
    'cmd' => array(
        'type' => array(
            'info' => array(
                'name' => 'Info',
                'subtype' => array(
                    'numeric' => array('name' => 'Numérique'),
                    'binary' => array('name' => 'Binaire'),
                    'string' => array('name' => 'Autre'),
                )
            ),
            'action' => array(
                'name' => 'Action',
                'subtype' => array(
                    'other' => array('name' => 'Défaut'),
                    'slider' => array('name' => 'Slider'),
                    'message' => array('name' => 'Message'),
                    'color' => array('name' => 'Couleur'),
                ),
            ),
        ),
    ),
);
?>