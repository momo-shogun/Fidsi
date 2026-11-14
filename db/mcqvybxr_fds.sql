-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 05, 2026 at 12:58 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcqvybxr_fds`
--

-- --------------------------------------------------------

--
-- Table structure for table `fds_contact`
--

CREATE TABLE `fds_contact` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ddate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fds_contact`
--

INSERT INTO `fds_contact` (`id`, `name`, `email`, `mobile`, `subject`, `message`, `ddate`) VALUES
(1, 'Rahul Negi', 'rahulnegi.dpi@gmail.com', '09716562282', 'test', 'tetre', '2025-07-19 10:25:46'),
(2, 'Neeraj kumar ', 'drneerajkumarytmleadrytmleadr@gmail.com', '7518064376', 'Direct selling ', 'Neeraj kumar associate ', '2025-08-09 07:47:40'),
(3, 'Mohd Rafi ', 'rbahi0754@gmail.com', '9906893276', 'English ', 'Drydigdk', '2026-01-30 10:02:45'),
(4, 'Taimur Rahaman ', 'taimurrahaman391@gmail.com', '8906193310', 'Good ', 'Good dedition ', '2026-03-09 11:41:23'),
(5, 'Ayush Rattan', 'ayush.rattan01@gmail.com', '7042673391', 'Formal Notice: Misleading Advertisement and Hidden Charges in BE10X Offer', 'I am filing this complaint against BE10X for misleading advertisement and unfair trade practices.\r\n\r\nI received a promotional offer via Blinkit stating that I had unlocked a â€œFree AI Workshop worth â‚¹1999â€ using coupon code BE10X. Based on this claim', '2026-04-29 18:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `fds_join`
--

CREATE TABLE `fds_join` (
  `id` int(11) NOT NULL,
  `co_name` varchar(255) DEFAULT NULL,
  `co_website` varchar(255) DEFAULT NULL,
  `co_nature` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ddate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fds_join`
--

INSERT INTO `fds_join` (`id`, `co_name`, `co_website`, `co_nature`, `name`, `mobile`, `email`, `message`, `ddate`) VALUES
(1, 'erwerwer', 'werwer', 'werwer', 'Rahul Negi', '09716562282', 'rahulnegi.dpi@gmail.com', 'werwer', '2025-07-19 10:38:36'),
(2, 'Rcm Consumer Product P Ltd', 'www.rcmworld.com ', 'Networking', 'Durgesh Dadhich', '9772668400', 'neru.durgesh10@gmail.com', 'Dsm à¤¬à¤¹à¥à¤¤ à¤–à¥‚à¤¬à¤¸à¥‚à¤°à¤¤ à¤‡à¤‚à¤¡à¤¿à¤¸à¥à¤Ÿà¥à¤°à¥€ à¤¹à¥ˆ ', '2025-07-19 10:53:31'),
(3, 'sandhya', 'www.directsellingtoday.co', 'direct seeling today ', 'sandhya ', 'aBGSCdvzsiKv', 'asHcfvUSDJVK', 'ABKVDISgfikZS', '2025-07-21 12:23:38'),
(4, 'sandhya', 'www.directsellingtoday', 'direct seeling today ', 'sandhya ', 'gfdgkjl', 'dfghjkl', 'dsfgh;l', '2025-07-21 12:36:01'),
(5, 'Asclepius Wellness Private Limited', 'www.aclepiuswellness.com ', 'Direct Selling ', 'Ranjit Kumar Singh', '8480155041', 'ranjitkumarsingh861@gmail.com', 'Hello,\r\nThis is Ranjit Kumar Singh, Blue Diamond (AWPL). How can I associated with FIDSI.', '2025-07-29 05:55:29'),
(6, 'RHI nature wellness private limited ', 'www.rhinature.com', 'Product sale ', 'Sahil Shivnakar ', '9284351491', 'sahilshivankar188@gmail.com', 'Thank you ðŸ‘ðŸ»', '2025-08-02 15:39:37'),
(7, 'BYOLIVA WELLNESS', 'www.mybyoliva.com', 'Direct Selling', 'DR. MOJAMMIL', '7903844636', 'byolivawellness@gmail.com', 'Jharkhand 1 St Direct Selling Company', '2025-12-31 15:05:43'),
(8, 'Vucana business marketing private limited ', 'vucanabusiness.co.in', 'Products sale', 'Mahabir parsad ', '8949818804', 'mahabirsaharan41@hmail.com', 'Sir mujhe apni company ko FIDSI Main Registered krana hai sir .\r\n05 Oct 2018 se company registered hai sir \r\n\r\n', '2026-01-01 15:10:07'),
(9, 'VUCANA BUSINESS MARKETING PRIVATE LIMITED', 'www.vucanabusiness.co.in', 'PRODUCT SALE', 'MAHABIR PARSAD', '8949818804', 'MAHABIRSAHARAN41@GMAIL.COM', 'Sir, I want to register my company Vuchna Business Marketing Pvt Ltd in FEEDC, what will be the process?', '2026-01-22 08:18:45'),
(10, 'ELETION CARRIER RESOURCE MARKETING PRIVATE LIMITED ', 'www.striverempire.com', 'Garments ', 'Pateliya Dhaval ', '9106061141', 'pateliyadhaval715@gmail.com', 'Best direct selling company STRIVER EMPIRE ', '2026-02-05 02:38:40'),
(11, 'Redream Marketing Pvt Ltd', 'www.welagrow.com', 'product selling', 'Laxmanram Lohar', '6357028005', 'welagrow@gmail.com', 'my company want join to fidsi', '2026-03-30 10:46:34');

-- --------------------------------------------------------

--
-- Table structure for table `subscribe`
--

CREATE TABLE `subscribe` (
  `id` int(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `ddate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subscribe`
--

INSERT INTO `subscribe` (`id`, `email`, `ddate`) VALUES
(1, 'ankit.kaushik08@yahoo.com', '2025-07-22 12:39:06'),
(2, 'ankit.dpiinfotech8@gmail.com', '2025-07-22 12:39:27'),
(3, 'rjvmpl@gmail.com', '2026-01-21 09:01:49'),
(4, 'rbahi0754@gmail.com', '2026-01-30 10:03:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fds_contact`
--
ALTER TABLE `fds_contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fds_join`
--
ALTER TABLE `fds_join`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribe`
--
ALTER TABLE `subscribe`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fds_contact`
--
ALTER TABLE `fds_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fds_join`
--
ALTER TABLE `fds_join`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subscribe`
--
ALTER TABLE `subscribe`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
