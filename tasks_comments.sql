-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 10, 2013 at 02:42 PM
-- Server version: 5.1.66
-- PHP Version: 5.3.3-7+squeeze17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `flow`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks_comments`
--

CREATE TABLE IF NOT EXISTS `tasks_comments` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `task` int(10) NOT NULL DEFAULT '0',
  `project` int(10) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `posted` varchar(255) NOT NULL,
  `user` int(10) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
