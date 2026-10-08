-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 07:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reviewtool`
--

-- --------------------------------------------------------

--
-- Table structure for table `base_prices`
--

CREATE TABLE `base_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `property_type` varchar(50) NOT NULL COMMENT 'Objektart',
  `base_price_per_sqm` decimal(12,2) NOT NULL COMMENT 'Basispreis CHF/m²',
  `currency` varchar(5) NOT NULL DEFAULT 'CHF',
  `unit` varchar(20) NOT NULL DEFAULT 'm²',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `base_prices`
--

INSERT INTO `base_prices` (`id`, `property_type`, `base_price_per_sqm`, `currency`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'Einfamilienhaus', 8188.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(2, 'Mehrfamilienhaus', 6800.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 16:15:25'),
(3, 'Wohnung', 8900.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 16:14:18'),
(4, 'Reihenhaus', 7700.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 16:14:38'),
(5, 'Doppelhaus', 7950.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 16:14:53'),
(6, 'Grundstück', 1000.00, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(7, 'Landwirtschaftlich', 0.10, 'CHF', 'm²', '2026-10-06 14:01:39', '2026-10-06 14:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `condition_factors`
--

CREATE TABLE `condition_factors` (
  `id` int(10) UNSIGNED NOT NULL,
  `property_condition` varchar(30) NOT NULL COMMENT 'Zustand',
  `factor` decimal(4,2) NOT NULL COMMENT 'Faktor',
  `description` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `condition_factors`
--

INSERT INTO `condition_factors` (`id`, `property_condition`, `factor`, `description`, `created_at`, `updated_at`) VALUES
(1, 'neuwertig', 1.15, 'Neuwertig', '2026-10-06 14:01:39', '2026-10-06 15:15:23'),
(2, 'renoviert', 1.08, 'Renoviert', '2026-10-06 14:01:39', '2026-10-06 15:15:15'),
(3, 'gepflegt', 1.00, 'Gepflegt', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(4, 'sanierungsbedürftig', 0.82, 'Sanierungsbedürftig', '2026-10-06 14:01:39', '2026-10-06 15:15:30'),
(5, 'renierungsbedürftig', 0.68, 'Renierungsbedürftig', '2026-10-06 14:01:39', '2026-10-06 15:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `development_potential_factors`
--

CREATE TABLE `development_potential_factors` (
  `id` int(11) NOT NULL,
  `reserve_type` varchar(30) NOT NULL COMMENT 'Ausnützungsreserve Typ',
  `factor` decimal(4,2) NOT NULL COMMENT 'Faktor',
  `description` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `development_potential_factors`
--

INSERT INTO `development_potential_factors` (`id`, `reserve_type`, `factor`, `description`) VALUES
(1, 'none', 1.00, 'Ausnützung voll ausgeschöpft (kein Ausbau)'),
(2, 'minor', 1.10, 'Moderate Reserve (+10%, anbau-/aufstockbar)'),
(3, 'major', 1.20, 'Grosse Ausnützungsreserve / Verdichtungspotenzial (+20%)');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_factors`
--

CREATE TABLE `equipment_factors` (
  `id` int(10) UNSIGNED NOT NULL,
  `equipment` varchar(20) NOT NULL COMMENT 'Ausstattung',
  `factor` decimal(4,2) NOT NULL COMMENT 'Faktor',
  `description` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment_factors`
--

INSERT INTO `equipment_factors` (`id`, `equipment`, `factor`, `description`, `created_at`, `updated_at`) VALUES
(1, 'luxus', 1.20, 'Luxus', '2026-10-06 14:01:39', '2026-10-06 15:17:32'),
(2, 'gehoben', 1.10, 'Gehoben', '2026-10-06 14:01:39', '2026-10-06 15:17:41'),
(3, 'standard', 1.00, 'Standard', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(4, 'einfach', 0.90, 'Einfach', '2026-10-06 14:01:39', '2026-10-06 15:17:52');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `plz` varchar(10) NOT NULL COMMENT 'Postleitzahl',
  `city` varchar(100) NOT NULL COMMENT 'Ortsname',
  `state` varchar(80) NOT NULL COMMENT 'Bezirk / Region',
  `macro_location_factor` decimal(4,2) NOT NULL DEFAULT 1.00 COMMENT 'Macro location factor (PLZ level)',
  `country` varchar(5) NOT NULL DEFAULT 'CH',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `plz`, `city`, `state`, `macro_location_factor`, `country`, `created_at`, `updated_at`) VALUES
(1, '6534', 'San Vittore', 'Moësa', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(2, '6535', 'Roveredo (GR)', 'Moësa', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(3, '6537', 'Grono', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(4, '6538', 'Grono', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(5, '6540', 'Castaneda', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(6, '6541', 'Santa Maria in Calanca', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(7, '6542', 'Buseno', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(8, '6543', 'Calanca', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(9, '6544', 'Calanca', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(10, '6545', 'Calanca', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(11, '6546', 'Calanca', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(12, '6548', 'Rossa', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(13, '6549', 'Roveredo (GR)', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(14, '6556', 'Grono', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(15, '6557', 'Cama', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(16, '6558', 'Lostallo', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(17, '6562', 'Soazza', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(18, '6563', 'Mesocco', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(19, '6565', 'Mesocco', 'Moësa', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(20, '7000', 'Chur', 'Plessur', 1.12, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(21, '7023', 'Haldenstein', 'Plessur', 0.98, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(22, '7026', 'Maladers', 'Plessur', 0.92, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(23, '7027', 'Calfreisen', 'Plessur', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(24, '7028', 'Pagig', 'Plessur', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(25, '7029', 'Peist', 'Plessur', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(26, '7031', 'Laax', 'Surselva', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(27, '7032', 'Laax', 'Surselva', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(28, '7050', 'Arosa', 'Plessur', 1.47, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(29, '7056', 'Molinis', 'Plessur', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(30, '7057', 'Langwies', 'Plessur', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(31, '7058', 'Litzirüti', 'Plessur', 1.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(32, '7062', 'Passugg', 'Plessur', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(33, '7063', 'Praden', 'Plessur', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(34, '7064', 'Tschiertschen', 'Plessur', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(35, '7074', 'Malix', 'Plessur', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:12:19'),
(36, '7075', 'Churwalden', 'Plessur', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 16:59:36'),
(37, '7076', 'Parpan', 'Plessur', 1.15, 'CH', '2026-10-06 14:01:39', '2026-10-06 16:59:47'),
(38, '7077', 'Valbella', 'Albula', 1.65, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(39, '7078', 'Lenzerheide/Lai', 'Albula', 1.82, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(40, '7082', 'Vaz/Obervaz', 'Albula', 1.45, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(41, '7083', 'Lantsch/Lenz', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(42, '7084', 'Brienz/Brinzauls', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(43, '7012', 'Felsberg', 'Imboden', 1.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(44, '7013', 'Domat/Ems', 'Imboden', 1.02, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(45, '7014', 'Trin', 'Imboden', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(46, '7015', 'Tamins', 'Imboden', 1.11, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(47, '7016', 'Trin Mulin', 'Imboden', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(48, '7017', 'Flims Dorf', 'Imboden', 1.42, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(49, '7018', 'Flims Waldhaus', 'Imboden', 1.30, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(50, '7019', 'Fidaz', 'Imboden', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(51, '7104', 'Versam', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(52, '7106', 'Tenna', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(53, '7107', 'Safien Platz', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(54, '7109', 'Thalkirch', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(55, '7110', 'Peiden', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(56, '7111', 'Pitasch', 'Surselva', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(57, '7112', 'Duvin', 'Surselva', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(58, '7113', 'Camuns', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(59, '7114', 'Uors (Lumnezia)', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(60, '7115', 'Surcasti', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(61, '7116', 'St. Martin (Lugnez)', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(62, '7122', 'Valendas', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(63, '7126', 'Castrisch', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(64, '7127', 'Sevgein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(65, '7128', 'Riein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(66, '7130', 'Ilanz', 'Surselva', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(67, '7132', 'Vals', 'Surselva', 1.02, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(68, '7134', 'Obersaxen', 'Surselva', 0.92, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(69, '7137', 'Flond', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(70, '7138', 'Surcuolm', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(71, '7141', 'Luven', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(72, '7142', 'Cumbel', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(73, '7143', 'Morissen', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(74, '7144', 'Vella', 'Surselva', 0.90, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(75, '7145', 'Degen', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(76, '7146', 'Vattiz', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(77, '7147', 'Vignogn', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(78, '7148', 'Lumbrein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(79, '7149', 'Vrin', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(80, '7151', 'Schluein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(81, '7152', 'Sagogn', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(82, '7153', 'Falera', 'Surselva', 1.25, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(83, '7154', 'Ruschein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(84, '7155', 'Ladir', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(85, '7156', 'Rueun', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(86, '7157', 'Siat', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(87, '7158', 'Waltensburg/Vuorz', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(88, '7159', 'Andiast', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(89, '7162', 'Tavanasa', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(90, '7163', 'Danis', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(91, '7164', 'Dardin', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(92, '7165', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(93, '7166', 'Trun', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(94, '7167', 'Zignau', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(95, '7168', 'Schlans', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(96, '7172', 'Rabius', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(97, '7173', 'Surrein', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(98, '7174', 'S.Benedetg', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(99, '7175', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(100, '7176', 'Cumpadials', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(101, '7180', 'Disentis/Mustér', 'Surselva', 0.92, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(102, '7182', 'Cavardiras', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(103, '7183', 'Mompé Medel', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(104, '7184', 'Curaglia', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(105, '7185', 'Platta', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(106, '7186', 'Mompé Tujetsch', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(107, '7187', 'Camischolas', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(108, '7188', 'Sedrun', 'Surselva', 0.95, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(109, '7189', 'Rueras', 'Surselva', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(110, '7202', 'Says', 'Landquart', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(111, '7203', 'Trimmis', 'Landquart', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(112, '7204', 'Untervaz', 'Landquart', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(113, '7205', 'Zizers', 'Landquart', 1.02, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(114, '7206', 'Igis', 'Landquart', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(115, '7208', 'Malans', 'Landquart', 1.12, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(116, '7212', 'Seewis Dorf', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(117, '7213', 'Valzeina', 'Landquart', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(118, '7214', 'Grüsch', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(119, '7215', 'Fanas', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(120, '7220', 'Schiers', 'Prättigau/Davos', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(121, '7222', 'Mittellunden', 'Prättigau/Davos', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(122, '7223', 'Buchen i.Prättigau', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(123, '7224', 'Putz', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(124, '7226', 'Stels', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(125, '7228', 'Schuders', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(126, '7231', 'Pragg-Jenaz', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(127, '7232', 'Furna', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(128, '7233', 'Jenaz', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(129, '7235', 'Fideris', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(130, '7240', 'Küblis', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(131, '7241', 'Conters im Prättigau', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(132, '7242', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(133, '7243', 'Pany', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(134, '7244', 'Gadenstätt', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(135, '7245', 'Ascharina', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(136, '7246', 'St.Antönien', 'Prättigau/Davos', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(137, '7247', 'Saas im Prättigau', 'Prättigau/Davos', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(138, '7249', 'Serneus', 'Prättigau/Davos', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(139, '7250', 'Klosters', 'Prättigau/Davos', 1.50, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(140, '7252', 'Klosters Dorf', 'Prättigau/Davos', 1.45, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(141, '7260', 'Davos Dorf', 'Prättigau/Davos', 1.73, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(142, '7265', 'Davos Wolfgang', 'Prättigau/Davos', 1.45, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(143, '7270', 'Davos Platz', 'Prättigau/Davos', 1.65, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(144, '7272', 'Davos Clavadel', 'Prättigau/Davos', 1.40, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(145, '7276', 'Davos Frauenkirch', 'Prättigau/Davos', 1.38, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(146, '7277', 'Davos Glaris', 'Prättigau/Davos', 1.30, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(147, '7278', 'Davos Monstein', 'Prättigau/Davos', 1.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(148, '7302', 'Landquart', 'Landquart', 0.91, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(149, '7303', 'Mastrils', 'Landquart', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(150, '7304', 'Maienfeld', 'Landquart', 1.12, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(151, '7306', 'Fläsch', 'Landquart', 1.18, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(152, '7307', 'Jenins', 'Landquart', 1.12, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(153, '7402', 'Bonaduz', 'Imboden', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(154, '7403', 'Rhäzüns', 'Imboden', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(155, '7404', 'Feldis/Veulden', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(156, '7405', 'Rothenbrunnen', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(157, '7407', 'Trans', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(158, '7408', 'Cazis', 'Viamala', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(159, '7411', 'Sils im Domleschg', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(160, '7412', 'Scharans', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(161, '7413', 'Fürstenaubruck', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(162, '7414', 'Fürstenau', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(163, '7415', 'Pratval', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(164, '7416', 'Almens', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(165, '7417', 'Paspels', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(166, '7418', 'Tumegl/Tomils', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(167, '7419', 'Scheid', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(168, '7421', 'Summaprada', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(169, '7422', 'Tartar', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(170, '7423', 'Portein', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(171, '7424', 'Dalin', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(172, '7425', 'Masein', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(173, '7426', 'Flerden', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(174, '7427', 'Urmein', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(175, '7428', 'Tschappina', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(176, '7430', 'Thusis', 'Viamala', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(177, '7431', 'Mutten', 'Viamala', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(178, '7432', 'Zillis', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(179, '7433', 'Donat', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(180, '7434', 'Sufers', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(181, '7435', 'Splügen', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(182, '7436', 'Medels im Rheinwald', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(183, '7437', 'Nufenen', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(184, '7438', 'Hinterrhein', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(185, '7440', 'Andeer', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(186, '7442', 'Clugin', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(187, '7443', 'Pignia', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(188, '7444', 'Ausserferrera', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(189, '7445', 'Innerferrera', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(190, '7446', 'Campsut-Cröt', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(191, '7447', 'Cresta (Avers)', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(192, '7448', 'Juf', 'Viamala', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(193, '7450', 'Tiefencastel', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(194, '7451', 'Alvaschein', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(195, '7452', 'Cunter', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(196, '7453', 'Tinizong', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(197, '7454', 'Rona', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(198, '7455', 'Mulegns', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(199, '7456', 'Sur', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(200, '7457', 'Bivio', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(201, '7458', 'Mon', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(202, '7459', 'Stierva', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(203, '7460', 'Savognin', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(204, '7462', 'Salouf', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(205, '7463', 'Riom', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(206, '7464', 'Parsonz', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(207, '7472', 'Surava', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(208, '7473', 'Alvaneu Bad', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(209, '7477', 'Filisur', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(210, '7482', 'Bergün/Bravuogn', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(211, '7484', 'Latsch', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(212, '7492', 'Alvaneu Dorf', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(213, '7493', 'Schmitten (Albula)', 'Albula', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(214, '7494', 'Wiesen', 'Albula', 1.20, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(215, '7500', 'St. Moritz', 'Maloja', 2.35, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(216, '7502', 'Bever', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(217, '7503', 'Samedan', 'Maloja', 1.87, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(218, '7504', 'Pontresina', 'Maloja', 1.82, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(219, '7505', 'Celerina/Schlarigna', 'Maloja', 1.70, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(220, '7512', 'Champfèr', 'Maloja', 2.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(221, '7513', 'Silvaplana', 'Maloja', 1.85, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(222, '7514', 'Sils/Segl Maria', 'Maloja', 1.72, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(223, '7515', 'Sils/Segl Baselgia', 'Maloja', 1.65, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(224, '7516', 'Maloja', 'Maloja', 1.55, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(225, '7517', 'Plaun da Lej', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(226, '7522', 'La Punt-Chamues-ch', 'Maloja', 1.35, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(227, '7523', 'Madulain', 'Maloja', 1.35, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(228, '7524', 'Zuoz', 'Maloja', 1.45, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(229, '7525', 'S-chanf', 'Maloja', 1.20, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(230, '7526', 'Cinuos-chel', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(231, '7527', 'Brail', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(232, '7530', 'Zernez', 'Region Engiadina Bassa/Val Müstair', 1.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(233, '7532', 'Tschierv', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(234, '7533', 'Fuldera', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(235, '7534', 'Lü', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(236, '7535', 'Valchava', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(237, '7536', 'Sta. Maria Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(238, '7537', 'Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(239, '7542', 'Susch', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(240, '7543', 'Lavin', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(241, '7545', 'Guarda', 'Region Engiadina Bassa/Val Müstair', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(242, '7546', 'Ardez', 'Region Engiadina Bassa/Val Müstair', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(243, '7550', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.25, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(244, '7551', 'Ftan', 'Region Engiadina Bassa/Val Müstair', 1.25, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(245, '7552', 'Vulpera', 'Region Engiadina Bassa/Val Müstair', 1.25, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(246, '7553', 'Tarasp', 'Region Engiadina Bassa/Val Müstair', 1.20, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(247, '7554', 'Sent', 'Region Engiadina Bassa/Val Müstair', 1.18, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(248, '7556', 'Ramosch', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(249, '7557', 'Vnà', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(250, '7558', 'Strada', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(251, '7559', 'Tschlin', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(252, '7560', 'Martina', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(253, '7562', 'Samnaun-Compatsch', 'Region Engiadina Bassa/Val Müstair', 1.05, 'CH', '2026-10-06 14:01:39', '2026-10-06 15:08:28'),
(254, '7563', 'Samnaun Dorf', 'Region Engiadina Bassa/Val Müstair', 1.08, 'CH', '2026-10-06 14:01:39', '2026-10-06 17:09:19'),
(255, '7602', 'Casaccia', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(256, '7603', 'Vicosoprano', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(257, '7604', 'Borgonovo', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(258, '7605', 'Stampa', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(259, '7606', 'Promontogno', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(260, '7608', 'Castasegna', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(261, '7610', 'Soglio', 'Maloja', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(262, '7710', 'Ospizio Bernina', 'Bernina', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(263, '7741', 'S. Carlo (Poschiavo)', 'Bernina', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(264, '7742', 'Poschiavo', 'Bernina', 1.10, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(265, '7743', 'Brusio', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(266, '7744', 'Campocologno', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(267, '7745', 'Li Curt', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(268, '7746', 'Le Prese', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(269, '7747', 'Viano', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39'),
(270, '7748', 'Campascio', 'Bernina', 1.00, 'CH', '2026-10-06 14:01:39', '2026-10-06 14:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `residence_status_factors`
--

CREATE TABLE `residence_status_factors` (
  `id` int(11) NOT NULL,
  `status` varchar(30) NOT NULL,
  `trend_threshold` decimal(3,2) DEFAULT 0.00,
  `factor` decimal(4,3) NOT NULL,
  `description` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residence_status_factors`
--

INSERT INTO `residence_status_factors` (`id`, `status`, `trend_threshold`, `factor`, `description`) VALUES
(1, 'erstwohnsitz', 0.00, 1.000, 'Erstwohnsitz: Baseline (100% des Basiswerts)'),
(2, 'zweitwohnsitz', 0.00, 1.180, 'Zweitwohnsitz: Standardgebiet (+18% wegen Knappheit)'),
(3, 'zweitwohnsitz', 1.01, 1.250, 'Zweitwohnsitz: Begehrte Tourismusregion (+25% wegen Lex Weber Knappheit)'),
(4, 'zweitwohnsitz_privilegiert', 0.00, 1.200, 'Privilegierte Zweitwohnung (Altrecht): Standardgebiet (+20%)'),
(5, 'zweitwohnsitz_privilegiert', 1.01, 1.250, 'Privilegierte Zweitwohnung (Altrecht): Tourismusregion (+25%)'),
(6, 'feriendomizil', 0.00, 1.000, 'Feriendomizil: Normalgebiet (kein Aufschlag)'),
(7, 'feriendomizil', 1.01, 1.150, 'Feriendomizil: Begehrte Tourismusregion (+15%)');

-- --------------------------------------------------------

--
-- Table structure for table `valuations`
--

CREATE TABLE `valuations` (
  `id` int(11) NOT NULL,
  `plz` varchar(10) NOT NULL,
  `location_name` varchar(100) DEFAULT NULL,
  `country` varchar(5) DEFAULT 'CH',
  `property_type` varchar(50) NOT NULL,
  `area` decimal(10,2) NOT NULL,
  `plot_area_m2` decimal(10,2) DEFAULT NULL COMMENT 'Grundstücksfläche in m² (für Einfamilienhaus)',
  `property_condition` varchar(30) NOT NULL,
  `equipment` varchar(30) NOT NULL,
  `micro_location` varchar(30) DEFAULT NULL COMMENT 'Micro-Lage: top, good, standard, peripheral',
  `development_potential` varchar(30) DEFAULT NULL COMMENT 'Ausnützungsreserve: none, minor, major',
  `price_per_sqm` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(14,2) NOT NULL DEFAULT 0.00,
  `location_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `micro_location_factor` decimal(4,3) NOT NULL DEFAULT 1.000 COMMENT 'Micro-location quality factor',
  `condition_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `equipment_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `residence_status` varchar(30) DEFAULT 'erstwohnsitz',
  `residence_status_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `development_potential_factor` decimal(4,3) NOT NULL DEFAULT 1.000 COMMENT 'Ausnützungsreserve Faktor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `valuations`
--

INSERT INTO `valuations` (`id`, `plz`, `location_name`, `country`, `property_type`, `area`, `plot_area_m2`, `property_condition`, `equipment`, `micro_location`, `development_potential`, `price_per_sqm`, `total_value`, `location_factor`, `micro_location_factor`, `condition_factor`, `equipment_factor`, `residence_status`, `residence_status_factor`, `development_potential_factor`, `created_at`, `updated_at`) VALUES
(1, '7000', 'Chur', 'CH', 'Einfamilienhaus', 180.00, 500.00, 'renoviert', 'gehoben', 'good', 'minor', 11700.00, 2106000.00, 1.900, 1.000, 1.100, 1.150, 'erstwohnsitz', 1.000, 1.080, '2025-01-15 13:30:00', '2025-01-15 13:30:00'),
(2, '7260', 'Davos', 'CH', 'Wohnung', 95.50, NULL, 'neuwertig', 'luxus', 'top', NULL, 14250.00, 1360875.00, 1.600, 1.200, 1.200, 1.300, 'zweitwohnsitz', 1.250, 1.000, '2025-02-20 09:15:00', '2025-02-20 09:15:00'),
(3, '7500', 'St. Moritz', 'CH', 'Einfamilienhaus', 250.00, 800.00, 'renoviert', 'luxus', 'top', 'major', 16500.00, 4125000.00, 1.600, 1.250, 1.100, 1.300, 'zweitwohnsitz', 1.250, 1.200, '2025-03-10 08:00:00', '2025-03-10 08:00:00'),
(4, '7031', 'Laax', 'CH', 'Mehrfamilienhaus', 320.00, NULL, 'gepflegt', 'standard', 'good', NULL, 8500.00, 2720000.00, 1.400, 1.000, 1.000, 1.000, 'feriendomizil', 1.150, 1.000, '2025-04-05 14:45:00', '2025-04-05 14:45:00'),
(5, '7111', 'Ilanz/Glion', 'CH', 'Reihenhaus', 140.00, 350.00, 'gepflegt', 'standard', 'standard', 'none', 6200.00, 868000.00, 1.100, 1.000, 1.000, 1.000, 'erstwohnsitz', 1.000, 1.000, '2025-05-18 09:20:00', '2025-05-18 09:20:00'),
(6, '7074', 'Malix', 'CH', 'Wohnung', 120.00, 0.00, 'neuwertig', 'gehoben', 'good', 'none', 13701.47, 1644176.16, 1.000, 1.100, 1.200, 1.150, 'erstwohnsitz', 1.000, 1.000, '2026-10-06 14:43:55', '2026-10-06 14:43:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `base_prices`
--
ALTER TABLE `base_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_property_type` (`property_type`);

--
-- Indexes for table `condition_factors`
--
ALTER TABLE `condition_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_condition` (`property_condition`);

--
-- Indexes for table `development_potential_factors`
--
ALTER TABLE `development_potential_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_reserve_type` (`reserve_type`);

--
-- Indexes for table `equipment_factors`
--
ALTER TABLE `equipment_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_equipment` (`equipment`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_plz` (`plz`),
  ADD KEY `idx_plz` (`plz`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_macro_location` (`macro_location_factor`);

--
-- Indexes for table `residence_status_factors`
--
ALTER TABLE `residence_status_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_trend` (`status`,`trend_threshold`);

--
-- Indexes for table `valuations`
--
ALTER TABLE `valuations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plz` (`plz`),
  ADD KEY `idx_location` (`location_name`),
  ADD KEY `idx_property_type` (`property_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_micro_location` (`micro_location`),
  ADD KEY `idx_development_potential` (`development_potential`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `base_prices`
--
ALTER TABLE `base_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `condition_factors`
--
ALTER TABLE `condition_factors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `development_potential_factors`
--
ALTER TABLE `development_potential_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment_factors`
--
ALTER TABLE `equipment_factors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `residence_status_factors`
--
ALTER TABLE `residence_status_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `valuations`
--
ALTER TABLE `valuations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
