-- MariaDB dump 10.19  Distrib 10.5.12-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dressapi-test
-- ------------------------------------------------------
-- Server version	10.5.12-MariaDB-0+deb11u1

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

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) DEFAULT NULL COMMENT 'Role of the user',
  `id_module` int(11) DEFAULT NULL COMMENT 'contains the index of module or table name managed by the base module',
  `can_read` enum('YES','NO') DEFAULT 'NO',
  `can_insert` enum('YES','NO') DEFAULT 'NO',
  `can_update` enum('YES','NO') DEFAULT 'NO',
  `can_delete` enum('YES','NO') DEFAULT 'NO',
  `only_owner` enum('NO','YES') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`),
  KEY `id_role` (`id_role`),
  CONSTRAINT `acl_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acl_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl`
--

LOCK TABLES `acl` WRITE;
/*!40000 ALTER TABLE `acl` DISABLE KEYS */;
INSERT INTO `acl` VALUES (1,1,NULL,'YES','YES','YES','YES','NO'),(2,NULL,1,'YES','YES','YES','YES','YES'),(4,NULL,2,'YES','NO','NO','NO','NO'),(5,101,2,'YES','YES','YES','YES','NO');
/*!40000 ALTER TABLE `acl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `val` varchar(250) NOT NULL,
  `description` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'HOMEPAGE_RELATIVE_URL','/page/1','The homepage relative url'),(2,'WEBSITE_OWNER','DressApi','');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `surname` varchar(80) NOT NULL,
  `address` varchar(160) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `city` varchar(80) NOT NULL,
  `state` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,'Joe','Sample','Via 1 Febbraio','15005','Rome','Italy','jxsample@userdressapi.com'),(2,'Michael','Franks','The art of the tea street, 1046','01975','Los Angeles','California','mfranks@userdressapi.com'),(3,'Pasquale','Tufano','Via Roccavione, 216','10047','Turin','Italy','ptufano@userdressapi.com'),(21,'Joe','Sample','Via 1 Febbraio','15005','Turin','Italy','jsample@userdressapi.com');
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document`
--

LOCK TABLES `document` WRITE;
/*!40000 ALTER TABLE `document` DISABLE KEYS */;
INSERT INTO `document` VALUES (3,'Electro','','jpg','1636638666_img_20181130_180133.jpg','https://dressapi.com','2021-11-11 14:52:55',0);
/*!40000 ALTER TABLE `document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `abstract` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `date` date NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `img` varchar(80) NOT NULL,
  `site` varchar(120) NOT NULL,
  `url` varchar(300) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faq`
--

DROP TABLE IF EXISTS `faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(300) NOT NULL,
  `answer` text NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `priority` int(11) NOT NULL DEFAULT 1000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faq`
--

LOCK TABLES `faq` WRITE;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `tablename` varchar(40) NOT NULL,
  `tablefilter` varchar(200) NOT NULL,
  `title` varchar(65) NOT NULL,
  `description` varchar(160) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'Sign','user','','Sign','Login, Logout, Subscription, Unsubscription'),(2,'Pages','node','id_nodetype=11','Page Module',''),(3,'News','news','','News',''),(4,'Events','event','','Events',''),(5,'Faq','faq','','Faq',''),(6,'Documents','document','','Documents','List of Documents');
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `abstract` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `img` varchar(80) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (3,'New Titlefh 56','sfhj','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.','yes','public','1636638573_img_20181130_180150.jpg','2021-11-11 14:56:13',0);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_nodetype` int(11) NOT NULL DEFAULT 11,
  `label` varchar(40) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `description` varchar(160) NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_nodetype` (`id_nodetype`),
  CONSTRAINT `node_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node`
--

LOCK TABLES `node` WRITE;
/*!40000 ALTER TABLE `node` DISABLE KEYS */;
INSERT INTO `node` VALUES (1,11,'HOME','Welcome to DressApi: the new ORM REST API','The name \"Dress\" means it \"dress\" up your database, substantially it provides a quick REST API, to your db schema. \nORM means Object-relational mapping and DressApi maps your database dynamically. Although it is structured as an MVC (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads it from the DB. \nThe most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.','Example of use DressApi','yes','draft','2021-01-13',2),(2,1,'Experience','DressApi is new but contains long experience inside','I have a very long experience in programming with various languages, for the web I have always preferred PHP.\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.','','no','reserved','2021-01-15',101),(3,1,'Test','title test','I have a very long experience in programming with various languages, for the web I have always preferred PHP.\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.','Title Test OK!','yes','reserved','2021-01-15',2);
/*!40000 ALTER TABLE `node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nodetype`
--

DROP TABLE IF EXISTS `nodetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodetype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodetype`
--

LOCK TABLES `nodetype` WRITE;
/*!40000 ALTER TABLE `nodetype` DISABLE KEYS */;
INSERT INTO `nodetype` VALUES (1,'container',''),(2,'menu',''),(3,'link',''),(4,'file',''),(11,'page',''),(12,'article',''),(13,'news',''),(14,'event',''),(15,'comment','');
/*!40000 ALTER TABLE `nodetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `description` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Administrator','Can read - All permissions for anonymous is valid for all user'),(2,'Anonymous','Can read - All permissions for anonymous is valid for all user'),(101,'Editor','Full power: can Delete or publish a post'),(102,'Writer','Can write a post'),(103,'Commentator','Can write a comment');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route`
--

DROP TABLE IF EXISTS `route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origin_path` varchar(512) NOT NULL,
  `destination_path` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route`
--

LOCK TABLES `route` WRITE;
/*!40000 ALTER TABLE `route` DISABLE KEYS */;
INSERT INTO `route` VALUES (1,'login','sign/login-form'),(2,'logout','sign/logout'),(3,'','pages/1');
/*!40000 ALTER TABLE `route` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `id_contact` int(11) DEFAULT NULL,
  `domain` varchar(60) DEFAULT 'local',
  `nickname` varchar(60) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pwd` varchar(120) NOT NULL DEFAULT '-HsjK673Hf@fhs',
  `status` enum('Subscribed','Verified','Refused') NOT NULL DEFAULT 'Subscribed',
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_contact`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Administrator',NULL,'local','admin','admin','51c3f5f5d8a8830bc5d8b7ebcb5717dfb4892d4766c2a77d','Verified'),(2,'Anonymous',NULL,'local','Anonymous','','','Verified'),(101,'Joe Sample',1,'local','Big Joe','jsample','1d628e0dd73490f28e5717bb2564f4760a6caf3922051f3a','Verified'),(102,'Michael Franks',2,'local','Mr Blue','mfranks','292498b56f83154fc913b173e51ca43c898cc35944280aaa','Verified'),(103,'Pasquale Tufano',3,'local','Pask','ptufano','9444701720f616c4a8985f7d4022c1507389a33208c9afb2','Verified'),(115,'J.Sample',21,'DressApi.com','Joe','Joe','119dcb517fedfaba6f41824968610987702bf221b8e6afdd','Verified');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_role` (`id_role`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1,1),(11,2,2),(101,101,101),(102,102,102),(103,103,103);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-03  2:03:18
