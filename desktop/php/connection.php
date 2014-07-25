<?php sendVarToJS('nodeJsKey', ''); ?>
<div id="wrap">
    <div class="container">
        <center>
            <img src="core/img/logo-jeedom-grand-nom-couleur.svg"/><br/><br/>
            <?php
            if (init('error') == 1) {
                echo '<div class="alert alert-danger">{{Nom d\'utilisateur ou mot de passe inccorect !}}</div>';
            }
            $getParams = "";
            foreach ($_GET AS $var => $value) {
                if ($var != 'logout') {
                    $getParams.='&' . $var . '=' . $value;
                }
            }
            ?>
            <form method="post" name="login" action="index.php?v=d<?php print htmlspecialchars($getParams); ?>" class="form-signin">
                <h2 class="form-signin-heading">{{Connectez-vous}}</h2>
                <input type="text" name="connect" id="connect" hidden value="1" style="display: none;"/>
                <br/><input class="input-block-level" type="text" name="login" id="login" placeholder="{{Nom d'utilisateur}}"/><br/>
                <br/><input class="input-block-level" type="password" id="mdp" name="mdp" placeholder="{{Mot de passe}}"/><br/>
                <br/><input class="input-block-level" type="checkbox" id="registerDesktop" name="registerDesktop"/> Enregistrer cet ordinateur<br/>
                <button type="submit" class="btn-lg btn-primary btn-block" style="margin-top: 10px;"><i class="fa fa-sign-in"></i> {{Connexion}}</button>
            </form>
        </center>
    </div>
    <br/>
</div>