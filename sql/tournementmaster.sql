-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 28 juin 2026 à 14:48
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tournementmaster`
--

-- --------------------------------------------------------

--
-- Structure de la table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `game`
--

INSERT INTO `game` (`id`, `game_name`) VALUES
(1, 'valorant'),
(2, 'brawlstar');

-- --------------------------------------------------------

--
-- Structure de la table `party`
--

DROP TABLE IF EXISTS `party`;
CREATE TABLE IF NOT EXISTS `party` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournement_id` int NOT NULL,
  `score_team_1` int DEFAULT NULL,
  `score_team_2` int DEFAULT NULL,
  `winner_team_id` int DEFAULT NULL,
  `time` time DEFAULT NULL,
  `phase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poule_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tournement_id` (`tournement_id`),
  KEY `winner_team_id` (`winner_team_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `party`
--

INSERT INTO `party` (`id`, `tournement_id`, `score_team_1`, `score_team_2`, `winner_team_id`, `time`, `phase`, `poule_name`) VALUES
(4, 9, 2, 0, 8, NULL, 'poule', 'A'),
(5, 9, 2, 1, 9, NULL, 'poule', 'A'),
(6, 9, 2, 0, 9, NULL, 'poule', 'A'),
(7, 9, 2, 0, 8, NULL, 'poule', 'A'),
(8, 9, 2, 0, 8, NULL, 'poule', 'A'),
(9, 9, 2, 1, 10, NULL, 'poule', 'A'),
(10, 9, 2, 0, 13, NULL, 'poule', 'B'),
(11, 9, 2, 0, 13, NULL, 'poule', 'B'),
(12, 9, 2, 0, 13, NULL, 'poule', 'B'),
(13, 9, 2, 0, 14, NULL, 'poule', 'B'),
(14, 9, 2, 0, 14, NULL, 'poule', 'B'),
(15, 9, 2, 0, 15, NULL, 'poule', 'B'),
(16, 9, 2, 0, 8, NULL, 'finale', NULL),
(17, 9, 2, 0, 8, NULL, 'finale', NULL),
(18, 9, 2, 0, 8, NULL, 'finale', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `party_participant`
--

DROP TABLE IF EXISTS `party_participant`;
CREATE TABLE IF NOT EXISTS `party_participant` (
  `party_id` int NOT NULL,
  `team_id` int NOT NULL,
  PRIMARY KEY (`party_id`,`team_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `party_participant`
--

INSERT INTO `party_participant` (`party_id`, `team_id`) VALUES
(4, 8),
(4, 9),
(5, 9),
(5, 10),
(6, 9),
(6, 11),
(7, 8),
(7, 10),
(8, 8),
(8, 11),
(9, 10),
(9, 11),
(10, 13),
(10, 14),
(11, 13),
(11, 15),
(12, 13),
(12, 16),
(13, 14),
(13, 15),
(14, 14),
(14, 16),
(15, 15),
(15, 16),
(16, 8),
(16, 13),
(17, 8),
(17, 13),
(18, 8),
(18, 13);

-- --------------------------------------------------------

--
-- Structure de la table `sanction`
--

DROP TABLE IF EXISTS `sanction`;
CREATE TABLE IF NOT EXISTS `sanction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `sanction_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `sanction_id` (`sanction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sanction_types`
--

DROP TABLE IF EXISTS `sanction_types`;
CREATE TABLE IF NOT EXISTS `sanction_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sanction_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader` int NOT NULL,
  `tournement_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leader` (`leader`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `team`
--

INSERT INTO `team` (`id`, `name`, `leader`, `tournement_id`) VALUES
(9, 'test', 4, 9),
(8, 'baguette', 5, 9),
(7, 'baguette', 5, 8),
(10, 'team3', 6, 9),
(11, 'team4', 7, 9),
(13, 'team6', 9, 9),
(14, 'team5', 8, 9),
(15, 'team7', 10, 9),
(16, 'team8', 11, 9);

-- --------------------------------------------------------

--
-- Structure de la table `team_member`
--

DROP TABLE IF EXISTS `team_member`;
CREATE TABLE IF NOT EXISTS `team_member` (
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`team_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `team_member`
--

INSERT INTO `team_member` (`team_id`, `user_id`) VALUES
(1, 4),
(2, 4),
(2, 5),
(2, 7),
(2, 8),
(3, 4),
(3, 5),
(3, 7),
(3, 8),
(4, 4),
(4, 5),
(4, 7),
(4, 8),
(5, 4),
(5, 5),
(5, 7),
(5, 8),
(6, 4),
(6, 5),
(6, 6),
(6, 8),
(7, 4),
(7, 5),
(7, 7),
(7, 8),
(8, 5),
(9, 4),
(10, 6),
(11, 7),
(12, 8),
(13, 9),
(14, 8),
(15, 10),
(16, 11);

-- --------------------------------------------------------

--
-- Structure de la table `tournement`
--

DROP TABLE IF EXISTS `tournement`;
CREATE TABLE IF NOT EXISTS `tournement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sans titre',
  `owner` int NOT NULL,
  `max_participant` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `date` datetime NOT NULL,
  `format` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tournement`
--

INSERT INTO `tournement` (`id`, `game_id`, `name`, `owner`, `max_participant`, `status`, `date`, `format`) VALUES
(6, 0, 'test', 2, 16, 'finish', '5839-07-25 00:00:00', 1),
(7, 0, 'baguette', 2, 16, 'ongoing', '2026-05-12 00:00:00', 3),
(8, 0, 'Raphale_YT', 2, 16, 'open', '2752-04-25 00:00:00', 4),
(9, 0, 'test_match', 2, 16, 'ongoing', '5421-02-12 00:00:00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `type`) VALUES
(1, 'raphale', 'raphaelecerf@gmail.com', '$2y$10$MOe.WdqXb.mfrFTXjs.eHONDexGJ9GqXuUiVfOw1wMJU820QaV4Sm', 'admin'),
(2, 'mwa', 'test1@gmail.com', '$2y$10$RHfs/SorrQmjccBtAGCud.Wv844K.TXX4jkL.u9hl7ZxRJo6w1b/u', 'gestionnaire'),
(4, 'test', 'test2@gmail.com', '$2y$10$7ywnnICeN1jZZgrM0wjaZ.20KKdR88O/Ow4.5fxfHz5g8uOIRzKL6', 'user'),
(5, 'test1', 'test3@gmail.com', '$2y$10$Fwc3IeKuyVlV2OOcXgcIJekmb6O1W0HGXNQTarME0cnjqWGFMa7Q6', 'user'),
(6, 'jp', 'test4@gmail.com', '$2y$10$V.4CJEy67/gnTAUfsZ0J5ObBBebSP4xpoov3IVfUxeXzj.jjCiFE2', 'user'),
(7, 'test3', 'test5@gmail.com', '$2y$10$su6JcKttqqoxAhFeBElTCuFROK9F6KWyFP9l4TgOfmGLShe21MZW6', 'user'),
(8, 'test', 'test6@gmail.com', '$2y$10$PuQu1YoB5kUkoaRWZdBb6OOZGLY1Yj/qsJr.OHoZNQRoDS2SYcxri', 'user'),
(9, 'j7', 'test7@gmail.com', '$2y$10$Enuy0A135xs4aDgzPDAZh.SZrUo9AOiUjsfQzxjuZpMs0ypRY8JQa', 'user'),
(10, 'j8', 'test8@gmail.com', '$2y$10$7rtIymDIDKr73CpIZBps1.rPV2Lxg53V/d1wmE/xHeMTbMxvTPQ9W', 'user'),
(11, 'j10', 'test9@gmail.com', '$2y$10$tmuk0NfFvnVMinwYBGgENe8MtRkuZPBSapOCM2bjkj1dN8YD3fFEm', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
