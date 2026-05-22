-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 22, 2026 at 11:41 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rinzify_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `name`, `contact`, `email`, `address`, `created_at`) VALUES
(1, 'Maria Santo', '09181234567', 'maria.santos@gmail.com', 'Diliman, Quezon City', '2026-05-06 17:23:48'),
(2, 'John Reyes', '09271234567', 'john.reyes@yahoo.com', 'Cebu City, Cebu', '2026-05-06 17:23:48'),
(3, 'Ana Lopez', '09351234567', 'ana.lopez@hotmail.com', 'Davao City, Davao del Sur', '2026-05-06 17:23:48'),
(4, 'Mark Cruz', '09461234567', 'mark.cruz@gmail.com', 'Makati City, Metro Manila', '2026-05-06 17:23:48'),
(5, 'Lisa Tan', '09571234567', 'lisa.tan@gmail.com', 'Bacolod City, Negros Occidental', '2026-05-06 17:23:48');

-- --------------------------------------------------------

--
-- Table structure for table `comprehensive_costs`
--

CREATE TABLE `comprehensive_costs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `cost_name` varchar(255) NOT NULL,
  `cost_type` enum('material','furniture','labor','other') NOT NULL,
  `material_type` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `furniture_type` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `wage_per_day` decimal(10,2) DEFAULT NULL,
  `estimated_days` int(11) DEFAULT NULL,
  `other_type` varchar(100) DEFAULT NULL,
  `calculated_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `expense_type` enum('material','furniture','labor','incidental','other') DEFAULT 'other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `project_name` varchar(150) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `active_enforcer` int(11) GENERATED ALWAYS AS (if(`is_active` = 1,1,NULL)) VIRTUAL,
  `total_budget` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `client_id`, `project_name`, `location`, `start_date`, `end_date`, `created_at`, `is_active`, `total_budget`, `status`) VALUES
(6, 1, 'Living Room Makeover', 'Diliman, Quezon City', '2025-02-01', '2025-02-20', '2026-05-06 17:24:58', 0, 0.00, 'completed'),
(7, 2, 'Kitchen Renovation', 'Makati City, Metro Manila', '2025-01-05', '2025-02-15', '2026-05-06 17:24:58', 0, 0.00, 'completed'),
(8, 3, 'Bathroom Upgrade', 'Cebu City, Cebu', '2024-11-10', '2024-12-05', '2026-05-06 17:24:58', 0, 0.00, 'completed'),
(9, 4, 'Office Interior Design', 'Taguig City, Metro Manila', '2025-03-01', '2025-04-10', '2026-05-06 17:24:58', 0, 0.00, 'completed'),
(10, 5, 'Bedroom Redesign', 'Davao City, Davao del Sur', '2024-10-01', '2024-10-25', '2026-05-06 17:24:58', 0, 0.00, 'completed'),
(12, 3, 'hotel room', 'davao', '2026-05-22', '2026-05-31', '2026-05-22 01:48:35', 0, 1500000.00, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `project_logs`
--

CREATE TABLE `project_logs` (
  `log_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attachment_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_updates`
--

CREATE TABLE `project_updates` (
  `update_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `update_note` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_done` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_updates`
--

INSERT INTO `project_updates` (`update_id`, `project_id`, `update_note`, `status`, `update_date`, `is_done`) VALUES
(12, 12, 'jkhsjkbfs', 'Documents', '2026-05-22 05:45:29', 0),
(13, 12, 'material', 'Resource Acquisition', '2026-05-22 05:45:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('employee','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `created_at`, `role`) VALUES
(6, 'admin test', 'admin@example.com', '$2y$10$Y/gh7ccijgu9VzbgBRZ6LeSmUqEYq9SvGEVrPyQM3UzYF/6juH7NG', '2026-05-22 02:01:23', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `comprehensive_costs`
--
ALTER TABLE `comprehensive_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD UNIQUE KEY `unique_active_project` (`active_enforcer`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `project_logs`
--
ALTER TABLE `project_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comprehensive_costs`
--
ALTER TABLE `comprehensive_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `project_logs`
--
ALTER TABLE `project_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_updates`
--
ALTER TABLE `project_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comprehensive_costs`
--
ALTER TABLE `comprehensive_costs`
  ADD CONSTRAINT `comprehensive_costs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_logs`
--
ALTER TABLE `project_logs`
  ADD CONSTRAINT `project_logs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD CONSTRAINT `project_updates_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
