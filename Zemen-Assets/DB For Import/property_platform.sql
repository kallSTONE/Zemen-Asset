-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 07:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `property_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('house','apartment','commercial','land','machinery') DEFAULT 'house',
  `price` decimal(12,2) NOT NULL,
  `location` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL DEFAULT '/property-platform/assets/images/p1.jpg',
  `listing_type` enum('sale','rent') DEFAULT 'sale',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `user_id`, `title`, `description`, `category`, `price`, `location`, `image_url`, `listing_type`, `status`, `created_at`) VALUES
(8, 18, 'Shop rental in Hossana', 'A 50m2 rental room in Hossana, in Choramo mall.', 'commercial', 10000.00, 'hossana', '/property-platform/assets/images/p1.jpg', 'rent', 'approved', '2025-07-25 07:28:46'),
(9, 18, 'Luxury Apartment', 'Describe Your Property in Details ...', 'apartment', 50000.00, 'Addis Ababa', '/property-platform/assets/images/p2.jpg', 'rent', 'approved', '2025-07-25 11:25:17'),
(10, 18, 'Vila House', 'Describe Your House', 'house', 400000.00, 'Hossana', '/property-platform/assets/images/p3.jpg', 'sale', 'approved', '2025-07-25 11:26:51'),
(11, 17, '3 Bed Room House with Compound', '3 Bed Room House with Compound\r\n300 Meter from main road\r\nKitchen, Shower included', 'house', 6000000.00, 'Hossana', '/property-platform/assets/images/p4.jpg', 'sale', 'approved', '2025-07-26 12:23:56'),
(12, 17, 'Ware House - 400 m2 ', 'Ware House in Hossana For rent\r\n\r\nArea Coverage : 400m2\r\nWall Type : Mixed [concrete and tin]\r\nfloor : cement\r\nElectricity : up to 600VPH', 'commercial', 50000.00, 'Hossana, Naramo ', '/property-platform/assets/images/p5.jpg', 'rent', 'approved', '2025-07-26 12:31:56'),
(13, 17, 'Machine ', 'Tractor 100HP For rent\r\n\r\nElectricity : up to 600VPH', '', 10000.00, 'Central Ethiopia', '/property-platform/assets/images/m5.jpg', 'rent', 'approved', '2025-07-26 12:40:23'),
(14, 17, 'Walking Tractor for Sale', 'Machine : Walking Tractor \r\nPower : 18HP\r\nK35000\r\n0975730838', 'machinery', 10000.00, 'Central Ethiopia Region', '/property-platform/assets/images/m2.jpg', 'sale', 'approved', '2025-07-26 12:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_images`
--

INSERT INTO `listing_images` (`id`, `listing_id`, `image_path`, `is_primary`, `created_at`) VALUES
(6, 8, 'uploads/listings/6883322e67641_p1.jpg', 1, '2025-07-25 07:28:46'),
(7, 8, 'uploads/listings/6883322e68d4a_p2.jpg', 0, '2025-07-25 07:28:46'),
(8, 8, 'uploads/listings/6883322e69f36_p3.jpg', 0, '2025-07-25 07:28:46'),
(9, 9, 'uploads/listings/6883699daebe2_p1.jpg', 1, '2025-07-25 11:25:17'),
(10, 10, 'uploads/listings/688369fb8e490_p4.jpg', 1, '2025-07-25 11:26:51');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','responded','closed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `seller_id`, `listing_id`, `message`, `status`, `created_at`) VALUES
(2, 17, 18, 10, 'I\'m intereseted in this property', 'pending', '2025-07-25 12:02:45'),
(3, 17, 18, 10, 'I\'m interest in this property, please contact me with more details', 'pending', '2025-07-25 12:03:36'),
(4, 17, 18, 9, 'j', 'pending', '2025-07-25 12:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `reviewer_name` varchar(100) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','seller','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(16, 'Kalab Tadesse', 'kalab@admin.com', '$2y$10$WGEOrQq/PH2frqF4/LSed.BIagOjky0QXyhiN2OCsJjJdJS58/kRa', '1234567890', 'admin', '2025-07-25 07:20:05'),
(17, 'Kalab Tadesse', 'kalab@customer.com', '$2y$10$ZZ63ysyUvDPNkRUOYV6yWeeIi6zZp5.5/KhIUFgehTmS45SSPlNU2', '1234567890', 'customer', '2025-07-25 07:20:41'),
(18, 'Kalab Tadesse', 'kalab@seller.com', '$2y$10$e8clcmuSH9Ln9sbHH91.D.S4FP1KFLPyEPJmgLs0180fT8Nk1K5xy', '1234567890', 'seller', '2025-07-25 07:21:06'),
(19, 'Ayele Bekele', 'Ayele@seller.com', '$2y$10$s6YJTWu.WE2SwEWCOVPFIu4dWi.Uuxkbn0OLVGv2SVPJbFiL3zhjC', '12341512', 'seller', '2025-07-27 18:41:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`listing_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`);

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
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
