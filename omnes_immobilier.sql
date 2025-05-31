-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 31 mai 2025 à 21:02
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
-- Structure de la table `achat`
--

DROP TABLE IF EXISTS `achat`;
CREATE TABLE IF NOT EXISTS `achat` (
  `id_achat` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `id_propriete` int NOT NULL,
  `date_achat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_achat`),
  KEY `id_client` (`id_client`),
  KEY `id_propriete` (`id_propriete`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `achat`
--

INSERT INTO `achat` (`id_achat`, `id_client`, `id_propriete`, `date_achat`) VALUES
(3, 5, 25, '2025-05-31 20:14:53');

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `agent`
--

INSERT INTO `agent` (`id_agent`, `id_utilisateur`, `telephone`, `agence`, `specialite`) VALUES
(6, 18, '06 12 34 56 78', 'Paris', 'Immobilier résidentiel'),
(7, 19, '07 98 76 54 32', 'Lille', 'Immobilier commercial'),
(8, 20, '06 00 11 22 33', 'Grenoble', 'Terrain'),
(9, 21, '07 01 23 45 67', 'Lyon', 'Appartement à louer'),
(10, 22, '06 89 45 67 23', 'Paris', 'Immobilier en vente par enchère');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom`) VALUES
(1, 'Immobilier résidentiel'),
(2, 'Immobilier commercial'),
(3, 'Terrain'),
(4, 'Appartement à louer'),
(5, 'Biens en vente par enchère');

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

DROP TABLE IF EXISTS `creneau`;
CREATE TABLE IF NOT EXISTS `creneau` (
  `id_creneau` int NOT NULL AUTO_INCREMENT,
  `id_agent` int NOT NULL,
  `jour` date DEFAULT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_creneau`),
  KEY `id_agent` (`id_agent`)
) ENGINE=MyISAM AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`id_creneau`, `id_agent`, `jour`, `heure_debut`, `heure_fin`, `disponible`) VALUES
(205, 6, '2025-06-01', '10:11:00', '13:11:00', 1),
(197, 10, '2025-06-03', '11:10:00', '12:10:00', 1),
(135, 7, '2025-06-02', '09:00:00', '10:00:00', 0),
(136, 7, '2025-06-03', '11:00:00', '12:00:00', 1),
(199, 10, '2025-06-17', '11:10:00', '12:10:00', 1),
(202, 6, '2025-06-09', '10:11:00', '13:11:00', 1),
(200, 10, '2025-06-24', '11:10:00', '12:10:00', 1),
(203, 6, '2025-06-16', '10:11:00', '13:11:00', 1),
(207, 6, '2025-06-15', '10:11:00', '13:11:00', 1),
(206, 6, '2025-06-08', '10:11:00', '13:11:00', 1),
(201, 6, '2025-06-02', '10:11:00', '13:11:00', 1),
(198, 10, '2025-06-10', '11:10:00', '12:10:00', 1),
(208, 6, '2025-06-22', '10:11:00', '13:11:00', 1),
(204, 6, '2025-06-23', '10:11:00', '13:11:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `encheres`
--

DROP TABLE IF EXISTS `encheres`;
CREATE TABLE IF NOT EXISTS `encheres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_propriete` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `montant_offre` decimal(10,2) NOT NULL,
  `date_offre` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_propriete` (`id_propriete`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `id_expediteur` int NOT NULL,
  `id_destinataire` int NOT NULL,
  `contenu` text,
  `type` enum('texte','audio','video','email') DEFAULT 'texte',
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `id_expediteur` (`id_expediteur`),
  KEY `id_destinataire` (`id_destinataire`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `id_expediteur`, `id_destinataire`, `contenu`, `type`, `date_envoi`) VALUES
(1, 5, 18, 'Bonjour Madame Dupont  jaurais une question par rapport a une de vos prorpietes', 'texte', '2025-05-31 19:46:35'),
(2, 18, 5, 'Oui bien sur a quel sujet', 'texte', '2025-05-31 19:53:48');

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE IF NOT EXISTS `paiement` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `id_propriete` int NOT NULL,
  `nom_carte` varchar(100) NOT NULL,
  `adresse_facturation` text NOT NULL,
  `numero_carte` varchar(20) NOT NULL,
  `expiration` date NOT NULL,
  `code_securite` varchar(4) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `solde_disponible` decimal(10,2) NOT NULL,
  `statut` enum('accepté','refusé') NOT NULL DEFAULT 'refusé',
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement`),
  KEY `id_client` (`id_client`),
  KEY `id_propriete` (`id_propriete`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `photo` varchar(255) DEFAULT NULL,
  `id_categorie` int DEFAULT NULL,
  `vendue` tinyint(1) DEFAULT '0',
  `statut` varchar(20) DEFAULT 'disponible',
  PRIMARY KEY (`id_propriete`),
  KEY `id_agent` (`id_admin`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `propriete`
--

INSERT INTO `propriete` (`id_propriete`, `id_admin`, `titre`, `description`, `adresse`, `ville`, `prix`, `type_bien`, `superficie`, `id_agent`, `photo`, `id_categorie`, `vendue`, `statut`) VALUES
(1, 4, 'Appartement 3 pièces lumineux – Quartier Montmartre', 'Charmant appartement de 65 m² situé au 3e étage sans ascenseur. Très lumineux, avec une vue dégagée sur les toits de Paris. Parquet ancien, cuisine équipée, double vitrage. Idéal pour un couple ou une petite famille.', '42 Rue des Martyrs', 'Paris', 585000.00, 'Appartement', 65, 15, NULL, NULL, 0, 'disponible'),
(3, 4, ' Maison familiale 5 pièces avec jardin – Clamart', ' Maison individuelle de 130 m² sur terrain de 400 m² avec grand jardin arboré. 4 chambres, 2 salles de bain, cuisine ouverte sur séjour, garage, proche écoles et transports.', '18 Allée des Tilleuls', 'Clamart', 789000.00, 'Maison', 130, 15, NULL, NULL, 0, 'disponible'),
(14, 4, 'Appartement lumineux 3 pièces', 'Appartement moderne proche métro.', '45 avenue Foch', 'Lyon', 310000.00, 'Immobilier résidentiel', 70, 6, NULL, 1, 0, 'disponible'),
(13, 4, 'Maison familiale avec jardin', 'Belle maison familiale avec 4 chambres.', '12 rue des Lilas', 'Paris', 650000.00, 'Immobilier résidentiel', 120, 6, NULL, 1, 0, 'disponible'),
(15, 4, 'Duplex moderne', 'Duplex avec terrasse et vue mer.', '89 rue Chanot', 'Marseille', 450000.00, 'Immobilier résidentiel', 95, 6, NULL, 1, 0, 'disponible'),
(16, 4, 'Local commercial centre-ville', 'Local idéal pour une boutique.', '23 rue du Commerce', 'Bordeaux', 520000.00, 'Immobilier commercial', 80, 7, NULL, 2, 0, 'disponible'),
(17, 4, 'Bureaux équipés open-space', 'Bureaux modernes dans immeuble pro.', '99 rue Nationale', 'Lille', 720000.00, 'Immobilier commercial', 150, 7, NULL, 2, 0, 'disponible'),
(18, 4, 'Hôtel particulier à rénover', 'Immeuble pour hôtel ou bureaux.', '3 place Masséna', 'Nice', 980000.00, 'Immobilier commercial', 300, 7, NULL, 2, 0, 'disponible'),
(19, 4, 'Terrain à bâtir', 'Parcelle viabilisée de 1000 m².', 'Chemin des Vignes', 'Toulouse', 180000.00, 'Terrain', 1000, 8, NULL, 3, 0, 'disponible'),
(20, 4, 'Terrain agricole', 'Idéal exploitation maraîchère.', 'Route de la Plaine', 'Angers', 90000.00, 'Terrain', 2500, 8, NULL, 3, 0, 'disponible'),
(21, 0, 'Terrain boisé non constructible', 'Parcelle forestière pour loisirs.', 'Bois de Valmy', 'Dijon', 60000.00, 'Terrain', 3500, 8, NULL, 3, 0, 'disponible'),
(22, 4, 'Studio meublé', 'Studio tout équipé proche tram.', '8 rue Pasteur', 'Grenoble', 550.00, 'Appartement à louer', 25, 9, NULL, 4, 0, 'disponible'),
(23, 4, 'T2 en centre ville', 'Appartement rénové avec balcon.', '55 quai des Bateliers', 'Strasbourg', 720.00, 'Appartement à louer', 45, 9, NULL, 4, 0, 'disponible'),
(24, 4, 'Colocation 4 chambres', 'Idéal pour étudiants, charges comprises.', '12 rue des Étudiants', 'Montpellier', 1200.00, 'Appartement à louer', 110, 9, NULL, 4, 0, 'disponible'),
(25, 4, 'Maison à rénover aux enchères', 'Maison ancienne avec travaux.', '14 rue des Iris', 'Rouen', 200000.00, 'Immobilier en vente par enchère', 90, 10, NULL, NULL, 0, 'disponible'),
(26, 4, 'Appartement en vente rapide', 'Appartement occupé vendu aux enchères.', '30 rue des Capucins', 'Reims', 150000.00, 'Immobilier en vente par enchère', 60, 10, NULL, NULL, 0, 'disponible'),
(27, 4, 'Immeuble locatif', 'Immeuble avec potentiel de rentabilité.', '10 rue d’Aiguillon', 'Nantes', 420000.00, 'Immobilier en vente par enchère', 200, 10, NULL, NULL, 0, 'disponible');

-- --------------------------------------------------------

--
-- Structure de la table `rendezvous`
--

DROP TABLE IF EXISTS `rendezvous`;
CREATE TABLE IF NOT EXISTS `rendezvous` (
  `id_rdv` int NOT NULL AUTO_INCREMENT,
  `id_client` int DEFAULT NULL,
  `id_agent` int DEFAULT NULL,
  `jour` date DEFAULT NULL,
  `heure` time(4) DEFAULT NULL,
  `commentaire` text,
  `id_creneau` int DEFAULT NULL,
  PRIMARY KEY (`id_rdv`),
  KEY `id_client` (`id_client`),
  KEY `id_agent` (`id_agent`),
  KEY `id_creneau` (`jour`),
  KEY `id_creneau_2` (`id_creneau`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rendezvous`
--

INSERT INTO `rendezvous` (`id_rdv`, `id_client`, `id_agent`, `jour`, `heure`, `commentaire`, `id_creneau`) VALUES
(15, 5, 2, '2025-05-30', '14:16:00.0000', NULL, 20),
(16, 5, 3, '2025-05-30', '11:23:00.0000', NULL, 25),
(17, 5, 3, '2025-06-18', '09:15:00.0000', NULL, 100),
(18, 5, 7, '2025-06-02', '09:00:00.0000', NULL, 135),
(19, 5, 6, '2025-06-23', '10:11:00.0000', NULL, 204);

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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`) VALUES
(5, 'Manolf', 'Manolo', 'ichigoamano222@gmail.com', '$2y$10$2VJUsF5kgj8oMgo1eDZ6DOnxjMTE6pQHbG2eM6g.R6vx3ZtyU1i7i', 'Client'),
(4, 'Gangbes', 'Pierre', 'lea.nondome@gmail.com', '$2y$10$x3QzU9tTS6Hgt7gtUxagfui1V.6oyPxwb.KvO.2wc/rfTvAAXh1s6', 'Admin'),
(18, 'Dupont', 'Marie', 'dupont.residentiel@omnes.fr', '$2y$10$dItj1nTjGWIGSDk6cDiWgOUYVuKb5.d3JDDbSGRybOexjWkoKpoW2', 'Agent'),
(19, 'Lefevre', 'Jean', 'lefevre.commercial@omnes.fr', '$2y$10$qlD5mwxXx2lNVPjs3H6Nlu9MvIC8932OXLFQcboHlv8Ttcca9TD6q', 'Agent'),
(20, 'Moreau', 'Claire', 'moreau.terrain@omnes.fr', '$2y$10$JNx2hG2DUtUb3FqfJxeUG.QN5RjSQwuRdVpcgPoPKTOMPYfL6T5Mi', 'Agent'),
(21, 'Robert', 'Alexandre', 'robert.location@omnes.fr', '$2y$10$tvPm8fK5xv39XLZ1TWg6ou3YZGLNBlN3bDv0v2vg3FlV0EnC/p2FK', 'Agent'),
(22, 'Blanc', 'Sophie', 'blanc.enchere@omnes.fr', '$2y$10$cUVxrHk1Kwk1XbFTodOed.IhDlk4nNit3UmTS2v7/zFHg3B33zO8q', 'Agent');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
