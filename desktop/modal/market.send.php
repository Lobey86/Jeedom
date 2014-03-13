<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
sendVarToJS('market_display_info', array(
    'logicalId' => init('logicalId'),
    'name' => init('name')
));
sendVarToJS('market_type', init('type'));
try {
    if (init('logicalId') != '') {
        $market = market::byLogicalId(init('logicalId'));
    }
} catch (Exception $e) {
    $market = null;
}
if (is_object($market)) {
    if ($market->getApi_author() != config::byKey('market::apikey') || $market->getApi_author() == '') {
        throw new Exception('Vous n\'etez pas l\'autheur du plugin');
    }
}

if (init('type') == 'plugin') {
    $plugin = new plugin(init('logicalId'));
    if (!is_object($plugin)) {
        throw new Exception('Le plugin : ' . init('logicalId') . ' est introuvable');
    }
    $plugin_info = utils::o2a($plugin);
    $plugin_info['logicalId'] = $plugin_info['id'];
    unset($plugin_info['id']);
    sendVarToJS('market_display_info', $plugin_info);
}
?>

<div style="display: none;width : 100%" id="div_alertMarketSend"></div>


<a class="btn btn-success pull-right" style="color : white;" id="bt_sendToMarket"><i class="fa fa-cloud-upload"></i> Envoyer</a>

<br/><br/><br/>
<form class="form-horizontal" role="form" id="form_sendToMarket">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-lg-4 control-label">ID</label>
                <div class="col-lg-8">
                    <input class="form-control marketAttr" data-l1key="id" style="display: none;">
                    <input class="form-control marketAttr" data-l1key="logicalId" placeholder="ID" disabled/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Nom</label>
                <div class="col-lg-6">
                    <input class="form-control marketAttr" data-l1key="name" placeholder="Nom" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Type</label>
                <div class="col-lg-6">
                    <select class="form-control marketAttr" data-l1key="type" disabled>
                        <option value="plugin">Plugin</option>
                        <option value="widget">Widget</option>
                        <option value="zwave">[Zwave] Configuration module</option>
                        <option value="script">Script</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Statut</label>
                <div class="col-lg-6">
                    <select class="form-control marketAttr" data-l1key="status" >
                        <option>A valider</option>
                        <option>Validé</option>
                        <option>Refusé</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Catégorie</label>
                <div class="col-lg-6">
                    <input class="form-control marketAttr" data-l1key="categorie" placeholder="Catégorie">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Version</label>
                <div class="col-lg-6">
                    <input class="form-control marketAttr" data-l1key="version" placeholder="Version">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Dernière modification de l'archive</label>
                <div class="col-lg-6">
                    <span class="marketAttr label label-info" data-l1key="datetime"></span>
                </div>
            </div>
        </div> 
        <div class="col-md-6">
            <div class="form-group">

                <div class="form-group">
                    <label class="col-lg-4 control-label">Description</label>
                    <div class="col-lg-6">
                        <textarea class="form-control marketAttr" data-l1key="description" placeholder="Description" style="height: 150px;"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Utilisation</label>
                    <div class="col-lg-6">
                        <textarea class="form-control marketAttr" data-l1key="utilization" placeholder="Utilisation" style="height: 150px;"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Changelog</label>
                    <div class="col-lg-6">
                        <textarea class="form-control marketAttr" data-l1key="changelog" placeholder="Changelog" style="height: 150px;"></textarea>
                    </div>
                </div>
            </div>
        </div> 
    </div> 
</form>


<?php
if (is_object($market)) {
    sendVarToJS('market_display_info', utils::o2a($market));
}
?>
<script>

    $('body').setValues(market_display_info, '.marketAttr');

    $('.marketAttr[data-l1key=type]').value(market_type);

    $('#bt_sendToMarket').on('click', function() {
        var market = $('#form_sendToMarket').getValues('.marketAttr');
        market = market[0];
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/market.ajax.php", // url du fichier php
            data: {
                action: "save",
                market: json_encode(market),
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_alertMarketSend'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertMarketSend').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                if (market.id == undefined || market.id == '') {
                    /*  bootbox.confirm('Votre objet a été envoyé avec succès sur le market. La page doit etre rafraichir mais toute les données non sauvegardées seront perdu, voulez-vous continuer ?', function(result) {
                     if (result) {*/
                    $.showLoading();
                    window.location.reload();
                    /*     }
                     });*/
                } else {
                    $('#div_alertMarketSend').showAlert({message: 'Votre objet a été envoyé avec succès sur le market', level: 'success'});
                }

            }
        });
    });
</script>