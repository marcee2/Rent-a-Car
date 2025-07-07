-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 09:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `comment_text` text DEFAULT NULL,
  `comment_time` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `reservation_id`, `comment_text`, `comment_time`, `user_id`, `vehicle_id`, `content`, `rating`, `created_at`) VALUES
(5, NULL, NULL, NULL, 1, 1, 'Odlično vozilo, sve je bilo kako treba.', 5, '2025-07-04 01:44:39'),
(6, 4, NULL, NULL, 5, 6, 'not bad', 3, '2025-07-06 17:56:23'),
(7, 5, NULL, NULL, 5, 10, 'Lepa voznja na autoputu', 4, '2025-07-06 17:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_time` datetime DEFAULT NULL,
  `return_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `is_cancelled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `vehicle_id`, `start_datetime`, `end_datetime`, `code`, `is_cancelled`) VALUES
(1, NULL, 3, '2025-07-06 00:00:00', '2025-07-09 00:00:00', 'e219ca1d69', 0),
(2, NULL, 6, '1111-11-11 00:00:00', '1111-12-11 00:00:00', 'deec0304cc', 0),
(3, 5, 7, '1111-02-14 00:00:00', '2025-11-02 00:00:00', '948c4df8fd', 0),
(4, 5, 6, '2025-02-14 00:00:00', '2025-02-16 00:00:00', '5ade638c1e', 0),
(5, 5, 10, '2025-07-01 00:00:00', '2025-07-06 00:00:00', '958a0769d3', 0),
(6, 5, 4, '2025-07-06 23:30:00', '2025-07-07 00:00:00', '68e5dc0846', 1),
(7, 5, 3, '2025-08-05 00:00:00', '2025-08-06 00:00:00', 'c5e1fb64dc', 0);

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `return_time` datetime DEFAULT NULL,
  `vehicle_condition` text DEFAULT NULL,
  `damage_report` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `reservation_id`, `return_time`, `vehicle_condition`, `damage_report`) VALUES
(1, 7, '2025-07-06 21:49:18', 'Vrlo dobro- malo ga je isprljao manijak', 'Nema');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `driver_license` varchar(50) NOT NULL,
  `license_number` varchar(20) DEFAULT NULL,
  `license_place` varchar(50) DEFAULT NULL,
  `role` enum('user','worker','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 0,
  `reset_token` varchar(100) DEFAULT NULL,
  `activation_token` varchar(100) DEFAULT NULL,
  `banned_until` datetime DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) NOT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `id_number`, `driver_license`, `license_number`, `license_place`, `role`, `is_active`, `reset_token`, `activation_token`, `banned_until`, `ban_reason`, `activation_code`, `reset_token_expiry`) VALUES
(1, 'roseli2326@cristout.com', '$2y$10$qk1VO/wPYKyNm3OvdS5qUujE/i6Uo52BnRDudaGqFtxhPKL/ug5uS', 'Nikola', 'Marcetic', NULL, '123123', '123123', NULL, 'SU', 'user', 1, NULL, NULL, NULL, NULL, '', NULL),
(3, 'admin@gmail.com', '$2y$10$8NP402W4r6Crp2U3/eCYuev8lN7C5uidf7cIDW5Hhb2i6Xgjt5IcW', 'Admin', 'User', NULL, NULL, '', NULL, NULL, 'admin', 1, NULL, NULL, NULL, NULL, '', NULL),
(4, 'keri@gmail.com', '$2y$10$0k2Jp3GIHjxaRzCQNGFyp.eZsEinKp36pJ/jvQWcLmuEZuHFiEnkG', 'David', 'Keri', NULL, NULL, '', NULL, NULL, 'worker', 1, NULL, NULL, NULL, NULL, '', NULL),
(5, 'raxica3931@binafex.com', '$2y$10$jyiUsm0S7P9TGSkwBAUK5.Ra.nEG6YdAqo/fcN451xli.FhadIzcy', 'test', 'aaaaaaaaaa12', '123123123123123213', '123123123123', '123123123123', NULL, 'SU', 'user', 1, 'b8f5f18f67b278007c45b24b99636b3b', NULL, NULL, NULL, '', '2025-07-06 21:45:28'),
(6, 'raxica331@binafex.com', '$2y$10$eNyvHv8aF6hfsjm6NMMS9ur7eIVWXdk.xZTihQztPIXHqac171ima', 'test', 'subject', NULL, '123123123', '123123123', NULL, 'SU', 'user', 0, NULL, NULL, NULL, NULL, '393192379605b62acc49aae724c864c7', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `price_per_day` decimal(10,2) DEFAULT 0.00,
  `category_id` int(11) DEFAULT NULL,
  `popularity` int(11) DEFAULT 0,
  `gearbox` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `name`, `model`, `year`, `fuel_type`, `seats`, `image`, `views`, `price_per_day`, `category_id`, `popularity`, `gearbox`, `is_available`) VALUES
(1, 'RangeRover', 'LandRover', 2018, 'Diesel', 5, 'images/beli.jpg', 0, 35.00, NULL, 351, 'Automatic', 1),
(2, 'Hyundai', 'Tucson', 2023, 'Petrol', 5, 'images/car5.jpg', 0, 40.00, NULL, 150, 'Automatic', 1),
(3, 'Toyota', 'Supra', 2017, 'Diesel', 2, 'images/car6.jpg', 0, 75.00, NULL, 516, 'Manual', 1),
(4, 'Hyundai', 'Land Cruiser', 2010, 'Diesel', 5, 'images/car7.jpg', 0, 25.00, NULL, 252, 'Automatic', 1),
(5, 'Jeep', 'Compass', 2019, 'Petrol', 5, 'images/car8.jpg', 0, 40.00, NULL, 221, 'Automatic', 1),
(6, 'Jeep', 'Wrangler', 2018, 'Diesel', 5, 'images/car9.jpg', 0, 45.00, NULL, 451, 'Manual', 1),
(7, 'Citroen', 'C4', 2023, 'Diesel', 5, 'images/car10.jpg', 0, 40.00, NULL, 486, 'Automatic', 1),
(8, 'Ford', 'Focus', 2017, 'Petrol', 5, 'images/crni.jpg', 0, 25.00, NULL, 300, 'Automatic', 1),
(9, 'Audi', 'A3', 2019, 'Petrol', 5, 'images/plavi.jpg', 0, 50.00, NULL, 401, 'Automatic', 1),
(10, 'Mercedes', 'A Class', 2015, 'Diesel', 5, 'images/svetli.jpg', 0, 25.00, NULL, 245, 'Automatic', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_categories`
--

CREATE TABLE `vehicle_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `worker_log`
--

CREATE TABLE `worker_log` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `type` enum('pickup','return') NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worker_log`
--

INSERT INTO `worker_log` (`id`, `reservation_id`, `worker_id`, `type`, `timestamp`) VALUES
(1, 7, 4, 'pickup', '2025-07-06 21:48:53'),
(2, 7, 4, 'return', '2025-07-06 21:49:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `vehicle_categories`
--
ALTER TABLE `vehicle_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `worker_log`
--
ALTER TABLE `worker_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicle_categories`
--
ALTER TABLE `vehicle_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `worker_log`
--
ALTER TABLE `worker_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `vehicle_categories` (`id`);

--
-- Constraints for table `worker_log`
--
ALTER TABLE `worker_log`
  ADD CONSTRAINT `worker_log_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `worker_log_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
