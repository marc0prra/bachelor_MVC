-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 05 oct. 2026 à 09:24
-- Version du serveur : 8.4.11
-- Version de PHP : 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bachelorMVC`
--

-- --------------------------------------------------------

--
-- Structure de la table `Album`
--

CREATE TABLE `Album` (
  `id` int NOT NULL,
  `trackNumber` int NOT NULL,
  `editor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Book`
--

CREATE TABLE `Book` (
  `id` int NOT NULL,
  `pageNumber` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Book`
--

INSERT INTO `Book` (`id`, `pageNumber`) VALUES
(7, 134);

-- --------------------------------------------------------

--
-- Structure de la table `Files`
--

CREATE TABLE `Files` (
  `id` int NOT NULL,
  `media_id` int NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `size` int UNSIGNED NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Files`
--

INSERT INTO `Files` (`id`, `media_id`, `original_name`, `stored_name`, `size`, `mime_type`, `uploaded_at`, `uploaded_by`) VALUES
(3, 2, 'screencapture-github-mozartsduweb-PTO-paid-time-off-pull-67-2026-10-01-09_52_13.png', '718e969e73d5f0efa8c5510a4096eabb.png', 1640086, 'image/png', '2026-10-05 10:12:27', 10),
(4, 7, 'screencapture-servjade-mdw-ovh-8443-smb-web-php-settings-id-44-2026-10-02-14_45_30.png', 'db6fb13b2d53d759506424c8fc821e3c.png', 644092, 'image/png', '2026-10-05 11:17:24', 10);

-- --------------------------------------------------------

--
-- Structure de la table `Media`
--

CREATE TABLE `Media` (
  `id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `disponible` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Media`
--

INSERT INTO `Media` (`id`, `titre`, `auteur`, `disponible`) VALUES
(2, 'avenger', 'stan lee', 0),
(7, 'yfsegduofbv', 'ejbfkbe', 0);

-- --------------------------------------------------------

--
-- Structure de la table `Movie`
--

CREATE TABLE `Movie` (
  `id` int NOT NULL,
  `duration` double NOT NULL,
  `gender` enum('Action','Comedie','Drame','Autre') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Movie`
--

INSERT INTO `Movie` (`id`, `duration`, `gender`) VALUES
(2, 140, 'Action');

-- --------------------------------------------------------

--
-- Structure de la table `Song`
--

CREATE TABLE `Song` (
  `id` int NOT NULL,
  `album_id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `duree` double NOT NULL,
  `note` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Users`
--

INSERT INTO `Users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'marco', 'marcopereira@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$DDaaNhA026m5NNFmeo2pfw$aLkXQob5S3In5qExkjpZ6RuOBUm9Q/75TyigaEOZgbM', '2026-09-15 22:23:26', '2026-09-15 22:23:26'),
(2, 'marco2', 'marco@g.fr', '$argon2id$v=19$m=65536,t=4,p=1$WEYljNa39TnVoFHhGC95oQ$Ksa6jgJjZ3C4KRjhlL7te53s7WHkKZEXv+8NPOp8Wag', '2026-09-15 22:24:51', '2026-09-15 22:24:51'),
(4, 'mc0', 'ma@gm.com', '$argon2id$v=19$m=65536,t=4,p=1$tb7cYAvJpnnlSYUuQT+CdQ$hJ2VVvPJ4h48uc6NM2y6lMxSChVLULxoXKJHI7xD2u0', '2026-09-16 23:17:17', '2026-09-16 23:17:17'),
(8, 'test', 'test@test.fr', '$argon2id$v=19$m=65536,t=4,p=1$/4Qz17cf4GgJRLxy5WWSHw$JE5L7SgWggU1yrLvhoDVSpM2xLZkUImDb7G5APDGeWE', '2026-09-18 22:36:45', '2026-09-18 22:36:45'),
(10, 'marco1', 'mc0@test.fr', '$argon2id$v=19$m=65536,t=4,p=1$3qIv+jEp1Nxw3oRdwjZq7Q$gUOf0QfvK49XvSPwRp79ryWHOVmEp3b9nPUcHem8o5M', '2026-10-05 10:02:15', '2026-10-05 10:02:15');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Album`
--
ALTER TABLE `Album`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Book`
--
ALTER TABLE `Book`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Files`
--
ALTER TABLE `Files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stored_name` (`stored_name`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Index pour la table `Media`
--
ALTER TABLE `Media`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Movie`
--
ALTER TABLE `Movie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Song`
--
ALTER TABLE `Song`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`);

--
-- Index pour la table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Book`
--
ALTER TABLE `Book`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `Files`
--
ALTER TABLE `Files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `Media`
--
ALTER TABLE `Media`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Album`
--
ALTER TABLE `Album`
  ADD CONSTRAINT `fk_album_media` FOREIGN KEY (`id`) REFERENCES `Media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Book`
--
ALTER TABLE `Book`
  ADD CONSTRAINT `fk_book_media` FOREIGN KEY (`id`) REFERENCES `Media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Files`
--
ALTER TABLE `Files`
  ADD CONSTRAINT `fk_files_media` FOREIGN KEY (`media_id`) REFERENCES `Media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_files_user` FOREIGN KEY (`uploaded_by`) REFERENCES `Users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `Movie`
--
ALTER TABLE `Movie`
  ADD CONSTRAINT `fk_movie_media` FOREIGN KEY (`id`) REFERENCES `Media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Song`
--
ALTER TABLE `Song`
  ADD CONSTRAINT `fk_song_album` FOREIGN KEY (`album_id`) REFERENCES `Album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
