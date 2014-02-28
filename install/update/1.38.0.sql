ALTER TABLE `cron` 
ADD UNIQUE INDEX `class_function` (`class` ASC, `function` ASC);
