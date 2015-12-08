-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mar 08 Décembre 2015 à 01:22
-- Version du serveur :  5.5.46-0+deb8u1
-- Version de PHP :  5.6.14-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `labo`
--

-- --------------------------------------------------------

--
-- Structure de la table `labo_employee`
--

CREATE TABLE `labo_employee` (
  `id_employee` int(10) UNSIGNED NOT NULL,
  `login` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  `id_lang` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `id_profile` int(10) UNSIGNED NOT NULL,
  `active` enum('0','1') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `start_date_from` date DEFAULT NULL,
  `start_date_to` date DEFAULT NULL,
  `last_connection_date` date DEFAULT '0000-00-00',
  `last_passwd_gen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(45) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `labo_employee`
--

INSERT INTO `labo_employee` (`id_employee`, `login`, `lastname`, `firstname`, `email`, `password`, `id_lang`, `id_profile`, `active`, `start_date_from`, `start_date_to`, `last_connection_date`, `last_passwd_gen`, `token`, `token_expire`) VALUES
(1, 'benayaf', 'Benayad', 'Afid', 'afid.benayad@gmail.com', '', 1, 1, '1', '2015-10-01', '0000-00-00', '2015-10-11', '2015-09-20 11:59:20', 'ccc82e21a26d79dc186161d4a825567f', '2015-12-10 01:07:17'),
(2, 'jannah', 'jannah', 'jannah', 'cette.corbeille@gmail.com', '', 1, 2, '0', '2015-08-22', '0000-00-00', '0000-00-00', '2015-09-22 11:42:03', NULL, NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `labo_employee`
--
ALTER TABLE `labo_employee`
  ADD PRIMARY KEY (`id_employee`),
  ADD KEY `employee_login` (`login`,`email`,`password`),
  ADD KEY `id_employee_passwd` (`id_employee`,`password`),
  ADD KEY `id_profile` (`id_profile`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `labo_employee`
--
ALTER TABLE `labo_employee`
  MODIFY `id_employee` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
