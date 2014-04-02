<center>
    <br/><br/>
    <img src="core/img/jeedom_ico.png" height="120" width="125"/>
    <?php
    if (init('error') == 1) {
        echo "<br\>Mauvais non d'utilisateur ou mot de passe<br\>";
    }
    ?>
    <form method="post" name="login" action="index.php?v=m&p=home">
        <div data-role="fieldcontain">
            <input class="form-control" type="text" name="login" placeholder="Nom d'utilisateur" />
            <input class="form-control" type="password" name="mdp" placeholder="Mot de passe" />
            <input class="form-control" type="submit" data-theme="b" value="Connexion"/>
        </div>
    </form>
</center>