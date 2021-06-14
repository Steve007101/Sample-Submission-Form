-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2021 at 08:42 AM
-- Server version: 10.3.29-MariaDB
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intercotrading_sl_forms`
--

-- --------------------------------------------------------

--
-- Table structure for table `sl_old_denise_lab`
--

CREATE TABLE `sl_old_denise_lab` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `tscreated` timestamp(6) NULL DEFAULT NULL,
  `rdescription` varchar(400) DEFAULT NULL,
  `ocompany` varchar(48) DEFAULT NULL,
  `srecipient` varchar(24) DEFAULT NULL,
  `lab_report` varchar(64) NOT NULL,
  `trader` varchar(64) NOT NULL,
  `sdescription` varchar(64) NOT NULL,
  `photo1` varchar(64) NOT NULL,
  `photo2` varchar(64) NOT NULL,
  `photo3` varchar(64) NOT NULL,
  `photo4` varchar(64) NOT NULL,
  `photo5` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_old_denise_to_customer`
--

CREATE TABLE `sl_old_denise_to_customer` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `tscreated` timestamp NULL DEFAULT NULL,
  `rdescription` varchar(117) DEFAULT NULL,
  `ocompany` varchar(34) DEFAULT NULL,
  `rcompany` varchar(44) DEFAULT NULL,
  `rco` varchar(33) DEFAULT NULL,
  `raddress` varchar(102) DEFAULT NULL,
  `trader` varchar(17) DEFAULT NULL,
  `sdescription` varchar(6) DEFAULT NULL,
  `photo1` varchar(44) DEFAULT NULL,
  `carrier` varchar(18) DEFAULT NULL,
  `tracking_num` varchar(29) DEFAULT NULL,
  `photo2` varchar(43) DEFAULT NULL,
  `photo3` varchar(40) DEFAULT NULL,
  `photo4` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_old_excel_log`
--

CREATE TABLE `sl_old_excel_log` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `tscreated` timestamp NULL DEFAULT NULL,
  `trader` varchar(32) DEFAULT NULL,
  `stype` varchar(32) DEFAULT NULL,
  `sorigin` varchar(32) DEFAULT NULL,
  `srecipient` varchar(32) DEFAULT NULL,
  `sdescription` varchar(400) DEFAULT NULL,
  `rcompany` varchar(48) DEFAULT NULL,
  `rco` varchar(32) DEFAULT NULL,
  `raddress` varchar(64) DEFAULT NULL,
  `raddress2` varchar(32) DEFAULT NULL,
  `rcity` varchar(32) DEFAULT NULL,
  `rregion` varchar(48) DEFAULT NULL,
  `rzip` varchar(16) DEFAULT NULL,
  `rcountry` varchar(32) DEFAULT NULL,
  `rphone` varchar(32) DEFAULT NULL,
  `remail` varchar(32) DEFAULT NULL,
  `rdescription` varchar(400) DEFAULT NULL,
  `rshipping` varchar(400) DEFAULT NULL,
  `hazardous` tinyint(1) DEFAULT NULL,
  `flammable` tinyint(1) DEFAULT NULL,
  `photo1` varchar(64) DEFAULT NULL,
  `photo2` varchar(64) DEFAULT NULL,
  `photo3` varchar(64) DEFAULT NULL,
  `photo4` varchar(64) DEFAULT NULL,
  `tracking_num` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_cover`
--

CREATE TABLE `sl_sample_cover` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `lab_full_comp` tinyint(1) DEFAULT NULL,
  `lab_oxide` tinyint(1) DEFAULT NULL,
  `lab_precious` tinyint(1) DEFAULT NULL,
  `lab_moisture` tinyint(1) DEFAULT NULL,
  `lab_as` tinyint(1) DEFAULT NULL,
  `lab_ba` tinyint(1) DEFAULT NULL,
  `lab_cd` tinyint(1) DEFAULT NULL,
  `lab_cr` tinyint(1) DEFAULT NULL,
  `lab_pb` tinyint(1) DEFAULT NULL,
  `lab_hg` tinyint(1) DEFAULT NULL,
  `lab_se` tinyint(1) DEFAULT NULL,
  `lab_ag` tinyint(1) DEFAULT NULL,
  `lab_notes` varchar(500) DEFAULT NULL,
  `lab_cover` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_origin_form`
--

CREATE TABLE `sl_sample_origin_form` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `ocompany` varchar(64) NOT NULL,
  `oco` varchar(64) DEFAULT NULL,
  `oaddress` varchar(128) DEFAULT NULL,
  `oaddress2` varchar(64) DEFAULT NULL,
  `ocity` varchar(64) DEFAULT NULL,
  `oregion` varchar(64) DEFAULT NULL,
  `ozip` varchar(24) DEFAULT NULL,
  `ocountry` varchar(64) DEFAULT NULL,
  `ophone` varchar(24) DEFAULT NULL,
  `oemail` varchar(64) DEFAULT NULL,
  `odescription` varchar(500) DEFAULT NULL,
  `oshipping` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_photos`
--

CREATE TABLE `sl_sample_photos` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `photo1` varchar(64) DEFAULT NULL,
  `photo2` varchar(64) DEFAULT NULL,
  `photo3` varchar(64) DEFAULT NULL,
  `photo4` varchar(64) DEFAULT NULL,
  `photo5` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_recipient_form`
--

CREATE TABLE `sl_sample_recipient_form` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `rcompany` varchar(64) NOT NULL,
  `rco` varchar(64) DEFAULT NULL,
  `raddress` varchar(128) DEFAULT NULL,
  `raddress2` varchar(64) DEFAULT NULL,
  `rcity` varchar(64) DEFAULT NULL,
  `rregion` varchar(64) DEFAULT NULL,
  `rzip` varchar(24) DEFAULT NULL,
  `rcountry` varchar(64) DEFAULT NULL,
  `rphone` varchar(24) DEFAULT NULL,
  `remail` varchar(64) DEFAULT NULL,
  `set_arrival` tinyint(1) DEFAULT NULL,
  `rdate` date DEFAULT NULL,
  `rdescription` varchar(500) DEFAULT NULL,
  `rshipping` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_report`
--

CREATE TABLE `sl_sample_report` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `tstracking` timestamp NULL DEFAULT NULL,
  `carrier` varchar(32) DEFAULT NULL,
  `tracking_num` varchar(64) DEFAULT NULL,
  `lab_report` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sl_sample_submission_form`
--

CREATE TABLE `sl_sample_submission_form` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `tscreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `tsmodified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trader` varchar(32) DEFAULT NULL,
  `trader_email` varchar(64) DEFAULT NULL,
  `stype` set('Samples To Be Mailed','Lab Testing Information','Special Lab Testing Information','') DEFAULT NULL,
  `sorigin` set('Supplier','ITC Warehouse','Customer','') DEFAULT NULL,
  `srecipient` set('Lab','Customer','Supplier','St Louis Testing (Lab)','UMSL Labs') DEFAULT NULL,
  `hazardous` tinyint(1) DEFAULT NULL,
  `flammable` tinyint(1) DEFAULT NULL,
  `sdescription` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sl_old_denise_lab`
--
ALTER TABLE `sl_old_denise_lab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_old_denise_to_customer`
--
ALTER TABLE `sl_old_denise_to_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_old_excel_log`
--
ALTER TABLE `sl_old_excel_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_cover`
--
ALTER TABLE `sl_sample_cover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_origin_form`
--
ALTER TABLE `sl_sample_origin_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_photos`
--
ALTER TABLE `sl_sample_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_recipient_form`
--
ALTER TABLE `sl_sample_recipient_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_report`
--
ALTER TABLE `sl_sample_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sl_sample_submission_form`
--
ALTER TABLE `sl_sample_submission_form`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sl_old_denise_lab`
--
ALTER TABLE `sl_old_denise_lab`
  MODIFY `id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sl_old_denise_to_customer`
--
ALTER TABLE `sl_old_denise_to_customer`
  MODIFY `id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sl_old_excel_log`
--
ALTER TABLE `sl_old_excel_log`
  MODIFY `id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sl_sample_submission_form`
--
ALTER TABLE `sl_sample_submission_form`
  MODIFY `id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sl_sample_cover`
--
ALTER TABLE `sl_sample_cover`
  ADD CONSTRAINT `foreign ID` FOREIGN KEY (`id`) REFERENCES `sl_sample_submission_form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sl_sample_origin_form`
--
ALTER TABLE `sl_sample_origin_form`
  ADD CONSTRAINT `sl_sample_origin_form_ibfk_1` FOREIGN KEY (`id`) REFERENCES `sl_sample_submission_form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sl_sample_photos`
--
ALTER TABLE `sl_sample_photos`
  ADD CONSTRAINT `Photos main table ID` FOREIGN KEY (`id`) REFERENCES `sl_sample_submission_form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
