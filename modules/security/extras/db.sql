-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_users`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_domains` int(11) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `level` char(1) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL,
  `expiration` datetime,
  `email` varchar(255) NOT NULL,
  `time_zone` int(11) NOT NULL DEFAULT '2',
  `role` int(11),
  `created` datetime NOT NULL,
  `modified` datetime,
  `password_generated_at` datetime,
  `temp_password` varchar(255),
  `password_used` char(1),
  `ID_packages` int(11),
  `lastlogin` datetime,
  `profile` int(11),
  `special` varchar(255),
  `avatar` varchar(255)  NOT NULL DEFAULT '',
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `lastname` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `ID_packages` (`ID_packages`),
  KEY `profile` (`profile`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `cm_mod_security_users`
--
INSERT INTO cm_mod_security_users (ID, ID_domains, username, password, level, status, expiration, email, time_zone, created) VALUES (NULL , 0, 'admin', password('password'), 3, 1, 0, 'admin@domain.com', '2', now());

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_users_fields`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_users_fields` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_users` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_users` (`ID_users`),
  KEY `ID_users_2` (`ID_users`,`field`),
  KEY `field` (`field`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_timezones`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_timezones` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `cm_mod_security_timezones`
--

INSERT INTO `cm_mod_security_timezones` (`ID`, `name`) VALUES
(1, 'GMT-14'),
(2, 'GMT-13'),
(3, 'GMT-12'),
(4, 'GMT-11'),
(5, 'GMT-10'),
(6, 'GMT-9'),
(7, 'GMT-8'),
(8, 'GMT-7'),
(9, 'GMT-6'),
(10, 'GMT-5'),
(11, 'GMT-4'),
(12, 'GMT-3'),
(13, 'GMT-2'),
(14, 'GMT-1'),
(15, 'GMT+0'),
(16, 'GMT+1'),
(17, 'GMT+2'),
(18, 'GMT+3'),
(19, 'GMT+4'),
(20, 'GMT+5'),
(21, 'GMT+6'),
(22, 'GMT+7'),
(23, 'GMT+8'),
(24, 'GMT+9'),
(25, 'GMT+10'),
(26, 'GMT+11'),
(27, 'GMT+12');

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_domains`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_domains` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `owner` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `expiration_date` date NOT NULL,
  `time_zone` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `billing_status` int(11) NOT NULL,
  `db_host` varchar(255) NOT NULL,
  `db_name` varchar(255) NOT NULL,
  `db_user` varchar(255) NOT NULL,
  `db_pass` varchar(255) NOT NULL,
  `ID_packages` int(11) NOT NULL,
  `max_users` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_packages` (`ID_packages`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `cm_mod_security_domains_fields` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_domains` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_domains` (`ID_domains`),
  KEY `ID_domains_2` (`ID_domains`,`field`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_packages`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_packages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` char(1) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_packages_fields`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_packages_fields` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_packages` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `unlimited` char(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_packages_2` (`ID_packages`,`field`),
  KEY `ID_packages` (`ID_packages`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `ff_international` (`ID_lang`, `word_code`, `description`) VALUES
(1, 'login_username', 'nome utente'),
(1, 'login_password', 'password'),
(1, 'login_confirm', 'accedi'),
(1, 'login_text_logout', 'Logout'),
(1, 'nav_profile', 'Profilo'),
(1, 'logout_title', 'Sei sicuro di voler effettuare il Logout?'),
(1, 'logout_confirm', 'Si'),
(1, 'logout_cancel', 'No'),
(1, 'user_username', 'Username'),
(1, 'user_password', 'Password'),
(1, 'user_confirmpass', 'Conferma Password'),
(1, 'user_email', 'Indirizzo E-Mail'),
(1, 'user_nome', 'Nome'),
(1, 'user_cognome', 'Cognome'),
(1, 'user_indirizzo', 'Indirizzo'),
(1, 'user_citta', 'Città'),
(1, 'user_provincia', 'Provincia'),
(1, 'user_cap', 'CAP'),
(1, 'user_paese', 'Paese'),
(1, 'user_telefono', 'Telefono'),
(1, 'user_datanascita', 'Data di Nascita (gg/mm/aaaa)'),
(1, 'user_sesso', 'Sesso'),
(1, 'user_provincianascita', 'Provincia di Nascita'),
(1, 'user_cittanascita', 'Nato a'),
(1, 'user_codicefiscale', 'Codice Fiscale'),
(1, 'user_sessomaschio', 'Maschio'),
(1, 'user_sessofemmina', 'Femmina'),
(1, 'user_piva', 'Partita IVA'),
(1, 'user_publish', 'Pubblica in Home Page'),
(1, 'back_to_site', 'Torna al sito');

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_profiles`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_profiles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `created_time` datetime NOT NULL,
  `created_user` int(11) NOT NULL,
  `modified_time` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `enabled` char(1) NOT NULL,
  `order` int(11) NOT NULL,
  `special` varchar(255) NOT NULL,
  `acl` varchar(255) NOT NULL,
  `ID_domains` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cm_mod_security_profiles_pairs`
--

CREATE TABLE IF NOT EXISTS `cm_mod_security_profiles_pairs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_profile` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `view_own` char(1) NOT NULL,
  `view_others` char(1) NOT NULL,
  `modify_own` char(1) NOT NULL,
  `modify_others` char(1) NOT NULL,
  `insert_own` char(1) NOT NULL,
  `insert_others` char(1) NOT NULL,
  `delete_own` char(1) NOT NULL,
  `delete_others` char(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_profile` (`ID_profile`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cm_mod_security_rel_profiles_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_user` int(11) NOT NULL,
  `ID_profile` int(11) NOT NULL,
  `enabled` char(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;


CREATE TABLE IF NOT EXISTS `cm_mod_security_token` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ID_user` int(11) NOT NULL,
  `ID_domain` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `type` (`type`, `ID_user`),
  KEY `ID_user` (`ID_user`),
  KEY `ID_domain` (`ID_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
