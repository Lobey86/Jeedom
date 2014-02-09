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
    'fibaro.fgk101' => array(
        'name' => 'Fibaro FGK-101',
        'vendor' => 'Fibar Group',
        'manufacturerId' => 271,
        'manufacturerProductType' => 1792,
        'manufacturerProductId' => 4096,
        'commands' => array(
            array('name' => 'Etat', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1, 'isHistorized' => 1, 'eventOnly' => 1,
                'configuration' => array('class' => '0x30', 'value' => 'data[1].level')
            ),
        ),
        'parameters' => array(
            '1' => array(
                'name' => 'Délai d’annulation d’alarme de l’entrée IN',
                'description' => 'Nombre de secondes avant l’annulation de l’alarme après que le capteur de l’entrée 1 soit repassé à son état normal.',
                'default' => '0',
                'type' => 'input',
                'unite' => 's',
                'min' => '0',
                'max' => '65535',
            ),
            '2' => array(
                'name' => 'Activation de la LED',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'LED inactive.',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'LED active lors des changement de statu.'
                    ),
                )
            ),
            '3' => array(
                'name' => 'Type d’entrée (entrée IN)',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'INPUT_NC',
                        'description' => 'Entrée normalement fermée',
                    ),
                    '1' => array(
                        'name' => 'INPUT_NO',
                        'description' => 'Entrée normalement ouverte'
                    ),
                    '2' => array(
                        'name' => 'INPUT_MONOSTABLE',
                        'description' => 'Entrée monostable (bouton poussoir)'
                    ),
                    '3' => array(
                        'name' => 'INPUT_BISTABLE',
                        'description' => 'Entrée bistable (interrupteur)'
                    ),
                )
            ),
            '5' => array(
                'name' => 'Type d’information transmise',
                'description' => '',
                'default' => '255',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'ALARM GENERIC',
                    ),
                    '1' => array(
                        'name' => 'ALARM SMOKE',
                    ),
                    '2' => array(
                        'name' => 'ALARM CO',
                    ),
                    '3' => array(
                        'name' => 'ALARM CO2',
                    ),
                    '4' => array(
                        'name' => 'ALARM HEAT',
                    ),
                    '5' => array(
                        'name' => 'ALARM WATER',
                    ),
                    '255' => array(
                        'name' => 'BASIC_SET',
                    ),
                )
            ),
            '7' => array(
                'name' => 'Valeur forcée transmise au groupe 1',
                'description' => '1~99 : spécifie le niveau de variation d’un éclairage ou d’ouverture d’un volet pour une action «On». 255 : demande au variateur de lumière de s’allumer au niveau de variation précédemment utilisé avant son extinction.',
                'default' => '255',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '255',
            ),
            '9' => array(
                'name' => 'Ordre d’extinction après annulation de l’alarme',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Oui',
                        'description' => 'L’ordre est envoyé eux groupe 1',
                    ),
                    '1' => array(
                        'name' => 'Non',
                        'description' => 'L’ordre n’est pas envoyé au groupe 1'
                    ),
                )
            ),
            '12' => array(
                'name' => 'Delta minimum entre 2 valeurs transmises',
                'description' => 'Valeur = xx°C x 16. Exemple pour 0,5°C --> 0,5x16 = 8 (valeur par défaut)',
                'default' => '8',
                'type' => 'input',
                'unite' => '',
                'min' => '0',
                'max' => '255',
            ),
            '13' => array(
                'name' => 'Transmission à tous les modules à proximité (broadcast)',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactif',
                        'description' => 'IN & TMP : Broadcast inactif',
                    ),
                    '1' => array(
                        'name' => 'Actif/Inactif',
                        'description' => 'IN : Broadcast actif / TMP : Broadcast inactif'
                    ),
                    '2' => array(
                        'name' => 'Inactif/Actif',
                        'description' => 'IN : Broadcast inactif / TMP : Broadcast actif'
                    ),
                    '3' => array(
                        'name' => 'Actif',
                        'description' => 'IN & TMP : Broadcast actif'
                    ),
                )
            ),
            '14' => array(
                'name' => 'Fonction de scènes',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Désactivé',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Activé',
                        'description' => ''
                    ),
                )
            ),
        ),
    ),
);
