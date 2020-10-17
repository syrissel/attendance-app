use usermanagement;

--
-- Table structure for table `user_positions`
--

CREATE TABLE `user_positions` (
  `id` int NOT NULL,
  `position` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_positions`
--

INSERT INTO `user_positions` (`id`, `position`) VALUES
(1, 'Intern'),
(2, 'Full-time'),
(3, 'Executive Director'),
(4, 'Board Member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_positions`
--
ALTER TABLE `user_positions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_positions`
--
ALTER TABLE `user_positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

ALTER TABLE `users` ADD `user_position_id` INT NOT NULL AFTER `user_type_id`;

UPDATE `users` SET `user_position_id` = 1;

ALTER TABLE `users` ADD CONSTRAINT `fk_user_position_users` FOREIGN KEY (`user_type_id`) REFERENCES `user_positions`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
