-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 30 mai 2025 à 20:04
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `omnes_immobilier`
--

-- --------------------------------------------------------

--
-- Structure de la table `agent`
--

DROP TABLE IF EXISTS `agent`;
CREATE TABLE IF NOT EXISTS `agent` (
  `id_agent` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `agence` varchar(100) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_agent`),
  KEY `fk_utilisateur_agent` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `agent`
--

INSERT INTO `agent` (`id_agent`, `id_utilisateur`, `telephone`, `agence`, `specialite`) VALUES
(3, 15, '', 'Paris', 'Maison'),
(2, 7, '0630452373', 'Paris', 'Immeuble');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

DROP TABLE IF EXISTS `creneau`;
CREATE TABLE IF NOT EXISTS `creneau` (
  `id_creneau` int NOT NULL AUTO_INCREMENT,
  `id_agent` int NOT NULL,
  `date` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_creneau`),
  KEY `id_agent` (`id_agent`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`id_creneau`, `id_agent`, `date`, `heure_debut`, `heure_fin`, `disponible`) VALUES
(100, 3, '2025-06-18', '09:15:00', '10:45:00', 1),
(101, 3, '2025-06-18', '10:45:00', '12:15:00', 1),
(102, 3, '2025-06-18', '12:15:00', '13:45:00', 1),
(104, 3, '2025-06-18', '15:15:00', '16:45:00', 1),
(106, 3, '2025-06-18', '18:15:00', '19:45:00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `propriete`
--

DROP TABLE IF EXISTS `propriete`;
CREATE TABLE IF NOT EXISTS `propriete` (
  `id_propriete` int NOT NULL AUTO_INCREMENT,
  `id_admin` int NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `type_bien` varchar(100) DEFAULT NULL,
  `superficie` int DEFAULT NULL,
  `id_agent` int DEFAULT NULL,
  PRIMARY KEY (`id_propriete`),
  KEY `id_agent` (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `propriete`
--

INSERT INTO `propriete` (`id_propriete`, `id_admin`, `titre`, `description`, `adresse`, `ville`, `prix`, `type_bien`, `superficie`, `id_agent`) VALUES
(1, 4, 'Appartement 3 pièces lumineux – Quartier Montmartre', 'Charmant appartement de 65 m² situé au 3e étage sans ascenseur. Très lumineux, avec une vue dégagée sur les toits de Paris. Parquet ancien, cuisine équipée, double vitrage. Idéal pour un couple ou une petite famille.', '42 Rue des Martyrs', 'Paris', 585000.00, 'Appartement', 65, 15),
(3, 4, ' Maison familiale 5 pièces avec jardin – Clamart', ' Maison individuelle de 130 m² sur terrain de 400 m² avec grand jardin arboré. 4 chambres, 2 salles de bain, cuisine ouverte sur séjour, garage, proche écoles et transports.', '18 Allée des Tilleuls', 'Clamart', 789000.00, 'Maison', 130, 15),
(4, 4, 'Plateau de bureaux 120 m² – Lyon Part-Dieu', ' Espace professionnel climatisé au 4e étage d’un immeuble moderne. Composé de 4 bureaux, salle de réunion, coin cuisine, accès sécurisé. Idéal professions libérales ou startup.', '3 Rue du Professeur Weill', 'Lyon', 349000.00, 'Bureau', 120, NULL),
(12, 4, ' Maison familiale 5 pièces avec jardin – Clamart', 'cd\"', '3 Rue du Professeur Weill', 'Clamart', 0.22, 'Maison', 19, 11),
(11, 4, ' Maison familiale 5 pièces avec jardin – Clamart', 'cd\"', '3 Rue du Professeur Weill', 'Clamart', 0.22, 'Maison', 19, 7);

-- --------------------------------------------------------

--
-- Structure de la table `rendezvous`
--

DROP TABLE IF EXISTS `rendezvous`;
CREATE TABLE IF NOT EXISTS `rendezvous` (
  `id_rdv` int NOT NULL AUTO_INCREMENT,
  `id_client` int DEFAULT NULL,
  `id_agent` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `heure` time(4) DEFAULT NULL,
  `commentaire` text,
  `id_creneau` int DEFAULT NULL,
  PRIMARY KEY (`id_rdv`),
  KEY `id_client` (`id_client`),
  KEY `id_agent` (`id_agent`),
  KEY `id_creneau` (`date`),
  KEY `id_creneau_2` (`id_creneau`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rendezvous`
--

INSERT INTO `rendezvous` (`id_rdv`, `id_client`, `id_agent`, `date`, `heure`, `commentaire`, `id_creneau`) VALUES
(15, 5, 2, '2025-05-30', '14:16:00.0000', NULL, 20),
(16, 5, 3, '2025-05-30', '11:23:00.0000', NULL, 25);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`) VALUES
(5, 'Manolf', 'Manolo', 'ichigoamano222@gmail.com', '$2y$10$2VJUsF5kgj8oMgo1eDZ6DOnxjMTE6pQHbG2eM6g.R6vx3ZtyU1i7i', 'Client'),
(4, 'Gangbes', 'Pierre', 'lea.nondome@gmail.com', '$2y$10$x3QzU9tTS6Hgt7gtUxagfui1V.6oyPxwb.KvO.2wc/rfTvAAXh1s6', 'Admin'),
(7, 'Strazzieri', 'Delphine', 'lea.gangbes@edu.ece.fr', '$2y$10$j4ZpNcIHIygMAOPhOdOCueJ38vHK9jLD3MmE7FVliu6upyGBmyxH.', 'Agent'),
(15, 'Gangbes', 'Delphine', 'delphine.strazzieri@gmail.com', '$2y$10$LJXKUZ2uypvvm53/bPz/2OZsgsC6PBZSEr/9KL7U2X9Oc8g4m5Bke', 'Agent');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
