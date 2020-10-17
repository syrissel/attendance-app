use clockin_db;

ALTER TABLE `attendances` ADD `arrive_late` DATETIME NULL AFTER `partial_day`, ADD `leave_early` DATETIME NULL AFTER `arrive_late`;
UPDATE `attendances` SET pto = (pto * 60) WHERE pto > 0;