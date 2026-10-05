-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 25 août 2026 à 15:16
-- Version du serveur : 11.4.13-MariaDB
-- Version de PHP : 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Structure de la table `tria_alerteabsrtd`
--

CREATE TABLE IF NOT EXISTS `tria_alerteabsrtd` (
  `ideleve` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `nb` int(11) NOT NULL,
  `signaler` tinyint(4) NOT NULL,
  `matiereabs` int(11) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`type`,`nb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_api_access`
--

CREATE TABLE IF NOT EXISTS `tria_api_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(250) NOT NULL,
  `clef` varchar(250) NOT NULL,
  `ip` varchar(250) NOT NULL,
  `date_creation` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_askevalens`
--

CREATE TABLE IF NOT EXISTS `tria_askevalens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_authenticator`
--

CREATE TABLE IF NOT EXISTS `tria_authenticator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_mail` varchar(10) NOT NULL,
  `tuteur` int(11) NOT NULL DEFAULT 0,
  `idpers` int(11) NOT NULL,
  `membre` varchar(15) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `date` date NOT NULL,
  `code_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_avertissement`
--

CREATE TABLE IF NOT EXISTS `tria_avertissement` (
  `id_pers` int(11) NOT NULL,
  `type_pers` varchar(20) NOT NULL,
  `parametrage` varchar(30) NOT NULL,
  `valeur` varchar(250) NOT NULL,
  UNIQUE KEY `id_pers` (`id_pers`,`type_pers`,`parametrage`,`valeur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_b2iA2notation`
--

CREATE TABLE IF NOT EXISTS `tria_b2iA2notation` (
  `type_notation` varchar(10) NOT NULL,
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `note` varchar(10) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `type_notation` (`type_notation`,`ideleve`,`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_blacklist`
--

CREATE TABLE IF NOT EXISTS `tria_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `navigateur` text DEFAULT NULL,
  `date` date NOT NULL,
  `nb_tentative` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `cause` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_brevetcoef`
--

CREATE TABLE IF NOT EXISTS `tria_brevetcoef` (
  `matiere` varchar(20) NOT NULL,
  `coef` decimal(10,2) NOT NULL,
  `type_brevet` varchar(30) NOT NULL,
  UNIQUE KEY `matiere` (`matiere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_brevetcom`
--

CREATE TABLE IF NOT EXISTS `tria_brevetcom` (
  `ideleve` int(11) NOT NULL,
  `annee` varchar(5) NOT NULL,
  `codematiere` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`annee`,`codematiere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_brevetconfig`
--

CREATE TABLE IF NOT EXISTS `tria_brevetconfig` (
  `libelle` varchar(250) NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `coefbrevet` decimal(10,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_brevetnote`
--

CREATE TABLE IF NOT EXISTS `tria_brevetnote` (
  `ine` varchar(30) NOT NULL,
  `matiere` varchar(20) NOT NULL,
  `note` varchar(10) NOT NULL,
  `type_brevet` varchar(30) NOT NULL,
  `ideleve` int(11) NOT NULL,
  UNIQUE KEY `ine` (`ine`,`matiere`,`type_brevet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bug`
--

CREATE TABLE IF NOT EXISTS `tria_bug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `membre` varchar(30) DEFAULT NULL,
  `action` varchar(30) DEFAULT NULL,
  `service` varchar(30) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_archivage`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_archivage` (
  `ideleve` int(11) NOT NULL,
  `anneescolaire` varchar(30) NOT NULL,
  `trimestre` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `classe` varchar(50) NOT NULL,
  `fichier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_blanc_coef`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_blanc_coef` (
  `idclasse` int(11) NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `coef` decimal(30,2) NOT NULL,
  `type_bull` varchar(30) NOT NULL,
  `ordre` int(11) NOT NULL,
  KEY `idclasse` (`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_com_classe`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_com_classe` (
  `idclasse` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `trimestre` varchar(30) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `idclasse` (`idclasse`,`idmatiere`,`trimestre`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_cycle`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_cycle` (
  `ideleve` int(11) NOT NULL,
  `cycle` int(11) NOT NULL,
  `q1` varchar(20) DEFAULT NULL,
  `q2` varchar(20) DEFAULT NULL,
  `q3` varchar(20) DEFAULT NULL,
  `q4` varchar(20) DEFAULT NULL,
  `q5` varchar(20) DEFAULT NULL,
  `q6` varchar(20) DEFAULT NULL,
  `q7` varchar(20) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `idprofp` int(11) NOT NULL,
  `q4bis` varchar(20) DEFAULT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`cycle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_direction_com`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_direction_com` (
  `ideleve` int(11) NOT NULL,
  `trimestre` varchar(20) NOT NULL,
  `commentaire` text NOT NULL,
  `montessori` varchar(30) DEFAULT NULL,
  `type_bulletin` varchar(30) DEFAULT NULL,
  `seminaire` varchar(30) DEFAULT NULL,
  `leap_encouragement` tinyint(4) NOT NULL DEFAULT 0,
  `leap_felicitation` tinyint(4) NOT NULL DEFAULT 0,
  `leap_meg_comp` tinyint(4) NOT NULL DEFAULT 0,
  `leap_meg_trav` tinyint(4) NOT NULL DEFAULT 0,
  `jtc_promu` tinyint(1) NOT NULL DEFAULT 0,
  `jtc_reprendre` tinyint(1) NOT NULL DEFAULT 0,
  `jtc_orientation` tinyint(1) NOT NULL DEFAULT 0,
  `pp_av_trav` tinyint(4) NOT NULL DEFAULT 0,
  `pp_av_comp` tinyint(4) NOT NULL DEFAULT 0,
  `pp_enc` tinyint(4) NOT NULL DEFAULT 0,
  `pp_feli` tinyint(4) NOT NULL DEFAULT 0,
  `ppv2_av` tinyint(1) NOT NULL DEFAULT 0,
  `ppv2_faible` tinyint(1) NOT NULL DEFAULT 0,
  `ppv2_passable` tinyint(1) NOT NULL DEFAULT 0,
  `ppv2_enc` tinyint(1) NOT NULL DEFAULT 0,
  `ppv2_feli` tinyint(1) NOT NULL DEFAULT 0,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`trimestre`,`type_bulletin`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_ipac`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_ipac` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `bulletinprovisoire` varchar(3) NOT NULL,
  `admis` varchar(3) NOT NULL,
  UNIQUE KEY `index` (`ideleve`,`idclasse`,`annee_scolaire`,`bulletinprovisoire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_livret_classe`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_livret_classe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(90) NOT NULL,
  `thematique` varchar(90) NOT NULL,
  `idprof` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `type_rubrique` varchar(20) NOT NULL,
  `annee_scolaire` varchar(20) NOT NULL,
  `trim` varchar(50) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `ideleve` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_profp_com`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_profp_com` (
  `ideleve` int(11) NOT NULL,
  `trimestre` varchar(20) NOT NULL,
  `commentaire` text NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `type_com` varchar(20) NOT NULL DEFAULT 'default',
  UNIQUE KEY `ideleve` (`ideleve`,`trimestre`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_profp_ue`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_profp_ue` (
  `ideleve` int(11) NOT NULL,
  `id_ue` int(11) NOT NULL,
  `tri` varchar(50) NOT NULL,
  `com` text NOT NULL,
  `idclasse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_prof_com`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_prof_com` (
  `idmatiere` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `trimestre` varchar(30) NOT NULL,
  `com` text DEFAULT NULL,
  `ideleve` int(11) NOT NULL,
  `idprof` int(11) NOT NULL,
  `idgroupe` int(11) DEFAULT NULL,
  `typecom` int(11) NOT NULL DEFAULT 0,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `idmatiere` (`idmatiere`,`idclasse`,`trimestre`,`ideleve`,`idprof`,`idgroupe`,`typecom`,`annee_scolaire`),
  KEY `idclasse` (`idclasse`,`idprof`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_prof_param`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_prof_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idprof` int(11) NOT NULL,
  `com` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idprof` (`idprof`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_ptabs`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_ptabs` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `trim` int(20) NOT NULL,
  `annee_scolaire` varchar(20) NOT NULL,
  `point` decimal(11,2) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`idclasse`,`trim`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_scolaire_com`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_scolaire_com` (
  `ideleve` int(11) NOT NULL,
  `trimestre` varchar(20) NOT NULL,
  `commentaire` text NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`trimestre`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_visible`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_visible` (
  `idclasse` int(11) NOT NULL,
  `bulletin` varchar(90) NOT NULL,
  UNIQUE KEY `idclasse` (`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_bulletin_visu_parele`
--

CREATE TABLE IF NOT EXISTS `tria_bulletin_visu_parele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclasse` int(11) NOT NULL,
  `tri` varchar(20) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cahiertexte`
--

CREATE TABLE IF NOT EXISTS `tria_cahiertexte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_class_or_grp` int(11) NOT NULL,
  `matiere_id` int(11) NOT NULL,
  `date_saisie` date NOT NULL,
  `heure_saisie` time NOT NULL,
  `classorgrp` tinyint(1) NOT NULL,
  `number` varchar(50) DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `idprof` int(11) NOT NULL,
  `objectif` text NOT NULL,
  `contenu` text NOT NULL,
  `date_contenu` date NOT NULL,
  `number_obj` varchar(50) NOT NULL,
  `fichier_obj` varchar(250) NOT NULL,
  `blocnote` text NOT NULL,
  `visadirecteur` tinyint(4) NOT NULL DEFAULT 0,
  `liste_id_classe` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_class_or_grp` (`id_class_or_grp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_calendrier_dst`
--

CREATE TABLE IF NOT EXISTS `tria_calendrier_dst` (
  `id_dst` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `matiere` varchar(30) NOT NULL,
  `code_classe` varchar(30) DEFAULT NULL,
  `heure` time DEFAULT NULL,
  `duree` varchar(5) DEFAULT NULL,
  `idsalle` int(11) NOT NULL,
  PRIMARY KEY (`id_dst`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_calend_evenement`
--

CREATE TABLE IF NOT EXISTS `tria_calend_evenement` (
  `id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `evenement` text NOT NULL,
  PRIMARY KEY (`id_evenement`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cantine_compte`
--

CREATE TABLE IF NOT EXISTS `tria_cantine_compte` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `idpers` int(11) NOT NULL,
  `membre` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `prix` decimal(10,5) NOT NULL,
  `plateau` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cantine_menu`
--

CREATE TABLE IF NOT EXISTS `tria_cantine_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(250) NOT NULL,
  `prix` decimal(50,5) NOT NULL,
  `attribue` varchar(50) NOT NULL,
  `indice_salaire` int(11) NOT NULL DEFAULT 0,
  `platdefault` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_carnet_competence`
--

CREATE TABLE IF NOT EXISTS `tria_carnet_competence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcarnet` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `ordre` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`,`idcarnet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_carnet_descriptif`
--

CREATE TABLE IF NOT EXISTS `tria_carnet_descriptif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcarnet` int(11) NOT NULL,
  `idcompetence` int(11) NOT NULL,
  `libelle` text NOT NULL,
  `bold` tinyint(4) NOT NULL,
  `ordre` decimal(6,0) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idcarnet` (`idcarnet`,`idcompetence`,`libelle`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_carnet_evaluation`
--

CREATE TABLE IF NOT EXISTS `tria_carnet_evaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcarnet` int(11) NOT NULL,
  `idcompetence` int(11) NOT NULL,
  `iddescriptif` int(11) NOT NULL,
  `note` text NOT NULL,
  `periode` smallint(6) NOT NULL,
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `type_notation` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idcarnet` (`idcarnet`,`idcompetence`,`iddescriptif`,`periode`,`ideleve`,`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_carnet_section`
--

CREATE TABLE IF NOT EXISTS `tria_carnet_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(10) NOT NULL,
  `listeidclasse` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_carnet_suivi`
--

CREATE TABLE IF NOT EXISTS `tria_carnet_suivi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_carnet` varchar(40) NOT NULL,
  `code_lettre` tinyint(4) NOT NULL,
  `code_chiffre` tinyint(4) NOT NULL,
  `code_couleur` tinyint(4) NOT NULL,
  `code_note` tinyint(4) NOT NULL,
  `section` varchar(50) NOT NULL,
  `nb_periode` smallint(6) NOT NULL,
  `code_julesverne` tinyint(4) DEFAULT NULL,
  `code_commentaire` tinyint(4) NOT NULL DEFAULT 0,
  `code_educnational` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_carnet` (`nom_carnet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cdi_config_creneau`
--

CREATE TABLE IF NOT EXISTS `tria_cdi_config_creneau` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_centralstageaffiliation`
--

CREATE TABLE IF NOT EXISTS `tria_centralstageaffiliation` (
  `productid` varchar(250) NOT NULL,
  `etablissement` varchar(250) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `datedemande` date NOT NULL,
  `autorise` tinyint(4) NOT NULL DEFAULT 0,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`productid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_centralstageattribution`
--

CREATE TABLE IF NOT EXISTS `tria_centralstageattribution` (
  `id` int(11) NOT NULL,
  `idcentralstage` int(11) NOT NULL,
  `attribution` text NOT NULL,
  `productid` varchar(250) NOT NULL,
  `emailenvoye` tinyint(1) NOT NULL DEFAULT 0,
  `confirmer` tinyint(1) NOT NULL DEFAULT 0,
  `vialacentral` tinyint(1) NOT NULL DEFAULT 0,
  `flagcv` varchar(10) NOT NULL,
  `flagent` varchar(50) NOT NULL,
  `flagok` varchar(10) NOT NULL,
  `flagconv` varchar(10) NOT NULL,
  `flagrfs` varchar(10) NOT NULL,
  `flagcause` varchar(255) NOT NULL,
  `emailenvoyerpiecejointe` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `id` (`id`,`idcentralstage`,`productid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_centralstagedate`
--

CREATE TABLE IF NOT EXISTS `tria_centralstagedate` (
  `nomstage` varchar(100) NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_centralstagesouhait`
--

CREATE TABLE IF NOT EXISTS `tria_centralstagesouhait` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datedemande` date NOT NULL,
  `identreprise` int(11) NOT NULL,
  `sexe` varchar(250) NOT NULL,
  `service` varchar(250) NOT NULL,
  `observation` varchar(250) NOT NULL,
  `nbdemande` int(11) NOT NULL DEFAULT 1,
  `idperiode` int(11) NOT NULL,
  `salaire` varchar(250) NOT NULL,
  `logement` tinyint(4) NOT NULL DEFAULT 0,
  `typerepas` varchar(50) NOT NULL,
  `indemnitestage` tinyint(4) NOT NULL,
  `repas` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cha_batiment`
--

CREATE TABLE IF NOT EXISTS `tria_cha_batiment` (
  `batiment_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) NOT NULL,
  `adresse_1` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `code_postal` varchar(5) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`batiment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cha_chambre`
--

CREATE TABLE IF NOT EXISTS `tria_cha_chambre` (
  `chambre_id` int(11) NOT NULL AUTO_INCREMENT,
  `batiment_id` int(11) NOT NULL DEFAULT 0,
  `numero` varchar(5) DEFAULT NULL,
  `libelle` varchar(64) NOT NULL,
  `type_chambre_id` int(11) NOT NULL DEFAULT 1,
  `etage_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`chambre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cha_etage`
--

CREATE TABLE IF NOT EXISTS `tria_cha_etage` (
  `etage_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `exposant` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ordre` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`etage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_cha_etage`
--

INSERT INTO `tria_cha_etage` VALUES
(1, 'Rdc', '', 1),
(2, '1', 'er', 2),
(3, '2', 'ème', 3),
(4, '3', 'ème', 4),
(5, '4', 'ème', 5),
(6, '5', 'ème', 6),
(7, '6', 'ème', 7),
(8, '7', 'ème', 8),
(9, '8', 'ème', 9),
(10, '9', 'ème', 10),
(11, '10', 'ème', 11),
(12, '11', 'ème', 12),
(13, '12', 'ème', 13),
(14, '13', 'ème', 14),
(15, '14', 'ème', 15);

-- --------------------------------------------------------

--
-- Structure de la table `tria_cha_reservation`
--

CREATE TABLE IF NOT EXISTS `tria_cha_reservation` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `elev_id` int(11) NOT NULL DEFAULT 0,
  `chambre_id` int(11) NOT NULL DEFAULT 0,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `commentaire` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `date_reservation` datetime DEFAULT NULL,
  PRIMARY KEY (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cha_type_chambre`
--

CREATE TABLE IF NOT EXISTS `tria_cha_type_chambre` (
  `type_chambre_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ordre` tinyint(1) NOT NULL DEFAULT 0,
  `nombre_lits` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`type_chambre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_cha_type_chambre`
--

INSERT INTO `tria_cha_type_chambre` VALUES
(1, 'Simple', 1, 1),
(2, 'Double', 2, 2),
(3, 'Triple', 3, 3),
(4, 'Quadruple', 4, 4),
(5, 'Quintuple', 5, 5),
(6, 'Sextuple', 6, 6),
(7, 'Septuple', 7, 7),
(8, 'Octuple', 8, 8),
(9, 'Nonuple', 9, 9),
(10, 'Decuple', 10, 10);

-- --------------------------------------------------------

--
-- Structure de la table `tria_checksum`
--

CREATE TABLE IF NOT EXISTS `tria_checksum` (
  `sum` varchar(50) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `etat` int(11) NOT NULL,
  KEY `fichier` (`fichier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_circulaire`
--

CREATE TABLE IF NOT EXISTS `tria_circulaire` (
  `id_circulaire` int(11) NOT NULL AUTO_INCREMENT,
  `sujet` varchar(30) NOT NULL,
  `refence` varchar(30) NOT NULL,
  `file` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `enseignant` tinyint(1) DEFAULT NULL,
  `classe` text DEFAULT NULL,
  `idprofp` int(11) DEFAULT NULL,
  `comptepersonnel` tinyint(4) NOT NULL DEFAULT 0,
  `compteviescolaire` tinyint(4) NOT NULL DEFAULT 0,
  `comptedirection` tinyint(4) NOT NULL DEFAULT 1,
  `comptetuteurdestage` tinyint(4) NOT NULL DEFAULT 0,
  `categorie` varchar(200) NOT NULL,
  PRIMARY KEY (`id_circulaire`),
  KEY `id_circulaire` (`id_circulaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_classes`
--

CREATE TABLE IF NOT EXISTS `tria_classes` (
  `code_class` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  `desclong` varchar(250) DEFAULT NULL,
  `offline` tinyint(1) NOT NULL,
  `idsite` int(11) NOT NULL DEFAULT 1,
  `langueclasse` varchar(20) NOT NULL,
  `niveau` varchar(10) NOT NULL,
  `specification` varchar(200) NOT NULL,
  `code_mef` varchar(11) DEFAULT NULL,
  `noconnexion` tinyint(4) NOT NULL,
  PRIMARY KEY (`code_class`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `code_class` (`code_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_codebar`
--

CREATE TABLE IF NOT EXISTS `tria_codebar` (
  `id` varchar(100) NOT NULL,
  `id_pers` int(11) NOT NULL,
  `membre` varchar(30) NOT NULL,
  `valide` tinyint(4) NOT NULL DEFAULT 1,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_code_postal`
--

CREATE TABLE IF NOT EXISTS `tria_code_postal` (
  `cod` varchar(10) NOT NULL,
  `ville` varchar(30) NOT NULL,
  KEY `cod` (`cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_code_postal`
--

INSERT INTO `tria_code_postal` VALUES
('01380', 'BAGE LA VILLE'),
('01340', 'ATTIGNAT'),
('01570', 'ASNIERES SUR SAONE'),
('01510', 'ARTEMARE'),
('01480', 'ARS SUR FORMANS'),
('01510', 'ARMIX'),
('01970', 'ARGIS'),
('01190', 'ARBIGNY'),
('01300', 'ARBIGNIEU'),
('01100', 'ARBENT'),
('01230', 'ARANDAS'),
('01110', 'ARANC'),
('01100', 'APREMONT'),
('01350', 'ANGLEFORT'),
('01300', 'ANDERT ET CONDON'),
('01780', 'AMBUTRIX'),
('01500', 'AMBRONAY'),
('01300', 'AMBLEON'),
('01330', 'AMBERIEUX EN DOMBES'),
('01500', 'AMBERIEU EN BUGEY'),
('01090', 'AMAREINS'),
('01640', 'L ABERGEMENT DE VAREY'),
('01400', 'L ABERGEMENT CLEMENCIAT'),
('01200', 'BELLEGARDE SUR VALSERINE'),
('01300', 'BELLEY'),
('01130', 'BELLEYDOUX'),
('01260', 'BELMONT LUTHEZIEU'),
('01470', 'BENONCES'),
('01370', 'BENY'),
('01350', 'BEON'),
('01340', 'BEREZIAT'),
('01500', 'BETTANT'),
('01290', 'BEY'),
('01700', 'BEYNOST'),
('01200', 'BILLIAT'),
('01330', 'BIRIEUX'),
('01290', 'BIZIAT'),
('01150', 'BLYES'),
('01250', 'BOHAS'),
('01120', 'LA BOISSE'),
('01190', 'BOISSEY'),
('01380', 'BAGE LE CHATEL'),
('01360', 'BALAN'),
('01990', 'BANEINS'),
('01270', 'BEAUPONT'),
('01480', 'BEAUREGARD'),
('01810', 'BELLIGNAT'),
('01360', 'BELIGNEUX'),
('01200', 'BELLEGARDE SUR VALSERINE'),
('01300', 'BELLEY'),
('01130', 'BELLEYDOUX'),
('01260', 'BELMONT LUTHEZIEU'),
('01470', 'BENONCES'),
('01370', 'BENY'),
('01350', 'BEON'),
('01340', 'BEREZIAT'),
('01500', 'BETTANT'),
('01290', 'BEY'),
('01700', 'BEYNOST'),
('01200', 'BILLIAT'),
('01330', 'BIRIEUX'),
('01290', 'BIZIAT'),
('01150', 'BLYES'),
('01250', 'BOHAS'),
('01120', 'LA BOISSE'),
('01190', 'BOISSEY'),
('01380', 'BOISSEY'),
('01450', 'BOLOZON'),
('01330', 'BOULIGNEUX'),
('01000', 'BOURG EN BRESSE'),
('01800', 'BOURG ST CHRISTOPHE'),
('01100', 'BOUVENT'),
('01640', 'BOYEUX ST JEROME'),
('01190', 'BOZ'),
('01300', 'BREGNIER CORDON'),
('01260', 'BRENAZ'),
('01110', 'BRENOD'),
('01300', 'BRENS'),
('01360', 'BRESSOLLES'),
('01460', 'BRION'),
('01470', 'BRIORD'),
('01310', 'BUELLAS'),
('01510', 'LA BURBANCHE'),
('01430', 'CEIGNES'),
('01450', 'CERDON'),
('01240', 'CERTINES'),
('01090', 'CESSEINS'),
('01170', 'CESSY'),
('01250', 'CEYZERIAT'),
('01350', 'CEYZERIEU'),
('01320', 'CHALAMONT'),
('01480', 'CHALEINS'),
('01970', 'CHALEY'),
('01450', 'CHALLES'),
('01630', 'CHALLEX'),
('01260', 'CHAMPAGNE EN VALROMEY'),
('01110', 'CHAMPDOR'),
('01410', 'CHAMPFROMIER'),
('01420', 'CHANAY'),
('01990', 'CHANEINS'),
('01400', 'CHANOZ CHATENAY'),
('01240', 'LA CHAPELLE DU CHATELARD'),
('01260', 'CHARANCIN'),
('01130', 'CHARIX'),
('01800', 'CHARNOZ SUR AIN'),
('01500', 'CHATEAU GAILLARD'),
('01320', 'CHATENAY'),
('01200', 'CHATILLON EN MICHAILLE'),
('01320', 'CHATILLON LA PALUD'),
('01400', 'CHATILLON SUR CHALARONNE'),
('01190', 'CHAVANNES SUR REYSSOUZE'),
('01250', 'CHAVANNES SUR SURAN'),
('01660', 'CHAVEYRIAT'),
('01510', 'CHAVORNAY'),
('01300', 'CHAZEY BONS'),
('01150', 'CHAZEY SUR AIN'),
('01510', 'CHEIGNIEU LA BALME'),
('01430', 'CHEVILLARD'),
('01190', 'CHEVROUX'),
('01170', 'CHEVRY'),
('01410', 'CHEZERY FORENS'),
('01200', 'CHEZERY FORENS'),
('01390', 'CIVRIEUX'),
('01250', 'CIZE'),
('01230', 'CLEYZIEU'),
('01270', 'COLIGNY'),
('01550', 'COLLONGES'),
('01300', 'COLOMIEU'),
('01230', 'CONAND'),
('01430', 'CONDAMINE'),
('01400', 'CONDEISSIAT'),
('01200', 'CONFORT'),
('01310', 'CONFRANCON'),
('01300', 'CONTREVOZ'),
('01300', 'CONZIEU'),
('01420', 'CORBONOD'),
('01110', 'CORCELLES'),
('01120', 'CORDIEUX'),
('01110', 'CORLIER'),
('01110', 'CORMARANCHE EN BUGEY'),
('01290', 'CORMORANCHE SUR SAONE'),
('01270', 'CORMOZ'),
('01250', 'CORVEISSIAT'),
('01370', 'COURMANGOUX'),
('01560', 'COURTES'),
('01320', 'CRANS'),
('01340', 'CRAS SUR REYSSOUZE'),
('01200', 'CRAZ'),
('01350', 'CRESSIN ROCHEFORT'),
('01290', 'CROTTET'),
('01750', 'CROTTET'),
('01170', 'CROZET'),
('01290', 'CRUZILLES LES MEPILLAT'),
('01370', 'CUISIAT'),
('01350', 'CULOZ'),
('01560', 'CURCIAT DONGALON'),
('01310', 'CURTAFOND'),
('01300', 'CUZIEU'),
('01120', 'DAGNEUX'),
('01220', 'DIVONNE LES BAINS'),
('01380', 'DOMMARTIN'),
('01240', 'DOMPIERRE SUR VEYLE'),
('01400', 'DOMPIERRE SUR CHALARONNE'),
('01270', 'DOMSURE'),
('01590', 'DORTAN'),
('01500', 'DOUVRES'),
('01250', 'DROM'),
('01160', 'DRUILLAT'),
('01130', 'ECHALLON'),
('01170', 'ECHENEVEX'),
('01340', 'ETREZ'),
('01230', 'EVOSGES'),
('01800', 'FARAMANS'),
('01480', 'FAREINS'),
('01550', 'FARGES'),
('01570', 'FEILLENS'),
('01210', 'FERNEY VOLTAIRE'),
('01260', 'FITIGNIEU'),
('01350', 'FLAXIEU'),
('01340', 'FOISSIAT'),
('01090', 'AMAREINS FRANCHELEINS CESSEINS'),
('01480', 'FRANS'),
('01140', 'GARNERANS'),
('01090', 'GENOUILLEUX'),
('01460', 'GEOVREISSIAT'),
('01100', 'GEOVREISSET'),
('01250', 'GERMAGNAT'),
('01170', 'GEX'),
('01130', 'GIRON'),
('01190', 'GORREVOD'),
('01260', 'LE GRAND ABERGEMENT'),
('01250', 'GRAND CORENT'),
('01580', 'GRANGES'),
('01290', 'GRIEGES'),
('01220', 'GRILLY'),
('01810', 'GROISSIAT'),
('01680', 'GROSLEE'),
('01090', 'GUEREINS'),
('01250', 'HAUTECOURT ROMANECHE'),
('01110', 'HAUTEVILLE LOMPNES'),
('01110', 'HOSTIAS'),
('01260', 'HOTONNES'),
('01140', 'ILLIAT'),
('01200', 'INJOUX GENISSIAT'),
('01680', 'INNIMOND'),
('01430', 'IZENAVE'),
('01580', 'IZERNORE'),
('01300', 'IZIEU'),
('01480', 'JASSANS RIOTTIER'),
('01250', 'JASSERON'),
('01340', 'JAYAT'),
('01250', 'JOURNANS'),
('01800', 'JOYEUX'),
('01640', 'JUJURIEUX'),
('01450', 'LABALME'),
('01150', 'LAGNIEU'),
('01290', 'LAIZ'),
('01130', 'LALLEYRIAT'),
('01200', 'LANCRANS'),
('01430', 'LANTENAY'),
('01330', 'LAPEYROUSE'),
('01350', 'LAVOURS'),
('01200', 'LEAZ'),
('01410', 'LELEX'),
('01240', 'LENT'),
('01560', 'LESCHEROUX'),
('01150', 'LEYMENT'),
('01450', 'LEYSSARD'),
('01420', 'LHOPITAL'),
('01680', 'LHUIS'),
('01260', 'LILIGNOD'),
('01260', 'LOCHIEU'),
('01680', 'LOMPNAS'),
('01260', 'LOMPNIEU'),
('01800', 'LOYES'),
('01360', 'LOYETTES'),
('01090', 'LURCY'),
('01260', 'LUTHEZIEU'),
('01300', 'MAGNIEU'),
('01430', 'MAILLAT'),
('01340', 'MALAFRETAZ'),
('01560', 'MANTENAY MONTLIN'),
('01570', 'MANZIAT'),
('01851', 'MARBOZ'),
('01680', 'MARCHAMP'),
('01300', 'MARIGNIEU'),
('01240', 'MARLIEUX'),
('01340', 'MARSONNAS'),
('01810', 'MARTIGNAT'),
('01600', 'MASSIEUX'),
('01300', 'MASSIGNIEU DE RIVES'),
('01580', 'MATAFELON GRANGES'),
('01370', 'MEILLONNAS'),
('01450', 'MERIGNAT'),
('01480', 'MESSIMY SUR SAONE'),
('01800', 'MEXIMIEUX'),
('01250', 'BOHAS MEYRIAT RIGNAT'),
('01660', 'MEZERIAT'),
('01410', 'MIJOUX'),
('01170', 'MIJOUX'),
('01390', 'MIONNAY'),
('01700', 'MIRIBEL'),
('01600', 'MISERIEUX'),
('01280', 'MOENS'),
('01140', 'MOGNENEINS'),
('01800', 'MOLLON'),
('01250', 'MONTAGNAT'),
('01470', 'MONTAGNIEU'),
('01200', 'MONTANGES'),
('01090', 'MONTCEAUX'),
('01310', 'MONTCET'),
('01800', 'LE MONTELLIER'),
('01390', 'MONTHIEUX'),
('01120', 'MONTLUEL'),
('01090', 'MONTMERLE SUR SAONE'),
('01310', 'MONTRACOL'),
('01460', 'MONTREAL LA CLUSE'),
('01340', 'MONTREVEL EN BRESSE'),
('01460', 'NURIEUX VOLOGNAT'),
('01300', 'MURS ET GELIGNIEUX'),
('01130', 'NANTUA'),
('01460', 'NANTUA'),
('01580', 'NAPT'),
('01300', 'NATTAGES'),
('01400', 'NEUVILLE LES DAMES'),
('01160', 'NEUVILLE SUR AIN'),
('01130', 'LES NEYROLLES'),
('01700', 'NEYRON'),
('01120', 'NIEVROZ'),
('01230', 'NIVOLLET MONTGRIFFON'),
('01200', 'OCHIAZ'),
('01230', 'ONCIEU'),
('01510', 'ORDONNAZ'),
('01210', 'ORNEX'),
('01430', 'OUTRIAZ'),
('01100', 'OYONNAX'),
('01190', 'OZAN'),
('01600', 'PARCIEUX'),
('01300', 'PARVES'),
('01260', 'PASSIN'),
('01630', 'PERON'),
('01960', 'PERONNAS'),
('01800', 'PEROUGES'),
('01540', 'PERREX'),
('01260', 'LE PETIT ABERGEMENT'),
('01430', 'PEYRIAT'),
('01300', 'PEYRIEU'),
('01140', 'PEYZIEUX SUR SAONE'),
('01270', 'PIRAJOUX'),
('01120', 'PIZAY'),
('01130', 'PLAGNE'),
('01330', 'LE PLANTAY'),
('01130', 'LE POIZAT'),
('01310', 'POLLIAT'),
('01350', 'POLLIEU'),
('01450', 'PONCIN'),
('01160', 'PONT D AIN'),
('01190', 'PONT DE VAUX'),
('01290', 'PONT DE VEYLE'),
('01460', 'PORT'),
('01550', 'POUGNY'),
('01250', 'POUILLAT'),
('01300', 'PREMEYZEL'),
('01110', 'PREMILLIEU'),
('01510', 'PREMILLIEU'),
('01370', 'PRESSIAT'),
('01280', 'PREVESSIN MOENS'),
('01160', 'PRIAY'),
('01510', 'PUGIEU'),
('01250', 'RAMASSE'),
('01390', 'RANCE'),
('01990', 'RELEVANT'),
('01750', 'REPLONGES'),
('01250', 'REVONNAS'),
('01600', 'REYRIEUX'),
('01190', 'REYSSOUZE'),
('01250', 'RIGNAT'),
('01800', 'RIGNIEUX LE FRANC'),
('01250', 'ROMANECHE'),
('01400', 'ROMANS'),
('01510', 'ROSSILLON'),
('01260', 'RUFFIEU'),
('01450', 'ST ALBAN'),
('01380', 'ST ANDRE DE BAGE'),
('01390', 'ST ANDRE DE CORCY'),
('01290', 'ST ANDRE D HUIRIAT'),
('01240', 'ST ANDRE LE BOUCHOUX'),
('01960', 'ST ANDRE SUR VIEUX JONC'),
('01190', 'ST BENIGNE'),
('01300', 'ST BENOIT'),
('01600', 'ST BERNARD'),
('01300', 'ST BOIS'),
('01300', 'ST CHAMP'),
('01120', 'STE CROIX'),
('01380', 'ST CYR SUR MENTHON'),
('01000', 'ST DENIS LES BOURG'),
('01780', 'ST DENIS EN BUGEY'),
('01340', 'ST DIDIER D AUSSIAT'),
('01600', 'ST DIDIER DE FORMANS'),
('01140', 'ST DIDIER SUR CHALARONNE'),
('01800', 'ST ELOI'),
('01370', 'ST ETIENNE DU BOIS'),
('01140', 'ST ETIENNE SUR CHALARONNE'),
('01190', 'ST ETIENNE SUR REYSSOUZE'),
('01600', 'STE EUPHEMIE'),
('01630', 'ST GENIS POUILLY'),
('01380', 'ST GENIS SUR MENTHON'),
('01400', 'ST GEORGES SUR RENON'),
('01130', 'ST GERMAIN DE JOUX'),
('01300', 'ST GERMAIN LES PAROISSES'),
('01240', 'ST GERMAIN SUR RENON'),
('01630', 'ST JEAN DE GONVILLE'),
('01800', 'ST JEAN DE NIOST'),
('01390', 'ST JEAN DE THURIGNEUX'),
('01640', 'ST JEAN LE VIEUX'),
('01560', 'ST JEAN SUR REYSSOUZE'),
('01290', 'ST JEAN SUR VEYLE'),
('01150', 'STE JULIE'),
('01560', 'ST JULIEN SUR REYSSOUZE'),
('01540', 'ST JULIEN SUR VEYLE'),
('01250', 'ST JUST'),
('01750', 'ST LAURENT SUR SAONE'),
('01390', 'ST MARCEL'),
('01510', 'ST MARTIN DE BAVEL'),
('01430', 'ST MARTIN DU FRENE'),
('01160', 'ST MARTIN DU MONT'),
('01310', 'ST MARTIN LE CHATEL'),
('01700', 'ST MAURICE DE BEYNOST'),
('01800', 'ST MAURICE DE GOURDANS'),
('01500', 'ST MAURICE DE REMENS'),
('01560', 'ST NIZIER LE BOUCHOUX'),
('01320', 'ST NIZIER LE DESERT'),
('01330', 'STE OLIVE'),
('01240', 'ST PAUL DE VARAX'),
('01230', 'ST RAMBERT EN BUGEY'),
('01310', 'ST REMY'),
('01150', 'ST SORLIN EN BUGEY'),
('01340', 'ST SULPICE'),
('01560', 'ST TRIVIER DE COURTES'),
('01990', 'ST TRIVIER SUR MOIGNANS'),
('01150', 'ST VULBAS'),
('01270', 'SALAVRE'),
('01580', 'SAMOGNAT'),
('01400', 'SANDRANS'),
('01790', 'SAULT BRENAZ'),
('01220', 'SAUVERNY'),
('01480', 'SAVIGNEUX'),
('01170', 'SEGNY'),
('01470', 'SEILLONNAZ'),
('01630', 'SERGY'),
('01190', 'SERMOYER'),
('01470', 'SERRIERES DE BRIORD'),
('01450', 'SERRIERES SUR AIN'),
('01960', 'SERVAS'),
('01560', 'SERVIGNAT'),
('01420', 'SEYSSEL'),
('01250', 'SIMANDRE'),
('01260', 'SONGIEU'),
('01580', 'SONTHONNAX LA MONTAGNE'),
('01150', 'SOUCLIN'),
('01400', 'SULIGNAT'),
('01420', 'SURJOUX'),
('01260', 'SUTRIEU'),
('01510', 'TALISSIEU'),
('01970', 'TENAY'),
('01110', 'THEZILLIEU'),
('01120', 'THIL'),
('01710', 'THOIRY'),
('01140', 'THOISSEY'),
('01230', 'TORCIEU'),
('01250', 'TOSSIAT'),
('01600', 'TOUSSIEUX'),
('01390', 'TRAMOYES'),
('01160', 'LA TRANCLIERE'),
('01370', 'TREFFORT CUISIAT'),
('01600', 'TREVOUX'),
('01140', 'VALEINS'),
('01660', 'VANDEINS'),
('01160', 'VARAMBON'),
('01150', 'VAUX EN BUGEY'),
('01270', 'VERJON'),
('01560', 'VERNOUX'),
('01330', 'VERSAILLEUX'),
('01210', 'VERSONNEX'),
('01170', 'VESANCY'),
('01560', 'VESCOURS'),
('01570', 'VESINES'),
('01100', 'VEYZIAT'),
('01430', 'VIEU D IZENAVE'),
('01260', 'VIEU'),
('01330', 'VILLARS LES DOMBES'),
('01790', 'VILLEBOIS'),
('01270', 'VILLEMOTIER'),
('01480', 'VILLENEUVE'),
('01250', 'VILLEREVERSURE'),
('01200', 'VILLES'),
('01320', 'VILLETTE SUR AIN'),
('01800', 'VILLIEU LOYES MOLLON'),
('01440', 'VIRIAT'),
('01510', 'VIRIEU LE GRAND'),
('01260', 'VIRIEU LE PETIT'),
('01300', 'VIRIGNIN'),
('01460', 'VOLOGNAT'),
('01350', 'VONGNES'),
('01540', 'VONNAS'),
('01200', 'VOUVRAY'),
('01360', 'CAMP DE LA VALBONNE'),
('01390', 'LE VIEUX MARSEILLE'),
('01310', 'CORGENON'),
('01120', 'JAILLEUX'),
('01120', 'LA LECHERE'),
('01120', 'RAPAN'),
('01360', 'LA VALBONNE'),
('01120', 'CHANES'),
('01800', 'MARFOZ'),
('01700', 'LE MAS RILLIER');

-- --------------------------------------------------------

--
-- Structure de la table `tria_comptaconfig`
--

CREATE TABLE IF NOT EXISTS `tria_comptaconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclasse` int(11) NOT NULL,
  `libellevers` varchar(30) NOT NULL,
  `montantvers` decimal(50,5) NOT NULL,
  `datevers` date NOT NULL,
  `ideleve` int(11) NOT NULL DEFAULT 0,
  `modedepaiement` varchar(100) NOT NULL,
  `anneescolaire` varchar(20) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_comptaconfigmodele`
--

CREATE TABLE IF NOT EXISTS `tria_comptaconfigmodele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nommodele` varchar(15) NOT NULL,
  `libellevers` varchar(30) NOT NULL,
  `montantvers` decimal(50,2) NOT NULL,
  `datevers` date NOT NULL,
  `refmodele` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_comptaexclu`
--

CREATE TABLE IF NOT EXISTS `tria_comptaexclu` (
  `ideleve` int(11) NOT NULL,
  `idcomptaclasse` int(11) NOT NULL,
  KEY `ideleve` (`ideleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_comptaversement`
--

CREATE TABLE IF NOT EXISTS `tria_comptaversement` (
  `ideleve` int(11) NOT NULL,
  `idversement` int(11) NOT NULL,
  `montantvers` decimal(50,2) NOT NULL,
  `datevers` date NOT NULL,
  `modepaiement` text DEFAULT NULL,
  `anneescolaire` varchar(20) NOT NULL,
  `num_cheque` varchar(250) DEFAULT NULL,
  `etablissement_bancaire` varchar(250) DEFAULT NULL,
  `libellevershistory` varchar(30) NOT NULL,
  UNIQUE KEY `ideleve_2` (`ideleve`,`idversement`,`datevers`),
  KEY `ideleve` (`ideleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_config_creneau`
--

CREATE TABLE IF NOT EXISTS `tria_config_creneau` (
  `libelle` varchar(30) NOT NULL,
  `dep_h` time DEFAULT NULL,
  `fin_h` time DEFAULT NULL,
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_config_examen`
--

CREATE TABLE IF NOT EXISTS `tria_config_examen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  `coef` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_config_note_usa`
--

CREATE TABLE IF NOT EXISTS `tria_config_note_usa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(15) NOT NULL,
  `min` decimal(10,2) NOT NULL,
  `max` decimal(10,2) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `min` (`min`,`max`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_config_rtd_abs`
--

CREATE TABLE IF NOT EXISTS `tria_config_rtd_abs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_cumul_abstrd`
--

CREATE TABLE IF NOT EXISTS `tria_cumul_abstrd` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `nbheureabs` int(11) NOT NULL,
  `nbdemijourabs` int(11) NOT NULL,
  `nbretards` int(11) NOT NULL,
  `anneescolaire` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_date_trimestrielle`
--

CREATE TABLE IF NOT EXISTS `tria_date_trimestrielle` (
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `trim_choix` varchar(20) DEFAULT NULL,
  `idclasse` int(11) DEFAULT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  KEY `trim_choix` (`trim_choix`,`date_debut`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_delegue`
--

CREATE TABLE IF NOT EXISTS `tria_delegue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclasse` int(11) NOT NULL,
  `nomparent1` int(11) DEFAULT NULL,
  `nomparent2` int(11) DEFAULT NULL,
  `eleve1` int(11) DEFAULT NULL,
  `eleve2` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idclasse` (`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_demande_dst`
--

CREATE TABLE IF NOT EXISTS `tria_demande_dst` (
  `id_dem` int(11) NOT NULL AUTO_INCREMENT,
  `id_pers` int(11) NOT NULL,
  `date_dem` date NOT NULL,
  `classe` varchar(30) NOT NULL,
  `mat_text` varchar(30) NOT NULL,
  `heure` time DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_dem`),
  KEY `date_dem` (`date_dem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_devoir_scolaire`
--

CREATE TABLE IF NOT EXISTS `tria_devoir_scolaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_class_or_grp` int(11) NOT NULL,
  `matiere_id` int(11) NOT NULL,
  `date_saisie` date NOT NULL,
  `heure_saisie` time NOT NULL,
  `date_devoir` date NOT NULL,
  `texte` text NOT NULL,
  `classorgrp` tinyint(1) NOT NULL,
  `number` varchar(50) DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `idprof` int(11) NOT NULL,
  `tempsestimedevoir` time NOT NULL,
  `visadirecteur` tinyint(4) NOT NULL DEFAULT 0,
  `liste_id_classe` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_class_or_grp` (`id_class_or_grp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_diaporama`
--

CREATE TABLE IF NOT EXISTS `tria_diaporama` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentaire` text DEFAULT NULL,
  `photo` text NOT NULL,
  `enseignant` tinyint(1) DEFAULT NULL,
  `classe` text DEFAULT NULL,
  `reference` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_discipline_prof`
--

CREATE TABLE IF NOT EXISTS `tria_discipline_prof` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `devoir_a_faire` text DEFAULT NULL,
  `devoir_pour_le` date DEFAULT NULL,
  `demande_retenu` tinyint(1) DEFAULT NULL,
  `retenu_enrg` tinyint(1) DEFAULT NULL,
  `info_plus` text DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `idprof` varchar(70) NOT NULL,
  `classe` text NOT NULL,
  `description_fait` text DEFAULT NULL,
  `idsanction` int(11) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_discipline_retenue`
--

CREATE TABLE IF NOT EXISTS `tria_discipline_retenue` (
  `id_elev` int(11) NOT NULL,
  `date_de_la_retenue` date NOT NULL,
  `heure_de_la_retenue` time NOT NULL,
  `date_de_saisie` date NOT NULL,
  `origi_saisie` varchar(30) NOT NULL,
  `id_category` int(11) NOT NULL,
  `retenue_effectuer` tinyint(1) NOT NULL,
  `motif` text DEFAULT NULL,
  `attribuer_par` varchar(30) DEFAULT NULL,
  `signature_parent` tinyint(1) DEFAULT NULL,
  `duree_retenu` time DEFAULT NULL,
  `devoir_a_faire` text DEFAULT NULL,
  `description_fait` text DEFAULT NULL,
  `courrier_env` tinyint(4) NOT NULL DEFAULT 0,
  `repport_du` date NOT NULL,
  PRIMARY KEY (`date_de_la_retenue`,`heure_de_la_retenue`,`id_elev`),
  KEY `id_elev` (`id_elev`,`date_de_la_retenue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_discipline_sanction`
--

CREATE TABLE IF NOT EXISTS `tria_discipline_sanction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `motif` text NOT NULL,
  `id_category` int(11) NOT NULL,
  `date_saisie` date NOT NULL,
  `origin_saisie` varchar(30) NOT NULL,
  `enr_en_retenue` tinyint(1) DEFAULT NULL,
  `signature_parent` tinyint(1) DEFAULT NULL,
  `attribuer_par` varchar(30) DEFAULT NULL,
  `devoir_a_faire` text DEFAULT NULL,
  `description_fait` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_eleve` (`id_eleve`,`enr_en_retenue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_dispenses`
--

CREATE TABLE IF NOT EXISTS `tria_dispenses` (
  `elev_id` int(11) NOT NULL,
  `code_mat` varchar(5) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `date_saisie` date NOT NULL,
  `certificat` tinyint(1) DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `heure1` varchar(5) DEFAULT NULL,
  `jour1` varchar(8) DEFAULT NULL,
  `heure2` varchar(5) DEFAULT NULL,
  `jour2` varchar(8) DEFAULT NULL,
  `heure3` varchar(5) DEFAULT NULL,
  `jour3` varchar(8) DEFAULT NULL,
  `origin_saisie` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`code_mat`,`date_debut`,`elev_id`),
  KEY `elev_id` (`elev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_droitmodule`
--

CREATE TABLE IF NOT EXISTS `tria_droitmodule` (
  `idpers` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `permission` tinyint(4) NOT NULL,
  `idpersperm` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_edt_enseignement`
--

CREATE TABLE IF NOT EXISTS `tria_edt_enseignement` (
  `code` varchar(30) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `code_matiere` varchar(30) NOT NULL,
  `duree_total` time NOT NULL,
  `duree_chaque_seance` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_edt_seances`
--

CREATE TABLE IF NOT EXISTS `tria_edt_seances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(90) NOT NULL,
  `enseignement` text NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `duree` time NOT NULL,
  `bgcolor` varchar(20) NOT NULL,
  `idclasse` int(11) DEFAULT NULL,
  `idprof` int(11) DEFAULT NULL,
  `prestation` int(11) NOT NULL,
  `idmatiere` int(11) DEFAULT NULL,
  `coursannule` varchar(250) DEFAULT NULL,
  `docdst` tinyint(4) NOT NULL DEFAULT 0,
  `reportle` date NOT NULL,
  `reporta` varchar(5) NOT NULL DEFAULT 'hh:mm',
  `emargement` tinyint(4) NOT NULL DEFAULT 1,
  `emargementeval` tinyint(4) NOT NULL DEFAULT 0,
  `emargementpedago` tinyint(4) NOT NULL DEFAULT 0,
  `idressource` int(11) DEFAULT NULL,
  `idgroupe` int(11) NOT NULL DEFAULT 0,
  `id_resa_liste` int(11) DEFAULT NULL,
  `affichehoraire` tinyint(4) NOT NULL DEFAULT 1,
  `autoriseChevauchement` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_eleves`
--

CREATE TABLE IF NOT EXISTS `tria_eleves` (
  `elev_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `classe` int(11) NOT NULL,
  `lv1` varchar(90) DEFAULT NULL,
  `lv2` varchar(90) DEFAULT NULL,
  `option` varchar(90) DEFAULT NULL,
  `regime` varchar(25) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(40) DEFAULT NULL,
  `nationalite` varchar(20) DEFAULT NULL,
  `passwd` varchar(100) DEFAULT NULL,
  `passwd_eleve` varchar(100) DEFAULT NULL,
  `civ_1` smallint(6) DEFAULT NULL,
  `nomtuteur` varchar(30) DEFAULT NULL,
  `prenomtuteur` varchar(30) DEFAULT NULL,
  `adr1` varchar(100) DEFAULT NULL,
  `code_post_adr1` varchar(15) DEFAULT NULL,
  `commune_adr1` varchar(40) DEFAULT NULL,
  `tel_port_1` varchar(25) DEFAULT NULL,
  `civ_2` smallint(6) DEFAULT NULL,
  `nom_resp_2` varchar(50) DEFAULT NULL,
  `prenom_resp_2` varchar(50) DEFAULT NULL,
  `adr2` varchar(100) DEFAULT NULL,
  `code_post_adr2` varchar(15) DEFAULT NULL,
  `commune_adr2` varchar(40) DEFAULT NULL,
  `tel_port_2` varchar(25) DEFAULT NULL,
  `telephone` varchar(18) DEFAULT NULL,
  `profession_pere` varchar(30) DEFAULT NULL,
  `tel_prof_pere` varchar(18) DEFAULT NULL,
  `profession_mere` varchar(30) DEFAULT NULL,
  `tel_prof_mere` varchar(18) DEFAULT NULL,
  `nom_etablissement` varchar(30) DEFAULT NULL,
  `numero_etablissement` varchar(30) DEFAULT NULL,
  `code_postal_etablissement` varchar(6) DEFAULT NULL,
  `commune_etablissement` varchar(30) DEFAULT NULL,
  `numero_eleve` varchar(30) DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_eleve` varchar(150) DEFAULT NULL,
  `email_resp_2` varchar(150) DEFAULT NULL,
  `class_ant` varchar(30) DEFAULT NULL,
  `annee_ant` varchar(20) DEFAULT NULL,
  `numero_gep` int(11) DEFAULT NULL,
  `valid_forward_mail_eleve` tinyint(4) DEFAULT NULL,
  `valid_forward_mail_parent` tinyint(4) DEFAULT NULL,
  `tel_eleve` varchar(25) DEFAULT NULL,
  `code_compta` varchar(30) DEFAULT NULL,
  `sexe` varchar(1) DEFAULT NULL,
  `passwd_parent_2` varchar(100) DEFAULT NULL,
  `annee_scolaire` varchar(15) NOT NULL DEFAULT ' ',
  `information` mediumtext NOT NULL,
  `adr_eleve` varchar(100) DEFAULT NULL,
  `ccp_eleve` varchar(15) DEFAULT NULL,
  `commune_eleve` varchar(40) DEFAULT NULL,
  `tel_fixe_eleve` varchar(25) DEFAULT NULL,
  `pays_eleve` varchar(50) DEFAULT NULL,
  `compte_inactif` tinyint(4) NOT NULL DEFAULT 0,
  `boursier` tinyint(4) NOT NULL DEFAULT 0,
  `montant_bourse` decimal(50,5) NOT NULL,
  `indemnite_stage` decimal(50,5) NOT NULL,
  `nbmoisindemnite` int(11) NOT NULL DEFAULT 0,
  `mdp_moodle` varchar(50) NOT NULL DEFAULT ' ',
  `emailpro_eleve` varchar(250) NOT NULL,
  `rangement` varchar(250) NOT NULL,
  `cdi` tinyint(1) NOT NULL,
  `bde` tinyint(1) NOT NULL,
  `mailing_el` tinyint(1) NOT NULL DEFAULT 0,
  `mailing_tu1` tinyint(1) NOT NULL DEFAULT 0,
  `mailing_tu2` tinyint(1) NOT NULL DEFAULT 0,
  `situation_familiale` varchar(40) NOT NULL,
  `probatoire` tinyint(1) NOT NULL DEFAULT 0,
  `serie_bac` varchar(50) NOT NULL,
  `annee_bac` varchar(4) NOT NULL,
  `departement_bac` varchar(20) NOT NULL,
  `departementnais` varchar(20) NOT NULL,
  `numero_eleve_vatel` varchar(50) NOT NULL,
  `keytriadeeleve` varchar(150) NOT NULL,
  `keytriadetuteur1` varchar(150) NOT NULL,
  `keytriadetuteur2` varchar(150) NOT NULL,
  `googleAuthenEleve` tinyint(4) NOT NULL DEFAULT 0,
  `googleAuthenTuteur1` tinyint(4) NOT NULL DEFAULT 0,
  `googleAuthenTuteur2` tinyint(4) NOT NULL DEFAULT 0,
  `ine` varchar(25) NOT NULL,
  PRIMARY KEY (`elev_id`,`classe`,`nom`,`prenom`),
  UNIQUE KEY `elev_id` (`elev_id`),
  KEY `elev_id_2` (`elev_id`,`classe`,`passwd_eleve`,`passwd`,`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_elevessansclasse`
--

CREATE TABLE IF NOT EXISTS `tria_elevessansclasse` (
  `elev_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `lv1` varchar(30) DEFAULT NULL,
  `lv2` varchar(30) DEFAULT NULL,
  `option` varchar(30) DEFAULT NULL,
  `regime` varchar(25) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(25) DEFAULT NULL,
  `nationalite` varchar(20) DEFAULT NULL,
  `passwd` varchar(50) DEFAULT NULL,
  `passwd_eleve` varchar(50) DEFAULT NULL,
  `civ_1` smallint(6) DEFAULT NULL,
  `nomtuteur` varchar(30) DEFAULT NULL,
  `prenomtuteur` varchar(30) DEFAULT NULL,
  `adr1` varchar(100) DEFAULT NULL,
  `code_post_adr1` varchar(6) DEFAULT NULL,
  `commune_adr1` varchar(40) DEFAULT NULL,
  `tel_port_1` varchar(25) DEFAULT NULL,
  `civ_2` smallint(6) DEFAULT NULL,
  `nom_resp_2` varchar(50) DEFAULT NULL,
  `prenom_resp_2` varchar(50) DEFAULT NULL,
  `adr2` varchar(100) DEFAULT NULL,
  `code_post_adr2` varchar(6) DEFAULT NULL,
  `commune_adr2` varchar(40) DEFAULT NULL,
  `tel_port_2` varchar(25) DEFAULT NULL,
  `telephone` varchar(18) DEFAULT NULL,
  `profession_pere` varchar(30) DEFAULT NULL,
  `tel_prof_pere` varchar(18) DEFAULT NULL,
  `profession_mere` varchar(30) DEFAULT NULL,
  `tel_prof_mere` varchar(18) DEFAULT NULL,
  `nom_etablissement` varchar(30) DEFAULT NULL,
  `numero_etablissement` varchar(30) DEFAULT NULL,
  `code_postal_etablissement` varchar(6) DEFAULT NULL,
  `commune_etablissement` varchar(30) DEFAULT NULL,
  `numero_eleve` varchar(30) DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `email_eleve` varchar(90) DEFAULT NULL,
  `email_resp_2` varchar(90) DEFAULT NULL,
  `class_ant` varchar(30) DEFAULT NULL,
  `annee_ant` varchar(20) DEFAULT NULL,
  `numero_gep` int(11) DEFAULT NULL,
  `valid_forward_mail_eleve` tinyint(4) DEFAULT NULL,
  `valid_forward_mail_parent` tinyint(4) DEFAULT NULL,
  `tel_eleve` varchar(25) DEFAULT NULL,
  `code_compta` varchar(30) NOT NULL,
  `sexe` varchar(1) NOT NULL,
  `adr_eleve` varchar(100) DEFAULT NULL,
  `ccp_eleve` varchar(15) DEFAULT NULL,
  `commune_eleve` varchar(40) DEFAULT NULL,
  `tel_fixe_eleve` varchar(25) DEFAULT NULL,
  `passwd_parent_2` varchar(50) DEFAULT NULL,
  `information` text DEFAULT NULL,
  `pays_eleve` varchar(50) DEFAULT NULL,
  `boursier` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`elev_id`,`nom`,`prenom`),
  KEY `elev_id` (`elev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_eleves_archive`
--

CREATE TABLE IF NOT EXISTS `tria_eleves_archive` (
  `elev_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `classe` int(11) NOT NULL,
  `lv1` varchar(90) DEFAULT NULL,
  `lv2` varchar(90) DEFAULT NULL,
  `option` varchar(90) DEFAULT NULL,
  `regime` varchar(25) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(40) DEFAULT '',
  `nationalite` varchar(20) DEFAULT NULL,
  `civ_1` smallint(6) DEFAULT NULL,
  `nomtuteur` varchar(30) DEFAULT NULL,
  `prenomtuteur` varchar(30) DEFAULT NULL,
  `adr1` varchar(100) DEFAULT NULL,
  `code_post_adr1` varchar(15) DEFAULT NULL,
  `commune_adr1` varchar(40) DEFAULT NULL,
  `tel_port_1` varchar(25) DEFAULT NULL,
  `civ_2` smallint(6) DEFAULT NULL,
  `nom_resp_2` varchar(50) DEFAULT NULL,
  `prenom_resp_2` varchar(50) DEFAULT NULL,
  `adr2` varchar(100) DEFAULT NULL,
  `code_post_adr2` varchar(15) DEFAULT NULL,
  `commune_adr2` varchar(40) DEFAULT NULL,
  `tel_port_2` varchar(25) DEFAULT NULL,
  `telephone` varchar(18) DEFAULT NULL,
  `profession_pere` varchar(30) DEFAULT NULL,
  `tel_prof_pere` varchar(18) DEFAULT NULL,
  `profession_mere` varchar(30) DEFAULT NULL,
  `tel_prof_mere` varchar(18) DEFAULT NULL,
  `nom_etablissement` varchar(30) DEFAULT NULL,
  `numero_etablissement` varchar(30) DEFAULT NULL,
  `code_postal_etablissement` varchar(6) DEFAULT NULL,
  `commune_etablissement` varchar(30) DEFAULT NULL,
  `numero_eleve` varchar(30) DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_eleve` varchar(150) DEFAULT NULL,
  `email_resp_2` varchar(150) DEFAULT NULL,
  `class_ant` varchar(30) DEFAULT NULL,
  `annee_ant` varchar(20) DEFAULT NULL,
  `numero_gep` int(11) DEFAULT NULL,
  `valid_forward_mail_eleve` tinyint(4) DEFAULT NULL,
  `valid_forward_mail_parent` tinyint(4) DEFAULT NULL,
  `tel_eleve` varchar(25) DEFAULT NULL,
  `code_compta` varchar(30) DEFAULT NULL,
  `sexe` varchar(1) DEFAULT NULL,
  `annee_scolaire` int(11) DEFAULT NULL,
  `information` text NOT NULL,
  `adr_eleve` varchar(100) DEFAULT NULL,
  `ccp_eleve` varchar(15) DEFAULT NULL,
  `commune_eleve` varchar(40) DEFAULT NULL,
  `tel_fixe_eleve` varchar(25) DEFAULT NULL,
  `pays_eleve` varchar(50) DEFAULT NULL,
  `compte_inactif` tinyint(4) NOT NULL DEFAULT 0,
  `boursier` tinyint(4) NOT NULL DEFAULT 0,
  `montant_bourse` decimal(50,5) NOT NULL,
  `indemnite_stage` decimal(50,5) NOT NULL,
  `nbmoisindemnite` int(11) NOT NULL DEFAULT 0,
  `mdp_moodle` varchar(50) NOT NULL DEFAULT ' ',
  `emailpro_eleve` varchar(250) NOT NULL,
  `rangement` varchar(250) NOT NULL,
  `cdi` tinyint(1) NOT NULL,
  `bde` tinyint(1) NOT NULL,
  `situation_familiale` varchar(40) NOT NULL,
  PRIMARY KEY (`elev_id`,`classe`,`nom`,`prenom`),
  UNIQUE KEY `elev_id` (`elev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_eleves_histo`
--

CREATE TABLE IF NOT EXISTS `tria_eleves_histo` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`idclasse`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_emploi`
--

CREATE TABLE IF NOT EXISTS `tria_emploi` (
  `classe` varchar(30) NOT NULL DEFAULT '',
  `groupe` varchar(30) NOT NULL DEFAULT '',
  `salle` varchar(30) NOT NULL DEFAULT '',
  `matiere` varchar(30) NOT NULL DEFAULT '',
  `prof` varchar(70) NOT NULL DEFAULT '',
  `jour` varchar(30) NOT NULL DEFAULT '',
  `datedebut` varchar(30) NOT NULL DEFAULT '',
  `debut` varchar(30) NOT NULL DEFAULT '',
  `fin` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`classe`,`groupe`,`salle`,`matiere`,`prof`,`jour`,`datedebut`,`debut`,`fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_entretiendureeprof`
--

CREATE TABLE IF NOT EXISTS `tria_entretiendureeprof` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idprof` int(11) NOT NULL,
  `duree` time NOT NULL,
  `idclasse` int(11) NOT NULL,
  `date_saisie` date NOT NULL,
  `ideleve` int(11) NOT NULL,
  `reference` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_entretieneleve`
--

CREATE TABLE IF NOT EXISTS `tria_entretieneleve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ideleve` int(11) NOT NULL,
  `date` date NOT NULL,
  `heuredebut` time NOT NULL,
  `heurefin` time NOT NULL,
  `nomclasse` varchar(40) NOT NULL,
  `objet` text NOT NULL,
  `recupar` varchar(100) NOT NULL,
  `preparation` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ideleve` (`ideleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_entretienpedagogue`
--

CREATE TABLE IF NOT EXISTS `tria_entretienpedagogue` (
  `id_entretieneleve` int(11) NOT NULL,
  `id_entretienpedagogue` int(11) NOT NULL,
  UNIQUE KEY `id_entretieneleve` (`id_entretieneleve`,`id_entretienpedagogue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_entretienprof`
--

CREATE TABLE IF NOT EXISTS `tria_entretienprof` (
  `idpers` int(11) NOT NULL,
  `date` date NOT NULL,
  `heuredebut` time NOT NULL,
  `heurefin` time NOT NULL,
  `nomclasse` varchar(40) NOT NULL,
  `objet` text NOT NULL,
  `recupar` varchar(100) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preparation` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idpers` (`idpers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_etude_affect`
--

CREATE TABLE IF NOT EXISTS `tria_etude_affect` (
  `id_eleve` int(11) NOT NULL,
  `id_etude` int(11) NOT NULL,
  `information` text DEFAULT NULL,
  `auto_exit` tinyint(1) NOT NULL,
  UNIQUE KEY `id_eleve` (`id_eleve`,`id_etude`),
  KEY `id_eleve_2` (`id_eleve`,`id_etude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_etude_param`
--

CREATE TABLE IF NOT EXISTS `tria_etude_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jour_semaine` varchar(15) NOT NULL,
  `pair_impaire` tinyint(1) DEFAULT NULL,
  `heure` time NOT NULL,
  `salle` varchar(20) DEFAULT NULL,
  `pion` varchar(60) DEFAULT NULL,
  `nom_etude` varchar(20) DEFAULT NULL,
  `duree` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ficheliaison`
--

CREATE TABLE IF NOT EXISTS `tria_ficheliaison` (
  `ideleve` int(11) NOT NULL,
  `trimestre` varchar(30) NOT NULL,
  `dom_progress` text DEFAULT NULL,
  `dom_difficulte` text DEFAULT NULL,
  `com_suj_aide` text DEFAULT NULL,
  `eleve_viescolaire` text DEFAULT NULL,
  `eleve_travscolaire` text DEFAULT NULL,
  `conclusion_prof` text DEFAULT NULL,
  `conclusion_dir` text DEFAULT NULL,
  `idclasse` int(11) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`trimestre`,`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fiche_info`
--

CREATE TABLE IF NOT EXISTS `tria_fiche_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  `idEleve` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `nomProf` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idEleve` (`idEleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fiche_med`
--

CREATE TABLE IF NOT EXISTS `tria_fiche_med` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `ideleve` int(11) NOT NULL,
  `nomProf` varchar(30) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ideleve` (`ideleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fichier`
--

CREATE TABLE IF NOT EXISTS `tria_fichier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(30) DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `TYPE` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_bareme`
--

CREATE TABLE IF NOT EXISTS `tria_fin_bareme` (
  `bareme_id` int(11) NOT NULL AUTO_INCREMENT,
  `code_class` int(11) NOT NULL DEFAULT 0,
  `libelle` varchar(64) NOT NULL,
  `annee_scolaire` varchar(11) NOT NULL,
  PRIMARY KEY (`bareme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_config_ecole`
--

CREATE TABLE IF NOT EXISTS `tria_fin_config_ecole` (
  `nom_fichier` varchar(32) NOT NULL,
  `numemet` varchar(6) NOT NULL,
  `icb` varchar(24) NOT NULL,
  `dom` varchar(24) NOT NULL,
  `cg` varchar(5) NOT NULL,
  `compt` varchar(11) NOT NULL,
  `libelle` varchar(31) NOT NULL,
  `cb` varchar(5) NOT NULL,
  `ref` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_echeancier`
--

CREATE TABLE IF NOT EXISTS `tria_fin_echeancier` (
  `echeancier_id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL DEFAULT 0,
  `date_echeance` date NOT NULL,
  `montant` double NOT NULL,
  `impaye` tinyint(1) NOT NULL DEFAULT 0,
  `type_reglement_id` int(11) NOT NULL DEFAULT 0,
  `libelle` varchar(64) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `numero_rib` tinyint(1) NOT NULL DEFAULT 0,
  `lisse` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`echeancier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_echeancier_groupe`
--

CREATE TABLE IF NOT EXISTS `tria_fin_echeancier_groupe` (
  `inscription_id` int(11) NOT NULL,
  `echeancier_id` int(11) NOT NULL,
  `groupe_id` int(11) NOT NULL,
  `montant` double NOT NULL,
  PRIMARY KEY (`inscription_id`,`echeancier_id`,`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_frais_bareme`
--

CREATE TABLE IF NOT EXISTS `tria_fin_frais_bareme` (
  `frais_bareme_id` int(11) NOT NULL AUTO_INCREMENT,
  `bareme_id` int(11) NOT NULL DEFAULT 0,
  `type_frais_id` int(11) NOT NULL DEFAULT 0,
  `montant` double NOT NULL DEFAULT 0,
  `optionnel` tinyint(1) NOT NULL DEFAULT 0,
  `lisse` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`frais_bareme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_frais_inscription`
--

CREATE TABLE IF NOT EXISTS `tria_fin_frais_inscription` (
  `frais_inscription_id` int(11) NOT NULL AUTO_INCREMENT,
  `inscription_id` int(11) NOT NULL DEFAULT 0,
  `type_frais_id` int(11) NOT NULL DEFAULT 0,
  `montant` double NOT NULL DEFAULT 0,
  `optionnel` tinyint(1) NOT NULL DEFAULT 0,
  `selectionne` tinyint(1) NOT NULL DEFAULT 0,
  `lisse` tinyint(1) NOT NULL DEFAULT 1,
  `caution_remboursee` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`frais_inscription_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_groupe_frais`
--

CREATE TABLE IF NOT EXISTS `tria_fin_groupe_frais` (
  `groupe_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) NOT NULL,
  PRIMARY KEY (`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_inscriptions`
--

CREATE TABLE IF NOT EXISTS `tria_fin_inscriptions` (
  `inscription_id` int(11) NOT NULL AUTO_INCREMENT,
  `elev_id` int(11) NOT NULL DEFAULT 0,
  `code_class` int(11) NOT NULL DEFAULT 0,
  `annee_scolaire` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `date_inscription` datetime NOT NULL,
  `type_echeancier_id` int(11) NOT NULL DEFAULT 0,
  `date_depart` date DEFAULT NULL,
  `commentaire` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `id_bareme_initial` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`inscription_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_reglement`
--

CREATE TABLE IF NOT EXISTS `tria_fin_reglement` (
  `reglement_id` int(11) NOT NULL AUTO_INCREMENT,
  `echeancier_id` int(11) NOT NULL DEFAULT 0,
  `libelle` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `date_reglement` date NOT NULL,
  `montant` double NOT NULL DEFAULT 0,
  `type_reglement_id` int(11) NOT NULL,
  `realise` tinyint(1) NOT NULL DEFAULT 0,
  `commentaire` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `numero_bordereau` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `numero_cheque` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `rib_id_utilise` int(11) DEFAULT 0,
  `code_banque_utilise` varchar(5) DEFAULT NULL,
  `code_guichet_utilise` varchar(11) DEFAULT NULL,
  `numero_compte_utilise` varchar(11) DEFAULT NULL,
  `cle_rib_utilise` varchar(2) DEFAULT NULL,
  `titulaire_utilise` varchar(32) DEFAULT NULL,
  `banque_utilise` varchar(24) DEFAULT NULL,
  `numero_serie_utilise` varchar(24) DEFAULT NULL,
  `reste_a_payer` double NOT NULL DEFAULT 0,
  `date_remise_bordereau` date DEFAULT NULL,
  `numero` int(32) NOT NULL,
  PRIMARY KEY (`reglement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_rib`
--

CREATE TABLE IF NOT EXISTS `tria_fin_rib` (
  `rib_id` int(11) NOT NULL AUTO_INCREMENT,
  `elev_id` int(11) NOT NULL,
  `numero_rib` tinyint(1) NOT NULL DEFAULT 0,
  `code_banque` varchar(5) NOT NULL,
  `code_guichet` varchar(5) NOT NULL,
  `numero_compte` varchar(11) NOT NULL,
  `cle_rib` varchar(2) NOT NULL,
  `libelle` varchar(32) NOT NULL,
  `titulaire` varchar(24) NOT NULL,
  `banque` varchar(24) NOT NULL,
  `iban` varchar(27) NOT NULL,
  `bic` varchar(11) NOT NULL,
  `swift` varchar(16) NOT NULL,
  PRIMARY KEY (`rib_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_type_echeancier`
--

CREATE TABLE IF NOT EXISTS `tria_fin_type_echeancier` (
  `type_echeancier_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ordre` tinyint(3) NOT NULL,
  `echeances` tinyint(2) NOT NULL,
  `intervale_mois` tinyint(99) NOT NULL,
  PRIMARY KEY (`type_echeancier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_type_frais`
--

CREATE TABLE IF NOT EXISTS `tria_fin_type_frais` (
  `type_frais_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) NOT NULL,
  `lisse` tinyint(1) NOT NULL DEFAULT 0,
  `caution` tinyint(1) NOT NULL DEFAULT 0,
  `groupe_id` int(11) NOT NULL,
  PRIMARY KEY (`type_frais_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_fin_type_reglement`
--

CREATE TABLE IF NOT EXISTS `tria_fin_type_reglement` (
  `type_reglement_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(64) NOT NULL,
  `modifiable` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`type_reglement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_gep_classe`
--

CREATE TABLE IF NOT EXISTS `tria_gep_classe` (
  `reference` varchar(50) NOT NULL,
  `id_classe` int(11) NOT NULL,
  UNIQUE KEY `reference` (`reference`),
  UNIQUE KEY `id_classe` (`id_classe`),
  KEY `id_classe_2` (`id_classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_gps_eleve`
--

CREATE TABLE IF NOT EXISTS `tria_gps_eleve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `date_gps` date NOT NULL,
  `heure_gps` time NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `precision_m` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_eleve_date` (`id_eleve`,`date_gps`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_groupes`
--

CREATE TABLE IF NOT EXISTS `tria_groupes` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `liste_elev` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  `annee_scolaire` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_id` (`group_id`,`libelle`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_history_bulletin`
--

CREATE TABLE IF NOT EXISTS `tria_history_bulletin` (
  `idHistory` int(11) NOT NULL AUTO_INCREMENT,
  `fichier` varchar(255) NOT NULL,
  `classe` varchar(30) NOT NULL,
  `trimestre` varchar(11) NOT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  PRIMARY KEY (`idHistory`),
  KEY `idHistory` (`idHistory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_history_cmd`
--

CREATE TABLE IF NOT EXISTS `tria_history_cmd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_cmd` time NOT NULL,
  `date_cmd` date NOT NULL,
  `user_cmd` varchar(30) NOT NULL,
  `cmd` varchar(50) NOT NULL,
  `commentaire` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_history_eleve`
--

CREATE TABLE IF NOT EXISTS `tria_history_eleve` (
  `ideleve` int(11) NOT NULL,
  `date` date NOT NULL,
  `action` varchar(250) NOT NULL,
  `info` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_history_periode`
--

CREATE TABLE IF NOT EXISTS `tria_history_periode` (
  `idHistory` int(11) NOT NULL AUTO_INCREMENT,
  `fichier` varchar(255) NOT NULL,
  `classe` varchar(30) NOT NULL,
  `periode` varchar(5) NOT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  PRIMARY KEY (`idHistory`),
  KEY `idHistory` (`idHistory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ia_coach`
--

CREATE TABLE IF NOT EXISTS `tria_ia_coach` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire` varchar(12) NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `objet` varchar(250) NOT NULL,
  `coaching` text NOT NULL,
  `idpers` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `annee_scolaire` (`annee_scolaire`,`idmatiere`,`idclasse`,`objet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_info_ecole`
--

CREATE TABLE IF NOT EXISTS `tria_info_ecole` (
  `nom_ecole` varchar(50) DEFAULT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `postal` varchar(20) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `urlsite` varchar(100) DEFAULT NULL,
  `directeur` varchar(50) DEFAULT NULL,
  `academie` varchar(50) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  `departement` varchar(50) DEFAULT NULL,
  `annee_scolaire` varchar(30) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map` tinyint(1) NOT NULL DEFAULT 0,
  `directeur_fonction` varchar(100) NOT NULL,
  `siret` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_introprez`
--

CREATE TABLE IF NOT EXISTS `tria_introprez` (
  `idpers` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `etat_intro` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ip_compte`
--

CREATE TABLE IF NOT EXISTS `tria_ip_compte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpers` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `tuteur` varchar(20) NOT NULL DEFAULT '',
  `ip` varchar(45) NOT NULL,
  `source` varchar(10) NOT NULL DEFAULT 'mobile' COMMENT 'mobile | web',
  `dernier_acces` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pers_ip` (`idpers`,`membre`,`ip`,`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ip_timeout`
--

CREATE TABLE IF NOT EXISTS `tria_ip_timeout` (
  `ip` varchar(30) NOT NULL,
  `timeout` int(11) NOT NULL,
  `locked_until` datetime DEFAULT NULL,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_jeton_mobile`
--

CREATE TABLE IF NOT EXISTS `tria_jeton_mobile` (
  `idpers` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `jeton` varchar(250) NOT NULL,
  `tuteur` varchar(20) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  UNIQUE KEY `idpers` (`idpers`,`membre`,`tuteur`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_mail_grp`
--

CREATE TABLE IF NOT EXISTS `tria_mail_grp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpers` int(11) NOT NULL,
  `liste_id` text NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `public` tinyint(1) DEFAULT NULL,
  `grpelev` int(11) NOT NULL DEFAULT 0,
  `cacher` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`,`idpers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_matieres`
--

CREATE TABLE IF NOT EXISTS `tria_matieres` (
  `code_mat` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(250) DEFAULT NULL,
  `sous_matiere` varchar(50) DEFAULT NULL,
  `offline` tinyint(4) NOT NULL DEFAULT 0,
  `couleur` varchar(7) NOT NULL,
  `libelle_long` varchar(250) NOT NULL,
  `code_matiere` varchar(20) NOT NULL,
  `libelle_en` varchar(250) NOT NULL,
  `libelle_sansaccent` varchar(250) NOT NULL,
  PRIMARY KEY (`code_mat`),
  UNIQUE KEY `libelle` (`libelle`,`sous_matiere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_messageries`
--

CREATE TABLE IF NOT EXISTS `tria_messageries` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `emetteur` int(11) NOT NULL,
  `destinataire` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `lu` tinyint(1) NOT NULL,
  `type_personne` varchar(15) NOT NULL,
  `objet` varchar(100) DEFAULT NULL,
  `type_personne_dest` varchar(5) DEFAULT NULL,
  `lu_par_utilisateur` tinyint(1) DEFAULT NULL,
  `idforward_mail` varchar(50) DEFAULT NULL,
  `repertoire` int(11) DEFAULT NULL,
  `idmess_envoyer` varchar(50) DEFAULT NULL,
  `idpiecejointe` varchar(250) DEFAULT NULL,
  `idgroupe` int(11) DEFAULT NULL,
  `brouillon` tinyint(4) NOT NULL DEFAULT 0,
  `impression` tinyint(4) NOT NULL DEFAULT 0,
  `alerte` tinyint(1) NOT NULL DEFAULT 0,
  `corbeille` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_message`),
  KEY `destinataire` (`destinataire`,`id_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_messagerie_abs`
--

CREATE TABLE IF NOT EXISTS `tria_messagerie_abs` (
  `idpers` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `tuteur` varchar(20) NOT NULL,
  `datefin` date NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_messagerie_envoyer`
--

CREATE TABLE IF NOT EXISTS `tria_messagerie_envoyer` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `emetteur` int(11) NOT NULL,
  `destinataire` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `lu` tinyint(1) NOT NULL,
  `type_personne` varchar(15) NOT NULL,
  `objet` varchar(40) DEFAULT NULL,
  `type_personne_dest` varchar(5) DEFAULT NULL,
  `lu_par_utilisateur` tinyint(1) DEFAULT NULL,
  `idforward_mail` varchar(50) DEFAULT NULL,
  `repertoire` int(11) DEFAULT NULL,
  `idmess_envoyer` varchar(50) DEFAULT NULL,
  `idpiecejointe` varchar(250) DEFAULT NULL,
  `idgroupe` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `destinataire` (`destinataire`,`id_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_messagerie_repertoire`
--

CREATE TABLE IF NOT EXISTS `tria_messagerie_repertoire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pers` int(11) NOT NULL,
  `membre` varchar(20) NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `category` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_pers` (`id_pers`,`membre`,`libelle`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_messages`
--

CREATE TABLE IF NOT EXISTS `tria_messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_msg_id` int(255) NOT NULL,
  `outgoing_msg_id` int(255) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_news_admin`
--

CREATE TABLE IF NOT EXISTS `tria_news_admin` (
  `idnews` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `titre` varchar(30) DEFAULT NULL,
  `texte` text DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `config_video` varchar(30) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`idnews`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_news_prof_p`
--

CREATE TABLE IF NOT EXISTS `tria_news_prof_p` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclasse` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `date_saisie` date NOT NULL,
  `idprof` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idclasse` (`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_notes`
--

CREATE TABLE IF NOT EXISTS `tria_notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `elev_id` int(11) NOT NULL,
  `prof_id` int(11) NOT NULL,
  `code_mat` int(11) NOT NULL,
  `coef` decimal(30,2) NOT NULL,
  `date` date NOT NULL,
  `sujet` text DEFAULT NULL,
  `note` decimal(30,6) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_groupe` int(11) DEFAULT NULL,
  `typenote` varchar(5) DEFAULT NULL,
  `noteexam` varchar(30) DEFAULT NULL,
  `notationsur` int(11) DEFAULT NULL,
  `notevisiblele` date NOT NULL,
  PRIMARY KEY (`note_id`),
  KEY `elev_id` (`elev_id`,`date`,`id_classe`,`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_notes_scolaire`
--

CREATE TABLE IF NOT EXISTS `tria_notes_scolaire` (
  `idmatiere` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `trimestre` varchar(30) NOT NULL,
  `note` decimal(10,2) NOT NULL,
  `ideleve` int(11) NOT NULL,
  `idprof` int(11) NOT NULL,
  `idgroupe` int(11) DEFAULT NULL,
  `commentaire` varchar(250) DEFAULT NULL,
  `examen` tinyint(1) NOT NULL DEFAULT 0,
  `annee_scolaire` varchar(15) NOT NULL,
  UNIQUE KEY `idmatiere` (`idmatiere`,`idclasse`,`trimestre`,`ideleve`,`idprof`,`idgroupe`,`examen`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_notes_scolaire_param`
--

CREATE TABLE IF NOT EXISTS `tria_notes_scolaire_param` (
  `idclasse` int(11) NOT NULL,
  `coefbull` decimal(10,2) NOT NULL,
  `coefprof` decimal(10,2) NOT NULL,
  `coefviescolaire` decimal(10,2) NOT NULL,
  `personnebulletin` varchar(50) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  KEY `idclasse` (`idclasse`,`annee_scolaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_oidc_auth_codes`
--

CREATE TABLE IF NOT EXISTS `tria_oidc_auth_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(128) NOT NULL,
  `client_id` varchar(64) NOT NULL,
  `id_pers` int(11) NOT NULL DEFAULT 0,
  `membre` varchar(50) NOT NULL DEFAULT '',
  `nom` varchar(100) NOT NULL DEFAULT '',
  `prenom` varchar(100) NOT NULL DEFAULT '',
  `redirect_uri` varchar(500) NOT NULL DEFAULT '',
  `scope` varchar(255) NOT NULL DEFAULT 'openid',
  `nonce` varchar(255) NOT NULL DEFAULT '',
  `code_challenge` varchar(255) NOT NULL DEFAULT '',
  `code_challenge_method` varchar(10) NOT NULL DEFAULT '',
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `client_id` (`client_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_oidc_clients`
--

CREATE TABLE IF NOT EXISTS `tria_oidc_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` varchar(64) NOT NULL,
  `client_secret` varchar(128) NOT NULL DEFAULT '',
  `client_name` varchar(100) NOT NULL DEFAULT '',
  `redirect_uris` text NOT NULL,
  `scopes` varchar(255) NOT NULL DEFAULT 'openid profile',
  `grant_types` varchar(100) NOT NULL DEFAULT 'authorization_code',
  `pkce_required` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_oidc_tokens`
--

CREATE TABLE IF NOT EXISTS `tria_oidc_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` varchar(128) NOT NULL,
  `client_id` varchar(64) NOT NULL,
  `id_pers` int(11) NOT NULL DEFAULT 0,
  `membre` varchar(50) NOT NULL DEFAULT '',
  `nom` varchar(100) NOT NULL DEFAULT '',
  `prenom` varchar(100) NOT NULL DEFAULT '',
  `scope` varchar(255) NOT NULL DEFAULT 'openid',
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token` (`access_token`),
  KEY `client_id` (`client_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_pacte_heures`
--

CREATE TABLE IF NOT EXISTS `tria_pacte_heures` (
  `id_heure` int(11) NOT NULL AUTO_INCREMENT,
  `id_inscription` int(11) NOT NULL,
  `date_realisation` date NOT NULL,
  `nb_heures` decimal(5,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_heure`),
  KEY `id_inscription` (`id_inscription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_pacte_inscriptions`
--

CREATE TABLE IF NOT EXISTS `tria_pacte_inscriptions` (
  `id_inscription` int(11) NOT NULL AUTO_INCREMENT,
  `id_type` int(11) NOT NULL,
  `id_pers` int(11) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `date_demande` date NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('propose','accepte','refuse','termine') NOT NULL DEFAULT 'propose',
  `commentaire` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_inscription`),
  KEY `id_pers` (`id_pers`),
  KEY `id_type` (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_pacte_type_mission`
--

CREATE TABLE IF NOT EXISTS `tria_pacte_type_mission` (
  `id_type` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL DEFAULT '',
  `taux_horaire` decimal(8,2) NOT NULL DEFAULT 26.51,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_parametrage`
--

CREATE TABLE IF NOT EXISTS `tria_parametrage` (
  `libelle` varchar(250) NOT NULL,
  `text` text NOT NULL,
  `idclasse` int(11) NOT NULL DEFAULT 0,
  `info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_passage_cdi_compte`
--

CREATE TABLE IF NOT EXISTS `tria_passage_cdi_compte` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `idpers` int(11) NOT NULL,
  `membre` varchar(15) NOT NULL,
  `date` date NOT NULL,
  `passage` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_patch`
--

CREATE TABLE IF NOT EXISTS `tria_patch` (
  `idpatch` varchar(7) NOT NULL,
  `date` date NOT NULL,
  `heure` time NOT NULL,
  `info` text DEFAULT NULL,
  PRIMARY KEY (`idpatch`),
  UNIQUE KEY `idpatch` (`idpatch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_personnel`
--

CREATE TABLE IF NOT EXISTS `tria_personnel` (
  `pers_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) NOT NULL,
  `prenom` varchar(40) NOT NULL,
  `prenom2` varchar(40) DEFAULT NULL,
  `mdp` varchar(100) NOT NULL,
  `type_pers` varchar(3) NOT NULL,
  `civ` smallint(6) DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `valid_forward_mail` tinyint(1) DEFAULT NULL,
  `adr` varchar(100) DEFAULT NULL,
  `code_post` varchar(15) DEFAULT NULL,
  `commune` varchar(40) DEFAULT NULL,
  `tel` varchar(18) DEFAULT NULL,
  `tel_port` varchar(20) DEFAULT NULL,
  `identifiant` varchar(40) DEFAULT NULL,
  `lieudenseigement` varchar(60) DEFAULT NULL,
  `offline` tinyint(4) NOT NULL DEFAULT 0,
  `id_societe_tuteur` int(11) NOT NULL DEFAULT 0,
  `pays` varchar(50) DEFAULT NULL,
  `indice_salaire` int(11) DEFAULT NULL,
  `qualite` varchar(100) NOT NULL DEFAULT ' ',
  `mailing_pers` tinyint(1) NOT NULL DEFAULT 0,
  `keytriadepers` varchar(150) NOT NULL,
  `googleAuthen` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pers_id`),
  UNIQUE KEY `nom` (`nom`,`prenom`,`type_pers`),
  KEY `pers_id` (`pers_id`,`mdp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_piecejointe`
--

CREATE TABLE IF NOT EXISTS `tria_piecejointe` (
  `nom` varchar(250) NOT NULL,
  `md5` varchar(250) NOT NULL,
  `idpiecejointe` varchar(250) NOT NULL,
  `etat` int(11) DEFAULT NULL,
  UNIQUE KEY `md5` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_planclasse`
--

CREATE TABLE IF NOT EXISTS `tria_planclasse` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `posx` int(11) NOT NULL,
  `posy` int(11) NOT NULL,
  UNIQUE KEY `ideleve` (`ideleve`,`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_preinscription_eleves`
--

CREATE TABLE IF NOT EXISTS `tria_preinscription_eleves` (
  `elev_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `classe` varchar(25) DEFAULT '',
  `lv1` varchar(30) DEFAULT '',
  `lv2` varchar(30) DEFAULT '',
  `option2` varchar(30) DEFAULT '',
  `regime` varchar(25) DEFAULT '',
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(40) DEFAULT '',
  `nationalite` varchar(20) DEFAULT '',
  `passwd` varchar(50) DEFAULT NULL,
  `passwd_eleve` varchar(50) NOT NULL,
  `civ_1` smallint(6) DEFAULT NULL,
  `nomtuteur` varchar(30) DEFAULT NULL,
  `prenomtuteur` varchar(30) DEFAULT NULL,
  `adr1` varchar(100) DEFAULT NULL,
  `code_post_adr1` varchar(15) DEFAULT NULL,
  `commune_adr1` varchar(40) DEFAULT NULL,
  `tel_port_1` varchar(25) DEFAULT NULL,
  `civ_2` smallint(6) DEFAULT NULL,
  `nom_resp_2` varchar(50) DEFAULT NULL,
  `prenom_resp_2` varchar(50) DEFAULT NULL,
  `adr2` varchar(100) DEFAULT NULL,
  `code_post_adr2` varchar(15) DEFAULT NULL,
  `commune_adr2` varchar(40) DEFAULT NULL,
  `tel_port_2` varchar(25) DEFAULT NULL,
  `telephone` varchar(18) DEFAULT NULL,
  `profession_pere` varchar(30) DEFAULT NULL,
  `tel_prof_pere` varchar(18) DEFAULT NULL,
  `profession_mere` varchar(30) DEFAULT NULL,
  `tel_prof_mere` varchar(18) DEFAULT NULL,
  `nom_etablissement` varchar(30) DEFAULT NULL,
  `numero_etablissement` varchar(30) DEFAULT NULL,
  `code_postal_etablissement` varchar(6) DEFAULT NULL,
  `commune_etablissement` varchar(30) DEFAULT NULL,
  `numero_eleve` varchar(30) DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_eleve` varchar(150) DEFAULT NULL,
  `email_resp_2` varchar(150) DEFAULT NULL,
  `class_ant` varchar(30) DEFAULT NULL,
  `annee_ant` varchar(20) DEFAULT NULL,
  `numero_gep` int(11) DEFAULT NULL,
  `valid_forward_mail_eleve` tinyint(4) DEFAULT NULL,
  `valid_forward_mail_parent` tinyint(4) DEFAULT NULL,
  `tel_eleve` varchar(25) DEFAULT NULL,
  `code_compta` varchar(30) DEFAULT NULL,
  `sexe` varchar(1) DEFAULT NULL,
  `decision` varchar(11) NOT NULL DEFAULT 'En attente',
  `date_demande` date NOT NULL,
  `datedecision` date NOT NULL,
  `annee_scolaire` year(4) NOT NULL,
  `information` text NOT NULL,
  `adr_eleve` varchar(100) NOT NULL,
  `ccp_eleve` varchar(15) NOT NULL,
  `commune_eleve` varchar(40) NOT NULL,
  `tel_fixe_eleve` varchar(25) NOT NULL,
  `pays_eleve` varchar(50) NOT NULL,
  `boursier` tinyint(4) NOT NULL DEFAULT 0,
  `boursier_montant` decimal(50,6) NOT NULL,
  PRIMARY KEY (`elev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_present`
--

CREATE TABLE IF NOT EXISTS `tria_present` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ideleve` int(11) NOT NULL,
  `idpers` int(11) NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `date` date NOT NULL,
  `horaire` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_prof_p`
--

CREATE TABLE IF NOT EXISTS `tria_prof_p` (
  `idprof` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  PRIMARY KEY (`idprof`,`idclasse`,`annee_scolaire`),
  KEY `idclasse` (`idclasse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_push_tokens`
--

CREATE TABLE IF NOT EXISTS `tria_push_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(5) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user` (`user_type`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_admin`
--

CREATE TABLE IF NOT EXISTS `tria_px_admin` (
  `admin_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_login` varchar(20) NOT NULL,
  `admin_passwd` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_agenda`
--

CREATE TABLE IF NOT EXISTS `tria_px_agenda` (
  `age_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `age_mere_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `age_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `age_aty_id` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `age_date` date NOT NULL DEFAULT '0000-00-00',
  `age_heure_debut` float(10,2) NOT NULL DEFAULT 0.00,
  `age_heure_fin` float(10,2) NOT NULL DEFAULT 0.00,
  `age_ape_id` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `age_periode1` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_periode2` int(7) UNSIGNED NOT NULL DEFAULT 0,
  `age_periode3` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_periode4` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_plage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_plage_duree` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `age_libelle` varchar(230) NOT NULL,
  `age_detail` text NOT NULL,
  `age_rappel` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_rappel_coeff` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `age_email` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_prive` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_couleur` varchar(20) NOT NULL,
  `age_nb_participant` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `age_createur_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `age_date_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `age_modificateur_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `age_date_modif` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `age_disponibilite` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_lieu` varchar(230) NOT NULL,
  `age_cal_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `age_email_contact` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_email_copie` varchar(230) NOT NULL DEFAULT '',
  PRIMARY KEY (`age_id`),
  KEY `age_cal_id` (`age_cal_id`),
  KEY `age_util_id` (`age_util_id`),
  KEY `age_date` (`age_date`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_agenda`
--

INSERT INTO `tria_px_agenda` VALUES
(1, 0, 1, 2, '2026-03-21', 10.00, 10.25, 1, 0, 0, 0, 0, 1, 10, 'OR 1=1 --', 'OR 1=1 --', 0, 1, 0, 0, '', 1, 1, '2026-03-20 23:17:14', 1, '2026-03-20 23:17:14', 0, 'OR 1=1 --', 0, 0, ''),
(2, 0, 1, 2, '2026-03-21', 11.00, 11.25, 1, 0, 0, 0, 0, 1, 10, 'les', 'les', 0, 1, 0, 0, '', 1, 1, '2026-03-20 23:18:02', 1, '2026-03-20 23:18:02', 0, 'les', 0, 0, ''),
(3, 0, 1, 2, '2026-03-21', 12.50, 12.75, 1, 0, 0, 0, 0, 1, 10, '\' OR 1=1 --', '\' OR 1=1 --', 0, 1, 0, 0, '', 1, 1, '2026-03-21 00:15:56', 1, '2026-03-21 00:15:56', 0, '\' OR 1=1 --', 0, 0, ''),
(4, 0, 1, 2, '2026-03-21', 12.50, 12.75, 1, 0, 0, 0, 0, 1, 10, '\' OR 1=1 --', '\' OR 1=1 --', 0, 1, 0, 0, '', 1, 1, '2026-03-21 00:17:10', 1, '2026-03-21 00:17:10', 0, '\' OR 1=1 --', 0, 0, ''),
(5, 0, 1, 2, '2026-03-21', 12.50, 12.75, 1, 0, 0, 0, 0, 1, 10, '\' OR 1=1 --', 'dqsdqs', 0, 1, 0, 0, '', 1, 1, '2026-03-21 00:21:43', 1, '2026-03-21 00:21:43', 0, '\' OR 1=1 --', 0, 0, ''),
(6, 0, 1, 2, '2026-03-21', 12.50, 12.75, 1, 0, 0, 0, 0, 1, 10, '\' OR 1=1 --', '\' OR 1=1 --', 0, 1, 0, 0, '', 1, 1, '2026-03-21 00:26:28', 1, '2026-03-21 00:26:57', 0, '\' OR 1=1 --', 0, 0, ''),
(7, 0, 2, 2, '2026-08-04', 8.00, 8.25, 1, 0, 0, 0, 0, 1, 10, 'Essai', '', 0, 1, 0, 0, '', 1, 2, '2026-08-03 21:01:58', 2, '2026-08-03 21:01:58', 0, '', 0, 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_agenda_concerne`
--

CREATE TABLE IF NOT EXISTS `tria_px_agenda_concerne` (
  `aco_age_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `aco_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `aco_rappel_ok` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `aco_termine` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`aco_age_id`,`aco_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_agenda_concerne`
--

INSERT INTO `tria_px_agenda_concerne` VALUES
(1, 1, 1, 0),
(2, 1, 1, 0),
(6, 1, 1, 0),
(7, 2, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_agenda_export`
--

CREATE TABLE IF NOT EXISTS `tria_px_agenda_export` (
  `aex_util_id` smallint(5) NOT NULL DEFAULT 0,
  `aex_creation` enum('0','1') NOT NULL DEFAULT '0',
  `aex_html` enum('0','1') NOT NULL DEFAULT '0',
  `aex_tz` enum('0','1') NOT NULL DEFAULT '0',
  `aex_ch_note` enum('0','1','2') NOT NULL DEFAULT '0',
  `aex_note_aff` enum('0','1') NOT NULL DEFAULT '0',
  `aex_date_old` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aex_type` varchar(10) NOT NULL,
  PRIMARY KEY (`aex_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_calepin`
--

CREATE TABLE IF NOT EXISTS `tria_px_calepin` (
  `cal_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cal_societe` varchar(50) NOT NULL,
  `cal_nom` varchar(50) NOT NULL,
  `cal_prenom` varchar(30) NOT NULL,
  `cal_adresse` text NOT NULL,
  `cal_cp` varchar(10) NOT NULL,
  `cal_ville` varchar(100) NOT NULL,
  `cal_pays` varchar(100) NOT NULL,
  `cal_domicile` varchar(20) NOT NULL,
  `cal_travail` varchar(20) NOT NULL,
  `cal_portable` varchar(20) NOT NULL,
  `cal_fax` varchar(20) NOT NULL,
  `cal_email` varchar(50) NOT NULL,
  `cal_icq` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cal_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `cal_partage` enum('O','N') NOT NULL DEFAULT 'N',
  `cal_note` text NOT NULL,
  `cal_date_naissance` date NOT NULL,
  `cal_aim` varchar(50) NOT NULL,
  `cal_msn` varchar(50) NOT NULL,
  `cal_yahoo` varchar(50) NOT NULL,
  `cal_emailpro` varchar(50) NOT NULL,
  `cal_siteweb` varchar(255) NOT NULL,
  `cal_rappel_ok` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cal_id`),
  KEY `cal_nom` (`cal_nom`),
  KEY `cal_util_id` (`cal_util_id`),
  KEY `cal_partage` (`cal_partage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_calepin_appartient`
--

CREATE TABLE IF NOT EXISTS `tria_px_calepin_appartient` (
  `cap_cal_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cap_cgr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  KEY `cap_cal_id` (`cap_cal_id`),
  KEY `cap_cgr_id` (`cap_cgr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_calepin_groupe`
--

CREATE TABLE IF NOT EXISTS `tria_px_calepin_groupe` (
  `cgr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cgr_pere_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cgr_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `cgr_nom` varchar(150) NOT NULL,
  PRIMARY KEY (`cgr_id`),
  KEY `cgr_pere_id` (`cgr_pere_id`),
  KEY `cgr_util_id` (`cgr_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_configuration`
--

CREATE TABLE IF NOT EXISTS `tria_px_configuration` (
  `param` varchar(50) NOT NULL,
  `valeur` text NOT NULL,
  `groupe` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`param`),
  KEY `groupe` (`groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_configuration`
--

INSERT INTO `tria_px_configuration` VALUES
('AFF_INFO_DEBUG', 'NON', 0),
('AFF_TITRE_MAILTO', 'OUI', 0),
('AIDE_AFF', '4', 0),
('AIDE_CHEMIN', 'help/', 0),
('AIDE_CHEMIN_PDF', 'help/Phenix.pdf', 0),
('APPLI_LANGUE', 'fr', 0),
('APPLI_VERSION', ' - TRIADE', 0),
('AUTO_UPPERCASE', 'OUI', 0),
('AUTORISE_FCKE', 'NON', 0),
('AUTORISE_HTML', 'NON', 0),
('AUTORISE_SCISSION', 'OUI', 0),
('AUTORISE_SUPPR', 'NON', 0),
('CHECK_VERSION', 'OUI', 0),
('COOKIE_AUTH', 'NON', 0),
('COOKIE_DUREE', '10', 0),
('COOKIE_NOM', 'PXlogin', 0),
('DUREE_SESSION', '300', 0),
('FCKE_BASE', '/UserFiles/', 0),
('FCKE_BROWSE', 'NON', 0),
('FCKE_TOOLBAR', 'Intermed', 0),
('FCKE_UPLOAD', 'NON', 0),
('HORO_DATE_MAJ', '04/30/23', 0),
('INDEX_STYLE', 'Petrole', 0),
('MET_DATE_MAJ', '04/30/23 20', 0),
('MODIF_PARTAGE', 'NON', 0),
('PUBLIC', 'OUI', 0),
('RELOAD_CALENDAR', 'OUI', 0),
('RELOAD_PLANNING', '0', 0),
('RSS_VIDEOS', 'OUI', 0),
('SMTP_LOGIN', '', 0),
('SMTP_PASSWORD', '', 0),
('SMTP_PORT', '', 0),
('SMTP_SERVER', '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_couleurs`
--

CREATE TABLE IF NOT EXISTS `tria_px_couleurs` (
  `cou_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cou_libelle` varchar(100) NOT NULL,
  `cou_couleur` varchar(20) NOT NULL,
  `cou_util_id` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`cou_id`),
  KEY `cou_util_id` (`cou_util_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_couleurs`
--

INSERT INTO `tria_px_couleurs` VALUES
(1, 'Rouge', '#F08080', 0),
(2, 'Orange', 'orange', 0),
(3, 'Jaune', 'yellow', 0),
(4, 'Vert', 'green', 0),
(5, 'Olive', 'olive', 0),
(6, 'Marron', 'brown', 0),
(7, 'Beige', 'beige', 0),
(8, 'Bleu', 'dodgerblue', 0),
(9, 'Violet', 'violet', 0),
(10, 'Corail', '#F08080', 0),
(11, 'Rose', 'pink', 0);

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_droit`
--

CREATE TABLE IF NOT EXISTS `tria_px_droit` (
  `droit_util_id` smallint(5) NOT NULL,
  `droit_profils` smallint(5) NOT NULL DEFAULT 20,
  `droit_agendas` smallint(5) NOT NULL DEFAULT 10,
  `droit_notes` smallint(5) NOT NULL DEFAULT 15,
  `droit_aff` varchar(5) NOT NULL DEFAULT '000',
  `droit_admin` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`droit_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_emplacement`
--

CREATE TABLE IF NOT EXISTS `tria_px_emplacement` (
  `empl_id` int(11) NOT NULL AUTO_INCREMENT,
  `empl_nom` varchar(250) NOT NULL,
  `empl_util_id` smallint(5) UNSIGNED DEFAULT 0,
  `empl_type` smallint(5) UNSIGNED DEFAULT 0,
  `empl_partage` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`empl_id`),
  KEY `empl_util_id` (`empl_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_evenement`
--

CREATE TABLE IF NOT EXISTS `tria_px_evenement` (
  `eve_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `eve_date_debut` date NOT NULL,
  `eve_date_fin` date NOT NULL,
  `eve_libelle` varchar(100) NOT NULL,
  `eve_type` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `eve_couleur` varchar(20) NOT NULL,
  `eve_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `eve_partage` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`eve_id`),
  KEY `eve_util_id` (`eve_util_id`),
  KEY `eve_date_debut` (`eve_date_debut`),
  KEY `eve_date_fin` (`eve_date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_favoris`
--

CREATE TABLE IF NOT EXISTS `tria_px_favoris` (
  `fav_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fav_nom` varchar(255) NOT NULL,
  `fav_url` text NOT NULL,
  `fav_commentaire` text NOT NULL,
  `fav_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `fav_partage` enum('O','N') NOT NULL DEFAULT 'N',
  `fav_fgr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fav_id`),
  KEY `fav_util_id` (`fav_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_favoris_groupe`
--

CREATE TABLE IF NOT EXISTS `tria_px_favoris_groupe` (
  `fgr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fgr_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `fgr_nom` varchar(150) NOT NULL,
  PRIMARY KEY (`fgr_id`),
  KEY `fgr_util_id` (`fgr_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_fetes`
--

CREATE TABLE IF NOT EXISTS `tria_px_fetes` (
  `fet_mois` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `fet_jour` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `fet_nom` varchar(50) NOT NULL,
  PRIMARY KEY (`fet_nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_fetes`
--

INSERT INTO `tria_px_fetes` VALUES
(8, 5, 'Abel'),
(5, 12, 'Achille'),
(12, 24, 'Adèle'),
(10, 20, 'Adeline'),
(9, 11, 'Adelphe'),
(2, 5, 'Agathe'),
(1, 21, 'Agnès'),
(9, 13, 'Aimé'),
(2, 20, 'Aimée'),
(9, 9, 'Alain'),
(6, 22, 'Alban'),
(11, 15, 'Albert'),
(4, 22, 'Alexandre'),
(2, 17, 'Alexis'),
(12, 16, 'Alice'),
(4, 26, 'Alida'),
(1, 9, 'Alix'),
(8, 1, 'Alphonse'),
(7, 9, 'Amandine'),
(12, 7, 'Ambroise'),
(3, 30, 'Amédée'),
(8, 9, 'Amour'),
(11, 30, 'André'),
(1, 27, 'Angèle'),
(4, 17, 'Anicet'),
(7, 26, 'Anne et Joachin'),
(3, 25, 'Annonciation'),
(4, 21, 'Anselme'),
(6, 26, 'Anthelme'),
(7, 5, 'Antoine'),
(6, 13, 'Antoine de Padoue'),
(9, 12, 'Apollinaire'),
(2, 9, 'Apolline'),
(8, 31, 'Aristide'),
(12, 23, 'Armand'),
(8, 16, 'Armel'),
(11, 11, 'Armistice 1918'),
(5, 8, 'Armistice 1945'),
(2, 10, 'Arnaud'),
(7, 19, 'Arsène'),
(8, 15, 'Assomption'),
(3, 1, 'Aubin'),
(11, 18, 'Aude'),
(6, 23, 'Audrey'),
(2, 29, 'Auguste'),
(5, 27, 'Augustin 1'),
(8, 28, 'Augustin 2'),
(9, 23, 'Automne'),
(5, 29, 'Aymar'),
(1, 23, 'Banard'),
(12, 4, 'Barbara'),
(6, 11, 'Barnabé'),
(8, 24, 'Barthélémy'),
(1, 2, 'Basile'),
(10, 17, 'Baudoin'),
(2, 13, 'Béatrice'),
(3, 16, 'Bénédicte'),
(3, 31, 'Benjamin'),
(7, 11, 'Benoît'),
(4, 16, 'Benoît-Joseph'),
(5, 26, 'Bérenger'),
(2, 18, 'Bernadette'),
(8, 20, 'Bernard'),
(5, 20, 'Bernardin'),
(11, 6, 'Bertille'),
(9, 6, 'Bertrand'),
(10, 30, 'Bienvenue'),
(2, 3, 'Blaise'),
(6, 2, 'Blandine'),
(5, 2, 'Boris'),
(11, 13, 'Brice'),
(7, 23, 'Brigitte'),
(10, 6, 'Bruno'),
(11, 7, 'Carine'),
(3, 4, 'Casimir'),
(3, 24, 'Catherine 1'),
(11, 25, 'Catherine 2'),
(4, 29, 'Catherine de Sienne'),
(11, 22, 'Cécile'),
(10, 21, 'Céline'),
(11, 4, 'Charles'),
(3, 2, 'Charles le Bon'),
(7, 17, 'Charlotte'),
(11, 12, 'Christian'),
(7, 24, 'Christine'),
(8, 21, 'Christophe'),
(8, 11, 'Claire'),
(8, 12, 'Clarisse'),
(2, 15, 'Claude'),
(3, 21, 'Clémence'),
(11, 23, 'Clément'),
(6, 4, 'Clotilde'),
(3, 6, 'Colette'),
(9, 26, 'Côme et Damien'),
(5, 21, 'Constantin'),
(1, 25, 'Conversion de Paul'),
(10, 25, 'Crépin'),
(9, 14, 'Croix Glorieuse'),
(3, 18, 'Cyrille'),
(2, 21, 'Damien'),
(12, 11, 'Daniel'),
(12, 29, 'David'),
(9, 20, 'Davy'),
(11, 2, 'Défunts'),
(11, 26, 'Delphine'),
(10, 9, 'Denis'),
(5, 15, 'Denise'),
(6, 9, 'Diane'),
(5, 23, 'Didier'),
(10, 26, 'Dimitri'),
(8, 8, 'Dominique'),
(7, 15, 'Donald'),
(5, 24, 'Donatien'),
(9, 16, 'Edith'),
(11, 20, 'Edmond'),
(1, 5, 'Edouard'),
(10, 16, 'Edwige'),
(11, 17, 'Elisabeth'),
(6, 14, 'Elisée'),
(2, 1, 'Ella'),
(10, 22, 'Elodie'),
(10, 27, 'Emeline'),
(5, 22, 'Emile'),
(9, 19, 'Emilie'),
(4, 19, 'Emma'),
(5, 18, 'Eric'),
(5, 11, 'Estelle'),
(6, 21, 'Eté'),
(12, 26, 'Etienne'),
(2, 7, 'Eugènie'),
(8, 14, 'Evrard'),
(8, 22, 'Fabrice'),
(3, 7, 'Félicité'),
(2, 12, 'Félix'),
(5, 30, 'Ferdinand'),
(6, 27, 'Fernand'),
(5, 1, 'Fête du travail'),
(7, 14, 'Fête Nationale'),
(8, 30, 'Fiacre'),
(4, 24, 'Fidèle'),
(10, 11, 'Firmin'),
(10, 5, 'Fleur'),
(11, 24, 'Flora'),
(12, 1, 'Florence'),
(7, 4, 'Florent'),
(10, 24, 'Florentin'),
(10, 4, 'François d\'Assise'),
(1, 24, 'François de Sales'),
(12, 3, 'François Xavier'),
(3, 9, 'Françoise'),
(12, 22, 'Françoise Xavière'),
(7, 18, 'Frédéric'),
(4, 10, 'Fulbert'),
(2, 19, 'Gabin'),
(12, 17, 'Gaël'),
(8, 7, 'Gaétan'),
(2, 6, 'Gaston'),
(12, 18, 'Gatien'),
(4, 9, 'Gautier'),
(1, 3, 'Geneviève'),
(11, 8, 'Geoffroy'),
(4, 23, 'Georges'),
(12, 5, 'Gérald'),
(10, 3, 'Gérard'),
(10, 13, 'Géraud'),
(5, 28, 'Germain'),
(6, 15, 'Germaine'),
(10, 10, 'Ghislain'),
(6, 7, 'Gilbert'),
(1, 29, 'Gildas'),
(9, 1, 'Gilles'),
(5, 7, 'Gisèle'),
(3, 28, 'Gontran'),
(9, 3, 'Grégoire'),
(3, 3, 'Guénolé'),
(1, 10, 'Guillaume'),
(6, 12, 'Guy'),
(3, 29, 'Gwladys'),
(3, 27, 'Habib'),
(8, 18, 'Hélène'),
(7, 13, 'Henri et Joël'),
(9, 25, 'Hermann'),
(6, 17, 'Hervé'),
(8, 13, 'Hippolyte'),
(12, 21, 'Hivers'),
(5, 16, 'Honoré'),
(2, 27, 'Honorine'),
(11, 3, 'Hubert'),
(4, 1, 'Hugues'),
(8, 17, 'Hyacinthe'),
(4, 13, 'Ida'),
(7, 31, 'Ignace de Loyola'),
(6, 5, 'Igor'),
(12, 8, 'Immaculée Conception'),
(9, 10, 'Inès'),
(9, 2, 'Ingrid'),
(12, 28, 'Innocents'),
(4, 5, 'Irène'),
(6, 28, 'Irénée'),
(2, 22, 'Isabelle'),
(4, 4, 'Isidore'),
(2, 8, 'Jacqueline'),
(7, 25, 'Jacques'),
(11, 28, 'Jacques de la Marche'),
(12, 27, 'Jean'),
(10, 23, 'Jean de Capistran'),
(3, 8, 'Jean de Dieu'),
(8, 19, 'Jean Eudes'),
(6, 16, 'Jean François Régis'),
(6, 24, 'Jean-Baptiste'),
(4, 7, 'Jean-Baptiste de la Salle'),
(8, 4, 'Jean-Marie Vianney'),
(12, 12, 'Jeanne-Françoise de Chantal'),
(9, 30, 'Jérôme'),
(3, 19, 'Joseph'),
(1, 1, 'Jour de l\'an'),
(10, 28, 'Jude'),
(5, 5, 'Judith'),
(4, 12, 'Jules'),
(4, 8, 'Julie'),
(8, 2, 'Julien Eymard'),
(2, 16, 'Julienne'),
(7, 30, 'Juliette'),
(10, 14, 'Juste'),
(6, 1, 'Justin'),
(3, 12, 'Justine'),
(6, 3, 'Kévin'),
(6, 10, 'Landry'),
(3, 26, 'Larissa'),
(8, 10, 'Laurent'),
(2, 23, 'Lazare'),
(3, 22, 'Léa'),
(10, 2, 'Léger'),
(11, 10, 'Léon'),
(6, 18, 'Léonce'),
(8, 25, 'Louis'),
(3, 15, 'Louise'),
(10, 18, 'Luc'),
(12, 13, 'Lucie'),
(1, 8, 'Lucien'),
(8, 3, 'Lydie'),
(4, 25, 'Marc'),
(1, 16, 'Marcel'),
(1, 31, 'Marcelle'),
(4, 6, 'Marcellin'),
(11, 16, 'Marguerite'),
(7, 22, 'Marie Madeleine'),
(7, 6, 'Mariette'),
(7, 20, 'Marina'),
(1, 19, 'Marius'),
(7, 29, 'Marthe'),
(6, 30, 'Martial'),
(1, 30, 'Martine'),
(7, 2, 'Martinien'),
(3, 14, 'Mathilde'),
(5, 14, 'Matthias'),
(9, 21, 'Matthieu'),
(9, 22, 'Maurice'),
(4, 14, 'Maxime'),
(6, 8, 'Médard'),
(1, 6, 'Mélaine'),
(9, 29, 'Michel - Gabriel - Raphaël'),
(2, 24, 'Modeste'),
(8, 27, 'Monique'),
(9, 18, 'Nadège'),
(10, 29, 'Narcisse'),
(8, 26, 'Natacha'),
(7, 27, 'Nathalie'),
(9, 8, 'Nativité'),
(2, 26, 'Nestor'),
(12, 6, 'Nicolas'),
(1, 14, 'Nina'),
(12, 15, 'Ninon'),
(12, 25, 'Noël'),
(6, 6, 'Norbert'),
(2, 11, 'Notre Dame de Lourdes'),
(7, 16, 'Nte Dame Mt Carmel'),
(4, 20, 'Odette'),
(12, 14, 'Odile'),
(1, 4, 'Odilon'),
(3, 5, 'Olive'),
(7, 12, 'Olivier'),
(5, 9, 'Pacôme'),
(4, 18, 'Parfait'),
(5, 17, 'Pascal'),
(4, 15, 'Paterne'),
(3, 17, 'Patrice'),
(1, 26, 'Paule'),
(1, 11, 'Pauline'),
(10, 8, 'Pélagie'),
(5, 3, 'Philippe - Jacques'),
(6, 29, 'Pierre - Paul'),
(12, 9, 'Pierre Fourier'),
(11, 21, 'Présence de Marie'),
(2, 2, 'Présentation'),
(3, 20, 'Printemps'),
(1, 18, 'Prisca'),
(6, 25, 'Prosper'),
(5, 6, 'Prudence'),
(10, 31, 'Quentin'),
(9, 5, 'Raïssa'),
(7, 7, 'Raoul'),
(1, 7, 'Raymond'),
(9, 7, 'Reine'),
(1, 15, 'Rémi'),
(9, 17, 'Renaud'),
(10, 19, 'René'),
(4, 3, 'Richard'),
(4, 30, 'Robert'),
(3, 13, 'Rodrigue'),
(12, 30, 'Roger'),
(9, 15, 'Roland'),
(5, 13, 'Rolande'),
(2, 28, 'Romain'),
(12, 10, 'Romaric'),
(2, 25, 'Roméo'),
(6, 19, 'Romuald'),
(9, 4, 'Rosalie'),
(8, 23, 'Rose de Lima'),
(1, 17, 'Roseline'),
(3, 11, 'Rosine'),
(8, 29, 'Sabine'),
(7, 28, 'Samson'),
(4, 2, 'Sandrine'),
(11, 29, 'Saturnin'),
(1, 20, 'Sébastien'),
(10, 7, 'Serge'),
(11, 27, 'Sévrin'),
(11, 14, 'Sidoine'),
(6, 20, 'Silvère'),
(5, 10, 'Solange'),
(5, 25, 'Sophie'),
(4, 11, 'Stanislas'),
(5, 4, 'Sylvain'),
(12, 31, 'Sylvestre'),
(11, 5, 'Sylvie'),
(11, 19, 'Tanguy'),
(1, 12, 'Tatiana'),
(9, 24, 'Thècle'),
(11, 9, 'Théodore'),
(12, 20, 'Théophile'),
(10, 15, 'Thérèse d\'Avila'),
(10, 1, 'Thérèse de l\'Enfant Jésus'),
(7, 8, 'Thibault'),
(7, 1, 'Thierry'),
(7, 3, 'Thomas'),
(1, 28, 'Thomas d\'Aquin'),
(11, 1, 'Toussaint'),
(8, 6, 'Transfiguration'),
(7, 10, 'Ulrich'),
(12, 19, 'Urbain'),
(2, 14, 'Valentin'),
(4, 28, 'Valérie'),
(9, 28, 'Venceslas'),
(2, 4, 'Véronique'),
(7, 21, 'Victor'),
(3, 23, 'Victorien'),
(1, 22, 'Vincent'),
(9, 27, 'Vincent de Paul'),
(5, 31, 'Visitation de la Sainte Vierge'),
(12, 2, 'Viviane'),
(3, 10, 'Vivien'),
(10, 12, 'Wilfried'),
(5, 19, 'Yves'),
(1, 13, 'Yvette'),
(4, 27, 'Zita');

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_global_groupe`
--

CREATE TABLE IF NOT EXISTS `tria_px_global_groupe` (
  `ggr_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ggr_util_id` smallint(5) NOT NULL,
  `ggr_nom` varchar(100) NOT NULL,
  `ggr_liste` varchar(100) NOT NULL DEFAULT '0',
  `ggr_aff` enum('O','N') NOT NULL DEFAULT 'N',
  `ggr_type` smallint(5) NOT NULL,
  PRIMARY KEY (`ggr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_groupe_util`
--

CREATE TABLE IF NOT EXISTS `tria_px_groupe_util` (
  `gr_util_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `gr_util_nom` varchar(100) NOT NULL,
  `gr_util_liste` text NOT NULL,
  PRIMARY KEY (`gr_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_horoscope`
--

CREATE TABLE IF NOT EXISTS `tria_px_horoscope` (
  `horo_signe` varchar(15) NOT NULL,
  `horo_detail` longtext NOT NULL,
  `horo_date_maj` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_information`
--

CREATE TABLE IF NOT EXISTS `tria_px_information` (
  `info_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `info_emetteur_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `info_destinataire_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `info_age_id` int(11) DEFAULT NULL,
  `info_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `info_commentaire` varchar(255) NOT NULL,
  `info_heure_rappel` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`info_id`),
  KEY `info_destinataire_id` (`info_destinataire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_libelle`
--

CREATE TABLE IF NOT EXISTS `tria_px_libelle` (
  `lib_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lib_nom` varchar(255) NOT NULL,
  `lib_detail` text NOT NULL,
  `lib_duree` float(10,2) NOT NULL DEFAULT 0.25,
  `lib_couleur` varchar(20) NOT NULL,
  `lib_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `lib_partage` enum('O','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`lib_id`),
  KEY `lib_util_id` (`lib_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_memo`
--

CREATE TABLE IF NOT EXISTS `tria_px_memo` (
  `mem_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mem_titre` varchar(255) NOT NULL,
  `mem_contenu` text NOT NULL,
  `mem_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `mem_partage` enum('O','N') NOT NULL DEFAULT 'N',
  `mem_progress` enum('O','N') NOT NULL DEFAULT 'O',
  `mem_pcent` int(11) NOT NULL DEFAULT 0,
  `mem_date` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`mem_id`),
  KEY `mem_util_id` (`mem_util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_meteo`
--

CREATE TABLE IF NOT EXISTS `tria_px_meteo` (
  `met_code_ville` varchar(10) NOT NULL,
  `met_nom_ville` varchar(50) NOT NULL,
  `met_releve` varchar(60) NOT NULL,
  `met_date_maj` varchar(60) NOT NULL,
  `met_jour0` varchar(50) NOT NULL,
  `met_jour1` varchar(50) NOT NULL,
  `met_jour2` varchar(50) NOT NULL,
  `met_jour3` varchar(50) NOT NULL,
  `met_jour4` varchar(50) NOT NULL,
  `met_jour5` varchar(50) NOT NULL,
  `met_jour6` varchar(50) NOT NULL,
  PRIMARY KEY (`met_code_ville`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_mods`
--

CREATE TABLE IF NOT EXISTS `tria_px_mods` (
  `mod_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `mod_fichier` varchar(128) NOT NULL,
  `mod_nom` varchar(64) NOT NULL,
  `mod_titre` varchar(96) NOT NULL,
  `mod_version` varchar(8) NOT NULL,
  `mod_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_planning_affecte`
--

CREATE TABLE IF NOT EXISTS `tria_px_planning_affecte` (
  `paf_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `paf_consultant_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `paf_gr` smallint(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`paf_util_id`,`paf_consultant_id`,`paf_gr`),
  KEY `paf_consultant_id` (`paf_consultant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_planning_affichage`
--

CREATE TABLE IF NOT EXISTS `tria_px_planning_affichage` (
  `aff_util_id` smallint(5) NOT NULL,
  `aff_type` smallint(5) UNSIGNED NOT NULL,
  `aff_figer` enum('O','N') NOT NULL DEFAULT 'N',
  `aff_user` enum('O','N') NOT NULL DEFAULT 'N',
  `aff_precision` enum('0','2','4') NOT NULL DEFAULT '0',
  `aff_debut` float(10,2) NOT NULL DEFAULT 0.00,
  `aff_fin` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`aff_util_id`,`aff_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_planning_partage`
--

CREATE TABLE IF NOT EXISTS `tria_px_planning_partage` (
  `ppl_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ppl_consultant_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ppl_gr` smallint(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ppl_util_id`,`ppl_consultant_id`,`ppl_gr`),
  KEY `ppl_consultant_id` (`ppl_consultant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_rss_reader`
--

CREATE TABLE IF NOT EXISTS `tria_px_rss_reader` (
  `rr_rss_id` int(11) NOT NULL AUTO_INCREMENT,
  `rr_util_id` smallint(5) NOT NULL,
  `rr_rss_url` varchar(200) NOT NULL,
  `rr_rss_titre` varchar(50) NOT NULL,
  `rr_rss_accueil` smallint(1) NOT NULL DEFAULT 0,
  `rr_rss_nb_obj` int(2) DEFAULT NULL,
  `rr_rss_ordre` char(3) DEFAULT NULL,
  PRIMARY KEY (`rr_rss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_sid`
--

CREATE TABLE IF NOT EXISTS `tria_px_sid` (
  `sid_id` varchar(8) NOT NULL,
  `sid_util_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `sid_admin_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `sid_last_maj` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sid_session_id` varchar(32) NOT NULL,
  `sid_util_subst_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `sid_semaine_type` varchar(7) NOT NULL DEFAULT '1111111',
  `sid_filtre_couleur` varchar(20) NOT NULL DEFAULT 'ALL',
  `sid_screen` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_timezone`
--

CREATE TABLE IF NOT EXISTS `tria_px_timezone` (
  `tzn_zone` varchar(40) NOT NULL,
  `tzn_libelle` varchar(50) NOT NULL,
  `tzn_gmt` float(10,2) NOT NULL DEFAULT 0.00,
  `tzn_regle` varchar(12) NOT NULL,
  `tzn_date_ete` varchar(12) NOT NULL,
  `tzn_heure_ete` varchar(6) NOT NULL,
  `tzn_date_hiver` varchar(12) NOT NULL,
  `tzn_heure_hiver` varchar(6) NOT NULL,
  PRIMARY KEY (`tzn_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_px_timezone`
--

INSERT INTO `tria_px_timezone` VALUES
('Africa/Abidjan', 'Abidjan', 0.00, '', '', '', '', ''),
('Africa/Accra', 'Accra', 0.00, '', '', '', '', ''),
('Africa/Addis_Ababa', 'Addis Abeba', 3.00, '', '', '', '', ''),
('Africa/Algiers', 'Alger', 1.00, '', '', '', '', ''),
('Africa/Asmara', 'Asmara', 3.00, '', '', '', '', ''),
('Africa/Bamako', 'Bamako', 0.00, '', '', '', '', ''),
('Africa/Bangui', 'Bangui', 1.00, '', '', '', '', ''),
('Africa/Banjul', 'Banjul', 0.00, '', '', '', '', ''),
('Africa/Bissau', 'Bissau', 0.00, '', '', '', '', ''),
('Africa/Blantyre', 'Blantyre', 2.00, '', '', '', '', ''),
('Africa/Brazzaville', 'Brazzaville', 1.00, '', '', '', '', ''),
('Africa/Bujumbura', 'Bujumbura', 2.00, '', '', '', '', ''),
('Africa/Cairo', 'Le Caire', 2.00, 'Egypt', 'Apr lastFri', '0:00', 'Aug lastThu', '0:00'),
('Africa/Casablanca', 'Casablanca', 0.00, '', '', '', '', ''),
('Africa/Ceuta', 'Ceuta et Melilla', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Africa/Conakry', 'Conakry', 0.00, '', '', '', '', ''),
('Africa/Dakar', 'Dakar', 0.00, '', '', '', '', ''),
('Africa/Dar_es_Salaam', 'Dar es Salaam', 3.00, '', '', '', '', ''),
('Africa/Djibouti', 'Djibouti', 3.00, '', '', '', '', ''),
('Africa/Douala', 'Douala', 1.00, '', '', '', '', ''),
('Africa/El_Aaiun', 'El Aaiun', 0.00, '', '', '', '', ''),
('Africa/Freetown', 'Freetown', 0.00, '', '', '', '', ''),
('Africa/Gaborone', 'Gaborone', 2.00, '', '', '', '', ''),
('Africa/Harare', 'Harare', 2.00, '', '', '', '', ''),
('Africa/Johannesburg', 'Johannesbourg', 2.00, '', '', '', '', ''),
('Africa/Kampala', 'Kampala', 3.00, '', '', '', '', ''),
('Africa/Khartoum', 'Khartoum', 3.00, '', '', '', '', ''),
('Africa/Kigali', 'Kigali', 2.00, '', '', '', '', ''),
('Africa/Kinshasa', 'Kinshasa', 1.00, '', '', '', '', ''),
('Africa/Lagos', 'Lagos', 1.00, '', '', '', '', ''),
('Africa/Libreville', 'Libreville', 1.00, '', '', '', '', ''),
('Africa/Lome', 'Lomé', 0.00, '', '', '', '', ''),
('Africa/Luanda', 'Luanda', 1.00, '', '', '', '', ''),
('Africa/Lubumbashi', 'Lubumbashi', 2.00, '', '', '', '', ''),
('Africa/Lusaka', 'Lusaka', 2.00, '', '', '', '', ''),
('Africa/Malabo', 'Malabo', 1.00, '', '', '', '', ''),
('Africa/Maputo', 'Maputo', 2.00, '', '', '', '', ''),
('Africa/Maseru', 'Maseru', 2.00, '', '', '', '', ''),
('Africa/Mbabane', 'Mbabane', 2.00, '', '', '', '', ''),
('Africa/Mogadishu', 'Mogadiscio', 3.00, '', '', '', '', ''),
('Africa/Monrovia', 'Monrovia', 0.00, '', '', '', '', ''),
('Africa/Nairobi', 'Nairobi', 3.00, '', '', '', '', ''),
('Africa/Ndjamena', 'N\'Djamena', 1.00, '', '', '', '', ''),
('Africa/Niamey', 'Niamey', 1.00, '', '', '', '', ''),
('Africa/Nouakchott', 'Nouakchott', 0.00, '', '', '', '', ''),
('Africa/Ouagadougou', 'Ouagadougou', 0.00, '', '', '', '', ''),
('Africa/Porto-Novo', 'Porto-Novo', 1.00, '', '', '', '', ''),
('Africa/Sao_Tome', 'Sao-Tomé', 0.00, '', '', '', '', ''),
('Africa/Timbuktu', 'Tombouctou', 0.00, '', '', '', '', ''),
('Africa/Tripoli', 'Tripoli', 2.00, '', '', '', '', ''),
('Africa/Tunis', 'Tunis', 1.00, 'Tunisia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Africa/Windhoek', 'Windhoek', 1.00, 'Namibia', 'Sep Sun>=1', '2:00', 'Apr Sun>=1', '2:00'),
('America/Adak', 'Adak', -10.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Anchorage', 'Anchorage', -9.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Anguilla', 'Anguilla', -4.00, '', '', '', '', ''),
('America/Antigua', 'Antigua', -4.00, '', '', '', '', ''),
('America/Araguaina', 'Araguaina', -3.00, '', '', '', '', ''),
('America/Argentina/Buenos_Aires', 'Buenos Aires', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Catamarca', 'Catamarca', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Cordoba', 'Córdoba', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Jujuy', 'Jujuy', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/La_Rioja', 'La Rioja', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Mendoza', 'Mendoza', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Rio_Gallegos', 'Rio Gallegos', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/San_Juan', 'San Juan', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Tucuman', 'Tucuman', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Argentina/Ushuaia', 'Ushuaia', -3.00, 'Arg', 'Oct Sun>=1', '0:00', 'Mar Sun>=15', '0:00'),
('America/Aruba', 'Aruba', -4.00, '', '', '', '', ''),
('America/Asuncion', 'Asuncion', -4.00, 'Para', 'Oct Sun>=15', '0:00', 'Mar Sun>=8', '0:00'),
('America/Atikokan', 'Atikokan', -5.00, '', '', '', '', ''),
('America/Atka', 'Atka', -9.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Bahia', 'Bahia', -3.00, '', '', '', '', ''),
('America/Barbados', 'La Barbade', -4.00, '', '', '', '', ''),
('America/Belem', 'Belém', -3.00, '', '', '', '', ''),
('America/Belize', 'Belize', -6.00, '', '', '', '', ''),
('America/Blanc-Sablon', 'Blanc-Sablon', -4.00, '', '', '', '', ''),
('America/Boa_Vista', 'Boa Vista', -4.00, '', '', '', '', ''),
('America/Bogota', 'Bogota', -5.00, '', '', '', '', ''),
('America/Boise', 'Boise', -7.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Cambridge_Bay', 'Cambridge Bay', -7.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Campo_Grande', 'Campo Grande', -4.00, 'Brazil', 'Oct Sun>=8', '0:00', 'Feb Sun>=15', '0:00'),
('America/Cancun', 'Cancun', -6.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Caracas', 'Caracas', -4.50, '', '', '', '', ''),
('America/Cayenne', 'Cayenne', -3.00, '', '', '', '', ''),
('America/Cayman', 'Iles Caïman', -5.00, '', '', '', '', ''),
('America/Chicago', 'Chicago', -6.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Chihuahua', 'Chihuahua', -7.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Coral_Harbour', 'Coral Harbour', -5.00, '', '', '', '', ''),
('America/Costa_Rica', 'Costa Rica', -6.00, '', '', '', '', ''),
('America/Cuiaba', 'Cuiabá', -4.00, 'Brazil', 'Oct Sun>=8', '0:00', 'Feb Sun>=15', '0:00'),
('America/Curacao', 'Curaçao', -4.00, '', '', '', '', ''),
('America/Danmarkshavn', 'Danmarkshavn', 0.00, '', '', '', '', ''),
('America/Dawson', 'Dawson', -8.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Dawson_Creek', 'Dawson Creek', -7.00, '', '', '', '', ''),
('America/Denver', 'Denver', -7.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Detroit', 'Détroit', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Dominica', 'Dominique', -4.00, '', '', '', '', ''),
('America/Edmonton', 'Edmonton', -7.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Eirunepe', 'Eirunepe', -5.00, '', '', '', '', ''),
('America/El_Salvador', 'El Salvador', -6.00, '', '', '', '', ''),
('America/Ensenada', 'Ensenada', -8.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Fortaleza', 'Fortaleza', -3.00, '', '', '', '', ''),
('America/Glace_Bay', 'Glace Bay', -4.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Godthab', 'Godthab', -3.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('America/Goose_Bay', 'Goose Bay', -4.00, 'StJohns', 'Mar Sun>=8', '0:01', 'Nov Sun>=1', '0:01'),
('America/Grand_Turk', 'Grand Turk', -5.00, 'TC', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Grenada', 'Grenade', -4.00, '', '', '', '', ''),
('America/Guadeloupe', 'Guadeloupe', -4.00, '', '', '', '', ''),
('America/Guatemala', 'Guatemala', -6.00, '', '', '', '', ''),
('America/Guayaquil', 'Guayaquil', -5.00, '', '', '', '', ''),
('America/Guyana', 'Guyana', -4.00, '', '', '', '', ''),
('America/Halifax', 'Halifax', -4.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Havana', 'La Havane', -5.00, 'Cuba', 'Mar Sun>=8', '0:00', 'Oct lastSun', '1:00'),
('America/Hermosillo', 'Hermosillo', -7.00, '', '', '', '', ''),
('America/Indiana/Indianapolis', 'Indianapolis', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Indiana/Knox', 'Knox', -6.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Indiana/Marengo', 'Marengo', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Indiana/Petersburg', 'Petersburg', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Indiana/Vevay', 'Vevay', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/indiana/Vincennes', 'Vincennes', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Indiana/Winamac', 'Winamac', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Inuvik', 'Inuvik', -7.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Iqaluit', 'Iqaluit', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Jamaica', 'Jamaïque', -5.00, '', '', '', '', ''),
('America/Juneau', 'Juneau', -9.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Kentucky/Louisville', 'Louisville', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Kentucky/Monticello', 'Monticello', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/La_Paz', 'La Paz', -4.00, '', '', '', '', ''),
('America/Lima', 'Lima', -5.00, '', '', '', '', ''),
('America/Los_Angeles', 'Los Angeles', -8.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Maceio', 'Maceió', -3.00, '', '', '', '', ''),
('America/Managua', 'Managua', -6.00, 'Nic', 'Apr 30', '2:00', 'Oct Sun>=1', '1:00'),
('America/Manaus', 'Manaus', -4.00, '', '', '', '', ''),
('America/Martinique', 'Martinique', -4.00, '', '', '', '', ''),
('America/Mazatlan', 'Mazatlan', -7.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Menominee', 'Menominee', -6.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Merida', 'Merida', -6.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Mexico_City', 'Mexico City', -6.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Miquelon', 'Miquelon', -3.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Moncton', 'Moncton', -4.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Monterrey', 'Monterrey', -6.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Montevideo', 'Montevideo', -3.00, 'Uruguay', 'Oct Sun>=1', '2:00', 'Mar Sun>=8', '2:00'),
('America/Montreal', 'Montréal', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Montserrat', 'Montserrat', -4.00, '', '', '', '', ''),
('America/Nassau', 'Nassau', -5.00, 'Bahamas', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/New_York', 'New York', -5.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Nipigon', 'Nipigon', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Nome', 'Nome', -9.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Noronha', 'Noronha', -2.00, '', '', '', '', ''),
('America/North_Dakota/Center', 'Dakota du Nord/Centre', -6.00, '', '', '', '', ''),
('America/North_Dakota/New_Salem', 'Dakota du Nord/New Salem', -7.00, '', '', '', '', ''),
('America/Panama', 'Panama', -5.00, '', '', '', '', ''),
('America/Pangnirtung', 'Pangnirtung', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Paramaribo', 'Paramaribo', -3.00, '', '', '', '', ''),
('America/Phoenix', 'Phoenix', -7.00, '', '', '', '', ''),
('America/Port_of_Spain', 'Port d\'Espagne', -4.00, '', '', '', '', ''),
('America/Port-au-Prince', 'Port-au-Prince', -5.00, '', '', '', '', ''),
('America/Porto_Acre', 'Porto Acre', -5.00, '', '', '', '', ''),
('America/Porto_Velho', 'Porto Velho', -4.00, '', '', '', '', ''),
('America/Puerto_Rico', 'Porto-Rico', -4.00, '', '', '', '', ''),
('America/Rainy_River', 'Rainy River', -6.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Rankin_Inlet', 'Rankin Inlet', -6.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Recife', 'Recife', -3.00, '', '', '', '', ''),
('America/Regina', 'Regina', -6.00, '', '', '', '', ''),
('America/Resolute', 'Resolute', -5.00, '', '', '', '', ''),
('America/Rio_Branco', 'Rio Branco', -5.00, '', '', '', '', ''),
('America/Santiago', 'Santiago', -4.00, 'Chile', 'Oct Sun>=9', '4:00u', 'Mar Sun>=9', '3:00u'),
('America/Santo_Domingo', 'Saint-Domingue', -4.00, '', '', '', '', ''),
('America/Sao_Paulo', 'São Paulo', -3.00, 'Brazil', 'Oct Sun>=8', '0:00', 'Feb Sun>=15', '0:00'),
('America/Scoresbysund', 'Scoresbysund', -1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('America/Shiprock', 'Shiprock', -7.00, '', '', '', '', ''),
('America/St_Johns', 'Saint-Johns', -3.50, 'StJohns', 'Mar Sun>=8', '0:01', 'Nov Sun>=1', '0:01'),
('America/St_Kitts', 'Saint-Kitts', -4.00, '', '', '', '', ''),
('America/St_Lucia', 'Sainte-Lucie', -4.00, '', '', '', '', ''),
('America/St_Thomas', 'Saint-Thomas', -4.00, '', '', '', '', ''),
('America/St_Vincent', 'Saint-Vincent', -4.00, '', '', '', '', ''),
('America/Swift_Current', 'Swift Current', -6.00, '', '', '', '', ''),
('America/Tegucigalpa', 'Tegucigalpa', -6.00, '', '', '', '', ''),
('America/Thule', 'Thulé', -4.00, 'Thule', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Thunder_Bay', 'Thunder Bay', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Tijuana', 'Tijuana', -8.00, 'Mexico', 'Apr Sun>=1', '2:00', 'Oct lastSun', '2:00'),
('America/Toronto', 'Toronto', -5.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Tortola', 'Tortola', -4.00, '', '', '', '', ''),
('America/Vancouver', 'Vancouver', -8.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Virgin', 'Iles Vierges', -4.00, '', '', '', '', ''),
('America/Whitehorse', 'Whitehorse', -8.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Winnipeg', 'Winnipeg', -6.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Yakutat', 'Yakutat', -9.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('America/Yellowknife', 'Yellowknife', -7.00, 'Canada', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('Asia/Aden', 'Aden', 3.00, '', '', '', '', ''),
('Asia/Almaty', 'Alma-Ata', 6.00, '', '', '', '', ''),
('Asia/Amman', 'Amman', 2.00, 'Jordan', 'Mar lastThu', '0:00', 'Oct lastFri', '1:00'),
('Asia/Anadyr', 'Anadyr', 12.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Aqtau', 'Aqtau', 5.00, '', '', '', '', ''),
('Asia/Aqtobe', 'Aqtobe', 5.00, '', '', '', '', ''),
('Asia/Ashgabat', 'Ashkhabad', 5.00, '', '', '', '', ''),
('Asia/Baghdad', 'Bagdad', 3.00, 'Iraq', 'Apr 1', '3:00', 'Oct 1', '4:00'),
('Asia/Bahrain', 'Bahreïn', 3.00, '', '', '', '', ''),
('Asia/Baku', 'Bakou', 4.00, 'Azer', 'Mar lastSun', '4:00', 'Oct lastSun', '5:00'),
('Asia/Bangkok', 'Bangkok', 7.00, '', '', '', '', ''),
('Asia/Beirut', 'Beyrouth', 2.00, 'Lebanon', 'Mar lastSun', '0:00', 'Oct lastSun', '0:00'),
('Asia/Bishkek', 'Bishkek', 6.00, '', '', '', '', ''),
('Asia/Brunei', 'Bruneï', 8.00, '', '', '', '', ''),
('Asia/Calcutta', 'Calcutta', 5.50, '', '', '', '', ''),
('Asia/Choibalsan', 'Choibalsan', 9.00, '', '', '', '', ''),
('Asia/Chongqing', 'Chongqing', 8.00, '', '', '', '', ''),
('Asia/Colombo', 'Colombo', 5.50, '', '', '', '', ''),
('Asia/Damascus', 'Damas', 2.00, 'Syria', 'Mar lastFri', '0:00', 'Nov Fri>=1', '0:00'),
('Asia/Dhaka', 'Dhaka', 6.00, '', '', '', '', ''),
('Asia/Dili', 'Dili', 9.00, '', '', '', '', ''),
('Asia/Dubai', 'Dubaï', 4.00, '', '', '', '', ''),
('Asia/Dushanbe', 'Douchanbé', 5.00, '', '', '', '', ''),
('Asia/Gaza', 'Gaza', 2.00, 'Palestine', 'Apr 1', '0:00', 'Sep Thu>=8', '2:00'),
('Asia/Harbin', 'Harbin', 8.00, '', '', '', '', ''),
('Asia/Hong_Kong', 'Hong-Kong', 8.00, '', '', '', '', ''),
('Asia/Hovd', 'Hovd', 7.00, '', '', '', '', ''),
('Asia/Irkutsk', 'Irkoutsk', 8.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Istanbul', 'Istanbul', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Asia/Jakarta', 'Djakarta', 7.00, '', '', '', '', ''),
('Asia/Jayapura', 'Jayapura', 9.00, '', '', '', '', ''),
('Asia/Jerusalem', 'Jérusalem', 2.00, 'Zion', 'Mar Fri>=26', '2:00', 'Sep 16', '2:00'),
('Asia/Kabul', 'Kaboul', 4.50, '', '', '', '', ''),
('Asia/Kamchatka', 'Kamtchatka', 12.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Karachi', 'Karachi', 5.00, '', '', '', '', ''),
('Asia/Kashgar', 'Kashgar', 8.00, '', '', '', '', ''),
('Asia/Katmandu', 'Katmandou', 5.75, '', '', '', '', ''),
('Asia/Krasnoyarsk', 'Krasnoïarsk', 7.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Kuala_Lumpur', 'Kuala Lumpur', 8.00, '', '', '', '', ''),
('Asia/Kuching', 'Kuching', 8.00, '', '', '', '', ''),
('Asia/Kuwait', 'Koweït', 3.00, '', '', '', '', ''),
('Asia/Macau', 'Macau', 8.00, '', '', '', '', ''),
('Asia/Magadan', 'Magadan', 11.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Makassar', 'Macasar', 8.00, '', '', '', '', ''),
('Asia/Manila', 'Manille', 8.00, '', '', '', '', ''),
('Asia/Muscat', 'Muscat', 4.00, '', '', '', '', ''),
('Asia/Nicosia', 'Nicosie', 2.00, 'EUAsia', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Asia/Novosibirsk', 'Novosibirsk', 6.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Omsk', 'Omsk', 6.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Oral', 'Oural', 5.00, '', '', '', '', ''),
('Asia/Phnom_Penh', 'Phnom Penh', 7.00, '', '', '', '', ''),
('Asia/Pontianak', 'Pontianak', 7.00, '', '', '', '', ''),
('Asia/Pyongyang', 'Pyongyang', 9.00, '', '', '', '', ''),
('Asia/Qatar', 'Qatar', 3.00, '', '', '', '', ''),
('Asia/Qyzylorda', 'Kzyl-Orda', 6.00, '', '', '', '', ''),
('Asia/Rangoon', 'Rangoon', 6.50, '', '', '', '', ''),
('Asia/Riyadh', 'Riyad', 3.00, '', '', '', '', ''),
('Asia/Saigon', 'Saïgon', 7.00, '', '', '', '', ''),
('Asia/Sakhalin', 'Sakhaline', 10.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Samarkand', 'Samarcande', 5.00, '', '', '', '', ''),
('Asia/Seoul', 'Séoul', 9.00, '', '', '', '', ''),
('Asia/Shanghai', 'Shanghaï', 8.00, '', '', '', '', ''),
('Asia/Singapore', 'Singapour', 8.00, '', '', '', '', ''),
('Asia/Taipei', 'Taipei', 8.00, '', '', '', '', ''),
('Asia/Tashkent', 'Tachkent', 5.00, '', '', '', '', ''),
('Asia/Tbilisi', 'Tbilissi', 4.00, '', '', '', '', ''),
('Asia/Tehran', 'Téhéran', 3.50, 'Iran', 'Mar 21', '0:00', 'Sep 21', '0:00'),
('Asia/Tel_Aviv', 'Tel-Aviv', 2.00, 'Zion', 'Mar Fri>=26', '2:00', 'Sep 16', '2:00'),
('Asia/Thimphu', 'Timphu', 6.00, '', '', '', '', ''),
('Asia/Tokyo', 'Tokyo', 9.00, '', '', '', '', ''),
('Asia/Ujung_Pandang', 'Ujung Pandang', 8.00, '', '', '', '', ''),
('Asia/Ulaanbaatar', 'Ulaanbaatar', 8.00, '', '', '', '', ''),
('Asia/Urumqi', 'Urumqi', 8.00, '', '', '', '', ''),
('Asia/Vientiane', 'Vientiane', 7.00, '', '', '', '', ''),
('Asia/Vladivostok', 'Vladivostok', 10.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Yakutsk', 'Yakoutsk', 9.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Yekaterinburg', 'Ekaterinenbourg', 5.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Asia/Yerevan', 'Erevan', 4.00, 'RussiaAsia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Atlantic/Azores', 'Açores', -1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Atlantic/Bermuda', 'Bermudes', -4.00, 'US', 'Mar Sun>=8', '2:00', 'Nov Sun>=1', '2:00'),
('Atlantic/Canary', 'Canaries', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Atlantic/Cape_Verde', 'Cap-Vert', -1.00, '', '', '', '', ''),
('Atlantic/Faroe', 'Faroe', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Atlantic/Jan_Mayen', 'Jan Mayen', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Atlantic/Madeira', 'Madère', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Atlantic/Reykjavik', 'Reykjavik', 0.00, '', '', '', '', ''),
('Atlantic/South_Georgia', 'Géorgie du Sud', -2.00, '', '', '', '', ''),
('Atlantic/St_Helena', 'Sainte-Hélène', 0.00, '', '', '', '', ''),
('Atlantic/Stanley', 'Stanley', -4.00, 'Falk', 'Sep Sun>=1', '2:00', 'Apr Sun>=15', '2:00'),
('Australia/Adelaide', 'Adelaïde', 9.50, 'AS', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Brisbane', 'Brisbane', 10.00, '', '', '', '', ''),
('Australia/Broken_Hill', 'Broken Hill', 9.50, 'AS', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Canberra', 'Canberra', 10.00, 'AT', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Currie', 'Currie', 10.00, 'AT', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Darwin', 'Darwin', 9.50, '', '', '', '', ''),
('Australia/Eucla', 'Eucla', 8.75, 'AW', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Hobart', 'Hobart', 10.00, 'AT', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Lindeman', 'Lindeman', 10.00, '', '', '', '', ''),
('Australia/Lord_Howe', 'Ile Lord Howe', 10.50, 'LH', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '2:00'),
('Australia/Melbourne', 'Melbourne', 10.00, 'AV', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Perth', 'Perth', 8.00, 'AW', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Australia/Sydney', 'Sydney', 10.00, 'AN', 'Oct Sun>=1', '2:00', 'Apr Sun>=1', '3:00'),
('Etc/UTC', 'UTC', 0.00, '', '', '', '', ''),
('Europe/Amsterdam', 'Amsterdam', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Andorra', 'Andorre', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Athens', 'Athènes', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Belfast', 'Belfast', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Belgrade', 'Belgrade', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Berlin', 'Berlin', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Bratislava', 'Bratislava', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Brussels', 'Bruxelles', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Bucharest', 'Bucarest', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Budapest', 'Budapest', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Chisinau', 'Chisinau', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Copenhagen', 'Copenhague', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Dublin', 'Dublin', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Gibraltar', 'Gibraltar', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Guernsey', 'Guernesey', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Helsinki', 'Helsinki', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Isle_of_Man', 'Ile de Man', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Jersey', 'Jersey', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Kaliningrad', 'Kaliningrad', 2.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Europe/Kiev', 'Kiev', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Lisbon', 'Lisbonne', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Ljubljana', 'Ljubljana', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/London', 'Londres', 0.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Luxembourg', 'Luxembourg', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Madrid', 'Madrid', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Malta', 'Malte', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Mariehamn', 'Mariehamn', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Minsk', 'Minsk', 2.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Europe/Monaco', 'Monaco', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Moscow', 'Moscou', 3.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Europe/Oslo', 'Oslo', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Paris', 'Paris', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Podgorica', 'Podgorica', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Prague', 'Prague', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Riga', 'Riga', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Rome', 'Rome', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Samara', 'Samara', 4.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Europe/San_Marino', 'Saint-Marin', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Sarajevo', 'Sarajevo', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Simferopol', 'Simferopol', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Skopje', 'Skopje', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Sofia', 'Sofia', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Stockholm', 'Stockholm', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Tallinn', 'Tallin', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Tirane', 'Tirana', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Tiraspol', 'Tiraspol', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Uzhgorod', 'Oujhorod', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Vaduz', 'Vaduz', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Vatican', 'Vatican', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Vienna', 'Vienne', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Vilnius', 'Vilnius', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Volgograd', 'Volgograd', 3.00, 'Russia', 'Mar lastSun', '2:00', 'Oct lastSun', '3:00'),
('Europe/Warsaw', 'Varsovie', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Zagreb', 'Zagreb', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Zaporozhye', 'Zaporijia', 2.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Europe/Zurich', 'Zurich', 1.00, 'EU', 'Mar lastSun', '1:00u', 'Oct lastSun', '1:00u'),
('Indian/Antananarivo', 'Tananarive', 3.00, '', '', '', '', ''),
('Indian/Chagos', 'Chagos', 6.00, '', '', '', '', ''),
('Indian/Christmas', 'Ile Christmas', 7.00, '', '', '', '', ''),
('Indian/Cocos', 'Iles Cocos', 6.50, '', '', '', '', ''),
('Indian/Comoro', 'Comores', 3.00, '', '', '', '', ''),
('Indian/Kerguelen', 'Kerguelen', 5.00, '', '', '', '', ''),
('Indian/Mahe', 'Mahé', 4.00, '', '', '', '', ''),
('Indian/Maldives', 'Maldives', 5.00, '', '', '', '', ''),
('Indian/Mauritius', 'Ile Maurice', 4.00, '', '', '', '', ''),
('Indian/Mayotte', 'Mayotte', 3.00, '', '', '', '', ''),
('Indian/Reunion', 'Réunion', 4.00, '', '', '', '', ''),
('Pacific/Apia', 'Apia', -11.00, '', '', '', '', ''),
('Pacific/Auckland', 'Auckland', 12.00, 'NZ', 'Sep lastSun', '2:00', 'Apr Sun>=1', '3:00'),
('Pacific/Chatham', 'Iles Chatham', 12.75, 'Chatham', 'Sep lastSun', '2:45', 'Apr Sun>=1', '3:45'),
('Pacific/Easter', 'Ile de Pâques', -6.00, 'Chile', 'Oct Sun>=9', '4:00u', 'Mar Sun>=9', '3:00u'),
('Pacific/Efate', 'Efate', 11.00, '', '', '', '', ''),
('Pacific/Enderbury', 'Iles Phoenix', 13.00, '', '', '', '', ''),
('Pacific/Fakaofo', 'Fakaofo', -10.00, '', '', '', '', ''),
('Pacific/Fiji', 'Fidji', 12.00, '', '', '', '', ''),
('Pacific/Funafuti', 'Funafuti', 12.00, '', '', '', '', ''),
('Pacific/Galapagos', 'Iles Galapagos', -6.00, '', '', '', '', ''),
('Pacific/Gambier', 'Iles Gambier', -9.00, '', '', '', '', ''),
('Pacific/Guadalcanal', 'Guadalcanal', 11.00, '', '', '', '', ''),
('Pacific/Guam', 'Guam', 10.00, '', '', '', '', ''),
('Pacific/Honolulu', 'Honolulu', -10.00, '', '', '', '', ''),
('Pacific/Johnston', 'Johnston', -10.00, '', '', '', '', ''),
('Pacific/Kiritimati', 'Iles Line', 14.00, '', '', '', '', ''),
('Pacific/Kosrae', 'Kosrae', 11.00, '', '', '', '', ''),
('Pacific/Kwajalein', 'Kwajalein', 12.00, '', '', '', '', ''),
('Pacific/Majuro', 'Majuro', 12.00, '', '', '', '', ''),
('Pacific/Marquesas', 'Iles Marquises', -9.50, '', '', '', '', ''),
('Pacific/Midway', 'Iles Midway', -11.00, '', '', '', '', ''),
('Pacific/Nauru', 'Nauru', 12.00, '', '', '', '', ''),
('Pacific/Niue', 'Niue', -11.00, '', '', '', '', ''),
('Pacific/Norfolk', 'Norfolk', 11.50, '', '', '', '', ''),
('Pacific/Noumea', 'Nouméa', 11.00, '', '', '', '', ''),
('Pacific/Pago_Pago', 'Pago Pago', -11.00, '', '', '', '', ''),
('Pacific/Palau', 'Palau', 9.00, '', '', '', '', ''),
('Pacific/Pitcairn', 'Pitcairn', -8.00, '', '', '', '', ''),
('Pacific/Ponape', 'Ponape', 11.00, '', '', '', '', ''),
('Pacific/Port_Moresby', 'Port-Moresby', 10.00, '', '', '', '', ''),
('Pacific/Rarotonga', 'Rarotonga', -10.00, '', '', '', '', ''),
('Pacific/Saipan', 'Saipan', 10.00, '', '', '', '', ''),
('Pacific/Samoa', 'Samoa', -11.00, '', '', '', '', ''),
('Pacific/Tahiti', 'Tahiti', -10.00, '', '', '', '', ''),
('Pacific/Tarawa', 'Iles Gilbert', 12.00, '', '', '', '', ''),
('Pacific/Tongatapu', 'Tongatapu', 13.00, '', '', '', '', ''),
('Pacific/Truk', 'Truk', 10.00, '', '', '', '', ''),
('Pacific/Wake', 'Ile Wake', 12.00, '', '', '', '', ''),
('Pacific/Wallis', 'Wallis', 12.00, '', '', '', '', ''),
('Pacific/Yap', 'Yap', 10.00, '', '', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_tria2phenix`
--

CREATE TABLE IF NOT EXISTS `tria_px_tria2phenix` (
  `idtriade` int(11) NOT NULL,
  `idphenix` int(11) NOT NULL,
  `membre` varchar(30) DEFAULT NULL,
  UNIQUE KEY `idtriade` (`idtriade`,`idphenix`,`membre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_px_utilisateur`
--

CREATE TABLE IF NOT EXISTS `tria_px_utilisateur` (
  `util_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `util_nom` varchar(32) NOT NULL,
  `util_prenom` varchar(32) NOT NULL,
  `util_login` varchar(20) NOT NULL,
  `util_passwd` varchar(32) DEFAULT NULL,
  `util_interface` varchar(32) NOT NULL DEFAULT 'Petrole',
  `util_debut_journee` float(10,2) NOT NULL DEFAULT 8.00,
  `util_fin_journee` float(10,2) NOT NULL DEFAULT 18.00,
  `util_telephone_vf` enum('O','N') NOT NULL DEFAULT 'O',
  `util_planning` tinyint(3) UNSIGNED DEFAULT 0,
  `util_partage_planning` enum('0','1','2') NOT NULL DEFAULT '0',
  `util_email` varchar(50) NOT NULL,
  `util_autorise_affect` enum('0','1','2','3') NOT NULL DEFAULT '0',
  `util_alert_affect` enum('O','N') NOT NULL DEFAULT 'N',
  `util_precision_planning` enum('1','2') NOT NULL DEFAULT '1',
  `util_semaine_type` varchar(7) NOT NULL DEFAULT '1111111',
  `util_duree_note` enum('1','2','3','4') NOT NULL DEFAULT '1',
  `util_rappel_delai` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `util_rappel_type` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `util_rappel_email` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `util_format_nom` enum('0','1') NOT NULL DEFAULT '0',
  `util_menu_dispo` enum('8','9') NOT NULL DEFAULT '8',
  `util_url_export` varchar(32) NOT NULL,
  `util_note_barree` enum('O','N') NOT NULL DEFAULT 'O',
  `util_rappel_anniv` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `util_rappel_anniv_coeff` smallint(4) UNSIGNED NOT NULL DEFAULT 1440,
  `util_rappel_anniv_email` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `util_langue` varchar(10) NOT NULL DEFAULT 'fr',
  `util_timezone` varchar(40) NOT NULL DEFAULT 'Europe/Paris',
  `util_timezone_partage` enum('O','N') NOT NULL DEFAULT 'O',
  `util_format_heure` enum('12','24') NOT NULL DEFAULT '24',
  `util_fcke` enum('O','N') NOT NULL DEFAULT 'O',
  `util_fcke_toolbar` varchar(20) NOT NULL DEFAULT 'Intermed',
  `util_fcke_aff_toolbar` enum('O','N') NOT NULL DEFAULT 'O',
  `util_menuonclick` enum('O','N') NOT NULL DEFAULT 'N',
  `util_horo` varchar(15) NOT NULL,
  `util_dd` int(1) NOT NULL DEFAULT 1,
  `util_menu_note` enum('O','N') NOT NULL DEFAULT 'N',
  `util_meteo_code` varchar(10) NOT NULL,
  `util_rappel_son` enum('O','N') NOT NULL DEFAULT 'O',
  `util_choix_son` varchar(50) NOT NULL DEFAULT 'son.wav',
  `util_rss_reader` varchar(50) DEFAULT NULL,
  `util_couleur` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`util_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_regime`
--

CREATE TABLE IF NOT EXISTS `tria_regime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(25) NOT NULL,
  `lundi_m` tinyint(4) NOT NULL DEFAULT 0,
  `lundi_s` tinyint(4) NOT NULL DEFAULT 0,
  `mardi_m` tinyint(4) NOT NULL DEFAULT 0,
  `mardi_s` tinyint(4) NOT NULL DEFAULT 0,
  `mercredi_m` tinyint(4) NOT NULL DEFAULT 0,
  `mercredi_s` tinyint(4) NOT NULL DEFAULT 0,
  `jeudi_m` tinyint(4) NOT NULL DEFAULT 0,
  `jeudi_s` tinyint(4) NOT NULL DEFAULT 0,
  `vendredi_m` tinyint(4) NOT NULL DEFAULT 0,
  `vendredi_s` tinyint(4) NOT NULL DEFAULT 0,
  `samedi_m` tinyint(4) NOT NULL DEFAULT 0,
  `samedi_s` tinyint(4) NOT NULL DEFAULT 0,
  `dimanche_m` tinyint(4) NOT NULL DEFAULT 0,
  `dimanche_s` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_reglement`
--

CREATE TABLE IF NOT EXISTS `tria_reglement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sujet` varchar(30) NOT NULL,
  `refence` varchar(30) NOT NULL,
  `file` varchar(250) NOT NULL,
  `date` date NOT NULL,
  `enseignant` tinyint(1) DEFAULT NULL,
  `classe` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_circulaire` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_resa_liste`
--

CREATE TABLE IF NOT EXISTS `tria_resa_liste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmatos` int(11) NOT NULL,
  `idqui` int(11) NOT NULL,
  `quand` date NOT NULL,
  `heure_depart` time NOT NULL,
  `heure_fin` time NOT NULL,
  `info` text DEFAULT NULL,
  `valider` tinyint(1) NOT NULL,
  `refcommun` varchar(80) NOT NULL,
  `id_edt_seance` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `idmatos` (`idmatos`,`id`,`quand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_resa_matos`
--

CREATE TABLE IF NOT EXISTS `tria_resa_matos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(200) DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `info` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_retards`
--

CREATE TABLE IF NOT EXISTS `tria_retards` (
  `elev_id` int(11) NOT NULL,
  `heure_ret` time NOT NULL,
  `date_ret` date NOT NULL,
  `date_saisie` date NOT NULL,
  `origin_saisie` varchar(30) DEFAULT NULL,
  `duree_ret` varchar(10) DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `idmatiere` int(11) DEFAULT NULL,
  `justifier` int(11) DEFAULT NULL,
  `heure_saisie` time DEFAULT NULL,
  `idprof` int(11) DEFAULT NULL,
  `creneaux` varchar(45) DEFAULT NULL,
  `smsenvoye` tinyint(4) NOT NULL DEFAULT 0,
  `courrierenvoyer` tinyint(4) NOT NULL DEFAULT 0,
  `idrattrapage` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`date_ret`,`elev_id`,`heure_ret`),
  KEY `elev_id` (`elev_id`,`date_ret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_rss`
--

CREATE TABLE IF NOT EXISTS `tria_rss` (
  `idgen` int(11) NOT NULL,
  `conx` varchar(10) NOT NULL,
  `title` text NOT NULL,
  KEY `idgen` (`idgen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_rssgen`
--

CREATE TABLE IF NOT EXISTS `tria_rssgen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpers` int(11) NOT NULL,
  `membre` varchar(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  UNIQUE KEY `idpers` (`idpers`,`membre`,`url`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_sanctions`
--

CREATE TABLE IF NOT EXISTS `tria_sanctions` (
  `elev_id` int(11) NOT NULL,
  `decis_date` date NOT NULL,
  `sanct_id` varchar(5) NOT NULL,
  `retenue` tinyint(1) NOT NULL,
  `date_ret` date DEFAULT NULL,
  `heur_ret` time DEFAULT NULL,
  `date_saisie` date NOT NULL,
  `origin_saisie` varchar(3) NOT NULL,
  PRIMARY KEY (`decis_date`,`elev_id`,`sanct_id`),
  KEY `elev_id` (`elev_id`,`date_ret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_save_moy_annuel`
--

CREATE TABLE IF NOT EXISTS `tria_save_moy_annuel` (
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `idmatiere` int(11) NOT NULL,
  `code_ue` int(11) NOT NULL,
  `moyenne` decimal(10,0) NOT NULL,
  `anne_scolaire` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_save_session`
--

CREATE TABLE IF NOT EXISTS `tria_save_session` (
  `id_pers` int(11) NOT NULL,
  `membre` varchar(30) NOT NULL,
  `session` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL CHECK (json_valid(`session`)),
  `tuteur` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_savoiretre`
--

CREATE TABLE IF NOT EXISTS `tria_savoiretre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ideleve` int(11) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `idpers` int(11) NOT NULL,
  `ponctualite` varchar(255) NOT NULL,
  `motivation` varchar(255) NOT NULL,
  `dynamisme` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `idmatiere` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_sso_tokens`
--

CREATE TABLE IF NOT EXISTS `tria_sso_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jti` varchar(64) NOT NULL,
  `id_pers` int(11) NOT NULL DEFAULT 0,
  `service` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jti` (`jti`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_activite`
--

CREATE TABLE IF NOT EXISTS `tria_stage_activite` (
  `libelle` varchar(60) NOT NULL,
  PRIMARY KEY (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_compterendu`
--

CREATE TABLE IF NOT EXISTS `tria_stage_compterendu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ideleve` int(11) NOT NULL,
  `classe` varchar(50) NOT NULL,
  `nom_etablissement` varchar(100) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `tuteur_stage` varchar(100) NOT NULL,
  `date_stage` varchar(100) NOT NULL,
  `service` varchar(100) NOT NULL,
  `q1` varchar(10) NOT NULL,
  `q2` varchar(10) NOT NULL,
  `q3` varchar(10) NOT NULL,
  `q4` varchar(10) NOT NULL,
  `q5` varchar(10) NOT NULL,
  `q6` text NOT NULL,
  `objectif` text NOT NULL,
  `q7` varchar(10) NOT NULL,
  `q8` text NOT NULL,
  `q9` varchar(10) NOT NULL,
  `q10` text NOT NULL,
  `humain` varchar(10) NOT NULL,
  `q11` text NOT NULL,
  `q12` varchar(10) NOT NULL,
  `q13` text NOT NULL,
  `q14` varchar(10) NOT NULL,
  `q15` text NOT NULL,
  `idstageeleve` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_contrerendu`
--

CREATE TABLE IF NOT EXISTS `tria_stage_contrerendu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ideleve` int(11) NOT NULL,
  `numStage` varchar(30) NOT NULL,
  `dateVisite` date NOT NULL,
  `heureVisite` time NOT NULL,
  `idsociete` int(11) NOT NULL,
  `contrerendu` text DEFAULT NULL,
  `visiteur` varchar(250) NOT NULL DEFAULT ' ',
  `datesaisie` date NOT NULL,
  `idstage` int(11) NOT NULL,
  `identreprise` int(11) NOT NULL,
  `fichier_md5` varchar(50) NOT NULL DEFAULT ' ',
  `fichier_name` varchar(250) NOT NULL DEFAULT ' ',
  `id_prof_visite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ideleve` (`ideleve`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_convention`
--

CREATE TABLE IF NOT EXISTS `tria_stage_convention` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_envoi` date NOT NULL,
  `date_retour` date NOT NULL,
  `idpers` int(11) NOT NULL,
  `idstage` int(11) NOT NULL,
  `message` text NOT NULL,
  `societe` int(11) NOT NULL,
  `etat` int(11) NOT NULL,
  `date_demande` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_date`
--

CREATE TABLE IF NOT EXISTS `tria_stage_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idclasse` int(11) NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `numstage` int(11) NOT NULL,
  `nom_stage` varchar(50) DEFAULT NULL,
  `jourdesemaine` varchar(20) NOT NULL,
  `duree_hebdo` smallint(6) NOT NULL DEFAULT 35,
  PRIMARY KEY (`id`),
  KEY `idclasse` (`idclasse`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_eleve`
--

CREATE TABLE IF NOT EXISTS `tria_stage_eleve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_entreprise` int(11) NOT NULL,
  `id_prof_visite` int(11) DEFAULT NULL,
  `lieu_stage` varchar(50) DEFAULT NULL,
  `visite_effectuer` tinyint(1) DEFAULT NULL,
  `ville_stage` varchar(50) DEFAULT NULL,
  `code_p` varchar(10) DEFAULT NULL,
  `tuteur_stage` varchar(30) DEFAULT NULL,
  `jour_repos` varchar(50) DEFAULT NULL,
  `info_plus` text DEFAULT NULL,
  `loger` tinyint(1) DEFAULT NULL,
  `nourri` tinyint(1) DEFAULT NULL,
  `passage_x_service` tinyint(1) DEFAULT NULL,
  `raison` text DEFAULT NULL,
  `date_visite_prof` date DEFAULT NULL,
  `num_stage` int(11) NOT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `compte_tuteur_stage` int(11) NOT NULL,
  `alternance` tinyint(4) NOT NULL DEFAULT 0,
  `jour_alternance` varchar(20) NOT NULL,
  `dateDebutAlternance` date NOT NULL,
  `dateFinAlternance` date NOT NULL,
  `horairedebutjournalier` time NOT NULL,
  `horairefinjournalier` time NOT NULL,
  `date_visite_prof2` date NOT NULL,
  `id_prof_visite2` int(11) NOT NULL,
  `service` varchar(200) NOT NULL,
  `indemnitestage` varchar(200) NOT NULL,
  `pays_stage` varchar(200) NOT NULL,
  `fax` varchar(30) NOT NULL,
  `autre_responsable` varchar(200) NOT NULL,
  `langue` varchar(200) NOT NULL,
  `trimestre` varchar(2) NOT NULL,
  `compte_tuteur_stage_2` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_entreprise`
--

CREATE TABLE IF NOT EXISTS `tria_stage_entreprise` (
  `id_serial` int(11) NOT NULL AUTO_INCREMENT,
  `secteur_ac` varchar(60) DEFAULT NULL,
  `activite_prin` varchar(30) DEFAULT NULL,
  `nom` varchar(50) NOT NULL DEFAULT '',
  `adresse` varchar(50) DEFAULT NULL,
  `ville` varchar(30) DEFAULT NULL,
  `code_p` varchar(10) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `info_plus` text DEFAULT NULL,
  `probleme` tinyint(1) DEFAULT NULL,
  `secteur_ac2` varchar(60) DEFAULT NULL,
  `secteur_ac3` varchar(60) DEFAULT NULL,
  `bonus` int(11) DEFAULT NULL,
  `pays_ent` varchar(50) DEFAULT NULL,
  `contact_fonction` varchar(50) DEFAULT NULL,
  `nbchambre` varchar(250) NOT NULL DEFAULT '0',
  `siteweb` varchar(250) NOT NULL,
  `grphotelier` varchar(250) NOT NULL,
  `nbetoile` varchar(250) NOT NULL DEFAULT '0',
  `registrecommerce` varchar(100) NOT NULL,
  `siren` varchar(100) NOT NULL,
  `siret` varchar(100) NOT NULL,
  `formejuridique` varchar(50) NOT NULL,
  `secteureconomique` varchar(50) NOT NULL,
  `INSEE` varchar(100) NOT NULL,
  `NAFAPE` varchar(100) NOT NULL,
  `NACE` varchar(100) NOT NULL,
  `typeorganisation` varchar(50) NOT NULL,
  `idcs` int(11) NOT NULL DEFAULT 0,
  `qualite` text NOT NULL,
  `accepte_mineur` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_serial`,`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stage_history`
--

CREATE TABLE IF NOT EXISTS `tria_stage_history` (
  `identreprise` int(11) NOT NULL,
  `nomprenomeleve` varchar(200) NOT NULL,
  `classeeleve` varchar(50) NOT NULL,
  `periodestage` varchar(50) NOT NULL,
  `ideleve` int(11) NOT NULL DEFAULT 0,
  `langue` varchar(200) NOT NULL,
  `trimestre` varchar(2) NOT NULL,
  `service` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statconxparheure`
--

CREATE TABLE IF NOT EXISTS `tria_statconxparheure` (
  `heure` int(11) NOT NULL,
  `nb_fois` int(11) NOT NULL,
  PRIMARY KEY (`heure`),
  KEY `heure` (`heure`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statdebit`
--

CREATE TABLE IF NOT EXISTS `tria_statdebit` (
  `debit` varchar(20) NOT NULL,
  `nb_fois` int(11) NOT NULL,
  PRIMARY KEY (`debit`),
  KEY `debit` (`debit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statexecution`
--

CREATE TABLE IF NOT EXISTS `tria_statexecution` (
  `file` varchar(30) NOT NULL,
  `time_max` decimal(10,10) NOT NULL,
  `time_min` decimal(10,10) NOT NULL,
  UNIQUE KEY `file` (`file`),
  KEY `file_2` (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statnavigateur`
--

CREATE TABLE IF NOT EXISTS `tria_statnavigateur` (
  `navigateur` varchar(30) NOT NULL,
  `version` varchar(15) NOT NULL,
  `nb_fois` int(11) NOT NULL,
  `os` varchar(20) DEFAULT NULL,
  `langue` varchar(10) DEFAULT NULL,
  KEY `navigateur` (`navigateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statscreen`
--

CREATE TABLE IF NOT EXISTS `tria_statscreen` (
  `taille` varchar(15) NOT NULL,
  `nb_fois` int(11) NOT NULL,
  PRIMARY KEY (`taille`),
  KEY `taille` (`taille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_statUtilisateur`
--

CREATE TABLE IF NOT EXISTS `tria_statUtilisateur` (
  `date_entree` date NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `idpers` int(11) NOT NULL,
  `type_membre` varchar(15) NOT NULL,
  `id_session` text DEFAULT NULL,
  `nb_conx` int(11) DEFAULT NULL,
  `der_conx` varchar(30) DEFAULT NULL,
  UNIQUE KEY `nom` (`nom`,`prenom`,`idpers`,`type_membre`),
  KEY `idpers` (`idpers`,`type_membre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stat_trace`
--

CREATE TABLE IF NOT EXISTS `tria_stat_trace` (
  `nom` varchar(80) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `heure` time DEFAULT NULL,
  `os` varchar(30) DEFAULT NULL,
  `navigateur` varchar(30) DEFAULT NULL,
  `membre` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_stockage_partage`
--

CREATE TABLE IF NOT EXISTS `tria_stockage_partage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fichier` varchar(255) NOT NULL,
  `chemin` text NOT NULL,
  `membreIdProprio` varchar(250) NOT NULL,
  `membreIdAutorise` varchar(250) NOT NULL,
  `idclasse` int(11) NOT NULL,
  `membresource` varchar(50) NOT NULL,
  `idsource` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_types_personnel`
--

CREATE TABLE IF NOT EXISTS `tria_types_personnel` (
  `type_pers` varchar(3) NOT NULL,
  `libelle` varchar(30) NOT NULL,
  `membre` varchar(30) NOT NULL,
  PRIMARY KEY (`type_pers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `tria_types_personnel`
--

INSERT INTO `tria_types_personnel` VALUES
('ADM', 'administrateur', 'menuadmin'),
('ENS', 'enseignant', 'menuprof'),
('MVS', 'Vie Scolaire', 'menuscolaire'),
('PER', 'Personnel', 'menupersonnel'),
('TUT', 'Tuteur de stage', 'menututeur');

-- --------------------------------------------------------

--
-- Structure de la table `tria_type_category`
--

CREATE TABLE IF NOT EXISTS `tria_type_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) NOT NULL,
  UNIQUE KEY `id` (`id`,`libelle`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_type_nb_sanction`
--

CREATE TABLE IF NOT EXISTS `tria_type_nb_sanction` (
  `sanction` int(11) NOT NULL,
  `nb` smallint(6) NOT NULL,
  `origin_user` varchar(30) NOT NULL,
  `date_saisie` date NOT NULL,
  PRIMARY KEY (`sanction`),
  KEY `sanction` (`sanction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_type_sanction`
--

CREATE TABLE IF NOT EXISTS `tria_type_sanction` (
  `id_sanc` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) NOT NULL,
  `id_category` int(11) NOT NULL,
  PRIMARY KEY (`id_sanc`),
  UNIQUE KEY `id_sanc` (`id_sanc`,`libelle`),
  KEY `id_sanc_2` (`id_sanc`,`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ue`
--

CREATE TABLE IF NOT EXISTS `tria_ue` (
  `code_ue` int(11) NOT NULL AUTO_INCREMENT,
  `code_classe` int(11) NOT NULL,
  `semestre` int(2) NOT NULL,
  `num_ue` int(2) NOT NULL,
  `nom_ue` varchar(255) NOT NULL,
  `coef_ue` decimal(10,0) NOT NULL,
  `ects_ue` decimal(10,0) NOT NULL,
  `idpers_profp` int(11) NOT NULL,
  `nom_ue_en` varchar(255) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `matricule_ue` varchar(15) NOT NULL,
  PRIMARY KEY (`code_ue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_ue_detail`
--

CREATE TABLE IF NOT EXISTS `tria_ue_detail` (
  `code_ue_detail` int(11) NOT NULL AUTO_INCREMENT,
  `code_ue` int(11) NOT NULL,
  `code_matiere` int(11) NOT NULL,
  `code_enseignant` int(11) NOT NULL,
  `code_idgroupe` int(11) NOT NULL,
  PRIMARY KEY (`code_ue_detail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_users`
--

CREATE TABLE IF NOT EXISTS `tria_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_id` int(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `update_sync` int(11) NOT NULL,
  `id_pers` int(11) DEFAULT NULL,
  `membre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_idpers_membre` (`id_pers`,`membre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_vacataires`
--

CREATE TABLE IF NOT EXISTS `tria_vacataires` (
  `pers_id` int(11) NOT NULL,
  `date_ent` date NOT NULL,
  `rpers_id` int(11) NOT NULL,
  `date_sort` date DEFAULT NULL,
  PRIMARY KEY (`pers_id`,`rpers_id`) USING BTREE,
  KEY `pers_id` (`pers_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_vacation_commande`
--

CREATE TABLE IF NOT EXISTS `tria_vacation_commande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmatiere` int(11) NOT NULL,
  `nbheure` int(11) NOT NULL,
  `type_prestation` varchar(20) DEFAULT NULL,
  `idclasse` int(11) DEFAULT NULL,
  `id_pers` int(11) DEFAULT NULL,
  `nbforfait` int(11) NOT NULL DEFAULT 1,
  `enNet` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_vacation_config`
--

CREATE TABLE IF NOT EXISTS `tria_vacation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(40) NOT NULL,
  `taux` decimal(10,5) NOT NULL,
  `type_prestation` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_vacation_paiement`
--

CREATE TABLE IF NOT EXISTS `tria_vacation_paiement` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_prof` int(11) NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `montant_ht` decimal(10,0) NOT NULL,
  `montant_tc` decimal(10,0) NOT NULL,
  `montant_tva` decimal(10,0) NOT NULL,
  `datetransaction` date NOT NULL,
  `idpiecejointe` varchar(50) NOT NULL DEFAULT ' ',
  `info` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tris`
--

CREATE TABLE IF NOT EXISTS `tris` (
  `id_tri` int(4) NOT NULL AUTO_INCREMENT,
  `tri_par` varchar(100) NOT NULL DEFAULT '',
  `nom_tri` varchar(100) NOT NULL DEFAULT '',
  `tri_reference` varchar(40) NOT NULL DEFAULT 'notices',
  PRIMARY KEY (`id_tri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_custom`
--

CREATE TABLE IF NOT EXISTS `tu_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_custom_dates`
--

CREATE TABLE IF NOT EXISTS `tu_custom_dates` (
  `tu_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_custom_date_type` int(11) DEFAULT NULL,
  `tu_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `tu_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `tu_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`tu_custom_champ`,`tu_custom_origine`,`tu_custom_order`),
  KEY `tu_custom_champ` (`tu_custom_champ`),
  KEY `tu_custom_origine` (`tu_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_custom_lists`
--

CREATE TABLE IF NOT EXISTS `tu_custom_lists` (
  `tu_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_custom_list_value` varchar(255) DEFAULT NULL,
  `tu_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`tu_custom_champ`),
  KEY `editorial_champ_list_value` (`tu_custom_champ`,`tu_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_custom_values`
--

CREATE TABLE IF NOT EXISTS `tu_custom_values` (
  `tu_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_custom_small_text` varchar(255) DEFAULT NULL,
  `tu_custom_text` text DEFAULT NULL,
  `tu_custom_integer` int(11) DEFAULT NULL,
  `tu_custom_date` date DEFAULT NULL,
  `tu_custom_float` float DEFAULT NULL,
  `tu_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`tu_custom_champ`),
  KEY `editorial_custom_origine` (`tu_custom_origine`),
  KEY `i_tcv_st` (`tu_custom_small_text`),
  KEY `i_tcv_t` (`tu_custom_text`(255)),
  KEY `i_tcv_i` (`tu_custom_integer`),
  KEY `i_tcv_d` (`tu_custom_date`),
  KEY `i_tcv_f` (`tu_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_distrib`
--

CREATE TABLE IF NOT EXISTS `tu_distrib` (
  `distrib_num_tu` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `distrib_name` varchar(255) NOT NULL DEFAULT '',
  `distrib_ordre` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`distrib_num_tu`,`distrib_ordre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_oeuvres_events`
--

CREATE TABLE IF NOT EXISTS `tu_oeuvres_events` (
  `oeuvre_event_tu_num` int(11) NOT NULL DEFAULT 0,
  `oeuvre_event_authperso_authority_num` int(11) NOT NULL DEFAULT 0,
  `oeuvre_event_order` int(11) NOT NULL DEFAULT 0,
  KEY `i_toe_oeuvre_event_tu_num` (`oeuvre_event_tu_num`),
  KEY `i_toe_oeuvre_event_authperso_authority_num` (`oeuvre_event_authperso_authority_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_oeuvres_links`
--

CREATE TABLE IF NOT EXISTS `tu_oeuvres_links` (
  `oeuvre_link_from` int(11) NOT NULL DEFAULT 0,
  `oeuvre_link_to` int(11) NOT NULL DEFAULT 0,
  `oeuvre_link_type` varchar(3) NOT NULL DEFAULT '',
  `oeuvre_link_expression` int(11) NOT NULL DEFAULT 0,
  `oeuvre_link_other_link` int(11) NOT NULL DEFAULT 1,
  `oeuvre_link_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`oeuvre_link_from`,`oeuvre_link_to`,`oeuvre_link_type`,`oeuvre_link_expression`,`oeuvre_link_other_link`),
  KEY `i_oeuvre_link_from` (`oeuvre_link_from`),
  KEY `i_oeuvre_link_to` (`oeuvre_link_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_ref`
--

CREATE TABLE IF NOT EXISTS `tu_ref` (
  `ref_num_tu` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `ref_name` varchar(255) NOT NULL DEFAULT '',
  `ref_ordre` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ref_num_tu`,`ref_ordre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tu_subdiv`
--

CREATE TABLE IF NOT EXISTS `tu_subdiv` (
  `subdiv_num_tu` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `subdiv_name` varchar(255) NOT NULL DEFAULT '',
  `subdiv_ordre` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`subdiv_num_tu`,`subdiv_ordre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tva_achats`
--

CREATE TABLE IF NOT EXISTS `tva_achats` (
  `id_tva` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `taux_tva` float(4,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `num_cp_compta` varchar(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tva`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `types_produits`
--

CREATE TABLE IF NOT EXISTS `types_produits` (
  `id_produit` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `num_cp_compta` varchar(25) NOT NULL DEFAULT '0',
  `num_tva_achat` varchar(25) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_produit`),
  KEY `libelle` (`libelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `type_abts`
--

CREATE TABLE IF NOT EXISTS `type_abts` (
  `id_type_abt` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_abt_libelle` varchar(255) DEFAULT NULL,
  `prepay` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `prepay_deflt_mnt` decimal(16,2) NOT NULL DEFAULT 0.00,
  `tarif` decimal(16,2) NOT NULL DEFAULT 0.00,
  `commentaire` text NOT NULL,
  `caution` decimal(16,2) NOT NULL DEFAULT 0.00,
  `localisations` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_type_abt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `type_comptes`
--

CREATE TABLE IF NOT EXISTS `type_comptes` (
  `id_type_compte` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `type_acces` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `acces_id` text NOT NULL,
  PRIMARY KEY (`id_type_compte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `upload_repertoire`
--

CREATE TABLE IF NOT EXISTS `upload_repertoire` (
  `repertoire_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repertoire_nom` varchar(255) NOT NULL DEFAULT '',
  `repertoire_url` text NOT NULL,
  `repertoire_path` text NOT NULL,
  `repertoire_navigation` int(1) NOT NULL DEFAULT 0,
  `repertoire_hachage` int(1) NOT NULL DEFAULT 0,
  `repertoire_subfolder` int(8) NOT NULL DEFAULT 0,
  `repertoire_utf8` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`repertoire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lastname` varchar(60) DEFAULT NULL,
  `firstname` varchar(60) DEFAULT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `auth_source` varchar(50) DEFAULT 'platform',
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5,
  `official_code` varchar(40) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `picture_uri` varchar(250) DEFAULT NULL,
  `creator_id` int(10) UNSIGNED DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `diplomas` text DEFAULT NULL,
  `openarea` text DEFAULT NULL,
  `teach` text DEFAULT NULL,
  `productions` varchar(250) DEFAULT NULL,
  `chatcall_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chatcall_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chatcall_text` varchar(50) NOT NULL DEFAULT '',
  `language` varchar(40) DEFAULT NULL,
  `registration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expiration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `openid` varchar(255) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `hr_dept_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `login_counter` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `login_failed_counter` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(5) NOT NULL AUTO_INCREMENT,
  `create_dt` date NOT NULL DEFAULT '0000-00-00',
  `last_updated_dt` date NOT NULL DEFAULT '0000-00-00',
  `username` varchar(100) NOT NULL DEFAULT '',
  `pwd` varchar(50) NOT NULL DEFAULT '',
  `user_digest` varchar(255) NOT NULL DEFAULT '',
  `nom` varchar(30) NOT NULL DEFAULT '',
  `prenom` varchar(30) DEFAULT NULL,
  `rights` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `user_lang` varchar(5) NOT NULL DEFAULT 'fr_FR',
  `nb_per_page_search` int(10) UNSIGNED NOT NULL DEFAULT 4,
  `nb_per_page_select` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `nb_per_page_gestion` int(10) UNSIGNED NOT NULL DEFAULT 20,
  `param_popup_ticket` smallint(1) UNSIGNED NOT NULL DEFAULT 0,
  `param_sounds` smallint(1) UNSIGNED NOT NULL DEFAULT 1,
  `param_rfid_activate` int(1) NOT NULL DEFAULT 1,
  `param_chat_activate` int(1) NOT NULL DEFAULT 0,
  `param_licence` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_notice_statut` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_notice_statut_analysis` int(6) UNSIGNED DEFAULT 0,
  `deflt_integration_notice_statut` int(6) NOT NULL DEFAULT 1,
  `xmlta_indexation_lang` varchar(10) NOT NULL DEFAULT '',
  `deflt_docs_type` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_serials_docs_type` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_lenders` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_styles` varchar(20) NOT NULL DEFAULT 'default',
  `deflt_docs_statut` int(6) UNSIGNED DEFAULT 0,
  `deflt_docs_codestat` int(6) UNSIGNED DEFAULT 0,
  `value_deflt_lang` varchar(20) DEFAULT 'fre',
  `value_deflt_fonction` varchar(20) DEFAULT '070',
  `value_deflt_relation` varchar(20) NOT NULL DEFAULT 'a',
  `value_deflt_relation_serial` varchar(20) NOT NULL DEFAULT '',
  `value_deflt_relation_bulletin` varchar(20) NOT NULL DEFAULT '',
  `value_deflt_relation_analysis` varchar(20) NOT NULL DEFAULT '',
  `deflt_docs_location` int(6) UNSIGNED DEFAULT 0,
  `deflt_collstate_location` int(6) UNSIGNED DEFAULT 0,
  `deflt_bulletinage_location` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_resas_location` int(6) UNSIGNED DEFAULT 0,
  `deflt_docs_section` int(6) UNSIGNED DEFAULT 0,
  `value_deflt_module` varchar(30) DEFAULT 'circu',
  `user_email` varchar(255) DEFAULT '',
  `user_alert_resamail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `user_alert_demandesmail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `user_alert_subscribemail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `user_alert_serialcircmail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `deflt2docs_location` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_empr_statut` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_empr_categ` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_empr_codestat` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_thesaurus` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_concept_scheme` int(3) NOT NULL DEFAULT -1,
  `deflt_import_thesaurus` int(11) NOT NULL DEFAULT 1,
  `value_prefix_cote` tinyblob NOT NULL,
  `xmlta_doctype` char(2) NOT NULL DEFAULT 'a',
  `xmlta_doctype_serial` varchar(2) NOT NULL DEFAULT '',
  `xmlta_doctype_bulletin` varchar(2) NOT NULL DEFAULT '',
  `xmlta_doctype_analysis` varchar(2) NOT NULL DEFAULT '',
  `speci_coordonnees_etab` mediumtext NOT NULL,
  `value_email_bcc` varchar(255) NOT NULL DEFAULT '',
  `value_deflt_antivol` varchar(50) NOT NULL DEFAULT '0',
  `explr_invisible` text DEFAULT NULL,
  `explr_visible_mod` text DEFAULT NULL,
  `explr_visible_unmod` text DEFAULT NULL,
  `deflt3bibli` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `deflt3exercice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `deflt3rubrique` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `deflt3type_produit` int(8) UNSIGNED DEFAULT 0,
  `deflt3dev_statut` int(3) NOT NULL DEFAULT -1,
  `deflt3cde_statut` int(3) NOT NULL DEFAULT -1,
  `deflt3liv_statut` int(3) NOT NULL DEFAULT -1,
  `deflt3fac_statut` int(3) NOT NULL DEFAULT -1,
  `deflt3sug_statut` int(3) NOT NULL DEFAULT -1,
  `environnement` mediumblob NOT NULL,
  `param_allloc` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `grp_num` int(10) UNSIGNED DEFAULT 0,
  `deflt_arch_statut` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_arch_emplacement` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_arch_type` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_upload_repertoire` int(8) NOT NULL DEFAULT 0,
  `deflt3lgstatdev` int(3) NOT NULL DEFAULT 1,
  `deflt3lgstatcde` int(3) NOT NULL DEFAULT 1,
  `deflt3receptsugstat` int(3) NOT NULL DEFAULT 32,
  `deflt_short_loan_activate` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_cashdesk` int(11) NOT NULL DEFAULT 0,
  `deflt_explnum_statut` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `user_alert_suggmail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_notice_replace_keep_categories` int(1) NOT NULL DEFAULT 0,
  `deflt_notice_is_new` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_agnostic_warehouse` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_cms_article_statut` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_cms_article_type` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_cms_section_type` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `deflt_scan_request_status` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `xmlta_doctype_scan_request_folder_record` varchar(2) NOT NULL DEFAULT 'a',
  `deflt_camera_empr` int(11) NOT NULL DEFAULT 0,
  `deflt_catalog_expanded_caddies` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `deflt_notice_replace_links` int(1) UNSIGNED DEFAULT 0,
  `deflt_printer` int(3) UNSIGNED DEFAULT 0,
  `deflt_opac_visible_bulletinage` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `grp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `grp_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`grp_id`),
  KEY `i_users_groups_grp_name` (`grp_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_api_key`
--

CREATE TABLE IF NOT EXISTS `user_api_key` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `api_key` char(32) NOT NULL,
  `api_service` char(10) NOT NULL DEFAULT 'dokeos',
  PRIMARY KEY (`id`),
  KEY `idx_user_api_keys_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_chat`
--

CREATE TABLE IF NOT EXISTS `user_chat` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_user` varchar(255) NOT NULL DEFAULT '',
  `to_user` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recd` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_course_category`
--

CREATE TABLE IF NOT EXISTS `user_course_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` text NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_field`
--

CREATE TABLE IF NOT EXISTS `user_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` int(11) NOT NULL DEFAULT 1,
  `field_variable` varchar(64) NOT NULL,
  `field_display_text` varchar(64) DEFAULT NULL,
  `field_default_value` text DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_visible` tinyint(4) DEFAULT 0,
  `field_changeable` tinyint(4) DEFAULT 0,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `field_filter` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `user_field`
--

INSERT INTO `user_field` VALUES
(1, 1, 'legal_accept', 'Legal', NULL, NULL, 0, 0, '2010-09-07 18:45:07', 0),
(2, 10, 'tags', 'tags', NULL, NULL, 0, 0, '2014-03-30 14:40:06', 0),
(3, 9, 'rssfeeds', 'RSS', NULL, NULL, 0, 0, '2014-03-30 14:40:06', 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_field_options`
--

CREATE TABLE IF NOT EXISTS `user_field_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `option_value` text DEFAULT NULL,
  `option_display_text` varchar(64) DEFAULT NULL,
  `option_order` int(11) DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_field_values`
--

CREATE TABLE IF NOT EXISTS `user_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_friend_relation_type`
--

CREATE TABLE IF NOT EXISTS `user_friend_relation_type` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `user_friend_relation_type`
--

INSERT INTO `user_friend_relation_type` VALUES
(1, 'SocialUnknow'),
(2, 'SocialParent'),
(3, 'SocialFriend'),
(4, 'SocialGoodFriend'),
(5, 'SocialEnemy'),
(6, 'SocialDeleted');

-- --------------------------------------------------------

--
-- Structure de la table `user_rel_tag`
--

CREATE TABLE IF NOT EXISTS `user_rel_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_rel_user`
--

CREATE TABLE IF NOT EXISTS `user_rel_user` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `friend_user_id` int(10) UNSIGNED NOT NULL,
  `relation_type` int(11) NOT NULL DEFAULT 0,
  `last_edit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_friend_user` (`user_id`),
  KEY `idx_user_friend_friend_user` (`friend_user_id`),
  KEY `idx_user_friend_user_friend_user` (`user_id`,`friend_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vedette`
--

CREATE TABLE IF NOT EXISTS `vedette` (
  `id_vedette` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `grammar` varchar(255) NOT NULL DEFAULT 'rameau',
  PRIMARY KEY (`id_vedette`),
  KEY `i_grammar` (`grammar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vedette_grammars_by_entity`
--

CREATE TABLE IF NOT EXISTS `vedette_grammars_by_entity` (
  `entity_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `grammar` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`entity_type`,`grammar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vedette_link`
--

CREATE TABLE IF NOT EXISTS `vedette_link` (
  `num_vedette` int(10) UNSIGNED NOT NULL,
  `num_object` int(10) UNSIGNED NOT NULL,
  `type_object` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`num_vedette`,`num_object`,`type_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vedette_object`
--

CREATE TABLE IF NOT EXISTS `vedette_object` (
  `object_type` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `object_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `num_vedette` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `subdivision` varchar(50) NOT NULL DEFAULT '',
  `position` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_available_field` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_type`,`object_id`,`num_vedette`,`subdivision`,`position`),
  KEY `i_vedette_object_object` (`object_type`,`object_id`),
  KEY `i_vedette_object_vedette` (`num_vedette`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vedette_schemes_by_entity`
--

CREATE TABLE IF NOT EXISTS `vedette_schemes_by_entity` (
  `entity_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scheme` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`entity_type`,`scheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `visionneuse_params`
--

CREATE TABLE IF NOT EXISTS `visionneuse_params` (
  `visionneuse_params_id` int(11) NOT NULL AUTO_INCREMENT,
  `visionneuse_params_class` varchar(255) NOT NULL DEFAULT '',
  `visionneuse_params_parameters` text NOT NULL,
  PRIMARY KEY (`visionneuse_params_id`),
  UNIQUE KEY `visionneuse_params_class` (`visionneuse_params_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `visio_abonnement`
--

CREATE TABLE IF NOT EXISTS `visio_abonnement` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan` varchar(20) NOT NULL DEFAULT 'starter',
  `statut` varchar(10) NOT NULL DEFAULT 'inactif',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `prix_mois` decimal(8,2) DEFAULT 0.00,
  `nb_max_participants` int(11) DEFAULT 6,
  `nb_max_salles` int(11) DEFAULT 2,
  `contact_facturation` varchar(200) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `visits_statistics`
--

CREATE TABLE IF NOT EXISTS `visits_statistics` (
  `visits_statistics_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `visits_statistics_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visits_statistics_location` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `visits_statistics_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`visits_statistics_id`),
  KEY `i_vs_visits_statistics_date` (`visits_statistics_date`),
  KEY `i_vs_visits_statistics_location_visits_statistics_type` (`visits_statistics_location`,`visits_statistics_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `voir_aussi`
--

CREATE TABLE IF NOT EXISTS `voir_aussi` (
  `num_noeud_orig` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_noeud_dest` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `langue` varchar(5) NOT NULL DEFAULT '',
  `comment_voir_aussi` text NOT NULL,
  PRIMARY KEY (`num_noeud_orig`,`num_noeud_dest`,`langue`),
  KEY `num_noeud_dest` (`num_noeud_dest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `words`
--

CREATE TABLE IF NOT EXISTS `words` (
  `id_word` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(10) NOT NULL DEFAULT '',
  `double_metaphone` varchar(255) NOT NULL DEFAULT '',
  `stem` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_word`),
  UNIQUE KEY `i_word_lang` (`word`,`lang`),
  KEY `i_stem_lang` (`stem`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `z_attr`
--

CREATE TABLE IF NOT EXISTS `z_attr` (
  `attr_bib_id` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `attr_libelle` varchar(250) NOT NULL DEFAULT '',
  `attr_attr` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`attr_bib_id`,`attr_libelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `z_attr`
--

INSERT INTO `z_attr` VALUES
(2, 'sujet', '21'),
(2, 'titre', '4'),
(2, 'auteur', '1003'),
(2, 'isbn', '7'),
(3, 'sujet', '21'),
(3, 'titre', '4'),
(3, 'isbn', '7'),
(3, 'auteur', '1003'),
(3, 'ismn', '9'),
(3, 'ean', '1214'),
(5, 'auteur', '1004'),
(5, 'titre', '4'),
(5, 'isbn', '7'),
(5, 'sujet', '21'),
(7, 'isbn', '7'),
(7, 'auteur', '1003'),
(7, 'titre', '4'),
(7, 'sujet', '21'),
(8, 'auteur', '1'),
(8, 'titre', '4'),
(8, 'isbn', '7'),
(8, 'sujet', '21'),
(8, 'mots', '1016'),
(10, 'auteur', '1003'),
(10, 'titre', '4'),
(10, 'isbn', '7'),
(10, 'sujet', '21'),
(12, 'sujet', '21'),
(12, 'auteur', '1003'),
(12, 'titre', '4'),
(12, 'isbn', '7'),
(11, 'sujet', '21'),
(11, 'auteur', '1003'),
(11, 'isbn', '7'),
(11, 'titre', '4'),
(15, 'auteur', '1003'),
(15, 'titre', '4'),
(15, 'isbn', '7'),
(15, 'sujet', '21'),
(17, 'sujet', '21'),
(17, 'auteur', '1003'),
(17, 'isbn', '7'),
(17, 'titre', '4'),
(21, 'sujet', '21'),
(21, 'auteur', '1003'),
(21, 'isbn', '7'),
(21, 'titre', '4');

-- --------------------------------------------------------

--
-- Structure de la table `z_bib`
--

CREATE TABLE IF NOT EXISTS `z_bib` (
  `bib_id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bib_nom` varchar(250) DEFAULT NULL,
  `search_type` varchar(20) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `port` varchar(6) DEFAULT NULL,
  `base` varchar(250) DEFAULT NULL,
  `format` varchar(250) DEFAULT NULL,
  `auth_user` varchar(250) NOT NULL DEFAULT '',
  `auth_pass` varchar(250) NOT NULL DEFAULT '',
  `sutrs_lang` varchar(10) NOT NULL DEFAULT '',
  `fichier_func` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`bib_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `z_bib`
--

INSERT INTO `z_bib` VALUES
(2, 'ENS Cachan', 'CATALOG', '138.231.48.2', '21210', 'ADVANCE', 'unimarc', '', '', '', ''),
(3, 'BN France', 'CATALOG', 'z3950.bnf.fr', '2211', 'TOUT-UTF8', 'UNIMARC', 'Z3950', 'Z3950_BNF', '', ''),
(5, 'Univ Lyon 2 SCD', 'CATALOG', 'scdinf.univ-lyon2.fr', '21210', 'ouvrages', 'unimarc', '', '', '', ''),
(7, 'Univ Oxford', 'CATALOG', 'library.ox.ac.uk', '210', 'ADVANCE', 'usmarc', '', '', '', ''),
(10, 'Univ Laval (QC)', 'CATALOG', 'ariane2.ulaval.ca', '2200', 'UNICORN', 'USMARC', '', '', '', ''),
(11, 'Univ Lib Edinburgh', 'CATALOG', 'catalogue.lib.ed.ac.uk', '7090', 'voyager', 'USMARC', '', '', '', ''),
(12, 'Library Of Congress', 'CATALOG', 'z3950.loc.gov', '7090', 'Voyager', 'USMARC', '', '', '', ''),
(15, 'ENS Paris', 'CATALOG', 'halley.ens.fr', '210', 'INNOPAC', 'UNIMARC', '', '', '', ''),
(17, 'Polytechnique Montréal', 'CATALOG', 'advance.biblio.polymtl.ca', '210', 'ADVANCE', 'USMARC', '', '', '', ''),
(21, 'SUDOC', 'CATALOG', 'carmin.sudoc.abes.fr', '210', 'ABES-Z39-PUBLIC', 'UNIMARC', '', '', '', ''),
(8, 'Univ Valenciennes', 'CATALOG', '195.221.187.151', '210', 'INNOPAC', 'UNIMARC', '', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `z_notices`
--

CREATE TABLE IF NOT EXISTS `z_notices` (
  `znotices_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `znotices_query_id` int(11) DEFAULT NULL,
  `znotices_bib_id` int(6) UNSIGNED DEFAULT 0,
  `isbd` text DEFAULT NULL,
  `isbn` varchar(250) DEFAULT NULL,
  `titre` varchar(250) DEFAULT NULL,
  `auteur` varchar(250) DEFAULT NULL,
  `z_marc` longblob NOT NULL,
  PRIMARY KEY (`znotices_id`),
  KEY `idx_z_notices_idq` (`znotices_query_id`),
  KEY `idx_z_notices_isbn` (`isbn`),
  KEY `idx_z_notices_titre` (`titre`),
  KEY `idx_z_notices_auteur` (`auteur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `z_query`
--

CREATE TABLE IF NOT EXISTS `z_query` (
  `zquery_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_attr` varchar(255) DEFAULT NULL,
  `zquery_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`zquery_id`),
  KEY `zquery_date` (`zquery_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `explnum`
--
ALTER TABLE `explnum` ADD FULLTEXT KEY `i_f_explnumwew` (`explnum_index_wew`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

