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

if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
?>

Les tags suivants seront automatiquement remplacés lors de la génération du widget : 
<br/><br/>

<legend>Info</legend>
<pre>
#id# => ID de la commande
#name# => Nom de la commande
#unite# => Unité de la commande
#collectDate# => Date exacte ou de la valeur de la commande
#state# => Valeur de la commande 
    Si la commande est binaire l'état peut être "green" ou "red"
#displayHistory# => Si la commande est historisée alors la valeur est de '' sinon 'display : none;'
#averageHistoryValue# => Valeur moyenne sur les x dernières heures de la commande
#minHistoryValue# => Minimum sur les x dernières heures de la commande
#maxHistoryValue# => Maximum moyenne sur les x dernières heures de la commande
#tendance# => Tendance soit 'fa fa-minus', 'fa fa-arrow-up' ou 'fa fa-arrow-down'
#minValue# => Valeur minimum que peut prendre la commande
#maxValue# => Valeur maximum que peut prendre la commande
#history# => si la commande est historisée historize vaudra : 'history cursor' et le widget 'cmd.info.history.default' sera ajouté (pour permettre d'afficher l'historique)
</pre>

<legend>Action</legend>
<pre>
#id# => ID de la commande
#name# => Nom de la commande
#lastValue# => Dernière valeur de la commande (peut être vide)
#minValue# => Valeur minimum que peut prendre la commande
#maxValue# => Valeur maximum que peut prendre la commande

Pour que l'action soit executée il faut appeler la fonction : execCmd(ID,options) avec
    ID => ID de la commande
    options => object contenant les options de la commande

Exemple pour une commande de type slider :
    execCmd('#id#', {slider : 30});

Exemple pour une commande de type color :
     execCmd('#id#', {color: '#000000'});
</pre>

