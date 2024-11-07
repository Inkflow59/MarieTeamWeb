-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 nov. 2024 à 12:13
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `marieteam`
--

-- --------------------------------------------------------

--
-- Structure de la table `bateau`
--

CREATE TABLE `bateau` (
  `idBat` int(11) NOT NULL,
  `nomBat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `bateau`
--

INSERT INTO `bateau` (`idBat`, `nomBat`) VALUES
(1, 'MS Stena Adventurer'),
(2, 'MS Finnstar'),
(3, 'Pride of Hull'),
(4, 'MS Moby Wonder'),
(5, 'HSC Francisco'),
(6, 'MV Ulysses'),
(7, 'MS Baltivia'),
(8, 'Aurelia Express'),
(9, 'MS Regalia'),
(10, 'MV Spirit of France'),
(11, 'Spirit of Tasmania I'),
(12, 'MS Diana'),
(13, 'Blue Star Patmos'),
(14, 'MS Kong Harald'),
(15, 'Nils Holgersson'),
(16, 'GNV Allegra'),
(17, 'Stena Line Superfast X'),
(18, 'Moby Aki'),
(19, 'Lindsay II'),
(20, 'Tirrenia Nuraghes');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `lettre` char(1) NOT NULL,
  `libelleCat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`lettre`, `libelleCat`) VALUES
('P', 'Passager'),
('V', 'Véhicule');

-- --------------------------------------------------------

--
-- Structure de la table `contenir`
--

CREATE TABLE `contenir` (
  `lettre` char(1) NOT NULL,
  `idBat` int(11) NOT NULL,
  `capaciteMax` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `contenir`
--

INSERT INTO `contenir` (`lettre`, `idBat`, `capaciteMax`) VALUES
('P', 1, 1200),
('P', 2, 2000),
('P', 3, 1200),
('P', 4, 1500),
('P', 5, 500),
('P', 6, 1400),
('P', 7, 1000),
('P', 8, 1500),
('P', 9, 1200),
('P', 10, 1800),
('P', 11, 800),
('P', 12, 1000),
('P', 13, 800),
('P', 14, 1000),
('P', 15, 900),
('P', 16, 600),
('P', 17, 1200),
('P', 18, 1000),
('P', 19, 1500),
('P', 20, 1200),
('V', 1, 200),
('V', 2, 450),
('V', 3, 250),
('V', 4, 300),
('V', 6, 500),
('V', 8, 350),
('V', 10, 400),
('V', 11, 250),
('V', 13, 200),
('V', 14, 300),
('V', 15, 150),
('V', 17, 350),
('V', 18, 300),
('V', 20, 250);

-- --------------------------------------------------------

--
-- Structure de la table `enregistrer`
--

CREATE TABLE `enregistrer` (
  `idType` int(11) NOT NULL,
  `numRes` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `liaison`
--

CREATE TABLE `liaison` (
  `code` int(11) NOT NULL,
  `distance` float DEFAULT NULL,
  `idSecteur` int(11) DEFAULT NULL,
  `idPort_Depart` int(11) DEFAULT NULL,
  `idPort_Arrivee` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `liaison`
--

INSERT INTO `liaison` (`code`, `distance`, `idSecteur`, `idPort_Depart`, `idPort_Arrivee`) VALUES
(1, 250.5, 1, 1, 2),
(2, 250.5, 1, 2, 1),
(3, 320.7, 2, 3, 4),
(4, 320.7, 2, 4, 3),
(5, 150.3, 3, 5, 6),
(6, 150.3, 3, 6, 5),
(7, 120.5, 4, 20, 21),
(8, 120.5, 4, 21, 20),
(9, 450.2, 1, 7, 10),
(10, 450.2, 1, 10, 7),
(11, 380.1, 3, 9, 12),
(12, 380.1, 3, 12, 9),
(13, 130.4, 2, 14, 15),
(14, 130.4, 2, 15, 14),
(15, 550, 4, 18, 19),
(16, 550, 4, 19, 18),
(17, 410.6, 2, 25, 27),
(18, 410.6, 2, 27, 25),
(19, 100, 1, 16, 17),
(20, 100, 1, 17, 16),
(21, 200.5, 1, 3, 5),
(22, 200.5, 1, 5, 3),
(23, 180.3, 3, 8, 9),
(24, 180.3, 3, 9, 8),
(25, 230.2, 2, 11, 13),
(26, 230.2, 2, 13, 11),
(27, 400, 2, 6, 7),
(28, 400, 2, 7, 6),
(29, 120, 1, 14, 20),
(30, 120, 1, 20, 14),
(31, 450.3, 3, 8, 12),
(32, 450.3, 3, 12, 8),
(33, 330, 1, 5, 2),
(34, 330, 1, 2, 5),
(35, 370.5, 4, 16, 17),
(36, 370.5, 4, 17, 16),
(37, 500.2, 2, 15, 21),
(38, 500.2, 2, 21, 15),
(39, 600, 4, 18, 22),
(40, 600, 4, 22, 18),
(41, 350.5, 3, 23, 24),
(42, 350.5, 3, 24, 23),
(43, 420.3, 2, 13, 14),
(44, 420.3, 2, 14, 13),
(45, 300.4, 1, 6, 8),
(46, 300.4, 1, 8, 6),
(47, 250.7, 2, 11, 19),
(48, 250.7, 2, 19, 11),
(49, 100.3, 4, 16, 20),
(50, 100.3, 4, 20, 16),
(51, 350, 2, 25, 18),
(52, 350, 2, 18, 25),
(53, 520.4, 1, 7, 9),
(54, 520.4, 1, 9, 7),
(55, 280.2, 3, 4, 6),
(56, 280.2, 3, 6, 4),
(57, 300, 4, 19, 16),
(58, 300, 4, 16, 19),
(59, 390.4, 1, 1, 6),
(60, 390.4, 1, 6, 1),
(61, 310.2, 2, 3, 12),
(62, 310.2, 2, 12, 3),
(63, 500.3, 3, 9, 5),
(64, 500.3, 3, 5, 9),
(65, 380.1, 4, 14, 15),
(66, 380.1, 4, 15, 14),
(67, 510, 2, 2, 5),
(68, 510, 2, 5, 2),
(69, 400.6, 3, 10, 11),
(70, 400.6, 3, 11, 10),
(71, 220.3, 1, 4, 7),
(72, 220.3, 1, 7, 4),
(73, 500.8, 4, 6, 23),
(74, 500.8, 4, 23, 6),
(75, 450.1, 2, 16, 9),
(76, 450.1, 2, 9, 16),
(77, 290.5, 3, 13, 18),
(78, 290.5, 3, 18, 13),
(79, 380, 2, 4, 17),
(80, 380, 2, 17, 4),
(81, 320.2, 1, 6, 16),
(82, 320.2, 1, 16, 6),
(83, 290.3, 1, 19, 21),
(84, 290.3, 1, 21, 19),
(85, 500.5, 2, 18, 12),
(86, 500.5, 2, 12, 18),
(87, 600.4, 3, 20, 3),
(88, 600.4, 3, 3, 20),
(89, 220.2, 4, 7, 23),
(90, 220.2, 4, 23, 7),
(91, 400.1, 2, 4, 8),
(92, 400.1, 2, 8, 4),
(93, 480.7, 1, 14, 21),
(94, 480.7, 1, 21, 14),
(95, 320.6, 4, 5, 9),
(96, 320.6, 4, 9, 5),
(97, 550.3, 2, 16, 23),
(98, 550.3, 2, 23, 16),
(99, 510.2, 3, 2, 17),
(100, 510.2, 3, 17, 2),
(101, 320.4, 1, 12, 20),
(102, 320.4, 1, 20, 12),
(103, 380.8, 4, 9, 18),
(104, 380.8, 4, 18, 9),
(105, 450.2, 2, 1, 3),
(106, 450.2, 2, 3, 1),
(107, 360.7, 3, 16, 12),
(108, 360.7, 3, 12, 16),
(109, 410, 1, 15, 18),
(110, 410, 1, 18, 15),
(111, 500.7, 4, 2, 18),
(112, 500.7, 4, 18, 2),
(113, 520.4, 3, 10, 14),
(114, 520.4, 3, 14, 10),
(115, 420.6, 2, 17, 21),
(116, 420.6, 2, 21, 17),
(117, 250.2, 1, 6, 9),
(118, 250.2, 1, 9, 6),
(119, 530.5, 4, 7, 19),
(120, 530.5, 4, 19, 7),
(121, 600.1, 2, 14, 16),
(122, 600.1, 2, 16, 14),
(123, 500.2, 3, 20, 9),
(124, 500.2, 3, 9, 20),
(125, 330.5, 1, 12, 6),
(126, 330.5, 1, 6, 12);

-- --------------------------------------------------------

--
-- Structure de la table `periode`
--

CREATE TABLE `periode` (
  `dateDeb` date NOT NULL,
  `dateFin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `periode`
--

INSERT INTO `periode` (`dateDeb`, `dateFin`) VALUES
('2024-01-01', '2024-05-31'),
('2024-06-01', '2024-12-31'),
('2025-01-01', '2025-05-31'),
('2025-06-01', '2025-12-31');

-- --------------------------------------------------------

--
-- Structure de la table `port`
--

CREATE TABLE `port` (
  `idPort` int(11) NOT NULL,
  `nomPort` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `port`
--

INSERT INTO `port` (`idPort`, `nomPort`) VALUES
(1, 'Marseille'),
(2, 'Le Havre'),
(3, 'Nantes'),
(4, 'Bordeaux'),
(5, 'Lorient'),
(6, 'Brest'),
(7, 'Saint-Nazaire'),
(8, 'Dunkerque'),
(9, 'Rouen'),
(10, 'Calais'),
(11, 'La Rochelle'),
(12, 'Cherbourg'),
(13, 'Toulon'),
(14, 'Nice'),
(15, 'Boulogne-sur-Mer'),
(16, 'Sète'),
(17, 'Dieppe'),
(18, 'Caen'),
(19, 'Saint-Malo'),
(20, 'Ajaccio'),
(21, 'Bastia'),
(22, 'Porto-Vecchio'),
(23, 'Bonifacio'),
(24, 'Quiberon'),
(25, 'Port-Vendres'),
(26, 'Roscoff'),
(27, 'Les Sables-d\'Olonne'),
(28, 'Pornic'),
(29, 'Bayonne'),
(30, 'Villefranche-sur-Mer'),
(31, 'Port-Saint-Louis-du-Rhône'),
(32, 'Concarneau'),
(33, 'Granville'),
(34, 'Fécamp'),
(35, 'Cannes'),
(36, 'La Ciotat'),
(37, 'Saint-Tropez'),
(38, 'Arcachon'),
(39, 'Vannes'),
(40, 'Saint-Raphaël');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `numRes` int(11) NOT NULL,
  `nomRes` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `codePostal` varchar(5) DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `numTra` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `secteur`
--

CREATE TABLE `secteur` (
  `idSecteur` int(11) NOT NULL,
  `nomSecteur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `secteur`
--

INSERT INTO `secteur` (`idSecteur`, `nomSecteur`) VALUES
(1, 'Méditerranée'),
(2, 'Atlantique'),
(3, 'Manche'),
(4, 'Corse');

-- --------------------------------------------------------

--
-- Structure de la table `tarifer`
--

CREATE TABLE `tarifer` (
  `dateDeb` date NOT NULL,
  `idType` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `tarif` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `traversee`
--

CREATE TABLE `traversee` (
  `numTra` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `heure` time DEFAULT NULL,
  `idBat` int(11) DEFAULT NULL,
  `code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `traversee`
--

INSERT INTO `traversee` (`numTra`, `date`, `heure`, `idBat`, `code`) VALUES
(1, '2024-12-14', '18:26:00', 8, 4),
(2, '2024-12-11', '06:47:00', 9, 4),
(3, '2024-12-13', '07:13:00', 10, 4),
(4, '2024-12-12', '07:11:00', 5, 3),
(5, '2024-12-16', '16:26:00', 8, 3),
(6, '2024-12-14', '18:22:00', 15, 3),
(7, '2024-12-17', '09:11:00', 14, 3),
(8, '2024-12-12', '10:50:00', 2, 3),
(9, '2024-12-17', '18:00:00', 13, 3),
(10, '2024-12-15', '13:44:00', 4, 4),
(11, '2024-12-16', '17:46:00', 16, 4),
(12, '2024-12-14', '13:19:00', 9, 1),
(13, '2024-12-12', '11:50:00', 3, 3),
(14, '2024-12-12', '16:52:00', 4, 2),
(15, '2024-12-14', '09:56:00', 18, 3),
(16, '2024-12-14', '07:06:00', 13, 3),
(17, '2024-12-14', '06:24:00', 5, 2),
(18, '2024-12-17', '10:23:00', 18, 3),
(19, '2024-12-13', '09:29:00', 9, 1),
(20, '2024-12-14', '18:30:00', 9, 4),
(21, '2024-12-16', '08:34:00', 2, 3),
(22, '2024-12-13', '11:56:00', 10, 3),
(23, '2024-12-14', '11:00:00', 5, 4),
(24, '2024-12-11', '16:12:00', 5, 3),
(25, '2024-12-13', '13:25:00', 3, 1),
(26, '2024-12-14', '07:38:00', 12, 1),
(27, '2024-12-15', '08:22:00', 20, 3),
(28, '2024-12-16', '10:03:00', 5, 3),
(29, '2024-12-16', '15:07:00', 1, 3),
(30, '2024-12-14', '07:16:00', 5, 4),
(31, '2024-12-14', '11:02:00', 11, 3),
(32, '2024-12-16', '08:01:00', 13, 2),
(33, '2024-12-13', '12:51:00', 11, 3),
(34, '2024-12-15', '16:37:00', 19, 1),
(35, '2024-12-12', '10:00:00', 1, 2),
(36, '2024-12-17', '09:01:00', 13, 2),
(37, '2024-12-17', '12:04:00', 3, 4),
(38, '2024-12-16', '15:42:00', 14, 3),
(39, '2024-12-14', '09:00:00', 4, 2),
(40, '2024-12-13', '06:39:00', 6, 4),
(41, '2024-12-14', '09:08:00', 19, 2),
(42, '2024-12-17', '09:41:00', 14, 1),
(43, '2024-12-13', '12:27:00', 11, 2),
(44, '2024-12-16', '17:00:00', 5, 2),
(45, '2024-12-15', '16:43:00', 9, 3),
(46, '2024-12-11', '17:05:00', 17, 3),
(47, '2024-12-17', '11:40:00', 20, 2),
(48, '2024-12-17', '11:51:00', 19, 3),
(49, '2024-12-17', '08:41:00', 15, 3),
(50, '2024-12-15', '07:53:00', 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `type`
--

CREATE TABLE `type` (
  `idType` int(11) NOT NULL,
  `libelleType` varchar(255) DEFAULT NULL,
  `lettre` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `type`
--

INSERT INTO `type` (`idType`, `libelleType`, `lettre`) VALUES
(1, 'Adulte', 'P'),
(2, 'Enfant', 'P'),
(3, 'Senior', 'P'),
(4, 'Voiture', 'V'),
(5, 'Moto', 'V'),
(6, 'Camion', 'V'),
(7, 'Camping-car', 'V');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bateau`
--
ALTER TABLE `bateau`
  ADD PRIMARY KEY (`idBat`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`lettre`);

--
-- Index pour la table `contenir`
--
ALTER TABLE `contenir`
  ADD PRIMARY KEY (`lettre`,`idBat`);

--
-- Index pour la table `enregistrer`
--
ALTER TABLE `enregistrer`
  ADD PRIMARY KEY (`idType`,`numRes`),
  ADD KEY `FK_Enregistrer_numRes` (`numRes`);

--
-- Index pour la table `liaison`
--
ALTER TABLE `liaison`
  ADD PRIMARY KEY (`code`),
  ADD KEY `FK_Liaison_idSecteur` (`idSecteur`),
  ADD KEY `FK_Liaison_idPort_Depart` (`idPort_Depart`),
  ADD KEY `FK_Liaison_idPort_Arrivee` (`idPort_Arrivee`);

--
-- Index pour la table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`dateDeb`);

--
-- Index pour la table `port`
--
ALTER TABLE `port`
  ADD PRIMARY KEY (`idPort`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`numRes`),
  ADD KEY `FK_Reservation_numTra` (`numTra`);

--
-- Index pour la table `secteur`
--
ALTER TABLE `secteur`
  ADD PRIMARY KEY (`idSecteur`);

--
-- Index pour la table `tarifer`
--
ALTER TABLE `tarifer`
  ADD PRIMARY KEY (`dateDeb`,`idType`,`code`),
  ADD KEY `FK_Tarifer_idType` (`idType`),
  ADD KEY `FK_Tarifer_code` (`code`);

--
-- Index pour la table `traversee`
--
ALTER TABLE `traversee`
  ADD PRIMARY KEY (`numTra`),
  ADD KEY `FK_Traversee_idBat` (`idBat`),
  ADD KEY `FK_Traversee_code` (`code`);

--
-- Index pour la table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`idType`),
  ADD KEY `FK_Type_lettre` (`lettre`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bateau`
--
ALTER TABLE `bateau`
  MODIFY `idBat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `enregistrer`
--
ALTER TABLE `enregistrer`
  MODIFY `idType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `liaison`
--
ALTER TABLE `liaison`
  MODIFY `code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT pour la table `port`
--
ALTER TABLE `port`
  MODIFY `idPort` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `secteur`
--
ALTER TABLE `secteur`
  MODIFY `idSecteur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `traversee`
--
ALTER TABLE `traversee`
  MODIFY `numTra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `type`
--
ALTER TABLE `type`
  MODIFY `idType` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `enregistrer`
--
ALTER TABLE `enregistrer`
  ADD CONSTRAINT `FK_Enregistrer_idType` FOREIGN KEY (`idType`) REFERENCES `type` (`idType`),
  ADD CONSTRAINT `FK_Enregistrer_numRes` FOREIGN KEY (`numRes`) REFERENCES `reservation` (`numRes`);

--
-- Contraintes pour la table `liaison`
--
ALTER TABLE `liaison`
  ADD CONSTRAINT `FK_Liaison_idPort_Arrivee` FOREIGN KEY (`idPort_Arrivee`) REFERENCES `port` (`idPort`),
  ADD CONSTRAINT `FK_Liaison_idPort_Depart` FOREIGN KEY (`idPort_Depart`) REFERENCES `port` (`idPort`),
  ADD CONSTRAINT `FK_Liaison_idSecteur` FOREIGN KEY (`idSecteur`) REFERENCES `secteur` (`idSecteur`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_Reservation_numTra` FOREIGN KEY (`numTra`) REFERENCES `traversee` (`numTra`);

--
-- Contraintes pour la table `tarifer`
--
ALTER TABLE `tarifer`
  ADD CONSTRAINT `FK_Tarifer_code` FOREIGN KEY (`code`) REFERENCES `liaison` (`code`),
  ADD CONSTRAINT `FK_Tarifer_dateDeb` FOREIGN KEY (`dateDeb`) REFERENCES `periode` (`dateDeb`),
  ADD CONSTRAINT `FK_Tarifer_idType` FOREIGN KEY (`idType`) REFERENCES `type` (`idType`);

--
-- Contraintes pour la table `traversee`
--
ALTER TABLE `traversee`
  ADD CONSTRAINT `FK_Traversee_code` FOREIGN KEY (`code`) REFERENCES `liaison` (`code`),
  ADD CONSTRAINT `FK_Traversee_idBat` FOREIGN KEY (`idBat`) REFERENCES `bateau` (`idBat`);

--
-- Contraintes pour la table `type`
--
ALTER TABLE `type`
  ADD CONSTRAINT `FK_Type_lettre` FOREIGN KEY (`lettre`) REFERENCES `categorie` (`lettre`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
