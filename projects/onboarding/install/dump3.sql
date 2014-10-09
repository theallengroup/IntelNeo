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
  `activity_type_id` int(11) NOT NULL,
  `activity_status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_activity_activity_type1_idx` (`activity_type_id`),
  KEY `fk_activity_activity_status1_idx` (`activity_status_id`),
  CONSTRAINT `fk_activity_activity_status1` FOREIGN KEY (`activity_status_id`) REFERENCES `activity_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_type1` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
INSERT INTO `activity` VALUES (1,'Example Poll 1',1,1),(2,'Acronym 1',5,1),(3,'Wager Quiz 1',3,1),(4,'complete? demo test 6',1,2),(6,'Spin Test',4,1),(7,'SHUFFLE TEST',2,1);
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity2session`
--

DROP TABLE IF EXISTS `activity2session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity2session` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_id` varchar(100) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity2session`
--

LOCK TABLES `activity2session` WRITE;
/*!40000 ALTER TABLE `activity2session` DISABLE KEYS */;
INSERT INTO `activity2session` VALUES (5,'1','1'),(6,'2','1'),(3,'3','1'),(8,'4','1'),(9,'6','1'),(10,'7','1');
/*!40000 ALTER TABLE `activity2session` ENABLE KEYS */;
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
INSERT INTO `activity_type` VALUES (1,'Poll','',200),(2,'Shuffle Quiz','',200),(3,'Wager Quiz','',200),(4,'Spin Quiz','',200),(5,'Acronym','',200);
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
INSERT INTO `answer` VALUES (54,1,1,'19',1,'1 '),(55,4,2,'19',1,'4 '),(56,13,5,'19',1,'13 '),(57,21,9,'19',1,'21 '),(58,23,10,'19',1,'23 ');
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
INSERT INTO `category` VALUES (1,'Topic 1'),(2,'Topic 2');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `completed_activity`
--

DROP TABLE IF EXISTS `completed_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `completed_activity` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activity_date` datetime DEFAULT NULL,
  `usr_id` int(10) DEFAULT '1',
  `activity_id` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `completed_activity`
--

LOCK TABLES `completed_activity` WRITE;
/*!40000 ALTER TABLE `completed_activity` DISABLE KEYS */;
INSERT INTO `completed_activity` VALUES (25,'2014-08-13 12:47:17',19,1),(26,'2014-08-13 13:13:41',19,6);
/*!40000 ALTER TABLE `completed_activity` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,1,'none','-1','db created','2014-07-31 09:14:29','app'),(2,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36 ','2014-07-31 14:17:45','1'),(3,1,'usr','3','INSERT:\n# = 3\nNombre = demo\nCorreo Electr&oacute;nico = demo\nContrase&ntilde;a = fe01ce2a7fbac8fafaed7c982a04e229\n# de Accesos = 0\n&Uacute;ltimo Acceso = 1970-01-01 00:00:00\nFecha de Creaci&oacute;n = 2014-07-31 14:17:52\nUltima IP = 0.0.0.0','2014-07-31 14:17:59','1'),(4,1,'session','1','INSERT:\nId = 1\nName = Session1','2014-07-31 14:37:33','1'),(5,1,'activity_status','1','INSERT:\nId = 1\nName = READY\nActivity value = 0','2014-07-31 14:37:47','1'),(6,1,'activity_status','2','INSERT:\nId = 2\nName = COMPLETE\nActivity value = 0','2014-07-31 14:38:37','1'),(7,1,'activity_type','1','INSERT:\nId = 1\nName = Poll\nDescription = \nActivity Value = 1','2014-07-31 14:41:10','1'),(8,1,'activity_type','2','INSERT:\nId = 2\nName = Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:15','1'),(9,1,'activity_type','3','INSERT:\nId = 3\nName = Wager Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:20','1'),(10,1,'activity','1','INSERT:\nId = 1\nName = Example Poll 1\nSession = 1\nActivity type = 1\nActivity status = 1','2014-07-31 14:42:33','1'),(11,1,'question','1','INSERT:\nId = 1\nName = Do you like Stuff?\nDescription = \nActivity = 1','2014-07-31 14:43:18','1'),(12,1,'question_option','1','INSERT:\nId = 1\nName = YES\nQuestion = 1','2014-07-31 14:43:25','1'),(13,1,'question_option','2','INSERT:\nId = 2\nName = NO\nQuestion = 1','2014-07-31 14:43:31','1'),(14,1,'privilege','23','INSERT:\nID = 23\nName = Categories\nAction = category/all*','2014-07-31 14:45:21','1'),(15,1,'category','1','INSERT:\nId = 1\nName = Category1','2014-07-31 14:45:29','1'),(16,1,'category','2','INSERT:\nId = 2\nName = Category2','2014-07-31 14:45:34','1'),(17,1,'rank','1','INSERT:\nId = 1\nName = Newbie\nRank value = 1\nScore start = 0\nScore end = 10','2014-07-31 14:46:32','1'),(18,1,'rank','2','INSERT:\nId = 2\nName = Advanced Learner\nRank value = 2\nScore start = 11\nScore end = 20','2014-07-31 14:46:55','1'),(19,1,'usr2session','1','INSERT:\nId = 1\nUser = 1\nSession = 1','2014-07-31 14:49:56','1'),(20,1,'usr_status','1','INSERT:\nId = 1\nUser = 1\nScore = 1\nRank = 1','2014-07-31 14:50:09','1'),(21,1,'usr_status','2','INSERT:\nId = 2\nUser = 3\nScore = 15\nRank = 2','2014-07-31 14:50:21','1'),(22,1,'content_type','1','INSERT:\nId = 1\nName = Text','2014-07-31 14:51:14','1'),(23,1,'content','1','INSERT:\nId = 1\nName = \nBody = \nUrl = \nSession = 1\nContent type = 1','2014-07-31 14:51:16','1'),(24,1,'content_type','2','INSERT:\nId = 2\nName = Video','2014-07-31 14:51:24','1'),(25,1,'content','2','INSERT:\nId = 2\nName = Video1\nBody = \nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ\nSession = 1\nContent type = 2','2014-07-31 14:52:07','1'),(26,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:16:49','1'),(27,1,'question','2','INSERT:\nId = 2\nName = question 3\nDescription = \nActivity = 1\nCategory = 1','2014-08-01 16:20:21','1'),(28,1,'question_option','3','INSERT:\nId = 3\nName = opcion1\nQuestion = 2','2014-08-01 16:20:27','1'),(29,1,'question_option','4','INSERT:\nId = 4\nName = opcion2\nQuestion = 2','2014-08-01 16:20:33','1'),(30,1,'role','3','INSERT:\nID = 3\nName = Content','2014-08-01 16:23:49','1'),(31,3,'usr','1','LOGIN:demo IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:23:54','1'),(32,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:25:07','1'),(33,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 13:03:28','1'),(34,1,'privilege','24','INSERT:\nID = 24\nName = FrontEnd\nAction = usr2session/register*','2014-08-02 13:25:35','1'),(35,1,'role','4','INSERT:\nID = 4\nName = FrontEnd','2014-08-02 13:25:49','1'),(36,1,'session','2','INSERT:\nId = 2\nName = Session2','2014-08-02 13:46:12','1'),(37,1,'session','3','INSERT:\nId = 3\nName = My New Session','2014-08-02 13:46:18','1'),(38,1,'privilege','24','UPDATE:\nAction = usr2session/register* -> usr2session/r_*','2014-08-02 14:54:49','1'),(39,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 14:55:15','1'),(40,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:16:42','1'),(41,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:16:48','1'),(42,1,'SQLCONSOLE','-1','select * from usr','2014-08-02 15:17:19','SQL'),(43,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:17:33','1'),(44,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:17:43','1'),(45,1,'usr','1','LOGIN FAILED:  ::1 a@b.com','2014-08-02 15:18:40','1'),(46,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 15:19:08','1'),(47,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:14:55','1'),(48,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:18:28','1'),(49,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:28:35','1'),(50,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:28:54','1'),(51,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:29:56','1'),(52,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:30:57','1'),(53,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:47:43','1'),(54,1,'content','1','UPDATE:\nName =  -> NAME\nBody =  -> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\nSession = 1 -> 2','2014-08-02 16:48:42','1'),(55,1,'content','1','UPDATE:\nSession = 2 -> 1','2014-08-02 16:51:35','1'),(56,1,'content','3','INSERT:\nId = 3\nName = SESSION2\nBody = \nUrl = \nSession = 2\nContent type = 1','2014-08-02 16:51:42','1'),(57,1,'content','4','INSERT:\nId = 4\nName = SESSION3\nBody = \nUrl = \nSession = 3\nContent type = 1','2014-08-02 16:51:48','1'),(58,1,'content','5','INSERT:\nId = 5\nName = VIDEO SESION1\nBody = \nUrl = \nSession = 1\nContent type = 2','2014-08-02 16:52:04','1'),(59,1,'usr','1','LOGIN FAILED:  ::1 ','2014-08-02 16:59:45','1'),(60,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 16:59:53','1'),(61,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 17:07:14','1'),(62,1,'content','6','INSERT:\nId = 6\nName = duck1\nBody = this is a test\nUrl = \nSession = 1\nContent type = 1','2014-08-02 17:07:28','1'),(63,1,'content','2','UPDATE:\nBody =  -> este es el video 1','2014-08-02 17:28:45','1'),(64,1,'content','5','UPDATE:\nBody =  -> este es el video 2','2014-08-02 17:28:51','1'),(65,1,'content','5','UPDATE:\nUrl =  -> https://www.youtube.com/watch?v=dQw4w9WgXcQ','2014-08-02 17:29:04','1'),(66,1,'content','2','UPDATE:\nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ -> http://www.youtube.com/embed/dQw4w9WgXcQ','2014-08-02 17:30:09','1'),(67,1,'content','5','UPDATE:\nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ -> http://www.youtube.com/embed/dQw4w9WgXcQ','2014-08-02 17:30:15','1'),(68,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:10:12','1'),(69,1,'session','4','INSERT:\nId = 4\nName = testSession','2014-08-02 23:11:10','1'),(70,21,'usr','1','LOGIN:prueba@algo.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:11:51','1'),(71,1,'content','7','INSERT:\nId = 7\nName = Session 1 content test\nBody = this is a test\nUrl = \nSession = 1\nContent type = 1','2014-08-02 23:12:37','1'),(72,1,'content','5','UPDATE:\nSession = 1 -> 2','2014-08-02 23:15:56','1'),(73,1,'usr','1','LOGIN FAILED:  ::1 root','2014-08-02 23:17:20','1'),(74,21,'usr','1','LOGIN:prueba@algo.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:17:41','1'),(75,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-02 23:43:46','1'),(76,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-03 21:19:17','1'),(77,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 00:19:20','1'),(78,1,'activity_type','4','INSERT:\nId = 4\nName = Sping Quiz\nDescription = \nActivity Value = 1','2014-08-04 00:19:34','1'),(79,1,'activity_type','4','UPDATE:\nName = Sping Quiz -> Spin Quiz','2014-08-04 00:20:00','1'),(80,1,'activity_type','5','INSERT:\nId = 5\nName = Acronym\nDescription = \nActivity Value = 1','2014-08-04 00:21:58','1'),(81,1,'activity_type','2','UPDATE:\nName = Quiz -> Shuffle Quiz','2014-08-04 00:22:08','1'),(82,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 00:39:04','1'),(83,1,'activity','2','INSERT:\nId = 2\nName = Acronym 1\nSession = 1\nActivity type = 5\nActivity status = 1','2014-08-04 01:03:35','1'),(84,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 01:03:41','1'),(85,1,'question','3','INSERT:\nId = 3\nName = Define SOP\nDescription = \nActivity = 2\nCategory = 1','2014-08-04 01:04:23','1'),(86,1,'question_option','5','INSERT:\nId = 5\nName = Dummy1\nQuestion = 3','2014-08-04 01:04:49','1'),(87,1,'question_option','6','INSERT:\nId = 6\nName = Dummy2\nQuestion = 3','2014-08-04 01:04:53','1'),(88,1,'question_option','7','INSERT:\nId = 7\nName = Dummy3\nQuestion = 3','2014-08-04 01:04:59','1'),(89,1,'question_option','5','UPDATE:\nName = Dummy1 -> First Term','2014-08-04 01:09:21','1'),(90,1,'question_option','6','UPDATE:\nName = Dummy2 -> Second Term','2014-08-04 01:09:26','1'),(91,1,'question_option','7','UPDATE:\nName = Dummy3 -> Third Term','2014-08-04 01:09:32','1'),(92,1,'privilege','11','UPDATE:\nAction = activity_status/all* -> activity_status/read_only*','2014-08-04 01:20:45','1'),(93,1,'privilege','12','UPDATE:\nAction = activity_type/all* -> activity_type/read_only*','2014-08-04 01:20:54','1'),(94,1,'privilege','21','UPDATE:\nAction = usr_status/all* -> usr_status/read_only*','2014-08-04 01:21:16','1'),(95,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 01:38:46','1'),(96,1,'activity','3','INSERT:\nId = 3\nName = Wager Quiz 1\nSession = 1\nActivity type = 3\nActivity status = 1','2014-08-04 01:38:54','1'),(97,1,'question','4','INSERT:\nId = 4\nName = Who won?\nDescription = \nActivity = 3\nCategory = 1','2014-08-04 01:39:07','1'),(98,1,'question_option','8','INSERT:\nId = 8\nName = Someone\nQuestion = 4','2014-08-04 01:39:20','1'),(99,1,'question_option','9','INSERT:\nId = 9\nName = The Winner\nQuestion = 4','2014-08-04 01:39:26','1'),(100,1,'question_option','10','INSERT:\nId = 10\nName = The Loser\nQuestion = 4','2014-08-04 01:39:30','1'),(101,1,'question_option','11','INSERT:\nId = 11\nName = Nobody\nQuestion = 4','2014-08-04 01:39:34','1'),(102,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 03:08:14','1'),(103,20,'usr','1','LOGIN:a@c.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 03:08:43','1'),(104,1,'activity','3','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:09:17','1'),(105,1,'activity','1','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:09:23','1'),(106,1,'question','5','INSERT:\nId = 5\nName = Another Question\nDescription = \nActivity = 1\nCategory = 1','2014-08-04 03:09:55','1'),(107,1,'question_option','12','INSERT:\nId = 12\nName = YES\nQuestion = 5','2014-08-04 03:10:01','1'),(108,1,'question_option','13','INSERT:\nId = 13\nName = Maybe\nQuestion = 5','2014-08-04 03:10:04','1'),(109,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:10:39','1'),(110,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:10:47','1'),(111,1,'question','6','INSERT:\nId = 6\nName = Define ABC\nDescription = \nActivity = 2\nCategory = 1','2014-08-04 03:11:27','1'),(112,1,'question_option','14','INSERT:\nId = 14\nName = A\nQuestion = 6','2014-08-04 03:11:31','1'),(113,1,'question_option','15','INSERT:\nId = 15\nName = Big\nQuestion = 6','2014-08-04 03:11:35','1'),(114,1,'question_option','16','INSERT:\nId = 16\nName = Cat\nQuestion = 6','2014-08-04 03:11:38','1'),(115,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 03:12:04','1'),(116,1,'activity','3','UPDATE:\nActivity status = 2 -> 1','2014-08-04 03:12:23','1'),(117,1,'question','7','INSERT:\nId = 7\nName = Frances capital\nDescription = \nActivity = 3\nCategory = 1','2014-08-04 03:12:59','1'),(118,1,'question_option','17','INSERT:\nId = 17\nName = Paris\nQuestion = 7','2014-08-04 03:13:07','1'),(119,1,'question_option','18','INSERT:\nId = 18\nName = Lyon\nQuestion = 7','2014-08-04 03:13:12','1'),(120,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-04 13:26:31','1'),(121,1,'activity','1','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:28:09','1'),(122,1,'activity','3','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:28:13','1'),(123,1,'activity','1','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:28:52','1'),(124,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:28:57','1'),(125,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-04 13:29:08','1'),(126,1,'activity','3','UPDATE:\nActivity status = 2 -> 1','2014-08-04 13:29:13','1'),(127,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-05 00:18:19','1'),(128,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-05 00:19:31','1'),(129,1,'usr','22','INSERT:\n# = 22\nName = newUser\nEmail = a@something.com\nPassword = 827ccb0eea8a706c4c34a16891f84e7b\nLogin Count = 0\nLast Login = 1970-01-01 00:00:00\nCreated Date = 2014-08-05 00:20:27\nLast IP = 0.0.0.0','2014-08-05 00:20:53','1'),(130,1,'activity','3','UPDATE:\nActivity status = 1 -> 2','2014-08-05 00:22:30','1'),(131,1,'activity','4','INSERT:\nId = 4\nName = demo test 6\nSession = 1\nActivity type = 1\nActivity status = 1','2014-08-05 00:22:41','1'),(132,1,'question','8','INSERT:\nId = 8\nName = do you like stuff?\nDescription = \nActivity = 4\nCategory = 1','2014-08-05 00:22:59','1'),(133,1,'question_option','19','INSERT:\nId = 19\nName = YES\nQuestion = 8','2014-08-05 00:23:07','1'),(134,1,'question_option','20','INSERT:\nId = 20\nName = NO\nQuestion = 8','2014-08-05 00:23:11','1'),(135,22,'usr','1','LOGIN:a@something.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-05 00:23:36','1'),(136,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-05 00:24:40','1'),(137,1,'activity','4','UPDATE:\nActivity status = 1 -> 2','2014-08-05 00:26:36','1'),(138,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-05 00:26:41','1'),(139,22,'usr','1','LOGIN:a@something.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-05 00:27:02','1'),(140,1,'activity','2','UPDATE:\nActivity status = 1 -> 2','2014-08-05 00:27:38','1'),(141,1,'activity','3','UPDATE:\nActivity status = 2 -> 1','2014-08-05 00:27:47','1'),(142,4,'usr','1','LOGIN:a@a.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-11 13:33:41','1'),(143,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-11 13:37:03','1'),(144,1,'activity','1','UPDATE:','2014-08-11 13:41:53','1'),(145,1,'activity','2','UPDATE:','2014-08-11 13:41:57','1'),(146,1,'activity','3','UPDATE:','2014-08-11 13:42:00','1'),(147,1,'activity','4','UPDATE:','2014-08-11 13:42:03','1'),(148,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-11 13:57:25','1'),(149,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-11 14:21:32','1'),(150,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:31.0) Gecko/20100101 Firefox/31.0 ','2014-08-11 17:17:28','1'),(151,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:31.0) Gecko/20100101 Firefox/31.0 ','2014-08-11 17:18:36','1'),(152,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:01:06','1'),(153,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:01:47','1'),(154,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:35:31','1'),(155,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:38:58','1'),(156,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:39:06','1'),(157,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:55:30','1'),(158,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:55:43','1'),(159,1,'activity','1','UPDATE:\nActivity status = 2 -> 1','2014-08-13 01:56:18','1'),(160,1,'activity','2','UPDATE:\nActivity status = 2 -> 1','2014-08-13 01:56:22','1'),(161,1,'activity','4','UPDATE:\nActivity status = 2 -> 1','2014-08-13 01:56:28','1'),(162,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 01:59:01','1'),(163,1,'SQLCONSOLE','-1','update activity_type set activity_value=200','2014-08-13 02:00:18','SQL'),(164,1,'activity','4','UPDATE:\nName = demo test 6 -> complete? demo test 6\nActivity status = 1 -> 2','2014-08-13 02:16:09','1'),(165,1,'privilege','27','INSERT:\nID = 27\nName = [##completed_activity]\nAction = completed_activity/all*','2014-08-13 02:26:29','1'),(166,1,'completed_activity','1','INSERT:\nId = 1\nActivity date = 2014-8-12 00:00:00\nUser = 1\nActivity = 1','2014-08-13 02:28:12','1'),(167,1,'completed_activity','1','UPDATE:\nActivity date = 2014-08-12 00:00:00 -> 2014-8-12 00:00:00\nActivity = 1 -> 3','2014-08-13 02:30:24','1'),(168,1,'completed_activity','2','INSERT:\nId = 2\nActivity date = 2014-8-12 00:00:00\nUser = 1\nActivity = 2','2014-08-13 02:30:33','1'),(169,1,'usr','19','UPDATE:\nName = a -> abcde','2014-08-13 02:31:33','1'),(170,1,'completed_activity','1','UPDATE:\nActivity date = 2014-08-12 00:00:00 -> 2014-8-12 00:00:00\nUser = 1 -> 19','2014-08-13 02:31:43','1'),(171,1,'completed_activity','2','UPDATE:\nActivity date = 2014-08-12 00:00:00 -> 2014-8-12 00:00:00\nUser = 1 -> 19','2014-08-13 02:31:48','1'),(172,1,'SQLCONSOLE','-1','select activity_id, activity_date from completed_activity where usr_id=19','2014-08-13 02:32:58','SQL'),(173,1,'rank','1','UPDATE:\nName = Newbie -> #1','2014-08-13 03:50:51','1'),(174,1,'rank','2','UPDATE:\nName = Advanced Learner -> #2','2014-08-13 03:51:01','1'),(175,1,'SQLCONSOLE','-1','select * from usr','2014-08-13 03:51:28','SQL'),(176,1,'SQLCONSOLE','-1','select * from usr_status','2014-08-13 03:51:35','SQL'),(177,1,'SQLCONSOLE','-1','update usr_status set score = score + 5 where usr_id=19','2014-08-13 03:51:55','SQL'),(178,1,'SQLCONSOLE','-1','select * from usr_status','2014-08-13 03:52:01','SQL'),(179,1,'rank','3','INSERT:\nId = 3\nName = 3\nRank value = 3\nScore start = 21\nScore end = 1000','2014-08-13 08:21:16','1'),(180,1,'activity','6','INSERT:\nId = 6\nName = Spin Test\nActivity type = 4\nActivity status = 1','2014-08-13 08:30:18','1'),(181,1,'question','9','INSERT:\nId = 9\nName = SPin question 1\nDescription = \nActivity = 6\nCategory = 1','2014-08-13 08:30:33','1'),(182,1,'question','10','INSERT:\nId = 10\nName = Spin QUestion two\nDescription = \nActivity = 6\nCategory = 1','2014-08-13 08:30:41','1'),(183,1,'question','9','UPDATE:\nName = SPin question 1 -> Who founded Intel','2014-08-13 08:31:16','1'),(184,1,'question','10','UPDATE:\nName = Spin QUestion two -> Whos law is it','2014-08-13 08:31:28','1'),(185,1,'question_option','21','INSERT:\nId = 21\nName = Moore\nQuestion = 9','2014-08-13 08:31:36','1'),(186,1,'question_option','22','INSERT:\nId = 22\nName = Another\nQuestion = 9','2014-08-13 08:31:42','1'),(187,1,'question_option','23','INSERT:\nId = 23\nName = Moore\nQuestion = 10','2014-08-13 08:31:53','1'),(188,1,'question_option','24','INSERT:\nId = 24\nName = This guy\nQuestion = 10','2014-08-13 08:31:58','1'),(189,1,'question_option','25','INSERT:\nId = 25\nName = That guy\nQuestion = 10','2014-08-13 08:32:03','1'),(190,1,'question','10','UPDATE:\nName = Whos law is it -> Whos law is it test long text here\nDescription =  -> long text here','2014-08-13 09:30:32','1'),(191,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 09:59:21','1'),(192,1,'privilege','25','UPDATE:\nName = Activities por Session -> Activities by Session','2014-08-13 09:59:44','1'),(193,1,'activity','7','INSERT:\nId = 7\nName = SHUFFLE TEST\nActivity type = 2\nActivity status = 1','2014-08-13 10:00:04','1'),(194,1,'usr','1','LOGIN:root IP:192.168.0.24 USER_AGENT:Mozilla/5.0 (Linux U Android 4.2.2 en-us GT-P5210 Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30 ','2014-08-13 10:03:15','1'),(195,19,'usr','1','LOGIN:a@b.com IP:192.168.0.24 USER_AGENT:Mozilla/5.0 (Linux U Android 4.2.2 en-us GT-P5210 Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30 ','2014-08-13 10:03:39','1'),(196,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 11:20:10','1'),(197,1,'question_option','1','UPDATE:\nis Correct = N -> Y','2014-08-13 11:20:49','1'),(198,1,'question','2','UPDATE:\nName = question 3 -> Isnt Intel a great company?','2014-08-13 11:21:16','1'),(199,1,'question_option','3','UPDATE:\nName = opcion1 -> yes it is\nis Correct = N -> Y','2014-08-13 11:21:24','1'),(200,1,'question_option','4','UPDATE:\nName = opcion2 -> not quite','2014-08-13 11:21:29','1'),(201,1,'question','5','UPDATE:\nName = Another Question -> Do you like bananas?','2014-08-13 11:21:45','1'),(202,1,'question_option','12','UPDATE:\nName = YES -> YES I like bananas\nis Correct = N -> Y','2014-08-13 11:21:53','1'),(203,1,'question_option','26','INSERT:\nId = 26\nName = never liked bananas\nQuestion = 5\nis Correct = N','2014-08-13 11:22:02','1'),(204,1,'question_option','27','INSERT:\nId = 27\nName = I hate bananas\nQuestion = 5\nis Correct = N','2014-08-13 11:22:09','1'),(205,1,'category','1','UPDATE:\nName = Category1 -> Topic 1','2014-08-13 11:22:29','1'),(206,1,'category','2','UPDATE:\nName = Category2 -> Topic 2','2014-08-13 11:22:34','1'),(207,1,'question','9','UPDATE:\nCategory = 1 -> 2','2014-08-13 11:23:10','1'),(208,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 12:39:49','1'),(209,1,'question','1','UPDATE:\nDescription =  -> [http://i.imgur.com/es5K9Lv.gif]','2014-08-13 12:40:50','1'),(210,1,'question','2','UPDATE:\nDescription =  -> Question description goes here','2014-08-13 12:44:09','1'),(211,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 13:12:43','1'),(212,19,'usr','1','LOGIN:a@b.com IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36 ','2014-08-13 13:12:58','1');
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
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;
INSERT INTO `privilege` VALUES (1,'[##usr]','usr/all*'),(2,'[##role]','role/all*'),(3,'[##privilege]','privilege/all*'),(4,'[##usr2role]','usr2role/all*'),(5,'[##role2priv]','role2priv/all*'),(6,'[##event]','log/read_only*'),(7,'Ayuda','help/help'),(8,'Editar Perfil','usr/edit_profile*'),(9,'DBAdmin','edit/db_manager'),(10,'Activities','activity/all*'),(11,'Activity status','activity_status/read_only*'),(12,'Activity types','activity_type/read_only*'),(13,'Answers','answer/all*'),(14,'Contents','content/all*'),(15,'Content types','content_type/all*'),(16,'Questions','question/all*'),(17,'Question options','question_option/all*'),(18,'Ranks','rank/all*'),(19,'Sessions','session/all*'),(20,'Users in Session','usr2session/all*'),(21,'Users Status','usr_status/read_only*'),(22,'categories','category/all*'),(23,'Categories','category/all*'),(24,'FrontEnd','usr2session/r_*'),(25,'Activities by Session','activity2session/all*'),(26,'Completed Activities','completed_activity/all*'),(27,'[##completed_activity]','completed_activity/all*');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (1,'Do you like Stuff?','[http://i.imgur.com/es5K9Lv.gif]',1,1),(2,'Isnt Intel a great company?','Question description goes here',1,1),(3,'Define SOP','',2,1),(4,'Who won?','',3,1),(5,'Do you like bananas?','',1,1),(6,'Define ABC','',2,1),(7,'Frances capital','',3,1),(8,'do you like stuff?','',4,1),(9,'Who founded Intel','',6,2),(10,'Whos law is it test long text here','long text here',6,1);
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
  `is_correct` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `fk_question_option_question1_idx` (`question_id`),
  CONSTRAINT `fk_question_option_question1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_option`
--

LOCK TABLES `question_option` WRITE;
/*!40000 ALTER TABLE `question_option` DISABLE KEYS */;
INSERT INTO `question_option` VALUES (1,'YES',1,'Y'),(2,'NO',1,'N'),(3,'yes it is',2,'Y'),(4,'not quite',2,'N'),(5,'First Term',3,'N'),(6,'Second Term',3,'N'),(7,'Third Term',3,'N'),(8,'Someone',4,'N'),(9,'The Winner',4,'N'),(10,'The Loser',4,'N'),(11,'Nobody',4,'N'),(12,'YES I like bananas',5,'Y'),(13,'Maybe',5,'N'),(14,'A',6,'N'),(15,'Big',6,'N'),(16,'Cat',6,'N'),(17,'Paris',7,'N'),(18,'Lyon',7,'N'),(19,'YES',8,'N'),(20,'NO',8,'N'),(21,'Moore',9,'N'),(22,'Another',9,'N'),(23,'Moore',10,'N'),(24,'This guy',10,'N'),(25,'That guy',10,'N'),(26,'never liked bananas',5,'N'),(27,'I hate bananas',5,'N');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank`
--

LOCK TABLES `rank` WRITE;
/*!40000 ALTER TABLE `rank` DISABLE KEYS */;
INSERT INTO `rank` VALUES (1,'#1',1,0,10),(2,'#2',2,11,20),(3,'3',3,21,1000);
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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role2priv`
--

LOCK TABLES `role2priv` WRITE;
/*!40000 ALTER TABLE `role2priv` DISABLE KEYS */;
INSERT INTO `role2priv` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(26,2,11),(27,2,12),(13,2,13),(30,2,27),(16,2,16),(17,2,17),(18,2,18),(19,2,19),(20,2,20),(28,2,21),(22,2,23),(25,4,24),(31,2,25);
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr`
--

LOCK TABLES `usr` WRITE;
/*!40000 ALTER TABLE `usr` DISABLE KEYS */;
INSERT INTO `usr` VALUES (1,'Administrator','root','31c23a230051702de27d954c3f4d25f9',29,'2014-08-13 13:12:43','2000-01-01 00:00:00','1'),(2,'onboarding_admin','onboarding_admin','3e47b75000b0924b6c9ba5759a7cf15d',0,'2000-01-01 00:00:00','2014-07-31 14:14:30','1'),(3,'demo','demo','fe01ce2a7fbac8fafaed7c982a04e229',1,'2014-08-01 16:23:54','2014-07-31 14:17:52','127.0.0.1'),(4,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',1,'2014-08-11 13:33:40','2014-08-02 14:56:03','1'),(5,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:57:38','::1'),(6,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:57:42','::1'),(7,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:58:40','::1'),(8,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:58:43','::1'),(9,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 14:59:35','::1'),(10,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:00:33','::1'),(11,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:06','::1'),(12,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:16','::1'),(13,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:01:30','::1'),(14,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:03:03','::1'),(15,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:03:15','::1'),(16,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:04:16','::1'),(17,'a','a@a.com','0cc175b9c0f1b6a831c399e269772661',0,'2000-01-01 00:00:00','2014-08-02 15:05:16','::1'),(18,'hola','hola@algo.com','4d186321c1a7f0f354b297e8914ab240',0,'2000-01-01 00:00:00','2014-08-02 15:07:35','::1'),(19,'abcde','a@b.com','0cc175b9c0f1b6a831c399e269772661',16,'2014-08-13 13:12:58','2014-08-02 15:16:37','1'),(20,'a','a@c.com','0cc175b9c0f1b6a831c399e269772661',3,'2014-08-04 03:08:43','2014-08-02 16:29:40','1'),(21,'prueba','prueba@algo.com','0cc175b9c0f1b6a831c399e269772661',2,'2014-08-02 23:17:41','2014-08-02 23:11:48','1'),(22,'newUser','a@something.com','827ccb0eea8a706c4c34a16891f84e7b',2,'2014-08-05 00:27:02','2014-08-05 00:20:27','1');
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2role`
--

LOCK TABLES `usr2role` WRITE;
/*!40000 ALTER TABLE `usr2role` DISABLE KEYS */;
INSERT INTO `usr2role` VALUES (1,1,1),(2,2,2),(3,1,2),(4,3,3),(5,1,4),(6,3,4),(7,4,4),(8,5,4),(9,6,4),(10,7,4),(11,8,4),(12,9,4),(13,10,4),(14,11,4),(15,12,4),(16,13,4),(17,14,4),(18,15,4),(19,16,4),(20,17,4),(21,18,4),(26,19,4),(23,20,4),(24,21,4),(25,22,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2session`
--

LOCK TABLES `usr2session` WRITE;
/*!40000 ALTER TABLE `usr2session` DISABLE KEYS */;
INSERT INTO `usr2session` VALUES (1,1,1),(2,4,2),(3,5,2),(4,6,2),(5,7,2),(6,8,2),(7,9,2),(8,10,2),(9,11,2),(10,12,2),(11,13,2),(12,14,2),(13,15,2),(14,16,2),(15,17,2),(16,18,3),(18,20,1),(19,21,1),(20,22,1),(21,19,1);
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
INSERT INTO `usr_status` VALUES (1,1,2,1),(2,3,15,2),(16,17,0,1),(17,18,0,1),(18,19,1805,3),(19,20,0,1),(20,21,0,1);
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

-- Dump completed on 2014-08-13  8:19:38
