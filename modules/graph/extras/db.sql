-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2010 at 02:54 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `admin_xnmadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_graph_chart`
--

CREATE TABLE IF NOT EXISTS `cm_mod_graph_chart` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ID_type` int(11) NOT NULL,
  `axisColor` varchar(255) NOT NULL,
  `axisFontSize` varchar(255) NOT NULL,
  `backgroundColor` varchar(255) NOT NULL,
  `borderColor` varchar(255) NOT NULL,
  `colors` varchar(255) NOT NULL,
  `enableTooltip` char(1) NOT NULL,
  `focusBorderColor` varchar(255) NOT NULL,
  `height` varchar(255) NOT NULL,
  `isStacked` char(1) NOT NULL,
  `is3D` char(1) NOT NULL,
  `legend` varchar(255) NOT NULL,
  `legendBackgroundColor` varchar(255) NOT NULL,
  `legendFontSize` varchar(255) NOT NULL,
  `legendTextColor` varchar(255) NOT NULL,
  `max` varchar(255) NOT NULL,
  `min` varchar(255) NOT NULL,
  `pieJoinAngle` varchar(255) NOT NULL,
  `pieMinimalAngle` varchar(255) NOT NULL,
  `titleX` varchar(255) NOT NULL,
  `titleY` varchar(255) NOT NULL,
  `titleColor` varchar(255) NOT NULL,
  `titleFontSize` varchar(255) NOT NULL,
  `tooltipFontSize` varchar(255) NOT NULL,
  `tooltipWidth` varchar(255) NOT NULL,
  `tooltipHeight` varchar(255) NOT NULL,
  `width` varchar(255) NOT NULL,
  `column` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `titleXType` varchar(255) NOT NULL,
  `titleYType` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_graph_data`
--

CREATE TABLE IF NOT EXISTS `cm_mod_graph_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sql` text NOT NULL,
  `column` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_graph_data_detail`
--

CREATE TABLE IF NOT EXISTS `cm_mod_graph_data_detail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_graph` int(11) NOT NULL,
  `ID_data` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_graph_type`
--

CREATE TABLE IF NOT EXISTS `cm_mod_graph_type` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `template_path` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

INSERT INTO `cm_mod_graph_type` (`ID`, `name`, `array_dimension`, `template_path`) VALUES
(1, 'Torta', '2', 'torta.html'),
(2, 'Istogramma', '2', 'istogramma.html'),
(3, 'Linee', '3', 'linee.html'),
(18, 'Area', '2', 'area.html');
