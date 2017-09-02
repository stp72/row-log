-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Sep 01, 2017 at 05:44 PM
-- Server version: 5.1.73-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `row_log`
--

-- --------------------------------------------------------

--
-- Table structure for table `rower`
--

CREATE TABLE IF NOT EXISTS `rower` (
  `rower_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `weight_class` varchar(1) DEFAULT '1',
  `passwd` varchar(85) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rower_id`),
  UNIQUE KEY `rower_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- --------------------------------------------------------

--
-- Table structure for table `rower_log`
--

CREATE TABLE IF NOT EXISTS `rower_log` (
  `rower_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `rower_id` int(11) NOT NULL,
  `date_rowed` date NOT NULL,
  `distance` int(11) NOT NULL,
  `hours` int(11) DEFAULT '0',
  `minutes` int(11) DEFAULT '0',
  `seconds` int(11) DEFAULT '0',
  `tenths` int(11) DEFAULT '0',
  `notes` text NOT NULL,
  PRIMARY KEY (`rower_log_id`),
  KEY `rower_id` (`rower_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rower_log`
--
ALTER TABLE `rower_log`
  ADD CONSTRAINT `rower_id_fk` FOREIGN KEY (`rower_id`) REFERENCES `rower` (`rower_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
