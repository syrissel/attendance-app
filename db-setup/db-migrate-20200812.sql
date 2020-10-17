use usermanagement;

ALTER TABLE `attendances` CHANGE `comments` `partial_day` VARCHAR(20) CHARACTER SET utf8mb4 NULL DEFAULT NULL;
ALTER TABLE `attendances` ADD `intended_date` DATETIME NULL AFTER `absent_reason_id`;
ALTER TABLE `settings` ADD `clock_out_time` VARCHAR(10) NULL AFTER `site_logo`;