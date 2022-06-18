-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2022 at 03:38 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aa_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_number` varchar(10) NOT NULL,
  `bank` varchar(50) NOT NULL,
  `default_status` varchar(10) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `unique_id`, `account_name`, `account_number`, `bank`, `default_status`, `added_date`, `last_modified`, `status`) VALUES
(2, 'gi0zvCaE8v64q2ABHMK6', 'Nwoye Emmanuel Aneku', '0000000000', 'Guaranty Trust Bank', 'Yes', '2021-11-13 23:09:54', '2021-11-13 23:10:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `shipping_fee_unique_id` varchar(20) DEFAULT NULL,
  `pickup_location` tinyint(1) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `product_unique_id`, `quantity`, `shipping_fee_unique_id`, `pickup_location`, `added_date`, `last_modified`, `status`) VALUES
(1, 'sz5GGmCNkDM4B5xfE2O6', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 5, 'Zr0ijzQjOolWjsUBCHA2', 0, '2021-11-09 03:03:07', '2021-11-09 03:08:30', 2),
(2, 'qsxupQeBemZRsJEcjfmw', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 2, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-09 03:07:00', '2021-11-09 03:08:30', 2),
(3, 'YfNy8ROevqnzU3oZ3TCm', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 5, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-09 03:52:43', '2021-11-09 03:54:33', 2),
(4, 'f2RRUJE8izY0ZknemLee', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 2, 'Zr0ijzQjOolWjsUBCHA2', 0, '2021-11-09 03:53:00', '2021-11-09 03:54:33', 2),
(5, 'B4dEtRMJbpvsy0RcVzmq', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 5, 'ArcpsF6zTWWPgjKJO7Uy', 0, '2021-11-09 03:53:19', '2021-11-09 03:54:33', 2),
(6, 'raYiJeoGY0jUCMa70XpE', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 10, 'ArcpsF6zTWWPgjKJO7Uy', 0, '2021-11-09 05:15:12', '2021-11-09 05:17:33', 2),
(7, 'DkjtH9dOOaYsvtvM1Ra9', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 6, 'Zr0ijzQjOolWjsUBCHA2', 0, '2021-11-09 05:15:21', '2021-11-09 05:17:33', 2),
(8, 'HXAXR4oMa1tA20oMyCsw', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 2, 'ArcpsF6zTWWPgjKJO7Uy', 0, '2021-11-09 05:26:37', '2021-11-09 05:27:38', 2),
(9, 'xI3MpAmA6mv4nQBalSyf', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 5, 'ArcpsF6zTWWPgjKJO7Uy', 0, '2021-11-09 05:31:31', '2021-11-09 05:32:04', 2),
(10, 'k1CGUKbLxm57An2y2Zlx', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 5, 'Zr0ijzQjOolWjsUBCHA2', 0, '2021-11-09 05:32:44', '2021-11-09 05:33:08', 2),
(11, 'k0kv9CKGj5AOsw0diSOr', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 7, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-09 05:33:49', '2021-11-09 05:34:26', 2),
(12, 'sXufNfnZBawjEorsWXDo', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 5, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-09 06:37:46', '2021-11-09 07:00:02', 2),
(15, 'v6IUUypVlec398zC5T1G', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'jZ5d1PgCf0cU4YgdDp4r', 6, '3q2xnybmtdTH44B5CS5p', 0, '2021-11-09 06:49:17', '2021-11-09 07:00:02', 2),
(16, 'E0OW5xQ82qf5vK0e8oGg', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 6, 'wcjrnAvv37jxa6Gy0h1x', 0, '2021-11-09 06:49:24', '2021-11-09 07:00:02', 2),
(17, 'cPOUUcj6UWauO8tRSfqi', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 10, 'wcjrnAvv37jxa6Gy0h1x', 0, '2021-11-10 17:44:13', '2021-11-10 17:48:41', 2),
(18, '0QpkDLIaNFC2rA6FPGb4', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 10, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-10 17:44:49', '2021-11-10 17:48:41', 2),
(19, 'ecqL4U1ZJCi7ERVITLdj', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 10, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-11 13:11:53', '2021-11-11 13:13:47', 2),
(20, 'M2wMQpOk1JH02DihL9Io', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 5, 'wcjrnAvv37jxa6Gy0h1x', 0, '2021-11-11 13:12:04', '2021-11-11 13:13:47', 2),
(21, 'X9Inu83F7wi2kNw5iFJD', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 5, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-11 13:20:19', '2021-11-11 13:20:55', 2),
(22, 'NmN0pEedGw91XRzZa4NA', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 5, 'wcjrnAvv37jxa6Gy0h1x', 0, '2021-11-11 13:20:24', '2021-11-11 13:20:55', 2),
(23, 'Qnoogmacuo0kbNBALEBy', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 5, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-11 13:26:16', '2021-11-11 13:26:51', 2),
(24, 'ixPPGJ7qDz9vmuQVrMGl', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 5, 'wcjrnAvv37jxa6Gy0h1x', 0, '2021-11-11 13:26:20', '2021-11-11 13:26:51', 2),
(25, 'SYVZZ3ovsVAiUOwQTxK8', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 6, 'RPdDkSzuXQv0a1rFbpTt', 0, '2021-11-22 03:38:49', '2021-11-22 18:57:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `stripped` varchar(100) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `unique_id`, `user_unique_id`, `name`, `stripped`, `added_date`, `last_modified`, `status`) VALUES
(1, '7doAq0l8LTSncbtYF5ul', 'deltorox', 'Native Foods', 'native-foods', '2021-11-07 15:08:30', '2021-11-07 15:08:30', 1),
(2, 'wGjN8YQRRrgMf67sQ3yS', 'deltorox', 'Snacks', 'snacks', '2021-11-07 15:08:35', '2021-11-07 15:08:35', 1),
(4, 'r1gUWYKWRcPmhJvgdAxc', 'deltorox', 'Soup and eba', 'soup-and-eba', '2021-11-07 15:09:02', '2021-11-07 15:12:45', 1),
(5, 'eG6LpdiYrTLPLXdtFSrN', 'deltorox', 'Soups', 'soups', '2021-11-13 22:51:44', '2021-11-13 22:51:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_images`
--

CREATE TABLE `category_images` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `category_unique_id` varchar(20) NOT NULL,
  `image` varchar(300) NOT NULL,
  `file` varchar(30) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) DEFAULT NULL,
  `product_unique_id` varchar(20) DEFAULT NULL,
  `category_unique_id` varchar(20) DEFAULT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage` double NOT NULL,
  `total_count` int(11) NOT NULL,
  `current_count` int(11) NOT NULL,
  `completion` varchar(20) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `unique_id`, `vendor_unique_id`, `user_unique_id`, `product_unique_id`, `category_unique_id`, `code`, `name`, `percentage`, `total_count`, `current_count`, `completion`, `expiry_date`, `added_date`, `last_modified`, `status`) VALUES
(2, 'DHqUZLXTV489jtVxUGDV', 'xHBBFaUQOshu2u1GHBhk', 'oiH9fzKVpI8jLubuBqUK', NULL, NULL, 'NOVSALES', 'November sales ongoing', 10, 5, 5, 'Processing', '2021-11-30 23:59:59', '2021-11-15 22:35:27', '2021-11-15 22:35:27', 1),
(3, 'K0NTbztjbX9jTw49Hbq0', 'xHBBFaUQOshu2u1GHBhk', NULL, 'qGYhNZXHwgj4SE4iwShQ', NULL, 'BOLE10', 'Bole special sales ', 5, 20, 20, 'Processing', '2021-11-25 23:59:59', '2021-11-15 22:44:00', '2021-11-15 22:44:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_history`
--

CREATE TABLE `coupon_history` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) DEFAULT NULL,
  `product_unique_id` varchar(20) DEFAULT NULL,
  `category_unique_id` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `price` double NOT NULL,
  `completion` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `order_unique_id` varchar(20) NOT NULL,
  `message` varchar(500) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `disputes`
--

INSERT INTO `disputes` (`id`, `unique_id`, `user_unique_id`, `order_unique_id`, `message`, `added_date`, `last_modified`, `status`) VALUES
(1, '1RM96u0NyIOQU802oXPJ', 'oiH9fzKVpI8jLubuBqUK', 'eyRBsuyOFrzL1wRyDUfb', 'Order is Unpaid', '2021-11-09 04:50:18', '2021-11-09 04:50:18', 1),
(2, '7PZjafmgtxWAYqVgShTH', 'oiH9fzKVpI8jLubuBqUK', 'U9ZwczGzkhwRBEi8VBWf', 'Order is Unpaid', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1),
(3, 'yc3d79enc1rADvJCsG1B', 'oiH9fzKVpI8jLubuBqUK', 'gadW2BOQDowzdSrDfXKe', 'Order is Unpaid', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1);

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `unique_id`, `user_unique_id`, `product_unique_id`, `added_date`, `last_modified`, `status`) VALUES
(1, 'GKAMH7iDN5DXGrO6Ni2j', 'bztRqJ7WTLOC32XmOwws', 'tRC63h8VciHiUNxefnJg', '2021-11-13 23:27:51', '2021-11-13 23:27:51', 1),
(2, 'lx9h4ILsEZrrcIly9O0V', 'rzl5nk7rIHDpqMUbHuz9', 'tRC63h8VciHiUNxefnJg', '2021-11-13 23:28:20', '2021-11-13 23:28:20', 1),
(3, 'CHC3UbkeKGEQR9ienexd', 'oiH9fzKVpI8jLubuBqUK', 'tRC63h8VciHiUNxefnJg', '2021-11-13 23:28:27', '2021-11-13 23:28:27', 1),
(4, 'WARkpq88LxYOENFkaVqc', 'oiH9fzKVpI8jLubuBqUK', '5CY9Krp5aDyMGai7eNnA', '2021-11-13 23:28:50', '2021-11-13 23:28:50', 1),
(5, '3qEdOUNF1fUdXXiAm6P7', 'oiH9fzKVpI8jLubuBqUK', 'qGYhNZXHwgj4SE4iwShQ', '2021-11-13 23:28:57', '2021-11-13 23:28:57', 1),
(6, 'BciPSErInmEbjiOfR4Qh', 'oiH9fzKVpI8jLubuBqUK', 'jZ5d1PgCf0cU4YgdDp4r', '2021-11-13 23:29:03', '2021-11-13 23:29:03', 1),
(7, 'YrAVjAaakouCPPGexe8C', 'oiH9fzKVpI8jLubuBqUK', 'AVcsIVjiqUAM0pnSOzhg', '2021-11-13 23:29:09', '2021-11-13 23:29:09', 1),
(8, 'pxZTnAEFeqVtgTtrQjHb', 'rzl5nk7rIHDpqMUbHuz9', 'AVcsIVjiqUAM0pnSOzhg', '2021-11-13 23:29:16', '2021-11-13 23:29:16', 1),
(11, 'mARN2NH6DdH9LVsWrPBn', 'rzl5nk7rIHDpqMUbHuz9', '5CY9Krp5aDyMGai7eNnA', '2021-11-13 23:29:39', '2021-11-13 23:29:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `user_unique_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `vendor_unique_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `stripped` varchar(100) CHARACTER SET latin1 NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `name`, `stripped`, `start_time`, `end_time`, `added_date`, `last_modified`, `status`) VALUES
(1, 'CnDc20MFldtIhe5ArP6J', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'Breakfast', 'breakfast', '06:00:00', '09:30:00', '2021-11-07 15:20:46', '2021-11-07 15:23:56', 1),
(2, '9VzxSKKdadP5XM2vm3oP', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'Lunch', 'lunch', '12:00:00', '15:40:00', '2021-11-07 15:24:47', '2021-11-07 15:24:47', 1),
(4, 'H5VTtCPQC7BHw9lptubC', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'Lunch', 'lunch', '12:00:00', '15:40:00', '2021-11-09 05:45:55', '2021-11-09 05:45:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `action` varchar(200) NOT NULL,
  `added_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `tracker_unique_id` varchar(20) NOT NULL,
  `coupon_unique_id` varchar(20) DEFAULT NULL,
  `shipping_fee_unique_id` varchar(20) DEFAULT NULL,
  `pickup_location` tinyint(1) NOT NULL,
  `rider_details` varchar(300) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `amount` double NOT NULL,
  `shipping_fee` double NOT NULL,
  `credit` double NOT NULL,
  `service_charge` double NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `checked_out` int(2) NOT NULL,
  `paid` int(2) NOT NULL,
  `shipped` int(2) NOT NULL,
  `disputed` int(2) NOT NULL,
  `delivery_status` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `product_unique_id`, `tracker_unique_id`, `coupon_unique_id`, `shipping_fee_unique_id`, `pickup_location`, `rider_details`, `quantity`, `amount`, `shipping_fee`, `credit`, `service_charge`, `payment_method`, `checked_out`, `paid`, `shipped`, `disputed`, `delivery_status`, `added_date`, `last_modified`, `status`) VALUES
(1, 'XnDEkUzkuaIlkRsqLUUG', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 'AFQrAtIjQveDS0qRVFbV', NULL, 'Zr0ijzQjOolWjsUBCHA2', 0, NULL, 5, 3750, 500, 3587.5, 162.5, 'Card', 1, 1, 1, 0, 'Completed', '2021-11-09 03:08:30', '2021-11-09 04:55:28', 1),
(2, 'zGItUQqHkGTj9CIVLU57', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'AFQrAtIjQveDS0qRVFbV', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 2, 3000, 400, 2870, 130, 'Card', 1, 1, 1, 0, 'Completed', '2021-11-09 03:08:30', '2021-11-09 04:55:12', 1),
(3, 'Ccrcp5nhgZc9MatdyKXL', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'TAmpcn23fBnwruNnPhDJ', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, 'Henry Davis, 08029328423', 5, 7500, 1000, 7175, 325, 'Cash', 1, 1, 1, 0, 'Completed', '2021-11-09 03:54:33', '2021-11-09 05:37:40', 1),
(4, 'UXhcZsB1kdM1YVoVPMYm', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 'TAmpcn23fBnwruNnPhDJ', NULL, 'Zr0ijzQjOolWjsUBCHA2', 0, 'James Hardy, 08029328423', 2, 1500, 200, 1435, 65, 'Cash', 1, 1, 1, 0, 'Completed', '2021-11-09 03:54:33', '2021-11-09 05:37:25', 1),
(5, 'eyRBsuyOFrzL1wRyDUfb', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 'TAmpcn23fBnwruNnPhDJ', NULL, 'ArcpsF6zTWWPgjKJO7Uy', 0, NULL, 5, 2750, 250, 2625, 125, 'Cash', 1, 0, 0, 1, 'Unpaid', '2021-11-09 03:54:33', '2021-11-09 04:50:18', 1),
(7, 'U9ZwczGzkhwRBEi8VBWf', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', '0QLJHpdn8S7sRYbH0Ub2', NULL, 'ArcpsF6zTWWPgjKJO7Uy', 0, NULL, 10, 5500, 500, 5250, 250, 'POS', 1, 0, 0, 1, 'Unpaid', '2021-11-09 05:17:33', '2021-11-09 05:23:47', 1),
(8, 'gadW2BOQDowzdSrDfXKe', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', '0QLJHpdn8S7sRYbH0Ub2', NULL, 'Zr0ijzQjOolWjsUBCHA2', 0, NULL, 6, 4500, 600, 4305, 195, 'POS', 1, 0, 0, 1, 'Unpaid', '2021-11-09 05:17:33', '2021-11-09 05:23:47', 1),
(9, 'oUuddC6FAlT8Jue9rrWm', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 'BcdQrvD4tLAqSZpLZWfU', NULL, 'ArcpsF6zTWWPgjKJO7Uy', 0, NULL, 2, 1100, 100, 1050, 50, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-09 05:27:38', '2021-11-10 17:25:35', 1),
(10, 'Tc4YKYxF8bdp23H7WNip', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 'SdQRPlE4bOaU3rFlWPSs', NULL, 'ArcpsF6zTWWPgjKJO7Uy', 0, NULL, 5, 2750, 250, 2625, 125, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-09 05:32:04', '2021-11-10 17:25:55', 1),
(11, 'x0wdBJadgVbbu1dPLQUY', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 'Af0I3ApUfWbwkDE3mcDf', NULL, 'Zr0ijzQjOolWjsUBCHA2', 0, NULL, 5, 3750, 500, 3587.5, 162.5, 'Wallet', 1, 1, 0, 0, 'Paid', '2021-11-09 05:33:08', '2021-11-10 17:26:42', 1),
(12, 'ovM0BhMhvwsgEGGwGwkE', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'Teqh6wk8EY1bCCFQyuhb', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 7, 10500, 1400, 10045, 455, 'Transfer', 1, 1, 0, 0, 'Paid', '2021-11-09 05:34:26', '2021-11-10 17:35:52', 1),
(13, 'DAPXy5aXUDcdVDhfu4Tp', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'buyP7yrVgAY7wXu72ITb', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 5, 7500, 1000, 7175, 325, 'Transfer', 1, 1, 0, 0, 'Paid', '2021-11-09 07:00:02', '2021-11-10 17:39:14', 1),
(14, 'MfKKtIBRAEIStVHwTARF', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'jZ5d1PgCf0cU4YgdDp4r', 'buyP7yrVgAY7wXu72ITb', NULL, '3q2xnybmtdTH44B5CS5p', 0, NULL, 6, 5100, 900, 4890, 210, 'Transfer', 1, 1, 0, 0, 'Paid', '2021-11-09 07:00:02', '2021-11-10 17:42:03', 1),
(15, '4q4Tpw6OT3DF121dfh0m', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'buyP7yrVgAY7wXu72ITb', NULL, 'wcjrnAvv37jxa6Gy0h1x', 0, NULL, 6, 9000, 1200, 8610, 390, 'Transfer', 1, 1, 0, 0, 'Paid', '2021-11-09 07:00:02', '2021-11-10 17:43:29', 1),
(16, 'PyV3T7477zjK8I4VOhLD', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'IV4Ka6Ac6HAofJfv0LJ9', NULL, 'wcjrnAvv37jxa6Gy0h1x', 0, NULL, 10, 15000, 2000, 14350, 650, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-10 17:48:41', '2021-11-11 04:19:26', 1),
(17, 'Sjz0hautcUEqaEf8AeQj', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'IV4Ka6Ac6HAofJfv0LJ9', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 10, 15000, 2000, 14350, 650, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-10 17:48:41', '2021-11-11 04:19:26', 1),
(18, 'HJpDJCC4CMTWrFyu3cCA', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'wqtRfTp8UEl2ROHbX6XQ', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 10, 15000, 2000, 14350, 650, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:13:47', '2021-11-11 13:18:04', 1),
(19, 'gKBiIH02qrU7pPTSG9QJ', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'wqtRfTp8UEl2ROHbX6XQ', NULL, 'wcjrnAvv37jxa6Gy0h1x', 0, NULL, 5, 7500, 1000, 7175, 325, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:13:47', '2021-11-11 13:18:04', 1),
(20, '6k3h63TJgRbAro5jvAnt', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'WcUyZqO8mFtthu0HRRYU', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 5, 7500, 1000, 7175, 325, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:20:55', '2021-11-11 13:21:21', 1),
(21, 'U6io19pVOpRRsjEW8xKx', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'WcUyZqO8mFtthu0HRRYU', NULL, 'wcjrnAvv37jxa6Gy0h1x', 0, NULL, 5, 7500, 1000, 7175, 325, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:20:55', '2021-11-11 13:21:21', 1),
(22, 'rCbfHf3T8BsfRaoRdQS7', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'VAhZe9x3O5lIkTOKYxOh', NULL, 'RPdDkSzuXQv0a1rFbpTt', 0, NULL, 5, 7500, 1000, 7175, 325, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:26:51', '2021-11-11 13:27:14', 1),
(23, 'T5oHzKxStPYO9L1tNrJ0', 'oiH9fzKVpI8jLubuBqUK', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'VAhZe9x3O5lIkTOKYxOh', NULL, 'wcjrnAvv37jxa6Gy0h1x', 0, NULL, 5, 7500, 1000, 7175, 325, 'Card', 1, 1, 0, 0, 'Paid', '2021-11-11 13:26:51', '2021-11-11 13:27:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_completed`
--

CREATE TABLE `orders_completed` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `order_unique_id` varchar(20) NOT NULL,
  `tracker_unique_id` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `coupon_name` varchar(50) DEFAULT NULL,
  `coupon_code` varchar(30) DEFAULT NULL,
  `coupon_percentage` varchar(11) DEFAULT NULL,
  `coupon_price` double DEFAULT NULL,
  `user_address_fullname` varchar(100) NOT NULL,
  `user_full_address` varchar(350) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `shipping_fee_price` double NOT NULL,
  `total_price` double NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders_completed`
--

INSERT INTO `orders_completed` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `order_unique_id`, `tracker_unique_id`, `quantity`, `payment_method`, `product_name`, `coupon_name`, `coupon_code`, `coupon_percentage`, `coupon_price`, `user_address_fullname`, `user_full_address`, `city`, `state`, `country`, `shipping_fee_price`, `total_price`, `added_date`, `last_modified`, `status`) VALUES
(1, 'feq5jJwjym6ZCphkM4I2', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'XnDEkUzkuaIlkRsqLUUG', 'AFQrAtIjQveDS0qRVFbV', 5, 'Card', 'Okpa special', NULL, NULL, NULL, NULL, 'Emmanuel Nwoye', 'No 4 Okija Street, Diobu, Port Harcourt, Rivers State ', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 500, 3750, '2021-11-09 04:12:31', '2021-11-09 04:12:31', 1),
(2, 'SrgxmJCkbJ9mUqy0RLud', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'zGItUQqHkGTj9CIVLU57', 'AFQrAtIjQveDS0qRVFbV', 2, 'Card', 'Ekpankukwo special', NULL, NULL, NULL, NULL, 'Emmanuel Nwoye', 'No 4 Okija Street, Diobu, Port Harcourt, Rivers State ', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 400, 3000, '2021-11-09 04:12:49', '2021-11-09 04:12:49', 1),
(3, 'PuVpUC8dGRzoyPotys30', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'UXhcZsB1kdM1YVoVPMYm', 'TAmpcn23fBnwruNnPhDJ', 2, 'Cash', 'Okpa special', NULL, NULL, NULL, NULL, 'Emmanuel Nwoye', 'No 4 Okija Street, Diobu, Port Harcourt, Rivers State ', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 200, 1500, '2021-11-09 05:37:25', '2021-11-09 05:37:25', 1),
(4, 'o2wyXzNVxKwsyNvn2o2P', 'oiH9fzKVpI8jLubuBqUK', 'xHBBFaUQOshu2u1GHBhk', 'Ccrcp5nhgZc9MatdyKXL', 'TAmpcn23fBnwruNnPhDJ', 5, 'Cash', 'Ekpankukwo special', NULL, NULL, NULL, NULL, 'Emmanuel Nwoye', 'No 4 Okija Street, Diobu, Port Harcourt, Rivers State ', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 1000, 7500, '2021-11-09 05:37:40', '2021-11-09 05:37:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_coupons`
--

CREATE TABLE `order_coupons` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `tracker_unique_id` varchar(20) NOT NULL,
  `coupon_unique_id` varchar(20) NOT NULL,
  `completion` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `order_unique_id` varchar(20) NOT NULL,
  `price` double DEFAULT NULL,
  `completion` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`id`, `unique_id`, `user_unique_id`, `order_unique_id`, `price`, `completion`, `added_date`, `last_modified`, `status`) VALUES
(1, 'FoyOvk0Gn6ExzmHHMsOc', 'oiH9fzKVpI8jLubuBqUK', 'XnDEkUzkuaIlkRsqLUUG', NULL, 'Checked Out', '2021-11-09 03:08:30', '2021-11-09 03:08:30', 1),
(2, 'otN03IgF5bfdvV5oPvbC', 'oiH9fzKVpI8jLubuBqUK', 'zGItUQqHkGTj9CIVLU57', NULL, 'Checked Out', '2021-11-09 03:08:30', '2021-11-09 03:08:30', 1),
(4, 'GumX283jt0JvAaQz25ci', 'oiH9fzKVpI8jLubuBqUK', 'XnDEkUzkuaIlkRsqLUUG', 3750, 'Paid', '2021-11-09 03:50:10', '2021-11-09 03:50:10', 1),
(5, '3SnfFT84LEW0JGxl0kRq', 'oiH9fzKVpI8jLubuBqUK', 'zGItUQqHkGTj9CIVLU57', 3000, 'Paid', '2021-11-09 03:50:10', '2021-11-09 03:50:10', 1),
(6, 'Sq1C1yU638zwnt3omdKU', 'oiH9fzKVpI8jLubuBqUK', 'Ccrcp5nhgZc9MatdyKXL', NULL, 'Checked Out', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(7, 'o95RR2bx3RfS1dOT8BR1', 'oiH9fzKVpI8jLubuBqUK', 'UXhcZsB1kdM1YVoVPMYm', NULL, 'Checked Out', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(8, 'Ehc87OwQPYMM9XHAWLSK', 'oiH9fzKVpI8jLubuBqUK', 'eyRBsuyOFrzL1wRyDUfb', NULL, 'Checked Out', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(9, 'HhL4O8lqkMJLg3jcsyZQ', 'oiH9fzKVpI8jLubuBqUK', 'XnDEkUzkuaIlkRsqLUUG', NULL, 'Shipped', '2021-11-09 03:58:49', '2021-11-09 03:58:49', 1),
(10, 'oPKKYRnSX444A3WlONNZ', 'oiH9fzKVpI8jLubuBqUK', 'zGItUQqHkGTj9CIVLU57', NULL, 'Shipped', '2021-11-09 04:02:19', '2021-11-09 04:02:19', 1),
(12, 'J2JDofl9KEGMKyzOuSBw', 'oiH9fzKVpI8jLubuBqUK', 'XnDEkUzkuaIlkRsqLUUG', NULL, 'Completed', '2021-11-09 04:12:31', '2021-11-09 04:12:31', 1),
(13, 'GcltazYd9mi56GrCiAu3', 'oiH9fzKVpI8jLubuBqUK', 'zGItUQqHkGTj9CIVLU57', NULL, 'Completed', '2021-11-09 04:12:49', '2021-11-09 04:12:49', 1),
(17, '5cg9gRbjThzvgt6gOLA6', 'oiH9fzKVpI8jLubuBqUK', 'Ccrcp5nhgZc9MatdyKXL', 7500, 'Paid', '2021-11-09 04:34:04', '2021-11-09 04:34:04', 1),
(18, 'k44ypPJBMq3Z2nj9TMj6', 'oiH9fzKVpI8jLubuBqUK', 'UXhcZsB1kdM1YVoVPMYm', 1500, 'Paid', '2021-11-09 04:37:16', '2021-11-09 04:37:16', 1),
(19, 'PEfHw5x2ece1z01kEcNi', 'oiH9fzKVpI8jLubuBqUK', 'eyRBsuyOFrzL1wRyDUfb', NULL, 'Unpaid', '2021-11-09 04:50:18', '2021-11-09 04:50:18', 1),
(20, 'FkJblPJNY2FKFNdVH3I3', 'oiH9fzKVpI8jLubuBqUK', 'Ccrcp5nhgZc9MatdyKXL', NULL, 'Shipped', '2021-11-09 04:52:35', '2021-11-09 04:52:35', 1),
(21, 'EDsiI0WuoENg9S48wuki', 'oiH9fzKVpI8jLubuBqUK', 'UXhcZsB1kdM1YVoVPMYm', NULL, 'Shipped', '2021-11-09 04:52:48', '2021-11-09 04:52:48', 1),
(23, 'aumjWKDIE0lNv8cpQ8i7', 'oiH9fzKVpI8jLubuBqUK', 'U9ZwczGzkhwRBEi8VBWf', NULL, 'Checked Out', '2021-11-09 05:17:33', '2021-11-09 05:17:33', 1),
(24, 'wXRoOabqgN3LgrtmBaAQ', 'oiH9fzKVpI8jLubuBqUK', 'gadW2BOQDowzdSrDfXKe', NULL, 'Checked Out', '2021-11-09 05:17:33', '2021-11-09 05:17:33', 1),
(27, '0uz4wqNeMX3MFomLDiIB', 'oiH9fzKVpI8jLubuBqUK', 'U9ZwczGzkhwRBEi8VBWf', NULL, 'Unpaid', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1),
(28, 'IkBMvlN7i47GSFgxfMEa', 'oiH9fzKVpI8jLubuBqUK', 'gadW2BOQDowzdSrDfXKe', NULL, 'Unpaid', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1),
(29, 'trVMSBHjPY88Ud9dox7b', 'oiH9fzKVpI8jLubuBqUK', 'oUuddC6FAlT8Jue9rrWm', NULL, 'Checked Out', '2021-11-09 05:27:38', '2021-11-09 05:27:38', 1),
(30, 'sL0Gj4bYfWdNWT3lBE1w', 'oiH9fzKVpI8jLubuBqUK', 'Tc4YKYxF8bdp23H7WNip', NULL, 'Checked Out', '2021-11-09 05:32:04', '2021-11-09 05:32:04', 1),
(31, '8uizXAiEkI2Wr9PUmax1', 'oiH9fzKVpI8jLubuBqUK', 'x0wdBJadgVbbu1dPLQUY', NULL, 'Checked Out', '2021-11-09 05:33:08', '2021-11-09 05:33:08', 1),
(32, 'bJyEhGolfWCW22fpVZe4', 'oiH9fzKVpI8jLubuBqUK', 'ovM0BhMhvwsgEGGwGwkE', NULL, 'Checked Out', '2021-11-09 05:34:26', '2021-11-09 05:34:26', 1),
(33, 'bIo63jVfYRfxAqofUS1X', 'oiH9fzKVpI8jLubuBqUK', 'UXhcZsB1kdM1YVoVPMYm', NULL, 'Completed', '2021-11-09 05:37:25', '2021-11-09 05:37:25', 1),
(34, 'j1adtdq4Iz8ylGd6pQrD', 'oiH9fzKVpI8jLubuBqUK', 'Ccrcp5nhgZc9MatdyKXL', NULL, 'Completed', '2021-11-09 05:37:40', '2021-11-09 05:37:40', 1),
(35, 'Kh0faMLswlSgE6TcT2ki', 'oiH9fzKVpI8jLubuBqUK', 'DAPXy5aXUDcdVDhfu4Tp', NULL, 'Checked Out', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(36, 'Ug5ovMq4ghI4VyJtp3FC', 'oiH9fzKVpI8jLubuBqUK', 'MfKKtIBRAEIStVHwTARF', NULL, 'Checked Out', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(37, 'NJl2QpT6n6dsMSPTs8Qk', 'oiH9fzKVpI8jLubuBqUK', '4q4Tpw6OT3DF121dfh0m', NULL, 'Checked Out', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(38, 'Toq45pXf991kpuO5vR2t', 'oiH9fzKVpI8jLubuBqUK', 'oUuddC6FAlT8Jue9rrWm', 1100, 'Paid', '2021-11-10 17:25:35', '2021-11-10 17:25:35', 1),
(39, 'vzkWrgK9T28TUrxQYYZt', 'oiH9fzKVpI8jLubuBqUK', 'Tc4YKYxF8bdp23H7WNip', 2750, 'Paid', '2021-11-10 17:25:55', '2021-11-10 17:25:55', 1),
(40, 'qocOsYIUI4RIxAbeckem', 'oiH9fzKVpI8jLubuBqUK', 'x0wdBJadgVbbu1dPLQUY', 3750, 'Paid', '2021-11-10 17:26:42', '2021-11-10 17:26:42', 1),
(41, '0yu0474GjlmvullRPv9Y', 'oiH9fzKVpI8jLubuBqUK', 'ovM0BhMhvwsgEGGwGwkE', 10500, 'Paid', '2021-11-10 17:35:52', '2021-11-10 17:35:52', 1),
(42, 'Rn4yNM3795ClILl6tzVB', 'oiH9fzKVpI8jLubuBqUK', 'DAPXy5aXUDcdVDhfu4Tp', 7500, 'Paid', '2021-11-10 17:39:14', '2021-11-10 17:39:14', 1),
(43, 'pjpJ2pScdH2v97TQ5Njh', 'oiH9fzKVpI8jLubuBqUK', 'MfKKtIBRAEIStVHwTARF', 5100, 'Paid', '2021-11-10 17:42:03', '2021-11-10 17:42:03', 1),
(44, '5ahZX9rpmNp51XrFqPVL', 'oiH9fzKVpI8jLubuBqUK', '4q4Tpw6OT3DF121dfh0m', 9000, 'Paid', '2021-11-10 17:43:29', '2021-11-10 17:43:29', 1),
(45, 'wqmeDSjOoELjbRxfr6Uf', 'oiH9fzKVpI8jLubuBqUK', 'PyV3T7477zjK8I4VOhLD', NULL, 'Checked Out', '2021-11-10 17:48:41', '2021-11-10 17:48:41', 1),
(46, '3PhTFzF9MvbT78DuAoDQ', 'oiH9fzKVpI8jLubuBqUK', 'Sjz0hautcUEqaEf8AeQj', NULL, 'Checked Out', '2021-11-10 17:48:41', '2021-11-10 17:48:41', 1),
(47, 'OG5twOzBQuPQv9uZk3uF', 'oiH9fzKVpI8jLubuBqUK', 'PyV3T7477zjK8I4VOhLD', 15000, 'Paid', '2021-11-11 04:19:26', '2021-11-11 04:19:26', 1),
(48, 'NPl1Q1SpW5XNEcWIW2B7', 'oiH9fzKVpI8jLubuBqUK', 'Sjz0hautcUEqaEf8AeQj', 15000, 'Paid', '2021-11-11 04:19:26', '2021-11-11 04:19:26', 1),
(49, '8bLLzACYseIQverkkyFS', 'oiH9fzKVpI8jLubuBqUK', 'HJpDJCC4CMTWrFyu3cCA', NULL, 'Checked Out', '2021-11-11 13:13:47', '2021-11-11 13:13:47', 1),
(50, 'MzCBCteJ7p4zu6BORKzV', 'oiH9fzKVpI8jLubuBqUK', 'gKBiIH02qrU7pPTSG9QJ', NULL, 'Checked Out', '2021-11-11 13:13:47', '2021-11-11 13:13:47', 1),
(51, 'qRxIZZwr2UiUwEmv92jF', 'oiH9fzKVpI8jLubuBqUK', 'HJpDJCC4CMTWrFyu3cCA', 15000, 'Paid', '2021-11-11 13:18:04', '2021-11-11 13:18:04', 1),
(52, 'A7FnOzisWLfMI9iFqNfw', 'oiH9fzKVpI8jLubuBqUK', 'gKBiIH02qrU7pPTSG9QJ', 7500, 'Paid', '2021-11-11 13:18:04', '2021-11-11 13:18:04', 1),
(53, 'V9Xz1dbu7g8tx9EOE38n', 'oiH9fzKVpI8jLubuBqUK', '6k3h63TJgRbAro5jvAnt', NULL, 'Checked Out', '2021-11-11 13:20:55', '2021-11-11 13:20:55', 1),
(54, 'oyFkQl4ifoCqFW4rhamB', 'oiH9fzKVpI8jLubuBqUK', 'U6io19pVOpRRsjEW8xKx', NULL, 'Checked Out', '2021-11-11 13:20:55', '2021-11-11 13:20:55', 1),
(55, 'ifQFdmMVLjQwd0K9m5zK', 'oiH9fzKVpI8jLubuBqUK', '6k3h63TJgRbAro5jvAnt', 7500, 'Paid', '2021-11-11 13:21:21', '2021-11-11 13:21:21', 1),
(56, 'RmyBbyrYXIelq1RIfjvt', 'oiH9fzKVpI8jLubuBqUK', 'U6io19pVOpRRsjEW8xKx', 7500, 'Paid', '2021-11-11 13:21:21', '2021-11-11 13:21:21', 1),
(57, 'yjN5dT9PRM5WvFapbiA3', 'oiH9fzKVpI8jLubuBqUK', 'rCbfHf3T8BsfRaoRdQS7', NULL, 'Checked Out', '2021-11-11 13:26:51', '2021-11-11 13:26:51', 1),
(58, 'YuSqhvpSj69V8NbN9ExN', 'oiH9fzKVpI8jLubuBqUK', 'T5oHzKxStPYO9L1tNrJ0', NULL, 'Checked Out', '2021-11-11 13:26:51', '2021-11-11 13:26:51', 1),
(59, 'Yc4KIzMkTWWMdpEzOE49', 'oiH9fzKVpI8jLubuBqUK', 'rCbfHf3T8BsfRaoRdQS7', 7500, 'Paid', '2021-11-11 13:27:14', '2021-11-11 13:27:14', 1),
(60, 'skv1XbrKspniYxduN39V', 'oiH9fzKVpI8jLubuBqUK', 'T5oHzKxStPYO9L1tNrJ0', 7500, 'Paid', '2021-11-11 13:27:14', '2021-11-11 13:27:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `increase_stock` tinyint(1) NOT NULL,
  `last_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `increase_stock`, `last_modified`) VALUES
(1, 'pwtDboakpBnU3CgKmsyq', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 1, '2021-11-09 14:31:06'),
(2, 'j13MOx2MzOnp9VU6nE8T', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 1, '2021-11-09 06:24:17'),
(3, '6QewpTokLPL0JV9pxVXD', 'ONfNeuZCOCuKcu7uMbY6', 'M6iqLP2EEaAJNoT7H43w', 0, '2021-11-09 06:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `menu_unique_id` varchar(20) DEFAULT NULL,
  `category_unique_id` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `stripped` varchar(250) NOT NULL,
  `description` varchar(3000) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `stock_remaining` int(11) NOT NULL,
  `price` double NOT NULL,
  `sales_price` double NOT NULL,
  `views` bigint(20) NOT NULL,
  `favorites` bigint(20) NOT NULL,
  `good_rating` bigint(20) NOT NULL,
  `bad_rating` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `menu_unique_id`, `category_unique_id`, `name`, `stripped`, `description`, `duration`, `weight`, `stock`, `stock_remaining`, `price`, `sales_price`, `views`, `favorites`, `good_rating`, `bad_rating`, `added_date`, `last_modified`, `status`) VALUES
(1, 'tRC63h8VciHiUNxefnJg', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', '9VzxSKKdadP5XM2vm3oP', '7doAq0l8LTSncbtYF5ul', 'Okpa special', 'okpa-special', 'This is the igbo native beans food - Okpa with grilled fish and stew', NULL, NULL, 20, 20, 700, 0, 8, 4, 1, 1, '2021-11-07 15:34:09', '2021-11-24 14:01:44', 1),
(2, '5CY9Krp5aDyMGai7eNnA', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', '9VzxSKKdadP5XM2vm3oP', '7doAq0l8LTSncbtYF5ul', 'Ekpankukwo special', 'ekpankukwo-special', 'This is the calabar native food - Apankukwo with grilled fish and stew', NULL, NULL, 20, 20, 1500, 1300, 4, 3, 2, 0, '2021-11-07 15:47:13', '2021-11-14 10:12:26', 1),
(3, 'qGYhNZXHwgj4SE4iwShQ', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', NULL, '7doAq0l8LTSncbtYF5ul', 'Bole special', 'bole-special', 'This is the port harcourt native food - Bole with grilled fish and stew', NULL, NULL, 20, 20, 500, 0, 1, 2, 0, 0, '2021-11-07 15:48:04', '2021-11-13 23:33:21', 1),
(6, 'jZ5d1PgCf0cU4YgdDp4r', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'H5VTtCPQC7BHw9lptubC', '7doAq0l8LTSncbtYF5ul', 'Bole special', 'bole-special', 'This is the port harcourt native food - Bole with grilled fish and stew', NULL, NULL, 15, 15, 700, 0, 7, 2, 0, 0, '2021-11-09 05:58:22', '2021-11-24 14:01:00', 1),
(7, 'AVcsIVjiqUAM0pnSOzhg', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'H5VTtCPQC7BHw9lptubC', '7doAq0l8LTSncbtYF5ul', 'Bole double special', 'bole-double-special', 'This is the port harcourt native food - Bole with grilled fish and stew', NULL, NULL, 10, 10, 1300, 0, 2, 3, 0, 0, '2021-11-09 06:08:32', '2021-11-24 14:00:05', 1),
(8, 'Bbm36u2XaIbZUvlMOCom', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', NULL, 'wGjN8YQRRrgMf67sQ3yS', 'Chin chin', 'chin-chin', 'Fried flour ', NULL, '1kg', 50, 50, 1000, 0, 6, 1, 0, 0, '2021-11-23 00:48:19', '2021-11-24 14:00:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `image` varchar(300) NOT NULL,
  `file` varchar(30) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `rating` varchar(5) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `unique_id`, `user_unique_id`, `product_unique_id`, `rating`, `added_date`, `last_modified`, `status`) VALUES
(1, 'xsXGQl52Xuvgo2RTvt9T', 'oiH9fzKVpI8jLubuBqUK', 'tRC63h8VciHiUNxefnJg', 'Yes', '2021-11-14 09:39:22', '2021-11-14 09:41:09', 1),
(2, 'RSh6KaDu5iTBSLKn4k11', 'oiH9fzKVpI8jLubuBqUK', '5CY9Krp5aDyMGai7eNnA', 'Yes', '2021-11-14 09:41:14', '2021-11-14 09:41:14', 1),
(3, 'jqlNX5GBP3Bzu5AArmJV', 'rzl5nk7rIHDpqMUbHuz9', '5CY9Krp5aDyMGai7eNnA', 'Yes', '2021-11-14 09:41:19', '2021-11-14 09:41:19', 1),
(4, 'iMklJdb7EvUWvzYZUzDy', 'rzl5nk7rIHDpqMUbHuz9', 'tRC63h8VciHiUNxefnJg', 'No', '2021-11-14 09:41:25', '2021-11-14 09:41:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `referral_user_unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `user_referral_link` varchar(200) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `unique_id`, `referral_user_unique_id`, `user_unique_id`, `user_referral_link`, `added_date`, `last_modified`, `status`) VALUES
(1, 'JOu0mi1630bKPmS9VYQG', 'oiH9fzKVpI8jLubuBqUK', 'tPBIE40TyKjOu0A35Joe', 'https://auth.reestoc.com/signup/tPBIE40TyKjOu0A35Joe', '2021-08-07 03:30:35', '2021-08-07 03:30:35', 1),
(2, '1znnkGqezmnwOGpPveRZ', 'oiH9fzKVpI8jLubuBqUK', 'bztRqJ7WTLOC32XmOwws', 'https://auth.reestoc.com/signup/bztRqJ7WTLOC32XmOwws', '2021-09-18 01:35:32', '2021-09-18 01:35:32', 1),
(3, 'wriuwNOeKukKIUN73IR0', 'bztRqJ7WTLOC32XmOwws', 'rzl5nk7rIHDpqMUbHuz9', 'https://auth.reestoc.com/signup/rzl5nk7rIHDpqMUbHuz9', '2021-09-18 01:39:48', '2021-09-18 01:39:48', 1),
(4, 'ltB6ZVD6Q7Roiar5dko7', 'Default', 'oiH9fzKVpI8jLubuBqUK', 'https://auth.reestoc.com/signup/oiH9fzKVpI8jLubuBqUK', '2021-09-18 03:08:44', '2021-09-18 03:08:44', 1),
(5, 's9FeLiS2GIJJptO1Bwy1', 'oiH9fzKVpI8jLubuBqUK', 'FIx1NsUOzWnIeLp970CQ', 'https://auth.reestoc.com/signup/FIx1NsUOzWnIeLp970CQ', '2021-10-06 16:53:55', '2021-10-06 16:53:55', 1),
(6, 'yWUPSwcjBffcUXgejJ8a', 'Default', 'ejm525FluTiIUQkSTNqK', 'https://auth.reestoc.com/signup/ejm525FluTiIUQkSTNqK', '2021-10-06 16:56:51', '2021-10-06 16:56:51', 1),
(7, 'i5FBcYpbgfZFEYIWeSgP', 'Default', 'UGjnvlj9mzxoc6qn5pwq', 'https://reestoc.com/sign-up/UGjnvlj9mzxoc6qn5pwq', '2021-10-25 22:39:46', '2021-10-25 22:39:46', 1),
(10, '7wBfhp1ZKkOdN9DI57qb', 'oiH9fzKVpI8jLubuBqUK', 'Q7hMIwEHCtAF4wL05coF', 'https://auth.reestoc.com/signup/Q7hMIwEHCtAF4wL05coF', '2021-11-18 12:22:02', '2021-11-18 12:22:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `review_ratings`
--

CREATE TABLE `review_ratings` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `yes_rating` tinyint(1) NOT NULL,
  `no_rating` tinyint(1) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `search_history`
--

CREATE TABLE `search_history` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) DEFAULT NULL,
  `search` varchar(300) NOT NULL,
  `type` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `search_history`
--

INSERT INTO `search_history` (`id`, `unique_id`, `user_unique_id`, `search`, `type`, `added_date`, `last_modified`, `status`) VALUES
(1, 'hM3LkAlwXzLEAHPfsSVz', 'oiH9fzKVpI8jLubuBqUK', 'okpa', '', '2021-07-10 11:37:39', '2021-07-10 11:37:39', 1),
(2, 'TFTqUzQXwiCEZcDrpvVo', 'oiH9fzKVpI8jLubuBqUK', 'Ekpankukwo', '', '2021-07-10 11:38:41', '2021-07-10 11:38:41', 1),
(3, 'ac1hogFpdNlquk2mN9AW', 'Anonymous', 'Teremana', '', '2021-07-10 13:02:36', '2021-07-10 13:02:36', 1),
(4, 'bgxmLhYSZKLqkvSrBXtY', 'rzl5nk7rIHDpqMUbHuz9', 'Ekpankukwo', '', '2021-07-10 13:02:53', '2021-07-10 13:02:53', 1),
(5, '2UFpCKgjtr7HlZmGU9C3', 'rzl5nk7rIHDpqMUbHuz9', 'bole', '', '2021-07-10 13:03:06', '2021-07-10 13:03:06', 1),
(6, 'LlaKxMriJUX8SvHaatsr', 'bztRqJ7WTLOC32XmOwws', '50cl', '', '2021-07-10 13:03:23', '2021-07-10 13:03:23', 1),
(9, 'VKZleIv9tT63uEY2x3Qj', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:20:22', '2021-11-24 14:20:22', 1),
(10, '2X9sHWAa0iHarGf9caMY', 'oiH9fzKVpI8jLubuBqUk', 'ekpa', 'Available', '2021-11-24 14:20:36', '2021-11-24 14:20:36', 1),
(11, '9ZKAeHYNtPrSekXvasOc', 'oiH9fzKVpI8jLubuBqUk', 'okpa', 'Available', '2021-11-24 14:20:48', '2021-11-24 14:20:48', 1),
(12, 'mkteSK6EBqhtvqPTJdnD', 'oiH9fzKVpI8jLubuBqUk', 'native foods', 'Available', '2021-11-24 14:21:04', '2021-11-24 14:21:04', 1),
(13, 'VKSd4FkCgkYkJWe8LU71', 'oiH9fzKVpI8jLubuBqUk', 'snacks', 'Available', '2021-11-24 14:21:19', '2021-11-24 14:21:19', 1),
(14, 'y0xlaavAWuXCgKpkpWHe', 'oiH9fzKVpI8jLubuBqUk', 'soup', 'Unavailable', '2021-11-24 14:21:29', '2021-11-24 14:21:29', 1),
(15, 'aBdUwrEH65fuuAPkwO2b', 'oiH9fzKVpI8jLubuBqUk', 'gold', 'Unavailable', '2021-11-24 14:21:38', '2021-11-24 14:21:38', 1),
(16, 'aYh9uOgJOUkR4KadB5Dq', 'Anonymous', 'gold', 'Unavailable', '2021-11-24 14:21:43', '2021-11-24 14:21:43', 1),
(17, 'Ibz6xIPvyKYS64LeoBnR', 'Anonymous', 'chin chin', 'Available', '2021-11-24 14:21:50', '2021-11-24 14:21:50', 1),
(18, 'NiYV4kczxHOr01MJ76zB', 'Anonymous', 'gold', 'Unavailable', '2021-11-24 14:22:59', '2021-11-24 14:22:59', 1),
(19, 'K0giNHNrv6sG0vGHmXI8', 'oiH9fzKVpI8jLubuBqUk', 'gold', 'Unavailable', '2021-11-24 14:23:09', '2021-11-24 14:23:09', 1),
(20, '6mrRrrNqN4P2zYWyy1qr', 'oiH9fzKVpI8jLubuBqUk', 'soups', 'Unavailable', '2021-11-24 14:23:25', '2021-11-24 14:23:25', 1),
(21, 'GOtCzb4R9CfqCq0XS504', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:25:11', '2021-11-24 14:25:11', 1),
(22, 'XhZZZystgpDNJdt8muIg', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:28:51', '2021-11-24 14:28:51', 1),
(23, 'sL4epOZlfRBiyPJxEmEK', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:29:05', '2021-11-24 14:29:05', 1),
(24, 'woZZ1svnFsW72qrJBFTF', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:29:19', '2021-11-24 14:29:19', 1),
(25, 'LkNxIN7YQ2CbIfLQYbpR', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:36:10', '2021-11-24 14:36:10', 1),
(26, '7KokFNY4JUVwC8XvYA0w', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:37:12', '2021-11-24 14:37:12', 1),
(27, 'sEzgZuyd0g7kQy3kBvD3', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:37:27', '2021-11-24 14:37:27', 1),
(28, 'Y2SfUWAJN5RPW3XVtNnw', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:37:58', '2021-11-24 14:37:58', 1),
(29, 'pXo0bDvDaU0m7MZVisKm', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:39:01', '2021-11-24 14:39:01', 1),
(30, 'diIce8SHhQLwiIRp8b5V', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:41:51', '2021-11-24 14:41:51', 1),
(31, 'a9bOn5vW61nBDfwFIZxM', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:54:54', '2021-11-24 14:54:54', 1),
(32, 'SoTkM9ZUDe9x8n7Wr2Z3', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:55:21', '2021-11-24 14:55:21', 1),
(33, 'G7rRf2BlPf9R6EpaLC3t', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:58:58', '2021-11-24 14:58:58', 1),
(34, 'bcwtSbQdqA8ZyBO1VJjk', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 14:59:45', '2021-11-24 14:59:45', 1),
(35, 'lpd6BZznXv4ifoH2vFD9', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:00:10', '2021-11-24 15:00:10', 1),
(36, '1JX5AK2Cr78hbEo9qpEz', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:03:52', '2021-11-24 15:03:52', 1),
(37, 'n0LkOMgbmqKPB0PKhCsA', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:04:15', '2021-11-24 15:04:15', 1),
(38, 'Yzb2Hp0kYjeSZTnnqCU7', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:04:22', '2021-11-24 15:04:22', 1),
(39, 'nw4uDWlWilOeTMQSdvwR', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:04:26', '2021-11-24 15:04:26', 1),
(40, 'FNkRIlTQj6eJiVpRXkc4', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:04:47', '2021-11-24 15:04:47', 1),
(41, 'b1PlEh0jfec5JfOxlc1k', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:04:51', '2021-11-24 15:04:51', 1),
(42, 'HdWKu2hOPvyW4yBJc3gz', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:06:23', '2021-11-24 15:06:23', 1),
(43, 'DP8PK3x1GzywRuAGmPap', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:06:41', '2021-11-24 15:06:41', 1),
(44, 'yfvmyikSCUeEH7Av7hGV', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:06:42', '2021-11-24 15:06:42', 1),
(45, 'avpoAw7k7lKRR93ve5p1', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:07:20', '2021-11-24 15:07:20', 1),
(46, 'tSzdrcovHKkgR14WoaDr', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:07:21', '2021-11-24 15:07:21', 1),
(47, '1Uqi25cDtbRxOzO6MmcD', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:07:32', '2021-11-24 15:07:32', 1),
(48, 'v2vTc9ldcbuDn1DkHbHU', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:23:01', '2021-11-24 15:23:01', 1),
(49, '7MlN6rxH2mytGjY9qkmA', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:23:50', '2021-11-24 15:23:50', 1),
(50, 'kWJJJI2w8ZaQ0MlzjIJb', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:25:03', '2021-11-24 15:25:03', 1),
(51, 'pEJqxFxIRnbVFrAnEOAT', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:25:18', '2021-11-24 15:25:18', 1),
(52, 'acJU7hvhUuIB8YpBV0I3', 'oiH9fzKVpI8jLubuBqUk', 'bole', 'Available', '2021-11-24 15:25:45', '2021-11-24 15:25:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_fees`
--

CREATE TABLE `shipping_fees` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `price` double NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shipping_fees`
--

INSERT INTO `shipping_fees` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `product_unique_id`, `city`, `state`, `country`, `price`, `added_date`, `last_modified`, `status`) VALUES
(1, 'Zr0ijzQjOolWjsUBCHA2', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'tRC63h8VciHiUNxefnJg', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 100, '2021-11-09 02:53:54', '2021-11-09 02:53:54', 1),
(2, 'RPdDkSzuXQv0a1rFbpTt', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', '5CY9Krp5aDyMGai7eNnA', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 200, '2021-11-09 02:54:53', '2021-11-09 02:59:14', 1),
(3, 'ArcpsF6zTWWPgjKJO7Uy', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'qGYhNZXHwgj4SE4iwShQ', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 50, '2021-11-09 02:55:06', '2021-11-09 03:02:10', 1),
(4, '3q2xnybmtdTH44B5CS5p', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'jZ5d1PgCf0cU4YgdDp4r', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 150, '2021-11-09 06:41:15', '2021-11-09 06:41:15', 1),
(5, 'wcjrnAvv37jxa6Gy0h1x', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'AVcsIVjiqUAM0pnSOzhg', 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 200, '2021-11-09 06:41:35', '2021-11-09 06:41:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `type` varchar(30) NOT NULL,
  `amount` double NOT NULL,
  `transaction_status` varchar(50) NOT NULL,
  `details` varchar(300) DEFAULT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `unique_id`, `vendor_unique_id`, `type`, `amount`, `transaction_status`, `details`, `added_date`, `last_modified`, `status`) VALUES
(1, '4ALs888qVSXeRyO2BBdd', 'xHBBFaUQOshu2u1GHBhk', 'Subscription', 1000, 'Processing', NULL, '2021-11-07 14:40:56', '2021-11-07 14:40:56', 1),
(2, 'V6YAWoJKOOGbq1icpvBW', 'xHBBFaUQOshu2u1GHBhk', 'Subscription', 1000, 'Paid', NULL, '2021-11-07 14:40:56', '2021-11-07 14:40:56', 1),
(3, '3N0dTUABR5tTrOWa4PK4', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 162.5, 'Service charge incurred', 'Service charge : 162.5 Naira Service charge incurred on order(XnDEkUzkuaIlkRsqLUUG) tracking id(AFQrAtIjQveDS0qRVFbV)', '2021-11-09 03:08:30', '2021-11-09 03:08:30', 1),
(4, '4PlP330kAgSYysJV0Aod', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 130, 'Service charge incurred', 'Debt : 130 Naira Debt incurred on order(zGItUQqHkGTj9CIVLU57) tracking id(AFQrAtIjQveDS0qRVFbV)', '2021-11-09 03:08:30', '2021-11-09 03:08:30', 1),
(5, 'p0uI1AdDuwIEyxZDAnWJ', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 325, 'Service charge incurred', 'Debt : 325 Naira Debt incurred on order(Ccrcp5nhgZc9MatdyKXL) tracking id(TAmpcn23fBnwruNnPhDJ)', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(6, 'RtfXrkB6tDf4qIJDKc5b', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 65, 'Service charge incurred', 'Debt : 65 Naira Debt incurred on order(UXhcZsB1kdM1YVoVPMYm) tracking id(TAmpcn23fBnwruNnPhDJ)', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(7, 'FQCJxzfb6OsknPOTuPtr', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 125, 'Service charge incurred', 'Debt : 125 Naira Debt incurred on order(eyRBsuyOFrzL1wRyDUfb) tracking id(TAmpcn23fBnwruNnPhDJ)', '2021-11-09 03:54:33', '2021-11-09 03:54:33', 1),
(8, '7O4irleX6DEdTMr6XkLc', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 125, 'Service charge nullified', 'Debt : 125 Naira Debt nullified on order(eyRBsuyOFrzL1wRyDUfb) tracking id(TAmpcn23fBnwruNnPhDJ)', '2021-11-09 04:50:18', '2021-11-09 04:50:18', 1),
(10, 'SB3GQDMcIb5mnTWmuoYl', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 250, 'Service charge incurred', 'Debt : 250 Naira Debt incurred on order(U9ZwczGzkhwRBEi8VBWf) tracking id(0QLJHpdn8S7sRYbH0Ub2)', '2021-11-09 05:17:33', '2021-11-09 05:17:33', 1),
(11, 'hiBpwcjCcluF47lthI8j', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 195, 'Service charge incurred', 'Debt : 195 Naira Debt incurred on order(gadW2BOQDowzdSrDfXKe) tracking id(0QLJHpdn8S7sRYbH0Ub2)', '2021-11-09 05:17:33', '2021-11-09 05:17:33', 1),
(12, '0cxAJujucIp97bwYT9qu', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 250, 'Service charge nullified', 'Debt : 250 Naira Debt nullified on order(U9ZwczGzkhwRBEi8VBWf) tracking id(0QLJHpdn8S7sRYbH0Ub2)', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1),
(13, 'ps5v7to87gz5HfxqlGtR', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 195, 'Service charge nullified', 'Debt : 195 Naira Debt nullified on order(gadW2BOQDowzdSrDfXKe) tracking id(0QLJHpdn8S7sRYbH0Ub2)', '2021-11-09 05:23:47', '2021-11-09 05:23:47', 1),
(15, 'qdCb3Mz0IQjcREVPuBhW', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 455, 'Service charge incurred', 'Debt : 455 Naira Debt incurred on order(ovM0BhMhvwsgEGGwGwkE) tracking id(Teqh6wk8EY1bCCFQyuhb)', '2021-11-09 05:34:26', '2021-11-09 05:34:26', 1),
(16, '6BMgm2mohcdiupnwxQjH', 'Q0k0T23V2rgLO8GeOYEY', 'Subscription', 1000, 'Processing', NULL, '2021-11-09 05:44:21', '2021-11-09 05:44:21', 1),
(17, 'GT6aqZULiYEwn9JEYbER', 'Q0k0T23V2rgLO8GeOYEY', 'Subscription', 1000, 'Paid', NULL, '2021-11-09 05:44:21', '2021-11-09 05:44:21', 1),
(18, 'jDJm2wGJ8L6TovYoEFOY', 'M6iqLP2EEaAJNoT7H43w', 'Subscription', 1000, 'Processing', NULL, '2021-11-09 06:12:37', '2021-11-09 06:12:37', 1),
(19, 'eHWGsVrsx2HFohKdm5jX', 'M6iqLP2EEaAJNoT7H43w', 'Subscription', 1000, 'Paid', NULL, '2021-11-09 06:12:37', '2021-11-09 06:12:37', 1),
(20, 'TljgLlAL6P7gCQSGuPoE', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 325, 'Service charge incurred', 'Debt : 325 Naira Debt incurred on order(DAPXy5aXUDcdVDhfu4Tp) tracking id(buyP7yrVgAY7wXu72ITb)', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(21, 'TlWt7zo2HJoD3yMmbYxm', 'Q0k0T23V2rgLO8GeOYEY', 'Service charge', 210, 'Service charge incurred', 'Debt : 210 Naira Debt incurred on order(MfKKtIBRAEIStVHwTARF) tracking id(buyP7yrVgAY7wXu72ITb)', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(22, '9mnva5kRTh4FkdQLhD66', 'Q0k0T23V2rgLO8GeOYEY', 'Service charge', 390, 'Service charge incurred', 'Debt : 390 Naira Debt incurred on order(4q4Tpw6OT3DF121dfh0m) tracking id(buyP7yrVgAY7wXu72ITb)', '2021-11-09 07:00:02', '2021-11-09 07:00:02', 1),
(24, 'b3f7uvKWtDL02YA263dF', 'xHBBFaUQOshu2u1GHBhk', 'Withdrawal', 40000, 'Cancelled', 'Withdrawal : 40000 Naira Withdrawal Processing. Bank details : Name - Nwoye Emmanuel Aneku, Acc No - 0245963215, Bank - Guaranty Trust Bank', '2021-11-13 21:13:20', '2021-11-13 21:38:58', 1),
(25, 'AQhb5nUtaqw0mzq3v4FH', 'xHBBFaUQOshu2u1GHBhk', 'Withdrawal', 50000, 'Completed', 'Withdrawal : 50000 Naira Withdrawal Processing. Bank details : Name - Nwoye Emmanuel Aneku, Acc No - 0245963215, Bank - Guaranty Trust Bank', '2021-11-13 21:49:47', '2021-11-13 21:50:13', 1),
(26, 'Bofd6fSSlIcyNBfuQL6E', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 10000, 'Cancelled', 'Service charge : 10000 Naira Service charge Processing. Payment via Transfer. Bank details : Name - Nwoye Emmanuel Aneku, Acc No - 0245986500, Bank - Guaranty Trust Bank', '2021-11-13 22:23:17', '2021-11-13 22:24:06', 1),
(27, 'QzsLxMbJcoCaO4BXBfPy', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 10000, 'Completed', 'Service charge : 10000 Naira Service charge Processing. Payment via Card', '2021-11-13 22:24:31', '2021-11-13 22:34:56', 1),
(28, 'GAL4zd1Xl97FipXxaQSz', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 5000, 'Completed', 'Service charge : 5000 Naira Service charge Processing. Payment via Transfer. Bank details : Name - Nwoye Emmanuel Aneku, Acc No - 0245986500, Bank - Guaranty Trust Bank', '2021-11-13 22:35:57', '2021-11-13 22:36:44', 1),
(29, 'bvLRu4diuMqFaU80H0CI', 'xHBBFaUQOshu2u1GHBhk', 'Service charge', 5000, 'Completed', 'Service charge : 5000 Naira Service charge Processing. Payment via Transfer. Bank details : Name - Nwoye Emmanuel Aneku, Acc No - 0245986500, Bank - Guaranty Trust Bank', '2021-11-13 22:39:27', '2021-11-13 22:40:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `image` varchar(300) DEFAULT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `access` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `fullname`, `email`, `phone_number`, `image`, `added_date`, `last_modified`, `access`, `status`) VALUES
(3, 'rzl5nk7rIHDpqMUbHuz9', 'Linda Henry', 'heliry@gmail.com', '08085245458', 'https://myaccount.xnyder.com/images/user.png', '2021-07-05 23:32:15', '2021-09-18 01:27:09', 1, 1),
(4, 'bztRqJ7WTLOC32XmOwws', 'Princess Latifah', 'princesslatifah@gmail.com', '08096545454', 'https://myaccount.xnyder.com/images/user.png', '2021-07-07 11:05:15', '2021-07-07 11:05:15', 1, 1),
(5, 'tPBIE40TyKjOu0A35Joe', 'Princess Latifah JR', 'princesslatifahjr@gmail.com', '08096556689', 'https://myaccount.xnyder.com/images/user.png', '2021-08-07 03:30:35', '2021-09-18 01:26:33', 1, 1),
(8, 'oiH9fzKVpI8jLubuBqUK', 'Emmanuel Nwoye', 'emmanuelnwoye5@gmail.com', '+2348093223317', 'https://myaccount.xnyder.com/images/user.png', '2021-09-18 03:08:44', '2021-09-18 03:08:44', 1, 1),
(9, 'FIx1NsUOzWnIeLp970CQ', 'Jaden Smith', 'jadensmith@gmail.com', '08125697513', 'https://myaccount.xnyder.com/images/user.png', '2021-10-06 16:53:55', '2021-10-06 16:53:55', 1, 1),
(10, 'ejm525FluTiIUQkSTNqK', 'Willow Smith', 'willowsmith@gmail.com', '07085663314', 'https://myaccount.xnyder.com/images/user.png', '2021-10-06 16:56:51', '2021-10-06 16:56:51', 1, 1),
(14, 'Q7hMIwEHCtAF4wL05coF', 'Lilith Fong', 'fongshway@gmail.com', '08093223317', 'https://myaccount.xnyder.com/images/user.png', '2021-11-18 12:22:02', '2021-11-18 13:38:19', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_addresses`
--

CREATE TABLE `users_addresses` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `address` varchar(200) NOT NULL,
  `additional_information` varchar(150) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `default_status` varchar(10) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_addresses`
--

INSERT INTO `users_addresses` (`id`, `unique_id`, `user_unique_id`, `firstname`, `lastname`, `address`, `additional_information`, `city`, `state`, `country`, `default_status`, `added_date`, `last_modified`, `status`) VALUES
(1, 'tXimAs9kr2QQXiNSgQ0u', 'rzl5nk7rIHDpqMUbHuz9', 'Linda', 'Henry', 'No 4 Okija Street, Diobu, Port Harcourt, RIvers State', 'Apartment 23', 'PORTHARCOURT-D/LINE', 'Rivers', 'Nigeria', 'Yes', '2021-09-04 02:43:11', '2021-10-22 02:35:03', 1),
(2, 'P883s1MAimZ5AwGpcBBY', 'rzl5nk7rIHDpqMUbHuz9', 'Linda', 'Henry', 'No 4 Okija Street, Diobu, Port Harcourt, RIvers State', 'Apartment 23', '1245', '33', 'NG', 'No', '2021-09-04 02:43:17', '2021-10-22 02:35:03', 0),
(3, 'b6ANNe1xzxQiqq95dkti', 'rzl5nk7rIHDpqMUbHuz9', 'Linda', 'Henry', 'No 4 Okija Street, Diobu, Port Harcourt, RIvers State', 'Apartment 23', '1245', '33', 'NG', 'No', '2021-09-04 02:43:23', '2021-10-22 02:35:03', 0),
(4, 'aWQA5D0ZTmIjpZx2YaFX', 'oiH9fzKVpI8jLubuBqUK', 'Emmanuel', 'Nwoye', 'No 4 Okija Street, Diobu, Port Harcourt, Rivers State', NULL, 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 'Yes', '2021-09-18 12:06:16', '2021-10-25 02:01:08', 1),
(5, 'uOv2B163479PUnY17790', 'oiH9fzKVpI8jLubuBqUK', 'Emmanuel', 'Nwoye', 'No 34 Emmanuel Drive, Fimena junction, Abuloma, Port Harcourt, Rivers State', 'Third apartment by your right', 'PORTHARCOURT-ABULOMA', 'Rivers', 'Nigeria', 'No', '2021-10-20 10:16:30', '2021-10-25 02:01:08', 1),
(6, 'PafUjs1NKiAf0WkZnhrV', 'oiH9fzKVpI8jLubuBqUK', 'Elvis', 'Puinaro', 'No 8 Mboushimini, Port Harcourt, Rivers State', NULL, 'PORTHARCOURT-MUGBUOSIMINI', 'Rivers', 'Nigeria', 'No', '2021-10-20 11:57:29', '2021-10-25 02:01:08', 0),
(7, 'LUqlkQTGC9Xy8QdPwZpR', 'rzl5nk7rIHDpqMUbHuz9', 'Linda', 'Henry', '56 agip road, port harcourt, rivers state', NULL, 'PORTHARCOURT-AGIP', 'Rivers', 'Nigeria', 'No', '2021-10-22 02:34:09', '2021-10-22 02:35:03', 1),
(8, '6J1sArWukCWiuNBNGNnp', 'oiH9fzKVpI8jLubuBqUK', 'Emmanuel', 'Aneku', 'No 8 Mboushimini, Port Harcourt, Rivers State.', NULL, 'PORTHARCOURT-MUGBUOSIMINI', 'Rivers', 'Nigeria', 'No', '2021-10-22 03:06:50', '2021-10-25 02:01:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_preferences`
--

CREATE TABLE `users_preferences` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `show_addresses` tinyint(1) NOT NULL,
  `last_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_preferences`
--

INSERT INTO `users_preferences` (`id`, `unique_id`, `user_unique_id`, `show_addresses`, `last_modified`) VALUES
(1, 'pwtDboakpBnU3CgKmsyq', 'oiH9fzKVpI8jLubuBqUK', 1, '2021-11-18 11:48:14'),
(2, 'j13MOx2MzOnp9VU6nE8T', 'rzl5nk7rIHDpqMUbHuz9', 1, '2021-11-09 06:24:17'),
(3, '6QewpTokLPL0JV9pxVXD', 'bztRqJ7WTLOC32XmOwws', 0, '2021-11-09 06:30:13'),
(4, 'CXMGRPDl2kV8sNwuQx1b', 'Q7hMIwEHCtAF4wL05coF', 0, '2021-11-18 12:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `business_name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `stripped` varchar(50) CHARACTER SET latin1 NOT NULL,
  `details` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `fullname` varchar(50) CHARACTER SET latin1 NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `phone_number` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `profile_image` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `cover_image` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `cover_image_file` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `opening_hours` time DEFAULT NULL,
  `closing_hours` time DEFAULT NULL,
  `balance` double NOT NULL,
  `service_charge` double NOT NULL,
  `city` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `address` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `access` tinyint(1) NOT NULL,
  `subscription` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `unique_id`, `business_name`, `stripped`, `details`, `fullname`, `email`, `phone_number`, `profile_image`, `cover_image`, `cover_image_file`, `opening_hours`, `closing_hours`, `balance`, `service_charge`, `city`, `state`, `country`, `address`, `added_date`, `last_modified`, `access`, `subscription`, `status`) VALUES
(1, 'xHBBFaUQOshu2u1GHBhk', 'Emmynem Inc', 'emmynem-inc', NULL, 'Emmanuel Nwoye', 'emmanuelnwoye5@gmail.com', '08093223317', 'https://myaccount.xnyder.com/images/user.png', NULL, NULL, '08:30:00', '21:00:00', 40890, 0, 'PORTHARCOURT-DIOBU', 'Rivers', 'Nigeria', 'No 34 Emekuku Street, Diobu, Port Harcourt, Rivers State, Nigeria', '2021-11-07 14:40:56', '2021-11-13 22:40:25', 1, 1, 1),
(2, 'Q0k0T23V2rgLO8GeOYEY', 'JayDeVida', 'jaydevida', NULL, 'Juanita Imbu', 'juanitaimbut@gmail.com', '07035689456', 'https://myaccount.xnyder.com/images/user.png', NULL, NULL, '09:00:00', '20:00:00', 48565, 0, 'PORTHARCOURT-MILE 1', 'Rivers', 'Nigeria', 'No 9 Education, Mile 1, Port Harcourt, Rivers State, Nigeria', '2021-11-09 05:44:21', '2021-11-11 13:27:14', 1, 1, 1),
(3, 'M6iqLP2EEaAJNoT7H43w', 'Rivers Kitchen', 'rivers-kitchen', NULL, 'Kennedy James', 'jameskennedy@gmail.com', '08178962456', 'https://myaccount.xnyder.com/images/user.png', NULL, NULL, '09:00:00', '20:00:00', 0, 0, 'PORTHARCOURT-AGIP', 'Rivers', 'Nigeria', 'No 9 Agip road, Mile 1, Port Harcourt, Rivers State, Nigeria', '2021-11-09 06:12:37', '2021-11-09 06:12:37', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bank_accounts`
--

CREATE TABLE `vendor_bank_accounts` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_number` varchar(10) NOT NULL,
  `bank` varchar(50) NOT NULL,
  `default_status` varchar(10) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vendor_bank_accounts`
--

INSERT INTO `vendor_bank_accounts` (`id`, `unique_id`, `vendor_unique_id`, `account_name`, `account_number`, `bank`, `default_status`, `added_date`, `last_modified`, `status`) VALUES
(1, '3vVjX6mMgfJUMwYynj6r', 'Q0k0T23V2rgLO8GeOYEY', 'Juanita Imbu', '2121505841', 'Guaranty Trust Bank', 'Yes', '2021-11-11 22:09:12', '2021-11-11 22:09:12', 1),
(3, 'jvOhLJt0GoYuBEkrEZbG', 'Q0k0T23V2rgLO8GeOYEY', 'Juanita Imbu', '1230589645', 'Access Bank', 'No', '2021-11-11 22:09:47', '2021-11-11 22:09:47', 1),
(4, 'nHE669HeVnqAnsreflqR', 'xHBBFaUQOshu2u1GHBhk', 'Nwoye Emmanuel Aneku', '0245963215', 'Guaranty Trust Bank', 'Yes', '2021-11-11 22:25:58', '2021-11-11 22:25:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_users`
--

CREATE TABLE `vendor_users` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `vendor_unique_id` varchar(20) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `access` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vendor_users`
--

INSERT INTO `vendor_users` (`id`, `unique_id`, `user_unique_id`, `vendor_unique_id`, `fullname`, `email`, `phone_number`, `role`, `added_date`, `last_modified`, `access`, `status`) VALUES
(1, '8RQVxN84PKSCusUnfDub', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'Emmanuel Nwoye', 'emmanuelnwoye5@gmail.com', '08093223317', 'Owner', '2021-11-07 14:40:56', '2021-11-07 15:04:38', 1, 1),
(2, 'ddsj0xo8xZ1s9Tux2pgG', '8RQVxN84PKSCusUnfDub', 'xHBBFaUQOshu2u1GHBhk', 'Richard Gigi', 'gigirichardofficial@gmail.com', NULL, 'Administrator', '2021-11-07 14:55:06', '2021-11-07 15:05:41', 1, 1),
(3, 'g99EAp1SUp347EwT4j9D', 'g99EAp1SUp347EwT4j9D', 'Q0k0T23V2rgLO8GeOYEY', 'Juanita Imbu', 'juanitaimbut@gmail.com', '07035689456', 'Owner', '2021-11-09 05:44:21', '2021-11-09 05:44:21', 1, 1),
(4, 'ONfNeuZCOCuKcu7uMbY6', 'ONfNeuZCOCuKcu7uMbY6', 'M6iqLP2EEaAJNoT7H43w', 'Kennedy James', 'jameskennedy@gmail.com', '08178962456', 'Owner', '2021-11-09 06:12:37', '2021-11-09 06:12:37', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `view_history`
--

CREATE TABLE `view_history` (
  `id` bigint(20) NOT NULL,
  `unique_id` varchar(20) NOT NULL,
  `user_unique_id` varchar(20) NOT NULL,
  `product_unique_id` varchar(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `view_history`
--

INSERT INTO `view_history` (`id`, `unique_id`, `user_unique_id`, `product_unique_id`, `added_date`, `last_modified`, `status`) VALUES
(1, '0o5X3kQMZDjbLXF5kxeg', 'rzl5nk7rIHDpqMUbHuz9', 'tRC63h8VciHiUNxefnJg', '2021-11-14 10:11:42', '2021-11-14 10:11:42', 1),
(2, 'hSj3Plic5bGaoGfXVY8C', 'oiH9fzKVpI8jLubuBqUK', 'tRC63h8VciHiUNxefnJg', '2021-11-14 10:11:46', '2021-11-14 10:11:46', 1),
(3, 'Xg19qFjZEFKVZ7IZJLx7', 'oiH9fzKVpI8jLubuBqUK', '5CY9Krp5aDyMGai7eNnA', '2021-11-14 10:11:49', '2021-11-14 10:11:49', 1),
(4, 'zqJfXgJSajbBlQkynyAT', 'rzl5nk7rIHDpqMUbHuz9', '5CY9Krp5aDyMGai7eNnA', '2021-11-14 10:11:52', '2021-11-14 10:11:52', 1),
(7, 'yXn4abClkPY5wLFYfvgH', 'Anonymous', 'jZ5d1PgCf0cU4YgdDp4r', '2021-11-24 13:59:11', '2021-11-24 13:59:11', 1),
(8, 'lqJN17MwD4wMdhxrOqNq', 'Anonymous', 'jZ5d1PgCf0cU4YgdDp4r', '2021-11-24 13:59:32', '2021-11-24 13:59:32', 1),
(9, 'LzmuIaHtC0SxqWmh4Vfo', 'oiH9fzKVpI8jLubuBqUK', 'jZ5d1PgCf0cU4YgdDp4r', '2021-11-24 14:01:00', '2021-11-24 14:01:00', 1),
(10, 'LO35qNAYV90UStqx9h7p', 'oiH9fzKVpI8jLubuBqUK', 'AVcsIVjiqUAM0pnSOzhg', '2021-11-24 14:00:05', '2021-11-24 14:00:05', 1),
(11, '6vAPPuD573YcZhBqj855', 'oiH9fzKVpI8jLubuBqUK', 'Bbm36u2XaIbZUvlMOCom', '2021-11-24 14:00:44', '2021-11-24 14:00:44', 1),
(12, 'x76VlEN49yOUeLRO61o1', 'oiH9fzKVpI8jLubuBqUK', 'tRC63h8VciHiUNxefnJg', '2021-11-24 14:01:44', '2021-11-24 14:01:44', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `category_images`
--
ALTER TABLE `category_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `coupon_history`
--
ALTER TABLE `coupon_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`) USING BTREE;

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `orders_completed`
--
ALTER TABLE `orders_completed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD UNIQUE KEY `order_unique_id` (`order_unique_id`);

--
-- Indexes for table `order_coupons`
--
ALTER TABLE `order_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `review_ratings`
--
ALTER TABLE `review_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `search_history`
--
ALTER TABLE `search_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `shipping_fees`
--
ALTER TABLE `shipping_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `users_preferences`
--
ALTER TABLE `users_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`unique_id`),
  ADD UNIQUE KEY `email_address` (`email`),
  ADD UNIQUE KEY `mobile_digits` (`phone_number`);

--
-- Indexes for table `vendor_bank_accounts`
--
ALTER TABLE `vendor_bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `vendor_users`
--
ALTER TABLE `vendor_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `view_history`
--
ALTER TABLE `view_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category_images`
--
ALTER TABLE `category_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `coupon_history`
--
ALTER TABLE `coupon_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders_completed`
--
ALTER TABLE `orders_completed`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_coupons`
--
ALTER TABLE `order_coupons`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `preferences`
--
ALTER TABLE `preferences`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review_ratings`
--
ALTER TABLE `review_ratings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `search_history`
--
ALTER TABLE `search_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `shipping_fees`
--
ALTER TABLE `shipping_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users_addresses`
--
ALTER TABLE `users_addresses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users_preferences`
--
ALTER TABLE `users_preferences`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vendor_bank_accounts`
--
ALTER TABLE `vendor_bank_accounts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendor_users`
--
ALTER TABLE `vendor_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `view_history`
--
ALTER TABLE `view_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
