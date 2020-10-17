use usermanagement;

ALTER TABLE `attendances` ADD `non_paid_in` DATETIME NULL AFTER `afternoonout`, ADD `non_paid_out` DATETIME NULL AFTER `non_paid_in`;