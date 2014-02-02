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
    'fibaro.fgwpe101' => array(
        'name' => 'Fibaro Wall Plug',
        'vendor' => 'Fibar Group',
        'manufacturerId' => 271,
        'manufacturerProductType' => 1536,
        'manufacturerProductId' => 4096,
        'commands' => array(
            array('name' => 'Puissance', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1, 'unite' => 'W', 'eventOnly' => 1,
                'configuration' => array('class' => '0x31', 'value' => 'data[4].val', 'maxValue' => 2500, 'minValue' => 0)
            ),
            array('name' => 'Consommation', 'type' => 'info', 'subtype' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0, 'unite' => 'kWh', 'eventOnly' => 1,
                'configuration' => array('class' => '0x32', 'value' => 'data[0].val')
            ),
            array('name' => 'On', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'value' => 'Set(255)')
            ),
            array('name' => 'Off', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x25', 'value' => 'Set(0)')
            ),
            array('name' => 'Reset', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 0, 'isHistorized' => 0, 'unite' => '',
                'configuration' => array('class' => '0x32', 'value' => 'Reset()')
            ),
        ),
        'parameters' => array(
            '1' => array(
                'name' => 'Toujours allumé',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Active',
                        'description' => 'La charge sera alimentée en permanence, le bouton «B» ne pourra pas éteindre la charge et la prise ne réagira pas aux envois de type alarme.'
                    ),
                    '1' => array(
                        'name' => 'Inactif',
                        'description' => 'Fonctionnement normal (commutateur)',
                    )
                )
            ),
            '16' => array(
                'name' => 'Mémoire d’état',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactif',
                        'description' => 'La charge restera éteinte après une coupure de courant'
                    ),
                    '1' => array(
                        'name' => 'Active',
                        'description' => 'La charge reprendra son état initial après une coupure de courant',
                    )
                )
            ),
            '34' => array(
                'name' => 'Réponse à une alarme',
                'description' => 'Types d’alarmes (additionner les valeurs) 0 : Aucune,1 : Générale, + 2 : Fumée, + 4 : CO, + 8 : CO2, + 16 : Haute température,32 : Inondation',
                'type' => 'input',
                'default' => '63',
                'min' => '0',
                'max' => '63',
            ),
            '35' => array(
                'name' => 'Always On Function',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Aucune',
                        'description' => ''
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Alimente la charge et le voyant clignote rouge-bleu-blanc pour une durée fixée par le param. 39.',
                    ),
                    '2' => array(
                        'name' => '2',
                        'description' => 'Eteint la charge et le voyant clignote rouge-bleu-blanc pour une durée fixée par le param. 39.',
                    ),
                    '3' => array(
                        'name' => '3',
                        'description' => 'Fait clignoter la charge toutes les 1 seconde pendant fixée par le param. 39 et le voyant clignote rouge-bleu-blanc puis la charge revient à son état d’origine.',
                    )
                )
            ),
            '39' => array(
                'name' => 'Durée de l’alarme',
                'description' => 'Durée avant la fin de l’alarme (dans le cas où cette durée n’est pas imposée par l’équipement déclencheur de cette alarme)',
                'type' => 'input',
                'unite' => 's',
                'default' => '600',
                'min' => '1',
                'max' => '65536',
            ),
            '40' => array(
                'name' => 'Transmission immédiate de la puissance si variation de',
                'description' => 'Variation de puissance nécessaire à l’envoi immédiat de la valeur de consommation.Attention: une valeur trop basse risque de surcharger le réseau Z-Wave en cas de variations constantes de la consommation de l’appareil (téléviseur par exemple).Une valeur de 100(%) désactive la fonction',
                'type' => 'input',
                'unite' => '%',
                'default' => '80',
                'min' => '1',
                'max' => '100',
            ),
            '42' => array(
                'name' => 'Transmission standard de la puissance si variation de',
                'description' => 'Variation de puissance nécessaire à l’envoi de la valeur de consommation.Par défaut l’envoi standard se fait à une cadence de 5 fois toutes les 30 secondes.Attention: une valeur trop basse risque de surcharger le réseau Z-Wave en cas de variations constantes de la consommation de l’appareil (téléviseur par exemple).Une valeur de 100(%) désactive la fonction',
                'type' => 'input',
                'unite' => '%',
                'default' => '15',
                'min' => '1',
                'max' => '100',
            ),
            '43' => array(
                'name' => 'Fréquence d’envoi de la puissance',
                'description' => 'Durée pendant laquelle la prise peut envoyer un maximum de 5 relevés de consommation (si variation de plus de 15% / voir param. 42).Une valeur de 255 signifie que la prise n’envoie pas cette donnée de manière cyclique et se contente de répondre en cas d’interrogation.',
                'type' => 'input',
                'unite' => 's',
                'default' => '30',
                'min' => '1',
                'max' => '254',
            ),
            '45' => array(
                'name' => 'Augmentation d’énergie consommée nécessaire au déclenchement d’une transmission',
                'description' => 'Augmentation de consommation énergétique nécessaire à l’envoi de la valeur de consommation.Une valeur de 255 signifie que la prise n’envoie pas cette donnée de manière cyclique et se contente de répondre en cas d’interrogation',
                'type' => 'input',
                'unite' => '0,1kWh',
                'default' => '10',
                'min' => '1',
                'max' => '254',
            ),
            '47' => array(
                'name' => 'Fréquence d’envoi des consommations en dehors de variations',
                'description' => 'Délai entre deux envois cycliques des données de consommations.Une valeur de 65534 signifie que la prise n’envoie des données qu’en cas de changement de consommation ou d’interrogation (param. 40, 42, 43, 45).',
                'type' => 'input',
                'unite' => 's',
                'default' => '3600',
                'min' => '1',
                'max' => '65534',
            ),
            '49' => array(
                'name' => 'Inclure l’énergie consommée par le module prise',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'Ne mesure pas la consommation de la charge.'
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'Inclue la consommation du module prise',
                    )
                )
            ),
            '50' => array(
                'name' => 'Groupe 2: Valeur basse',
                'description' => 'Une puissance inférieure à cette valeur enverra une commande   (cf. param 52) aux modules associés au groupe 2.La valeur du paramètre 50 doit être inférieure à la valeur du paramètre 51.',
                'type' => 'input',
                'unite' => '0,1W',
                'default' => '300',
                'min' => '0',
                'max' => '25000',
            ),
            '51' => array(
                'name' => 'Groupe 2: Valeur haute',
                'description' => 'Une puissance inférieure à cette valeur enverra une commande (cf. param 52) aux modules associés au groupe 2.La valeur du paramètre 51 doit être supérieure à la valeur du paramètre 50.',
                'type' => 'input',
                'unite' => '0,1W',
                'default' => '300',
                'min' => '0',
                'max' => '25000',
            ),
            '52' => array(
                'name' => 'Groupe 2: Action en cas de dépassement des valeurs 50 & 51',
                'description' => '',
                'default' => '6',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Aucune',
                        'description' => 'Aucune action.'
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Active les charges du groupe 2 jusqu’à ce que la puissance retombe en dessous de la valeur basse (param 50)',
                    ),
                    '2' => array(
                        'name' => '2',
                        'description' => 'Désactive les charges du groupe 2 jusqu’à ce que la puissance retombe en dessous de la valeur basse (param 50)',
                    ),
                    '3' => array(
                        'name' => '3',
                        'description' => 'Active les charges du groupe 2 jusqu’à ce que la puissance augmente au dessus de la valeur haute (param 51)',
                    ),
                    '4' => array(
                        'name' => '4',
                        'description' => 'Désactive les charges du groupe 2 jusqu’à ce que la puissance augmente au dessus de la valeur haute (param 51)',
                    ),
                    '5' => array(
                        'name' => '5',
                        'description' => '1 & 4 combinés',
                    ),
                    '6' => array(
                        'name' => '6',
                        'description' => '2 & 3 combinés',
                    ),
                )
            ),
            '60' => array(
                'name' => 'Puissance maximale pour la couleur violette',
                'description' => 'Puissance nécessaire l’affichage de la couleur violette (puissance maximale, valable uniquement pour une valeur de param. 61 à 0 ou 1)',
                'type' => 'input',
                'unite' => '0,1W',
                'default' => '25000',
                'min' => '1000',
                'max' => '32000',
            ),
            '61' => array(
                'name' => 'Couleur si charge allumée',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Variation',
                        'description' => 'Variation de couleurs par étapes en fonction de la puissance consommée.'
                    ),
                    '1' => array(
                        'name' => 'Continue',
                        'description' => 'Variation de couleurs continue en fonction de la puissance consommée.',
                    ),
                    '2' => array(
                        'name' => 'Blanc',
                        'description' => '',
                    ),
                    '3' => array(
                        'name' => 'Rouge',
                        'description' => '',
                    ),
                    '4' => array(
                        'name' => 'Vert',
                        'description' => '',
                    ),
                    '5' => array(
                        'name' => 'Bleu',
                        'description' => '',
                    ),
                    '6' => array(
                        'name' => 'Jaune',
                        'description' => '',
                    ),
                    '7' => array(
                        'name' => 'Cyan',
                        'description' => '',
                    ),
                    '8' => array(
                        'name' => 'Manjenta',
                        'description' => '',
                    ),
                    '9' => array(
                        'name' => 'Etein',
                        'description' => '',
                    ),
                )
            ),
            '63' => array(
                'name' => 'Couleur si alarme',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Aucune',
                        'description' => 'Pas de changement (fonctionnement normal suivant param. 61)'
                    ),
                    '1' => array(
                        'name' => 'Rouge-bleu-blanc',
                        'description' => 'Clignotement rouge-bleu-blanc.',
                    ),
                    '2' => array(
                        'name' => 'Blanc',
                        'description' => '',
                    ),
                    '3' => array(
                        'name' => 'Rouge',
                        'description' => '',
                    ),
                    '4' => array(
                        'name' => 'Vert',
                        'description' => '',
                    ),
                    '5' => array(
                        'name' => 'Bleu',
                        'description' => '',
                    ),
                    '6' => array(
                        'name' => 'Jaune',
                        'description' => '',
                    ),
                    '7' => array(
                        'name' => 'Cyan',
                        'description' => '',
                    ),
                    '8' => array(
                        'name' => 'Manjenta',
                        'description' => '',
                    ),
                    '9' => array(
                        'name' => 'Etein',
                        'description' => '',
                    ),
                )
            ),
            '70' => array(
                'name' => 'Fonction sécurité de sur-puissanc',
                'description' => 'Coupe la charge en cas de dépassement de la puissance paramétrée.Une valeur supérieure à 32000 (3200W) désactivé la sécurité.',
                'type' => 'input',
                'unite' => '0,1W',
                'default' => '65535',
                'min' => '10',
                'max' => '31999',
            ),
        ),
    )
);
?>
