use usermanagement_test;

CREATE TABLE `absent_reasons` ( `id` INT NOT NULL AUTO_INCREMENT , `reason` VARCHAR(30) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `absent_reasons` (`reason`) VALUES ('Vacation');
INSERT INTO `absent_reasons` (`reason`) VALUES ('Sick');

ALTER TABLE `attendances` ADD `absent_reason_id` INT NULL AFTER `comments`;

ALTER TABLE `attendances` ADD CONSTRAINT `fk_absent_reason_attendances` FOREIGN KEY (`absent_reason_id`) REFERENCES `absent_reasons`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `users` ADD `expected_clockin` VARCHAR(8) NOT NULL DEFAULT '08:30' AFTER `user_status`, ADD `expected_clockout` VARCHAR(8) NOT NULL DEFAULT '16:30' AFTER `expected_clockin`;
