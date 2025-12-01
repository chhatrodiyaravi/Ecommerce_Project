-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 05:07 AM
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
-- Database: `camera_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123'),
(2, 'raviahir', 'raviahir');

-- --------------------------------------------------------

--
-- Table structure for table `cameras`
--

CREATE TABLE `cameras` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `company` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_name`, `phone`, `address`, `total_amount`, `payment_method`, `order_date`) VALUES
(1, 0, 'rahiul', '45455646546', 'Rajkot (M Corp. + OG) AHIR BOARDING', 4545454.00, 'COD', '2025-08-12 09:01:46'),
(2, 0, 'raj', '4578962145', 'veraval', 118998.00, 'Online', '2025-08-12 09:13:32'),
(3, 0, 'raj', '4578962145', 'veraval', 118998.00, 'Online', '2025-08-12 09:16:49'),
(4, 0, 'xyz', '4', '4', 42999.00, 'COD', '2025-08-12 10:16:09'),
(5, 0, 'xyz', '4', '4', 42999.00, 'COD', '2025-08-12 10:16:15'),
(6, 0, 'ram', '7896541236', 'Rajkot (M Corp. + OG) AHIR BOARDING', 79998.00, 'COD', '2025-08-17 17:21:18'),
(7, 0, 'ram', '7896541236', 'Rajkot (M Corp. + OG) AHIR BOARDING', 79998.00, 'COD', '2025-08-17 17:23:04'),
(8, 0, 'ram', '7896541236', 'Rajkot (M Corp. + OG) AHIR BOARDING', 79998.00, 'COD', '2025-08-17 17:24:39'),
(9, 0, 'rajesh', '48784646546546546446', 'fdsfsdd', 379995.00, 'Online', '2025-08-17 17:25:13'),
(10, 0, 'rajesh', '7896541236', 'Rajkot ', 39999.00, 'COD', '2025-08-18 20:04:07'),
(11, 0, 'ram', '45455646546', 'Rajkot (M Corp. + OG) AHIR BOARDING', 199995.00, 'COD', '2025-08-23 11:44:19'),
(12, 7, 'meet', '4569782136', 'xysdfsfsf', 840.00, 'COD', '2025-08-27 16:01:52'),
(13, 7, 'meet', '4569782136', 'xysdfsfsf', 840.00, 'COD', '2025-08-27 16:12:01'),
(14, 9, 'ketan', '45455646546', 'bhuj', 1680.00, 'COD', '2025-08-27 17:09:23'),
(15, 9, 'dgdfg', 'gddgsgg', 'gsd', 10449.00, 'COD', '2025-08-27 17:15:15'),
(16, 9, 'ram', '45332132', 'fsfa', 45000.00, 'COD', '2025-08-27 17:21:02'),
(17, 7, 'ram', '45455646546', 'Rajkot (M Corp. + OG) AHIR BOARDING', 27999.30, 'COD', '2025-08-28 08:21:28'),
(18, 7, 'fdsfdsf', '5454', 'r', 40000.00, 'Online', '2025-09-03 22:22:18'),
(19, 7, 'virat', '456', 'mumbai', 31999.00, 'Online', '2025-09-03 22:24:40'),
(20, 7, 'dasd', 'fsd', 'Rajkot (M Corp. + OG) AHIR BOARDING', 144000.00, 'Online', '2025-09-11 08:12:39'),
(21, 10, 'ravichhatrodiya', '1234567898', 'veraval,girsomnath', 680.00, 'Online', '2025-09-19 20:57:12'),
(22, 10, 'ravi', '2345678965', 'veraval', 44516.00, 'COD', '2025-09-19 22:30:26'),
(23, 7, 'meet patel', '45455646546', 'Rajkot (M Corp. + OG) AHIR BOARDING', 72999.30, 'COD', '2025-09-23 16:43:33'),
(24, 7, 'meet', '45455646546', 'junagadh', 40000.00, 'COD', '2025-09-30 18:20:13'),
(25, 7, 'meet patel', '9925001596', 'bhuj,kutch', 45000.00, 'COD', '2025-09-30 18:43:28'),
(26, 12, 'john', '1234567895', 'maninager,ahemdabad', 1360.00, 'Online', '2025-09-30 21:17:27');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 3, 1, 75999.00),
(2, 2, 2, 1, 42999.00),
(3, 4, 2, 1, 42999.00),
(4, 6, 1, 0, 39999.00),
(5, 6, 1, 0, 39999.00),
(6, 9, 3, 5, 75999.00),
(7, 10, 1, 1, 39999.00),
(8, 11, 1, 5, 39999.00),
(9, 12, 4, 1, 840.00),
(10, 14, 4, 1, 840.00),
(11, 14, 4, 1, 840.00),
(12, 15, 3, 1, 10449.00),
(13, 16, 10, 1, 45000.00),
(14, 17, 1, 1, 27999.30),
(15, 18, 7, 1, 40000.00),
(16, 19, 18, 1, 31999.00),
(17, 20, 5, 3, 48000.00),
(18, 21, 6, 1, 680.00),
(19, 22, 3, 4, 10449.00),
(20, 22, 6, 4, 680.00),
(21, 23, 10, 1, 45000.00),
(22, 23, 1, 1, 27999.30),
(23, 24, 7, 1, 40000.00),
(24, 25, 7, 1, 45000.00),
(25, 26, 6, 2, 680.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `token`, `expires_at`, `email`) VALUES
(1, '09ad59df0d8de7ffef0ee4cabc728692', '2025-09-23 13:42:03', 'trendcart04@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `offer_text` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `total_reviews` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `company`, `price`, `description`, `image`, `discount_price`, `offer_text`, `rating`, `total_reviews`) VALUES
(1, 'Canon EOS 1500D', 'Canon', 39999.00, '24.1MP DSLR Camera with 18-55mm Lens', 'Canon1.jpeg', 27999.30, '30% off on Canon', 3.3, 268),
(3, 'Sony Alpha a6400', 'Sony', 75999.00, 'Mirrorless Digital Camera with 16-50mm Lens', 'Sony4.jpeg', 10449.00, '50% off + Bank Offer', 4.9, 834),
(4, 'Canon EOS 90D DSLR', 'Canon', 1200.00, 'Professional DSLR with 32.5 MP sensor', 'canon2.webp\r\n', 840.00, '30% off on Canon', 3.8, 438),
(5, 'Sony Alpha a7 III', 'Sony', 80000.00, 'Full-frame Mirrorless Camera', 'sony2.jpeg', 48000.00, '40% off + Bank Offer', 3.1, 820),
(6, 'Nikon D5600 DSLR', 'Nikon', 800.00, '24.2 MP DSLR Camera', 'nikon1.jpeg', 680.00, '15% off on Nikon', 3, 635),
(7, 'Panasonic Lumix GH5', 'Panasonic', 45000.00, 'Mirrorless 4K camera', 'Panasonic1.jpeg', 40000.00, '10% off + Bank Offer', 3.2, 684),
(8, 'Fujifilm X-T4', 'Fujifilm', 100000.00, 'Mirrorless camera with film simulation', 'Fujifilm1.jpeg', 80000.00, '10% off + Bank Offer', 3.1, 294),
(9, 'Canon PowerShot G7X', 'Canon', 90000.00, 'Compact camera with 20.1MP sensor', 'Canon3.jpeg', 45000.00, '10% off + Bank Offer', 3.5, 440),
(10, 'Sony ZV-E10', 'Sony', 56000.00, 'Mirrorless vlogging camera', 'Sony3.jpeg', 45000.00, '10% off + Bank Offer', 3.8, 715),
(11, 'sony EOS 90D DSLR', 'sony', 70000.00, 'Professional DSLR with 32.5 MP sensor', 'sony1.jpeg', 59000.00, '15% off + Bank Offer', 3.7, 636),
(13, 'Canon EOS 1500D', 'Canon', 50000.00, 'Best DSLR Camera for beginners.', 'canon1500d.jpg', 40000.30, 'best offer', 3.2, 642),
(19, 'Nikon z5ii', 'Nikon', 150000.00, 'Nikon Z5II Mirrorless Camera with 24-200mm f/4-6.3 Lens', '1759245489_Nikon2.webp', 100000.00, 'best offer', 0, 0),
(20, 'nikon d5600 dslr', 'nikon', 50000.00, 'Nikon D5600 DSLR Camera with 18-55mm VR and 70-300mm Dual Lens', '1759245849_Nikon3.jpeg', 45000.00, 'today best offer', 0, 0),
(21, 'nikon d5600 dslr', 'nikon', 50000.00, 'Nikon D5600 DSLR Camera with 18-55mm VR and 70-300mm Dual Lens', '1759245966_Nikon3.jpeg', 45000.00, 'today best offer', 0, 0),
(22, 'Panasonic Lumix S5 ', 'Panasonic', 99999.00, 'Panasonic Lumix S5 II Mirrorless Camera', '1759246234_Panasonic2.webp', 88999.00, '', 0, 0),
(23, 'Panasonic HC-V785K Full HD Camcorder', 'Panasonic ', 49999.00, 'Stunning Images, Even in Dim Lighting. The Panasonic HC-V785 is a full HD camcorder with a 29.5mm wide-angle lens, 20x optical zoom, intelligent 50x zoom, high sensitivity and optical image stabiliser.', '1759246451_Panasonic3.webp', 41999.00, '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `order_id`, `user_id`, `product_id`, `user_name`, `rating`, `review_text`, `created_at`) VALUES
(1, 15, 9, 3, '', 2, 'dvdfvdf', '2025-08-27 11:47:45'),
(2, 15, 9, 3, '', 2, 'dvdfvdf', '2025-08-27 11:47:52'),
(3, 15, 9, 3, '', 2, 'dvdfvdf', '2025-08-27 11:48:52'),
(4, 15, 9, 3, '', 2, 'dvdfvdf', '2025-08-27 11:49:12'),
(5, 16, 9, 10, '', 2, 'good', '2025-08-27 11:51:30'),
(6, 17, 7, 1, '', 2, 'good', '2025-08-28 02:51:40'),
(7, 18, 7, 7, '', 3, 'good', '2025-09-03 16:52:31'),
(9, 20, 7, 5, '', 5, 'good', '2025-09-11 02:43:07'),
(11, 24, 7, 7, '', 2, 'good', '2025-09-30 12:50:45'),
(12, 26, 12, 6, '', 3, 'good experience of this camera', '2025-09-30 15:47:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp` varchar(10) DEFAULT NULL,
  `otp_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `otp`, `otp_expire`) VALUES
(1, 'ravi', 'trendcart04@gmail.com', '$2y$10$KPeUcAd.hnshtHKHRp9QZe07ucKiSgtTCZf69Yl4HpMonbTB0/MEK', '2025-08-11 04:09:36', NULL, NULL),
(3, 'virat', 'v@gmail.com', '$2y$10$Mv2cdC7Qt9IzUSI/8yvouuK.bOPeYu2a..ZvtYUwqlOz.kXoj92Au', '2025-08-18 14:26:14', NULL, NULL),
(6, 'ram', 'ramhir@gmail.com', '$2y$10$ZhDp3MO/goSl6y2l8CNSLuDS1dQyhuOnotz0Vvc5416cG/JjIVb7.', '2025-08-23 06:13:41', NULL, NULL),
(7, 'meet', 'meet@gmail.com', '$2y$10$KuIY4Yqk84Qrg/2inkLRVO5dS/FL294KZXyrhLD2ZeHB39ZBoa8Zy', '2025-08-23 10:07:07', NULL, NULL),
(8, 'dhaval', 'dvl@gmail.com', '$2y$10$3BqYiphQsk48Wh3QJBe7uOeCdJ1Y0LCRwNOFoHXc91osT/He1wk1S', '2025-08-25 13:07:41', NULL, NULL),
(9, 'ketan', 'k@gmail.com', '$2y$10$/jCzuVDfSXk69Z4Ic1qbGec3nIQc.MplKvV0jVwptuW2EeYNLZzVG', '2025-08-27 11:29:38', NULL, NULL),
(11, 'ravichhatrodiya', 'rchhatrodiya@gmail.com', '$2y$10$E7ZlRBL505SE/LiRKQOsT.SUGWN9b6ozKxM3yOVwAJEep3XaJT4o6', '2025-09-30 13:09:41', NULL, NULL),
(12, 'john', 'john12@gamail.com', '$2y$10$yZifmGUP.05LGN3x8/NxAu/0U0B4ZA2A0IF2rIZ9sI3TXQznW1LVO', '2025-09-30 15:46:25', NULL, NULL),
(13, 'Harsh', 'harshravaliya3@gmail.com', '$2y$10$NvlUYr.W8uEbS2ux5zxvxOJVzYlmH8Vu0NH4G4lWYG/aD3EWJgCpC', '2025-10-10 15:14:02', NULL, NULL),
(14, 'Rajkachhot', 'kachhotraj77@gmail.com', '$2y$10$ELT.pejqddAlWcwyQmRDOO.mBaAgfhX8Bb5I77BJ5IDuAmiiUx4N6', '2025-10-10 15:40:36', NULL, NULL),
(15, 'yash', 'yc@gmail.com', '$2y$10$YciVj/3V2rGgQcmHOJaOEuGH9SgVpkh67U5FUQ24DZC8bfZBdvxpq', '2025-11-06 04:25:28', '599476', '2025-11-06 10:07:04'),
(16, 'dhruvin', 'dhruvinchauhan9158@gmail.com', '$2y$10$F76ECUQJL1YGJCXQdQNjjeh/X3F2gKAXX9n3M.Z/zcjsxpMTQzTpS', '2025-11-06 04:30:18', '816420', '2025-11-06 10:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart`
--

CREATE TABLE `user_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cart`
--

INSERT INTO `user_cart` (`id`, `user_id`, `product_id`, `qty`, `added_on`) VALUES
(5, 10, 3, 4, '2025-09-19 16:47:41'),
(6, 10, 6, 4, '2025-09-19 16:53:00'),
(9, 7, 7, 1, '2025-09-30 12:49:36'),
(11, 12, 6, 2, '2025-09-30 15:46:46'),
(12, 15, 3, 1, '2025-11-06 04:26:07'),
(13, 1, 3, 1, '2025-11-27 15:33:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cameras`
--
ALTER TABLE `cameras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_reviews_user` (`user_id`),
  ADD KEY `fk_reviews_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cameras`
--
ALTER TABLE `cameras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
