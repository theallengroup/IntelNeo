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
  CONSTRAINT `fk_activity_session1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_type1` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_status1` FOREIGN KEY (`activity_status_id`) REFERENCES `activity_status` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
INSERT INTO `activity` VALUES (1,'Example Poll 1',1,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_type`
--

LOCK TABLES `activity_type` WRITE;
/*!40000 ALTER TABLE `activity_type` DISABLE KEYS */;
INSERT INTO `activity_type` VALUES (1,'Poll','',1),(2,'Quiz','',1),(3,'Wager Quiz','',1);
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
  CONSTRAINT `fk_video_session1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_content_type1` FOREIGN KEY (`content_type_id`) REFERENCES `content_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,'','','',1,1),(2,'Video1','','https://www.youtube.com/watch?v=dQw4w9WgXcQ',1,2);
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
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,1,'none','-1','db created','2014-07-31 09:14:29','app'),(2,1,'usr','1','LOGIN:root IP:::1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36 ','2014-07-31 14:17:45','1'),(3,1,'usr','3','INSERT:\n# = 3\nNombre = demo\nCorreo Electr&oacute;nico = demo\nContrase&ntilde;a = fe01ce2a7fbac8fafaed7c982a04e229\n# de Accesos = 0\n&Uacute;ltimo Acceso = 1970-01-01 00:00:00\nFecha de Creaci&oacute;n = 2014-07-31 14:17:52\nUltima IP = 0.0.0.0','2014-07-31 14:17:59','1'),(4,1,'session','1','INSERT:\nId = 1\nName = Session1','2014-07-31 14:37:33','1'),(5,1,'activity_status','1','INSERT:\nId = 1\nName = READY\nActivity value = 0','2014-07-31 14:37:47','1'),(6,1,'activity_status','2','INSERT:\nId = 2\nName = COMPLETE\nActivity value = 0','2014-07-31 14:38:37','1'),(7,1,'activity_type','1','INSERT:\nId = 1\nName = Poll\nDescription = \nActivity Value = 1','2014-07-31 14:41:10','1'),(8,1,'activity_type','2','INSERT:\nId = 2\nName = Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:15','1'),(9,1,'activity_type','3','INSERT:\nId = 3\nName = Wager Quiz\nDescription = \nActivity Value = 1','2014-07-31 14:41:20','1'),(10,1,'activity','1','INSERT:\nId = 1\nName = Example Poll 1\nSession = 1\nActivity type = 1\nActivity status = 1','2014-07-31 14:42:33','1'),(11,1,'question','1','INSERT:\nId = 1\nName = Do you like Stuff?\nDescription = \nActivity = 1','2014-07-31 14:43:18','1'),(12,1,'question_option','1','INSERT:\nId = 1\nName = YES\nQuestion = 1','2014-07-31 14:43:25','1'),(13,1,'question_option','2','INSERT:\nId = 2\nName = NO\nQuestion = 1','2014-07-31 14:43:31','1'),(14,1,'privilege','23','INSERT:\nID = 23\nName = Categories\nAction = category/all*','2014-07-31 14:45:21','1'),(15,1,'category','1','INSERT:\nId = 1\nName = Category1','2014-07-31 14:45:29','1'),(16,1,'category','2','INSERT:\nId = 2\nName = Category2','2014-07-31 14:45:34','1'),(17,1,'rank','1','INSERT:\nId = 1\nName = Newbie\nRank value = 1\nScore start = 0\nScore end = 10','2014-07-31 14:46:32','1'),(18,1,'rank','2','INSERT:\nId = 2\nName = Advanced Learner\nRank value = 2\nScore start = 11\nScore end = 20','2014-07-31 14:46:55','1'),(19,1,'usr2session','1','INSERT:\nId = 1\nUser = 1\nSession = 1','2014-07-31 14:49:56','1'),(20,1,'usr_status','1','INSERT:\nId = 1\nUser = 1\nScore = 1\nRank = 1','2014-07-31 14:50:09','1'),(21,1,'usr_status','2','INSERT:\nId = 2\nUser = 3\nScore = 15\nRank = 2','2014-07-31 14:50:21','1'),(22,1,'content_type','1','INSERT:\nId = 1\nName = Text','2014-07-31 14:51:14','1'),(23,1,'content','1','INSERT:\nId = 1\nName = \nBody = \nUrl = \nSession = 1\nContent type = 1','2014-07-31 14:51:16','1'),(24,1,'content_type','2','INSERT:\nId = 2\nName = Video','2014-07-31 14:51:24','1'),(25,1,'content','2','INSERT:\nId = 2\nName = Video1\nBody = \nUrl = https://www.youtube.com/watch?v=dQw4w9WgXcQ\nSession = 1\nContent type = 2','2014-07-31 14:52:07','1'),(26,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:16:49','1'),(27,1,'question','2','INSERT:\nId = 2\nName = question 3\nDescription = \nActivity = 1\nCategory = 1','2014-08-01 16:20:21','1'),(28,1,'question_option','3','INSERT:\nId = 3\nName = opcion1\nQuestion = 2','2014-08-01 16:20:27','1'),(29,1,'question_option','4','INSERT:\nId = 4\nName = opcion2\nQuestion = 2','2014-08-01 16:20:33','1'),(30,1,'role','3','INSERT:\nID = 3\nName = Content','2014-08-01 16:23:49','1'),(31,3,'usr','1','LOGIN:demo IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:23:54','1'),(32,1,'usr','1','LOGIN:root IP:127.0.0.1 USER_AGENT:Mozilla/5.0 (Macintosh Intel Mac OS X 10.8 rv:30.0) Gecko/20100101 Firefox/30.0 ','2014-08-01 16:25:07','1');
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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;
INSERT INTO `privilege` VALUES (1,'[##usr]','usr/all*'),(2,'[##role]','role/all*'),(3,'[##privilege]','privilege/all*'),(4,'[##usr2role]','usr2role/all*'),(5,'[##role2priv]','role2priv/all*'),(6,'[##event]','log/read_only*'),(7,'Ayuda','help/help'),(8,'Editar Perfil','usr/edit_profile*'),(9,'DBAdmin','edit/db_manager'),(10,'Activities','activity/all*'),(11,'Activity status','activity_status/all*'),(12,'Activity types','activity_type/all*'),(13,'Answers','answer/all*'),(14,'Contents','content/all*'),(15,'Content types','content_type/all*'),(16,'Questions','question/all*'),(17,'Question options','question_option/all*'),(18,'Ranks','rank/all*'),(19,'Sessions','session/all*'),(20,'Users in Session','usr2session/all*'),(21,'Users Status','usr_status/all*'),(22,'categories','category/all*'),(23,'Categories','category/all*');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (1,'Do you like Stuff?','',1,1),(2,'question 3','',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_option`
--

LOCK TABLES `question_option` WRITE;
/*!40000 ALTER TABLE `question_option` DISABLE KEYS */;
INSERT INTO `question_option` VALUES (1,'YES',1),(2,'NO',1),(3,'opcion1',2),(4,'opcion2',2);
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Administrator'),(2,'onboarding_admin'),(3,'Content');
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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role2priv`
--

LOCK TABLES `role2priv` WRITE;
/*!40000 ALTER TABLE `role2priv` DISABLE KEYS */;
INSERT INTO `role2priv` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(11,2,11),(12,2,12),(13,2,13),(14,2,14),(15,2,15),(16,2,16),(17,2,17),(18,2,18),(19,2,19),(20,2,20),(21,2,21),(22,2,23),(23,3,14);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES (1,'Session1');
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr`
--

LOCK TABLES `usr` WRITE;
/*!40000 ALTER TABLE `usr` DISABLE KEYS */;
INSERT INTO `usr` VALUES (1,'Administrator','root','31c23a230051702de27d954c3f4d25f9',3,'2014-08-01 16:25:07','2000-01-01 00:00:00','127.0.0.1'),(2,'onboarding_admin','onboarding_admin','3e47b75000b0924b6c9ba5759a7cf15d',0,'2000-01-01 00:00:00','2014-07-31 14:14:30','1'),(3,'demo','demo','fe01ce2a7fbac8fafaed7c982a04e229',1,'2014-08-01 16:23:54','2014-07-31 14:17:52','127.0.0.1');
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2role`
--

LOCK TABLES `usr2role` WRITE;
/*!40000 ALTER TABLE `usr2role` DISABLE KEYS */;
INSERT INTO `usr2role` VALUES (1,1,1),(2,2,2),(3,1,2),(4,3,3);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr2session`
--

LOCK TABLES `usr2session` WRITE;
/*!40000 ALTER TABLE `usr2session` DISABLE KEYS */;
INSERT INTO `usr2session` VALUES (1,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_status`
--

LOCK TABLES `usr_status` WRITE;
/*!40000 ALTER TABLE `usr_status` DISABLE KEYS */;
INSERT INTO `usr_status` VALUES (1,1,1,1),(2,3,15,2);
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

-- Dump completed on 2014-08-01 13:25:36
