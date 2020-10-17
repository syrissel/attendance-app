use clockin_db;

ALTER TABLE `users` ADD `expected_work_hours` INT NOT NULL DEFAULT '75' AFTER `expected_clockout`;
ALTER TABLE `users` ADD `payroll_id` VARCHAR(100) NULL AFTER `expected_work_hours`;
