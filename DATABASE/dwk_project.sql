-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 08:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dwk`
--

-- --------------------------------------------------------

--
-- Table structure for table `abike`
--

CREATE TABLE `abike` (
  `id` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `deposite` varchar(50) DEFAULT NULL,
  `price_per_day` int(11) DEFAULT 500,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','booked') DEFAULT 'active',
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abike`
--

INSERT INTO `abike` (`id`, `address`, `model`, `color`, `deposite`, `price_per_day`, `image`, `status`, `name`) VALUES
('GJ32AB5500', 'shreeji bike rentals,xyz road.', 'OLA s1', 'yellow', '500', 200, 'YLLOW_ola.jpeg', 'active', ''),
('GJ32RG5509', 'shreeji bike rentals,xyz road.', 'OLA s1', 'Blue', '600', 200, 'BLUE_ola.jpeg', 'active', ''),
('GJ37RG1111', 'Krishna Bike Rental,1st Floor ABC Building.', 'TVS IQUBE', 'Blue', '500', 300, 'blue_IQUBE.jpeg', 'active', ''),
('GJ37RG1343', 'Krishna Bike Rental,1st Floor ABC Building.', 'REVOLT', 'Black', '1000', 500, 'BLACK_revolt.jpeg', 'active', ''),
('GJ37RG2266', 'Krishna Bike Rental,1st Floor ABC Building.', 'Ather', 'white', '400', 300, 'W_ATHER.jpeg', '', ''),
('GJ37RG6363', 'Krishna Bike Rental,1st Floor ABC Building.', 'REVOLT', 'Green', '1000', 500, 'GREEN_revolt.jpeg', '', ''),
('GJ37RG9977', 'Bharat Bikes,1st Floor ABC Building.', 'OLA s1', 'Black', '500', 200, 'black_ola.jpeg', 'active', '');

-- --------------------------------------------------------

--
-- Table structure for table `abookings`
--

CREATE TABLE `abookings` (
  `id` varchar(255) NOT NULL,
  `sr_no` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bike_id` varchar(225) NOT NULL,
  `booking_date` date NOT NULL,
  `return_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `name` text NOT NULL,
  `age` int(100) NOT NULL,
  `idProof` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `paymentMethod` varchar(255) NOT NULL,
  `booking_status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `booking_id` varchar(20) NOT NULL,
  `pick_up_time` time NOT NULL,
  `drop_off_time` time NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `paymentStatus` varchar(20) DEFAULT 'PENDING',
  `merchantTransactionId` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abookings`
--

INSERT INTO `abookings` (`id`, `sr_no`, `user_id`, `bike_id`, `booking_date`, `return_date`, `total_price`, `name`, `age`, `idProof`, `mobile`, `email`, `paymentMethod`, `booking_status`, `booking_id`, `pick_up_time`, `drop_off_time`, `payment_id`, `transaction_id`, `payment_status`, `paymentStatus`, `merchantTransactionId`) VALUES
('', 2, 12, '2', '2024-12-05', '2024-12-06', 1400.00, 'Rishit Gokani', 19, 'Aadhar Card', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'cancelled', '', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 3, 12, '1', '2024-12-04', '2024-12-06', 1500.00, 'Rishit Gokani', 18, 'Voter ID', '9054626501', 'rishitgokani004@gmail.com', 'Cash', 'cancelled', '', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 6, 3, 'AB3377', '2024-12-06', '2024-12-09', 500.00, 'rishit', 20, 'aadhar card', '9054626501', 'rishit000@gamil.com', 'cash', 'cancelled', '', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 7, 12, 'AB37RG5501', '2024-12-06', '2024-12-07', 1180.00, 'RRR', 19, 'Driving License', '9099494643', 'rishitgokani22@gmail.com', 'Cash', 'cancelled', '', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 8, 13, '1', '2024-12-06', '2024-12-09', 2000.00, 'ZZZ', 20, 'Driving License', '9054626501', 'rishitgokani001@gmail.com', 'Cash', 'cancelled', 'DWK0000', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 9, 13, '2', '2024-12-07', '2024-12-09', 2100.00, 'YYYY', 20, 'Driving License', '9054626501', 'rishitgokani001@gmail.com', 'Cash', 'cancelled', 'DWK0000', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 10, 13, '1', '2024-12-06', '2024-12-09', 2000.00, 'OOOO', 20, 'Driving License', '9054626501', 'rishitgokani001@gmail.com', 'Cash', 'cancelled', 'DWK0001', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 11, 14, '1', '2024-12-07', '2024-12-09', 1500.00, 'Rishit', 20, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK0001', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 12, 14, 'AB37RG5501', '2024-12-07', '2024-12-09', 1770.00, 'Rishit Gokani', 20, 'Voter ID', '9428892111', 'rishitgokani004@gmail.com', 'Cash', 'cancelled', 'DWK0001', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 13, 14, 'AB37RG5501', '2024-12-07', '2024-12-09', 1770.00, 'R', 80, 'Voter ID', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK0002', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 14, 14, 'AB37RG5501', '2024-12-06', '2024-12-09', 2360.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK0002', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 15, 14, 'AB37RG5501', '2024-12-06', '2024-12-09', 2360.00, 'TT', 20, 'Driving License', '9054626501', 'rishitgokani001@gmail.com', 'Cash', 'cancelled', 'DWK1733467240', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 16, 14, '2', '2024-12-07', '2024-12-09', 2100.00, 'II', 20, 'Driving License', '9054626501', 'rishitgokani001@gmail.com', 'Cash', 'cancelled', 'DWK1733467294', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 17, 14, '2', '2024-12-07', '2024-12-09', 2100.00, 'EEE', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733467432', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 18, 14, '2', '2024-12-07', '2024-12-08', 1400.00, 'Rishit Gokani', 30, 'Aadhar Card', '9190991568', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733467677', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 19, 14, 'AB37RG5501', '2024-12-07', '2024-12-09', 1770.00, 'Rishit Gokani', 20, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733467712', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 20, 13, 'AB37RG5501', '2024-12-08', '2024-12-09', 1180.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733468284', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 21, 13, 'AB37RG5501', '2024-12-08', '2024-12-09', 1180.00, 'lLL', 63, 'Voter ID', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733468521', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 22, 13, 'AB37RG5501', '2024-12-08', '2024-12-09', 1180.00, 'Vo', 55, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733468551', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 23, 13, 'AB37RG5501', '2024-12-08', '2024-12-09', 1180.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733468670', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 24, 13, 'AB37RG5501', '2024-12-06', '2024-12-06', 590.00, 'Rishit', 68, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733468697', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 25, 13, 'AB37RG5501', '2024-12-06', '2024-12-08', 1770.00, 'Rishit Gokani', 30, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1733469043', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 26, 13, '2', '2024-12-06', '2024-12-06', 700.00, 'Rishit Gokani', 80, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733469073', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 27, 15, 'AB37RG5501', '2024-12-09', '2024-12-09', 590.00, 'Rishit Gokani', 60, 'Driving License', '9054654064', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733495431', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 28, 15, '1', '2024-12-06', '2024-12-09', 2000.00, 'Rishit Gokani', 20, 'Driving License', '9054654064', 'fwefw@gmail.coma', 'Cash', 'cancelled', 'DWK1733495731', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 29, 15, '2', '2024-12-07', '2024-12-09', 2100.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1733555895', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 30, 15, '4994', '2024-12-09', '2024-12-10', 1120.00, 'Rishit Gokani', 19, 'Driving License', '9099494643', 'efg2@gmail.com', '', 'cancelled', 'DWK1733682123', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 31, 15, 'Krishna Bike Rental,1st Floor ABC Building.', '2024-12-09', '2024-12-10', 1040.00, 'dwd', 20, 'Driving License', '9099494643', '', '', 'completed', 'DWK1733682178', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 32, 15, '4994', '2024-12-08', '2024-12-09', 1120.00, 'Rishit Gokani', 19, 'Driving License', '9099494643', 'rishitgokani22@gmail.com', 'Cash', 'cancelled', 'DWK1733682305', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 33, 15, '4994', '2024-12-10', '2024-12-11', 1120.00, 'Rishit Gokani', 90, 'Driving License', '9099494643', 'rishitgokani22@gmail.com', 'Cash', 'cancelled', 'DWK1733682659', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 34, 15, '2', '2024-12-09', '2024-12-12', 2800.00, 'Rishit Gokani', 20, 'Driving License', '9099494643', 'rishitgokani22@gmail.com', 'Cash', 'completed', 'DWK1733682691', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 35, 15, 'GJ37RG5509', '2024-12-09', '2024-12-12', 2200.00, 'Rishit Gokani', 60, 'Driving License', '9099494643', 'rishitgokani1@gmail.com', 'Cash', 'completed', 'DWK1733683559', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 36, 15, '4994', '2024-12-10', '2024-12-11', 1120.00, 'Rishit', 19, 'Driving License', '9054626501', 'utsavmodi111@gmail.com', 'Cash', 'completed', 'DWK1733739552', '00:00:00', '00:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 37, 15, 'GJ37RG5509', '2024-12-10', '2024-12-11', 1100.00, 'Rishit Gokani', 20, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1733839471', '15:00:00', '15:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 38, 15, 'GJ37RG5506', '2024-12-11', '2024-12-11', 900.00, 'YYY', 19, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1733839707', '11:00:00', '13:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 39, 12, 'GJ37RG5501', '2025-02-13', '2025-02-13', 500.00, 'Rishit Gokani', 20, 'Driving License', '9054654064', 'rishitgokani22@gmail.com', 'Cash', 'completed', 'DWK1739350562', '09:00:00', '09:00:00', NULL, 'TXN67ac62225b672', 'pending', 'PENDING', NULL),
('', 40, 12, 'GJ37RG5503', '2025-02-13', '2025-02-14', 180.00, 'Rishit Gokani', 90, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739350599', '09:00:00', '09:00:00', NULL, 'TXN67ac6247c7993', 'pending', 'PENDING', NULL),
('', 41, 12, 'GJ37RG5503', '2025-02-12', '2025-02-13', 180.00, 'Rishit Gokani', 52, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739350766', '09:00:00', '09:00:00', NULL, 'TXN67ac62ee4acc3', 'pending', 'PENDING', NULL),
('', 42, 12, 'GJ37RG5503', '2025-02-12', '2025-02-13', 180.00, 'Rishit Gokani', 52, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739350796', '09:00:00', '09:00:00', NULL, 'TXN67ac630c04bc0', 'pending', 'PENDING', NULL),
('', 43, 12, 'GJ37RG5503', '2025-02-12', '2025-02-13', 180.00, 'Rishit Gokani', 52, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739350865', '09:00:00', '09:00:00', NULL, 'TXN67ac63514992c', 'pending', 'PENDING', NULL),
('', 44, 12, 'GJ37RG5503', '2025-02-12', '2025-02-13', 180.00, 'Rishit Gokani', 52, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739350954', '09:00:00', '09:00:00', NULL, 'TXN67ac63aa0d049', 'pending', 'PENDING', NULL),
('', 45, 12, 'GJ37RG5503', '2025-02-12', '2025-02-13', 180.00, 'Rishit Gokani', 55, 'Driving License', '9054654064', 'rishitgokani05@gmail.com', 'Cash', 'completed', 'DWK1739351229', '09:00:00', '09:00:00', NULL, 'TXN67ac64bdd1987', 'pending', 'PENDING', NULL),
('', 46, 12, 'GJ37RG5501', '2025-02-16', '2025-02-19', 2000.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', '', 'cancelled', 'DWK1739708842', '17:00:00', '09:00:00', NULL, 'TXN67b1d9aa9e1d4', 'pending', 'PENDING', NULL),
('', 47, 12, 'GJ37RG5501', '2025-02-16', '2025-02-19', 2000.00, 'Rishit Gokani', 30, 'Voter ID', '9428892111', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1739708960', '17:00:00', '09:00:00', NULL, 'TXN67b1da20595c7', 'pending', 'PENDING', NULL),
('', 48, 12, 'GJ37RG5501', '2025-02-16', '2025-02-19', 2000.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1739709129', '09:00:00', '09:00:00', NULL, 'TXN67b1dac92a9c5', 'pending', 'PENDING', NULL),
('', 49, 12, 'GJ37RG5501', '2025-02-16', '2025-02-16', 500.00, 'Rishit Gokani', 30, 'Aadhar Card', '9428892111', 'rishitgokani003@gmail.com', 'Cash', 'completed', 'DWK1739709550', '09:00:00', '09:00:00', NULL, 'TXN67b1dc6edf4af', 'pending', 'PENDING', NULL),
('', 50, 12, 'GJ37RG5501', '2025-02-16', '2025-02-16', 500.00, 'Rishit', 20, 'Voter ID', '9428892111', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1739709654', '18:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 51, 12, 'GJ32AB5500', '2025-02-18', '2025-02-18', 200.00, 'Rishit Gokani', 30, 'Aadhar Card', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1739885422', '17:00:00', '09:00:00', NULL, 'TXN67b48b6ee90e0', 'pending', 'PENDING', NULL),
('', 52, 12, 'GJ32AB5500', '2025-02-18', '2025-02-18', 200.00, 'Rishit Gokani', 21, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'Cash', 'completed', 'DWK1739885549', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 53, 12, 'GJ32AB5500', '2025-02-19', '2025-02-20', 400.00, 'Rishit Gokani', 20, 'Driving License', '9054626265', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739909375', '09:00:00', '17:00:00', NULL, 'TXN67b4e8ffafbda', 'pending', 'PENDING', NULL),
('', 54, 12, 'GJ32AB5500', '2025-02-19', '2025-02-20', 400.00, 'Rishit Gokani', 20, 'Driving License', '9054626265', 'rishitgokani000@gmail.com', 'Cash', 'cancelled', 'DWK1739909424', '09:00:00', '17:00:00', NULL, 'TXN67b4e930a4b43', 'pending', 'PENDING', NULL),
('', 55, 12, 'GJ32AB5500', '2025-02-19', '2025-02-20', 400.00, 'Rishit Gokani', 20, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739909465', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 56, 12, 'GJ32RG5509', '2025-02-19', '2025-02-20', 400.00, 'Rishit Gokani', 21, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739949084', '10:00:00', '20:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 57, 12, 'GJ37RG1111', '2025-02-19', '2025-02-21', 900.00, 'Rishit Gokani', 21, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739949593', '10:00:00', '19:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 58, 12, 'GJ32RG5509', '2025-02-19', '2025-02-20', 400.00, 'Rishit Gokani', 20, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739950086', '10:00:00', '19:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 59, 12, 'GJ37RG9977', '2025-02-19', '2025-02-21', 600.00, 'Rishit Gokani', 20, 'Driving License', '9054616501', 'rishitgokani000@gmail.com', 'Cash', 'active', 'DWK1739950788', '10:00:00', '20:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 60, 95, 'GJ37RG2266', '2025-02-19', '2025-02-20', 600.00, 'Rishit Gokani', 20, 'Driving License', '9054616501', 'rishit01@gmail.com', 'Cash', 'active', 'DWK1739957765', '10:00:00', '20:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 61, 98, 'GJ37RG6363', '2025-02-19', '2025-02-20', 1000.00, 'Rishit Gokani', 20, 'Driving License', '9054626501', 'rishit001@gmail.com', 'Cash', 'active', 'DWK1739966665', '10:00:00', '20:00:00', NULL, NULL, 'pending', 'PENDING', NULL),
('', 62, 12, 'GJ32RG5509', '2025-02-21', '2025-02-21', 200.00, 'Rishit Gokani', 30, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740140988', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b871bcf2ce9'),
('', 63, 12, 'GJ32RG5509', '2025-02-21', '2025-02-21', 200.00, 'Rishit Gokani', 30, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141239', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b872b7b16e6'),
('', 64, 12, 'GJ32RG5509', '2025-02-21', '2025-02-21', 200.00, 'Rishit Gokani', 30, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141310', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b872fe3f7ad'),
('', 65, 12, 'GJ32RG5509', '2025-02-21', '2025-02-21', 200.00, 'Rishit Gokani', 30, 'Driving License', '9054626501', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141401', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b87359b4f9a'),
('', 66, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141456', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b873902d016'),
('', 67, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141579', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b8740b3ab9d'),
('', 68, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141610', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b8742a5fd43'),
('', 69, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141626', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b8743abaff9'),
('', 70, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141684', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b8747463f3d'),
('', 71, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141689', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b874797a7b0'),
('', 72, 12, 'GJ32RG5509', '2025-02-21', '2025-02-23', 600.00, 'Rishit', 20, 'Driving License', '9428892111', 'rishitgokani000@gmail.com', 'PhonePe', 'active', 'DWK1740141772', '09:00:00', '09:00:00', NULL, NULL, 'pending', 'PENDING', '67b874cc6f423');

--
-- Triggers `abookings`
--
DELIMITER $$
CREATE TRIGGER `generate_booking_id` BEFORE INSERT ON `abookings` FOR EACH ROW BEGIN
    -- Concatenate 'BKG' with the current timestamp or some unique value
    SET NEW.booking_id = CONCAT('DWK', UNIX_TIMESTAMP());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `dashboard_url` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `security_question1` varchar(255) NOT NULL,
  `security_answer1` varchar(255) NOT NULL,
  `security_question2` varchar(255) NOT NULL,
  `security_answer2` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `reset_token`, `created_at`, `role`, `dashboard_url`, `reset_expiry`, `security_question1`, `security_answer1`, `security_question2`, `security_answer2`) VALUES
(1, 'XYZ', 'abcd123@gmail.com', '9054626501', '$2y$10$gbgpxgQMATO3FYO2rG5hKunsVwZgzFhQrYT6tIOTmCnyL8maZWzzi', NULL, '2024-12-06 17:26:47', 'user', NULL, NULL, '', '', '', ''),
(2, 'rishit', 'rishit123@gmail.com', '9054626501', '$2y$10$Go4Sf7m/xJAZvKHleOoxoeDrAHQSWil1V.RqpvhEBhjzhvMq/uLOu', 'bccb2cfa2899375c6e6802c409c23e76aa5e994bdf8e4c754d51d8b89407c7e225b19a081a70000bdf263dc5db4d482e447a', '2024-09-27 07:50:13', 'user', NULL, NULL, '', '', '', ''),
(3, 'Gokani Rishit', 'rishitgokani004@gmail.com', '09054626501', '$2y$10$HMlbOBxhs.QXvV9atxi9oehk.mBxnN5lz.LObYQUKahGv01JpW6wG', NULL, '2024-09-27 08:06:57', 'user', NULL, NULL, '', '', '', ''),
(4, 'Rishit Gokani', 'rishitgokani22@gmail.com', '9054626501', '$2y$10$dphKfKK3Kp3pkhWphuVNlOlGPa2ldQUGBDJXl50GP9SkriEE38ZBm', NULL, '2024-09-27 13:30:14', 'user', NULL, NULL, '', '', '', ''),
(5, 'Rishit Gokani', 'rishitgokani2@gmail.com', '9054626501', '$2y$10$x32EmRK6WxsmpQMXkqgSTObDslqYvA2/Gi9/q5oz/ci7XxCkKQdVG', NULL, '2024-09-27 13:33:02', 'user', NULL, NULL, '', '', '', ''),
(6, 'Rishit Gokani', 'rishitgokani5@gmail.com', '9054626501', '$2y$10$WHnjuVzwazDvRGqKsZZzBe7Jv9CN2QE16z0lme7zmR7iLRq9XzzBa', NULL, '2024-09-27 13:34:43', 'user', NULL, NULL, '', '', '', ''),
(7, 'utsav modi', 'utsav@123', '9428892111', '$2y$10$H7Fio/7CMLAO462Rm1.2TeicPf707MBKwSXzNExYfYDn8/xA.mNju', NULL, '2024-09-27 12:05:34', 'user', NULL, NULL, '', '', '', ''),
(8, 'Utsav Modi', 'utsavmodi@gmail.com', '9428892111', '$2y$10$55z9xUfizktklyr1x9taF..uWCNqJfvGjGAYYnai0A/QNi.VGk4By', NULL, '2024-11-01 07:30:22', 'user', NULL, NULL, '', '', '', ''),
(9, 'vishesh', 'vishesh12@gmail.com', '123456789', '$2y$10$c6qr0xbeJRt7QkmGbxAsrekK8.oi/LvDPYkgjx.7.tDYhq39iuHSW', NULL, '2024-09-27 13:17:06', 'user', NULL, NULL, '', '', '', ''),
(10, 'EFGH', 'eefgh12@gmail.com', '9054656', '$2y$10$WWNqGwjhZa9KAaW1s1wk3exODYwQpekc9zpwhSgw4f.bYEmNSX7Nu', NULL, '2024-10-31 19:15:52', 'user', NULL, NULL, '', '', '', ''),
(11, 'POIU', 'rishitgokani4@gmail.com', '09054626501', '$2y$10$7D7VIVHZDacUbAUxHB8Q9O862rg5y4umzUrr/EBCnblWlaGPWljCS', NULL, '2024-11-01 07:23:19', 'user', NULL, NULL, '', '', '', ''),
(12, 'Gokani Rishit', 'rishitgokani000@gmail.com', '09054626501', '$2y$10$BPNQubvhFSxnMGjG.00rZedsKxzs3qPx8haMcRkYUVGzHQngXNNDS', NULL, '2025-01-25 12:16:12', 'user', NULL, NULL, '', '', '', ''),
(13, 'Gokani Rishit', 'rishitgokani001@gmail.com', '09054626501', '$2y$10$TauvT9JwozCviR.DtDo/IeH3x7lHdmxs3CntC2rMsEeSQRb2I479e', NULL, '2024-12-06 06:09:45', 'user', NULL, NULL, '', '', '', ''),
(14, 'Zzz', 'rishitgokani002@gmail.com', '9054626501', '$2y$10$4kv24aCYebTKW/YHjd0UWOD6OkkeBB3XinqdHzVHN3T.RxUiLZTAS', NULL, '2024-12-06 06:30:16', 'user', NULL, NULL, '', '', '', ''),
(15, 'Rishit Gokani', 'rishitgokani003@gmail.com', '9054626501', '$2y$10$lZ6nPYxoqwm4fWRGxKCm/ur/kHhwI/NIl6iNLXRm/8L9ZBMXcBYTi', NULL, '2024-12-06 13:24:28', 'user', NULL, NULL, '', '', '', ''),
(90, 'ABC', 'abc3@gmail.com', '9054626501', 'abc@3', '', '2024-12-06 17:55:42', 'admin', 'aadmin.php', NULL, '', '', '', ''),
(91, 'EFG', 'efg2@gmail.com', '9595959595', 'efg@2', NULL, '2024-12-06 17:55:42', 'admin', '2admin.php', NULL, '', '', '', ''),
(92, 'ABCadmin', 'admin@abcgmail.com', '9054626501', '$2y$10$GAN/CCkSfqUvke4uBeG61OqhCQ.VVWX7PkGYNQLj0/nrerKWxpPNO', NULL, '2024-12-07 06:37:53', 'admin', '1admin.php\r\n', NULL, '', '', '', ''),
(93, 'Zadmin', 'zadmin@gmail.com', '9054626501', '$2y$10$mE//VpZAkZ3KIt9SR7j9Uuv9nnQvGwIxaWZLkdKodECRzpekuC2Ay', NULL, '2024-12-06 18:37:18', 'admin', 'aadmin.php\r\n', NULL, '', '', '', ''),
(94, 'Akkak', 'rishitgokani0@gmail.com', '9054626501', '$2y$10$5K1IWo/n4fzAdAsZ3BObTO9FwHblj9eN2zvyP0qoiQbrmGHaGaCdm', NULL, '2025-02-06 17:33:22', 'user', NULL, NULL, '', '', '', ''),
(95, 'Rishit Gokani', 'rishit01@gmail.com', '9054626510', '$2y$10$BTqGP3PIJjsH4Vfswt7dh.kgTkMZnddB6vI1U5sKu7HQlSIQjEybu', NULL, '2025-02-19 09:33:33', 'user', NULL, NULL, '', '', '', ''),
(96, 'Rishit Gokani', 'rishitgokani01@gmail.com', '9054626510', '$2y$10$j46xcjbg4GsKzUlSZC/xSumjDYB/NfGXu7J81rWnnVVP8xZPXWCNu', NULL, '2025-02-19 11:54:07', 'user', NULL, NULL, '', '', '', ''),
(97, 'Rishit Gokani', 'rishitgokani1@gmail.com', '9054626501', '$2y$10$6sb5ESnE3WFAn1.8vR9DCenXG064FtB1ch/OFvAef4WcxVqeJw9uC', NULL, '2025-02-19 11:55:49', 'user', NULL, NULL, '', '', '', ''),
(98, 'Rishit Gokani', 'rishit001@gmail.com', '9054626501', '$2y$10$z/Z548YcBHBVQ6fAxFHbyeOLoTXoU7GGDhl08z6DTYL3Ze5OWd5oa', NULL, '2025-02-19 12:02:16', 'user', NULL, NULL, '', '', '', ''),
(99, 'Rishit Gokani', 'rishit@gmail.com', '9090909090', '$2y$10$iiOlXvL3QrMOYu2ktLZnjO/3uPKGxIzqlvlR8jCLrtLDZ/9Odebse', NULL, '2025-03-06 06:29:10', 'user', NULL, NULL, '', '', '', ''),
(100, 'Rishit Gokani', 'rishit1@gmail.com', '9090909090', '$2y$10$DtG5cpO8XdOX2yX5cSggxOOj0yfeUcpTrc3VWco9PY4k18xa2wbwG', NULL, '2025-03-06 06:57:16', 'user', NULL, NULL, 'What was your first pet\\\'s name?', '$2y$10$5Vzq0JrTouNvgZc69SRhBu0yaqgOA7zD.uu1fbU5bJCoMJwlRxxoS', 'What was your favorite childhood book?', '$2y$10$mW1PWi8apmYVkHT6/3y..eP1tWwOtN8nRBd8v293nBEzyVJjBRBE.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abike`
--
ALTER TABLE `abike`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `abookings`
--
ALTER TABLE `abookings`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bike_id` (`bike_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `email` (`email`) KEY_BLOCK_SIZE=9 USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abookings`
--
ALTER TABLE `abookings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abookings`
--
ALTER TABLE `abookings`
  ADD CONSTRAINT `abookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
