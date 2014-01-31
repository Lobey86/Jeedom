ALTER TABLE `eqLogic` 
CHANGE COLUMN `eqType_name` `plugin` VARCHAR(127) NOT NULL ;

ALTER TABLE `config` 
CHANGE COLUMN `module` `plugin` VARCHAR(127) NOT NULL DEFAULT 'core' ;

ALTER TABLE `jeedom`.`message` 
CHANGE COLUMN `module` `plugin` VARCHAR(127) NOT NULL ;