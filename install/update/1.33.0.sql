ALTER TABLE `config` 
CHANGE COLUMN `module` `plugin` VARCHAR(127) NOT NULL DEFAULT 'core' ;

ALTER TABLE `jeedom`.`message` 
CHANGE COLUMN `module` `plugin` VARCHAR(127) NOT NULL ;