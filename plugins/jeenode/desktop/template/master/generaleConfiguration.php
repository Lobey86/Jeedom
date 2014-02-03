<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>

<form class="form-horizontal">
    <fieldset>
        <legend>Configuration master</legend>
        <div class="form-group">
            <label class="col-lg-2 control-label">Adresse mac</label>
            <div class="col-lg-2">
                <input type="text" class="configuration form-control" data-l1key="MAC" placeholder="Adresse mac du master"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Adresse IP</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="IP" placeholder="Adresse ip du master"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Gateway</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="gateway" placeholder="Passerelle pour le master" value="192.168.1.1"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">DNS</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="dns" placeholder="DNS pour le master" value="192.168.1.1"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Adresse de Jeedom</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="addrJeedom" placeholder="Adresse de Jeedom" value=""/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Timeout</label>
            <div class="col-lg-2">
                <input type="text" class="eqRealAttr form-control" data-l1key="configuration" data-l2key="timeout" placeholder="Timeout" value="1500"/>
            </div>
        </div>
    </fieldset>
</form>

