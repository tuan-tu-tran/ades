-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 28 Septembre 2011 à 13:38
-- Version du serveur: 5.1.54
-- Version de PHP: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `ades_package`
--

-- --------------------------------------------------------

--
-- Structure de la table `ades_boite_email`
--

CREATE TABLE IF NOT EXISTS `ades_boite_email` (
  `id_boite_email` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `ref_user` int(11) NOT NULL,
  `ref_mail` int(11) NOT NULL,
  `ref_dossier` int(11) NOT NULL,
  `Lu` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_boite_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Contenu de la table `ades_boite_email`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_eleves`
--

CREATE TABLE IF NOT EXISTS `ades_eleves` (
  `ideleve` smallint(6) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `prenom` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `classe` char(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `anniv` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00/00',
  `contrat` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `codeInfo` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `nomResp` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `courriel` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `telephone1` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `telephone2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `telephone3` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `memo` blob NOT NULL,
  `dermodif` date NOT NULL DEFAULT '0000-00-00',
  `idunique` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ideleve`),
  UNIQUE KEY `idunique` (`idunique`),
  KEY `classe` (`classe`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ades_eleves`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_faits`
--

CREATE TABLE IF NOT EXISTS `ades_faits` (
  `idfait` int(11) NOT NULL AUTO_INCREMENT,
  `idorigine` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `ideleve` int(11) NOT NULL DEFAULT '0',
  `ladate` date NOT NULL DEFAULT '0000-00-00',
  `motif` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `professeur` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `idretenue` int(11) NOT NULL,
  `travail` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `materiel` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `sanction` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nopv` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dermodif` date NOT NULL DEFAULT '0000-00-00',
  `qui` smallint(6) NOT NULL DEFAULT '0',
  `supprime` enum('O','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idfait`),
  KEY `ideleve` (`ideleve`),
  KEY `date` (`ladate`),
  KEY `idorigine` (`idorigine`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ades_faits`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_mail`
--

CREATE TABLE IF NOT EXISTS `ades_mail` (
  `id_mail` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `ref_expediteur` int(11) NOT NULL,
  `ref_mail` int(11) NOT NULL,
  `sujet` varchar(100) NOT NULL,
  `texte` text NOT NULL,
  `Brouillon` tinyint(1) NOT NULL,
  `date_envoi` datetime NOT NULL,
  PRIMARY KEY (`id_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `ades_mail`
--

INSERT INTO `ades_mail` (`id_mail`, `ref_expediteur`, `ref_mail`, `sujet`, `texte`, `Brouillon`, `date_envoi`) VALUES
(0000000025, 1, 0, 'ter', 'gef', 0, '2011-09-28 13:29:31');

-- --------------------------------------------------------

--
-- Structure de la table `ades_mail_destinataire`
--

CREATE TABLE IF NOT EXISTS `ades_mail_destinataire` (
  `id_mail_destinataire` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `ref_mail` int(11) NOT NULL,
  `ref_id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_mail_destinataire`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Contenu de la table `ades_mail_destinataire`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_mail_dossier`
--

CREATE TABLE IF NOT EXISTS `ades_mail_dossier` (
  `id_dossier` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `nom_dossier` varchar(45) NOT NULL,
  PRIMARY KEY (`id_dossier`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ades_mail_dossier`
--

INSERT INTO `ades_mail_dossier` (`id_dossier`, `nom_dossier`) VALUES
(0000000001, 'Boite Principale'),
(0000000002, 'Archive');

-- --------------------------------------------------------

--
-- Structure de la table `ades_retenues`
--

CREATE TABLE IF NOT EXISTS `ades_retenues` (
  `typeDeRetenue` tinyint(4) NOT NULL DEFAULT '0',
  `idretenue` int(11) NOT NULL AUTO_INCREMENT,
  `ladate` date NOT NULL DEFAULT '0000-00-00',
  `heure` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `duree` tinyint(4) NOT NULL DEFAULT '1',
  `local` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `places` tinyint(4) NOT NULL DEFAULT '0',
  `occupation` tinyint(4) NOT NULL DEFAULT '0',
  `affiche` enum('O','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'O',
  PRIMARY KEY (`idretenue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `ades_retenues`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_todo`
--

CREATE TABLE IF NOT EXISTS `ades_todo` (
  `id_todo` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `ref_personne` int(11) NOT NULL,
  `todo` text NOT NULL,
  PRIMARY KEY (`id_todo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=242 ;

--
-- Contenu de la table `ades_todo`
--


-- --------------------------------------------------------

--
-- Structure de la table `ades_users`
--

CREATE TABLE IF NOT EXISTS `ades_users` (
  `idedu` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nom` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `prenom` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mdp` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `privilege` enum('admin','educ','readonly') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'educ',
  `timeover` tinyint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idedu`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Utilisateurs de l''application' AUTO_INCREMENT=2 ;

--
-- Contenu de la table `ades_users`
--

INSERT INTO `ades_users` (`idedu`, `user`, `nom`, `prenom`, `email`, `mdp`, `privilege`, `timeover`) VALUES
(1, 'admin', 'admin', 'admin', 'admin@ades_edu.net', '21232f297a57a5a743894a0e4a801fc3', 'admin', 0);
