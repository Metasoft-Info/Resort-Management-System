USE tufanconx_tufanresort;
-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: tufan_resort
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `changes` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'System data imported from old website','System',NULL,'{\"rooms\": \"11 rooms added\", \"hero_slides\": \"3 slides created\", \"resort_info\": \"Updated\", \"room_images\": \"Downloaded from tufanconventionresort.com\"}','127.0.0.1','CLI Import','2026-01-26 00:26:38','2026-01-26 00:26:38'),(2,1,'System data imported','System',NULL,'{\"items\": [\"rooms\", \"resort_info\", \"hero_slides\"], \"source\": \"tufanconventionresort.com\"}','127.0.0.1','CLI','2026-01-26 00:39:45','2026-01-26 00:39:45'),(3,2,'Updated booking status','Booking',1,'{\"new_status\": \"checked_in\", \"old_status\": \"confirmed\"}','127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-01-26 00:59:02','2026-01-26 00:59:02');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `additional_guests`
--

DROP TABLE IF EXISTS `additional_guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `additional_guests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `additional_guests_booking_id_foreign` (`booking_id`),
  CONSTRAINT `additional_guests_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `additional_guests`
--

LOCK TABLES `additional_guests` WRITE;
/*!40000 ALTER TABLE `additional_guests` DISABLE KEYS */;
/*!40000 ALTER TABLE `additional_guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addon_services`
--

DROP TABLE IF EXISTS `addon_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addon_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('room','convention','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `service_type` enum('room','convention','both') COLLATE utf8mb4_unicode_ci DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addon_services`
--

LOCK TABLES `addon_services` WRITE;
/*!40000 ALTER TABLE `addon_services` DISABLE KEYS */;
INSERT INTO `addon_services` VALUES (1,'Extra Bed','Additional bed for extra guest',500.00,NULL,'room','both',1,'2026-01-19 05:39:58','2026-01-19 05:39:58');
/*!40000 ALTER TABLE `addon_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_menu_settings`
--

DROP TABLE IF EXISTS `admin_menu_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_menu_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-circle',
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_pattern` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_menu_settings_menu_key_unique` (`menu_key`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu_settings`
--

LOCK TABLES `admin_menu_settings` WRITE;
/*!40000 ALTER TABLE `admin_menu_settings` DISABLE KEYS */;
INSERT INTO `admin_menu_settings` VALUES (1,'dashboard','Dashboard','fas fa-chart-line','admin.dashboard','admin.dashboard',NULL,1,1,1,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(2,'todays_summary','Today\'s Summary','fas fa-calendar-day','admin.todays-summary','admin.todays-summary',NULL,2,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(3,'rooms','Rooms','fas fa-bed','admin.rooms.index','admin.rooms.*','Rooms Management',10,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(4,'room_types','Room Types','fas fa-door-open','admin.room-types.index','admin.room-types.*','Rooms Management',11,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(5,'search_book_room','Search & Book','fas fa-search-plus','admin.premium-booking.index','admin.premium-booking.*','Room Bookings',20,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(6,'all_bookings','All Bookings','fas fa-list','admin.bookings.index','admin.bookings.*','Room Bookings',21,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(7,'search_book_hall','Search & Book Hall','fas fa-search-plus','admin.premium-convention.index','admin.premium-convention.*','Convention Halls',30,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(8,'all_hall_bookings','All Hall Bookings','fas fa-list','admin.convention-bookings.index','admin.convention-bookings.*','Convention Halls',31,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(9,'manage_halls','Manage Halls','fas fa-building','admin.convention-halls.index','admin.convention-halls.*','Convention Halls',32,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(10,'addon_services','Addon Services','fas fa-plus-circle','admin.addon-services.index','admin.addon-services.*','Services',40,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(11,'food_packages','Food Packages','fas fa-utensils','admin.food-packages.index','admin.food-packages.*','Services',41,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(12,'hero_slides','Hero Slides','fas fa-images','admin.hero-slides.index','admin.hero-slides.*','Website',50,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(13,'room_reports','Room Bookings Report','fas fa-file-alt','admin.reports.room-bookings','admin.reports.room-bookings','Reports',60,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(14,'convention_reports','Convention Bookings Report','fas fa-chart-bar','admin.reports.convention-bookings','admin.reports.convention-bookings','Reports',61,1,0,'2026-01-25 23:20:07','2026-01-25 23:20:07'),(15,'users','User Management','fas fa-users','admin.users.index','admin.users.*','System',70,1,1,'2026-01-25 23:20:07','2026-01-25 23:24:37'),(16,'settings','Settings','fas fa-cog','admin.settings.index','admin.settings.*','System',71,1,1,'2026-01-25 23:20:07','2026-01-25 23:26:07'),(17,'activity_logs','Activity Logs','fas fa-history','admin.activity-logs.index','admin.activity-logs.*','System',91,1,0,'2026-01-25 23:24:28','2026-01-25 23:24:28');
/*!40000 ALTER TABLE `admin_menu_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_payments`
--

DROP TABLE IF EXISTS `booking_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','card','mfs') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `type` enum('advance','payment','refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment',
  `note` text COLLATE utf8mb4_unicode_ci,
  `recorded_by_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_payments_booking_id_foreign` (`booking_id`),
  KEY `booking_payments_recorded_by_id_foreign` (`recorded_by_id`),
  CONSTRAINT `booking_payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_payments_recorded_by_id_foreign` FOREIGN KEY (`recorded_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_payments`
--

LOCK TABLES `booking_payments` WRITE;
/*!40000 ALTER TABLE `booking_payments` DISABLE KEYS */;
INSERT INTO `booking_payments` VALUES (1,2,5000.00,'cash','advance','Initial advance payment during booking creation',2,'2026-01-19 12:34:05','2026-01-19 12:34:05'),(2,2,6000.00,'cash','payment',NULL,2,'2026-01-25 22:53:30','2026-01-25 22:53:30'),(3,2,799.00,'cash','payment',NULL,2,'2026-01-25 23:07:45','2026-01-25 23:07:45');
/*!40000 ALTER TABLE `booking_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_nid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_nid_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visiting_card` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `check_in_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_date` date NOT NULL,
  `check_out_time` time DEFAULT NULL,
  `number_of_guests` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remaining_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','card','mfs') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('pending','partial','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `extra_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `extra_charges_description` text COLLATE utf8mb4_unicode_ci,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('none','percentage','flat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `food_package_id` bigint unsigned DEFAULT NULL,
  `food_package_guests` int NOT NULL DEFAULT '0',
  `food_package_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selected_addons` json DEFAULT NULL,
  `addons_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `extras` json DEFAULT NULL,
  `additional_guests` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `ac_preference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ac',
  `vat_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_by_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_room_id_foreign` (`room_id`),
  KEY `bookings_created_by_id_foreign` (`created_by_id`),
  CONSTRAINT `bookings_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`),
  CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,1,'Mir Javed Jahanger','4567643234567',NULL,NULL,NULL,NULL,NULL,'01811480222',NULL,NULL,NULL,'javedmirjeetu.official@gmail.com','243/A, Salimuddin Market Road, Mirpur 1, Dhaka','2026-01-20','14:00:00','2026-01-21','11:00:00',1,2000.00,500.00,1500.00,'cash','partial','checked_in',0.00,NULL,0.00,0.00,'none',NULL,0,0.00,NULL,0.00,NULL,NULL,NULL,'ac',0,0.00,2,'2026-01-19 11:59:24','2026-01-26 00:59:02'),(2,2,'Mir Javed Jahanger','4567643234567',NULL,NULL,NULL,NULL,NULL,'01811480222',NULL,NULL,NULL,'javedmirjeetu.official@gmail.com','243/A, Salimuddin Market Road, Mirpur 1, Dhaka','2026-01-21','14:00:00','2026-01-22','11:00:00',2,6000.00,11799.00,0.00,'cash','paid','checked_out',5799.00,'teA; test',0.00,0.00,'none',NULL,0,0.00,NULL,0.00,NULL,NULL,NULL,'ac',0,0.00,2,'2026-01-19 12:34:05','2026-01-25 23:39:19');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `convention_bookings`
--

DROP TABLE IF EXISTS `convention_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `convention_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hall_id` bigint unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_nid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `time_slot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_description` text COLLATE utf8mb4_unicode_ci,
  `number_of_guests` int NOT NULL,
  `food_package_id` bigint unsigned DEFAULT NULL,
  `food_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selected_addons` json DEFAULT NULL,
  `addon_quantities` json DEFAULT NULL,
  `addons_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hall_rent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('flat','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'flat',
  `discount_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vat_percentage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `remaining_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','card','mfs') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('pending','partial','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `program_status` enum('pending','confirmed','running','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `convention_bookings_hall_id_foreign` (`hall_id`),
  KEY `convention_bookings_created_by_id_foreign` (`created_by_id`),
  CONSTRAINT `convention_bookings_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`),
  CONSTRAINT `convention_bookings_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `convention_halls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `convention_bookings`
--

LOCK TABLES `convention_bookings` WRITE;
/*!40000 ALTER TABLE `convention_bookings` DISABLE KEYS */;
INSERT INTO `convention_bookings` VALUES (1,1,'Mir Javed Jahanger',NULL,'01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-21',NULL,NULL,'morning','fsdf',NULL,NULL,200,NULL,0.00,NULL,NULL,0.00,25000.00,0.00,'flat',0.00,0.00,0.00,25000.00,10000.00,15000.00,'cash','pending','confirmed','pending','dsfsdf',2,'2026-01-20 01:30:23','2026-01-20 01:30:23'),(2,1,'Mir Javed Jahanger',NULL,'01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-22',NULL,NULL,'afternoon','fsdf',NULL,NULL,500,NULL,0.00,NULL,NULL,0.00,25000.00,0.00,'flat',0.00,0.00,0.00,25000.00,5000.00,20000.00,'cash','partial','confirmed','pending','',2,'2026-01-20 01:35:26','2026-01-20 01:40:02'),(3,1,'Mir Javed Jahanger',NULL,'01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-22',NULL,NULL,'evening','fsdf',NULL,NULL,34,NULL,0.00,NULL,NULL,0.00,25000.00,0.00,'flat',0.00,0.00,0.00,25000.00,0.00,25000.00,'cash','pending','confirmed','pending','',2,'2026-01-20 01:40:43','2026-01-20 01:40:43'),(4,1,'Mir Javed Jahanger',NULL,'01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-21',NULL,NULL,'evening','wedding','SALAM HOUSE',NULL,600,NULL,0.00,'[\"1\"]','{\"1\": \"2\"}',1000.00,10000.00,0.00,'flat',0.00,1650.00,15.00,12650.00,12650.00,0.00,'cash','paid','confirmed','pending',NULL,2,'2026-01-20 02:11:37','2026-01-20 02:18:52'),(5,1,'Mir Javed Jahanger','4567643234567','01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-28',NULL,NULL,'morning','conference','SALAM HOUSE',NULL,600,NULL,0.00,'[\"1\"]','{\"1\": \"1\"}',500.00,10000.00,0.00,'flat',0.00,1575.00,15.00,12075.00,600.00,11475.00,'mfs','partial','confirmed','pending',NULL,2,'2026-01-25 10:41:22','2026-01-25 10:41:22'),(6,1,'Mir Javed Jahanger','4567643234567','01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-28',NULL,NULL,'morning','conference','SALAM HOUSE',NULL,600,NULL,0.00,'[\"1\"]','{\"1\": \"1\"}',500.00,10000.00,0.00,'flat',0.00,1575.00,15.00,12075.00,600.00,11475.00,'mfs','partial','confirmed','pending',NULL,2,'2026-01-25 10:45:42','2026-01-25 10:45:42'),(7,1,'Mir Javed Jahanger','4567643234567','01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-28',NULL,NULL,'morning','conference','SALAM HOUSE',NULL,600,NULL,0.00,'[\"1\"]','{\"1\": \"1\"}',500.00,10000.00,0.00,'flat',0.00,1575.00,15.00,12075.00,600.00,11475.00,'mfs','partial','confirmed','pending',NULL,2,'2026-01-25 10:47:38','2026-01-25 10:47:38'),(8,1,'Mir Javed Jahanger','4567643234567','01811480222',NULL,'javedmirjeetu.official@gmail.com',NULL,'2026-01-27',NULL,NULL,'afternoon','conference','SALAM HOUSE',NULL,700,NULL,0.00,'[\"1\"]','{\"1\": \"1\"}',500.00,10000.00,0.00,'flat',0.00,1575.00,15.00,12075.00,12075.00,0.00,'cash','paid','confirmed','pending',NULL,2,'2026-01-25 10:51:24','2026-01-25 10:51:58');
/*!40000 ALTER TABLE `convention_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `convention_halls`
--

DROP TABLE IF EXISTS `convention_halls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `convention_halls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `dimensions` decimal(10,2) DEFAULT NULL COMMENT 'in sq ft',
  `max_capacity` int DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `amenities` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `event_types` json DEFAULT NULL,
  `time_slots` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `convention_halls`
--

LOCK TABLES `convention_halls` WRITE;
/*!40000 ALTER TABLE `convention_halls` DISABLE KEYS */;
INSERT INTO `convention_halls` VALUES (1,'Tufan Convention Hall','No stay is complete without a party or social gathering. Tufan Convention & Resort offers banquet halls to be used for events. We offer large and small halls for your Wedding Party, Meetings, Conventions, Dinners and Corporate Product Launches etc.',5000.00,500,25000.00,1,'[\"AC\", \"Parking\"]','[\"convention-halls/XNkdRtqRxCxYQxRX6XXWnUFlUr9Fu0VpXwSmJfUR.jpg\"]','[\"Wedding\", \"Conference\", \"Birthday\", \"Meeting\", \"Seminar\", \"Party\", \"Exhibition\", \"Other\"]','[\"Morning (8AM-12PM)\", \"Afternoon (1PM-5PM)\", \"Evening (6PM-10PM)\", \"Full Day (8AM-10PM)\"]','2026-01-19 05:39:58','2026-01-26 01:31:22');
/*!40000 ALTER TABLE `convention_halls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `convention_payments`
--

DROP TABLE IF EXISTS `convention_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `convention_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `convention_booking_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mfs') COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `received_by_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `convention_payments_convention_booking_id_foreign` (`convention_booking_id`),
  KEY `convention_payments_received_by_id_foreign` (`received_by_id`),
  CONSTRAINT `convention_payments_convention_booking_id_foreign` FOREIGN KEY (`convention_booking_id`) REFERENCES `convention_bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `convention_payments_received_by_id_foreign` FOREIGN KEY (`received_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `convention_payments`
--

LOCK TABLES `convention_payments` WRITE;
/*!40000 ALTER TABLE `convention_payments` DISABLE KEYS */;
INSERT INTO `convention_payments` VALUES (1,2,5000.00,'cash','2026-01-20',NULL,2,'2026-01-20 01:40:02','2026-01-20 01:40:02'),(2,4,12650.00,'cash','2026-01-20',NULL,2,'2026-01-20 02:18:52','2026-01-20 02:18:52'),(3,8,12075.00,'cash','2026-01-25','jii',2,'2026-01-25 10:51:58','2026-01-25 10:51:58');
/*!40000 ALTER TABLE `convention_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_packages`
--

DROP TABLE IF EXISTS `food_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_per_person` decimal(10,2) NOT NULL,
  `items` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_packages`
--

LOCK TABLES `food_packages` WRITE;
/*!40000 ALTER TABLE `food_packages` DISABLE KEYS */;
INSERT INTO `food_packages` VALUES (1,'Basic Package','Simple and delicious meals',500.00,'[\"Rice\", \"Dal\", \"Chicken Curry\", \"Vegetables\", \"Salad\", \"Dessert\"]',1,'2026-01-19 05:39:58','2026-01-19 05:39:58');
/*!40000 ALTER TABLE `food_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_links`
--

DROP TABLE IF EXISTS `footer_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_section_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_links_footer_section_id_foreign` (`footer_section_id`),
  CONSTRAINT `footer_links_footer_section_id_foreign` FOREIGN KEY (`footer_section_id`) REFERENCES `footer_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_links`
--

LOCK TABLES `footer_links` WRITE;
/*!40000 ALTER TABLE `footer_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `footer_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_sections`
--

DROP TABLE IF EXISTS `footer_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_sections`
--

LOCK TABLES `footer_sections` WRITE;
/*!40000 ALTER TABLE `footer_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `footer_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hero_slides`
--

DROP TABLE IF EXISTS `hero_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

LOCK TABLES `hero_slides` WRITE;
/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
INSERT INTO `hero_slides` VALUES (1,'Welcome to Tufan Resort','Discover Luxury & Tranquility by the Lake','তুফান কনভেনশন এন্ড রিসোর্ট এ আপনাকে স্বাগতম','View Rooms','/rooms','hero/tufan1.jpg',1,1,'2026-01-19 05:39:58','2026-01-26 00:22:09'),(2,'Experience Luxury & Tranquility',NULL,'Premium accommodation and event hosting by the lake','Explore Venues','/convention-hall','hero/tufan2.jpg',2,1,'2026-01-26 00:22:09','2026-01-26 00:22:09'),(3,'Your Perfect Getaway Awaits',NULL,'Weddings, Conferences & Celebrations','Contact Us','/about','hero/tufan3.jpg',3,1,'2026-01-26 00:22:09','2026-01-26 00:22:09');
/*!40000 ALTER TABLE `hero_slides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_19_112225_1_create_rooms_table',1),(5,'2026_01_19_112225_2_create_convention_halls_table',1),(6,'2026_01_19_112225_3_create_bookings_table',1),(7,'2026_01_19_112225_4_create_convention_bookings_table',1),(8,'2026_01_19_112225_5_create_convention_payments_table',1),(9,'2026_01_19_112225_create_addon_services_table',1),(10,'2026_01_19_112225_create_food_packages_table',1),(11,'2026_01_19_112225_create_hero_slides_table',1),(12,'2026_01_19_112226_create_activity_logs_table',1),(13,'2026_01_19_112226_create_footer_sections_table',1),(14,'2026_01_19_112226_create_menu_items_table',1),(15,'2026_01_19_112226_create_navbar_links_table',1),(16,'2026_01_19_112226_create_resort_info_table',1),(17,'2026_01_19_112226_create_room_types_table',1),(18,'2026_01_19_112226_create_system_settings_table',1),(19,'2026_01_19_112227_create_footer_links_table',1),(21,'2026_01_19_170502_add_room_type_id_to_rooms_table',2),(22,'2026_01_20_000003_create_additional_guests_table',3),(23,'2026_01_20_000004_create_booking_payments_table',3),(24,'2026_01_20_084048_add_missing_fields_to_resort_info_table',4),(25,'2026_01_26_000001_create_admin_menu_settings_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `navbar_links`
--

DROP TABLE IF EXISTS `navbar_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `navbar_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navbar_links`
--

LOCK TABLES `navbar_links` WRITE;
/*!40000 ALTER TABLE `navbar_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `navbar_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resort_info`
--

DROP TABLE IF EXISTS `resort_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resort_info` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `resort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resort_tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_text` text COLLATE utf8mb4_unicode_ci,
  `mission_text` text COLLATE utf8mb4_unicode_ci,
  `footer_description` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `map_embed_url` text COLLATE utf8mb4_unicode_ci,
  `facilities` json DEFAULT NULL,
  `social_links` json DEFAULT NULL,
  `copyright_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `header_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resort_info`
--

LOCK TABLES `resort_info` WRITE;
/*!40000 ALTER TABLE `resort_info` DISABLE KEYS */;
INSERT INTO `resort_info` VALUES (1,'Tufan Convention & Resort','তুফান কনভেনশন এন্ড রিসোর্ট','Welcome to Tufan Resort, where luxury meets nature. Nestled in the heart of pristine landscapes, we offer world-class hospitality and unforgettable experiences.','Our mission is to provide guests with exceptional service, comfort, and memorable experiences that exceed expectations.','Premium accommodation and event hosting services. Experience luxury and tranquility by the lake at Kamalnagar, Satkhira.','Kamalnagar, Satkhira Sadar','+88 01958-216728','info@tufanconventionresort.com',NULL,'[\"Wellness\", \"Restaurant\", \"Gym\", \"Garden\", \"Parking\", \"Kids Indoor and Outdoor Playground\", \"Convention Halls\"]','{\"facebook\": \"https://www.facebook.com/TufanConventionCenter\"}','© 2026 Tufan Resort. All rights reserved.','2026-01-19 05:39:58','2026-01-26 00:16:41','logos/vjHnA1qA8XlQoYDJjunEqTNqBV0BFMsGZHnHYLEr.jpg','logos/8CV9dpVcY71MaIVkK9iaz0PqkQT90HuPYMr8aeP6.jpg','logos/E2Zryu7zSaqzRHRXNHl4yUwx0uFh7gzPZHUoVnKt.jpg','logos/KpZ7f3Fj4grRn0WTemEypQaLpxtWDjbv4n0dcbw1.jpg');
/*!40000 ALTER TABLE `resort_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_types`
--

LOCK TABLES `room_types` WRITE;
/*!40000 ALTER TABLE `room_types` DISABLE KEYS */;
INSERT INTO `room_types` VALUES (1,'Standard','Standard Room',2000.00,1,'2026-01-19 11:07:38','2026-01-19 11:07:38'),(2,'Deluxe','Deluxe Room',2000.00,1,'2026-01-19 11:07:38','2026-01-19 11:07:38'),(3,'Suite','Suite Room',2000.00,1,'2026-01-19 11:07:38','2026-01-19 11:07:38'),(4,'Family','Family Room',2000.00,1,'2026-01-19 11:07:39','2026-01-19 11:07:39'),(5,'Superior','AC Superior room with luxury amenities',3000.00,1,'2026-01-26 00:19:14','2026-01-26 00:19:14');
/*!40000 ALTER TABLE `room_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_type_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('standard','deluxe','suite','family') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_per_night` decimal(10,2) NOT NULL,
  `has_ac` tinyint(1) NOT NULL DEFAULT '1',
  `ac_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `non_ac_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_guests` int DEFAULT NULL,
  `number_of_beds` int DEFAULT NULL,
  `amenities` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `status` enum('available','booked','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_room_number_unique` (`room_number`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'101',2,'Deluxe Ocean View','deluxe','Spacious room with stunning lake views, perfect for couples.',3500.00,1,3500.00,2500.00,2,1,'[\"WiFi\", \"AC\", \"TV\", \"Mini Bar\", \"Lake View\"]','[\"rooms/wMjS3CneTex04tY5x1SVHf8bOBf7uCucFkUKgY4N.png\"]','available','2026-01-19 05:39:58','2026-01-25 10:18:43'),(2,'201',3,'Family Suite','suite','Large family suite with separate bedroom and living area.',6000.00,1,6000.00,4500.00,6,3,'[\"WiFi\", \"AC\", \"TV\", \"Mini Bar\", \"Kitchenette\", \"Balcony\"]',NULL,'available','2026-01-19 05:39:58','2026-01-19 11:08:59'),(3,'102',1,'Standard Room','standard','Comfortable standard room with all basic amenities.',2000.00,1,2000.00,1500.00,2,1,'[\"WiFi\", \"AC\", \"TV\"]',NULL,'available','2026-01-19 05:39:58','2026-01-19 11:08:59'),(4,'01',2,'VIP Couple Bed (Delux Room)','deluxe','VIP Couple Bed Deluxe Room with AC. King Bed with 50 sqm space.',2000.00,1,2000.00,0.00,3,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan4.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(5,'02',2,'VIP Double Bed (Delux Room)','deluxe','VIP Double Bed Deluxe Room with AC. King Bed with 60 sqm space.',3000.00,1,3000.00,0.00,4,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan5.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(6,'03',2,'VIP Couple Bed (Delux Room)','deluxe','VIP Couple Bed Deluxe Room with AC.',2000.00,1,2000.00,0.00,3,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan6.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(7,'04',2,'VIP Couple Bed (Delux Room)','deluxe','VIP Couple Bed Deluxe Room with AC.',2000.00,1,2000.00,0.00,3,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan7.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(8,'05',2,'VIP Couple Bed (Delux Room)','deluxe','VIP Couple Bed Premium Deluxe Room with AC.',3000.00,1,3000.00,0.00,3,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan8.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(9,'06',1,'Single Bed (Standard Room)','standard','Single Bed Standard Room. Non-AC with 30 sqm space.',1000.00,0,0.00,1000.00,1,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan4.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(10,'07',1,'Single Bed (Standard Room)','standard','Single Bed Standard Room. Non-AC with 30 sqm space.',1000.00,0,0.00,1000.00,1,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan5.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(11,'09',5,'VIP Couple Bed (Superior Room)','suite','VIP Couple Bed Superior Room with AC. Double King Bed with 50 sqm space.',3000.00,1,3000.00,0.00,2,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan6.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(12,'10',5,'VIP Couple Bed (Superior Room)','suite','VIP Couple Bed Superior Room with AC.',3000.00,1,3000.00,0.00,2,1,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan7.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(13,'11',5,'VIP Double Bed (Superior Room)','suite','VIP Double Bed Superior Room with AC. 2 King Beds with 50 sqm space.',3500.00,1,3500.00,0.00,4,2,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan8.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59'),(14,'12',5,'VIP Couple Bed (Superior Room)','suite','VIP Couple Bed Premium Superior Room with AC.',4000.00,1,4000.00,0.00,4,2,'[\"WiFi\", \"TV\", \"Hot Water\", \"Room Service\"]','[\"rooms/tufan4.jpg\"]','available','2026-01-26 00:20:08','2026-01-26 00:23:59');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ApFj1vv84ggxxF4hVrGTccV24hbGMrAHu0qhxTx8',2,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHB1MmhkTEtTT0x0UTZGall2T3ZhQXQ4T2JpUXlHdGhueUN4SHZBbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=',1769413686);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('owner','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `permissions` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com','2026-01-19 05:39:18','$2y$12$hLkbfyJHixapMCTS0P.qAu.xyIpL/.IhRDCoXqYt3OKZveYQtI1hi','staff',NULL,1,'nUCNlrQSAz','2026-01-19 05:39:18','2026-01-19 05:39:18'),(2,'Resort Owner','owner@tufanresort.com',NULL,'$2y$12$FRBQPhS3ik.9aSoWEeyCbu6KuI5TSPXCYN8gvczFWpL5yjc4ejGJ6','owner','[\"*\"]',1,'5S493jy3ZS0e3XdqvHTN0bGidTTnPWolMeb4RFJ12yO26pS80bv30Pr3SEBy','2026-01-19 05:39:58','2026-01-19 05:39:58'),(3,'Staff Member','staff@tufanresort.com',NULL,'$2y$12$H/cxj8/rmKtQsyWZFmuaKeB3sxDdoKoKEhGEXddyFFBM3d97ssEA2','staff',NULL,1,NULL,'2026-01-19 05:39:58','2026-01-19 05:39:58');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-27 19:00:43
