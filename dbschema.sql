SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `members_count` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `messages_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `conversations_members` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `conversations_messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `message` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `key_email` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `hash` char(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `used_ip` varchar(45) DEFAULT NULL,
  `used_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `log_logins` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `agent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `content` text,
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `delete_id` int(10) UNSIGNED DEFAULT NULL,
  `delete_ip` varchar(45) DEFAULT NULL,
  `delete_datetime` datetime DEFAULT NULL,
  `delete_reason` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts_comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `content` varchar(300) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts_likes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_name` varchar(32) NOT NULL,
  `username` varchar(24) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `password` char(60) NOT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `registration_datetime` datetime NOT NULL,
  `login_ip` varchar(45) DEFAULT NULL,
  `login_datetime` datetime DEFAULT NULL,
  `login_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `last_online` datetime DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `timezone` smallint(6) DEFAULT NULL,
  `avatar` char(16) DEFAULT NULL,
  `account_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `account_standing` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `suggestion_points` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `displayName` (`display_name`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `avatar` (`avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users_details` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` tinyint(3) UNSIGNED DEFAULT NULL,
  `city` varchar(58) DEFAULT NULL,
  `description` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
