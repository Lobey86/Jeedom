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
if (init('id') == '') {
    throw new Exception('EqLogic ID ne peut etre vide');
}
$eqLogic = eqLogic::byId(init('id'));
if (!is_object($eqLogic)) {
    throw new Exception('EqLogic non trouvé');
}
$device = zwave::devicesParameters($eqLogic->getConfiguration('device'));

if (!is_array($device) || count($device) == 0) {
    throw new Exception('Equipement inconnu : ' . $eqLogic->getConfiguration('device'));
}

sendVarToJS('configureDeviceId', init('id'));
?>
<div id='div_configureDeviceAlert' style="display: none;"></div>
<form class="form-horizontal">
    <fieldset>
        <legend>Informations <a class="btn btn-success btn-xs pull-right" style="color : white;" id="bt_configureDeviceSend"><i class="fa fa-check"></i> Appliquer</a></legend>

        <div class="form-group">
            <label class="col-lg-3 control-label">Nom de l'équipement</label>
            <div class="col-lg-8">
                <span class="tooltips label label-default"><?php echo $device['name'] ?></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-3 control-label">Marque</label>
            <div class="col-lg-8">
                <span class="tooltips label label-default"><?php echo $device['vendor'] ?></span>
            </div>
        </div>

        <legend>Configuration</legend>
        <div class="alert alert-info">Certaines valeur de configurations peuvent mettre plusieurs minutes à arriver lors de la premiere récuperation
            <a class="btn btn-warning bt_forceRefresh pull-right btn-xs" style="color : white;"><i class="fa fa-refresh"></i> Forcer la mise à jour</a>
        </div>
        <div id="div_configureDeviceParameters">
            <?php
            foreach ($device['parameters'] as $id => $parameter) {
                echo '<div class="form-group">';
                echo '<label class="col-lg-1 control-label tooltips" title="' . $parameter['description'] . '"><span class="tooltips label label-warning zwaveParameters">' . $id . '</span></label>';
                echo '<label class="col-lg-3 control-label tooltips" title="' . $parameter['description'] . '">' . $parameter['name'] . '</span></label>';
                echo '<div class="col-lg-3">';
                switch ($parameter['type']) {
                    case 'input':
                        echo '<input class="zwaveParameters form-control" data-l1key="' . $id . '" data-l2key="value"/>';
                        break;
                    case 'select':
                        echo '<select class = "zwaveParameters form-control" data-l1key="' . $id . '" data-l2key="value">';
                        foreach ($parameter['value'] as $value => $details) {
                            echo '<option value="' . $value . '" data-description="' . $details['description'] . '">' . $details['name'] . '</option>';
                        }
                        echo '</select>';
                        break;
                }
                echo '</div>';
                echo '<div class="col-lg-2">';
                if (isset($parameter['unite'])) {
                    echo '<span class="tooltips label label-primary tooltips" title="Unité">' . $parameter['unite'] . '</span> ';
                }
                if (isset($parameter['min']) || isset($parameter['max'])) {
                    echo '<span class="tooltips label label-primary tooltips" title="[min-max]">[' . $parameter['min'] . '-' . $parameter['max'] . ']</span> ';
                }

                if (isset($parameter['default'])) {
                    echo '<span class="tooltips label label-primary tooltips" title="Défaut">' . $parameter['default'] . '</span> ';
                }
                echo '<span class="tooltips label label-default zwaveParameters" data-l1key="' . $id . '" data-l2key="size" title="Taille en byte"></span> ';
                echo '<span class="tooltips label label-info zwaveParameters" data-l1key="' . $id . '" data-l2key="datetime" title="Date"></span>';
                echo '</div>';
                echo '<div class="col-lg-3">';
                echo '<span class="tooltips description"></span> ';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </fieldset>
</form>

<script>
    activateTooltips();
    configureDeviceLoad();

    $('select.zwaveParameters').on('change', function() {
        $(this).closest('.form-group').find('.description').html($(this).find('option:selected').attr('data-description'));
    });

    $('.bt_forceRefresh').on('click', function() {
        configureDeviceLoad(true);
    });

    function configureDeviceLoad(_forceRefresh) {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
            data: {
                action: "getDeviceConfiguration",
                id: configureDeviceId,
                forceRefresh: init(_forceRefresh, false)
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_configureDeviceAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_configureDeviceAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_configureDeviceParameters').setValues(data.result, '.zwaveParameters');
            }
        });
    }



    $('#bt_configureDeviceSend').on('click', function() {
        var configurations = $('#div_configureDeviceParameters').getValues('.zwaveParameters');
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/zwave/core/ajax/zwave.ajax.php", // url du fichier php
            data: {
                action: "setDeviceConfiguration",
                id: configureDeviceId,
                configurations: json_encode(configurations[0])
            },
            dataType: 'json',
            error: function(request, status, error) {
                handleAjaxError(request, status, error, $('#div_configureDeviceAlert'));
            },
            success: function(data) { // si l'appel a bien fonctionné
                if (data.state != 'ok') {
                    $('#div_configureDeviceAlert').showAlert({message: data.result, level: 'danger'});
                    return;
                }
                $('#div_configureDeviceAlert').showAlert({message: 'Parrametres envoyés avec succes (la prise en compte peut prendre jsuqu\'à plusieurs minutes', level: 'success'});
            }
        });
    });

</script>
