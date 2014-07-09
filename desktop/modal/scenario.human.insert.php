<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<select id="mod_insertScenariocValue_value">
    <?php
    foreach (scenario::all() as $scenario) {
        echo '<option>' . $scenario->getHumanName() . '</option>';
    }
    ?>
</select>
<script>
    function mod_insertScenario() {
    }

    mod_insertScenario.getValue = function() {
        return '#[' + object_name + '][' + group_name + '][' + scenario_name + ']#';
    }
</script>
