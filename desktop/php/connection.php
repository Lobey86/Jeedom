<?php sendVarToJS('nodeJsKey', ''); ?>
<div id="wrap">
    <div class="container">
        <center>
            <img src="core/img/jeedom_ico.png" /><br/><br/>
            <?php
            if (init('error') == 1) {
                echo '<div class="alert alert-danger">{{Nom d\'utilisateur ou mot de passe inccorect !}}</div>';
            }
            ?>
            <form method="post" name="login" action="index.php?v=d" class="form-signin">
                <h2 class="form-signin-heading">{{Connectez-vous}}</h2>
                <br/><input class="input-block-level" type="text" name="login" id="login" placeholder="{{Nom d'utilisateur}}"/><br/>
                <br/><input class="input-block-level" type="password" id="mdp" name="mdp" placeholder="{{Mot de passe}}"/><br/>
                <button type="submit" class="btn-lg btn-primary btn-block" style="margin-top: 10px;"><i class="fa fa-sign-in"></i> {{Connexion}}</button>
            </form>
        </center>
    </div>
    <br/>
</div>