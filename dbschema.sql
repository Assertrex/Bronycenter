SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `conversations` (
  `id` int(10) UNSIGNED NOT NULL,
  `members_count` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `messages_count` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `conversations_members` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `conversations_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `key_email` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `hash` char(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `used_ip` varchar(45) DEFAULT NULL,
  `used_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `log_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `agent` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `content` text,
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `edit_count` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `delete_id` int(10) UNSIGNED DEFAULT NULL,
  `delete_ip` varchar(45) DEFAULT NULL,
  `delete_datetime` datetime DEFAULT NULL,
  `delete_reason` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `content` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `posts_edits` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts_likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts_reported` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category` tinyint(3) UNSIGNED NOT NULL,
  `reason` tinytext,
  `datetime` datetime NOT NULL,
  `result` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `displayname_changes` tinyint(3) UNSIGNED NOT NULL,
  `displaynames_recent` varchar(162) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` tinyint(3) UNSIGNED DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `full_description` text,
  `contact_methods` text,
  `favourite_music` text,
  `favourite_movies` text,
  `favourite_games` text,
  `fandom_becameabrony` text,
  `fandom_favouritepony` text,
  `fandom_favouriteepisode` text,
  `creations_links` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users_statistics` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_points` int(10) NOT NULL DEFAULT '0',
  `posts_created` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `posts_likes_given` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `posts_comments_given` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `posts_deleted` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `posts_likes_received` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `posts_comments_received` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conversations_members`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conversations_messages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `key_email`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `log_logins`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts_comments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts_edits`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts_likes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts_reported`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `displayName` (`display_name`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `avatar` (`avatar`);

ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId` (`user_id`);

ALTER TABLE `users_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);


ALTER TABLE `conversations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `conversations_members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `conversations_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `key_email`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `log_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts_edits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts_likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `posts_reported`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users_statistics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
