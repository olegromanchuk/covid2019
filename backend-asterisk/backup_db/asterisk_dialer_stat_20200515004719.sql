-- MySQL dump 10.13  Distrib 5.6.48, for Linux (x86_64)
--
-- Host: localhost    Database: asterisk_dialer_stat
-- ------------------------------------------------------
-- Server version	5.6.48

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `callrecords`
--

DROP TABLE IF EXISTS `callrecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callrecords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_contact` varchar(90) DEFAULT NULL,
  `main_contact_phone` varchar(100) DEFAULT NULL,
  `email_address` varchar(50) DEFAULT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `processed_datetime` datetime DEFAULT NULL,
  `result` varchar(99) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `dialed_number_debug` varchar(100) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callrecords`
--

LOCK TABLES `callrecords` WRITE;
/*!40000 ALTER TABLE `callrecords` DISABLE KEYS */;
INSERT INTO `callrecords` VALUES (1,'Joe Dow','7772223333','joedow@mail.net',1,'2020-05-13 09:47:42','voicemail or answering service',NULL,'7772223333',1);
INSERT INTO `callrecords` VALUES (2,'Mary Teresa','7772223344','mary@mail.net',1,'2020-05-13 09:47:42','confirmed human',NULL,'7772223344',1);
INSERT INTO `callrecords` VALUES (3,'Alice Ku','7772223344',NULL,1,'2020-05-13 09:48:21','hangup',NULL,'7772223344',1);
/*!40000 ALTER TABLE `callrecords` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-05-15  0:47:19
