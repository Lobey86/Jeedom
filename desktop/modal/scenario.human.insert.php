<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<select id="mod_insertScenariocValue_value">
    <?php
    foreach (scenario::all() as $scenario) {
        echo '<option value="#' . $scenario->getHumanName(true) . '#">' . $scenario->getHumanName(true) . '</option>';
    }
    ?>
</select>
<script>
    function mod_insertScenario() {
    }

    mod_insertScenario.getValue = function() {
        return $('#mod_insertScenariocValue_value').value();
    }
</script>
