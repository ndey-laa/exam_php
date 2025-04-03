-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 29 mars 2025 à 19:51
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `location_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

CREATE TABLE `administrateurs` (
  `id_admin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id_admin`, `nom`, `prenom`, `email`, `mot_de_passe_hash`) VALUES
(1, 'Diallo', 'Khalidou', 'khalidou.diallo@email.com', '$2y$10$RsvRYpjiPP.xpdpcP7f9j.KS6C1yFtgr/fWZ8iUFu2yk9uCRn1ori'),
(2, 'Diop', 'Moussa', 'moussa.dio@email.com', '$2y$10$ZUxG144cc2w4BGCqga0AT.vemSlEHLYmvOOKB5D9btduRIpG1r15u');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id_client` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `numero_permis` varchar(100) NOT NULL,
  `mot_de_passe_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `prenom`, `nom`, `email`, `date_naissance`, `adresse`, `numero_permis`, `mot_de_passe_hash`) VALUES
(1, 'Mamadou', 'Diop', 'mamadou.diop@email.com', '1985-05-10', '123 Rue Ndiagane, Dakar', 'AB1234567', '$2y$10$egPGfj.F.3clD14WpMEn0O.rFjbEyoMt3lJPsmRRciF5GQnMyGOUq'),
(2, 'Aminata', 'Sow', 'aminata.sow@email.com', '1990-08-25', '456 Avenue Fann, Dakar', 'CD9876543', '$2y$10$2grFIdWjjPCcUAwZiA8SU.2SwjC39RSy587LA6rc38l6XEkH3tWry'),
(3, 'Ibrahime', 'Ngom', 'ibrahime.ngom@email.com', '1982-12-15', '789 Boulevard Sene, Thiès', 'EF1122334', '$2y$10$JP0uV14jclh7IKZtJVke8.nluqyspVu312E34CAmxRrM7ijdyE3VK'),
(4, 'Fatou', 'Ba', 'fatou.ba@email.com', '1995-03-22', '321 Rue de la Liberté, Saint-Louis', 'GH4455667', '$2y$10$yTQ.CZ2z3M44CiFfEderX.m/8olv9naVjOqeSKAsOh7FhH.kc2bPi'),
(5, 'Ousmane', 'Fall', 'ousmane.fall@email.com', '1988-07-30', '654 Rue de la Gare, Kaolack', 'IJ7788990', '$2y$10$DnL4MmB.OIE2q/t9Bk0GpOwVxHGE7ro1PiTfTb6SuqY5gpDj5N4VG'),
(6, 'Yamai', 'Seck', 'seckyamai@email.com', '2003-08-16', 'Yoff', '773645453', '$2y$10$pGJlsxK3zkPKNnP4jjtNX.dZXv3/7M9g27SfJJN/Zr7Gj2/WpZwLO'),
(7, 'Demba', 'Mbaye', 'mbaye.demba@email.com', '1998-06-11', 'Yoff', '1098743', '$2y$10$EJD3De2izoANBAkXOem1kenMQhtJyTeD1zlevXI8ftw0ql8OE1i8y');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notification`, `id_client`, `message`, `date_creation`, `lu`) VALUES
(1, 7, '✅ Votre réservation #112 a été confirmée !', '2025-03-29 15:20:14', 1),
(2, 7, 'Paiement confirmé pour la réservation #111 (wave)', '2025-03-29 15:36:40', 1),
(3, 7, 'Paiement confirmé pour la réservation #113 (paypal)', '2025-03-29 16:41:43', 1),
(4, 7, '✅ Votre réservation #113 a été confirmée !', '2025-03-29 16:43:47', 1);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id_paiement` int(11) NOT NULL,
  `id_reservation` int(11) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `date_paiement` date DEFAULT NULL,
  `mode_paiement` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `id_reservation`, `montant`, `date_paiement`, `mode_paiement`) VALUES
(1, 113, 36000.00, '2025-03-29', 'paypal'),
(4, 104, 2000.00, '2025-03-13', 'PayPal'),
(6, 108, 5000.00, '2025-03-15', 'orange_money'),
(8, 110, 60000.00, '2025-03-30', 'wave');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservation` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_voiture` varchar(10) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut_paiement` enum('en attente','payé','échoué') DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id_reservation`, `id_client`, `id_voiture`, `date_debut`, `date_fin`, `statut`, `montant`, `statut_paiement`) VALUES
(1, 4, 'A01', '2025-03-04', '2025-03-28', 'Confirmée', 0.00, 'en attente'),
(101, 1, 'A01', '2025-03-10', '2025-03-15', 'Confirmée', 0.00, 'en attente'),
(102, 2, 'B02', '2025-03-11', '2025-03-16', 'Confirmée', 0.00, 'en attente'),
(103, 3, 'C03', '2025-03-12', '2025-03-17', 'Confirmée', 0.00, 'en attente'),
(104, 4, 'D04', '2025-03-13', '2025-03-18', 'Confirmée', 0.00, 'en attente'),
(105, 5, 'E05', '2025-03-14', '2025-03-19', 'Confirmée', 0.00, 'en attente'),
(106, 4, 'C03', '2025-03-30', '2025-04-04', 'Confirmée', 0.00, 'en attente'),
(107, 4, 'E05', '2025-03-30', '2025-04-10', 'Confirmée', 0.00, 'en attente'),
(108, 4, 'A01', '2025-03-15', '2025-03-16', NULL, 5000.00, 'payé'),
(109, 1, 'A03', '2025-03-31', '2025-04-05', NULL, 60000.00, 'payé'),
(110, 7, 'A03', '2025-03-30', '2025-04-04', 'Confirmée', 60000.00, 'payé'),
(111, 7, 'Z03', '2025-04-02', '2025-04-04', 'Confirmée', 12000.00, 'payé'),
(112, 7, 'C03', '2025-04-05', '2025-04-06', 'Confirmée', 4000.00, 'payé'),
(113, 7, 'E05', '2025-03-30', '2025-04-05', 'Confirmée', 36000.00, 'payé');

-- --------------------------------------------------------

--
-- Structure de la table `voitures`
--

CREATE TABLE `voitures` (
  `id_voiture` varchar(10) NOT NULL,
  `marque` varchar(50) DEFAULT NULL,
  `modele` varchar(50) DEFAULT NULL,
  `annee` year(4) DEFAULT NULL,
  `plaque_immatriculation` varchar(15) DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `tarif_journalier` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voitures`
--

INSERT INTO `voitures` (`id_voiture`, `marque`, `modele`, `annee`, `plaque_immatriculation`, `statut`, `image`, `tarif_journalier`) VALUES
('A01', 'Toyota', 'Corolla', '2020', 'ABC1234', 'Réservée', 'toyota-corolla-front-view.jpg', 5000.00),
('A03', 'Audi', 'A4', '2020', 'SL2014', 'Disponible', 'audia4sedan20201.jpg', 12000.00),
('B02', 'Peugeot', '308', '2019', 'XYZ5678', 'Réservée', '1PP5A5P_0MM50NVH_vue002_1230x750_MOBILE.avif', 6000.00),
('C03', 'Ford', 'Focus', '2021', 'LMN9101', 'Disponible', 'ford-focus-st-edition-2024.jpg', 4000.00),
('D04', 'Renault', 'Clio', '2018', 'OPQ2345', 'Réservée', 'S0-renault-clio-comment-le-prix-de-base-s-est-envole-en-deux-ans-192370.jpg\r\n', 8000.00),
('E05', 'Nissan', 'Qashqai', '2022', 'RST6789', 'Disponible', '01-qq-article-page-header-image.jpg', 6000.00),
('Z02', 'BMW', 'Série 3', '2022', 'DKR1209', 'Disponible', '15235_st0640_116.png', 20000.00),
('Z03', 'Honda', 'Civic', '2019', 'THS2022', 'Disponible', 'model-image-2019-civic-sedan-front.png\r\n', 6000.00);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id_client`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `numero_permis` (`numero_permis`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_client` (`id_client`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `id_reservation` (`id_reservation`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_voiture` (`id_voiture`);

--
-- Index pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD PRIMARY KEY (`id_voiture`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_reservation`) REFERENCES `reservations` (`id_reservation`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_voiture`) REFERENCES `voitures` (`id_voiture`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
