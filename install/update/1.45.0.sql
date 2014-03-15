ALTER TABLE `jeedom`.`scenario` 
CHANGE COLUMN `isVisible` `isVisible` TINYINT(1) NULL DEFAULT 1 ,
CHANGE COLUMN `object_id` `object_id` INT(11) NULL DEFAULT NULL ,
ADD INDEX `fk_scenario_object1_idx` (`object_id` ASC),
DROP INDEX `fk_scenario_object1_idx` ;

ALTER TABLE `jeedom`.`scenario` 
ADD CONSTRAINT `fk_scenario_object1`
  FOREIGN KEY (`object_id`)
  REFERENCES `jeedom`.`object` (`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
