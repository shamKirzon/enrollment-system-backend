-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 08, 2024 at 06:34 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enrollment`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `start_at` date NOT NULL,
  `end_at` date NOT NULL,
  `status` enum('upcoming','open','ongoing','finished') NOT NULL DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `start_at`, `end_at`, `status`) VALUES
(3, '2023-08-05', '2024-05-01', 'open'),
(4, '2024-08-05', '2025-05-01', 'open'),
(5, '2025-08-05', '2026-05-01', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `street` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `country`, `region`, `province`, `city`, `barangay`, `street`) VALUES
(1, 'Philippines', 'NCR', 'Province', 'Taguig City', 'East Rembo', NULL),
(4, 'Philippines', 'NCR', '4th District', 'Taguig City', 'East Rembo', '111-Z 99th Avenue'),
(5, 'Philippines', 'NCR', '4th District', 'Taguig City', 'West Rembo', NULL),
(6, 'Philippines', 'NCR', '4th District', 'Taguig', 'East Rembo', '121-Z'),
(7, 'dasadsasd', 'asdasdads', 'adsdas', 'dasdas', 'asdads', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enrolled_tuition_plans`
--

CREATE TABLE `enrolled_tuition_plans` (
  `id` char(36) NOT NULL,
  `enrollment_id` char(36) NOT NULL,
  `tuition_plan_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` char(36) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','done') NOT NULL DEFAULT 'pending',
  `student_id` char(36) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_level_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `enrolled_at`, `status`, `student_id`, `academic_year_id`, `year_level_id`) VALUES
('4882ccdb-24e9-11ef-9890-00e18ce201d5', '2023-06-07 16:16:23', 'done', 'edcb084a-197b-11ef-a11c-00e18ce201d5', 4, 'g11'),
('cf3789f0-24a6-11ef-b6e2-00e18ce201d5', '2022-06-07 08:20:33', 'done', 'edcb084a-197b-11ef-a11c-00e18ce201d5', 3, 'g10');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_discounts`
--

CREATE TABLE `enrollment_discounts` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percentage` decimal(6,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_discount_applications`
--

CREATE TABLE `enrollment_discount_applications` (
  `id` char(36) NOT NULL,
  `enrollment_id` char(36) NOT NULL,
  `enrollment_discount_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_fees`
--

CREATE TABLE `enrollment_fees` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_fees`
--

INSERT INTO `enrollment_fees` (`id`, `name`) VALUES
('computer-fee', 'Computer Fee'),
('energy-fee', 'Energy Fee'),
('instructional-material', 'Instructional Material'),
('regular-and-other-school-fee', 'Regular and Other School Fee'),
('tuition-fee', 'Tuition Fee');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_fee_levels`
--

CREATE TABLE `enrollment_fee_levels` (
  `id` varchar(255) NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL,
  `enrollment_fee_id` varchar(255) NOT NULL,
  `year_level_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_fee_levels`
--

INSERT INTO `enrollment_fee_levels` (`id`, `amount`, `enrollment_fee_id`, `year_level_id`) VALUES
('computer-fee-g10', 5000.00, 'computer-fee', 'g10'),
('computer-fee-g7', 1200.00, 'computer-fee', 'g7'),
('computer-fee-g8', 1300.00, 'computer-fee', 'g8'),
('computer-fee-g9', 1300.00, 'computer-fee', 'g9'),
('energy-fee-g10', 2500.00, 'energy-fee', 'g10'),
('energy-fee-g7', 2500.00, 'energy-fee', 'g7'),
('energy-fee-g8', 2500.00, 'energy-fee', 'g8'),
('energy-fee-g9', 2500.00, 'energy-fee', 'g9'),
('instructional-material-g10', 12985.00, 'instructional-material', 'g10'),
('instructional-material-g7', 8935.00, 'instructional-material', 'g7'),
('instructional-material-g8', 8935.00, 'instructional-material', 'g8'),
('instructional-material-g9', 10785.00, 'instructional-material', 'g9'),
('regular-and-other-school-fee-g10', 4584.00, 'regular-and-other-school-fee', 'g10'),
('regular-and-other-school-fee-g7', 4680.00, 'regular-and-other-school-fee', 'g7'),
('regular-and-other-school-fee-g8', 4325.00, 'regular-and-other-school-fee', 'g8'),
('regular-and-other-school-fee-g9', 4584.00, 'regular-and-other-school-fee', 'g9'),
('tuition-fee-g10', 17955.00, 'tuition-fee', 'g10'),
('tuition-fee-g7', 19685.90, 'tuition-fee', 'g7'),
('tuition-fee-g8', 18334.05, 'tuition-fee', 'g8'),
('tuition-fee-g9', 17955.00, 'tuition-fee', 'g9');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_strands`
--

CREATE TABLE `enrollment_strands` (
  `id` varchar(150) NOT NULL,
  `enrollment_id` char(36) NOT NULL,
  `strand_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_strands`
--

INSERT INTO `enrollment_strands` (`id`, `enrollment_id`, `strand_id`) VALUES
('stem-4882ccdb-24e9-11ef-9890-00e18ce201d5', '4882ccdb-24e9-11ef-9890-00e18ce201d5', 'stem');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_transactions`
--

CREATE TABLE `enrollment_transactions` (
  `id` int(11) NOT NULL,
  `enrollment_id` char(36) NOT NULL,
  `transaction_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_transactions`
--

INSERT INTO `enrollment_transactions` (`id`, `enrollment_id`, `transaction_id`) VALUES
(24, 'cf3789f0-24a6-11ef-b6e2-00e18ce201d5', 'cf360d52-24a6-11ef-b6e2-00e18ce201d5'),
(25, '4882ccdb-24e9-11ef-9890-00e18ce201d5', '48820d6a-24e9-11ef-9890-00e18ce201d5');

-- --------------------------------------------------------

--
-- Table structure for table `parent_student_links`
--

CREATE TABLE `parent_student_links` (
  `id` char(36) NOT NULL,
  `parent_id` char(36) NOT NULL,
  `student_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parent_student_links`
--

INSERT INTO `parent_student_links` (`id`, `parent_id`, `student_id`) VALUES
('19a174dc-19a6-11ef-ac3d-00e18ce201d5', 'f93a2ed0-19a5-11ef-ac3d-00e18ce201d5', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
('3244e16c-2516-11ef-9890-00e18ce201d5', 'f93a2ed0-19a5-11ef-ac3d-00e18ce201d5', '3241660d-2516-11ef-9890-00e18ce201d5'),
('6c4fcc13-2158-11ef-a4a2-0242a5745a66', 'f93a2ed0-19a5-11ef-ac3d-00e18ce201d5', '6c4a7c00-2158-11ef-a4a2-0242a5745a66'),
('97fd2648-2156-11ef-a4a2-0242a5745a66', 'f93a2ed0-19a5-11ef-ac3d-00e18ce201d5', '57790e94-2156-11ef-a4a2-0242a5745a66');

-- --------------------------------------------------------

--
-- Table structure for table `payment_accounts`
--

CREATE TABLE `payment_accounts` (
  `id` varchar(255) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `account_number` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_accounts`
--

INSERT INTO `payment_accounts` (`id`, `account_name`, `account_number`) VALUES
('pateros-catholic-school-12345679', 'Pateros Catholic School', '12345679');

-- --------------------------------------------------------

--
-- Table structure for table `payment_modes`
--

CREATE TABLE `payment_modes` (
  `id` varchar(50) NOT NULL,
  `payment_channel` varchar(100) NOT NULL,
  `payment_account_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_modes`
--

INSERT INTO `payment_modes` (`id`, `payment_channel`, `payment_account_id`) VALUES
('bpi-pateros-catholic-school-12345679', 'BPI', 'pateros-catholic-school-12345679');

-- --------------------------------------------------------

--
-- Table structure for table `report_cards`
--

CREATE TABLE `report_cards` (
  `id` int(11) NOT NULL,
  `report_card_url` text NOT NULL,
  `enrollment_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_cards`
--

INSERT INTO `report_cards` (`id`, `report_card_url`, `enrollment_id`) VALUES
(10, '/storage/report-cards/STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_REPORT-CARD.jpg', 'cf3789f0-24a6-11ef-b6e2-00e18ce201d5');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`) VALUES
('agatha-of-sicily', 'Agatha of Sicily'),
('alexandria', 'Alexandria'),
('andrew', 'Andrew'),
('callistus', 'Callistus'),
('ignatius-of-loyola', 'Ignatius of Loyola'),
('jerome', 'Jerome'),
('laurence', 'Laurence'),
('pedro-calungsod', 'Pedro Calungsod');

-- --------------------------------------------------------

--
-- Table structure for table `section_assignments`
--

CREATE TABLE `section_assignments` (
  `id` int(11) NOT NULL,
  `enrollment_id` char(36) NOT NULL,
  `section_level_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_assignments`
--

INSERT INTO `section_assignments` (`id`, `enrollment_id`, `section_level_id`) VALUES
(5, 'cf3789f0-24a6-11ef-b6e2-00e18ce201d5', 'andrew-g10');

-- --------------------------------------------------------

--
-- Table structure for table `section_assignment_strands`
--

CREATE TABLE `section_assignment_strands` (
  `id` int(11) NOT NULL,
  `section_assignment_id` int(11) NOT NULL,
  `strand_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_levels`
--

CREATE TABLE `section_levels` (
  `id` varchar(100) NOT NULL,
  `section_id` varchar(50) NOT NULL,
  `year_level_id` varchar(50) NOT NULL,
  `adviser_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_levels`
--

INSERT INTO `section_levels` (`id`, `section_id`, `year_level_id`, `adviser_id`) VALUES
('agatha-of-sicily-g11', 'agatha-of-sicily', 'g11', NULL),
('alexandria-g12', 'alexandria', 'g11', NULL),
('andrew-g10', 'andrew', 'g10', NULL),
('callistus-g7', 'callistus', 'g8', '71b7dc45-197a-11ef-a11c-00e18ce201d5'),
('ignatius-of-loyola-g9', 'ignatius-of-loyola', 'g9', NULL),
('jerome-g9', 'jerome', 'g9', NULL),
('laurence-g6', 'laurence', 'g6', NULL),
('pedro-calungsod-g11', 'pedro-calungsod', 'g11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `section_strands`
--

CREATE TABLE `section_strands` (
  `id` varchar(100) NOT NULL,
  `section_level_id` varchar(100) NOT NULL,
  `strand_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_strands`
--

INSERT INTO `section_strands` (`id`, `section_level_id`, `strand_id`) VALUES
('agatha-of-sicily-g11-stem', 'agatha-of-sicily-g11', 'stem'),
('alexandria-g12-humss', 'alexandria-g12', 'ad'),
('pedro-calungsod-g11-stem', 'pedro-calungsod-g11', 'abm');

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `id` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`id`, `name`) VALUES
('abm', 'Accountancy, Business, and Management'),
('ad', 'Arts and Design'),
('gas', 'General Academic Strand'),
('humss', 'Humanities and Social Sciences'),
('stem', 'Science, Technology, Engineering, and Mathematics'),
('tvl', 'Technical-Vocational Livelihood');

-- --------------------------------------------------------

--
-- Table structure for table `student_family_members`
--

CREATE TABLE `student_family_members` (
  `id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix_name` varchar(255) DEFAULT NULL,
  `relationship` enum('mother','father','guardian') NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `address_id` int(11) NOT NULL,
  `student_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_family_members`
--

INSERT INTO `student_family_members` (`id`, `first_name`, `middle_name`, `last_name`, `suffix_name`, `relationship`, `occupation`, `address_id`, `student_id`) VALUES
('5e7959f5-199b-11ef-ac3d-00e18ce201d5', 'Aaron', NULL, 'Melendres', NULL, 'father', 'Professional E-Sports Player', 1, 'edcb084a-197b-11ef-a11c-00e18ce201d5');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `id` int(11) NOT NULL,
  `grade` decimal(6,3) UNSIGNED NOT NULL,
  `period` enum('1','2','3','4') NOT NULL,
  `subject_level_id` varchar(50) NOT NULL,
  `student_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`id`, `grade`, `period`, `subject_level_id`, `student_id`) VALUES
(71, 85.000, '2', 'FIL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(72, 89.000, '3', 'FIL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(73, 89.000, '4', 'FIL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(74, 97.000, '1', 'MATH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(75, 99.000, '2', 'MATH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(76, 99.000, '3', 'MATH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(77, 98.000, '4', 'MATH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(78, 94.000, '1', 'MAPEH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(79, 95.000, '2', 'MAPEH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(80, 93.000, '3', 'MAPEH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(81, 95.000, '4', 'MAPEH-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(82, 95.000, '1', 'SCI-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(83, 94.000, '2', 'SCI-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(84, 93.000, '3', 'SCI-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(85, 94.000, '4', 'SCI-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(86, 95.000, '1', 'TLE-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(87, 96.000, '2', 'TLE-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(88, 97.000, '3', 'TLE-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(89, 97.000, '4', 'TLE-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(99, 89.000, '1', 'AP-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(100, 89.000, '2', 'AP-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(101, 89.000, '3', 'AP-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(102, 87.000, '4', 'AP-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(103, 90.000, '1', 'CL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(104, 92.000, '2', 'CL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(105, 89.000, '3', 'CL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(106, 91.000, '4', 'CL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(107, 93.000, '1', 'ELEC-A-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(108, 97.000, '2', 'ELEC-A-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(109, 95.000, '3', 'ELEC-A-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(110, 95.000, '4', 'ELEC-A-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(111, 97.000, '1', 'ELEC-B-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(112, 97.000, '2', 'ELEC-B-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(113, 98.000, '3', 'ELEC-B-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(114, 95.000, '4', 'ELEC-B-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(115, 92.000, '1', 'ENG-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(116, 91.000, '2', 'ENG-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(117, 95.000, '3', 'ENG-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(118, 94.000, '4', 'ENG-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
(119, 94.000, '1', 'FIL-g10', 'edcb084a-197b-11ef-a11c-00e18ce201d5');

-- --------------------------------------------------------

--
-- Table structure for table `student_grade_strands`
--

CREATE TABLE `student_grade_strands` (
  `id` int(11) NOT NULL,
  `student_grade_id` int(11) NOT NULL,
  `strand_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` char(36) NOT NULL,
  `lrn` varchar(20) NOT NULL,
  `birth_date` date NOT NULL,
  `birth_place` text NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `citizenship` varchar(100) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `parent_contact_number` varchar(20) NOT NULL,
  `landline` varchar(20) NOT NULL,
  `birth_certificate_url` text NOT NULL,
  `baptismal_certificate_url` text NOT NULL,
  `address_id` int(11) NOT NULL,
  `student_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `lrn`, `birth_date`, `birth_place`, `sex`, `citizenship`, `religion`, `parent_contact_number`, `landline`, `birth_certificate_url`, `baptismal_certificate_url`, `address_id`, `student_id`) VALUES
('32443d22-2516-11ef-9890-00e18ce201d5', '111111111111', '2024-03-01', 'Taguig City', 'male', 'Filipino', 'Roman Catholic', '190111', '190111', '/storage/birth-certificates/STUDENT_3241660d-2516-11ef-9890-00e18ce201d5_BIRTH-CERTIFICATE.jpg', '/storage/baptismal-certificates/STUDENT_3241660d-2516-11ef-9890-00e18ce201d5_BAPTISMAL-CERTIFICATE.jpg', 6, '3241660d-2516-11ef-9890-00e18ce201d5'),
('3e4a8a7c-199d-11ef-ac3d-00e18ce201d5', '123456654321', '2005-03-27', 'Makati City', 'female', 'Filipino', 'Roman Catholic', '111', '222', 'https://www.j-14.com/wp-content/uploads/2023/07/newjeans-hanni.-.jpg?resize=1200%2C630&quality=86&strip=all', 'https://upload.wikimedia.org/wikipedia/commons/6/6a/20230921_Newjeans_Hanni_%ED%8B%B0%EB%B9%84%ED%85%90_01.jpg', 1, 'edcb084a-197b-11ef-a11c-00e18ce201d5'),
('577c6be0-2156-11ef-a4a2-0242a5745a66', '407299111111', '2024-06-01', 'House ni Shammy', 'male', 'Filipino', 'Roman Catholic', '911', '911', '/storage/birth-certificates/STUDENT_57790e94-2156-11ef-a4a2-0242a5745a66_BIRTH-CERTIFICATE.jpg', '/storage/baptismal-certificates/STUDENT_57790e94-2156-11ef-a4a2-0242a5745a66_BAPTISMAL-CERTIFICATE.jpg', 4, '57790e94-2156-11ef-a4a2-0242a5745a66'),
('6c4e49c2-2158-11ef-a4a2-0242a5745a66', '407299999999', '2024-05-01', 'Tokyo, Japan', 'male', 'Filipino', 'Roman Catholic', '222', '222', '/storage/birth-certificates/STUDENT_6c4a7c00-2158-11ef-a4a2-0242a5745a66_BIRTH-CERTIFICATE.jpg', '/storage/baptismal-certificates/STUDENT_6c4a7c00-2158-11ef-a4a2-0242a5745a66_BAPTISMAL-CERTIFICATE.jpg', 5, '6c4a7c00-2158-11ef-a4a2-0242a5745a66'),
('e06c4724-251e-11ef-9890-00e18ce201d5', '43232', '2024-06-08', 'asddas', 'male', 'adsdsa', 'sdadas', '233223', '232332', '/storage/birth-certificates/STUDENT_e06758b2-251e-11ef-9890-00e18ce201d5_BIRTH-CERTIFICATE.jpg', '/storage/baptismal-certificates/STUDENT_e06758b2-251e-11ef-9890-00e18ce201d5_BAPTISMAL-CERTIFICATE.jpg', 7, 'e06758b2-251e-11ef-9890-00e18ce201d5');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
('AP', 'Araling Panlipunan'),
('BASICCAL', 'Basic Calculus'),
('CAPSTONE', 'Capstone Project'),
('CL', 'Christian Living'),
('CL-SHS', 'Christian Living'),
('DRRR', 'Disaster Readiness & Risk Reduction'),
('EAPP', 'English for Academic and Professional Purposes'),
('ELEC-A', 'Elective A'),
('ELEC-B', 'Elective B'),
('ENG', 'English'),
('ENTREP', 'Entrepreneurship'),
('FIL', 'Filipino'),
('GENBIO', 'General Biology'),
('GENCHEM1', 'General Chemistry'),
('GENCHEM2', 'General Chemistry'),
('GENMATH', 'General Mathematics'),
('GENPHYSICS', 'General Physics'),
('INTROPHILO', 'Introduction to Philosophy of Human Person'),
('KOMPAN', 'Komunikasyon at Pananaliksik'),
('LITPHIL', '21st Century Literature from the Philippines'),
('MAPEH', 'Music, Arts, P.E., & Health'),
('MATH', 'Mathematics'),
('ORALCOM', 'Oral Communication in Context'),
('PAGSULAT', 'Pagsulat sa Filipino sa Piling Larangan'),
('PEH', 'Physical Education and Health'),
('PERDEV', 'Personal Development'),
('PHILO', 'Philosophy'),
('PRECAL', 'Pre-Calculus'),
('RW', 'Reading and Writing Skills'),
('SCI', 'Science'),
('STAT', 'Statistics and Probability'),
('STEM-ELEC', 'STEM Elective'),
('TEST-AGAIN', 'Test Again'),
('TEST-SHS', 'Test SHS'),
('TLE', 'Technological Livelihood Education'),
('TSET', 'Test'),
('UCSP', 'Understanding Culture, Society, and Politics');

-- --------------------------------------------------------

--
-- Table structure for table `subject_levels`
--

CREATE TABLE `subject_levels` (
  `id` varchar(50) NOT NULL,
  `subject_id` varchar(50) NOT NULL,
  `year_level_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_levels`
--

INSERT INTO `subject_levels` (`id`, `subject_id`, `year_level_id`) VALUES
('AP-g1', 'AP', 'g1'),
('AP-g10', 'AP', 'g10'),
('AP-g2', 'AP', 'g2'),
('AP-g3', 'AP', 'g3'),
('AP-g4', 'AP', 'g4'),
('AP-g5', 'AP', 'g5'),
('AP-g6', 'AP', 'g6'),
('AP-g7', 'AP', 'g7'),
('AP-g8', 'AP', 'g8'),
('AP-g9', 'AP', 'g9'),
('BASICCAL-g11', 'BASICCAL', 'g11'),
('CAPSTONE-g12', 'CAPSTONE', 'g12'),
('CL-g1', 'CL', 'g1'),
('CL-g10', 'CL', 'g10'),
('CL-g2', 'CL', 'g2'),
('CL-g3', 'CL', 'g3'),
('CL-g4', 'CL', 'g4'),
('CL-g5', 'CL', 'g5'),
('CL-g6', 'CL', 'g6'),
('CL-g7', 'CL', 'g7'),
('CL-g8', 'CL', 'g8'),
('CL-g9', 'CL', 'g9'),
('CL-SHS-g11', 'CL-SHS', 'g11'),
('CL-SHS-g12', 'CL-SHS', 'g12'),
('DRRR-g11', 'DRRR', 'g11'),
('EAPP-g12', 'EAPP', 'g12'),
('ELEC-A-g10', 'ELEC-A', 'g10'),
('ELEC-A-g7', 'ELEC-A', 'g7'),
('ELEC-A-g8', 'ELEC-A', 'g8'),
('ELEC-A-g9', 'ELEC-A', 'g9'),
('ELEC-B-g10', 'ELEC-B', 'g10'),
('ELEC-B-g7', 'ELEC-B', 'g7'),
('ELEC-B-g8', 'ELEC-B', 'g8'),
('ELEC-B-g9', 'ELEC-B', 'g9'),
('ENG-g1', 'ENG', 'g1'),
('ENG-g10', 'ENG', 'g10'),
('ENG-g2', 'ENG', 'g2'),
('ENG-g3', 'ENG', 'g3'),
('ENG-g4', 'ENG', 'g4'),
('ENG-g5', 'ENG', 'g5'),
('ENG-g6', 'ENG', 'g6'),
('ENG-g7', 'ENG', 'g7'),
('ENG-g8', 'ENG', 'g8'),
('ENG-g9', 'ENG', 'g9'),
('ENTREP-g12', 'ENTREP', 'g12'),
('FIL-g1', 'FIL', 'g1'),
('FIL-g10', 'FIL', 'g10'),
('FIL-g2', 'FIL', 'g2'),
('FIL-g3', 'FIL', 'g3'),
('FIL-g4', 'FIL', 'g4'),
('FIL-g5', 'FIL', 'g5'),
('FIL-g6', 'FIL', 'g6'),
('FIL-g7', 'FIL', 'g7'),
('FIL-g8', 'FIL', 'g8'),
('FIL-g9', 'FIL', 'g9'),
('GENBIO-g12', 'GENBIO', 'g12'),
('GENCHEM1-g11', 'GENCHEM1', 'g11'),
('GENCHEM2-g12', 'GENCHEM2', 'g12'),
('GENMATH-g11', 'GENMATH', 'g11'),
('GENPHYSICS-g12', 'GENPHYSICS', 'g12'),
('INTROPHILO-g11', 'INTROPHILO', 'g11'),
('KOMPAN-g11', 'KOMPAN', 'g11'),
('LITPHIL-g12', 'LITPHIL', 'g12'),
('MAPEH-g1', 'MAPEH', 'g1'),
('MAPEH-g10', 'MAPEH', 'g10'),
('MAPEH-g2', 'MAPEH', 'g2'),
('MAPEH-g3', 'MAPEH', 'g3'),
('MAPEH-g4', 'MAPEH', 'g4'),
('MAPEH-g5', 'MAPEH', 'g5'),
('MAPEH-g6', 'MAPEH', 'g6'),
('MAPEH-g7', 'MAPEH', 'g7'),
('MAPEH-g8', 'MAPEH', 'g8'),
('MAPEH-g9', 'MAPEH', 'g9'),
('MATH-g1', 'MATH', 'g1'),
('MATH-g10', 'MATH', 'g10'),
('MATH-g2', 'MATH', 'g2'),
('MATH-g3', 'MATH', 'g3'),
('MATH-g4', 'MATH', 'g4'),
('MATH-g5', 'MATH', 'g5'),
('MATH-g6', 'MATH', 'g6'),
('MATH-g7', 'MATH', 'g7'),
('MATH-g8', 'MATH', 'g8'),
('MATH-g9', 'MATH', 'g9'),
('ORALCOM-g11', 'ORALCOM', 'g11'),
('PAGSULAT-g12', 'PAGSULAT', 'g12'),
('PEH-g12', 'PEH', 'g12'),
('PERDEV-g11', 'PERDEV', 'g11'),
('PHILO-g11', 'PHILO', 'g11'),
('PRECAL-g11', 'PRECAL', 'g11'),
('RW-g11', 'RW', 'g11'),
('SCI-g1', 'SCI', 'g1'),
('SCI-g10', 'SCI', 'g10'),
('SCI-g2', 'SCI', 'g2'),
('SCI-g3', 'SCI', 'g3'),
('SCI-g4', 'SCI', 'g4'),
('SCI-g5', 'SCI', 'g5'),
('SCI-g6', 'SCI', 'g6'),
('SCI-g7', 'SCI', 'g7'),
('SCI-g8', 'SCI', 'g8'),
('SCI-g9', 'SCI', 'g9'),
('STAT-g11', 'STAT', 'g11'),
('STEM-ELEC-g11', 'STEM-ELEC', 'g11'),
('STEM-ELEC-g12', 'STEM-ELEC', 'g12'),
('TEST-AGAIN-g1', 'TEST-AGAIN', 'g1'),
('TEST-AGAIN-g2', 'TEST-AGAIN', 'g2'),
('TEST-AGAIN-g3', 'TEST-AGAIN', 'g3'),
('TEST-AGAIN-g4', 'TEST-AGAIN', 'g4'),
('TEST-AGAIN-g5', 'TEST-AGAIN', 'g5'),
('TEST-AGAIN-g6', 'TEST-AGAIN', 'g6'),
('TEST-SHS-g11', 'TEST-SHS', 'g11'),
('TLE-g1', 'TLE', 'g1'),
('TLE-g10', 'TLE', 'g10'),
('TLE-g2', 'TLE', 'g2'),
('TLE-g3', 'TLE', 'g3'),
('TLE-g4', 'TLE', 'g4'),
('TLE-g5', 'TLE', 'g5'),
('TLE-g6', 'TLE', 'g6'),
('TLE-g7', 'TLE', 'g7'),
('TLE-g8', 'TLE', 'g8'),
('TLE-g9', 'TLE', 'g9'),
('UCSP-g12', 'UCSP', 'g12');

-- --------------------------------------------------------

--
-- Table structure for table `subject_strands`
--

CREATE TABLE `subject_strands` (
  `id` varchar(50) NOT NULL,
  `subject_level_id` varchar(50) NOT NULL,
  `strand_id` varchar(100) NOT NULL,
  `semester` enum('1','2') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_strands`
--

INSERT INTO `subject_strands` (`id`, `subject_level_id`, `strand_id`, `semester`) VALUES
('BASICCAL-g11-stem-2', 'BASICCAL-g11', 'stem', '2'),
('CAPSTONE-g12-abm-2', 'CAPSTONE-g12', 'abm', '2'),
('CAPSTONE-g12-ad-2', 'CAPSTONE-g12', 'ad', '2'),
('CAPSTONE-g12-gas-2', 'CAPSTONE-g12', 'gas', '2'),
('CAPSTONE-g12-humss-2', 'CAPSTONE-g12', 'humss', '2'),
('CAPSTONE-g12-stem-2', 'CAPSTONE-g12', 'stem', '2'),
('CAPSTONE-g12-tvl-2', 'CAPSTONE-g12', 'tvl', '2'),
('CL-SHS-g11-abm-1', 'CL-SHS-g11', 'abm', '1'),
('CL-SHS-g11-abm-2', 'CL-SHS-g11', 'abm', '2'),
('CL-SHS-g11-ad-1', 'CL-SHS-g11', 'ad', '1'),
('CL-SHS-g11-ad-2', 'CL-SHS-g11', 'ad', '2'),
('CL-SHS-g11-gas-1', 'CL-SHS-g11', 'gas', '1'),
('CL-SHS-g11-gas-2', 'CL-SHS-g11', 'gas', '2'),
('CL-SHS-g11-humss-1', 'CL-SHS-g11', 'humss', '1'),
('CL-SHS-g11-humss-2', 'CL-SHS-g11', 'humss', '2'),
('CL-SHS-g11-stem-1', 'CL-SHS-g11', 'stem', '1'),
('CL-SHS-g11-stem-2', 'CL-SHS-g11', 'stem', '2'),
('CL-SHS-g11-tvl-1', 'CL-SHS-g11', 'tvl', '1'),
('CL-SHS-g11-tvl-2', 'CL-SHS-g11', 'tvl', '2'),
('CL-SHS-g12-abm-1', 'CL-SHS-g12', 'abm', '1'),
('CL-SHS-g12-abm-2', 'CL-SHS-g12', 'abm', '2'),
('CL-SHS-g12-ad-1', 'CL-SHS-g12', 'ad', '1'),
('CL-SHS-g12-ad-2', 'CL-SHS-g12', 'ad', '2'),
('CL-SHS-g12-gas-1', 'CL-SHS-g12', 'gas', '1'),
('CL-SHS-g12-gas-2', 'CL-SHS-g12', 'gas', '2'),
('CL-SHS-g12-humss-1', 'CL-SHS-g12', 'humss', '1'),
('CL-SHS-g12-humss-2', 'CL-SHS-g12', 'humss', '2'),
('CL-SHS-g12-stem-1', 'CL-SHS-g12', 'stem', '1'),
('CL-SHS-g12-stem-2', 'CL-SHS-g12', 'stem', '2'),
('CL-SHS-g12-tvl-1', 'CL-SHS-g12', 'tvl', '1'),
('CL-SHS-g12-tvl-2', 'CL-SHS-g12', 'tvl', '2'),
('DRRR-g11-abm-2', 'DRRR-g11', 'abm', '2'),
('DRRR-g11-ad-2', 'DRRR-g11', 'ad', '2'),
('DRRR-g11-gas-2', 'DRRR-g11', 'gas', '2'),
('DRRR-g11-humss-2', 'DRRR-g11', 'humss', '2'),
('DRRR-g11-stem-2', 'DRRR-g11', 'stem', '2'),
('DRRR-g11-tvl-2', 'DRRR-g11', 'tvl', '2'),
('EAPP-g12-abm-1', 'EAPP-g12', 'abm', '1'),
('EAPP-g12-ad-1', 'EAPP-g12', 'ad', '1'),
('EAPP-g12-gas-1', 'EAPP-g12', 'gas', '1'),
('EAPP-g12-humss-1', 'EAPP-g12', 'humss', '1'),
('EAPP-g12-stem-1', 'EAPP-g12', 'stem', '1'),
('EAPP-g12-tvl-1', 'EAPP-g12', 'tvl', '1'),
('ENTREP-g12-abm-2', 'ENTREP-g12', 'abm', '2'),
('ENTREP-g12-ad-2', 'ENTREP-g12', 'ad', '2'),
('ENTREP-g12-gas-2', 'ENTREP-g12', 'gas', '2'),
('ENTREP-g12-humss-2', 'ENTREP-g12', 'humss', '2'),
('ENTREP-g12-stem-2', 'ENTREP-g12', 'stem', '2'),
('ENTREP-g12-tvl-2', 'ENTREP-g12', 'tvl', '2'),
('GENBIO-g12-stem-1', 'GENBIO-g12', 'stem', '1'),
('GENBIO-g12-stem-2', 'GENBIO-g12', 'stem', '2'),
('GENCHEM1-g11-stem-2', 'GENCHEM1-g11', 'stem', '2'),
('GENCHEM2-g12-stem-1', 'GENCHEM2-g12', 'stem', '1'),
('GENMATH-g11-abm-1', 'GENMATH-g11', 'abm', '1'),
('GENMATH-g11-ad-1', 'GENMATH-g11', 'ad', '1'),
('GENMATH-g11-gas-1', 'GENMATH-g11', 'gas', '1'),
('GENMATH-g11-humss-1', 'GENMATH-g11', 'humss', '1'),
('GENMATH-g11-stem-1', 'GENMATH-g11', 'stem', '1'),
('GENMATH-g11-tvl-1', 'GENMATH-g11', 'tvl', '1'),
('GENPHYSICS-g12-stem-1', 'GENPHYSICS-g12', 'stem', '1'),
('GENPHYSICS-g12-stem-2', 'GENPHYSICS-g12', 'stem', '2'),
('INTROPHILO-g11-abm-1', 'INTROPHILO-g11', 'abm', '1'),
('INTROPHILO-g11-ad-1', 'INTROPHILO-g11', 'ad', '1'),
('INTROPHILO-g11-gas-1', 'INTROPHILO-g11', 'gas', '1'),
('INTROPHILO-g11-humss-1', 'INTROPHILO-g11', 'humss', '1'),
('INTROPHILO-g11-stem-1', 'INTROPHILO-g11', 'stem', '1'),
('INTROPHILO-g11-tvl-1', 'INTROPHILO-g11', 'tvl', '1'),
('KOMPAN-g11-abm-1', 'KOMPAN-g11', 'abm', '1'),
('KOMPAN-g11-ad-1', 'KOMPAN-g11', 'ad', '1'),
('KOMPAN-g11-gas-1', 'KOMPAN-g11', 'gas', '1'),
('KOMPAN-g11-humss-1', 'KOMPAN-g11', 'humss', '1'),
('KOMPAN-g11-stem-1', 'KOMPAN-g11', 'stem', '1'),
('KOMPAN-g11-tvl-1', 'KOMPAN-g11', 'tvl', '1'),
('LITPHIL-g12-abm-2', 'LITPHIL-g12', 'abm', '2'),
('LITPHIL-g12-ad-2', 'LITPHIL-g12', 'ad', '2'),
('LITPHIL-g12-gas-2', 'LITPHIL-g12', 'gas', '2'),
('LITPHIL-g12-humss-2', 'LITPHIL-g12', 'humss', '2'),
('LITPHIL-g12-stem-2', 'LITPHIL-g12', 'stem', '2'),
('LITPHIL-g12-tvl-2', 'LITPHIL-g12', 'tvl', '2'),
('ORALCOM-g11-abm-1', 'ORALCOM-g11', 'abm', '1'),
('ORALCOM-g11-ad-1', 'ORALCOM-g11', 'ad', '1'),
('ORALCOM-g11-gas-1', 'ORALCOM-g11', 'gas', '1'),
('ORALCOM-g11-stem-1', 'ORALCOM-g11', 'stem', '1'),
('ORALCOM-g11-tvl-1', 'ORALCOM-g11', 'tvl', '1'),
('PAGSULAT-g12-abm-1', 'PAGSULAT-g12', 'abm', '1'),
('PAGSULAT-g12-ad-1', 'PAGSULAT-g12', 'ad', '1'),
('PAGSULAT-g12-gas-1', 'PAGSULAT-g12', 'gas', '1'),
('PAGSULAT-g12-humss-1', 'PAGSULAT-g12', 'humss', '1'),
('PAGSULAT-g12-stem-1', 'PAGSULAT-g12', 'stem', '1'),
('PAGSULAT-g12-tvl-1', 'PAGSULAT-g12', 'tvl', '1'),
('PEH-g12-abm-1', 'PEH-g12', 'abm', '1'),
('PEH-g12-abm-2', 'PEH-g12', 'abm', '2'),
('PEH-g12-ad-1', 'PEH-g12', 'ad', '1'),
('PEH-g12-ad-2', 'PEH-g12', 'ad', '2'),
('PEH-g12-gas-1', 'PEH-g12', 'gas', '1'),
('PEH-g12-gas-2', 'PEH-g12', 'gas', '2'),
('PEH-g12-humss-1', 'PEH-g12', 'humss', '1'),
('PEH-g12-humss-2', 'PEH-g12', 'humss', '2'),
('PEH-g12-stem-1', 'PEH-g12', 'stem', '1'),
('PEH-g12-stem-2', 'PEH-g12', 'stem', '2'),
('PEH-g12-tvl-1', 'PEH-g12', 'tvl', '1'),
('PEH-g12-tvl-2', 'PEH-g12', 'tvl', '2'),
('PERDEV-g11-abm-2', 'PERDEV-g11', 'abm', '2'),
('PERDEV-g11-ad-2', 'PERDEV-g11', 'ad', '2'),
('PERDEV-g11-gas-2', 'PERDEV-g11', 'gas', '2'),
('PERDEV-g11-humss-2', 'PERDEV-g11', 'humss', '2'),
('PERDEV-g11-stem-2', 'PERDEV-g11', 'stem', '2'),
('PERDEV-g11-tvl-2', 'PERDEV-g11', 'tvl', '2'),
('PHILO-g11-abm-1', 'PHILO-g11', 'abm', '1'),
('PHILO-g11-ad-1', 'PHILO-g11', 'ad', '1'),
('PHILO-g11-gas-1', 'PHILO-g11', 'gas', '1'),
('PHILO-g11-humss-1', 'PHILO-g11', 'humss', '1'),
('PHILO-g11-stem-1', 'PHILO-g11', 'stem', '1'),
('PHILO-g11-tvl-1', 'PHILO-g11', 'tvl', '1'),
('PRECAL-g11-stem-1', 'PRECAL-g11', 'stem', '1'),
('RW-g11-abm-2', 'RW-g11', 'abm', '2'),
('RW-g11-ad-2', 'RW-g11', 'ad', '2'),
('RW-g11-gas-2', 'RW-g11', 'gas', '2'),
('RW-g11-humss-2', 'RW-g11', 'humss', '2'),
('RW-g11-stem-2', 'RW-g11', 'stem', '2'),
('RW-g11-tvl-2', 'RW-g11', 'tvl', '2'),
('STAT-g11-abm-2', 'STAT-g11', 'abm', '2'),
('STAT-g11-ad-2', 'STAT-g11', 'ad', '2'),
('STAT-g11-gas-2', 'STAT-g11', 'gas', '2'),
('STAT-g11-humss-2', 'STAT-g11', 'humss', '2'),
('STAT-g11-stem-2', 'STAT-g11', 'stem', '2'),
('STAT-g11-tvl-2', 'STAT-g11', 'tvl', '2'),
('STEM-ELEC-g11-stem-1', 'STEM-ELEC-g11', 'stem', '1'),
('STEM-ELEC-g11-stem-2', 'STEM-ELEC-g11', 'stem', '2'),
('STEM-ELEC-g12-stem-1', 'STEM-ELEC-g12', 'stem', '1'),
('STEM-ELEC-g12-stem-2', 'STEM-ELEC-g12', 'stem', '2'),
('TEST-SHS-g11-gas', 'TEST-SHS-g11', 'gas', '1'),
('TEST-SHS-g11-stem', 'TEST-SHS-g11', 'stem', '1'),
('UCSP-g12-abm-2', 'UCSP-g12', 'abm', '2'),
('UCSP-g12-ad-2', 'UCSP-g12', 'ad', '2'),
('UCSP-g12-gas-2', 'UCSP-g12', 'gas', '2'),
('UCSP-g12-humss-2', 'UCSP-g12', 'humss', '2'),
('UCSP-g12-stem-2', 'UCSP-g12', 'stem', '2'),
('UCSP-g12-tvl-2', 'UCSP-g12', 'tvl', '2');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix_name` varchar(255) DEFAULT NULL,
  `sex` enum('male','female') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `first_name`, `middle_name`, `last_name`, `suffix_name`, `sex`) VALUES
('71b7dc45-197a-11ef-a11c-00e18ce201d5', 'Shammy Kierson', NULL, 'Suyat', NULL, 'male'),
('7f01661b-197a-11ef-a11c-00e18ce201d5', 'Samantha Althea', 'Perlas', 'Oris', NULL, 'female');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_number` varchar(50) NOT NULL,
  `payment_amount` decimal(10,2) UNSIGNED NOT NULL,
  `payment_method` enum('cash','installment') NOT NULL,
  `payment_receipt_url` text NOT NULL,
  `payment_mode_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `created_at`, `transaction_number`, `payment_amount`, `payment_method`, `payment_receipt_url`, `payment_mode_id`) VALUES
('0990a7c9-2166-11ef-a4a2-0242a5745a66', '2024-06-03 04:59:20', '999', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('0ed87f2c-2401-11ef-90c3-00e18ce201d5', '2024-06-06 12:34:03', '4567890', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('12197abf-2513-11ef-9890-00e18ce201d5', '2024-06-07 21:15:31', '45634', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('14d1a9a2-253d-11ef-bf59-00e18ce201d5', '2024-06-08 02:59:54', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('1a8408d3-2407-11ef-90c3-00e18ce201d5', '2024-06-06 13:17:20', '9999999', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('210e79ec-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:14', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('224ac0da-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('22621384-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('2276fcfd-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('228de6ca-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('22a05bbd-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('22bd439d-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:00:17', '911', 20000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('2b7cd84c-206e-11ef-901b-00e18ce201d5', '2024-06-01 23:25:01', '111', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g10_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('31a4c294-2513-11ef-9890-00e18ce201d5', '2024-06-07 21:16:23', '25245455', 43434.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_6c4a7c00-2158-11ef-a4a2-0242a5745a66_g7_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('36e42205-24a1-11ef-b6e2-00e18ce201d5', '2024-06-07 07:40:30', '43344', 40012.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('452eda90-2400-11ef-90c3-00e18ce201d5', '2024-06-06 12:28:25', '55555555555', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('4543d674-2402-11ef-90c3-00e18ce201d5', '2024-06-06 12:42:44', '222', 20000.00, 'installment', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('48820d6a-24e9-11ef-9890-00e18ce201d5', '2024-06-07 16:16:23', '23828392', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('58ef093f-2169-11ef-a4a2-0242a5745a66', '2024-06-03 05:23:01', '12121212', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('5f106fe6-206e-11ef-901b-00e18ce201d5', '2024-06-01 23:26:28', '222', 20000.00, 'installment', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('5fe29562-2400-11ef-90c3-00e18ce201d5', '2024-06-06 12:29:09', '55555555555', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('7dc1d897-2408-11ef-90c3-00e18ce201d5', '2024-06-06 13:27:15', '9999999', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('821133fc-253d-11ef-bf59-00e18ce201d5', '2024-06-08 03:02:57', '238273', 928283.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('85a6d15f-2402-11ef-90c3-00e18ce201d5', '2024-06-06 12:44:32', '222', 18000.00, 'installment', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('9c23f7a2-200c-11ef-86f8-00e18ce201d5', '2024-06-01 11:46:40', '222', 41000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('a523682a-2400-11ef-90c3-00e18ce201d5', '2024-06-06 12:31:06', '55555555555', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('a625cad8-240b-11ef-90c3-00e18ce201d5', '2024-06-06 13:49:52', '987654321', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('af1d6c6f-2512-11ef-9890-00e18ce201d5', '2024-06-07 21:12:44', '834834932', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('c3276506-23fb-11ef-90c3-00e18ce201d5', '2024-06-06 11:56:08', '2222222', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('c50579b8-2403-11ef-90c3-00e18ce201d5', '2024-06-06 12:53:28', '7777', 17500.00, 'installment', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g11_4.jpg', 'bpi-pateros-catholic-school-12345679'),
('c8d9392a-2408-11ef-90c3-00e18ce201d5', '2024-06-06 13:29:21', '34343', 40040.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('cf360d52-24a6-11ef-b6e2-00e18ce201d5', '2024-06-07 08:20:33', '342342', 3233232.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g10_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('d50a5580-24a2-11ef-b6e2-00e18ce201d5', '2024-06-07 07:52:04', '23424', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g10_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('da3b12ad-24a4-11ef-b6e2-00e18ce201d5', '2024-06-07 08:06:32', '23232', 12414.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g10_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('db8b486a-2512-11ef-9890-00e18ce201d5', '2024-06-07 21:13:59', '434434', 50000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_0.jpg', 'bpi-pateros-catholic-school-12345679'),
('dd358503-240a-11ef-90c3-00e18ce201d5', '2024-06-06 13:44:15', '1638712312389', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('e92bad41-24a3-11ef-b6e2-00e18ce201d5', '2024-06-07 07:59:48', '23232', 12414.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g10_3.jpg', 'bpi-pateros-catholic-school-12345679'),
('ebb00ca4-23fb-11ef-90c3-00e18ce201d5', '2024-06-06 11:57:16', '2222222', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('edaa101e-2406-11ef-90c3-00e18ce201d5', '2024-06-06 13:16:04', '9999999', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('f1514dd3-2408-11ef-90c3-00e18ce201d5', '2024-06-06 13:30:29', '99999', 40000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679'),
('f4648c9b-23fd-11ef-90c3-00e18ce201d5', '2024-06-06 12:11:50', '444444', 41000.00, 'cash', '/storage/payment-receipts/PAYMENT-RECEIPT_STUDENT_edcb084a-197b-11ef-a11c-00e18ce201d5_g12_5.jpg', 'bpi-pateros-catholic-school-12345679');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_plans`
--

CREATE TABLE `tuition_plans` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuition_plans`
--

INSERT INTO `tuition_plans` (`id`, `name`) VALUES
('a-1', 'A-1'),
('a-2', 'A-2'),
('a-3', 'A-3');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_plan_levels`
--

CREATE TABLE `tuition_plan_levels` (
  `id` char(36) NOT NULL,
  `down_payment_amount` decimal(10,2) UNSIGNED NOT NULL,
  `monthly_payment_amount` decimal(10,2) UNSIGNED NOT NULL,
  `tuition_plan_id` varchar(20) NOT NULL,
  `year_level_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuition_plan_levels`
--

INSERT INTO `tuition_plan_levels` (`id`, `down_payment_amount`, `monthly_payment_amount`, `tuition_plan_id`, `year_level_id`) VALUES
('0e48f550-19a3-11ef-ac3d-00e18ce201d5', 23037.00, 1500.00, 'a-1', 'g7'),
('2570cd4c-19a3-11ef-ac3d-00e18ce201d5', 18037.00, 2000.00, 'a-2', 'g7'),
('30d89dd9-19a3-11ef-ac3d-00e18ce201d5', 21359.00, 1500.00, 'a-1', 'g8'),
('371bb4ed-19a3-11ef-ac3d-00e18ce201d5', 23069.00, 1500.00, 'a-1', 'g9'),
('3d932253-19a3-11ef-ac3d-00e18ce201d5', 25369.00, 1500.00, 'a-1', 'g10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `role` enum('student','parent','teacher','admin') NOT NULL DEFAULT 'student',
  `avatar_url` text DEFAULT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `created_at`, `first_name`, `middle_name`, `last_name`, `suffix_name`, `email`, `contact_number`, `role`, `avatar_url`, `password`) VALUES
('3241660d-2516-11ef-9890-00e18ce201d5', '2024-06-07 21:37:53', 'Aaron', NULL, 'Melendres', 'Jr.', 'aaronjr@gmail.com', '1234', 'student', NULL, '$2y$10$kyNevSKXVG1PzxkPzVu8zedQM3/ifPMAg8C3QPSz/PQLcnV3WQpyC'),
('57790e94-2156-11ef-a4a2-0242a5745a66', '2024-06-03 03:06:59', 'Goodbye', NULL, 'World', NULL, 'hello@gmail.com', '0987654321', 'student', NULL, '$2y$10$TYmDYngzklGkXrGEuEX7D.AP61tFE88TRpsdXXedZP1DOPBfK0f8S'),
('66844843-1e12-11ef-9209-00e18ce201d5', '2024-05-29 23:23:04', 'Hanni', NULL, 'My Love', NULL, 'hanni@gmail.com', '143', 'admin', NULL, '$2y$10$d1diUyACOG1Z0dPVR08e1eOzeW9cJVHuoRo8icq7N32SGGR8w0d.6'),
('6c4a7c00-2158-11ef-a4a2-0242a5745a66', '2024-06-03 03:21:52', 'Izuku', NULL, 'Midoriya', NULL, 'deku@gmail.com', '555', 'student', NULL, '$2y$10$5XM9a3VrGECY0BWVvgt2CeRi9j0YbR4pJCCtmmWZaqDxfvM/0VUrC'),
('8accfe15-1d6f-11ef-9953-00e18ce201d5', '2024-05-29 03:57:17', 'Rhenz', NULL, 'Ganotice', NULL, 'rhenz@gmail.com', '123', 'parent', NULL, '$2y$10$5LXJYzX6SeS9MIx.gDUjT..ClJNDilw51t/MXQeVURSRNh3TCmkj6'),
('b00cf259-197c-11ef-a11c-00e18ce201d5', '2024-05-24 03:21:19', 'Gojo', NULL, 'Satoru', NULL, 'admin@gmail.com', '1234', 'admin', NULL, '$2y$10$u2RssLCI91Oo2f6igDYZveUwS0cYQh63j/sokVpOCkbQSIFxU.gEi'),
('e06758b2-251e-11ef-9890-00e18ce201d5', '2024-06-07 22:40:01', 'Delete', NULL, 'Delete', NULL, 'deletethis@gmail.com', '87877', 'student', NULL, '$2y$10$xIh5n0R/5.nHMAWBFVNY4urx5gX4j1DE5oRwlsHacVJtHzInKgfFO'),
('edcb084a-197b-11ef-a11c-00e18ce201d5', '2024-05-24 03:15:53', 'Lorena', NULL, 'Sanchez', NULL, 'giordnnuz27@gmail.com', '1234', 'student', 'https://i.pinimg.com/736x/3c/2f/90/3c2f901d02252bca7fc2fabfdfcf896a.jpg', '$2y$10$O.g10gwAcVFRD.iUXPA7m.A86jdLMhUVjPkKE8XAf4Uk44ZDUZ/aq'),
('f93a2ed0-19a5-11ef-ac3d-00e18ce201d5', '2024-05-24 08:16:51', 'Aaron', NULL, 'Melendres', NULL, 'giordnnuz.dummy@gmail.com', '111', 'parent', NULL, '$2y$10$vGbAlELtlvj2bLhy12tICun/CDXQg/y0pBIOw4GQT2qYUtlZqoT/W');

-- --------------------------------------------------------

--
-- Table structure for table `year_levels`
--

CREATE TABLE `year_levels` (
  `id` varchar(50) NOT NULL,
  `name` varchar(20) NOT NULL,
  `education_level` enum('preschool','elementary','junior-high-school','senior-high-school') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `year_levels`
--

INSERT INTO `year_levels` (`id`, `name`, `education_level`) VALUES
('g1', 'Grade 1', 'elementary'),
('g10', 'Grade 10', 'junior-high-school'),
('g11', 'Grade 11', 'senior-high-school'),
('g12', 'Grade 12', 'senior-high-school'),
('g2', 'Grade 2', 'elementary'),
('g3', 'Grade 3', 'elementary'),
('g4', 'Grade 4', 'elementary'),
('g5', 'Grade 5', 'elementary'),
('g6', 'Grade 6', 'elementary'),
('g7', 'Grade 7', 'junior-high-school'),
('g8', 'Grade 8', 'junior-high-school'),
('g9', 'Grade 9', 'junior-high-school'),
('kin', 'Kinder', 'preschool'),
('nur', 'Nursery', 'preschool');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrolled_tuition_plans`
--
ALTER TABLE `enrolled_tuition_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `tuition_plan_id` (`tuition_plan_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_level_id` (`year_level_id`);

--
-- Indexes for table `enrollment_discounts`
--
ALTER TABLE `enrollment_discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollment_discount_applications`
--
ALTER TABLE `enrollment_discount_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `enrollment_discount_id` (`enrollment_discount_id`);

--
-- Indexes for table `enrollment_fees`
--
ALTER TABLE `enrollment_fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollment_fee_levels`
--
ALTER TABLE `enrollment_fee_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_fee_id` (`enrollment_fee_id`),
  ADD KEY `year_level_id` (`year_level_id`);

--
-- Indexes for table `enrollment_strands`
--
ALTER TABLE `enrollment_strands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- Indexes for table `enrollment_transactions`
--
ALTER TABLE `enrollment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `parent_student_links`
--
ALTER TABLE `parent_student_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `payment_accounts`
--
ALTER TABLE `payment_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_modes`
--
ALTER TABLE `payment_modes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_account_id` (`payment_account_id`);

--
-- Indexes for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_assignments`
--
ALTER TABLE `section_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `section_level_id` (`section_level_id`);

--
-- Indexes for table `section_assignment_strands`
--
ALTER TABLE `section_assignment_strands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_assignment_id` (`section_assignment_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- Indexes for table `section_levels`
--
ALTER TABLE `section_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `year_level_id` (`year_level_id`),
  ADD KEY `adviser_id` (`adviser_id`);

--
-- Indexes for table `section_strands`
--
ALTER TABLE `section_strands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_level_id` (`section_level_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_family_members`
--
ALTER TABLE `student_family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_level_id` (`subject_level_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_grade_strands`
--
ALTER TABLE `student_grade_strands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_grade_id` (`student_grade_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_levels`
--
ALTER TABLE `subject_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `year_level_id` (`year_level_id`);

--
-- Indexes for table `subject_strands`
--
ALTER TABLE `subject_strands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_level_id` (`subject_level_id`),
  ADD KEY `strand_id` (`strand_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_mode_id` (`payment_mode_id`);

--
-- Indexes for table `tuition_plans`
--
ALTER TABLE `tuition_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tuition_plan_levels`
--
ALTER TABLE `tuition_plan_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tuition_plan_id` (`tuition_plan_id`),
  ADD KEY `year_level_id` (`year_level_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `year_levels`
--
ALTER TABLE `year_levels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrollment_transactions`
--
ALTER TABLE `enrollment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `report_cards`
--
ALTER TABLE `report_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `section_assignments`
--
ALTER TABLE `section_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `section_assignment_strands`
--
ALTER TABLE `section_assignment_strands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `student_grade_strands`
--
ALTER TABLE `student_grade_strands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrolled_tuition_plans`
--
ALTER TABLE `enrolled_tuition_plans`
  ADD CONSTRAINT `enrolled_tuition_plans_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrolled_tuition_plans_ibfk_2` FOREIGN KEY (`tuition_plan_id`) REFERENCES `tuition_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_discount_applications`
--
ALTER TABLE `enrollment_discount_applications`
  ADD CONSTRAINT `enrollment_discount_applications_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_discount_applications_ibfk_2` FOREIGN KEY (`enrollment_discount_id`) REFERENCES `enrollment_discounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_fee_levels`
--
ALTER TABLE `enrollment_fee_levels`
  ADD CONSTRAINT `enrollment_fee_levels_ibfk_1` FOREIGN KEY (`enrollment_fee_id`) REFERENCES `enrollment_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_fee_levels_ibfk_2` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_strands`
--
ALTER TABLE `enrollment_strands`
  ADD CONSTRAINT `enrollment_strands_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_strands_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_transactions`
--
ALTER TABLE `enrollment_transactions`
  ADD CONSTRAINT `enrollment_transactions_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_transactions_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_student_links`
--
ALTER TABLE `parent_student_links`
  ADD CONSTRAINT `parent_student_links_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_student_links_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_modes`
--
ALTER TABLE `payment_modes`
  ADD CONSTRAINT `payment_modes_ibfk_1` FOREIGN KEY (`payment_account_id`) REFERENCES `payment_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_cards`
--
ALTER TABLE `report_cards`
  ADD CONSTRAINT `report_cards_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_assignments`
--
ALTER TABLE `section_assignments`
  ADD CONSTRAINT `section_assignments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_assignments_ibfk_2` FOREIGN KEY (`section_level_id`) REFERENCES `section_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_assignment_strands`
--
ALTER TABLE `section_assignment_strands`
  ADD CONSTRAINT `section_assignment_strands_ibfk_1` FOREIGN KEY (`section_assignment_id`) REFERENCES `section_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_assignment_strands_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_levels`
--
ALTER TABLE `section_levels`
  ADD CONSTRAINT `adviser_id` FOREIGN KEY (`adviser_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_levels_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_levels_ibfk_2` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_strands`
--
ALTER TABLE `section_strands`
  ADD CONSTRAINT `section_strands_ibfk_1` FOREIGN KEY (`section_level_id`) REFERENCES `section_levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_strands_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_family_members`
--
ALTER TABLE `student_family_members`
  ADD CONSTRAINT `student_family_members_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_family_members_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD CONSTRAINT `student_grades_ibfk_1` FOREIGN KEY (`subject_level_id`) REFERENCES `subject_levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_grades_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_grade_strands`
--
ALTER TABLE `student_grade_strands`
  ADD CONSTRAINT `student_grade_strands_ibfk_1` FOREIGN KEY (`student_grade_id`) REFERENCES `student_grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_grade_strands_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_profiles_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_levels`
--
ALTER TABLE `subject_levels`
  ADD CONSTRAINT `subject_levels_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_levels_ibfk_2` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_strands`
--
ALTER TABLE `subject_strands`
  ADD CONSTRAINT `subject_strands_ibfk_1` FOREIGN KEY (`subject_level_id`) REFERENCES `subject_levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_strands_ibfk_2` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`payment_mode_id`) REFERENCES `payment_modes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tuition_plan_levels`
--
ALTER TABLE `tuition_plan_levels`
  ADD CONSTRAINT `tuition_plan_levels_ibfk_1` FOREIGN KEY (`tuition_plan_id`) REFERENCES `tuition_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tuition_plan_levels_ibfk_2` FOREIGN KEY (`year_level_id`) REFERENCES `year_levels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
