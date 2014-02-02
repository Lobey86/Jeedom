<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>

<div class="form-group cmd" data-mode="!" data-type="d" data-value="1">
    <input type="text" class="cmdAttr form-control" data-l1key="id" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="eventOnly" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="unite" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="isHistorized" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="type" value="action" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="subType" value="binary" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="mode" value="!" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="type" value="d" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="value" value="1" style="display: none;"/>

    <label class="col-lg-2 control-label" >Nom écriture "1"</label>
    <div class="col-lg-2">
        <input type="text" class="cmdAttr form-control" data-l1key="name" value="On"/>
    </div>
</div>
<div class="form-group cmd" data-type="!" data-mode="d" data-value="1">
    <input type="text" class="cmdAttr form-control" data-l1key="id" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="eventOnly" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="unite" value="" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="isHistorized" value="0" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="type" value="action" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="subType" value="binary" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="mode" value="!" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="type" value="d" style="display: none;"/>
    <input type="text" class="cmdAttr form-control" data-l1key="configuration" data-l2key="value" value="0" style="display: none;"/>

    <label class="col-lg-2 control-label" >Nom écriture "0"</label>
    <div class="col-lg-2">
        <input type="text" class="cmdAttr form-control" data-l1key="name" value="Off"/>
    </div>
</div>