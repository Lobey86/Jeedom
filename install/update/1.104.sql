ALTER TABLE `jeedom`.`cron` 
ADD INDEX `deamon` (`deamon` ASC);

ALTER TABLE `jeedom`.`cache` 
ADD INDEX `lifetime` (`lifetime` ASC);

ALTER TABLE `jeedom`.`internalEvent` 
ADD INDEX `datetime` (`datetime` ASC);

ALTER TABLE `jeedom`.`cmd` 
ADD INDEX `type_eventOnly` (`type` ASC, `eventOnly` ASC);

ALTER TABLE `jeedom`.`message` 
ALTER TABLE `jeedom`.`connection` 
ADD INDEX `datetime` (`datetime` ASC);

ALTER TABLE `jeedom`.`connection` 
ADD INDEX `status_datetime` (`status` ASC, `datetime` ASC);  

ALTER TABLE `jeedom`.`scenario` 
ADD COLUMN `configuration` TEXT NULL DEFAULT NULL AFTER `description`;