<?php
if (!isConnect()) {
    throw new Exception('401 Unauthorized');
}

$scenario = scenario::byId(init('scenario_id'));
if (!is_object($scenario)) {
    throw new Exception('Scenario introuvable');
}
?>
<pre>
<?php
echo trim($scenario->getConsolidateLog());
?>
</pre>