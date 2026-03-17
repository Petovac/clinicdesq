-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 12, 2026 at 11:04 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinicdesq`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `vet_id` bigint UNSIGNED DEFAULT NULL,
  `pet_parent_id` bigint UNSIGNED DEFAULT NULL,
  `pet_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `pet_age_at_visit` int DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `checked_in_at` datetime DEFAULT NULL,
  `consultation_started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `appointment_number` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `clinic_id`, `vet_id`, `pet_parent_id`, `pet_id`, `created_by`, `weight`, `pet_age_at_visit`, `scheduled_at`, `status`, `checked_in_at`, `consultation_started_at`, `completed_at`, `created_at`, `updated_at`, `appointment_number`) VALUES
(2, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-20 21:40:00', 'completed', NULL, NULL, NULL, '2026-02-20 10:40:52', '2026-02-20 12:40:44', NULL),
(3, 5, 9, 1, 3, NULL, NULL, NULL, '2026-02-20 12:20:00', 'completed', NULL, NULL, NULL, '2026-02-20 11:57:16', '2026-02-20 13:43:21', NULL),
(4, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-24 00:45:00', 'completed', NULL, NULL, NULL, '2026-02-20 13:45:31', '2026-02-21 01:41:14', NULL),
(5, 5, 9, 1, 4, NULL, NULL, NULL, '2026-02-27 17:28:00', 'scheduled', NULL, NULL, NULL, '2026-02-21 06:28:51', '2026-02-21 06:28:51', NULL),
(6, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-23 05:32:00', 'completed', NULL, NULL, NULL, '2026-02-21 18:32:53', '2026-03-04 10:44:00', NULL),
(7, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-24 20:05:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 09:05:39', '2026-02-23 09:05:39', NULL),
(8, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-25 20:07:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 09:07:38', '2026-02-23 09:07:38', NULL),
(9, 5, 9, 1, 2, NULL, NULL, NULL, '2026-02-26 20:20:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 09:20:23', '2026-02-23 09:20:23', NULL),
(10, 5, 9, 1, 2, NULL, '20.00', NULL, '2026-02-25 22:45:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 09:45:48', '2026-02-23 09:45:48', NULL),
(11, 5, 9, 1, 2, NULL, '20.00', NULL, '2026-02-28 23:36:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 12:36:07', '2026-02-23 12:36:07', NULL),
(12, 5, 9, 1, 7, NULL, '30.00', 3, '2026-02-26 23:36:00', 'scheduled', NULL, NULL, NULL, '2026-02-23 12:37:01', '2026-02-23 12:37:01', NULL),
(13, 5, 9, 6, 8, NULL, '24.00', 2, '2026-02-27 00:19:00', 'completed', NULL, NULL, NULL, '2026-02-23 13:19:55', '2026-02-24 15:06:47', NULL),
(14, 5, 9, 6, 9, NULL, '34.00', 7, '2026-02-27 00:32:00', 'completed', NULL, NULL, NULL, '2026-02-23 13:32:59', '2026-02-24 10:44:41', NULL),
(15, 5, 9, 6, 9, NULL, '34.00', 7, '2026-02-26 21:52:00', 'scheduled', NULL, NULL, NULL, '2026-02-24 10:52:28', '2026-02-24 10:52:28', NULL),
(16, 5, 9, 6, 10, NULL, '40.00', 5, '2026-02-26 13:26:00', 'completed', NULL, NULL, NULL, '2026-02-26 02:26:16', '2026-02-26 13:39:04', NULL),
(17, 5, 9, 6, 10, NULL, '40.00', 5, '2026-02-28 00:39:00', 'completed', NULL, NULL, NULL, '2026-02-26 13:39:46', '2026-02-28 19:52:15', NULL),
(18, 5, 9, 6, 8, NULL, '32.00', 2, '2026-03-18 14:25:00', 'checked_in', NULL, NULL, NULL, '2026-03-01 03:25:26', '2026-03-07 16:10:44', NULL),
(19, 5, 9, 6, 8, NULL, '30.00', NULL, '2026-03-09 02:14:00', 'cancelled', NULL, NULL, NULL, '2026-03-07 15:14:55', '2026-03-07 16:10:24', NULL),
(20, 5, 9, 6, 9, NULL, '25.00', NULL, '2026-03-08 02:19:00', 'in_consultation', NULL, NULL, NULL, '2026-03-07 15:19:23', '2026-03-07 15:32:05', NULL),
(21, 5, 9, 6, 9, NULL, '30.00', NULL, '2026-03-08 13:53:00', 'completed', '2026-03-08 08:23:14', '2026-03-08 09:03:34', NULL, '2026-03-08 02:53:11', '2026-03-08 10:24:24', NULL),
(22, 5, 9, 6, 9, NULL, '30.00', NULL, '2026-03-10 21:06:00', 'completed', '2026-03-08 15:36:31', '2026-03-08 15:56:05', NULL, '2026-03-08 10:06:19', '2026-03-08 10:26:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_treatments`
--

DROP TABLE IF EXISTS `appointment_treatments`;
CREATE TABLE `appointment_treatments` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_id` bigint UNSIGNED NOT NULL,
  `drug_generic_id` bigint UNSIGNED DEFAULT NULL,
  `drug_brand_id` bigint UNSIGNED DEFAULT NULL,
  `dose_mg` decimal(10,3) DEFAULT NULL,
  `dose_volume_ml` decimal(10,3) DEFAULT NULL,
  `billing_quantity` decimal(10,3) DEFAULT NULL,
  `route` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_list_item_id` bigint UNSIGNED NOT NULL,
  `quantity` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointment_treatments`
--

INSERT INTO `appointment_treatments` (`id`, `appointment_id`, `drug_generic_id`, `drug_brand_id`, `dose_mg`, `dose_volume_ml`, `billing_quantity`, `route`, `price_list_item_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 6, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-04 10:21:04', '2026-03-04 10:21:04'),
(2, 6, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, '2026-03-04 10:21:11', '2026-03-04 10:21:11'),
(3, 5, NULL, NULL, NULL, NULL, '1.000', NULL, 22, 1, '2026-03-11 03:52:26', '2026-03-11 03:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
CREATE TABLE `bills` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `payment_status` enum('pending','paid','partial','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `appointment_id`, `clinic_id`, `created_by`, `total_amount`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2000.00', 'pending', NULL, NULL),
(2, 1, 1, 1, '1000.00', 'pending', NULL, NULL),
(3, 1, 1, 1, '249.00', 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

DROP TABLE IF EXISTS `bill_items`;
CREATE TABLE `bill_items` (
  `id` bigint UNSIGNED NOT NULL,
  `bill_id` bigint UNSIGNED NOT NULL,
  `price_list_item_id` bigint UNSIGNED NOT NULL,
  `quantity` int DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`id`, `bill_id`, `price_list_item_id`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '699.00', '699.00', NULL, NULL),
(2, 1, 3, 1, '249.00', '249.00', NULL, NULL),
(3, 1, 3, 1, '249.00', '249.00', NULL, NULL);

--
-- Triggers `bill_items`
--
DROP TRIGGER IF EXISTS `deduct_inventory_after_bill_item`;
DELIMITER $$
CREATE TRIGGER `deduct_inventory_after_bill_item` AFTER INSERT ON `bill_items` FOR EACH ROW BEGIN

DECLARE inv_id BIGINT;
DECLARE clinic BIGINT;

SELECT inventory_item_id INTO inv_id
FROM price_list_items
WHERE id = NEW.price_list_item_id;

SELECT clinic_id INTO clinic
FROM bills
WHERE id = NEW.bill_id;

IF inv_id IS NOT NULL THEN

UPDATE clinic_inventory
SET stock = stock - NEW.quantity
WHERE clinic_id = clinic
AND inventory_item_id = inv_id;

INSERT INTO inventory_movements
(clinic_id, inventory_item_id, quantity, movement_type, reference_id)
VALUES
(clinic, inv_id, -NEW.quantity, 'sale', NEW.bill_id);

END IF;

END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `prevent_negative_inventory`;
DELIMITER $$
CREATE TRIGGER `prevent_negative_inventory` BEFORE INSERT ON `bill_items` FOR EACH ROW BEGIN

DECLARE inv_id BIGINT;
DECLARE clinic BIGINT;
DECLARE current_stock INT;

-- Get inventory item linked to price item
SELECT inventory_item_id INTO inv_id
FROM price_list_items
WHERE id = NEW.price_list_item_id;

-- Get clinic from bill
SELECT clinic_id INTO clinic
FROM bills
WHERE id = NEW.bill_id;

-- If the item is a medicine/product
IF inv_id IS NOT NULL THEN

SELECT stock INTO current_stock
FROM clinic_inventory
WHERE clinic_id = clinic
AND inventory_item_id = inv_id;

IF current_stock < NEW.quantity THEN
SIGNAL SQLSTATE '45000'
SET MESSAGE_TEXT = 'Insufficient stock for this item';
END IF;

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_sheets`
--

DROP TABLE IF EXISTS `case_sheets`;
CREATE TABLE `case_sheets` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_id` bigint UNSIGNED NOT NULL,
  `presenting_complaint` text COLLATE utf8mb4_unicode_ci,
  `history` text COLLATE utf8mb4_unicode_ci,
  `clinical_examination` text COLLATE utf8mb4_unicode_ci,
  `differentials` text COLLATE utf8mb4_unicode_ci,
  `diagnosis` text COLLATE utf8mb4_unicode_ci,
  `treatment_given` text COLLATE utf8mb4_unicode_ci,
  `procedures_done` text COLLATE utf8mb4_unicode_ci,
  `further_plan` text COLLATE utf8mb4_unicode_ci,
  `advice` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `case_sheets`
--

INSERT INTO `case_sheets` (`id`, `appointment_id`, `presenting_complaint`, `history`, `clinical_examination`, `differentials`, `diagnosis`, `treatment_given`, `procedures_done`, `further_plan`, `advice`, `created_at`, `updated_at`) VALUES
(2, 2, 'wrfgkubk', 'uhl', 'iug', 'jybg', 'yb', 'kjyb', 'kjyb', 'kjy', 'bvfvihrgkuherg', '2026-02-20 11:42:22', '2026-02-20 11:42:22'),
(3, 3, 'erfhbqerlhb', 'uywgfonqyehrfnmiuy', 'hrgfiuyehrmgquyhb', 'uyb', 'flkjentgljknwrlthgrg', 'uybhreuwhrbgerg', 'glkjnwrtglhjertbnl hb', 'nl', NULL, '2026-02-20 13:43:14', '2026-02-20 13:43:14'),
(4, 4, 'skhwriWKHRGKhwr', 'h', 'hwfiq', 'hewfi', 'qfwihq', NULL, 'qegkjwrgh', 'dfbkjeguj', 'rbkhjerg', '2026-02-21 01:40:47', '2026-02-21 01:40:47'),
(5, 5, 'erljfnr', 'liuh', 'luyg', 'liug', 'uyg', 'kuyg', NULL, NULL, NULL, '2026-02-21 18:32:25', '2026-02-21 18:32:25'),
(6, 6, 'vomition since 3 days', 'vomition randomly 5 times in the past 3 days, no blood in vomit', 'slight dehydration, temp 102.3 F, CMM/OMM - NAD, PLN - NAD', 'kv', 'k', 'v', NULL, NULL, NULL, '2026-02-21 18:52:07', '2026-02-22 10:28:14'),
(7, 14, 'vomition', 'vomiting since 3 days', 'temp 103.5 F', 'pacreatisis? gastritis? Parvo?', NULL, NULL, NULL, NULL, NULL, '2026-02-24 10:43:54', '2026-02-24 10:43:54'),
(8, 9, 'Presenting complaint: Vomiting, lethargy, and anorexia.', 'History:  \r\nThe patient has experienced vomiting for the past three days, occurring after eating.', 'Rectal temperature is 103.4°F. Cranial and oral mucous membranes are within normal limits. Peripheral lymph nodes are slightly enlarged.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-24 13:10:51', '2026-02-24 13:10:51'),
(9, 16, 'Patient is lethargic and exhibiting vomiting.', 'History of vomiting after food intake for the past 2 days. Vaccinations are up to date. Patient is highly aggressive.', 'Mucous membranes and oral mucosa are slightly congested. Temperature is 102.7°F. Yellow pigmentation noted on the penis. Patient appears dull.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-26 02:29:15', '2026-02-26 02:29:15'),
(10, 17, 'no improvement in condition, vomition has stopped but pet still anorexic and dull', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-26 15:14:34', '2026-02-26 15:14:34'),
(11, 18, 'Chronic itching and scratching for ~6 months\r\nRecurrent ear infections (both ears, worse on right)\r\nPaw licking and interdigital redness\r\nOccasional head shaking\r\nMild hair loss over neck and axilla', 'Itching initially seasonal → now year-round\r\nMultiple flare-ups every 4–6 weeks\r\nTemporary relief with:\r\nInjectable steroids (exact drug unknown)\r\nAntibiotics during ear infections\r\nSymptoms recur within 2–3 weeks of stopping medication\r\nNo history of:\r\nVomiting\r\nDiarrhea\r\nWeight loss\r\nSeizures\r\n\r\nDiet History\r\nCommercial dry food (chicken-based)\r\nOccasional table scraps\r\nMilk given daily\r\nNo formal elimination diet attempted\r\n5. Preventive History\r\nVaccination: Up to date\r\nDeworming: Irregular\r\nTick/Flea prevention: Inconsistent (last dose ~2 months ago)\r\n6. Previous Treatments\r\nEar drops: Ofloxacin-based (intermittent use)\r\nOral antibiotics: Cefpodoxime / Cefixime (as per owner recall)\r\nInjectable steroids: Multiple times in past 6 months\r\nNo long-term allergy management protocol', 'General:\r\nBright, alert, responsive\r\nMild panting\r\nOverweight\r\nSkin:\r\nErythema in axilla, groin, paws\r\nHyperpigmentation around neck\r\nMild lichenification on paws\r\nNo visible ectoparasites\r\nEars:\r\nRight ear: Thick brown discharge, foul smell\r\nLeft ear: Mild erythema, waxy discharge\r\nPain on manipulation (right > left)\r\nVitals:\r\nTemperature: 102.4°F\r\nHeart rate: 96 bpm\r\nRespiration: 28/min\r\n\r\nPreliminary Diagnostics Done\r\nEar cytology (right ear):\r\nNumerous cocci\r\nModerate Malassezia\r\nOccasional inflammatory cells\r\nSkin scraping: Negative for mites', 'Chronic allergic dermatitis\r\nRecurrent otitis externa\r\nSuspected Atopy vs Food allergy', NULL, 'Tab Apoquel 16 mg – ½ tab BID\r\nEar cleaning with Tris-EDTA once daily\r\nOfloxacin ear drops once daily\r\nOmega-3 fatty acid supplement daily\r\nKetoconazole + Chlorhexidine shampoo twice weekly', NULL, NULL, NULL, '2026-03-01 03:27:15', '2026-03-01 03:27:15');

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

DROP TABLE IF EXISTS `clinics`;
CREATE TABLE `clinics` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `organisation_id`, `user_id`, `name`, `phone`, `email`, `address`, `city`, `state`, `pincode`, `gst_number`, `created_at`, `updated_at`) VALUES
(5, 9, 27, 'petovac indiranagr', '9019181810', 'petovac@gmail.com', 'indiranagar', NULL, NULL, NULL, NULL, '2026-02-19 10:56:45', '2026-03-10 16:27:10'),
(6, 9, NULL, 'petovac whitefiels', '9019181812', 'petovac2@gmail.com', 'indiranagr', 'blr', 'ka', '560038', '29aakct7282', '2026-02-19 11:01:17', '2026-02-19 11:01:17'),
(7, 9, 16, 'Petovac yelahanka', '98245987245', 'wrefuwrfb@gmail.com', 'fvlbreflbh', 'liuhwrfiuh', 'liuhwrliuh', '24897245', 'lwrbhfwhrg', '2026-02-19 11:40:03', '2026-03-04 07:29:36'),
(8, 9, NULL, 'Petovac electronic city', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 17:55:59', '2026-02-21 17:55:59'),
(9, 9, NULL, 'Petovac rajajinagar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 17:56:12', '2026-02-21 17:56:12'),
(10, 9, NULL, 'Petovac Banashankari', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 17:56:20', '2026-02-21 17:56:20'),
(11, 9, NULL, 'petovac', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 04:42:29', '2026-03-07 04:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_brands`
--

DROP TABLE IF EXISTS `clinic_brands`;
CREATE TABLE `clinic_brands` (
  `id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic_brands`
--

INSERT INTO `clinic_brands` (`id`, `clinic_id`, `brand_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 3, NULL, NULL),
(3, 1, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clinic_inventory`
--

DROP TABLE IF EXISTS `clinic_inventory`;
CREATE TABLE `clinic_inventory` (
  `id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `inventory_item_id` bigint UNSIGNED NOT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic_inventory`
--

INSERT INTO `clinic_inventory` (`id`, `clinic_id`, `inventory_item_id`, `stock`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 50, NULL, NULL),
(2, 1, 2, 38, NULL, NULL),
(3, 1, 3, 200, NULL, NULL),
(4, 1, 4, 25, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clinic_user_assignments`
--

DROP TABLE IF EXISTS `clinic_user_assignments`;
CREATE TABLE `clinic_user_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_vet`
--

DROP TABLE IF EXISTS `clinic_vet`;
CREATE TABLE `clinic_vet` (
  `id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `vet_id` bigint UNSIGNED NOT NULL,
  `role` enum('owner','vet','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vet',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `offboarded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic_vet`
--

INSERT INTO `clinic_vet` (`id`, `clinic_id`, `vet_id`, `role`, `is_active`, `last_login_at`, `created_by`, `created_at`, `updated_at`, `offboarded_at`) VALUES
(6, 5, 9, 'vet', 1, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(7, 7, 9, 'vet', 1, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(8, 6, 9, 'vet', 1, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(9, 8, 9, 'vet', 0, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(10, 9, 9, 'vet', 0, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(11, 10, 9, 'vet', 0, NULL, NULL, NULL, '2026-02-21 17:58:55', NULL),
(13, 5, 10, 'vet', 1, NULL, NULL, NULL, '2026-03-08 03:20:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diagnostic_files`
--

DROP TABLE IF EXISTS `diagnostic_files`;
CREATE TABLE `diagnostic_files` (
  `id` bigint UNSIGNED NOT NULL,
  `diagnostic_report_id` bigint UNSIGNED NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `extracted_text` longtext COLLATE utf8mb4_unicode_ci,
  `ai_summary` longtext COLLATE utf8mb4_unicode_ci,
  `status` enum('uploaded','extracted','human_verified') COLLATE utf8mb4_unicode_ci DEFAULT 'uploaded',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diagnostic_files`
--

INSERT INTO `diagnostic_files` (`id`, `diagnostic_report_id`, `original_filename`, `display_name`, `storage_path`, `mime_type`, `file_size`, `extracted_text`, `ai_summary`, `status`, `created_at`, `updated_at`) VALUES
(22, 15, 'Canine_Dog_Simba_5Y_M_Rottweiler_13240.pdf', 'cPL', 'diagnostics/16/U0VzoLCvoiXV6GTUnVHB1g5OTID6t3HnPl99roZi.pdf', 'application/pdf', 216945, '• Canine pancreatic lipase (cPL) value: 50.0 ng/mL  \r\n• cPL interpretation: Normal', '• Canine pancreatic lipase (cPL) value: 50.0 ng/mL  \r\n• cPL interpretation: Normal', 'human_verified', '2026-02-26 11:12:37', '2026-02-26 11:18:14'),
(23, 15, 'Canine_Dog_Simba_5Y_M_Rottweiler_16158.pdf', 'Vit B12', 'diagnostics/16/RLCLC1Zt5CBmFB9smkotJssJTwv34fgOMoHThoPO.pdf', 'application/pdf', 133663, '• Vitamin B-12: 245.0 pg/mL (**decreased**)', '• Vitamin B-12: 245.0 pg/mL (**decreased**)', 'human_verified', '2026-02-26 11:12:37', '2026-02-26 11:18:16'),
(24, 15, 'Canine_Dog_Simba_5Y_M_Rottweiler_112434.pdf', 'CBC, LFT, KFT, Vit D3', 'diagnostics/16/G9tVMpuRl0370VlkUfnUb8Pkuf8FtVmSMuB17gMt.pdf', 'application/pdf', 153239, '• Lymphocytosis: Absolute lymphocyte count 3.65 x10^3/uL (↑)\r\n• Erythrocytopenia: Total erythrocyte count 3.25 x10^6/uL (↓)\r\n• Anemia: Hemoglobin 8.70 g/dL (↓), Hematocrit 23.60% (↓)\r\n• Macrocytosis: MCH 26.90 pg (↑), MCHC 36.90 g/dL (↑), RDW-CV 16.20% (↑)\r\n• Thrombocytopenia: Platelet count 145 x10^3/uL (↓), PCT 0.141% (↓)\r\n• Markedly elevated SGPT (ALT): 1399.50 U/L (↑)\r\n• Markedly elevated SGOT (AST): 184.50 U/L (↑)\r\n• Elevated alkaline phosphatase: 653.80 IU/L (↑)\r\n• Elevated gamma glutamyl transferase: 18.10 U/L (↑)\r\n• Markedly elevated total bilirubin: 8.18 mg/dL (↑)\r\n• Markedly elevated direct bilirubin: 6.85 mg/dL (↑)\r\n• Elevated indirect bilirubin: 1.33 mg/dL (↑)\r\n• Hyperglobulinemia: Globulin 4.88 g/dL (↑)\r\n• Decreased albumin: 2.32 g/dL (↓)\r\n• Decreased A:G ratio: 0.48 (↓)\r\n• Hyperphosphatemia: Phosphorus 6.45 mg/dL (↑)\r\n• Vitamin D deficiency: Vitamin D (25 OH) 10.24 ng/mL (↓)', '• Lymphocytosis: Absolute lymphocyte count 3.65 x10^3/uL (↑)\r\n• Erythrocytopenia: Total erythrocyte count 3.25 x10^6/uL (↓)\r\n• Anemia: Hemoglobin 8.70 g/dL (↓), Hematocrit 23.60% (↓)\r\n• Macrocytosis: MCH 26.90 pg (↑), MCHC 36.90 g/dL (↑), RDW-CV 16.20% (↑)\r\n• Thrombocytopenia: Platelet count 145 x10^3/uL (↓), PCT 0.141% (↓)\r\n• Markedly elevated SGPT (ALT): 1399.50 U/L (↑)\r\n• Markedly elevated SGOT (AST): 184.50 U/L (↑)\r\n• Elevated alkaline phosphatase: 653.80 IU/L (↑)\r\n• Elevated gamma glutamyl transferase: 18.10 U/L (↑)\r\n• Markedly elevated total bilirubin: 8.18 mg/dL (↑)\r\n• Markedly elevated direct bilirubin: 6.85 mg/dL (↑)\r\n• Elevated indirect bilirubin: 1.33 mg/dL (↑)\r\n• Hyperglobulinemia: Globulin 4.88 g/dL (↑)\r\n• Decreased albumin: 2.32 g/dL (↓)\r\n• Decreased A:G ratio: 0.48 (↓)\r\n• Hyperphosphatemia: Phosphorus 6.45 mg/dL (↑)\r\n• Vitamin D deficiency: Vitamin D (25 OH) 10.24 ng/mL (↓)', 'human_verified', '2026-02-26 11:12:37', '2026-02-26 11:18:17'),
(25, 15, 'Canine_Dog_Simba_5Y_M_Rottweiler_124954.pdf', 'PCR', 'diagnostics/16/ESOsvza9d1nbDY5iKNEQFB6zIMHlW0BT7iLuT5u6.pdf', 'application/pdf', 422618, '• Babesia gibsoni not detected by RT-PCR \r\n• Babesia canis vogeli not detected by RT-PCR\r\n• Babesia canis canis not detected by RT-PCR\r\n• Babesia canis rossi not detected by RT-PCR\r\n• Hepatozoan canis not detected by RT-PCR\r\n• Anaplasma platys not detected by RT-PCR\r\n• Ehrlichia canis detected by RT-PCR (Positive; CT Value: 33.65)\r\n• Leptospira spp. not detected by RT-PCR', '• Babesia gibsoni not detected by RT-PCR \r\n• Babesia canis vogeli not detected by RT-PCR\r\n• Babesia canis canis not detected by RT-PCR\r\n• Babesia canis rossi not detected by RT-PCR\r\n• Hepatozoan canis not detected by RT-PCR\r\n• Anaplasma platys not detected by RT-PCR\r\n• Ehrlichia canis detected by RT-PCR (Positive; CT Value: 33.65)\r\n• Leptospira spp. not detected by RT-PCR', 'human_verified', '2026-02-26 13:21:18', '2026-02-26 13:21:26'),
(27, 17, 'Canine_Dog_Bingo_10Y_M_GoldenRetriever_163715.pdf', 'CBC, alt, alp, bun , creat', 'diagnostics/10/GzUUaJq8jSx630S4vyyoMmBvzoffmD1tuqr9Ayx9.pdf', 'application/pdf', 138587, '• Total leukocyte count: 19.19 x10^3/uL (elevated)  \r\n• Absolute neutrophil count: 17.12 x10^3/uL (elevated)  \r\n• Absolute lymphocyte count: 0.65 x10^3/uL (low-normal)  \r\n• Differential neutrophils: 89.20% (elevated)  \r\n• Differential lymphocytes: 3.40% (decreased)  \r\n• Total erythrocyte count: 4.15 x10^6/uL (decreased)  \r\n• Hemoglobin: 8.90 g/dL (decreased)  \r\n• Hematocrit (PCV): 27.20% (decreased)  \r\n• MCV: 60.20 fL (decreased)  \r\n• RDW-CV: 19.20% (elevated)  \r\n• Platelet count: 115 x10^3/uL (decreased)  \r\n• PCT: 0.071% (decreased)  \r\n• AST:ALT ratio: 1.58 (elevated)  \r\n• SGPT (ALT): 15.60 U/L (decreased)  \r\n• Blood urea nitrogen: 7.29 mg/dL (decreased)  \r\n• Globulin: 4.30 g/dL (elevated)  \r\n• Albumin:Globulin ratio: 0.55 (low-normal)', '• Total leukocyte count: 19.19 x10^3/uL (elevated)  \r\n• Absolute neutrophil count: 17.12 x10^3/uL (elevated)  \r\n• Absolute lymphocyte count: 0.65 x10^3/uL (low-normal)  \r\n• Differential neutrophils: 89.20% (elevated)  \r\n• Differential lymphocytes: 3.40% (decreased)  \r\n• Total erythrocyte count: 4.15 x10^6/uL (decreased)  \r\n• Hemoglobin: 8.90 g/dL (decreased)  \r\n• Hematocrit (PCV): 27.20% (decreased)  \r\n• MCV: 60.20 fL (decreased)  \r\n• RDW-CV: 19.20% (elevated)  \r\n• Platelet count: 115 x10^3/uL (decreased)  \r\n• PCT: 0.071% (decreased)  \r\n• AST:ALT ratio: 1.58 (elevated)  \r\n• SGPT (ALT): 15.60 U/L (decreased)  \r\n• Blood urea nitrogen: 7.29 mg/dL (decreased)  \r\n• Globulin: 4.30 g/dL (elevated)  \r\n• Albumin:Globulin ratio: 0.55 (low-normal)', 'extracted', '2026-02-28 20:13:28', '2026-02-28 20:13:28'),
(28, 18, 'Canine_Dog_Ganga_11Y_F_Lab_115512.pdf', 'skin microscopic', 'diagnostics/6/FkLXkJJDj77XqYYtpj3FZ0fd8NUfcjFadtXAHb4u.pdf', 'application/pdf', 254063, '• Exfoliated epithelial cells, cellular debris, and fragments of hair follicles with associated hair shaft debris observed on skin scraping  \r\n• Mild structural deformities of hair shafts noted  \r\n• No bacteria, fungal elements, ectoparasites, malignant cells, or cytological atypia identified', '• Exfoliated epithelial cells and cellular debris, and fragments of hair follicles with associated hair shaft debris observed on skin scraping  \r\n• Mild structural deformities of hair shafts noted  \r\n• No bacteria, fungal elements, ectoparasites, malignant cells, or cytological atypia identified', 'human_verified', '2026-02-28 20:21:55', '2026-03-01 02:38:30'),
(29, 18, 'Canine_Dog_Simba_5Y_M_Rottweiler_13240.pdf', 'cPL', 'diagnostics/6/NBLf5K2rrzQZ1m2nibbxdiFPM3JTin0ZjFAZEQNV.pdf', 'application/pdf', 216945, '• Canine pancreatic lipase (cPL): 50.0 ng/mL  \r\n• cPL value within normal limits', '• Canine pancreatic lipase (cPL): 50.0 ng/mL  \r\n• cPL value within normal limits', 'human_verified', '2026-03-01 02:46:18', '2026-03-01 02:46:42'),
(30, 19, 'Canine_Dog Mouly.pdf', 'cbc lft kft', 'diagnostics/7/SxA4I8G91m6aDOsRWgvZO0zoZJDu3qxG0kq0M2jx.pdf', 'application/pdf', 140934, '• Haemoglobin: 19.00 g/dL (**elevated**)\r\n• MCH: 27.80 pg (**elevated**)\r\n• MCHC: 39.20 g/dL (**elevated**)\r\n• RDW-SD: 34.00 fL (**decreased**)\r\n• PCT: 0.212% (**decreased**)\r\n• AST:ALT ratio: 1.67 (**elevated**)\r\n• Globulin: 3.72 g/dL (**elevated**)\r\n• Urea: 67.84 mg/dL (**elevated**)\r\n• Blood Urea Nitrogen: 31.68 mg/dL (**elevated**)\r\n• Estrogen: 486.47 pg/mL (**elevated**)', '• Haemoglobin: 21.00 g/dL (**elevated**)\r\n• MCH: 27.80 pg (**elevated**)\r\n• MCHC: 39.20 g/dL (**elevated**)\r\n• RDW-SD: 34.00 fL (**decreased**)\r\n• PCT: 0.212% (**decreased**)\r\n• AST:ALT ratio: 1.67 (**elevated**)\r\n• Globulin: 3.72 g/dL (**elevated**)\r\n• Urea: 67.84 mg/dL (**elevated**)\r\n• Blood Urea Nitrogen: 31.68 mg/dL (**elevated**)\r\n• Estrogen: 486.47 pg/mL (**elevated**)', 'human_verified', '2026-03-05 13:25:53', '2026-03-05 13:27:17');

-- --------------------------------------------------------

--
-- Table structure for table `diagnostic_reports`
--

DROP TABLE IF EXISTS `diagnostic_reports`;
CREATE TABLE `diagnostic_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_id` bigint UNSIGNED NOT NULL,
  `pet_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `vet_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('lab','radiology') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `lab_or_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` longtext COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diagnostic_reports`
--

INSERT INTO `diagnostic_reports` (`id`, `appointment_id`, `pet_id`, `clinic_id`, `vet_id`, `type`, `title`, `report_date`, `lab_or_center`, `summary`, `file_path`, `file_type`, `created_at`, `updated_at`) VALUES
(15, 16, 10, 5, 9, 'lab', NULL, '2026-02-26', NULL, NULL, NULL, NULL, '2026-02-26 11:12:37', '2026-02-26 11:12:37'),
(17, 10, 2, 5, 9, 'lab', NULL, '2026-03-01', NULL, NULL, NULL, NULL, '2026-02-28 20:13:28', '2026-02-28 20:13:28'),
(18, 6, 2, 5, 9, 'lab', NULL, '2026-03-01', NULL, NULL, NULL, NULL, '2026-02-28 20:21:55', '2026-02-28 20:21:55'),
(19, 7, 2, 5, 9, 'lab', NULL, '2026-03-05', NULL, NULL, NULL, NULL, '2026-03-05 13:25:53', '2026-03-05 13:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `drug_brands`
--

DROP TABLE IF EXISTS `drug_brands`;
CREATE TABLE `drug_brands` (
  `id` bigint UNSIGNED NOT NULL,
  `generic_id` bigint UNSIGNED NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strength_value` decimal(8,2) DEFAULT NULL,
  `strength_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'mg/ml',
  `pack_size` decimal(8,2) DEFAULT NULL,
  `strength` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form` enum('tablet','capsule','injection','vial','fluid') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pack_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drug_brands`
--

INSERT INTO `drug_brands` (`id`, `generic_id`, `brand_name`, `strength_value`, `strength_unit`, `pack_size`, `strength`, `form`, `manufacturer`, `created_at`, `updated_at`, `pack_unit`) VALUES
(11, 14, 'MeloMelo', '5.00', 'mg/ml', '30.00', NULL, 'injection', NULL, '2026-03-10 13:33:40', '2026-03-10 13:33:40', 'ml');

-- --------------------------------------------------------

--
-- Table structure for table `drug_dosages`
--

DROP TABLE IF EXISTS `drug_dosages`;
CREATE TABLE `drug_dosages` (
  `id` bigint UNSIGNED NOT NULL,
  `generic_id` bigint UNSIGNED NOT NULL,
  `species` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dose_min` decimal(8,2) NOT NULL,
  `dose_max` decimal(8,2) NOT NULL,
  `dose_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'mg/kg',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `routes` json DEFAULT NULL,
  `frequencies` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drug_dosages`
--

INSERT INTO `drug_dosages` (`id`, `generic_id`, `species`, `dose_min`, `dose_max`, `dose_unit`, `created_at`, `updated_at`, `routes`, `frequencies`) VALUES
(7, 13, 'dog', '12.00', '24.00', 'mg/kg', '2026-03-09 10:16:30', '2026-03-09 10:17:25', '\"[\\\"IV\\\",\\\"SC\\\"]\"', '\"[\\\"SID\\\"]\"'),
(8, 13, 'cat', '12.00', '24.00', 'mg/kg', '2026-03-09 10:21:57', '2026-03-09 10:21:57', '\"[\\\"IV\\\",\\\"SC\\\"]\"', '\"[\\\"BID\\\"]\"'),
(9, 13, 'rabbit', '15.00', '30.00', 'mg/kg', '2026-03-09 10:22:11', '2026-03-09 10:22:11', '\"[\\\"IM\\\"]\"', '\"[\\\"SID\\\"]\"'),
(10, 14, 'dog', '0.10', '0.30', 'mg/kg', '2026-03-09 14:09:26', '2026-03-09 14:09:26', '\"[\\\"IV\\\",\\\"IM\\\",\\\"SC\\\",\\\"Oral\\\"]\"', '\"[\\\"SID\\\"]\"');

-- --------------------------------------------------------

--
-- Table structure for table `drug_generics`
--

DROP TABLE IF EXISTS `drug_generics`;
CREATE TABLE `drug_generics` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `drug_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_dose_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'mg/kg',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drug_generics`
--

INSERT INTO `drug_generics` (`id`, `name`, `drug_class`, `default_dose_unit`, `created_by`, `created_at`, `updated_at`) VALUES
(13, 'Ceftriaxone', 'Cephalosporin', 'mg/kg', 2, '2026-03-09 10:16:16', '2026-03-09 10:16:16'),
(14, 'Meloxicam', 'NSAID', 'mg/kg', 2, '2026-03-09 14:09:06', '2026-03-09 14:09:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batches`
--

DROP TABLE IF EXISTS `inventory_batches`;
CREATE TABLE `inventory_batches` (
  `id` bigint UNSIGNED NOT NULL,
  `inventory_item_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED DEFAULT NULL,
  `batch_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
CREATE TABLE `inventory_items` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED NOT NULL,
  `item_type` enum('drug','consumable') COLLATE utf8mb4_unicode_ci DEFAULT 'drug',
  `generic_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `drug_brand_id` bigint UNSIGNED DEFAULT NULL,
  `strength_value` decimal(10,2) DEFAULT NULL,
  `strength_unit` enum('mg/ml','mg','gm','IU','%') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package_type` enum('tablet','capsule','injection','vial','fluid','bottle','strip','packet','tube','piece','sachet') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_volume_ml` decimal(10,2) DEFAULT NULL,
  `pack_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_multi_use` tinyint(1) DEFAULT '0',
  `track_inventory` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `organisation_id`, `item_type`, `generic_name`, `brand_id`, `drug_brand_id`, `strength_value`, `strength_unit`, `name`, `unit`, `package_type`, `unit_volume_ml`, `pack_unit`, `is_multi_use`, `track_inventory`, `created_at`, `updated_at`) VALUES
(11, 9, 'drug', 'Meloxicam', NULL, NULL, '5.00', 'mg/ml', 'Melotest', NULL, 'injection', '30.00', 'ml', 0, 0, '2026-03-10 14:21:28', '2026-03-10 14:21:28'),
(12, 9, 'consumable', NULL, NULL, NULL, NULL, NULL, 'Royal Canin Recovery Dog food Wet', NULL, 'packet', '2.00', 'kg', 0, 0, '2026-03-10 15:29:22', '2026-03-10 15:29:22'),
(13, 9, 'drug', 'Meloxicam', NULL, 11, '5.00', 'mg/ml', 'MeloMelo', NULL, 'injection', '30.00', 'ml', 0, 0, '2026-03-10 15:46:04', '2026-03-10 15:46:04');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
CREATE TABLE `inventory_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `inventory_item_id` bigint UNSIGNED NOT NULL,
  `inventory_batch_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `movement_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'unit',
  `movement_type` enum('purchase','transfer_in','transfer_out','treatment_usage','manual_adjustment','expired') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_usage_logs`
--

DROP TABLE IF EXISTS `inventory_usage_logs`;
CREATE TABLE `inventory_usage_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_treatment_id` bigint UNSIGNED DEFAULT NULL,
  `inventory_item_id` bigint UNSIGNED NOT NULL,
  `inventory_batch_id` bigint UNSIGNED NOT NULL,
  `quantity_used` decimal(10,3) NOT NULL,
  `usage_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'ml',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_16_213158_create_clinics_table', 1),
(5, '2026_02_16_213158_create_vets_table', 1),
(6, '2026_02_16_213159_create_clinic_vet_table', 1),
(7, '2026_02_17_170848_add_profile_fields_to_vets_table', 2),
(8, '2026_02_17_173225_create_appointments_table', 3),
(9, '2026_02_17_174820_create_pet_parents_table', 4),
(10, '2026_02_17_175321_create_pets_table', 5),
(11, '2026_02_17_183825_create_pet_parent_clinic_access_table', 6),
(12, '2026_02_17_183908_create_pet_parent_access_otps_table', 6),
(13, '2026_02_17_220605_create_prescriptions_table', 7),
(14, '2026_02_17_220726_create_prescription_items_table', 8),
(15, '2026_02_17_225219_update_case_sheets_table', 8),
(16, '2026_02_19_164749_create_clinic_user_assignments_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `organisations`
--

DROP TABLE IF EXISTS `organisations`;
CREATE TABLE `organisations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single_clinic','corporate') COLLATE utf8mb4_unicode_ci DEFAULT 'single_clinic',
  `primary_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organisations`
--

INSERT INTO `organisations` (`id`, `name`, `type`, `primary_email`, `primary_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 'Petovac', 'corporate', 'harshitdaffo@gmail.com', '9454367093', 1, '2026-02-19 07:59:10', '2026-02-19 07:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `organisation_roles`
--

DROP TABLE IF EXISTS `organisation_roles`;
CREATE TABLE `organisation_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `clinic_scope` enum('none','single','multiple') COLLATE utf8mb4_unicode_ci DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organisation_roles`
--

INSERT INTO `organisation_roles` (`id`, `organisation_id`, `name`, `description`, `created_at`, `updated_at`, `clinic_scope`) VALUES
(12, 9, 'Clinic Manager', NULL, '2026-03-07 14:47:00', '2026-03-07 14:47:00', 'single');

-- --------------------------------------------------------

--
-- Table structure for table `organisation_user_roles`
--

DROP TABLE IF EXISTS `organisation_user_roles`;
CREATE TABLE `organisation_user_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organisation_user_roles`
--

INSERT INTO `organisation_user_roles` (`id`, `organisation_id`, `clinic_id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(6, 9, 5, 26, 12, '2026-03-07 14:52:41', '2026-03-07 14:52:41'),
(7, 9, 5, 27, 12, '2026-03-10 16:27:10', '2026-03-10 16:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Manage Users', 'manage_users', '2026-03-04 17:28:37', '2026-03-04 17:28:37'),
(3, 'View Dashboard', 'dashboard.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(4, 'View Clinics', 'clinics.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(5, 'Manage Clinics', 'clinics.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(6, 'View Users', 'users.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(7, 'Manage Users', 'users.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(8, 'View Roles', 'roles.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(9, 'Manage Roles', 'roles.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(10, 'View Vets', 'vets.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(11, 'Assign Vets', 'vets.assign', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(12, 'Create Appointments', 'appointments.create', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(13, 'View Appointments', 'appointments.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(14, 'Manage Appointments', 'appointments.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(15, 'View Appointment Metrics', 'appointments.metrics', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(16, 'View Case Sheets', 'cases.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(17, 'View Treatments', 'treatments.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(18, 'View Prescriptions', 'prescriptions.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(19, 'Upload Reports', 'reports.upload', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(20, 'View Reports', 'reports.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(21, 'Create Billing', 'billing.create', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(22, 'View Billing', 'billing.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(23, 'View Billing Metrics', 'billing.metrics', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(24, 'View Inventory', 'inventory.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(25, 'Manage Inventory', 'inventory.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(26, 'Adjust Inventory', 'inventory.adjust', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(27, 'Purchase Inventory', 'inventory.purchase', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(28, 'View Inventory Movements', 'inventory.movements.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(29, 'Transfer Inventory', 'inventory.transfer', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(30, 'View Inventory Metrics', 'inventory.metrics', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(31, 'View Pricing', 'pricing.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(32, 'Manage Pricing', 'pricing.manage', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(33, 'View Followups', 'followups.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(34, 'View Reviews', 'reviews.view', '2026-03-04 19:42:35', '2026-03-04 19:42:35'),
(35, 'View Doctor Performance', 'doctors.performance_view', '2026-03-04 19:42:35', '2026-03-04 19:42:35');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

DROP TABLE IF EXISTS `pets`;
CREATE TABLE `pets` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `species` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age_months` tinyint UNSIGNED DEFAULT NULL,
  `age_recorded_at` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pet_parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `name`, `species`, `breed`, `age`, `age_months`, `age_recorded_at`, `gender`, `pet_parent_id`, `created_at`, `updated_at`) VALUES
(1, 'rfliher', 'kuhrgkuh', 'rsfuh', '10', NULL, NULL, 'kubsrf', 2, '2026-02-17 12:41:44', '2026-02-17 12:41:44'),
(2, 'bruno', 'dog', 'lab', '1', NULL, NULL, 'male', 1, '2026-02-17 13:32:09', '2026-02-17 13:32:09'),
(3, 'testfkjb', 'kbfv', 'bfeviub', '3', NULL, NULL, 'kbefrvhber', 1, '2026-02-17 15:36:12', '2026-02-17 15:36:12'),
(4, 'efkjvnervkjn', 'jnfdvkjn', 'kjnfvkjn', '2', NULL, NULL, 'jsfv f', 1, '2026-02-17 15:41:56', '2026-02-17 15:41:56'),
(5, 'Bruno', 'lab', 'lab', '12', NULL, NULL, 'male', 8, '2026-02-21 06:20:05', '2026-02-21 06:20:05'),
(6, 'Bruno', 'dog', 'labrador', '2.5', NULL, '2026-02-23', 'Male', 1, '2026-02-23 10:04:31', '2026-02-23 10:04:31'),
(7, 'New Pet', 'dog', 'labrador', '3', NULL, '2026-02-23', 'male', 1, '2026-02-23 12:36:46', '2026-02-23 12:36:46'),
(8, 'Ginger', 'dog', 'labrador', '2', NULL, '2026-02-23', 'female', 6, '2026-02-23 13:19:32', '2026-02-23 13:19:32'),
(9, 'Dolly', 'dog', 'Golden Retreiver', '7', 6, '2026-02-23', 'male', 6, '2026-02-23 13:32:20', '2026-02-23 13:32:20'),
(10, 'Simba', 'Dog', 'Rottweiler', '5', 2, '2026-02-26', 'Male', 6, '2026-02-26 02:25:56', '2026-02-26 02:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `pet_parents`
--

DROP TABLE IF EXISTS `pet_parents`;
CREATE TABLE `pet_parents` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pet_parents`
--

INSERT INTO `pet_parents` (`id`, `name`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Harshit', '9454367093', '2026-02-17 12:32:11', '2026-02-17 12:32:11'),
(2, 'harshit', '9454367093', '2026-02-17 12:34:23', '2026-02-17 12:34:23'),
(3, 'Unknown', '9873984733', '2026-02-17 13:49:47', '2026-02-17 13:49:47'),
(5, 'Test', '9019181810', '2026-02-17 15:04:33', '2026-02-17 15:04:33'),
(6, 'Harshit Gupta', '9019181812', '2026-02-21 06:19:36', '2026-02-21 06:19:36'),
(7, 'Harshit Gupta', '9454367093', '2026-02-21 06:19:44', '2026-02-21 06:19:44'),
(8, 'Harshit Gupta', '9019181812', '2026-02-21 06:19:52', '2026-02-21 06:19:52');

-- --------------------------------------------------------

--
-- Table structure for table `pet_parent_access_otps`
--

DROP TABLE IF EXISTS `pet_parent_access_otps`;
CREATE TABLE `pet_parent_access_otps` (
  `id` bigint UNSIGNED NOT NULL,
  `pet_parent_id` bigint UNSIGNED DEFAULT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pet_parent_access_otps`
--

INSERT INTO `pet_parent_access_otps` (`id`, `pet_parent_id`, `clinic_id`, `mobile`, `otp`, `expires_at`, `verified_at`, `created_at`, `updated_at`) VALUES
(15, NULL, 5, '9454367093', '997140', '2026-02-21 06:00:00', NULL, '2026-02-20 10:39:17', '2026-02-21 05:55:00'),
(16, NULL, 5, '9019181810', '126990', '2026-02-20 11:55:02', NULL, '2026-02-20 11:46:07', '2026-02-20 11:50:02'),
(18, NULL, 7, '9454367093', '802021', '2026-02-20 13:53:41', NULL, '2026-02-20 13:48:00', '2026-02-20 13:48:41');

-- --------------------------------------------------------

--
-- Table structure for table `pet_parent_clinic_access`
--

DROP TABLE IF EXISTS `pet_parent_clinic_access`;
CREATE TABLE `pet_parent_clinic_access` (
  `id` bigint UNSIGNED NOT NULL,
  `pet_parent_id` bigint UNSIGNED NOT NULL,
  `clinic_id` bigint UNSIGNED NOT NULL,
  `granted_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `granted_via` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp_otp',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pet_parent_clinic_access`
--

INSERT INTO `pet_parent_clinic_access` (`id`, `pet_parent_id`, `clinic_id`, `granted_at`, `revoked_at`, `granted_via`, `created_at`, `updated_at`) VALUES
(4, 1, 5, '2026-02-21 01:41:34', NULL, 'whatsapp_otp', '2026-02-20 10:39:22', '2026-02-21 01:41:34'),
(5, 5, 5, '2026-02-20 11:46:12', NULL, 'whatsapp_otp', '2026-02-20 11:46:12', '2026-02-20 11:46:12'),
(6, 1, 7, '2026-02-20 13:48:06', NULL, 'whatsapp_otp', '2026-02-20 13:48:06', '2026-02-20 13:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE `prescriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `appointment_id` bigint UNSIGNED NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `notes`, `created_at`, `updated_at`) VALUES
(3, 3, 'test daisgnosis', '2026-02-20 13:42:53', '2026-02-20 13:42:53'),
(4, 4, 'tessfghsrg', '2026-02-21 01:41:11', '2026-02-21 01:41:11'),
(5, 16, NULL, '2026-02-26 13:33:40', '2026-02-26 13:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

DROP TABLE IF EXISTS `prescription_items`;
CREATE TABLE `prescription_items` (
  `id` bigint UNSIGNED NOT NULL,
  `prescription_id` bigint UNSIGNED NOT NULL,
  `drug_generic_id` bigint UNSIGNED DEFAULT NULL,
  `drug_brand_id` bigint UNSIGNED DEFAULT NULL,
  `medicine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `drug_generic_id`, `drug_brand_id`, `medicine`, `dosage`, `frequency`, `duration`, `instructions`, `created_at`, `updated_at`) VALUES
(7, 3, NULL, NULL, 'ergiuh', 'iub', 'iub', 'ib', 'iub', '2026-02-20 13:42:53', '2026-02-20 13:42:53'),
(8, 3, NULL, NULL, 'erigubherghiberg', 'hyb', 'iub', 'iub', 'rfireu', '2026-02-20 13:42:53', '2026-02-20 13:42:53'),
(9, 3, NULL, NULL, 'test', 'you', 'are', 'okay', 'or not', '2026-02-20 13:42:53', '2026-02-20 13:42:53'),
(10, 4, NULL, NULL, 'sfkghjrgkuj', 'kjrgkjh', 'kjwrgkj', 'krgkj', 'krgkjh', '2026-02-21 01:41:11', '2026-02-21 01:41:11'),
(11, 4, NULL, NULL, 'gkjhetrgkh', 'krgkhjewrg', 'wjrgkjhergkj', 'wkrjgk', 'jwrgkjrg', '2026-02-21 01:41:11', '2026-02-21 01:41:11'),
(12, 4, NULL, NULL, 'ejvhrkh', 'khrgkh', 'krhjgkhergkhj', 'kewrgkherge', 'gjhergh', '2026-02-21 01:41:11', '2026-02-21 01:41:11'),
(13, 5, NULL, NULL, 'Tab Doxycyline 300 mg', '1.5 tabs', 'OD', '28 days', 'avoid with milk/milk products', '2026-02-26 13:33:40', '2026-02-26 13:33:40'),
(14, 5, NULL, NULL, 'Tab Pan 40', '1 tab', 'OD', '10 days', '40 mins before 1st meal', '2026-02-26 13:33:40', '2026-02-26 13:33:40'),
(15, 5, NULL, NULL, 'Syp SAMePET Forte', '7.5ml', 'BID', '28 days', NULL, '2026-02-26 13:33:40', '2026-02-26 13:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `price_lists`
--

DROP TABLE IF EXISTS `price_lists`;
CREATE TABLE `price_lists` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_lists`
--

INSERT INTO `price_lists` (`id`, `organisation_id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 9, 'Clinic Prices', 1, '2026-02-20 02:12:37', '2026-02-20 02:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `price_list_items`
--

DROP TABLE IF EXISTS `price_list_items`;
CREATE TABLE `price_list_items` (
  `id` bigint UNSIGNED NOT NULL,
  `price_list_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `drug_brand_id` bigint DEFAULT NULL,
  `item_type` enum('service','treatment','product') COLLATE utf8mb4_unicode_ci DEFAULT 'service',
  `inventory_item_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `procedure_price` decimal(10,2) DEFAULT '0.00',
  `billing_type` enum('fixed','per_ml','per_vial','per_tablet','per_unit') COLLATE utf8mb4_unicode_ci DEFAULT 'fixed',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_list_items`
--

INSERT INTO `price_list_items` (`id`, `price_list_id`, `name`, `drug_brand_id`, `item_type`, `inventory_item_id`, `code`, `price`, `procedure_price`, `billing_type`, `is_active`, `created_at`, `updated_at`) VALUES
(22, 1, 'Inj MeloMelo', 8, 'treatment', NULL, NULL, '47.00', '300.00', 'per_ml', 1, '2026-03-09 15:35:29', '2026-03-09 15:35:45'),
(23, 1, 'Inj tramadol', NULL, 'service', NULL, NULL, '49.00', '300.00', 'fixed', 1, '2026-03-09 15:35:29', '2026-03-09 15:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(146, 12, 12, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(147, 12, 21, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(148, 12, 14, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(149, 12, 19, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(150, 12, 15, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(151, 12, 13, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(152, 12, 22, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(153, 12, 23, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(154, 12, 3, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(155, 12, 33, '2026-03-10 16:39:29', '2026-03-10 16:39:29'),
(156, 12, 20, '2026-03-10 16:39:29', '2026-03-10 16:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0tedCdRMP3a7wKrCckKG7yvUMj5WwbTXee023mpH', 10, '49.37.170.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieFJ4OW1hQ0dlellsVDF4NjB4RnJhTlFhS1o5YzdHNWlBTkdoOUpkYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NDoiaHR0cHM6Ly9jbGluaWNkZXNxLmNvbS9vcmdhbmlzYXRpb24vcHJpY2UtbGlzdHMvMS9lZGl0Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vY2xpbmljZGVzcS5jb20vb3JnYW5pc2F0aW9uL3JvbGVzIjtzOjU6InJvdXRlIjtzOjI0OiJvcmdhbmlzYXRpb24ucm9sZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMDt9', 1773311308),
('6fZcxCaAHPcjimKcWCQsG2PHcSSpwPwdYFW4lmKZ', NULL, '206.168.34.202', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicmtXM0xiSTFyVG43S2RCZnB4NzNvaGpKc21SM0FrcU1zVkRsR0lqYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAvc3RhZmYvbG9naW4iO3M6NToicm91dGUiO3M6MTE6InN0YWZmLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773309247),
('6HfiaRg5scGREu4pkqyNtcqhjBAmTB5zGhS7UAwE', NULL, '119.28.140.106', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWnUxYWZRd1NvaHlLQmZqeGdDdkZQRzhGRlhKaG82d3AwR3lHS0tLciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773305564),
('b85c2oYEBVnc2gp8Rv8Cyqh1UDHweaOTh2kpQiZN', NULL, '43.157.22.57', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWdOSnNJQzY4T2FrZTlWMHV2djVhM0pNVThPVTRFaTJzZmpnNjYzdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773310640),
('blYoUXzoWyAOiffBk8VkvjOd6jTJK38N4bSvQ06T', NULL, '43.157.22.57', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMGNJcmg0RWVaeFBLUG9NRHp1Zkh6dUJ4dUNidGE2VzRKV2tDSXd6MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773310642),
('fmQRotqOXNBp6SDsMsllhR2hogpjQe5lmLb21luL', NULL, '206.168.34.202', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWRJeDY1V0lDdjRGYVRYWEZ3OUEzR3ZneU15SlZIdWczSW15MWNQTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773309239),
('FxypGVxDg28jXB3EhBZABdfFuSQk3ouczIxByDHb', NULL, '182.42.105.85', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVdySnRMVEtJclp2Mnc0VTViQ2pxZVpGN2dBWjlNSUtEaXI0aEw0eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tL3N0YWZmL2xvZ2luIjtzOjU6InJvdXRlIjtzOjExOiJzdGFmZi5sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773311961),
('KQ2nK9oTdhlIrM1YEAO1QrQARC5vJaxuaAMbTgr5', NULL, '119.28.140.106', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWVUSk13N3FXd2E1WTk2anF4b2daQlk5alJGWllybXVkRDZoMWNoRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAvc3RhZmYvbG9naW4iO3M6NToicm91dGUiO3M6MTE6InN0YWZmLmxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773305565),
('kUbagxyNCDjChDnvGu9oSxRqD10WOXj1PjrHc9VC', NULL, '43.157.22.57', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVBVYk1SbUx4S25MZk1HTnc1amVrWDdXMkJvemNCdTdBQWIzUGtYViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tL3N0YWZmL2xvZ2luIjtzOjU6InJvdXRlIjtzOjExOiJzdGFmZi5sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773310644),
('n5FiQkXZGRd4rZnyw5pP2MKl2zVTrhHxdurBsepF', 9, '49.37.170.70', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZDlxT2V0cEpXOGtLc0QxRUtrTFhxN281dDBEc3BwMFAyZXV2T3FGRiI7czo1MDoibG9naW5fdmV0XzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6OTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1NToiaHR0cHM6Ly9jbGluaWNkZXNxLmNvbS92ZXQvYXBwb2ludG1lbnRzLzEzL2hpc3RvcnktdmlldyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxNjoiYWN0aXZlX2NsaW5pY19pZCI7aTo1O30=', 1773311474),
('ne8aMl8CXB7gQlxMDtQEiHImARFSjmAnL2NcnS7i', NULL, '119.28.140.106', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT3cwODlXbzJlRmxYREdjY0ZIRnBhcEYyMEV3UkdoOVZmUFZkMlZMaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773305563),
('vYprWZxbMYXCUiW7tN3ftFUCV8X8ENXCd5IHFHQ6', NULL, '206.168.34.202', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamRmYzFOMnhJc05oWWxsQVRudjFBTHJWVjM4Vk94dzhzTVVEMktHRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vNDMuMjA1LjE1Ni4xMzAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773309244),
('XFajs3ra1Lkvyjc6JZ5kXKq41qEs5k8dD32WtFiG', NULL, '182.42.105.85', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidHZLaHdyOUdqOHlZdmFiUDMzdk1HT3dMZkFCYUlMSjlOb0pYbVcxYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773311959),
('XVnxPu71mthUoMLaOYIPBc2NMvZtdHcYk951VSpl', NULL, '182.42.105.85', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzZDVzFFYTd5dVY0TVoyMVBITzMwVDh3dlpBU3VRbEJBaTBBTUsxRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vd3d3LmNsaW5pY2Rlc3EuY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773311952);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `organisation_id` bigint UNSIGNED DEFAULT NULL,
  `clinic_id` bigint UNSIGNED DEFAULT NULL,
  `vet_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `organisation_id`, `clinic_id`, `vet_id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, NULL, NULL, NULL, 'Super Admin', 'admin@clinicdesq.com', NULL, NULL, '$2y$12$WXRpEOORRJ0baUPzj8X9fOzCDON3UMBDIno0BXSF9rgqHqPf0qLyS', 'superadmin', 1, NULL, '2026-02-18 06:46:58', '2026-02-18 06:46:58'),
(10, 9, NULL, NULL, 'harshit', 'harshitdaffo@gmail.com', '9454367093', NULL, '$2y$12$CHHBwLATcUC0c8b4Hh7dj.LD/sZLoiByOIzxmpxHklxDrAZlwkKDK', 'organisation_owner', 1, NULL, '2026-02-19 07:59:11', '2026-02-19 07:59:11'),
(26, 9, NULL, NULL, 'Nisha', 'nisha.test@gmail.com', '9019181816', NULL, '$2y$12$5zftXPwFp7uvKRv05E5Vn.cNlBP26JHb9aNUTGTyZPibubq3/GfDy', 'clinic_manager', 1, NULL, '2026-03-07 14:52:41', '2026-03-10 16:27:10'),
(27, 9, 5, NULL, 'Isha', 'isha.test@gmail.com', '9999999998', NULL, '$2y$12$n5zl.ZlVKKb1bmrubRlKOO//eF/gEE6Cr8B8n7yH31Nt4nZF3WRiG', 'clinic_manager', 1, NULL, '2026-03-10 16:27:10', '2026-03-10 16:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `vets`
--

DROP TABLE IF EXISTS `vets`;
CREATE TABLE `vets` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` text COLLATE utf8mb4_unicode_ci,
  `certifications` text COLLATE utf8mb4_unicode_ci,
  `experience` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vets`
--

INSERT INTO `vets` (`id`, `name`, `phone`, `email`, `password`, `remember_token`, `registration_number`, `specialization`, `degree`, `skills`, `certifications`, `experience`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 'Dr Amit Sharma', '9999999991', 'amit.vet@test.com', '$2y$12$elvJsf5NMknQhtaANgghWObmZXXdyEFMr1bLO4hp8uiMvjDAhmKPa', 'KygwEDCBUoIuGxxWapDHITPiLP1OzfF31dcoSLCqWfNhDH7ZhXB2I1nZ4vZV', 'VET-REG-001', 'Small Animal Medicine', 'BVSc AH', 'surergry, USG, X ray', 'WVS', '4 years', 1, NULL, '2026-02-20 09:31:37'),
(10, 'Dr Neha Verma', '9999999992', 'neha.vet@test.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'VET-REG-002', 'Surgery', NULL, NULL, NULL, '7 years', 1, NULL, NULL),
(11, 'Dr Rahul Iyer', '9999999993', 'rahul.vet@test.com', '$2y$12$elvJsf5NMknQhtaANgghWObmZXXdyEFMr1bLO4hp8uiMvjDAhmKPa', '7Fg5eDMAjVmli7ghDjbmHSJyR9MPyuHK36bd1dJllWIbDkGqCdT382hmITqS', 'VET-REG-003', 'Dermatology', NULL, NULL, NULL, '4 years', 1, NULL, NULL),
(12, 'Dr Pooja Singh', '9000000004', 'pooja.vet@test.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'VET-REG-004', 'Internal Medicine', NULL, NULL, NULL, '6 years', 1, NULL, NULL),
(13, 'Dr Kunal Mehta', '9000000005', 'kunal.vet@test.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'VET-REG-005', 'Orthopedics', NULL, NULL, NULL, '8 years', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_vet_id_foreign` (`vet_id`),
  ADD KEY `idx_appointments_clinic_date` (`clinic_id`,`scheduled_at`),
  ADD KEY `idx_clinic_date_appt` (`clinic_id`,`scheduled_at`,`appointment_number`);

--
-- Indexes for table `appointment_treatments`
--
ALTER TABLE `appointment_treatments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_treatment_appointment` (`appointment_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bills_appointment` (`appointment_id`),
  ADD KEY `idx_bills_clinic` (`clinic_id`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bill_items_bill` (`bill_id`),
  ADD KEY `idx_bill_items_price_item` (`price_list_item_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `case_sheets`
--
ALTER TABLE `case_sheets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_sheets_appointment_id_unique` (`appointment_id`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clinic_org` (`organisation_id`);

--
-- Indexes for table `clinic_brands`
--
ALTER TABLE `clinic_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_clinic_brand` (`clinic_id`,`brand_id`);

--
-- Indexes for table `clinic_inventory`
--
ALTER TABLE `clinic_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_clinic_item` (`clinic_id`,`inventory_item_id`),
  ADD KEY `idx_clinic_inventory_clinic` (`clinic_id`),
  ADD KEY `idx_clinic_inventory_item` (`inventory_item_id`);

--
-- Indexes for table `clinic_user_assignments`
--
ALTER TABLE `clinic_user_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinic_user_assignments_user_id_clinic_id_unique` (`user_id`,`clinic_id`),
  ADD KEY `clinic_user_assignments_clinic_id_foreign` (`clinic_id`);

--
-- Indexes for table `clinic_vet`
--
ALTER TABLE `clinic_vet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinic_vet_clinic_id_vet_id_unique` (`clinic_id`,`vet_id`),
  ADD KEY `clinic_vet_vet_id_foreign` (`vet_id`),
  ADD KEY `idx_clinic_vet_active` (`clinic_id`,`is_active`);

--
-- Indexes for table `diagnostic_files`
--
ALTER TABLE `diagnostic_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_diag_files_report` (`diagnostic_report_id`);

--
-- Indexes for table `diagnostic_reports`
--
ALTER TABLE `diagnostic_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_diag_appointment` (`appointment_id`),
  ADD KEY `fk_diag_pet` (`pet_id`),
  ADD KEY `fk_diag_clinic` (`clinic_id`),
  ADD KEY `fk_diag_vet` (`vet_id`);

--
-- Indexes for table `drug_brands`
--
ALTER TABLE `drug_brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drug_brand_generic` (`generic_id`);

--
-- Indexes for table `drug_dosages`
--
ALTER TABLE `drug_dosages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drug_dosages_generic` (`generic_id`);

--
-- Indexes for table `drug_generics`
--
ALTER TABLE `drug_generics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_generic_name` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_batches_item` (`inventory_item_id`),
  ADD KEY `idx_inventory_batches_clinic` (`clinic_id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_org` (`organisation_id`),
  ADD KEY `idx_inventory_drug_brand` (`drug_brand_id`);

--
-- Indexes for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inventory_movements_clinic` (`clinic_id`),
  ADD KEY `idx_inventory_movements_item` (`inventory_item_id`),
  ADD KEY `idx_inventory_movements_batch` (`inventory_batch_id`),
  ADD KEY `idx_inventory_movements_type` (`movement_type`);

--
-- Indexes for table `inventory_usage_logs`
--
ALTER TABLE `inventory_usage_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usage_batch` (`inventory_batch_id`),
  ADD KEY `idx_usage_treatment` (`appointment_treatment_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organisation_roles`
--
ALTER TABLE `organisation_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_org` (`organisation_id`);

--
-- Indexes for table `organisation_user_roles`
--
ALTER TABLE `organisation_user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_org` (`organisation_id`),
  ADD KEY `idx_clinic` (`clinic_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_role_id` (`role_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pets_pet_parent_id_foreign` (`pet_parent_id`);

--
-- Indexes for table `pet_parents`
--
ALTER TABLE `pet_parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pet_parent_access_otps`
--
ALTER TABLE `pet_parent_access_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_parent_access_otps_pet_parent_id_foreign` (`pet_parent_id`),
  ADD KEY `pet_parent_access_otps_clinic_id_foreign` (`clinic_id`);

--
-- Indexes for table `pet_parent_clinic_access`
--
ALTER TABLE `pet_parent_clinic_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_parent_clinic_access_pet_parent_id_foreign` (`pet_parent_id`),
  ADD KEY `pet_parent_clinic_access_clinic_id_foreign` (`clinic_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescriptions_appointment_id_unique` (`appointment_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_items_prescription_id_foreign` (`prescription_id`);

--
-- Indexes for table `price_lists`
--
ALTER TABLE `price_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `price_lists_org_active_idx` (`organisation_id`,`is_active`);

--
-- Indexes for table `price_list_items`
--
ALTER TABLE `price_list_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `price_list_items_price_list_id_idx` (`price_list_id`),
  ADD KEY `idx_price_list_drug` (`drug_brand_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_id` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_clinic_id_foreign` (`clinic_id`),
  ADD KEY `users_vet_id_foreign` (`vet_id`);

--
-- Indexes for table `vets`
--
ALTER TABLE `vets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vets_phone_unique` (`phone`),
  ADD UNIQUE KEY `vets_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `appointment_treatments`
--
ALTER TABLE `appointment_treatments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `case_sheets`
--
ALTER TABLE `case_sheets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `clinic_brands`
--
ALTER TABLE `clinic_brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clinic_inventory`
--
ALTER TABLE `clinic_inventory`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clinic_user_assignments`
--
ALTER TABLE `clinic_user_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `clinic_vet`
--
ALTER TABLE `clinic_vet`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `diagnostic_files`
--
ALTER TABLE `diagnostic_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `diagnostic_reports`
--
ALTER TABLE `diagnostic_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `drug_brands`
--
ALTER TABLE `drug_brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `drug_dosages`
--
ALTER TABLE `drug_dosages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `drug_generics`
--
ALTER TABLE `drug_generics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_usage_logs`
--
ALTER TABLE `inventory_usage_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `organisations`
--
ALTER TABLE `organisations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `organisation_roles`
--
ALTER TABLE `organisation_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `organisation_user_roles`
--
ALTER TABLE `organisation_user_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pet_parents`
--
ALTER TABLE `pet_parents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pet_parent_access_otps`
--
ALTER TABLE `pet_parent_access_otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pet_parent_clinic_access`
--
ALTER TABLE `pet_parent_clinic_access`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `price_lists`
--
ALTER TABLE `price_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `price_list_items`
--
ALTER TABLE `price_list_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `vets`
--
ALTER TABLE `vets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_vet_id_foreign` FOREIGN KEY (`vet_id`) REFERENCES `vets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `case_sheets`
--
ALTER TABLE `case_sheets`
  ADD CONSTRAINT `case_sheets_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinics`
--
ALTER TABLE `clinics`
  ADD CONSTRAINT `fk_clinic_org` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_user_assignments`
--
ALTER TABLE `clinic_user_assignments`
  ADD CONSTRAINT `clinic_user_assignments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_user_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_vet`
--
ALTER TABLE `clinic_vet`
  ADD CONSTRAINT `clinic_vet_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_vet_vet_id_foreign` FOREIGN KEY (`vet_id`) REFERENCES `vets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diagnostic_files`
--
ALTER TABLE `diagnostic_files`
  ADD CONSTRAINT `fk_diag_files_report` FOREIGN KEY (`diagnostic_report_id`) REFERENCES `diagnostic_reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diagnostic_reports`
--
ALTER TABLE `diagnostic_reports`
  ADD CONSTRAINT `fk_diag_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_diag_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_diag_pet` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_diag_vet` FOREIGN KEY (`vet_id`) REFERENCES `vets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drug_dosages`
--
ALTER TABLE `drug_dosages`
  ADD CONSTRAINT `fk_drug_dosages_generic` FOREIGN KEY (`generic_id`) REFERENCES `drug_generics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD CONSTRAINT `fk_inventory_batches_item` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_usage_logs`
--
ALTER TABLE `inventory_usage_logs`
  ADD CONSTRAINT `fk_usage_batch` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `organisation_user_roles`
--
ALTER TABLE `organisation_user_roles`
  ADD CONSTRAINT `fk_our_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`),
  ADD CONSTRAINT `fk_our_org` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`),
  ADD CONSTRAINT `fk_our_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `organisation_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_pet_parent_id_foreign` FOREIGN KEY (`pet_parent_id`) REFERENCES `pet_parents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pet_parent_access_otps`
--
ALTER TABLE `pet_parent_access_otps`
  ADD CONSTRAINT `pet_parent_access_otps_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pet_parent_access_otps_pet_parent_id_foreign` FOREIGN KEY (`pet_parent_id`) REFERENCES `pet_parents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pet_parent_clinic_access`
--
ALTER TABLE `pet_parent_clinic_access`
  ADD CONSTRAINT `pet_parent_clinic_access_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pet_parent_clinic_access_pet_parent_id_foreign` FOREIGN KEY (`pet_parent_id`) REFERENCES `pet_parents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `price_lists`
--
ALTER TABLE `price_lists`
  ADD CONSTRAINT `price_lists_org_fk` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `price_list_items`
--
ALTER TABLE `price_list_items`
  ADD CONSTRAINT `price_list_items_list_fk` FOREIGN KEY (`price_list_id`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `organisation_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_vet_id_foreign` FOREIGN KEY (`vet_id`) REFERENCES `vets` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
