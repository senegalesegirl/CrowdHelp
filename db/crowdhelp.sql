-- phpMiniAdmin dump 1.9.150917
-- Datetime: 2016-01-12 20:32:26
-- Host: 
-- Database: crowdhelp

/*!40030 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES ('1','Changer les termes Fran-glais en francais dans le texte (150 mots)','Emmanuel Petit','2016-01-08 11:40:00','https://drive.google.com/open?id=1z1bS4ILmGOyzCIUPzNgoPMpabSCFZblq0qFsBbu4ds4','Coloriage','epetit11@gmail.com','1'),('2','Trouver une image (libre de droits) pour un serveur XPM-666 DUalPro Teal\'c','Emmanuel Petit','2016-01-06 19:18:00','','Recherche d\'images','epetit11@gmail.com','3'),('3','Traduire en mandarin l\'introduction de ma thèse : Le mandarin pour les nuls Vol 1 (555 mots)','Emmanuel Petit','2016-01-08 04:37:26','','Traduction','epetit11@gmail.com','3');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;

DROP TABLE IF EXISTS `task_state`;
CREATE TABLE `task_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `task_state` DISABLE KEYS */;
INSERT INTO `task_state` VALUES ('1','En attente'),('2','En cours'),('3','Terminée'),('4','Validée');
/*!40000 ALTER TABLE `task_state` ENABLE KEYS */;

DROP TABLE IF EXISTS `task_types`;
CREATE TABLE `task_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `task_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_types` ENABLE KEYS */;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('1','Petit','Emmanuel ','epetit11@gmail.com','$2y$12$AGjUfW51ew.4.N.hIS0Fcu0d0wAI3IAedMpa2hBPiTcmZ2.WyJWx2','1140','0','d8d32446bcc0d78cefd83aa7663abce6'),('2','Toto','Jean Claude','abc@alphabet.xyz','$2y$12$WUCiJCQoK3SHcSddUQJymeWRF9DGb/44RVTmlTkCbE6BQxQowxWUm','0','0','14adfb1786eb1aefb95e8e3db4504411'),('3','Abh','Samy','samy35@gmail.com','$2y$12$aweTmB73sglNiq0p5gmBouo6UAPmDL30c3RAN13jp5yzzhdZI6.xu','0','0','3673040ba1248a72917ca6f13884ce4f');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

DROP TABLE IF EXISTS `user_activation`;
CREATE TABLE `user_activation` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `user_activation` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activation` ENABLE KEYS */;

DROP TABLE IF EXISTS `user_task`;
CREATE TABLE `user_task` (
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40000 ALTER TABLE `user_task` DISABLE KEYS */;
INSERT INTO `user_task` VALUES ('1','3','2016-01-09 21:04:25'),('1','2','2016-01-09 21:14:46');
/*!40000 ALTER TABLE `user_task` ENABLE KEYS */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;


-- phpMiniAdmin dump end
