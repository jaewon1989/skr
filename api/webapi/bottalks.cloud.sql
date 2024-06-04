-- MySQL dump 10.13  Distrib 5.6.46, for Linux (x86_64)
--
-- Host: localhost    Database: syscloud2
-- ------------------------------------------------------
-- Server version	5.6.46-log

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
-- Table structure for table `beta_tester`
--

DROP TABLE IF EXISTS `beta_tester`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `beta_tester` (
  `uid` int(11) NOT NULL,
  `site` int(11) NOT NULL DEFAULT '0',
  `upjong` varchar(45) NOT NULL DEFAULT '',
  `damdang` varchar(30) NOT NULL DEFAULT '',
  `comName` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `tel` varchar(20) NOT NULL DEFAULT '',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `email` (`email`),
  KEY `damdang` (`damdang`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beta_tester`
--

LOCK TABLES `beta_tester` WRITE;
/*!40000 ALTER TABLE `beta_tester` DISABLE KEYS */;
/*!40000 ALTER TABLE `beta_tester` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bg_naverIn`
--

DROP TABLE IF EXISTS `bg_naverIn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bg_naverIn` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) NOT NULL DEFAULT '',
  `result_title` varchar(250) NOT NULL DEFAULT '',
  `result_des` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='지식인 데이타 ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bg_naverIn`
--

LOCK TABLES `bg_naverIn` WRITE;
/*!40000 ALTER TABLE `bg_naverIn` DISABLE KEYS */;
/*!40000 ALTER TABLE `bg_naverIn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legacy_products`
--

DROP TABLE IF EXISTS `legacy_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legacy_products` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `price` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legacy_products`
--

LOCK TABLES `legacy_products` WRITE;
/*!40000 ALTER TABLE `legacy_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `legacy_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poly_course`
--

DROP TABLE IF EXISTS `poly_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poly_course` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '2' COMMENT '2 : 학과 \n1 : 과 ',
  `isson` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `summary` varchar(250) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `upload` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='과정/학과 관리';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poly_course`
--

LOCK TABLES `poly_course` WRITE;
/*!40000 ALTER TABLE `poly_course` DISABLE KEYS */;
/*!40000 ALTER TABLE `poly_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_data`
--

DROP TABLE IF EXISTS `rb_bbs_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_data` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `gid` double(11,2) NOT NULL DEFAULT '0.00',
  `bbs` int(11) NOT NULL DEFAULT '0',
  `bbsid` varchar(30) NOT NULL DEFAULT '',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `parentmbr` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `notice` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `nic` varchar(50) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(16) NOT NULL DEFAULT '',
  `pw` varchar(50) NOT NULL DEFAULT '',
  `category` varchar(100) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `tag` varchar(200) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `comment` int(11) NOT NULL DEFAULT '0',
  `oneline` int(11) NOT NULL DEFAULT '0',
  `trackback` int(11) NOT NULL DEFAULT '0',
  `score1` int(11) NOT NULL DEFAULT '0',
  `score2` int(11) NOT NULL DEFAULT '0',
  `singo` int(11) NOT NULL DEFAULT '0',
  `point1` int(11) NOT NULL DEFAULT '0',
  `point2` int(11) NOT NULL DEFAULT '0',
  `point3` int(11) NOT NULL DEFAULT '0',
  `point4` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `d_comment` varchar(14) NOT NULL DEFAULT '',
  `d_trackback` varchar(14) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `ip` varchar(25) NOT NULL DEFAULT '',
  `agent` varchar(150) NOT NULL DEFAULT '',
  `sns` varchar(100) NOT NULL DEFAULT '',
  `adddata` text NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `featured_img` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `gid` (`gid`),
  KEY `bbs` (`bbs`),
  KEY `bbsid` (`bbsid`),
  KEY `parentmbr` (`parentmbr`),
  KEY `display` (`display`),
  KEY `notice` (`notice`),
  KEY `mbruid` (`mbruid`),
  KEY `category` (`category`),
  KEY `subject` (`subject`),
  KEY `tag` (`tag`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_data`
--

LOCK TABLES `rb_bbs_data` WRITE;
/*!40000 ALTER TABLE `rb_bbs_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_day`
--

DROP TABLE IF EXISTS `rb_bbs_day`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_day` (
  `date` char(8) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `bbs` int(11) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  KEY `date` (`date`),
  KEY `site` (`site`),
  KEY `bbs` (`bbs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_day`
--

LOCK TABLES `rb_bbs_day` WRITE;
/*!40000 ALTER TABLE `rb_bbs_day` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_day` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_index`
--

DROP TABLE IF EXISTS `rb_bbs_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_index` (
  `site` int(11) NOT NULL DEFAULT '0',
  `notice` tinyint(4) NOT NULL DEFAULT '0',
  `bbs` int(11) NOT NULL DEFAULT '0',
  `gid` double(11,2) NOT NULL DEFAULT '0.00',
  KEY `site` (`site`),
  KEY `notice` (`notice`),
  KEY `bbs` (`bbs`,`gid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_index`
--

LOCK TABLES `rb_bbs_index` WRITE;
/*!40000 ALTER TABLE `rb_bbs_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_list`
--

DROP TABLE IF EXISTS `rb_bbs_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_list` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `category` text NOT NULL,
  `num_r` int(11) NOT NULL DEFAULT '0',
  `d_last` varchar(14) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `imghead` varchar(100) NOT NULL DEFAULT '',
  `imgfoot` varchar(100) NOT NULL DEFAULT '',
  `puthead` varchar(20) NOT NULL DEFAULT '',
  `putfoot` varchar(20) NOT NULL DEFAULT '',
  `addinfo` text NOT NULL,
  `writecode` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_list`
--

LOCK TABLES `rb_bbs_list` WRITE;
/*!40000 ALTER TABLE `rb_bbs_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_month`
--

DROP TABLE IF EXISTS `rb_bbs_month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_month` (
  `date` char(6) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `bbs` int(11) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  KEY `date` (`date`),
  KEY `site` (`site`),
  KEY `bbs` (`bbs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_month`
--

LOCK TABLES `rb_bbs_month` WRITE;
/*!40000 ALTER TABLE `rb_bbs_month` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_month` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_upload`
--

DROP TABLE IF EXISTS `rb_bbs_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_upload` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `tmpcode` varchar(20) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `ext` varchar(4) NOT NULL DEFAULT '0',
  `fserver` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(150) NOT NULL DEFAULT '',
  `folder` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `tmpname` varchar(100) NOT NULL DEFAULT '',
  `thumbname` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `caption` text NOT NULL,
  `down` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `cync` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `tmpcode` (`tmpcode`),
  KEY `site` (`site`),
  KEY `mbruid` (`mbruid`),
  KEY `type` (`type`),
  KEY `ext` (`ext`),
  KEY `name` (`name`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_upload`
--

LOCK TABLES `rb_bbs_upload` WRITE;
/*!40000 ALTER TABLE `rb_bbs_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_bbs_xtra`
--

DROP TABLE IF EXISTS `rb_bbs_xtra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_bbs_xtra` (
  `parent` int(11) NOT NULL DEFAULT '0',
  `site` int(11) NOT NULL DEFAULT '0',
  `bbs` int(11) NOT NULL DEFAULT '0',
  `down` text NOT NULL,
  `score1` text NOT NULL,
  `score2` text NOT NULL,
  `singo` text NOT NULL,
  KEY `parent` (`parent`),
  KEY `site` (`site`),
  KEY `bbs` (`bbs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_bbs_xtra`
--

LOCK TABLES `rb_bbs_xtra` WRITE;
/*!40000 ALTER TABLE `rb_bbs_xtra` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_bbs_xtra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_added`
--

DROP TABLE IF EXISTS `rb_chatbot_added`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_added` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `botuid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `botuid` (`botuid`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_added`
--

LOCK TABLES `rb_chatbot_added` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_added` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_added` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_addrBot`
--

DROP TABLE IF EXISTS `rb_chatbot_addrBot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_addrBot` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `bot` int(11) NOT NULL DEFAULT '0',
  `access_token` VARCHAR(255) NULL DEFAULT '',
  `room_token` VARCHAR(50) NULL DEFAULT '',
  `context` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `bot` (`bot`),
  KEY `access_token` (`access_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_addrBot`
--

LOCK TABLES `rb_chatbot_addrBot` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_addrBot` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_addrBot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_apiList`
--

DROP TABLE IF EXISTS `rb_chatbot_apiList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_apiList` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `type` char(1) NOT NULL DEFAULT 'S',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botId` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(150) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_apiList`
--

LOCK TABLES `rb_chatbot_apiList` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_apiList` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_apiList` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_apiReq`
--

DROP TABLE IF EXISTS `rb_chatbot_apiReq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_apiReq` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `base_path` varchar(200) NOT NULL DEFAULT '',
  `method` varchar(10) NOT NULL DEFAULT '',
  `statusCode` varchar(10) NOT NULL DEFAULT '0',
  `bodyType` varchar(45) NOT NULL DEFAULT 'text',
  PRIMARY KEY (`uid`),
  KEY `api` (`api`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_apiReq`
--

LOCK TABLES `rb_chatbot_apiReq` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_apiReq` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_apiReq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_apiReqParam`
--

DROP TABLE IF EXISTS `rb_chatbot_apiReqParam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_apiReqParam` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT '0',
  `req` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `position` varchar(10) NOT NULL DEFAULT '',
  `val_type` varchar(10) NOT NULL DEFAULT '',
  `param_type` varchar(10) NOT NULL DEFAULT '',
  `length` varchar(10) NOT NULL DEFAULT '',
  `text_val` text NOT NULL,
  `varchar_val` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `api` (`api`),
  KEY `req` (`req`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_apiReqParam`
--

LOCK TABLES `rb_chatbot_apiReqParam` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_apiReqParam` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_apiReqParam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_apiSettings`
--

DROP TABLE IF EXISTS `rb_chatbot_apiSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_apiSettings` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `api` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `api` (`api`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_apiSettings`
--

LOCK TABLES `rb_chatbot_apiSettings` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_apiSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_apiSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_blackList`
--

DROP TABLE IF EXISTS `rb_chatbot_blackList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_blackList` (
    `uid` int AUTO_INCREMENT
    PRIMARY KEY,
    `blackList` varchar(200) NULL COMMENT '블랙리스트 설정값',
    `bot` int default 0 NULL,
    `d_regis` varchar(14) DEFAULT '' NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_blackList`
--

LOCK TABLES `rb_chatbot_blackList` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_blackList` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_blackList` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_bot`
--

DROP TABLE IF EXISTS `rb_chatbot_bot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_bot` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `bottype` varchar(10) NOT NULL DEFAULT 'chat',
  `role` varchar(10) NOT NULL DEFAULT 'bot' COMMENT '역할 : bot or topic',
  `is_temp` tinyint(4) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : 내부에서 build 한것\n2 : 외부 봇 등록한 것',
  `auth` tinyint(4) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `induCat` varchar(20) NOT NULL DEFAULT '',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL DEFAULT '',
  `service` varchar(250) NOT NULL DEFAULT '',
  `intro` varchar(300) NOT NULL DEFAULT '',
  `website` varchar(100) NOT NULL DEFAULT '',
  `boturl` varchar(200) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(20) NOT NULL DEFAULT '',
  `callno` varchar(100) NOT NULL DEFAULT '',
  `userno` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `tag` varchar(300) NOT NULL DEFAULT '',
  `lang` varchar(3) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `report` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `avatar` varchar(200) NOT NULL DEFAULT '',
  `upload` varchar(200) NOT NULL DEFAULT '',
  `monitering_fa` text NOT NULL COMMENT '관리자 > 모니터링 > 자주사용하는 문장',
  `nrank` int(11) NOT NULL DEFAULT '0',
  `user_uid` int(11) NOT NULL DEFAULT '0',
  `c_uid` int(11) NOT NULL DEFAULT '0',
  `paid` tinyint(3) NOT NULL DEFAULT '0',
  `o_uid` int(11) NOT NULL DEFAULT '0',
  `error_msg` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `hidden` (`hidden`),
  KEY `likes` (`likes`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`),
  KEY `hit` (`hit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_bot`
--

LOCK TABLES `rb_chatbot_bot` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_bot` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_bot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_botChatLog`
--

DROP TABLE IF EXISTS `rb_chatbot_botChatLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_botChatLog` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '해당 챗봇 active 값 (1 = dev, 2 =live)',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `user` int(11) NOT NULL DEFAULT '0',
  `chat` int(11) NOT NULL DEFAULT '0',
  `printType` varchar(10) NOT NULL DEFAULT '' COMMENT '출력 타입  : T, M, E',
  `chatType` char(1) NOT NULL DEFAULT 'R' COMMENT '채팅타입 : Q, R',
  `findType` varchar(2) NOT NULL DEFAULT '' COMMENT '답변 선택기준\ngetMarkForReply',
  `content` text NOT NULL,
  `intent` varchar(50) NOT NULL DEFAULT '',
  `score` varchar(20) NOT NULL DEFAULT '',
  `entity` varchar(255) NOT NULL DEFAULT '',
  `node` varchar(50) NOT NULL DEFAULT '',
  `is_unknown` tinyint(4) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `user` (`user`),
  KEY `chat` (`chat`),
  KEY `d_regis` (`d_regis`),
  KEY `roomToken` (`roomToken`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='봇 챗 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_botChatLog`
--

LOCK TABLES `rb_chatbot_botChatLog` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_botChatLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_botChatLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_botSettings`
--

DROP TABLE IF EXISTS `rb_chatbot_botSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_botSettings` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(800) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `botId` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_botSettings`
--

LOCK TABLES `rb_chatbot_botSettings` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_botSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_botSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_category`
--

DROP TABLE IF EXISTS `rb_chatbot_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_category` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `isson` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `skin_mobile` varchar(50) NOT NULL DEFAULT '',
  `imghead` varchar(100) NOT NULL DEFAULT '',
  `imgfoot` varchar(100) NOT NULL DEFAULT '',
  `puthead` tinyint(4) NOT NULL DEFAULT '0',
  `putfoot` tinyint(4) NOT NULL DEFAULT '0',
  `recnum` int(11) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `sosokmenu` varchar(50) NOT NULL DEFAULT '',
  `review` varchar(50) NOT NULL DEFAULT '',
  `tags` varchar(50) DEFAULT '',
  `featured_img` int(11) NOT NULL DEFAULT '0',
  `rp_sentence` text NOT NULL COMMENT '해당 인텐트로 치환할 문장(용어) ',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_category`
--

LOCK TABLES `rb_chatbot_category` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_category` DISABLE KEYS */;
INSERT INTO `rb_chatbot_category` VALUES (1,1,0,0,1,0,0,'백화점','','','','','',0,0,0,0,'','','',0,''),(2,2,0,0,1,0,0,'통신','','','','','',0,0,0,0,'','','',0,''),(3,3,0,0,1,0,0,'식품','','','','','',0,0,0,0,'','','',0,''),(4,4,0,0,1,0,0,'보험','','','','','',0,0,0,0,'','','',0,''),(5,5,0,0,1,0,0,'방송','','','','','',0,0,0,0,'','','',0,''),(6,6,0,0,1,0,0,'레저','','','','','',0,0,0,0,'','','',0,''),(7,7,0,0,1,0,0,'숙박','','','','','',0,0,0,0,'','','',0,''),(8,8,0,0,1,0,0,'병원','','','','','',0,0,0,0,'','','',0,''),(9,9,0,0,1,0,0,'학교','','','','','',0,0,0,0,'','','',0,''),(10,10,0,0,1,0,0,'학원','','','','','',0,0,0,0,'','','',0,''),(11,11,0,0,1,0,0,'일반기업','','','','','',0,0,0,0,'','','',0,''),(12,12,0,0,1,0,0,'여행','','','','','',0,0,0,0,'','','',0,''),(13,13,0,0,1,0,0,'항공','','','','','',0,0,0,0,'','','',0,''),(14,14,0,0,1,0,0,'쇼핑몰','','','','','',0,0,0,0,'','','',0,''),(15,15,0,0,1,0,0,'증권사','','','','','',0,0,0,0,'','','',0,''),(16,16,0,0,1,0,0,'기타','','','','','',0,0,0,0,'','','',0,'');
/*!40000 ALTER TABLE `rb_chatbot_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_channelSettings`
--

DROP TABLE IF EXISTS `rb_chatbot_channelSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_channelSettings` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `botid` varchar(50) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `channel` varchar(45) NOT NULL DEFAULT '',
  `name` varchar(60) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_channelSettings`
--

LOCK TABLES `rb_chatbot_channelSettings` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_channelSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_channelSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chat`
--

DROP TABLE IF EXISTS `rb_chatbot_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chat` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `auth` tinyint(4) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `user_display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `notice` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `nic` varchar(50) NOT NULL DEFAULT '',
  `mbrid` varchar(50) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `botuid` int(11) NOT NULL DEFAULT '0',
  `botid` varchar(100) NOT NULL DEFAULT '',
  `induCat` varchar(20) NOT NULL DEFAULT '',
  `quesCat` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `unlikes` int(11) NOT NULL DEFAULT '0',
  `report` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `ip` varchar(25) NOT NULL DEFAULT '',
  `agent` varchar(150) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  `by_who` varchar(45) NOT NULL DEFAULT '',
  `msg_type` char(1) NOT NULL DEFAULT '' COMMENT 'T : Text\nB : Button\nS : Slot',
  PRIMARY KEY (`uid`),
  KEY `display` (`display`),
  KEY `hidden` (`hidden`),
  KEY `vendor` (`vendor`),
  KEY `notice` (`notice`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`),
  KEY `botid` (`botid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chat`
--

LOCK TABLES `rb_chatbot_chat` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chatLog`
--

DROP TABLE IF EXISTS `rb_chatbot_chatLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chatLog` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '해당 챗봇 active 값 (1 = dev, 2 =live)',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `userName` varchar(60) NOT NULL DEFAULT '',
  `userId` varchar(100) NOT NULL DEFAULT '',
  `userUid` int(11) NOT NULL DEFAULT '0',
  `printType` char(1) NOT NULL DEFAULT 'T',
  `chatType` char(1) NOT NULL DEFAULT 'Q',
  `content` text NOT NULL,
  `ip` varchar(25) NOT NULL DEFAULT '',
  `agent` varchar(150) NOT NULL DEFAULT '',
  `intent` varchar(50) NOT NULL DEFAULT '',
  `score` VARCHAR(20) NOT NULL DEFAULT '',
  `entity` varchar(255) NOT NULL DEFAULT '',
  `node` VARCHAR(50) NOT NULL DEFAULT '',
  `is_unknown` TINYINT(4) NOT NULL DEFAULT '0',
  `emotion` varchar(250) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `userId` (`userId`),
  KEY `userUid` (`userUid`),
  KEY `d_regis` (`d_regis`),
  KEY `roomToken` (`roomToken`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='사용자 챗 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chatLog`
--

LOCK TABLES `rb_chatbot_chatLog` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chatLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chatLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chatStsLog`
--

DROP TABLE IF EXISTS `rb_chatbot_chatStsLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chatStsLog` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '해당 챗봇 active 값 (1 = dev, 2 =live)',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `sentence` varchar(250) NOT NULL DEFAULT '' COMMENT '사용자 입력 문장',
  `hit` int(11) NOT NULL DEFAULT '0' COMMENT '사용 횟수 카운',
  `date` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `sentence` (`sentence`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='문장 카운팅';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chatStsLog`
--

LOCK TABLES `rb_chatbot_chatStsLog` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chatStsLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chatStsLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chatStsRelation`
--

DROP TABLE IF EXISTS `rb_chatbot_chatStsRelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chatStsRelation` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `user` int(11) NOT NULL DEFAULT '0',
  `chat` int(11) NOT NULL DEFAULT '0',
  `sentence` varchar(250) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `user` (`user`),
  KEY `chat` (`chat`),
  KEY `sentence` (`sentence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='사용자 입력 문장 관계 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chatStsRelation`
--

LOCK TABLES `rb_chatbot_chatStsRelation` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chatStsRelation` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chatStsRelation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chatWordLog`
--

DROP TABLE IF EXISTS `rb_chatbot_chatWordLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chatWordLog` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1' COMMENT '해당 챗봇 active 값 (1 = dev, 2 =live)',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `keyword` varchar(70) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `keyword` (`keyword`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='사용자 입력 단어 로그';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chatWordLog`
--

LOCK TABLES `rb_chatbot_chatWordLog` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chatWordLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chatWordLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_chatWordRelation`
--

DROP TABLE IF EXISTS `rb_chatbot_chatWordRelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_chatWordRelation` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `user` int(11) NOT NULL DEFAULT '0',
  `chat` int(11) NOT NULL DEFAULT '0',
  `keyword` varchar(250) NOT NULL DEFAULT '0',
  `date` char(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `user` (`user`),
  KEY `chat` (`chat`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='사용자 입력 단어 관계 설명 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_chatWordRelation`
--

LOCK TABLES `rb_chatbot_chatWordRelation` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_chatWordRelation` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_chatWordRelation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_context`
--

DROP TABLE IF EXISTS `rb_chatbot_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_context` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `context` text,
  `d_regis` varchar(14) DEFAULT '',
  PRIMARY KEY (`uid`) USING BTREE,
  KEY `vendor` (`vendor`) USING BTREE,
  KEY `bot` (`bot`) USING BTREE,
  KEY `roomToken` (`roomToken`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_context`
--

LOCK TABLES `rb_chatbot_context` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_context` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_context` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_counter`
--

DROP TABLE IF EXISTS `rb_chatbot_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_counter` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `botuid` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `amod` char(1) NOT NULL DEFAULT '' COMMENT 'M : 모바일\nD : 데스크탑',
  `page` int(11) NOT NULL DEFAULT '0',
  `male` int(11) NOT NULL DEFAULT '0',
  `female` int(11) NOT NULL DEFAULT '0',
  `age_10` int(11) NOT NULL DEFAULT '0',
  `age_20` int(11) NOT NULL DEFAULT '0',
  `age_30` int(11) NOT NULL DEFAULT '0',
  `age_40` int(11) NOT NULL DEFAULT '0',
  `age_50` int(11) NOT NULL DEFAULT '0',
  `age_60` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `botuid` (`botuid`),
  KEY `date` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_counter`
--

LOCK TABLES `rb_chatbot_counter` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_counter` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_counter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dcounter`
--

DROP TABLE IF EXISTS `rb_chatbot_dcounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dcounter` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `botuid` int(11) NOT NULL DEFAULT '0',
  `botActive` tinyint(4) NOT NULL DEFAULT '1',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `amod` char(1) NOT NULL DEFAULT '',
  `page` int(11) NOT NULL DEFAULT '0',
  `male` int(11) NOT NULL DEFAULT '0',
  `female` int(11) NOT NULL DEFAULT '0',
  `age_10` int(11) NOT NULL DEFAULT '0',
  `age_20` int(11) NOT NULL DEFAULT '0',
  `age_30` int(11) NOT NULL DEFAULT '0',
  `age_40` int(11) NOT NULL DEFAULT '0',
  `age_50` int(11) NOT NULL DEFAULT '0',
  `age_60` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `botuid` (`botuid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dcounter`
--

LOCK TABLES `rb_chatbot_dcounter` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dcounter` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dcounter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialog`
--

DROP TABLE IF EXISTS `rb_chatbot_dialog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialog` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT 'D' COMMENT 'D: default\nT: topic ',
  `is_temp` tinyint(4) NOT NULL DEFAULT '0' COMMENT '토픽인 경우 > 템플릿 여부 값 ',
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '템플릿 인 경우 순서대로 tracking',
  `name` varchar(60) NOT NULL DEFAULT '',
  `intro` varchar(250) NOT NULL DEFAULT '',
  `active` tinyint(4) NOT NULL DEFAULT '1' COMMENT ' 사용여부값(여러 dialog 중)',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `graph` longtext NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0' COMMENT '- 챗봇 복사할때 필요\n- node 테이블 use_topic 값에 저장된 값을 치환할때 필요 ',
  `o_botuid` INT(11) NOT NULL DEFAULT '0',
  `is_temp_del` char(1) NOT NULL DEFAULT 'N' COMMENT '임시 삭제 flag',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialog`
--

LOCK TABLES `rb_chatbot_dialog` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogBackup`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogBackup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogBackup` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(4) NOT NULL DEFAULT '1' COMMENT ' 사용여부값(여러 dialog 중)',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `graph` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogBackup`
--

LOCK TABLES `rb_chatbot_dialogBackup` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogBackup` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogBackup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogNode`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogNode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogNode` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `isson` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `recCondition` text COMMENT '노드 인지조건(uid 포함)',
  `context` varchar(250) NOT NULL DEFAULT '',
  `recQry` varchar(250) NOT NULL DEFAULT '' COMMENT '노드 인지 조건 쿼리 ( 실제 비교대상 쿼리)',
  `track_flag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '노드 추적(체킹)할지 여부 값 : 기본 1',
  `node_action` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : 사용자 입력대기\n2 : 대화상자 이동',
  `jumpTo_node` int(11) NOT NULL DEFAULT '0' COMMENT 'node_action 이 대화상자 이동인 경우 이동할 node id ',
  `is_unknown` tinyint(4) NOT NULL DEFAULT '0' COMMENT '대화상자 못찾은 경우 응답하는 대화상자인지 여부값 \n1 : yes\n0: no',
  `use_topic` int(11) NOT NULL DEFAULT '0' COMMENT '연결된 토픽(dialog) uid',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `timeout` varchar(2) NOT NULL DEFAULT '0' COMMENT '노드별 timeout 값(초) 최대 99초',
  `timeout_msg` varchar(1000) COMMENT '노드별 timeout 시 노출 메시지',
  `unrecognized_count` varchar(2) NOT NULL DEFAULT '0' COMMENT '노드별 미인식 최대 횟수',
  `unrecognized_msg` varchar(1000) COMMENT '노드별 미인식 노출 메시지',
  `exceeded_msg` varchar(1000) COMMENT '노드별 미인식 횟수 초과 메시지',
  `fail_msg` varchar(1000) COMMENT '노드별 실패 메시지',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `dialog` (`dialog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogNode`
--

LOCK TABLES `rb_chatbot_dialogNode` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogNode` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogNode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogResApiOutput`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogResApiOutput`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogResApiOutput` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(1) NOT NULL DEFAULT '0',
  `bot` int(1) NOT NULL DEFAULT '0',
  `itemOC` int(11) NOT NULL DEFAULT '0' COMMENT 'dialogResItemOC uid',
  `gid` int(11) NOT NULL DEFAULT '0',
  `resType` varchar(20) NOT NULL DEFAULT '' COMMENT '응답 타입',
  `text_val` text NOT NULL,
  `varchar_val` varchar(250) NOT NULL DEFAULT '' COMMENT '카드타입 요약내용',
  `o_uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `itemOC` (`itemOC`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='api 답변';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogResApiOutput`
--

LOCK TABLES `rb_chatbot_dialogResApiOutput` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogResApiOutput` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogResApiOutput` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogResApiParam`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogResApiParam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogResApiParam` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `itemOC` int(11) NOT NULL DEFAULT '0',
  `api` int(11) NOT NULL DEFAULT '0',
  `req` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `position` varchar(10) NOT NULL DEFAULT '',
  `val_type` varchar(10) NOT NULL DEFAULT '',
  `param_type` varchar(10) NOT NULL DEFAULT '',
  `length` varchar(10) NOT NULL DEFAULT '',
  `text_val` text NOT NULL,
  `varchar_val` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `itemOC` (`itemOC`),
  KEY `api` (`api`),
  KEY `req` (`req`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogResApiParam`
--

LOCK TABLES `rb_chatbot_dialogResApiParam` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogResApiParam` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogResApiParam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogResGroup`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogResGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogResGroup` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `id` varchar(45) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `node` int(11) NOT NULL DEFAULT '0' COMMENT 'node id',
  `resType` varchar(20) NOT NULL DEFAULT '' COMMENT '응답 타입',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `dialog` (`dialog`),
  KEY `node` (`node`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='응답 그룹(출력순서)\nRG : Respond Group';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogResGroup`
--

LOCK TABLES `rb_chatbot_dialogResGroup` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogResGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogResGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogResItem`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogResItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogResItem` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `node` int(11) NOT NULL DEFAULT '0' COMMENT 'node id',
  `resGroupId` varchar(45) NOT NULL DEFAULT '' COMMENT 'dialogResGroup 테이블 id 값',
  `resType` varchar(20) NOT NULL DEFAULT '' COMMENT '응답 타입',
  `id` varchar(45) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '카드 제목,메뉴 라벨',
  `summary` varchar(250) NOT NULL DEFAULT '' COMMENT '카드타입 요약내용',
  `content` text NOT NULL,
  `img_url` varchar(200) NOT NULL DEFAULT '',
  `link1` varchar(200) NOT NULL DEFAULT '',
  `link2` varchar(200) NOT NULL DEFAULT '',
  `link3` varchar(200) NOT NULL DEFAULT '',
  `recQry` varchar(100) NOT NULL DEFAULT '',
  `recCondition` mediumtext NOT NULL,
  `ctx_init` tinyint(3) NOT NULL DEFAULT '0',
  `bargein` tinyint(3) NOT NULL DEFAULT '0',
  `ctiaction` varchar(100) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0',
  `tts_speed` varchar(2) NOT NULL DEFAULT '0' COMMENT '콜봇 tts 속도(-5~5)',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `dialog` (`dialog`),
  KEY `node` (`node`),
  KEY `resGroup` (`resGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='응답 아이템(출력내용)\nRI : Respond Item';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogResItem`
--

LOCK TABLES `rb_chatbot_dialogResItem` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogResItem` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogResItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_dialogResItemOC`
--

DROP TABLE IF EXISTS `rb_chatbot_dialogResItemOC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_dialogResItemOC` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(1) NOT NULL DEFAULT '0',
  `bot` int(1) NOT NULL DEFAULT '0',
  `item` int(11) NOT NULL DEFAULT '0' COMMENT 'dialogResItem uid',
  `gid` int(11) NOT NULL DEFAULT '0',
  `resType` varchar(20) NOT NULL DEFAULT '' COMMENT '응답 타입',
  `text_val` text NOT NULL,
  `varchar_val` varchar(250) NOT NULL DEFAULT '' COMMENT '카드타입 요약내용',
  `bargein` tinyint(3) NOT NULL DEFAULT '0',
  `ctiaction` varchar(100) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0',
  `tts_speed` varchar(2) NOT NULL DEFAULT '0' COMMENT '콜봇 tts 속도(-5~5)',
  PRIMARY KEY (`uid`),
  KEY `resRI` (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='조건별 or 메뉴 선택시 응답(출력내용)\nRI : Resp';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_dialogResItemOC`
--

LOCK TABLES `rb_chatbot_dialogResItemOC` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_dialogResItemOC` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_dialogResItemOC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_entity`
--

DROP TABLE IF EXISTS `rb_chatbot_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_entity` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT 'S' COMMENT 'S:시스템 제공, V:벤더 지정',
  `site` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `rp_sentence` text NOT NULL COMMENT '해당 인텐트로 치환할 문장(용어) ',
  `induCat` varchar(45) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0' COMMENT '카피한 경우 original uid',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_entity`
--

LOCK TABLES `rb_chatbot_entity` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_entityData`
--

DROP TABLE IF EXISTS `rb_chatbot_entityData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_entityData` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT 'S' COMMENT '데이타 타입',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `entity` int(11) NOT NULL DEFAULT '0',
  `entityVal` int(11) NOT NULL DEFAULT '0',
  `intent` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `value` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `entityVal` (`entityVal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='인텐트*엔터티 데이타 테이블';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_entityData`
--

LOCK TABLES `rb_chatbot_entityData` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_entityData` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_entityData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_entityVal`
--

DROP TABLE IF EXISTS `rb_chatbot_entityVal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_entityVal` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT 'S' COMMENT 'S:시스템 제공, V:벤더 지정',
  `site` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `entity` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `synonyms` text NOT NULL COMMENT '해당 엔터티와 유사어',
  `patterns` varchar(250) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0' COMMENT '카피한 경우 original uid ',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `entity` (`entity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='엔터티 value 들';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_entityVal`
--

LOCK TABLES `rb_chatbot_entityVal` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_entityVal` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_entityVal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_goods`
--

DROP TABLE IF EXISTS `rb_chatbot_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_goods` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `induCat` varchar(20) NOT NULL DEFAULT '',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL,
  `f_img` varchar(45) NOT NULL DEFAULT '',
  `code` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_goods`
--

LOCK TABLES `rb_chatbot_goods` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_induCat`
--

DROP TABLE IF EXISTS `rb_chatbot_induCat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_induCat` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `isson` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `skin_mobile` varchar(50) NOT NULL DEFAULT '',
  `imghead` varchar(100) NOT NULL DEFAULT '',
  `imgfoot` varchar(100) NOT NULL DEFAULT '',
  `puthead` tinyint(4) NOT NULL DEFAULT '0',
  `putfoot` tinyint(4) NOT NULL DEFAULT '0',
  `recnum` int(11) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `sosokmenu` varchar(50) NOT NULL DEFAULT '',
  `review` varchar(50) NOT NULL DEFAULT '',
  `tags` varchar(50) NOT NULL DEFAULT '',
  `featured_img` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_induCat`
--

LOCK TABLES `rb_chatbot_induCat` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_induCat` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_induCat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_intent`
--

DROP TABLE IF EXISTS `rb_chatbot_intent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_intent` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT 'S' COMMENT 'S:시스템 제공, V:벤더 지정',
  `site` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `rp_sentence` text NOT NULL COMMENT '해당 인텐트로 치환할 문장(용어) ',
  `induCat` varchar(45) NOT NULL DEFAULT '',
  `o_uid` int(11) NOT NULL DEFAULT '0' COMMENT '카피한 경우 original uid',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_intent`
--

LOCK TABLES `rb_chatbot_intent` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_intent` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_intent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_intentEx`
--

DROP TABLE IF EXISTS `rb_chatbot_intentEx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_intentEx` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT 'S',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `intent` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `content` text NOT NULL COMMENT '예문 내용',
  `o_uid` int(11) NOT NULL DEFAULT '0' COMMENT '카피한 경우 original uid',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`),
  KEY `intent` (`intent`),
  KEY `dialog` (`dialog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='인텐트 예문 들';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_intentEx`
--

LOCK TABLES `rb_chatbot_intentEx` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_intentEx` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_intentEx` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_keyword`
--

DROP TABLE IF EXISTS `rb_chatbot_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_keyword` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `is_child` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `mobile` tinyint(4) NOT NULL DEFAULT '0',
  `printType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '출력 타입\n\n1. 엔터티만\n2. 엔터티 + 정보 \n',
  `replyType` varchar(2) NOT NULL DEFAULT '' COMMENT '답변 타입 \n\n01 : 텍스트 \n02 : 링크 \n03 : 이미지 \n04 : 전화  ',
  `keyword` varchar(70) NOT NULL DEFAULT '',
  `showMenu` tinyint(4) NOT NULL DEFAULT '0',
  `replace` text NOT NULL COMMENT '치화시킬 텍스트 : 해당 name 으로 치환 ',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_keyword`
--

LOCK TABLES `rb_chatbot_keyword` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_keywordInfo`
--

DROP TABLE IF EXISTS `rb_chatbot_keywordInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_keywordInfo` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `kwd_uid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `summary` varchar(250) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `price1` int(11) NOT NULL DEFAULT '0',
  `price2` int(11) NOT NULL DEFAULT '0',
  `vote` tinyint(4) NOT NULL DEFAULT '0',
  `upload` varchar(200) NOT NULL DEFAULT '',
  `featured_img` int(11) NOT NULL DEFAULT '0',
  `img_url` varchar(250) NOT NULL DEFAULT '',
  `link1` varchar(250) NOT NULL DEFAULT '',
  `link2` varchar(250) NOT NULL DEFAULT '',
  `link3` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `kwd_uid` (`kwd_uid`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_keywordInfo`
--

LOCK TABLES `rb_chatbot_keywordInfo` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_keywordInfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_keywordInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_liveActBot`
--

DROP TABLE IF EXISTS `rb_chatbot_liveActBot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_liveActBot` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `UN` tinyint(4) NOT NULL DEFAULT '0' COMMENT '답변못한 응답 갯수',
  `RS` tinyint(4) NOT NULL DEFAULT '0' COMMENT '중복문장 갯수 \n:  rb_chatbot_chatStsLog 테이블에서 roomToken 이 같은 것 중 hit 이 2 개 이상인 갯수  ',
  `ip` varchar(25) NOT NULL DEFAULT '',
  `d_open` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='실시간 접속한 챗봇 데이터 (방토큰이 핵심)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_liveActBot`
--

LOCK TABLES `rb_chatbot_liveActBot` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_liveActBot` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_liveActBot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_manager`
--

DROP TABLE IF EXISTS `rb_chatbot_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_manager` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 : 승인, 2: 미승인',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `parentmbr` int(11) NOT NULL DEFAULT '0',
  `role` varchar(50) NOT NULL DEFAULT '',
  `role_intro` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_manager`
--

LOCK TABLES `rb_chatbot_manager` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_manager` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_moniteringFA`
--

DROP TABLE IF EXISTS `rb_chatbot_moniteringFA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_moniteringFA` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `botId` varchar(100) NOT NULL DEFAULT '',
  `fa` text NOT NULL,
  `hit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='모니터링 페이지 자주사용하는 문장';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_moniteringFA`
--

LOCK TABLES `rb_chatbot_moniteringFA` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_moniteringFA` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_moniteringFA` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_notification`
--

DROP TABLE IF EXISTS `rb_chatbot_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_notification` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `botuid` int(11) NOT NULL DEFAULT '0',
  `frommodule` varchar(50) NOT NULL DEFAULT '',
  `frommbr` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `referer` varchar(250) NOT NULL DEFAULT '',
  `target` varchar(20) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_read` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `vendor` (`vendor`),
  KEY `frommbr` (`frommbr`),
  KEY `d_read` (`d_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_notification`
--

LOCK TABLES `rb_chatbot_notification` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_question`
--

DROP TABLE IF EXISTS `rb_chatbot_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_question` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `use_default` tinyint(4) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `r_uid` int(11) NOT NULL DEFAULT '0',
  `r_type` char(1) NOT NULL DEFAULT '',
  `quesCat` varchar(20) NOT NULL DEFAULT '',
  `pattern` varchar(100) NOT NULL DEFAULT '',
  `lang` char(3) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `morpheme` varchar(200) NOT NULL DEFAULT '',
  `q_key` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `r_uid` (`r_uid`),
  KEY `pattern` (`pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_question`
--

LOCK TABLES `rb_chatbot_question` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_referer`
--

DROP TABLE IF EXISTS `rb_chatbot_referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_referer` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `botuid` varchar(200) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `mbrsex` tinyint(4) NOT NULL DEFAULT '0',
  `mbrage` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `referer` varchar(200) NOT NULL DEFAULT '',
  `agent` varchar(200) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `botuid` (`botuid`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_referer`
--

LOCK TABLES `rb_chatbot_referer` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_referer` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_referer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_reply`
--

DROP TABLE IF EXISTS `rb_chatbot_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_reply` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `induCat` varchar(20) NOT NULL DEFAULT '',
  `quesCat` varchar(20) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '' COMMENT 'A : 단답형\nS : 선택형\nM: 멀',
  `lang` varchar(20) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `showType` varchar(45) NOT NULL DEFAULT '' COMMENT 'type 값이 M (멀티 일때) , Text, Card,Image,Menu  타입 출력여부 및 순서 ',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_reply`
--

LOCK TABLES `rb_chatbot_reply` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_reply` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_replyMulti`
--

DROP TABLE IF EXISTS `rb_chatbot_replyMulti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_replyMulti` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `r_uid` int(11) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `type` char(1) NOT NULL DEFAULT '' COMMENT 'T : 텍스트 타입\nC : 카드 타입\nM: 메뉴타입\n',
  `quesCat` varchar(20) NOT NULL DEFAULT '',
  `entity` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL COMMENT 'text 타입인 경우',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '카드형인 경우 콤마로 구분해서 title,subTitle,link 값도 함께 저장',
  `sub_title` varchar(250) NOT NULL DEFAULT '',
  `link_url` varchar(250) NOT NULL DEFAULT '' COMMENT '웹페이지 url 혹은 이미지 url',
  `link_target` char(1) NOT NULL DEFAULT '' COMMENT 'I : image\nW : web page',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_replyMulti`
--

LOCK TABLES `rb_chatbot_replyMulti` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_replyMulti` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_replyMulti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_rescounter`
--

DROP TABLE IF EXISTS `rb_chatbot_rescounter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_rescounter` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `mbruid` INT(11) NOT NULL DEFAULT '0',
  `user_uid` int(11) NOT NULL DEFAULT '0',
  `botid` VARCHAR(50) NOT NULL DEFAULT '',
  `roomToken` VARCHAR(100) NOT NULL DEFAULT '',
  `bottype` VARCHAR(10) NOT NULL DEFAULT 'chat',
  `channel` VARCHAR(20) NOT NULL DEFAULT '',
  `rcount` INT(11) NOT NULL DEFAULT '0',
  `unknown` INT(11) NOT NULL DEFAULT '0',
  `ctime` INT(11) NOT NULL DEFAULT '0',
  `cstarttime` VARCHAR(15) NOT NULL DEFAULT '',
  `cendtime` VARCHAR(15) NOT NULL DEFAULT '',
  `sms` INT(11) NOT NULL DEFAULT '0',
  `lms` INT(11) NOT NULL DEFAULT '0',
  `ip` VARCHAR(20) NOT NULL DEFAULT '',
  `device` VARCHAR(10) NOT NULL DEFAULT '',
  `d_date` VARCHAR(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `botid` (`botid`),
  KEY `roomToken` (`roomToken`),
  KEY `d_date` (`d_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_rescounter`
--

LOCK TABLES `rb_chatbot_rescounter` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_rescounter` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_rescounter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_rule`
--

DROP TABLE IF EXISTS `rb_chatbot_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_rule` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `quesCat` varchar(50) NOT NULL DEFAULT '',
  `lang` char(3) NOT NULL DEFAULT '0',
  `r_uid` int(11) NOT NULL DEFAULT '0',
  `r_type` char(1) NOT NULL DEFAULT 'A',
  `q_uid` int(11) NOT NULL DEFAULT '0',
  `pattern` varchar(200) NOT NULL DEFAULT '',
  `reply` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `category` (`quesCat`),
  KEY `pattern` (`pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_rule`
--

LOCK TABLES `rb_chatbot_rule` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_ruleC`
--

DROP TABLE IF EXISTS `rb_chatbot_ruleC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_ruleC` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `register` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `quesCat` varchar(100) NOT NULL DEFAULT '',
  `question` text NOT NULL,
  `pattern` varchar(200) NOT NULL DEFAULT '',
  `reply` text NOT NULL,
  `lang` char(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `quesCat` (`quesCat`),
  KEY `pattern` (`pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='일반 질문에 대한 룰';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_ruleC`
--

LOCK TABLES `rb_chatbot_ruleC` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_ruleC` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_ruleC` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_tempData`
--

DROP TABLE IF EXISTS `rb_chatbot_tempData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_tempData` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `item_type` char(2) NOT NULL DEFAULT '' COMMENT 'RI : ResItem or ResItemOC',
  `item_uid` int(11) NOT NULL DEFAULT '0',
  `label` varchar(50) NOT NULL DEFAULT '' COMMENT '사용자 화면에 노출되는 라벨',
  `resType` varchar(45) NOT NULL DEFAULT '' COMMENT 'img,text...',
  `node` int(11) DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`),
  KEY `bot` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='템플릿에서 지정한 데이터';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_tempData`
--

LOCK TABLES `rb_chatbot_tempData` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_tempData` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_tempData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_token`
--

DROP TABLE IF EXISTS `rb_chatbot_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_token` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `bot` int(11) NOT NULL DEFAULT '0',
  `access_mod` varchar(45) NOT NULL DEFAULT '' COMMENT '상황값 \nex) 문진봇 관리자 접속 = mediExam',
  `access_token` varchar(250) NOT NULL DEFAULT '',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `userId` varchar(100) NOT NULL DEFAULT '',
  `expire` int(11) NOT NULL DEFAULT '0',
  `r_data` text NULL,
  PRIMARY KEY (`uid`),
  KEY `consol_id` (`bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_token`
--

LOCK TABLES `rb_chatbot_token` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_unknown`
--

DROP TABLE IF EXISTS `rb_chatbot_unknown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_unknown` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `roomToken` varchar(100) NOT NULL DEFAULT '',
  `sentence` varchar(250) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `is_learn` tinyint(4) NOT NULL DEFAULT '0' COMMENT '학습되었는지 여부값',
  `d_learn` varchar(8) NOT NULL DEFAULT '' COMMENT '학습된 날짜',
  `learnType` char(1) NOT NULL DEFAULT '' COMMENT 'E : Entity 추가 \nI : Intent 추가 \nD : Deep 런닝 진행',
  `add_intentex` int(11) NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `botuid` (`bot`),
  KEY `vendor` (`vendor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_unknown`
--

LOCK TABLES `rb_chatbot_unknown` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_unknown` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_unknown` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_upload`
--

DROP TABLE IF EXISTS `rb_chatbot_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_upload` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `tmpcode` varchar(20) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `bot` int(11) NOT NULL DEFAULT '0',
  `dialog` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `ext` varchar(4) NOT NULL DEFAULT '0',
  `fserver` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(150) NOT NULL DEFAULT '',
  `folder` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `tmpname` varchar(100) NOT NULL DEFAULT '',
  `thumbname` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `alt` varchar(50) NOT NULL DEFAULT '',
  `caption` text NOT NULL,
  `description` text NOT NULL,
  `src` text NOT NULL,
  `linkto` tinyint(4) NOT NULL DEFAULT '0',
  `license` tinyint(4) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  `linkurl` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `tmpcode` (`tmpcode`),
  KEY `mbruid` (`mbruid`),
  KEY `type` (`type`),
  KEY `name` (`name`),
  KEY `d_regis` (`d_regis`),
  KEY `tmpname` (`tmpname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_upload`
--

LOCK TABLES `rb_chatbot_upload` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_vendor`
--

DROP TABLE IF EXISTS `rb_chatbot_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_vendor` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `auth` tinyint(4) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `is_admin` tinyint(4) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 : 일반,\n2 : 프리미엄,',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `induCat` varchar(20) NOT NULL DEFAULT '',
  `id` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `service` varchar(100) NOT NULL DEFAULT '',
  `intro` varchar(500) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `tel` varchar(45) NOT NULL DEFAULT '',
  `tel2` varchar(45) NOT NULL DEFAULT '',
  `email` varchar(45) NOT NULL DEFAULT '',
  `logo` varchar(100) NOT NULL DEFAULT '',
  `upload` varchar(300) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `id` (`id`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_vendor`
--

LOCK TABLES `rb_chatbot_vendor` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_vendor` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_pms_category`
--

DROP TABLE IF EXISTS `rb_pms_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_pms_category` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `isson` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `skin` varchar(50) NOT NULL DEFAULT '',
  `skin_mobile` varchar(50) NOT NULL DEFAULT '',
  `imghead` varchar(100) NOT NULL DEFAULT '',
  `imgfoot` varchar(100) NOT NULL DEFAULT '',
  `puthead` tinyint(4) NOT NULL DEFAULT '0',
  `putfoot` tinyint(4) NOT NULL DEFAULT '0',
  `recnum` int(11) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `sosokmenu` varchar(50) NOT NULL DEFAULT '',
  `review` varchar(50) NOT NULL DEFAULT '',
  `tags` varchar(50) NOT NULL DEFAULT '',
  `featured_img` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`),
  KEY `hidden` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_pms_category`
--

LOCK TABLES `rb_pms_category` WRITE;
/*!40000 ALTER TABLE `rb_pms_category` DISABLE KEYS */;
INSERT INTO `rb_pms_category` VALUES (1,1,1,0,1,0,0,'데스크탑','','','','','',0,0,10,-17,'PC','','',0),(2,2,1,0,1,0,0,'모바일','','','','','',0,0,10,-9,'PC','','',0),(3,1,0,1,2,0,0,'메인페이지','','','','','',0,0,10,0,'PC','','',0),(4,2,0,1,2,0,0,'상세화면','','','','','',0,0,10,0,'PC','','',0),(5,3,0,1,2,0,0,'챗봇 만들기','','','','','',0,0,10,0,'PC','','',0),(6,4,0,1,2,0,0,'나의챗봇','','','','','',0,0,10,0,'PC','','',0),(7,5,0,1,2,0,0,'회원관리','','','','','',0,0,10,0,'PC','','',0),(8,6,0,1,2,0,0,'기타','','','','','',0,0,10,0,'PC','','',0),(9,1,0,2,2,0,0,'메인 페이지','','','','','',0,0,10,0,'PC','','',0),(10,2,0,2,2,0,0,'상세화면','','','','','',0,0,10,0,'PC','','',0),(11,3,0,2,2,0,0,'챗봇 만들기','','','','','',0,0,10,0,'PC','','',0),(12,4,0,2,2,0,0,'나의챗봇','','','','','',0,0,10,0,'PC','','',0),(13,5,0,2,2,0,0,'회원관리','','','','','',0,0,10,0,'PC','','',0),(14,6,0,2,2,0,0,'기타','','','','','',0,0,10,0,'PC','','',0);
/*!40000 ALTER TABLE `rb_pms_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_pms_product`
--

DROP TABLE IF EXISTS `rb_pms_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_pms_product` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `category` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `price` int(11) NOT NULL DEFAULT '0',
  `price1` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `price_x` tinyint(4) NOT NULL DEFAULT '0',
  `country` varchar(30) NOT NULL DEFAULT '',
  `maker` varchar(30) NOT NULL DEFAULT '',
  `brand` varchar(30) NOT NULL DEFAULT '',
  `model` varchar(30) NOT NULL DEFAULT '',
  `stock` tinyint(4) NOT NULL DEFAULT '0',
  `stock_num` int(11) NOT NULL DEFAULT '0',
  `addinfo` text NOT NULL,
  `addoptions` text NOT NULL,
  `icons` text NOT NULL,
  `tags` varchar(200) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `ext` varchar(3) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `comment` int(11) NOT NULL DEFAULT '0',
  `vote` int(11) NOT NULL DEFAULT '0',
  `qna` int(11) NOT NULL DEFAULT '0',
  `hit` int(11) NOT NULL DEFAULT '0',
  `wish` int(11) NOT NULL DEFAULT '0',
  `buy` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `vendor` int(11) NOT NULL DEFAULT '0',
  `md` int(11) NOT NULL DEFAULT '0',
  `num1` int(11) NOT NULL DEFAULT '0',
  `num2` int(11) NOT NULL DEFAULT '0',
  `code` varchar(13) NOT NULL DEFAULT '',
  `namekey` char(1) NOT NULL DEFAULT '',
  `d_make` varchar(8) NOT NULL DEFAULT '',
  `is_free` tinyint(4) NOT NULL DEFAULT '0',
  `is_cash` tinyint(4) NOT NULL DEFAULT '0',
  `halin_event` varchar(30) NOT NULL DEFAULT '',
  `halin_mbr` varchar(200) NOT NULL DEFAULT '',
  `joint` text NOT NULL,
  `featured_img` int(11) NOT NULL DEFAULT '0',
  `review` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `display` (`display`),
  KEY `category` (`category`),
  KEY `name` (`name`),
  KEY `price` (`price`),
  KEY `point` (`point`),
  KEY `country` (`country`),
  KEY `maker` (`maker`),
  KEY `brand` (`brand`),
  KEY `model` (`model`),
  KEY `stock` (`stock`),
  KEY `stock_num` (`stock_num`),
  KEY `tags` (`tags`),
  KEY `hit` (`hit`),
  KEY `wish` (`wish`),
  KEY `buy` (`buy`),
  KEY `d_regis` (`d_regis`),
  KEY `vendor` (`vendor`),
  KEY `md` (`md`),
  KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_pms_product`
--

LOCK TABLES `rb_pms_product` WRITE;
/*!40000 ALTER TABLE `rb_pms_product` DISABLE KEYS */;
INSERT INTO `rb_pms_product` VALUES (7,99999997,0,'1/4','상세화면',0,0,0,0,'','','','',0,0,'','','','','좌측메뉴 닫힘','TEXT','','',0,0,0,0,0,0,'20170119141355',0,0,1,0,'0170119141355','ㅅ','20170119',0,0,'','','',8,'04.html'),(5,99999999,0,'1/7','회원관리',0,0,0,0,'','','','',0,0,'','','','','로그인','TEXT','','',0,0,0,0,0,0,'20170119135910',0,0,1,0,'0170119135910','ㅎ','20170119',0,0,'','','',3,'02.html'),(6,99999998,0,'1/7','회원관리',0,0,0,0,'','','','',0,0,'','','','','회원가입','TEXT','','',0,0,0,0,0,0,'20170119140104',0,0,1,0,'0170119140104','ㅎ','20170119',0,0,'','','',4,'03.html'),(4,100000000,0,'1/3','메인페이지',0,0,0,0,'','','','',0,0,'','','','','메인 페이지','TEXT','','',0,0,0,0,0,0,'20170119135210',0,0,1,0,'0170119135210','ㅁ','20170119',0,0,'','','',2,'01.html'),(8,99999996,0,'1/4','상세화면',0,0,0,0,'','','','',0,0,'','','','','좌측메뉴 열림','TEXT','','',0,0,0,0,0,0,'20170119141643',0,0,1,0,'0170119141643','ㅅ','20170119',0,0,'','','',7,'05.html'),(9,99999995,0,'1/7','회원관리',0,0,0,0,'','','','',0,0,'','','','','회원정보 변경','TEXT','','',0,0,0,0,0,0,'20170119142247',0,0,1,0,'0170119142247','ㅎ','20170119',0,0,'','','',35,'06.html'),(10,99999994,0,'1/8','기타',0,0,0,0,'','','','',0,0,'','','','','챗봇과 대화','TEXT','','',0,0,0,0,0,0,'20170119144549',0,0,1,0,'0170119144549','ㄱ','20170119',0,0,'','','',13,'07.html'),(11,99999993,0,'1/5','챗봇 만들기',0,0,0,0,'','','','',0,0,'','','','','챗봇 만들기 4 단계','TEXT','','',0,0,0,0,0,0,'20170119144741',0,0,1,0,'0170119144741','ㅊ','20170119',0,0,'','','',14,'08.html,09.html,10.html,11.html,12.html'),(12,99999992,0,'1/8','기타',0,0,0,0,'','','','',0,0,'','','','','내가 대화(추가)한 챗봇','TEXT','','',0,0,0,0,0,0,'20170119144925',0,0,1,0,'0170119144925','ㄱ','20170119',0,0,'','','',16,'13.html,14.html'),(13,99999991,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','기본설정 변경','TEXT','','',0,0,0,0,0,0,'20170119151340',0,0,1,0,'0170119151340','ㄱ','20170119',0,0,'','','',17,'15.html'),(14,99999990,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','광고 메세지','TEXT','','',0,0,0,0,0,0,'20170119151445',0,0,1,0,'0170119151445','ㄱ','20170119',0,0,'','','',18,'16.html'),(15,99999989,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','대화 보기','TEXT','','',0,0,0,0,0,0,'20170119151625',0,0,1,0,'0170119151625','ㄱ','20170119',0,0,'','','',34,'17.html'),(16,99999988,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','멤버관리','TEXT','','',0,0,0,0,0,0,'20170119151808',0,0,1,0,'0170119151808','ㄱ','20170119',0,0,'','','',33,'18.html'),(17,99999987,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','통계 보기','TEXT','','',0,0,0,0,0,0,'20170119151902',0,0,1,0,'0170119151902','ㄱ','20170119',0,0,'','','',32,'19.html'),(18,99999986,0,'1/6','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','프리미엄','TEXT','','',0,0,0,0,0,0,'20170119151943',0,0,1,0,'0170119151943','ㄱ','20170119',0,0,'','','',36,''),(19,99999985,0,'2/9','메인 페이지',0,0,0,0,'','','','',0,0,'','','','','메인 페이지','TEXT','','',0,0,0,0,0,0,'20170119154707',0,0,1,0,'0170119154707','ㅁ','20170119',0,0,'','','',23,'01.html'),(20,99999984,0,'2/13','회원관리',0,0,0,0,'','','','',0,0,'','','','','로그인','TEXT','','',0,0,0,0,0,0,'20170119155605',0,0,1,0,'0170119155605','ㅎ','20170119',0,0,'','','',24,'02.html'),(21,99999983,0,'2/13','회원관리',0,0,0,0,'','','','',0,0,'','','','','회원가입','TEXT','','',0,0,0,0,0,0,'20170119155641',0,0,1,0,'0170119155641','ㅎ','20170119',0,0,'','','',25,'03.html'),(22,99999982,0,'2/9','메인 페이지',0,0,0,0,'','','','',0,0,'','','','','좌측메뉴 > 로그인 전','TEXT','','',0,0,0,0,0,0,'20170119155918',0,0,1,0,'0170119155918','ㅁ','20170119',0,0,'','','',27,'04.html'),(23,99999981,0,'2/9','메인 페이지',0,0,0,0,'','','','',0,0,'','','','','좌측 메뉴 > 로그인 후','TEXT','','',0,0,0,0,0,0,'20170119160415',0,0,1,0,'0170119160415','ㅁ','20170119',0,0,'','','',0,'05.html'),(24,99999980,0,'2/11','챗봇 만들기',0,0,0,0,'','','','',0,0,'','','','','카테고리 출력','TEXT','','',0,0,0,0,0,0,'20170119160739',0,0,1,0,'0170119160739','ㅊ','20170119',0,0,'','','',0,'06.html'),(25,99999979,0,'2/10','상세화면',0,0,0,0,'','','','',0,0,'','','','','상세보기','TEXT','','',0,0,0,0,0,0,'20170119160952',0,0,1,0,'0170119160952','ㅅ','20170119',0,0,'','','',28,'07.html'),(26,99999978,0,'2/14','기타',0,0,0,0,'','','','',0,0,'','','','','챗봇 대화하기','TEXT','','',0,0,0,0,0,0,'20170119161607',0,0,1,0,'0170119161607','ㄱ','20170119',0,0,'','','',31,'08.html,09.html,10.html'),(27,99999977,0,'2/13','회원관리',0,0,0,0,'','','','',0,0,'','','','','회원정보 변경','TEXT','','',0,0,0,0,0,0,'20170119161825',0,0,1,0,'0170119161825','ㅎ','20170119',0,0,'','','',30,'11.html'),(28,99999976,0,'2/12','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','내가 추가한 챗봇','TEXT','','',0,0,0,0,0,0,'20170123123408',0,0,1,0,'0170123123408','ㄱ','20170123',0,0,'','','',37,''),(29,99999975,0,'2/12','나의챗봇',0,0,0,0,'','','','',0,0,'','','','','내가 대화한 챗봇','TEXT','','',0,0,0,0,0,0,'20170123123436',0,0,1,0,'0170123123436','ㄱ','20170123',0,0,'','','',38,'');
/*!40000 ALTER TABLE `rb_pms_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_pms_qna`
--

DROP TABLE IF EXISTS `rb_pms_qna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_pms_qna` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `product` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `html1` varchar(4) NOT NULL DEFAULT '',
  `reply` mediumtext NOT NULL,
  `html2` varchar(4) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `hidden` (`hidden`),
  KEY `product` (`product`),
  KEY `mbruid` (`mbruid`),
  KEY `subject` (`subject`),
  KEY `hit` (`hit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_pms_qna`
--

LOCK TABLES `rb_pms_qna` WRITE;
/*!40000 ALTER TABLE `rb_pms_qna` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_pms_qna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_pms_upload`
--

DROP TABLE IF EXISTS `rb_pms_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_pms_upload` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `parent` varchar(20) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `tmpcode` varchar(20) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `fileonly` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `ext` varchar(4) NOT NULL DEFAULT '0',
  `fserver` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(150) NOT NULL DEFAULT '',
  `folder` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `tmpname` varchar(100) NOT NULL DEFAULT '',
  `thumbname` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `alt` varchar(50) NOT NULL DEFAULT '',
  `caption` text NOT NULL,
  `description` text NOT NULL,
  `src` text NOT NULL,
  `linkto` tinyint(4) NOT NULL DEFAULT '0',
  `license` tinyint(4) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  `linkurl` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `category` (`category`),
  KEY `tmpcode` (`tmpcode`),
  KEY `site` (`site`),
  KEY `mbruid` (`mbruid`),
  KEY `fileonly` (`fileonly`),
  KEY `type` (`type`),
  KEY `ext` (`ext`),
  KEY `name` (`name`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_pms_upload`
--

LOCK TABLES `rb_pms_upload` WRITE;
/*!40000 ALTER TABLE `rb_pms_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_pms_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_adminpage`
--

DROP TABLE IF EXISTS `rb_s_adminpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_adminpage` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `memberuid` int(11) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `memberuid` (`memberuid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_adminpage`
--

LOCK TABLES `rb_s_adminpage` WRITE;
/*!40000 ALTER TABLE `rb_s_adminpage` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_adminpage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_browser`
--

DROP TABLE IF EXISTS `rb_s_browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_browser` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `browser` varchar(10) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `date` (`date`),
  KEY `browser` (`browser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_browser`
--

LOCK TABLES `rb_s_browser` WRITE;
/*!40000 ALTER TABLE `rb_s_browser` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_browser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_cash`
--

DROP TABLE IF EXISTS `rb_s_cash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_cash` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `my_mbruid` int(11) NOT NULL DEFAULT '0',
  `by_mbruid` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `my_mbruid` (`my_mbruid`),
  KEY `by_mbruid` (`by_mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_cash`
--

LOCK TABLES `rb_s_cash` WRITE;
/*!40000 ALTER TABLE `rb_s_cash` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_cash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_comment`
--

DROP TABLE IF EXISTS `rb_s_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_comment` (
  `uid` int(11) NOT NULL,
  `site` int(11) NOT NULL DEFAULT '0',
  `parent` varchar(30) NOT NULL DEFAULT '0',
  `parentmbr` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `notice` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `nic` varchar(50) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(16) NOT NULL DEFAULT '',
  `pw` varchar(250) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `oneline` int(11) NOT NULL DEFAULT '0',
  `score1` int(11) NOT NULL DEFAULT '0',
  `score2` int(11) NOT NULL DEFAULT '0',
  `report` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `d_oneline` varchar(14) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `ip` varchar(25) NOT NULL DEFAULT '',
  `agent` varchar(150) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  `sns` varchar(100) NOT NULL DEFAULT '',
  `adddata` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `parent` (`parent`),
  KEY `parentmbr` (`parentmbr`),
  KEY `display` (`display`),
  KEY `hidden` (`hidden`),
  KEY `notice` (`notice`),
  KEY `mbruid` (`mbruid`),
  KEY `subject` (`subject`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_comment`
--

LOCK TABLES `rb_s_comment` WRITE;
/*!40000 ALTER TABLE `rb_s_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_counter`
--

DROP TABLE IF EXISTS `rb_s_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_counter` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `page` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_counter`
--

LOCK TABLES `rb_s_counter` WRITE;
/*!40000 ALTER TABLE `rb_s_counter` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_counter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_domain`
--

DROP TABLE IF EXISTS `rb_s_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_domain` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `is_child` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`),
  KEY `name` (`name`),
  KEY `site` (`site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_domain`
--

LOCK TABLES `rb_s_domain` WRITE;
/*!40000 ALTER TABLE `rb_s_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_friend`
--

DROP TABLE IF EXISTS `rb_s_friend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_friend` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `rel` tinyint(4) NOT NULL DEFAULT '0',
  `my_mbruid` int(11) NOT NULL DEFAULT '0',
  `by_mbruid` int(11) NOT NULL DEFAULT '0',
  `category` varchar(50) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `rel` (`rel`),
  KEY `my_mbruid` (`my_mbruid`),
  KEY `by_mbruid` (`by_mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_friend`
--

LOCK TABLES `rb_s_friend` WRITE;
/*!40000 ALTER TABLE `rb_s_friend` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_friend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_inkey`
--

DROP TABLE IF EXISTS `rb_s_inkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_inkey` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `date` (`date`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_inkey`
--

LOCK TABLES `rb_s_inkey` WRITE;
/*!40000 ALTER TABLE `rb_s_inkey` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_inkey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrcomp`
--

DROP TABLE IF EXISTS `rb_s_mbrcomp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrcomp` (
  `memberuid` int(11) NOT NULL,
  `comp_num` int(11) NOT NULL DEFAULT '0',
  `comp_type` tinyint(4) NOT NULL DEFAULT '0',
  `comp_name` varchar(50) NOT NULL DEFAULT '',
  `comp_ceo` varchar(30) NOT NULL DEFAULT '',
  `comp_condition` varchar(100) NOT NULL DEFAULT '',
  `comp_item` varchar(100) NOT NULL DEFAULT '',
  `comp_tel` varchar(20) NOT NULL DEFAULT '',
  `comp_fax` varchar(20) NOT NULL DEFAULT '',
  `comp_zip` varchar(20) NOT NULL DEFAULT '',
  `comp_addr0` varchar(6) NOT NULL DEFAULT '',
  `comp_addr1` varchar(250) NOT NULL DEFAULT '',
  `comp_addr2` varchar(250) NOT NULL DEFAULT '',
  `comp_part` varchar(30) NOT NULL DEFAULT '',
  `comp_level` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`memberuid`),
  KEY `comp_num` (`comp_num`),
  KEY `comp_type` (`comp_type`),
  KEY `comp_name` (`comp_name`),
  KEY `comp_ceo` (`comp_ceo`),
  KEY `comp_addr0` (`comp_addr0`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrcomp`
--

LOCK TABLES `rb_s_mbrcomp` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrcomp` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_mbrcomp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrdata`
--

DROP TABLE IF EXISTS `rb_s_mbrdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrdata` (
  `memberuid` int(11) NOT NULL,
  `site` int(11) NOT NULL DEFAULT '0',
  `auth` tinyint(4) NOT NULL DEFAULT '0',
  `mygroup` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `comp` tinyint(4) NOT NULL DEFAULT '0',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `adm_view` text NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `nic` varchar(50) NOT NULL DEFAULT '',
  `grade` varchar(20) NOT NULL DEFAULT '',
  `photo` varchar(200) NOT NULL DEFAULT '',
  `home` varchar(100) NOT NULL DEFAULT '',
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `birth1` smallint(6) NOT NULL DEFAULT '0',
  `birth2` smallint(4) unsigned zerofill NOT NULL DEFAULT '0000',
  `birthtype` tinyint(4) NOT NULL DEFAULT '0',
  `tel1` varchar(20) NOT NULL DEFAULT '',
  `tel2` varchar(20) NOT NULL DEFAULT '',
  `zip` varchar(20) NOT NULL DEFAULT '',
  `addr0` varchar(6) NOT NULL DEFAULT '',
  `addr1` varchar(250) NOT NULL DEFAULT '',
  `addr2` varchar(250) NOT NULL DEFAULT '',
  `job` varchar(30) NOT NULL DEFAULT '',
  `marr1` smallint(6) NOT NULL DEFAULT '0',
  `marr2` smallint(4) unsigned zerofill NOT NULL DEFAULT '0000',
  `sms` tinyint(4) NOT NULL DEFAULT '0',
  `mailing` tinyint(4) NOT NULL DEFAULT '0',
  `smail` tinyint(4) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `usepoint` int(11) NOT NULL DEFAULT '0',
  `money` int(11) NOT NULL DEFAULT '0',
  `cash` int(11) NOT NULL DEFAULT '0',
  `num_login` int(11) NOT NULL DEFAULT '0',
  `pw_q` varchar(250) NOT NULL DEFAULT '',
  `pw_a` varchar(100) NOT NULL DEFAULT '',
  `now_log` tinyint(4) NOT NULL DEFAULT '0',
  `last_log` varchar(14) NOT NULL DEFAULT '',
  `last_pw` varchar(8) NOT NULL DEFAULT '',
  `is_paper` tinyint(4) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `tmpcode` varchar(250) NOT NULL DEFAULT '',
  `sns` text NOT NULL,
  `noticeconf` text NOT NULL,
  `num_notice` int(11) NOT NULL DEFAULT '0',
  `addfield` text NOT NULL,
  `age` varchar(20) NOT NULL DEFAULT '',
  `cgroup` varchar(50) NOT NULL DEFAULT '',
  `manager` tinyint(1) NOT NULL DEFAULT '0',
  `super` tinyint(1) NOT NULL DEFAULT '0',
  `log_fail_cnt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_lock` char(1) NOT NULL DEFAULT 'N',
  `before_pw1` varchar(250) NOT NULL DEFAULT '',
  `before_pw2` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`memberuid`),
  KEY `site` (`site`),
  KEY `auth` (`auth`),
  KEY `comp` (`comp`),
  KEY `mygroup` (`mygroup`),
  KEY `level` (`level`),
  KEY `admin` (`admin`),
  KEY `email` (`email`),
  KEY `name` (`name`),
  KEY `nic` (`nic`),
  KEY `sex` (`sex`),
  KEY `birth1` (`birth1`),
  KEY `birth2` (`birth2`),
  KEY `birthtype` (`birthtype`),
  KEY `addr0` (`addr0`),
  KEY `job` (`job`),
  KEY `marr1` (`marr1`),
  KEY `marr2` (`marr2`),
  KEY `sms` (`sms`),
  KEY `mailing` (`mailing`),
  KEY `smail` (`smail`),
  KEY `point` (`point`),
  KEY `usepoint` (`usepoint`),
  KEY `now_log` (`now_log`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrdata`
--

LOCK TABLES `rb_s_mbrdata` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_mbrdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrgroup`
--

DROP TABLE IF EXISTS `rb_s_mbrgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrgroup` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `gid` tinyint(4) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `bot` text NULL DEFAULT NULL,
  `menu` text NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrgroup`
--

LOCK TABLES `rb_s_mbrgroup` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrgroup` DISABLE KEYS */;
INSERT INTO `rb_s_mbrgroup` VALUES (1,'관리자',0,0,'','',''),(2,'일반사용자',1,0,'','','');
/*!40000 ALTER TABLE `rb_s_mbrgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrid`
--

DROP TABLE IF EXISTS `rb_s_mbrid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrid` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `id` varchar(50) NOT NULL DEFAULT '',
  `pw` varchar(250) NOT NULL DEFAULT '',
  `partner` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrid`
--

LOCK TABLES `rb_s_mbrid` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrid` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_mbrid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrlevel`
--

DROP TABLE IF EXISTS `rb_s_mbrlevel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrlevel` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `login` int(11) NOT NULL DEFAULT '0',
  `post` int(11) NOT NULL DEFAULT '0',
  `comment` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrlevel`
--

LOCK TABLES `rb_s_mbrlevel` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrlevel` DISABLE KEYS */;
INSERT INTO `rb_s_mbrlevel` VALUES (1,0,'레벨1',0,0,0,0),(2,0,'레벨2',0,0,0,0),(3,0,'레벨3',0,0,0,0),(4,0,'레벨4',0,0,0,0),(5,0,'레벨5',0,0,0,0),(6,0,'레벨6',0,0,0,0),(7,0,'레벨7',0,0,0,0),(8,0,'레벨8',0,0,0,0),(9,0,'레벨9',0,0,0,0),(10,0,'레벨10',0,0,0,0),(11,0,'레벨11',0,0,0,0),(12,0,'레벨12',0,0,0,0),(13,0,'레벨13',0,0,0,0),(14,0,'레벨14',0,0,0,0),(15,0,'레벨15',0,0,0,0),(16,0,'레벨16',0,0,0,0),(17,0,'레벨17',0,0,0,0),(18,0,'레벨18',0,0,0,0),(19,0,'레벨19',0,0,0,0),(20,1,'레벨20',0,0,0,0),(21,0,'레벨21',0,0,0,0),(22,0,'레벨22',0,0,0,0),(23,0,'레벨23',0,0,0,0),(24,0,'레벨24',0,0,0,0),(25,0,'레벨25',0,0,0,0),(26,0,'레벨26',0,0,0,0),(27,0,'레벨27',0,0,0,0),(28,0,'레벨28',0,0,0,0),(29,0,'레벨29',0,0,0,0),(30,0,'레벨30',0,0,0,0),(31,0,'레벨31',0,0,0,0),(32,0,'레벨32',0,0,0,0),(33,0,'레벨33',0,0,0,0),(34,0,'레벨34',0,0,0,0),(35,0,'레벨35',0,0,0,0),(36,0,'레벨36',0,0,0,0),(37,0,'레벨37',0,0,0,0),(38,0,'레벨38',0,0,0,0),(39,0,'레벨39',0,0,0,0),(40,0,'레벨40',0,0,0,0),(41,0,'레벨41',0,0,0,0),(42,0,'레벨42',0,0,0,0),(43,0,'레벨43',0,0,0,0),(44,0,'레벨44',0,0,0,0),(45,0,'레벨45',0,0,0,0),(46,0,'레벨46',0,0,0,0),(47,0,'레벨47',0,0,0,0),(48,0,'레벨48',0,0,0,0),(49,0,'레벨49',0,0,0,0),(50,0,'레벨50',0,0,0,0),(51,0,'레벨51',0,0,0,0),(52,0,'레벨52',0,0,0,0),(53,0,'레벨53',0,0,0,0),(54,0,'레벨54',0,0,0,0),(55,0,'레벨55',0,0,0,0),(56,0,'레벨56',0,0,0,0),(57,0,'레벨57',0,0,0,0),(58,0,'레벨58',0,0,0,0),(59,0,'레벨59',0,0,0,0),(60,0,'레벨60',0,0,0,0),(61,0,'레벨61',0,0,0,0),(62,0,'레벨62',0,0,0,0),(63,0,'레벨63',0,0,0,0),(64,0,'레벨64',0,0,0,0),(65,0,'레벨65',0,0,0,0),(66,0,'레벨66',0,0,0,0),(67,0,'레벨67',0,0,0,0),(68,0,'레벨68',0,0,0,0),(69,0,'레벨69',0,0,0,0),(70,0,'레벨70',0,0,0,0),(71,0,'레벨71',0,0,0,0),(72,0,'레벨72',0,0,0,0),(73,0,'레벨73',0,0,0,0),(74,0,'레벨74',0,0,0,0),(75,0,'레벨75',0,0,0,0),(76,0,'레벨76',0,0,0,0),(77,0,'레벨77',0,0,0,0),(78,0,'레벨78',0,0,0,0),(79,0,'레벨79',0,0,0,0),(80,0,'레벨80',0,0,0,0),(81,0,'레벨81',0,0,0,0),(82,0,'레벨82',0,0,0,0),(83,0,'레벨83',0,0,0,0),(84,0,'레벨84',0,0,0,0),(85,0,'레벨85',0,0,0,0),(86,0,'레벨86',0,0,0,0),(87,0,'레벨87',0,0,0,0),(88,0,'레벨88',0,0,0,0),(89,0,'레벨89',0,0,0,0),(90,0,'레벨90',0,0,0,0),(91,0,'레벨91',0,0,0,0),(92,0,'레벨92',0,0,0,0),(93,0,'레벨93',0,0,0,0),(94,0,'레벨94',0,0,0,0),(95,0,'레벨95',0,0,0,0),(96,0,'레벨96',0,0,0,0),(97,0,'레벨97',0,0,0,0),(98,0,'레벨98',0,0,0,0),(99,0,'레벨99',0,0,0,0),(100,0,'레벨100',0,0,0,0);
/*!40000 ALTER TABLE `rb_s_mbrlevel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mbrsns`
--

DROP TABLE IF EXISTS `rb_s_mbrsns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mbrsns` (
  `memberuid` int(11) NOT NULL,
  `st` varchar(40) NOT NULL DEFAULT '',
  `sf` varchar(40) NOT NULL DEFAULT '',
  `sg` varchar(40) NOT NULL DEFAULT '',
  `sd` varchar(40) NOT NULL DEFAULT '',
  `sn` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`memberuid`),
  KEY `st` (`st`),
  KEY `sf` (`sf`),
  KEY `sm` (`sg`),
  KEY `sy` (`sd`),
  KEY `sn` (`sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mbrsns`
--

LOCK TABLES `rb_s_mbrsns` WRITE;
/*!40000 ALTER TABLE `rb_s_mbrsns` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_mbrsns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_menu`
--

DROP TABLE IF EXISTS `rb_s_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_menu` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `site` int(11) NOT NULL DEFAULT '0',
  `is_child` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `depth` tinyint(4) NOT NULL DEFAULT '0',
  `id` varchar(50) NOT NULL DEFAULT '',
  `menutype` tinyint(4) NOT NULL DEFAULT '0',
  `mobile` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `reject` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `target` varchar(20) NOT NULL DEFAULT '',
  `redirect` tinyint(4) NOT NULL DEFAULT '0',
  `joint` varchar(250) NOT NULL DEFAULT '',
  `perm_g` varchar(200) NOT NULL DEFAULT '',
  `perm_l` tinyint(4) NOT NULL DEFAULT '0',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `imghead` varchar(100) NOT NULL DEFAULT '',
  `imgfoot` varchar(100) NOT NULL DEFAULT '',
  `addattr` varchar(100) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `d_last` varchar(14) NOT NULL DEFAULT '',
  `addinfo` text NOT NULL,
  `mediaset` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `site` (`site`),
  KEY `parent` (`parent`),
  KEY `depth` (`depth`),
  KEY `id` (`id`),
  KEY `mobile` (`mobile`),
  KEY `hidden` (`hidden`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_menu`
--

LOCK TABLES `rb_s_menu` WRITE;
/*!40000 ALTER TABLE `rb_s_menu` DISABLE KEYS */;
INSERT INTO `rb_s_menu` VALUES (1,0,1,1,0,1,'intro-',1,1,0,0,'챗봇소개','',0,'','',0,'','','','',0,'','',''),(2,1,1,0,0,1,'build',1,1,0,0,'챗봇만들기','',0,'/?m=chatbot&page=build/step1&mod=new','',0,'','','','',0,'','',''),(3,3,1,0,0,1,'talked',1,1,0,0,'내가 대화한 챗봇','',0,'/?r=home&m=chatbot&page=user/talked','',0,'','','','',0,'','',''),(4,4,1,0,0,1,'added',1,1,0,0,'ADD 챗봇','',0,'/?r=home&m=chatbot&page=user/added','',0,'','','','',0,'','',''),(5,5,1,1,0,1,'mybot',1,1,0,0,'나의 챗봇','',0,'/?r=home&m=chatbot&page=vendor/main','',0,'','','','',0,'','',''),(6,6,1,0,0,1,'support',1,1,0,0,'고객센터','',0,'/?m=bbs&bid=contact_us&mod=write','',0,'','','','',9,'20170418132821','',''),(8,2,1,0,1,2,'customized',3,1,0,0,'맞춤형 챗봇','',0,'','',0,'','','','',0,'','',''),(9,3,1,0,1,2,'premium',3,1,0,0,'프리미엄 서비스','',0,'','',0,'','','','',0,'','',''),(11,6,1,0,5,2,'message',1,1,0,0,'광고 메세지','',0,'/?r=home&m=chatbot&page=vendor/message','',0,'','','','',0,'','',''),(12,7,1,0,5,2,'story',1,1,0,0,'대화보기','',0,'/?r=home&m=chatbot&page=vendor/story','',0,'','','','',0,'','',''),(13,8,1,0,5,2,'manager',1,1,0,0,'부운영자 관리','',0,'/?r=home&m=chatbot&page=vendor/manager','',0,'','','','',0,'','',''),(14,9,1,0,5,2,'statistics',1,1,0,0,'통계','',0,'/?r=home&m=chatbot&page=vendor/statics','',0,'','','','',0,'','',''),(17,2,1,0,0,1,'regisBot',1,1,0,0,'챗봇등록하기','',0,'/?m=chatbot&page=build/regis&mod=new','',0,'','','','',0,'','',''),(18,3,1,0,5,2,'keyword',1,0,0,0,'메뉴 관리','',0,'/?r=home&m=chatbot&page=vendor/makekeyword','',0,'','','','',0,'','',''),(21,7,1,0,0,1,'21',3,1,0,0,'폴리쳇 메뉴','',0,'','',0,'','','','',0,'','',''),(25,4,1,0,5,2,'qna',1,1,0,0,'Q&A 관리','',0,'/?r=home&m=chatbot&page=vendor/qna','',0,'','','','',0,'','',''),(26,0,1,0,5,2,'dialog',1,1,0,0,'대화관리','',0,'/?r=home&m=chatbot&page=vendor/dialog','',0,'','','','',0,'','',''),(27,8,1,1,0,1,'adm',1,1,0,0,'업체관리자','',0,'/?r=home&m=chatbot&page=adm/main','',0,'','','','',0,'','',''),(28,3,1,0,27,2,'dashboard',1,1,0,0,'대시보드','',0,'/?m=chatbot&page=adm/dashboard','',0,'','','','',0,'','',''),(29,5,1,1,27,2,'make',1,1,0,0,'챗봇제작','',0,'/?m=chatbot&page=adm/make','',0,'','','','',0,'','',''),(32,0,1,0,29,3,'graph',1,1,0,0,'대화그래프','',0,'/?m=chatbot&page=adm/graph','',0,'','','','',0,'','',''),(33,2,1,0,29,3,'intentSet',1,1,0,0,'인텐트 관리','',0,'/?m=chatbot&page=adm/intentSet','',0,'','','','',0,'','',''),(34,3,1,0,29,3,'entitySet',1,1,0,0,'엔터티 관리','',0,'/?m=chatbot&page=adm/entitySet','',0,'','','','',0,'','',''),(39,8,1,1,27,2,'analysis',1,1,0,0,'통계/분석','',0,'/?m=chatbot&page=adm/analysis','',0,'','','','',0,'','',''),(40,4,1,1,27,2,'settings',1,1,0,0,'챗봇설정','',0,'/?m=chatbot&page=adm/settings','',0,'','','','',0,'','',''),(42,3,1,0,39,3,'user',1,1,0,0,'사용자 현황','',0,'/?m=chatbot&page=adm/user','',0,'','','','',0,'','',''),(43,0,1,0,39,3,'intent',1,1,1,0,'인텐트','',0,'/?m=chatbot&page=adm/intent','',0,'','','','',0,'','',''),(44,1,1,0,39,3,'entity',1,1,1,0,'엔터티','',0,'/?m=chatbot&page=adm/entity','',0,'','','','',0,'','',''),(45,2,1,0,39,3,'context',1,1,1,0,'문맥','',0,'/?m=chatbot&page=adm/context','',0,'','','','',0,'','',''),(47,5,1,0,39,3,'convstat',1,1,0,0,'대화 현황','',0,'/?m=chatbot&page=adm/convstat','',0,'','','','',0,'','',''),(48,10,1,0,39,3,'learning',1,1,0,0,'학습','',0,'/?m=chatbot&page=adm/learning','',0,'','','','',0,'','',''),(49,0,1,0,40,3,'config',1,1,0,0,'기본설정','',0,'/?m=chatbot&page=adm/config','',0,'','','','',0,'','',''),(50,4,1,0,40,3,'api',1,1,0,0,'API 설정','',0,'/?m=chatbot&page=adm/api','',0,'','','','',0,'','',''),(51,5,1,0,40,3,'legacy',1,1,0,0,'레거시 설정','',0,'/?m=chatbot&page=adm/legacy','',0,'','','','',0,'','',''),(52,6,1,0,27,2,'channel',1,1,0,0,'채널설정','',0,'/?m=chatbot&page=adm/chanel','',0,'','','','',0,'','',''),(53,9,1,1,0,1,'53',3,1,0,0,'총관리자','',0,'','',0,'','','','',0,'','',''),(54,0,1,0,53,2,'a_dashboard',1,1,1,0,'대시보드','',0,'/?m=chatbot&page=suAdm/dashboard','',0,'','','','',0,'','',''),(55,2,1,1,53,2,'sysData',1,1,0,0,'시스템 리소스','',0,'/?m=chatbot&page=suAdm/make','',0,'','','','',0,'','',''),(56,3,1,0,53,2,'a_analysis',1,1,1,0,'통계/분석','',0,'/?m=chatbot&page=suAdm/analysis','',0,'','','','',0,'','',''),(57,4,1,0,53,2,'a_settings',1,1,1,0,'시스템설정','',0,'/?m=chatbot&page=suAdm/settings','',0,'','','','',0,'','',''),(58,1,1,0,55,3,'sysEntity',1,1,0,0,'시스템-엔터티','',0,'/?m=chatbot&page=suAdm/sysEntity','',0,'','','','',0,'','',''),(59,7,1,0,27,2,'link',3,1,1,0,'API 관리','',0,'','',0,'','','','',0,'','',''),(60,2,1,0,55,3,'sysLegacy',3,1,0,0,'시스템-레거시','',0,'','',0,'','','','',0,'','',''),(61,1,1,0,29,3,'nodeSet',1,1,1,0,'대화상자 관리','',0,'/?m=chatbot&page=adm/nodeSet','',0,'','','','',0,'','',''),(62,1,1,1,53,2,'template',3,1,0,0,'챗봇관리','',0,'','',0,'','','','',0,'','',''),(63,1,1,0,62,3,'tempList',3,1,1,0,'템플릿 리스트','',0,'','',0,'','','','',0,'','',''),(64,2,1,0,62,3,'chatbotList',3,1,0,0,'챗봇 리스트','',0,'','',0,'','','','',0,'','',''),(65,3,1,0,40,3,'response',3,1,1,0,'응답설정','',0,'','',0,'','','','',0,'','',''),(66,0,1,0,27,2,'main',1,1,0,0,'챗봇 리스트','',0,'/?m=chatbot&page=adm/main','',0,'','','','',0,'','',''),(67,5,1,1,53,2,'voice',3,1,0,0,'음성인식','',0,'','',0,'','','','',0,'','',''),(68,1,1,0,67,3,'stt',3,1,0,0,'STT','',0,'','',0,'','','','',0,'','',''),(69,2,1,0,67,3,'tts',3,1,0,0,'TTS','',0,'','',0,'','','','',0,'','',''),(70,3,1,0,67,3,'rtt',3,1,1,0,'RTT','',0,'','',0,'','','','',0,'','',''),(71,9,1,0,27,2,'monitering',3,1,0,0,'모니터링','',0,'','',0,'','','','',0,'','',''),(72,2,1,0,40,3,'operation',3,1,0,0,'고급설정','',0,'','',0,'','','','',0,'','',''),(73,1,1,0,40,3,'skin',1,1,0,0,'스킨설정','',0,'/?m=chatbot&page=adm/skin','',0,'','','','',0,'','',''),(74,10,1,0,27,2,'74',3,1,1,0,'시스템리소스','',0,'','',0,'','','','',0,'','',''),(79,6,1,0,40,3,'intro',1,1,0,0,'인트로 설정','',0,'/?m=chatbot&page=adm/intro','',0,'','','','',0,'','',''),(80,4,1,0,39,3,'statis',1,1,0,0,'통계관리','',0,'/?m=chatbot&page=adm/statis','',0,'','','','',0,'','',''),(81,9,1,0,39,3,'gathering',1,1,0,0,'군집 분석','',0,'/?m=chatbot&page=adm/gathering','',0,'','','','',0,'','',''),(82,6,1,0,39,3,'conversation',1,1,0,0,'대화 로그','',0,'/?m=chatbot&page=adm/conversation','',0,'','','','',0,'','',''),(83,7,1,0,39,3,'convanalysis',1,1,0,0,'대화 분석','',0,'/?m=chatbot&page=adm/convanalysis','',0,'','','','',0,'','',''),(84,8,1,0,39,3,'convflow',1,1,0,0,'대화 흐름 분석','',0,'/?m=chatbot&page=adm/convflow','',0,'','','','',0,'','',''),(85,4,1,0,29,3,'convtest',1,1,0,0,'대화테스트','',0,'/?m=chatbot&page=adm/convtest','',0,'','','','',0,'','',''),(86,7,1,0,40,3,'faq',1,1,0,0,'FAQ 설정','',0,'/?m=chatbot&page=adm/faq','',0,'','','','',0,'','',''),(87,11,1,0,39,3,'statgpt',1,1,0,0,'ChatGPT 통계','',0,'/?m=chatbot&page=adm/statgpt','',0,'','','','',0,'','','');
/*!40000 ALTER TABLE `rb_s_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_mobile`
--

DROP TABLE IF EXISTS `rb_s_mobile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_mobile` (
  `usemobile` tinyint(4) NOT NULL DEFAULT '0',
  `startsite` int(11) NOT NULL DEFAULT '0',
  `startdomain` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_mobile`
--

LOCK TABLES `rb_s_mobile` WRITE;
/*!40000 ALTER TABLE `rb_s_mobile` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_mobile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_module`
--

DROP TABLE IF EXISTS `rb_s_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_module` (
  `gid` int(11) NOT NULL DEFAULT '0',
  `system` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `mobile` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL DEFAULT '',
  `id` varchar(30) NOT NULL DEFAULT '',
  `tblnum` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(50) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `lang` varchar(20) NOT NULL DEFAULT '',
  KEY `gid` (`gid`),
  KEY `system` (`system`),
  KEY `hidden` (`hidden`),
  KEY `mobile` (`mobile`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_module`
--

LOCK TABLES `rb_s_module` WRITE;
/*!40000 ALTER TABLE `rb_s_module` DISABLE KEYS */;
INSERT INTO `rb_s_module` VALUES (0,1,1,1,'대시보드','dashboard',0,'kf-dashboard','20161223172728',''),(1,1,1,0,'마켓','market',0,'kf-market','20161223172728',''),(2,1,1,0,'시스템 도구','admin',36,'kf-admin','20161223172728',''),(3,1,1,1,'모듈','module',0,'kf-module','20161223172728',''),(4,1,0,0,'사이트','site',0,'kf-home','20161223172728',''),(5,1,1,0,'레이아웃','layout',0,'kf-layout','20161223172728',''),(6,1,1,1,'미디어셋','mediaset',0,'kf-upload','20161223172728',''),(7,1,1,1,'도메인','domain',0,'kf-domain','20161223172728',''),(8,1,1,1,'디바이스','device',0,'kf-device','20161223172728',''),(9,1,1,1,'알림','notification',0,'kf-notify','20161223172728',''),(10,1,1,1,'통합검색','search',0,'kf-search','20161223172728',''),(11,0,1,0,'﻿프로젝트 관리','pms',3,'glyphicon glyphicon-folder-open','20170119094357',''),(12,0,0,0,'﻿챗봇','chatbot',10,'glyphicon glyphicon-comment','20170124162948',''),(13,0,0,0,'﻿회원관리','member',0,'kf-member','20170128004218',''),(14,0,1,0,'소셜링크','slogin',0,'fa fa-skype','20170131034741',''),(15,0,1,0,'게시판','bbs',7,'kf-bbs','20170207114046',''),(16,0,1,0,'접속통계','counter',0,'kf-analysis','20170221132446','');
/*!40000 ALTER TABLE `rb_s_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_money`
--

DROP TABLE IF EXISTS `rb_s_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_money` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `my_mbruid` int(11) NOT NULL DEFAULT '0',
  `by_mbruid` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `my_mbruid` (`my_mbruid`),
  KEY `by_mbruid` (`by_mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_money`
--

LOCK TABLES `rb_s_money` WRITE;
/*!40000 ALTER TABLE `rb_s_money` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_money` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_notice`
--

DROP TABLE IF EXISTS `rb_s_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_notice` (
  `uid` char(16) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `site` int(11) NOT NULL DEFAULT '0',
  `frommodule` varchar(50) NOT NULL DEFAULT '',
  `frommbr` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `referer` varchar(250) NOT NULL DEFAULT '',
  `target` varchar(20) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_read` varchar(14) NOT NULL DEFAULT '',
  KEY `uid` (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `site` (`site`),
  KEY `frommbr` (`frommbr`),
  KEY `d_read` (`d_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_notice`
--

LOCK TABLES `rb_s_notice` WRITE;
/*!40000 ALTER TABLE `rb_s_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_numinfo`
--

DROP TABLE IF EXISTS `rb_s_numinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_numinfo` (
  `date` char(8) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `visit` int(11) NOT NULL DEFAULT '0',
  `login` int(11) NOT NULL DEFAULT '0',
  `comment` int(11) NOT NULL DEFAULT '0',
  `oneline` int(11) NOT NULL DEFAULT '0',
  `rcvtrack` int(11) NOT NULL DEFAULT '0',
  `sndtrack` int(11) NOT NULL DEFAULT '0',
  `upload` int(11) NOT NULL DEFAULT '0',
  `download` int(11) NOT NULL DEFAULT '0',
  `mbrjoin` int(11) NOT NULL DEFAULT '0',
  `mbrout` int(11) NOT NULL DEFAULT '0',
  KEY `date` (`date`),
  KEY `site` (`site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_numinfo`
--

LOCK TABLES `rb_s_numinfo` WRITE;
/*!40000 ALTER TABLE `rb_s_numinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_numinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_oneline`
--

DROP TABLE IF EXISTS `rb_s_oneline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_oneline` (
  `uid` int(11) NOT NULL,
  `site` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `parentmbr` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `nic` varchar(30) NOT NULL DEFAULT '',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(16) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `report` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `ip` varchar(25) NOT NULL DEFAULT '',
  `agent` varchar(150) NOT NULL DEFAULT '',
  `adddata` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `parent` (`parent`),
  KEY `parentmbr` (`parentmbr`),
  KEY `hidden` (`hidden`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_oneline`
--

LOCK TABLES `rb_s_oneline` WRITE;
/*!40000 ALTER TABLE `rb_s_oneline` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_oneline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_outkey`
--

DROP TABLE IF EXISTS `rb_s_outkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_outkey` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `naver` int(11) NOT NULL DEFAULT '0',
  `nate` int(11) NOT NULL DEFAULT '0',
  `daum` int(11) NOT NULL DEFAULT '0',
  `yahoo` int(11) NOT NULL DEFAULT '0',
  `google` int(11) NOT NULL DEFAULT '0',
  `etc` int(11) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `date` (`date`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_outkey`
--

LOCK TABLES `rb_s_outkey` WRITE;
/*!40000 ALTER TABLE `rb_s_outkey` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_outkey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_page`
--

DROP TABLE IF EXISTS `rb_s_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_page` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `pagetype` tinyint(4) NOT NULL DEFAULT '0',
  `ismain` tinyint(4) NOT NULL DEFAULT '0',
  `mobile` tinyint(4) NOT NULL DEFAULT '0',
  `id` varchar(50) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `perm_g` varchar(200) NOT NULL DEFAULT '',
  `perm_l` tinyint(4) NOT NULL DEFAULT '0',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `joint` varchar(250) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  `linkedmenu` varchar(100) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `mediaset` text NOT NULL,
  `member` int(11) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `ismain` (`ismain`),
  KEY `mobile` (`mobile`),
  KEY `id` (`id`),
  KEY `category` (`category`),
  KEY `linkedmenu` (`linkedmenu`),
  KEY `d_regis` (`d_regis`),
  KEY `d_update` (`d_update`),
  KEY `member` (`member`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_page`
--

LOCK TABLES `rb_s_page` WRITE;
/*!40000 ALTER TABLE `rb_s_page` DISABLE KEYS */;
INSERT INTO `rb_s_page` VALUES (1,1,3,0,0,'privacy','기본페이지','개인정보 취급방침','',0,'default/_full_layout.php','',0,'','20161223172728','','0',0,''),(2,1,3,0,0,'terms','기본페이지','이용약관','',0,'default/_full_layout.php','',0,'','20161223172728','','0',0,''),(3,1,3,0,0,'test','기본페이지','테스트','',0,'default/_full_layout.php','',0,'','20170112143308','20170227042631','',1,''),(4,1,1,0,0,'login','기본페이지','로그인','',0,'chatbot-desktop/default.php','/?m=member&front=login',0,'','20170131025038','20170131025300','',1,''),(5,1,1,0,0,'join','기본페이지','회원가입','',0,'chatbot-desktop/default.php','/?m=member&front=join',0,'','20170131025109','20170131025340','',1,''),(6,1,1,0,0,'idpwsearch','기본페이지','비밀번호 찾기','',0,'chatbot-desktop/default.php','/?m=member&front=login&page=idpwsearch',0,'','20170202173825','20170225155428','',1,''),(7,1,1,0,0,'profile','기본페이지','프로필','',0,'','/?m=member&front=profile',0,'','20170205074219','20170205074254','',1,''),(8,1,3,0,1,'botlist','기본페이지','모바일 챗봇 리스트','',0,'rc-instar/home.php','',0,'','20170216025704','20170216040101','',1,''),(9,1,3,0,0,'ga_test','기본페이지','구글 애널리틱스 테스트','',0,'default/default.php','',0,'','20170221111719','20170221112222','',1,''),(10,1,3,0,0,'getWidget','기본페이지','봇 위젯','',0,'getWidget/default.php','',0,'','20170226093141','20170226094105','',1,''),(11,1,3,0,0,'pos_tag','기본페이지','형태소분석','',0,'','',0,'','20171006221747','20180731164833','',1,''),(12,1,3,0,0,'coffee_price','기본페이지','커피가격','',0,'default/_blank.php','',0,'','20181212011355','20181228175438','',1,''),(13,1,3,0,0,'watson_chat','기본페이지','왓슨챗봇','',0,'default/_watson_chat.php','',0,'','20181228171826','20181228175800','',1,'');
/*!40000 ALTER TABLE `rb_s_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_paper`
--

DROP TABLE IF EXISTS `rb_s_paper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_paper` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0',
  `my_mbruid` int(11) NOT NULL DEFAULT '0',
  `by_mbruid` int(11) NOT NULL DEFAULT '0',
  `inbox` tinyint(4) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_read` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `parent` (`parent`),
  KEY `my_mbruid` (`my_mbruid`),
  KEY `by_mbruid` (`by_mbruid`),
  KEY `inbox` (`inbox`),
  KEY `d_regis` (`d_regis`),
  KEY `d_read` (`d_read`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_paper`
--

LOCK TABLES `rb_s_paper` WRITE;
/*!40000 ALTER TABLE `rb_s_paper` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_paper` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_point`
--

DROP TABLE IF EXISTS `rb_s_point`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_point` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `my_mbruid` int(11) NOT NULL DEFAULT '0',
  `by_mbruid` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `my_mbruid` (`my_mbruid`),
  KEY `by_mbruid` (`by_mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_point`
--

LOCK TABLES `rb_s_point` WRITE;
/*!40000 ALTER TABLE `rb_s_point` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_point` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_popup`
--

DROP TABLE IF EXISTS `rb_s_popup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_popup` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `term0` tinyint(4) NOT NULL DEFAULT '0',
  `term1` varchar(14) NOT NULL DEFAULT '',
  `term2` varchar(14) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `html` varchar(4) NOT NULL DEFAULT '',
  `upload` text NOT NULL,
  `center` tinyint(4) NOT NULL DEFAULT '0',
  `ptop` int(11) NOT NULL DEFAULT '0',
  `pleft` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `scroll` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `dispage` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_popup`
--

LOCK TABLES `rb_s_popup` WRITE;
/*!40000 ALTER TABLE `rb_s_popup` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_popup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_referer`
--

DROP TABLE IF EXISTS `rb_s_referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_referer` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `referer` varchar(200) NOT NULL DEFAULT '',
  `req_uri` varchar(200) NOT NULL DEFAULT '',
  `agent` varchar(200) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `gender` tinyint(4) NOT NULL DEFAULT '0',
  `age` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_referer`
--

LOCK TABLES `rb_s_referer` WRITE;
/*!40000 ALTER TABLE `rb_s_referer` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_referer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_scrap`
--

DROP TABLE IF EXISTS `rb_s_scrap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_scrap` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `category` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_scrap`
--

LOCK TABLES `rb_s_scrap` WRITE;
/*!40000 ALTER TABLE `rb_s_scrap` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_scrap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_seo`
--

DROP TABLE IF EXISTS `rb_s_seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_seo` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `rel` tinyint(4) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `keywords` varchar(200) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `classification` varchar(200) NOT NULL DEFAULT '',
  `image_src` text NOT NULL,
  `replyto` varchar(50) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT '',
  `build` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `rel` (`rel`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_seo`
--

LOCK TABLES `rb_s_seo` WRITE;
/*!40000 ALTER TABLE `rb_s_seo` DISABLE KEYS */;
INSERT INTO `rb_s_seo` VALUES (1,0,1,'봇톡스, 1인 1봇','','인공지능 기반의 챗봇 솔루션 BOTTALKS AI','ALL','[132]','','','');
/*!40000 ALTER TABLE `rb_s_seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_site`
--

DROP TABLE IF EXISTS `rb_s_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_site` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `id` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `titlefix` tinyint(4) NOT NULL DEFAULT '0',
  `icon` varchar(50) NOT NULL DEFAULT '',
  `layout` varchar(50) NOT NULL DEFAULT '',
  `startpage` int(11) NOT NULL DEFAULT '0',
  `m_layout` varchar(50) NOT NULL DEFAULT '',
  `m_startpage` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(20) NOT NULL DEFAULT '',
  `open` tinyint(4) NOT NULL DEFAULT '0',
  `dtd` varchar(20) NOT NULL DEFAULT '',
  `nametype` varchar(5) NOT NULL DEFAULT '',
  `timecal` tinyint(4) NOT NULL DEFAULT '0',
  `rewrite` tinyint(4) NOT NULL DEFAULT '0',
  `buffer` tinyint(4) NOT NULL DEFAULT '0',
  `usescode` tinyint(4) NOT NULL DEFAULT '0',
  `headercode` text NOT NULL,
  `footercode` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `id` (`id`),
  KEY `open` (`open`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_site`
--

LOCK TABLES `rb_s_site` WRITE;
/*!40000 ALTER TABLE `rb_s_site` DISABLE KEYS */;
INSERT INTO `rb_s_site` VALUES (1,0,'home','AI Bot','{subject} | {site}',0,'glyphicon glyphicon-home','chatbot-desktop/chat.php',0,'rc-instar/chat.php',0,'',1,'UA-92305998-1','nic',0,0,0,1,'','');
/*!40000 ALTER TABLE `rb_s_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_tag`
--

DROP TABLE IF EXISTS `rb_s_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_tag` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL DEFAULT '0',
  `date` char(8) NOT NULL DEFAULT '',
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `hit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `date` (`date`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_tag`
--

LOCK TABLES `rb_s_tag` WRITE;
/*!40000 ALTER TABLE `rb_s_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_token`
--

DROP TABLE IF EXISTS `rb_s_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_token` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `memberuid` int(11) NOT NULL DEFAULT '0',
  `access_token` varchar(100) NOT NULL DEFAULT '0',
  `expire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `memberuid` (`memberuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_token`
--

LOCK TABLES `rb_s_token` WRITE;
/*!40000 ALTER TABLE `rb_s_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_trackback`
--

DROP TABLE IF EXISTS `rb_s_trackback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_trackback` (
  `uid` int(11) NOT NULL,
  `site` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `parent` varchar(30) NOT NULL DEFAULT '0',
  `parentmbr` int(11) NOT NULL DEFAULT '0',
  `url` varchar(150) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_modify` varchar(14) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `type` (`type`),
  KEY `parent` (`parent`),
  KEY `parentmbr` (`parentmbr`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_trackback`
--

LOCK TABLES `rb_s_trackback` WRITE;
/*!40000 ALTER TABLE `rb_s_trackback` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_trackback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_upload`
--

DROP TABLE IF EXISTS `rb_s_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_upload` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `tmpcode` varchar(20) NOT NULL DEFAULT '',
  `site` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `fileonly` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `ext` varchar(4) NOT NULL DEFAULT '0',
  `fserver` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(150) NOT NULL DEFAULT '',
  `folder` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `tmpname` varchar(100) NOT NULL DEFAULT '',
  `thumbname` varchar(100) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `alt` varchar(50) NOT NULL DEFAULT '',
  `caption` text NOT NULL,
  `description` text NOT NULL,
  `src` text NOT NULL,
  `linkto` tinyint(4) NOT NULL DEFAULT '0',
  `license` tinyint(4) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  `sync` varchar(250) NOT NULL DEFAULT '',
  `linkurl` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `category` (`category`),
  KEY `tmpcode` (`tmpcode`),
  KEY `site` (`site`),
  KEY `mbruid` (`mbruid`),
  KEY `fileonly` (`fileonly`),
  KEY `type` (`type`),
  KEY `ext` (`ext`),
  KEY `name` (`name`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_upload`
--

LOCK TABLES `rb_s_upload` WRITE;
/*!40000 ALTER TABLE `rb_s_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_uploadcat`
--

DROP TABLE IF EXISTS `rb_s_uploadcat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_uploadcat` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL DEFAULT '0',
  `site` int(11) NOT NULL DEFAULT '0',
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `users` text NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `r_num` int(11) NOT NULL DEFAULT '0',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  `d_update` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `site` (`site`),
  KEY `mbruid` (`mbruid`),
  KEY `type` (`type`),
  KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_uploadcat`
--

LOCK TABLES `rb_s_uploadcat` WRITE;
/*!40000 ALTER TABLE `rb_s_uploadcat` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_uploadcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_userpic`
--

DROP TABLE IF EXISTS `rb_s_userpic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_userpic` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `mbruid` int(11) NOT NULL DEFAULT '0',
  `gid` int(11) NOT NULL DEFAULT '0',
  `photo` varchar(100) NOT NULL DEFAULT '',
  `d_regis` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `mbruid` (`mbruid`),
  KEY `d_regis` (`d_regis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_userpic`
--

LOCK TABLES `rb_s_userpic` WRITE;
/*!40000 ALTER TABLE `rb_s_userpic` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_userpic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_s_xtralog`
--

DROP TABLE IF EXISTS `rb_s_xtralog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_s_xtralog` (
  `module` varchar(30) NOT NULL DEFAULT '',
  `parent` int(11) NOT NULL DEFAULT '0',
  `down` text NOT NULL,
  `score1` text NOT NULL,
  `score2` text NOT NULL,
  `report` text NOT NULL,
  KEY `module` (`module`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_s_xtralog`
--

LOCK TABLES `rb_s_xtralog` WRITE;
/*!40000 ALTER TABLE `rb_s_xtralog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_s_xtralog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknown_inputs`
--

DROP TABLE IF EXISTS `unknown_inputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknown_inputs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `input` text NOT NULL,
  `userid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unknown_inputs`
--

LOCK TABLES `unknown_inputs` WRITE;
/*!40000 ALTER TABLE `unknown_inputs` DISABLE KEYS */;
/*!40000 ALTER TABLE `unknown_inputs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_reserve`
--

DROP TABLE IF EXISTS `rb_chatbot_reserve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_reserve` (
  `uid` INT(10) NOT NULL AUTO_INCREMENT,
  `vendor` INT(10) NOT NULL DEFAULT '0',
  `bot` INT(10) NOT NULL DEFAULT '0',
  `roomToken` VARCHAR(100) NOT NULL DEFAULT '',
  `category` VARCHAR(20) NULL DEFAULT '',
  `name` VARCHAR(20) NULL DEFAULT '',
  `phone` VARCHAR(20) NULL DEFAULT '',
  `d_reserve` VARCHAR(14) NULL DEFAULT '',
  `content` TEXT NULL DEFAULT NULL,
  `addval` TEXT NULL DEFAULT NULL,
  `status` VARCHAR(10) NULL DEFAULT '',
  `d_regis` VARCHAR(14) NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `vendor` (`vendor`) USING BTREE,
  KEY `bot` (`bot`) USING BTREE,
  KEY `d_reserve` (`d_reserve`) USING BTREE,
  KEY `d_regis` (`d_regis`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='학원용 예약 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_reserve`
--

LOCK TABLES `rb_chatbot_reserve` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_reserve` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_reserve` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rb_chatbot_faq`
--

DROP TABLE IF EXISTS `rb_chatbot_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rb_chatbot_faq` (
  `uid` INT(10) NOT NULL AUTO_INCREMENT,
  `vendor` INT(10) NOT NULL DEFAULT '0',
  `bot` INT(10) NOT NULL DEFAULT '0',
  `category1` VARCHAR(50) NULL DEFAULT '',
  `category2` VARCHAR(50) NULL DEFAULT '',
  `category3` VARCHAR(50) NULL DEFAULT '',
  `question` VARCHAR(255) NULL DEFAULT '',
  `answer` TEXT NULL DEFAULT NULL,
  `d_regis` VARCHAR(14) NULL DEFAULT '',
  PRIMARY KEY (`uid`) USING BTREE,
  KEY `vendor` (`vendor`) USING BTREE,
  KEY `bot` (`bot`) USING BTREE,
  KEY `d_regis` (`d_regis`) USING BTREE,
  FULLTEXT KEY `question` (`question`) /*!50100 WITH PARSER `ngram` */
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='FAQ 정보';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rb_chatbot_faq`
--

LOCK TABLES `rb_chatbot_faq` WRITE;
/*!40000 ALTER TABLE `rb_chatbot_faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `rb_chatbot_faq` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-03 15:29:51
