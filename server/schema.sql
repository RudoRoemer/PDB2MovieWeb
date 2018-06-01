-- MySQL dump 10.14  Distrib 5.5.56-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: db_pdb2movie
-- ------------------------------------------------------
-- Server version	5.5.56-MariaDB

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
-- Table structure for table `Favourites`
--

DROP TABLE IF EXISTS `Favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Favourites` (
  `email_suffix` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `max_requests` int(11) NOT NULL,
  `favourite_id` int(11) NOT NULL AUTO_INCREMENT,
  `blacklisted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`favourite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Requests`
--

DROP TABLE IF EXISTS `Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Requests` (
  `filename` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `python_used` tinyint(1) DEFAULT NULL,
  `resolution` varchar(9) COLLATE utf8_unicode_ci NOT NULL,
  `combi` tinyint(1) DEFAULT NULL,
  `multi` tinyint(1) DEFAULT NULL,
  `waters` tinyint(1) DEFAULT NULL,
  `threed` tinyint(1) DEFAULT NULL,
  `confs` int(11) NOT NULL,
  `freq` int(11) NOT NULL,
  `step` double(9,8) NOT NULL,
  `dstep` double(10,9) NOT NULL,
  `molList` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modList` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cutList` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `complete` tinyint(1) DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `original_name` varchar(40) COLLATE utf8_unicode_ci DEFAULT 'Unnamed File',
  `time_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_comp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `extension` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` varchar(300) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`req_id`)
) ENGINE=InnoDB AUTO_INCREMENT=356 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `max_requests` int(11) NOT NULL,
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `current_requests` int(11) NOT NULL DEFAULT '0',
  `blacklisted` tinyint(1) DEFAULT '0',
  `secret_code` int(6) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-06-01 11:29:14
