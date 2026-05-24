-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 01:10 PM
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
-- Database: `hbw`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_cred`
--

CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_cred`
--

INSERT INTO `admin_cred` (`sr_no`, `admin_name`, `admin_pass`) VALUES
(1, 'Admin', '54321');

-- --------------------------------------------------------

--
-- Table structure for table `banner_claims`
--

CREATE TABLE `banner_claims` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `claimed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner_claims`
--

INSERT INTO `banner_claims` (`id`, `user_id`, `claimed_at`) VALUES
(1, 39, '2026-04-19 00:05:53'),
(2, 55, '2026-04-19 11:15:07'),
(3, 56, '2026-05-18 13:52:12'),
(4, 57, '2026-05-22 20:21:20'),
(5, 58, '2026-05-22 20:24:34'),
(6, 59, '2026-05-22 20:30:15');

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`sr_no`, `booking_id`, `room_name`, `price`, `total_pay`, `room_no`, `user_name`, `phonenum`, `address`) VALUES
(175, 175, 'Simple', 1000, 7, '12', 'Prince Thapaliya', '9812426018', 'kalanki'),
(176, 176, 'Simple', 1000, 7, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(177, 177, 'Better', 2000, 2000, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(178, 178, 'Simple', 1000, 7, '1', 'Prince Thapaliya', '9812426016', 'kalanki'),
(179, 179, 'Simple', 1000, 1000, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(180, 180, 'Stander', 3000, 3000, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(185, 185, 'Better', 2000, 13, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(186, 186, 'Premium Family Suite', 3000, 20, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(187, 187, 'Deluxe Garden Room', 1000, 5, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(188, 188, 'Deluxe Garden Room', 1000, 6, '12', 'Prince Thapaliya', '9812426016', 'kalanki'),
(189, 189, 'Executive City View', 2000, 1900, NULL, 'Prince Thapaliya', '1234567890', 'hhh'),
(190, 190, 'Executive City View', 2000, 13, '2', 'Prince Thapaliya', '1234567890', 'hhh'),
(191, 191, 'Premium Family Suite', 3000, 3000, NULL, 'Prince Thapaliya', '1234567890', 'hhh'),
(192, 192, 'Premium Family Suite', 3000, 20, NULL, 'Shreya Malla', '9849860305', 'Sukedhara'),
(193, 193, 'Premium Family Suite', 3000, 20, '13', 'Shreya Malla', '9849860305', 'Sukedhara'),
(194, 194, 'Premium Family Suite', 3000, 2850, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(195, 195, 'Premium Family Suite', 3000, 2850, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(196, 196, 'Deluxe Garden Room', 1000, 950, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(197, 197, 'Deluxe Garden Room', 1000, 1000, NULL, 'Prince Thapaliya', '1234567890', 'hhh'),
(198, 198, 'Premium Family Suite', 3000, 19, NULL, 'Prince Thapaliya', '9812426016', 'kalanki'),
(199, 199, 'Premium Family Suite', 3000, 19, '1', 'Prince Thapaliya', '9812426016', 'kalanki'),
(200, 200, 'Deluxe Garden Room', 1000, 7, '3', 'Prince Thapaliya', '9812426016', 'kalanki'),
(201, 201, 'Executive City View', 2000, 13, '15', 'Prince Thapaliya', '9812426016', 'kalanki'),
(202, 202, 'Deluxe Garden Room', 1000, 7, '2', 'Prince Thapaliya', '9812426016', 'kalanki'),
(203, 203, 'Premium Family Suite', 3000, 20, '6', 'Prince Thapaliya', '1234567890', 'hhh'),
(204, 204, 'Deluxe Garden Room', 1000, 7, '1', 'Prince Thapaliya', '1234567890', 'hhh'),
(205, 205, 'Premium Family Suite', 3000, 20, '3', 'Bomkar', '9812401265', 'Kalanki'),
(206, 206, 'Executive City View', 2000, 13, '2', 'Prince Thapaliya', '9812426016', 'kalanki'),
(207, 207, 'Deluxe Garden Room', 1000, 7, NULL, 'Priyamshu', '9869602487', 'Jorpati'),
(208, 208, 'Deluxe Garden Room', 1000, 1000, NULL, 'Book Ease', '9807765433', 'ktm'),
(209, 209, 'Deluxe Garden Room', 1000, 2000, NULL, 'Book Ease', '9807765433', 'ktm'),
(210, 210, 'Deluxe Garden Room', 1000, 1000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(211, 211, 'Executive City View', 2000, 2000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(212, 212, 'Premium Family Suite', 3000, 3000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(213, 213, 'Executive City View', 2000, 2000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(214, 214, 'Deluxe Garden Room', 1000, 591, 'q', 'Arpita', '9848056665', 'Naxal'),
(215, 215, 'Deluxe Garden Room', 1000, 900, '3', 'meroxek', '7894561230', 'Austria'),
(216, 216, 'Premium Family Suite', 3000, 2700, '1', 'meroxek', '7894561230', 'Austria'),
(217, 217, 'Premium Family Suite', 3000, 3000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(218, 218, 'Premium Family Suite', 3000, 3000, NULL, 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(219, 219, 'Premium Family Suite', 3000, 3000, '12', 'Prince Thapaliya', '1234567899', 'Khasibazars'),
(220, 220, 'Executive City View', 2000, 2000, '33', 'Prince Thapaliya', '1234567899', 'Khasibazars');

-- --------------------------------------------------------

--
-- Table structure for table `booking_order`
--

CREATE TABLE `booking_order` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'pending',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) NOT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'pending',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `rate_review` int(11) DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `currency` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_order`
--

INSERT INTO `booking_order` (`booking_id`, `user_id`, `room_id`, `check_in`, `check_out`, `arrival`, `refund`, `booking_status`, `order_id`, `trans_id`, `trans_amt`, `trans_status`, `trans_resp_msg`, `rate_review`, `datentime`, `currency`) VALUES
(167, 38, 8, '2026-03-21', '2026-03-22', 0, NULL, 'pending', 'ORD_387967782', NULL, 1000, 'pending', NULL, NULL, '2026-03-21 12:12:32', 'NPR'),
(168, 39, 10, '2026-03-21', '2026-03-22', 0, NULL, 'completed', 'ORD_392401260', NULL, 20, 'pending', NULL, NULL, '2026-03-21 15:44:57', 'USD'),
(169, 39, 10, '2026-03-21', '2026-03-22', 0, NULL, 'cancelled', 'ORD_398945089', NULL, 1887, 'pending', NULL, NULL, '2026-03-21 15:50:16', 'INR'),
(170, 39, 10, '2026-03-21', '2026-03-23', 0, NULL, 'cancelled', 'ORD_399246570', NULL, 3775, 'pending', NULL, NULL, '2026-03-21 15:52:59', 'INR'),
(171, 39, 10, '2026-03-21', '2026-03-22', 0, NULL, 'cancelled', 'ORD_398107207', NULL, 3000, 'pending', NULL, NULL, '2026-03-21 15:56:16', 'NPR'),
(172, 39, 8, '2026-03-22', '2026-03-27', 0, 0, 'cancelled', 'ORD_393531182', 'pi_3TDReb4Gjr7oYQNH1zjGQzvU', 33, 'TXN_SUCCESS', NULL, NULL, '2026-03-21 21:28:39', 'USD'),
(173, 39, 8, '2026-03-22', '2026-03-23', 0, NULL, 'cancelled', 'ORD_397090587', NULL, 1000, 'pending', NULL, NULL, '2026-03-21 21:31:52', 'NPR'),
(174, 39, 9, '2026-03-23', '2026-03-24', 0, NULL, 'cancelled', 'ORD_39521428', NULL, 2000, 'pending', NULL, NULL, '2026-03-22 11:17:01', 'NPR'),
(175, 39, 8, '2026-03-22', '2026-03-23', 1, NULL, 'completed', 'ORD_392429671', 'pi_3TDezV4Gjr7oYQNH0rDs6LvU', 7, 'TXN_SUCCESS', NULL, 1, '2026-03-22 11:43:55', 'USD'),
(176, 39, 8, '2026-03-23', '2026-03-24', 0, 1, 'cancelled', 'ORD_391212331', 'pi_3TDfRB4Gjr7oYQNH0IrpUKvc', 7, 'TXN_SUCCESS', NULL, NULL, '2026-03-22 12:12:19', 'USD'),
(177, 39, 9, '2026-03-23', '2026-03-24', 0, NULL, 'cancelled', 'ORD_396352615', NULL, 2000, 'pending', NULL, NULL, '2026-03-22 15:13:41', 'NPR'),
(178, 39, 8, '2026-03-22', '2026-03-23', 1, NULL, 'completed', 'ORD_394199840', 'pi_3TDiHJ4Gjr7oYQNH0z4fqVfH', 7, 'TXN_SUCCESS', NULL, 1, '2026-03-22 15:14:31', 'USD'),
(179, 39, 8, '2026-03-25', '2026-03-26', 0, NULL, 'pending', 'ORD_395465678', NULL, 1000, 'pending', NULL, NULL, '2026-03-24 07:34:53', 'NPR'),
(180, 39, 10, '2026-03-25', '2026-03-26', 0, NULL, 'completed', 'ORD_395488663', NULL, 3000, 'pending', NULL, NULL, '2026-03-24 08:13:23', 'NPR'),
(181, 39, 8, '2026-03-25', '2026-03-26', 0, 0, 'cancelled', 'ORD_397041704', 'pi_3TEKhE4Gjr7oYQNH1oD7z1w1', 7, 'TXN_SUCCESS', NULL, NULL, '2026-03-24 08:15:17', 'USD'),
(182, 39, 10, '2026-03-26', '2026-03-27', 0, NULL, 'completed', 'ORD_39265357', NULL, 3000, 'pending', NULL, NULL, '2026-03-25 08:53:41', 'NPR'),
(183, 39, 8, '2026-03-26', '2026-03-27', 0, 0, 'cancelled', 'ORD_399015273', 'pi_3TF3Rd4Gjr7oYQNH1F157VNE', 7, 'TXN_SUCCESS', NULL, NULL, '2026-03-26 08:02:03', 'USD'),
(184, 39, 8, '2026-03-30', '2026-03-31', 1, 0, 'cancelled', 'ORD_395915991', 'pi_3TG8R14Gjr7oYQNH06283dhc', 7, 'TXN_SUCCESS', NULL, 0, '2026-03-29 07:34:23', 'USD'),
(185, 39, 9, '2026-03-30', '2026-03-31', 0, 0, 'cancelled', 'ORD_391172769', 'pi_3TG9lD4Gjr7oYQNH0HozPD6o', 13, 'TXN_SUCCESS', NULL, NULL, '2026-03-29 08:59:10', 'USD'),
(186, 39, 10, '2026-03-29', '2026-03-30', 0, 0, 'cancelled', 'ORD_397741607', 'pi_3TGAZf4Gjr7oYQNH1M8CqntP', 20, 'TXN_SUCCESS', NULL, NULL, '2026-03-29 09:52:01', 'USD'),
(187, 39, 8, '2026-03-29', '2026-03-30', 0, NULL, 'pending', 'ORD_392177828', NULL, 5, 'pending', NULL, NULL, '2026-03-29 10:02:16', 'USD'),
(188, 39, 8, '2026-03-30', '2026-03-31', 1, NULL, 'completed', 'ORD_393364370', 'pi_3TGFRu4Gjr7oYQNH07h38bV4', 6, 'TXN_SUCCESS', NULL, 0, '2026-03-29 15:04:23', 'USD'),
(189, 38, 9, '2026-04-03', '2026-04-04', 0, NULL, 'cancelled', 'ORD_38512284', NULL, 1900, 'pending', NULL, NULL, '2026-04-03 18:48:25', 'NPR'),
(190, 38, 9, '2026-04-03', '2026-04-04', 1, NULL, 'completed', 'ORD_385083923', 'pi_3TI7Ob4Gjr7oYQNH0apD2OzW', 13, 'TXN_SUCCESS', NULL, 1, '2026-04-03 18:51:57', 'USD'),
(191, 38, 10, '2026-04-03', '2026-04-04', 0, NULL, 'completed', 'ORD_385642008', NULL, 3000, 'pending', NULL, NULL, '2026-04-03 19:10:05', 'NPR'),
(192, 50, 10, '2026-04-04', '2026-04-05', 0, NULL, 'cancelled', 'ORD_509583292', NULL, 20, 'pending', NULL, NULL, '2026-04-04 13:10:31', 'USD'),
(193, 50, 10, '2026-04-04', '2026-04-05', 1, NULL, 'completed', 'ORD_506209517', 'pi_3TIOXm4Gjr7oYQNH1kfQa6Fa', 20, 'TXN_SUCCESS', NULL, 1, '2026-04-04 13:10:39', 'USD'),
(194, 39, 10, '2026-04-04', '2026-04-05', 0, NULL, 'cancelled', 'ORD_395486150', NULL, 2850, 'pending', NULL, NULL, '2026-04-04 13:59:18', 'NPR'),
(195, 39, 10, '2026-04-04', '2026-04-05', 0, NULL, 'cancelled', 'ORD_399987568', NULL, 2850, 'pending', NULL, NULL, '2026-04-04 14:08:13', 'NPR'),
(196, 39, 8, '2026-04-04', '2026-04-05', 0, NULL, 'cancelled', 'ORD_39756579', NULL, 950, 'pending', NULL, NULL, '2026-04-04 14:11:52', 'NPR'),
(197, 38, 8, '2026-04-04', '2026-04-05', 0, NULL, 'cancelled', 'ORD_388540544', NULL, 1000, 'pending', NULL, NULL, '2026-04-04 16:21:04', 'NPR'),
(198, 39, 10, '2026-04-04', '2026-04-05', 0, 0, 'cancelled', 'ORD_398641178', 'pi_3TIURB4Gjr7oYQNH1QdHJPGu', 19, 'TXN_SUCCESS', NULL, NULL, '2026-04-04 19:28:46', 'USD'),
(199, 39, 10, '2026-04-04', '2026-04-05', 1, NULL, 'completed', 'ORD_392014653', 'pi_3TIUhk4Gjr7oYQNH1n4E0AkJ', 19, 'TXN_SUCCESS', NULL, 0, '2026-04-04 19:46:02', 'USD'),
(200, 39, 8, '2026-04-04', '2026-04-05', 1, NULL, 'completed', 'ORD_392004246', 'pi_3TIUjk4Gjr7oYQNH10FMcIFQ', 7, 'TXN_SUCCESS', NULL, 0, '2026-04-04 19:48:14', 'USD'),
(201, 39, 9, '2026-04-04', '2026-04-05', 1, NULL, 'booked', 'ORD_398746805', 'pi_3TIUoH4Gjr7oYQNH0agGdxLW', 13, 'TXN_SUCCESS', NULL, 0, '2026-04-04 19:52:50', 'USD'),
(202, 39, 8, '2026-04-04', '2026-04-05', 1, NULL, 'completed', 'ORD_395088105', 'pi_3TIUxz4Gjr7oYQNH0QIqRoep', 7, 'TXN_SUCCESS', NULL, 0, '2026-04-04 20:02:46', 'USD'),
(203, 38, 10, '2026-04-04', '2026-04-05', 1, NULL, 'completed', 'ORD_388076235', 'pi_3TIV374Gjr7oYQNH0KQA7HsY', 20, 'TXN_SUCCESS', NULL, 0, '2026-04-04 20:08:03', 'USD'),
(204, 38, 8, '2026-04-05', '2026-04-06', 1, NULL, 'completed', 'ORD_381537882', 'pi_3TIV8C4Gjr7oYQNH1ZdCUWml', 7, 'TXN_SUCCESS', NULL, 0, '2026-04-04 20:13:09', 'USD'),
(205, 51, 10, '2026-04-05', '2026-04-06', 1, NULL, 'completed', 'ORD_512241838', 'pi_3TIh5D4Gjr7oYQNH1lpoMKWR', 20, 'TXN_SUCCESS', NULL, 1, '2026-04-05 08:59:02', 'USD'),
(206, 39, 9, '2026-04-05', '2026-04-06', 1, NULL, 'booked', 'ORD_399110493', 'pi_3TInTm4Gjr7oYQNH05sethkS', 13, 'TXN_SUCCESS', NULL, 0, '2026-04-05 15:48:27', 'USD'),
(207, 54, 8, '2026-04-08', '2026-04-09', 0, NULL, 'pending', 'ORD_547505307', NULL, 7, 'pending', NULL, NULL, '2026-04-08 10:50:20', 'USD'),
(208, 55, 8, '2026-04-20', '2026-04-21', 0, NULL, 'pending', 'ORD_556997433', NULL, 1000, 'pending', NULL, NULL, '2026-04-19 11:12:06', 'NPR'),
(209, 55, 8, '2026-04-20', '2026-04-22', 0, NULL, 'pending', 'ORD_551727689', NULL, 2000, 'pending', NULL, NULL, '2026-04-19 11:14:04', 'NPR'),
(210, 38, 8, '2026-04-19', '2026-04-20', 0, NULL, 'pending', 'ORD_385098048', NULL, 1000, 'pending', NULL, NULL, '2026-04-19 12:31:53', 'NPR'),
(211, 38, 9, '2026-04-19', '2026-04-20', 0, NULL, 'cancelled', 'ORD_384682680', NULL, 2000, 'pending', NULL, NULL, '2026-04-19 12:37:49', 'NPR'),
(212, 38, 10, '2026-04-20', '2026-04-21', 0, NULL, 'completed', 'ORD_386587921', NULL, 3000, 'pending', NULL, NULL, '2026-04-19 12:40:45', 'NPR'),
(213, 38, 9, '2026-04-19', '2026-04-20', 0, NULL, 'cancelled', 'ORD_38452667', NULL, 2000, 'pending', NULL, NULL, '2026-04-19 15:23:22', 'NPR'),
(214, 56, 8, '2026-05-18', '2026-05-19', 1, NULL, 'completed', 'ORD_562109677', 'pi_3TYMMt4Gjr7oYQNH0WyoWRvW', 591, 'TXN_SUCCESS', NULL, 0, '2026-05-18 14:07:00', 'INR'),
(215, 59, 8, '2026-05-22', '2026-05-23', 1, NULL, 'completed', 'ORD_598341928', 'pi_3TZuKa4Gjr7oYQNH0t2MSjgM', 900, 'TXN_SUCCESS', NULL, 1, '2026-05-22 20:34:47', 'NPR'),
(216, 59, 10, '2026-05-22', '2026-05-23', 1, NULL, 'completed', 'ORD_593265392', 'pi_3TZujB4Gjr7oYQNH0t5VU9aN', 2700, 'TXN_SUCCESS', NULL, 0, '2026-05-22 20:59:58', 'NPR'),
(217, 38, 10, '2026-05-22', '2026-05-23', 0, NULL, 'cancelled', 'ORD_381168374', NULL, 3000, 'pending', NULL, NULL, '2026-05-22 21:01:43', 'NPR'),
(218, 38, 10, '2026-05-22', '2026-05-23', 0, NULL, 'cancelled', 'ORD_38548419', NULL, 3000, 'pending', NULL, NULL, '2026-05-22 21:02:48', 'NPR'),
(219, 38, 10, '2026-05-22', '2026-05-23', 1, NULL, 'completed', 'ORD_382509842', 'pi_3TZvGI4Gjr7oYQNH0wSSXx8x', 3000, 'TXN_SUCCESS', NULL, 0, '2026-05-22 21:34:23', 'NPR'),
(220, 38, 9, '2026-05-22', '2026-05-23', 1, NULL, 'completed', 'ORD_389018339', 'pi_3TZvJE4Gjr7oYQNH1HVTL1tL', 2000, 'TXN_SUCCESS', NULL, 0, '2026-05-22 21:37:41', 'NPR');

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`sr_no`, `image`) VALUES
(5, 'IMG_93127.png'),
(6, 'IMG_99736.png'),
(8, 'IMG_40905.png'),
(9, 'IMG_55677.png'),
(11, 'IMG_73731.png'),
(13, 'IMG_74144.png');

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `pn2` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `pn2`, `email`, `fb`, `insta`, `tw`, `iframe`) VALUES
(1, 'Kalanki Kathmandu', 'https://maps.app.goo.gl/RDByLbB9Mtk7x6wh8', 9812426013, 9812426012, 'bookease26@gmail.com', 'https://www.facebook.com/', 'https://www.instagram.com/', 'https://x.com/?lang=en', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25892.250315452122!2d85.2806539!3d27.6931052!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb187a97f390b1:0xec3f47092df0d4ca!2sKalanki%2C%20Kathmandu%2044600!5e0!3m2!1sen!2snp!4v1739422972226!5m2!1sen!2snp');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `rate` decimal(10,6) NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `currency`, `rate`, `last_updated`) VALUES
(3253, 'AED', 3.672500, '2026-05-22 18:15:02'),
(3254, 'AFN', 62.999998, '2026-05-22 18:15:02'),
(3255, 'ALL', 82.322810, '2026-05-22 18:15:02'),
(3256, 'AMD', 367.970000, '2026-05-22 18:15:02'),
(3257, 'ANG', 1.790000, '2026-05-22 18:15:02'),
(3258, 'AOA', 913.116000, '2026-05-22 18:15:02'),
(3259, 'ARS', 1393.272500, '2026-05-22 18:15:02'),
(3260, 'AUD', 1.402381, '2026-05-22 18:15:02'),
(3261, 'AWG', 1.801250, '2026-05-22 18:15:02'),
(3262, 'AZN', 1.700000, '2026-05-22 18:15:02'),
(3263, 'BAM', 1.686820, '2026-05-22 18:15:02'),
(3264, 'BBD', 2.000000, '2026-05-22 18:15:02'),
(3265, 'BDT', 122.945161, '2026-05-22 18:15:02'),
(3266, 'BGN', 1.685720, '2026-05-22 18:15:02'),
(3267, 'BHD', 0.377249, '2026-05-22 18:15:02'),
(3268, 'BIF', 2977.412991, '2026-05-22 18:15:02'),
(3269, 'BMD', 1.000000, '2026-05-22 18:15:02'),
(3270, 'BND', 1.280682, '2026-05-22 18:15:02'),
(3271, 'BOB', 6.911838, '2026-05-22 18:15:02'),
(3272, 'BRL', 5.039900, '2026-05-22 18:15:02'),
(3273, 'BSD', 1.000000, '2026-05-22 18:15:02'),
(3274, 'BTC', 0.000013, '2026-05-22 18:15:02'),
(3275, 'BTN', 95.666951, '2026-05-22 18:15:02'),
(3276, 'BWP', 13.528887, '2026-05-22 18:15:02'),
(3277, 'BYN', 2.746451, '2026-05-22 18:15:02'),
(3278, 'BZD', 2.011799, '2026-05-22 18:15:02'),
(3279, 'CAD', 1.381844, '2026-05-22 18:15:02'),
(3280, 'CDF', 2299.158553, '2026-05-22 18:15:02'),
(3281, 'CHF', 0.785737, '2026-05-22 18:15:02'),
(3282, 'CLF', 0.022876, '2026-05-22 18:15:02'),
(3283, 'CLP', 900.370000, '2026-05-22 18:15:02'),
(3284, 'CNH', 6.796505, '2026-05-22 18:15:02'),
(3285, 'CNY', 6.797000, '2026-05-22 18:15:02'),
(3286, 'COP', 3687.630000, '2026-05-22 18:15:02'),
(3287, 'CRC', 452.710603, '2026-05-22 18:15:02'),
(3288, 'CUC', 1.000000, '2026-05-22 18:15:02'),
(3289, 'CUP', 25.750000, '2026-05-22 18:15:02'),
(3290, 'CVE', 95.100320, '2026-05-22 18:15:02'),
(3291, 'CZK', 20.926450, '2026-05-22 18:15:02'),
(3292, 'DJF', 178.126014, '2026-05-22 18:15:02'),
(3293, 'DKK', 6.439662, '2026-05-22 18:15:02'),
(3294, 'DOP', 58.958483, '2026-05-22 18:15:02'),
(3295, 'DZD', 133.081358, '2026-05-22 18:15:02'),
(3296, 'EGP', 52.917300, '2026-05-22 18:15:02'),
(3297, 'ERN', 15.000000, '2026-05-22 18:15:02'),
(3298, 'ETB', 161.261247, '2026-05-22 18:15:02'),
(3299, 'EUR', 0.861874, '2026-05-22 18:15:02'),
(3300, 'FJD', 2.205150, '2026-05-22 18:15:02'),
(3301, 'FKP', 0.744203, '2026-05-22 18:15:02'),
(3302, 'GBP', 0.744203, '2026-05-22 18:15:02'),
(3303, 'GEL', 2.660000, '2026-05-22 18:15:02'),
(3304, 'GGP', 0.744203, '2026-05-22 18:15:02'),
(3305, 'GHS', 11.547058, '2026-05-22 18:15:02'),
(3306, 'GIP', 0.744203, '2026-05-22 18:15:02'),
(3307, 'GMD', 73.000001, '2026-05-22 18:15:02'),
(3308, 'GNF', 8767.123216, '2026-05-22 18:15:02'),
(3309, 'GTQ', 7.627689, '2026-05-22 18:15:02'),
(3310, 'GYD', 209.243935, '2026-05-22 18:15:02'),
(3311, 'HKD', 7.835543, '2026-05-22 18:15:02'),
(3312, 'HNL', 26.613251, '2026-05-22 18:15:02'),
(3313, 'HRK', 6.493959, '2026-05-22 18:15:02'),
(3314, 'HTG', 130.992006, '2026-05-22 18:15:02'),
(3315, 'HUF', 309.027108, '2026-05-22 18:15:02'),
(3316, 'IDR', 9999.999999, '2026-05-22 18:15:02'),
(3317, 'ILS', 2.890650, '2026-05-22 18:15:02'),
(3318, 'IMP', 0.744203, '2026-05-22 18:15:02'),
(3319, 'INR', 95.605091, '2026-05-22 18:15:02'),
(3320, 'IQD', 1310.348581, '2026-05-22 18:15:02'),
(3321, 'IRR', 9999.999999, '2026-05-22 18:15:02'),
(3322, 'ISK', 123.760000, '2026-05-22 18:15:02'),
(3323, 'JEP', 0.744203, '2026-05-22 18:15:02'),
(3324, 'JMD', 157.909712, '2026-05-22 18:15:02'),
(3325, 'JOD', 0.709000, '2026-05-22 18:15:02'),
(3326, 'JPY', 159.200875, '2026-05-22 18:15:02'),
(3327, 'KES', 129.700000, '2026-05-22 18:15:02'),
(3328, 'KGS', 87.450000, '2026-05-22 18:15:02'),
(3329, 'KHR', 4008.781190, '2026-05-22 18:15:02'),
(3330, 'KMF', 423.999883, '2026-05-22 18:15:02'),
(3331, 'KPW', 900.000000, '2026-05-22 18:15:02'),
(3332, 'KRW', 1517.535971, '2026-05-22 18:15:02'),
(3333, 'KWD', 0.309516, '2026-05-22 18:15:02'),
(3334, 'KYD', 0.833588, '2026-05-22 18:15:02'),
(3335, 'KZT', 472.383885, '2026-05-22 18:15:02'),
(3336, 'LAK', 9999.999999, '2026-05-22 18:15:02'),
(3337, 'LBP', 9999.999999, '2026-05-22 18:15:02'),
(3338, 'LKR', 334.586757, '2026-05-22 18:15:02'),
(3339, 'LRD', 183.053100, '2026-05-22 18:15:02'),
(3340, 'LSL', 16.499491, '2026-05-22 18:15:02'),
(3341, 'LYD', 6.374519, '2026-05-22 18:15:02'),
(3342, 'MAD', 9.228089, '2026-05-22 18:15:02'),
(3343, 'MDL', 17.290662, '2026-05-22 18:15:02'),
(3344, 'MGA', 4202.823698, '2026-05-22 18:15:02'),
(3345, 'MKD', 53.132618, '2026-05-22 18:15:02'),
(3346, 'MMK', 2099.810000, '2026-05-22 18:15:02'),
(3347, 'MNT', 3569.470000, '2026-05-22 18:15:02'),
(3348, 'MOP', 8.073777, '2026-05-22 18:15:02'),
(3349, 'MRU', 39.972056, '2026-05-22 18:15:02'),
(3350, 'MUR', 47.379998, '2026-05-22 18:15:02'),
(3351, 'MVR', 15.400000, '2026-05-22 18:15:02'),
(3352, 'MWK', 1734.510632, '2026-05-22 18:15:02'),
(3353, 'MXN', 17.326168, '2026-05-22 18:15:02'),
(3354, 'MYR', 3.967900, '2026-05-22 18:15:02'),
(3355, 'MZN', 63.909994, '2026-05-22 18:15:02'),
(3356, 'NAD', 16.499491, '2026-05-22 18:15:02'),
(3357, 'NGN', 1372.650000, '2026-05-22 18:15:02'),
(3358, 'NIO', 36.810241, '2026-05-22 18:15:02'),
(3359, 'NOK', 9.272530, '2026-05-22 18:15:02'),
(3360, 'NPR', 153.066988, '2026-05-22 18:15:02'),
(3361, 'NZD', 1.708190, '2026-05-22 18:15:02'),
(3362, 'OMR', 0.384503, '2026-05-22 18:15:02'),
(3363, 'PAB', 1.000000, '2026-05-22 18:15:02'),
(3364, 'PEN', 3.410467, '2026-05-22 18:15:02'),
(3365, 'PGK', 4.362376, '2026-05-22 18:15:02'),
(3366, 'PHP', 61.608007, '2026-05-22 18:15:02'),
(3367, 'PKR', 278.494701, '2026-05-22 18:15:02'),
(3368, 'PLN', 3.653099, '2026-05-22 18:15:02'),
(3369, 'PYG', 6095.925532, '2026-05-22 18:15:02'),
(3370, 'QAR', 3.657220, '2026-05-22 18:15:02'),
(3371, 'RON', 4.522600, '2026-05-22 18:15:02'),
(3372, 'RSD', 101.178000, '2026-05-22 18:15:02'),
(3373, 'RUB', 71.547244, '2026-05-22 18:15:02'),
(3374, 'RWF', 1462.423649, '2026-05-22 18:15:02'),
(3375, 'SAR', 3.754239, '2026-05-22 18:15:02'),
(3376, 'SBD', 8.045182, '2026-05-22 18:15:02'),
(3377, 'SCR', 13.773127, '2026-05-22 18:15:02'),
(3378, 'SDG', 600.500000, '2026-05-22 18:15:02'),
(3379, 'SEK', 9.355702, '2026-05-22 18:15:02'),
(3380, 'SGD', 1.279712, '2026-05-22 18:15:02'),
(3381, 'SHP', 0.744203, '2026-05-22 18:15:02'),
(3382, 'SLE', 24.600000, '2026-05-22 18:15:02'),
(3383, 'SLL', 9999.999999, '2026-05-22 18:15:02'),
(3384, 'SOS', 571.645574, '2026-05-22 18:15:02'),
(3385, 'SRD', 37.154000, '2026-05-22 18:15:02'),
(3386, 'SSP', 130.260000, '2026-05-22 18:15:02'),
(3387, 'STD', 9999.999999, '2026-05-22 18:15:02'),
(3388, 'STN', 21.130526, '2026-05-22 18:15:02'),
(3389, 'SVC', 8.752350, '2026-05-22 18:15:02'),
(3390, 'SYP', 9999.999999, '2026-05-22 18:15:02'),
(3391, 'SZL', 16.495610, '2026-05-22 18:15:02'),
(3392, 'THB', 32.656500, '2026-05-22 18:15:02'),
(3393, 'TJS', 9.292774, '2026-05-22 18:15:02'),
(3394, 'TMT', 3.500000, '2026-05-22 18:15:02'),
(3395, 'TND', 2.928260, '2026-05-22 18:15:02'),
(3396, 'TOP', 2.407760, '2026-05-22 18:15:02'),
(3397, 'TRY', 45.742881, '2026-05-22 18:15:02'),
(3398, 'TTD', 6.789426, '2026-05-22 18:15:02'),
(3399, 'TWD', 31.411999, '2026-05-22 18:15:02'),
(3400, 'TZS', 2629.998000, '2026-05-22 18:15:02'),
(3401, 'UAH', 44.271560, '2026-05-22 18:15:02'),
(3402, 'UGX', 3787.943985, '2026-05-22 18:15:02'),
(3403, 'USD', 1.000000, '2026-05-22 18:15:02'),
(3404, 'UYU', 39.934447, '2026-05-22 18:15:02'),
(3405, 'UZS', 9999.999999, '2026-05-22 18:15:02'),
(3406, 'VES', 526.210936, '2026-05-22 18:15:02'),
(3407, 'VND', 9999.999999, '2026-05-22 18:15:02'),
(3408, 'VUV', 119.389000, '2026-05-22 18:15:02'),
(3409, 'WST', 2.744220, '2026-05-22 18:15:02'),
(3410, 'XAF', 565.352316, '2026-05-22 18:15:02'),
(3411, 'XAG', 0.013163, '2026-05-22 18:15:02'),
(3412, 'XAU', 0.000221, '2026-05-22 18:15:02'),
(3413, 'XCD', 2.702550, '2026-05-22 18:15:02'),
(3414, 'XCG', 1.802822, '2026-05-22 18:15:02'),
(3415, 'XDR', 0.736052, '2026-05-22 18:15:02'),
(3416, 'XOF', 565.352316, '2026-05-22 18:15:02'),
(3417, 'XPD', 0.000738, '2026-05-22 18:15:02'),
(3418, 'XPF', 102.848932, '2026-05-22 18:15:02'),
(3419, 'XPT', 0.000521, '2026-05-22 18:15:02'),
(3420, 'YER', 238.649953, '2026-05-22 18:15:02'),
(3421, 'ZAR', 16.448985, '2026-05-22 18:15:02'),
(3422, 'ZMW', 18.830317, '2026-05-22 18:15:02'),
(3423, 'ZWG', 25.362600, '2026-05-22 18:15:02'),
(3424, 'ZWL', 322.000000, '2026-05-22 18:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `name`, `description`) VALUES
(26, 'IMG_58711.svg', 'Air Conditioner', 'The room AC in your hotel room is a climate control system that provides both cooling and heating. You can use it to cool the room during hot weather or warm it up during colder seasons. Simply adjust the temperature settings using the remote or wall'),
(27, 'IMG_36481.svg', 'WIFI', 'The hotel room is equipped with Wi-Fi to keep you connected during your stay. You can use it to browse the internet, check emails, stream videos, or work online. The network name and password are usually provided at check-in or found in the room. Wi-'),
(28, 'IMG_48417.svg', 'Television(TV)', 'The hotel room includes a TV for your entertainment. You can watch local and international channels, news, movies, and more. Some TVs may also offer smart features like streaming apps (e.g., YouTube, Netflix) for added convenience. The remote control'),
(29, 'IMG_95786.svg', 'SPA', 'The hotel offers spa services to help you relax and unwind during your stay. You can enjoy a variety of treatments such as massages, facials, and body therapies, all designed to refresh your body and mind. Spa facilities may also include a sauna, ste'),
(30, 'IMG_68823.svg', 'Bar', 'The hotel room includes a mini bar, a small fridge stocked with beverages like soft drinks, bottled water, beer, and sometimes small liquor bottles. It may also include snacks like chips, chocolates, or nuts. Items in the mini bar are for your conven'),
(31, 'IMG_44447.svg', 'Heater', 'The room heater helps keep your space warm and cozy during cold weather. You can control the temperature to stay comfortable, especially in chilly seasons or cooler climates. It ensures a pleasant environment for a restful stay.'),
(32, 'IMG_62388.svg', 'Geyser', 'A geyser is a water heater installed in the bathroom that provides hot water for showers and washing. It ensures you have warm water whenever you need it, especially during cold weather, for a comfortable and refreshing experience.');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `name`) VALUES
(13, 'bedroom'),
(14, 'balcony'),
(15, 'kitchen'),
(17, 'sofa');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points_balance` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_points`
--

INSERT INTO `loyalty_points` (`id`, `user_id`, `points_balance`, `created_at`, `updated_at`) VALUES
(1, 33, 800, '2025-05-19 16:22:33', '2025-05-25 08:44:45'),
(6, 38, 581, '2026-03-21 06:27:32', '2026-05-22 15:53:15'),
(7, 39, 3088, '2026-03-21 09:59:57', '2026-04-18 18:20:53'),
(31, 50, 600, '2026-04-04 07:25:12', '2026-04-04 07:25:39'),
(44, 51, 0, '2026-04-05 03:13:40', '2026-04-05 03:13:40'),
(45, 52, 0, '2026-04-05 05:37:00', '2026-04-05 05:37:00'),
(46, 53, 0, '2026-04-05 09:56:01', '2026-04-05 09:56:01'),
(47, 54, 0, '2026-04-08 05:05:09', '2026-04-08 05:05:09'),
(50, 55, 500, '2026-04-19 05:26:35', '2026-04-19 05:30:07'),
(52, 56, 494, '2026-05-18 08:07:12', '2026-05-18 08:22:30'),
(54, 57, 500, '2026-05-22 14:30:41', '2026-05-22 14:36:20'),
(56, 58, 500, '2026-05-22 14:39:34', '2026-05-22 14:39:34'),
(57, 59, 360, '2026-05-22 14:45:15', '2026-05-22 15:16:01');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_rewards`
--

CREATE TABLE `loyalty_rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `points_required` int(11) NOT NULL,
  `discount_percent` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_rewards`
--

INSERT INTO `loyalty_rewards` (`id`, `name`, `description`, `points_required`, `discount_percent`, `is_active`, `created_at`) VALUES
(1, 'Bronze Discount', 'Get 5% off on your next booking', 100, 5, 1, '2025-05-19 16:21:42'),
(2, 'Silver Discount', 'Get 10% off on your next booking', 250, 10, 1, '2025-05-19 16:21:42'),
(3, 'Gold Discount', 'Get 15% off on your next booking', 500, 15, 1, '2025-05-19 16:21:42'),
(4, 'Platinum Discount', 'Get 20% off on your next booking', 1000, 20, 1, '2025-05-19 16:21:42'),
(5, 'Free Night', 'Get one free night stay', 2000, 100, 1, '2025-05-19 16:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_transactions`
--

CREATE TABLE `loyalty_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `type` enum('earn','redeem') NOT NULL,
  `description` varchar(255) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_transactions`
--

INSERT INTO `loyalty_transactions` (`id`, `user_id`, `points`, `type`, `description`, `booking_id`, `created_at`) VALUES
(3, 33, 500, 'redeem', 'Redeemed Gold Discount', NULL, '2025-05-20 11:29:43'),
(4, 33, 600, 'earn', 'Points earned from booking', 165, '2025-05-24 11:04:59'),
(5, 33, 300, 'earn', 'Points earned from booking', 166, '2025-05-24 11:08:04'),
(6, 33, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2025-05-25 08:44:45'),
(7, 38, 100, 'earn', 'Points earned from booking', 167, '2026-03-21 06:27:32'),
(8, 39, 300, 'earn', 'Points earned from booking', 168, '2026-03-21 09:59:57'),
(9, 39, 300, 'earn', 'Points earned from booking', 169, '2026-03-21 10:05:16'),
(10, 39, 599, 'earn', 'Points earned from booking', 170, '2026-03-21 10:07:59'),
(11, 39, 300, 'earn', 'Points earned from booking', 171, '2026-03-21 10:11:16'),
(12, 39, 500, 'earn', 'Points earned from booking', 172, '2026-03-21 15:43:39'),
(13, 39, 100, 'earn', 'Points earned from booking', 173, '2026-03-21 15:46:52'),
(14, 39, 200, 'earn', 'Points earned from booking', 174, '2026-03-22 05:32:01'),
(15, 39, 99, 'earn', 'Points earned from booking', 175, '2026-03-22 05:58:55'),
(16, 39, 99, 'earn', 'Points earned from booking', 176, '2026-03-22 06:27:19'),
(17, 39, 200, 'earn', 'Points earned from booking', 177, '2026-03-22 09:28:41'),
(18, 39, 99, 'earn', 'Points earned from booking', 178, '2026-03-22 09:29:31'),
(19, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-03-24 01:49:16'),
(20, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-03-24 01:49:23'),
(21, 39, 100, 'earn', 'Points earned from booking', 179, '2026-03-24 01:49:53'),
(22, 39, 300, 'earn', 'Points earned from booking', 180, '2026-03-24 02:28:23'),
(23, 39, 99, 'earn', 'Points earned from booking', 181, '2026-03-24 02:30:17'),
(24, 39, 300, 'earn', 'Points earned from booking', 182, '2026-03-25 03:08:41'),
(25, 39, 99, 'earn', 'Points earned from booking', 183, '2026-03-26 02:17:03'),
(26, 39, 99, 'earn', 'Points earned from booking', 184, '2026-03-29 01:49:23'),
(27, 39, 199, 'earn', 'Points earned from booking', 185, '2026-03-29 03:14:10'),
(28, 39, 300, 'earn', 'Points earned from booking', 186, '2026-03-29 04:07:01'),
(29, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-03-29 04:09:54'),
(30, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-03-29 04:10:00'),
(31, 39, 1000, 'redeem', 'Redeemed Platinum Discount', NULL, '2026-03-29 04:17:16'),
(32, 39, 80, 'earn', 'Points earned from booking', 187, '2026-03-29 04:17:16'),
(33, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-03-29 04:25:43'),
(34, 39, 250, 'redeem', 'Redeemed Silver Discount', NULL, '2026-03-29 09:19:23'),
(35, 39, 90, 'earn', 'Points earned from booking', 188, '2026-03-29 09:19:23'),
(36, 38, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-04-03 13:03:25'),
(37, 38, 190, 'earn', 'Points earned from booking', 189, '2026-04-03 13:03:25'),
(38, 38, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-04-03 13:06:57'),
(39, 38, 190, 'earn', 'Points earned from booking', 190, '2026-04-03 13:06:57'),
(40, 38, 300, 'earn', 'Points earned from booking', 191, '2026-04-03 13:25:05'),
(41, 50, 300, 'earn', 'Points earned from booking', 192, '2026-04-04 07:25:31'),
(42, 50, 300, 'earn', 'Points earned from booking', 193, '2026-04-04 07:25:39'),
(43, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-04-04 08:14:18'),
(44, 39, 285, 'earn', 'Points earned from booking', 194, '2026-04-04 08:14:18'),
(45, 39, 285, 'earn', 'Points earned from booking', 195, '2026-04-04 08:23:13'),
(46, 38, 500, 'redeem', 'Redeemed Gold Discount', NULL, '2026-04-04 13:20:35'),
(47, 39, 100, 'redeem', 'Redeemed Bronze Discount', NULL, '2026-04-04 13:24:39'),
(48, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-976A880E', NULL, '2026-04-04 13:33:58'),
(49, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-23A1643D', NULL, '2026-04-04 13:34:03'),
(50, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-7E0887EF', NULL, '2026-04-04 13:34:32'),
(51, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-C5D95B5D', NULL, '2026-04-04 13:39:26'),
(52, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-99D1FC67', NULL, '2026-04-04 13:39:28'),
(53, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-9CD96DBF', NULL, '2026-04-04 13:39:30'),
(54, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-3F1F5E1F', NULL, '2026-04-04 13:41:42'),
(55, 39, 1, 'earn', 'Points earned from booking', 198, '2026-04-04 13:56:35'),
(56, 39, 1, 'earn', 'Points earned from booking', 198, '2026-04-04 13:56:51'),
(57, 39, 1, 'earn', 'Points earned from booking', 199, '2026-04-04 14:06:23'),
(58, 39, 1, 'earn', 'Points earned from booking', 201, '2026-04-04 14:14:36'),
(59, 38, 2, 'earn', 'Points earned from booking', 203, '2026-04-04 14:23:46'),
(60, 38, 99, 'earn', 'Points earned from booking', 204, '2026-04-04 14:28:55'),
(61, 38, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-C2266D61', NULL, '2026-04-04 14:30:31'),
(62, 39, 104, 'earn', 'Points earned from booking', 200, '2026-04-04 16:15:59'),
(63, 39, 104, 'earn', 'Points earned from booking', 202, '2026-04-04 16:15:59'),
(64, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-E0C559C3', NULL, '2026-04-04 16:16:23'),
(65, 39, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-BFBE9A7F', NULL, '2026-04-05 10:02:27'),
(66, 39, 194, 'earn', 'Points earned from booking', 206, '2026-04-08 09:30:22'),
(67, 39, 500, 'earn', 'Welcome bonus - First login reward', NULL, '2026-04-18 18:20:53'),
(68, 55, 500, 'earn', 'Welcome bonus - First login reward', NULL, '2026-04-19 05:30:07'),
(69, 56, 500, 'earn', 'Welcome bonus - First login reward', NULL, '2026-05-18 08:07:12'),
(70, 56, 100, 'redeem', 'Redeemed Bronze Discount - Voucher: VOUCHER-867DD38A', NULL, '2026-05-18 08:22:30'),
(71, 56, 94, 'earn', 'Points earned from booking', 214, '2026-05-18 08:22:30'),
(72, 57, 500, 'earn', 'Welcome bonus - New member reward', NULL, '2026-05-22 14:36:20'),
(73, 58, 500, 'earn', 'Welcome bonus - New member reward', NULL, '2026-05-22 14:39:34'),
(74, 59, 500, 'earn', 'Welcome bonus - New member reward', NULL, '2026-05-22 14:45:15'),
(75, 59, 250, 'redeem', 'Redeemed Silver Discount - Voucher: VOUCHER-DF6123F9', NULL, '2026-05-22 14:50:37'),
(76, 59, 90, 'earn', 'Points earned from booking', 215, '2026-05-22 14:50:37'),
(77, 59, 250, 'redeem', 'Redeemed Silver Discount - Voucher: VOUCHER-18881580', NULL, '2026-05-22 15:16:01'),
(78, 59, 270, 'earn', 'Points earned from booking', 216, '2026-05-22 15:16:01'),
(79, 38, 300, 'earn', 'Points earned from booking', 219, '2026-05-22 15:50:12'),
(80, 38, 200, 'earn', 'Points earned from booking', 220, '2026-05-22 15:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `promo_banner`
--

CREATE TABLE `promo_banner` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Welcome to BookEase!',
  `subtitle` varchar(255) NOT NULL DEFAULT 'Exclusive New Member Offer',
  `description` text NOT NULL,
  `loyalty_points` int(11) NOT NULL DEFAULT 500,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `bg_image` varchar(255) NOT NULL DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `occasion` varchar(50) NOT NULL DEFAULT 'welcome_bonus',
  `offer_text` varchar(100) NOT NULL DEFAULT '',
  `offer_label` varchar(100) NOT NULL DEFAULT 'LOYALTY POINTS',
  `badge_label` varchar(100) NOT NULL DEFAULT 'NEW MEMBER EXCLUSIVE',
  `cta_text` varchar(100) NOT NULL DEFAULT '',
  `cta_url` varchar(255) NOT NULL DEFAULT '',
  `target_audience` varchar(50) NOT NULL DEFAULT 'new_members',
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_banner`
--

INSERT INTO `promo_banner` (`id`, `title`, `subtitle`, `description`, `loyalty_points`, `is_active`, `bg_image`, `updated_at`, `occasion`, `offer_text`, `offer_label`, `badge_label`, `cta_text`, `cta_url`, `target_audience`, `expiry_date`) VALUES
(1, 'Welcome to BookEase!', 'Exclusive New Member Offer', 'Start your journey with us and enjoy complimentary loyalty points on your very first login. Redeem them for exclusive discounts on future bookings!', 500, 1, 'banner_rewards_hero.png', '2026-04-18 19:05:36', 'welcome_bonus', '', 'LOYALTY POINTS', 'NEW MEMBER EXCLUSIVE', '', '', 'new_members', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rating_review`
--

CREATE TABLE `rating_review` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(200) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_review`
--

INSERT INTO `rating_review` (`sr_no`, `booking_id`, `room_id`, `user_id`, `rating`, `review`, `seen`, `datentime`) VALUES
(17, 178, 8, 39, 5, 'Its was good. Hospitality was Excellent.', 1, '2026-04-03 15:34:12'),
(18, 175, 8, 39, 5, 'Room were pretty good.', 1, '2026-04-03 15:53:23'),
(19, 190, 9, 38, 5, 'Excellent view and good food. Must visit', 1, '2026-04-03 19:04:13'),
(20, 193, 10, 50, 4, 'View was pretty. Food was not up to the mark!', 1, '2026-04-04 13:13:37'),
(21, 205, 10, 51, 5, 'Ehh Dami layo', 1, '2026-04-05 09:01:05'),
(22, 215, 8, 59, 4, 'view is quite good and room services are impressed.', 1, '2026-05-22 20:40:16');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` varchar(350) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed`) VALUES
(8, 'Deluxe Garden Room', 100, 1000, 4, 2, 1, 'Bright room with garden views, queen bed, work desk, and rainfall shower. Ideal for couples or short business stays.', 1, 0),
(9, 'Executive City View', 150, 2000, 4, 2, 2, 'Spacious executive room with city skyline views, king bed, seating area, and premium bath amenities.', 1, 0),
(10, 'Premium Family Suite', 200, 3000, 1, 2, 2, 'Large suite with separate lounge, two beds, extra storage, and family-friendly layout—perfect for longer stays.', 1, 0),
(26, 'Super', 400, 6000, 5, 3, 2, 'Super', 1, 1),
(28, 'Super Dexule', 205, 2000, 2, 2, 4, 'Big Rooms for the Children', 0, 0),
(29, 'Super Dexule', 205, 2000, 2, 2, 4, 'Big Spacious rooms for Children.', 0, 0),
(30, 'Nightout', 120, 1000, 3, 2, 1, 'For 1 Day only', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_360_images`
--

CREATE TABLE `room_360_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_360_images`
--

INSERT INTO `room_360_images` (`id`, `room_id`, `image`) VALUES
(41, 30, '360_30_default.jpg'),
(68, 8, '360_8_hotel_room.jpg'),
(69, 9, '360_9_exec_hotel_unique.jpg'),
(70, 10, '360_10_family_hotel_unique.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `room_facilities`
--

CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_facilities`
--

INSERT INTO `room_facilities` (`sr_no`, `room_id`, `facilities_id`) VALUES
(448, 10, 26),
(449, 10, 27),
(450, 10, 28),
(451, 10, 30),
(459, 9, 26),
(460, 9, 27),
(461, 9, 28),
(463, 29, 26),
(464, 29, 27),
(465, 29, 31),
(466, 8, 27),
(467, 8, 28),
(468, 8, 31),
(469, 8, 32),
(470, 30, 27),
(471, 30, 28),
(472, 30, 32);

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`sr_no`, `room_id`, `features_id`) VALUES
(367, 10, 13),
(368, 10, 14),
(369, 10, 17),
(374, 9, 13),
(375, 9, 14),
(376, 9, 17),
(378, 29, 13),
(379, 29, 14),
(380, 29, 17),
(381, 8, 13),
(382, 8, 14),
(383, 8, 15),
(384, 8, 17),
(385, 30, 13);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0,
  `scale` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`sr_no`, `room_id`, `image`, `thumb`, `scale`) VALUES
(3, 9, 'ROOM_9_executive.jpg', 1, 1),
(4, 10, 'ROOM_10_suite.jpg', 1, 1),
(32, 8, 'ROOM_8_deluxe.jpg', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(250) NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'BookEase', 'BookEase is your ultimate hotel booking platform, offering a wide range of accommodations across Nepal. Find affordable, comfortable stays with easy booking, reliable service, and seamless experiences for all travelers.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `team_details`
--

CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_details`
--

INSERT INTO `team_details` (`sr_no`, `name`, `picture`) VALUES
(15, 'Rambo', 'IMG_69943.jpg'),
(18, 'Prince', 'IMG_12481.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_cred`
--

CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expire` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `banner_eligible` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cred`
--

INSERT INTO `user_cred` (`id`, `name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `token`, `t_expire`, `status`, `datentime`, `banner_eligible`) VALUES
(38, 'Prince Thapaliya', 'prince.thapaliya69@gmail.com', 'Khasibazars', '1234567899', 46500, '2003-04-04', 'IMG_40261.png', '$2y$10$R.NS2XcaxlMjb3m3ReGhV.c/4Bp2fTLCZG5g3mQgktjsU0WLslaVC', 1, NULL, NULL, 1, '2026-03-21 12:08:54', 0),
(39, 'Prince Thapaliya', 'np03cs4a230422@heraldcollege.edu.np', 'kalanki', '9812426016', 0, '2000-01-01', 'IMG_49199.png', '$2y$10$EDvp4N98KsxDB0M3dJKsoevLTcTa/wqeS5xN7hXwqM3kyVYAZ.VfO', 1, '', NULL, 1, '2026-03-21 12:51:53', 0),
(40, 'Greesh', 'greeshdahal432@gmail.com', 'Kritipur', '9860461600', 46500, '2003-01-04', 'IMG_63636.png', '$2y$10$VTyLMyrik.F.yAIW5fcEZu/2lpBngYMShBcjG6R1Cd3rYKsV56Vy2', 0, '8063967681a282a514a7595fccdd844d', NULL, 1, '2026-03-29 08:57:42', 0),
(41, 'John', 'johncarliblue@gmail.com', 'Khasibazar', '9848059995', 46500, '2005-01-08', 'IMG_21715.jpeg', '$2y$10$6ueNmKyZ2dvaDsKnx1iATumLYV0u5WvvszyAtsOephNQeves4SRfa', 0, 'd6d62c0ab25399dae29d6c5905dc7bef', NULL, 1, '2026-03-29 09:07:38', 0),
(42, 'Valo', 'rantvalo488@gmail.com', 'Chobar', '9814759310', 46500, '2015-01-01', 'IMG_86890.jpg', '$2y$10$yIVFblaHQVS8QJhN6YMkueR0BJYV8ZuxbA.6Q4lFhGx1E4xqX2jgq', 0, 'a6a0b136578d983846097c37935100c7', NULL, 1, '2026-03-29 09:12:02', 0),
(44, 'Ayyusha', 'aayusha744@gmail.com', 'kalanki', '9812425016', 46500, '2026-03-05', 'IMG_37016.png', '$2y$10$h/RQX5PeiYUK.OmakqhsEO90fuav4dhAUnooFji7S6h9khlf838la', 0, '37e3e5cee184fdb48493d0ab616d32af', NULL, 1, '2026-03-29 16:39:23', 0),
(50, 'Shreya Malla', 'shreyamalla14@gmail.com', 'Sukedhara', '9849860305', 400247, '2007-12-26', 'IMG_86682.png', '$2y$10$w9NOQwJOK9vxFZfUdmIireh3OOtDIgKlQLrLF.y9B24LQ0E5C8Ooq', 1, '', NULL, 1, '2026-04-04 13:04:49', 0),
(51, 'Bomkar', 'omkarpoudel06@gmail.com', 'Kalanki', '9812401265', 46500, '2008-03-25', 'IMG_20352.png', '$2y$10$TiJ7xxVNevty.wnVQKVBeubMoMnNZ6degA15oMcf0aCUMz1u/W446', 1, '', NULL, 1, '2026-04-05 08:57:34', 0),
(52, 'Ayush', 'ayushneupane2023@gmail.com', 'Kapan', '9854655262', 123456, '2008-04-03', 'IMG_14671.jpg', '$2y$10$c0UOzrfn7iN35Pdazu4UwuiVSBwUavtEGcwU/4bRcUJMJJb0eAfLW', 1, '', NULL, 1, '2026-04-05 11:20:15', 0),
(53, 'Prince Thapaliya', 'princechina273@gmail.com', 'kalanki', '9814526665', 46500, '2008-04-01', 'IMG_62120.jpg', '$2y$10$bDq6QJoa6BOpVr528ZwMJubLMTwiLnxzYs3wkEhB.roW4H6Johk12', 1, '', NULL, 1, '2026-04-05 15:39:09', 0),
(54, 'Priyamshu', 'priyamshukc13@gmail.com', 'Jorpati', '9869602487', 46500, '2008-04-01', 'IMG_11568.png', '$2y$10$qDOVQkZRp8BGHHwy8rsoDOPg1EwxokDY4dYuOo9mcA8LyF3RQ2rlG', 1, '', NULL, 1, '2026-04-08 10:49:11', 0),
(55, 'Book Ease', 'bookease26@gmail.com', '', '0', 0, '2000-01-01', 'GOOGLE_107335521247296234563.jpg', '$2y$10$goVOPZG44rDgTebdsLSYv.mQdIgrEvQZqfreejw9vN0/nMU0aoo0K', 1, '', NULL, 1, '2026-04-19 11:10:53', 1),
(56, 'Arpita', 'arpitalutel@gmail.com', 'Naxal', '9848056665', 46500, '2008-05-01', 'IMG_48243.png', '$2y$10$AujFAEgoH3Emym0B/JZMMOD0l9S4PwfKm1nDj0510o6INxR97JsCq', 1, '', NULL, 1, '2026-05-18 13:51:12', 1),
(57, 'Yeek09', 'msykprince69@gmail.com', 'kalanki', '9812426017', 446610, '2008-05-01', 'IMG_58406.png', '$2y$10$tKuDPuLBQkUGzUJuMoip5.7NqmdHxA8hl3M4p9vMZKqi8ICujBi3O', 1, '', NULL, 1, '2026-05-22 20:14:43', 1),
(58, 'yoniv', 'yoniv84226@bittnex.com', 'USA', '3165135135', 1140, '2008-05-01', 'IMG_48328.png', '$2y$10$FSTKioI/vv8vGvI0FSki8eQsXw1QB0D.6MTIHgOI9NxMWo84/VsLq', 1, '', NULL, 1, '2026-05-22 20:24:13', 1),
(59, 'meroxek', 'meroxek669@bittnex.com', 'Austria', '7894561230', 98, '2008-05-06', 'default.svg', '$2y$10$lIRTyiKweqsGFecCNrxCIeGxuo3Ofa3uvVo1md88lFZnIHlXGXul2', 1, '', NULL, 1, '2026-05-22 20:29:52', 1),
(60, 'Veji', 'vejipam140@nriza.com', 'Pakistan', '7412369852', 1003, '2008-05-05', 'default.svg', '$2y$10$0J/CvDJx8sA.sZAPzsQDjuUl/KiscrS.wUHGGt0VqTCoZmXxF85L2', 0, '572599', '2026-05-23', 1, '2026-05-23 13:28:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_queries`
--

CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_queries`
--

INSERT INTO `user_queries` (`sr_no`, `name`, `email`, `subject`, `message`, `datentime`, `seen`) VALUES
(17, 'Prince Thapaliya', 'np03cs4a230422@heraldcollege.edu.np', 'related room booking', 'my payment is failed', '2026-04-04 14:33:13', 1),
(22, 'Prince', 'prince.thapaliya69@gmail.com', 'payment issue', 'i cannot make the payment.', '2026-04-04 14:53:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `voucher_code` varchar(20) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `points_used` int(11) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_vouchers`
--

INSERT INTO `user_vouchers` (`id`, `user_id`, `reward_id`, `voucher_code`, `discount_percent`, `points_used`, `is_used`, `created_at`, `expires_at`, `used_at`) VALUES
(1, 39, 1, 'VOUCHER-976A880E', 5.00, 100, 0, '2026-04-04 19:18:58', '2026-05-04 19:18:58', NULL),
(2, 39, 1, 'VOUCHER-23A1643D', 5.00, 100, 0, '2026-04-04 19:19:03', '2026-05-04 19:19:03', NULL),
(3, 39, 1, 'VOUCHER-7E0887EF', 5.00, 100, 0, '2026-04-04 19:19:32', '2026-05-04 19:19:32', NULL),
(4, 39, 1, 'VOUCHER-C5D95B5D', 5.00, 100, 0, '2026-04-04 19:24:26', '2026-05-04 19:24:26', NULL),
(5, 39, 1, 'VOUCHER-99D1FC67', 5.00, 100, 0, '2026-04-04 19:24:28', '2026-05-04 19:24:28', NULL),
(6, 39, 1, 'VOUCHER-9CD96DBF', 5.00, 100, 0, '2026-04-04 19:24:30', '2026-05-04 19:24:30', NULL),
(7, 39, 1, 'VOUCHER-3F1F5E1F', 5.00, 100, 0, '2026-04-04 19:26:42', '2026-05-04 19:26:42', NULL),
(8, 38, 1, 'VOUCHER-C2266D61', 5.00, 100, 0, '2026-04-04 20:15:31', '2026-05-04 20:15:31', NULL),
(9, 39, 1, 'VOUCHER-E0C559C3', 5.00, 100, 0, '2026-04-04 22:01:23', '2026-05-04 22:01:23', NULL),
(10, 39, 1, 'VOUCHER-BFBE9A7F', 5.00, 100, 0, '2026-04-05 15:47:27', '2026-05-05 15:47:27', NULL),
(11, 56, 1, 'VOUCHER-867DD38A', 5.00, 100, 0, '2026-05-18 14:07:30', '2026-06-17 14:07:30', NULL),
(12, 59, 2, 'VOUCHER-DF6123F9', 10.00, 250, 0, '2026-05-22 20:35:37', '2026-06-21 20:35:37', NULL),
(13, 59, 2, 'VOUCHER-18881580', 10.00, 250, 0, '2026-05-22 21:01:01', '2026-06-21 21:01:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_cred`
--
ALTER TABLE `admin_cred`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `banner_claims`
--
ALTER TABLE `banner_claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_id` (`user_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `booking_order`
--
ALTER TABLE `booking_order`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_currency` (`currency`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `loyalty_rewards`
--
ALTER TABLE `loyalty_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `promo_banner`
--
ALTER TABLE `promo_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_review`
--
ALTER TABLE `rating_review`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_360_images`
--
ALTER TABLE `room_360_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `facilities id` (`facilities_id`),
  ADD KEY `room id` (`room_id`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `features id` (`features_id`),
  ADD KEY `rm id` (`room_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_queries`
--
ALTER TABLE `user_queries`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_code_unique` (`voucher_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_cred`
--
ALTER TABLE `admin_cred`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banner_claims`
--
ALTER TABLE `banner_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `booking_order`
--
ALTER TABLE `booking_order`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5056;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `loyalty_rewards`
--
ALTER TABLE `loyalty_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `promo_banner`
--
ALTER TABLE `promo_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rating_review`
--
ALTER TABLE `rating_review`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `room_360_images`
--
ALTER TABLE `room_360_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=473;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=386;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_details`
--
ALTER TABLE `team_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `user_queries`
--
ALTER TABLE `user_queries`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_order`
--
ALTER TABLE `booking_order`
  ADD CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  ADD CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
