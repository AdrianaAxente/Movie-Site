-- Adminer 4.8.3 MySQL 8.0.16 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reviews` (`id`, `movie_id`, `name`, `email`, `message`, `created_at`) VALUES
(1,	22,	'Adriana Axente',	'adrianaaxente@yahoo.com',	'super!',	'2024-10-16 14:26:58'),
(2,	25,	'Adriana Axente',	'adrianaaxente@yahoo.com',	'super!',	'2024-10-16 14:38:43'),
(3,	26,	'Adriana Axente',	'adrianaaxente@yahoo.com',	'super!',	'2024-10-16 14:41:30'),
(4,	27,	'Adriana Axente',	'adrianaaxente@yahoo.com',	'super',	'2024-10-16 14:48:43');

-- 2024-10-16 14:59:09
