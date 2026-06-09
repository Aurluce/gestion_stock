-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 29 mai 2026 à 21:42
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_stock`
--

-- --------------------------------------------------------

--
-- Structure de la table `banque`
--

CREATE TABLE `banque` (
  `id_banque` int(11) NOT NULL,
  `nom_banque` varchar(200) NOT NULL,
  `sigle` varchar(20) DEFAULT NULL,
  `responsable` varchar(200) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `banque`
--

INSERT INTO `banque` (`id_banque`, `nom_banque`, `sigle`, `responsable`, `adresse`, `tel`, `email`, `date_creation`) VALUES
(1, 'Société Générale Cameroun', 'SGC', 'Mouelle Arsène', 'Avenue Kennedy, Yaoundé', '222 22 10 00', 'contact@sgc.cm', '2026-05-29 21:33:52'),
(2, 'Afriland First Bank', 'AFB', 'Tchinda Marie', 'Place de l Indépendance, Douala', '233 42 01 23', 'info@afrilandfirstbank.com', '2026-05-29 21:33:52'),
(3, 'BICEC', 'BICEC', 'Ngono Pierre', 'Rue Nachtigal, Yaoundé', '222 23 50 00', 'bicec@bicec.cm', '2026-05-29 21:33:52'),
(4, 'Ecobank Cameroun', 'ECO', 'Abomo Sabine', 'Bd de la Liberté, Douala', '233 50 40 00', 'ecobank@ecobank.com', '2026-05-29 21:33:52'),
(5, 'UBA Cameroun', 'UBA', 'Fouda Charles', 'Rue Joffre, Yaoundé', '222 22 80 00', 'uba@ubagroup.com', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `bon_commande_fourn`
--

CREATE TABLE `bon_commande_fourn` (
  `id_bcf` int(11) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_commande` date NOT NULL DEFAULT curdate(),
  `statut` enum('brouillon','envoye','receptionne','annule') DEFAULT 'brouillon',
  `reference` varchar(50) NOT NULL,
  `montant_total` decimal(15,2) DEFAULT 0.00,
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bon_commande_fourn`
--

INSERT INTO `bon_commande_fourn` (`id_bcf`, `id_fournisseur`, `id_utilisateur`, `date_commande`, `statut`, `reference`, `montant_total`, `observations`, `date_creation`, `date_modif`) VALUES
(1, 1, 4, '2025-01-05', 'receptionne', 'BCF-2025-001', 370000.00, 'Commande mensuelle janvier', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 2, 4, '2025-01-10', 'receptionne', 'BCF-2025-002', 218000.00, 'Réassort produits ménagers', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 4, 4, '2025-01-15', 'receptionne', 'BCF-2025-003', 255000.00, 'Commande boissons janvier', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 1, 4, '2025-02-03', 'receptionne', 'BCF-2025-004', 462000.00, 'Commande alimentaire février', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 3, 2, '2025-02-10', 'receptionne', 'BCF-2025-005', 185000.00, 'Produits importés', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 7, 2, '2025-02-18', 'receptionne', 'BCF-2025-006', 312000.00, 'Accessoires électroniques', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 6, 4, '2025-03-02', 'receptionne', 'BCF-2025-007', 196000.00, 'Produits frais mars', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 1, 4, '2025-03-10', 'receptionne', 'BCF-2025-008', 504000.00, 'Commande principale mars', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 4, 2, '2025-03-20', 'envoye', 'BCF-2025-009', 180000.00, 'Réassort boissons', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 2, 4, '2025-04-02', 'receptionne', 'BCF-2025-010', 287000.00, 'Commande hygiène avril', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 5, 2, '2025-04-08', 'receptionne', 'BCF-2025-011', 145000.00, 'Textile et papeterie', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 1, 4, '2025-04-15', 'receptionne', 'BCF-2025-012', 590000.00, 'Grande commande alimentaire', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(13, 10, 4, '2025-05-02', 'receptionne', 'BCF-2025-013', 162000.00, 'Produits ménagers mai', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(14, 3, 2, '2025-05-10', 'envoye', 'BCF-2025-014', 240000.00, 'Produits importés mai', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(15, 1, 4, '2025-05-20', 'brouillon', 'BCF-2025-015', 385000.00, 'Commande prévisionnelle juin', '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `bon_entree`
--

CREATE TABLE `bon_entree` (
  `id_be` int(11) NOT NULL,
  `id_br` int(11) DEFAULT NULL,
  `id_don` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_entree` date NOT NULL DEFAULT curdate(),
  `reference` varchar(50) NOT NULL,
  `type_source` enum('achat','don','retour','autre') NOT NULL,
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bon_entree`
--

INSERT INTO `bon_entree` (`id_be`, `id_br`, `id_don`, `id_utilisateur`, `date_entree`, `reference`, `type_source`, `observations`, `date_creation`) VALUES
(1, 1, NULL, 4, '2025-01-08', 'BE-2025-001', 'achat', 'Entrée réception BCF-001', '2026-05-29 21:33:52'),
(2, 2, NULL, 4, '2025-01-14', 'BE-2025-002', 'achat', 'Entrée réception BCF-002', '2026-05-29 21:33:52'),
(3, 3, NULL, 4, '2025-01-18', 'BE-2025-003', 'achat', 'Entrée boissons BCF-003', '2026-05-29 21:33:52'),
(4, 4, NULL, 4, '2025-02-07', 'BE-2025-004', 'achat', 'Entrée alimentaire BCF-004', '2026-05-29 21:33:52'),
(5, 5, NULL, 4, '2025-02-14', 'BE-2025-005', 'achat', 'Entrée partielle BCF-005', '2026-05-29 21:33:52'),
(6, 6, NULL, 4, '2025-02-22', 'BE-2025-006', 'achat', 'Entrée électronique BCF-006', '2026-05-29 21:33:52'),
(7, 7, NULL, 4, '2025-03-05', 'BE-2025-007', 'achat', 'Entrée frais BCF-007', '2026-05-29 21:33:52'),
(8, 8, NULL, 4, '2025-03-14', 'BE-2025-008', 'achat', 'Grande entrée BCF-008', '2026-05-29 21:33:52'),
(9, NULL, 1, 4, '2025-02-20', 'BE-2025-009', 'don', 'Don de l ONG CRS', '2026-05-29 21:33:52'),
(10, 9, NULL, 4, '2025-04-05', 'BE-2025-010', 'achat', 'Entrée hygiène BCF-009', '2026-05-29 21:33:52'),
(11, 10, NULL, 4, '2025-04-11', 'BE-2025-011', 'achat', 'Entrée papeterie BCF-010', '2026-05-29 21:33:52'),
(12, 11, NULL, 4, '2025-04-19', 'BE-2025-012', 'achat', 'Entrée principale BCF-011', '2026-05-29 21:33:52'),
(13, 12, NULL, 4, '2025-05-06', 'BE-2025-013', 'achat', 'Entrée ménager BCF-012', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `bon_livraison`
--

CREATE TABLE `bon_livraison` (
  `id_bl` int(11) NOT NULL,
  `id_cc` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_livraison` date NOT NULL DEFAULT curdate(),
  `reference` varchar(50) NOT NULL,
  `statut` enum('en_cours','livre','partiel','annule') DEFAULT 'en_cours',
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bon_livraison`
--

INSERT INTO `bon_livraison` (`id_bl`, `id_cc`, `id_utilisateur`, `date_livraison`, `reference`, `statut`, `observations`, `date_creation`) VALUES
(1, 1, 4, '2025-01-11', 'BL-2025-001', 'livre', NULL, '2026-05-29 21:33:52'),
(2, 2, 4, '2025-01-13', 'BL-2025-002', 'livre', NULL, '2026-05-29 21:33:52'),
(3, 3, 4, '2025-01-17', 'BL-2025-003', 'livre', NULL, '2026-05-29 21:33:52'),
(4, 6, 4, '2025-02-04', 'BL-2025-004', 'livre', NULL, '2026-05-29 21:33:52'),
(5, 7, 4, '2025-02-06', 'BL-2025-005', 'livre', NULL, '2026-05-29 21:33:52'),
(6, 8, 4, '2025-02-13', 'BL-2025-006', 'livre', NULL, '2026-05-29 21:33:52'),
(7, 10, 4, '2025-02-21', 'BL-2025-007', 'livre', NULL, '2026-05-29 21:33:52'),
(8, 11, 4, '2025-03-05', 'BL-2025-008', 'en_cours', NULL, '2026-05-29 21:33:52'),
(9, 12, 4, '2025-03-08', 'BL-2025-009', 'livre', NULL, '2026-05-29 21:33:52'),
(10, 13, 4, '2025-03-13', 'BL-2025-010', 'livre', NULL, '2026-05-29 21:33:52'),
(11, 15, 4, '2025-03-23', 'BL-2025-011', 'livre', NULL, '2026-05-29 21:33:52'),
(12, 16, 4, '2025-04-03', 'BL-2025-012', 'livre', NULL, '2026-05-29 21:33:52'),
(13, 17, 4, '2025-04-06', 'BL-2025-013', 'livre', NULL, '2026-05-29 21:33:52'),
(14, 18, 4, '2025-04-11', 'BL-2025-014', 'en_cours', NULL, '2026-05-29 21:33:52'),
(15, 20, 4, '2025-04-19', 'BL-2025-015', 'livre', NULL, '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `bon_reception`
--

CREATE TABLE `bon_reception` (
  `id_br` int(11) NOT NULL,
  `id_bcf` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_reception` date NOT NULL DEFAULT curdate(),
  `reference` varchar(50) NOT NULL,
  `statut` enum('partiel','complet','en_attente') DEFAULT 'en_attente',
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bon_reception`
--

INSERT INTO `bon_reception` (`id_br`, `id_bcf`, `id_utilisateur`, `date_reception`, `reference`, `statut`, `observations`, `date_creation`) VALUES
(1, 1, 4, '2025-01-08', 'BR-2025-001', 'complet', 'Tout reçu conforme', '2026-05-29 21:33:52'),
(2, 2, 4, '2025-01-14', 'BR-2025-002', 'complet', 'RAS', '2026-05-29 21:33:52'),
(3, 3, 4, '2025-01-18', 'BR-2025-003', 'complet', 'Quelques bouteilles cassées', '2026-05-29 21:33:52'),
(4, 4, 4, '2025-02-07', 'BR-2025-004', 'complet', 'Conforme à la commande', '2026-05-29 21:33:52'),
(5, 5, 4, '2025-02-14', 'BR-2025-005', 'partiel', '3 articles manquants sur le maïs', '2026-05-29 21:33:52'),
(6, 6, 4, '2025-02-22', 'BR-2025-006', 'complet', 'Emballages en bon état', '2026-05-29 21:33:52'),
(7, 7, 4, '2025-03-05', 'BR-2025-007', 'complet', 'Produits frais conformes', '2026-05-29 21:33:52'),
(8, 8, 4, '2025-03-14', 'BR-2025-008', 'complet', 'Grande réception OK', '2026-05-29 21:33:52'),
(9, 10, 4, '2025-04-05', 'BR-2025-009', 'complet', 'Hygiène reçue', '2026-05-29 21:33:52'),
(10, 11, 4, '2025-04-11', 'BR-2025-010', 'complet', 'Papeterie et textile OK', '2026-05-29 21:33:52'),
(11, 12, 4, '2025-04-19', 'BR-2025-011', 'complet', 'Réception principale avril', '2026-05-29 21:33:52'),
(12, 13, 4, '2025-05-06', 'BR-2025-012', 'complet', 'Ménager reçu complet', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `categorie_client`
--

CREATE TABLE `categorie_client` (
  `id_categorie_client` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `taux_remise` decimal(5,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie_client`
--

INSERT INTO `categorie_client` (`id_categorie_client`, `nom_categorie`, `taux_remise`, `description`, `date_creation`) VALUES
(1, 'Grand compte', 15.00, 'Entreprises et administrations avec gros volumes', '2026-05-29 21:33:52'),
(2, 'Client régulier', 8.00, 'Clients fidèles avec achats fréquents', '2026-05-29 21:33:52'),
(3, 'Client occasionnel', 3.00, 'Clients avec achats ponctuels', '2026-05-29 21:33:52'),
(4, 'Revendeur', 12.00, 'Grossistes et détaillants revendeurs', '2026-05-29 21:33:52'),
(5, 'Particulier', 0.00, 'Client individuel sans remise', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `id_categorie_client` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prenom` varchar(150) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'Cameroun',
  `tel` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `type_client` enum('particulier','entreprise','administration') DEFAULT 'particulier',
  `solde_credit` decimal(15,2) DEFAULT 0.00,
  `est_actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id_client`, `id_categorie_client`, `nom`, `prenom`, `adresse`, `ville`, `code_postal`, `pays`, `tel`, `email`, `type_client`, `solde_credit`, `est_actif`, `date_creation`, `date_modif`) VALUES
(1, 1, 'CAMAIR-CO', NULL, 'Aéroport Nsimalen', 'Yaoundé', NULL, 'Cameroun', '222 21 00 00', 'achats@camairco.cm', 'entreprise', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 1, 'Hôtel Mont Fébé', NULL, 'Colline de Fébé', 'Yaoundé', NULL, 'Cameroun', '222 21 42 00', 'approvisionnement@montfebe.cm', 'entreprise', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 1, 'MINSANTE', NULL, 'Quartier Administratif', 'Yaoundé', NULL, 'Cameroun', '222 22 20 55', 'daf@minsante.cm', 'administration', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 1, 'CHU de Yaoundé', NULL, 'Route de Melen', 'Yaoundé', NULL, 'Cameroun', '222 23 41 00', 'pharmacie@chuy.cm', 'administration', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 4, 'Supermarché DOVV', NULL, 'Carrefour Mvog-Mbi', 'Yaoundé', NULL, 'Cameroun', '222 31 45 67', 'dovv@dovv.cm', 'entreprise', 150000.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 4, 'Alimentation Centrale', NULL, 'Marché Mfoundi', 'Yaoundé', NULL, 'Cameroun', '699 12 34 56', 'alicentrale@gmail.com', 'entreprise', 75000.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 4, 'Shop & Go Sarl', NULL, 'Rue de la Joie, Akwa', 'Douala', NULL, 'Cameroun', '677 89 01 23', 'shopgo@shopgo.cm', 'entreprise', 50000.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 2, 'Fondation Paul Biya', NULL, 'Av des Cocotiers', 'Yaoundé', NULL, 'Cameroun', '222 20 10 00', 'fondation@paulbiya.cm', 'entreprise', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 2, 'École Publique Bastos', NULL, 'Quartier Bastos', 'Yaoundé', NULL, 'Cameroun', '222 21 55 66', 'epbastos@education.cm', 'administration', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 2, 'Ngono', 'Bernadette', 'Quartier Mimboman', 'Yaoundé', NULL, 'Cameroun', '697 23 45 67', 'bernadette.ngono@gmail.com', 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 2, 'Fouda', 'Marcel', 'Rue de Tanger, Douala', 'Douala', NULL, 'Cameroun', '677 34 56 78', 'fouda.marcel@hotmail.com', 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 2, 'Assembe', 'Pauline', 'Cité Verte', 'Yaoundé', NULL, 'Cameroun', '690 56 78 90', 'pauline.assembe@gmail.com', 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(13, 5, 'Manga', 'Thomas', 'Nlongkak', 'Yaoundé', NULL, 'Cameroun', '655 78 90 12', NULL, 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(14, 5, 'Samba', 'Irène', 'New-Bell', 'Douala', NULL, 'Cameroun', '676 90 12 34', NULL, 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(15, 5, 'Nkoulou', 'Eric', 'Essos', 'Yaoundé', NULL, 'Cameroun', '691 12 34 56', 'eric.nkoulou@yahoo.fr', 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(16, 3, 'Restaurant Le Wouri', NULL, 'Bd de la Liberté', 'Douala', NULL, 'Cameroun', '233 42 11 22', 'lewouri@lewouri.cm', 'entreprise', 25000.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(17, 3, 'Cantine Scolaire Mvogt', NULL, 'Mvogt-Ada', 'Yaoundé', NULL, 'Cameroun', '699 44 55 66', NULL, 'entreprise', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(18, 5, 'Beti', 'Sandrine', 'Quartier Mendong', 'Yaoundé', NULL, 'Cameroun', '695 67 89 01', NULL, 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(19, 5, 'Zang', 'Christophe', 'Deido', 'Douala', NULL, 'Cameroun', '670 23 45 67', NULL, 'particulier', 0.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(20, 4, 'Épicerie du Carrefour', NULL, 'Carrefour Elig-Effa', 'Yaoundé', NULL, 'Cameroun', '699 78 90 12', NULL, 'entreprise', 20000.00, 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `commande_client`
--

CREATE TABLE `commande_client` (
  `id_cc` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_commande` date NOT NULL DEFAULT curdate(),
  `statut` enum('en_cours','livree','facturee','reglee','annulee','en_attente') DEFAULT 'en_cours',
  `montant_total` decimal(15,2) DEFAULT 0.00,
  `reference` varchar(50) NOT NULL,
  `type_vente` enum('comptant','credit') DEFAULT 'credit',
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande_client`
--

INSERT INTO `commande_client` (`id_cc`, `id_client`, `id_utilisateur`, `date_commande`, `statut`, `montant_total`, `reference`, `type_vente`, `observations`, `date_creation`, `date_modif`) VALUES
(1, 1, 3, '2025-01-10', 'reglee', 480000.00, 'CC-2025-001', 'credit', 'Commande mensuelle CAMAIR-CO', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 4, 3, '2025-01-12', 'reglee', 320000.00, 'CC-2025-002', 'credit', 'CHU commande médicaments/hygiène', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 5, 3, '2025-01-16', 'reglee', 215000.00, 'CC-2025-003', 'credit', 'DOVV réassort', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 10, 3, '2025-01-20', 'reglee', 42500.00, 'CC-2025-004', 'comptant', 'Vente comptant Bernadette Ngono', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 13, 3, '2025-01-22', 'reglee', 18500.00, 'CC-2025-005', 'comptant', 'Vente au détail Thomas Manga', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 1, 3, '2025-02-03', 'reglee', 560000.00, 'CC-2025-006', 'credit', 'Commande CAMAIR-CO février', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 2, 3, '2025-02-05', 'reglee', 390000.00, 'CC-2025-007', 'credit', 'Hôtel Mont Fébé', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 5, 3, '2025-02-12', 'reglee', 275000.00, 'CC-2025-008', 'credit', 'DOVV réassort semaine 7', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 11, 3, '2025-02-14', 'reglee', 35000.00, 'CC-2025-009', 'comptant', 'Fouda Marcel vente comptant', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 6, 3, '2025-02-20', 'reglee', 185000.00, 'CC-2025-010', 'credit', 'Alimentation Centrale', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 1, 3, '2025-03-04', 'livree', 610000.00, 'CC-2025-011', 'credit', 'Commande mars CAMAIR-CO', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 3, 3, '2025-03-07', 'reglee', 450000.00, 'CC-2025-012', 'credit', 'MINSANTE approvisionnement', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(13, 8, 3, '2025-03-12', 'reglee', 128000.00, 'CC-2025-013', 'credit', 'Fondation Paul Biya', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(14, 14, 3, '2025-03-15', 'reglee', 22000.00, 'CC-2025-014', 'comptant', 'Samba Irène comptant', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(15, 5, 3, '2025-03-22', 'facturee', 320000.00, 'CC-2025-015', 'credit', 'DOVV mars', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(16, 7, 3, '2025-04-02', 'reglee', 240000.00, 'CC-2025-016', 'credit', 'Shop & Go Douala', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(17, 2, 3, '2025-04-05', 'reglee', 380000.00, 'CC-2025-017', 'credit', 'Hôtel Mont Fébé avril', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(18, 4, 3, '2025-04-10', 'livree', 520000.00, 'CC-2025-018', 'credit', 'CHU Yaoundé', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(19, 12, 3, '2025-04-14', 'reglee', 29500.00, 'CC-2025-019', 'comptant', 'Assembe Pauline', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(20, 20, 3, '2025-04-18', 'reglee', 175000.00, 'CC-2025-020', 'credit', 'Épicerie du Carrefour', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(21, 1, 3, '2025-05-02', 'en_cours', 495000.00, 'CC-2025-021', 'credit', 'Commande mai CAMAIR-CO', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(22, 5, 3, '2025-05-06', 'en_cours', 280000.00, 'CC-2025-022', 'credit', 'DOVV mai', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(23, 13, 3, '2025-05-08', 'reglee', 15000.00, 'CC-2025-023', 'comptant', 'Manga Thomas mai', '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `compte_bancaire`
--

CREATE TABLE `compte_bancaire` (
  `id_compte` int(11) NOT NULL,
  `id_banque` int(11) NOT NULL,
  `numero_compte` varchar(50) NOT NULL,
  `libelle_compte` varchar(200) NOT NULL,
  `solde_actuel` decimal(15,2) DEFAULT 0.00,
  `type_compte` enum('principal','secondaire','epargne','courant') DEFAULT 'principal',
  `date_ouverture` date DEFAULT curdate(),
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `compte_bancaire`
--

INSERT INTO `compte_bancaire` (`id_compte`, `id_banque`, `numero_compte`, `libelle_compte`, `solde_actuel`, `type_compte`, `date_ouverture`, `actif`, `date_creation`) VALUES
(1, 1, 'SGC-001-2024-YDE', 'Compte courant principal SGC', 15750000.00, 'principal', '2020-01-15', 1, '2026-05-29 21:33:52'),
(2, 1, 'SGC-002-2024-YDE', 'Compte épargne SGC', 4200000.00, 'epargne', '2021-03-10', 1, '2026-05-29 21:33:52'),
(3, 2, 'AFB-001-2024-DLA', 'Compte courant AFB Douala', 8300000.00, 'courant', '2019-06-01', 1, '2026-05-29 21:33:52'),
(4, 3, 'BIC-001-2024-YDE', 'Compte BICEC principal', 6125000.00, 'principal', '2018-11-20', 1, '2026-05-29 21:33:52'),
(5, 4, 'ECO-001-2024-DLA', 'Compte Ecobank opérations', 3870000.00, 'courant', '2022-02-14', 1, '2026-05-29 21:33:52'),
(6, 5, 'UBA-001-2024-YDE', 'Compte UBA secondaire', 1250000.00, 'secondaire', '2023-04-05', 1, '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `don`
--

CREATE TABLE `don` (
  `id_don` int(11) NOT NULL,
  `donateur` varchar(200) NOT NULL,
  `contact_donateur` varchar(100) DEFAULT NULL,
  `date_don` date NOT NULL DEFAULT curdate(),
  `description` text DEFAULT NULL,
  `valeur_estimee` decimal(15,2) DEFAULT 0.00,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `don`
--

INSERT INTO `don` (`id_don`, `donateur`, `contact_donateur`, `date_don`, `description`, `valeur_estimee`, `date_creation`) VALUES
(1, 'ONG CRS Cameroun', 'cr@crs.cm', '2025-02-19', 'Don de produits alimentaires et hygiéniques', 450000.00, '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `droit`
--

CREATE TABLE `droit` (
  `id_droit` int(11) NOT NULL,
  `nom_droit` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `droit`
--

INSERT INTO `droit` (`id_droit`, `nom_droit`, `module`, `description`, `date_creation`) VALUES
(1, 'creer_bcf', 'approvisionnement', 'Créer un bon de commande fournisseur', '2026-05-29 21:33:52'),
(2, 'editer_bcf', 'approvisionnement', 'Modifier un bon de commande fournisseur', '2026-05-29 21:33:52'),
(3, 'imprimer_bcf', 'approvisionnement', 'Imprimer un bon de commande fournisseur', '2026-05-29 21:33:52'),
(4, 'creer_reception', 'approvisionnement', 'Enregistrer une réception', '2026-05-29 21:33:52'),
(5, 'imprimer_reception', 'approvisionnement', 'Imprimer un bon de réception', '2026-05-29 21:33:52'),
(6, 'creer_bon_entree', 'approvisionnement', 'Créer un bon d entrée', '2026-05-29 21:33:52'),
(7, 'imprimer_bon_entree', 'approvisionnement', 'Imprimer un bon d entrée', '2026-05-29 21:33:52'),
(8, 'saisir_don', 'approvisionnement', 'Enregistrer une entrée par don', '2026-05-29 21:33:52'),
(9, 'creer_facture_fourn', 'approvisionnement', 'Saisir une facture fournisseur', '2026-05-29 21:33:52'),
(10, 'payer_fournisseur', 'approvisionnement', 'Enregistrer un paiement fournisseur', '2026-05-29 21:33:52'),
(11, 'imprimer_recu_fourn', 'approvisionnement', 'Imprimer un reçu fournisseur', '2026-05-29 21:33:52'),
(12, 'etat_achats_jour', 'approvisionnement', 'Imprimer état des achats par jour', '2026-05-29 21:33:52'),
(13, 'etat_achats_annuel', 'approvisionnement', 'Imprimer état des achats annuel', '2026-05-29 21:33:52'),
(14, 'creer_commande_client', 'ventes', 'Enregistrer une commande client', '2026-05-29 21:33:52'),
(15, 'editer_commande_client', 'ventes', 'Modifier un bon de commande client', '2026-05-29 21:33:52'),
(16, 'livrer_produits', 'ventes', 'Enregistrer une livraison', '2026-05-29 21:33:52'),
(17, 'editer_bon_livraison', 'ventes', 'Modifier un bon de livraison', '2026-05-29 21:33:52'),
(18, 'creer_facture_client', 'ventes', 'Créer une facture client', '2026-05-29 21:33:52'),
(19, 'editer_facture_client', 'ventes', 'Modifier une facture client', '2026-05-29 21:33:52'),
(20, 'regler_facture_client', 'ventes', 'Enregistrer un règlement client', '2026-05-29 21:33:52'),
(21, 'sortie_stock', 'ventes', 'Enregistrer une sortie de stock', '2026-05-29 21:33:52'),
(22, 'editer_bon_sortie', 'ventes', 'Modifier un bon de sortie', '2026-05-29 21:33:52'),
(23, 'etat_ventes_jour', 'ventes', 'Imprimer état des ventes par jour', '2026-05-29 21:33:52'),
(24, 'etat_ventes_annuel', 'ventes', 'Imprimer état des ventes annuel', '2026-05-29 21:33:52'),
(25, 'creer_famille', 'structure', 'Créer une famille de produits', '2026-05-29 21:33:52'),
(26, 'creer_produit', 'structure', 'Créer un produit', '2026-05-29 21:33:52'),
(27, 'creer_produit_fils', 'structure', 'Créer un produit fils', '2026-05-29 21:33:52'),
(28, 'fractionner_produit', 'structure', 'Fractionner un produit père', '2026-05-29 21:33:52'),
(29, 'creer_fournisseur', 'structure', 'Créer un fournisseur', '2026-05-29 21:33:52'),
(30, 'creer_client', 'structure', 'Créer un client', '2026-05-29 21:33:52'),
(31, 'creer_categorie_client', 'structure', 'Créer une catégorie de clients', '2026-05-29 21:33:52'),
(32, 'creer_banque', 'structure', 'Créer une banque', '2026-05-29 21:33:52'),
(33, 'lister_produits', 'structure', 'Consulter la liste des produits', '2026-05-29 21:33:52'),
(34, 'lister_fournisseurs', 'structure', 'Consulter la liste des fournisseurs', '2026-05-29 21:33:52'),
(35, 'lister_clients', 'structure', 'Consulter la liste des clients', '2026-05-29 21:33:52'),
(36, 'lister_banques', 'structure', 'Consulter la liste des banques', '2026-05-29 21:33:52'),
(37, 'etat_versements', 'structure', 'Consulter l état des versements bancaires', '2026-05-29 21:33:52'),
(38, 'versement_banque', 'banque', 'Effectuer un versement', '2026-05-29 21:33:52'),
(39, 'retrait_banque', 'banque', 'Effectuer un retrait', '2026-05-29 21:33:52'),
(40, 'voir_transactions', 'banque', 'Consulter les transactions', '2026-05-29 21:33:52'),
(41, 'creer_groupe', 'utilisateurs', 'Créer un groupe utilisateur', '2026-05-29 21:33:52'),
(42, 'affecter_droits', 'utilisateurs', 'Affecter des droits à un groupe', '2026-05-29 21:33:52'),
(43, 'creer_utilisateur', 'utilisateurs', 'Créer un utilisateur', '2026-05-29 21:33:52'),
(44, 'modifier_connexion', 'utilisateurs', 'Modifier les paramètres de connexion', '2026-05-29 21:33:52'),
(45, 'voir_journal_audit', 'utilisateurs', 'Consulter le journal d audit', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `facture_client`
--

CREATE TABLE `facture_client` (
  `id_facture` int(11) NOT NULL,
  `id_cc` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_facture` date NOT NULL DEFAULT curdate(),
  `montant_ht` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taux_tva` decimal(5,2) DEFAULT 19.25,
  `montant_ttc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reference` varchar(50) NOT NULL,
  `statut` enum('impayee','partielle','payee','annulee') DEFAULT 'impayee',
  `date_echeance` date DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture_client`
--

INSERT INTO `facture_client` (`id_facture`, `id_cc`, `id_utilisateur`, `date_facture`, `montant_ht`, `taux_tva`, `montant_ttc`, `reference`, `statut`, `date_echeance`, `date_creation`, `date_modif`) VALUES
(1, 1, 3, '2025-01-11', 402660.00, 19.25, 480171.00, 'FC-2025-001', 'payee', '2025-02-11', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(2, 2, 3, '2025-01-13', 268543.00, 19.25, 320249.00, 'FC-2025-002', 'payee', '2025-02-13', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(3, 3, 3, '2025-01-17', 180378.00, 19.25, 215101.00, 'FC-2025-003', 'payee', '2025-02-17', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(4, 4, 3, '2025-01-20', 42500.00, 19.25, 42500.00, 'FC-2025-004', 'payee', '2025-01-20', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(5, 5, 3, '2025-01-22', 18500.00, 19.25, 18500.00, 'FC-2025-005', 'payee', '2025-01-22', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(6, 6, 3, '2025-02-04', 469934.00, 19.25, 560381.00, 'FC-2025-006', 'payee', '2025-03-04', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(7, 7, 3, '2025-02-06', 327132.00, 19.25, 390035.00, 'FC-2025-007', 'payee', '2025-03-06', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(8, 8, 3, '2025-02-13', 230682.00, 19.25, 275138.00, 'FC-2025-008', 'payee', '2025-03-13', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(9, 9, 3, '2025-02-14', 35000.00, 19.25, 35000.00, 'FC-2025-009', 'payee', '2025-02-14', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(10, 10, 3, '2025-02-21', 155262.00, 19.25, 185050.00, 'FC-2025-010', 'payee', '2025-03-21', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(11, 12, 3, '2025-03-08', 377358.00, 19.25, 449894.00, 'FC-2025-011', 'payee', '2025-04-08', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(12, 13, 3, '2025-03-13', 107379.00, 19.25, 128050.00, 'FC-2025-012', 'payee', '2025-04-13', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(13, 14, 3, '2025-03-15', 22000.00, 19.25, 22000.00, 'FC-2025-013', 'payee', '2025-03-15', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(14, 15, 3, '2025-03-23', 268800.00, 19.25, 320534.00, 'FC-2025-014', 'partielle', '2025-04-23', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(15, 16, 3, '2025-04-03', 201430.00, 19.25, 240206.00, 'FC-2025-015', 'payee', '2025-05-03', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(16, 17, 3, '2025-04-06', 318888.00, 19.25, 380244.00, 'FC-2025-016', 'payee', '2025-05-06', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(17, 19, 3, '2025-04-14', 29500.00, 19.25, 29500.00, 'FC-2025-017', 'payee', '2025-04-14', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(18, 20, 3, '2025-04-19', 146846.00, 19.25, 175114.00, 'FC-2025-018', 'payee', '2025-05-19', '2026-05-29 21:33:53', '2026-05-29 21:33:53'),
(19, 23, 3, '2025-05-08', 15000.00, 19.25, 15000.00, 'FC-2025-019', 'payee', '2025-05-08', '2026-05-29 21:33:53', '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `facture_fournisseur`
--

CREATE TABLE `facture_fournisseur` (
  `id_facture_f` int(11) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `id_bcf` int(11) DEFAULT NULL,
  `date_facture` date NOT NULL,
  `numero_facture` varchar(100) NOT NULL,
  `montant_ht` decimal(15,2) NOT NULL DEFAULT 0.00,
  `taux_tva` decimal(5,2) DEFAULT 19.25,
  `montant_ttc` decimal(15,2) NOT NULL DEFAULT 0.00,
  `statut` enum('impayee','partielle','payee','annulee') DEFAULT 'impayee',
  `reference` varchar(100) DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture_fournisseur`
--

INSERT INTO `facture_fournisseur` (`id_facture_f`, `id_fournisseur`, `id_bcf`, `date_facture`, `numero_facture`, `montant_ht`, `taux_tva`, `montant_ttc`, `statut`, `reference`, `date_echeance`, `date_creation`, `date_modif`) VALUES
(1, 1, 1, '2025-01-09', 'FF-SCPC-0125-001', 310756.00, 19.25, 370527.00, 'payee', NULL, '2025-02-09', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 2, 2, '2025-01-15', 'FF-SOAC-0125-001', 182928.00, 19.25, 218132.00, 'payee', NULL, '2025-02-15', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 4, 3, '2025-01-19', 'FF-BRAS-0125-001', 214002.00, 19.25, 255178.00, 'payee', NULL, '2025-02-19', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 1, 4, '2025-02-08', 'FF-SCPC-0225-001', 387424.00, 19.25, 461966.00, 'payee', NULL, '2025-03-08', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 3, 5, '2025-02-15', 'FF-IMPO-0225-001', 155319.00, 19.25, 185183.00, 'partielle', NULL, '2025-03-15', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 7, 6, '2025-02-23', 'FF-TECH-0225-001', 261959.00, 19.25, 312476.00, 'payee', NULL, '2025-03-23', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 6, 7, '2025-03-06', 'FF-FOOD-0325-001', 164473.00, 19.25, 196133.00, 'payee', NULL, '2025-04-06', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 1, 8, '2025-03-15', 'FF-SCPC-0325-001', 423007.00, 19.25, 504420.00, 'payee', NULL, '2025-04-15', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 2, 10, '2025-04-06', 'FF-SOAC-0425-001', 240755.00, 19.25, 287101.00, 'payee', NULL, '2025-05-06', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 5, 11, '2025-04-12', 'FF-CICA-0425-001', 121643.00, 19.25, 145059.00, 'payee', NULL, '2025-05-12', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 1, 12, '2025-04-20', 'FF-SCPC-0425-001', 494979.00, 19.25, 590282.00, 'impayee', NULL, '2025-05-20', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 10, 13, '2025-05-07', 'FF-CLEA-0525-001', 135912.00, 19.25, 162051.00, 'impayee', NULL, '2025-06-07', '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `famille`
--

CREATE TABLE `famille` (
  `id_famille` int(11) NOT NULL,
  `nom_famille` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `famille`
--

INSERT INTO `famille` (`id_famille`, `nom_famille`, `description`, `date_creation`, `date_modif`) VALUES
(1, 'Alimentation générale', 'Produits alimentaires non périssables', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 'Boissons', 'Eau, jus, sodas, bières, vins', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 'Produits laitiers', 'Lait, yaourt, fromage, beurre', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 'Boucherie / Charcuterie', 'Viandes, saucisses, charcuterie', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 'Fruits & Légumes', 'Produits frais et végétaux', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 'Hygiène & Beauté', 'Savon, shampoing, cosmétiques', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 'Produits ménagers', 'Détergents, balais, produits entretien', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 'Papeterie & Bureautique', 'Stylos, cahiers, cartouches imprimantes', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 'Électronique', 'Accessoires téléphonie et informatique', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 'Matériaux & Quincaillerie', 'Visserie, ciment, peintures', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 'Pharmacie / Para', 'Médicaments sans ordonnance, vitamines', '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 'Textile & Habillement', 'Vêtements, chaussures, accessoires', '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

CREATE TABLE `fournisseur` (
  `id_fournisseur` int(11) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'Cameroun',
  `tel` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `nif` varchar(50) DEFAULT NULL,
  `site_web` varchar(200) DEFAULT NULL,
  `statut` enum('actif','inactif','suspendu') DEFAULT 'actif',
  `est_actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fournisseur`
--

INSERT INTO `fournisseur` (`id_fournisseur`, `nom`, `adresse`, `ville`, `code_postal`, `pays`, `tel`, `email`, `nif`, `site_web`, `statut`, `est_actif`, `date_creation`, `date_modif`) VALUES
(1, 'SCPC Cameroun SA', 'Zone Industrielle Bassa', 'Douala', NULL, 'Cameroun', '233 40 12 34', 'approvisionnement@scpc.cm', 'M0123456789A', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 'SOACAM', 'Rue du Commerce', 'Yaoundé', NULL, 'Cameroun', '222 22 45 67', 'commandes@soacam.cm', 'M0987654321B', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 'Importex Sarl', 'Avenue de Gaulle', 'Douala', NULL, 'Cameroun', '233 41 00 11', 'contact@importex-cm.com', 'P0112233445C', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 'Brasseries du Cameroun', 'Bd de la Réunification', 'Douala', NULL, 'Cameroun', '233 42 00 00', 'commercial@brasseries.cm', 'M0001112223D', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 'CICAM', 'Zone Industrielle', 'Garoua', NULL, 'Cameroun', '222 27 10 00', 'ventes@cicam.cm', 'M0445566778E', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 'FoodDistrib Sarl', 'Marché Central', 'Bafoussam', NULL, 'Cameroun', '233 44 55 66', 'fooddist@fooddist.cm', 'P0778899001F', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 'TechImport SA', 'Quartier Akwa', 'Douala', NULL, 'Cameroun', '233 43 22 33', 'techimport@techimport.cm', 'M0334455667G', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 'AgriCM Coopérative', 'Marché de Mokolo', 'Maroua', NULL, 'Cameroun', '222 29 33 44', 'agricm@agricm.cm', 'P0556677889H', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 'PharmaPlus SA', 'Rue Nachtigal', 'Yaoundé', NULL, 'Cameroun', '222 22 77 88', 'pharmaplus@pharma.cm', 'M0667788990I', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 'CleanPro Sarl', 'Zone Bonabéri', 'Douala', NULL, 'Cameroun', '233 48 90 12', 'cleanpro@cleanpro.cm', 'P0889900112J', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 'OfficePro Sarl', 'Centre Commercial Warda', 'Yaoundé', NULL, 'Cameroun', '222 23 11 22', 'officepro@officepro.cm', 'M0990011234K', NULL, 'actif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 'FerroMet SA', 'Bp 4512', 'Douala', NULL, 'Cameroun', '233 46 78 90', 'ferromet@ferromet.cm', 'M0112344556L', NULL, 'inactif', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

CREATE TABLE `groupe` (
  `id_groupe` int(11) NOT NULL,
  `nom_groupe` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupe`
--

INSERT INTO `groupe` (`id_groupe`, `nom_groupe`, `description`, `date_creation`) VALUES
(1, 'Administrateur', 'Accès complet à toutes les fonctionnalités', '2026-05-29 21:33:52'),
(2, 'Gestionnaire', 'Gestion stocks, ventes et approvisionnements', '2026-05-29 21:33:52'),
(3, 'Caissier', 'Enregistrement des ventes et règlements', '2026-05-29 21:33:52'),
(4, 'Magasinier', 'Réceptions et gestion des entrées de stock', '2026-05-29 21:33:52'),
(5, 'Comptable', 'Consultation états financiers et bancaires', '2026-05-29 21:33:52'),
(6, 'Directeur', 'Consultation globale et validation', '2026-05-29 21:33:52'),
(7, 'Commercial', 'Gestion des commandes clients uniquement', '2026-05-29 21:33:52'),
(8, 'Auditeur', 'Lecture seule — journal et états', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `groupe_droit`
--

CREATE TABLE `groupe_droit` (
  `id_groupe` int(11) NOT NULL,
  `id_droit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupe_droit`
--

INSERT INTO `groupe_droit` (`id_groupe`, `id_droit`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(2, 39),
(2, 40),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 33),
(3, 35),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 33),
(4, 34),
(5, 12),
(5, 13),
(5, 23),
(5, 24),
(5, 37),
(5, 40),
(5, 45),
(6, 3),
(6, 5),
(6, 7),
(6, 11),
(6, 12),
(6, 13),
(6, 23),
(6, 24),
(6, 33),
(6, 34),
(6, 35),
(6, 36),
(6, 37),
(6, 40),
(6, 45),
(7, 14),
(7, 15),
(7, 23),
(7, 24),
(7, 33),
(7, 35),
(8, 13),
(8, 24),
(8, 33),
(8, 34),
(8, 35),
(8, 36),
(8, 40),
(8, 45);

-- --------------------------------------------------------

--
-- Structure de la table `journal_audit`
--

CREATE TABLE `journal_audit` (
  `id_audit` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `date_heure` datetime NOT NULL DEFAULT current_timestamp(),
  `action` enum('INSERT','UPDATE','DELETE','LOGIN','LOGOUT','PRINT','EXPORT') NOT NULL,
  `table_cible` varchar(100) DEFAULT NULL,
  `id_enregistrement` varchar(50) DEFAULT NULL,
  `ancienne_valeur` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ancienne_valeur`)),
  `nouvelle_valeur` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nouvelle_valeur`)),
  `ip_adresse` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `journal_audit`
--

INSERT INTO `journal_audit` (`id_audit`, `id_utilisateur`, `date_heure`, `action`, `table_cible`, `id_enregistrement`, `ancienne_valeur`, `nouvelle_valeur`, `ip_adresse`, `user_agent`) VALUES
(1, 1, '2025-01-01 08:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.10', NULL),
(2, 1, '2025-01-01 08:05:00', 'INSERT', 'groupe', '1', NULL, NULL, '192.168.1.10', NULL),
(3, 1, '2025-01-01 08:10:00', 'INSERT', 'droit', '1', NULL, NULL, '192.168.1.10', NULL),
(4, 1, '2025-01-01 08:30:00', 'INSERT', 'utilisateur', '2', NULL, NULL, '192.168.1.10', NULL),
(5, 4, '2025-01-08 09:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.14', NULL),
(6, 4, '2025-01-08 09:01:00', 'INSERT', 'bon_entree', '1', NULL, NULL, '192.168.1.14', NULL),
(7, 4, '2025-01-08 09:30:00', 'PRINT', 'bon_entree', '1', NULL, NULL, '192.168.1.14', NULL),
(8, 3, '2025-01-10 10:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.12', NULL),
(9, 3, '2025-01-10 10:05:00', 'INSERT', 'commande_client', '1', NULL, NULL, '192.168.1.12', NULL),
(10, 3, '2025-01-11 10:15:00', 'INSERT', 'bon_livraison', '1', NULL, NULL, '192.168.1.12', NULL),
(11, 3, '2025-01-11 10:20:00', 'PRINT', 'bon_livraison', '1', NULL, NULL, '192.168.1.12', NULL),
(12, 3, '2025-01-11 11:00:00', 'INSERT', 'facture_client', '1', NULL, NULL, '192.168.1.12', NULL),
(13, 3, '2025-01-11 11:05:00', 'PRINT', 'facture_client', '1', NULL, NULL, '192.168.1.12', NULL),
(14, 5, '2025-01-20 14:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.15', NULL),
(15, 5, '2025-01-20 14:05:00', 'INSERT', 'paiement_fourn', '1', NULL, NULL, '192.168.1.15', NULL),
(16, 5, '2025-01-25 09:00:00', 'INSERT', 'versement', '1', NULL, NULL, '192.168.1.15', NULL),
(17, 2, '2025-01-25 09:30:00', 'UPDATE', 'produit', '15', NULL, NULL, '192.168.1.11', NULL),
(18, 4, '2025-01-25 11:00:00', 'INSERT', 'sortie_stock', '1', NULL, NULL, '192.168.1.14', NULL),
(19, 4, '2025-01-25 11:01:00', 'PRINT', 'sortie_stock', '1', NULL, NULL, '192.168.1.14', NULL),
(20, 1, '2025-02-01 08:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.10', NULL),
(21, 1, '2025-02-01 08:10:00', 'EXPORT', 'mouvement_stock', NULL, NULL, NULL, '192.168.1.10', NULL),
(22, 6, '2025-02-01 09:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.16', NULL),
(23, 6, '2025-02-01 09:05:00', 'EXPORT', 'facture_client', NULL, NULL, NULL, '192.168.1.16', NULL),
(24, 3, '2025-03-04 08:00:00', 'LOGIN', NULL, NULL, NULL, NULL, '192.168.1.12', NULL),
(25, 3, '2025-03-04 08:30:00', 'INSERT', 'commande_client', '11', NULL, NULL, '192.168.1.12', NULL),
(26, 3, '2025-05-08 10:00:00', 'INSERT', 'reglement_client', '19', NULL, NULL, '192.168.1.12', NULL),
(27, 3, '2025-05-08 10:01:00', 'PRINT', 'reglement_client', '19', NULL, NULL, '192.168.1.12', NULL),
(28, 1, '2025-05-10 08:00:00', 'LOGOUT', NULL, NULL, NULL, NULL, '192.168.1.10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_bon_entree`
--

CREATE TABLE `ligne_bon_entree` (
  `id_lbe` int(11) NOT NULL,
  `id_be` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` decimal(15,3) NOT NULL,
  `prix_unitaire` decimal(15,2) DEFAULT 0.00,
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_bon_entree`
--

INSERT INTO `ligne_bon_entree` (`id_lbe`, `id_be`, `id_produit`, `quantite`, `prix_unitaire`, `observations`) VALUES
(1, 1, 1, 10.000, 18500.00, NULL),
(2, 1, 2, 20.000, 5200.00, NULL),
(3, 1, 3, 5.000, 14000.00, NULL),
(4, 2, 17, 30.000, 2500.00, NULL),
(5, 2, 18, 20.000, 450.00, NULL),
(6, 2, 19, 20.000, 800.00, NULL),
(7, 3, 10, 98.000, 350.00, NULL),
(8, 3, 11, 50.000, 750.00, NULL),
(9, 3, 12, 97.000, 850.00, NULL),
(10, 4, 1, 15.000, 18500.00, NULL),
(11, 4, 4, 10.000, 22000.00, NULL),
(12, 4, 6, 50.000, 750.00, NULL),
(13, 4, 7, 80.000, 450.00, NULL),
(14, 5, 8, 97.000, 800.00, NULL),
(15, 5, 6, 50.000, 750.00, NULL),
(16, 5, 14, 20.000, 2500.00, NULL),
(17, 6, 24, 100.000, 800.00, NULL),
(18, 6, 25, 60.000, 2500.00, NULL),
(19, 6, 26, 50.000, 1800.00, NULL),
(20, 7, 27, 80.000, 350.00, NULL),
(21, 7, 28, 100.000, 300.00, NULL),
(22, 7, 29, 20.000, 1200.00, NULL),
(23, 8, 1, 20.000, 18500.00, NULL),
(24, 8, 2, 30.000, 5200.00, NULL),
(25, 8, 4, 10.000, 22000.00, NULL),
(26, 8, 5, 200.000, 350.00, NULL),
(27, 9, 1, 10.000, 0.00, NULL),
(28, 9, 5, 20.000, 0.00, NULL),
(29, 9, 17, 15.000, 0.00, NULL),
(30, 10, 17, 50.000, 2500.00, NULL),
(31, 10, 18, 80.000, 450.00, NULL),
(32, 10, 21, 30.000, 800.00, NULL),
(33, 11, 20, 40.000, 800.00, NULL),
(34, 11, 21, 200.000, 150.00, NULL),
(35, 11, 22, 20.000, 3200.00, NULL),
(36, 12, 1, 25.000, 18500.00, NULL),
(37, 12, 2, 40.000, 5200.00, NULL),
(38, 12, 3, 10.000, 14000.00, NULL),
(39, 12, 4, 15.000, 22000.00, NULL),
(40, 13, 17, 40.000, 2500.00, NULL),
(41, 13, 18, 60.000, 450.00, NULL),
(42, 13, 23, 30.000, 800.00, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande_client`
--

CREATE TABLE `ligne_commande_client` (
  `id_lcc` int(11) NOT NULL,
  `id_cc` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` decimal(15,3) NOT NULL,
  `prix_unitaire` decimal(15,2) NOT NULL,
  `taux_remise` decimal(5,2) DEFAULT 0.00,
  `montant_ligne` decimal(15,2) GENERATED ALWAYS AS (`quantite` * `prix_unitaire` * (1 - `taux_remise` / 100)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_commande_client`
--

INSERT INTO `ligne_commande_client` (`id_lcc`, `id_cc`, `id_produit`, `quantite`, `prix_unitaire`, `taux_remise`) VALUES
(1, 1, 1, 5.000, 22000.00, 15.00),
(2, 1, 2, 10.000, 6500.00, 15.00),
(3, 1, 8, 20.000, 1100.00, 15.00),
(4, 1, 10, 50.000, 500.00, 15.00),
(5, 2, 17, 20.000, 3000.00, 15.00),
(6, 2, 18, 30.000, 700.00, 15.00),
(7, 2, 19, 20.000, 1050.00, 15.00),
(8, 3, 1, 5.000, 22000.00, 12.00),
(9, 3, 4, 3.000, 26500.00, 12.00),
(10, 3, 10, 20.000, 500.00, 12.00),
(11, 4, 5, 10.000, 500.00, 0.00),
(12, 4, 8, 5.000, 1100.00, 0.00),
(13, 4, 10, 20.000, 500.00, 0.00),
(14, 5, 1, 1.000, 22000.00, 0.00),
(15, 6, 1, 8.000, 22000.00, 15.00),
(16, 6, 2, 15.000, 6500.00, 15.00),
(17, 6, 11, 10.000, 1000.00, 15.00),
(18, 6, 12, 20.000, 1200.00, 15.00),
(19, 7, 2, 8.000, 6500.00, 8.00),
(20, 7, 10, 30.000, 500.00, 8.00),
(21, 7, 11, 20.000, 1000.00, 8.00),
(22, 7, 12, 30.000, 1200.00, 8.00),
(23, 8, 1, 6.000, 22000.00, 12.00),
(24, 8, 4, 4.000, 26500.00, 12.00),
(25, 8, 8, 15.000, 1100.00, 12.00),
(26, 9, 17, 5.000, 3000.00, 0.00),
(27, 9, 18, 5.000, 700.00, 0.00),
(28, 10, 1, 4.000, 22000.00, 8.00),
(29, 10, 2, 8.000, 6500.00, 8.00),
(30, 10, 6, 10.000, 1000.00, 8.00),
(31, 11, 1, 10.000, 22000.00, 15.00),
(32, 11, 2, 20.000, 6500.00, 15.00),
(33, 11, 8, 30.000, 1100.00, 15.00),
(34, 12, 17, 30.000, 3000.00, 15.00),
(35, 12, 18, 50.000, 700.00, 15.00),
(36, 12, 24, 30.000, 500.00, 15.00),
(37, 13, 1, 3.000, 22000.00, 8.00),
(38, 13, 2, 5.000, 6500.00, 8.00),
(39, 13, 17, 5.000, 3000.00, 8.00),
(40, 14, 5, 5.000, 500.00, 0.00),
(41, 14, 10, 5.000, 500.00, 0.00),
(42, 14, 18, 5.000, 700.00, 0.00),
(43, 15, 1, 8.000, 22000.00, 12.00),
(44, 15, 2, 12.000, 6500.00, 12.00),
(45, 16, 10, 50.000, 500.00, 12.00),
(46, 16, 11, 30.000, 1000.00, 12.00),
(47, 16, 17, 10.000, 3000.00, 12.00),
(48, 17, 2, 12.000, 6500.00, 8.00),
(49, 17, 10, 40.000, 500.00, 8.00),
(50, 17, 12, 20.000, 1200.00, 8.00),
(51, 18, 17, 40.000, 3000.00, 15.00),
(52, 18, 18, 60.000, 700.00, 15.00),
(53, 18, 24, 50.000, 500.00, 15.00),
(54, 19, 17, 5.000, 3000.00, 0.00),
(55, 19, 18, 3.000, 700.00, 0.00),
(56, 19, 5, 10.000, 500.00, 0.00),
(57, 20, 1, 4.000, 22000.00, 12.00),
(58, 20, 2, 6.000, 6500.00, 12.00),
(59, 20, 8, 10.000, 1100.00, 12.00),
(60, 21, 1, 8.000, 22000.00, 15.00),
(61, 21, 2, 15.000, 6500.00, 15.00),
(62, 21, 11, 20.000, 1000.00, 15.00),
(63, 22, 1, 6.000, 22000.00, 12.00),
(64, 22, 4, 4.000, 26500.00, 12.00),
(65, 23, 10, 5.000, 500.00, 0.00),
(66, 23, 18, 5.000, 700.00, 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande_fourn`
--

CREATE TABLE `ligne_commande_fourn` (
  `id_lcf` int(11) NOT NULL,
  `id_bcf` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `qte_commandee` decimal(15,3) NOT NULL,
  `prix_unitaire` decimal(15,2) NOT NULL,
  `montant_ligne` decimal(15,2) GENERATED ALWAYS AS (`qte_commandee` * `prix_unitaire`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_commande_fourn`
--

INSERT INTO `ligne_commande_fourn` (`id_lcf`, `id_bcf`, `id_produit`, `qte_commandee`, `prix_unitaire`) VALUES
(1, 1, 1, 10.000, 18500.00),
(2, 1, 2, 20.000, 5200.00),
(3, 1, 3, 5.000, 14000.00),
(4, 2, 17, 30.000, 2500.00),
(5, 2, 18, 20.000, 450.00),
(6, 2, 19, 20.000, 800.00),
(7, 3, 10, 100.000, 350.00),
(8, 3, 11, 50.000, 750.00),
(9, 3, 12, 100.000, 850.00),
(10, 4, 1, 15.000, 18500.00),
(11, 4, 4, 10.000, 22000.00),
(12, 4, 6, 50.000, 750.00),
(13, 4, 7, 80.000, 450.00),
(14, 5, 8, 100.000, 800.00),
(15, 5, 6, 50.000, 750.00),
(16, 5, 14, 20.000, 2500.00),
(17, 6, 24, 100.000, 800.00),
(18, 6, 25, 60.000, 2500.00),
(19, 6, 26, 50.000, 1800.00),
(20, 7, 27, 80.000, 350.00),
(21, 7, 28, 100.000, 300.00),
(22, 7, 29, 20.000, 1200.00),
(23, 8, 1, 20.000, 18500.00),
(24, 8, 2, 30.000, 5200.00),
(25, 8, 4, 10.000, 22000.00),
(26, 8, 5, 200.000, 350.00),
(27, 10, 17, 50.000, 2500.00),
(28, 10, 18, 80.000, 450.00),
(29, 10, 21, 30.000, 800.00),
(30, 11, 20, 40.000, 800.00),
(31, 11, 21, 200.000, 150.00),
(32, 11, 22, 20.000, 3200.00),
(33, 12, 1, 25.000, 18500.00),
(34, 12, 2, 40.000, 5200.00),
(35, 12, 3, 10.000, 14000.00),
(36, 12, 4, 15.000, 22000.00),
(37, 13, 17, 40.000, 2500.00),
(38, 13, 18, 60.000, 450.00),
(39, 13, 23, 30.000, 800.00),
(40, 15, 1, 15.000, 18500.00),
(41, 15, 2, 25.000, 5200.00),
(42, 15, 8, 50.000, 800.00);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_facture_fourn`
--

CREATE TABLE `ligne_facture_fourn` (
  `id_ligne_ff` int(11) NOT NULL,
  `id_facture_f` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` decimal(15,3) NOT NULL,
  `prix_unitaire` decimal(15,2) NOT NULL,
  `montant_ligne` decimal(15,2) GENERATED ALWAYS AS (`quantite` * `prix_unitaire`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_facture_fourn`
--

INSERT INTO `ligne_facture_fourn` (`id_ligne_ff`, `id_facture_f`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 10.000, 18500.00),
(2, 1, 2, 20.000, 5200.00),
(3, 1, 3, 5.000, 14000.00),
(4, 2, 17, 30.000, 2500.00),
(5, 2, 18, 20.000, 450.00),
(6, 2, 19, 20.000, 800.00),
(7, 3, 10, 98.000, 350.00),
(8, 3, 11, 50.000, 750.00),
(9, 3, 12, 97.000, 850.00),
(10, 4, 1, 15.000, 18500.00),
(11, 4, 4, 10.000, 22000.00),
(12, 4, 6, 50.000, 750.00),
(13, 4, 7, 80.000, 450.00),
(14, 5, 8, 97.000, 800.00),
(15, 5, 6, 50.000, 750.00),
(16, 5, 14, 20.000, 2500.00),
(17, 6, 24, 100.000, 800.00),
(18, 6, 25, 60.000, 2500.00),
(19, 6, 26, 50.000, 1800.00),
(20, 7, 27, 80.000, 350.00),
(21, 7, 28, 100.000, 300.00),
(22, 7, 29, 20.000, 1200.00),
(23, 8, 1, 20.000, 18500.00),
(24, 8, 2, 30.000, 5200.00),
(25, 8, 4, 10.000, 22000.00),
(26, 8, 5, 200.000, 350.00),
(27, 9, 17, 50.000, 2500.00),
(28, 9, 18, 80.000, 450.00),
(29, 9, 21, 30.000, 800.00),
(30, 10, 20, 40.000, 800.00),
(31, 10, 21, 200.000, 150.00),
(32, 10, 22, 20.000, 3200.00),
(33, 11, 1, 25.000, 18500.00),
(34, 11, 2, 40.000, 5200.00),
(35, 11, 3, 10.000, 14000.00),
(36, 11, 4, 15.000, 22000.00),
(37, 12, 17, 40.000, 2500.00),
(38, 12, 18, 60.000, 450.00),
(39, 12, 23, 30.000, 800.00);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_livraison`
--

CREATE TABLE `ligne_livraison` (
  `id_ll` int(11) NOT NULL,
  `id_bl` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `qte_livree` decimal(15,3) NOT NULL,
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_livraison`
--

INSERT INTO `ligne_livraison` (`id_ll`, `id_bl`, `id_produit`, `qte_livree`, `observations`) VALUES
(1, 1, 1, 5.000, NULL),
(2, 1, 2, 10.000, NULL),
(3, 1, 8, 20.000, NULL),
(4, 1, 10, 50.000, NULL),
(5, 2, 17, 20.000, NULL),
(6, 2, 18, 30.000, NULL),
(7, 2, 19, 20.000, NULL),
(8, 3, 1, 5.000, NULL),
(9, 3, 4, 3.000, NULL),
(10, 3, 10, 20.000, NULL),
(11, 4, 1, 8.000, NULL),
(12, 4, 2, 15.000, NULL),
(13, 4, 11, 10.000, NULL),
(14, 4, 12, 20.000, NULL),
(15, 5, 2, 8.000, NULL),
(16, 5, 10, 30.000, NULL),
(17, 5, 11, 20.000, NULL),
(18, 5, 12, 30.000, NULL),
(19, 6, 1, 6.000, NULL),
(20, 6, 4, 4.000, NULL),
(21, 6, 8, 15.000, NULL),
(22, 7, 1, 4.000, NULL),
(23, 7, 2, 8.000, NULL),
(24, 7, 6, 10.000, NULL),
(25, 8, 1, 10.000, NULL),
(26, 8, 2, 20.000, NULL),
(27, 8, 8, 30.000, NULL),
(28, 9, 17, 30.000, NULL),
(29, 9, 18, 50.000, NULL),
(30, 9, 24, 30.000, NULL),
(31, 10, 1, 3.000, NULL),
(32, 10, 2, 5.000, NULL),
(33, 10, 17, 5.000, NULL),
(34, 11, 1, 8.000, NULL),
(35, 11, 2, 12.000, NULL),
(36, 12, 10, 50.000, NULL),
(37, 12, 11, 30.000, NULL),
(38, 12, 17, 10.000, NULL),
(39, 13, 2, 12.000, NULL),
(40, 13, 10, 40.000, NULL),
(41, 13, 12, 20.000, NULL),
(42, 14, 17, 40.000, NULL),
(43, 14, 18, 60.000, NULL),
(44, 14, 24, 50.000, NULL),
(45, 15, 1, 4.000, NULL),
(46, 15, 2, 6.000, NULL),
(47, 15, 8, 10.000, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_reception`
--

CREATE TABLE `ligne_reception` (
  `id_lr` int(11) NOT NULL,
  `id_br` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `qte_recue` decimal(15,3) NOT NULL,
  `prix_unitaire` decimal(15,2) NOT NULL,
  `etat_produit` enum('bon','abime','perime','a_verifier') DEFAULT 'bon',
  `observations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ligne_reception`
--

INSERT INTO `ligne_reception` (`id_lr`, `id_br`, `id_produit`, `qte_recue`, `prix_unitaire`, `etat_produit`, `observations`) VALUES
(1, 1, 1, 10.000, 18500.00, 'bon', NULL),
(2, 1, 2, 20.000, 5200.00, 'bon', NULL),
(3, 1, 3, 5.000, 14000.00, 'bon', NULL),
(4, 2, 17, 30.000, 2500.00, 'bon', NULL),
(5, 2, 18, 20.000, 450.00, 'bon', NULL),
(6, 2, 19, 20.000, 800.00, 'bon', NULL),
(7, 3, 10, 98.000, 350.00, 'bon', NULL),
(8, 3, 11, 50.000, 750.00, 'bon', NULL),
(9, 3, 12, 97.000, 850.00, 'abime', NULL),
(10, 4, 1, 15.000, 18500.00, 'bon', NULL),
(11, 4, 4, 10.000, 22000.00, 'bon', NULL),
(12, 4, 6, 50.000, 750.00, 'bon', NULL),
(13, 4, 7, 80.000, 450.00, 'bon', NULL),
(14, 5, 8, 97.000, 800.00, 'bon', NULL),
(15, 5, 6, 50.000, 750.00, 'bon', NULL),
(16, 5, 14, 20.000, 2500.00, 'bon', NULL),
(17, 6, 24, 100.000, 800.00, 'bon', NULL),
(18, 6, 25, 60.000, 2500.00, 'bon', NULL),
(19, 6, 26, 50.000, 1800.00, 'bon', NULL),
(20, 7, 27, 80.000, 350.00, 'bon', NULL),
(21, 7, 28, 100.000, 300.00, 'bon', NULL),
(22, 7, 29, 20.000, 1200.00, 'bon', NULL),
(23, 8, 1, 20.000, 18500.00, 'bon', NULL),
(24, 8, 2, 30.000, 5200.00, 'bon', NULL),
(25, 8, 4, 10.000, 22000.00, 'bon', NULL),
(26, 8, 5, 200.000, 350.00, 'bon', NULL),
(27, 9, 17, 50.000, 2500.00, 'bon', NULL),
(28, 9, 18, 80.000, 450.00, 'bon', NULL),
(29, 9, 21, 30.000, 800.00, 'bon', NULL),
(30, 10, 20, 40.000, 800.00, 'bon', NULL),
(31, 10, 21, 200.000, 150.00, 'bon', NULL),
(32, 10, 22, 20.000, 3200.00, 'bon', NULL),
(33, 11, 1, 25.000, 18500.00, 'bon', NULL),
(34, 11, 2, 40.000, 5200.00, 'bon', NULL),
(35, 11, 3, 10.000, 14000.00, 'bon', NULL),
(36, 11, 4, 15.000, 22000.00, 'bon', NULL),
(37, 12, 17, 40.000, 2500.00, 'bon', NULL),
(38, 12, 18, 60.000, 450.00, 'bon', NULL),
(39, 12, 23, 30.000, 800.00, 'bon', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `mouvement_stock`
--

CREATE TABLE `mouvement_stock` (
  `id_mouvement` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_mouvement` datetime NOT NULL DEFAULT current_timestamp(),
  `type_mouvement` enum('entree_achat','entree_don','entree_retour','sortie_vente','sortie_perime','sortie_casse','ajustement') NOT NULL,
  `quantite` decimal(15,3) NOT NULL,
  `stock_avant` decimal(15,3) NOT NULL,
  `stock_apres` decimal(15,3) NOT NULL,
  `ref_document` varchar(100) DEFAULT NULL,
  `type_document` varchar(50) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mouvement_stock`
--

INSERT INTO `mouvement_stock` (`id_mouvement`, `id_produit`, `id_utilisateur`, `date_mouvement`, `type_mouvement`, `quantite`, `stock_avant`, `stock_apres`, `ref_document`, `type_document`, `date_creation`) VALUES
(1, 1, 4, '2025-01-08 09:00:00', 'entree_achat', 10.000, 0.000, 10.000, 'BE-2025-001', 'BonEntree', '2026-05-29 21:33:53'),
(2, 2, 4, '2025-01-08 09:05:00', 'entree_achat', 20.000, 0.000, 20.000, 'BE-2025-001', 'BonEntree', '2026-05-29 21:33:53'),
(3, 3, 4, '2025-01-08 09:10:00', 'entree_achat', 5.000, 0.000, 5.000, 'BE-2025-001', 'BonEntree', '2026-05-29 21:33:53'),
(4, 1, 4, '2025-02-07 08:30:00', 'entree_achat', 15.000, 10.000, 25.000, 'BE-2025-004', 'BonEntree', '2026-05-29 21:33:53'),
(5, 4, 4, '2025-02-07 08:35:00', 'entree_achat', 10.000, 0.000, 10.000, 'BE-2025-004', 'BonEntree', '2026-05-29 21:33:53'),
(6, 1, 3, '2025-01-11 10:00:00', 'sortie_vente', 5.000, 25.000, 20.000, 'BL-2025-001', 'BonLivraison', '2026-05-29 21:33:53'),
(7, 2, 3, '2025-01-11 10:05:00', 'sortie_vente', 10.000, 20.000, 10.000, 'BL-2025-001', 'BonLivraison', '2026-05-29 21:33:53'),
(8, 8, 3, '2025-01-11 10:10:00', 'sortie_vente', 20.000, 97.000, 77.000, 'BL-2025-001', 'BonLivraison', '2026-05-29 21:33:53'),
(9, 15, 4, '2025-01-25 11:00:00', 'sortie_perime', 5.000, 80.000, 75.000, 'SS-2025-001', 'SortieStock', '2026-05-29 21:33:53'),
(10, 12, 4, '2025-02-10 14:00:00', 'sortie_perime', 3.000, 97.000, 94.000, 'SS-2025-002', 'SortieStock', '2026-05-29 21:33:53'),
(11, 1, 4, '2025-02-20 09:00:00', 'entree_don', 10.000, 20.000, 30.000, 'BE-2025-009', 'BonEntree', '2026-05-29 21:33:53'),
(12, 5, 4, '2025-02-20 09:05:00', 'entree_don', 20.000, 0.000, 20.000, 'BE-2025-009', 'BonEntree', '2026-05-29 21:33:53'),
(13, 10, 3, '2025-02-15 15:00:00', 'entree_retour', 10.000, 196.000, 206.000, 'SS-2025-009', 'SortieStock', '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `paiement_fournisseur`
--

CREATE TABLE `paiement_fournisseur` (
  `id_paiement` int(11) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `id_facture_f` int(11) NOT NULL,
  `id_transaction` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_paiement` date NOT NULL DEFAULT curdate(),
  `mode_paiement` enum('espece','cheque','virement','mobile_money','carte') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiement_fournisseur`
--

INSERT INTO `paiement_fournisseur` (`id_paiement`, `id_fournisseur`, `id_facture_f`, `id_transaction`, `id_utilisateur`, `montant`, `date_paiement`, `mode_paiement`, `reference`, `observations`, `date_creation`) VALUES
(1, 1, 1, 1, 5, 370527.00, '2025-01-20', 'virement', 'VIR-2025-001', NULL, '2026-05-29 21:33:52'),
(2, 2, 2, 2, 5, 218132.00, '2025-01-25', 'virement', 'VIR-2025-002', NULL, '2026-05-29 21:33:52'),
(3, 4, 3, 3, 5, 255178.00, '2025-01-30', 'cheque', 'CHQ-2025-001', NULL, '2026-05-29 21:33:52'),
(4, 1, 4, 4, 5, 461966.00, '2025-02-25', 'virement', 'VIR-2025-003', NULL, '2026-05-29 21:33:52'),
(5, 3, 5, NULL, 5, 100000.00, '2025-03-01', 'espece', 'ESP-2025-001', NULL, '2026-05-29 21:33:52'),
(6, 7, 6, 5, 5, 312476.00, '2025-03-10', 'virement', 'VIR-2025-004', NULL, '2026-05-29 21:33:52'),
(7, 6, 7, 6, 5, 196133.00, '2025-03-25', 'mobile_money', 'MM-2025-001', NULL, '2026-05-29 21:33:52'),
(8, 1, 8, 7, 5, 504420.00, '2025-04-01', 'virement', 'VIR-2025-005', NULL, '2026-05-29 21:33:52'),
(9, 2, 9, 8, 5, 287101.00, '2025-04-30', 'virement', 'VIR-2025-006', NULL, '2026-05-29 21:33:52'),
(10, 5, 10, 9, 5, 145059.00, '2025-05-10', 'cheque', 'CHQ-2025-002', NULL, '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `id_famille` int(11) NOT NULL,
  `id_produit_pere` int(11) DEFAULT NULL,
  `code_barre` varchar(50) DEFAULT NULL,
  `nom_produit` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `prix_achat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `prix_vente` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock_actuel` decimal(15,3) NOT NULL DEFAULT 0.000,
  `seuil_alerte` decimal(15,3) NOT NULL DEFAULT 0.000,
  `perissable` tinyint(1) DEFAULT 0,
  `date_peremption` date DEFAULT NULL,
  `unite` varchar(20) NOT NULL DEFAULT 'pce',
  `est_actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `id_famille`, `id_produit_pere`, `code_barre`, `nom_produit`, `description`, `prix_achat`, `prix_vente`, `stock_actuel`, `seuil_alerte`, `perissable`, `date_peremption`, `unite`, `est_actif`, `date_creation`, `date_modif`) VALUES
(1, 1, NULL, '6001234000001', 'Riz parfumé 25kg', 'Sac de riz parfumé thaï', 18500.00, 22000.00, 80.000, 5.000, 0, NULL, 'sac', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 1, NULL, '6001234000002', 'Huile végétale 5L', 'Huile de palme raffinée', 5200.00, 6500.00, 110.000, 10.000, 0, NULL, 'bidon', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 1, NULL, '6001234000003', 'Farine de blé 50kg', 'Farine type 55', 14000.00, 17000.00, 30.000, 5.000, 0, NULL, 'sac', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 1, NULL, '6001234000004', 'Sucre cristallisé 50kg', 'Sucre blanc cristal', 22000.00, 26500.00, 45.000, 5.000, 0, NULL, 'sac', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 1, NULL, '6001234000005', 'Sel iodé 1kg', 'Sel de table iodé', 350.00, 500.00, 220.000, 20.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 1, NULL, '6001234000006', 'Tomate concentrée 400g', 'Boite de conserve', 750.00, 1000.00, 145.000, 15.000, 0, '2026-06-01', 'boite', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 1, NULL, '6001234000007', 'Sardines à la tomate', 'Conserve 125g', 450.00, 650.00, 177.000, 10.000, 0, '2026-09-01', 'boite', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 1, NULL, '6001234000008', 'Cube Maggi 100pcs', 'Assaisonnement', 800.00, 1100.00, 247.000, 30.000, 0, '2025-12-01', 'boite', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 2, NULL, '6001234000010', 'Eau minérale 1.5L', 'Bouteille eau plate', 350.00, 500.00, 500.000, 50.000, 0, '2026-01-01', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 2, NULL, '6001234000011', 'Coca-Cola 1.5L', 'Soda cola bouteille pet', 750.00, 1000.00, 196.000, 20.000, 0, '2025-11-01', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 2, NULL, '6001234000012', 'Bière Castel 65cl', 'Bière blonde locale', 850.00, 1200.00, 100.000, 30.000, 0, '2025-10-01', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 2, NULL, '6001234000013', 'Jus de fruit 1L', 'Jus multi-fruits tropicaux', 600.00, 900.00, 97.000, 15.000, 0, '2025-09-01', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(13, 2, NULL, '6001234000014', 'Vin rouge 75cl', 'Vin de table importé', 2500.00, 3500.00, 40.000, 6.000, 0, '2027-01-01', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(14, 3, NULL, '6001234000020', 'Lait concentré 400g', 'Lait sucré en boite', 650.00, 900.00, 20.000, 10.000, 0, '2026-03-01', 'boite', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(15, 3, NULL, '6001234000021', 'Yaourt nature 400g', 'Yaourt local artisanal', 400.00, 600.00, 80.000, 8.000, 1, '2025-05-15', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(16, 3, NULL, '6001234000022', 'Beurre 250g', 'Beurre de cuisine', 900.00, 1200.00, 60.000, 6.000, 1, '2025-06-30', 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(17, 6, NULL, '6001234000030', 'Savon Palmolive 90g', 'Savon de toilette', 350.00, 500.00, 75.000, 40.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(18, 6, NULL, '6001234000031', 'Shampoing Pantène 400ml', 'Shampoing fortifiant', 2200.00, 3000.00, 160.000, 8.000, 0, NULL, 'flacon', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(19, 6, NULL, '6001234000032', 'Dentifrice Colgate 75ml', 'Dentifrice menthe fraîche', 750.00, 1050.00, 30.000, 12.000, 0, NULL, 'tube', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(20, 6, NULL, '6001234000033', 'Déodorant AXE 150ml', 'Spray déodorant homme', 1800.00, 2500.00, 40.000, 6.000, 0, NULL, 'flacon', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(21, 6, NULL, '6001234000034', 'Serviette hygiénique 10', 'Pack serviettes Always', 1200.00, 1800.00, 200.000, 8.000, 0, NULL, 'pack', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(22, 7, NULL, '6001234000040', 'Lessive OMO 2kg', 'Lessive en poudre', 2500.00, 3500.00, 20.000, 12.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(23, 7, NULL, '6001234000041', 'Eau de javel 1L', 'Javel 12° chlorométriques', 450.00, 700.00, 30.000, 20.000, 0, NULL, 'bidon', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(24, 7, NULL, '6001234000042', 'Balai brosse', 'Balai de ménage', 800.00, 1200.00, 150.000, 5.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(25, 7, NULL, '6001234000043', 'Seau plastique 15L', 'Seau ménager', 900.00, 1400.00, 60.000, 4.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(26, 8, NULL, '6001234000050', 'Cahier 200 pages', 'Cahier grand format', 800.00, 1200.00, 50.000, 20.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(27, 8, NULL, '6001234000051', 'Stylo bille bleu', 'Stylo bille BIC', 150.00, 250.00, 80.000, 50.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(28, 8, NULL, '6001234000052', 'Rame A4 80g', 'Papier ramette 500 feuilles', 3200.00, 4500.00, 100.000, 8.000, 0, NULL, 'rame', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(29, 9, NULL, '6001234000060', 'Câble USB-C 1m', 'Câble charge rapide', 800.00, 1500.00, 20.000, 10.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(30, 9, NULL, '6001234000061', 'Chargeur mural 20W', 'Adaptateur secteur', 2500.00, 4000.00, 60.000, 6.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(31, 9, NULL, '6001234000062', 'Écouteurs intra', 'Écouteurs stéréo jack 3.5', 1800.00, 3000.00, 50.000, 5.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(32, 5, NULL, '6001234000070', 'Tomates fraîches 1kg', 'Tomates locales fraîches', 350.00, 600.00, 50.000, 5.000, 1, NULL, 'kg', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(33, 5, NULL, '6001234000071', 'Oignons 1kg', 'Oignons blancs locaux', 300.00, 500.00, 80.000, 8.000, 1, NULL, 'kg', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(34, 5, NULL, '6001234000072', 'Bananes plantain', 'Régime de plantains', 1200.00, 2000.00, 30.000, 3.000, 1, NULL, 'regime', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(35, 1, 1, '6001234100001', 'Riz parfumé 1kg', 'Issu fractionnement sac 25kg', 900.00, 1200.00, 0.000, 5.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(36, 1, 1, '6001234100002', 'Riz parfumé 5kg', 'Issu fractionnement sac 25kg', 4200.00, 5500.00, 0.000, 3.000, 0, NULL, 'pce', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(37, 2, 9, '6001234100010', 'Eau minérale 6x1.5L', 'Pack de 6 bouteilles', 1900.00, 2800.00, 0.000, 5.000, 0, NULL, 'pack', 1, '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `reglement_client`
--

CREATE TABLE `reglement_client` (
  `id_reglement` int(11) NOT NULL,
  `id_facture` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_transaction` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_reglement` date NOT NULL DEFAULT curdate(),
  `mode_paiement` enum('espece','cheque','virement','mobile_money','carte') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reglement_client`
--

INSERT INTO `reglement_client` (`id_reglement`, `id_facture`, `id_client`, `id_transaction`, `id_utilisateur`, `montant`, `date_reglement`, `mode_paiement`, `reference`, `observations`, `date_creation`) VALUES
(1, 1, 1, 10, 3, 480171.00, '2025-01-15', 'virement', 'VIR-CLI-001', NULL, '2026-05-29 21:33:53'),
(2, 2, 4, 11, 3, 320249.00, '2025-02-10', 'virement', 'VIR-CLI-002', NULL, '2026-05-29 21:33:53'),
(3, 3, 5, NULL, 3, 215101.00, '2025-01-18', 'espece', 'ESP-CLI-001', NULL, '2026-05-29 21:33:53'),
(4, 4, 10, NULL, 3, 42500.00, '2025-01-20', 'espece', 'ESP-CLI-002', NULL, '2026-05-29 21:33:53'),
(5, 5, 13, NULL, 3, 18500.00, '2025-01-22', 'mobile_money', 'MM-CLI-001', NULL, '2026-05-29 21:33:53'),
(6, 6, 1, 12, 3, 560381.00, '2025-02-10', 'virement', 'VIR-CLI-003', NULL, '2026-05-29 21:33:53'),
(7, 7, 2, NULL, 3, 390035.00, '2025-02-07', 'cheque', 'CHQ-CLI-001', NULL, '2026-05-29 21:33:53'),
(8, 8, 5, 13, 3, 275138.00, '2025-03-05', 'virement', 'VIR-CLI-004', NULL, '2026-05-29 21:33:53'),
(9, 9, 11, NULL, 3, 35000.00, '2025-02-14', 'espece', 'ESP-CLI-003', NULL, '2026-05-29 21:33:53'),
(10, 10, 6, NULL, 3, 185050.00, '2025-02-22', 'mobile_money', 'MM-CLI-002', NULL, '2026-05-29 21:33:53'),
(11, 11, 3, 14, 3, 449894.00, '2025-03-18', 'virement', 'VIR-CLI-005', NULL, '2026-05-29 21:33:53'),
(12, 12, 8, 15, 3, 128050.00, '2025-04-08', 'virement', 'VIR-CLI-006', NULL, '2026-05-29 21:33:53'),
(13, 13, 14, NULL, 3, 22000.00, '2025-03-15', 'espece', 'ESP-CLI-004', NULL, '2026-05-29 21:33:53'),
(14, 14, 5, NULL, 3, 200000.00, '2025-04-01', 'virement', 'VIR-CLI-007', NULL, '2026-05-29 21:33:53'),
(15, 15, 7, 16, 3, 240206.00, '2025-04-04', 'virement', 'VIR-CLI-008', NULL, '2026-05-29 21:33:53'),
(16, 16, 2, NULL, 3, 380244.00, '2025-04-07', 'cheque', 'CHQ-CLI-002', NULL, '2026-05-29 21:33:53'),
(17, 17, 12, NULL, 3, 29500.00, '2025-04-14', 'espece', 'ESP-CLI-005', NULL, '2026-05-29 21:33:53'),
(18, 18, 20, 17, 3, 175114.00, '2025-04-19', 'virement', 'VIR-CLI-009', NULL, '2026-05-29 21:33:53'),
(19, 19, 13, NULL, 3, 15000.00, '2025-05-08', 'mobile_money', 'MM-CLI-003', NULL, '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `retrait`
--

CREATE TABLE `retrait` (
  `id_retrait` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_transaction` int(11) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_retrait` date NOT NULL DEFAULT curdate(),
  `motif` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `retrait`
--

INSERT INTO `retrait` (`id_retrait`, `id_compte`, `id_utilisateur`, `id_transaction`, `montant`, `date_retrait`, `motif`, `date_creation`) VALUES
(1, 1, 5, 21, 300000.00, '2025-02-28', 'Frais généraux et charges mensuelles', '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `sortie_stock`
--

CREATE TABLE `sortie_stock` (
  `id_sortie` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_sortie` date NOT NULL DEFAULT curdate(),
  `quantite` decimal(15,3) NOT NULL,
  `motif_sortie` enum('perime','non_vendu','retour_client','casse','don','autre') NOT NULL,
  `reference` varchar(50) NOT NULL,
  `observations` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sortie_stock`
--

INSERT INTO `sortie_stock` (`id_sortie`, `id_produit`, `id_client`, `id_utilisateur`, `date_sortie`, `quantite`, `motif_sortie`, `reference`, `observations`, `date_creation`) VALUES
(1, 15, NULL, 4, '2025-01-25', 5.000, 'perime', 'SS-2025-001', 'Yaourt périmé date dépassée', '2026-05-29 21:33:53'),
(2, 12, NULL, 4, '2025-02-10', 3.000, 'perime', 'SS-2025-002', 'Bières cassées réception BR-003', '2026-05-29 21:33:53'),
(3, 27, NULL, 4, '2025-02-28', 10.000, 'perime', 'SS-2025-003', 'Tomates fraîches non vendues', '2026-05-29 21:33:53'),
(4, 16, NULL, 4, '2025-03-10', 2.000, 'casse', 'SS-2025-004', 'Beurre tombé en entrepôt', '2026-05-29 21:33:53'),
(5, 28, NULL, 4, '2025-03-25', 8.000, 'perime', 'SS-2025-005', 'Oignons avariés', '2026-05-29 21:33:53'),
(6, 15, NULL, 4, '2025-04-05', 3.000, 'non_vendu', 'SS-2025-006', 'Yaourt invendu fin de semaine', '2026-05-29 21:33:53'),
(7, 29, NULL, 4, '2025-04-20', 5.000, 'perime', 'SS-2025-007', 'Plantains trop mûrs', '2026-05-29 21:33:53'),
(8, 6, NULL, 4, '2025-05-02', 5.000, 'perime', 'SS-2025-008', 'Tomates concentrées dates passées', '2026-05-29 21:33:53'),
(9, 10, 5, 3, '2025-02-15', 10.000, 'retour_client', 'SS-2025-009', 'Retour DOVV eau minérale', '2026-05-29 21:33:53'),
(10, 11, 7, 3, '2025-03-18', 5.000, 'retour_client', 'SS-2025-010', 'Retour Coca-Cola Shop&Go', '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `transaction_bancaire`
--

CREATE TABLE `transaction_bancaire` (
  `id_transaction` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `type_mouvement` enum('debit','credit') NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `solde_apres` decimal(15,2) NOT NULL,
  `date_transaction` datetime NOT NULL DEFAULT current_timestamp(),
  `libelle` varchar(300) NOT NULL,
  `reference_externe` varchar(100) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `transaction_bancaire`
--

INSERT INTO `transaction_bancaire` (`id_transaction`, `id_compte`, `id_utilisateur`, `type_mouvement`, `montant`, `solde_apres`, `date_transaction`, `libelle`, `reference_externe`, `date_creation`) VALUES
(1, 1, 5, 'debit', 370527.00, 15379473.00, '2025-01-20 00:00:00', 'Paiement FF-SCPC-0125-001 SCPC Cameroun', NULL, '2026-05-29 21:33:52'),
(2, 1, 5, 'debit', 218132.00, 15161341.00, '2025-01-25 00:00:00', 'Paiement FF-SOAC-0125-001 SOACAM', NULL, '2026-05-29 21:33:52'),
(3, 1, 5, 'debit', 255178.00, 14906163.00, '2025-01-30 00:00:00', 'Paiement FF-BRAS-0125-001 Brasseries', NULL, '2026-05-29 21:33:52'),
(4, 1, 5, 'debit', 461966.00, 14444197.00, '2025-02-25 00:00:00', 'Paiement FF-SCPC-0225-001 SCPC', NULL, '2026-05-29 21:33:52'),
(5, 3, 5, 'debit', 312476.00, 7987524.00, '2025-03-10 00:00:00', 'Paiement FF-TECH-0225-001 TechImport', NULL, '2026-05-29 21:33:52'),
(6, 1, 5, 'debit', 196133.00, 14248064.00, '2025-03-25 00:00:00', 'Paiement FF-FOOD-0325-001 FoodDistrib', NULL, '2026-05-29 21:33:52'),
(7, 1, 5, 'debit', 504420.00, 13743644.00, '2025-04-01 00:00:00', 'Paiement FF-SCPC-0325-001 SCPC', NULL, '2026-05-29 21:33:52'),
(8, 1, 5, 'debit', 287101.00, 13456543.00, '2025-04-30 00:00:00', 'Paiement FF-SOAC-0425-001 SOACAM', NULL, '2026-05-29 21:33:52'),
(9, 4, 5, 'debit', 145059.00, 5979941.00, '2025-05-10 00:00:00', 'Paiement FF-CICA-0425-001 CICAM', NULL, '2026-05-29 21:33:52'),
(10, 1, 3, 'credit', 500000.00, 13956543.00, '2025-01-15 00:00:00', 'Versement CAMAIR-CO facture FC-001', NULL, '2026-05-29 21:33:52'),
(11, 1, 3, 'credit', 750000.00, 14706543.00, '2025-02-10 00:00:00', 'Versement CHU Yaoundé', NULL, '2026-05-29 21:33:52'),
(12, 1, 3, 'credit', 350000.00, 15056543.00, '2025-02-25 00:00:00', 'Encaissement ventes comptant semaine 8', NULL, '2026-05-29 21:33:52'),
(13, 1, 3, 'credit', 620000.00, 15676543.00, '2025-03-05 00:00:00', 'Versement DOVV supermarché', NULL, '2026-05-29 21:33:52'),
(14, 1, 3, 'credit', 280000.00, 15956543.00, '2025-03-20 00:00:00', 'Encaissement caisse semaine 12', NULL, '2026-05-29 21:33:52'),
(15, 1, 3, 'credit', 490000.00, 16446543.00, '2025-04-08 00:00:00', 'Versement Fondation Paul Biya', NULL, '2026-05-29 21:33:52'),
(16, 1, 3, 'credit', 380000.00, 16826543.00, '2025-04-22 00:00:00', 'Encaissement ventes diverses', NULL, '2026-05-29 21:33:52'),
(17, 1, 3, 'credit', 650000.00, 17476543.00, '2025-05-10 00:00:00', 'Versement Supermarché DOVV', NULL, '2026-05-29 21:33:52'),
(18, 1, 5, 'credit', 2000000.00, 19476543.00, '2025-01-02 00:00:00', 'Apport capital début exercice', NULL, '2026-05-29 21:33:53'),
(19, 1, 5, 'credit', 1500000.00, 20976543.00, '2025-01-31 00:00:00', 'Recettes mensuelle janvier', NULL, '2026-05-29 21:33:53'),
(20, 2, 5, 'credit', 500000.00, 4700000.00, '2025-02-15 00:00:00', 'Versement compte épargne', NULL, '2026-05-29 21:33:53'),
(21, 1, 5, 'debit', 300000.00, 20676543.00, '2025-02-28 00:00:00', 'Frais généraux opérationnels', NULL, '2026-05-29 21:33:53'),
(22, 1, 5, 'credit', 1800000.00, 22476543.00, '2025-03-31 00:00:00', 'Recettes mensuelle mars', NULL, '2026-05-29 21:33:53'),
(23, 3, 5, 'credit', 1000000.00, 9300000.00, '2025-04-01 00:00:00', 'Approvisionnement compte AFB', NULL, '2026-05-29 21:33:53');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `nom_complet` varchar(200) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `derniere_connexion` datetime DEFAULT NULL,
  `nb_tentatives_echouees` int(11) DEFAULT 0,
  `date_expiration_mdp` date DEFAULT NULL,
  `ip_derniere_connexion` varchar(45) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modif` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `id_groupe`, `nom_complet`, `login`, `password_hash`, `actif`, `derniere_connexion`, `nb_tentatives_echouees`, `date_expiration_mdp`, `ip_derniere_connexion`, `date_creation`, `date_modif`) VALUES
(1, 1, 'Mbarga Jean-Paul', 'admin', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(2, 2, 'Ngo Biyong Cécile', 'cecile.ngo', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(3, 3, 'Eto Achille', 'achille.eto', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(4, 4, 'Foko Didier', 'didier.foko', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(5, 5, 'Atangana Rose', 'rose.atang', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(6, 6, 'Bello Ibrahim', 'bello.ibr', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(7, 7, 'Kamga Sylvie', 'sylvie.kamg', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(8, 8, 'Nkolo François', 'nkolo.fra', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(9, 3, 'Owona Patrick', 'patrick.ow', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-06-30', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(10, 3, 'Mengue Christine', 'chris.meng', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-06-30', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(11, 2, 'Tabi Serge', 'serge.tabi', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 1, NULL, 0, '2026-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52'),
(12, 4, 'Essomba Honoré', 'essomba.h', '$2b$12$KIx4yQ3HzPj5lW2mNvT6TuE3f1gRkA9sD7vY0cB2nX8pL4qM6jH5K', 0, NULL, 0, '2025-12-31', NULL, '2026-05-29 21:33:52', '2026-05-29 21:33:52');

-- --------------------------------------------------------

--
-- Structure de la table `versement`
--

CREATE TABLE `versement` (
  `id_versement` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_transaction` int(11) NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_versement` date NOT NULL DEFAULT curdate(),
  `observation` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `versement`
--

INSERT INTO `versement` (`id_versement`, `id_compte`, `id_utilisateur`, `id_transaction`, `montant`, `date_versement`, `observation`, `date_creation`) VALUES
(1, 1, 5, 18, 2000000.00, '2025-01-02', 'Apport capital début exercice 2025', '2026-05-29 21:33:53'),
(2, 1, 5, 19, 1500000.00, '2025-01-31', 'Versement recettes janvier', '2026-05-29 21:33:53'),
(3, 2, 5, 20, 500000.00, '2025-02-15', 'Alimentation compte épargne', '2026-05-29 21:33:53'),
(4, 1, 5, 22, 1800000.00, '2025-03-31', 'Versement recettes mars', '2026-05-29 21:33:53'),
(5, 3, 5, 23, 1000000.00, '2025-04-01', 'Versement compte AFB Douala', '2026-05-29 21:33:53');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `banque`
--
ALTER TABLE `banque`
  ADD PRIMARY KEY (`id_banque`);

--
-- Index pour la table `bon_commande_fourn`
--
ALTER TABLE `bon_commande_fourn`
  ADD PRIMARY KEY (`id_bcf`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_bcf_fourn` (`id_fournisseur`);

--
-- Index pour la table `bon_entree`
--
ALTER TABLE `bon_entree`
  ADD PRIMARY KEY (`id_be`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_br` (`id_br`),
  ADD KEY `id_don` (`id_don`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `bon_livraison`
--
ALTER TABLE `bon_livraison`
  ADD PRIMARY KEY (`id_bl`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_cc` (`id_cc`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `bon_reception`
--
ALTER TABLE `bon_reception`
  ADD PRIMARY KEY (`id_br`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_bcf` (`id_bcf`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `categorie_client`
--
ALTER TABLE `categorie_client`
  ADD PRIMARY KEY (`id_categorie_client`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`),
  ADD KEY `id_categorie_client` (`id_categorie_client`);

--
-- Index pour la table `commande_client`
--
ALTER TABLE `commande_client`
  ADD PRIMARY KEY (`id_cc`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_cc_client` (`id_client`),
  ADD KEY `idx_cc_statut` (`statut`),
  ADD KEY `idx_cc_date` (`date_commande`);

--
-- Index pour la table `compte_bancaire`
--
ALTER TABLE `compte_bancaire`
  ADD PRIMARY KEY (`id_compte`),
  ADD UNIQUE KEY `numero_compte` (`numero_compte`),
  ADD KEY `id_banque` (`id_banque`);

--
-- Index pour la table `don`
--
ALTER TABLE `don`
  ADD PRIMARY KEY (`id_don`);

--
-- Index pour la table `droit`
--
ALTER TABLE `droit`
  ADD PRIMARY KEY (`id_droit`);

--
-- Index pour la table `facture_client`
--
ALTER TABLE `facture_client`
  ADD PRIMARY KEY (`id_facture`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_cc` (`id_cc`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  ADD PRIMARY KEY (`id_facture_f`),
  ADD KEY `id_bcf` (`id_bcf`),
  ADD KEY `idx_ff_fourn` (`id_fournisseur`),
  ADD KEY `idx_ff_statut` (`statut`);

--
-- Index pour la table `famille`
--
ALTER TABLE `famille`
  ADD PRIMARY KEY (`id_famille`);

--
-- Index pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD PRIMARY KEY (`id_fournisseur`);

--
-- Index pour la table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`id_groupe`),
  ADD UNIQUE KEY `nom_groupe` (`nom_groupe`);

--
-- Index pour la table `groupe_droit`
--
ALTER TABLE `groupe_droit`
  ADD PRIMARY KEY (`id_groupe`,`id_droit`),
  ADD KEY `id_droit` (`id_droit`);

--
-- Index pour la table `journal_audit`
--
ALTER TABLE `journal_audit`
  ADD PRIMARY KEY (`id_audit`),
  ADD KEY `idx_audit_user` (`id_utilisateur`),
  ADD KEY `idx_audit_date` (`date_heure`);

--
-- Index pour la table `ligne_bon_entree`
--
ALTER TABLE `ligne_bon_entree`
  ADD PRIMARY KEY (`id_lbe`),
  ADD KEY `id_be` (`id_be`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `ligne_commande_client`
--
ALTER TABLE `ligne_commande_client`
  ADD PRIMARY KEY (`id_lcc`),
  ADD KEY `id_cc` (`id_cc`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `ligne_commande_fourn`
--
ALTER TABLE `ligne_commande_fourn`
  ADD PRIMARY KEY (`id_lcf`),
  ADD KEY `id_bcf` (`id_bcf`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `ligne_facture_fourn`
--
ALTER TABLE `ligne_facture_fourn`
  ADD PRIMARY KEY (`id_ligne_ff`),
  ADD KEY `id_facture_f` (`id_facture_f`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `ligne_livraison`
--
ALTER TABLE `ligne_livraison`
  ADD PRIMARY KEY (`id_ll`),
  ADD KEY `id_bl` (`id_bl`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `ligne_reception`
--
ALTER TABLE `ligne_reception`
  ADD PRIMARY KEY (`id_lr`),
  ADD KEY `id_br` (`id_br`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD PRIMARY KEY (`id_mouvement`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_ms_produit` (`id_produit`),
  ADD KEY `idx_ms_date` (`date_mouvement`);

--
-- Index pour la table `paiement_fournisseur`
--
ALTER TABLE `paiement_fournisseur`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_facture_f` (`id_facture_f`),
  ADD KEY `id_transaction` (`id_transaction`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `code_barre` (`code_barre`),
  ADD KEY `id_produit_pere` (`id_produit_pere`),
  ADD KEY `idx_produit_famille` (`id_famille`),
  ADD KEY `idx_produit_barre` (`code_barre`);

--
-- Index pour la table `reglement_client`
--
ALTER TABLE `reglement_client`
  ADD PRIMARY KEY (`id_reglement`),
  ADD KEY `id_facture` (`id_facture`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_transaction` (`id_transaction`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `retrait`
--
ALTER TABLE `retrait`
  ADD PRIMARY KEY (`id_retrait`),
  ADD KEY `id_compte` (`id_compte`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_transaction` (`id_transaction`);

--
-- Index pour la table `sortie_stock`
--
ALTER TABLE `sortie_stock`
  ADD PRIMARY KEY (`id_sortie`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `transaction_bancaire`
--
ALTER TABLE `transaction_bancaire`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_tb_compte` (`id_compte`),
  ADD KEY `idx_tb_date` (`date_transaction`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `id_groupe` (`id_groupe`);

--
-- Index pour la table `versement`
--
ALTER TABLE `versement`
  ADD PRIMARY KEY (`id_versement`),
  ADD KEY `id_compte` (`id_compte`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_transaction` (`id_transaction`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `banque`
--
ALTER TABLE `banque`
  MODIFY `id_banque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `bon_commande_fourn`
--
ALTER TABLE `bon_commande_fourn`
  MODIFY `id_bcf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `bon_entree`
--
ALTER TABLE `bon_entree`
  MODIFY `id_be` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `bon_livraison`
--
ALTER TABLE `bon_livraison`
  MODIFY `id_bl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `bon_reception`
--
ALTER TABLE `bon_reception`
  MODIFY `id_br` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `categorie_client`
--
ALTER TABLE `categorie_client`
  MODIFY `id_categorie_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `commande_client`
--
ALTER TABLE `commande_client`
  MODIFY `id_cc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `compte_bancaire`
--
ALTER TABLE `compte_bancaire`
  MODIFY `id_compte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `don`
--
ALTER TABLE `don`
  MODIFY `id_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `droit`
--
ALTER TABLE `droit`
  MODIFY `id_droit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `facture_client`
--
ALTER TABLE `facture_client`
  MODIFY `id_facture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  MODIFY `id_facture_f` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `famille`
--
ALTER TABLE `famille`
  MODIFY `id_famille` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  MODIFY `id_fournisseur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `groupe`
--
ALTER TABLE `groupe`
  MODIFY `id_groupe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `journal_audit`
--
ALTER TABLE `journal_audit`
  MODIFY `id_audit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `ligne_bon_entree`
--
ALTER TABLE `ligne_bon_entree`
  MODIFY `id_lbe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `ligne_commande_client`
--
ALTER TABLE `ligne_commande_client`
  MODIFY `id_lcc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `ligne_commande_fourn`
--
ALTER TABLE `ligne_commande_fourn`
  MODIFY `id_lcf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `ligne_facture_fourn`
--
ALTER TABLE `ligne_facture_fourn`
  MODIFY `id_ligne_ff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `ligne_livraison`
--
ALTER TABLE `ligne_livraison`
  MODIFY `id_ll` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT pour la table `ligne_reception`
--
ALTER TABLE `ligne_reception`
  MODIFY `id_lr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  MODIFY `id_mouvement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `paiement_fournisseur`
--
ALTER TABLE `paiement_fournisseur`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `reglement_client`
--
ALTER TABLE `reglement_client`
  MODIFY `id_reglement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `retrait`
--
ALTER TABLE `retrait`
  MODIFY `id_retrait` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `sortie_stock`
--
ALTER TABLE `sortie_stock`
  MODIFY `id_sortie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `transaction_bancaire`
--
ALTER TABLE `transaction_bancaire`
  MODIFY `id_transaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `versement`
--
ALTER TABLE `versement`
  MODIFY `id_versement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bon_commande_fourn`
--
ALTER TABLE `bon_commande_fourn`
  ADD CONSTRAINT `bon_commande_fourn_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_commande_fourn_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_entree`
--
ALTER TABLE `bon_entree`
  ADD CONSTRAINT `bon_entree_ibfk_1` FOREIGN KEY (`id_br`) REFERENCES `bon_reception` (`id_br`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_entree_ibfk_2` FOREIGN KEY (`id_don`) REFERENCES `don` (`id_don`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_entree_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_livraison`
--
ALTER TABLE `bon_livraison`
  ADD CONSTRAINT `bon_livraison_ibfk_1` FOREIGN KEY (`id_cc`) REFERENCES `commande_client` (`id_cc`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_livraison_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_reception`
--
ALTER TABLE `bon_reception`
  ADD CONSTRAINT `bon_reception_ibfk_1` FOREIGN KEY (`id_bcf`) REFERENCES `bon_commande_fourn` (`id_bcf`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_reception_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`id_categorie_client`) REFERENCES `categorie_client` (`id_categorie_client`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_client`
--
ALTER TABLE `commande_client`
  ADD CONSTRAINT `commande_client_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_client_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `compte_bancaire`
--
ALTER TABLE `compte_bancaire`
  ADD CONSTRAINT `compte_bancaire_ibfk_1` FOREIGN KEY (`id_banque`) REFERENCES `banque` (`id_banque`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_client`
--
ALTER TABLE `facture_client`
  ADD CONSTRAINT `facture_client_ibfk_1` FOREIGN KEY (`id_cc`) REFERENCES `commande_client` (`id_cc`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_client_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  ADD CONSTRAINT `facture_fournisseur_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ibfk_2` FOREIGN KEY (`id_bcf`) REFERENCES `bon_commande_fourn` (`id_bcf`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `groupe_droit`
--
ALTER TABLE `groupe_droit`
  ADD CONSTRAINT `groupe_droit_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupe` (`id_groupe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `groupe_droit_ibfk_2` FOREIGN KEY (`id_droit`) REFERENCES `droit` (`id_droit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `journal_audit`
--
ALTER TABLE `journal_audit`
  ADD CONSTRAINT `journal_audit_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_bon_entree`
--
ALTER TABLE `ligne_bon_entree`
  ADD CONSTRAINT `ligne_bon_entree_ibfk_1` FOREIGN KEY (`id_be`) REFERENCES `bon_entree` (`id_be`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_bon_entree_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_commande_client`
--
ALTER TABLE `ligne_commande_client`
  ADD CONSTRAINT `ligne_commande_client_ibfk_1` FOREIGN KEY (`id_cc`) REFERENCES `commande_client` (`id_cc`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_commande_client_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_commande_fourn`
--
ALTER TABLE `ligne_commande_fourn`
  ADD CONSTRAINT `ligne_commande_fourn_ibfk_1` FOREIGN KEY (`id_bcf`) REFERENCES `bon_commande_fourn` (`id_bcf`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_commande_fourn_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_facture_fourn`
--
ALTER TABLE `ligne_facture_fourn`
  ADD CONSTRAINT `ligne_facture_fourn_ibfk_1` FOREIGN KEY (`id_facture_f`) REFERENCES `facture_fournisseur` (`id_facture_f`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_facture_fourn_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_livraison`
--
ALTER TABLE `ligne_livraison`
  ADD CONSTRAINT `ligne_livraison_ibfk_1` FOREIGN KEY (`id_bl`) REFERENCES `bon_livraison` (`id_bl`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_livraison_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `ligne_reception`
--
ALTER TABLE `ligne_reception`
  ADD CONSTRAINT `ligne_reception_ibfk_1` FOREIGN KEY (`id_br`) REFERENCES `bon_reception` (`id_br`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ligne_reception_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `mouvement_stock`
--
ALTER TABLE `mouvement_stock`
  ADD CONSTRAINT `mouvement_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mouvement_stock_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `paiement_fournisseur`
--
ALTER TABLE `paiement_fournisseur`
  ADD CONSTRAINT `paiement_fournisseur_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paiement_fournisseur_ibfk_2` FOREIGN KEY (`id_facture_f`) REFERENCES `facture_fournisseur` (`id_facture_f`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paiement_fournisseur_ibfk_3` FOREIGN KEY (`id_transaction`) REFERENCES `transaction_bancaire` (`id_transaction`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `paiement_fournisseur_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_famille`) REFERENCES `famille` (`id_famille`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_2` FOREIGN KEY (`id_produit_pere`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `reglement_client`
--
ALTER TABLE `reglement_client`
  ADD CONSTRAINT `reglement_client_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture_client` (`id_facture`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reglement_client_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reglement_client_ibfk_3` FOREIGN KEY (`id_transaction`) REFERENCES `transaction_bancaire` (`id_transaction`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `reglement_client_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `retrait`
--
ALTER TABLE `retrait`
  ADD CONSTRAINT `retrait_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte_bancaire` (`id_compte`) ON UPDATE CASCADE,
  ADD CONSTRAINT `retrait_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `retrait_ibfk_3` FOREIGN KEY (`id_transaction`) REFERENCES `transaction_bancaire` (`id_transaction`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `sortie_stock`
--
ALTER TABLE `sortie_stock`
  ADD CONSTRAINT `sortie_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sortie_stock_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sortie_stock_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `transaction_bancaire`
--
ALTER TABLE `transaction_bancaire`
  ADD CONSTRAINT `transaction_bancaire_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte_bancaire` (`id_compte`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_bancaire_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupe` (`id_groupe`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `versement`
--
ALTER TABLE `versement`
  ADD CONSTRAINT `versement_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte_bancaire` (`id_compte`) ON UPDATE CASCADE,
  ADD CONSTRAINT `versement_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `versement_ibfk_3` FOREIGN KEY (`id_transaction`) REFERENCES `transaction_bancaire` (`id_transaction`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
