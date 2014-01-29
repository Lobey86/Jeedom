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
    'fibaro.fgrgb101' => array(
        'name' => 'Fibaro FGRGB-101',
        'vendor' => 'Fibar Group',
        'manufacturerId' => 271,
        'manufacturerProductType' => 2304,
        'manufacturerProductId' => 4096,
        'commands' => array(
            array('name' => 'Couleur', 'type' => 'action', 'subtype' => 'color', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => '#color#')
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
            '6' => array(
                'name' => 'Action des boutons (type de commande Z-Wave envoyées)',
                'description' => '',
                'default' => '255',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Variateur',
                        'description' => 'Mode Normal (VARIATEUR) "BASIC SET /SWITCH_MULTILEVEL_START/STOP"',
                    ),
                    '1' => array(
                        'name' => 'Normale 1',
                        'description' => 'Mode Normal (RGBW) "COLOR_CONTROL_SET/START/STOP_STATE_CHANGE"'
                    ),
                    '2' => array(
                        'name' => 'Normale 2',
                        'description' => 'Mode Normal (RGBW) "COLOR_CONTROL_SET"',
                    ),
                    '3' => array(
                        'name' => 'Normale 3',
                        'description' => 'Mode Normal (RGBW) "BASIC SET/SWITCH_MULTILEVEL_START/STOP"',
                    ),
                    '4' => array(
                        'name' => 'Arc-en-ciel',
                        'description' => 'Mode "ARC-EN-CIEL" (RGBW) "COLOR_CONTROL_SET"',
                    ),
                )
            ),
            '8' => array(
                'name' => 'Action des boutons (type de commande Z-Wave envoyées)',
                'description' => '',
                'default' => '255',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Pas à pas',
                        'description' => 'Transition "pas à pas". Voir les paramètres 9 et 10 (nombre d\'étapes + délai entre étapes)',
                    ),
                    '1' => array(
                        'name' => 'Progressive',
                        'description' => 'Transition progressive. Voir le paramètre 11 (durée de la transition)'
                    ),
                )
            ),
            '9' => array(
                'name' => 'Sélection du nombre d’étapes',
                'description' => 'Nombre d’étapes ',
                'default' => '1',
                'type' => 'input',
                'min' => '1',
                'max' => '255',
            ),
            '10' => array(
                'name' => 'Sélection du délai entre chaque étapes',
                'description' => 'Nombre d’étapes ',
                'default' => '10',
                'type' => 'input',
                'min' => '0',
                'max' => '60000',
            ),
            '11' => array(
                'name' => 'Sélection de la durée de la transition d’état',
                'description' => 'Changement d’état immédiat. 20-260 ms (correspond à  [valeur choisie]*20ms. 1-63s (correspond à  [valeur choisie - 64]*1 seconde). 10-630s (correspond à  [valeur choisie - 128]*10 secondes). 1-63 min (correspond à  [valeur choisie - 192]*1 minute)',
                'default' => '10',
                'type' => 'input',
                'min' => '0',
                'max' => '60000',
            ),
            '12' => array(
                'name' => 'Niveau maximum de luminosité',
                'description' => '',
                'default' => '255',
                'type' => 'input',
                'min' => '3',
                'max' => '255',
            ),
            '13' => array(
                'name' => 'Niveau minimum de variation',
                'description' => '',
                'default' => '2',
                'type' => 'input',
                'min' => '2',
                'max' => '255',
            ),
            '16' => array(
                'name' => 'Mémorisation d’état à la coupure d’alimentation',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Aucune',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => ''
                    ),
                )
            ),
            '30' => array(
                'name' => 'Réaction aux Alarmes',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactif',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'ALARME ON',
                        'description' => ''
                    ),
                    '2' => array(
                        'name' => 'ALARME OFF',
                        'description' => ''
                    ),
                    '3' => array(
                        'name' => 'PROGRAMME D’ALERTE',
                        'description' => ''
                    ),
                )
            ),
            '38' => array(
                'name' => 'Programme de séquence d’alerte',
                'description' => 'Précise le numéro du programme d’alerte.',
                'default' => '10',
                'type' => 'input',
                'min' => '1',
                'max' => '10',
            ),
            '39' => array(
                'name' => 'Choix du de la duré du programme d’alerte',
                'description' => 'Choix de la durée en secondes',
                'default' => '600',
                'type' => 'input',
                'min' => '1',
                'max' => '65534',
            ),
            '42' => array(
                'name' => 'Classe de commande utilisée pour la remontée d\'information lors des changements de statuts.',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => '0',
                        'description' => 'Remontée suite à une action sur une entrée ou depuis un contrôleur via Z-Wave (SWITCH_MULTILEVEL)',
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Remontée à la suite d’action sur les entrées (SWITCH_MULTILEVEL).'
                    ),
                    '2' => array(
                        'name' => '2',
                        'description' => 'Remontée à la suite d’action sur les entrées. (COLOR_CONTROL)'
                    ),
                )
            ),
            '43' => array(
                'name' => 'Seuil de déclenchement de remontée d\'information sur les entrées 0-10V',
                'description' => 'Défini la variation minimale nécessaire au déclenchement d\'une remontée d\'information (en décivolt).',
                'default' => '5',
                'type' => 'input',
                'min' => '1',
                'max' => '100',
                'unite' => 'dcV'
            ),
            '44' => array(
                'name' => 'Fréquence des mesures de puissance.',
                'description' => '',
                'default' => '30',
                'type' => 'input',
                'min' => '0',
                'max' => '65534',
                'unite' => 's'
            ),
            '45' => array(
                'name' => 'Seuil de variation déclenchant la remontée de la consommation cumulée du produit contrôlé.',
                'description' => '',
                'default' => '30',
                'type' => 'input',
                'min' => '0',
                'max' => '254',
                'unite' => '0,1kWh'
            ),
            '71' => array(
                'name' => 'Réaction d’une luminosité à 0%',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Blanc',
                        'description' => 'La couleur de l’éclairage est définie en blanc (contrôle les quatre canaux).',
                    ),
                    '1' => array(
                        'name' => 'Mémorisé',
                        'description' => 'La dernière couleur mémorisée est rappelée.'
                    ),
                )
            ),
            '72' => array(
                'name' => 'Démarrer un programme prédéfini au démarrage du mode RGB/RGBW',
                'description' => 'Numéro du programme d’animation.',
                'default' => '1',
                'type' => 'input',
                'min' => '1',
                'max' => '10',
            ),
            '73' => array(
                'name' => 'Résultat d’un triple clic',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => '0',
                        'description' => 'Envoi d’une commande d’information de noeud "NIF" (Node Information Frame).',
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Démarrage du programme préféré.'
                    ),
                )
            ),
        ),
    ),
);
?>
