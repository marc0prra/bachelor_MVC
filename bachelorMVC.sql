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
-- Structure de la table `Media`
--

CREATE TABLE `Media` (
  `id` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `disponible` tinyint(1) NOT NULL,
  `illustration` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Media`
--

INSERT INTO `Media` (`id`, `titre`, `auteur`, `disponible`, `illustration`) VALUES
(2, 'avenger', 'stan lee', 0, NULL),
(7, 'yfsegduofbv', 'ejbfkbe', 0, NULL);

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
