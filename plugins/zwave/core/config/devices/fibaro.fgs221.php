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
    'fibaro.fgs221' => array(
        'name' => 'Fibaro FGS-221 Double charge',
        'vendor' => 'Fibar Group',
        'manufacturerId' => 271,
        'manufacturerProductType' => -1,
        'manufacturerProductId' => -1,
        'commands' => array(
            array('name' => 'On 1', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 0, 'value' => 'Set(255)')
            ),
            array('name' => 'Off 1', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 0, 'value' => 'Set(0)')
            ),
            array('name' => 'Etat 1', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 0, 'value' => 'data.level')
            ),
            array('name' => 'On 2 ', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 1, 'value' => 'Set(255)')
            ),
            array('name' => 'Off 2 ', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 1, 'value' => 'Set(0)')
            ),
            array('name' => 'Etat 2', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'instanceId' => 1, 'value' => 'data.level')
            ),
        ),
        'parameters' => array(
            '1' => array(
                'name' => 'Commande ALL ON / ALL OFF',
                'description' => '',
                'default' => '255',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Aucun',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'All ON',
                        'description' => ''
                    ),
                    '2' => array(
                        'name' => 'All OFF',
                        'description' => '',
                    ),
                    '255' => array(
                        'name' => 'ALL ON & ALL OFF activés',
                        'description' => '',
                    ),
                )
            ),
            '3' => array(
                'name' => 'Arrêt automatique de la charge',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactif',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Actif pour la sortie 1',
                        'description' => ''
                    ),
                    '2' => array(
                        'name' => 'Actif pour la sortie 2',
                        'description' => '',
                    ),
                    '3' => array(
                        'name' => 'Activées pour les deux sorties',
                        'description' => '',
                    ),
                )
            ),
            '4' => array(
                'name' => 'Délai de l’arrêt automatique sortie 1',
                'description' => 'Délai avant l’arrêt automatique de la sortie 1. FW1.10- : x 10 ms (1 = 10ms , 255 = 2,55s). FW2.1+ : X0,1s ( 10 = 1s , 65535 = 6553,5s)',
                'default' => '0',
                'type' => 'input',
                'unite' => 's',
                'min' => '1',
                'max' => '65535',
            ),
            '5' => array(
                'name' => 'Délai de l’arrêt automatique sortie 2',
                'description' => 'Délai avant l’arrêt automatique de la sortie 2. FW1.10- : x 10 ms (1 = 10ms , 255 = 2,55s). FW2.1+ : X0,1s ( 10 = 1s , 65535 = 6553,5s)',
                'default' => '0',
                'type' => 'input',
                'unite' => 's',
                'min' => '1',
                'max' => '65535',
            ),
            '6' => array(
                'name' => 'Transmission des commandes locales du bouton 1 au groupe 1',
                'description' => 'Délai avant l’arrêt automatique de la sortie 2. FW1.10- : x 10 ms (1 = 10ms , 255 = 2,55s). FW2.1+ : X0,1s ( 10 = 1s , 65535 = 6553,5s)',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Marche/Arrêt',
                        'description' => 'Transmet les commande Marche et Arrêt',
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => 'Ne transmet que la commande Arrêt. Un double appui transmet la commande Marche (dans le cas d’un variateur, celui-ci s’allume à 100%). Nécessite l’activation du paramètre 15'
                    ),
                )
            ),
            '7' => array(
                'name' => 'Transmission des commandes locales du bouton 2 au groupe 2',
                'description' => 'Délai avant l’arrêt automatique de la sortie 2. FW1.10- : x 10 ms (1 = 10ms , 255 = 2,55s). FW2.1+ : X0,1s ( 10 = 1s , 65535 = 6553,5s)',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Marche/Arrêt',
                        'description' => 'Transmet les commande Marche et Arrêt',
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => 'Ne transmet que la commande Arrêt. Un double appui transmet la commande Marche (dans le cas d’un variateur, celui-ci s’allume à 100%). Nécessite l’activation du paramètre 15'
                    ),
                )
            ),
            '13' => array(
                'name' => 'Comportement avec commutateur bi-stable',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Mono-stable',
                        'description' => 'Inversion d’état à chaque changement de position du bouton.',
                    ),
                    '1' => array(
                        'name' => 'Bi-stable',
                        'description' => 'Bouton sur marche --> relais actif. Bouton sur arrêt --> relais inactif.'
                    ),
                )
            ),
            '14' => array(
                'name' => 'Compatibilité commutateurs bi-stables',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Mono-stable',
                        'description' => 'Bouton-poussoir impusionnel (mono-stable)',
                    ),
                    '1' => array(
                        'name' => 'Bi-stable',
                        'description' => 'Commutateur Marche / Arrêt (bi-stable)'
                    ),
                )
            ),
            '15' => array(
                'name' => 'Compatibilité variateurs et volets roulants',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactive',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Active',
                        'description' => ''
                    ),
                )
            ),
            '16' => array(
                'name' => 'Mémorisation de l’état',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Reste éteint',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Reprend',
                        'description' => ''
                    ),
                )
            ),
            '30' => array(
                'name' => 'Alarme Générale sortie 1',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '31' => array(
                'name' => 'Alarme Innondation sortie 1',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '32' => array(
                'name' => 'Alarme Fumée, CO, CO2 sortie 1',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '33' => array(
                'name' => 'Alarme Température sortie 1',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '41' => array(
                'name' => 'Alarme Innondation sortie 2',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '42' => array(
                'name' => 'Alarme Fumée, CO, CO2 sortie 2',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '43' => array(
                'name' => 'Alarme Température sortie 2',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas de réponse',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Marche',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Arrêt',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => 'Clignotement',
                        'description' => 'Clignotement pendant 10 minutes max.'
                    ),
                )
            ),
            '43' => array(
                'name' => 'Durée de l’alarme',
                'description' => 'Durée de l’activation en cas d’alarme (en ms)',
                'default' => '600',
                'type' => 'input',
                'unite' => 'ms',
                'min' => '1',
                'max' => '65535'
            ),
        )
    ),
);
?>
