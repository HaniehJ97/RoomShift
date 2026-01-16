-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Jan 16, 2026 at 12:50 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `developmentdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `creator_id` int(11) NOT NULL DEFAULT 1,
  `is_published` tinyint(1) DEFAULT 0,
  `difficulty` varchar(20) DEFAULT 'medium',
  `estimated_time` int(11) DEFAULT 30,
  `level_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{"grid_width":12,"grid_height":12,"walls":[],"bombs":[],"key":{"x":0,"y":0},"door":{"x":11,"y":11}}' CHECK (json_valid(`level_config`)),
  `starting_state_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `description`, `created_at`, `creator_id`, `is_published`, `difficulty`, `estimated_time`, `level_config`, `starting_state_id`, `updated_at`) VALUES
(1, 'escape room', 'something test something', '2025-11-22 15:59:06', 1, 1, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-13 12:35:04'),
(2, 'test room', 'fsdfghjg', '2025-11-24 12:06:52', 1, 1, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-13 12:35:08'),
(3, 'dfdsfs', 'sdfsdfsfsdfs', '2025-11-29 15:52:19', 1, 1, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-13 12:35:11'),
(4, 'sdsdfds', 'sfsdfsfdsfds', '2025-11-29 16:10:42', 1, 1, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-13 12:35:14'),
(5, 'Snake room', 'you are an snake, on your way there are 2 walls and 3 bombs ', '2026-01-12 14:31:08', 5, 1, 'medium', 30, '{\"grid_width\": 10, \"grid_height\": 10, \"difficulty\": \"medium\", \"walls\": [{\"x\": 1, \"y\": 3}, {\"x\": 1, \"y\": 5}, {\"x\": 1, \"y\": 7}, {\"x\": 1, \"y\": 6}, {\"x\": 1, \"y\": 4}, {\"x\": 1, \"y\": 2}, {\"x\": 5, \"y\": 2}, {\"x\": 5, \"y\": 4}, {\"x\": 5, \"y\": 5}, {\"x\": 5, \"y\": 3}, {\"x\": 7, \"y\": 2}, {\"x\": 6, \"y\": 2}, {\"x\": 6, \"y\": 5}, {\"x\": 8, \"y\": 7}, {\"x\": 7, \"y\": 7}, {\"x\": 7, \"y\": 8}, {\"x\": 7, \"y\": 9}], \"bombs\": [{\"x\": 3, \"y\": 0}, {\"x\": 3, \"y\": 1}, {\"x\": 3, \"y\": 8}, {\"x\": 3, \"y\": 9}], \"key\": {\"x\": 6, \"y\": 4}, \"door\": {\"x\": 9, \"y\": 9}}', NULL, '2026-01-12 17:29:22'),
(6, 'small room', 'room full of bombs', '2026-01-12 16:18:28', 5, 0, 'hard', 30, '{\"grid_width\": 10, \"grid_height\": 10, \"difficulty\": \"hard\", \"walls\": [], \"bombs\": [{\"x\": 2, \"y\": 2}, {\"x\": 2, \"y\": 4}, {\"x\": 2, \"y\": 5}, {\"x\": 2, \"y\": 7}, {\"x\": 3, \"y\": 9}, {\"x\": 6, \"y\": 5}, {\"x\": 7, \"y\": 3}, {\"x\": 8, \"y\": 2}, {\"x\": 6, \"y\": 8}, {\"x\": 6, \"y\": 4}], \"key\": {\"x\": 0, \"y\": 0}, \"door\": {\"x\": 9, \"y\": 9}}', NULL, '2026-01-12 17:29:22'),
(7, 'sfdsfdsf', 'dfdsfdsf', '2026-01-12 17:17:07', 5, 0, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-12 17:17:07'),
(8, 'sdfsfs', 'sfdsfsdfdsfsdf', '2026-01-12 17:17:14', 5, 1, 'medium', 30, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-12 17:17:14'),
(9, 'test room', 'this is just a simple test', '2026-01-12 17:50:44', 5, 1, 'easy', 15, '{\"grid_width\":14,\"grid_height\":14,\"walls\":[],\"bombs\":[],\"key\":{\"x\":0,\"y\":0},\"door\":{\"x\":11,\"y\":11}}', NULL, '2026-01-12 17:50:44'),
(10, 'text room', 'this is the test room with the girid', '2026-01-12 17:55:11', 5, 1, 'easy', 10, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[],\"bombs\":[{\"x\":3,\"y\":1},{\"x\":3,\"y\":2},{\"x\":3,\"y\":4},{\"x\":3,\"y\":7},{\"x\":3,\"y\":9}],\"key\":{\"x\":9,\"y\":5},\"door\":{\"x\":8,\"y\":8}}', NULL, '2026-01-12 17:57:22'),
(11, 'escape room 1', 'there will be 2 row of walls, 4 bombs , you must follow the walls to find the key to the door, be careful for the bombs', '2026-01-13 11:11:30', 5, 1, 'medium', 5, '{\"grid_width\":12,\"grid_height\":12,\"walls\":[{\"x\":2,\"y\":1},{\"x\":2,\"y\":2},{\"x\":2,\"y\":3},{\"x\":2,\"y\":4},{\"x\":2,\"y\":5},{\"x\":4,\"y\":1},{\"x\":4,\"y\":2},{\"x\":4,\"y\":3},{\"x\":4,\"y\":4},{\"x\":4,\"y\":5},{\"x\":6,\"y\":1},{\"x\":6,\"y\":2},{\"x\":6,\"y\":3},{\"x\":6,\"y\":4},{\"x\":6,\"y\":5},{\"x\":8,\"y\":1},{\"x\":8,\"y\":2},{\"x\":8,\"y\":3},{\"x\":8,\"y\":4},{\"x\":8,\"y\":5},{\"x\":2,\"y\":7},{\"x\":2,\"y\":8},{\"x\":2,\"y\":9},{\"x\":2,\"y\":10},{\"x\":4,\"y\":7},{\"x\":4,\"y\":8},{\"x\":4,\"y\":9},{\"x\":4,\"y\":10},{\"x\":6,\"y\":7},{\"x\":6,\"y\":8},{\"x\":6,\"y\":9},{\"x\":6,\"y\":10},{\"x\":8,\"y\":7},{\"x\":8,\"y\":8},{\"x\":8,\"y\":9},{\"x\":8,\"y\":10}],\"bombs\":[{\"x\":1,\"y\":4},{\"x\":3,\"y\":4},{\"x\":5,\"y\":8},{\"x\":10,\"y\":10}],\"key\":{\"x\":11,\"y\":6},\"door\":{\"x\":10,\"y\":2}}', NULL, '2026-01-13 11:11:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('player','admin') DEFAULT 'player',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Haniehj.jafari@gmail.com', '$2y$12$F83InwpDSbmfWJ/RAK4zte2Xnc5Ft.e.UBvjQX5tHKXPHzqqEnv9m', 'Hanieh Jafari', 'player', '2025-12-12 17:19:03', '2025-12-13 10:45:19'),
(5, 'admin@roomshift.com', '$2y$12$anOU8yzlvLkj3cBZkgq4ouV357Nfc65DikUbLf9KLiKY37yykSvlu', 'admin', 'admin', '2025-12-13 10:46:12', '2025-12-13 10:46:38'),
(6, 'test@email.com', '$2y$12$NwfJ8ePFnY3u7bffLlOgyeR0NHd2UvuAdZNtAkasHcMuw0SBBut8W', 'test user', 'player', '2025-12-17 11:39:32', '2025-12-17 11:39:32'),
(7, 'hanieh@gmail.com', '$2y$12$/DJSJiDBv5QngrDt3pvDiuzO6HKQ6qCjdwBSZRp.E.Z1XNeUSDpVC', 'Hanieh', 'admin', '2026-01-13 10:48:08', '2026-01-13 12:51:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
