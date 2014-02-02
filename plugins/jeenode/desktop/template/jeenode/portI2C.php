<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>
<br/>
<div class="form-group">
    <label class="col-lg-2 control-label" >Type du port I2C</label>
    <div class="col-lg-2">
        <select class="sel_portType form-control" port="I2C">
            <option value='0'>Selectionner une option...</option>
            <option value='1' data-cmdName='i2c.blinkm' data-active_only='1'>Blink M</option>
            <option value='2' data-cmdName='i2c.pressure'>Pression</option>
        </select>
    </div>
    <div class="col-lg-2">
        <a class="btn btn-default" onClick="configurationPort($(this).closest('.form-group').find('.sel_portType'));">
            <i class="fa fa-plus-circle"></i> Ajouter
        </a>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-2 control-label" >Nom du port</label>
    <div class="col-lg-2">
        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display: none;"/>
        <input type="text" class="eqLogicAttr form-control" data-l1key="logicalId" value="#portNumber#" style="display: none;"/>
        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Nom du port I2C" disabled/>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-2 control-label" >Objet parent</label>
    <div class="col-lg-2">
        <?php
        echo '<select class="eqLogicAttr form-control" data-l1key="object_id" disabled>';
        foreach (object::all() as $object) {
            echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
        }
        echo '</select>';
        ?>
    </div>
</div>


<div class="form-group">
    <label class="col-lg-2 control-label" >Activer</label>
    <div class="col-lg-1">
        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" disabled checked/>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-2 control-label" >Visible</label>
    <div class="col-lg-1">
        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" disabled checked/>
    </div>
</div>

<div class="confSpePort" data-port="#portNumber#">


</div>