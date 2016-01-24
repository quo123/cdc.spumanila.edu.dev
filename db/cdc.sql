-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2016 at 10:13 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cdc`
--
CREATE DATABASE IF NOT EXISTS `cdc` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cdc`;

-- --------------------------------------------------------

--
-- Table structure for table `actdates`
--

CREATE TABLE IF NOT EXISTS `actdates` (
  `dateid` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `actid` int(10) unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`dateid`),
  UNIQUE KEY `actid` (`actid`,`type`,`start`,`end`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `actdates`
--

INSERT INTO `actdates` (`dateid`, `actid`, `type`, `start`, `end`) VALUES
(18, 1, 'oncampus', '2015-03-03 01:01:00', '2015-03-03 01:05:00'),
(19, 1, 'oncampus', '2015-03-10 05:04:00', '2015-03-03 03:01:00'),
(16, 1, 'oncampus', '2015-03-11 02:02:00', '2015-03-18 01:00:00'),
(17, 1, 'oncampus', '2015-03-12 03:01:00', '2015-03-17 02:00:00'),
(8, 3, 'offcampus', '2015-03-06 14:37:00', '2015-03-06 14:40:00'),
(7, 4, 'offcampus', '2015-03-02 17:06:00', '2015-03-02 18:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE IF NOT EXISTS `evaluation` (
  `id` int(15) unsigned NOT NULL AUTO_INCREMENT,
  `reqcode` varchar(10) DEFAULT NULL,
  `student` int(10) unsigned NOT NULL,
  `schoolyear` int(4) NOT NULL,
  `semester` int(2) NOT NULL,
  `q1e1` varchar(1000) NOT NULL,
  `q2e1` varchar(50) NOT NULL,
  `q3e1` varchar(32) NOT NULL,
  `q3e2` varchar(1000) NOT NULL,
  `q4e1` varchar(50) NOT NULL,
  `q5e1` int(2) unsigned NOT NULL,
  `q5e2` varchar(1000) NOT NULL,
  `q6e1` int(2) unsigned NOT NULL,
  `q6e2` varchar(1000) NOT NULL,
  `q7e1` varchar(1000) NOT NULL,
  `q8e1` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student` (`student`,`schoolyear`,`semester`),
  KEY `reqcode` (`reqcode`),
  KEY `consolidation` (`reqcode`,`schoolyear`,`semester`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `reqcode`, `student`, `schoolyear`, `semester`, `q1e1`, `q2e1`, `q3e1`, `q3e2`, `q4e1`, `q5e1`, `q5e2`, `q6e1`, `q6e2`, `q7e1`, `q8e1`) VALUES
(1, 'OK', 2, 2014, 2, 'None. No reason.', 'wow', 'Christ-Centeredness', 'community - something something&trade;\r\ntesting "special ch@racters"\r\n\r\nmutiple spaces <script></script><?php echo "sdsds"; ?>', 'honesty', 2, 'fire', 4, '', 'Everything will be daijoubu.', 'become meguca'),
(2, 'OK', 1, 2014, 2, 'hallow', 'Lorem', 'Charity', 'IPsum', 'Dolor', 4, '', 2, 'Sit', 'Amet', 'Set asd\r\n\r\ndaw \r\nd\r\naw\r\nda\r\nwd\r\naw'),
(3, 'OK', 3, 2014, 2, 'Magic', 'Powered', 'Commitment to Mission', 'wowo', 'Halp', 2, 'all', 2, 'WEverthinsd', 'blablablalblab', 'itle>jQuery UI Tooltip - Custom animation demo</title>\r\n<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">\r\n<script src="//code.jquery.com/jquery-1.10.2.js"></script>\r\n<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>\r\n<link rel="stylesheet" href="/resources/demos/style.css">\r\n<script>\r\n$(function() {\r\n$( "#show-option" ).tooltip({\r\nshow: {\r\neffect: "slideDown",\r\ndelay: 250\r\n}\r\n});\r\n$( "#hide-option"'),
(6, 'OK', 3, 2015, 1, 'awda wda wd aw', 'nawidji ajw;dja iwojd oijaw', 'Commitment to Mission', 'oajdioajwd oijawijdoa ijwdoi jawjd ajw d', 'ajidoiaj w;odij;aoiwjdoia jw;odij a', 2, 'aiwdji ajwd lajwd ijoiawjd', 2, 'awjdiawj doiajwdi jaowijdoiajwdij', 'awjdawjdo ijawdjj', 'ajwdijaw 029j j1dj12 jd1'),
(7, 'V5-7RA-DHQ', 2, 2015, 1, '', '', '', '', '', 0, '', 0, '', '', ''),
(8, 'OK', 3, 2014, 1, 'awdawd awd aw', 'awd awd a2d2 ', 'Commitment to Mission', 'a2 a22 d2a', 'a da2 d', 3, '', 3, '', 'awd a awd ', 'adawd awd aw dad'),
(10, 'CH-OMM-BIF', 4, 2014, 2, '', '', '', '', '', 0, '', 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `offcampus`
--

CREATE TABLE IF NOT EXISTS `offcampus` (
  `offid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `actname` varchar(255) NOT NULL DEFAULT 'Activity Name',
  `organizer` varchar(64) NOT NULL DEFAULT 'Organizer',
  `address` varchar(255) NOT NULL DEFAULT 'Address',
  `pointperson` varchar(64) NOT NULL DEFAULT 'Point Person',
  `contact` varchar(13) NOT NULL DEFAULT 'Contact',
  `student` int(10) unsigned NOT NULL,
  `schoolyear` int(4) unsigned NOT NULL,
  `semester` int(2) unsigned NOT NULL,
  `hours` float NOT NULL DEFAULT '0',
  `cert` tinyint(1) NOT NULL DEFAULT '0',
  `eval` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`offid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `offcampus`
--

INSERT INTO `offcampus` (`offid`, `actname`, `organizer`, `address`, `pointperson`, `contact`, `student`, `schoolyear`, `semester`, `hours`, `cert`, `eval`) VALUES
(1, 'Jello Hello World', 'CDC', 'Blk 2 Lot 23, Sample Long Address, District, City', 'Teh Person', '09032165487', 1, 2014, 2, 2, 1, 0),
(2, 'Activity Name Sample', 'Personal', 'Address', 'Who What When', '09998788554', 1, 2014, 2, 2, 1, 0),
(3, 'Activity Names Sample', 'CDC', 'Address', 'Point Person', '09024848759', 2, 2014, 1, 2, 1, 0),
(4, 'My Convention', 'The Organizer', 'Unit 16F, Two Adriatico Place', 'Mr. Point Square', '09082525255', 2, 2014, 2, 4, 1, 1),
(5, 'Long off-campus activity', 'Organizers', 'Far, far away', 'Mr. Point', '4564646486468', 3, 2014, 2, 4, 1, 0),
(6, 'Othes', 'Org', 'Add', 'Mr', '168468484864', 4, 2014, 2, 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `oncampus`
--

CREATE TABLE IF NOT EXISTS `oncampus` (
  `onid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `actname` varchar(255) NOT NULL DEFAULT 'Activity Name',
  `category` varchar(32) NOT NULL DEFAULT 'Category',
  `initiator` varchar(64) NOT NULL DEFAULT 'Initiator',
  `student` int(10) unsigned NOT NULL,
  `schoolyear` int(4) unsigned NOT NULL,
  `semester` int(2) unsigned NOT NULL,
  `hours` float NOT NULL DEFAULT '0',
  `eval` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`onid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `oncampus`
--

INSERT INTO `oncampus` (`onid`, `actname`, `category`, `initiator`, `student`, `schoolyear`, `semester`, `hours`, `eval`) VALUES
(1, 'Activity Name', 'CDC', 'Initiator', 1, 2014, 2, 8, 0),
(2, 'Sample long activity name that is long', 'Category', 'Initiator', 1, 2014, 1, 0, 0),
(3, 'Convention', 'Community', 'Personal', 2, 2014, 2, 4, 1),
(4, 'Act2', 'Random', 'Course Requirement', 2, 2014, 2, 2, 0),
(5, 'Other Act', 'Sample others', 'Mr. X', 2, 2014, 2, 2, 1),
(8, 'more', 'Parish', 'Personal', 2, 2014, 2, 0, 0),
(9, 'Stull more', 'CDC', 'Mr long initiator that seervers to destroy table layout', 2, 2014, 2, 0, 0),
(10, 'adaw', 'Parish', 'Personal', 2, 2014, 2, 0, 0),
(11, 'awdwa', 'Parish', 'Personal', 2, 2014, 2, 0, 0),
(12, 'awdwa', 'Community', 'Personal', 2, 2015, 2, 0.5, 0),
(13, 'Stull more', 'Parish', 'Course Requirement', 2, 2015, 1, 0, 0),
(14, 'Sample long activity name that is long', 'Parish', 'Course Requirement', 3, 2014, 2, 8, 0),
(15, 'Other Act', 'CDC', 'Course Requirement', 4, 2014, 2, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `schoolyear` int(4) NOT NULL,
  `semester` int(2) NOT NULL,
  PRIMARY KEY (`schoolyear`,`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`schoolyear`, `semester`) VALUES
(2014, 2);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lname` varchar(25) NOT NULL,
  `fname` varchar(25) NOT NULL,
  `mname` varchar(4) NOT NULL,
  `course` varchar(10) NOT NULL,
  `year` varchar(2) NOT NULL DEFAULT '1',
  `address` varchar(90) NOT NULL,
  `Contact` varchar(13) NOT NULL,
  `Bday` date NOT NULL,
  `Age` int(2) NOT NULL,
  `Gender` varchar(1) NOT NULL,
  `CivStat` varchar(1) NOT NULL,
  `Father` varchar(64) NOT NULL,
  `FatherPhone` varchar(13) NOT NULL,
  `Mother` varchar(64) NOT NULL,
  `MotherPhone` varchar(13) NOT NULL,
  `HasPhoto` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `student_name` (`lname`,`fname`),
  FULLTEXT KEY `standard_search` (`lname`,`fname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`sid`, `lname`, `fname`, `mname`, `course`, `year`, `address`, `Contact`, `Bday`, `Age`, `Gender`, `CivStat`, `Father`, `FatherPhone`, `Mother`, `MotherPhone`, `HasPhoto`) VALUES
(1, 'Betoya', 'Alvin', 'R', 'BSCS-ST', '3', 'Unit 16F, Two Adriatico Place', '09012345678', '1990-04-30', 24, 'm', 's', 'Mr. Father', '09198654321', 'Mrs. Mother', '09050102030', 1),
(2, 'Betoya', 'Shinary Ley', 'R', 'BAPSY', '3', 'Sample Address', '09012345678', '1999-03-03', 15, 'f', 's', 'Mr. Father', '09198654321', 'Mrs. Mother', '09050102030', 1),
(3, 'Shiba', 'Tatsuya', 'D', 'MAGIC', '1', 'Sample Address', '09082525255', '1998-04-09', 16, 'm', 's', 'Mr. Father', '09198654321', 'Mrs. Mother', '09050102030', 1),
(4, 'Shiba', 'Miyuki', 'Y', 'MAGIC', '1', 'First High', '09012345678', '2003-03-07', 12, 'f', 's', 'Shiba', '09046589999', 'Yotsuba', '09089988987', 1);

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE IF NOT EXISTS `userinfo` (
  `userid` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `userpass` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `userinfo`
--

INSERT INTO `userinfo` (`userid`, `username`, `userpass`, `status`, `admin`) VALUES
(1, '001', '202cb962ac59075b964b07152d234b70', 1, 1),
(2, 'test', '202cb962ac59075b964b07152d234b70', 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
