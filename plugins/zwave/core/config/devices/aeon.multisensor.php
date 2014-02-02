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
    'aeon.multisensor' => array(
        'name' => 'Aeon Multi-Sensor',
        'vendor' => 'Aeon Labs',
        'manufacturerId' => 134,
        'manufacturerProductType' => 2,
        'manufacturerProductId' => 5,
        'commands' => array(
            array('name' => 'Présence', 'type' => 'info', 'subtype' => 'binary', 'isVisible' => 1, 'isHistorized' => 1, 'eventOnly' => 1,
                'configuration' => array('class' => '0x30', 'value' => 'data[1].level')
            ),
            array('name' => 'Température', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '°C', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[1].val')
            ),
            array('name' => 'Luminosité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => 'Lux', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[3].val', 'maxValue' => 1000, 'minValue' => 0)
            ),
            array('name' => 'Humidité', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => '%', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[5].val')
            ),
            array('name' => 'Batterie', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 0, 'isHistorized' => 0, 'unite' => '%',
                'configuration' => array('class' => '0x80', 'value' => 'data.last')
            ),
        ),
        'parameters' => array(
            '2' => array(
                'name' => 'Reveille de 10 min à l\'insertion des piles',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Oui',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Non',
                        'description' => ''
                    ),
                )
            ),
            '3' => array(
                'name' => 'Délai mémorisation mouvement',
                'description' => 'Délai après une absence de mouvement avant que le DSB05 n\'envoie la valeur "OFF". Attention si la valeur est supérieure à 255 secondes, le DSB05 convertie la valeur donnée en minutes arrondie à la minute supérieure.',
                'default' => '240',
                'type' => 'input',
                'unite' => 's',
                'min' => '1',
                'max' => '15300',
            ),
            '4' => array(
                'name' => 'Activer désactiver détecteur de mouvement',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Oui',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Non',
                        'description' => ''
                    ),
                )
            ),
            '5' => array(
                'name' => 'Type de commande à envoyer lors des détections de mouvement',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Basic Set',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Sensor Binary Report',
                        'description' => ''
                    ),
                )
            ),
            '100' => array(
                'name' => 'Remet les paramètres 101 à 103 aux valeurs par défaut.',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Oui',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Non',
                        'description' => ''
                    ),
                )
            ),
            '101' => array(
                'name' => 'Type d\'informations à envoyer lors transmissions à intervalles régulières au groupe 1',
                'description' => '',
                'default' => '0',
                'type' => 'input',
            ),
            '102' => array(
                'name' => 'Type d\'informations à envoyer lors transmissions à intervalles régulières au groupe 2',
                'description' => '',
                'default' => '0',
                'type' => 'input',
            ),
            '103' => array(
                'name' => 'Type d\'informations à envoyer lors transmissions à intervalles régulières au groupe 3',
                'description' => '',
                'default' => '0',
                'type' => 'input',
            ),
            '111' => array(
                'name' => 'Durée de l\'intervalle entre deux transmissions automatiques au groupe 1',
                'description' => '',
                'default' => '720',
                'type' => 'input',
                'unite' => 's',
                'min' => '0',
                'max' => '2678400',
            ),
            '112' => array(
                'name' => 'Durée de l\'intervalle entre deux transmissions automatiques au groupe 2',
                'description' => '',
                'default' => '720',
                'type' => 'input',
                'unite' => 's',
                'min' => '0',
                'max' => '2678400',
            ),
            '113' => array(
                'name' => 'Durée de l\'intervalle entre deux transmissions automatiques au groupe 3',
                'description' => '',
                'default' => '720',
                'type' => 'input',
                'unite' => 's',
                'min' => '0',
                'max' => '2678400',
            ),
            '114' => array(
                'name' => 'Remise aux valeurs d\'usine de l\'ensemble des paramètres',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Oui',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Non',
                        'description' => ''
                    ),
                )
            ),
        )
    ),
);
?>
