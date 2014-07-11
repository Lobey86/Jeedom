SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `jeedom`.`object` 
CHANGE COLUMN `configuration` `configuration` VARCHAR(8192) NULL DEFAULT NULL ,
CHANGE COLUMN `display` `display` VARCHAR(8192) NULL DEFAULT NULL ;

ALTER TABLE `jeedom`.`eqLogic` 
DROP COLUMN `specificCapatibilities`,
CHANGE COLUMN `configuration` `configuration` VARCHAR(8192) NULL DEFAULT NULL ,
CHANGE COLUMN `status` `status` VARCHAR(2048) NULL DEFAULT NULL ,
CHANGE COLUMN `category` `category` VARCHAR(1024) NULL DEFAULT NULL ;

ALTER TABLE `jeedom`.`cmd` 
CHANGE COLUMN `configuration` `configuration` VARCHAR(8192) BINARY NULL DEFAULT NULL ,
CHANGE COLUMN `template` `template` VARCHAR(2048) NULL DEFAULT NULL ,
CHANGE COLUMN `cache` `cache` VARCHAR(2048) NULL DEFAULT NULL ,
CHANGE COLUMN `display` `display` VARCHAR(2048) NULL DEFAULT NULL ;

CREATE TABLE IF NOT EXISTS `jeedom`.`object_persist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `father_id` INT(11) NULL DEFAULT NULL,
  `isVisible` TINYINT(1) NULL DEFAULT NULL,
  `position` INT(11) NULL DEFAULT NULL,
  `configuration` VARCHAR(8192) NULL DEFAULT NULL,
  `display` VARCHAR(8192) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `fk_object_object1_idx1` (`father_id` ASC),
  INDEX `position` (`position` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `jeedom`.`eqLogic_persist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(127) NOT NULL,
  `logicalId` VARCHAR(127) NULL DEFAULT NULL,
  `object_id` INT(11) NULL DEFAULT NULL,
  `eqType_name` VARCHAR(127) NOT NULL,
  `configuration` VARCHAR(8192) NULL DEFAULT NULL,
  `isVisible` TINYINT(1) NULL DEFAULT NULL,
  `eqReal_id` INT(11) NULL DEFAULT NULL,
  `isEnable` TINYINT(1) NULL DEFAULT NULL,
  `status` VARCHAR(2048) NULL DEFAULT NULL,
  `timeout` INT(11) NULL DEFAULT NULL,
  `category` VARCHAR(1024) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique` (`name` ASC, `object_id` ASC),
  INDEX `eqTypeName` (`eqType_name` ASC),
  INDEX `name` (`name` ASC),
  INDEX `logica_id_eqTypeName` (`logicalId` ASC, `eqType_name` ASC),
  INDEX `logical_id` (`logicalId` ASC),
  INDEX `object_id` (`object_id` ASC),
  INDEX `timeout` (`timeout` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `jeedom`.`cmd_persist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `eqLogic_id` INT(11) NOT NULL,
  `eqType` VARCHAR(127) NULL DEFAULT NULL,
  `logicalId` VARCHAR(127) NULL DEFAULT NULL,
  `order` INT(11) NULL DEFAULT NULL,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  `configuration` VARCHAR(8192) BINARY NULL DEFAULT NULL,
  `template` VARCHAR(2048) NULL DEFAULT NULL,
  `isHistorized` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NULL DEFAULT NULL,
  `subType` VARCHAR(45) NULL DEFAULT NULL,
  `cache` VARCHAR(2048) NULL DEFAULT NULL,
  `unite` VARCHAR(45) NULL DEFAULT NULL,
  `eventOnly` TINYINT(1) NULL DEFAULT 0,
  `display` VARCHAR(2048) NULL DEFAULT NULL,
  `isVisible` INT(11) NULL DEFAULT 1,
  `value` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique` (`eqLogic_id` ASC, `name` ASC),
  INDEX `isHistorized` (`isHistorized` ASC),
  INDEX `type` (`type` ASC),
  INDEX `eventOnly` (`eventOnly` ASC),
  INDEX `name` (`name` ASC),
  INDEX `subtype` (`subType` ASC),
  INDEX `eqLogic_id` (`eqLogic_id` ASC),
  INDEX `value` (`value` ASC),
  INDEX `order` (`order` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `jeedom`.`object` 
DROP FOREIGN KEY `fk_object_object1`;

ALTER TABLE `jeedom`.`eqLogic` 
DROP FOREIGN KEY `fk_eqLogic_object1`,
DROP FOREIGN KEY `fk_eqLogic_jeenode1`;

ALTER TABLE `jeedom`.`cmd` 
DROP FOREIGN KEY `fk_cmd_eqLogic1`;

ALTER TABLE `jeedom`.`scenario` 
DROP FOREIGN KEY `fk_scenario_object1`;

ALTER TABLE `jeedom`.`history` 
DROP FOREIGN KEY `fk_history_cmd1`;

ALTER TABLE `jeedom`.`historyArch` 
DROP FOREIGN KEY `fk_historyArch_cmd1`;

ALTER TABLE `jeedom`.`object` 
ENGINE = MEMORY ;

ALTER TABLE `jeedom`.`eqLogic` 
ENGINE = MEMORY ,
DROP INDEX `eqReal_id` ;

ALTER TABLE `jeedom`.`cmd` 
ENGINE = MEMORY ;

ALTER TABLE `jeedom`.`scenario` 
DROP INDEX `name` ,
ADD UNIQUE INDEX `name` (`group` ASC, `object_id` ASC, `name` ASC);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
