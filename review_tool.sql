-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 11:25 AM
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
-- Database: `review_tool`
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
(1, 'Einfamilienhaus', 8188.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(2, 'Mehrfamilienhaus', 6772.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(3, 'Wohnung', 9026.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(4, 'Reihenhaus', 7400.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(5, 'Doppelhaus', 7900.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(6, 'Grundstück', 1000.00, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(7, 'Landwirtschaftlich', 0.10, 'CHF', 'm²', '2026-07-06 15:12:48', '2026-07-06 15:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `condition_factors`
--

CREATE TABLE `condition_factors` (
  `id` int(10) UNSIGNED NOT NULL,
  `condition` varchar(30) NOT NULL COMMENT 'Zustand',
  `factor` decimal(4,2) NOT NULL COMMENT 'Faktor',
  `description` varchar(100) DEFAULT NULL COMMENT 'Beschreibung',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `condition_factors`
--

INSERT INTO `condition_factors` (`id`, `condition`, `factor`, `description`, `created_at`, `updated_at`) VALUES
(1, 'neuwertig', 1.20, 'Neuwertig', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(2, 'renoviert', 1.10, 'Renoviert', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(3, 'gepflegt', 1.00, 'Gepflegt', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(4, 'sanierungsbedürftig', 0.85, 'Sanierungsbedürftig', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(5, 'renierungsbedürftig', 0.70, 'Renierungsbedürftig', '2026-07-06 15:12:48', '2026-07-06 15:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_factors`
--

CREATE TABLE `equipment_factors` (
  `id` int(10) UNSIGNED NOT NULL,
  `equipment` varchar(20) NOT NULL COMMENT 'Ausstattung',
  `factor` decimal(4,2) NOT NULL COMMENT 'Faktor',
  `description` varchar(100) DEFAULT NULL COMMENT 'Beschreibung',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment_factors`
--

INSERT INTO `equipment_factors` (`id`, `equipment`, `factor`, `description`, `created_at`, `updated_at`) VALUES
(1, 'luxus', 1.30, 'Luxus', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(2, 'gehoben', 1.15, 'Gehoben', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(3, 'standard', 1.00, 'Standard', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(4, 'einfach', 0.85, 'Einfach', '2026-07-06 15:12:48', '2026-07-06 15:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `plz` varchar(10) NOT NULL COMMENT 'Postleitzahl',
  `city` varchar(100) NOT NULL COMMENT 'Ortsname',
  `state` varchar(80) NOT NULL COMMENT 'Bezirk / Region',
  `trend_factor` decimal(4,2) NOT NULL DEFAULT 1.00 COMMENT 'Standorttrend-Faktor',
  `country` varchar(5) NOT NULL DEFAULT 'CH' COMMENT 'Land',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `plz`, `city`, `state`, `trend_factor`, `country`, `created_at`, `updated_at`) VALUES
(1, '6534', 'San Vittore', 'Moësa', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(2, '6535', 'Roveredo (GR)', 'Moësa', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(3, '6537', 'Grono', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(4, '6538', 'Grono', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(5, '6540', 'Castaneda', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(6, '6541', 'Santa Maria in Calanca', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(7, '6542', 'Buseno', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(8, '6543', 'Calanca', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(9, '6544', 'Calanca', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(10, '6545', 'Calanca', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(11, '6546', 'Calanca', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(12, '6548', 'Rossa', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(13, '6549', 'Roveredo (GR)', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(14, '6556', 'Grono', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(15, '6557', 'Cama', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(16, '6558', 'Lostallo', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(17, '6562', 'Soazza', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(18, '6563', 'Mesocco', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(19, '6565', 'Mesocco', 'Moësa', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(20, '7000', 'Chur', 'Plessur', 1.50, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:16:58'),
(21, '7023', 'Haldenstein', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(22, '7026', 'Maladers', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(23, '7027', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(24, '7028', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(25, '7029', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(26, '7031', 'Laax', 'Surselva', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:07:06'),
(27, '7032', 'Laax', 'Surselva', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:07:11'),
(28, '7050', 'Arosa', 'Plessur', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:06:45'),
(29, '7056', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(30, '7057', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(31, '7058', 'Arosa', 'Plessur', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(32, '7062', 'Churwalden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(33, '7063', 'Tschiertschen-Praden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(34, '7064', 'Tschiertschen-Praden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(35, '7074', 'Churwalden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(36, '7075', 'Churwalden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(37, '7076', 'Churwalden', 'Plessur', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(38, '7077', 'Vaz/Obervaz', 'Albula', 1.30, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:05:02'),
(39, '7078', 'Vaz/Obervaz', 'Albula', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:05:15'),
(40, '7082', 'Vaz/Obervaz', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(41, '7083', 'Lantsch/Lenz', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(42, '7084', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(43, '7012', 'Felsberg', 'Imboden', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(44, '7013', 'Domat/Ems', 'Imboden', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(45, '7014', 'Trin', 'Imboden', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(46, '7015', 'Tamins', 'Imboden', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(47, '7016', 'Trin', 'Imboden', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(48, '7017', 'Flims', 'Imboden', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:10:37'),
(49, '7018', 'Flims', 'Imboden', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(50, '7019', 'Flims', 'Imboden', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(51, '7104', 'Safiental', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(52, '7106', 'Safiental', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(53, '7107', 'Safiental', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(54, '7109', 'Safiental', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(55, '7110', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(56, '7111', 'Ilanz/Glion', 'Surselva', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(57, '7112', 'Ilanz/Glion', 'Surselva', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(58, '7113', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(59, '7114', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(60, '7115', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(61, '7116', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(62, '7122', 'Safiental', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(63, '7126', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(64, '7127', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(65, '7128', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(66, '7130', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(67, '7132', 'Vals', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(68, '7134', 'Obersaxen Mundaun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(69, '7137', 'Obersaxen Mundaun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(70, '7138', 'Obersaxen Mundaun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(71, '7141', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(72, '7142', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(73, '7143', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(74, '7144', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(75, '7145', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(76, '7146', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(77, '7147', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(78, '7148', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(79, '7149', 'Lumnezia', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(80, '7151', 'Schluein', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(81, '7152', 'Sagogn', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(82, '7153', 'Falera', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(83, '7154', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(84, '7155', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(85, '7156', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(86, '7157', 'Ilanz/Glion', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(87, '7158', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(88, '7159', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(89, '7162', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(90, '7163', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(91, '7164', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(92, '7165', 'Breil/Brigels', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(93, '7166', 'Trun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(94, '7167', 'Trun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(95, '7168', 'Trun', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(96, '7172', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(97, '7173', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(98, '7174', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(99, '7175', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(100, '7176', 'Sumvitg', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(101, '7180', 'Disentis/Mustér', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(102, '7182', 'Disentis/Mustér', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(103, '7183', 'Disentis/Mustér', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(104, '7184', 'Medel (Lucmagn)', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(105, '7185', 'Medel (Lucmagn)', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(106, '7186', 'Disentis/Mustér', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(107, '7187', 'Tujetsch', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(108, '7188', 'Tujetsch', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(109, '7189', 'Tujetsch', 'Surselva', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(110, '7202', 'Trimmis', 'Landquart', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(111, '7203', 'Trimmis', 'Landquart', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(112, '7204', 'Untervaz', 'Landquart', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(113, '7205', 'Zizers', 'Landquart', 1.30, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:19:30'),
(114, '7206', 'Landquart', 'Landquart', 1.10, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:20:58'),
(115, '7208', 'Malans', 'Landquart', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(116, '7212', 'Seewis im Prättigau', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(117, '7213', 'Grüsch', 'Landquart', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(118, '7214', 'Grüsch', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(119, '7215', 'Grüsch', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(120, '7220', 'Schiers', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(121, '7222', 'Schiers', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(122, '7223', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(123, '7224', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(124, '7226', 'Schiers', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(125, '7228', 'Schiers', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(126, '7231', 'Jenaz', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(127, '7232', 'Furna', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(128, '7233', 'Jenaz', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(129, '7235', 'Fideris', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(130, '7240', 'Küblis', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(131, '7241', 'Conters im Prättigau', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(132, '7242', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(133, '7243', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(134, '7244', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(135, '7245', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(136, '7246', 'Luzein', 'Prättigau/Davos', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(137, '7247', 'Klosters-Serneus', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(138, '7249', 'Klosters-Serneus', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(139, '7250', 'Klosters-Serneus', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(140, '7252', 'Klosters-Serneus', 'Prättigau/Davos', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(141, '7260', 'Davos', 'Prättigau/Davos', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:18:57'),
(142, '7265', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:17:34'),
(143, '7270', 'Davos', 'Prättigau/Davos', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:17:18'),
(144, '7272', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:17:44'),
(145, '7276', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:17:54'),
(146, '7277', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:18:01'),
(147, '7278', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:18:06'),
(148, '7302', 'Landquart', 'Landquart', 1.40, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:20:44'),
(149, '7303', 'Landquart', 'Landquart', 1.10, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:21:13'),
(150, '7304', 'Maienfeld', 'Landquart', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(151, '7306', 'Fläsch', 'Landquart', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(152, '7307', 'Jenins', 'Landquart', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(153, '7402', 'Bonaduz', 'Imboden', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(154, '7403', 'Rhäzüns', 'Imboden', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(155, '7404', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(156, '7405', 'Rothenbrunnen', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(157, '7407', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(158, '7408', 'Cazis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(159, '7411', 'Sils im Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(160, '7412', 'Scharans', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(161, '7413', 'Fürstenau', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(162, '7414', 'Fürstenau', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(163, '7415', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(164, '7416', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(165, '7417', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(166, '7418', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(167, '7419', 'Domleschg', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(168, '7421', 'Cazis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(169, '7422', 'Cazis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(170, '7423', 'Cazis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(171, '7424', 'Cazis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(172, '7425', 'Masein', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(173, '7426', 'Flerden', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(174, '7427', 'Urmein', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(175, '7428', 'Tschappina', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(176, '7430', 'Thusis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(177, '7431', 'Thusis', 'Viamala', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(178, '7432', 'Zillis-Reischen', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(179, '7433', 'Donat', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(180, '7434', 'Sufers', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(181, '7435', 'Rheinwald', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(182, '7436', 'Rheinwald', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(183, '7437', 'Rheinwald', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(184, '7438', 'Rheinwald', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(185, '7440', 'Andeer', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(186, '7442', 'Andeer', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(187, '7443', 'Andeer', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(188, '7444', 'Ferrera', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(189, '7445', 'Ferrera', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(190, '7446', 'Avers', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(191, '7447', 'Avers', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(192, '7448', 'Avers', 'Viamala', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(193, '7450', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(194, '7451', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(195, '7452', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(196, '7453', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(197, '7454', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(198, '7455', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(199, '7456', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(200, '7457', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(201, '7458', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(202, '7459', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(203, '7460', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(204, '7462', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(205, '7463', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(206, '7464', 'Surses', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(207, '7472', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(208, '7473', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(209, '7477', 'Bergün Filisur', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(210, '7482', 'Bergün Filisur', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(211, '7484', 'Bergün Filisur', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(212, '7492', 'Albula/Alvra', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(213, '7493', 'Schmitten (GR)', 'Albula', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(214, '7494', 'Davos', 'Prättigau/Davos', 1.20, 'CH', '2026-07-06 15:12:48', '2026-08-18 09:18:13'),
(215, '7500', 'St. Moritz', 'Maloja', 1.60, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(216, '7502', 'Bever', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(217, '7503', 'Samedan', 'Maloja', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(218, '7504', 'Pontresina', 'Maloja', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(219, '7505', 'Celerina/Schlarigna', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(220, '7512', 'Silvaplana', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(221, '7513', 'Silvaplana', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(222, '7514', 'Sils im Engadin/Segl', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(223, '7515', 'Sils im Engadin/Segl', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(224, '7516', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(225, '7517', 'Sils im Engadin/Segl', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(226, '7522', 'La Punt-Chamues-ch', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(227, '7523', 'Madulain', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(228, '7524', 'Zuoz', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(229, '7525', 'S-chanf', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(230, '7526', 'S-chanf', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(231, '7527', 'Zernez', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(232, '7530', 'Zernez', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(233, '7532', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(234, '7533', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(235, '7534', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(236, '7535', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(237, '7536', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(238, '7537', 'Val Müstair', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(239, '7542', 'Zernez', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(240, '7543', 'Zernez', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(241, '7545', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(242, '7546', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(243, '7550', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(244, '7551', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(245, '7552', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(246, '7553', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(247, '7554', 'Scuol', 'Region Engiadina Bassa/Val Müstair', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(248, '7556', 'Valsot', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(249, '7557', 'Valsot', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(250, '7558', 'Valsot', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(251, '7559', 'Valsot', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(252, '7560', 'Valsot', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(253, '7562', 'Samnaun', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(254, '7563', 'Samnaun', 'Region Engiadina Bassa/Val Müstair', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(255, '7602', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(256, '7603', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(257, '7604', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(258, '7605', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(259, '7606', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(260, '7608', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(261, '7610', 'Bregaglia', 'Maloja', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(262, '7710', 'Poschiavo', 'Bernina', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(263, '7741', 'Poschiavo', 'Bernina', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(264, '7742', 'Poschiavo', 'Bernina', 1.10, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(265, '7743', 'Brusio', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(266, '7744', 'Brusio', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(267, '7745', 'Poschiavo', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(268, '7746', 'Poschiavo', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(269, '7747', 'Brusio', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48'),
(270, '7748', 'Brusio', 'Bernina', 1.00, 'CH', '2026-07-06 15:12:48', '2026-07-06 15:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `residence_status_factors`
--

CREATE TABLE `residence_status_factors` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `trend_threshold` decimal(3,2) DEFAULT 0.00,
  `factor` decimal(4,3) NOT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residence_status_factors`
--

INSERT INTO `residence_status_factors` (`id`, `status`, `trend_threshold`, `factor`, `description`) VALUES
(1, 'erstwohnsitz', 0.00, 0.800, 'Erstwohnsitz: Markt-Restriktionen'),
(2, 'feriendomizil', 1.00, 1.000, 'Feriendomizil: Normalgebiet'),
(3, 'feriendomizil', 1.01, 1.400, 'Feriendomizil: Begehrte Tourismusregion');

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
  `condition` varchar(30) NOT NULL,
  `equipment` varchar(30) NOT NULL,
  `price_per_sqm` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(14,2) NOT NULL DEFAULT 0.00,
  `location_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `condition_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `equipment_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `residence_status` varchar(20) DEFAULT 'erstwohnsitz',
  `residence_status_factor` decimal(4,3) NOT NULL DEFAULT 1.000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `valuations`
--

INSERT INTO `valuations` (`id`, `plz`, `location_name`, `country`, `property_type`, `area`, `condition`, `equipment`, `price_per_sqm`, `total_value`, `location_factor`, `condition_factor`, `equipment_factor`, `residence_status`, `residence_status_factor`, `created_at`, `updated_at`) VALUES
(1, '7000', 'Chur', 'CH', 'Einfamilienhaus', 180.00, 'renoviert', 'gehoben', 11700.00, 2106000.00, 1.900, 1.100, 1.150, 'erstwohnsitz', 1.000, '2025-01-15 13:30:00', '2025-01-15 13:30:00'),
(2, '7260', 'Davos', 'CH', 'Wohnung', 95.50, 'neuwertig', ' luxus', 14250.00, 1360875.00, 1.600, 1.200, 1.300, 'feriendomizil', 1.300, '2025-02-20 09:15:00', '2025-02-20 09:15:00'),
(3, '7500', 'St. Moritz', 'CH', 'Einfamilienhaus', 250.00, 'renoviert', ' luxus', 16500.00, 4125000.00, 1.600, 1.100, 1.300, 'feriendomizil', 1.300, '2025-03-10 08:00:00', '2025-03-10 08:00:00'),
(4, '7031', 'Laax', 'CH', 'Mehrfamilienhaus', 320.00, 'gepflegt', 'standard', 8500.00, 2720000.00, 1.100, 1.000, 1.000, 'feriendomizil', 1.300, '2025-04-05 14:45:00', '2025-04-05 14:45:00'),
(5, '7111', 'Ilanz/Glion', 'CH', 'Reihenhaus', 140.00, 'gepflegt', 'standard', 6200.00, 868000.00, 1.100, 1.000, 1.000, 'erstwohnsitz', 1.000, '2025-05-18 09:20:00', '2025-05-18 09:20:00');

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
  ADD UNIQUE KEY `uk_condition` (`condition`);

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
  ADD KEY `idx_country` (`country`);

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
  ADD KEY `idx_created_at` (`created_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `valuations`
--
ALTER TABLE `valuations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
