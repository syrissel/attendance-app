use clockin_db;

ALTER TABLE `attendances` ADD `arrive_early` DATETIME NULL AFTER `partial_day`;