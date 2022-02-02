-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 19, 2022 at 06:42 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tg_wallet`
--

-- --------------------------------------------------------

--
-- Table structure for table `fight`
--

DROP TABLE IF EXISTS `fight`;
CREATE TABLE IF NOT EXISTS `fight` (
  `userid` int(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `amount` int(9) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `messageid` int(9) NOT NULL,
  `uid` int(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guessno`
--

DROP TABLE IF EXISTS `guessno`;
CREATE TABLE IF NOT EXISTS `guessno` (
  `userid` int(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `guess` int(2) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

DROP TABLE IF EXISTS `transfers`;
CREATE TABLE IF NOT EXISTS `transfers` (
  `response` int(2) NOT NULL,
  `messageid` int(15) NOT NULL,
  `userid` int(15) NOT NULL,
  `amountsent` int(9) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `username` varchar(30) NOT NULL,
  `recepuser` varchar(34) NOT NULL,
  `senderbalb` int(10) NOT NULL,
  `senderbala` int(10) NOT NULL,
  `recepbalb` int(10) NOT NULL,
  `recepbala` int(10) NOT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `txelicoin`
--

DROP TABLE IF EXISTS `txelicoin`;
CREATE TABLE IF NOT EXISTS `txelicoin` (
  `account` varchar(15) NOT NULL,
  `address` varchar(34) NOT NULL,
  `category` varchar(7) NOT NULL,
  `amount` int(9) NOT NULL,
  `txid` varchar(64) NOT NULL,
  `timereceived` int(10) NOT NULL,
  PRIMARY KEY (`txid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(30) NOT NULL,
  `address` varchar(34) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `userid` int(15) NOT NULL,
  `balance` int(9) NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
