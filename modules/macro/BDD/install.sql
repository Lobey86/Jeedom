CREATE TABLE IF NOT EXISTS `macro` (
  `macroCmd_id` INT(11) NOT NULL,
  `execute_cmd_id` INT NOT NULL,
  `order` INT NOT NULL,
  `option` TEXT NULL,
  PRIMARY KEY (`execute_cmd_id`, `order`, `macroCmd_id`),
  INDEX `fk_command_has_command_cmd2_idx` (`execute_cmd_id` ASC),
  INDEX `fk_command_has_command_cmd1_idx` (`macroCmd_id` ASC),
  CONSTRAINT `fk_command_has_command_cmd1`
    FOREIGN KEY (`macroCmd_id`)
    REFERENCES `cmd` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_command_has_command_cmd2`
    FOREIGN KEY (`execute_cmd_id`)
    REFERENCES `cmd` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
