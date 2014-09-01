ALTER TABLE `jeedom`.`cron` 
ADD COLUMN `priority` INT(11) NULL DEFAULT NULL AFTER `id`;
