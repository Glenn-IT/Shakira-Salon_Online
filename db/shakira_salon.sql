-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 07:52 AM
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
-- Database: `shakira_salon`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `type`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Sample', 'Lezzgo', 'info', 'active', '2025-10-25 14:53:08', '2025-10-25 14:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `service` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `schedule` varchar(20) DEFAULT NULL,
  `stylist` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending',
  `proof_payment` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `date`, `user_id`, `appointment_date`, `appointment_time`, `service`, `status`, `created_at`, `customer_name`, `phone`, `address`, `price`, `schedule`, `stylist`, `payment_proof`, `payment_status`, `proof_payment`, `approved_at`, `email`) VALUES
(60, '0000-00-00', NULL, '0000-00-00', '00:00:00', 'Hair Coloring', 'approved', '2025-09-30 00:17:04', 'Jovelyn Erece', '09756432188', 'niug', 2000.00, '08:00 AM', 'Shakira', 'proof_68db21807feec.jpg', 'Pending', NULL, NULL, 'erecejovelyn@gmail.com'),
(61, '0000-00-00', NULL, '0000-00-00', '00:00:00', 'Hair Coloring', 'pending', '2025-09-30 00:41:19', 'Nicole Daguio Acojedo', '09657951427', 'Tabang', 2000.00, '08:00 AM', 'melody delacruz', 'proof_68db272fd6903.jpg', 'Pending', NULL, NULL, 'nicoleacojedo03@gmail.com'),
(62, '0000-00-00', NULL, '0000-00-00', '00:00:00', 'Hair Coloring', 'approved', '2025-10-25 15:26:45', 'Sample', '09192783312', 'sample', 2000.00, '05:00 PM', 'Lala Ursola Uy', NULL, 'Pending', NULL, NULL, 'glenard0823@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `service` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `booking_date` datetime NOT NULL,
  `status` enum('Pending','Approved','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

CREATE TABLE `business_hours` (
  `id` int(11) NOT NULL,
  `open_hour` int(11) NOT NULL,
  `close_hour` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`id`, `open_hour`, `close_hour`, `status`) VALUES
(1, 11, 14, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') NOT NULL DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(12, 'Rica Attaban', 'ricamaeattaban24@gmail.com', 'haircut', 'hi', '2025-09-25 05:41:32', 'read');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `style_name` varchar(255) NOT NULL,
  `haircut_name` varchar(255) DEFAULT NULL,
  `before_image` varchar(255) NOT NULL,
  `after_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `service_name`, `style_name`, `haircut_name`, `before_image`, `after_image`, `created_at`) VALUES
(11, 'Layered haircut', '', NULL, 'images/before/IMG_20250926_070008.jpg', 'images/after/IMG_20250926_070020.jpg', '2025-09-25 23:02:52'),
(12, 'Make up look is poilished and elegant', '', NULL, 'images/before/IMG_20250926_071120.jpg', 'images/after/IMG_20250926_071129.jpg', '2025-09-25 23:14:54'),
(13, 'Hair Rebonding', '', NULL, 'images/before/IMG_20250827_174814.jpg', 'images/after/IMG_20250827_174835.jpg', '2025-09-25 23:17:42'),
(14, 'Hair coloring rich auburn shade', '', NULL, 'images/before/IMG_20250926_072053.jpg', 'images/after/IMG_20250926_072122.jpg', '2025-09-25 23:23:31'),
(15, 'Low skin fade a textured top', '', NULL, 'images/before/IMG_20250924_071532.jpg', 'images/after/IMG_20250924_071541.jpg', '2025-09-25 23:25:25'),
(16, 'Make Up', '', NULL, 'images/before/IMG_20250929_191108.jpg', 'images/after/IMG_20250929_191123.jpg', '2025-09-30 00:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `hairstylists`
--

CREATE TABLE `hairstylists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(200) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `hairstyle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hairstylists`
--

INSERT INTO `hairstylists` (`id`, `name`, `email`, `phone`, `specialization`, `status`, `created_at`, `full_name`, `phone_number`, `role`, `hairstyle`) VALUES
(3, '', NULL, NULL, NULL, 'active', '2025-09-24 11:00:27', 'melody delacruz', '09269223220', 'Administrator', NULL),
(6, '', NULL, NULL, NULL, 'active', '2025-09-30 00:20:40', 'Nicole Acojedo', '09623224038', 'Manager', NULL),
(7, '', NULL, NULL, NULL, 'active', '2025-09-30 00:51:11', 'Lala Ursola Uy', '09657951427', 'Manager', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempt_time`, `success`, `user_agent`) VALUES
(7, 'glenard2308@gmail.com', '::1', '2025-10-25 14:25:39', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(8, 'glenard2308@gmail.com', '::1', '2025-10-25 14:51:28', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(9, 'glenard2308@gmail.com', '::1', '2025-10-25 14:51:42', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(10, 'glenard2308@gmail.com', '::1', '2025-10-25 14:54:37', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(11, 'glenard0823@gmail.com', '::1', '2025-10-25 15:14:46', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(12, 'glenard2308@gmail.com', '::1', '2025-10-25 15:32:52', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(13, 'glenard0823@gmail.com', '::1', '2025-10-25 15:33:15', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(14, 'glenard0823@gmail.com', '::1', '2025-10-25 15:33:40', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(15, 'glenard0823@gmail.com', '::1', '2025-10-25 15:42:55', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(16, 'glenard2308@gmail.com', '::1', '2025-10-25 15:49:02', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(17, 'glenard0823@gmail.com', '::1', '2025-10-25 15:50:11', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(18, 'glenard2308@gmail.com', '::1', '2025-10-25 15:53:22', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(19, 'glenard2308@gmail.com', '::1', '2025-10-26 14:17:10', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(20, 'glenard2308@gmail.com', '::1', '2025-10-26 14:17:16', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(21, 'glenard2308@gmail.com', '::1', '2025-10-26 14:17:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(22, 'glenard2308@gmail.com', '::1', '2025-10-26 14:17:26', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(23, 'glenard2308@gmail.com', '::1', '2025-10-26 14:18:58', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(24, 'glenard2308@gmail.com', '::1', '2025-10-26 14:28:53', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(25, 'glenard0823@gmail.com', '::1', '2025-10-26 14:29:35', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(26, 'glenard0823@gmail.com', '::1', '2025-10-26 14:31:35', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(27, 'glenard2308@gmail.com', '::1', '2025-10-26 14:34:50', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(28, 'glenard0823@gmail.com', '::1', '2025-10-26 14:40:52', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(29, 'glenard2308@gmail.com', '::1', '2025-11-02 16:11:26', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(30, 'glenard2308@gmail.com', '::1', '2025-11-02 16:11:31', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(31, 'glenard0823@gmail.com', '::1', '2025-11-02 16:11:40', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(32, 'glenard2308@gmail.com', '::1', '2025-11-02 16:15:52', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(33, 'glenard2308@gmail.com', '::1', '2026-03-23 06:34:03', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(34, 'glenard2308@gmail.com', '::1', '2026-03-23 06:34:08', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(35, 'glenard2308@gmail.com', '::1', '2026-03-23 06:34:12', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(36, 'glenard2308@gmail.com', '::1', '2026-03-23 06:37:10', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(37, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:45:51', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(38, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:45:55', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(39, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:46:17', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(40, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:46:22', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(41, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:46:52', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(42, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:47:08', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(43, 'glenard2308@gmail.com', '::1', '2026-03-23 06:47:18', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(44, 'glenard2308@gmail.com', '::1', '2026-03-23 06:47:22', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(45, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:47:26', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(46, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:48:04', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(47, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:48:18', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(48, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:48:43', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(49, 'glenard2308@gmail.com', '::1', '2026-03-23 06:50:44', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(50, 'glenard2308@gmail.com', '::1', '2026-03-23 06:50:52', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36'),
(51, 'erecejovelyn@gmail.com', '::1', '2026-03-23 06:51:23', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `promos`
--

CREATE TABLE `promos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promos`
--

INSERT INTO `promos` (`id`, `title`, `description`, `discount_percentage`, `discount_amount`, `valid_from`, `valid_until`, `promo_code`, `image`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Sample', 'Sample', 10, 500.00, '2025-10-25', '2025-10-29', '123', 'uploads/promos/68fce483c9d46_Hero 2.jpg', 'active', '2025-10-25 14:53:55', '2025-10-25 14:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `security_questions`
--

CREATE TABLE `security_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_questions`
--

INSERT INTO `security_questions` (`id`, `question`) VALUES
(1, 'What is your mother’s maiden name?'),
(2, 'What is the name of your first pet?'),
(3, 'What city were you born in?'),
(4, 'What is your favorite color?'),
(5, 'What is your favorite food?');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `image`) VALUES
(19, 'Hair Coloring', 'Transform your look with Oakey Premium hair color in orange red(6/43)', 2000.00, 'uploads/services/1758840040_FB_IMG_1756900640237.jpg'),
(20, 'Hair Cut', 'Long layered haircut with soft waves, designed to add volume and movement', 350.00, 'uploads/services/1758840118_Long+Layered+Cut+for+Effortless+Volume.jpg'),
(21, 'Make Up', 'Fresh Make up', 1000.00, 'uploads/services/1758840486_0914c4ee311ea3669d11458f3fe64909.jpg'),
(22, 'Men\'s Haircut', 'Clean and stylish haircut with fade, tailored to you look', 350.00, 'uploads/services/1758840770_short-skin-fade-and-swept-forward-crew-cut-for-men.jpg'),
(23, 'Make Up', 'Flawless make', 1000.00, 'uploads/services/1759191686_IMG_20250929_191123.jpg'),
(24, 'Make Up', 'flawless make up', 1000.00, 'uploads/services/1759193656_IMG_20250929_191123.jpg'),
(25, 'HairCut', 'Boys & Girls', 100.00, 'uploads/services/1759193832_Swanky-Malone-Skin-Fade.jpg'),
(26, 'Men\'s Haircut', 'SHDD', 250.00, 'uploads/services/1759193872_IMG_20250924_071541.jpg'),
(27, 'Make Ups', 'Sample', 1001.00, 'uploads/services/1761404100_Hero 3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(100) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `security_question` text DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `security_question_id` int(11) DEFAULT NULL,
  `security_answer_hash` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `id`, `full_name`, `first_name`, `last_name`, `name`, `email`, `phone_number`, `password`, `phone`, `security_question`, `security_answer`, `created_at`, `password_hash`, `role`, `security_question_id`, `security_answer_hash`, `contact_number`) VALUES
('Admin', 22, 'Nicole Acojedo', '', '', '', 'erecejovelyn@gmail.com', '', '$2y$10$rZqC.gjnp8wnzuyX58d.3OgLwZOBUmLDnetpYruhRA.RKR1m07M7u', NULL, 'What is your favorite color?', '16477688c0e00699c6cfa4497a3612d7e83c532062b64b250fed8908128ed548', '2025-05-26 23:59:06', '', 'admin', NULL, NULL, '09657951427');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hairstylists`
--
ALTER TABLE `hairstylists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_time` (`email`,`attempt_time`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`);

--
-- Indexes for table `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_questions`
--
ALTER TABLE `security_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_hours`
--
ALTER TABLE `business_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `hairstylists`
--
ALTER TABLE `hairstylists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `promos`
--
ALTER TABLE `promos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `security_questions`
--
ALTER TABLE `security_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
