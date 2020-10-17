use usermanagement;

ALTER TABLE `settings` ADD `service_team_phone` VARCHAR(15) NULL AFTER `notice_content`, ADD `service_team_toll_free` VARCHAR(15) NULL AFTER `service_team_phone`;
