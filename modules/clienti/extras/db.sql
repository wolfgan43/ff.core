-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 21 lug, 2010 at 10:29 AM
-- Versione MySQL: 5.1.37
-- Versione PHP: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cm_mod_clienti_contatti`
--

CREATE TABLE IF NOT EXISTS `cm_mod_clienti_contatti` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_clienti` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cognome` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `cellulare` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `isReferente` char(1) NOT NULL,
  `reseller` char(1) NOT NULL,
  `disabled` char(1) NOT NULL,
  `nascita` date NOT NULL,
  `fax` varchar(255) NOT NULL,
  `hobby` varchar(255) NOT NULL,
  `squadra` varchar(255) NOT NULL,
  `associazione` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `cm_mod_clienti_fields`
--

CREATE TABLE IF NOT EXISTS `cm_mod_clienti_fields` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_clienti` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `cm_mod_clienti_main`
--

CREATE TABLE IF NOT EXISTS `cm_mod_clienti_main` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ragsoc` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cognome` varchar(255) NOT NULL,
  `piva` varchar(255) NOT NULL,
  `cf` varchar(255) NOT NULL,
  `indirizzo` varchar(255) NOT NULL,
  `cap` varchar(255) NOT NULL,
  `citta` varchar(255) NOT NULL,
  `provincia` int(11) NOT NULL,
  `nazione` int(11) NOT NULL,
  `isPotenziale` char(1) NOT NULL,
  `telefono1` varchar(255) NOT NULL,
  `telefono2` varchar(255) NOT NULL,
  `cellulare1` varchar(255) NOT NULL,
  `cellulare2` varchar(255) NOT NULL,
  `email1` varchar(255) NOT NULL,
  `email2` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `fax2` varchar(255) NOT NULL,
  `referente` int(11) NOT NULL,
  `tipo_azienda` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
