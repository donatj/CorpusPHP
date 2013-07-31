/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `module` varchar(255) CHARACTER SET ascii NOT NULL,
  `key` varchar(255) CHARACTER SET ascii NOT NULL,
  `value` longblob NOT NULL,
  `expires` datetime NOT NULL,
  `autoclear` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`module`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` text,
  `page_header` text,
  `description` text,
  `large_description` text,
  `head` text,
  `details` text,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `list` smallint(1) DEFAULT '1',
  `sort` int(11) NOT NULL,
  `layout` varchar(255) DEFAULT NULL,
  `template` varchar(255) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_keywords` text,
  `meta_description` text,
  `redirect` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `supplementary` text COMMENT 'JSON encoded supplementary data to the data row.',
  PRIMARY KEY (`categories_id`),
  KEY `parent_id` (`parent_id`),
  KEY `layout` (`template`),
  KEY `url` (`url`),
  FULLTEXT KEY `fts` (`name`,`description`,`large_description`,`details`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `categories` VALUES (1,0,'Home',NULL,'test',NULL,NULL,1,1,1,NULL,'-2',NULL,NULL,NULL,'index.php',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),(2,0,'CorpusPHP',NULL,'CorpusPHP is setting out to be a world class PHP Framework. The aim is to provide simple encapsulation, modularization, and helpful tools, all without changing the way you write PHP.\n<br /><br />\n<strong>Project Information, documentation, and GIT repository coming soon.</strong>',NULL,NULL,1,1,4,NULL,'plain',NULL,NULL,NULL,NULL,'CorpusPHP','0000-00-00 00:00:00','0000-00-00 00:00:00',NULL),(3,0,'Contact',NULL,NULL,NULL,NULL,1,1,5,NULL,'-2',NULL,NULL,NULL,'contact',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_filters` (
  `categories_filter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categories_id` int(11) NOT NULL,
  `filter` varchar(100) NOT NULL,
  `sort` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`categories_filter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_tags` (
  `categories_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `grouping` varchar(255) NOT NULL DEFAULT 'default',
  `grouping_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `comment_date` datetime NOT NULL,
  `comment_ip` varchar(255) NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `module` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT '',
  `key` varchar(255) CHARACTER SET ascii NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`module`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Set as InnoDB for efficency puproses, keeps keys tidy.';
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `config` VALUES ('','DEFAULT_META_DESC',''),('','DEFAULT_META_KEYS',''),('','DEFAULT_META_TITLE','CorpusPHP Base'),('','DEFAULT_META_TITLE_POST',' &mdash; CorpusPHP'),('','DEFAULT_META_TITLE_PRE',''),('','DEFAULT_TIMEZONE','America/Chicago'),('','DISPLAY_DATE_FORMAT','M. j, Y'),('','GENERIC_DB_ERROR','An unspecified error occured, Please try again later.'),('','GENERIC_LOGIN_ERROR','Access Violation - You must be signed in'),('','GENERIC_PERM_ERROR','User lacks access rights'),('','GENERIC_USER_ERROR','The intergalactic space cat does not approve of your user'),('','PAGELOADS','0'),('','PATTERN_MODULE_CALL','#%([a-z0-9_/-]+?)([[{])(.*?)[\\]}]%#i'),('','PATTERN_SEO_URL','([^a-zA-Z0-9_\\-\\.]+)'),('','STORE_NAME','CorpusPHP');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crux` (
  `key` varchar(255) CHARACTER SET ascii NOT NULL,
  `value` longtext,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Set as InnoDB for efficency puproses, keeps keys tidy.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logtime` datetime NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `request` text,
  `data` text,
  `data2` text,
  `data3` text,
  `data4` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `access` enum('admin','superadmin','user') NOT NULL DEFAULT 'user',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL COMMENT 'Only applies to Institutions',
  `company` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(7) NOT NULL,
  `zip` varchar(63) NOT NULL,
  `phone` varchar(63) NOT NULL,
  `cell` varchar(63) NOT NULL,
  `fax` varchar(63) NOT NULL,
  `country` varchar(255) NOT NULL,
  `active` smallint(1) unsigned NOT NULL DEFAULT '1',
  `deleted` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`) USING BTREE,
  UNIQUE KEY `email` (`email`),
  KEY `access` (`access`),
  KEY `active` (`active`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `users` VALUES (1,'admin','admin@donatstudios.com','password','CorpusPHP','Admin',NULL,'Donat Studios','','','','','','','','','',1,0);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zones` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_country_id` int(11) NOT NULL,
  `zone_code` varchar(32) DEFAULT NULL,
  `zone_name` varchar(32) DEFAULT NULL,
  `us_territory` tinyint(1) NOT NULL,
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO `zones` VALUES (1,223,'AL','Alabama',0),(2,223,'AK','Alaska',0),(3,223,'AS','American Samoa',1),(4,223,'AZ','Arizona',0),(5,223,'AR','Arkansas',0),(6,223,'AF','Armed Forces Africa',1),(7,223,'AA','Armed Forces Americas',1),(8,223,'AC','Armed Forces Canada',1),(9,223,'AE','Armed Forces Europe',1),(10,223,'AM','Armed Forces Middle East',1),(11,223,'AP','Armed Forces Pacific',1),(12,223,'CA','California',0),(13,223,'CO','Colorado',0),(14,223,'CT','Connecticut',0),(15,223,'DE','Delaware',0),(16,223,'DC','District of Columbia',0),(17,223,'FM','Federated States Of Micronesia',1),(18,223,'FL','Florida',0),(19,223,'GA','Georgia',0),(20,223,'GU','Guam',1),(21,223,'HI','Hawaii',0),(22,223,'ID','Idaho',0),(23,223,'IL','Illinois',0),(24,223,'IN','Indiana',0),(25,223,'IA','Iowa',0),(26,223,'KS','Kansas',0),(27,223,'KY','Kentucky',0),(28,223,'LA','Louisiana',0),(29,223,'ME','Maine',0),(30,223,'MH','Marshall Islands',1),(31,223,'MD','Maryland',0),(32,223,'MA','Massachusetts',0),(33,223,'MI','Michigan',0),(34,223,'MN','Minnesota',0),(35,223,'MS','Mississippi',0),(36,223,'MO','Missouri',0),(37,223,'MT','Montana',0),(38,223,'NE','Nebraska',0),(39,223,'NV','Nevada',0),(40,223,'NH','New Hampshire',0),(41,223,'NJ','New Jersey',0),(42,223,'NM','New Mexico',0),(43,223,'NY','New York',0),(44,223,'NC','North Carolina',0),(45,223,'ND','North Dakota',0),(46,223,'MP','Northern Mariana Islands',1),(47,223,'OH','Ohio',0),(48,223,'OK','Oklahoma',0),(49,223,'OR','Oregon',0),(50,223,'PW','Palau',1),(51,223,'PA','Pennsylvania',0),(52,223,'PR','Puerto Rico',1),(53,223,'RI','Rhode Island',0),(54,223,'SC','South Carolina',0),(55,223,'SD','South Dakota',0),(56,223,'TN','Tennessee',0),(57,223,'TX','Texas',0),(58,223,'UT','Utah',0),(59,223,'VT','Vermont',0),(60,223,'VI','Virgin Islands',1),(61,223,'VA','Virginia',0),(62,223,'WA','Washington',0),(63,223,'WV','West Virginia',0),(64,223,'WI','Wisconsin',0),(65,223,'WY','Wyoming',0),(66,38,'AB','Alberta',0),(67,38,'BC','British Columbia',0),(68,38,'MB','Manitoba',0),(69,38,'NF','Newfoundland',0),(70,38,'NB','New Brunswick',0),(71,38,'NS','Nova Scotia',0),(72,38,'NT','Northwest Territories',0),(73,38,'NU','Nunavut',0),(74,38,'ON','Ontario',0),(75,38,'PE','Prince Edward Island',0),(76,38,'QC','Quebec',0),(77,38,'SK','Saskatchewan',0),(78,38,'YT','Yukon Territory',0),(79,81,'NDS','Niedersachsen',0),(80,81,'BAW','Baden-W?rttemberg',0),(81,81,'BAY','Bayern',0),(82,81,'BER','Berlin',0),(83,81,'BRG','Brandenburg',0),(84,81,'BRE','Bremen',0),(85,81,'HAM','Hamburg',0),(86,81,'HES','Hessen',0),(87,81,'MEC','Mecklenburg-Vorpommern',0),(88,81,'NRW','Nordrhein-Westfalen',0),(89,81,'RHE','Rheinland-Pfalz',0),(90,81,'SAR','Saarland',0),(91,81,'SAS','Sachsen',0),(92,81,'SAC','Sachsen-Anhalt',0),(93,81,'SCN','Schleswig-Holstein',0),(94,81,'THE','Th?ringen',0),(95,14,'WI','Wien',0),(96,14,'NO','Nieder?sterreich',0),(97,14,'OO','Ober?sterreich',0),(98,14,'SB','Salzburg',0),(99,14,'KN','K?rnten',0),(100,14,'ST','Steiermark',0),(101,14,'TI','Tirol',0),(102,14,'BL','Burgenland',0),(103,14,'VB','Voralberg',0),(104,204,'AG','Aargau',0),(105,204,'AI','Appenzell Innerrhoden',0),(106,204,'AR','Appenzell Ausserrhoden',0),(107,204,'BE','Bern',0),(108,204,'BL','Basel-Landschaft',0),(109,204,'BS','Basel-Stadt',0),(110,204,'FR','Freiburg',0),(111,204,'GE','Genf',0),(112,204,'GL','Glarus',0),(113,204,'JU','Graub?nden',0),(114,204,'JU','Jura',0),(115,204,'LU','Luzern',0),(116,204,'NE','Neuenburg',0),(117,204,'NW','Nidwalden',0),(118,204,'OW','Obwalden',0),(119,204,'SG','St. Gallen',0),(120,204,'SH','Schaffhausen',0),(121,204,'SO','Solothurn',0),(122,204,'SZ','Schwyz',0),(123,204,'TG','Thurgau',0),(124,204,'TI','Tessin',0),(125,204,'UR','Uri',0),(126,204,'VD','Waadt',0),(127,204,'VS','Wallis',0),(128,204,'ZG','Zug',0),(129,204,'ZH','Z?rich',0),(130,195,'A Coru?a','A Coru?a',0),(131,195,'Alava','Alava',0),(132,195,'Albacete','Albacete',0),(133,195,'Alicante','Alicante',0),(134,195,'Almeria','Almeria',0),(135,195,'Asturias','Asturias',0),(136,195,'Avila','Avila',0),(137,195,'Badajoz','Badajoz',0),(138,195,'Baleares','Baleares',0),(139,195,'Barcelona','Barcelona',0),(140,195,'Burgos','Burgos',0),(141,195,'Caceres','Caceres',0),(142,195,'Cadiz','Cadiz',0),(143,195,'Cantabria','Cantabria',0),(144,195,'Castellon','Castellon',0),(145,195,'Ceuta','Ceuta',0),(146,195,'Ciudad Real','Ciudad Real',0),(147,195,'Cordoba','Cordoba',0),(148,195,'Cuenca','Cuenca',0),(149,195,'Girona','Girona',0),(150,195,'Granada','Granada',0),(151,195,'Guadalajara','Guadalajara',0),(152,195,'Guipuzcoa','Guipuzcoa',0),(153,195,'Huelva','Huelva',0),(154,195,'Huesca','Huesca',0),(155,195,'Jaen','Jaen',0),(156,195,'La Rioja','La Rioja',0),(157,195,'Las Palmas','Las Palmas',0),(158,195,'Leon','Leon',0),(159,195,'Lleida','Lleida',0),(160,195,'Lugo','Lugo',0),(161,195,'Madrid','Madrid',0),(162,195,'Malaga','Malaga',0),(163,195,'Melilla','Melilla',0),(164,195,'Murcia','Murcia',0),(165,195,'Navarra','Navarra',0),(166,195,'Ourense','Ourense',0),(167,195,'Palencia','Palencia',0),(168,195,'Pontevedra','Pontevedra',0),(169,195,'Salamanca','Salamanca',0),(170,195,'Santa Cruz de Tenerife','Santa Cruz de Tenerife',0),(171,195,'Segovia','Segovia',0),(172,195,'Sevilla','Sevilla',0),(173,195,'Soria','Soria',0),(174,195,'Tarragona','Tarragona',0),(175,195,'Teruel','Teruel',0),(176,195,'Toledo','Toledo',0),(177,195,'Valencia','Valencia',0),(178,195,'Valladolid','Valladolid',0),(179,195,'Vizcaya','Vizcaya',0),(180,195,'Zamora','Zamora',0),(181,195,'Zaragoza','Zaragoza',0);
