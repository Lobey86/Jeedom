<?php
require_once(dirname(__FILE__) . '/../../../../../core/php/core.inc.php');
include_file('core', 'authentification', 'php');

if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}
?>
<br/>
<div class="form-group">
    <label class="col-lg-2 control-label" >Type du port</label>
    <div class="col-lg-3">
        <select port="#portNumber#" class="form-control sel_portType">
            <option value='0'>Selectionner une option...</option>
            <option cmdName='analogique.read' value='2' exclude='2,3,4,102'>[2] Entrée port analogique</option>
            <option cmdName='ldr' value='4' exclude='2,3,4,102'>[4] LDR</option>
            <option cmdName='sh11' value='3' exclude='*'>[3] SH11</option>
            <option cmdName='pir' value='51' exclude='3,51,52,53,101,102,103'>[51] PIR</option>
            <option cmdName='digital.read' value='52' exclude='3,51,52,53,101,102,103'>[52] Entrée port digital (évènement)</option>
            <option cmdName='digital.impulsion' value='53' exclude='3,51,52,53,101,102,103'>[53] Impulsion</option>
            <option cmdName='digital.write' value='101' active_only='1' exclude='3,51,52,53,101,102,103'>[101] Sortie port digital</option>
            <option cmdName='analogique.write' value='102' active_only='1' exclude='2,3,4,102'>[102] Sortie port analogique</option>
            <option cmdName='digital.pwm' value='103' active_only='1' exclude='3,51,52,53,101,102,103'>[103] Sortie PWM port digital</option>
        </select>
    </div>
    <div class="col-lg-2">
        <a class="btn btn-default" onClick="configurationPort($(this).closest('.form-group').find('.sel_portType'))">
            <i class="fa fa-plus-circle"></i> Ajouter
        </a>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-2 control-label" >Nom du port</label>
    <div class="col-lg-2">
        <input type="text" class="eqLogicAttr form-control" l1key="id" style="display: none;"/>
        <input type="text" class="eqLogicAttr form-control" l1key="timeout" value="30" style="display: none;"/>
        <input type="text" class="eqLogicAttr form-control" l1key="logicalId" value="#portNumber#" style="display: none;"/>
        <input type="text" class="eqLogicAttr form-control" l1key="name" placeholder="Nom du port" disabled/>
    </div>
</div>

<div class="form-group">
    <label class="col-lg-2 control-label" >Objet parent</label>
    <div class="col-lg-2">
        <?php
        echo '<select class="eqLogicAttr form-control" l1key="object_id" disabled>';
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
        <input type="checkbox" class="eqLogicAttr" l1key="isEnable" disabled checked/>
    </div>
</div>
<div class="form-group">
    <label class="col-lg-2 control-label" >Visible</label>
    <div class="col-lg-1">
        <input type="checkbox" class="eqLogicAttr" l1key="isVisible" disabled checked/>
    </div>
</div>


<div class="confSpePort" port="#portNumber#">


</div>