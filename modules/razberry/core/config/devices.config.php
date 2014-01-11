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

global $listZwaveDevice;

$listZwaveDevice = array(
    'Fibaro FGS-221' => array(
        'name' => 'Fibaro FGS-221 Double charge',
        'vendor' => 'Fibar Group',
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
    'aeon multisensor' => array(
        'name' => 'Aeon Multi-Sensor',
        'vendor' => 'Aeon Labs',
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
    'fibaro FGD-211' => array(
        'name' => 'Fibaro FGD-211',
        'vendor' => 'Fibar Group',
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
    'fibaro FGWPE-101' => array(
        'name' => 'Fibaro Wall Plug',
        'vendor' => 'Fibar Group',
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
