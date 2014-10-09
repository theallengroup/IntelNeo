-- MySQL dump 10.13  Distrib 5.1.44, for apple-darwin8.11.1 (i386)
--
-- Host: localhost    Database: onboarding
-- ------------------------------------------------------
-- Server version	5.1.44

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
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `activity_type_id` int(11) NOT NULL,
  `activity_status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_session1_idx` (`session_id`),
  KEY `fk_activity_activity_type1_idx` (`activity_type_id`),
  KEY `fk_activity_activity_status1_idx` (`activity_status_id`),
  CONSTRAINT `fk_activity_activity_status1` FOREIGN KEY (`activity_status_id`) REFERENCES `activity_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_type1` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_session1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
INSERT INTO `activity` VALUES (1,'Example Poll 1',1,1,2),(2,'Acronym 1',1,5,2),(3,'Wager Quiz 1',1,3,1);
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_status`
--

DROP TABLE IF EXISTS `activity_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_status`
--

LOCK TABLES `activity_status` WRITE;
/*!40000 ALTER TABLE `activity_status` DISABLE KEYS */;
INSERT INTO `activity_status` VALUES (1,'READY'),(2,'COMPLETE');
/*!40000 ALTER TABLE `activity_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_type`
--

DROP TABLE IF EXISTS `activity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `activity_value` int(100) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_type`
--

LOCK TABLES `activity_type` WRITE;
/*!40000 ALTER TABLE `activity_type` DISABLE KEYS */;
INSERT INTO `activity_type` VALUES (1,'Poll','',1),(2,'Shuffle Quiz','',1),(3,'Wager Quiz','',1),(4,'Spin Quiz','',1),(5,'Acronym','',1);
/*!40000 ALTER TABLE `activity_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `usr_id` varchar(45) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_answer_question_option1_idx` (`question_option_id`),
  KEY `fk_answer_question1_idx` (`question_id`),
  CONSTRAINT `fk_answer_question1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_answer_question_option1` FOREIGN KEY (`question_option_id`) REFERENCES `question_option` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Category1'),(2,'Category2');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `body` text,
  `url` varchar(255) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_video_session1_idx` (`session_id`),
  KEY `fk_content_content_type1_idx` (`content_type_id`),
  CONSTRAINT `fk_content_content_type1` FOREIGN KEY (`content_type_id`) REFERENCES `content_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_video_session1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,'NAME','Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.','',1,1),(2,'Video1','este es el video 1','http://www.youtube.com/embed/dQw4w9WgXcQ',1,2),(3,'SESSION2','','',2,1),(4,'SESSION3','','',3,1),(5,'VIDEO SESION1','este es el video 2','http://www.youtube.com/embed/dQw4w9WgXcQ',2,2),(6,'duck1','this is a test','',1,1),(7,'Session 1 content test','this is a test','',1,1);
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_type`
--

DROP TABLE IF EXISTS `content_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_type`
--

LOCK TABLES `content_type` WRITE;
/*!40000 ALTER TABLE `content_type` DISABLE KEYS */;
INSERT INTO `content_type` VALUES (1,'Text'),(2,'Video');
/*!40000 ALTER TABLE `content_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) NOT NULL DEFAULT '0',
  `table_name` varchar(32) NOT NULL DEFAULT '',
  `record` varchar(32) NOT NULL DEFAULT '',
  `info` mediumtext NOT NULL,
  `log_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `log_level` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,1,'none','-1','db created','2014-07-31 09:14:29','app'),(2,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36 ','2014-07-31 14:17:45','1'),(3,1,'usr','3','INSERT:\n# = 3\nNombre = demo\nCorreo Electr&oacute;nico = demo\nContrase&ntilde;a = fe01ce2a7fbac8fafaed7c982a04e229\n# de Accesos = 0\n&Uacute;ltimo Acceso = 1970-01-01 00:00:00\nFecha de Creaci&oacute;n = 2014-07-31 14:17:52\nUltima IP = 0.0.0.0','2014-07-31 14:17:59','1'),(4,1,'session','1','INSERT:\nId = 1\nName = Session1','2014-07-31 14:37:33','1'),(5,1,'activity_status','1','INSERT:\nId = 1\nName = READY\nActivity value = 0','2014-07-31 14:37:47','1'),(6,1,'activity_status','2','INSERT:\nId = 2\nName = COMPLETE\nActivity value = 0','2014-07-31 14:38:37','1'),(7,1,'activity_type','1','INSERT:\nId = 1\nName = Poll\nDescription = \nActivity Value = 1','2014-07-31 14:41:10','1'),(8,1,'activity_type','2','INSERT:\nId = 2\nName = Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:15','1'),(9,1,'activity_type','3','INSERT:\nId = 3\nName = Wager Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:20','1'),(10,1,'activity','1','INSERT:\nId = 1\nName = Example Poll 1\nSession = 1\nActivity type = 1\nActivity status = 1','2014-07-31 14:42:33','1'),(11,1,'question','1','INSERT:\nId = 1\nName = Do you like Stuff?\nDescription = \nActivity = 1','2014-07-31 14:43:18','1'),(12,1,'question_option','1','INSERT:\nId = 1\nName = YES\nQuestion = 1','2014-07-31 14:43:25','1'),(13,1,'question_option','2','INSERT:\nId = 2\nName = NO\nQuestion = 1','2014-07-31 14:43:31','1'),(14,1,'privilege','23','INSERT:\nID = 23\nName = Categories\nAction = category/all*','2014-07-31 14:45:21','1'),(15,1,'category','1','INSERT:\nId = 1\nName = Category1','2014-07-31 14:45:29','1'),(16,1,'category','2','INSERT:\nId = 2\nName = Category2','2014-07-31 14:45:34','1'),(17,1,'rank','1','INSERT:\nId = 1\nName = Newbie\nRank value = 1\nScore start = 0\nScore end = 10','2014-07-31 14:46:32','1'),(18,1,'rank','2','INSERT:\nId = 2\nName = Advanced Learner\nRank value = 2\nScore start = 11\nScore end = 20','2014-07-31 14:46:55','1'),(19,1,'usr2session','1','INSERT:\nId = 1\nUser = 1\nSession = 1','2014-07-31 14:49:56','1'),(20,1,'usr_status','1','INSERT:\nId = 1\nUser = 1\nScore = 1\nRank = 1','2014-07-31 14:50:09','1'),(21,1,'usr_status','2','INSERT:\nId = 2\nUser = 3\nScore = 15\nRank = 2','2014-07-31 14:50:21','1'),(22,1,'content_type','1','INSERT:\nId = 1\nName = Text','2014-07-31 14:51:14','1'),(23,1,'content','1','INSERT:\nId = 1\nName = \nBody = \nUrl = \nSession = 1\nContent type = 1','2014-07-31 14:51:16','1'),(24,1,'content_type','2','INSERT:\nId = 2\nName = Video','2014-07-31 14:51:24','1'),(25,1,'content','2','INSERT:\nId = 2\nName = Video1\nBody = \nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ\nSession = 1\nContent type = 2','2014-07-31 14:52:07','1'),(26,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:16:49','1'),(27,1,'question','2','INSERT:\nId = 2\nName = question 3\nDescription = \nActivity = 1\nCategory = 1','2014-08-01 16:20:21','1'),(28,1,'question_option','3','INSERT:\nId = 3\nName = opcion1\nQuestion = 2','2014-08-01 16:20:27','1'),(29,1,'question_option','4','INSERT:\nId = 4\nName = opcion2\nQuestion = 2','2014-08-01 16:20:33','1'),(30,1,'role','3','INSERT:\nID = 3\nName = Content','2014-08-01 16:23:49','1'),(31,3,'usr','1','LOGIN:demo IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:23:54','1'),(32,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:25:07','1'),(33,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 13:03:28','1'),(34,1,'privilege','24','INSERT:\nID = 24\nName = FrontEnd\nAction = usr2session/register*','2014-08-02 13:25:35','1'),(35,1,'role','4','INSERT:\nID = 4\nName = FrontEnd','2014-08-02 13:25:49','1'),(36,1,'session','2','INSERT:\nId = 2\nName = Session2','2014-08-02 13:46:12','1'),(37,1,'session','3','INSERT:\nId = 3\nName = My New Session','2014-08-02 13:46:18','1'),(38,1,'privilege','24','UPDATE:\nAction = usr2session/register* -> usr2session/r_*','2014-08-02 14:54:49','1'),(39,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 14:55:15','1'),(40,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:16:42','1'),(41,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:16:48','1'),(42,1,'SQLCONSOLE','-1','select * from usr','2014-08-02 15:17:19','SQL'),(43,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:17:33','1'),(44,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:17:43','1'),(45,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:18:40','1'),(46,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:19:08','1'),(47,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:14:55','1'),(48,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:18:28','1'),(49,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:28:35','1'),(50,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:28:54','1'),(51,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:29:56','1'),(52,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:30:57','1'),(53,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:47:43','1'),(54,1,'content','1','UPDATE:\nName =  -> NAME\nBody =  -> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\nSession = 1 -> 2','2014-08-02 16:48:42','1'),(55,1,'content','1','UPDATE:\nSession = 2 -> 1','2014-08-02 16:51:35','1'),(56,1,'content','3','INSERT:\nId = 3\nName = SESSION2\nBody = \nUrl = \nSession = 2\nContent type = 1','2014-08-02 16:51:42','1'),(57,1,'content','4','INSERT:\nId = 4\nName = SESSION3\nBody = \nUrl = \nSession = 3\nContent type = 1','2014-08-02 16:51:48','1'),(58,1,'content','5','INSERT:\nId = 5\nName = VIDEO SESION1\nBody = \nUrl = \nSession = 1\nContent type = 2','2014-08-02 16:52:04','1'),(59,1,'usr','1','LOGIN FAILED:  ::1 ','2014-08-02 16:59:45','1'),(60,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:59:53','1'),(61,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 17:07:14','1'),(62,1,'content','6','INSERT:\nId = 6\nName = duck1\nBody = this is a test\nUrl = \nSession = 1\nContent type = 1','2014-08-02 17:07:28','1'),(63,1,'content','2','UPDATE:\nBody =  -> este es el video 1','2014-08-02 17:28:45','1'),(64,1,'content','5','UPDATE:\nBody =  -> este es el video 2','2014-08-02 17:28:51','1'),(65,1,'content','5','UPDATE:\nUrl =  -> https://www.youtube.com/watch?v=dQw4w9WgXcQ','2014-08-02 17:29:04','1'),(66,1,'content','2','UPDATE:\nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ -> http://www.youtube.com/embed/dQw4w9WgXcQ','2014-08-02 17:30:09','1'),(67,1,'content','5','UPDATE:\nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ -> http://www.youtube.com/embed/dQw4w9WgXcQ','2014-08-02 17:30:15','1'),(68,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:10:12','1'),(69,1,'session','4','INSERT:\nId = 4\nName = testSession','2014-08-02 23:11:10','1'),(70,21,'usr','1','LOGIN:prueba@algo.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:11:51','1'),(71,1,'content','7','INSERT:\nId = 7\nName = Session 1 content test\nBody = this is a test\nUrl = \nSession = 1\nContent type = 1','2014-08-02 23:12:37','1'),(72,1,'content','5','UPDATE:\nSession = 1 -> 2','2014-08-02 23:15:56','1'),(73,1,'usr','1','LOGIN FAILED:  ::1 root','2014-08-02 23:17:20','1'),(74,21,'usr','1','LOGIN:prueba@algo.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:17:41','1'),(75,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:43:46','1'),(76,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-03 21:19:17','1'),(77,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 00:19:20','1'),(78,1,'activity_type','4','INSERT:\nId = 4\nName = Sping Quiz\nDescription = \nActivity Value = 1','2014-08-04 00:19:34','1'),(79,1,'activity_type','4','UPDATE:\nName = Sping Quiz -> Spin Quiz','2014-08-04 00:20:00','1'),(80,1,'activity_type','5','INSERT:\nId = 5\nName = Acronym\nDescription = \nActivity Value = 1','2014-08-04 00:21:58','1'),(81,1,'activity_type','2','UPDATE:\nName = Quiz -> Shuffle Quiz','2014-08-04 00:22:08','1'),(82,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 00:39:04','1'),(83,1,'activity','2','INSERT:\nId = 2\nName = Acronym 1\nSession = 1\nActivity type = 5\nActivity status = 1','2014-08-04 01:03:35','1'),(84,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 01:03:41','1'),(85,1,'question','3','INSERT:\nId = 3\nName = Define SOP\nDescription = \nActivity = 2\nCategory = 1','2014-08-04 01:04:23','1'),(86,1,'question_option','5','INSERT:\nId = 5\nName = Dummy1\nQuestion = 3','2014-08-04 01:04:49','1'),(87,1,'question_option','6','INSERT:\nId = 6\nName = Dummy2\nQuestion = 3','2014-08-04 01:04:53','1'),(88,1,'question_option','7','INSERT:\nId = 7\nName = Dummy3\nQuestion = 3','2014-08-04 01:04:59','1'),(89,1,'question_option','5','UPDATE:\nName = Dummy1 -> First Term','2014-08-04 01:09:21','1'),(90,1,'question_option','6','UPDATE:\nName = Dummy2 -> Second Term','2014-08-04 01:09:26','1'),(91,1,'question_option','7','UPDATE:\nName = Dummy3 -> Third Term','2014-08-04 01:09:32','1'),(92,1,'privilege','11','UPDATE:\nAction = activity_status/all* -> activity_status/read_only*','2014-08-04 01:20:45','1'),(93,1,'privilege','12','UPDATE:\nAction = activity_type/all* -> activity_type/read_only*','2014-08-04 01:20:54','1'),(94,1,'privilege','21','UPDATE:\nAction = usr_status/all* -> usr_status/read_only*','2014-08-04 01:21:16','1'),(95,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 01:38:46','1'),(96,1,'activity','3','INSERT:\nId = 3\nName = Wager Quiz 1\nSession = 1\nActivity type = 3\nActivity status = 1','2014-08-04 01:38:54','1'),(97,1,'question','4','INSERT:\nId = 4\nName = Who won?\nDescription = \nActivity = 3\nCategory = 1','2014-08-04 01:39:07','1'),(98,1,'question_option','8','INSERT:\nId = 8\nName = Someone\nQuestion = 4','2014-08-04 01:39:20','1'),(99,1,'question_option','9','INSERT:\nId = 9\nName = The Winner\nQuestion = 4','2014-08-04 01:39:26','1'),(100,1,'question_option','10','INSERT:\nId = 10\nName = The Loser\nQuestion = 4','2014-08-04 01:39:30','1'),(101,1,'question_option','11','INSERT:\nId = 11\nName = Nobody\nQuestion = 4','2014-08-04 01:39:34','1'),(102,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 03:08:14','1'),(103,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 03:08:43','1'),(104,1,'activity','3','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:09:17','1'),(105,1,'activity','1','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:09:23','1'),(106,1,'question','5','INSERT:\nId = 5\nName = Another Question\nDescription = \nActivity = 1\nCategory = 1','2014-08-04 03:09:55','1'),(107,1,'question_option','12','INSERT:\nId = 12\nName = YES\nQuestion = 5','2014-08-04 03:10:01','1'),(108,1,'question_option','13','INSERT:\nId = 13\nName = Maybe\nQuestion = 5','2014-08-04 03:10:04','1'),(109,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:10:39','1'),(110,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:10:47','1'),(111,1,'question','6','INSERT:\nId = 6\nName = Define ABC\nDescription = \nActivity = 2\nCategory = 1','2014-08-04 03:11:27','1'),(112,1,'question_option','14','INSERT:\nId = 14\nName = A\nQuestion = 6','2014-08-04 03:11:31','1'),(113,1,'question_option','15','INSERT:\nId = 15\nName = Big\nQuestion = 6','2014-08-04 03:11:35','1'),(114,1,'question_option','16','INSERT:\nId = 16\nName = Cat\nQuestion = 6','2014-08-04 03:11:38','1'),(115,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:12:04','1'),(116,1,'activity','3','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:12:23','1'),(117,1,'question','7','INSERT:\nId = 7\nName = Frances capital\nDescription = \nActivity = 3\nCategory = 1','2014-08-04 03:12:59','1'),(118,1,'question_option','17','INSERT:\nId = 17\nName = Paris\nQuestion = 7','2014-08-04 03:13:07','1'),(119,1,'question_option','18','INSERT:\nId = 18\nName = Lyon\nQuestion = 7','2014-08-04 03:13:12','1'),(120,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 13:26:31','1'),(121,1,'activity','1','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:28:09','1'),(122,1,'activity','3','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:28:13','1'),(123,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:28:52','1'),(124,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:28:57','1'),(125,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:29:08','1'),(126,1,'activity','3','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:29:13','1');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `privilege`
--

DROP TABLE IF EXISTS `privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `privilege` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `action` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;
INSERT INTO `privilege` VALUES (1,'[##usr]','usr/all*'),(2,'[##role]','role/all*'),(3,'[##privilege]','privilege/all*'),(4,'[##usr2role]','usr2role/all*'),(5,'[##role2priv]','role2priv/all*'),(6,'[##event]','log/read_only*'),(7,'Ayuda','help/help'),(8,'Editar Perfil','usr/edit_profile*'),(9,'DBAdmin','edit/db_manager'),(10,'Activities','activity/all*'),(11,'Activity status','activity_status/read_only*'),(12,'Activity types','activity_type/read_only*'),(13,'Answers','answer/all*'),(14,'Contents','content/all*'),(15,'Content types','content_type/all*'),(16,'Questions','question/all*'),(17,'Question options','question_option/all*'),(18,'Ranks','rank/all*'),(19,'Sessions','session/all*'),(20,'Users in Session','usr2session/all*'),(21,'Users Status','usr_status/read_only*'),(22,'categories','category/all*'),(23,'Categories','category/all*'),(24,'FrontEnd','usr2session/r_*');
/*!40000 ALTER TABLE `privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `activity_id` int(11) NOT NULL,
  `category_id` int(10) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_question_activity1_idx` (`activity_id`),
  CONSTRAINT `fk_question_activity1` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (1,'Do you like Stuff?','',1,1),(2,'question 3','',1,1),(3,'Define SOP','',2,1),(4,'Who won?','',3,1),(5,'Another Question','',1,1),(6,'Define ABC','',2,1),(7,'Frances capital','',3,1);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_option`
--

DROP TABLE IF EXISTS `question_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_question_option_question1_idx` (`question_id`),
  CONSTRAINT `fk_question_option_question1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_option`
--

LOCK TABLES `question_option` WRITE;
/*!40000 ALTER TABLE `question_option` DISABLE KEYS */;
INSERT INTO `question_option` VALUES (1,'YES',1),(2,'NO',1),(3,'opcion1',2),(4,'opcion2',2),(5,'First Term',3),(6,'Second Term',3),(7,'Third Term',3),(8,'Someone',4),(9,'The Winner',4),(10,'The Loser',4),(11,'Nobody',4),(12,'YES',5),(13,'Maybe',5),(14,'A',6),(15,'Big',6),(16,'Cat',6),(17,'Paris',7),(18,'Lyon',7);
/*!40000 ALTER TABLE `question_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rank`
--

DROP TABLE IF EXISTS `rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `rank_value` int(11) DEFAULT NULL,
  `score_start` int(11) DEFAULT NULL,
  `score_end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank`
--

LOCK TABLES `rank` WRITE;
/*!40000 ALTER TABLE `rank` DISABLE KEYS */;
INSERT INTO `rank` VALUES (1,'Newbie',1,0,10),(2,'Advanced Learner',2,11,20);
/*!40000 ALTER TABLE `rank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Administrator'),(2,'onboarding_admin'),(3,'Content'),(4,'FrontEnd');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role2priv`
--

DROP TABLE IF EXISTS `role2priv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role2priv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) DEFAULT NULL,
  `privilege_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role2priv`
--

LOCK TABLES `role2priv` WRITE;
/*!40000 ALTER TABLE `role2priv` DISABLE KEYS */;
INSERT INTO `role2priv` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(26,2,11),(27,2,12),(13,2,13),(14,2,14),(15,2,15),(16,2,16),(17,2,17),(18,2,18),(19,2,19),(20,2,20),(28,2,21),(22,2,23),(23,3,14),(25,4,24);
/*!40000 ALTER TABLE `role2priv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES (1,'Session1'),(2,'Session2'),(3,'My New Session'),(4,'testSession');
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr`
--

DROP TABLE IF EXISTS `usr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `login_count` int(10) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `last_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr`
--

LOCK TABLES `usr` WRITE;
/*!40000 ALTER TABLE `usr` DISABLE KEYS */;
INSERT INTO `usr` VALUES (1,'Administrator','root','31c23a230051702de27d954c3f4d25f9',14,'2014-08-04 03:08:14','2000-01-01 00:00:00','1'),(2,'onboarding_admin','onboarding_admin','3e47b75000b0924b6c9ba5759a7cf15d',0,'2000-01-01 00:00:00','2014-07-31 14:14:30','1'),(3,'demo','demo','fe01ce2a7fbac8fafaed7c982a04e229',1,'2014-08-01 16:23:54','2014-07-31 14:17:52','127.0.0.1'),(4,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:56:03','::1'),(5,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:57:38','::1'),(6,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:57:42','::1'),(7,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:58:40','::1'),(8,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:58:43','::1'),(9,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:59:35','::1'),(10,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:00:33','::1'),(11,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:06','::1'),(12,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:16','::1'),(13,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:30','::1'),(14,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:03:03','::1'),(15,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:03:15','::1'),(16,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:04:16','::1'),(17,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:05:16','::1'),(18,'hola','hola@algo.com','4d186321c1a7f0f354b297e8914ab240',0,'2000-01-01 00:00:00','2014-08-02 15:07:35','::1'),(19,'a','a@b.com','0cc175b9c0f1b6a831c399e269772661',8,'2014-08-04 13:26:31','2014-08-02 15:16:37','1'),(20,'a','a@c.com','0cc175b9c0f1b6a831c399e269772661',3,'2014-08-04 03:08:43','2014-08-02 16:29:40','1'),(21,'prueba','prueba@algo.com','0cc175b9c0f1b6a831c399e269772661',2,'2014-08-02 23:17:41','2014-08-02 23:11:48','1');
/*!40000 ALTER TABLE `usr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr2role`
--

DROP TABLE IF EXISTS `usr2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr2role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) DEFAULT NULL,
  `role_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2role`
--

LOCK TABLES `usr2role` WRITE;
/*!40000 ALTER TABLE `usr2role` DISABLE KEYS */;
INSERT INTO `usr2role` VALUES (1,1,1),(2,2,2),(3,1,2),(4,3,3),(5,1,4),(6,3,4),(7,4,4),(8,5,4),(9,6,4),(10,7,4),(11,8,4),(12,9,4),(13,10,4),(14,11,4),(15,12,4),(16,13,4),(17,14,4),(18,15,4),(19,16,4),(20,17,4),(21,18,4),(22,19,4),(23,20,4),(24,21,4);
/*!40000 ALTER TABLE `usr2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr2session`
--

DROP TABLE IF EXISTS `usr2session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr2session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usr2session_session_idx` (`session_id`),
  CONSTRAINT `fk_usr2session_session` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2session`
--

LOCK TABLES `usr2session` WRITE;
/*!40000 ALTER TABLE `usr2session` DISABLE KEYS */;
INSERT INTO `usr2session` VALUES (1,1,1),(2,4,2),(3,5,2),(4,6,2),(5,7,2),(6,8,2),(7,9,2),(8,10,2),(9,11,2),(10,12,2),(11,13,2),(12,14,2),(13,15,2),(14,16,2),(15,17,2),(16,18,3),(17,19,1),(18,20,1),(19,21,1);
/*!40000 ALTER TABLE `usr2session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_status`
--

DROP TABLE IF EXISTS `usr_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `rank_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usr_status_rank1_idx` (`rank_id`),
  CONSTRAINT `fk_usr_status_rank1` FOREIGN KEY (`rank_id`) REFERENCES `rank` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_status`
--

LOCK TABLES `usr_status` WRITE;
/*!40000 ALTER TABLE `usr_status` DISABLE KEYS */;
INSERT INTO `usr_status` VALUES (1,1,1,1),(2,3,15,2),(16,17,0,1),(17,18,0,1),(18,19,0,1),(19,20,0,1),(20,21,0,1);
/*!40000 ALTER TABLE `usr_status` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-04 16:18:27
