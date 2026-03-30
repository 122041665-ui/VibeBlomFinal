-- MySQL dump 10.13  Distrib 9.6.0, for macos15 (arm64)
--
-- Host: localhost    Database: vibebloom
-- ------------------------------------------------------
-- Server version	9.6.0

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
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `entity_id` int DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_label` varchar(100) NOT NULL,
  `performed_by` varchar(255) DEFAULT NULL,
  `performer_role` varchar(100) DEFAULT NULL,
  `status_label` varchar(50) DEFAULT NULL,
  `details` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activity_module` (`module`),
  KEY `idx_activity_action_type` (`action_type`),
  KEY `idx_activity_created_at` (`created_at`),
  KEY `idx_activity_entity_id` (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,'approvals',3,'La hoja','created','Solicitud enviada','Rafael Resendiz','admin','Pendiente','Se envió solicitud de aprobación para \'La hoja\' en Santiago de Querétaro','2026-03-28 00:30:11'),(2,'approvals',3,'La hoja','approved','Aprobado','Administrador','admin','Activo','Se aprobó la solicitud \'La hoja\' y se publicó el lugar en la ciudad \'Santiago de Querétaro\'.','2026-03-27 18:56:56'),(3,'approvals',4,'La Hoja','created','Solicitud enviada','Rafael Resendiz','admin','Pendiente','Se envió solicitud de aprobación para \'La Hoja\' en Santiago de Querétaro','2026-03-29 18:09:19'),(4,'approvals',3,'La hoja','deleted','Solicitud eliminada','Rafael Resendiz','admin','Cancelado','Se eliminó la solicitud \'La hoja\'','2026-03-29 18:13:13'),(5,'approvals',5,'La Hoja','created','Solicitud enviada','Dulce Mariel','user','Pendiente','Se envió solicitud de aprobación para \'La Hoja\' en Santiago de Querétaro','2026-03-29 18:29:02'),(6,'approvals',6,'Estadio','created','Solicitud enviada','Dulce Mariel','user','Pendiente','Se envió solicitud de aprobación para \'Estadio\' en Santiago de Querétaro','2026-03-29 19:31:19');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `entity_id` bigint DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `description` text,
  `metadata_json` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'general','export_pdf',NULL,'Reporte activity','Se exportó el reporte activity en formato PDF.','{\"report\": \"activity\", \"format\": \"pdf\", \"start_date\": \"\", \"end_date\": \"\", \"place_type\": \"\", \"rating\": \"\"}','2026-03-17 01:25:47'),(2,'general','export_xlsx',NULL,'Reporte places','Se exportó el reporte places en formato XLSX.','{\"report\": \"places\", \"format\": \"xlsx\", \"start_date\": \"\", \"end_date\": \"\", \"place_type\": \"Mirador\", \"rating\": \"\", \"operation\": \"created\", \"detail_level\": \"detailed\", \"include_deleted\": \"0\"}','2026-03-17 02:12:09'),(3,'general','export_pdf',NULL,'Reporte places','Se exportó el reporte places en formato PDF.','{\"report\": \"places\", \"format\": \"pdf\", \"start_date\": \"\", \"end_date\": \"\", \"place_type\": \"Mirador\", \"rating\": \"\", \"operation\": \"created\", \"detail_level\": \"detailed\", \"include_deleted\": \"0\"}','2026-03-17 02:12:24'),(4,'general','export_pdf',NULL,'Reporte reviews','Se exportó el reporte reviews en formato PDF.','{\"report\": \"reviews\", \"format\": \"pdf\", \"start_date\": \"\", \"end_date\": \"\", \"place_type\": \"\", \"rating\": \"\", \"operation\": \"all\", \"detail_level\": \"summary\", \"include_deleted\": \"0\"}','2026-03-17 22:46:59'),(5,'general','export_pdf',NULL,'Reporte reviews','Se exportó el reporte reviews en formato PDF.','{\"report\": \"reviews\", \"format\": \"pdf\", \"start_date\": \"2025-09-01\", \"end_date\": \"\", \"place_type\": \"\", \"rating\": \"\", \"operation\": \"created\", \"detail_level\": \"detailed\", \"include_deleted\": \"0\"}','2026-03-17 22:47:26'),(6,'general','export_pdf',NULL,'Reporte reviews','Se exportó el reporte reviews en formato PDF.','{\"report\": \"reviews\", \"format\": \"pdf\", \"start_date\": \"\", \"end_date\": \"\", \"place_type\": \"\", \"rating\": \"\", \"operation\": \"all\", \"detail_level\": \"summary\", \"include_deleted\": \"0\"}','2026-03-18 05:15:11'),(7,'users','delete',7,'Uriel Suarez','Se eliminó el usuario \'Uriel Suarez\'.','{}','2026-03-18 07:36:05'),(8,'places','reject',16,'Puerta La Victoria','Se rechazó el lugar \'Puerta La Victoria\'.','{\"type\": \"Antro\", \"city\": \"Querétaro\", \"status\": \"Rechazado\"}','2026-03-18 07:37:35'),(9,'places','reject',16,'Puerta La Victoria','Se rechazó el lugar \'Puerta La Victoria\'.','{\"type\": \"Antro\", \"city\": \"Querétaro\", \"status\": \"Rechazado\"}','2026-03-18 07:37:39'),(10,'places','approve',17,'Estadio Azteca','Se aprobó el lugar \'Estadio Azteca\'.','{\"type\": \"Mirador\", \"city\": \"Ciudad de México\", \"status\": \"Aprobado\"}','2026-03-20 20:52:56'),(11,'places','approve',17,'Estadio Azteca','Se aprobó el lugar \'Estadio Azteca\'.','{\"type\": \"Mirador\", \"city\": \"Ciudad de México\", \"status\": \"Aprobado\"}','2026-03-20 20:53:18'),(12,'places','approve',17,'Estadio Azteca','Se aprobó el lugar \'Estadio Azteca\'.','{\"type\": \"Mirador\", \"city\": \"Ciudad de México\", \"status\": \"Aprobado\"}','2026-03-20 21:03:05');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-122042665@upq.edu.mx|127.0.0.1','i:1;',1774763950),('laravel-cache-122042665@upq.edu.mx|127.0.0.1:timer','i:1774763950;',1774763950),('laravel-cache-2ec18631cc02fd182400c72b79c4ae12','i:1;',1774485595),('laravel-cache-2ec18631cc02fd182400c72b79c4ae12:timer','i:1774485595;',1774485595),('laravel-cache-53f70946fe888e031b214a682b391ef4','i:1;',1773290218),('laravel-cache-53f70946fe888e031b214a682b391ef4:timer','i:1773290218;',1773290218),('laravel-cache-54f388920faa66a2557bb72cf1647104','i:3;',1773289919),('laravel-cache-54f388920faa66a2557bb72cf1647104:timer','i:1773289919;',1773289919),('laravel-cache-736f55b41e27eb73b02f4dc109ace2a1','i:1;',1773476117),('laravel-cache-736f55b41e27eb73b02f4dc109ace2a1:timer','i:1773476117;',1773476117),('laravel-cache-76217a01ebb388982b896785db8079be','i:2;',1774807364),('laravel-cache-76217a01ebb388982b896785db8079be:timer','i:1774807364;',1774807364),('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb','i:1;',1773991654),('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1773991654;',1773991654),('laravel-cache-8a659ba7318704f3b203d12c131347dc','i:1;',1773289934),('laravel-cache-8a659ba7318704f3b203d12c131347dc:timer','i:1773289934;',1773289934),('laravel-cache-97bbb54c98a5cc0268dd20a6cd027e3d','i:1;',1774808938),('laravel-cache-97bbb54c98a5cc0268dd20a6cd027e3d:timer','i:1774808938;',1774808938),('laravel-cache-b1381e665d1d592ac7ceca1d33d8d1af','i:1;',1774763950),('laravel-cache-b1381e665d1d592ac7ceca1d33d8d1af:timer','i:1774763950;',1774763950),('laravel-cache-casitillo.delira@upq.edu.mx|127.0.0.1','i:1;',1773289934),('laravel-cache-casitillo.delira@upq.edu.mx|127.0.0.1:timer','i:1773289934;',1773289934),('laravel-cache-casitillodelira@upq.edu.mx|127.0.0.1','i:3;',1773289919),('laravel-cache-casitillodelira@upq.edu.mx|127.0.0.1:timer','i:1773289919;',1773289919),('laravel-cache-castillo.delira@gmail.com|127.0.0.1','i:1;',1773289997),('laravel-cache-castillo.delira@gmail.com|127.0.0.1:timer','i:1773289997;',1773289997),('laravel-cache-castillo.delira@upq.edu.mx|127.0.0.1','i:1;',1773290218),('laravel-cache-castillo.delira@upq.edu.mx|127.0.0.1:timer','i:1773290218;',1773290218),('laravel-cache-castillodelira@gmail.com|127.0.0.1','i:1;',1773290005),('laravel-cache-castillodelira@gmail.com|127.0.0.1:timer','i:1773290005;',1773290005),('laravel-cache-castillodelira@upq.edu.x|127.0.0.1','i:1;',1773476117),('laravel-cache-castillodelira@upq.edu.x|127.0.0.1:timer','i:1773476117;',1773476117),('laravel-cache-e9d938695991ab5bb4ea30ee19b9765d','i:1;',1773867173),('laravel-cache-e9d938695991ab5bb4ea30ee19b9765d:timer','i:1773867173;',1773867173),('laravel-cache-f1f0bb50bf48f6ad0b0741eff12d5ec7','i:1;',1773290005),('laravel-cache-f1f0bb50bf48f6ad0b0741eff12d5ec7:timer','i:1773290005;',1773290005),('laravel-cache-f2d7cbbe521884614a8cde4782d8dabc','i:1;',1773289997),('laravel-cache-f2d7cbbe521884614a8cde4782d8dabc:timer','i:1773289997;',1773289997),('vibebloom-cache-a0cfb06aed6a4ecc7c3e3bae313590a5','i:1;',1774903367),('vibebloom-cache-a0cfb06aed6a4ecc7c3e3bae313590a5:timer','i:1774903367;',1774903367),('vibebloom-cache-edf5f843c9258fb9d2b2870e8f08d360','i:1;',1774812635),('vibebloom-cache-edf5f843c9258fb9d2b2870e8f08d360:timer','i:1774812635;',1774812635);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `place_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_place_id_unique` (`user_id`,`place_id`),
  KEY `favorites_place_id_foreign` (`place_id`),
  CONSTRAINT `favorites_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (9,10,20,'2026-03-26 06:56:39','2026-03-26 06:56:39'),(20,16,21,NULL,NULL),(21,3,21,NULL,NULL);
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `memories`
--

DROP TABLE IF EXISTS `memories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `memory_date` date DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memories_user_id_foreign` (`user_id`),
  CONSTRAINT `memories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memories`
--

LOCK TABLES `memories` WRITE;
/*!40000 ALTER TABLE `memories` DISABLE KEYS */;
INSERT INTO `memories` VALUES (1,11,'Salida con amigos','Fue una noche muy buena','2026-03-28','Querétaro',NULL,NULL),(2,11,'Salida actualizada','Descripción nueva','2026-03-29','Centro',NULL,NULL),(5,16,'xag<v','san','2026-03-11','sjabs',NULL,NULL);
/*!40000 ALTER TABLE `memories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memory_photos`
--

DROP TABLE IF EXISTS `memory_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memory_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `memory_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memory_photos_memory_id_foreign` (`memory_id`),
  CONSTRAINT `memory_photos_memory_id_foreign` FOREIGN KEY (`memory_id`) REFERENCES `memories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memory_photos`
--

LOCK TABLES `memory_photos` WRITE;
/*!40000 ALTER TABLE `memory_photos` DISABLE KEYS */;
INSERT INTO `memory_photos` VALUES (3,5,'memories/77a233f58a0b4465a09ac3a0012d7d7a.jpg',NULL,NULL),(4,5,'memories/b737dd6a672c405fb8aaf2539cb055d3.jpg',NULL,NULL),(5,5,'memories/6a70e636f3fe4eab99d42a7b4cecbb48.jpg',NULL,NULL);
/*!40000 ALTER TABLE `memory_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_11_21_033803_create_personal_access_tokens_table',1),(5,'2025_11_21_203851_create_places_table',1),(6,'2025_11_22_081856_create_favorites_table',1),(7,'2025_11_22_092354_add_two_factor_columns_to_users_table',1),(8,'2025_11_22_093021_add_missing_two_factor_columns_to_users_table',1),(9,'2025_11_23_014055_add_role_to_users_table',1),(10,'2025_11_23_020634_create_permission_tables',1),(11,'2026_01_21_065907_add_photos_to_places_table',2),(20,'2026_01_21_073833_add_photos_type_rating_to_places_table',3),(21,'2026_01_21_164537_create_reviews_table',3),(22,'2026_01_21_164538_create_review_replies_table',3),(23,'2026_01_22_063547_create_memories_table',3),(24,'2026_01_22_063547_create_memory_photos_table',3),(25,'2026_01_23_010414_add_location_fields_to_places_table',3),(26,'2026_01_23_015838_add_address_reference_to_places_table',3),(27,'2026_01_23_054432_add_lat_lng_to_places_table',3),(29,'2026_03_12_022000_create_place_submissions_table',4),(30,'2026_03_12_022025_create_place_submission_photos_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (3,'App\\Models\\User',2),(1,'App\\Models\\User',3),(2,'App\\Models\\User',4);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `place_id` int DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place_submission_photos`
--

DROP TABLE IF EXISTS `place_submission_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `place_submission_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `place_submission_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `place_submission_photos_place_submission_id_foreign` (`place_submission_id`),
  CONSTRAINT `place_submission_photos_place_submission_id_foreign` FOREIGN KEY (`place_submission_id`) REFERENCES `place_submissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place_submission_photos`
--

LOCK TABLES `place_submission_photos` WRITE;
/*!40000 ALTER TABLE `place_submission_photos` DISABLE KEYS */;
INSERT INTO `place_submission_photos` VALUES (1,1,'place-submissions/nxegRRiZeajW8abaejSFo9yDFq1JGkgziEzxjw97.png','2026-03-12 12:03:49','2026-03-12 12:03:49'),(2,2,'place-submissions/yAqbRYOsY4Ys6MJAm78oqgjavgeX62c8mKZ9XaNi.jpg','2026-03-26 06:55:00','2026-03-26 06:55:00'),(5,4,'place-submissions/UckQexK8IaRnp3fqpMgi9iSJRRmXmdgj390tnTxD.jpg','2026-03-30 00:09:19','2026-03-30 00:09:19'),(6,4,'place-submissions/pvlqyUjGxjbboYWT7AOU6yxCptuZFC89aBnm8rri.jpg','2026-03-30 00:09:19','2026-03-30 00:09:19'),(7,5,'place-submissions/qn7s8D6nwExWXLeS7FZ9VjfsOMy9yQh2u9UKwv3T.jpg','2026-03-30 00:29:02','2026-03-30 00:29:02'),(8,6,'place-submissions/kvLEPHBjfY8ie97pXQbf4HDQ1JkWvDsm098quvo8.jpg','2026-03-30 01:31:19','2026-03-30 01:31:19');
/*!40000 ALTER TABLE `place_submission_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `place_submissions`
--

DROP TABLE IF EXISTS `place_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `place_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_place_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_to_flask` tinyint(1) NOT NULL DEFAULT '0',
  `sent_to_flask_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `place_submissions_user_id_foreign` (`user_id`),
  CONSTRAINT `place_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `place_submissions`
--

LOCK TABLES `place_submissions` WRITE;
/*!40000 ALTER TABLE `place_submissions` DISABLE KEYS */;
INSERT INTO `place_submissions` VALUES (1,2,'Maniatica','Cafetería',4,240.00,'Santiago de Querétaro','place.41552031','Calle 33 1011, 76080 Santiago de Querétaro, Querétaro, México',20.5651811,-100.3930302,NULL,'approved',1,'2026-03-12 12:03:49','2026-03-12 12:03:49','2026-03-25 14:26:40'),(2,10,'El rincón del jaguar','Restaurante',4,200.00,'Querétaro','place.21833887','Calle Miguel Hidalgo 3b, 76000 Santiago de Querétaro, Querétaro, México',20.5942576,-100.3932148,NULL,'approved',1,'2026-03-26 06:55:00','2026-03-26 06:55:00','2026-03-28 00:48:53'),(4,3,'La Hoja','Restaurante',1,459.00,'Santiago de Querétaro','place.41552031','Privada R. Fresnos 9, 76168 Santiago de Querétaro, Querétaro, México',20.6141082,-100.3959472,'buen lugar','pending',1,'2026-03-30 00:09:19','2026-03-30 00:09:19','2026-03-30 00:09:19'),(5,16,'La Hoja','Cafetería',4,300.00,'Santiago de Querétaro','place.41552031','Calle Nicolás Campa 49, 76000 Santiago de Querétaro, Querétaro, México',20.5871667,-100.3980275,'mal lugar','pending',1,'2026-03-30 00:29:02','2026-03-30 00:29:02','2026-03-30 00:29:02'),(6,16,'Estadio','Restaurante',3,450.00,'Santiago de Querétaro','place.41552031','Calle Topacio 95, 76150 Santiago de Querétaro, Querétaro, México',20.6046318,-100.4040870,'muy buen lugar','rejected',1,'2026-03-30 01:31:19','2026-03-30 01:31:19','2026-03-30 01:31:19');
/*!40000 ALTER TABLE `place_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `places` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photos` json DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `places_user_id_foreign` (`user_id`),
  CONSTRAINT `places_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `places`
--

LOCK TABLES `places` WRITE;
/*!40000 ALTER TABLE `places` DISABLE KEYS */;
INSERT INTO `places` VALUES (17,3,'Estadio Banorte','Ciudad de México','Otro',4,'Circuito Estadio Azteca 3465, 04650 Ciudad de México, México',NULL,19.3028271,-99.1488315,1500.00,'places/fcPJdXPbK60MWdYqD9gZi4u9KCmn9uKKWinRcTzM.jpg',NULL,'El mejor lugar','2026-03-08 07:05:35','2026-03-29 18:20:53'),(20,2,'Maniatica','Santiago de Querétaro','Cafetería',4,'Calle 33 1011, 76080 Santiago de Querétaro, Querétaro, México',NULL,20.5651811,-100.3930302,240.00,'place-submissions/nxegRRiZeajW8abaejSFo9yDFq1JGkgziEzxjw97.png',NULL,NULL,'2026-03-25 14:26:40','2026-03-25 14:26:40'),(21,10,'El rincón del jaguar','Querétaro','',4,'Calle Miguel Hidalgo 3b, 76000 Santiago de Querétaro, Querétaro, México',NULL,20.5942576,-100.3932148,200.00,'place-submissions/yAqbRYOsY4Ys6MJAm78oqgjavgeX62c8mKZ9XaNi.jpg',NULL,'','2026-03-28 00:48:53','2026-03-30 02:48:41');
/*!40000 ALTER TABLE `places` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_replies`
--

DROP TABLE IF EXISTS `review_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_replies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `review_replies_review_id_foreign` (`review_id`),
  KEY `review_replies_user_id_foreign` (`user_id`),
  CONSTRAINT `review_replies_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `review_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_replies`
--

LOCK TABLES `review_replies` WRITE;
/*!40000 ALTER TABLE `review_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `review_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `place_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_place_id_foreign` (`place_id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  CONSTRAINT `reviews_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (11,21,3,'el mejor lugar del mundo',NULL,'2026-03-30 01:07:36');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2025-11-23 13:22:04','2025-11-23 13:22:04'),(2,'moderator','web','2026-03-26 03:13:41','2026-03-26 03:13:41'),(3,'user','web','2026-03-26 03:14:41','2026-03-26 03:14:41');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('5hyvhQ1lbq0I4GRQgUSLFNPb2ewV2ttyZlxJBgOC',3,'151.101.128.223','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTo4OntzOjY6Il90b2tlbiI7czo0MDoiSVBNR3ZpQmpvTkQzcnpQSTNYcXFGbzhEekgzR1BHTEc1MW9qVkszdiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTI6ImFjY2Vzc190b2tlbiI7czoxODA6ImV5SmhiR2NpT2lKSVV6STFOaUlzSW5SNWNDSTZJa3BYVkNKOS5leUp6ZFdJaU9pSXpJaXdpWlcxaGFXd2lPaUl4TWpJd05ERTJOalZBZFhCeExtVmtkUzV0ZUNJc0luSnZiR1VpT2lKaFpHMXBiaUlzSW1WNGNDSTZNVGMzTkRrd05qa3dPSDAuN05JYko3S1V4a2FiaWMxZmxrSE04T3lGR1ZmcEFyYU1TdjJHSS1ldDV3byI7czo4OiJhcGlfdXNlciI7YTo0OntzOjQ6Im5hbWUiO3M6MTU6IlJhZmFlbCBSZXNlbmRpeiI7czo1OiJlbWFpbCI7czoyMDoiMTIyMDQxNjY1QHVwcS5lZHUubXgiO3M6NDoicm9sZSI7czo1OiJhZG1pbiI7czoyOiJpZCI7aTozO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2NDoiODI2NjFmYjQyZjI2NDBlYTEyNmQ2ZmRiYTA5NjI1OWE1MGY5YjdkYTc2ZWEzY2M0ZTcyZDkzZTEzNGIwODMyZiI7fQ==',1774903448),('6P5OvsBMxfPT12KkOskytCU82m2uGbM8xnc35nWp',3,'151.101.128.223','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRWFZQUswcnlZQmFIMVVRZmJGRFVYRG12TDFJd1hRQ3ZTeGE1WmlCYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czoxMjoiYWNjZXNzX3Rva2VuIjtzOjE4MDoiZXlKaGJHY2lPaUpJVXpJMU5pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SnpkV0lpT2lJeklpd2laVzFoYVd3aU9pSXhNakl3TkRFMk5qVkFkWEJ4TG1Wa2RTNXRlQ0lzSW5KdmJHVWlPaUpoWkcxcGJpSXNJbVY0Y0NJNk1UYzNORGcyTWpReU4zMC4zT09JZU9BVllHaVVWWFpZMTZzZEdPZWpvcHZ1N2VqcVBxd09XSVg2N3ZBIjtzOjg6ImFwaV91c2VyIjthOjQ6e3M6NDoibmFtZSI7czoxNToiUmFmYWVsIFJlc2VuZGl6IjtzOjU6ImVtYWlsIjtzOjIwOiIxMjIwNDE2NjVAdXBxLmVkdS5teCI7czo0OiJyb2xlIjtzOjU6ImFkbWluIjtzOjI6ImlkIjtpOjM7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjY0OiI5ZDNmNzQ1NWExOGJhYTM5ZWY5NDM1MjM2NjYyMjc0ZmNhODdlZGVhMzE1NTgwNDYzY2VmZGYyZGI3ODQ4NmZlIjt9',1774858827),('ARFfUjLdu0LGvDH1xjHIubBLRjppA1i1LmIh3A3E',3,'151.101.128.223','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTnpiTWk4eExzUDlHdHpPNmhhbk9FQ0xpZHVHWXZJZUJYemxiNTBoNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wbGFjZXMvMTYiO3M6NToicm91dGUiO3M6MTE6InBsYWNlcy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMjoiYWNjZXNzX3Rva2VuIjtzOjE4MDoiZXlKaGJHY2lPaUpJVXpJMU5pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SnpkV0lpT2lJeklpd2laVzFoYVd3aU9pSXhNakl3TkRFMk5qVkFkWEJ4TG1Wa2RTNXRlQ0lzSW5KdmJHVWlPaUpoWkcxcGJpSXNJbVY0Y0NJNk1UYzNORGcwTURZNE9YMC45eDlOQm1BQXpsbnV0bTZDeVQ4THZqQ25ReDlnaUVvdE94b2Jhb3QwU2w4IjtzOjg6ImFwaV91c2VyIjthOjQ6e3M6NDoibmFtZSI7czoxNToiUmFmYWVsIFJlc2VuZGl6IjtzOjU6ImVtYWlsIjtzOjIwOiIxMjIwNDE2NjVAdXBxLmVkdS5teCI7czo0OiJyb2xlIjtzOjU6ImFkbWluIjtzOjI6ImlkIjtpOjM7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjY0OiI3OWNhOWFkZGE3MmUwMjE5ZTM0MmMyNWVkZTE4MzkwMTY2ZTg1MThhNGE3M2M0ZGFmYjE2OTAwMDgxNmRkYTBkIjt9',1774837133),('JuIDUFaEMK1107J4Vsci8M6hRaCBqUKxRgCUfdqH',16,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiNkRpMmtuankyZ2hXUDdXVHdRRkFMV1FiSXp5dHBGSllJb2VuTFEwUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9taXMtbHVnYXJlcyI7czo1OiJyb3V0ZSI7czoxMToicGxhY2VzLm1pbmUiO31zOjEyOiJhY2Nlc3NfdG9rZW4iO3M6MTc5OiJleUpoYkdjaU9pSklVekkxTmlJc0luUjVjQ0k2SWtwWFZDSjkuZXlKemRXSWlPaUl4TmlJc0ltVnRZV2xzSWpvaVpIVnNZMlZ0WVhKQWRYQnhMbVZrZFM1dGVDSXNJbkp2YkdVaU9pSjFjMlZ5SWl3aVpYaHdJam94TnpjME9ERXlORGM0ZlEuZWxYbC05V0ZSNXJ1Z1VFX0xmOUV0djNiQkl0UXRwclZaTFF6N1kyRk5kSSI7czo4OiJhcGlfdXNlciI7YTo0OntzOjQ6Im5hbWUiO3M6MTI6IkR1bGNlIE1hcmllbCI7czo1OiJlbWFpbCI7czoxOToiZHVsY2VtYXJAdXBxLmVkdS5teCI7czo0OiJyb2xlIjtzOjQ6InVzZXIiO3M6MjoiaWQiO2k6MTY7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE2O3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2NDoiMjZlYTQ3ZTQ4YmUzOWZkNGI0YWVhOWUzOTRhNTZmZTllZDNkNWY4ZGYyMzlhMWUwMWJmOTc5NzBhNjBhZTI3OCI7fQ==',1774808994),('KOxZ817HaYIvxsavVMc2udFM62eXpy6Dk4ykVQ1R',NULL,'151.101.128.223','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYm96OFZNT0N5cm9SNERkVjhmSFNvWnVvRXM5MGVVcGNoRWdxbTBVRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1774857913),('y9k0rJMd8f4ADxTwCL3QTrWdE7nv3MsG6sy23pYy',3,'151.101.128.223','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiUTFaWmRSUlZ5WWc5aFplaWd2VDV3OWhwcnhZdXRrejgzVjZFd1Q1bCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wbGFjZXMvMjEiO3M6NToicm91dGUiO3M6MTE6InBsYWNlcy5zaG93Ijt9czoxMjoiYWNjZXNzX3Rva2VuIjtzOjE4MDoiZXlKaGJHY2lPaUpJVXpJMU5pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SnpkV0lpT2lJeklpd2laVzFoYVd3aU9pSXhNakl3TkRFMk5qVkFkWEJ4TG1Wa2RTNXRlQ0lzSW5KdmJHVWlPaUpoWkcxcGJpSXNJbVY0Y0NJNk1UYzNORGd5TVRJMk1IMC4tamlsY3lLZ0tuRmJwSUQxUzhaM0hBYmhyUkN4R2hKYmtfYVJhUC03SGdBIjtzOjg6ImFwaV91c2VyIjthOjQ6e3M6NDoibmFtZSI7czoxNToiUmFmYWVsIFJlc2VuZGl6IjtzOjU6ImVtYWlsIjtzOjIwOiIxMjIwNDE2NjVAdXBxLmVkdS5teCI7czo0OiJyb2xlIjtzOjU6ImFkbWluIjtzOjI6ImlkIjtpOjM7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czoyMToicGFzc3dvcmRfaGFzaF9zYW5jdHVtIjtzOjY0OiI4MWU2N2IwNzg5Njg5OGJiZGQzNzg2YjgyZmVmMTc5NTdlMDIzZTYwNTY4ZTUyMjFhYTVkOGU0MGRjN2Q0Y2IyIjt9',1774817674);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Andres','castillodelira@upq.edu.mx',NULL,'$2y$12$EVrC5RZbVukzdssjX8/Caekbq.GsXgTBoGq/uI77IHgY9TreAuKQO',NULL,NULL,NULL,'2025-11-23 13:40:59','2025-11-23 13:40:59',NULL,NULL,NULL,'moderator'),(3,'Rafael Resendiz','122041665@upq.edu.mx',NULL,'$2y$12$Xq687Ulu.rzeL9DeinBKJu9kivV05pYd3Ih.IRvxyvyzxi8TX3X5e',NULL,NULL,'profile-photos/xEvHXjV9ysJoYA6vucqqUIzs16x4yvLEDKs2DiUV.jpg','2025-11-23 15:20:05','2026-03-31 02:41:48',NULL,NULL,NULL,'admin'),(10,'Aidee','andrea.martinez@upq.edu.mx',NULL,'$2y$12$R0vzO36P/Etqo2Io2fZrzuUgxs.LpDmQWhAVzX4hE8zLkSg88vbW2',NULL,NULL,NULL,'2026-03-26 06:37:58','2026-03-26 06:39:11',NULL,NULL,NULL,'user'),(11,'Rafael','rafael@correo.com',NULL,'8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'moderator'),(12,'Rafael','rafael1@correo.com',NULL,'8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'user'),(13,'Leonardo Estrada','estradaleonardo@upq.com',NULL,'$2y$12$6xgwQphaq96yVd6j.CUatebyX7c8TnVfrKjUf7kA.l7srpjqhEBp2',NULL,NULL,NULL,'2026-03-29 12:08:56','2026-03-29 12:08:56',NULL,NULL,NULL,'user'),(14,'Daniel Mendoza','danielmendoza@upq.edu.mx',NULL,'$2y$12$9r97masEIQJIpOhMYSEa5O1kJWL47MdFVrDkiWAHgEoWXZibJ6b9S',NULL,NULL,NULL,'2026-03-29 12:20:47','2026-03-29 12:20:47',NULL,NULL,NULL,'user'),(15,'Ricardo Resendiz','resendizvazquezrafael@gamil.com',NULL,'$2y$12$D4ZM9vTLt1lrbZCGKTP/KOIr1Iw8Nxa5.xyTSZeJErAKo/H4bPDba',NULL,NULL,NULL,'2026-03-29 12:23:08','2026-03-29 12:23:08',NULL,NULL,NULL,'user'),(16,'Dulce Mariel Montes','dulcemariel@upq.edu.mx',NULL,'773a677ada886e0d92fcf5460eef892dbdedcfb451c8b25e313a45016e3ae6c0',NULL,NULL,NULL,'2026-03-29 12:42:13','2026-03-30 01:29:36',NULL,NULL,NULL,'user');
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

-- Dump completed on 2026-03-30 15:46:18
