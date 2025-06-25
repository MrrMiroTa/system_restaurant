-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 11:40 AM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `picture`, `category`, `description`, `qty`, `price`, `created_by`, `date_created`) VALUES
(24, 'បាយឆាគ្រឿងសមុទ្រ ', '../uploads/seafoodfriedrice.jpg', 'Fried Rice', '', 99, 2.00, 11, '2025-06-23 17:08:02'),
(25, 'បាយឆាសាច់គោ', '../uploads/beeffriedrice.jpg', 'Fried Rice', '', 97, 1.50, 11, '2025-06-23 17:11:03'),
(26, 'បាយលីងខ្ទឹមស', '../uploads/grilicfriedrice.jpg', 'Fried Rice', '', 100, 1.25, 11, '2025-06-23 17:12:35'),
(27, 'មីឆាសាច់គោ', '../uploads/Micha.jpg', 'Fried Noodle', '', 99, 1.50, 11, '2025-06-23 17:13:10'),
(28, 'មីឆាគ្រឿងសមុទ្រ', '../uploads/maxresdefault.jpg', 'Fried Noodle', '', 100, 2.00, 11, '2025-06-23 17:13:47'),
(29, 'បាយឆាឡុកឡាក់', '../uploads/loklak.jpg', 'Fried Rice', '', 98, 2.00, 11, '2025-06-23 17:14:15'),
(30, 'មីឆាបន្លែពងទា', '../uploads/noodlevetget.jpg', 'Fried Noodle', '', 100, 1.50, 11, '2025-06-23 17:15:17'),
(31, 'ស្តេកសាច់គោ', '../uploads/beefsteak.jpg', 'Steak', '', 100, 5.00, 11, '2025-06-23 17:16:44'),
(32, 'ស្តេកសាច់មាន់', '../uploads/chicken-steak-1080x721-1.webp', 'Steak', '', 99, 3.75, 11, '2025-06-23 17:17:50'),
(33, 'សាច់គោបំពងល្ង', '../uploads/Crispy Fried Beef.jpg', 'Beef', '', 100, 2.50, 11, '2025-06-23 17:20:28'),
(34, 'ភ្លាសាច់គោ', '../uploads/--421436166971232586215244-b.jpg', 'Beef', '', 99, 2.50, 11, '2025-06-23 17:20:52'),
(35, 'ឡាបសាច់គោ', '../uploads/beeflab.jpg', 'Beef', '', 99, 2.50, 11, '2025-06-23 17:21:20'),
(36, 'ឆាខាត់ណាខៀវសាច់គោ', '../uploads/beef.jpg', 'Beef', '', 94, 2.50, 11, '2025-06-23 17:22:52'),
(37, 'ឆាបន្លែគ្រប់មុខសាច់គោ', '../uploads/sddefault.jpg', 'Beef', '', 100, 2.50, 11, '2025-06-23 17:25:57'),
(38, 'ឆាពពាយសាច់គោ', '../uploads/sddefault (1).jpg', 'Beef', '', 100, 2.50, 11, '2025-06-23 17:30:01'),
(39, 'ឆាខ្ញីសាច់គោ', '../uploads/3493582.jpg', 'Beef', '', 99, 2.50, 11, '2025-06-23 17:37:22'),
(40, 'ឆាស្ពៃតឿសាច់គោ', '../uploads/hq720.jpg', 'Beef', '', 99, 2.50, 11, '2025-06-23 17:39:45'),
(41, 'តុងយុាំ (សាច់មាន់ / គ្រឿងសមុទ្រ)', '../uploads/tongyam.jpg', 'Soup', '', 98, 2.50, 11, '2025-06-23 17:40:58'),
(42, 'សម្លកកូរ (ឆ្អឹងជំនីជ្រូក/សាច់មាន់)', '../uploads/កោកោរ.jpg', 'Soup', '', 97, 2.50, 11, '2025-06-23 17:42:37'),
(43, 'ស្ងោរជ្រក់ (សាច់មាន់​​​ /សាច់គោ )', '../uploads/Beef-clear-soup-with-herbs.jpg', 'Soup', '', 99, 2.50, 11, '2025-06-23 17:44:22'),
(44, ' ម្ជូរគ្រឿង ( សាច់គោ​ / សាច់មាន់)', '../uploads/Beefsoursoup.jpg', 'Soup', '', 98, 2.50, 11, '2025-06-23 17:44:49'),
(45, 'ស្ងោរស្ពៃឆ្អឹងជំនីជ្រូក', '../uploads/hqdefault.jpg', 'Pork', '', 99, 2.50, 11, '2025-06-23 17:49:11'),
(46, 'ឆ្អឹងជំនីជ្រូកឆាជូរអែម', '../uploads/clse1ytig00020gkxhnvhhczf.jpg', 'Pork', '', 100, 2.50, 11, '2025-06-23 17:50:08'),
(47, 'ឆ្អឹងជំនីជ្រូកចៀន', '../uploads/msp412313548 (3).jpg', 'Pork', '', 98, 2.50, 11, '2025-06-23 17:51:47'),
(48, 'ឆ្អឹងជំនីជ្រូកលីងខ្ទឹមស', '../uploads/IMG_3606-4.jpg', 'Pork', '', 100, 2.50, 11, '2025-06-23 17:52:42'),
(49, 'ឆាមាន់លីងគល់ស្លឹកគ្រៃ', '../uploads/chiken.jpg', 'Chicken', '', 99, 2.50, 11, '2025-06-23 17:53:41'),
(50, 'ឆាមាន់ប្រៃផ្អែម', '../uploads/hq720 (1).jpg', 'Chicken', '', 97, 2.50, 11, '2025-06-23 17:54:38'),
(51, 'មាន់លីងអំបិលម្ទេស', '../uploads/images.jfif', 'Chicken', '', 100, 2.50, 11, '2025-06-23 17:55:31'),
(52, 'ជើងមាន់លីងខ្ទឹមស', '../uploads/images (1).jfif', 'Chicken', '', 98, 2.50, 11, '2025-06-23 17:56:21'),
(53, 'ឆាក្ដៅ (សាច់មាន់​ /​ សាច់គោ​ /​ កង្កែប / អន្ទង់)', '../uploads/etnb-listing.jpg', 'Stir Fried', '', 98, 2.50, 11, '2025-06-23 17:58:01'),
(54, 'ឆាគ្រឿង (សាច់គោ / កង្កែប / អន្ទង់)', '../uploads/maxresdefault (2).jpg', 'Stir Fried', '', 99, 2.50, 11, '2025-06-23 17:59:02'),
(55, ' ពោតលីង', '../uploads/cls3b495x00090fl51mqp0bx8.jpg', 'Other', '', 99, 2.50, 11, '2025-06-23 17:59:51'),
(56, 'ពងទា', '../uploads/12376602_851407941624737_8001240627176583749_n.webp', 'Other', '', 77, 0.25, 11, '2025-06-23 18:00:57'),
(57, 'ឆាបន្លែគ្រប់មុខ', '../uploads/71a2840204234cd39011a80886a455d1.jpg', 'Other', '', 100, 1.50, 11, '2025-06-23 18:02:05'),
(58, 'ឆាត្រកួនប្រេងខ្យង', '../uploads/sddefault (2).jpg', 'Other', '', 99, 1.50, 11, '2025-06-23 18:03:11'),
(59, 'ឆាស្ពៃតឿប្រេងខ្យង', '../uploads/download.jfif', 'Other', '', 100, 2.50, 11, '2025-06-23 18:03:34'),
(60, ' បាយស', '../uploads/3342857.jpg', 'Other', '', 95, 0.25, 11, '2025-06-23 18:04:09'),
(61, 'Delivery', '../uploads/Jinro.jpg', 'Other', '', 98, 1.25, 11, '2025-06-24 06:16:27'),
(62, 'ងាវស្រុះ', '../uploads/clyigjl9900020cl9dn9e0s49.jpg', 'Other', '', 99, 5.00, 11, '2025-06-24 16:27:23'),
(63, 'Beer', '../uploads/image_picker_B6FBF5BA-03D6-409B-B6F5-60623B633B8B-1320-00000026A6238268.jpg', 'Beer', '', 471, 0.88, 11, '2025-06-24 16:29:15'),
(64, 'ពងទាឆាក្ដៅ', '../uploads/images (2).jfif', 'Other', '', 97, 2.00, 11, '2025-06-24 16:31:52'),
(65, 'ពងចៀនបង្គា', '../uploads/បង្គាឆាពងទាប្រៃ.jpg', 'Other', '', 99, 3.00, 11, '2025-06-25 05:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `date_created`, `discount_amount`, `subtotal`, `note`) VALUES
(35, 11, 3.00, 'paid', '2025-06-24 06:04:21', 0.00, 3.00, 'សាច់គោ'),
(36, 11, 6.50, 'paid', '2025-06-24 06:05:19', 0.00, 6.50, ''),
(37, 11, 5.00, 'paid', '2025-06-24 06:05:41', 0.00, 5.00, ''),
(38, 11, 3.00, 'paid', '2025-06-24 06:06:02', 0.00, 3.00, ''),
(40, 11, 10.25, 'paid', '2025-06-24 06:09:38', 0.00, 10.25, 'Vattanac'),
(41, 11, 13.25, 'paid', '2025-06-24 06:18:11', 0.00, 13.25, 'Chipmong'),
(42, 11, 33.52, 'paid', '2025-06-24 17:18:40', 4.00, 37.52, ''),
(43, 10, 10.25, 'paid', '2025-06-25 05:43:47', 0.00, 10.25, 'Chipmong'),
(44, 10, 8.75, 'paid', '2025-06-25 05:47:37', 0.00, 8.75, 'Vattanak'),
(45, 10, 12.50, 'paid', '2025-06-25 05:51:35', 0.00, 12.50, '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `qty`, `price`, `discount`) VALUES
(37, 35, 43, 1, 2.50, 0.00),
(38, 35, 56, 2, 0.25, 0.00),
(39, 36, 45, 1, 2.50, 0.00),
(40, 36, 54, 1, 2.50, 0.00),
(41, 36, 27, 1, 1.50, 0.00),
(42, 37, 47, 1, 2.50, 0.00),
(43, 37, 41, 1, 2.50, 0.00),
(44, 38, 42, 1, 2.50, 0.00),
(45, 38, 56, 2, 0.25, 0.00),
(51, 40, 50, 2, 2.50, 0.00),
(52, 40, 56, 6, 0.25, 0.00),
(53, 40, 32, 1, 3.75, 0.00),
(54, 41, 53, 1, 2.50, 0.00),
(55, 41, 44, 1, 2.50, 0.00),
(56, 41, 25, 1, 1.50, 0.00),
(57, 41, 36, 2, 2.50, 0.00),
(58, 41, 56, 2, 0.25, 0.00),
(59, 41, 61, 1, 1.25, 0.00),
(60, 42, 63, 29, 0.88, 0.00),
(61, 42, 62, 1, 5.00, 50.00),
(62, 42, 64, 1, 2.00, 75.00),
(63, 42, 52, 2, 2.50, 0.00),
(64, 43, 50, 1, 2.50, 0.00),
(65, 43, 25, 1, 1.50, 0.00),
(66, 43, 24, 1, 2.00, 0.00),
(67, 43, 36, 1, 2.50, 0.00),
(68, 43, 56, 2, 0.25, 0.00),
(69, 43, 61, 1, 1.25, 0.00),
(70, 44, 40, 1, 2.50, 0.00),
(71, 44, 36, 1, 2.50, 0.00),
(72, 44, 65, 1, 3.00, 0.00),
(73, 44, 56, 3, 0.25, 0.00),
(74, 45, 35, 1, 2.50, 0.00),
(75, 45, 39, 1, 2.50, 0.00),
(76, 45, 29, 2, 2.00, 0.00),
(77, 45, 56, 4, 0.25, 0.00),
(78, 45, 49, 1, 2.50, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `category` enum('ingredient','meat','vegetable','drink') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `name`, `picture`, `category`, `description`, `qty`, `price`, `created_by`, `date_created`) VALUES
(1, 'jinro', '../uploads/Jinro.jpg', 'drink', 'test', 24, 32.00, 1, '2025-06-18 13:17:08'),
(2, 'jinro', '../uploads/Jinro.jpg', 'drink', 'test', 23, 32.00, 1, '2025-06-18 13:17:08'),
(3, 'rice', '../uploads/bacelona.png', 'meat', 'dedede', 4, 342.00, 1, '2025-06-18 14:19:46'),
(4, 'Arsenal ', '../uploads/t-shirt.jpg', 'ingredient', '', 1, 11.00, 1, '2025-06-20 09:43:41'),
(5, 'Juventus', '../uploads/juventus.png', 'meat', 'test add stock and track time', 10, 20.00, 6, '2025-06-23 13:16:22'),
(6, 'Winter Woman Jacket', '../uploads/jacket.png', 'meat', 'control stock', 9, 3.00, 6, '2025-06-23 13:26:28'),
(7, 'Beef Fried Rice', '../uploads/friedrice.jpg', 'meat', 'Test add stock item and control', 3, 5.00, 6, '2025-06-23 13:40:48');

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('in','out') NOT NULL,
  `qty` int(11) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_history`
--

INSERT INTO `stock_history` (`id`, `stock_id`, `user_id`, `type`, `qty`, `date`, `price`) VALUES
(18, 7, 11, 'in', 2, '2025-06-25 15:04:48', 5.00),
(19, 7, 11, 'out', 2, '2025-06-25 15:05:19', 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_by` int(11) DEFAULT NULL,
  `remember_token` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`, `created_by`, `remember_token`) VALUES
(1, 'admin', '$2y$10$OdgE47BhvU9FOlRY3.xWBueZGzp3/e1JGqbWhdMTXvFdgPBRpSalm', 'admin@gmail.com', '2025-06-18 11:30:51', 'admin', NULL, NULL),
(3, 'uta', '$2y$10$pU0OvkBS.IpOBTnMWdaXSe10ZlnuXbbtd/pHsstma.TK91INqKLAW', 'uta@gmail.com', '2025-06-18 12:20:25', 'user', NULL, NULL),
(5, 'user', '$2y$10$NhVdamEwKdBHj2wSLMVyNu2JlabIexKq98SJmPln5pUeHxW3nFVR.', 'user@example.com', '2025-06-20 11:29:01', 'user', NULL, NULL),
(6, 'newadmin', '$2y$10$T/Aldeppf95BS1.gALUvL.IfHiGnCy6/.KRG2PM1QWjjvQ5n9DDxO', 'admin@example.com', '2025-06-20 11:36:13', 'admin', NULL, 'b4d54dd427b739decc3ea974fa5c15f6164503433de26344592edf10264ec447'),
(7, 'test', '$2y$10$GXW.FYq7nsn4wJFAPJZv8.Pd71W1l854EIAfXLg/3a9rtn6Vxh04e', 'test@mail.com', '2025-06-20 11:46:25', 'user', 6, NULL),
(9, 'new', '$2y$10$.LDPBpWxwfEHu2k.9NFvxeKn7q8hjAieEdt1T/3pISeLlRMLHz5Ne', 'ta@gmail.com', '2025-06-21 05:50:46', 'user', 6, NULL),
(10, 'UziTa', '$2y$10$8LsUrmkkkStJT8mn0DZizuLycrnXcXuKqFjCueIWSoqF/IQ66iyw.', 'utato@gmail.com', '2025-06-22 07:43:18', 'user', 6, '1fa9fd45ec9ab52dbe08697034a260318aa5627094173bbdc5282f5ec37bd9ce'),
(11, 'Mrr Phors', '$2y$10$Ji7zv9WzvR86IrErUCec8esxVFC0WKAlLsxkzRphW4O.KNxeINu8a', 'mrrmirota@gmail.com', '2025-06-23 16:18:08', 'admin', 6, '65588d962da6cb343d65b8eb18f478087bac9e6c1f100b44f02ee856198a0c98'),
(12, 'testadmin', '$2y$10$XigQ8Ld7dEEuz19uT9J1JebOlbFN32Oztbchg8FeKODcoKCEAJhjS', 'admintest@mail.com', '2025-06-25 08:07:58', 'admin', 11, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_id` (`stock_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`),
  ADD CONSTRAINT `stock_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
