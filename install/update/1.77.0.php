<?php

foreach (cmd::all() as $cmd) {
    $cmd->setEqType($cmd->getEqLogic()->getEqType_name());
    $cmd->save();
}
