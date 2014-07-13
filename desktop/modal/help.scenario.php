<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_trigger">
                    {{Déclencheur}}
                </a>
            </h4>
        </div>
        <div id="collapse_trigger" class="panel-collapse collapse">
            <div class="panel-body">
                {{Il existe des déclencheur spécifique (autre que ceux fournis par les commandes) :}} <br/>
                <pre>{{#start#  : déclenché au (re)démarrage de Jeedom}}</pre>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_condition">
                    {{Condition}}
                </a>
            </h4>
        </div>
        <div id="collapse_condition" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="alert alert-danger">{{Attention à bien mettre des ' ou " (quote ou double quote) lors de l'utilisation de chaine de caractère}}</div>
                {{Vous pouvez utiliser n'importe lequel des symboles suivant pour les opérateurs : }}
                <pre>
{{= : égal}}
{{> : supérieur}}
{{>= : supérieur ou égal}}
{{< : inférieur}}
{{<= : inférieur ou égal}}
{{!= : différent}}
{{~ : contient}}
{{!~ : ne contient pas}}
                </pre>
                {{Vous pouvez combiner n'importe quelle opération avec les opérateurs suivants : }}
                <pre>
{{&&/ET/et/AND/and : et}}
{{||/OU/ou/OR/or : ou}}
                </pre>
                {{Vous pouvez aussi utiliser les tags suivants :}}
                <pre>
{{#heure#  : heure courante (ex : 17 pour 17h15)}}
{{#minute# : minute courante (ex : 15 pour 17h15)}}
{{#jour# : jour courant}}
{{#mois# : mois courant}}
{{#annee# : année courante}}
{{#time# : heure et minute courante (ex : 1715 pour 17h15)}}
{{#date# : jour et mois courant (ex : 1215 pour le 15 decembre)}}
{{#semaine# : numéro de la semaine (ex : 51)}}
{{#sjour# : pour le nom du jour de la semaine (ex : Samedi)}}
{{rand(1,10) : pour un nombre aléatoire de 1 à 10}}
{{tendance(commande,period) : donne la tendance de la commande sur la period 
    Ex : tendance(#[Salle de bain][Hydrometrie][Humidité]#,1 hour) : Renvoi 1 si en augmentation, 0 si constant et -1 si en diminution}}
{{variable(mavariable,valeur par default) : récupération de la valeur d'une variable ou de la valeur souhaitée par défaut
    Ex : variable(plop,10) renvoie la valeur de la variable plop ou 10 si elle est vide ou n'existe pas}}
{{scenario(scenario) : donne le statut du scenario
    Ex : tendance(#[Salle de bain][Lumière][Auto]#) : Renvoi 1 en cours, 0 si arreté et -1 si desactivé}}
                </pre>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_action">{{Action}}</a>
            </h4>
        </div>
        <div id="collapse_action" class="panel-collapse collapse">
            <div class="panel-body">
                {{En plus des commandes domotiques vous avez accès aux fonctions suivantes : }}
                <pre>
{{sleep : pause de x seconde(s)
variable : création/modification d'une ou de la valeur d'une variable
scenario : permet le controle des scénarios
stop : arrête le scénario
icon : permet de changer l'icone de représentation du scenario}}
                </pre>
                {{Vous pouvez aussi utiliser les tags suivants dans les options : }}
                <pre>
{{#heure#  : heure courante (ex : 17 pour 17h15)}}
{{#minute# : minute courante (ex : 15 pour 17h15)}}
{{#jour# : jour courant}}
{{#mois# : mois courant}}
{{#annee# : année courante}}
{{#time# : heure et minute courante (ex : 1715 pour 17h15)}}
{{#date# : jour et mois courant (ex : 1215 pour le 15 decembre)}}
{{#semaine# : numéro de la semaine (ex : 51)}}
{{#sjour# : pour le nom du jour de la semaine en anglais (ex : sunday)}}
{{rand[1-10] : pour un nombre aléatoire de 1 à 10}}
{{variablemavariable,valeur par default) : récupération de la valeur d'une variable ou de la valeur souhaitée par défaut
    Ex : variable(plop,10) renvoie la valeur de la variable plop ou 10 si elle est vide ou n'existe pas}}
                </pre>
            </div>
        </div>
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_code">{{Code}}</a>
            </h4>
        </div>
        <div id="collapse_code" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="panel-group" id="accordion2">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_cmd">
                                    {{Commandes (capteurs et actionneurs)}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_cmd" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <pre>
<h5 style='display: inline;'>cmd::byString($string);</h5>
    {{Retourne l'objet commande correspondant
    $string => lien vers la commande voulue : [type].[nom de l'équipement].[nom de la commande] (ex : [weather].[Villeurbanne].[Température])}}
                                </pre>  
                                <pre>
<h5 style='display: inline;'>cmd::byId($id);</h5>
    {{Retourne l'objet commande correspondant
    $string => Id de la commande voulue (voir Général => Affichage)}}
                                </pre> 
                                <pre>
<h5 style='display: inline;'>$cmd->execCmd($options = null, $cache = 1);</h5>
    {{Exécute la commande et retourne le résultat
    $options => Options pour l'exécution de la commande (peut être spécifique au plugin), option de base : 
          Sous-type de la commande : message => $option = array('title' => 'titre du message , 'message' => 'Mon message');
                                     color => $option = array('color' => 'couleur en hexadécimal');
                                     value => $option = array('color' => 'valeur voulue');
                                     slider => $option = array('slider' => 'valeur voulue de 0 à 100');
    $cache  => 0 = ignorer le cache , 1 = mode normal, 2 = cache utilisé même si expiré (puis marqué à recollecter)}}
                                </pre>                



                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_log">
                                    {{Log}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_log" class="panel-collapse collapse">
                            <div class="panel-body">
                                <pre>
<h5 style='display: inline;'>log::add('filename','level','message');</h5>
    {{filename => nom du fichier de log
    level => [debug],[info],[error],[event]
    message => message à écrire dans les logs}}
                                </pre>
                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_scenario">
                                    {{Scénario}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_scenario" class="panel-collapse collapse">
                            <div class="panel-body">
                                <pre>
<h5 style='display: inline;'>$scenario->getName();</h5>
    {{Retourne le nom du scénario courant}}
                                </pre> 
                                <pre>
<h5 style='display: inline;'>$scenario->getGroup();</h5>
    {{Retourne le groupe du scénario}}
                                </pre> 
                                <pre>
<h5 style='display: inline;'>$scenario->getIsActive();</h5>
    {{Retourne l'état du scénario}}
                                </pre>
                                <pre>
<h5 style='display: inline;'>$scenario->setIsActive($active);</h5>
    {{Permet d'activer ou non du scénario
    $active => 1 actif , 0 non actif}}
                                </pre> 
                                <pre>
<h5 style='display: inline;'>$scenario->setOnGoing($onGoing);</h5>
    {{Permet de dire si le scénario est en cours ou non
    $onGoing => 1 en cours , 0 arrêter}}
                                </pre>
                                <pre>
<h5 style='display: inline;'>$scenario->save();</h5>
    {{Sauvegarde les modifications}}
                                </pre>
                                <pre>
<h5 style='display: inline;'>$scenario->setData($key, $value);</h5>
    {{Sauvegarde une donnée
    $key => clef de la valeur (int ou string)
    $value => valeur à stocker (int, string, array ou object)}}
                                </pre>
                                <pre>
<h5 style='display: inline;'>$scenario->getData($key);</h5>
    {{Récupère une donnée
    $key => clef de la valeur (int ou string)}}
                                </pre>
                                <pre>
<h5 style='display: inline;'>$scenario->removeData($key);</h5>
    {{Supprime une donnée}}
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
