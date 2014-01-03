<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>
<div class="form-group cmd" mode="?" type="p">
    <label class="col-lg-2 control-label" >Nom du capteur de pression</label>
    <input type="text" class="cmdAttr form-control" l1key="id" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="unite" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="eventOnly" value="1" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="type" value="action" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="subType" value="color" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="configuration" l2key="mode" value="?" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="configuration" l2key="type" value="p" style="display: none;"/>

    <div class="col-lg-2 " >
        <input type="text" class="cmdAttr form-control" l1key="name" value="Pression"/>
    </div>
    <label class="col-lg-1 control-label" >Historiser</label>
    <div class="col-lg-1" >
        <input class="cmdAttr form-control" l1key="isHistorized" type="checkbox" /> 
    </div>
</div>
