SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


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
  `agent` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages_simple_conversations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_one_id` int(10) UNSIGNED NOT NULL,
  `user_two_id` int(10) UNSIGNED NOT NULL,
  `last_message_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `messages_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages_simple_messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `message` text NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `content` text DEFAULT NULL,
  `like_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `edit_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `report_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `report_checked` tinyint(1) NOT NULL DEFAULT 0,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `delete_moderator` tinyint(1) NOT NULL DEFAULT 0,
  `delete_id` int(10) UNSIGNED DEFAULT NULL,
  `delete_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts_comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `content` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts_edits` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `content` text DEFAULT NULL,
  `like_count` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts_likes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts_reported` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `display_name` varchar(32) NOT NULL,
  `username` varchar(24) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `registration_datetime` datetime NOT NULL,
  `login_ip` varchar(45) DEFAULT NULL,
  `login_datetime` datetime DEFAULT NULL,
  `login_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `last_online` datetime DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `avatar` char(16) DEFAULT NULL,
  `account_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `account_standing` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `suggestion_points` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `displayname_changes` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `displaynames_recent` varchar(162) DEFAULT NULL,
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
  `city` varchar(32) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `contact_methods` text DEFAULT NULL,
  `favourite_music` text DEFAULT NULL,
  `favourite_movies` text DEFAULT NULL,
  `favourite_games` text DEFAULT NULL,
  `fandom_becameabrony` text DEFAULT NULL,
  `fandom_favouritepony` text DEFAULT NULL,
  `fandom_favouriteepisode` text DEFAULT NULL,
  `creations_links` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users_statistics` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_points` int(10) NOT NULL DEFAULT 0,
  `posts_created` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_likes_given` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_comments_given` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_removed` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_removed_mod` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_likes_received` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_comments_removed` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_comments_removed_mod` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `posts_comments_received` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
