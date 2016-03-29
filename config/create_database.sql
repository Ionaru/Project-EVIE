SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `web_evie` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `web_evie`;

CREATE TABLE `apikeys` (
  `apikey_id` int(11) NOT NULL,
  `apikey_pid` varchar(10) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `apikey_name` varchar(50) NOT NULL,
  `apikey_keyid` int(11) NOT NULL,
  `apikey_vcode` varchar(100) NOT NULL,
  `apikey_type` varchar(20) NOT NULL DEFAULT 'Invalid',
  `apikey_isactive` int(1) NOT NULL DEFAULT '0',
  `apikey_dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `apikey_accessmask` int(20) NOT NULL,
  `apikey_expiry` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `apikey_isexpired` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_pid` varchar(10) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_password_hash` varchar(60) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_isadmin` int(1) NOT NULL DEFAULT '0',
  `user_registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_lastseendate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `apikeys`
  ADD PRIMARY KEY (`apikey_id`),
  ADD UNIQUE KEY `apikey_pid` (`apikey_pid`),
  ADD KEY `user_name` (`user_name`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_name`),
  ADD UNIQUE KEY `Unique` (`user_id`),
  ADD UNIQUE KEY `user_pid` (`user_pid`);


ALTER TABLE `apikeys`
  MODIFY `apikey_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `apikeys`
  ADD CONSTRAINT `user_name` FOREIGN KEY (`user_name`) REFERENCES `users` (`user_name`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
