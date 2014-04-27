<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="padding:10px 20px;">
            <h3>{{Connectez-vous}}</h3>
            <input type="text" id="in_login_username" value="" placeholder="{{Nom d'utilisateur}}" data-theme="a" />
            <input type="password" id="in_login_password" value="" placeholder="{{Mot de passe}}" data-theme="a" />
            <label>{{Enregistrer cet appareil}}</label>
            <select id="cb_storeConnection" data-role="slider" data-mini="true">
                <option value="0">{{Non}}</option>
                <option value="1">{{Oui}}</option>
            </select>


            <a id="bt_login_validate" href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check">{{Connexion}}</a>
        </div>
        <script>
            $('#bt_login_validate').on('click', function() {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/user.ajax.php", // url du fichier php
                    data: {
                        action: "login",
                        username: $('#in_login_username').val(),
                        password: $('#in_login_password').val(),
                        storeConnection: $('#cb_storeConnection').val()
                    },
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error, $('#div_alert'));
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('#div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                        if ($('#cb_storeConnection').val() == 1) {
                            sessionStorage.setItem("deviceKey", data.result.deviceKey);
                        }
                        initApplication();
                    }
                });
            });
        </script>
    </body>
</html>



