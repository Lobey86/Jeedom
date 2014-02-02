<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>

<script>
    var listPort = new Array('1', '2', '3', '4', 'I2C');
</script>

<form class="form-horizontal">
    <fieldset>
        <legend>Global</legend>
        <div class="form-group">
            <label class="col-lg-2 control-label">Node ID master</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="masterId" placeholder="ID du master"/>
            </div>
        </div>
    </fieldset>
</form>
<hr>
<ul class="nav nav-tabs">
    <li class="active"><a href="#div_jeenodePort1" data-toggle="tab">Port 1</a></li>
    <li><a href="#div_jeenodePort2" data-toggle="tab">Port 2</a></li>
    <li><a href="#div_jeenodePort3" data-toggle="tab">Port 3</a></li>
    <li><a href="#div_jeenodePort4" data-toggle="tab">Port 4</a></li>
    <li><a href="#div_jeenodePortI2C" data-toggle="tab">Port I2C</a></li>
</ul>
<div class="tab-content">
    <div id="div_jeenodePort1" class="tab-pane active eqLogic" data-port="1">
        <form class="form-horizontal">
            <fieldset>
                <script>
                    var replace = new Array();
                    replace['#portNumber#'] = '1';
                    $('#div_jeenodePort1 fieldset').html(getTemplate('jeenode', 'jeenode', 'port.php', replace));
                </script>
            </fieldset>
        </form>
    </div> 
    <div id="div_jeenodePort2" class="tab-pane eqLogic" data-port="2">
        <form class="form-horizontal">
            <fieldset>
                <script>
                    var replace = new Array();
                    replace['#portNumber#'] = '2';
                    $('#div_jeenodePort2 fieldset').html(getTemplate('jeenode', 'jeenode', 'port.php', replace));
                </script>
            </fieldset>
        </form>
    </div> 
    <div id="div_jeenodePort3" class="tab-pane eqLogic" data-port="3">
        <form class="form-horizontal">
            <fieldset>
                <script>
                    var replace = new Array();
                    replace['#portNumber#'] = '3';
                    $('#div_jeenodePort3 fieldset').html(getTemplate('jeenode', 'jeenode', 'port.php', replace));
                </script>
            </fieldset>
        </form>
    </div> 
    <div id="div_jeenodePort4" class="tab-pane eqLogic" data-port="4">
        <form class="form-horizontal">
            <fieldset>
                <script>
                    var replace = new Array();
                    replace['#portNumber#'] = '4';
                    $('#div_jeenodePort4 fieldset').html(getTemplate('jeenode', 'jeenode', 'port.php', replace));
                </script>
            </fieldset>
        </form>
    </div>
    <div id="div_jeenodePortI2C" class="tab-pane eqLogic" data-port="I2C">
        <form class="form-horizontal">
            <fieldset>
                <script>
                    var replace = new Array();
                    replace['#portNumber#'] = 'I2C';
                    $('#div_jeenodePortI2C fieldset').html(getTemplate('jeenode', 'jeenode', 'portI2C.php', replace));
                </script>
            </fieldset>
        </form>
    </div>
</div>


<script>
    var generaleConfData = ('#generaleConfData#' != '') ? jQuery.parseJSON('#generaleConfData#') : null;
    if (generaleConfData != null) {
        for (var k in generaleConfData.port) {
            var port = $('#div_jeenodePort' + generaleConfData.port[k].logicalId);
            for (var key in generaleConfData.port[k]) {
                if (is_object(generaleConfData.port[k][key]) || is_array(generaleConfData.port[k][key])) {
                    for (var subkey in generaleConfData.port[k][key]) {
                        port.find('.eqLogicAttr[data-l1key="' + key + '"][data-l2key="' + subkey + '"]').value(generaleConfData.port[k][key][subkey]);
                    }
                } else {
                    port.find('.eqLogicAttr[data-l1key="' + key + '"]').value(generaleConfData.port[k][key]);
                }
            }

            for (var m in generaleConfData.port[k].configuration.portType) {
                if (generaleConfData.port[k].configuration.portType[m] != 0) {
                    port.find('.sel_portType').value(generaleConfData.port[k].configuration.portType[m]);
                    configurationPort(port.find('.sel_portType'), generaleConfData.port[k].cmd);
                }
            }
        }
    }

    $('.eqLogic').delegate('.removePortType', 'click', function() {
        var portType = $(this).closest('.portType');
        var eqLogic = portType.closest('.eqLogic');
        var sel_portType = eqLogic.find('.sel_portType');
        sel_portType.find('option').prop('disabled', false);
        portType.remove();
        eqLogic.find('.confSpePort .portType').each(function() {
            excludePortType(sel_portType, sel_portType.find('option[value=' + $(this).attr('data-code') + ']'));
        });
    });

    function excludePortType(_select, _option) {
        var exclude = null;
        if (!isset(_option)) {
            _option = _select.find('option:selected');
        }
        exclude = _option.attr('data-exclude');
        if (exclude != null) {
            if (exclude == '*') {
                _select.find('option').prop('disabled', true);
                _select.find('option:first').prop('disabled', false);
            } else {
                exclude = exclude.split(',');
                for (var i in exclude) {
                    _select.find('option[value=' + exclude[i] + ']').prop('disabled', true);
                }
            }
        }
    }

    function configurationPort(_select, _cmd) {
        var portType = _select.find('option:selected').attr('data-cmdName');
        var jeenodeType = $('.eqRealAttr[data-l1key=type]').value();
        if (portType == undefined || portType == '' || jeenodeType == '') {
            $('#div_alert').showAlert({message: 'Veuillez selectionner un type de port', level: 'warning'});
            return;
        }
        var logicalId = _select.attr('data-port');

        var port = $('#div_jeenodePort' + logicalId);
        if (portType != 'none') {
            port.find('.eqLogicAttr').prop('disabled', false);
        } else {
            port.find('.eqLogicAttr').prop('disabled', true);
            port.find('.sel_portType').prop('disabled', false);
        }
        var confSpePort = port.find('.confSpePort');
        var portConfiguration = '<div class="portType well" data-code="' + _select.value() + '">';
        portConfiguration += '<a class="btn btn-danger pull-right removePortType"><i class="fa fa-minus-circle"></i> Supprimer</a>';
        portConfiguration += getTemplate('jeenode', 'jeenode', portType + '.php');
        portConfiguration += '</div>';
        confSpePort.prepend(portConfiguration);

        if (isset(_cmd)) {
            for (var i in _cmd) {
                if (isset(_cmd[i].configuration)) {

                    var cmd = null;
                    if (isset(_cmd[i].configuration.value)) {
                        cmd = confSpePort.find('.cmd[data-mode="' + _cmd[i].configuration.mode + '"][data-type="' + _cmd[i].configuration.type + '"][data-value="' + _cmd[i].configuration.value + '"]');
                    } else {
                        cmd = confSpePort.find('.cmd[data-mode="' + _cmd[i].configuration.mode + '"][data-type="' + _cmd[i].configuration.type + '"]');
                    }
                    if (cmd != null && cmd.length == 1) {
                        for (var key in _cmd[i]) {
                            if (is_array(_cmd[i][key]) || is_object(_cmd[i][key])) {
                                for (var subkey in _cmd[i][key]) {
                                    cmd.find('.cmdAttr[data-l1key="' + key + '"][data-l2key="' + subkey + '"]').value(_cmd[i][key][subkey]);
                                }
                            } else {
                                cmd.find('.cmdAttr[data-l1key="' + key + '"]').value(_cmd[i][key]);
                            }
                        }
                    }
                }
            }
        }
        excludePortType(_select);
        _select.find('option:first').prop('selected', true);
    }
</script>
