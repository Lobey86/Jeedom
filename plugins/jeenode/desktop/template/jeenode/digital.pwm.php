<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>

<div class="form-group cmd" mode="!" type="p">
    <input type="text" class="cmdAttr form-control" l1key="id" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="eventOnly" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="unite" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="isHistorized" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="type" value="action" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="subType" value="slider" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="configuration" l2key="mode" value="!" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" l1key="configuration" l2key="type" value="p" style="display: none;"/>

    <label class="col-lg-2 control-label" >PWM</label>
    <div class="col-lg-2">
        <input type="text" class="cmdAttr form-control" l1key="name" value="Slider"/>
    </div>
</div>