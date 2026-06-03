-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 03, 2026 at 02:44 AM
-- Server version: 11.4.10-MariaDB-cll-lve-log
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tufanconx_tufanresort`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, NULL, 'System data imported from old website', 'System', NULL, '{\"rooms\": \"11 rooms added\", \"hero_slides\": \"3 slides created\", \"resort_info\": \"Updated\", \"room_images\": \"Downloaded from tufanconventionresort.com\"}', '127.0.0.1', 'CLI Import', '2026-01-26 00:26:38', '2026-01-26 00:26:38'),
(2, NULL, 'System data imported', 'System', NULL, '{\"items\": [\"rooms\", \"resort_info\", \"hero_slides\"], \"source\": \"tufanconventionresort.com\"}', '127.0.0.1', 'CLI', '2026-01-26 00:39:45', '2026-01-26 00:39:45'),
(3, 2, 'Updated booking status', 'Booking', 1, '{\"new_status\": \"checked_in\", \"old_status\": \"confirmed\"}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 00:59:02', '2026-01-26 00:59:02'),
(4, 2, 'User logged in', 'User', 2, NULL, '37.111.253.29', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 18:34:04', '2026-01-27 18:34:04'),
(5, 2, 'Reset convention bookings', 'System', NULL, '{\"action\":\"convention_booking_reset\"}', '37.111.253.29', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 18:54:10', '2026-01-27 18:54:10'),
(6, 2, 'User logged in', 'User', 2, NULL, '37.111.245.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 19:55:40', '2026-01-28 19:55:40'),
(7, 2, 'User logged out', 'User', 2, NULL, '37.111.245.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 20:03:01', '2026-01-28 20:03:01'),
(8, 4, 'User logged in', 'User', 4, NULL, '37.111.245.209', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 20:03:08', '2026-01-28 20:03:08'),
(9, 4, 'User logged out', 'User', 4, NULL, '37.111.245.169', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 20:31:05', '2026-01-28 20:31:05'),
(10, 2, 'User logged in', 'User', 2, NULL, '37.111.245.169', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 20:31:08', '2026-01-28 20:31:08'),
(11, 2, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '37.111.245.169', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 20:32:14', '2026-01-28 20:32:14'),
(12, 2, 'User logged in', 'User', 2, NULL, '37.111.245.222', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-01-28 20:39:44', '2026-01-28 20:39:44'),
(13, 2, 'User logged in', 'User', 2, NULL, '37.111.243.119', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 18:24:52', '2026-01-29 18:24:52'),
(14, 2, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '37.111.243.79', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 18:37:23', '2026-01-29 18:37:23'),
(15, 2, 'User logged out', 'User', 2, NULL, '37.111.243.79', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 18:39:29', '2026-01-29 18:39:29'),
(16, 5, 'User logged in', 'User', 5, NULL, '37.111.243.79', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 18:39:32', '2026-01-29 18:39:32'),
(17, 4, 'User logged in', 'User', 4, NULL, '150.228.135.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-30 01:21:55', '2026-01-30 01:21:55'),
(18, 4, 'User logged out', 'User', 4, NULL, '150.228.135.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-30 01:27:24', '2026-01-30 01:27:24'),
(19, 5, 'User logged in', 'User', 5, NULL, '37.111.206.111', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:31:38', '2026-02-01 09:31:38'),
(20, 5, 'User logged in', 'User', 5, NULL, '103.187.66.118', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 21:55:20', '2026-02-01 21:55:20'),
(21, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:40:05', '2026-02-02 10:40:05'),
(22, 8, 'Created booking', 'Booking', 1, '{\"customer_name\":\"Mamun\",\"room_id\":\"9\",\"total_amount\":\"2000\"}', '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:52:30', '2026-02-02 10:52:30'),
(23, 8, 'User logged out', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 11:09:48', '2026-02-02 11:09:48'),
(24, 5, 'User logged in', 'User', 5, NULL, '37.111.215.73', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-02 13:35:51', '2026-02-02 13:35:51'),
(25, 5, 'User logged in', 'User', 5, NULL, '37.111.215.73', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-02 13:36:00', '2026-02-02 13:36:00'),
(26, 5, 'User logged in', 'User', 5, NULL, '103.187.66.118', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 20:52:09', '2026-02-02 20:52:09'),
(27, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-03 09:42:06', '2026-02-03 09:42:06'),
(28, 5, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"pending\",\"new_status\":\"checked_in\"}', '115.127.105.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-03 09:43:33', '2026-02-03 09:43:33'),
(29, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 12:35:29', '2026-02-03 12:35:29'),
(30, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-03 13:09:48', '2026-02-03 13:09:48'),
(31, 5, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"checked_in\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-02-03 15:52:21', '2026-02-03 15:52:21'),
(32, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 16:24:54', '2026-02-05 16:24:54'),
(33, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 12:19:49', '2026-02-06 12:19:49'),
(34, 8, 'Created booking', 'Booking', 2, '{\"customer_name\":\"Shopon Kumar\",\"room_id\":\"9\",\"total_amount\":\"3000\"}', '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 12:22:31', '2026-02-06 12:22:31'),
(35, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 08:24:41', '2026-02-07 08:24:41'),
(36, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 14:37:00', '2026-02-07 14:37:00'),
(37, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 11:34:31', '2026-02-08 11:34:31'),
(38, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 11:53:31', '2026-02-08 11:53:31'),
(39, 5, 'Updated booking status', 'Booking', 3, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_in\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 12:40:53', '2026-02-08 12:40:53'),
(40, 5, 'Updated booking status', 'Booking', 3, '{\"old_status\":\"checked_in\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 12:40:59', '2026-02-08 12:40:59'),
(41, 5, 'Updated booking status', 'Booking', 4, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_in\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 12:47:36', '2026-02-08 12:47:36'),
(42, 8, 'User logged out', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 13:08:29', '2026-02-08 13:08:29'),
(43, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 13:08:59', '2026-02-08 13:08:59'),
(44, 5, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 14:14:31', '2026-02-08 14:14:31'),
(45, 5, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 14:18:25', '2026-02-08 14:18:25'),
(46, 5, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_in\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 15:01:05', '2026-02-08 15:01:05'),
(47, 5, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"checked_in\",\"new_status\":\"checked_in\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 15:09:50', '2026-02-08 15:09:50'),
(48, 5, 'User logged in', 'User', 5, NULL, '37.111.206.180', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 15:17:33', '2026-02-08 15:17:33'),
(49, 5, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"checked_in\",\"new_status\":\"checked_out\"}', '37.111.206.180', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-08 15:18:27', '2026-02-08 15:18:27'),
(50, 5, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:20:25', '2026-02-08 16:20:25'),
(51, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:55:36', '2026-02-08 16:55:36'),
(52, 5, 'User logged in', 'User', 5, NULL, '103.187.66.118', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 19:51:09', '2026-02-08 19:51:09'),
(53, 5, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '103.187.66.118', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 19:54:10', '2026-02-08 19:54:10'),
(54, 5, 'User logged in', 'User', 5, NULL, '103.187.66.118', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-09 07:09:36', '2026-02-09 07:09:36'),
(55, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 08:49:26', '2026-02-09 08:49:26'),
(56, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 12:01:16', '2026-02-09 12:01:16'),
(57, 8, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 12:31:09', '2026-02-09 12:31:09'),
(58, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:18:08', '2026-02-09 13:18:08'),
(59, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:39:24', '2026-02-09 13:39:24'),
(60, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:46:02', '2026-02-09 14:46:02'),
(61, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:53:34', '2026-02-09 14:53:34'),
(62, 5, 'Updated booking status', 'Booking', 5, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:35:27', '2026-02-09 15:35:27'),
(63, 5, 'Updated booking status', 'Booking', 5, '{\"old_status\":\"checked_out\",\"new_status\":\"confirmed\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:59:04', '2026-02-09 15:59:04'),
(64, 5, 'Updated booking status', 'Booking', 5, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:59:28', '2026-02-09 15:59:28'),
(65, 5, 'Updated booking status', 'Booking', 8, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 16:22:39', '2026-02-09 16:22:39'),
(66, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 12:54:32', '2026-02-10 12:54:32'),
(67, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 13:32:45', '2026-02-10 13:32:45'),
(68, 5, 'User logged in', 'User', 5, NULL, '37.111.206.239', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-11 13:20:57', '2026-02-11 13:20:57'),
(69, 5, 'User logged in', 'User', 5, NULL, '37.111.206.239', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-11 13:21:07', '2026-02-11 13:21:07'),
(70, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:34:28', '2026-02-11 18:34:28'),
(71, 8, 'Updated booking status', 'Booking', 7, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 18:42:24', '2026-02-11 18:42:24'),
(72, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 08:22:18', '2026-02-15 08:22:18'),
(73, 5, 'Reset room bookings', 'System', NULL, '{\"action\":\"room_booking_reset\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:02:48', '2026-02-15 09:02:48'),
(74, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 14:21:55', '2026-02-15 14:21:55'),
(75, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 11:37:29', '2026-02-16 11:37:29'),
(76, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-17 08:47:19', '2026-02-17 08:47:19'),
(77, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-17 13:38:38', '2026-02-17 13:38:38'),
(78, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 07:59:13', '2026-02-19 07:59:13'),
(79, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 11:24:30', '2026-02-19 11:24:30'),
(80, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 13:54:15', '2026-02-19 13:54:15'),
(81, 5, 'User logged in', 'User', 5, NULL, '103.187.66.118', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 14:05:21', '2026-02-21 14:05:21'),
(82, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:33:18', '2026-02-22 12:33:18'),
(83, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:52:13', '2026-02-22 12:52:13'),
(84, 5, 'User logged in', 'User', 5, NULL, '37.111.213.135', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 08:35:13', '2026-02-23 08:35:13'),
(85, 5, 'User logged in', 'User', 5, NULL, '37.111.213.55', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 16:30:32', '2026-02-23 16:30:32'),
(86, 5, 'User logged in', 'User', 5, NULL, '37.111.213.55', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 16:30:40', '2026-02-23 16:30:40'),
(87, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 11:16:23', '2026-02-24 11:16:23'),
(88, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:05:01', '2026-02-24 12:05:01'),
(89, 8, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:10:18', '2026-02-24 12:10:18'),
(90, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:03:55', '2026-02-25 08:03:55'),
(91, 5, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:44:59', '2026-02-25 08:44:59'),
(92, 5, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"checked_out\",\"new_status\":\"confirmed\"}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:58:38', '2026-02-25 08:58:38'),
(93, 5, 'User logged out', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 09:13:41', '2026-02-25 09:13:41'),
(94, 8, 'User logged in', 'User', 8, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 09:13:56', '2026-02-25 09:13:56'),
(95, 8, 'User logged out', 'User', 8, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 09:14:39', '2026-02-25 09:14:39'),
(96, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 09:14:42', '2026-02-25 09:14:42'),
(97, 5, 'Created convention booking', 'ConventionBooking', 1, '{\"customer_name\":\"Mir Javed Jahanger\",\"hall_id\":null,\"event_date\":\"2026-02-25T00:00:00.000000Z\",\"total_amount\":null}', '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:05:10', '2026-02-25 10:05:10'),
(98, 5, 'User logged out', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:48:57', '2026-02-25 10:48:57'),
(99, 8, 'User logged in', 'User', 8, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:49:12', '2026-02-25 10:49:12'),
(100, 8, 'User logged out', 'User', 8, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:42:02', '2026-02-25 12:42:02'),
(101, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:42:04', '2026-02-25 12:42:04'),
(102, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-25 13:27:25', '2026-02-25 13:27:25'),
(103, 5, 'User logged in', 'User', 5, NULL, '37.111.212.107', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 15:05:17', '2026-03-02 15:05:17'),
(104, 2, 'User logged in', 'User', 2, NULL, '37.111.212.107', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-02 15:05:23', '2026-03-02 15:05:23'),
(105, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:03:14', '2026-03-04 11:03:14'),
(106, 5, 'User logged in', 'User', 5, NULL, '103.190.133.14', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:43:31', '2026-03-04 11:43:31'),
(107, 8, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:52:48', '2026-03-04 11:52:48'),
(108, 8, 'Updated booking status', 'Booking', 3, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_in\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 12:05:10', '2026-03-04 12:05:10'),
(109, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 14:33:10', '2026-03-04 14:33:10'),
(110, 5, 'User logged in', 'User', 5, NULL, '37.111.211.208', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 19:01:45', '2026-03-04 19:01:45'),
(111, 5, 'User logged in', 'User', 5, NULL, '37.111.211.208', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 19:01:55', '2026-03-04 19:01:55'),
(112, 5, 'User logged in', 'User', 5, NULL, '37.111.211.208', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-04 19:02:04', '2026-03-04 19:02:04'),
(113, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 11:18:41', '2026-03-05 11:18:41'),
(114, 8, 'Updated booking status', 'Booking', 2, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 11:20:20', '2026-03-05 11:20:20'),
(115, 8, 'Updated booking status', 'Booking', 1, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 11:21:42', '2026-03-05 11:21:42'),
(116, 8, 'User logged out', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 12:29:08', '2026-03-05 12:29:08'),
(117, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-05 13:13:24', '2026-03-05 13:13:24'),
(118, 5, 'User logged in', 'User', 5, NULL, '103.187.66.97', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-06 11:53:06', '2026-03-06 11:53:06'),
(119, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 14:17:19', '2026-03-06 14:17:19'),
(120, 8, 'User logged out', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 14:42:59', '2026-03-06 14:42:59'),
(121, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-06 15:05:14', '2026-03-06 15:05:14'),
(122, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 08:02:00', '2026-03-07 08:02:00'),
(123, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 09:56:35', '2026-03-07 09:56:35'),
(124, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 10:00:58', '2026-03-07 10:00:58'),
(125, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-07 10:42:04', '2026-03-07 10:42:04'),
(126, 5, 'User logged in', 'User', 5, NULL, '103.187.66.97', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-09 03:25:45', '2026-03-09 03:25:45'),
(127, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-09 08:39:54', '2026-03-09 08:39:54'),
(128, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-11 10:20:53', '2026-03-11 10:20:53'),
(129, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-12 12:05:29', '2026-03-12 12:05:29'),
(130, 5, 'User logged in', 'User', 5, NULL, '37.111.245.230', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-17 20:46:01', '2026-03-17 20:46:01'),
(131, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-03-17 22:14:34', '2026-03-17 22:14:34'),
(132, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:16:28', '2026-03-18 11:16:28'),
(133, 8, 'Updated booking status', 'Booking', 4, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:19:40', '2026-03-18 11:19:40'),
(134, 8, 'Updated booking status', 'Booking', 3, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:34:51', '2026-03-18 11:34:51'),
(135, 5, 'User logged in', 'User', 5, NULL, '37.111.245.33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-18 12:29:37', '2026-03-18 12:29:37'),
(136, 8, 'Updated booking status', 'Booking', 6, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_in\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:42:50', '2026-03-18 13:42:50'),
(137, 8, 'Updated booking status', 'Booking', 6, '{\"old_status\":\"checked_in\",\"new_status\":\"cancelled\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:43:01', '2026-03-18 13:43:01'),
(138, 5, 'User logged in', 'User', 5, NULL, '37.111.243.59', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', '2026-03-20 13:04:29', '2026-03-20 13:04:29'),
(139, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 15:00:18', '2026-03-25 15:00:18'),
(140, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 13:58:50', '2026-03-26 13:58:50'),
(141, 8, 'Updated booking status', 'Booking', 6, '{\"old_status\":\"cancelled\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 14:08:59', '2026-03-26 14:08:59'),
(142, 8, 'Updated booking status', 'Booking', 5, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 14:10:33', '2026-03-26 14:10:33'),
(143, 8, 'User logged in', 'User', 8, NULL, '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 15:09:01', '2026-03-29 15:09:01'),
(144, 8, 'Updated booking status', 'Booking', 8, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 15:09:26', '2026-03-29 15:09:26'),
(145, 8, 'Updated booking status', 'Booking', 7, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.129', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 15:12:06', '2026-03-29 15:12:06'),
(146, 5, 'User logged in', 'User', 5, NULL, '115.127.105.235', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 09:50:58', '2026-03-31 09:50:58'),
(147, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 12:07:02', '2026-04-01 12:07:02'),
(148, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 11:39:21', '2026-04-05 11:39:21'),
(149, 8, 'Created convention booking', 'ConventionBooking', 2, '{\"customer_name\":\"jahangir\",\"hall_id\":null,\"event_date\":\"2026-04-05T00:00:00.000000Z\",\"total_amount\":null}', '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 11:45:48', '2026-04-05 11:45:48'),
(150, 8, 'User logged out', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 11:59:51', '2026-04-05 11:59:51'),
(151, 8, 'User logged in', 'User', 8, NULL, '103.153.211.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 13:01:37', '2026-04-05 13:01:37'),
(152, 8, 'User logged in', 'User', 8, NULL, '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 10:54:34', '2026-05-12 10:54:34'),
(153, 8, 'Updated booking status', 'Booking', 11, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 11:24:13', '2026-05-12 11:24:13'),
(154, 8, 'User logged in', 'User', 8, NULL, '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:39:23', '2026-05-13 10:39:23'),
(155, 8, 'Updated booking status', 'Booking', 14, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:40:10', '2026-05-13 10:40:10'),
(156, 8, 'Updated booking status', 'Booking', 13, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:40:54', '2026-05-13 10:40:54'),
(157, 8, 'Updated booking status', 'Booking', 12, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:41:28', '2026-05-13 10:41:28'),
(158, 8, 'Updated booking status', 'Booking', 9, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:42:55', '2026-05-13 10:42:55'),
(159, 8, 'Updated booking status', 'Booking', 10, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-13 10:43:19', '2026-05-13 10:43:19'),
(160, 5, 'User logged in', 'User', 5, NULL, '202.4.117.73', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 12:11:43', '2026-05-20 12:11:43'),
(161, 8, 'User logged in', 'User', 8, NULL, '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 12:44:20', '2026-05-20 12:44:20'),
(162, 8, 'Updated booking status', 'Booking', 15, '{\"old_status\":\"confirmed\",\"new_status\":\"checked_out\"}', '103.153.211.83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20 13:18:04', '2026-05-20 13:18:04'),
(163, 5, 'User logged in', 'User', 5, NULL, '202.4.117.73', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 09:44:38', '2026-06-03 09:44:38'),
(164, 5, 'User logged out', 'User', 5, NULL, '202.4.117.73', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-03 10:06:24', '2026-06-03 10:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `additional_guests`
--

CREATE TABLE `additional_guests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `nid` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `addon_services`
--

CREATE TABLE `addon_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `category` enum('decoration','sound_system','photography','catering','transport','room_service','laundry','parking','other') NOT NULL DEFAULT 'other',
  `service_type` enum('room','convention','both') DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addon_services`
--

INSERT INTO `addon_services` (`id`, `name`, `description`, `price`, `unit`, `category`, `service_type`, `is_active`, `created_at`, `updated_at`) VALUES
(107, 'চেয়ার-প্লাস্টিক', NULL, 5.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(108, 'চেয়ার-প্লাস্টিক (হেভি)', NULL, 10.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(109, 'চেয়ার (ভি.আই.পি কুশন)', NULL, 200.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(110, 'চেয়ার কভার', NULL, 15.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(111, 'হাড়ি বা ডেকচি ২৬ পর্যন্ত', NULL, 100.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(112, 'হাড়ি বা ডেকচি ২৭/৩০', NULL, 150.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(113, 'কড়াই ছোট', NULL, 100.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(114, 'কড়াই গোহার', NULL, 200.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(115, 'লাঙ্গা', NULL, 50.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(116, 'হামান দিস্তা', NULL, 15.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(117, 'বিরা মেশিন', NULL, 250.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(118, 'ওয়েল গীট', NULL, 75.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(119, 'গাদলা গদি', NULL, 15.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(120, 'গাদলা মালামাইন', NULL, 15.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(121, 'জগ প্লাস্টিক', NULL, 15.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(122, 'জল কাট', NULL, 20.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(123, 'মালামাইন প্লেট', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(124, 'কড়ির প্লেট', NULL, 10.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(125, 'প্লেট হাফ কড়ি', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(126, 'গ্লাস মালামাইন', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(127, 'গ্লাস কাঁচের', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(128, 'রাইস ডিস মালামাইন', NULL, 20.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(129, 'চামচ স্টিল', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(130, 'চা অথবা কাটা চামচ', NULL, 5.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(131, 'পানির ড্রাম', NULL, 100.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(132, 'বেসিন ডাবল', NULL, 500.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(133, 'বেসিন সিঙ্গেল', NULL, 100.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(134, 'বরের প্লেট', NULL, 200.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(135, 'সুপ বাটি', NULL, 10.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(136, 'বালতি', NULL, 50.00, 'পিস', 'catering', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(137, 'টেবিল লম্বা', NULL, 50.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(138, 'টেবিল গোল', NULL, 100.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(139, 'টেবিল রুশ', NULL, 20.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(140, 'টেবিল কেচি', NULL, 400.00, 'জোড়া', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(141, 'টেবিল ফেস সিঙ্গেল', NULL, 200.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(142, 'সামিয়ানা ১৫×২০ ফুট', NULL, 300.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(143, 'চেন টু কালার', NULL, 50.00, 'সেট', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(144, 'চেন এক কালার', NULL, 30.00, 'সেট', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(145, 'বড় পতাকা পাইপ ফিটিং সহ', NULL, 500.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(146, 'শো পর্দা সহ লাইট সাইড ৩০ ফুট', NULL, 50.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(147, 'সাইড পানি', NULL, 100.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(148, 'গিলাপ ২০×৩০ সুট', NULL, 400.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(149, 'গিলাপ ৩০×৩০ সুট', NULL, 600.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(150, 'গিলাপ ৩০×৪০ সুট', NULL, 800.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(151, 'জেনারেটর', NULL, 2500.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(152, 'প্যান্ডেল মাইকে স্যাম্পল', NULL, 5.00, 'স্কয়ার', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(153, 'গেট অর্ডিনারি', NULL, 5.00, 'স্কয়ার', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(154, 'প্যান্ডেল সাধারণ', NULL, 15.00, 'স্কয়ার', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(155, 'প্যান্ডেল অর্ডিনারি', NULL, 15.00, 'স্কয়ার', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(156, 'বিড়িকান প্যান্ডেল', NULL, 10000.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(157, 'গেইট স্ট্যান্ড', NULL, 200.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(158, 'পাটি কান্দেত', NULL, 50.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:30:06', '2026-02-25 10:30:06'),
(211, 'বেস্ট কার্পেট ৩০×৪ ফুট', NULL, 400.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(212, 'রেড কার্পেট ৪/৩০ ফুট', NULL, 500.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(213, 'স্বর্গ হাট তুল আদম', NULL, 4000.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(214, 'সাধার ঝালার ডিজাইন', NULL, 2000.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(215, 'ফ্লাস স্ট্যান্ড', NULL, 50.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(216, 'কার বিয়াল ২০×৩০ ফুট', NULL, 200.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(217, 'রোড সিফনি বোট', NULL, 300.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(218, 'পুংকা কাষ্টম', NULL, 50.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(219, 'শীতলের সুন্দর চব', NULL, 200.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(220, 'সিড ড্রয়ার ক্যানের', NULL, 300.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(221, 'সিড ড্রয়ার স্টিলের', NULL, 300.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(222, 'সোফা সেট ১ সিটের', NULL, 300.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(223, 'সোফা দুই সিটের তিতাস', NULL, 1500.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(224, 'সোফা ৩ সিটের তিতাস', NULL, 2000.00, 'পিস', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(225, 'ওয়েটিং ড্রয়াস ৩ টেবিলের', NULL, 800.00, 'সেট', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(226, 'সোফা ৫ টেবিলের তিতাস', NULL, 1000.00, 'সেট', 'decoration', 'convention', 1, '2026-02-25 10:31:13', '2026-02-25 10:31:13'),
(295, 'গেইট সিলেড রোড এম হাইলাইট ছোট', NULL, 10000.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(296, 'গেইট সিলেড রোড এম হাইলাইট বড়', NULL, 15000.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(297, 'টিউব লাইট', NULL, 50.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(298, 'হ্যালোজেন লাইট', NULL, 100.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(299, 'লাইট সেট একস্ট্রা', NULL, 500.00, 'সেট', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(300, 'পার্কিং লাইট', NULL, 20.00, 'পিস', 'other', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(301, 'স্টেইন ওয়্যার ১.৫ ফুট', NULL, 50.00, 'ফেন্স', 'decoration', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(302, 'স্টেজ ওয়্যা ২.৫ ফুট', NULL, 100.00, 'ফেন্স', 'decoration', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44'),
(303, 'স্টেজ ওয়্যার ৫ ফুট', NULL, 300.00, 'ফেন্স', 'decoration', 'convention', 1, '2026-02-25 10:31:44', '2026-02-25 10:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_settings`
--

CREATE TABLE `admin_menu_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_key` varchar(255) NOT NULL,
  `menu_label` varchar(255) NOT NULL,
  `menu_icon` varchar(255) NOT NULL DEFAULT 'fas fa-circle',
  `route_name` varchar(255) NOT NULL,
  `route_pattern` varchar(255) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menu_settings`
--

INSERT INTO `admin_menu_settings` (`id`, `menu_key`, `menu_label`, `menu_icon`, `route_name`, `route_pattern`, `group_name`, `order`, `is_active`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', 'Dashboard', 'fas fa-chart-line', 'admin.dashboard', 'admin.dashboard', NULL, 1, 1, 1, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(2, 'todays_summary', 'Today\'s Summary', 'fas fa-calendar-day', 'admin.todays-summary', 'admin.todays-summary', NULL, 2, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(3, 'rooms', 'Rooms', 'fas fa-bed', 'admin.rooms.index', 'admin.rooms.*', 'Rooms Management', 10, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(4, 'room_types', 'Room Types', 'fas fa-door-open', 'admin.room-types.index', 'admin.room-types.*', 'Rooms Management', 11, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(5, 'search_book_room', 'Search & Book', 'fas fa-search-plus', 'admin.premium-booking.index', 'admin.premium-booking.*', 'Room Bookings', 20, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(6, 'all_bookings', 'All Bookings', 'fas fa-list', 'admin.bookings.index', 'admin.bookings.*', 'Room Bookings', 21, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(7, 'search_book_hall', 'Search & Book Hall', 'fas fa-search-plus', 'admin.premium-convention.index', 'admin.premium-convention.*', 'Convention Halls', 30, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(8, 'all_hall_bookings', 'All Hall Bookings', 'fas fa-list', 'admin.convention-bookings.index', 'admin.convention-bookings.*', 'Convention Halls', 31, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(9, 'manage_halls', 'Manage Halls', 'fas fa-building', 'admin.convention-halls.index', 'admin.convention-halls.*', 'Convention Halls', 32, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(10, 'addon_services', 'Addon Services (Convention)', 'fas fa-plus-circle', 'admin.addon-services.index', 'admin.addon-services.*', 'Services', 40, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(11, 'food_packages', 'Food Packages (Convention)', 'fas fa-utensils', 'admin.food-packages.index', 'admin.food-packages.*', 'Services', 41, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(12, 'hero_slides', 'Hero Slides', 'fas fa-images', 'admin.hero-slides.index', 'admin.hero-slides.*', 'Website', 50, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(13, 'room_reports', 'Room Bookings Report', 'fas fa-file-alt', 'admin.reports.room-bookings', 'admin.reports.room-bookings', 'Reports', 60, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(14, 'convention_reports', 'Convention Bookings Report', 'fas fa-chart-bar', 'admin.reports.convention-bookings', 'admin.reports.convention-bookings', 'Reports', 61, 1, 0, '2026-01-25 23:20:07', '2026-01-25 23:20:07'),
(15, 'users', 'User Management', 'fas fa-users', 'admin.users.index', 'admin.users.*', 'System', 70, 1, 1, '2026-01-25 23:20:07', '2026-01-25 23:24:37'),
(16, 'settings', 'Settings', 'fas fa-cog', 'admin.settings.index', 'admin.settings.*', 'System', 71, 1, 1, '2026-01-25 23:20:07', '2026-01-25 23:26:07'),
(17, 'activity_logs', 'Activity Logs', 'fas fa-history', 'admin.activity-logs.index', 'admin.activity-logs.*', 'System', 91, 1, 0, '2026-01-25 23:24:28', '2026-01-25 23:24:28'),
(18, 'extra_charge_categories', 'Extra Charges (Resort)', 'fas fa-tags', 'admin.extra-charge-categories.index', 'admin.extra-charge-categories.*', 'Services', 42, 1, 0, '2026-02-08 08:49:31', '2026-02-08 08:49:31'),
(19, 'customers', 'Customers', 'fas fa-users', 'admin.customers.index', 'admin.customers.*', 'Management', 25, 1, 0, '2026-02-08 10:20:14', '2026-02-08 10:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_nid` varchar(255) DEFAULT NULL,
  `customer_photo` varchar(255) DEFAULT NULL,
  `customer_nid_document` varchar(255) DEFAULT NULL,
  `passport_number` varchar(255) DEFAULT NULL,
  `passport_document` varchar(255) DEFAULT NULL,
  `visiting_card` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `reference_name` varchar(255) DEFAULT NULL,
  `reference_phone` varchar(255) DEFAULT NULL,
  `customer_whatsapp` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_date` date NOT NULL,
  `check_out_time` time DEFAULT NULL,
  `number_of_guests` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','mfs','bkash') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','partial','paid','refunded') NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',
  `extra_charges` decimal(10,2) DEFAULT 0.00,
  `extra_charges_description` text DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `discount_type` varchar(50) DEFAULT 'none',
  `food_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `food_package_guests` int(11) DEFAULT 0,
  `food_package_cost` decimal(10,2) DEFAULT 0.00,
  `selected_addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_addons`)),
  `addons_cost` decimal(10,2) DEFAULT 0.00,
  `extras` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extras`)),
  `additional_guests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_guests`)),
  `notes` text DEFAULT NULL,
  `ac_preference` varchar(255) NOT NULL DEFAULT 'ac',
  `vat_enabled` tinyint(4) DEFAULT 0,
  `vat_amount` decimal(10,2) DEFAULT 0.00,
  `created_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `bkash_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `extra_charges_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_charges_data`)),
  `discount_reference` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `customer_name`, `customer_nid`, `customer_photo`, `customer_nid_document`, `passport_number`, `passport_document`, `visiting_card`, `customer_phone`, `reference_name`, `reference_phone`, `customer_whatsapp`, `customer_email`, `customer_address`, `check_in_date`, `check_in_time`, `check_out_date`, `check_out_time`, `number_of_guests`, `total_amount`, `advance_payment`, `remaining_payment`, `payment_method`, `payment_status`, `status`, `extra_charges`, `extra_charges_description`, `discount_amount`, `discount_percentage`, `discount_type`, `food_package_id`, `food_package_guests`, `food_package_cost`, `selected_addons`, `addons_cost`, `extras`, `additional_guests`, `notes`, `ac_preference`, `vat_enabled`, `vat_amount`, `created_by_id`, `created_at`, `updated_at`, `company_name`, `bkash_number`, `bank_name`, `extra_charges_data`, `discount_reference`) VALUES
(1, NULL, 'tanvir Hasan Khan', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'Satkhira', '2026-03-04', '12:00:00', '2026-03-05', '12:00:00', 1, 3000.00, 2500.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 500.00, 0.00, 'flat', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 203]', 'ac', 0, 0.00, 8, '2026-03-04 14:35:17', '2026-03-05 11:21:57', 'Tufan Company', NULL, NULL, NULL, NULL),
(2, NULL, 'Tanvir', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'satkhira', '2026-03-04', '12:00:00', '2026-03-05', '12:00:00', 1, 6000.00, 5000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 1000.00, 0.00, 'flat', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 205, 301]', 'ac', 0, 0.00, 8, '2026-03-04 14:39:26', '2026-03-05 11:20:37', 'Tufan Company', NULL, NULL, NULL, NULL),
(3, NULL, 'tanvir Hasan Khan', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'Satkhira', '2026-03-05', '12:00:00', '2026-03-06', '12:00:00', 1, 3000.00, 0.00, 3000.00, 'cash', 'pending', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 204]', 'ac', 0, 0.00, 8, '2026-03-05 11:34:42', '2026-03-18 11:34:51', 'Tufan Company', NULL, NULL, NULL, NULL),
(4, NULL, 'tanvir', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'satkhira', '2026-03-18', '12:00:00', '2026-03-19', '12:00:00', 1, 3000.00, 0.00, 3000.00, 'cash', 'pending', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 301]', 'ac', 0, 0.00, 8, '2026-03-18 11:18:08', '2026-03-18 11:19:40', 'tufan resort', NULL, NULL, NULL, NULL),
(5, NULL, 'tanvir Hasan Khan', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'Satkhira', '2026-03-18', '12:00:00', '2026-03-19', '12:00:00', 1, 4000.00, 4140.00, 0.00, 'cash', 'paid', 'checked_out', 140.00, 'Breakfast = ৳70.00; Water 1 Litter × 2 = ৳70.00', 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 201]', 'ac', 0, 0.00, 8, '2026-03-18 13:22:33', '2026-03-26 14:10:45', 'Tufan Company', NULL, NULL, NULL, NULL),
(6, NULL, 'tanvir Hasan Khan', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', 'SHEIKH TANJIM KALAM TAMAL', '01818825363', NULL, NULL, 'Satkhira', '2026-03-19', '12:00:00', '2026-03-20', '12:00:00', 1, 4000.00, 4090.00, 0.00, 'cash', 'paid', 'checked_out', 90.00, 'Tea = ৳20.00; Breakfast = ৳70.00', 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 202]', 'ac', 0, 0.00, 8, '2026-03-18 13:40:16', '2026-03-26 14:09:15', 'Tufan Company', NULL, NULL, NULL, NULL),
(7, 4, 'Alim Mia', '5545272139', NULL, NULL, NULL, NULL, NULL, '01713336853', NULL, NULL, NULL, NULL, 'Dhaka', '2026-03-26', '15:45:00', '2026-03-28', '12:00:00', 1, 6000.00, 4000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 3000.00, 0.00, 'flat', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, '[Rooms: 203]', 'ac', 0, 0.00, 8, '2026-03-26 14:06:04', '2026-03-29 15:17:03', 'Square Pharma', NULL, NULL, NULL, NULL),
(8, NULL, 'Amirul Islam', NULL, NULL, NULL, NULL, NULL, NULL, '01717090960', NULL, NULL, NULL, NULL, 'Mushipara Satkhira', '2026-03-26', '12:00:00', '2026-03-27', '12:00:00', 1, 2000.00, 1500.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 500.00, 0.00, 'flat', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 304]', 'ac', 0, 0.00, 8, '2026-03-26 14:08:15', '2026-03-29 15:10:56', 'Weading Program', NULL, NULL, NULL, 'tanvir'),
(9, 4, 'Kabir Hossain Bhuya', NULL, NULL, NULL, NULL, NULL, NULL, '01911939250', NULL, NULL, NULL, NULL, 'Dhaka', '2026-05-13', '12:00:00', '2026-05-14', '12:00:00', 1, 8000.00, 4000.00, 4000.00, 'cash', 'partial', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, '[Rooms: 201]', 'ac', 0, 0.00, 8, '2026-05-12 11:08:19', '2026-05-13 10:43:05', NULL, NULL, NULL, NULL, NULL),
(10, NULL, 'Mohidul (Membor)', NULL, NULL, NULL, NULL, NULL, NULL, '01799046334', 'MIthu Khan Satkhira', NULL, NULL, NULL, 'Keragachi, Kolaroya, Satkhira', '2026-05-11', '12:00:00', '2026-05-13', '12:00:00', 1, 8000.00, 8000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 202]', 'ac', 0, 0.00, 8, '2026-05-12 11:12:10', '2026-05-13 10:43:32', NULL, NULL, NULL, NULL, NULL),
(11, NULL, 'Mohidul (Membor)', NULL, NULL, NULL, NULL, NULL, NULL, '01799046334', 'MIthu Khan Satkhira', NULL, NULL, NULL, 'Keragachi, Kolaroya, Satkhira', '2026-05-11', '12:00:00', '2026-05-12', '12:00:00', 1, 3000.00, 0.00, 3000.00, 'cash', 'pending', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 203]', 'ac', 0, 0.00, 8, '2026-05-12 11:13:28', '2026-05-12 11:24:13', NULL, NULL, NULL, NULL, NULL),
(12, NULL, 'Saifun Nasir', NULL, NULL, NULL, NULL, NULL, NULL, '01704123375', NULL, NULL, NULL, NULL, NULL, '2026-05-11', '12:00:00', '2026-05-13', '12:00:00', 1, 6000.00, 6000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 301]', 'ac', 0, 0.00, 8, '2026-05-12 11:14:31', '2026-05-13 10:41:39', NULL, NULL, NULL, NULL, NULL),
(13, NULL, 'Md Mynul Islam', '4610792907', NULL, NULL, NULL, NULL, NULL, '01718033991', NULL, NULL, NULL, NULL, 'Barishal', '2026-05-11', '12:00:00', '2026-05-13', '12:00:00', 1, 4000.00, 4000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 304]', 'ac', 0, 0.00, 8, '2026-05-12 11:20:15', '2026-05-13 10:41:08', 'Cropem Agro Ltd', NULL, NULL, NULL, NULL),
(14, NULL, 'Sajuti Dhar', '5553086802', NULL, NULL, NULL, NULL, NULL, '07152452947', NULL, NULL, NULL, NULL, 'Jamalpur', '2026-05-11', '12:00:00', '2026-05-13', '12:00:00', 1, 4000.00, 4000.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 0.00, 0.00, 'none', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, ' [Rooms: 303]', 'ac', 0, 0.00, 8, '2026-05-12 11:23:02', '2026-05-13 10:40:33', 'Ramru Ngo', NULL, NULL, NULL, NULL),
(15, 4, 'tanvir Hasan Khan', NULL, NULL, NULL, NULL, NULL, NULL, '01958216728', NULL, NULL, NULL, NULL, 'Satkhira', '2026-05-20', '12:00:00', '2026-05-22', '12:00:00', 1, 4000.00, 3500.00, 0.00, 'cash', 'paid', 'checked_out', 0.00, NULL, 500.00, 0.00, 'flat', NULL, 0, 0.00, NULL, 0.00, NULL, NULL, '[Rooms: 201]', 'ac', 0, 0.00, 8, '2026-05-20 13:15:53', '2026-05-20 13:21:16', 'Tufan Company', NULL, NULL, NULL, 'tanvir');

-- --------------------------------------------------------

--
-- Table structure for table `booking_payments`
--

CREATE TABLE `booking_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','card','mfs') NOT NULL DEFAULT 'cash',
  `type` enum('advance','payment','refund') NOT NULL DEFAULT 'payment',
  `note` text DEFAULT NULL,
  `recorded_by_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_payments`
--

INSERT INTO `booking_payments` (`id`, `booking_id`, `amount`, `method`, `type`, `note`, `recorded_by_id`, `created_at`, `updated_at`) VALUES
(1, 1, 500.00, 'cash', 'advance', 'Initial advance payment during booking creation', 8, '2026-03-04 14:35:17', '2026-03-04 14:35:17'),
(2, 2, 1000.00, 'cash', 'advance', 'Initial advance payment during booking creation', 8, '2026-03-04 14:39:26', '2026-03-04 14:39:26'),
(3, 5, 2000.00, 'cash', 'advance', 'Initial advance payment during booking creation', 8, '2026-03-18 13:22:33', '2026-03-18 13:22:33'),
(4, 6, 1000.00, 'cash', 'advance', 'Initial advance payment during booking creation', 8, '2026-03-18 13:40:16', '2026-03-18 13:40:16');

-- --------------------------------------------------------

--
-- Table structure for table `booking_rooms`
--

CREATE TABLE `booking_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `price_per_night` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `booking_rooms`
--

INSERT INTO `booking_rooms` (`id`, `booking_id`, `room_id`, `price_per_night`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 3000.00, '2026-03-04 14:35:17', '2026-03-04 14:35:17'),
(2, 2, 8, 3000.00, '2026-03-04 14:39:26', '2026-03-04 14:39:26'),
(3, 2, 9, 3000.00, '2026-03-04 14:39:26', '2026-03-04 14:39:26'),
(4, 3, 7, 3000.00, '2026-03-05 11:34:42', '2026-03-05 11:34:42'),
(5, 4, 9, 3000.00, '2026-03-18 11:18:08', '2026-03-18 11:18:08'),
(6, 5, 4, 4000.00, '2026-03-18 13:22:33', '2026-03-18 13:22:33'),
(7, 6, 5, 4000.00, '2026-03-18 13:40:16', '2026-03-18 13:40:16'),
(8, 7, 6, 3000.00, '2026-03-26 14:06:04', '2026-03-26 14:06:04'),
(9, 8, 12, 2000.00, '2026-03-26 14:08:15', '2026-03-26 14:08:15'),
(10, 9, 4, 4000.00, '2026-05-12 11:08:19', '2026-05-12 11:08:19'),
(11, 10, 5, 4000.00, '2026-05-12 11:12:10', '2026-05-12 11:12:10'),
(12, 11, 6, 3000.00, '2026-05-12 11:13:28', '2026-05-12 11:13:28'),
(13, 12, 9, 3000.00, '2026-05-12 11:14:31', '2026-05-12 11:14:31'),
(14, 13, 12, 2000.00, '2026-05-12 11:20:15', '2026-05-12 11:20:15'),
(15, 14, 11, 2000.00, '2026-05-12 11:23:02', '2026-05-12 11:23:02'),
(16, 15, 4, 4000.00, '2026-05-20 13:15:53', '2026-05-20 13:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('tufan_convention_resort_cache_convention_halls', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:20:\"PADMAHALL ( NON AC )\";s:11:\"description\";s:248:\"No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.\";s:10:\"dimensions\";s:7:\"5000.00\";s:12:\"max_capacity\";s:3:\"150\";s:13:\"price_per_day\";s:8:\"20000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:30:\"[\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:65:\"[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]\";s:11:\"event_types\";s:91:\"[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]\";s:10:\"time_slots\";s:90:\"[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-04-05 09:02:46\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:20:\"PADMAHALL ( NON AC )\";s:11:\"description\";s:248:\"No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.\";s:10:\"dimensions\";s:7:\"5000.00\";s:12:\"max_capacity\";s:3:\"150\";s:13:\"price_per_day\";s:8:\"20000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:30:\"[\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:65:\"[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]\";s:11:\"event_types\";s:91:\"[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]\";s:10:\"time_slots\";s:90:\"[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-04-05 09:02:46\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:18:\"MEGHNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"1000.00\";s:12:\"max_capacity\";s:2:\"50\";s:13:\"price_per_day\";s:8:\"25000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:51:11\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:09\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:18:\"MEGHNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"1000.00\";s:12:\"max_capacity\";s:2:\"50\";s:13:\"price_per_day\";s:8:\"25000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:51:11\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:09\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:18:\"JAMUNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"3000.00\";s:12:\"max_capacity\";s:2:\"70\";s:13:\"price_per_day\";s:8:\"35000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:52:29\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:24\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:18:\"JAMUNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"3000.00\";s:12:\"max_capacity\";s:2:\"70\";s:13:\"price_per_day\";s:8:\"35000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:52:29\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:24\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780455512),
('tufan_convention_resort_cache_global_footer_sections', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780469073),
('tufan_convention_resort_cache_global_navbar_links', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780469073),
('tufan_convention_resort_cache_global_resort_info', 'O:21:\"App\\Models\\ResortInfo\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:11:\"resort_info\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:19:{s:2:\"id\";s:1:\"1\";s:11:\"resort_name\";s:25:\"Tufan Convention & Resort\";s:14:\"resort_tagline\";s:72:\"তুফান কনভেনশন এন্ড রিসোর্ট\";s:10:\"about_text\";s:160:\"Welcome to Tufan Resort, where luxury meets nature. Nestled in the heart of pristine landscapes, we offer world-class hospitality and unforgettable experiences.\";s:12:\"mission_text\";s:119:\"Our mission is to provide guests with exceptional service, comfort, and memorable experiences that exceed expectations.\";s:18:\"footer_description\";s:120:\"Premium accommodation and event hosting services. Experience luxury and tranquility by the lake at Kamalnagar, Satkhira.\";s:7:\"address\";s:26:\"Kamalnagar, Satkhira Sadar\";s:5:\"phone\";s:16:\"+88 01958-216728\";s:5:\"email\";s:30:\"info@tufanconventionresort.com\";s:13:\"map_embed_url\";s:244:\"https://www.google.com/maps/place/Lake+View+Resort/@22.7082089,89.0580919,17z/data=!4m6!3m5!1s0x39ff5fd2100f6d3b:0x23742a937768252b!8m2!3d22.7081601!4d89.0608847!16s%2Fg%2F11hdz9r3lh?entry=ttu&g_ep=EgoyMDI2MDEyMS4wIKXMDSoKLDEwMDc5MjA2OUgBUAM%3D\";s:10:\"facilities\";s:112:\"[\"Wellness\", \"Restaurant\", \"Gym\", \"Garden\", \"Parking\", \"Kids Indoor and Outdoor Playground\", \"Convention Halls\"]\";s:12:\"social_links\";s:64:\"{\"facebook\":\"https:\\/\\/web.facebook.com\\/TufanConventionCenter\"}\";s:14:\"copyright_text\";s:42:\"© 2026 Tufan Resort. All rights reserved.\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-01-27 14:12:01\";s:11:\"header_logo\";s:50:\"logos/vjHnA1qA8XlQoYDJjunEqTNqBV0BFMsGZHnHYLEr.jpg\";s:11:\"footer_logo\";s:50:\"logos/8CV9dpVcY71MaIVkK9iaz0PqkQT90HuPYMr8aeP6.jpg\";s:7:\"favicon\";s:50:\"logos/E2Zryu7zSaqzRHRXNHl4yUwx0uFh7gzPZHUoVnKt.jpg\";s:10:\"admin_logo\";s:50:\"logos/KpZ7f3Fj4grRn0WTemEypQaLpxtWDjbv4n0dcbw1.jpg\";}s:11:\"\0*\0original\";a:19:{s:2:\"id\";s:1:\"1\";s:11:\"resort_name\";s:25:\"Tufan Convention & Resort\";s:14:\"resort_tagline\";s:72:\"তুফান কনভেনশন এন্ড রিসোর্ট\";s:10:\"about_text\";s:160:\"Welcome to Tufan Resort, where luxury meets nature. Nestled in the heart of pristine landscapes, we offer world-class hospitality and unforgettable experiences.\";s:12:\"mission_text\";s:119:\"Our mission is to provide guests with exceptional service, comfort, and memorable experiences that exceed expectations.\";s:18:\"footer_description\";s:120:\"Premium accommodation and event hosting services. Experience luxury and tranquility by the lake at Kamalnagar, Satkhira.\";s:7:\"address\";s:26:\"Kamalnagar, Satkhira Sadar\";s:5:\"phone\";s:16:\"+88 01958-216728\";s:5:\"email\";s:30:\"info@tufanconventionresort.com\";s:13:\"map_embed_url\";s:244:\"https://www.google.com/maps/place/Lake+View+Resort/@22.7082089,89.0580919,17z/data=!4m6!3m5!1s0x39ff5fd2100f6d3b:0x23742a937768252b!8m2!3d22.7081601!4d89.0608847!16s%2Fg%2F11hdz9r3lh?entry=ttu&g_ep=EgoyMDI2MDEyMS4wIKXMDSoKLDEwMDc5MjA2OUgBUAM%3D\";s:10:\"facilities\";s:112:\"[\"Wellness\", \"Restaurant\", \"Gym\", \"Garden\", \"Parking\", \"Kids Indoor and Outdoor Playground\", \"Convention Halls\"]\";s:12:\"social_links\";s:64:\"{\"facebook\":\"https:\\/\\/web.facebook.com\\/TufanConventionCenter\"}\";s:14:\"copyright_text\";s:42:\"© 2026 Tufan Resort. All rights reserved.\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-01-27 14:12:01\";s:11:\"header_logo\";s:50:\"logos/vjHnA1qA8XlQoYDJjunEqTNqBV0BFMsGZHnHYLEr.jpg\";s:11:\"footer_logo\";s:50:\"logos/8CV9dpVcY71MaIVkK9iaz0PqkQT90HuPYMr8aeP6.jpg\";s:7:\"favicon\";s:50:\"logos/E2Zryu7zSaqzRHRXNHl4yUwx0uFh7gzPZHUoVnKt.jpg\";s:10:\"admin_logo\";s:50:\"logos/KpZ7f3Fj4grRn0WTemEypQaLpxtWDjbv4n0dcbw1.jpg\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"facilities\";s:5:\"array\";s:12:\"social_links\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:12:{i:0;s:11:\"resort_name\";i:1;s:14:\"resort_tagline\";i:2;s:10:\"about_text\";i:3;s:12:\"mission_text\";i:4;s:18:\"footer_description\";i:5;s:7:\"address\";i:6;s:5:\"phone\";i:7;s:5:\"email\";i:8;s:13:\"map_embed_url\";i:9;s:10:\"facilities\";i:10;s:12:\"social_links\";i:11;s:14:\"copyright_text\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1780469073),
('tufan_convention_resort_cache_homepage_halls', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:20:\"PADMAHALL ( NON AC )\";s:11:\"description\";s:248:\"No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.\";s:10:\"dimensions\";s:7:\"5000.00\";s:12:\"max_capacity\";s:3:\"150\";s:13:\"price_per_day\";s:8:\"20000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:30:\"[\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:65:\"[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]\";s:11:\"event_types\";s:91:\"[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]\";s:10:\"time_slots\";s:90:\"[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-04-05 09:02:46\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"1\";s:4:\"name\";s:20:\"PADMAHALL ( NON AC )\";s:11:\"description\";s:248:\"No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.\";s:10:\"dimensions\";s:7:\"5000.00\";s:12:\"max_capacity\";s:3:\"150\";s:13:\"price_per_day\";s:8:\"20000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:30:\"[\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:65:\"[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]\";s:11:\"event_types\";s:91:\"[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]\";s:10:\"time_slots\";s:90:\"[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-04-05 09:02:46\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:18:\"MEGHNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"1000.00\";s:12:\"max_capacity\";s:2:\"50\";s:13:\"price_per_day\";s:8:\"25000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:51:11\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:09\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:18:\"MEGHNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"1000.00\";s:12:\"max_capacity\";s:2:\"50\";s:13:\"price_per_day\";s:8:\"25000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:51:11\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:09\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:25:\"App\\Models\\ConventionHall\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"convention_halls\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:18:\"JAMUNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"3000.00\";s:12:\"max_capacity\";s:2:\"70\";s:13:\"price_per_day\";s:8:\"35000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:52:29\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:24\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:18:\"JAMUNA HALL ( AC )\";s:11:\"description\";N;s:10:\"dimensions\";s:7:\"3000.00\";s:12:\"max_capacity\";s:2:\"70\";s:13:\"price_per_day\";s:8:\"35000.00\";s:12:\"is_available\";s:1:\"1\";s:9:\"amenities\";s:35:\"[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]\";s:6:\"images\";s:2:\"[]\";s:11:\"event_types\";s:84:\"[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]\";s:10:\"time_slots\";N;s:10:\"created_at\";s:19:\"2026-04-05 07:52:29\";s:10:\"updated_at\";s:19:\"2026-04-05 09:03:24\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:5:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:11:\"event_types\";s:5:\"array\";s:10:\"time_slots\";s:5:\"array\";s:12:\"is_available\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:10:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"dimensions\";i:3;s:12:\"max_capacity\";i:4;s:13:\"price_per_day\";i:5;s:12:\"is_available\";i:6;s:9:\"amenities\";i:7;s:6:\"images\";i:8;s:11:\"event_types\";i:9;s:10:\"time_slots\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780469080),
('tufan_convention_resort_cache_homepage_hero_slides', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:20:\"App\\Models\\HeroSlide\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:11:\"hero_slides\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:11:{s:2:\"id\";s:1:\"1\";s:5:\"title\";s:23:\"Welcome to Tufan Resort\";s:11:\"description\";s:41:\"Discover Luxury & Tranquility by the Lake\";s:8:\"subtitle\";s:117:\"তুফান কনভেনশন এন্ড রিসোর্ট এ আপনাকে স্বাগতম\";s:11:\"button_text\";s:10:\"View Rooms\";s:11:\"button_link\";s:6:\"/rooms\";s:5:\"image\";s:15:\"hero/tufan1.jpg\";s:5:\"order\";s:1:\"1\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:11:\"\0*\0original\";a:11:{s:2:\"id\";s:1:\"1\";s:5:\"title\";s:23:\"Welcome to Tufan Resort\";s:11:\"description\";s:41:\"Discover Luxury & Tranquility by the Lake\";s:8:\"subtitle\";s:117:\"তুফান কনভেনশন এন্ড রিসোর্ট এ আপনাকে স্বাগতম\";s:11:\"button_text\";s:10:\"View Rooms\";s:11:\"button_link\";s:6:\"/rooms\";s:5:\"image\";s:15:\"hero/tufan1.jpg\";s:5:\"order\";s:1:\"1\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-19 00:39:58\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:5:\"title\";i:1;s:11:\"description\";i:2;s:8:\"subtitle\";i:3;s:11:\"button_text\";i:4;s:11:\"button_link\";i:5;s:5:\"image\";i:6;s:5:\"order\";i:7;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:20:\"App\\Models\\HeroSlide\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:11:\"hero_slides\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:11:{s:2:\"id\";s:1:\"2\";s:5:\"title\";s:31:\"Experience Luxury & Tranquility\";s:11:\"description\";N;s:8:\"subtitle\";s:51:\"Premium accommodation and event hosting by the lake\";s:11:\"button_text\";s:14:\"Explore Venues\";s:11:\"button_link\";s:16:\"/convention-hall\";s:5:\"image\";s:15:\"hero/tufan2.jpg\";s:5:\"order\";s:1:\"2\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:22:09\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:11:\"\0*\0original\";a:11:{s:2:\"id\";s:1:\"2\";s:5:\"title\";s:31:\"Experience Luxury & Tranquility\";s:11:\"description\";N;s:8:\"subtitle\";s:51:\"Premium accommodation and event hosting by the lake\";s:11:\"button_text\";s:14:\"Explore Venues\";s:11:\"button_link\";s:16:\"/convention-hall\";s:5:\"image\";s:15:\"hero/tufan2.jpg\";s:5:\"order\";s:1:\"2\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:22:09\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:5:\"title\";i:1;s:11:\"description\";i:2;s:8:\"subtitle\";i:3;s:11:\"button_text\";i:4;s:11:\"button_link\";i:5;s:5:\"image\";i:6;s:5:\"order\";i:7;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:20:\"App\\Models\\HeroSlide\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:11:\"hero_slides\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:11:{s:2:\"id\";s:1:\"3\";s:5:\"title\";s:27:\"Your Perfect Getaway Awaits\";s:11:\"description\";N;s:8:\"subtitle\";s:36:\"Weddings, Conferences & Celebrations\";s:11:\"button_text\";s:10:\"Contact Us\";s:11:\"button_link\";s:6:\"/about\";s:5:\"image\";s:15:\"hero/tufan3.jpg\";s:5:\"order\";s:1:\"3\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:22:09\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:11:\"\0*\0original\";a:11:{s:2:\"id\";s:1:\"3\";s:5:\"title\";s:27:\"Your Perfect Getaway Awaits\";s:11:\"description\";N;s:8:\"subtitle\";s:36:\"Weddings, Conferences & Celebrations\";s:11:\"button_text\";s:10:\"Contact Us\";s:11:\"button_link\";s:6:\"/about\";s:5:\"image\";s:15:\"hero/tufan3.jpg\";s:5:\"order\";s:1:\"3\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:22:09\";s:10:\"updated_at\";s:19:\"2026-01-25 19:22:09\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:5:\"title\";i:1;s:11:\"description\";i:2;s:8:\"subtitle\";i:3;s:11:\"button_text\";i:4;s:11:\"button_link\";i:5;s:5:\"image\";i:6;s:5:\"order\";i:7;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780469080);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('tufan_convention_resort_cache_homepage_rooms', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:6:{i:0;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"4\";s:11:\"room_number\";s:3:\"201\";s:12:\"room_type_id\";s:1:\"6\";s:4:\"name\";s:25:\"Premium Suit Room ( Vip )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:63:\"VIP Couple Bed Deluxe Room with AC. King Bed with 50 sqm space.\";s:15:\"price_per_night\";s:7:\"4000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:217:\"[\"rooms\\/429cAwz18CHRTcY479REPeM2mx6IhJvujdmw3G0d.jpg\",\"rooms\\/KzEqOIaHjWsUQiQ7x1fZWHj9ckNbQXevpnWwbXWp.jpg\",\"rooms\\/TuBRbpSz0tmpK0xkOpMsbpzwQrXwMYODmgE2ThhL.jpg\",\"rooms\\/U9GeZtYhw8uph3i9bEEfKY1MwA8pE5i5aVQK4qzo.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 06:59:20\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"4\";s:11:\"room_number\";s:3:\"201\";s:12:\"room_type_id\";s:1:\"6\";s:4:\"name\";s:25:\"Premium Suit Room ( Vip )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:63:\"VIP Couple Bed Deluxe Room with AC. King Bed with 50 sqm space.\";s:15:\"price_per_night\";s:7:\"4000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:217:\"[\"rooms\\/429cAwz18CHRTcY479REPeM2mx6IhJvujdmw3G0d.jpg\",\"rooms\\/KzEqOIaHjWsUQiQ7x1fZWHj9ckNbQXevpnWwbXWp.jpg\",\"rooms\\/TuBRbpSz0tmpK0xkOpMsbpzwQrXwMYODmgE2ThhL.jpg\",\"rooms\\/U9GeZtYhw8uph3i9bEEfKY1MwA8pE5i5aVQK4qzo.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 06:59:20\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";O:19:\"App\\Models\\RoomType\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"room_types\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:28:\"Premium Suit Room ( Ac Vip )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"4000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-02-08 06:53:26\";s:10:\"updated_at\";s:19:\"2026-03-04 06:58:46\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";s:1:\"6\";s:4:\"name\";s:28:\"Premium Suit Room ( Ac Vip )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"4000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-02-08 06:53:26\";s:10:\"updated_at\";s:19:\"2026-03-04 06:58:46\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"base_price\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"5\";s:11:\"room_number\";s:3:\"202\";s:12:\"room_type_id\";s:1:\"5\";s:4:\"name\";s:33:\"Premium Double Bed Room ( Suite )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:63:\"VIP Double Bed Deluxe Room with AC. King Bed with 60 sqm space.\";s:15:\"price_per_night\";s:7:\"4000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"3000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"4\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:55:\"[\"rooms\\/3FEDNEUoUjwiTGkkhLIpg1eoQZSH1wviG2XhREZy.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 06:59:46\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"5\";s:11:\"room_number\";s:3:\"202\";s:12:\"room_type_id\";s:1:\"5\";s:4:\"name\";s:33:\"Premium Double Bed Room ( Suite )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:63:\"VIP Double Bed Deluxe Room with AC. King Bed with 60 sqm space.\";s:15:\"price_per_night\";s:7:\"4000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"3000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"4\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:55:\"[\"rooms\\/3FEDNEUoUjwiTGkkhLIpg1eoQZSH1wviG2XhREZy.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 06:59:46\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";O:19:\"App\\Models\\RoomType\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"room_types\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:36:\"Premium Double Bed Room ( Ac Suite )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"4000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:19:14\";s:10:\"updated_at\";s:19:\"2026-03-04 06:58:26\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";s:1:\"5\";s:4:\"name\";s:36:\"Premium Double Bed Room ( Ac Suite )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"4000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-25 19:19:14\";s:10:\"updated_at\";s:19:\"2026-03-04 06:58:26\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"base_price\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"6\";s:11:\"room_number\";s:3:\"203\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:35:\"VIP Couple Bed Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:109:\"[\"rooms\\/jIHvMPQQZkKB32YLwIYMuosZhQyK79WGbIxeuB82.jpg\",\"rooms\\/WivysSRMGh8tvdhBWKv7ktXIcfKbotrXkB1PHiST.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:00:19\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"6\";s:11:\"room_number\";s:3:\"203\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:35:\"VIP Couple Bed Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:109:\"[\"rooms\\/jIHvMPQQZkKB32YLwIYMuosZhQyK79WGbIxeuB82.jpg\",\"rooms\\/WivysSRMGh8tvdhBWKv7ktXIcfKbotrXkB1PHiST.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:00:19\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";O:19:\"App\\Models\\RoomType\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"room_types\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:35:\"Premium Twin Bed Room ( Ac Deluxe )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"3000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-19 06:07:38\";s:10:\"updated_at\";s:19:\"2026-03-04 06:57:01\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";s:1:\"3\";s:4:\"name\";s:35:\"Premium Twin Bed Room ( Ac Deluxe )\";s:11:\"description\";N;s:10:\"base_price\";s:7:\"3000.00\";s:9:\"is_active\";s:1:\"1\";s:10:\"created_at\";s:19:\"2026-01-19 06:07:38\";s:10:\"updated_at\";s:19:\"2026-03-04 06:57:01\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:11:\"description\";i:2;s:10:\"base_price\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"7\";s:11:\"room_number\";s:3:\"204\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:35:\"VIP Couple Bed Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:109:\"[\"rooms\\/IjoP5Xh8YRnlxCnwS69vljYFyVsJiHiHesB2rqou.jpg\",\"rooms\\/zNgn6VtPJ7nBRTtDiu4etM3RfJAAYQmO0ucAfnaz.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:00:44\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"7\";s:11:\"room_number\";s:3:\"204\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:35:\"VIP Couple Bed Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"2000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:109:\"[\"rooms\\/IjoP5Xh8YRnlxCnwS69vljYFyVsJiHiHesB2rqou.jpg\",\"rooms\\/zNgn6VtPJ7nBRTtDiu4etM3RfJAAYQmO0ucAfnaz.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:00:44\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";r:341;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"8\";s:11:\"room_number\";s:3:\"205\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:43:\"VIP Couple Bed Premium Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"3000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:2:\"[]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:01:10\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"8\";s:11:\"room_number\";s:3:\"205\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:6:\"deluxe\";s:11:\"description\";s:43:\"VIP Couple Bed Premium Deluxe Room with AC.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"1\";s:8:\"ac_price\";s:7:\"3000.00\";s:12:\"non_ac_price\";s:4:\"0.00\";s:10:\"max_guests\";s:1:\"3\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:2:\"[]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:01:10\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";r:341;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:15:\"App\\Models\\Room\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"rooms\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:17:{s:2:\"id\";s:1:\"9\";s:11:\"room_number\";s:3:\"301\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:8:\"standard\";s:11:\"description\";s:51:\"Single Bed Standard Room. Non-AC with 30 sqm space.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"0\";s:8:\"ac_price\";s:4:\"0.00\";s:12:\"non_ac_price\";s:7:\"1000.00\";s:10:\"max_guests\";s:1:\"1\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:55:\"[\"rooms\\/2PYncAB7qKEKUXB22hkgSSSNb44arOFzcpwIdLRk.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:01:48\";}s:11:\"\0*\0original\";a:17:{s:2:\"id\";s:1:\"9\";s:11:\"room_number\";s:3:\"301\";s:12:\"room_type_id\";s:1:\"3\";s:4:\"name\";s:32:\"Premium Twin Bed Room ( Deluxe )\";s:4:\"type\";s:8:\"standard\";s:11:\"description\";s:51:\"Single Bed Standard Room. Non-AC with 30 sqm space.\";s:15:\"price_per_night\";s:7:\"3000.00\";s:6:\"has_ac\";s:1:\"0\";s:8:\"ac_price\";s:4:\"0.00\";s:12:\"non_ac_price\";s:7:\"1000.00\";s:10:\"max_guests\";s:1:\"1\";s:14:\"number_of_beds\";s:1:\"1\";s:9:\"amenities\";s:43:\"[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]\";s:6:\"images\";s:55:\"[\"rooms\\/2PYncAB7qKEKUXB22hkgSSSNb44arOFzcpwIdLRk.jpg\"]\";s:6:\"status\";s:9:\"available\";s:10:\"created_at\";s:19:\"2026-01-25 19:20:08\";s:10:\"updated_at\";s:19:\"2026-02-08 07:01:48\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:9:\"amenities\";s:5:\"array\";s:6:\"images\";s:5:\"array\";s:6:\"has_ac\";s:7:\"boolean\";s:15:\"price_per_night\";s:9:\"decimal:2\";s:8:\"ac_price\";s:9:\"decimal:2\";s:12:\"non_ac_price\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"roomType\";r:341;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:14:{i:0;s:11:\"room_number\";i:1;s:4:\"name\";i:2;s:4:\"type\";i:3;s:12:\"room_type_id\";i:4;s:11:\"description\";i:5;s:15:\"price_per_night\";i:6;s:6:\"has_ac\";i:7;s:8:\"ac_price\";i:8;s:12:\"non_ac_price\";i:9;s:10:\"max_guests\";i:10;s:14:\"number_of_beds\";i:11;s:9:\"amenities\";i:12;s:6:\"images\";i:13;s:6:\"status\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1780469080);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `convention_bookings`
--

CREATE TABLE `convention_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_nid` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_whatsapp` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `time_slot` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) NOT NULL,
  `organization_name` varchar(255) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `number_of_guests` int(11) NOT NULL,
  `food_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `food_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selected_addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_addons`)),
  `addon_quantities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addon_quantities`)),
  `addons_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hall_rent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_type` enum('flat','percentage') NOT NULL DEFAULT 'flat',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vat_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vat_percentage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','mfs') NOT NULL,
  `payment_status` enum('pending','partial','paid','refunded') NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `program_status` enum('pending','confirmed','running','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `convention_bookings`
--

INSERT INTO `convention_bookings` (`id`, `hall_id`, `customer_name`, `customer_nid`, `customer_phone`, `customer_whatsapp`, `customer_email`, `customer_address`, `event_date`, `start_time`, `end_time`, `time_slot`, `event_type`, `organization_name`, `event_description`, `number_of_guests`, `food_package_id`, `food_cost`, `selected_addons`, `addon_quantities`, `addons_cost`, `hall_rent`, `discount`, `discount_type`, `discount_value`, `vat_amount`, `vat_percentage`, `total_amount`, `advance_payment`, `remaining_payment`, `payment_method`, `payment_status`, `status`, `program_status`, `notes`, `created_by_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mir Javed Jahanger', NULL, '01811480222', NULL, 'javedmirjeetu.official@gmail.com', 'Satkhira', '2026-02-25', NULL, NULL, 'morning', 'conference', 'SALAM HOUSE', NULL, 500, NULL, 0.00, '[\"225\",\"118\",\"216\",\"119\"]', '{\"225\":\"1\",\"118\":\"1\",\"216\":\"1\",\"119\":\"1\",\"148\":\"1\",\"149\":\"1\",\"150\":\"1\",\"157\":\"1\",\"153\":\"1\",\"144\":\"1\",\"143\":\"1\",\"109\":\"1\",\"110\":\"1\",\"107\":\"1\",\"108\":\"1\",\"140\":\"1\",\"138\":\"1\",\"141\":\"1\",\"139\":\"1\",\"137\":\"1\",\"158\":\"1\",\"155\":\"1\",\"152\":\"1\",\"154\":\"1\",\"215\":\"1\",\"145\":\"1\",\"156\":\"1\",\"211\":\"1\",\"212\":\"1\",\"217\":\"1\",\"219\":\"1\",\"146\":\"1\",\"147\":\"1\",\"214\":\"1\",\"142\":\"1\",\"220\":\"1\",\"221\":\"1\",\"224\":\"1\",\"226\":\"1\",\"223\":\"1\",\"222\":\"1\",\"301\":\"1\",\"302\":\"1\",\"303\":\"1\",\"213\":\"1\",\"114\":\"1\",\"113\":\"1\",\"124\":\"1\",\"120\":\"1\",\"127\":\"1\",\"126\":\"1\",\"130\":\"1\",\"129\":\"1\",\"121\":\"1\",\"122\":\"1\",\"131\":\"1\",\"125\":\"1\",\"134\":\"1\",\"136\":\"1\",\"117\":\"1\",\"132\":\"1\",\"133\":\"1\",\"123\":\"1\",\"128\":\"1\",\"115\":\"1\",\"135\":\"1\",\"111\":\"1\",\"112\":\"1\",\"116\":\"1\",\"295\":\"1\",\"296\":\"1\",\"151\":\"1\",\"297\":\"1\",\"300\":\"1\",\"218\":\"1\",\"299\":\"1\",\"298\":\"1\"}', 1090.00, 10000.00, 0.00, 'flat', 0.00, 1663.50, 15.00, 12753.50, 12753.00, 0.50, 'cash', 'partial', 'confirmed', 'pending', NULL, 5, '2026-02-25 10:05:10', '2026-02-25 13:07:15'),
(2, 1, 'jahangir', NULL, '01958216727', NULL, NULL, 'satkhira', '2026-04-05', NULL, NULL, 'morning', 'meeting', 'tufan company', NULL, 50, NULL, 0.00, '[\"107\",\"138\"]', '{\"225\":\"1\",\"118\":\"1\",\"216\":\"1\",\"119\":\"1\",\"148\":\"1\",\"149\":\"1\",\"150\":\"1\",\"157\":\"1\",\"153\":\"1\",\"144\":\"1\",\"143\":\"1\",\"109\":\"1\",\"110\":\"1\",\"107\":\"60\",\"108\":\"1\",\"140\":\"1\",\"138\":\"4\",\"141\":\"1\",\"139\":\"1\",\"137\":\"1\",\"158\":\"1\",\"155\":\"1\",\"152\":\"1\",\"154\":\"1\",\"215\":\"1\",\"145\":\"1\",\"156\":\"1\",\"211\":\"1\",\"212\":\"1\",\"217\":\"1\",\"219\":\"1\",\"146\":\"1\",\"147\":\"1\",\"214\":\"1\",\"142\":\"1\",\"220\":\"1\",\"221\":\"1\",\"224\":\"1\",\"226\":\"1\",\"223\":\"1\",\"222\":\"1\",\"301\":\"1\",\"302\":\"1\",\"303\":\"1\",\"213\":\"1\",\"114\":\"1\",\"113\":\"1\",\"124\":\"1\",\"120\":\"1\",\"127\":\"1\",\"126\":\"1\",\"130\":\"1\",\"129\":\"1\",\"121\":\"1\",\"122\":\"1\",\"131\":\"1\",\"125\":\"1\",\"134\":\"1\",\"136\":\"1\",\"117\":\"1\",\"132\":\"1\",\"133\":\"1\",\"123\":\"1\",\"128\":\"1\",\"115\":\"1\",\"135\":\"1\",\"111\":\"1\",\"112\":\"1\",\"116\":\"1\",\"295\":\"1\",\"296\":\"1\",\"151\":\"1\",\"297\":\"1\",\"300\":\"1\",\"218\":\"1\",\"299\":\"1\",\"298\":\"1\"}', 700.00, 10000.00, 0.00, 'flat', 0.00, 0.00, 15.00, 10700.00, 10700.00, 0.00, 'cash', 'paid', 'completed', 'pending', NULL, 8, '2026-04-05 11:45:48', '2026-04-05 11:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `convention_halls`
--

CREATE TABLE `convention_halls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `dimensions` decimal(10,2) DEFAULT NULL COMMENT 'in sq ft',
  `max_capacity` int(11) DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `event_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_types`)),
  `time_slots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`time_slots`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `convention_halls`
--

INSERT INTO `convention_halls` (`id`, `name`, `description`, `dimensions`, `max_capacity`, `price_per_day`, `is_available`, `amenities`, `images`, `event_types`, `time_slots`, `created_at`, `updated_at`) VALUES
(1, 'PADMAHALL ( NON AC )', 'No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.', 5000.00, 150, 20000.00, 1, '[\"Parking\",\"WiFi\",\"Generator\"]', '[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]', '[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]', '[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]', '2026-01-19 05:39:58', '2026-04-05 13:02:46'),
(2, 'MEGHNA HALL ( AC )', NULL, 1000.00, 50, 25000.00, 1, '[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]', '[]', '[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]', NULL, '2026-04-05 11:51:11', '2026-04-05 13:03:09'),
(3, 'JAMUNA HALL ( AC )', NULL, 3000.00, 70, 35000.00, 1, '[\"AC\",\"Parking\",\"WiFi\",\"Generator\"]', '[]', '[\"Wedding\",\"Conference\",\"Birthday\",\"Meeting\",\"Seminar\",\"Party\",\"Exhibition\",\"Other\"]', NULL, '2026-04-05 11:52:29', '2026-04-05 13:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `convention_payments`
--

CREATE TABLE `convention_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `convention_booking_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mfs') NOT NULL,
  `payment_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `received_by_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `convention_payments`
--

INSERT INTO `convention_payments` (`id`, `convention_booking_id`, `amount`, `payment_method`, `payment_date`, `notes`, `received_by_id`, `created_at`, `updated_at`) VALUES
(1, 1, 12000.00, 'cash', '2026-02-25', NULL, 5, '2026-02-25 10:25:44', '2026-02-25 10:25:44'),
(2, 1, 753.00, 'cash', '2026-02-25', NULL, 5, '2026-02-25 13:07:15', '2026-02-25 13:07:15'),
(3, 2, 10700.00, 'cash', '2026-04-05', NULL, 8, '2026-04-05 11:55:44', '2026-04-05 11:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `extra_charge_categories`
--

CREATE TABLE `extra_charge_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `extra_charge_categories`
--

INSERT INTO `extra_charge_categories` (`id`, `name`, `price`, `unit`, `description`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(5, 'Tea', 20.00, '1', NULL, 1, 15, '2026-02-09 13:59:47', '2026-02-09 13:59:47'),
(4, 'Water 1 Litter', 35.00, '1', NULL, 1, 20, '2026-02-09 13:59:25', '2026-02-09 13:59:25'),
(3, 'Breakfast', 70.00, '1', NULL, 1, 50, '2026-02-09 13:57:52', '2026-02-09 13:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_packages`
--

CREATE TABLE `food_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_person` decimal(10,2) NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_packages`
--

INSERT INTO `food_packages` (`id`, `name`, `description`, `price_per_person`, `items`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Basic Package', 'Simple and delicious meals', 500.00, '[\"Rice\", \"Dal\", \"Chicken Curry\", \"Vegetables\", \"Salad\", \"Dessert\"]', 1, '2026-01-19 05:39:58', '2026-01-19 05:39:58');

-- --------------------------------------------------------

--
-- Table structure for table `footer_links`
--

CREATE TABLE `footer_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `footer_section_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `footer_sections`
--

CREATE TABLE `footer_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `description`, `subtitle`, `button_text`, `button_link`, `image`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Welcome to Tufan Resort', 'Discover Luxury & Tranquility by the Lake', 'তুফান কনভেনশন এন্ড রিসোর্ট এ আপনাকে স্বাগতম', 'View Rooms', '/rooms', 'hero/tufan1.jpg', 1, 1, '2026-01-19 05:39:58', '2026-01-26 00:22:09'),
(2, 'Experience Luxury & Tranquility', NULL, 'Premium accommodation and event hosting by the lake', 'Explore Venues', '/convention-hall', 'hero/tufan2.jpg', 2, 1, '2026-01-26 00:22:09', '2026-01-26 00:22:09'),
(3, 'Your Perfect Getaway Awaits', NULL, 'Weddings, Conferences & Celebrations', 'Contact Us', '/about', 'hero/tufan3.jpg', 3, 1, '2026-01-26 00:22:09', '2026-01-26 00:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_19_112225_1_create_rooms_table', 1),
(5, '2026_01_19_112225_2_create_convention_halls_table', 1),
(6, '2026_01_19_112225_3_create_bookings_table', 1),
(7, '2026_01_19_112225_4_create_convention_bookings_table', 1),
(8, '2026_01_19_112225_5_create_convention_payments_table', 1),
(9, '2026_01_19_112225_create_addon_services_table', 1),
(10, '2026_01_19_112225_create_food_packages_table', 1),
(11, '2026_01_19_112225_create_hero_slides_table', 1),
(12, '2026_01_19_112226_create_activity_logs_table', 1),
(13, '2026_01_19_112226_create_footer_sections_table', 1),
(14, '2026_01_19_112226_create_menu_items_table', 1),
(15, '2026_01_19_112226_create_navbar_links_table', 1),
(16, '2026_01_19_112226_create_resort_info_table', 1),
(17, '2026_01_19_112226_create_room_types_table', 1),
(18, '2026_01_19_112226_create_system_settings_table', 1),
(19, '2026_01_19_112227_create_footer_links_table', 1),
(21, '2026_01_19_170502_add_room_type_id_to_rooms_table', 2),
(22, '2026_01_20_000003_create_additional_guests_table', 3),
(23, '2026_01_20_000004_create_booking_payments_table', 3),
(24, '2026_01_20_084048_add_missing_fields_to_resort_info_table', 4),
(25, '2026_01_26_000001_create_admin_menu_settings_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `navbar_links`
--

CREATE TABLE `navbar_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resort_info`
--

CREATE TABLE `resort_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `resort_name` varchar(255) DEFAULT NULL,
  `resort_tagline` varchar(255) DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `mission_text` text DEFAULT NULL,
  `footer_description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `map_embed_url` text DEFAULT NULL,
  `facilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`facilities`)),
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `copyright_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `header_logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `admin_logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resort_info`
--

INSERT INTO `resort_info` (`id`, `resort_name`, `resort_tagline`, `about_text`, `mission_text`, `footer_description`, `address`, `phone`, `email`, `map_embed_url`, `facilities`, `social_links`, `copyright_text`, `created_at`, `updated_at`, `header_logo`, `footer_logo`, `favicon`, `admin_logo`) VALUES
(1, 'Tufan Convention & Resort', 'তুফান কনভেনশন এন্ড রিসোর্ট', 'Welcome to Tufan Resort, where luxury meets nature. Nestled in the heart of pristine landscapes, we offer world-class hospitality and unforgettable experiences.', 'Our mission is to provide guests with exceptional service, comfort, and memorable experiences that exceed expectations.', 'Premium accommodation and event hosting services. Experience luxury and tranquility by the lake at Kamalnagar, Satkhira.', 'Kamalnagar, Satkhira Sadar', '+88 01958-216728', 'info@tufanconventionresort.com', 'https://www.google.com/maps/place/Lake+View+Resort/@22.7082089,89.0580919,17z/data=!4m6!3m5!1s0x39ff5fd2100f6d3b:0x23742a937768252b!8m2!3d22.7081601!4d89.0608847!16s%2Fg%2F11hdz9r3lh?entry=ttu&g_ep=EgoyMDI2MDEyMS4wIKXMDSoKLDEwMDc5MjA2OUgBUAM%3D', '[\"Wellness\", \"Restaurant\", \"Gym\", \"Garden\", \"Parking\", \"Kids Indoor and Outdoor Playground\", \"Convention Halls\"]', '{\"facebook\":\"https:\\/\\/web.facebook.com\\/TufanConventionCenter\"}', '© 2026 Tufan Resort. All rights reserved.', '2026-01-19 05:39:58', '2026-01-27 19:12:01', 'logos/vjHnA1qA8XlQoYDJjunEqTNqBV0BFMsGZHnHYLEr.jpg', 'logos/8CV9dpVcY71MaIVkK9iaz0PqkQT90HuPYMr8aeP6.jpg', 'logos/E2Zryu7zSaqzRHRXNHl4yUwx0uFh7gzPZHUoVnKt.jpg', 'logos/KpZ7f3Fj4grRn0WTemEypQaLpxtWDjbv4n0dcbw1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_number` varchar(255) NOT NULL,
  `room_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('standard','deluxe','suite','family') NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `has_ac` tinyint(1) NOT NULL DEFAULT 1,
  `ac_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `non_ac_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_guests` int(11) DEFAULT NULL,
  `number_of_beds` int(11) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `status` enum('available','booked','maintenance') NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `room_type_id`, `name`, `type`, `description`, `price_per_night`, `has_ac`, `ac_price`, `non_ac_price`, `max_guests`, `number_of_beds`, `amenities`, `images`, `status`, `created_at`, `updated_at`) VALUES
(4, '201', 6, 'Premium Suit Room ( Vip )', 'deluxe', 'VIP Couple Bed Deluxe Room with AC. King Bed with 50 sqm space.', 4000.00, 1, 2000.00, 0.00, 3, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/429cAwz18CHRTcY479REPeM2mx6IhJvujdmw3G0d.jpg\",\"rooms\\/KzEqOIaHjWsUQiQ7x1fZWHj9ckNbQXevpnWwbXWp.jpg\",\"rooms\\/TuBRbpSz0tmpK0xkOpMsbpzwQrXwMYODmgE2ThhL.jpg\",\"rooms\\/U9GeZtYhw8uph3i9bEEfKY1MwA8pE5i5aVQK4qzo.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 11:59:20'),
(5, '202', 5, 'Premium Double Bed Room ( Suite )', 'deluxe', 'VIP Double Bed Deluxe Room with AC. King Bed with 60 sqm space.', 4000.00, 1, 3000.00, 0.00, 4, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/3FEDNEUoUjwiTGkkhLIpg1eoQZSH1wviG2XhREZy.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 11:59:46'),
(6, '203', 3, 'Premium Twin Bed Room ( Deluxe )', 'deluxe', 'VIP Couple Bed Deluxe Room with AC.', 3000.00, 1, 2000.00, 0.00, 3, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/jIHvMPQQZkKB32YLwIYMuosZhQyK79WGbIxeuB82.jpg\",\"rooms\\/WivysSRMGh8tvdhBWKv7ktXIcfKbotrXkB1PHiST.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 12:00:19'),
(7, '204', 3, 'Premium Twin Bed Room ( Deluxe )', 'deluxe', 'VIP Couple Bed Deluxe Room with AC.', 3000.00, 1, 2000.00, 0.00, 3, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/IjoP5Xh8YRnlxCnwS69vljYFyVsJiHiHesB2rqou.jpg\",\"rooms\\/zNgn6VtPJ7nBRTtDiu4etM3RfJAAYQmO0ucAfnaz.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 12:00:44'),
(8, '205', 3, 'Premium Twin Bed Room ( Deluxe )', 'deluxe', 'VIP Couple Bed Premium Deluxe Room with AC.', 3000.00, 1, 3000.00, 0.00, 3, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[]', 'available', '2026-01-26 00:20:08', '2026-02-08 12:01:10'),
(9, '301', 3, 'Premium Twin Bed Room ( Deluxe )', 'standard', 'Single Bed Standard Room. Non-AC with 30 sqm space.', 3000.00, 0, 0.00, 1000.00, 1, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/2PYncAB7qKEKUXB22hkgSSSNb44arOFzcpwIdLRk.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 12:01:48'),
(10, '302', 4, 'Premium Double Bed Room ( Deluxe )', 'standard', 'Single Bed Standard Room. Non-AC with 30 sqm space.', 3000.00, 0, 0.00, 1000.00, 1, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/MArEXmRHDmgzFURPDYeN2MkkCustLiAgSiToMjqL.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 12:02:27'),
(11, '303', 2, 'Single Ac Room ( Standard )', 'suite', 'VIP Couple Bed Superior Room with AC. Double King Bed with 50 sqm space.', 2000.00, 1, 3000.00, 0.00, 2, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/b0Yn2p2S43ym0uwDkQ3Ep8N1wAokUJ8tU9pmgZkD.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 11:56:00'),
(12, '304', 2, 'Single Ac Room ( Standard )', 'suite', 'VIP Couple Bed Superior Room with AC.', 2000.00, 1, 3000.00, 0.00, 2, 1, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[\"rooms\\/a0vTVXF8RaCKV8eBRyX3LbAn1zO6fRgL0KP2L5Hp.jpg\"]', 'available', '2026-01-26 00:20:08', '2026-02-08 11:56:28'),
(13, '305', 5, 'Single Non Ac Room', 'suite', 'VIP Double Bed Superior Room with AC. 2 King Beds with 50 sqm space.', 1000.00, 1, 3500.00, 0.00, 4, 2, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[]', 'available', '2026-01-26 00:20:08', '2026-02-03 13:35:13'),
(14, '306', 5, 'Single Non Ac Room', 'suite', 'VIP Couple Bed Premium Superior Room with AC.', 1000.00, 1, 4000.00, 0.00, 4, 2, '[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]', '[]', 'available', '2026-01-26 00:20:08', '2026-02-03 13:35:55'),
(15, '307', 7, 'Single Non AC Three Bed', 'standard', NULL, 400.00, 1, 0.00, 0.00, 1, 1, NULL, '[]', 'available', '2026-02-03 13:37:25', '2026-02-08 12:07:25'),
(16, '401', 6, 'Premium Suit Room ( Vip )', 'standard', NULL, 4000.00, 1, 0.00, 0.00, 2, 1, NULL, '[\"rooms\\/C15MvidgL8rnTrq663qxgQ4xxKTshYe0JJYSrJwl.jpg\",\"rooms\\/Hki2uTevqVdaDv6W4bihqPXlhgbqTezm8kDROC5D.jpg\"]', 'available', '2026-02-03 13:41:20', '2026-02-08 11:58:33'),
(17, '402', 5, 'Premium Double Bed Room ( Suite )', 'standard', NULL, 4000.00, 1, 0.00, 0.00, 2, 2, NULL, '[\"rooms\\/vsT7iIq2Jkxtm9sXLClWduRTdxBiZ36kyD8mVQNu.jpg\",\"rooms\\/ZsLW9VsK6hAY5jG4C9f9AOnsDoI33H0EzY4UPMZo.jpg\",\"rooms\\/jtoBqJwPV9m0uiznPjby30soboQooUjsDrWS0sns.jpg\"]', 'available', '2026-02-03 13:42:37', '2026-02-08 11:58:02'),
(18, '403', 3, 'Premium Twin Bed Room ( Deluxe )', 'standard', NULL, 3000.00, 1, 0.00, 0.00, 2, 1, NULL, '[\"rooms\\/LCD09fMpQH2E2IA5wEzNA5j5HRQEi4IlwUcp7kZn.jpg\"]', 'available', '2026-02-03 13:43:40', '2026-02-08 11:57:24'),
(19, '404', 2, 'Single Ac Room ( Standard )', 'standard', NULL, 2000.00, 1, 0.00, 0.00, 2, 1, NULL, '[\"rooms\\/DGqdObUIu2bi6xEKT7DeKeHjPMIcztxFNdXuVeNY.jpg\"]', 'available', '2026-02-03 13:45:08', '2026-02-08 11:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `base_price`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Non Ac Single Room', NULL, 1000.00, 1, '2026-01-19 11:07:38', '2026-02-08 11:41:04'),
(2, 'Single Ac Room ( Standard )', NULL, 2000.00, 1, '2026-01-19 11:07:38', '2026-02-08 11:41:38'),
(3, 'Premium Twin Bed Room ( Ac Deluxe )', NULL, 3000.00, 1, '2026-01-19 11:07:38', '2026-03-04 11:57:01'),
(4, 'Premium Double Bed Room ( Ac Deluxe )', NULL, 3000.00, 1, '2026-01-19 11:07:39', '2026-03-04 11:57:48'),
(5, 'Premium Double Bed Room ( Ac Suite )', NULL, 4000.00, 1, '2026-01-26 00:19:14', '2026-03-04 11:58:26'),
(6, 'Premium Suit Room ( Ac Vip )', NULL, 4000.00, 1, '2026-02-08 11:53:26', '2026-03-04 11:58:46'),
(7, 'Non Ac Three Bed Room', NULL, 400.00, 1, '2026-02-08 12:04:06', '2026-02-08 12:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ApFj1vv84ggxxF4hVrGTccV24hbGMrAHu0qhxTx8', 2, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHB1MmhkTEtTT0x0UTZGall2T3ZhQXQ4T2JpUXlHdGhueUN4SHZBbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1769413686);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'staff',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `dashboard_mode` varchar(20) DEFAULT 'both',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `permissions`, `is_active`, `remember_token`, `dashboard_mode`, `created_at`, `updated_at`) VALUES
(2, 'Resort Owner', 'owner@tufanresort.com', NULL, '$2y$12$FRBQPhS3ik.9aSoWEeyCbu6KuI5TSPXCYN8gvczFWpL5yjc4ejGJ6', 'owner', '[\"*\"]', 1, 'gouydb0pAPoIo0HlpzF0OA6gECf3XTMEbuscn6MaP9DwaWRbw2di6IR6WuOT', 'convention', '2026-01-19 05:39:58', '2026-03-02 15:05:44'),
(4, 'Ibrahim-resort', 'ibrahim@lakeview-cafe.com', NULL, '$2y$12$laqeJfYqAPj6uZSLY3sKqeptgoe4ibjg8CiW5VMXbGQR0ocrraryS', 'staff', '[\"todays_summary\",\"rooms\",\"room_types\",\"search_book_room\",\"all_bookings\",\"addon_services\",\"food_packages\",\"room_reports\"]', 1, NULL, 'both', '2026-01-28 20:02:48', '2026-01-28 20:02:48'),
(5, 'Mir Javed Jahanger', 'javedmirjeetu.official@gmail.com', NULL, '$2y$12$ewfi0hDngaQW5AWWgHE/c.SfwhwZ./dUAeJo.8bvmXqNQhVpvRgsG', 'superadmin', '[]', 1, NULL, 'resort', '2026-01-29 18:39:16', '2026-03-25 15:00:25'),
(6, 'Tuhin', 'tuhin@lakeview-cafe.com', NULL, '$2y$12$DiYxngdQcEwihi5FjuccE.S0MeI88lXap.fF6tHxWVirOU/7D2GIe', 'admin', '[]', 1, NULL, 'both', '2026-01-29 18:52:35', '2026-01-29 18:52:35'),
(7, 'Tanjim Kalam Tamal', 'tamal@lakeview-cafe.com', NULL, '$2y$12$Z4iFm9Hu7c.DFoqARpp0ou4qaxruUbchM6cQ/xCMsnLBlorHJFFQO', 'superadmin', '[]', 1, NULL, 'both', '2026-01-29 19:07:55', '2026-01-29 19:07:55'),
(8, 'Tanvir', 'tanvir@lakeview-cafe.com', NULL, '$2y$12$0Gs.v3e4FhSstoEMMpqdzeykGF5YqGb3Bv0C2j8LqHdoeo3HUWTSm', 'staff', '[\"todays_summary\",\"rooms\",\"room_types\",\"search_book_room\",\"all_bookings\",\"customers\",\"search_book_hall\",\"all_hall_bookings\",\"manage_halls\",\"addon_services\",\"food_packages\",\"extra_charge_categories\",\"room_reports\",\"convention_reports\"]', 1, NULL, 'resort', '2026-02-01 21:56:39', '2026-03-04 12:05:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `additional_guests`
--
ALTER TABLE `additional_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `additional_guests_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `addon_services`
--
ALTER TABLE `addon_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_addon_services_is_active` (`is_active`),
  ADD KEY `idx_addon_services_service_type` (`service_type`);

--
-- Indexes for table `admin_menu_settings`
--
ALTER TABLE `admin_menu_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_menu_settings_menu_key_unique` (`menu_key`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_room_id_foreign` (`room_id`),
  ADD KEY `bookings_created_by_id_foreign` (`created_by_id`),
  ADD KEY `idx_bookings_check_in_date` (`check_in_date`),
  ADD KEY `idx_bookings_check_out_date` (`check_out_date`),
  ADD KEY `idx_bookings_status` (`status`),
  ADD KEY `idx_bookings_room_id` (`room_id`),
  ADD KEY `idx_bookings_created_at` (`created_at`);

--
-- Indexes for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_payments_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_payments_recorded_by_id_foreign` (`recorded_by_id`);

--
-- Indexes for table `booking_rooms`
--
ALTER TABLE `booking_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `convention_bookings`
--
ALTER TABLE `convention_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `convention_bookings_hall_id_foreign` (`hall_id`),
  ADD KEY `convention_bookings_created_by_id_foreign` (`created_by_id`),
  ADD KEY `idx_convention_bookings_event_date` (`event_date`),
  ADD KEY `idx_convention_bookings_status` (`status`),
  ADD KEY `idx_convention_bookings_created_at` (`created_at`);

--
-- Indexes for table `convention_halls`
--
ALTER TABLE `convention_halls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `convention_payments`
--
ALTER TABLE `convention_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `convention_payments_convention_booking_id_foreign` (`convention_booking_id`),
  ADD KEY `convention_payments_received_by_id_foreign` (`received_by_id`);

--
-- Indexes for table `extra_charge_categories`
--
ALTER TABLE `extra_charge_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `food_packages`
--
ALTER TABLE `food_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_food_packages_is_active` (`is_active`);

--
-- Indexes for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `footer_links_footer_section_id_foreign` (`footer_section_id`);

--
-- Indexes for table `footer_sections`
--
ALTER TABLE `footer_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hero_slides_is_active` (`is_active`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `navbar_links`
--
ALTER TABLE `navbar_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `resort_info`
--
ALTER TABLE `resort_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_room_number_unique` (`room_number`),
  ADD KEY `idx_rooms_room_type_id` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `additional_guests`
--
ALTER TABLE `additional_guests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addon_services`
--
ALTER TABLE `addon_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;

--
-- AUTO_INCREMENT for table `admin_menu_settings`
--
ALTER TABLE `admin_menu_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_payments`
--
ALTER TABLE `booking_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `booking_rooms`
--
ALTER TABLE `booking_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `convention_bookings`
--
ALTER TABLE `convention_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `convention_halls`
--
ALTER TABLE `convention_halls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `convention_payments`
--
ALTER TABLE `convention_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `extra_charge_categories`
--
ALTER TABLE `extra_charge_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_packages`
--
ALTER TABLE `food_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `footer_links`
--
ALTER TABLE `footer_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `footer_sections`
--
ALTER TABLE `footer_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `navbar_links`
--
ALTER TABLE `navbar_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resort_info`
--
ALTER TABLE `resort_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `additional_guests`
--
ALTER TABLE `additional_guests`
  ADD CONSTRAINT `additional_guests_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD CONSTRAINT `booking_payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_payments_recorded_by_id_foreign` FOREIGN KEY (`recorded_by_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `convention_bookings`
--
ALTER TABLE `convention_bookings`
  ADD CONSTRAINT `convention_bookings_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `convention_bookings_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `convention_halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `convention_payments`
--
ALTER TABLE `convention_payments`
  ADD CONSTRAINT `convention_payments_convention_booking_id_foreign` FOREIGN KEY (`convention_booking_id`) REFERENCES `convention_bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `convention_payments_received_by_id_foreign` FOREIGN KEY (`received_by_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `footer_links`
--
ALTER TABLE `footer_links`
  ADD CONSTRAINT `footer_links_footer_section_id_foreign` FOREIGN KEY (`footer_section_id`) REFERENCES `footer_sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
