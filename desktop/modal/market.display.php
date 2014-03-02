<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}

if (init('id') != '') {
    $market = market::byId(init('id'));
}
if (init('logicalId') != '') {
    $market = market::byLogicalId(init('logicalId'));
}
if (!isset($market)) {
    throw new Exception('404 not found');
}

include_file('3rdparty', 'bootstrap.rating/bootstrap.rating', 'js');

$rating = $market->getRating();
$market_array = utils::o2a($market);
$market_array['rating'] = $rating['average'];
sendVarToJS('market_display_info', $market_array);



if (config::byKey('installVersionDate', $market->getLogicalId()) != '' && config::byKey('installVersionDate', $market->getLogicalId()) < $market->getDatetime()) {
    echo '<div style="width : 100%" class="alert alert-warning" id="div_pluginUpdate">Une mise à jour est disponible. Cliquez sur installer pour l\'effectuer</div>';
}
?>

<div style="display: none;width : 100%" id="div_alertMarketDisplay"></div>
<?php if ($market->getPurchase() == 1) { ?>
    <a class="btn btn-warning pull-right" style="color : white;" id="bt_installFromMarket" data-market_id="<?php echo $market->getId(); ?>" ><i class="fa fa-plus-circle"></i> Installer</a>
    <?php
} else if (config::byKey('market::apikey') != '') {
    $purchase_info = market::getPurchaseInfo();
    if (count($purchase_info) == 3 && isset($purchase_info['user_id']) && is_numeric($purchase_info['user_id']) && isset($purchase_info['paypal::url']) && isset($purchase_info['paypal::marchandMail'])) {
        ?>
        <form action="<?php echo $purchase_info['paypal::url'] ?>/cgi-bin/webscr" method="post" style="display: inline-block;" class="pull-right" target="_blank" id='form_paypal'>
            <input type='hidden' name="amount" value="<?php echo $market->getRealcost() ?>" />
            <input name="currency_code" type="hidden" value="EUR" />
            <input name="shipping" type="hidden" value="0.00" />
            <input name="tax" type="hidden" value="0.00" />
            <input name="return" type="hidden" value="<?php echo config::byKey('market::address') . '/index.php?v=d&p=resultBuy&success=1' ?>" />
            <input name="cancel_return" type="hidden" value="<?php echo config::byKey('market::address') . '/index.php?v=d&p=resultBuy&success=0' ?>" />
            <input name="notify_url" type="hidden" value="<?php echo config::byKey('market::address') . '/index.php?v=d&p=registerBuy' ?>" />
            <input name="cmd" type="hidden" value="_xclick" />
            <input name="business" type="hidden" value="<?php echo $purchase_info['paypal::marchandMail'] ?>" />
            <input name="item_name" type="hidden" value="<?php echo '[' . $market->getType() . '] ' . $market->getLogicalId() ?>" />
            <input name="no_note" type="hidden" value="1" />
            <input name="lc" type="hidden" value="FR" />
            <input name="bn" type="hidden" value="PP-BuyNowBF" />
            <input name="custom" type="hidden" value="<?php echo $purchase_info['user_id'] . ':' . $market->getId() ?>" />
            <input class="pull-right" id='bt_paypalClick' alt="Effectuez vos paiements via PayPal : une solution rapide, gratuite et sécurisée" name="submit" src="https://www.paypal.com/fr_FR/FR/i/btn/btn_buynow_LG.gif" type="image" style="display: inline-block;"/><img class="pull-right" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" border="0" alt="" width="1" height="1" style="display: inline-block;"/>
        </form>
        <?php
    }
}
?>
<?php if (config::byKey('installVersionDate', $market->getLogicalId()) != '') { ?>
    <a class="btn btn-danger pull-right" style="color : white;" id="bt_removeFromMarket" data-market_id="<?php echo $market->getId(); ?>" ><i class="fa fa-minus-circle"></i> Supprimer</a>
<?php } ?>
<a class="btn btn-default pull-right" id="bt_viewComment"><i class="fa fa-comments-o"></i> Commentaires</a>
<br/><br/><br/>
<form class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-6">
            <?php if (config::byKey('market::apikey') != '') { ?>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Ma Note</label>
                    <div class="col-lg-8">
                        <span><input type="number" class="rating" id="in_myRating" data-max="5" data-empty-value="0" data-min="1" data-clearable="Effacer" value="<?php echo $rating['user'] ?>" /></span>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <label class="col-lg-4 control-label">Note</label>
                <div class="col-lg-8">
                    <input class="form-control marketAttr" data-l1key="id" style="display: none;">
                    <span class="label label-primary marketAttr" data-l1key="rating" style="font-size: 1.2em;"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Prix</label>
                <div class="col-lg-8">
                    <?php
                    if ($market->getCost() > 0) {
                        echo '<span class="label label-primary" data-l1key="rating" style="font-size: 1.2em;">' . $market->getRealcost() . ' €</span>';
                    } else {
                        echo '<span class="label label-primary" data-l1key="rating" style="font-size: 1.2em;">Gratuit</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">ID</label>
                <div class="col-lg-8">
                    <input class="form-control marketAttr" data-l1key="id" style="display: none;">
                    <span class="label label-success marketAttr" data-l1key="logicalId" placeholder="Nom"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Nom</label>
                <div class="col-lg-8">
                    <span class="label label-success marketAttr" data-l1key="name" placeholder="Nom"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Type</label>
                <div class="col-lg-8">
                    <select class="form-control marketAttr" data-l1key="type" disabled>
                        <option value="plugin">Plugin</option>
                        <option value="widget">Widget</option>
                        <option value="zwave_module">[Zwave] Configuration module</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Auteur</label>
                <div class="col-lg-8">
                    <span class="label label-success" ><?php echo $market->getAuthor() ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Description</label>
                <div class="col-lg-8">
                    <pre class="marketAttr" data-l1key="description" style="word-wrap: break-word;white-space: -moz-pre-wrap;white-space: pre-wrap;" ></pre>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Utilisation</label>
                <div class="col-lg-8">
                    <pre class="marketAttr" data-l1key="utilization" style="word-wrap: break-word;white-space: -moz-pre-wrap;white-space: pre-wrap;" ></pre>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Changelog</label>
                <div class="col-lg-8">
                    <pre class="marketAttr" data-l1key="changelog" style="word-wrap: break-word;white-space: -moz-pre-wrap;white-space: pre-wrap;" ></pre>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Statut</label>
                <div class="col-lg-8">
                    <select class="form-control marketAttr" data-l1key="status" disabled>
                        <option>A valider</option>
                        <option>Validé</option>
                        <option>Refusé</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Catégorie</label>
                <div class="col-lg-8">
                    <span class="label label-warning marketAttr" data-l1key="categorie" placeholder="Catégorie"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Version</label>
                <div class="col-lg-8">
                    <span class="label label-success marketAttr" data-l1key="version" placeholder="Version" ></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Dernière modification de l'archive</label>
                <div class="col-lg-6">
                    <span class="marketAttr label label-info" data-l1key="datetime"></span>
                </div>
            </div>
            <?php if (config::byKey('installVersionDate', $market->getName()) != '') { ?>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Version utilisé actuelement</label>
                    <div class="col-lg-6">
                        <span class="marketAttr label label-info" ><?php echo config::byKey('installVersionDate', $market->getLogicalId()); ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <label class="col-lg-4 control-label">Nombre de téléchargements</label>
                <div class="col-lg-8">
                    <span class="marketAttr label label-info" data-l1key="downloaded"></span>
                </div>
            </div>
        </div> 
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-lg-12">
                    <?php
                    $urlPath = config::byKey('market::address') . '/market/' . $market->getType() . '/' . $market->getLogicalId() . '.jpg';
                    if (fopen($urlPath, "r")) {
                        ?>
                        <img   src="<?php echo $urlPath; ?>"  class="img-responsive img-thumbnail" />
                    <?php } ?>
                </div>
            </div>
        </div> 
    </div> 
</form>

<div id="div_comments" title="Commentaires"></div>

<script>
    $('body').setValues(market_display_info, '.marketAttr');

    $('#bt_paypalClick').on('click', function() {
        $(this).hide();
    });

    $("#div_comments").dialog({
        autoOpen: false,
        modal: true,
        height: (jQuery(window).height() - 300),
        width: 600,
        position: {my: 'center', at: 'center', of: window},
        open: function() {
            if ((jQuery(window).width() - 50) < 1500) {
                $('#md_modal').dialog({width: jQuery(window).width() - 50});
            }
        }
    });

    $('#bt_viewComment').on('click', function() {
        reloadMarketComment();
        $('#div_comments').dialog('open');
    });


    function reloadMarketComment() {
        $('#div_comments').load('index.php?v=d&modal=market.comment&id=' + $('.marketAttr[data-l1key=id]').value());
    }

    $('#bt_installFromMarket').on('click', function() {
        var id = $(this).attr('data-market_id');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/market.ajax.php", // url du fichier php
            data: {
                action: "install",
                id: id
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertMarketDisplay').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                /* bootbox.confirm('L\'installation a été réalisée avec succès. La page doit etre rafraichir mais toute les données non sauvegardées seront perdu, voulez-vous continuer ?', function(result) {
                 if (result) {*/
                window.location.reload();
                /*  }
                 });*/
            }
        });
    });

    $('#bt_removeFromMarket').on('click', function() {
        var id = $(this).attr('data-market_id');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/market.ajax.php", // url du fichier php
            data: {
                action: "remove",
                id: id
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alertMarketDisplay').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                /*bootbox.confirm('La désinstallation a été réalisée avec succès. La page doit etre rafraichir mais toute les données non sauvegardées seront perdu, voulez-vous continuer ?', function(result) {
                 if (result) {*/
                window.location.reload();
                /*      }
                 });*/
            }
        });
    });



    $('#in_myRating').on('change', function() {
        var id = $('.marketAttr[data-l1key=id]').value();
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "core/ajax/market.ajax.php", // url du fichier php
            data: {
                action: "setRating",
                id: id,
                rating: $(this).val()
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error);
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_alert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
            }
        });
    });
</script>