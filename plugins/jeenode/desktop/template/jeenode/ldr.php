<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>

<div class="form-group cmd" data-mode="?" data-type="a">
    <label class="col-lg-2 control-label" >Nom du capteur de lumière</label>
    <input type="text" class="cmdAttr form-control" data-l1key="id" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="unite" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="eventOnly" value="1" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="type" value="info" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="subType" value="numeric" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="mode" value="?" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="type" value="a" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="minValue" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="maxValue" value="255" style="display: none;"/>

    <div class="col-lg-2" >
        <input type="text" class="cmdAttr form-control" data-l1key="name" value="Luminosité"/>
    </div>
    <label class="col-lg-1 control-label" >Historiser</label>
    <div class="col-lg-1" >
        <input class="cmdAttr form-control" data-l1key="isHistorized" type="checkbox" /> 
    </div>
</div>