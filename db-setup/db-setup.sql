/*
Clock-in App Database Setup Script. This script will initialize Database structure
required for the app to function.

Author: Steph Mireault
Date:   July 23, 2020
*/

SET @create_user = CONCAT("CREATE USER 'clockin_app'@'localhost' IDENTIFIED BY '", @userpass, "'");

CREATE DATABASE IF NOT EXISTS `clockin_db`;
USE `clockin_db`;
DROP TABLE IF EXISTS `attendances`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `user_types`;
DROP TABLE IF EXISTS `user_positions`;
DROP TABLE IF EXISTS `settings`;

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `clockin` datetime DEFAULT NULL,
  `clockout` datetime DEFAULT NULL,
  `morningin` datetime DEFAULT NULL,
  `morningout` datetime DEFAULT NULL,
  `lunchin` datetime DEFAULT NULL,
  `lunchout` datetime DEFAULT NULL,
  `afternoonin` datetime DEFAULT NULL,
  `afternoonout` datetime DEFAULT NULL,
  `present` char(1) NOT NULL DEFAULT 'Y',
  `comments` varchar(128) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_logo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_logo`) VALUES
(1, 'Clock-in App');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pin` varchar(60) NOT NULL,
  `in_building` char(1) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `overtime` varchar(10) DEFAULT NULL,
  `organization` varchar(30) DEFAULT NULL,
  `user_status` varchar(10) NOT NULL DEFAULT 'active',
  `user_type_id` int(11) NOT NULL,
  `user_position_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_positions` (
  `id` int(11) NOT NULL,
  `position` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_positions`
--

INSERT INTO `user_positions` (`id`, `position`) VALUES
(1, 'Intern'),
(2, 'Full-time'),
(3, 'Executive Director');

CREATE TABLE `user_types` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `type`) VALUES
(1, 'User'),
(2, 'Admin'),
(3, 'Guest');

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_attendances` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_type_users` (`user_type_id`),
  ADD KEY `fk_user_position_users` (`user_position_id`);

--
-- Indexes for table `user_positions`
--
ALTER TABLE `user_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;
--
-- AUTO_INCREMENT for table `user_positions`
--
ALTER TABLE `user_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `attendances`
  ADD CONSTRAINT `fk_user_attendances` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_position_users` FOREIGN KEY (`user_position_id`) REFERENCES `user_positions` (`id`),
  ADD CONSTRAINT `fk_user_type_users` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`);

-- Disables guests at 8 AM every day.
CREATE EVENT IF NOT EXISTS `disable_guests` 
  ON SCHEDULE EVERY 1 DAY STARTS '2020-07-23 08:00:00.000000' 
  ON COMPLETION NOT PRESERVE 
  ENABLE 
  DO 
    UPDATE `users` SET `user_status` = `inactive`
    WHERE `user_type_id` = 3;

-- Sets everyone's clockout to 4:30 PM if they haven't clocked out already.
-- Runs everyday at 4:00 PM 
CREATE EVENT IF NOT EXISTS `clockout_employees` 
  ON SCHEDULE EVERY 1 DAY STARTS '2020-07-23 16:00:00.000000' 
  ON COMPLETION NOT PRESERVE 
  ENABLE 
  DO 
    UPDATE `attendances` 
    SET `clockout` = DATE_FORMAT(NOW(), '%Y-%m-%d 16:30:00')
    WHERE DATE_FORMAT(`clockin`, '%Y%m%d') = DATE_FORMAT(NOW(), '%Y%m%d')
    AND `clockout` IS NULL;

DROP USER IF EXISTS `clockin_app`@`localhost`;
PREPARE stmt FROM @create_user;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
GRANT SELECT, INSERT, UPDATE, DELETE ON `clockin_db`.* TO 'clockin_app'@'localhost';
FLUSH PRIVILEGES;
