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

global $listCmdXBMC;
$listCmdXBMC = array(
    array(
        'name' => 'Quitter',
        'configuration' => array(
            'request' => 'Application.Quit',
            'parameters' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Ferme XBMC',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Volume',
        'configuration' => array(
            'request' => 'Application.SetVolume',
            'parameters' => '{"volume" : #slider#}',
        ),
        'type' => 'action',
        'subType' => 'slider',
        'description' => 'Change le volume',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Muet',
        'configuration' => array(
            'request' => 'Application.SetMute',
            'parameters' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Met en muet',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Play',
        'configuration' => array(
            'request' => 'Player.PlayPause',
            'parameters' => '',
        ),
        'type' => 'action',
        'subType' => 'slider',
        'description' => 'Met en pause ou lecture',
        'version' => '0.1',
        'required' => '',
    ),
    array(
        'name' => 'Stop',
        'configuration' => array(
            'request' => 'Player.Stop',
            'parameters' => '',
        ),
        'type' => 'action',
        'subType' => 'other',
        'description' => 'Stop la lecture',
        'version' => '0.1',
        'required' => '',
    ),
);
?>
