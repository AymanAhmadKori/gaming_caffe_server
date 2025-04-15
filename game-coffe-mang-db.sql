-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 02:58 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game-coffe-mang-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `google_id` longtext DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `google_id`, `email`, `full_name`, `entry_date`) VALUES
(1, 'sdfasfasf', 'test@example.com', 'محمد احمد', '2025-04-13 13:20:58'),
(2, '102231961515767709745', 'aymanahma033@gmail.com', 'أيمن احمد كوري', '2025-04-12 13:40:43');

-- --------------------------------------------------------

--
-- Table structure for table `ban_details`
--

CREATE TABLE `ban_details` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `ban_type` int(11) NOT NULL,
  `Unblock_at` datetime NOT NULL,
  `ban_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ban_types`
--

CREATE TABLE `ban_types` (
  `id` int(11) NOT NULL,
  `reason_of_ban` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ban_types`
--

INSERT INTO `ban_types` (`id`, `reason_of_ban`) VALUES
(1, 'تم حظر حسابك بسبب انتهاك لإرشادات المجتمع. ❤️');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon_name` varchar(255) NOT NULL,
  `cycle_duration` int(11) NOT NULL COMMENT 'In days',
  `price_per_cycle` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `icon_name`, `cycle_duration`, `price_per_cycle`) VALUES
(1, 'الافتراضي', 'clock', 15, '0.00'),
(2, 'اساسي', 'pakman', 30, '95.00');

-- --------------------------------------------------------

--
-- Table structure for table `subs`
--

CREATE TABLE `subs` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `plan_id` int(11) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expiry` datetime NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `subs`
--

INSERT INTO `subs` (`id`, `account_id`, `plan_id`, `cost`, `expiry`, `entry_date`) VALUES
(2, 2, 1, '0.00', '2025-04-27 13:40:43', '2025-04-12 11:40:43');

-- --------------------------------------------------------

--
-- Table structure for table `subs_history`
--

CREATE TABLE `subs_history` (
  `id` int(11) NOT NULL,
  `sub_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `plan_id` int(11) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `expiry` datetime NOT NULL,
  `entry_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`) USING HASH;

--
-- Indexes for table `ban_details`
--
ALTER TABLE `ban_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_id` (`account_id`),
  ADD KEY `Ban type` (`ban_type`);

--
-- Indexes for table `ban_types`
--
ALTER TABLE `ban_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subs`
--
ALTER TABLE `subs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_id_2` (`account_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `sub_details_id` (`plan_id`) USING BTREE;

--
-- Indexes for table `subs_history`
--
ALTER TABLE `subs_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_id` (`sub_id`),
  ADD KEY `Account` (`account_id`),
  ADD KEY `Plan` (`plan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ban_details`
--
ALTER TABLE `ban_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `ban_types`
--
ALTER TABLE `ban_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subs`
--
ALTER TABLE `subs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subs_history`
--
ALTER TABLE `subs_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ban_details`
--
ALTER TABLE `ban_details`
  ADD CONSTRAINT `Account id ` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Ban type` FOREIGN KEY (`ban_type`) REFERENCES `ban_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `subs`
--
ALTER TABLE `subs`
  ADD CONSTRAINT `Accoutn id ` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Plan id` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `subs_history`
--
ALTER TABLE `subs_history`
  ADD CONSTRAINT `Account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Sub id` FOREIGN KEY (`sub_id`) REFERENCES `subs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
