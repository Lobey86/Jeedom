<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>

<div class="alert alert-danger">
    <legend>{{Principe}}</legend>
    {{Le principe est le suivant : une phrase de commande declenche une unique commande. Pour simplifier la création des phrases il est possible d'utiliser des mots clef afin que jeedom genere automtiquement un liste de phrases.}}
</div>

<div class="alert alert-success">
    <legend>{{Demande}}</legend>
    {{Vous pouvez utiliser #commande# et #objet# (les 2 doivent absolument etre utiliser ensemble) pour generer une liste de commande (il est possible de filtrer la génération pour réduire la liste).Il est aussi possible d'utiliser #equipement# (utile si plusieurs commandes appartenant au meme objet on le meme nom)}}
    <br/>{{Exemple :}} <em>{{Quelle est la #commande# [du |de la |de l']#objet#}}</em>

    <br/><br/>
    {{Lors de la génération des commandes vous pouvez utiliser le champs synonyme (syn1=syn2,syn3|syn4=syn5) pour remplacer le nom des objets, des équipements et/ou des commandes }}
    <br/><br/>
    {{Pour les actions vous pouvez utiliser #color# (obligatoire si la commande est de type couleur) pour la valeur d'une couleur ou #slider# (obligatoire si la commande est de type slider) pour la valeur d'un slider.}}
</div>

<div class="alert alert-warning">
    <legend>{{Reponse}}</legend>
    {{Vous pouvez utiliser #valeur# et #unite# dans le retour (ils seront remplacés par la valeur et l'unité de la commande).Toutes les valeurs passer dans demande (#mavaleur#) sont accessible. Vous avez aussi #heure#, #date#, #jour# et #datetime#.}}
    <br/>{{Exemple :}} <em>{{#valeur# #unite#}}</em>

    <br/><br/>
    {{Vous pouvez utiliser le champs convertion binaire pour convertir les valeurs binaire (0 et 1) : }}
    <br/>{{Exemple :}} <em>{{non|oui}}</em>
</div>

<div class="alert alert-info">
    <legend>{{Personne}}</legend>
    {{Le champs personne permet de n'autoriser que certain personne à executer la commande, vous pouvez mettre plusieurs profile en les separants par |.}}
    <br/>{{Exemple :}} <em>{{personne1|personne2}}</em>
</div>

