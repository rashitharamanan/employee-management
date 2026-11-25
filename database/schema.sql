-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: employee_qr
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `employee`;
USE `employee`;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$10$HhnorJegtgpGZHyly.WSiuw7aXB/8HI7mSL5GmsrypFl.4Ia3Ltfq','System Administrator','admin@example.com','2026-08-11 08:49:15','2026-08-16 06:02:28');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_corrections`
--

DROP TABLE IF EXISTS `attendance_corrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_corrections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `requested_check_in` time DEFAULT NULL,
  `requested_check_out` time DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_corrections`
--

LOCK TABLES `attendance_corrections` WRITE;
/*!40000 ALTER TABLE `attendance_corrections` DISABLE KEYS */;
INSERT INTO `attendance_corrections` VALUES (1,1,'2026-09-14','09:00:00','18:00:00','Forgot to punch QR on arrival','PENDING','2026-09-11 09:09:42');
/*!40000 ALTER TABLE `attendance_corrections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PRESENT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `location` varchar(100) DEFAULT 'Office',
  `remarks` varchar(255) DEFAULT NULL,
  `total_hours` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_employee_date` (`employee_id`,`attendance_date`),
  KEY `idx_attendance_date` (`attendance_date`),
  KEY `idx_attendance_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
INSERT INTO `attendance_records` VALUES (1,1,'2026-09-01','2026-09-01 09:10:00','2026-09-01 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(2,1,'2026-09-02','2026-09-02 09:10:00','2026-09-02 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(3,1,'2026-09-03','2026-09-03 09:10:00','2026-09-03 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(4,1,'2026-09-04','2026-09-04 09:10:00','2026-09-04 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(5,1,'2026-09-05','2026-09-05 09:00:00','2026-09-05 18:00:00','WFH','2026-09-11 14:38:37','Work From Home','-','9h 00m'),(6,1,'2026-09-07','2026-09-07 09:10:00','2026-09-07 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(7,1,'2026-09-08','2026-09-08 09:10:00','2026-09-08 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(8,1,'2026-09-09','2026-09-09 09:10:00','2026-09-09 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(9,1,'2026-09-10','2026-09-10 09:00:00','2026-09-10 17:30:00','HALF_DAY','2026-09-11 14:38:37','Office','Left early','8h 30m'),(10,1,'2026-09-11','2026-09-11 09:08:00','2026-09-11 11:09:42','WFH','2026-09-11 14:38:37','Work From Home','Left at end of day','2h 1m'),(11,1,'2026-09-12','2026-09-12 09:10:00','2026-09-12 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(12,1,'2026-09-14','2026-09-14 09:45:00','2026-09-14 18:00:00','LATE','2026-09-11 14:38:37','Office','Traffic delay','8h 15m'),(13,1,'2026-09-15','2026-09-15 09:05:00','2026-09-15 18:10:00','PRESENT','2026-09-11 14:38:37','Office','-','9h 05m'),(14,1,'2026-09-16','2026-09-16 09:12:00',NULL,'PRESENT','2026-09-11 14:38:37','Office','-','--'),(15,1,'2026-09-17','2026-09-17 09:10:00','2026-09-17 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(16,1,'2026-09-18','2026-09-18 09:10:00','2026-09-18 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(17,1,'2026-09-19','2026-09-19 09:10:00','2026-09-19 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(18,1,'2026-09-21','2026-09-21 09:00:00','2026-09-21 18:00:00','WFH','2026-09-11 14:38:37','Work From Home','-','9h 00m'),(19,1,'2026-09-22','2026-09-22 09:10:00','2026-09-22 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(20,1,'2026-09-23','2026-09-23 09:10:00','2026-09-23 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(21,1,'2026-09-24','2026-09-24 09:35:00','2026-09-24 18:00:00','LATE','2026-09-11 14:38:37','Office','Traffic delay','8h 25m'),(22,1,'2026-09-25',NULL,NULL,'ABSENT','2026-09-11 14:38:37','-','-','--'),(23,1,'2026-09-26','2026-09-26 09:10:00','2026-09-26 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(24,1,'2026-09-28','2026-09-28 09:10:00','2026-09-28 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(25,1,'2026-09-29','2026-09-29 09:10:00','2026-09-29 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(26,1,'2026-09-30','2026-09-30 09:10:00','2026-09-30 18:05:00','PRESENT','2026-09-11 14:38:37','Office','-','8h 55m'),(27,15,'2026-09-01','2026-09-01 09:10:00','2026-09-01 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(28,15,'2026-09-02','2026-09-02 09:10:00','2026-09-02 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(29,15,'2026-09-03','2026-09-03 09:10:00','2026-09-03 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(30,15,'2026-09-04','2026-09-04 09:10:00','2026-09-04 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(31,15,'2026-09-05','2026-09-05 09:00:00','2026-09-05 18:00:00','WFH','2026-11-11 21:19:55','Work From Home','-','9h 00m'),(32,15,'2026-09-07','2026-09-07 09:10:00','2026-09-07 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(33,15,'2026-09-08','2026-09-08 09:10:00','2026-09-08 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(34,15,'2026-09-09','2026-09-09 09:10:00','2026-09-09 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(35,15,'2026-09-10','2026-09-10 09:00:00','2026-09-10 17:30:00','HALF_DAY','2026-11-11 21:19:55','Office','Left early','8h 30m'),(36,15,'2026-09-11','2026-09-11 09:08:00','2026-09-11 18:00:00','WFH','2026-11-11 21:19:55','Work From Home','-','8h 52m'),(37,15,'2026-09-12','2026-09-12 09:10:00','2026-09-12 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(38,15,'2026-09-14','2026-09-14 09:45:00','2026-09-14 18:00:00','LATE','2026-11-11 21:19:55','Office','Traffic delay','8h 15m'),(39,15,'2026-09-15','2026-09-15 09:05:00','2026-09-15 18:10:00','PRESENT','2026-11-11 21:19:55','Office','-','9h 05m'),(40,15,'2026-09-16','2026-09-16 09:12:00',NULL,'PRESENT','2026-11-11 21:19:55','Office','-','--'),(41,15,'2026-09-17','2026-09-17 09:10:00','2026-09-17 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(42,15,'2026-09-18','2026-09-18 09:10:00','2026-09-18 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(43,15,'2026-09-19','2026-09-19 09:10:00','2026-09-19 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(44,15,'2026-09-21','2026-09-21 09:00:00','2026-09-21 18:00:00','WFH','2026-11-11 21:19:55','Work From Home','-','9h 00m'),(45,15,'2026-09-22','2026-09-22 09:10:00','2026-09-22 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(46,15,'2026-09-23','2026-09-23 09:10:00','2026-09-23 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(47,15,'2026-09-24','2026-09-24 09:35:00','2026-09-24 18:00:00','LATE','2026-11-11 21:19:55','Office','Traffic delay','8h 25m'),(48,15,'2026-09-25',NULL,NULL,'ABSENT','2026-11-11 21:19:55','-','-','--'),(49,15,'2026-09-26','2026-09-26 09:10:00','2026-09-26 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(50,15,'2026-09-28','2026-09-28 09:10:00','2026-09-28 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(51,15,'2026-09-29','2026-09-29 09:10:00','2026-09-29 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m'),(52,15,'2026-09-30','2026-09-30 09:10:00','2026-09-30 18:05:00','PRESENT','2026-11-11 21:19:55','Office','-','8h 55m');
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(150) NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'TestBranch634','City','123','active','2026-09-17 14:21:07','2026-09-17 14:21:07');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'India',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'My Company','DEFAULT',NULL,NULL,NULL,NULL,NULL,'India','ACTIVE','2026-08-23 14:25:27','2026-08-23 14:25:27');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_departments_company_id` (`company_id`),
  CONSTRAINT `fk_departments_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,1,'IT','ACTIVE','2026-08-25 11:58:29','2026-08-25 11:58:29'),(2,1,'Finance','ACTIVE','2026-08-25 11:58:29','2026-08-25 11:58:29'),(3,1,'HR','ACTIVE','2026-08-25 11:58:29','2026-08-25 11:58:29'),(4,1,'Sales','ACTIVE','2026-08-25 11:58:29','2026-08-25 11:58:29'),(5,1,'Marketing','ACTIVE','2026-08-25 11:58:29','2026-08-25 11:58:29'),(7,1,'TestDept394','ACTIVE','2026-09-17 14:21:07','2026-09-17 14:21:07');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `designations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_id` (`department_id`,`name`),
  CONSTRAINT `fk_designations_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` VALUES (1,2,'Finance Manager','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(2,2,'Senior Accountant','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(3,2,'Accountant','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(4,2,'Finance Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(5,2,'Accounts Assistant','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(6,3,'HR Manager','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(7,3,'HR Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(8,3,'HR Coordinator','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(9,3,'Recruiter','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(10,3,'HR Assistant','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(11,4,'Sales Manager','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(12,4,'Sales Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(13,4,'Senior Sales Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(14,4,'Sales Representative','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(15,4,'Sales Coordinator','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(16,5,'Marketing Manager','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(17,5,'Marketing Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(18,5,'Digital Marketing Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(19,5,'Marketing Coordinator','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(20,5,'Content Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(21,1,'IT Manager','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(22,1,'Software Developer','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(23,1,'Senior Software Developer','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(24,1,'System Administrator','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(25,1,'Network Administrator','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(26,1,'IT Support Executive','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(27,1,'QA Engineer','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(28,1,'UI/UX Designer','ACTIVE','2026-08-25 11:58:56','2026-08-25 11:58:56'),(30,1,'TestDesig152','ACTIVE','2026-09-17 14:21:07','2026-09-17 14:21:07');
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_bank_details`
--

DROP TABLE IF EXISTS `employee_bank_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_bank_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `account_holder_name` varchar(150) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(30) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_type` varchar(30) NOT NULL DEFAULT 'Savings',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `idx_bank_emp` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_bank_details`
--

LOCK TABLES `employee_bank_details` WRITE;
/*!40000 ALTER TABLE `employee_bank_details` DISABLE KEYS */;
INSERT INTO `employee_bank_details` VALUES (1,1,'Priya Nair','HDFC Bank','5010048291823','HDFC0001245','Indiranagar, Bengaluru','Savings','2026-09-11 16:10:44'),(2,15,'Priya Nair','HDFC Bank','5010048291823','HDFC0001245','Indiranagar, Bengaluru','Savings','2026-11-11 21:20:02');
/*!40000 ALTER TABLE `employee_bank_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_document_requests`
--

DROP TABLE IF EXISTS `employee_document_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_document_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `reason` text DEFAULT NULL,
  `urgency` enum('NORMAL','URGENT') NOT NULL DEFAULT 'NORMAL',
  `status` enum('PENDING','APPROVED','REJECTED','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_req_emp` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_document_requests`
--

LOCK TABLES `employee_document_requests` WRITE;
/*!40000 ALTER TABLE `employee_document_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_document_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_documents`
--

DROP TABLE IF EXISTS `employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(100) NOT NULL DEFAULT 'General',
  `uploaded_on` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` varchar(50) DEFAULT '1.2 MB',
  `status` enum('ACTIVE','ARCHIVED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_doc_emp` (`employee_id`),
  KEY `idx_doc_type` (`document_type`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_documents`
--

LOCK TABLES `employee_documents` WRITE;
/*!40000 ALTER TABLE `employee_documents` DISABLE KEYS */;
INSERT INTO `employee_documents` VALUES (1,1,'Offer Letter','Employment','2024-01-12','storage/uploads/documents/offer_letter.pdf','2.4 MB','ACTIVE','2026-09-11 16:10:44'),(2,1,'ID Proof (Aadhar)','Identity','2024-01-14','storage/uploads/documents/aadhar_card.pdf','1.1 MB','ACTIVE','2026-09-11 16:10:44'),(3,1,'PAN Card','Identity','2024-01-14','storage/uploads/documents/pan_card.pdf','980 KB','ACTIVE','2026-09-11 16:10:44'),(4,1,'Educational Certificate','Education','2024-01-20','storage/uploads/documents/degree_cert.pdf','3.2 MB','ACTIVE','2026-09-11 16:10:44'),(5,1,'Experience Letter','Experience','2024-02-10','storage/uploads/documents/experience_letter.pdf','1.8 MB','ACTIVE','2026-09-11 16:10:44'),(6,1,'NDA & Confidentiality Agreement','Employment','2024-01-12','storage/uploads/documents/nda.pdf','1.5 MB','ACTIVE','2026-09-11 16:10:44'),(7,1,'Address Proof (Utility Bill)','Identity','2024-01-15','storage/uploads/documents/address_proof.pdf','850 KB','ACTIVE','2026-09-11 16:10:44'),(8,1,'10th Marksheet','Education','2024-01-18','storage/uploads/documents/10th_marks.pdf','1.4 MB','ACTIVE','2026-09-11 16:10:44'),(9,1,'12th Marksheet','Education','2024-01-18','storage/uploads/documents/12th_marks.pdf','1.4 MB','ACTIVE','2026-09-11 16:10:44'),(10,1,'Relieving Letter - Previous Org','Experience','2024-02-10','storage/uploads/documents/relieving.pdf','1.2 MB','ACTIVE','2026-09-11 16:10:44'),(11,1,'Bank Passbook Copy','Financial','2024-01-16','storage/uploads/documents/bank_passbook.pdf','900 KB','ACTIVE','2026-09-11 16:10:44'),(12,1,'Form 16 (FY 2024-25)','Financial','2025-05-20','storage/uploads/documents/form_16.pdf','2.1 MB','ACTIVE','2026-09-11 16:10:44'),(13,15,'Offer Letter','Employment','2024-01-12','storage/uploads/documents/offer_letter.pdf','2.4 MB','ACTIVE','2026-11-11 21:20:02'),(14,15,'ID Proof (Aadhar)','Identity','2024-01-14','storage/uploads/documents/aadhar_card.pdf','1.1 MB','ACTIVE','2026-11-11 21:20:02'),(15,15,'PAN Card','Identity','2024-01-14','storage/uploads/documents/pan_card.pdf','980 KB','ACTIVE','2026-11-11 21:20:02'),(16,15,'Educational Certificate','Education','2024-01-20','storage/uploads/documents/degree_cert.pdf','3.2 MB','ACTIVE','2026-11-11 21:20:02'),(17,15,'Experience Letter','Experience','2024-02-10','storage/uploads/documents/experience_letter.pdf','1.8 MB','ACTIVE','2026-11-11 21:20:02'),(18,15,'NDA & Confidentiality Agreement','Employment','2024-01-12','storage/uploads/documents/nda.pdf','1.5 MB','ACTIVE','2026-11-11 21:20:02'),(19,15,'Address Proof (Utility Bill)','Identity','2024-01-15','storage/uploads/documents/address_proof.pdf','850 KB','ACTIVE','2026-11-11 21:20:02'),(20,15,'10th Marksheet','Education','2024-01-18','storage/uploads/documents/10th_marks.pdf','1.4 MB','ACTIVE','2026-11-11 21:20:02'),(21,15,'12th Marksheet','Education','2024-01-18','storage/uploads/documents/12th_marks.pdf','1.4 MB','ACTIVE','2026-11-11 21:20:02'),(22,15,'Relieving Letter - Previous Org','Experience','2024-02-10','storage/uploads/documents/relieving.pdf','1.2 MB','ACTIVE','2026-11-11 21:20:02'),(23,15,'Bank Passbook Copy','Financial','2024-01-16','storage/uploads/documents/bank_passbook.pdf','900 KB','ACTIVE','2026-11-11 21:20:02'),(24,15,'Form 16 (FY 2024-25)','Financial','2025-05-20','storage/uploads/documents/form_16.pdf','2.1 MB','ACTIVE','2026-11-11 21:20:02');
/*!40000 ALTER TABLE `employee_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `personal_email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 35000.00,
  `reporting_manager` int(11) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE','SUSPENDED') NOT NULL DEFAULT 'ACTIVE',
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relationship` varchar(50) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience_years` decimal(4,1) DEFAULT NULL,
  `qr_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'EMPB3B7D665','John Doe',NULL,NULL,NULL,'john@example.com',NULL,'9876543210','CSE','Software Developer',NULL,NULL,35000.00,NULL,'ACTIVE','Puducherry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 07:23:29','2026-08-16 07:35:08'),(2,'EMPAD5D72FC','Jane Smith',NULL,NULL,NULL,'jane@example.com',NULL,'9876543212','HR','HR Executive',NULL,NULL,35000.00,NULL,'ACTIVE','Puducherry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 07:46:46','2026-08-16 07:46:46'),(4,'EMP3B00FA6F','Jane Smith',NULL,NULL,NULL,'janeee@example.com',NULL,'9876543212','IT','HR Executive',NULL,NULL,35000.00,NULL,'ACTIVE','Puducherry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 08:10:40','2026-08-16 08:10:40'),(6,'EMP69EFA154','Jane Smith',NULL,NULL,NULL,'rashi@example.com',NULL,'9876543212','IT','HR Executive',NULL,NULL,35000.00,NULL,'ACTIVE','Puducherry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 08:23:14','2026-08-16 08:23:14'),(8,'EMPA7CEE93E','Jane Smith',NULL,NULL,NULL,'karthi@example.com',NULL,'9876543212','IT','HR Executive',NULL,NULL,35000.00,NULL,'ACTIVE','Puducherry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'storage/qr/EMPA7CEE93E.png','2026-08-16 08:24:46','2026-08-16 08:24:46'),(11,'EMP4268ACCF','vasu',NULL,NULL,NULL,'vasu@gmail.com',NULL,'9843099850','it','hr',NULL,NULL,35000.00,NULL,'ACTIVE','no 1 krishnasamy nagar, seliamedu bahour',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'storage/qr/EMP4268ACCF.png','2026-08-20 11:19:36','2026-08-20 11:19:57'),(13,'EMPC08BF93F','Test Employee','2000-05-15','MALE',NULL,'test@company.com','test@gmail.com','9876543210','IT','Software Developer','2026-08-25','FULL_TIME',35000.00,NULL,'ACTIVE','Test Address','Puducherry','Puducherry','605001','Test Contact','9876543211','Parent','B.Tech',2.0,'storage/qr/EMPC08BF93F.png','2026-08-25 12:41:41','2026-08-25 12:41:43'),(14,'EMP322E8B32','Test Employee','2000-01-01','FEMALE',NULL,'employee@gmail.com',NULL,'9876543210','IT','Software Developer','2026-08-26','FULL_TIME',35000.00,NULL,'ACTIVE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'storage/qr/EMP322E8B32.png','2026-08-26 07:46:18','2026-08-26 07:46:19'),(15,'EMP009','savitha','1999-12-04',NULL,NULL,'ssavitha@gmail.com','ssavitha@gmail.com','86104887360','IT','IT Support Executive','2026-09-03','Full Time',35000.00,NULL,'ACTIVE','no 1 krishnasamy nagar, seliamedu bahour','Puducherry','Puducherry','607402','Rashitha Rashitha','8838876077','parent','btech',3.0,'storage/qr/EMP009.png','2026-09-04 11:57:44','2026-09-04 11:57:44'),(16,'USR007','yohan','2026-10-09','MALE',NULL,'shabanasardhar6@gmail.com','shabanasardhar6@gmail.com','8838876077','Finance','Accountant','2026-10-10','Part Time',5000.00,NULL,'ACTIVE','no 1 krishnasamy nagar, seliamedu bahour','Puducherry','Puducherry','607402','Rashitha Ramana','9843099850','sisster','b com',1.0,'storage/qr/USR007.png','2026-10-08 12:17:46','2026-10-08 12:17:48');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exit_records`
--

DROP TABLE IF EXISTS `exit_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exit_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `last_working_date` date NOT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `current_stage` varchar(100) DEFAULT NULL,
  `progress` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_exit_employee` (`employee_id`),
  KEY `idx_exit_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exit_records`
--

LOCK TABLES `exit_records` WRITE;
/*!40000 ALTER TABLE `exit_records` DISABLE KEYS */;
INSERT INTO `exit_records` VALUES (1,16,'2026-10-26','COMPLETED','IT Asset Handover Pending',60,'2026-10-11 09:51:17','2026-10-11 09:51:27'),(2,15,'2026-10-16','IN_PROGRESS','Finance Dues & Accounts',40,'2026-10-11 09:51:17','2026-10-11 09:51:17');
/*!40000 ALTER TABLE `exit_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generated_reports`
--

DROP TABLE IF EXISTS `generated_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generated_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_name` varchar(160) NOT NULL,
  `category` varchar(60) NOT NULL DEFAULT 'General',
  `generated_by` varchar(120) NOT NULL DEFAULT 'System',
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `report_format` enum('PDF','EXCEL','CSV') NOT NULL DEFAULT 'PDF',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reports_generated_at` (`generated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_reports`
--

LOCK TABLES `generated_reports` WRITE;
/*!40000 ALTER TABLE `generated_reports` DISABLE KEYS */;
INSERT INTO `generated_reports` VALUES (1,'Employee Analytics Report','Attendance','admin@example.com','2026-08-28 13:12:26','PDF','2026-08-22','2026-08-28','2026-08-28 13:12:26'),(10,'Employee Master (2025-01-01 → 2026-09-06)','Employment','vasu@gmail.com','2026-09-06 21:25:30','CSV','2025-01-01','2026-09-06','2026-09-06 21:25:30'),(12,'Attendance Log (2026-08-07 → 2026-09-06)','Attendance','vasundhara@gmail.com','2026-09-06 21:43:24','CSV','2026-08-07','2026-09-06','2026-09-06 21:43:24'),(13,'HR Monthly Pack (verify)','General','cli-verify','2026-09-08 19:08:41','CSV','2026-09-01','2026-09-06','2026-09-08 19:08:41'),(14,'Employee Master (2025-01-01 → 2026-09-06)','Employment','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(15,'Attendance Log (2025-01-01 → 2026-09-06)','Attendance','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(16,'Leave Records (2025-01-01 → 2026-09-06)','Leave','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(17,'Payroll Register (2025-01-01 → 2026-09-06)','Payroll','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(18,'Training Enrollments (2025-01-01 → 2026-09-06)','Training','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(19,'Performance Reviews (2025-01-01 → 2026-09-06)','Performance','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(20,'Recruitment Pipeline (2025-01-01 → 2026-09-06)','Recruitment','cli-verify','2026-09-08 19:08:41','CSV','2025-01-01','2026-09-06','2026-09-08 19:08:41'),(21,'Attendance Analytics Report (PDF)','Attendance','admin@example.com','2026-09-19 14:35:37','PDF','2026-08-20','2026-09-19','2026-09-19 14:35:37'),(22,'Employee Master (2026-09-11 → 2026-10-11)','Employment','vasundhara@gmail.com','2026-10-11 11:01:49','CSV','2026-09-11','2026-10-11','2026-10-11 11:01:49');
/*!40000 ALTER TABLE `generated_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `holiday_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
INSERT INTO `holidays` VALUES (1,'Gandhi Jayanti','2026-10-02','National holiday','2026-09-11 08:33:22'),(2,'Diwali','2026-11-12','Festival of Lights','2026-09-11 08:33:22'),(3,'Christmas','2026-12-25','Christmas celebration','2026-09-11 08:33:22'),(4,'New Year Day','2027-01-01','New Year 2027','2026-09-11 08:33:22'),(5,'Republic Day','2027-01-26','Republic Day of India','2026-09-11 08:33:22');
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_document_templates`
--

DROP TABLE IF EXISTS `hr_document_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_document_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `category` enum('ONBOARDING','EXIT','GENERAL') NOT NULL DEFAULT 'GENERAL',
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_document_templates`
--

LOCK TABLES `hr_document_templates` WRITE;
/*!40000 ALTER TABLE `hr_document_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_document_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_workflow_tasks`
--

DROP TABLE IF EXISTS `hr_workflow_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_workflow_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `workflow_type` enum('ONBOARDING','EXIT') NOT NULL,
  `record_id` int(10) unsigned NOT NULL,
  `task_name` varchar(200) NOT NULL,
  `assigned_to` varchar(120) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_workflow_tasks` (`workflow_type`,`record_id`),
  KEY `idx_workflow_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_workflow_tasks`
--

LOCK TABLES `hr_workflow_tasks` WRITE;
/*!40000 ALTER TABLE `hr_workflow_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_workflow_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_openings`
--

DROP TABLE IF EXISTS `job_openings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_openings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `employment_type` varchar(50) NOT NULL DEFAULT 'Full-time',
  `vacancies` int(11) NOT NULL DEFAULT 1,
  `applicants_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('OPEN','CLOSED','DRAFT') NOT NULL DEFAULT 'OPEN',
  `hiring_manager` varchar(120) DEFAULT NULL,
  `posted_at` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_job_status` (`status`),
  KEY `idx_job_department` (`department`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_openings`
--

LOCK TABLES `job_openings` WRITE;
/*!40000 ALTER TABLE `job_openings` DISABLE KEYS */;
INSERT INTO `job_openings` VALUES (1,'Senior UI/UX Designer','Design','Chennai','Full-time',2,28,'OPEN','Jessica Morgan','2026-09-05','2026-09-05 15:15:36'),(2,'UX/UI Designer','Design','Bangalore','Full-time',2,15,'OPEN','Olivia Johnson','2026-09-05','2026-09-05 15:15:36'),(3,'Product Manager','Product','Hyderabad','Full-time',1,25,'OPEN','Michael Chen','2026-09-05','2026-09-05 15:15:36'),(4,'HR Executive','HR','Chennai','Full-time',2,18,'OPEN','Linda Carter','2026-09-05','2026-09-05 15:15:36'),(5,'QA Analyst','Engineering','Mumbai','Full-time',2,20,'OPEN','David Lee','2026-09-05','2026-09-05 15:15:36'),(6,'Marketing Coordinator','Marketing','Chennai','Full-time',1,12,'CLOSED','Olivia Taylor','2026-09-05','2026-09-05 15:15:36');
/*!40000 ALTER TABLE `job_openings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_balances`
--

DROP TABLE IF EXISTS `leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `allocated_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `used_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `remaining_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_leave_balance_employee_year_type` (`employee_id`,`year`,`leave_type_id`),
  KEY `idx_leave_balance_employee` (`employee_id`),
  KEY `idx_leave_balance_type` (`leave_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balances`
--

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
INSERT INTO `leave_balances` VALUES (1,1,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(2,1,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(3,1,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(4,1,5,2026,15.00,4.00,11.00,'2026-09-05 13:44:34','2026-10-03 19:50:56'),(5,1,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(6,2,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(7,2,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(8,2,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(9,2,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(10,2,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(11,4,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(12,4,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(13,4,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(14,4,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(15,4,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(16,6,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(17,6,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(18,6,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(19,6,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(20,6,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(21,8,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(22,8,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(23,8,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(24,8,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(25,8,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(26,11,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(27,11,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(28,11,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(29,11,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(30,11,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(31,13,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(32,13,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(33,13,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(34,13,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(35,13,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(36,14,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(37,14,4,2026,15.00,2.00,13.00,'2026-09-05 13:44:34','2026-09-18 19:38:10'),(38,14,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(39,14,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(40,14,3,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(41,15,2,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(42,15,4,2026,15.00,0.00,15.00,'2026-09-05 13:44:34','2026-09-05 13:44:34'),(43,15,6,2026,15.00,0.00,15.00,'2026-09-05 13:44:35','2026-09-05 13:44:35'),(44,15,5,2026,15.00,0.00,15.00,'2026-09-05 13:44:35','2026-09-05 13:44:35'),(45,15,3,2026,15.00,5.00,10.00,'2026-09-05 13:44:35','2026-10-03 19:51:03');
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `leave_type_id` int(10) unsigned NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_leave_request_employee` (`employee_id`),
  KEY `idx_leave_request_status` (`status`),
  KEY `idx_leave_request_dates` (`from_date`,`to_date`),
  KEY `idx_leave_request_type` (`leave_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES (1,1,2,'2026-09-01','2026-09-01',1.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-08-29 00:00:00','2026-09-05 13:44:35'),(2,2,4,'2026-09-02','2026-09-03',2.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-08-31 00:00:00',NULL,'2026-08-30 00:00:00','2026-09-05 13:44:35'),(3,4,6,'2026-09-03','2026-09-05',3.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-01 00:00:00',NULL,'2026-08-31 00:00:00','2026-09-05 13:44:35'),(4,6,5,'2026-09-04','2026-09-07',4.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-02 00:00:00',NULL,'2026-09-01 00:00:00','2026-09-05 13:44:35'),(5,8,3,'2026-09-05','2026-09-09',5.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-03 00:00:00',NULL,'2026-09-02 00:00:00','2026-09-05 13:44:35'),(6,11,2,'2026-09-06','2026-09-06',1.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-04 00:00:00',NULL,'2026-09-03 00:00:00','2026-09-05 13:44:35'),(7,13,4,'2026-09-07','2026-09-08',2.00,'Annual vacation trip with family',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-04 00:00:00','2026-09-05 13:44:35'),(8,14,6,'2026-09-08','2026-09-10',3.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-06 00:00:00',NULL,'2026-09-05 00:00:00','2026-09-05 13:44:35'),(9,15,5,'2026-09-09','2026-09-12',4.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-07 00:00:00',NULL,'2026-09-06 00:00:00','2026-09-05 13:44:35'),(10,1,3,'2026-09-10','2026-09-14',5.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-08 00:00:00',NULL,'2026-09-07 00:00:00','2026-09-05 13:44:35'),(11,2,2,'2026-09-11','2026-09-11',1.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-09 00:00:00',NULL,'2026-09-08 00:00:00','2026-09-05 13:44:35'),(12,4,4,'2026-09-12','2026-09-13',2.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-10 00:00:00',NULL,'2026-09-09 00:00:00','2026-09-05 13:44:35'),(13,6,6,'2026-09-13','2026-09-15',3.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-10 00:00:00','2026-09-05 13:44:35'),(14,8,5,'2026-09-14','2026-09-17',4.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-12 00:00:00',NULL,'2026-09-11 00:00:00','2026-09-05 13:44:35'),(15,11,3,'2026-09-15','2026-09-19',5.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-09-12 00:00:00','2026-09-05 13:44:35'),(16,13,2,'2026-09-16','2026-09-16',1.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-14 00:00:00',NULL,'2026-09-13 00:00:00','2026-09-05 13:44:35'),(17,14,4,'2026-09-17','2026-09-18',2.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-15 00:00:00',NULL,'2026-09-14 00:00:00','2026-09-05 13:44:35'),(18,15,6,'2026-09-18','2026-09-20',3.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-16 00:00:00',NULL,'2026-09-15 00:00:00','2026-09-05 13:44:35'),(19,1,5,'2026-09-19','2026-09-22',4.00,'House shifting and relocation tasks',NULL,'APPROVED',3,'2026-10-03 19:50:56',NULL,'2026-09-16 00:00:00','2026-10-03 19:50:56'),(20,2,3,'2026-09-20','2026-09-24',5.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-17 00:00:00','2026-09-05 13:44:35'),(21,4,2,'2026-09-21','2026-09-21',1.00,'Annual vacation trip with family',NULL,'PENDING',NULL,NULL,NULL,'2026-09-18 00:00:00','2026-09-05 13:44:35'),(22,6,4,'2026-09-22','2026-09-23',2.00,'Family function and personal travel',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-19 00:00:00','2026-09-05 13:44:35'),(23,8,6,'2026-09-23','2026-09-25',3.00,'Viral fever and doctor recommended rest',NULL,'PENDING',NULL,NULL,NULL,'2026-09-20 00:00:00','2026-09-05 13:44:35'),(24,11,5,'2026-09-24','2026-09-27',4.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-22 00:00:00',NULL,'2026-09-21 00:00:00','2026-09-05 13:44:35'),(25,13,3,'2026-09-25','2026-09-29',5.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-23 00:00:00',NULL,'2026-09-22 00:00:00','2026-09-05 13:44:35'),(26,14,2,'2026-09-26','2026-09-26',1.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-24 00:00:00',NULL,'2026-09-23 00:00:00','2026-09-05 13:44:35'),(27,15,4,'2026-09-27','2026-09-28',2.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-25 00:00:00',NULL,'2026-09-24 00:00:00','2026-09-05 13:44:35'),(28,1,6,'2026-09-28','2026-09-30',3.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-26 00:00:00',NULL,'2026-09-25 00:00:00','2026-09-05 13:44:35'),(29,2,5,'2026-09-01','2026-09-04',4.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-08-30 00:00:00',NULL,'2026-08-29 00:00:00','2026-09-05 13:44:35'),(30,4,3,'2026-09-02','2026-09-06',5.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-08-31 00:00:00',NULL,'2026-08-30 00:00:00','2026-09-05 13:44:35'),(31,6,2,'2026-09-03','2026-09-03',1.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-01 00:00:00',NULL,'2026-08-31 00:00:00','2026-09-05 13:44:35'),(32,8,4,'2026-09-04','2026-09-05',2.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-02 00:00:00',NULL,'2026-09-01 00:00:00','2026-09-05 13:44:35'),(33,11,6,'2026-09-05','2026-09-07',3.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-03 00:00:00',NULL,'2026-09-02 00:00:00','2026-09-05 13:44:35'),(34,13,5,'2026-09-06','2026-09-09',4.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-03 00:00:00','2026-09-05 13:44:35'),(35,14,3,'2026-09-07','2026-09-11',5.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-05 00:00:00',NULL,'2026-09-04 00:00:00','2026-09-05 13:44:35'),(36,15,2,'2026-09-08','2026-09-08',1.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-06 00:00:00',NULL,'2026-09-05 00:00:00','2026-09-05 13:44:35'),(37,1,4,'2026-09-09','2026-09-10',2.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-07 00:00:00',NULL,'2026-09-06 00:00:00','2026-09-05 13:44:35'),(38,2,6,'2026-09-10','2026-09-12',3.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-08 00:00:00',NULL,'2026-09-07 00:00:00','2026-09-05 13:44:35'),(39,4,5,'2026-09-11','2026-09-14',4.00,'Medical checkup and recovery',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-08 00:00:00','2026-09-05 13:44:35'),(40,6,3,'2026-09-12','2026-09-16',5.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-10 00:00:00',NULL,'2026-09-09 00:00:00','2026-09-05 13:44:35'),(41,8,2,'2026-09-13','2026-09-13',1.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-10 00:00:00','2026-09-05 13:44:35'),(42,11,4,'2026-09-14','2026-09-15',2.00,'Annual vacation trip with family',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-11 00:00:00','2026-09-05 13:44:35'),(43,13,6,'2026-09-15','2026-09-17',3.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-13 00:00:00',NULL,'2026-09-12 00:00:00','2026-09-05 13:44:35'),(44,14,5,'2026-09-16','2026-09-19',4.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-14 00:00:00',NULL,'2026-09-13 00:00:00','2026-09-05 13:44:35'),(45,15,3,'2026-09-17','2026-09-21',5.00,'Attending cousin wedding out of station',NULL,'APPROVED',3,'2026-10-03 19:51:03',NULL,'2026-09-14 00:00:00','2026-10-03 19:51:03'),(46,1,2,'2026-09-18','2026-09-18',1.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-16 00:00:00',NULL,'2026-09-15 00:00:00','2026-09-05 13:44:35'),(47,2,4,'2026-09-19','2026-09-20',2.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-17 00:00:00',NULL,'2026-09-16 00:00:00','2026-09-05 13:44:35'),(48,4,6,'2026-09-20','2026-09-22',3.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-17 00:00:00','2026-09-05 13:44:35'),(49,6,5,'2026-09-21','2026-09-24',4.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-19 00:00:00',NULL,'2026-09-18 00:00:00','2026-09-05 13:44:35'),(50,8,3,'2026-09-22','2026-09-26',5.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-20 00:00:00',NULL,'2026-09-19 00:00:00','2026-09-05 13:44:35'),(51,11,2,'2026-09-23','2026-09-23',1.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-21 00:00:00',NULL,'2026-09-20 00:00:00','2026-09-05 13:44:35'),(52,13,4,'2026-09-24','2026-09-25',2.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-22 00:00:00',NULL,'2026-09-21 00:00:00','2026-09-05 13:44:35'),(53,14,6,'2026-09-25','2026-09-27',3.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-23 00:00:00',NULL,'2026-09-22 00:00:00','2026-09-05 13:44:35'),(54,15,5,'2026-09-26','2026-09-29',4.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-24 00:00:00',NULL,'2026-09-23 00:00:00','2026-09-05 13:44:35'),(55,1,3,'2026-09-27','2026-10-01',5.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-25 00:00:00',NULL,'2026-09-24 00:00:00','2026-09-05 13:44:35'),(56,2,2,'2026-09-28','2026-09-28',1.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-26 00:00:00',NULL,'2026-09-25 00:00:00','2026-09-05 13:44:35'),(57,4,4,'2026-09-01','2026-09-02',2.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-08-29 00:00:00','2026-09-05 13:44:35'),(58,6,6,'2026-09-02','2026-09-04',3.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-08-31 00:00:00',NULL,'2026-08-30 00:00:00','2026-09-05 13:44:35'),(59,8,5,'2026-09-03','2026-09-06',4.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-01 00:00:00',NULL,'2026-08-31 00:00:00','2026-09-05 13:44:35'),(60,11,3,'2026-09-04','2026-09-08',5.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-02 00:00:00',NULL,'2026-09-01 00:00:00','2026-09-05 13:44:35'),(61,13,2,'2026-09-05','2026-09-05',1.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-03 00:00:00',NULL,'2026-09-02 00:00:00','2026-09-05 13:44:35'),(62,14,4,'2026-09-06','2026-09-07',2.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-04 00:00:00',NULL,'2026-09-03 00:00:00','2026-09-05 13:44:35'),(63,15,6,'2026-09-07','2026-09-09',3.00,'Annual vacation trip with family',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-04 00:00:00','2026-09-05 13:44:35'),(64,1,5,'2026-09-08','2026-09-11',4.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-06 00:00:00',NULL,'2026-09-05 00:00:00','2026-09-05 13:44:35'),(65,2,3,'2026-09-09','2026-09-13',5.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-07 00:00:00',NULL,'2026-09-06 00:00:00','2026-09-05 13:44:35'),(66,4,2,'2026-09-10','2026-09-10',1.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-08 00:00:00',NULL,'2026-09-07 00:00:00','2026-09-05 13:44:35'),(67,6,4,'2026-09-11','2026-09-12',2.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-09 00:00:00',NULL,'2026-09-08 00:00:00','2026-09-05 13:44:35'),(68,8,6,'2026-09-12','2026-09-14',3.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-10 00:00:00',NULL,'2026-09-09 00:00:00','2026-09-05 13:44:35'),(69,11,5,'2026-09-13','2026-09-16',4.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-11 00:00:00',NULL,'2026-09-10 00:00:00','2026-09-05 13:44:35'),(70,13,3,'2026-09-14','2026-09-18',5.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-12 00:00:00',NULL,'2026-09-11 00:00:00','2026-09-05 13:44:35'),(71,14,2,'2026-09-15','2026-09-15',1.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-09-12 00:00:00','2026-09-05 13:44:35'),(72,15,4,'2026-09-16','2026-09-17',2.00,'Viral fever and doctor recommended rest',NULL,'PENDING',NULL,NULL,NULL,'2026-09-13 00:00:00','2026-09-05 13:44:35'),(73,1,6,'2026-09-17','2026-09-19',3.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-15 00:00:00',NULL,'2026-09-14 00:00:00','2026-09-05 13:44:35'),(74,2,5,'2026-09-18','2026-09-21',4.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-16 00:00:00',NULL,'2026-09-15 00:00:00','2026-09-05 13:44:35'),(75,4,3,'2026-09-19','2026-09-23',5.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-17 00:00:00',NULL,'2026-09-16 00:00:00','2026-09-05 13:44:35'),(76,6,2,'2026-09-20','2026-09-20',1.00,'Emergency personal matters at hometown',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-17 00:00:00','2026-09-05 13:44:35'),(77,8,4,'2026-09-21','2026-09-22',2.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-19 00:00:00',NULL,'2026-09-18 00:00:00','2026-09-05 13:44:35'),(78,11,6,'2026-09-22','2026-09-24',3.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-20 00:00:00',NULL,'2026-09-19 00:00:00','2026-09-05 13:44:35'),(79,13,5,'2026-09-23','2026-09-26',4.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-21 00:00:00',NULL,'2026-09-20 00:00:00','2026-09-05 13:44:35'),(80,14,3,'2026-09-24','2026-09-28',5.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-22 00:00:00',NULL,'2026-09-21 00:00:00','2026-09-05 13:44:35'),(81,15,2,'2026-09-25','2026-09-25',1.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-23 00:00:00',NULL,'2026-09-22 00:00:00','2026-09-05 13:44:35'),(82,1,4,'2026-09-26','2026-09-27',2.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-24 00:00:00',NULL,'2026-09-23 00:00:00','2026-09-05 13:44:35'),(83,2,6,'2026-09-27','2026-09-29',3.00,'Emergency personal matters at hometown',NULL,'REJECTED',1,'2026-09-18 13:03:29','so many work are ther','2026-09-24 00:00:00','2026-09-18 13:03:29'),(84,4,5,'2026-09-28','2026-10-01',4.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-26 00:00:00',NULL,'2026-09-25 00:00:00','2026-09-05 13:44:35'),(85,6,3,'2026-09-01','2026-09-05',5.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-08-29 00:00:00','2026-09-05 13:44:35'),(86,8,2,'2026-09-02','2026-09-02',1.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-08-31 00:00:00',NULL,'2026-08-30 00:00:00','2026-09-05 13:44:35'),(87,11,4,'2026-09-03','2026-09-04',2.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-01 00:00:00',NULL,'2026-08-31 00:00:00','2026-09-05 13:44:35'),(88,13,6,'2026-09-04','2026-09-06',3.00,'Medical checkup and recovery',NULL,'PENDING',NULL,NULL,NULL,'2026-09-01 00:00:00','2026-09-05 13:44:35'),(89,14,5,'2026-09-05','2026-09-08',4.00,'House shifting and relocation tasks',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-02 00:00:00','2026-09-05 13:44:35'),(90,15,3,'2026-09-06','2026-09-10',5.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-04 00:00:00',NULL,'2026-09-03 00:00:00','2026-09-05 13:44:35'),(91,1,2,'2026-09-07','2026-09-07',1.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-05 00:00:00',NULL,'2026-09-04 00:00:00','2026-09-05 13:44:35'),(92,2,4,'2026-09-08','2026-09-09',2.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-06 00:00:00',NULL,'2026-09-05 00:00:00','2026-09-05 13:44:35'),(93,4,6,'2026-09-09','2026-09-11',3.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-07 00:00:00',NULL,'2026-09-06 00:00:00','2026-09-05 13:44:35'),(94,6,5,'2026-09-10','2026-09-13',4.00,'Attending cousin wedding out of station',NULL,'PENDING',NULL,NULL,NULL,'2026-09-07 00:00:00','2026-09-05 13:44:35'),(95,8,3,'2026-09-11','2026-09-15',5.00,'Medical checkup and recovery',NULL,'PENDING',NULL,NULL,NULL,'2026-09-08 00:00:00','2026-09-05 13:44:35'),(96,11,2,'2026-09-12','2026-09-12',1.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-10 00:00:00',NULL,'2026-09-09 00:00:00','2026-09-05 13:44:35'),(97,13,4,'2026-09-13','2026-09-14',2.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-11 00:00:00',NULL,'2026-09-10 00:00:00','2026-09-05 13:44:35'),(98,14,6,'2026-09-14','2026-09-16',3.00,'Annual vacation trip with family',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-11 00:00:00','2026-09-05 13:44:35'),(99,15,5,'2026-09-15','2026-09-18',4.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-13 00:00:00',NULL,'2026-09-12 00:00:00','2026-09-05 13:44:35'),(100,1,3,'2026-09-16','2026-09-20',5.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-14 00:00:00',NULL,'2026-09-13 00:00:00','2026-09-05 13:44:35'),(101,2,2,'2026-09-17','2026-09-17',1.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-15 00:00:00',NULL,'2026-09-14 00:00:00','2026-09-05 13:44:35'),(102,4,4,'2026-09-18','2026-09-19',2.00,'Medical checkup and recovery',NULL,'PENDING',NULL,NULL,NULL,'2026-09-15 00:00:00','2026-09-05 13:44:35'),(103,6,6,'2026-09-19','2026-09-21',3.00,'House shifting and relocation tasks',NULL,'REJECTED',NULL,NULL,'Insufficient leave balance or team bandwidth constraint.','2026-09-16 00:00:00','2026-09-05 13:44:35'),(104,8,5,'2026-09-20','2026-09-23',4.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-18 00:00:00',NULL,'2026-09-17 00:00:00','2026-09-05 13:44:35'),(105,11,3,'2026-09-21','2026-09-25',5.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-19 00:00:00',NULL,'2026-09-18 00:00:00','2026-09-05 13:44:35'),(106,13,2,'2026-09-22','2026-09-22',1.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-20 00:00:00',NULL,'2026-09-19 00:00:00','2026-09-05 13:44:35'),(107,14,4,'2026-09-23','2026-09-24',2.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-18 19:38:10',NULL,'2026-09-20 00:00:00','2026-09-18 19:38:10'),(108,15,6,'2026-09-24','2026-09-26',3.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-22 00:00:00',NULL,'2026-09-21 00:00:00','2026-09-05 13:44:35'),(109,1,5,'2026-09-25','2026-09-28',4.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-23 00:00:00',NULL,'2026-09-22 00:00:00','2026-09-05 13:44:35'),(110,2,3,'2026-09-26','2026-09-30',5.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-24 00:00:00',NULL,'2026-09-23 00:00:00','2026-09-05 13:44:35'),(111,4,2,'2026-09-27','2026-09-27',1.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-25 00:00:00',NULL,'2026-09-24 00:00:00','2026-09-05 13:44:35'),(112,6,4,'2026-09-28','2026-09-29',2.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-26 00:00:00',NULL,'2026-09-25 00:00:00','2026-09-05 13:44:35'),(113,8,6,'2026-09-01','2026-09-03',3.00,'Family function and personal travel',NULL,'PENDING',NULL,NULL,NULL,'2026-08-29 00:00:00','2026-09-05 13:44:35'),(114,11,5,'2026-09-02','2026-09-05',4.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-08-31 00:00:00',NULL,'2026-08-30 00:00:00','2026-09-05 13:44:35'),(115,13,3,'2026-09-03','2026-09-07',5.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-01 00:00:00',NULL,'2026-08-31 00:00:00','2026-09-05 13:44:35'),(116,14,2,'2026-09-04','2026-09-04',1.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-02 00:00:00',NULL,'2026-09-01 00:00:00','2026-09-05 13:44:35'),(117,15,4,'2026-09-05','2026-09-06',2.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-03 00:00:00',NULL,'2026-09-02 00:00:00','2026-09-05 13:44:35'),(118,1,6,'2026-09-06','2026-09-08',3.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-04 00:00:00',NULL,'2026-09-03 00:00:00','2026-09-05 13:44:35'),(119,2,5,'2026-09-07','2026-09-10',4.00,'Annual vacation trip with family',NULL,'APPROVED',1,'2026-09-05 00:00:00',NULL,'2026-09-04 00:00:00','2026-09-05 13:44:35'),(120,4,3,'2026-09-08','2026-09-12',5.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-06 00:00:00',NULL,'2026-09-05 00:00:00','2026-09-05 13:44:35'),(121,6,2,'2026-09-09','2026-09-09',1.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-07 00:00:00',NULL,'2026-09-06 00:00:00','2026-09-05 13:44:35'),(122,8,4,'2026-09-10','2026-09-11',2.00,'Attending cousin wedding out of station',NULL,'APPROVED',1,'2026-09-08 00:00:00',NULL,'2026-09-07 00:00:00','2026-09-05 13:44:35'),(123,11,6,'2026-09-11','2026-09-13',3.00,'Medical checkup and recovery',NULL,'APPROVED',1,'2026-09-09 00:00:00',NULL,'2026-09-08 00:00:00','2026-09-05 13:44:35'),(124,13,5,'2026-09-12','2026-09-15',4.00,'House shifting and relocation tasks',NULL,'APPROVED',1,'2026-09-10 00:00:00',NULL,'2026-09-09 00:00:00','2026-09-05 13:44:35'),(125,14,3,'2026-09-13','2026-09-17',5.00,'Emergency personal matters at hometown',NULL,'APPROVED',1,'2026-09-11 00:00:00',NULL,'2026-09-10 00:00:00','2026-09-05 13:44:35'),(126,15,2,'2026-09-14','2026-09-14',1.00,'Annual vacation trip with family',NULL,'PENDING',NULL,NULL,NULL,'2026-09-11 00:00:00','2026-09-05 13:44:35'),(127,1,4,'2026-09-15','2026-09-16',2.00,'Family function and personal travel',NULL,'APPROVED',1,'2026-09-13 00:00:00',NULL,'2026-09-12 00:00:00','2026-09-05 13:44:35'),(128,2,6,'2026-09-16','2026-09-18',3.00,'Viral fever and doctor recommended rest',NULL,'APPROVED',1,'2026-09-14 00:00:00',NULL,'2026-09-13 00:00:00','2026-09-05 13:44:35');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organisation_id` int(10) unsigned NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `annual_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_leave_type_org_name` (`organisation_id`,`name`),
  KEY `idx_leave_type_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES (2,1,'Casual Leave','Casual leave for personal needs',12.00,1,'ACTIVE','2026-08-31 10:53:25','2026-08-31 10:53:25'),(3,1,'Sick Leave','Medical or health-related leave',10.00,1,'ACTIVE','2026-08-31 10:53:25','2026-08-31 10:53:25'),(4,1,'Earned Leave','Annual earned leave entitlement',15.00,1,'ACTIVE','2026-08-31 10:53:25','2026-08-31 10:53:25'),(5,1,'Permission / Half Day','Short leave or half-day permission',6.00,1,'ACTIVE','2026-08-31 10:53:25','2026-08-31 10:53:25'),(6,1,'Maternity Leave','Maternity-related leave entitlement',90.00,1,'ACTIVE','2026-08-31 10:53:25','2026-08-31 10:53:25');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_approvals`
--

DROP TABLE IF EXISTS `manager_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_approvals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `employee_name` varchar(150) NOT NULL,
  `request_type` varchar(100) NOT NULL,
  `category` enum('LEAVE','REMOTE','DOCUMENT','TRAINING','OTHER') NOT NULL DEFAULT 'LEAVE',
  `icon_type` varchar(50) NOT NULL DEFAULT 'leave',
  `request_date` date NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_appr_mgr` (`manager_id`),
  KEY `idx_appr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_approvals`
--

LOCK TABLES `manager_approvals` WRITE;
/*!40000 ALTER TABLE `manager_approvals` DISABLE KEYS */;
INSERT INTO `manager_approvals` VALUES (1,NULL,'Rohan Verma','Leave Request','LEAVE','leave','2025-08-26','APPROVED',NULL,'2026-09-11 17:24:29','2026-09-12 19:32:29'),(2,NULL,'Neha Tiwari','Remote Work','REMOTE','remote','2025-08-25','APPROVED',NULL,'2026-09-11 17:24:29','2026-09-22 12:27:21'),(3,NULL,'Arun Kumar','Document Update','DOCUMENT','document','2025-08-24','APPROVED',NULL,'2026-09-11 17:24:29','2026-10-11 11:02:26'),(4,NULL,'Sneha Iyer','Training Request','TRAINING','training','2025-08-22','PENDING',NULL,'2026-09-11 17:24:29','2026-09-11 17:24:29'),(5,NULL,'Karan Mehta','Leave Request','LEAVE','leave','2025-08-21','PENDING',NULL,'2026-09-11 17:24:29','2026-09-11 17:24:29');
/*!40000 ALTER TABLE `manager_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_attendance_requests`
--

DROP TABLE IF EXISTS `manager_attendance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_attendance_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(150) NOT NULL,
  `avatar_initials` varchar(10) NOT NULL,
  `department` varchar(100) NOT NULL,
  `request_type` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `request_date` date NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_attendance_requests`
--

LOCK TABLES `manager_attendance_requests` WRITE;
/*!40000 ALTER TABLE `manager_attendance_requests` DISABLE KEYS */;
INSERT INTO `manager_attendance_requests` VALUES (1,'Sneha Iyer','SI','HR','Casual Leave','1 day','2025-08-28','PENDING','2026-09-12 20:18:17'),(2,'Rohan Verma','RV','Development','Sick Leave','2 days','2025-08-27','PENDING','2026-09-12 20:18:17');
/*!40000 ALTER TABLE `manager_attendance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_daily_attendance`
--

DROP TABLE IF EXISTS `manager_daily_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_daily_attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `avatar_initials` varchar(10) NOT NULL,
  `department` varchar(100) NOT NULL,
  `check_in` varchar(20) DEFAULT NULL,
  `check_out` varchar(20) DEFAULT NULL,
  `work_hours` varchar(20) DEFAULT NULL,
  `status` enum('PRESENT','ON_LEAVE','ABSENT') NOT NULL DEFAULT 'PRESENT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mda_date` (`date`),
  KEY `idx_mda_dept` (`department`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_daily_attendance`
--

LOCK TABLES `manager_daily_attendance` WRITE;
/*!40000 ALTER TABLE `manager_daily_attendance` DISABLE KEYS */;
INSERT INTO `manager_daily_attendance` VALUES (1,'2025-08-26','Priya Sharma','PS','Design','09:02 AM','06:10 PM','9h 8m','PRESENT','2026-09-12 20:18:17'),(2,'2025-08-26','Rohan Verma','RV','Development','09:15 AM','-','-','ON_LEAVE','2026-09-12 20:18:17'),(3,'2025-08-26','Sneha Iyer','SI','HR','-','-','-','ON_LEAVE','2026-09-12 20:18:17'),(4,'2025-08-26','Aditya Kapoor','AK','QA','09:05 AM','06:00 PM','8h 55m','PRESENT','2026-09-12 20:18:17'),(5,'2025-08-26','Neha Tiwari','NT','Marketing','09:20 AM','-','-','ABSENT','2026-09-12 20:18:17'),(6,'2025-08-26','Rohit Kulkarni','RK','Development','09:10 AM','06:25 PM','9h 15m','PRESENT','2026-09-12 20:18:17');
/*!40000 ALTER TABLE `manager_daily_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_performance_distribution`
--

DROP TABLE IF EXISTS `manager_performance_distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_performance_distribution` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `score_range` varchar(50) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `color` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_performance_distribution`
--

LOCK TABLES `manager_performance_distribution` WRITE;
/*!40000 ALTER TABLE `manager_performance_distribution` DISABLE KEYS */;
INSERT INTO `manager_performance_distribution` VALUES (1,'Excellent (4.5 – 5.0)','4.5 - 5.0',3,'#14b8a6'),(2,'Good (3.5 – 4.4)','3.5 - 4.4',5,'#0284c7'),(3,'Average (2.5 – 3.4)','2.5 - 3.4',3,'#f59e0b'),(4,'Needs Improvement (< 2.5)','< 2.5',1,'#f43f5e');
/*!40000 ALTER TABLE `manager_performance_distribution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_performance_reviews`
--

DROP TABLE IF EXISTS `manager_performance_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_performance_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `employee_name` varchar(150) NOT NULL,
  `role_title` varchar(100) NOT NULL,
  `avatar_initials` varchar(10) NOT NULL,
  `review_period` varchar(100) NOT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 4.0,
  `status` enum('COMPLETED','IN_REVIEW','PENDING') NOT NULL DEFAULT 'IN_REVIEW',
  `reviewer_name` varchar(150) NOT NULL DEFAULT 'Arun Kumar',
  `feedback` text DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mpr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_performance_reviews`
--

LOCK TABLES `manager_performance_reviews` WRITE;
/*!40000 ALTER TABLE `manager_performance_reviews` DISABLE KEYS */;
INSERT INTO `manager_performance_reviews` VALUES (1,NULL,'Priya Sharma','UI/UX Designer','PS','Jan – Jun 2025',4.8,'COMPLETED','Arun Kumar','Exceeded expectations in design system revamp and sprint deliverables.','2025-08-26','2026-09-12 20:33:43'),(2,NULL,'Rohit Kulkarni','Frontend Developer','RK','Jan – Jun 2025',4.2,'COMPLETED','Arun Kumar','Strong delivery of UI components, timely feature releases.','2025-08-26','2026-09-12 20:33:43'),(3,NULL,'Sneha Iyer','HR Executive','SI','Jan – Jun 2025',4.0,'IN_REVIEW','Arun Kumar','Effective onboarding workflows and team pulse surveys.','2025-08-26','2026-09-12 20:33:43'),(4,NULL,'Rohan Verma','Backend Developer','RV','Jan – Jun 2025',3.8,'IN_REVIEW','Arun Kumar','Reliable API development, working on query optimization.','2025-08-26','2026-09-12 20:33:43'),(5,NULL,'Neha Tiwari','Content Writer','NT','Jan – Jun 2025',4.1,'COMPLETED','Arun Kumar','High quality content and marketing collateral generation.','2025-08-26','2026-09-12 20:33:43');
/*!40000 ALTER TABLE `manager_performance_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_performance_trends`
--

DROP TABLE IF EXISTS `manager_performance_trends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_performance_trends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `month_idx` int(11) NOT NULL,
  `month_label` varchar(20) NOT NULL,
  `month_name` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_performance_trends`
--

LOCK TABLES `manager_performance_trends` WRITE;
/*!40000 ALTER TABLE `manager_performance_trends` DISABLE KEYS */;
INSERT INTO `manager_performance_trends` VALUES (1,1,'Mar','March',2025,2.8),(2,2,'Apr','April',2025,3.1),(3,3,'May','May',2025,3.4),(4,4,'Jun','June',2025,3.7),(5,5,'Jul','July',2025,4.0),(6,6,'Aug','August',2025,4.2);
/*!40000 ALTER TABLE `manager_performance_trends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_reports_records`
--

DROP TABLE IF EXISTS `manager_reports_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_reports_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `avatar_initials` varchar(10) NOT NULL,
  `attendance_rate` int(11) NOT NULL DEFAULT 90,
  `tasks_completed` int(11) NOT NULL DEFAULT 10,
  `average_rating` decimal(2,1) NOT NULL DEFAULT 4.2,
  `status` enum('EXCELLENT','GOOD','NEEDS_SUPPORT') NOT NULL DEFAULT 'GOOD',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mrr_dept` (`department`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_reports_records`
--

LOCK TABLES `manager_reports_records` WRITE;
/*!40000 ALTER TABLE `manager_reports_records` DISABLE KEYS */;
INSERT INTO `manager_reports_records` VALUES (1,'Priya Sharma','Design','PS',96,12,4.8,'EXCELLENT','2026-09-12 21:11:46'),(2,'Rohit Kulkarni','Development','RK',90,10,4.6,'GOOD','2026-09-12 21:11:46'),(3,'Sneha Iyer','HR','SI',88,8,4.2,'GOOD','2026-09-12 21:11:46'),(4,'Rohan Verma','Development','RV',95,11,4.1,'GOOD','2026-09-12 21:11:46'),(5,'Neha Tiwari','Marketing','NT',89,7,3.9,'NEEDS_SUPPORT','2026-09-12 21:11:46'),(6,'Aditya Kapoor','QA','AK',92,9,4.3,'GOOD','2026-09-12 21:11:46');
/*!40000 ALTER TABLE `manager_reports_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_tasks`
--

DROP TABLE IF EXISTS `manager_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('HIGH','MEDIUM','LOW') NOT NULL DEFAULT 'MEDIUM',
  `status` enum('NOT_STARTED','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'NOT_STARTED',
  `progress_pct` int(11) NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_scope` varchar(20) NOT NULL DEFAULT 'INDIVIDUAL',
  `assigned_to_name` varchar(150) DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `employee_email` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_task_assigned` (`assigned_to`),
  KEY `idx_task_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_tasks`
--

LOCK TABLES `manager_tasks` WRITE;
/*!40000 ALTER TABLE `manager_tasks` DISABLE KEYS */;
INSERT INTO `manager_tasks` VALUES (6,NULL,6,'Website Redesign','Redesign corporate landing page and responsive components.','HIGH','COMPLETED',100,NULL,'2025-08-28','2026-09-12 19:41:06','2026-10-11 11:02:44','INDIVIDUAL',NULL,NULL,NULL),(7,NULL,7,'API Integration','Integrate third-party payroll payment gateway API.','MEDIUM','NOT_STARTED',0,NULL,'2025-08-30','2026-09-12 19:41:06','2026-09-12 19:41:06','INDIVIDUAL',NULL,NULL,NULL),(8,NULL,8,'Employee Handbook','Update FY 2025-26 HR policies and conduct code.','LOW','COMPLETED',100,NULL,'2025-08-25','2026-09-12 19:41:06','2026-09-12 19:41:06','INDIVIDUAL',NULL,NULL,NULL),(9,NULL,9,'Bug Fixes - Release 2.1','Patch authentication token timeout and cache bugs.','HIGH','IN_PROGRESS',40,NULL,'2025-08-27','2026-09-12 19:41:06','2026-09-12 19:41:06','INDIVIDUAL',NULL,NULL,NULL),(10,NULL,10,'Social Media Content','Prepare promotional copy and LinkedIn case study graphics.','MEDIUM','NOT_STARTED',0,NULL,'2025-09-02','2026-09-12 19:41:06','2026-09-12 19:41:06','INDIVIDUAL',NULL,NULL,NULL),(11,1,6,'Complete Security Audit & Auth Sync','Review all auth endpoints and verify user session validity.','HIGH','NOT_STARTED',0,'2026-11-01','2026-11-04','2026-11-01 17:11:49','2026-11-01 17:11:49','INDIVIDUAL','Priya Sharma',6,'rashi@example.com'),(12,1,0,'Sprint 42 Team Retrospective & Planning','All team members please review sprint deliverables and submit feedback.','MEDIUM','NOT_STARTED',0,'2026-11-01','2026-11-06','2026-11-01 17:11:49','2026-11-01 17:11:49','TEAM','Entire Team',NULL,NULL),(13,1,6,'Complete Security Audit & Auth Sync','Review all auth endpoints and verify user session validity.','HIGH','NOT_STARTED',0,'2026-11-01','2026-11-04','2026-11-01 17:12:32','2026-11-01 17:12:32','INDIVIDUAL','Priya Sharma',6,'rashi@example.com'),(14,1,0,'Sprint 42 Team Retrospective & Planning','All team members please review sprint deliverables and submit feedback.','MEDIUM','NOT_STARTED',0,'2026-11-01','2026-11-06','2026-11-01 17:12:32','2026-11-01 17:12:32','TEAM','Entire Team',NULL,NULL),(15,1,6,'Complete Security Audit & Auth Sync','Review all auth endpoints and verify user session validity.','HIGH','NOT_STARTED',0,'2026-11-01','2026-11-04','2026-11-01 17:14:26','2026-11-01 17:14:26','INDIVIDUAL','Priya Sharma',6,'rashi@example.com'),(16,1,0,'Sprint 42 Team Retrospective & Planning','All team members please review sprint deliverables and submit feedback.','MEDIUM','NOT_STARTED',0,'2026-11-01','2026-11-06','2026-11-01 17:14:26','2026-11-01 17:14:26','TEAM','Entire Team',NULL,NULL),(17,1,6,'Complete Security Audit & Auth Sync','Review all auth endpoints and verify user session validity.','HIGH','NOT_STARTED',0,'2026-11-01','2026-11-04','2026-11-01 17:15:14','2026-11-01 17:15:14','INDIVIDUAL','Priya Sharma',6,'rashi@example.com'),(18,1,0,'Sprint 42 Team Retrospective & Planning','All team members please review sprint deliverables and submit feedback.','MEDIUM','IN_PROGRESS',50,'2026-11-01','2026-11-06','2026-11-01 17:15:14','2026-11-01 17:15:14','TEAM','Entire Team',NULL,NULL);
/*!40000 ALTER TABLE `manager_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_team_competencies`
--

DROP TABLE IF EXISTS `manager_team_competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_team_competencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quarter` varchar(20) NOT NULL,
  `competency_name` varchar(100) NOT NULL,
  `score` decimal(3,1) NOT NULL,
  `max_score` decimal(3,1) NOT NULL DEFAULT 5.0,
  `bar_color` varchar(30) NOT NULL DEFAULT 'blue',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_team_competencies`
--

LOCK TABLES `manager_team_competencies` WRITE;
/*!40000 ALTER TABLE `manager_team_competencies` DISABLE KEYS */;
INSERT INTO `manager_team_competencies` VALUES (1,'Q3 2025','Job Knowledge',4.3,5.0,'blue'),(2,'Q3 2025','Quality of Work',4.1,5.0,'teal'),(3,'Q3 2025','Communication',4.0,5.0,'amber'),(4,'Q3 2025','Teamwork',4.4,5.0,'purple'),(5,'Q3 2025','Initiative',3.9,5.0,'magenta');
/*!40000 ALTER TABLE `manager_team_competencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_team_goals`
--

DROP TABLE IF EXISTS `manager_team_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_team_goals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `progress_pct` int(11) NOT NULL DEFAULT 0,
  `due_date` varchar(50) NOT NULL,
  `status` enum('ON_TRACK','IN_PROGRESS','AT_RISK','COMPLETED') NOT NULL DEFAULT 'IN_PROGRESS',
  `bar_color` varchar(30) NOT NULL DEFAULT '#3b82f6',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mtg_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_team_goals`
--

LOCK TABLES `manager_team_goals` WRITE;
/*!40000 ALTER TABLE `manager_team_goals` DISABLE KEYS */;
INSERT INTO `manager_team_goals` VALUES (1,NULL,'Improve client satisfaction',80,'30 Sep','ON_TRACK','#14b8a6','2026-09-12 20:33:43'),(2,NULL,'Complete product module',60,'15 Oct','IN_PROGRESS','#3b82f6','2026-09-12 20:33:43'),(3,NULL,'Reduce bug count',40,'30 Sep','AT_RISK','#f59e0b','2026-09-12 20:33:43'),(4,NULL,'Enhance documentation',100,'10 Sep','COMPLETED','#14b8a6','2026-09-12 20:33:43'),(5,NULL,'Team skill development',50,'25 Oct','IN_PROGRESS','#3b82f6','2026-09-12 20:33:43');
/*!40000 ALTER TABLE `manager_team_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_team_members`
--

DROP TABLE IF EXISTS `manager_team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_team_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `role_title` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL DEFAULT 'Development',
  `avatar_initials` varchar(10) NOT NULL,
  `status` enum('PRESENT','ON_LEAVE','ABSENT') NOT NULL DEFAULT 'PRESENT',
  `workload_current` int(11) NOT NULL DEFAULT 5,
  `workload_max` int(11) NOT NULL DEFAULT 8,
  `performance_rating` decimal(2,1) NOT NULL DEFAULT 4.2,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_team_members`
--

LOCK TABLES `manager_team_members` WRITE;
/*!40000 ALTER TABLE `manager_team_members` DISABLE KEYS */;
INSERT INTO `manager_team_members` VALUES (6,NULL,'Priya Sharma','UI/UX Designer','Design','PS','PRESENT',8,8,4.5,'priya.sharma@mindazzle.com',NULL,'2026-09-12 18:36:46'),(7,NULL,'Rohit Kulkarni','Frontend Developer','Development','RK','PRESENT',5,8,4.2,'rohit.kulkarni@mindazzle.com',NULL,'2026-09-12 18:36:46'),(8,NULL,'Sneha Iyer','HR Executive','HR','SI','ON_LEAVE',2,8,4.0,'sneha.iyer@mindazzle.com',NULL,'2026-09-12 18:36:46'),(9,NULL,'Rohan Verma','Backend Developer','Development','RV','PRESENT',7,8,4.3,'rohan.verma@mindazzle.com',NULL,'2026-09-12 18:36:46'),(10,NULL,'Neha Tiwari','Content Writer','Marketing','NT','ON_LEAVE',3,8,4.1,'neha.tiwari@mindazzle.com',NULL,'2026-09-12 18:36:46'),(11,NULL,'Aditya Kapoor','QA Engineer','Quality Assurance','AK','PRESENT',5,8,4.4,'aditya.kapoor@mindazzle.com',NULL,'2026-09-12 18:36:46'),(12,NULL,'Vikram Malhotra','DevOps Engineer','Infrastructure','VM','PRESENT',4,8,4.6,'vikram.m@mindazzle.com',NULL,'2026-09-12 18:36:46'),(13,NULL,'Ananya Sengupta','Product Designer','Design','AS','PRESENT',5,8,4.3,'ananya.s@mindazzle.com',NULL,'2026-09-12 18:36:46'),(14,NULL,'Siddharth Rao','Full Stack Developer','Development','SR','PRESENT',6,8,4.2,'siddharth.r@mindazzle.com',NULL,'2026-09-12 18:36:46'),(15,NULL,'Divya Krishnan','Data Analyst','Product','DK','PRESENT',4,8,4.5,'divya.k@mindazzle.com',NULL,'2026-09-12 18:36:46'),(16,NULL,'Manish Joshi','Mobile Developer','Development','MJ','PRESENT',5,8,4.1,'manish.j@mindazzle.com',NULL,'2026-09-12 18:36:46'),(17,NULL,'Tanvi Chawla','Technical Writer','Marketing','TC','ABSENT',3,8,4.2,'tanvi.c@mindazzle.com',NULL,'2026-09-12 18:36:46');
/*!40000 ALTER TABLE `manager_team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_trainings`
--

DROP TABLE IF EXISTS `manager_trainings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager_trainings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `target_audience` varchar(150) NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` enum('REGISTERED','NOT_STARTED','COMPLETED') NOT NULL DEFAULT 'NOT_STARTED',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_trainings`
--

LOCK TABLES `manager_trainings` WRITE;
/*!40000 ALTER TABLE `manager_trainings` DISABLE KEYS */;
INSERT INTO `manager_trainings` VALUES (1,'Advanced UI/UX Workshop','For Design Team','2025-08-28','REGISTERED','2026-09-11 17:24:29'),(2,'Agile Project Management','For Development Team','2025-08-30','NOT_STARTED','2026-09-11 17:24:29'),(3,'Leadership Skills','For Team Leads','2025-09-05','NOT_STARTED','2026-09-11 17:24:29'),(4,'Effective Communication','For All Team Members','2025-09-12','NOT_STARTED','2026-09-11 17:24:29');
/*!40000 ALTER TABLE `manager_trainings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `onboarding_records`
--

DROP TABLE IF EXISTS `onboarding_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `onboarding_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `joining_date` date NOT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `current_stage` varchar(100) DEFAULT NULL,
  `progress` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_onboarding_employee` (`employee_id`),
  KEY `idx_onboarding_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `onboarding_records`
--

LOCK TABLES `onboarding_records` WRITE;
/*!40000 ALTER TABLE `onboarding_records` DISABLE KEYS */;
INSERT INTO `onboarding_records` VALUES (1,1,'2026-10-16','COMPLETED','Documentation & IT Setup',75,'2026-10-11 09:51:17','2026-10-11 09:51:27'),(2,2,'2026-10-21','PENDING','Background Verification',35,'2026-10-11 09:51:17','2026-10-11 09:51:17'),(3,4,'2026-10-31','COMPLETED','Orientation Scheduled',90,'2026-10-11 09:51:17','2026-10-11 09:51:17');
/*!40000 ALTER TABLE `onboarding_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_records`
--

DROP TABLE IF EXISTS `payroll_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `pay_period` varchar(7) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `hra` decimal(12,2) NOT NULL DEFAULT 0.00,
  `special_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `pf` decimal(12,2) NOT NULL DEFAULT 0.00,
  `professional_tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `income_tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('PENDING','PAID','VOID') NOT NULL DEFAULT 'PENDING',
  `paid_on` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payroll_employee_period` (`pay_period`,`employee_id`),
  KEY `idx_payroll_period` (`pay_period`),
  KEY `idx_payroll_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_records`
--

LOCK TABLES `payroll_records` WRITE;
/*!40000 ALTER TABLE `payroll_records` DISABLE KEYS */;
INSERT INTO `payroll_records` VALUES (1,1,'2025-08',45000.00,18000.00,12000.00,0.00,75000.00,5400.00,200.00,6400.00,12000.00,58000.00,'PAID','2025-08-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(2,1,'2025-07',45000.00,18000.00,12000.00,0.00,75000.00,5400.00,200.00,6400.00,12000.00,58000.00,'PAID','2025-07-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(3,1,'2025-06',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-06-30','2026-09-11 16:10:44','2026-09-11 16:10:44'),(4,1,'2025-05',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-05-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(5,1,'2025-04',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-04-30','2026-09-11 16:10:44','2026-09-11 16:10:44'),(6,1,'2025-03',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-03-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(7,1,'2025-02',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-02-28','2026-09-11 16:10:44','2026-09-11 16:10:44'),(8,1,'2025-01',43000.00,17000.00,11000.00,0.00,71000.00,5160.00,200.00,5640.00,11000.00,55000.00,'PAID','2025-01-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(9,1,'2024-12',40000.00,16000.00,10000.00,0.00,66000.00,4800.00,200.00,6000.00,11000.00,55000.00,'PAID','2024-12-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(10,1,'2024-11',40000.00,16000.00,10000.00,0.00,66000.00,4800.00,200.00,6000.00,11000.00,55000.00,'PAID','2024-11-30','2026-09-11 16:10:44','2026-09-11 16:10:44'),(11,1,'2024-10',40000.00,16000.00,10000.00,0.00,66000.00,4800.00,200.00,6000.00,11000.00,55000.00,'PAID','2024-10-31','2026-09-11 16:10:44','2026-09-11 16:10:44'),(12,1,'2024-09',40000.00,16000.00,10000.00,0.00,66000.00,4800.00,200.00,6000.00,11000.00,55000.00,'PAID','2024-09-30','2026-09-11 16:10:44','2026-09-11 16:10:44'),(13,2,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(14,4,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(15,6,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(16,8,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(17,1,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 10:16:41'),(18,15,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(19,13,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(20,14,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(21,11,'2026-10',35000.00,10500.00,4200.00,14700.00,49700.00,1800.00,200.00,2485.00,4485.00,45215.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41'),(22,16,'2026-10',5000.00,1500.00,600.00,2100.00,7100.00,600.00,200.00,355.00,1155.00,5945.00,'PENDING',NULL,'2026-10-11 09:56:41','2026-10-11 09:56:41');
/*!40000 ALTER TABLE `payroll_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_goals`
--

DROP TABLE IF EXISTS `performance_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performance_goals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `review_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `progress` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('ACTIVE','COMPLETED','ARCHIVED') NOT NULL DEFAULT 'ACTIVE',
  `display_status` varchar(50) NOT NULL DEFAULT 'In Progress',
  `priority` enum('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_goal_employee` (`employee_id`),
  KEY `idx_goal_review` (`review_id`),
  KEY `idx_goal_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_goals`
--

LOCK TABLES `performance_goals` WRITE;
/*!40000 ALTER TABLE `performance_goals` DISABLE KEYS */;
INSERT INTO `performance_goals` VALUES (1,1,NULL,'UI Redesign for Client Portal','Complete responsive wireframes, user testing, and high-fidelity prototype handoff for the Q3 client portal release.','2026-07-01','2026-09-30',85,'ACTIVE','On Track','HIGH','2026-09-11 15:44:47','2026-09-11 15:44:47'),(2,1,NULL,'Improve Design System','Standardize typography scale, color tokens, and rebuild core buttons and inputs in Figma and Storybook.','2026-07-01','2026-10-30',60,'ACTIVE','In Progress','MEDIUM','2026-09-11 15:44:47','2026-09-11 15:44:47'),(3,1,NULL,'Complete Advanced Figma Course','Finish certification modules on variable modes, nested components, and automated code handoff.','2026-08-15','2026-09-15',100,'COMPLETED','Completed','HIGH','2026-09-11 15:44:47','2026-09-11 15:44:47'),(4,1,NULL,'Support 2 Product Releases','Provide QA design verification, bug scrubs, and release asset packs for Mobile App v2 and Web Portal v4.','2026-09-01','2026-12-31',25,'ACTIVE','At Risk','MEDIUM','2026-09-11 15:44:47','2026-09-11 15:44:47'),(5,15,NULL,'UI Redesign for Client Portal','Complete responsive wireframes, user testing, and high-fidelity prototype handoff for the Q3 client portal release.','2026-07-01','2026-09-30',85,'ACTIVE','On Track','HIGH','2026-11-11 21:20:07','2026-11-11 21:20:07'),(6,15,NULL,'Improve Design System','Standardize typography scale, color tokens, and rebuild core buttons and inputs in Figma and Storybook.','2026-07-01','2026-10-30',60,'ACTIVE','In Progress','MEDIUM','2026-11-11 21:20:07','2026-11-11 21:20:07'),(7,15,NULL,'Complete Advanced Figma Course','Finish certification modules on variable modes, nested components, and automated code handoff.','2026-08-15','2026-09-15',100,'COMPLETED','Completed','HIGH','2026-11-11 21:20:07','2026-11-11 21:20:07'),(8,15,NULL,'Support 2 Product Releases','Provide QA design verification, bug scrubs, and release asset packs for Mobile App v2 and Web Portal v4.','2026-09-01','2026-12-31',25,'ACTIVE','At Risk','MEDIUM','2026-11-11 21:20:07','2026-11-11 21:20:07');
/*!40000 ALTER TABLE `performance_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_ratings`
--

DROP TABLE IF EXISTS `performance_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performance_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int(10) unsigned NOT NULL,
  `criteria` varchar(150) NOT NULL,
  `rating` decimal(3,2) NOT NULL,
  `weight` decimal(3,2) NOT NULL DEFAULT 1.00,
  `comments` text DEFAULT NULL,
  `color` varchar(30) DEFAULT 'purple',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rating_review` (`review_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_ratings`
--

LOCK TABLES `performance_ratings` WRITE;
/*!40000 ALTER TABLE `performance_ratings` DISABLE KEYS */;
INSERT INTO `performance_ratings` VALUES (1,1,'Job Knowledge',4.50,1.00,'Deep expertise in Figma design tokens, component architecture, and design tokens hierarchy.','purple','2026-09-11 15:44:47'),(2,1,'Quality of Work',4.20,1.00,'Exceptional attention to pixel fidelity, user states, and micro-interactions.','blue','2026-09-11 15:44:47'),(3,1,'Communication',4.30,1.00,'Clear design rationale presentations and proactive cross-team collaboration.','cyan','2026-09-11 15:44:47'),(4,1,'Teamwork',4.40,1.00,'Highly supportive of junior designers and engineers during handoffs.','orange','2026-09-11 15:44:47'),(5,1,'Initiative',4.00,1.00,'Proactively initiated the component library overhaul and accessibility audit.','pink','2026-09-11 15:44:47'),(6,2,'Job Knowledge',4.10,1.00,'Solid understanding of design standards and workflows.','purple','2026-09-11 15:44:47'),(7,2,'Quality of Work',4.00,1.00,'Consistently delivers polished designs with few revision rounds.','blue','2026-09-11 15:44:47'),(8,2,'Communication',4.00,1.00,'Articulates design decisions clearly with stakeholders.','cyan','2026-09-11 15:44:47'),(9,2,'Teamwork',4.10,1.00,'Cooperative and reliable team player.','orange','2026-09-11 15:44:47'),(10,2,'Initiative',3.80,1.00,'Takes on challenging tasks willingly.','pink','2026-09-11 15:44:47'),(11,3,'Job Knowledge',3.80,1.00,'Growing grasp of company product suite.','purple','2026-09-11 15:44:47'),(12,3,'Quality of Work',3.80,1.00,'Good visual quality and timely deliveries.','blue','2026-09-11 15:44:47'),(13,3,'Communication',3.90,1.00,'Open to feedback and communicative.','cyan','2026-09-11 15:44:47'),(14,3,'Teamwork',3.90,1.00,'Collaborates enthusiastically.','orange','2026-09-11 15:44:47'),(15,3,'Initiative',3.60,1.00,'Growing independence in day-to-day execution.','pink','2026-09-11 15:44:47'),(16,4,'Job Knowledge',3.50,1.00,'Quick learner with modern UI tooling.','purple','2026-09-11 15:44:47'),(17,4,'Quality of Work',3.60,1.00,'Dependable execution on design tickets.','blue','2026-09-11 15:44:47'),(18,4,'Communication',3.70,1.00,'Asks great clarifying questions.','cyan','2026-09-11 15:44:47'),(19,4,'Teamwork',3.80,1.00,'Positive and receptive team member.','orange','2026-09-11 15:44:47'),(20,4,'Initiative',3.40,1.00,'Following established patterns effectively.','pink','2026-09-11 15:44:47'),(21,5,'Job Knowledge',4.50,1.00,'Deep expertise in Figma design tokens, component architecture, and design tokens hierarchy.','purple','2026-11-11 21:20:07'),(22,5,'Quality of Work',4.20,1.00,'Exceptional attention to pixel fidelity, user states, and micro-interactions.','blue','2026-11-11 21:20:07'),(23,5,'Communication',4.30,1.00,'Clear design rationale presentations and proactive cross-team collaboration.','cyan','2026-11-11 21:20:07'),(24,5,'Teamwork',4.40,1.00,'Highly supportive of junior designers and engineers during handoffs.','orange','2026-11-11 21:20:07'),(25,5,'Initiative',4.00,1.00,'Proactively initiated the component library overhaul and accessibility audit.','pink','2026-11-11 21:20:07'),(26,6,'Job Knowledge',4.10,1.00,'Solid understanding of design standards and workflows.','purple','2026-11-11 21:20:07'),(27,6,'Quality of Work',4.00,1.00,'Consistently delivers polished designs with few revision rounds.','blue','2026-11-11 21:20:07'),(28,6,'Communication',4.00,1.00,'Articulates design decisions clearly with stakeholders.','cyan','2026-11-11 21:20:07'),(29,6,'Teamwork',4.10,1.00,'Cooperative and reliable team player.','orange','2026-11-11 21:20:07'),(30,6,'Initiative',3.80,1.00,'Takes on challenging tasks willingly.','pink','2026-11-11 21:20:07'),(31,7,'Job Knowledge',3.80,1.00,'Growing grasp of company product suite.','purple','2026-11-11 21:20:07'),(32,7,'Quality of Work',3.80,1.00,'Good visual quality and timely deliveries.','blue','2026-11-11 21:20:07'),(33,7,'Communication',3.90,1.00,'Open to feedback and communicative.','cyan','2026-11-11 21:20:07'),(34,7,'Teamwork',3.90,1.00,'Collaborates enthusiastically.','orange','2026-11-11 21:20:07'),(35,7,'Initiative',3.60,1.00,'Growing independence in day-to-day execution.','pink','2026-11-11 21:20:07'),(36,8,'Job Knowledge',3.50,1.00,'Quick learner with modern UI tooling.','purple','2026-11-11 21:20:07'),(37,8,'Quality of Work',3.60,1.00,'Dependable execution on design tickets.','blue','2026-11-11 21:20:07'),(38,8,'Communication',3.70,1.00,'Asks great clarifying questions.','cyan','2026-11-11 21:20:07'),(39,8,'Teamwork',3.80,1.00,'Positive and receptive team member.','orange','2026-11-11 21:20:07'),(40,8,'Initiative',3.40,1.00,'Following established patterns effectively.','pink','2026-11-11 21:20:07');
/*!40000 ALTER TABLE `performance_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performance_reviews`
--

DROP TABLE IF EXISTS `performance_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performance_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `reviewer_id` int(10) unsigned DEFAULT NULL,
  `reviewer_name` varchar(150) DEFAULT NULL,
  `reviewer_role` varchar(150) DEFAULT NULL,
  `review_period_name` varchar(100) DEFAULT NULL,
  `review_period_start` date NOT NULL,
  `review_period_end` date NOT NULL,
  `review_date` date DEFAULT NULL,
  `overall_rating` decimal(3,2) DEFAULT NULL,
  `overall_comments` text DEFAULT NULL,
  `employee_comments` text DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','APPROVED','COMPLETED') NOT NULL DEFAULT 'DRAFT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_review_employee` (`employee_id`),
  KEY `idx_review_status` (`status`),
  KEY `idx_review_period` (`review_period_start`,`review_period_end`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performance_reviews`
--

LOCK TABLES `performance_reviews` WRITE;
/*!40000 ALTER TABLE `performance_reviews` DISABLE KEYS */;
INSERT INTO `performance_reviews` VALUES (1,1,NULL,'Rahul Mehta','Engineering Manager','Jul 2026','2026-01-01','2026-06-30','2026-07-15',4.30,'Priya delivered exceptional work on the Client Portal redesign. Her prototyping speed, stakeholder alignment, and design system governance have elevated team velocity significantly.',NULL,'COMPLETED','2026-09-11 15:44:47','2026-09-11 15:44:47'),(2,1,NULL,'Rahul Mehta','Engineering Manager','Jan 2026','2025-07-01','2025-12-31','2026-01-18',4.00,'Strong performance during Q4 delivery cycles. Demonstrates high ownership and high aesthetic quality.',NULL,'COMPLETED','2026-09-11 15:44:47','2026-09-11 15:44:47'),(3,1,NULL,'Sneha Iyer','HR Manager','Jul 2025','2025-01-01','2025-06-30','2025-07-12',3.80,'Demonstrated substantial growth in product thinking and teamwork. Great cultural fit.',NULL,'COMPLETED','2026-09-11 15:44:47','2026-09-11 15:44:47'),(4,1,NULL,'Sneha Iyer','HR Manager','Jan 2025','2024-07-01','2024-12-31','2025-01-15',3.60,'Successful onboarding review. Priya has quickly integrated into our product development cycle.',NULL,'COMPLETED','2026-09-11 15:44:47','2026-09-11 15:44:47'),(5,15,NULL,'Rahul Mehta','Engineering Manager','Jul 2026','2026-01-01','2026-06-30','2026-07-15',4.30,'Priya delivered exceptional work on the Client Portal redesign. Her prototyping speed, stakeholder alignment, and design system governance have elevated team velocity significantly.',NULL,'COMPLETED','2026-11-11 21:20:07','2026-11-11 21:20:07'),(6,15,NULL,'Rahul Mehta','Engineering Manager','Jan 2026','2025-07-01','2025-12-31','2026-01-18',4.00,'Strong performance during Q4 delivery cycles. Demonstrates high ownership and high aesthetic quality.',NULL,'COMPLETED','2026-11-11 21:20:07','2026-11-11 21:20:07'),(7,15,NULL,'Sneha Iyer','HR Manager','Jul 2025','2025-01-01','2025-06-30','2025-07-12',3.80,'Demonstrated substantial growth in product thinking and teamwork. Great cultural fit.',NULL,'COMPLETED','2026-11-11 21:20:07','2026-11-11 21:20:07'),(8,15,NULL,'Sneha Iyer','HR Manager','Jan 2025','2024-07-01','2024-12-31','2025-01-15',3.60,'Successful onboarding review. Priya has quickly integrated into our product development cycle.',NULL,'COMPLETED','2026-11-11 21:20:07','2026-11-11 21:20:07');
/*!40000 ALTER TABLE `performance_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.view','View dashboard','2026-08-23 14:24:52'),(2,'employee.view','View employee information','2026-08-23 14:24:52'),(3,'employee.create','Create employees','2026-08-23 14:24:52'),(4,'employee.edit','Edit employee information','2026-08-23 14:24:52'),(5,'employee.delete','Delete employees','2026-08-23 14:24:52'),(6,'department.view','View departments','2026-08-23 14:24:52'),(7,'department.create','Create departments','2026-08-23 14:24:52'),(8,'department.edit','Edit departments','2026-08-23 14:24:52'),(9,'department.delete','Delete departments','2026-08-23 14:24:52'),(10,'user.view','View user accounts','2026-08-23 14:24:52'),(11,'user.create','Create user accounts','2026-08-23 14:24:52'),(12,'user.edit','Edit user accounts','2026-08-23 14:24:52'),(13,'user.delete','Delete user accounts','2026-08-23 14:24:52'),(14,'attendance.view','View attendance','2026-08-23 14:24:52'),(15,'attendance.manage','Manage attendance','2026-08-23 14:24:52'),(16,'leave.view','View leave requests','2026-08-23 14:24:52'),(17,'leave.apply','Apply for leave','2026-08-23 14:24:52'),(18,'leave.approve','Approve leave requests','2026-08-23 14:24:52'),(19,'task.view','View tasks','2026-08-23 14:24:52'),(20,'task.create','Create tasks','2026-08-23 14:24:52'),(21,'task.edit','Edit tasks','2026-08-23 14:24:52'),(22,'performance.view','View performance','2026-08-23 14:24:52'),(23,'performance.manage','Manage performance','2026-08-23 14:24:52'),(24,'reports.view','View reports','2026-08-23 14:24:52'),(25,'settings.manage','Manage system settings','2026-08-23 14:24:52'),(26,'audit.view','View audit logs','2026-08-23 14:24:52'),(27,'organisation.view','View organisations and structure','2026-08-26 08:49:44'),(28,'organisation.create','Create new organisation','2026-08-26 08:49:44'),(29,'organisation.edit','Edit organisation details and status','2026-08-26 08:49:44'),(30,'organisation.delete','Delete organisation','2026-08-26 08:49:44'),(31,'designation.view','View designations','2026-08-26 09:07:48'),(32,'designation.create','Create designation','2026-08-26 09:07:48'),(33,'designation.edit','Edit designation','2026-08-26 09:07:48'),(34,'designation.delete','Delete designation','2026-08-26 09:07:48');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recruitment_candidates`
--

DROP TABLE IF EXISTS `recruitment_candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recruitment_candidates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `job_opening_id` int(10) unsigned DEFAULT NULL,
  `stage` enum('APPLIED','SCREENING','INTERVIEW','OFFERED','HIRED','REJECTED') NOT NULL DEFAULT 'APPLIED',
  `source` varchar(80) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `applied_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_candidate_stage` (`stage`),
  KEY `idx_candidate_job` (`job_opening_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recruitment_candidates`
--

LOCK TABLES `recruitment_candidates` WRITE;
/*!40000 ALTER TABLE `recruitment_candidates` DISABLE KEYS */;
INSERT INTO `recruitment_candidates` VALUES (1,'Priya Nair','priya.nair@example.com',1,'APPLIED','Job Portal',NULL,'2026-09-04 15:15:36','2026-09-05 15:15:36'),(2,'Arun Kumar','arun.kumar@example.com',2,'SCREENING','Employee Referral',NULL,'2026-09-03 15:15:36','2026-09-05 15:15:36'),(3,'Vishal Raj','vishal.raj@example.com',3,'INTERVIEW','Company Website',NULL,'2026-09-02 15:15:36','2026-09-05 15:15:36'),(4,'Anitha Das','anitha.das@example.com',4,'OFFERED','Social Media',NULL,'2026-09-01 15:15:36','2026-09-05 15:15:36'),(5,'Rahul Mehta','rahul.mehta@example.com',5,'HIRED','Walk-in',NULL,'2026-08-31 15:15:36','2026-09-05 15:15:36'),(6,'Sneha Iyer','sneha.iyer@example.com',6,'APPLIED','Job Portal',NULL,'2026-08-30 15:15:36','2026-09-05 15:15:36'),(7,'Daniel Martin','daniel.martin@example.com',1,'INTERVIEW','Employee Referral',NULL,'2026-08-29 15:15:36','2026-09-05 15:15:36'),(8,'Kavya S','kavya.s@example.com',2,'SCREENING','Other',NULL,'2026-08-28 15:15:36','2026-09-05 15:15:36');
/*!40000 ALTER TABLE `recruitment_candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recruitment_interviews`
--

DROP TABLE IF EXISTS `recruitment_interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recruitment_interviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` int(10) unsigned NOT NULL,
  `interviewer` varchar(150) DEFAULT NULL,
  `interview_at` datetime NOT NULL,
  `interview_type` varchar(60) NOT NULL DEFAULT 'Video Interview',
  `status` enum('SCHEDULED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_interview_datetime` (`interview_at`),
  KEY `idx_interview_candidate` (`candidate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recruitment_interviews`
--

LOCK TABLES `recruitment_interviews` WRITE;
/*!40000 ALTER TABLE `recruitment_interviews` DISABLE KEYS */;
INSERT INTO `recruitment_interviews` VALUES (1,1,'HR Manager','2026-09-06 10:00:00','Video Interview','SCHEDULED','2026-09-05 15:15:36'),(2,2,'HR Manager','2026-09-07 10:00:00','Video Interview','SCHEDULED','2026-09-05 15:15:36'),(3,3,'HR Manager','2026-09-08 10:00:00','Video Interview','SCHEDULED','2026-09-05 15:15:36'),(4,4,'HR Manager','2026-09-09 10:00:00','Video Interview','SCHEDULED','2026-09-05 15:15:36');
/*!40000 ALTER TABLE `recruitment_interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_role_permissions_permission` (`permission_id`),
  CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,32),(1,33),(1,34),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,14),(2,15),(2,16),(2,18),(2,24),(2,27),(2,31),(3,1),(3,2),(3,14),(3,16),(3,18),(3,19),(3,20),(3,21),(3,22),(3,24),(3,31),(4,1),(4,2),(4,14),(4,17),(4,19),(4,22),(4,31);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','System administrator with full system access','2026-08-23 14:24:15','2026-08-23 14:24:15'),(2,'HR','Human resources management','2026-08-23 14:24:15','2026-08-23 14:24:15'),(3,'MANAGER','Manager or Team Lead with team/department access','2026-08-23 14:24:15','2026-08-23 14:24:15'),(4,'EMPLOYEE','Regular employee','2026-08-23 14:24:15','2026-08-23 14:24:15');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_configurations`
--

DROP TABLE IF EXISTS `system_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(120) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(60) NOT NULL DEFAULT 'General',
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_configurations_key` (`config_key`),
  KEY `idx_system_configurations_status` (`status`),
  KEY `idx_system_configurations_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_configurations`
--

LOCK TABLES `system_configurations` WRITE;
/*!40000 ALTER TABLE `system_configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_policies`
--

DROP TABLE IF EXISTS `system_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_policies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `category` varchar(60) NOT NULL DEFAULT 'General',
  `description` varchar(255) NOT NULL DEFAULT '',
  `policy_value` text DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_policies_name` (`name`),
  KEY `idx_system_policies_status` (`status`),
  KEY `idx_system_policies_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_policies`
--

LOCK TABLES `system_policies` WRITE;
/*!40000 ALTER TABLE `system_policies` DISABLE KEYS */;
INSERT INTO `system_policies` VALUES (1,'leave policy','General','in one year there should be 30 days leave will be granted','','ACTIVE','2026-09-19 10:44:28','2026-09-19 10:44:28');
/*!40000 ALTER TABLE `system_policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(60) NOT NULL DEFAULT 'GENERAL',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_system_settings_key` (`setting_key`),
  KEY `idx_system_settings_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=37360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'system_name','Employee Management System','GENERAL','2026-08-28 13:15:11'),(2,'date_format','DD MMM YYYY','GENERAL','2026-08-28 13:15:11'),(3,'time_format','12 Hour','GENERAL','2026-08-28 13:15:11'),(4,'timezone','Asia/Kolkata','GENERAL','2026-08-28 13:15:11'),(5,'language','English (US)','GENERAL','2026-08-28 13:15:11'),(6,'items_per_page','10','GENERAL','2026-08-28 13:15:11'),(7,'maintenance_mode','0','GENERAL','2026-08-28 13:15:11'),(8,'session_timeout','30','SECURITY','2026-08-28 13:15:11'),(9,'password_expiry_days','90','SECURITY','2026-08-28 13:15:11');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_certificates`
--

DROP TABLE IF EXISTS `training_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_certificates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `course_title` varchar(255) NOT NULL,
  `certificate_number` varchar(100) NOT NULL,
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expiry_at` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cert_number` (`certificate_number`),
  KEY `idx_cert_enrollment` (`enrollment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_certificates`
--

LOCK TABLES `training_certificates` WRITE;
/*!40000 ALTER TABLE `training_certificates` DISABLE KEYS */;
INSERT INTO `training_certificates` VALUES (1,3,1,'Agile Fundamentals','CERT-2026-AF-998','2026-08-20 14:30:00',NULL,NULL,'2026-09-11 15:44:47'),(2,999,1,'Human-Centered Design Fundamentals','CERT-2026-HCD-108','2026-05-14 11:15:00',NULL,NULL,'2026-09-11 15:44:47');
/*!40000 ALTER TABLE `training_certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_enrollments`
--

DROP TABLE IF EXISTS `training_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_enrollments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('ENROLLED','IN_PROGRESS','COMPLETED','DROPPED') NOT NULL DEFAULT 'ENROLLED',
  `progress` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `completion_date` date DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `certificate_issued` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_enrollment` (`program_id`,`employee_id`),
  KEY `idx_enroll_employee` (`employee_id`),
  KEY `idx_enroll_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_enrollments`
--

LOCK TABLES `training_enrollments` WRITE;
/*!40000 ALTER TABLE `training_enrollments` DISABLE KEYS */;
INSERT INTO `training_enrollments` VALUES (1,2,1,'2026-07-10','IN_PROGRESS',80,NULL,NULL,NULL,0,'2026-09-11 15:44:47','2026-09-11 15:44:47'),(2,3,1,'2026-07-10','IN_PROGRESS',60,NULL,NULL,NULL,0,'2026-09-11 15:44:47','2026-09-11 15:44:47'),(3,4,1,'2026-07-10','COMPLETED',100,NULL,NULL,NULL,1,'2026-09-11 15:44:47','2026-09-11 15:44:47'),(4,5,1,'2026-07-10','ENROLLED',0,NULL,NULL,NULL,0,'2026-09-11 15:44:47','2026-09-11 15:44:47'),(5,6,1,'2026-07-10','IN_PROGRESS',40,NULL,NULL,NULL,0,'2026-09-11 15:44:47','2026-09-11 15:44:47'),(6,7,1,'2026-09-14','IN_PROGRESS',0,NULL,NULL,NULL,0,'2026-09-14 16:31:18','2026-09-14 16:31:18'),(7,2,15,'2026-07-10','IN_PROGRESS',80,NULL,NULL,NULL,0,'2026-11-11 21:20:07','2026-11-11 21:20:07'),(8,3,15,'2026-07-10','IN_PROGRESS',60,NULL,NULL,NULL,0,'2026-11-11 21:20:07','2026-11-11 21:20:07'),(9,4,15,'2026-07-10','COMPLETED',100,NULL,NULL,NULL,1,'2026-11-11 21:20:07','2026-11-11 21:20:07');
/*!40000 ALTER TABLE `training_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_programs`
--

DROP TABLE IF EXISTS `training_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_programs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `provider` varchar(150) NOT NULL DEFAULT 'Mindazzle Academy',
  `category` varchar(100) DEFAULT NULL,
  `trainer` varchar(150) DEFAULT NULL,
  `duration` varchar(50) DEFAULT '2h 30m',
  `rating` decimal(2,1) NOT NULL DEFAULT 4.5,
  `icon_type` varchar(50) NOT NULL DEFAULT 'default',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration_hours` decimal(6,2) DEFAULT NULL,
  `max_participants` int(10) unsigned DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `delivery_mode` enum('ONLINE','IN_PERSON','HYBRID') NOT NULL DEFAULT 'IN_PERSON',
  `status` enum('DRAFT','ACTIVE','COMPLETED','CANCELLED') NOT NULL DEFAULT 'DRAFT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_training_status` (`status`),
  KEY `idx_training_category` (`category`),
  KEY `idx_training_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_programs`
--

LOCK TABLES `training_programs` WRITE;
/*!40000 ALTER TABLE `training_programs` DISABLE KEYS */;
INSERT INTO `training_programs` VALUES (1,'leaders','hii to leadership','Mindazzle Academy','leadership','raja','2h 30m',4.5,'default','2026-09-02','2026-09-03',7.00,1,'room','IN_PERSON','ACTIVE','2026-09-08 19:11:02','2026-09-08 19:11:02'),(2,'Advanced Figma','Master advanced Auto Layout, design tokens, interactive components and prototyping.','Mindazzle Academy','Design',NULL,'2h 30m',4.8,'figma',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(3,'UI/UX Best Practices','Comprehensive guide to user research, wireframing, and accessibility standards.','Coursera','Design',NULL,'5h 00m',4.7,'uiux',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(4,'Agile Fundamentals','Learn Scrum, Kanban, sprints, and agile product development frameworks.','LinkedIn Learning','Management',NULL,'3h 45m',4.6,'agile',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(5,'Design Leadership','Developing executive presence, leading cross-functional teams, and design critique.','Udemy','Leadership',NULL,'4h 15m',4.5,'leadership',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(6,'Product Management','Product lifecycle, roadmapping, backlog prioritization, and metrics.','Mindazzle Academy','Management',NULL,'6h 00m',4.6,'pm',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(7,'Effective Communication','Interpersonal communications, presentation mastery, and storytelling.','Coursera','Soft Skills',NULL,'3h 15m',4.7,'communication',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(8,'Project Management','Planning, execution, risk management, and stakeholder communication.','Udemy','Management',NULL,'4h 20m',4.6,'project',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47'),(9,'Design Thinking','Empathize, define, ideate, prototype, and test solution methodologies.','LinkedIn Learning','Innovation',NULL,'2h 10m',4.5,'thinking',NULL,NULL,NULL,NULL,NULL,'IN_PERSON','ACTIVE','2026-09-11 15:44:47','2026-09-11 15:44:47');
/*!40000 ALTER TABLE `training_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_sessions`
--

DROP TABLE IF EXISTS `training_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration_hours` decimal(5,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `trainer` varchar(150) DEFAULT NULL,
  `status` enum('SCHEDULED','COMPLETED','CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session_program` (`program_id`),
  KEY `idx_session_date` (`session_date`),
  KEY `idx_session_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_sessions`
--

LOCK TABLES `training_sessions` WRITE;
/*!40000 ALTER TABLE `training_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `target_scope` enum('INDIVIDUAL','TEAM','ALL') NOT NULL DEFAULT 'INDIVIDUAL',
  `type` varchar(50) NOT NULL DEFAULT 'TASK_ASSIGNED',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `priority` enum('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_un_emp` (`employee_id`,`is_read`),
  KEY `idx_un_scope` (`target_scope`,`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
INSERT INTO `user_notifications` VALUES (1,NULL,6,'INDIVIDUAL','TASK_ASSIGNED','New Task Assigned: Complete Security Audit & Auth Sync','Manager assigned you a new task: \"Complete Security Audit & Auth Sync\". Due on 04 Nov 2026.',11,'HIGH',0,'2026-11-01 17:11:49'),(2,NULL,NULL,'TEAM','TASK_ASSIGNED','New Team Task: Sprint 42 Team Retrospective & Planning','Manager assigned a new task to the entire team: \"Sprint 42 Team Retrospective & Planning\". Due on 06 Nov 2026.',12,'MEDIUM',0,'2026-11-01 17:11:49'),(3,NULL,6,'INDIVIDUAL','TASK_ASSIGNED','New Task Assigned: Complete Security Audit & Auth Sync','Manager assigned you a new task: \"Complete Security Audit & Auth Sync\". Due on 04 Nov 2026.',13,'HIGH',0,'2026-11-01 17:12:32'),(4,NULL,NULL,'TEAM','TASK_ASSIGNED','New Team Task: Sprint 42 Team Retrospective & Planning','Manager assigned a new task to the entire team: \"Sprint 42 Team Retrospective & Planning\". Due on 06 Nov 2026.',14,'MEDIUM',0,'2026-11-01 17:12:32'),(5,NULL,6,'INDIVIDUAL','TASK_ASSIGNED','New Task Assigned: Complete Security Audit & Auth Sync','Manager assigned you a new task: \"Complete Security Audit & Auth Sync\". Due on 04 Nov 2026.',15,'HIGH',0,'2026-11-01 17:14:26'),(6,NULL,NULL,'TEAM','TASK_ASSIGNED','New Team Task: Sprint 42 Team Retrospective & Planning','Manager assigned a new task to the entire team: \"Sprint 42 Team Retrospective & Planning\". Due on 06 Nov 2026.',16,'MEDIUM',0,'2026-11-01 17:14:26'),(7,NULL,6,'INDIVIDUAL','TASK_ASSIGNED','New Task Assigned: Complete Security Audit & Auth Sync','Manager assigned you a new task: \"Complete Security Audit & Auth Sync\". Due on 04 Nov 2026.',17,'HIGH',0,'2026-11-01 17:15:14'),(8,NULL,NULL,'TEAM','TASK_ASSIGNED','New Team Task: Sprint 42 Team Retrospective & Planning','Manager assigned a new task to the entire team: \"Sprint 42 Team Retrospective & Planning\". Due on 06 Nov 2026.',18,'MEDIUM',0,'2026-11-01 17:15:14');
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  
  `role_id` bigint(20) unsigned NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('PENDING','ACTIVE','SUSPENDED','LOCKED','DEACTIVATED') DEFAULT 'PENDING',
  `scope_type` enum('SELF','TEAM','DEPARTMENT','BRANCH','COMPANY','ALL') DEFAULT 'SELF',
  `scope_id` bigint(20) unsigned DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 0,
  `failed_login_attempts` int(10) unsigned DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_employee_id` (`employee_id`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_company_id` (`company_id`),
  KEY `idx_users_status` (`status`),
  CONSTRAINT `fk_users_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,1,1,'admin','admin@example.com','$2y$10$SJZngkzj8.59KCNQx4p2R.5MQC4e0aT3hLbgBXThTXp37ZiWVPU36','ACTIVE','ALL',NULL,0,0,NULL,'2026-11-14 13:31:00','2026-08-23 14:26:08','2026-11-14 08:01:00'),(2,14,NULL,2,'hr','vasu@gmail.com','$2y$10$YSbsyHJdjzta6l8hgELMt.nVCEQsqftdzpngfTgpxrMCww.q1ihzG','','ALL',NULL,0,0,NULL,'2026-08-26 14:30:42','2026-08-26 07:46:18','2026-09-16 17:44:35'),(3,NULL,1,2,'vasundhara','vasundhara@gmail.com','$2y$10$nG2Ng7GUuTDksNi1NxLfS.zS7c/HioYbpu3BYoeEW70XDu1HrZrHe','ACTIVE','ALL',NULL,0,0,NULL,'2026-11-14 12:35:09','2026-09-03 13:12:42','2026-11-14 07:05:09'),(4,15,NULL,4,'ssavitha','ssavitha@gmail.com','$2y$10$VngXXrZ.8s3we3c6NLwFm.jeunQzbHeMTYoy1Okd2K1OO1OZ2d4nC','ACTIVE','ALL',NULL,0,0,NULL,'2026-11-14 13:31:58','2026-09-04 11:57:44','2026-11-14 08:01:58'),(5,NULL,NULL,3,'manager','arun.kumar@mindazzle.com','$2y$10$oe8qLSz.YusL/pvpfnY/FOyKKdQYQtRkbjwdEowm46vfQzMvF2FP.','ACTIVE','SELF',NULL,0,0,NULL,'2026-09-11 17:25:08','2026-09-11 11:54:29','2026-09-11 11:55:08'),(6,NULL,1,3,'seetha','seetha@gmail.com','$2y$10$9RDTnOkpj8nwJ9VHhTZyOetjG8gSzxCklyNKJPXHj1F0Oy6UdoBIu','ACTIVE','ALL',NULL,0,0,NULL,'2026-11-14 12:33:37','2026-09-11 12:29:38','2026-11-14 07:03:37'),(7,16,1,4,'shabanasardhar6','shabanasardhar6@gmail.com','$2y$10$N7EtqTyUR2jk7JZfSToDmuLditC6X0lYetAmvFK2UD0C2urNCHns2','ACTIVE','ALL',NULL,0,0,NULL,NULL,'2026-10-08 12:17:46','2026-10-08 12:17:46');
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

-- Dump completed on 2026-11-25 11:26:55
