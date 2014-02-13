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
    'fibaro.fgd211' => array(
        'name' => 'Fibaro FGD-211 [Dimmer]',
        'vendor' => 'Fibar Group',
        'manufacturerId' => 271,
        'manufacturerProductType' => 256,
        'manufacturerProductId' => 4106,
        'commands' => array(
            array('name' => 'Intensité', 'type' => 'action', 'subtype' => 'slider', 'isVisible' => 1, 'value' => 'Etat',
                'configuration' => array('class' => '0x26', 'value' => 'Set(#slider#)')
            ),
            array('name' => 'On', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'Set(99)')
            ),
            array('name' => 'Off', 'type' => 'action', 'subtype' => 'other', 'isVisible' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'Set(0)')
            ),
            array('name' => 'Etat', 'type' => 'info', 'subtype' => 'numeric', 'unite' => '%', 'isVisible' => 0, 'eventOnly' => 1,
                'configuration' => array('class' => '0x26', 'value' => 'data.level', 'minValue' => 0, 'minValue' => 100)
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
                'name' => 'Transmission des commandes locales au groupe',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => '0',
                        'description' => 'Transmet les commande Marche et Arrêt',
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Ne transmet que la commande Arrêt. Un double appui transmet la commande Marche (dans le cas d’un variateur, celui-ci s’allume à son dernier niveau). Nécessite l’activation du paramètre 15'
                    ),
                    '2' => array(
                        'name' => '2',
                        'description' => 'Ne transmet que la commande Arrêt. Un double appui transmet la commande Marche (dans le cas d’un variateur, celui-ci s’allume à 100%). Nécessite l’activation du paramètre 15',
                    ),
                )
            ),
            '7' => array(
                'name' => 'Vérifier l’état de l’équipement distant avant d’envoyer un ordre depuis le bouton 2',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => ''
                    ),
                )
            ),
            '8' => array(
                'name' => 'Palier de variation automatique',
                'description' => 'Pourcentage de variation à chaque palier de 1 à 99% (en commande automatique)',
                'default' => '1',
                'type' => 'input',
                'unite' => '%',
                'min' => '1',
                'max' => '99',
            ),
            '9' => array(
                'name' => 'Durée de variation manuelle',
                'description' => 'Durée de la variation entre deux valeurs extrêmes (1% --> 100% ou 100% --> 1%) lors d’une commande manuelle. x 10ms (1 = 10ms / 255 = 2,55s)',
                'default' => '5',
                'type' => 'input',
                'unite' => 's',
                'min' => '1',
                'max' => '255',
            ),
            '10' => array(
                'name' => 'Durée de variation automatique',
                'description' => 'Durée de la variation douce entre deux valeurs extrêmes (1% --> 100% ou 100% --> 1%) lors d’une commande automatique ou d’un allumage / extinction. x 10ms (1 = 10ms / 255 = 2,55s) - 0 pour désactiver',
                'default' => '5',
                'type' => 'input',
                'unite' => 's',
                'min' => '0',
                'max' => '255',
            ),
            '11' => array(
                'name' => 'Palier de variation manuelle',
                'description' => 'Pourcentage de variation à chaque palier de 1 à 99% (en commande manuelle)',
                'default' => '1',
                'type' => 'input',
                'unite' => '%',
                'min' => '1',
                'max' => '99',
            ),
            '12' => array(
                'name' => 'Niveau maximum de luminosité',
                'description' => 'Niveau maximum de luminosité autorisé (en %). Doit être supérieur au paramètre 13',
                'default' => '99',
                'type' => 'input',
                'unite' => '%',
                'min' => '2',
                'max' => '99',
            ),
            '13' => array(
                'name' => 'Niveau minimum de luminosité',
                'description' => 'Niveau minimum de luminosité autorisé (en %). Doit être inférieur au paramètre 12',
                'default' => '2',
                'type' => 'input',
                'unite' => '%',
                'min' => '2',
                'max' => '99',
            ),
            '14' => array(
                'name' => 'Compatibilité commutateurs bi-stables',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'Bouton-poussoir impusionnel (mono-stable)',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'Commutateur Marche / Arrêt (bi-stable)'
                    ),
                )
            ),
            '15' => array(
                'name' => 'Fonction double impulsion (double-clic)',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Inactif',
                        'description' => '',
                    ),
                    '1' => array(
                        'name' => 'Actif',
                        'description' => 'Active (fixe la luminosité à 100% lors d’une double impulsion)'
                    ),
                )
            ),
            '16' => array(
                'name' => 'Mémorisation de l’état',
                'description' => '',
                'default' => '1',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'Reste éteint après une coupure de courant',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'Reprend l’état précédent la coupure de courant'
                    ),
                )
            ),
            '17' => array(
                'name' => 'Fonction va et vient ou télérupteur',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'L’entrée bouton 2 commande le groupe 2. Il est possible de connecter plusieurs boutons poussoirs à impulsion (mono-stable) sur l’entrée 1 ou deux interrupteurs 3 pôles en mode va-et-vient.',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'Chaque entrée est reliée à un commutateur bi-stable à 2 pôles, l’ensemble fonctionne comme un « va-et-vient ».'
                    ),
                )
            ),
            '18' => array(
                'name' => 'Synchronisation de la variation',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => 'Non',
                        'description' => 'Ne communique que la mise en marche et l’arrêt.',
                    ),
                    '1' => array(
                        'name' => 'Oui',
                        'description' => 'Communique aussi le niveau de variation aux autres variateurs du groupe.'
                    ),
                )
            ),
            '19' => array(
                'name' => 'Mode de fonctionnement avec interrupteur bi-stable',
                'description' => '',
                'default' => '0',
                'type' => 'select',
                'value' => array(
                    '0' => array(
                        'name' => '0',
                        'description' => 'Chaque changement de position de l’interrupteur bi-stable inverse l’état du variateur (Marche / Arrêt).',
                    ),
                    '1' => array(
                        'name' => '1',
                        'description' => 'Interrupteur bi-stable fermé (marche) --> lampe allumée. Interrupteur bi-stable ouvert (arrêt) --> lampe éteinte. Vérifier la bonne configuration du paramètre 14'
                    ),
                )
            ),
            '30' => array(
                'name' => 'Alarme de tout type',
                'description' => '',
                'default' => '3',
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
                    '2' => array(
                        'name' => 'Arrêt',
                        'description' => '',
                    ),
                    '3' => array(
                        'name' => 'Clignotement pendant 10 minutes max.',
                        'description' => '',
                    ),
                )
            ),
            '39' => array(
                'name' => 'Durée de l’alarme',
                'description' => 'Durée de l’activation en cas d’alarme (en ms)',
                'default' => '600',
                'type' => 'input',
                'unite' => 'ms',
                'min' => '1',
                'max' => '65535',
            ),
            '20' => array(
                'name' => 'Contrôle fin de la fréquence',
                'description' => 'Permet d’optimiser le niveau minimum de variation des lampes LED compatibles variateur (vérifiez si votre modèle est concerné). Attention: Une mauvaise configuration peut empêcher le bon fonctionnement du variateur.',
                'default' => '110',
                'type' => 'input',
                'unite' => '0.5 Hz',
                'min' => '100',
                'max' => '170',
            ),
        ),
    ),
);
?>
