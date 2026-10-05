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
-- Structure de la table `abo_liste_lecture`
--

CREATE TABLE IF NOT EXISTS `abo_liste_lecture` (
  `num_empr` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_liste` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `etat` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`num_empr`,`num_liste`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_abts`
--

CREATE TABLE IF NOT EXISTS `abts_abts` (
  `abt_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `abt_name` varchar(255) NOT NULL DEFAULT '',
  `base_modele_name` varchar(255) NOT NULL DEFAULT '',
  `base_modele_id` int(11) NOT NULL DEFAULT 0,
  `num_notice` int(11) NOT NULL DEFAULT 0,
  `date_debut` date NOT NULL DEFAULT '0000-00-00',
  `date_fin` date NOT NULL DEFAULT '0000-00-00',
  `fournisseur` int(11) NOT NULL DEFAULT 0,
  `destinataire` varchar(255) NOT NULL DEFAULT '',
  `cote` varchar(255) NOT NULL DEFAULT '',
  `typdoc_id` int(11) NOT NULL DEFAULT 0,
  `exemp_auto` int(11) NOT NULL DEFAULT 0,
  `location_id` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `lender_id` int(11) NOT NULL DEFAULT 0,
  `statut_id` int(11) NOT NULL DEFAULT 0,
  `codestat_id` int(11) NOT NULL DEFAULT 0,
  `type_antivol` int(11) NOT NULL DEFAULT 0,
  `duree_abonnement` int(11) NOT NULL DEFAULT 0,
  `abt_numeric` int(1) NOT NULL DEFAULT 0,
  `prix` varchar(255) NOT NULL DEFAULT '',
  `abt_status` int(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`abt_id`),
  KEY `index_num_notice` (`num_notice`),
  KEY `i_date_fin` (`date_fin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_abts_modeles`
--

CREATE TABLE IF NOT EXISTS `abts_abts_modeles` (
  `modele_id` int(11) NOT NULL DEFAULT 0,
  `abt_id` int(11) NOT NULL DEFAULT 0,
  `num` int(11) NOT NULL DEFAULT 0,
  `vol` int(11) NOT NULL DEFAULT 0,
  `tome` int(11) NOT NULL DEFAULT 0,
  `delais` int(11) NOT NULL DEFAULT 0,
  `critique` int(11) NOT NULL DEFAULT 0,
  `num_statut_general` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`modele_id`,`abt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_grille_abt`
--

CREATE TABLE IF NOT EXISTS `abts_grille_abt` (
  `id_bull` int(11) NOT NULL AUTO_INCREMENT,
  `num_abt` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_parution` date NOT NULL DEFAULT '0000-00-00',
  `modele_id` int(11) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL DEFAULT 0,
  `nombre` int(11) NOT NULL DEFAULT 0,
  `numero` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `state` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_bull`),
  KEY `num_abt` (`num_abt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_grille_modele`
--

CREATE TABLE IF NOT EXISTS `abts_grille_modele` (
  `num_modele` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_parution` date NOT NULL DEFAULT '0000-00-00',
  `type_serie` int(11) NOT NULL DEFAULT 0,
  `numero` varchar(50) NOT NULL DEFAULT '',
  `nombre_recu` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`num_modele`,`date_parution`,`type_serie`),
  KEY `num_modele` (`num_modele`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_modeles`
--

CREATE TABLE IF NOT EXISTS `abts_modeles` (
  `modele_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `modele_name` varchar(255) NOT NULL DEFAULT '',
  `num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_periodicite` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duree_abonnement` int(11) NOT NULL DEFAULT 0,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `days` varchar(7) NOT NULL DEFAULT '1111111',
  `day_month` varchar(31) NOT NULL DEFAULT '1111111111111111111111111111111',
  `week_month` varchar(6) NOT NULL DEFAULT '111111',
  `week_year` varchar(54) NOT NULL DEFAULT '111111111111111111111111111111111111111111111111111111',
  `month_year` varchar(12) NOT NULL DEFAULT '111111111111',
  `num_cycle` int(11) NOT NULL DEFAULT 0,
  `num_combien` int(11) NOT NULL DEFAULT 0,
  `num_increment` int(11) NOT NULL DEFAULT 0,
  `num_date_unite` int(11) NOT NULL DEFAULT 0,
  `num_increment_date` int(11) NOT NULL DEFAULT 0,
  `num_depart` int(11) NOT NULL DEFAULT 0,
  `vol_actif` int(11) NOT NULL DEFAULT 0,
  `vol_increment` int(11) NOT NULL DEFAULT 0,
  `vol_date_unite` int(11) NOT NULL DEFAULT 0,
  `vol_increment_numero` int(11) NOT NULL DEFAULT 0,
  `vol_increment_date` int(11) NOT NULL DEFAULT 0,
  `vol_cycle` int(11) NOT NULL DEFAULT 0,
  `vol_combien` int(11) NOT NULL DEFAULT 0,
  `vol_depart` int(11) NOT NULL DEFAULT 0,
  `tom_actif` int(11) NOT NULL DEFAULT 0,
  `tom_increment` int(11) NOT NULL DEFAULT 0,
  `tom_date_unite` int(11) NOT NULL DEFAULT 0,
  `tom_increment_numero` int(11) NOT NULL DEFAULT 0,
  `tom_increment_date` int(11) NOT NULL DEFAULT 0,
  `tom_cycle` int(11) NOT NULL DEFAULT 0,
  `tom_combien` int(11) NOT NULL DEFAULT 0,
  `tom_depart` int(11) NOT NULL DEFAULT 0,
  `format_aff` varchar(255) NOT NULL DEFAULT '',
  `format_periode` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`modele_id`),
  KEY `num_notice` (`num_notice`),
  KEY `num_periodicite` (`num_periodicite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `abts_periodicites`
--

CREATE TABLE IF NOT EXISTS `abts_periodicites` (
  `periodicite_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `duree` int(11) NOT NULL DEFAULT 0,
  `unite` int(11) NOT NULL DEFAULT 0,
  `retard_periodicite` int(4) DEFAULT 0,
  `seuil_periodicite` int(4) DEFAULT 0,
  `consultation_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`periodicite_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `abts_periodicites`
--

INSERT INTO `abts_periodicites` VALUES
(5, 'Bimestriel', 2, 1, 30, 10, 0),
(4, 'Mensuel', 1, 1, 15, 5, 0),
(3, 'Bimensuel', 14, 0, 10, 5, 0),
(2, 'Hebdomadaire', 7, 0, 4, 2, 0),
(1, 'Quotidien', 1, 0, 2, 1, 0),
(6, 'Trimestriel', 3, 1, 30, 10, 0),
(7, 'Quadrimestriel', 4, 1, 30, 10, 0),
(8, 'Semestriel', 6, 1, 30, 10, 0),
(9, 'Annuel', 1, 2, 30, 10, 0);

-- --------------------------------------------------------

--
-- Structure de la table `abts_status`
--

CREATE TABLE IF NOT EXISTS `abts_status` (
  `abts_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `abts_status_gestion_libelle` varchar(255) NOT NULL DEFAULT '',
  `abts_status_opac_libelle` varchar(255) NOT NULL DEFAULT '',
  `abts_status_class_html` varchar(255) NOT NULL DEFAULT '',
  `abts_status_bulletinage_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`abts_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `abts_status`
--

INSERT INTO `abts_status` VALUES
(1, 'Statut par défaut', 'Statut par défaut', 'statutnot1', 1);

-- --------------------------------------------------------

--
-- Structure de la table `access_url`
--

CREATE TABLE IF NOT EXISTS `access_url` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `access_url_rel_course`
--

CREATE TABLE IF NOT EXISTS `access_url_rel_course` (
  `access_url_id` int(10) UNSIGNED NOT NULL,
  `course_code` char(40) NOT NULL,
  PRIMARY KEY (`access_url_id`,`course_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `access_url_rel_session`
--

CREATE TABLE IF NOT EXISTS `access_url_rel_session` (
  `access_url_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`access_url_id`,`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `access_url_rel_user`
--

CREATE TABLE IF NOT EXISTS `access_url_rel_user` (
  `access_url_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`access_url_id`,`user_id`),
  KEY `idx_access_url_rel_user_user` (`user_id`),
  KEY `idx_access_url_rel_user_access_url` (`access_url_id`),
  KEY `idx_access_url_rel_user_access_url_user` (`user_id`,`access_url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `access_url_rel_user`
--

INSERT INTO `access_url_rel_user` VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `acces_profiles`
--

CREATE TABLE IF NOT EXISTS `acces_profiles` (
  `prf_id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prf_type` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `prf_name` varchar(255) NOT NULL,
  `prf_rule` blob NOT NULL,
  `prf_hrule` text NOT NULL,
  `prf_used` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `dom_num` int(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`prf_id`),
  KEY `prf_type` (`prf_type`),
  KEY `prf_name` (`prf_name`),
  KEY `dom_num` (`dom_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `acces_rights`
--

CREATE TABLE IF NOT EXISTS `acces_rights` (
  `dom_num` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `usr_prf_num` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `res_prf_num` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `dom_rights` int(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`dom_num`,`usr_prf_num`,`res_prf_num`),
  KEY `dom_num` (`dom_num`),
  KEY `usr_prf_num` (`usr_prf_num`),
  KEY `res_prf_num` (`res_prf_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `actes`
--

CREATE TABLE IF NOT EXISTS `actes` (
  `id_acte` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_acte` date NOT NULL DEFAULT '0000-00-00',
  `numero` varchar(255) NOT NULL DEFAULT '',
  `type_acte` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_paiement` date NOT NULL DEFAULT '0000-00-00',
  `num_paiement` varchar(255) NOT NULL DEFAULT '',
  `num_entite` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_fournisseur` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_contact_livr` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_contact_fact` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_exercice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `commentaires` text NOT NULL,
  `reference` varchar(255) NOT NULL DEFAULT '',
  `index_acte` text NOT NULL,
  `devise` varchar(25) NOT NULL DEFAULT '',
  `commentaires_i` text NOT NULL,
  `date_valid` date NOT NULL DEFAULT '0000-00-00',
  `date_ech` date NOT NULL DEFAULT '0000-00-00',
  `nom_acte` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_acte`),
  KEY `num_fournisseur` (`num_fournisseur`),
  KEY `date` (`date_acte`),
  KEY `num_entite` (`num_entite`),
  KEY `numero` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admin_session`
--

CREATE TABLE IF NOT EXISTS `admin_session` (
  `userid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `session` mediumblob DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adodb_logsql`
--

CREATE TABLE IF NOT EXISTS `adodb_logsql` (
  `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `sql0` varchar(250) NOT NULL DEFAULT '',
  `sql1` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  `tracer` text DEFAULT NULL,
  `timer` decimal(16,6) NOT NULL DEFAULT 0.000000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `analysis`
--

CREATE TABLE IF NOT EXISTS `analysis` (
  `analysis_bulletin` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `analysis_notice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`analysis_bulletin`,`analysis_notice`),
  KEY `analysis_notice` (`analysis_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `arch_emplacement`
--

CREATE TABLE IF NOT EXISTS `arch_emplacement` (
  `archempla_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `archempla_libelle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`archempla_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `arch_statut`
--

CREATE TABLE IF NOT EXISTS `arch_statut` (
  `archstatut_id` int(8) NOT NULL AUTO_INCREMENT,
  `archstatut_gestion_libelle` varchar(255) NOT NULL DEFAULT '',
  `archstatut_opac_libelle` varchar(255) NOT NULL,
  `archstatut_visible_opac` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archstatut_visible_opac_abon` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archstatut_visible_gestion` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `archstatut_class_html` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`archstatut_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `arch_type`
--

CREATE TABLE IF NOT EXISTS `arch_type` (
  `archtype_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `archtype_libelle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`archtype_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit`
--

CREATE TABLE IF NOT EXISTS `audit` (
  `type_obj` int(1) NOT NULL DEFAULT 0,
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `type_modif` int(1) NOT NULL DEFAULT 1,
  `quand` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type_user` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `info` text NOT NULL,
  KEY `type_obj` (`type_obj`),
  KEY `object_id` (`object_id`),
  KEY `user_id` (`user_id`),
  KEY `type_modif` (`type_modif`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities`
--

CREATE TABLE IF NOT EXISTS `authorities` (
  `id_authority` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_object` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `type_object` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_statut` int(2) UNSIGNED NOT NULL DEFAULT 1,
  `thumbnail_url` mediumblob NOT NULL,
  PRIMARY KEY (`id_authority`),
  UNIQUE KEY `i_a_num_object_type_object` (`num_object`,`type_object`),
  KEY `i_a_num_statut` (`num_statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_caddie`
--

CREATE TABLE IF NOT EXISTS `authorities_caddie` (
  `idcaddie` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `caddie_classement` varchar(255) NOT NULL DEFAULT '',
  `acces_rapide` int(11) NOT NULL DEFAULT 0,
  `favorite_color` varchar(255) NOT NULL DEFAULT '',
  `creation_user_name` varchar(255) NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idcaddie`),
  KEY `caddie_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_caddie_content`
--

CREATE TABLE IF NOT EXISTS `authorities_caddie_content` (
  `caddie_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `flag` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`caddie_id`,`object_id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_caddie_procs`
--

CREATE TABLE IF NOT EXISTS `authorities_caddie_procs` (
  `idproc` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'SELECT',
  `name` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `comment` tinytext NOT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `parameters` text DEFAULT NULL,
  PRIMARY KEY (`idproc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_fields_global_index`
--

CREATE TABLE IF NOT EXISTS `authorities_fields_global_index` (
  `id_authority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(10) NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) NOT NULL DEFAULT 0,
  `ordre` int(4) NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  `pond` int(4) NOT NULL DEFAULT 100,
  `lang` varchar(10) NOT NULL DEFAULT '',
  `authority_num` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_authority`,`code_champ`,`code_ss_champ`,`ordre`),
  KEY `i_value` (`value`(300)),
  KEY `i_id_value` (`id_authority`,`value`(300)),
  KEY `i_code_champ_code_ss_champ` (`code_champ`,`code_ss_champ`),
  KEY `i_id_authority` (`id_authority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_sources`
--

CREATE TABLE IF NOT EXISTS `authorities_sources` (
  `id_authority_source` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_authority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authority_number` varchar(50) NOT NULL DEFAULT '',
  `authority_type` varchar(20) NOT NULL DEFAULT '',
  `num_origin_authority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authority_favorite` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `import_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_authority_source`),
  KEY `i_num_authority_authority_type` (`num_authority`,`authority_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authorities_statuts`
--

CREATE TABLE IF NOT EXISTS `authorities_statuts` (
  `id_authorities_statut` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `authorities_statut_label` varchar(255) NOT NULL DEFAULT '',
  `authorities_statut_class_html` varchar(25) NOT NULL DEFAULT '',
  `authorities_statut_available_for` text DEFAULT NULL,
  PRIMARY KEY (`id_authorities_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `authorities_statuts`
--

INSERT INTO `authorities_statuts` VALUES
(1, 'Statut par défaut', 'statutnot1', 'a:9:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";i:5;s:1:\"6\";i:6;s:1:\"8\";i:7;s:1:\"7\";i:8;s:2:\"10\";}');

-- --------------------------------------------------------

--
-- Structure de la table `authorities_words_global_index`
--

CREATE TABLE IF NOT EXISTS `authorities_words_global_index` (
  `id_authority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_ss_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_word` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `field_position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_authority`,`code_champ`,`code_ss_champ`,`num_word`,`position`,`field_position`),
  KEY `code_champ` (`code_champ`),
  KEY `i_id_mot` (`num_word`,`id_authority`),
  KEY `i_code_champ_code_ss_champ_num_word` (`code_champ`,`code_ss_champ`,`num_word`),
  KEY `i_num_word` (`num_word`),
  KEY `i_id_authority` (`id_authority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `author_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_type` enum('70','71','72') NOT NULL DEFAULT '70',
  `author_name` varchar(255) NOT NULL DEFAULT '',
  `author_rejete` varchar(255) NOT NULL DEFAULT '',
  `author_date` varchar(255) NOT NULL DEFAULT '',
  `author_see` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `author_web` varchar(255) NOT NULL DEFAULT '',
  `index_author` text DEFAULT NULL,
  `author_comment` text DEFAULT NULL,
  `author_lieu` varchar(255) NOT NULL DEFAULT '',
  `author_ville` varchar(255) NOT NULL DEFAULT '',
  `author_pays` varchar(255) NOT NULL DEFAULT '',
  `author_subdivision` varchar(255) NOT NULL DEFAULT '',
  `author_numero` varchar(50) NOT NULL DEFAULT '',
  `author_import_denied` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_isni` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`author_id`),
  KEY `author_see` (`author_see`),
  KEY `author_name` (`author_name`),
  KEY `author_rejete` (`author_rejete`),
  KEY `i_author_type` (`author_type`),
  KEY `i_index_author_author_type` (`index_author`(333),`author_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `author_custom`
--

CREATE TABLE IF NOT EXISTS `author_custom` (
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
-- Structure de la table `author_custom_dates`
--

CREATE TABLE IF NOT EXISTS `author_custom_dates` (
  `author_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_custom_date_type` int(11) DEFAULT NULL,
  `author_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `author_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `author_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`author_custom_champ`,`author_custom_origine`,`author_custom_order`),
  KEY `author_custom_champ` (`author_custom_champ`),
  KEY `author_custom_origine` (`author_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `author_custom_lists`
--

CREATE TABLE IF NOT EXISTS `author_custom_lists` (
  `author_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_custom_list_value` varchar(255) DEFAULT NULL,
  `author_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`author_custom_champ`),
  KEY `editorial_champ_list_value` (`author_custom_champ`,`author_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `author_custom_values`
--

CREATE TABLE IF NOT EXISTS `author_custom_values` (
  `author_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `author_custom_small_text` varchar(255) DEFAULT NULL,
  `author_custom_text` text DEFAULT NULL,
  `author_custom_integer` int(11) DEFAULT NULL,
  `author_custom_date` date DEFAULT NULL,
  `author_custom_float` float DEFAULT NULL,
  `author_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`author_custom_champ`),
  KEY `editorial_custom_origine` (`author_custom_origine`),
  KEY `i_acv_st` (`author_custom_small_text`),
  KEY `i_acv_t` (`author_custom_text`(255)),
  KEY `i_acv_i` (`author_custom_integer`),
  KEY `i_acv_d` (`author_custom_date`),
  KEY `i_acv_f` (`author_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authperso`
--

CREATE TABLE IF NOT EXISTS `authperso` (
  `id_authperso` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `authperso_name` varchar(255) NOT NULL DEFAULT '',
  `authperso_notice_onglet_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_isbd_script` text NOT NULL,
  `authperso_view_script` text NOT NULL,
  `authperso_opac_search` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_opac_multi_search` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_gestion_search` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_gestion_multi_search` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_comment` text NOT NULL,
  `authperso_oeuvre_event` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_authperso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authperso_authorities`
--

CREATE TABLE IF NOT EXISTS `authperso_authorities` (
  `id_authperso_authority` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `authperso_authority_authperso_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_infos_global` text NOT NULL,
  `authperso_index_infos_global` text NOT NULL,
  PRIMARY KEY (`id_authperso_authority`),
  KEY `i_authperso_authority_authperso_num` (`authperso_authority_authperso_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authperso_custom`
--

CREATE TABLE IF NOT EXISTS `authperso_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `custom_prefixe` varchar(255) NOT NULL DEFAULT '',
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
-- Structure de la table `authperso_custom_dates`
--

CREATE TABLE IF NOT EXISTS `authperso_custom_dates` (
  `authperso_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_custom_date_type` int(11) DEFAULT NULL,
  `authperso_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `authperso_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `authperso_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`authperso_custom_champ`,`authperso_custom_origine`,`authperso_custom_order`),
  KEY `authperso_custom_champ` (`authperso_custom_champ`),
  KEY `authperso_custom_origine` (`authperso_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authperso_custom_lists`
--

CREATE TABLE IF NOT EXISTS `authperso_custom_lists` (
  `authperso_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_custom_list_value` varchar(255) DEFAULT NULL,
  `authperso_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`authperso_custom_champ`),
  KEY `editorial_champ_list_value` (`authperso_custom_champ`,`authperso_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `authperso_custom_values`
--

CREATE TABLE IF NOT EXISTS `authperso_custom_values` (
  `authperso_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `authperso_custom_small_text` varchar(255) DEFAULT NULL,
  `authperso_custom_text` text DEFAULT NULL,
  `authperso_custom_integer` int(11) DEFAULT NULL,
  `authperso_custom_date` date DEFAULT NULL,
  `authperso_custom_float` float DEFAULT NULL,
  `authperso_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`authperso_custom_champ`),
  KEY `editorial_custom_origine` (`authperso_custom_origine`),
  KEY `i_acv_st` (`authperso_custom_small_text`),
  KEY `i_acv_t` (`authperso_custom_text`(255)),
  KEY `i_acv_i` (`authperso_custom_integer`),
  KEY `i_acv_d` (`authperso_custom_date`),
  KEY `i_acv_f` (`authperso_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `aut_link`
--

CREATE TABLE IF NOT EXISTS `aut_link` (
  `aut_link_from` int(2) NOT NULL DEFAULT 0,
  `aut_link_from_num` int(11) NOT NULL DEFAULT 0,
  `aut_link_to` int(2) NOT NULL DEFAULT 0,
  `aut_link_to_num` int(11) NOT NULL DEFAULT 0,
  `aut_link_type` varchar(2) NOT NULL DEFAULT '',
  `aut_link_reciproc` int(1) NOT NULL DEFAULT 0,
  `aut_link_comment` varchar(255) NOT NULL DEFAULT '',
  `aut_link_string_start_date` varchar(255) NOT NULL DEFAULT '',
  `aut_link_string_end_date` varchar(255) NOT NULL DEFAULT '',
  `aut_link_start_date` date NOT NULL DEFAULT '0000-00-00',
  `aut_link_end_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`aut_link_from`,`aut_link_from_num`,`aut_link_to`,`aut_link_to_num`,`aut_link_type`),
  KEY `i_from` (`aut_link_from`,`aut_link_from_num`),
  KEY `i_to` (`aut_link_to`,`aut_link_to_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE IF NOT EXISTS `avis` (
  `id_avis` mediumint(8) NOT NULL AUTO_INCREMENT,
  `num_empr` mediumint(8) NOT NULL DEFAULT 0,
  `num_notice` mediumint(8) NOT NULL DEFAULT 0,
  `type_object` mediumint(8) NOT NULL,
  `note` int(3) DEFAULT NULL,
  `sujet` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `dateajout` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valide` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `avis_rank` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `avis_private` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `avis_num_liste_lecture` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_avis`),
  KEY `avis_num_notice` (`num_notice`),
  KEY `avis_num_empr` (`num_empr`),
  KEY `avis_note` (`note`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannettes`
--

CREATE TABLE IF NOT EXISTS `bannettes` (
  `id_bannette` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_classement` int(8) UNSIGNED NOT NULL DEFAULT 1,
  `nom_bannette` varchar(255) NOT NULL DEFAULT '',
  `comment_gestion` text NOT NULL,
  `comment_public` text NOT NULL,
  `entete_mail` text NOT NULL,
  `date_last_remplissage` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_envoi` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `proprio_bannette` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_auto` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `periodicite` int(3) UNSIGNED NOT NULL DEFAULT 7,
  `diffusion_email` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `categorie_lecteurs` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `nb_notices_diff` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `num_panier` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `limite_type` char(1) NOT NULL DEFAULT '',
  `limite_nombre` int(6) NOT NULL DEFAULT 0,
  `update_type` char(1) NOT NULL DEFAULT 'C',
  `typeexport` varchar(20) NOT NULL DEFAULT '',
  `prefixe_fichier` varchar(50) NOT NULL DEFAULT '',
  `param_export` blob NOT NULL,
  `piedpage_mail` text NOT NULL,
  `notice_tpl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `group_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `group_pperso` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `display_notice_in_every_group` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `statut_not_account` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `archive_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `document_generate` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `document_notice_tpl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `document_insert_docnum` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `document_group` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `document_add_summary` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupe_lecteurs` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_opac_accueil` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_tpl_num` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_aff_notice_number` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `associated_campaign` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_num_sender` int(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_bannette`),
  KEY `i_bannette_tpl_num` (`bannette_tpl_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannettes_descriptors`
--

CREATE TABLE IF NOT EXISTS `bannettes_descriptors` (
  `num_bannette` int(11) NOT NULL DEFAULT 0,
  `num_noeud` int(11) NOT NULL DEFAULT 0,
  `bannette_descriptor_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_bannette`,`num_noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_abon`
--

CREATE TABLE IF NOT EXISTS `bannette_abon` (
  `num_bannette` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_empr` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `actif` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `bannette_mail` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`num_bannette`,`num_empr`),
  KEY `i_num_empr` (`num_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_contenu`
--

CREATE TABLE IF NOT EXISTS `bannette_contenu` (
  `num_bannette` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_notice` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`num_bannette`,`num_notice`),
  KEY `date_ajout` (`date_ajout`),
  KEY `i_num_notice` (`num_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_empr_categs`
--

CREATE TABLE IF NOT EXISTS `bannette_empr_categs` (
  `empr_categ_num_bannette` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_categ_num_categ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`empr_categ_num_bannette`,`empr_categ_num_categ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_empr_groupes`
--

CREATE TABLE IF NOT EXISTS `bannette_empr_groupes` (
  `empr_groupe_num_bannette` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_groupe_num_groupe` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`empr_groupe_num_bannette`,`empr_groupe_num_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_equation`
--

CREATE TABLE IF NOT EXISTS `bannette_equation` (
  `num_bannette` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_equation` int(9) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_bannette`,`num_equation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_exports`
--

CREATE TABLE IF NOT EXISTS `bannette_exports` (
  `num_bannette` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `export_format` int(3) NOT NULL DEFAULT 0,
  `export_data` longblob NOT NULL,
  `export_nomfichier` varchar(255) DEFAULT '',
  PRIMARY KEY (`num_bannette`,`export_format`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_facettes`
--

CREATE TABLE IF NOT EXISTS `bannette_facettes` (
  `num_ban_facette` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ban_facette_critere` int(5) NOT NULL DEFAULT 0,
  `ban_facette_ss_critere` int(5) NOT NULL DEFAULT 0,
  `ban_facette_order` int(1) NOT NULL DEFAULT 0,
  KEY `bannette_facettes_key` (`num_ban_facette`,`ban_facette_critere`,`ban_facette_ss_critere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bannette_tpl`
--

CREATE TABLE IF NOT EXISTS `bannette_tpl` (
  `bannettetpl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bannettetpl_name` varchar(255) NOT NULL DEFAULT '',
  `bannettetpl_comment` varchar(255) NOT NULL DEFAULT '',
  `bannettetpl_tpl` text NOT NULL,
  PRIMARY KEY (`bannettetpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `bannette_tpl`
--

INSERT INTO `bannette_tpl` VALUES
(1, 'Template PMB', '', '{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n    <ul>\r\n        {% for sommaire in sommaires %}\r\n            {% if sommaire.level==1 %}\r\n                <li>\r\n                    <a href=\"#[{{loop.counter}}]\">{{sommaire.title}}</a>\r\n                </li>\r\n            {% endif %}\r\n        {% endfor %}\n			    		\r\n    </ul>\r\n</div>\r\n{% for sommaire in sommaires %}\r\n    {% if sommaire.level==1 %}\r\n        <h2 class=\"dsi_rang_1\"><a name=\"[{{loop.counter}}]\"></a>{{sommaire.title}}</h2>\r\n    {% endif %}\r\n    {% if sommaire.level==2 %}\r\n        <h3 class=\"dsi_rang_2\">{{sommaire.title}}</h3>\r\n    {% endif %}\r\n    {% if sommaire.level==3 %}\r\n        <h4 class=\"dsi_rang_3\">{{sommaire.title}}</h4>\n			    		\r\n    {% endif %}\r\n    {% for record in sommaire.records %}\r\n        {{record.render}}\r\n    {% endfor %}\n			    		\r\n    <br />\r\n{% endfor %}\r\n{{info.footer}}');

-- --------------------------------------------------------

--
-- Structure de la table `budgets`
--

CREATE TABLE IF NOT EXISTS `budgets` (
  `id_budget` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_entite` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_exercice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `commentaires` text DEFAULT NULL,
  `montant_global` double(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `seuil_alerte` int(3) UNSIGNED NOT NULL DEFAULT 100,
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `type_budget` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_budget`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bulletins`
--

CREATE TABLE IF NOT EXISTS `bulletins` (
  `bulletin_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bulletin_numero` varchar(255) NOT NULL DEFAULT '',
  `bulletin_notice` int(8) NOT NULL DEFAULT 0,
  `mention_date` varchar(255) NOT NULL DEFAULT '',
  `date_date` date NOT NULL DEFAULT '0000-00-00',
  `bulletin_titre` text DEFAULT NULL,
  `index_titre` text DEFAULT NULL,
  `bulletin_cb` varchar(30) DEFAULT NULL,
  `num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`bulletin_id`),
  KEY `bulletin_numero` (`bulletin_numero`),
  KEY `bulletin_notice` (`bulletin_notice`),
  KEY `date_date` (`date_date`),
  KEY `i_num_notice` (`num_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_amendes`
--

CREATE TABLE IF NOT EXISTS `cache_amendes` (
  `id_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cache_date` date NOT NULL DEFAULT '0000-00-00',
  `data_amendes` blob NOT NULL,
  KEY `id_empr` (`id_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `caddie`
--

CREATE TABLE IF NOT EXISTS `caddie` (
  `idcaddie` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'NOTI',
  `comment` varchar(255) DEFAULT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `caddie_classement` varchar(255) NOT NULL DEFAULT '',
  `acces_rapide` int(11) NOT NULL DEFAULT 0,
  `favorite_color` varchar(255) NOT NULL DEFAULT '',
  `creation_user_name` varchar(255) NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idcaddie`),
  KEY `caddie_type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `caddie_content`
--

CREATE TABLE IF NOT EXISTS `caddie_content` (
  `caddie_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `content` varchar(100) NOT NULL DEFAULT '',
  `blob_type` varchar(100) DEFAULT '',
  `flag` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`caddie_id`,`object_id`,`content`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `caddie_procs`
--

CREATE TABLE IF NOT EXISTS `caddie_procs` (
  `idproc` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'SELECT',
  `name` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `comment` tinytext NOT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `parameters` text DEFAULT NULL,
  PRIMARY KEY (`idproc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns`
--

CREATE TABLE IF NOT EXISTS `campaigns` (
  `id_campaign` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_type` varchar(255) NOT NULL DEFAULT '',
  `campaign_label` varchar(255) NOT NULL DEFAULT '',
  `campaign_date` datetime DEFAULT NULL,
  `campaign_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns_descriptors`
--

CREATE TABLE IF NOT EXISTS `campaigns_descriptors` (
  `num_campaign` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_noeud` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `campaign_descriptor_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_campaign`,`num_noeud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns_logs`
--

CREATE TABLE IF NOT EXISTS `campaigns_logs` (
  `campaign_log_num_campaign` int(11) NOT NULL DEFAULT 0,
  `campaign_log_num_recipient` int(11) NOT NULL DEFAULT 0,
  `campaign_log_hash` varchar(255) NOT NULL DEFAULT '',
  `campaign_log_url` varchar(255) NOT NULL DEFAULT '',
  `campaign_log_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `i_campaign_log_num_campaign` (`campaign_log_num_campaign`),
  KEY `i_campaign_log_num_recipient` (`campaign_log_num_recipient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns_recipients`
--

CREATE TABLE IF NOT EXISTS `campaigns_recipients` (
  `id_campaign_recipient` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_recipient_hash` varchar(255) NOT NULL DEFAULT '',
  `campaign_recipient_num_campaign` int(11) NOT NULL DEFAULT 0,
  `campaign_recipient_num_empr` int(11) NOT NULL DEFAULT 0,
  `campaign_recipient_empr_cp` varchar(5) NOT NULL DEFAULT '',
  `campaign_recipient_empr_ville` varchar(255) NOT NULL DEFAULT '',
  `campaign_recipient_empr_prof` varchar(255) NOT NULL DEFAULT '',
  `campaign_recipient_empr_year` int(11) NOT NULL DEFAULT 0,
  `campaign_recipient_empr_categ` smallint(5) UNSIGNED DEFAULT 0,
  `campaign_recipient_empr_codestat` smallint(5) UNSIGNED DEFAULT 0,
  `campaign_recipient_empr_sexe` tinyint(3) UNSIGNED DEFAULT 0,
  `campaign_recipient_empr_statut` bigint(20) UNSIGNED DEFAULT 0,
  `campaign_recipient_empr_location` int(6) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id_campaign_recipient`),
  KEY `i_campaign_recipient_num_campaign` (`campaign_recipient_num_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns_stats`
--

CREATE TABLE IF NOT EXISTS `campaigns_stats` (
  `campaign_stat_num_campaign` int(11) NOT NULL DEFAULT 0,
  `campaign_stat_data` text NOT NULL,
  `campaign_stat_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`campaign_stat_num_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns_tags`
--

CREATE TABLE IF NOT EXISTS `campaigns_tags` (
  `num_campaign` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_tag` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `campaign_tag_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_campaign`,`num_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cashdesk`
--

CREATE TABLE IF NOT EXISTS `cashdesk` (
  `cashdesk_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cashdesk_name` varchar(255) NOT NULL DEFAULT '',
  `cashdesk_autorisations` varchar(255) NOT NULL DEFAULT '',
  `cashdesk_transactypes` varchar(255) NOT NULL DEFAULT '',
  `cashdesk_cashbox` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cashdesk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cashdesk_locations`
--

CREATE TABLE IF NOT EXISTS `cashdesk_locations` (
  `cashdesk_loc_cashdesk_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cashdesk_loc_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cashdesk_loc_cashdesk_num`,`cashdesk_loc_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cashdesk_sections`
--

CREATE TABLE IF NOT EXISTS `cashdesk_sections` (
  `cashdesk_section_cashdesk_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cashdesk_section_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cashdesk_section_cashdesk_num`,`cashdesk_section_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `num_thesaurus` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `num_noeud` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `langue` varchar(5) NOT NULL DEFAULT 'fr_FR',
  `libelle_categorie` text NOT NULL,
  `note_application` text NOT NULL,
  `comment_public` text NOT NULL,
  `comment_voir` text NOT NULL,
  `index_categorie` text NOT NULL,
  `path_word_categ` text NOT NULL,
  `index_path_word_categ` text NOT NULL,
  PRIMARY KEY (`num_noeud`,`langue`),
  KEY `categ_langue` (`langue`),
  KEY `libelle_categorie` (`libelle_categorie`(5)),
  KEY `i_num_thesaurus` (`num_thesaurus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` VALUES
(1, 3, 'fr_FR', '~termes non classés', '', '', '', ' termes non classes ', '', ''),
(1, 2, 'fr_FR', '~termes orphelins', '', '', '', ' termes orphelins ', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `categ_custom`
--

CREATE TABLE IF NOT EXISTS `categ_custom` (
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
-- Structure de la table `categ_custom_dates`
--

CREATE TABLE IF NOT EXISTS `categ_custom_dates` (
  `categ_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_custom_date_type` int(11) DEFAULT NULL,
  `categ_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `categ_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `categ_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`categ_custom_champ`,`categ_custom_origine`,`categ_custom_order`),
  KEY `categ_custom_champ` (`categ_custom_champ`),
  KEY `categ_custom_origine` (`categ_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categ_custom_lists`
--

CREATE TABLE IF NOT EXISTS `categ_custom_lists` (
  `categ_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_custom_list_value` varchar(255) DEFAULT NULL,
  `categ_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`categ_custom_champ`),
  KEY `editorial_champ_list_value` (`categ_custom_champ`,`categ_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categ_custom_values`
--

CREATE TABLE IF NOT EXISTS `categ_custom_values` (
  `categ_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_custom_small_text` varchar(255) DEFAULT NULL,
  `categ_custom_text` text DEFAULT NULL,
  `categ_custom_integer` int(11) DEFAULT NULL,
  `categ_custom_date` date DEFAULT NULL,
  `categ_custom_float` float DEFAULT NULL,
  `categ_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`categ_custom_champ`),
  KEY `editorial_custom_origine` (`categ_custom_origine`),
  KEY `i_ccv_st` (`categ_custom_small_text`),
  KEY `i_ccv_t` (`categ_custom_text`(255)),
  KEY `i_ccv_i` (`categ_custom_integer`),
  KEY `i_ccv_d` (`categ_custom_date`),
  KEY `i_ccv_f` (`categ_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `chat_groups`
--

CREATE TABLE IF NOT EXISTS `chat_groups` (
  `id_chat_group` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_group_name` varchar(255) NOT NULL DEFAULT '',
  `chat_group_author_user_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_group_author_user_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_chat_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id_chat_message` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_message_from_user_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_message_from_user_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_message_to_user_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_message_to_user_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_message_text` text NOT NULL,
  `chat_message_file` blob DEFAULT NULL,
  `chat_message_read` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_message_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_chat_message`),
  KEY `i_from_user_num` (`chat_message_from_user_num`,`chat_message_from_user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `chat_users_groups`
--

CREATE TABLE IF NOT EXISTS `chat_users_groups` (
  `chat_user_group_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_user_group_user_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_user_group_user_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `chat_user_group_unread_messages_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`chat_user_group_num`,`chat_user_group_user_type`,`chat_user_group_user_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(40) DEFAULT '',
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classements`
--

CREATE TABLE IF NOT EXISTS `classements` (
  `id_classement` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_classement` char(3) NOT NULL DEFAULT 'BAN',
  `nom_classement` varchar(255) NOT NULL DEFAULT '',
  `classement_opac_name` varchar(255) NOT NULL DEFAULT '',
  `classement_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_classement`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `classements`
--

INSERT INTO `classements` VALUES
(1, '', '_NON CLASSE_', '', 1),
(2, 'BAN', 'Nouveautés', '', 2),
(3, 'EQU', 'Nouveautés', '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `class_user`
--

CREATE TABLE IF NOT EXISTS `class_user` (
  `class_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`class_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms`
--

CREATE TABLE IF NOT EXISTS `cms` (
  `id_cms` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cms_name` varchar(255) NOT NULL DEFAULT '',
  `cms_comment` text NOT NULL,
  `cms_opac_default` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_opac_view_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_cms`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_articles`
--

CREATE TABLE IF NOT EXISTS `cms_articles` (
  `id_article` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_title` varchar(255) NOT NULL DEFAULT '',
  `article_resume` mediumtext NOT NULL,
  `article_contenu` mediumtext NOT NULL,
  `article_logo` mediumblob NOT NULL,
  `article_publication_state` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `article_start_date` datetime DEFAULT NULL,
  `article_end_date` datetime DEFAULT NULL,
  `num_section` int(11) NOT NULL DEFAULT 0,
  `article_num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `article_creation_date` date DEFAULT NULL,
  `article_order` int(10) UNSIGNED DEFAULT 0,
  `article_update_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_article`),
  KEY `i_cms_article_title` (`article_title`),
  KEY `i_cms_article_publication_state` (`article_publication_state`),
  KEY `i_cms_article_num_parent` (`num_section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_articles_descriptors`
--

CREATE TABLE IF NOT EXISTS `cms_articles_descriptors` (
  `num_article` int(11) NOT NULL DEFAULT 0,
  `num_noeud` int(11) NOT NULL DEFAULT 0,
  `article_descriptor_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_article`,`num_noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_build`
--

CREATE TABLE IF NOT EXISTS `cms_build` (
  `id_build` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `build_version_num` int(11) NOT NULL DEFAULT 0,
  `build_type` varchar(255) NOT NULL DEFAULT 'cadre',
  `build_fixed` int(11) NOT NULL DEFAULT 0,
  `build_obj` varchar(255) NOT NULL DEFAULT '',
  `build_page` int(11) NOT NULL DEFAULT 0,
  `build_parent` varchar(255) NOT NULL DEFAULT '',
  `build_child_before` varchar(255) NOT NULL DEFAULT '',
  `build_child_after` varchar(255) NOT NULL DEFAULT '',
  `build_css` text NOT NULL,
  `build_div` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_build`),
  KEY `cms_build_index` (`build_version_num`,`build_obj`),
  KEY `i_build_parent_build_version_num` (`build_parent`,`build_version_num`),
  KEY `i_build_obj_build_version_num` (`build_obj`,`build_version_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_cache_cadres`
--

CREATE TABLE IF NOT EXISTS `cms_cache_cadres` (
  `cache_cadre_hash` varchar(32) NOT NULL,
  `cache_cadre_type_content` varchar(30) NOT NULL,
  `cache_cadre_create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `cache_cadre_content` mediumtext NOT NULL,
  PRIMARY KEY (`cache_cadre_hash`,`cache_cadre_type_content`),
  KEY `i_cache_cadre_create_date` (`cache_cadre_create_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_cadres`
--

CREATE TABLE IF NOT EXISTS `cms_cadres` (
  `id_cadre` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cadre_hash` varchar(255) NOT NULL DEFAULT '',
  `cadre_object` varchar(255) NOT NULL DEFAULT '',
  `cadre_name` varchar(255) NOT NULL DEFAULT '',
  `cadre_fixed` int(11) NOT NULL DEFAULT 0,
  `cadre_styles` text NOT NULL,
  `cadre_dom_parent` varchar(255) NOT NULL DEFAULT '',
  `cadre_dom_after` varchar(255) NOT NULL DEFAULT '',
  `cadre_url` text NOT NULL,
  `cadre_memo_url` int(11) NOT NULL DEFAULT 0,
  `cadre_classement` varchar(255) NOT NULL DEFAULT '',
  `cadre_modcache` varchar(255) NOT NULL DEFAULT 'get_post_view',
  `cadre_css_class` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cadre`),
  KEY `i_cadre_memo_url` (`cadre_memo_url`),
  KEY `i_cadre_object` (`cadre_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_cadre_content`
--

CREATE TABLE IF NOT EXISTS `cms_cadre_content` (
  `id_cadre_content` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cadre_content_hash` varchar(255) NOT NULL DEFAULT '',
  `cadre_content_type` varchar(255) NOT NULL DEFAULT '',
  `cadre_content_object` varchar(255) NOT NULL DEFAULT '',
  `cadre_content_num_cadre` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cadre_content_data` text NOT NULL,
  `cadre_content_num_cadre_content` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_cadre_content`),
  KEY `i_cadre_content_num_cadre` (`cadre_content_num_cadre`),
  KEY `i_cadre_content_num_cadre_content_cadre_content_type` (`cadre_content_num_cadre_content`,`cadre_content_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_collections`
--

CREATE TABLE IF NOT EXISTS `cms_collections` (
  `id_collection` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `collection_title` varchar(255) NOT NULL DEFAULT '',
  `collection_description` text NOT NULL,
  `collection_num_parent` int(11) NOT NULL DEFAULT 0,
  `collection_num_storage` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_collection`),
  KEY `i_cms_collection_title` (`collection_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_documents`
--

CREATE TABLE IF NOT EXISTS `cms_documents` (
  `id_document` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_title` varchar(255) NOT NULL DEFAULT '',
  `document_description` text NOT NULL,
  `document_filename` varchar(255) NOT NULL DEFAULT '',
  `document_mimetype` varchar(100) NOT NULL DEFAULT '',
  `document_filesize` int(11) NOT NULL DEFAULT 0,
  `document_vignette` mediumblob NOT NULL,
  `document_url` text NOT NULL,
  `document_path` varchar(255) NOT NULL DEFAULT '',
  `document_create_date` date NOT NULL DEFAULT '0000-00-00',
  `document_num_storage` int(11) NOT NULL DEFAULT 0,
  `document_type_object` varchar(255) NOT NULL DEFAULT '',
  `document_num_object` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_document`),
  KEY `i_cms_document_title` (`document_title`),
  KEY `i_document_num_object_document_type_object` (`document_num_object`,`document_type_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_documents_links`
--

CREATE TABLE IF NOT EXISTS `cms_documents_links` (
  `document_link_type_object` varchar(255) NOT NULL DEFAULT '',
  `document_link_num_object` int(11) NOT NULL DEFAULT 0,
  `document_link_num_document` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`document_link_type_object`,`document_link_num_object`,`document_link_num_document`),
  KEY `i_document_link_num_document` (`document_link_num_document`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_custom`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_custom` (
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
  PRIMARY KEY (`idchamp`),
  KEY `i_num_type` (`num_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_custom_dates`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_custom_dates` (
  `cms_editorial_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_editorial_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_editorial_custom_date_type` int(11) DEFAULT NULL,
  `cms_editorial_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `cms_editorial_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `cms_editorial_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`cms_editorial_custom_champ`,`cms_editorial_custom_origine`,`cms_editorial_custom_order`),
  KEY `cms_editorial_custom_champ` (`cms_editorial_custom_champ`),
  KEY `cms_editorial_custom_origine` (`cms_editorial_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_custom_lists`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_custom_lists` (
  `cms_editorial_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_editorial_custom_list_value` varchar(255) DEFAULT NULL,
  `cms_editorial_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`cms_editorial_custom_champ`),
  KEY `editorial_champ_list_value` (`cms_editorial_custom_champ`,`cms_editorial_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_custom_values`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_custom_values` (
  `cms_editorial_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_editorial_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cms_editorial_custom_small_text` varchar(255) DEFAULT NULL,
  `cms_editorial_custom_text` text DEFAULT NULL,
  `cms_editorial_custom_integer` int(11) DEFAULT NULL,
  `cms_editorial_custom_date` date DEFAULT NULL,
  `cms_editorial_custom_float` float DEFAULT NULL,
  `cms_editorial_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`cms_editorial_custom_champ`),
  KEY `editorial_custom_origine` (`cms_editorial_custom_origine`),
  KEY `i_ccv_st` (`cms_editorial_custom_small_text`),
  KEY `i_ccv_t` (`cms_editorial_custom_text`(255)),
  KEY `i_ccv_i` (`cms_editorial_custom_integer`),
  KEY `i_ccv_d` (`cms_editorial_custom_date`),
  KEY `i_ccv_f` (`cms_editorial_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_fields_global_index`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_fields_global_index` (
  `num_obj` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `code_champ` int(3) NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) NOT NULL DEFAULT 0,
  `ordre` int(4) NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  `pond` int(4) NOT NULL DEFAULT 100,
  `lang` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`num_obj`,`type`,`code_champ`,`code_ss_champ`,`ordre`),
  KEY `i_value` (`value`(300))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_publications_states`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_publications_states` (
  `id_publication_state` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `editorial_publication_state_label` varchar(255) NOT NULL DEFAULT '',
  `editorial_publication_state_opac_show` int(1) NOT NULL DEFAULT 0,
  `editorial_publication_state_auth_opac_show` int(1) NOT NULL DEFAULT 0,
  `editorial_publication_state_class_html` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_publication_state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_types`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_types` (
  `id_editorial_type` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `editorial_type_element` varchar(20) NOT NULL DEFAULT '',
  `editorial_type_label` varchar(255) NOT NULL DEFAULT '',
  `editorial_type_comment` text NOT NULL,
  `editorial_type_extension` text NOT NULL,
  `editorial_type_permalink_num_page` int(11) NOT NULL DEFAULT 0,
  `editorial_type_permalink_var_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_editorial_type`),
  KEY `i_editorial_type_element` (`editorial_type_element`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `cms_editorial_types`
--

INSERT INTO `cms_editorial_types` VALUES
(1, 'article_generic', 'CP pour Article', '', '', 0, ''),
(2, 'section_generic', 'CP pour Rubrique', '', '', 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `cms_editorial_words_global_index`
--

CREATE TABLE IF NOT EXISTS `cms_editorial_words_global_index` (
  `num_obj` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `code_champ` int(11) NOT NULL DEFAULT 0,
  `code_ss_champ` int(11) NOT NULL DEFAULT 0,
  `num_word` int(11) NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `position` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`num_obj`,`type`,`code_champ`,`code_ss_champ`,`num_word`,`position`),
  KEY `i_num_word` (`num_word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_hash`
--

CREATE TABLE IF NOT EXISTS `cms_hash` (
  `hash` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_managed_modules`
--

CREATE TABLE IF NOT EXISTS `cms_managed_modules` (
  `managed_module_name` varchar(255) NOT NULL DEFAULT '',
  `managed_module_box` text NOT NULL,
  PRIMARY KEY (`managed_module_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_modules_extensions_datas`
--

CREATE TABLE IF NOT EXISTS `cms_modules_extensions_datas` (
  `id_extension_datas` int(10) NOT NULL AUTO_INCREMENT,
  `extension_datas_module` varchar(255) NOT NULL DEFAULT '',
  `extension_datas_type` varchar(255) NOT NULL DEFAULT '',
  `extension_datas_type_element` varchar(255) NOT NULL DEFAULT '',
  `extension_datas_num_element` int(10) NOT NULL DEFAULT 0,
  `extension_datas_datas` blob DEFAULT NULL,
  PRIMARY KEY (`id_extension_datas`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_pages`
--

CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id_page` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_hash` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `page_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_pages_env`
--

CREATE TABLE IF NOT EXISTS `cms_pages_env` (
  `page_env_num_page` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_env_name` varchar(255) NOT NULL DEFAULT '',
  `page_env_id_selector` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`page_env_num_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_sections`
--

CREATE TABLE IF NOT EXISTS `cms_sections` (
  `id_section` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `section_title` varchar(255) NOT NULL DEFAULT '',
  `section_resume` mediumtext NOT NULL,
  `section_logo` mediumblob NOT NULL,
  `section_publication_state` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `section_start_date` datetime DEFAULT NULL,
  `section_end_date` datetime DEFAULT NULL,
  `section_num_parent` int(11) NOT NULL DEFAULT 0,
  `section_num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `section_creation_date` date DEFAULT NULL,
  `section_order` int(10) UNSIGNED DEFAULT 0,
  `section_update_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_section`),
  KEY `i_cms_section_title` (`section_title`),
  KEY `i_cms_section_publication_state` (`section_publication_state`),
  KEY `i_cms_section_num_parent` (`section_num_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_sections_descriptors`
--

CREATE TABLE IF NOT EXISTS `cms_sections_descriptors` (
  `num_section` int(11) NOT NULL DEFAULT 0,
  `num_noeud` int(11) NOT NULL DEFAULT 0,
  `section_descriptor_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_section`,`num_noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_toolkits`
--

CREATE TABLE IF NOT EXISTS `cms_toolkits` (
  `cms_toolkit_name` varchar(255) NOT NULL DEFAULT '',
  `cms_toolkit_active` int(1) NOT NULL DEFAULT 0,
  `cms_toolkit_data` text NOT NULL,
  `cms_toolkit_order` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`cms_toolkit_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_vars`
--

CREATE TABLE IF NOT EXISTS `cms_vars` (
  `id_var` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `var_num_page` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `var_name` varchar(255) NOT NULL DEFAULT '',
  `var_comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_var`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cms_version`
--

CREATE TABLE IF NOT EXISTS `cms_version` (
  `id_version` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version_cms_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `version_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version_comment` text NOT NULL,
  `version_public` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `version_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collections`
--

CREATE TABLE IF NOT EXISTS `collections` (
  `collection_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `collection_name` varchar(255) NOT NULL DEFAULT '',
  `collection_parent` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `collection_issn` varchar(12) NOT NULL DEFAULT '',
  `index_coll` text DEFAULT NULL,
  `collection_web` text NOT NULL,
  `collection_comment` text NOT NULL,
  `authority_import_denied` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`collection_id`),
  KEY `collection_name` (`collection_name`),
  KEY `collection_parent` (`collection_parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collections_state`
--

CREATE TABLE IF NOT EXISTS `collections_state` (
  `collstate_id` int(8) NOT NULL AUTO_INCREMENT,
  `id_serial` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `location_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `state_collections` text NOT NULL,
  `collstate_emplacement` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_type` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_origine` varchar(255) NOT NULL DEFAULT '',
  `collstate_cote` varchar(255) NOT NULL DEFAULT '',
  `collstate_archive` varchar(255) NOT NULL DEFAULT '',
  `collstate_statut` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_lacune` text NOT NULL,
  `collstate_note` text NOT NULL,
  PRIMARY KEY (`collstate_id`),
  KEY `i_colls_arc` (`collstate_archive`),
  KEY `i_colls_empl` (`collstate_emplacement`),
  KEY `i_colls_type` (`collstate_type`),
  KEY `i_colls_orig` (`collstate_origine`),
  KEY `i_colls_cote` (`collstate_cote`),
  KEY `i_colls_stat` (`collstate_statut`),
  KEY `i_colls_serial` (`id_serial`),
  KEY `i_colls_loc` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collection_custom`
--

CREATE TABLE IF NOT EXISTS `collection_custom` (
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
-- Structure de la table `collection_custom_dates`
--

CREATE TABLE IF NOT EXISTS `collection_custom_dates` (
  `collection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collection_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collection_custom_date_type` int(11) DEFAULT NULL,
  `collection_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `collection_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `collection_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`collection_custom_champ`,`collection_custom_origine`,`collection_custom_order`),
  KEY `collection_custom_champ` (`collection_custom_champ`),
  KEY `collection_custom_origine` (`collection_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collection_custom_lists`
--

CREATE TABLE IF NOT EXISTS `collection_custom_lists` (
  `collection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collection_custom_list_value` varchar(255) DEFAULT NULL,
  `collection_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`collection_custom_champ`),
  KEY `editorial_champ_list_value` (`collection_custom_champ`,`collection_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collection_custom_values`
--

CREATE TABLE IF NOT EXISTS `collection_custom_values` (
  `collection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collection_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collection_custom_small_text` varchar(255) DEFAULT NULL,
  `collection_custom_text` text DEFAULT NULL,
  `collection_custom_integer` int(11) DEFAULT NULL,
  `collection_custom_date` date DEFAULT NULL,
  `collection_custom_float` float DEFAULT NULL,
  `collection_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`collection_custom_champ`),
  KEY `editorial_custom_origine` (`collection_custom_origine`),
  KEY `i_ccv_st` (`collection_custom_small_text`),
  KEY `i_ccv_t` (`collection_custom_text`(255)),
  KEY `i_ccv_i` (`collection_custom_integer`),
  KEY `i_ccv_d` (`collection_custom_date`),
  KEY `i_ccv_f` (`collection_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collstate_bulletins`
--

CREATE TABLE IF NOT EXISTS `collstate_bulletins` (
  `collstate_bulletins_num_collstate` int(8) NOT NULL DEFAULT 0,
  `collstate_bulletins_num_bulletin` int(8) NOT NULL DEFAULT 0,
  `collstate_bulletins_order` int(8) NOT NULL DEFAULT 0,
  PRIMARY KEY (`collstate_bulletins_num_collstate`,`collstate_bulletins_num_bulletin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collstate_custom`
--

CREATE TABLE IF NOT EXISTS `collstate_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `search` int(11) NOT NULL DEFAULT 0,
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
-- Structure de la table `collstate_custom_dates`
--

CREATE TABLE IF NOT EXISTS `collstate_custom_dates` (
  `collstate_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_custom_date_type` int(11) DEFAULT NULL,
  `collstate_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `collstate_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `collstate_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`collstate_custom_champ`,`collstate_custom_origine`,`collstate_custom_order`),
  KEY `collstate_custom_champ` (`collstate_custom_champ`),
  KEY `collstate_custom_origine` (`collstate_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collstate_custom_lists`
--

CREATE TABLE IF NOT EXISTS `collstate_custom_lists` (
  `collstate_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_custom_list_value` varchar(255) NOT NULL DEFAULT '',
  `collstate_custom_list_lib` varchar(255) NOT NULL DEFAULT '',
  `ordre` int(11) NOT NULL DEFAULT 0,
  KEY `collstate_custom_champ` (`collstate_custom_champ`),
  KEY `i_ccl_lv` (`collstate_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collstate_custom_values`
--

CREATE TABLE IF NOT EXISTS `collstate_custom_values` (
  `collstate_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `collstate_custom_small_text` varchar(255) DEFAULT NULL,
  `collstate_custom_text` text DEFAULT NULL,
  `collstate_custom_integer` int(11) DEFAULT NULL,
  `collstate_custom_date` date DEFAULT NULL,
  `collstate_custom_float` float DEFAULT NULL,
  `collstate_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `collstate_custom_champ` (`collstate_custom_champ`),
  KEY `collstate_custom_origine` (`collstate_custom_origine`),
  KEY `i_ccv_st` (`collstate_custom_small_text`),
  KEY `i_ccv_t` (`collstate_custom_text`(255)),
  KEY `i_ccv_i` (`collstate_custom_integer`),
  KEY `i_ccv_d` (`collstate_custom_date`),
  KEY `i_ccv_f` (`collstate_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comptes`
--

CREATE TABLE IF NOT EXISTS `comptes` (
  `id_compte` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `type_compte_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `solde` decimal(16,2) DEFAULT 0.00,
  `prepay_mnt` decimal(16,2) NOT NULL DEFAULT 0.00,
  `proprio_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `droits` text NOT NULL,
  PRIMARY KEY (`id_compte`),
  KEY `i_cpt_proprio_id` (`proprio_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors`
--

CREATE TABLE IF NOT EXISTS `connectors` (
  `connector_id` varchar(20) NOT NULL DEFAULT '',
  `parameters` text NOT NULL,
  `repository` int(11) NOT NULL DEFAULT 0,
  `timeout` int(11) NOT NULL DEFAULT 5,
  `retry` int(11) NOT NULL DEFAULT 3,
  `ttl` int(11) NOT NULL DEFAULT 1440,
  PRIMARY KEY (`connector_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_categ`
--

CREATE TABLE IF NOT EXISTS `connectors_categ` (
  `connectors_categ_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `connectors_categ_name` varchar(64) NOT NULL DEFAULT '',
  `opac_expanded` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`connectors_categ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_categ_sources`
--

CREATE TABLE IF NOT EXISTS `connectors_categ_sources` (
  `num_categ` smallint(6) NOT NULL DEFAULT 0,
  `num_source` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_categ`,`num_source`),
  KEY `i_num_source` (`num_source`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out`
--

CREATE TABLE IF NOT EXISTS `connectors_out` (
  `connectors_out_id` int(11) NOT NULL AUTO_INCREMENT,
  `connectors_out_config` longblob NOT NULL,
  PRIMARY KEY (`connectors_out_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_oai_deleted_records`
--

CREATE TABLE IF NOT EXISTS `connectors_out_oai_deleted_records` (
  `num_set` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `num_notice` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `deletion_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`num_set`,`num_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_oai_tokens`
--

CREATE TABLE IF NOT EXISTS `connectors_out_oai_tokens` (
  `connectors_out_oai_token_token` varchar(32) NOT NULL,
  `connectors_out_oai_token_environnement` text NOT NULL,
  `connectors_out_oai_token_expirationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`connectors_out_oai_token_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_setcaches`
--

CREATE TABLE IF NOT EXISTS `connectors_out_setcaches` (
  `connectors_out_setcache_id` int(11) NOT NULL AUTO_INCREMENT,
  `connectors_out_setcache_setnum` int(11) NOT NULL DEFAULT 0,
  `connectors_out_setcache_lifeduration` int(4) NOT NULL DEFAULT 0,
  `connectors_out_setcache_lifeduration_unit` enum('seconds','minutes','hours','days','weeks','months') NOT NULL DEFAULT 'seconds',
  `connectors_out_setcache_lastupdatedate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`connectors_out_setcache_id`),
  UNIQUE KEY `connectors_out_setcache_setnum` (`connectors_out_setcache_setnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_setcache_values`
--

CREATE TABLE IF NOT EXISTS `connectors_out_setcache_values` (
  `connectors_out_setcache_values_cachenum` int(11) NOT NULL DEFAULT 0,
  `connectors_out_setcache_values_value` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`connectors_out_setcache_values_cachenum`,`connectors_out_setcache_values_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_setcategs`
--

CREATE TABLE IF NOT EXISTS `connectors_out_setcategs` (
  `connectors_out_setcateg_id` int(11) NOT NULL AUTO_INCREMENT,
  `connectors_out_setcateg_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`connectors_out_setcateg_id`),
  UNIQUE KEY `connectors_out_setcateg_name` (`connectors_out_setcateg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_setcateg_sets`
--

CREATE TABLE IF NOT EXISTS `connectors_out_setcateg_sets` (
  `connectors_out_setcategset_setnum` int(11) NOT NULL,
  `connectors_out_setcategset_categnum` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`connectors_out_setcategset_setnum`,`connectors_out_setcategset_categnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_sets`
--

CREATE TABLE IF NOT EXISTS `connectors_out_sets` (
  `connector_out_set_id` int(11) NOT NULL AUTO_INCREMENT,
  `connector_out_set_caption` varchar(100) NOT NULL DEFAULT '',
  `connector_out_set_type` int(4) NOT NULL DEFAULT 0,
  `connector_out_set_config` longblob NOT NULL,
  `being_refreshed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`connector_out_set_id`),
  UNIQUE KEY `connector_out_set_caption` (`connector_out_set_caption`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_sources`
--

CREATE TABLE IF NOT EXISTS `connectors_out_sources` (
  `connectors_out_source_id` int(11) NOT NULL AUTO_INCREMENT,
  `connectors_out_sources_connectornum` int(11) NOT NULL DEFAULT 0,
  `connectors_out_source_name` varchar(100) NOT NULL DEFAULT '',
  `connectors_out_source_comment` varchar(200) NOT NULL DEFAULT '',
  `connectors_out_source_config` longblob NOT NULL,
  PRIMARY KEY (`connectors_out_source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_out_sources_esgroups`
--

CREATE TABLE IF NOT EXISTS `connectors_out_sources_esgroups` (
  `connectors_out_source_esgroup_sourcenum` int(11) NOT NULL DEFAULT 0,
  `connectors_out_source_esgroup_esgroupnum` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`connectors_out_source_esgroup_sourcenum`,`connectors_out_source_esgroup_esgroupnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `connectors_sources`
--

CREATE TABLE IF NOT EXISTS `connectors_sources` (
  `source_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_connector` varchar(20) NOT NULL DEFAULT '',
  `parameters` mediumtext NOT NULL,
  `comment` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `repository` int(11) NOT NULL DEFAULT 0,
  `timeout` int(11) NOT NULL DEFAULT 5,
  `retry` int(11) NOT NULL DEFAULT 3,
  `ttl` int(11) NOT NULL DEFAULT 1440,
  `opac_allowed` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `rep_upload` int(11) NOT NULL DEFAULT 0,
  `upload_doc_num` int(11) NOT NULL DEFAULT 1,
  `enrichment` int(11) NOT NULL DEFAULT 0,
  `opac_affiliate_search` int(11) NOT NULL DEFAULT 0,
  `opac_selected` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `gestion_selected` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `type_enrichment_allowed` text NOT NULL,
  `ico_notice` varchar(256) NOT NULL DEFAULT '',
  `last_sync_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clean_html` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_form_objects`
--

CREATE TABLE IF NOT EXISTS `contact_form_objects` (
  `id_object` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_label` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_areas`
--

CREATE TABLE IF NOT EXISTS `contribution_area_areas` (
  `id_area` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_title` varchar(255) NOT NULL DEFAULT '',
  `area_comment` text NOT NULL,
  `area_color` varchar(10) NOT NULL DEFAULT '',
  `area_order` int(5) NOT NULL DEFAULT 0,
  `area_status` int(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_computed_fields`
--

CREATE TABLE IF NOT EXISTS `contribution_area_computed_fields` (
  `id_computed_fields` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `computed_fields_area_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `computed_fields_field_num` varchar(255) NOT NULL DEFAULT '',
  `computed_fields_template` text DEFAULT NULL,
  PRIMARY KEY (`id_computed_fields`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_computed_fields_used`
--

CREATE TABLE IF NOT EXISTS `contribution_area_computed_fields_used` (
  `id_computed_fields_used` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `computed_fields_used_origine_field_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `computed_fields_used_label` text DEFAULT NULL,
  `computed_fields_used_num` varchar(255) NOT NULL DEFAULT '',
  `computed_fields_used_alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_computed_fields_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_equations`
--

CREATE TABLE IF NOT EXISTS `contribution_area_equations` (
  `contribution_area_equation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contribution_area_equation_name` varchar(255) NOT NULL DEFAULT '',
  `contribution_area_equation_type` varchar(255) NOT NULL DEFAULT '',
  `contribution_area_equation_query` text NOT NULL,
  `contribution_area_equation_human_query` text NOT NULL,
  PRIMARY KEY (`contribution_area_equation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_forms`
--

CREATE TABLE IF NOT EXISTS `contribution_area_forms` (
  `id_form` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_title` varchar(255) NOT NULL DEFAULT '',
  `form_type` varchar(255) NOT NULL DEFAULT '',
  `form_parameters` blob NOT NULL,
  `form_comment` text NOT NULL,
  PRIMARY KEY (`id_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contribution_area_status`
--

CREATE TABLE IF NOT EXISTS `contribution_area_status` (
  `contribution_area_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contribution_area_status_gestion_libelle` varchar(255) NOT NULL DEFAULT '',
  `contribution_area_status_opac_libelle` varchar(255) NOT NULL DEFAULT '',
  `contribution_area_status_class_html` varchar(255) NOT NULL DEFAULT '',
  `contribution_area_status_available_for` text DEFAULT NULL,
  PRIMARY KEY (`contribution_area_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `contribution_area_status`
--

INSERT INTO `contribution_area_status` VALUES
(1, 'Statut par défaut', 'Statut par défaut', 'statutnot1', 'a:10:{i:0;s:6:\"record\";i:1;s:6:\"author\";i:2;s:8:\"category\";i:3;s:9:\"publisher\";i:4;s:10:\"collection\";i:5;s:14:\"sub_collection\";i:6;s:5:\"serie\";i:7;s:4:\"work\";i:8;s:8:\"indexint\";i:9;s:7:\"concept\";}');

-- --------------------------------------------------------

--
-- Structure de la table `coordonnees`
--

CREATE TABLE IF NOT EXISTS `coordonnees` (
  `id_contact` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_coord` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `num_entite` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `contact` varchar(255) NOT NULL DEFAULT '',
  `adr1` varchar(255) NOT NULL DEFAULT '',
  `adr2` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(15) NOT NULL DEFAULT '',
  `ville` varchar(100) NOT NULL DEFAULT '',
  `etat` varchar(100) NOT NULL DEFAULT '',
  `pays` varchar(100) NOT NULL DEFAULT '',
  `tel1` varchar(100) NOT NULL DEFAULT '',
  `tel2` varchar(100) NOT NULL DEFAULT '',
  `fax` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `commentaires` text DEFAULT NULL,
  PRIMARY KEY (`id_contact`),
  KEY `i_num_entite` (`num_entite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `code` varchar(40) NOT NULL,
  `directory` varchar(40) DEFAULT NULL,
  `db_name` varchar(40) DEFAULT NULL,
  `course_language` varchar(20) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_code` varchar(40) DEFAULT NULL,
  `visibility` tinyint(4) DEFAULT 0,
  `show_score` int(11) NOT NULL DEFAULT 1,
  `tutor_name` varchar(200) DEFAULT NULL,
  `visual_code` varchar(40) DEFAULT NULL,
  `department_name` varchar(30) DEFAULT NULL,
  `department_url` varchar(180) DEFAULT NULL,
  `disk_quota` int(10) UNSIGNED DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `last_edit` datetime DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `target_course_code` varchar(40) DEFAULT NULL,
  `subscribe` tinyint(4) NOT NULL DEFAULT 1,
  `unsubscribe` tinyint(4) NOT NULL DEFAULT 1,
  `registration_code` varchar(255) NOT NULL DEFAULT '',
  `default_enrolment` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `course_category`
--

CREATE TABLE IF NOT EXISTS `course_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `code` varchar(40) NOT NULL DEFAULT '',
  `parent_id` varchar(40) DEFAULT NULL,
  `tree_pos` int(10) UNSIGNED DEFAULT NULL,
  `children_count` smallint(6) DEFAULT NULL,
  `auth_course_child` enum('TRUE','FALSE') DEFAULT 'TRUE',
  `auth_cat_child` enum('TRUE','FALSE') DEFAULT 'TRUE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`),
  KEY `tree_pos` (`tree_pos`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `course_category`
--

INSERT INTO `course_category` VALUES
(4, 'Collège', 'COLLEGE', NULL, 2, 0, 'TRUE', 'TRUE'),
(5, 'Lycée', 'LYCEE', NULL, 3, 0, 'TRUE', 'TRUE'),
(6, 'Faculté', 'FAC', NULL, 4, 0, 'TRUE', 'TRUE'),
(7, 'Ecole', 'ECOLE', NULL, 1, 0, 'TRUE', 'TRUE');

-- --------------------------------------------------------

--
-- Structure de la table `course_field`
--

CREATE TABLE IF NOT EXISTS `course_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` int(11) NOT NULL DEFAULT 1,
  `field_variable` varchar(64) NOT NULL,
  `field_display_text` varchar(64) DEFAULT NULL,
  `field_default_value` text DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_visible` tinyint(4) DEFAULT 0,
  `field_changeable` tinyint(4) DEFAULT 0,
  `field_filter` tinyint(4) DEFAULT 0,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `course_field`
--

INSERT INTO `course_field` VALUES
(1, 10, 'special_course', 'SpecialCourse', 'Yes', NULL, 1, 1, 0, '2014-03-30 14:40:06');

-- --------------------------------------------------------

--
-- Structure de la table `course_field_values`
--

CREATE TABLE IF NOT EXISTS `course_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(40) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `course_module`
--

CREATE TABLE IF NOT EXISTS `course_module` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `row` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `column` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `position` varchar(20) NOT NULL DEFAULT 'basic',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `course_module`
--

INSERT INTO `course_module` VALUES
(1, 'calendar_event', 'calendar/agenda.php', 'agenda.gif', 1, 1, 'basic'),
(2, 'link', 'link/link.php', 'links.gif', 4, 1, 'basic'),
(3, 'document', 'document/document.php', 'documents.gif', 3, 1, 'basic'),
(4, 'student_publication', 'work/work.php', 'works.gif', 3, 2, 'basic'),
(5, 'announcement', 'announcements/announcements.php', 'valves.gif', 2, 1, 'basic'),
(6, 'user', 'user/user.php', 'members.gif', 2, 3, 'basic'),
(7, 'forum', 'forum/index.php', 'forum.gif', 1, 2, 'basic'),
(8, 'quiz', 'exercice/exercice.php', 'quiz.gif', 2, 2, 'basic'),
(9, 'group', 'group/group.php', 'group.gif', 3, 3, 'basic'),
(10, 'course_description', 'course_description/', 'info.gif', 1, 3, 'basic'),
(11, 'chat', 'chat/chat.php', 'chat.gif', 0, 0, 'external'),
(12, 'dropbox', 'dropbox/index.php', 'dropbox.gif', 4, 2, 'basic'),
(13, 'tracking', 'tracking/courseLog.php', 'statistics.gif', 1, 3, 'courseadmin'),
(14, 'homepage_link', 'link/link.php?action=addlink', 'npage.gif', 1, 1, 'courseadmin'),
(15, 'course_setting', 'course_info/infocours.php', 'reference.gif', 1, 1, 'courseadmin'),
(16, 'External', '', 'external.gif', 0, 0, 'external'),
(17, 'AddedLearnpath', '', 'scormbuilder.gif', 0, 0, 'external'),
(18, 'conference', 'conference/index.php?type=conference', 'conf.gif', 0, 0, 'external'),
(19, 'conference', 'conference/index.php?type=classroom', 'conf.gif', 0, 0, 'external'),
(20, 'learnpath', 'newscorm/lp_controller.php', 'scorm.gif', 5, 1, 'basic'),
(21, 'blog', 'blog/blog.php', 'blog.gif', 1, 2, 'basic'),
(22, 'blog_management', 'blog/blog_admin.php', 'blog_admin.gif', 1, 2, 'courseadmin'),
(23, 'course_maintenance', 'course_info/maintenance.php', 'backup.gif', 2, 3, 'courseadmin'),
(24, 'survey', 'survey/survey_list.php', 'survey.gif', 2, 1, 'basic'),
(25, 'wiki', 'wiki/index.php', 'wiki.gif', 2, 3, 'basic'),
(26, 'gradebook', 'gradebook/index.php', 'gradebook.gif', 2, 2, 'basic'),
(27, 'glossary', 'glossary/index.php', 'glossary.gif', 2, 1, 'basic'),
(28, 'notebook', 'notebook/index.php', 'notebook.gif', 2, 1, 'basic');

-- --------------------------------------------------------

--
-- Structure de la table `course_rel_class`
--

CREATE TABLE IF NOT EXISTS `course_rel_class` (
  `course_code` char(40) NOT NULL,
  `class_id` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`course_code`,`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `course_rel_user`
--

CREATE TABLE IF NOT EXISTS `course_rel_user` (
  `course_code` varchar(40) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 5,
  `role` varchar(60) DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT 0,
  `tutor_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort` int(11) DEFAULT NULL,
  `user_course_cat` int(11) DEFAULT 0,
  PRIMARY KEY (`course_code`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE IF NOT EXISTS `demandes` (
  `id_demande` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_demandeur` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `theme_demande` int(3) NOT NULL DEFAULT 0,
  `type_demande` int(3) NOT NULL DEFAULT 0,
  `etat_demande` int(3) NOT NULL DEFAULT 0,
  `date_demande` date NOT NULL DEFAULT '0000-00-00',
  `date_prevue` date NOT NULL DEFAULT '0000-00-00',
  `deadline_demande` date NOT NULL DEFAULT '0000-00-00',
  `titre_demande` varchar(255) NOT NULL DEFAULT '',
  `sujet_demande` text NOT NULL,
  `progression` mediumint(3) NOT NULL DEFAULT 0,
  `num_user_cloture` mediumint(3) NOT NULL DEFAULT 0,
  `num_notice` int(10) NOT NULL DEFAULT 0,
  `dmde_read_gestion` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `reponse_finale` text DEFAULT NULL,
  `dmde_read_opac` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `demande_note_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_linked_notice` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_demande`),
  KEY `i_num_demandeur` (`num_demandeur`),
  KEY `i_date_demande` (`date_demande`),
  KEY `i_deadline_demande` (`deadline_demande`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_actions`
--

CREATE TABLE IF NOT EXISTS `demandes_actions` (
  `id_action` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_action` int(3) NOT NULL DEFAULT 0,
  `statut_action` int(3) NOT NULL DEFAULT 0,
  `sujet_action` varchar(255) NOT NULL DEFAULT '',
  `detail_action` text NOT NULL,
  `date_action` date NOT NULL DEFAULT '0000-00-00',
  `deadline_action` date NOT NULL DEFAULT '0000-00-00',
  `temps_passe` float DEFAULT NULL,
  `cout` mediumint(3) NOT NULL DEFAULT 0,
  `progression_action` mediumint(3) NOT NULL DEFAULT 0,
  `prive_action` int(1) NOT NULL DEFAULT 0,
  `num_demande` int(10) NOT NULL DEFAULT 0,
  `actions_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `actions_type_user` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `actions_read_opac` int(11) NOT NULL DEFAULT 0,
  `actions_read_gestion` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_action`),
  KEY `i_date_action` (`date_action`),
  KEY `i_deadline_action` (`deadline_action`),
  KEY `i_num_demande` (`num_demande`),
  KEY `i_actions_user` (`actions_num_user`,`actions_type_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_custom`
--

CREATE TABLE IF NOT EXISTS `demandes_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
-- Structure de la table `demandes_custom_dates`
--

CREATE TABLE IF NOT EXISTS `demandes_custom_dates` (
  `demandes_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `demandes_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `demandes_custom_date_type` int(11) DEFAULT NULL,
  `demandes_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `demandes_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `demandes_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`demandes_custom_champ`,`demandes_custom_origine`,`demandes_custom_order`),
  KEY `demandes_custom_champ` (`demandes_custom_champ`),
  KEY `demandes_custom_origine` (`demandes_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_custom_lists`
--

CREATE TABLE IF NOT EXISTS `demandes_custom_lists` (
  `demandes_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `demandes_custom_list_value` varchar(255) DEFAULT NULL,
  `demandes_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `i_demandes_custom_champ` (`demandes_custom_champ`),
  KEY `i_demandes_champ_list_value` (`demandes_custom_champ`,`demandes_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_custom_values`
--

CREATE TABLE IF NOT EXISTS `demandes_custom_values` (
  `demandes_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `demandes_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `demandes_custom_small_text` varchar(255) DEFAULT NULL,
  `demandes_custom_text` text DEFAULT NULL,
  `demandes_custom_integer` int(11) DEFAULT NULL,
  `demandes_custom_date` date DEFAULT NULL,
  `demandes_custom_float` float DEFAULT NULL,
  `demandes_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `i_demandes_custom_champ` (`demandes_custom_champ`),
  KEY `i_demandes_custom_origine` (`demandes_custom_origine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_notes`
--

CREATE TABLE IF NOT EXISTS `demandes_notes` (
  `id_note` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prive` int(1) NOT NULL DEFAULT 0,
  `rapport` int(1) NOT NULL DEFAULT 0,
  `contenu` text NOT NULL,
  `date_note` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_action` int(10) NOT NULL DEFAULT 0,
  `num_note_parent` int(10) NOT NULL DEFAULT 0,
  `notes_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notes_type_user` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `notes_read_gestion` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `notes_read_opac` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_note`),
  KEY `i_date_note` (`date_note`),
  KEY `i_num_action` (`num_action`),
  KEY `i_num_note_parent` (`num_note_parent`),
  KEY `i_notes_user` (`notes_num_user`,`notes_type_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_theme`
--

CREATE TABLE IF NOT EXISTS `demandes_theme` (
  `id_theme` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_theme` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_type`
--

CREATE TABLE IF NOT EXISTS `demandes_type` (
  `id_type` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_type` varchar(255) NOT NULL DEFAULT '',
  `allowed_actions` text NOT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_users`
--

CREATE TABLE IF NOT EXISTS `demandes_users` (
  `num_user` int(10) NOT NULL DEFAULT 0,
  `num_demande` int(10) NOT NULL DEFAULT 0,
  `date_creation` date NOT NULL DEFAULT '0000-00-00',
  `users_statut` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_user`,`num_demande`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docsloc_section`
--

CREATE TABLE IF NOT EXISTS `docsloc_section` (
  `num_section` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_location` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_pclass` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_section`,`num_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docs_codestat`
--

CREATE TABLE IF NOT EXISTS `docs_codestat` (
  `idcode` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codestat_libelle` varchar(255) DEFAULT NULL,
  `statisdoc_codage_import` char(2) NOT NULL DEFAULT '',
  `statisdoc_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`idcode`),
  KEY `statisdoc_owner` (`statisdoc_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docs_location`
--

CREATE TABLE IF NOT EXISTS `docs_location` (
  `idlocation` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `location_libelle` varchar(255) DEFAULT NULL,
  `locdoc_codage_import` varchar(255) NOT NULL DEFAULT '',
  `locdoc_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `location_pic` varchar(255) NOT NULL DEFAULT '',
  `location_visible_opac` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL DEFAULT '',
  `adr1` varchar(255) NOT NULL DEFAULT '',
  `adr2` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(15) NOT NULL DEFAULT '',
  `town` varchar(100) NOT NULL DEFAULT '',
  `state` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `website` varchar(100) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `commentaire` text NOT NULL,
  `transfert_ordre` smallint(2) UNSIGNED NOT NULL DEFAULT 9999,
  `transfert_statut_defaut` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_infopage` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `css_style` varchar(100) NOT NULL DEFAULT '',
  `surloc_num` int(11) NOT NULL DEFAULT 0,
  `surloc_used` tinyint(1) NOT NULL DEFAULT 0,
  `show_a2z` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`idlocation`),
  KEY `locdoc_owner` (`locdoc_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docs_section`
--

CREATE TABLE IF NOT EXISTS `docs_section` (
  `idsection` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `section_libelle` varchar(255) DEFAULT NULL,
  `sdoc_codage_import` varchar(255) NOT NULL DEFAULT '',
  `sdoc_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `section_pic` varchar(255) NOT NULL DEFAULT '',
  `section_visible_opac` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idsection`),
  KEY `sdoc_owner` (`sdoc_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docs_statut`
--

CREATE TABLE IF NOT EXISTS `docs_statut` (
  `idstatut` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `statut_libelle` varchar(255) DEFAULT NULL,
  `statut_libelle_opac` varchar(255) DEFAULT '',
  `pret_flag` tinyint(4) NOT NULL DEFAULT 1,
  `statusdoc_codage_import` char(2) NOT NULL DEFAULT '',
  `statusdoc_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `transfert_flag` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `statut_visible_opac` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `statut_allow_resa` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`idstatut`),
  KEY `statusdoc_owner` (`statusdoc_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docs_type`
--

CREATE TABLE IF NOT EXISTS `docs_type` (
  `idtyp_doc` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tdoc_libelle` varchar(255) DEFAULT NULL,
  `duree_pret` smallint(6) NOT NULL DEFAULT 31,
  `duree_resa` int(6) UNSIGNED NOT NULL DEFAULT 15,
  `tdoc_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `tdoc_codage_import` varchar(255) NOT NULL DEFAULT '',
  `tarif_pret` decimal(16,2) NOT NULL DEFAULT 0.00,
  `short_loan_duration` int(6) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`idtyp_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_categories`
--

CREATE TABLE IF NOT EXISTS `docwatch_categories` (
  `id_category` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_title` varchar(255) NOT NULL DEFAULT '',
  `category_num_parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_datasources`
--

CREATE TABLE IF NOT EXISTS `docwatch_datasources` (
  `id_datasource` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datasource_type` varchar(255) NOT NULL DEFAULT '',
  `datasource_title` varchar(255) NOT NULL DEFAULT '',
  `datasource_ttl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datasource_last_date` datetime DEFAULT NULL,
  `datasource_parameters` mediumtext NOT NULL,
  `datasource_num_category` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datasource_default_interesting` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datasource_clean_html` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `datasource_boolean_expression` varchar(255) NOT NULL DEFAULT '',
  `datasource_num_watch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_datasource`),
  KEY `i_docwatch_datasource_title` (`datasource_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_datasource_monitoring_website`
--

CREATE TABLE IF NOT EXISTS `docwatch_datasource_monitoring_website` (
  `datasource_monitoring_website_num_datasource` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datasource_monitoring_website_upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datasource_monitoring_website_content` mediumtext NOT NULL,
  `datasource_monitoring_website_content_hash` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`datasource_monitoring_website_num_datasource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_items`
--

CREATE TABLE IF NOT EXISTS `docwatch_items` (
  `id_item` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_type` varchar(255) NOT NULL DEFAULT '',
  `item_title` varchar(255) NOT NULL DEFAULT '',
  `item_summary` mediumtext NOT NULL,
  `item_content` mediumtext NOT NULL,
  `item_added_date` datetime DEFAULT NULL,
  `item_publication_date` datetime DEFAULT NULL,
  `item_hash` varchar(255) NOT NULL DEFAULT '',
  `item_url` varchar(255) NOT NULL DEFAULT '',
  `item_logo_url` varchar(255) NOT NULL DEFAULT '',
  `item_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_interesting` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_num_article` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_num_section` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_num_datasource` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_num_watch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_index_sew` mediumtext NOT NULL DEFAULT '',
  `item_index_wew` mediumtext NOT NULL DEFAULT '',
  PRIMARY KEY (`id_item`),
  KEY `i_docwatch_item_type` (`item_type`),
  KEY `i_docwatch_item_title` (`item_title`),
  KEY `i_docwatch_item_num_article` (`item_num_article`),
  KEY `i_docwatch_item_num_section` (`item_num_section`),
  KEY `i_docwatch_item_num_notice` (`item_num_notice`),
  KEY `i_docwatch_item_num_watch` (`item_num_watch`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_items_descriptors`
--

CREATE TABLE IF NOT EXISTS `docwatch_items_descriptors` (
  `num_item` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_noeud` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_item`,`num_noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_items_tags`
--

CREATE TABLE IF NOT EXISTS `docwatch_items_tags` (
  `num_item` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_tag` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_item`,`num_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_selectors`
--

CREATE TABLE IF NOT EXISTS `docwatch_selectors` (
  `id_selector` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `selector_type` varchar(255) NOT NULL DEFAULT '',
  `selector_num_datasource` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `selector_parameters` mediumtext NOT NULL,
  PRIMARY KEY (`id_selector`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_tags`
--

CREATE TABLE IF NOT EXISTS `docwatch_tags` (
  `id_tag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag_title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `docwatch_watches`
--

CREATE TABLE IF NOT EXISTS `docwatch_watches` (
  `id_watch` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `watch_title` varchar(255) NOT NULL DEFAULT '',
  `watch_owner` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_allowed_users` varchar(255) NOT NULL DEFAULT '',
  `watch_num_category` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_last_date` datetime DEFAULT NULL,
  `watch_ttl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_desc` text NOT NULL,
  `watch_logo` mediumblob NOT NULL,
  `watch_logo_url` varchar(255) NOT NULL DEFAULT '',
  `watch_record_default_type` char(2) NOT NULL DEFAULT 'a',
  `watch_record_default_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_record_default_index_lang` varchar(20) NOT NULL DEFAULT '',
  `watch_record_default_lang` varchar(20) NOT NULL DEFAULT '',
  `watch_record_default_is_new` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `watch_article_default_parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_article_default_content_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_article_default_publication_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_section_default_parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_section_default_content_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_section_default_publication_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `watch_rss_link` varchar(255) NOT NULL,
  `watch_rss_lang` varchar(255) NOT NULL,
  `watch_rss_copyright` varchar(255) NOT NULL,
  `watch_rss_editor` varchar(255) NOT NULL,
  `watch_rss_webmaster` varchar(255) NOT NULL,
  `watch_rss_image_title` varchar(255) NOT NULL,
  `watch_rss_image_website` varchar(255) NOT NULL,
  `watch_boolean_expression` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_watch`),
  KEY `i_docwatch_watch_title` (`watch_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dsi_archive`
--

CREATE TABLE IF NOT EXISTS `dsi_archive` (
  `num_banette_arc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_notice_arc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_diff_arc` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`num_banette_arc`,`num_notice_arc`,`date_diff_arc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `editions_states`
--

CREATE TABLE IF NOT EXISTS `editions_states` (
  `id_editions_state` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `editions_state_name` varchar(255) NOT NULL DEFAULT '',
  `editions_state_num_classement` int(11) NOT NULL DEFAULT 0,
  `editions_state_used_datasource` varchar(50) NOT NULL DEFAULT '',
  `editions_state_comment` text NOT NULL,
  `editions_state_fieldslist` text NOT NULL,
  `editions_state_fieldsparams` text NOT NULL,
  PRIMARY KEY (`id_editions_state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr`
--

CREATE TABLE IF NOT EXISTS `empr` (
  `id_empr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empr_cb` varchar(255) DEFAULT NULL,
  `empr_nom` varchar(255) NOT NULL DEFAULT '',
  `empr_prenom` varchar(255) NOT NULL DEFAULT '',
  `empr_adr1` varchar(255) NOT NULL DEFAULT '',
  `empr_adr2` varchar(255) NOT NULL DEFAULT '',
  `empr_cp` varchar(10) NOT NULL DEFAULT '',
  `empr_ville` varchar(255) NOT NULL DEFAULT '',
  `empr_pays` varchar(255) NOT NULL DEFAULT '',
  `empr_mail` varchar(255) NOT NULL DEFAULT '',
  `empr_tel1` varchar(255) NOT NULL DEFAULT '',
  `empr_tel2` varchar(255) NOT NULL DEFAULT '',
  `empr_prof` varchar(255) NOT NULL DEFAULT '',
  `empr_year` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `empr_categ` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `empr_codestat` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `empr_creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `empr_modif` date NOT NULL DEFAULT '0000-00-00',
  `empr_sexe` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `empr_login` varchar(255) NOT NULL DEFAULT '',
  `empr_password` varchar(255) NOT NULL DEFAULT '',
  `empr_password_is_encrypted` int(1) NOT NULL DEFAULT 0,
  `empr_digest` varchar(255) NOT NULL DEFAULT '',
  `empr_date_adhesion` date DEFAULT NULL,
  `empr_date_expiration` date DEFAULT NULL,
  `empr_msg` text DEFAULT NULL,
  `empr_lang` varchar(10) NOT NULL DEFAULT 'fr_FR',
  `empr_ldap` tinyint(1) UNSIGNED DEFAULT 0,
  `type_abt` int(1) NOT NULL DEFAULT 0,
  `last_loan_date` date DEFAULT NULL,
  `empr_location` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `date_fin_blocage` date NOT NULL DEFAULT '0000-00-00',
  `total_loans` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `empr_statut` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `cle_validation` varchar(255) NOT NULL DEFAULT '',
  `empr_sms` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `empr_subscription_action` text DEFAULT NULL,
  `empr_pnb_password` varchar(255) NOT NULL DEFAULT '',
  `empr_pnb_password_hint` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_empr`),
  UNIQUE KEY `empr_cb` (`empr_cb`),
  KEY `empr_nom` (`empr_nom`),
  KEY `empr_date_adhesion` (`empr_date_adhesion`),
  KEY `empr_date_expiration` (`empr_date_expiration`),
  KEY `i_empr_categ` (`empr_categ`),
  KEY `i_empr_codestat` (`empr_codestat`),
  KEY `i_empr_location` (`empr_location`),
  KEY `i_empr_statut` (`empr_statut`),
  KEY `i_empr_typabt` (`type_abt`),
  KEY `i_empr_login` (`empr_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_caddie`
--

CREATE TABLE IF NOT EXISTS `empr_caddie` (
  `idemprcaddie` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `empr_caddie_classement` varchar(255) NOT NULL DEFAULT '',
  `acces_rapide` int(11) NOT NULL DEFAULT 0,
  `favorite_color` varchar(255) NOT NULL DEFAULT '',
  `creation_user_name` varchar(255) NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idemprcaddie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_caddie_content`
--

CREATE TABLE IF NOT EXISTS `empr_caddie_content` (
  `empr_caddie_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `object_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `flag` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`empr_caddie_id`,`object_id`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_caddie_procs`
--

CREATE TABLE IF NOT EXISTS `empr_caddie_procs` (
  `idproc` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'SELECT',
  `name` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `comment` tinytext NOT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `parameters` text DEFAULT NULL,
  PRIMARY KEY (`idproc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_categ`
--

CREATE TABLE IF NOT EXISTS `empr_categ` (
  `id_categ_empr` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `duree_adhesion` int(10) UNSIGNED DEFAULT 365,
  `tarif_abt` decimal(16,2) NOT NULL DEFAULT 0.00,
  `age_min` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `age_max` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_categ_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_codestat`
--

CREATE TABLE IF NOT EXISTS `empr_codestat` (
  `idcode` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT 'DEFAULT',
  PRIMARY KEY (`idcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_custom`
--

CREATE TABLE IF NOT EXISTS `empr_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
-- Structure de la table `empr_custom_dates`
--

CREATE TABLE IF NOT EXISTS `empr_custom_dates` (
  `empr_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_custom_date_type` int(11) DEFAULT NULL,
  `empr_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `empr_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `empr_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`empr_custom_champ`,`empr_custom_origine`,`empr_custom_order`),
  KEY `empr_custom_champ` (`empr_custom_champ`),
  KEY `empr_custom_origine` (`empr_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_custom_lists`
--

CREATE TABLE IF NOT EXISTS `empr_custom_lists` (
  `empr_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_custom_list_value` varchar(255) DEFAULT NULL,
  `empr_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `empr_custom_champ` (`empr_custom_champ`),
  KEY `i_ecl_lv` (`empr_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_custom_values`
--

CREATE TABLE IF NOT EXISTS `empr_custom_values` (
  `empr_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_custom_small_text` varchar(255) DEFAULT NULL,
  `empr_custom_text` text DEFAULT NULL,
  `empr_custom_integer` int(11) DEFAULT NULL,
  `empr_custom_date` date DEFAULT NULL,
  `empr_custom_float` float DEFAULT NULL,
  `empr_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `empr_custom_champ` (`empr_custom_champ`),
  KEY `empr_custom_origine` (`empr_custom_origine`),
  KEY `i_ecv_st` (`empr_custom_small_text`),
  KEY `i_ecv_t` (`empr_custom_text`(255)),
  KEY `i_ecv_i` (`empr_custom_integer`),
  KEY `i_ecv_d` (`empr_custom_date`),
  KEY `i_ecv_f` (`empr_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_devices`
--

CREATE TABLE IF NOT EXISTS `empr_devices` (
  `empr_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `device_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`empr_num`,`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_grilles`
--

CREATE TABLE IF NOT EXISTS `empr_grilles` (
  `empr_grille_categ` int(5) NOT NULL DEFAULT 0,
  `empr_grille_location` int(5) NOT NULL DEFAULT 0,
  `empr_grille_format` longtext DEFAULT NULL,
  PRIMARY KEY (`empr_grille_categ`,`empr_grille_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_groupe`
--

CREATE TABLE IF NOT EXISTS `empr_groupe` (
  `empr_id` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `groupe_id` int(6) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`empr_id`,`groupe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_renewal_form_fields`
--

CREATE TABLE IF NOT EXISTS `empr_renewal_form_fields` (
  `empr_renewal_form_field_code` varchar(255) NOT NULL,
  `empr_renewal_form_field_display` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `empr_renewal_form_field_mandatory` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `empr_renewal_form_field_alterable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `empr_renewal_form_field_explanation` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`empr_renewal_form_field_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empr_statut`
--

CREATE TABLE IF NOT EXISTS `empr_statut` (
  `idstatut` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `statut_libelle` varchar(255) NOT NULL DEFAULT '',
  `allow_loan` tinyint(4) NOT NULL DEFAULT 1,
  `allow_loan_hist` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `allow_book` tinyint(4) NOT NULL DEFAULT 1,
  `allow_opac` tinyint(4) NOT NULL DEFAULT 1,
  `allow_dsi` tinyint(4) NOT NULL DEFAULT 1,
  `allow_dsi_priv` tinyint(4) NOT NULL DEFAULT 1,
  `allow_sugg` tinyint(4) NOT NULL DEFAULT 1,
  `allow_dema` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `allow_prol` tinyint(4) NOT NULL DEFAULT 1,
  `allow_avis` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `allow_tag` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `allow_pwd` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `allow_liste_lecture` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `allow_self_checkout` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `allow_self_checkin` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
  `allow_serialcirc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `allow_scan_request` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `allow_contribution` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`idstatut`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `empr_statut`
--

INSERT INTO `empr_statut` VALUES
(1, 'Actif', 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0),
(2, 'Interdit', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `empr_temp`
--

CREATE TABLE IF NOT EXISTS `empr_temp` (
  `cb` varchar(255) NOT NULL,
  `sess` varchar(12) NOT NULL,
  UNIQUE KEY `cb` (`cb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `empty_words_calculs`
--

CREATE TABLE IF NOT EXISTS `empty_words_calculs` (
  `id_calcul` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_calcul` date NOT NULL DEFAULT '0000-00-00',
  `php_empty_words` text NOT NULL,
  `nb_notices_calcul` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `archive_calcul` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_calcul`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entites`
--

CREATE TABLE IF NOT EXISTS `entites` (
  `id_entite` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_entite` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_bibli` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `raison_sociale` varchar(255) NOT NULL DEFAULT '',
  `commentaires` text DEFAULT NULL,
  `siret` varchar(255) NOT NULL DEFAULT '',
  `naf` varchar(255) NOT NULL DEFAULT '',
  `rcs` varchar(255) NOT NULL DEFAULT '',
  `tva` varchar(255) NOT NULL DEFAULT '',
  `num_cp_client` varchar(255) NOT NULL DEFAULT '',
  `num_cp_compta` varchar(255) NOT NULL DEFAULT '',
  `site_web` varchar(255) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `autorisations` mediumtext NOT NULL,
  `num_frais` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_paiement` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `index_entite` text NOT NULL,
  PRIMARY KEY (`id_entite`),
  KEY `raison_sociale` (`raison_sociale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entrepots_localisations`
--

CREATE TABLE IF NOT EXISTS `entrepots_localisations` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_code` varchar(255) NOT NULL DEFAULT '',
  `loc_libelle` varchar(255) NOT NULL DEFAULT '',
  `loc_visible` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`loc_id`),
  UNIQUE KEY `loc_code` (`loc_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equations`
--

CREATE TABLE IF NOT EXISTS `equations` (
  `id_equation` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_classement` int(8) UNSIGNED NOT NULL DEFAULT 1,
  `nom_equation` text NOT NULL,
  `comment_equation` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `proprio_equation` int(9) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_equation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `error_log`
--

CREATE TABLE IF NOT EXISTS `error_log` (
  `error_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `error_origin` varchar(255) DEFAULT NULL,
  `error_text` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_cache`
--

CREATE TABLE IF NOT EXISTS `es_cache` (
  `escache_groupname` varchar(100) NOT NULL DEFAULT '',
  `escache_unique_id` varchar(100) NOT NULL DEFAULT '',
  `escache_value` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`escache_groupname`,`escache_unique_id`,`escache_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_cache_blob`
--

CREATE TABLE IF NOT EXISTS `es_cache_blob` (
  `es_cache_objectref` varchar(100) NOT NULL DEFAULT '',
  `es_cache_objecttype` int(11) NOT NULL DEFAULT 0,
  `es_cache_objectformat` varchar(100) NOT NULL DEFAULT '',
  `es_cache_owner` varchar(100) NOT NULL DEFAULT '',
  `es_cache_creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `es_cache_expirationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `es_cache_content` mediumblob NOT NULL,
  PRIMARY KEY (`es_cache_objectref`,`es_cache_objecttype`,`es_cache_objectformat`,`es_cache_owner`),
  KEY `cache_index` (`es_cache_owner`,`es_cache_objectformat`,`es_cache_objecttype`),
  KEY `i_es_cache_expirationdate` (`es_cache_expirationdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_cache_int`
--

CREATE TABLE IF NOT EXISTS `es_cache_int` (
  `es_cache_objectref` varchar(100) NOT NULL DEFAULT '',
  `es_cache_objecttype` int(11) NOT NULL DEFAULT 0,
  `es_cache_objectformat` varchar(100) NOT NULL DEFAULT '',
  `es_cache_owner` varchar(100) NOT NULL DEFAULT '',
  `es_cache_creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `es_cache_expirationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `es_cache_content` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`es_cache_objectref`,`es_cache_objecttype`,`es_cache_objectformat`,`es_cache_owner`),
  KEY `cache_index` (`es_cache_owner`,`es_cache_objectformat`,`es_cache_objecttype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_converted_cache`
--

CREATE TABLE IF NOT EXISTS `es_converted_cache` (
  `es_converted_cache_objecttype` int(11) NOT NULL DEFAULT 0,
  `es_converted_cache_objectref` int(11) NOT NULL DEFAULT 0,
  `es_converted_cache_format` varchar(50) NOT NULL DEFAULT '',
  `es_converted_cache_value` text NOT NULL,
  `es_converted_cache_bestbefore` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`es_converted_cache_objecttype`,`es_converted_cache_objectref`,`es_converted_cache_format`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_esgroups`
--

CREATE TABLE IF NOT EXISTS `es_esgroups` (
  `esgroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `esgroup_name` varchar(100) NOT NULL DEFAULT '',
  `esgroup_fullname` varchar(255) NOT NULL DEFAULT '',
  `esgroup_pmbusernum` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`esgroup_id`),
  UNIQUE KEY `esgroup_name` (`esgroup_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_esgroup_esusers`
--

CREATE TABLE IF NOT EXISTS `es_esgroup_esusers` (
  `esgroupuser_groupnum` int(11) NOT NULL DEFAULT 0,
  `esgroupuser_usertype` int(4) NOT NULL DEFAULT 0,
  `esgroupuser_usernum` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`esgroupuser_usernum`,`esgroupuser_groupnum`,`esgroupuser_usertype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_esusers`
--

CREATE TABLE IF NOT EXISTS `es_esusers` (
  `esuser_id` int(11) NOT NULL AUTO_INCREMENT,
  `esuser_username` varchar(100) NOT NULL DEFAULT '',
  `esuser_password` varchar(100) NOT NULL DEFAULT '',
  `esuser_fullname` varchar(255) NOT NULL DEFAULT '',
  `esuser_groupnum` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`esuser_id`),
  UNIQUE KEY `esuser_username` (`esuser_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_methods`
--

CREATE TABLE IF NOT EXISTS `es_methods` (
  `id_method` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupe` varchar(255) NOT NULL DEFAULT '',
  `method` varchar(255) NOT NULL DEFAULT '',
  `available` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_method`),
  KEY `i_groupe_method_available` (`groupe`(50),`method`(50),`available`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_methods_users`
--

CREATE TABLE IF NOT EXISTS `es_methods_users` (
  `num_method` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `anonymous` smallint(6) DEFAULT 0,
  PRIMARY KEY (`num_method`,`num_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_searchcache`
--

CREATE TABLE IF NOT EXISTS `es_searchcache` (
  `es_searchcache_searchid` varchar(100) NOT NULL DEFAULT '',
  `es_searchcache_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `es_searchcache_serializedsearch` text NOT NULL,
  PRIMARY KEY (`es_searchcache_searchid`),
  KEY `i_es_searchcache_date` (`es_searchcache_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `es_searchsessions`
--

CREATE TABLE IF NOT EXISTS `es_searchsessions` (
  `es_searchsession_id` varchar(100) NOT NULL DEFAULT '',
  `es_searchsession_searchnum` varchar(100) NOT NULL DEFAULT '',
  `es_searchsession_searchrealm` varchar(100) NOT NULL DEFAULT '',
  `es_searchsession_pmbuserid` int(11) NOT NULL DEFAULT -1,
  `es_searchsession_opacemprid` int(11) NOT NULL DEFAULT -1,
  `es_searchsession_lastseendate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`es_searchsession_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etagere`
--

CREATE TABLE IF NOT EXISTS `etagere` (
  `idetagere` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `comment` blob NOT NULL,
  `validite` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `validite_date_deb` date NOT NULL DEFAULT '0000-00-00',
  `validite_date_fin` date NOT NULL DEFAULT '0000-00-00',
  `visible_accueil` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `autorisations` mediumtext DEFAULT NULL,
  `id_tri` int(11) NOT NULL,
  `thumbnail_url` mediumblob NOT NULL,
  `etagere_classement` varchar(255) NOT NULL DEFAULT '',
  `comment_gestion` text NOT NULL,
  PRIMARY KEY (`idetagere`),
  KEY `i_id_tri` (`id_tri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etagere_caddie`
--

CREATE TABLE IF NOT EXISTS `etagere_caddie` (
  `etagere_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `caddie_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`etagere_id`,`caddie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exemplaires`
--

CREATE TABLE IF NOT EXISTS `exemplaires` (
  `expl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `expl_cb` varchar(255) DEFAULT NULL,
  `expl_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_bulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_typdoc` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_cote` varchar(255) DEFAULT NULL,
  `expl_section` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_statut` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_location` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_codestat` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_date_depot` date NOT NULL DEFAULT '0000-00-00',
  `expl_date_retour` date NOT NULL DEFAULT '0000-00-00',
  `expl_note` tinytext NOT NULL,
  `expl_prix` varchar(255) NOT NULL DEFAULT '',
  `expl_owner` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `expl_lastempr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_loan_date` date DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '2005-01-01 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type_antivol` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `transfert_location_origine` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `transfert_statut_origine` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_comment` text DEFAULT NULL,
  `expl_nbparts` int(8) UNSIGNED NOT NULL DEFAULT 1,
  `expl_retloc` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `expl_abt_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transfert_section_origine` smallint(5) NOT NULL DEFAULT 0,
  `expl_ref_num` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`expl_id`),
  UNIQUE KEY `expl_cb` (`expl_cb`),
  KEY `expl_typdoc` (`expl_typdoc`),
  KEY `expl_cote` (`expl_cote`),
  KEY `expl_notice` (`expl_notice`),
  KEY `expl_codestat` (`expl_codestat`),
  KEY `expl_owner` (`expl_owner`),
  KEY `expl_bulletin` (`expl_bulletin`),
  KEY `i_expl_location` (`expl_location`),
  KEY `i_expl_section` (`expl_section`),
  KEY `i_expl_statut` (`expl_statut`),
  KEY `i_expl_lastempr` (`expl_lastempr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exemplaires_temp`
--

CREATE TABLE IF NOT EXISTS `exemplaires_temp` (
  `cb` varchar(50) NOT NULL DEFAULT '',
  `sess` varchar(12) NOT NULL DEFAULT '',
  UNIQUE KEY `cb` (`cb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exercices`
--

CREATE TABLE IF NOT EXISTS `exercices` (
  `id_exercice` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_entite` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `date_debut` date NOT NULL DEFAULT '2006-01-01',
  `date_fin` date NOT NULL DEFAULT '2006-01-01',
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_exercice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum`
--

CREATE TABLE IF NOT EXISTS `explnum` (
  `explnum_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_notice` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_bulletin` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_nom` varchar(255) NOT NULL DEFAULT '',
  `explnum_mimetype` varchar(255) NOT NULL DEFAULT '',
  `explnum_url` text NOT NULL,
  `explnum_data` mediumblob DEFAULT NULL,
  `explnum_vignette` mediumblob DEFAULT NULL,
  `explnum_extfichier` varchar(20) DEFAULT '',
  `explnum_nomfichier` text DEFAULT NULL,
  `explnum_statut` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_index_sew` mediumtext NOT NULL,
  `explnum_index_wew` mediumtext NOT NULL,
  `explnum_repertoire` int(8) NOT NULL DEFAULT 0,
  `explnum_path` text NOT NULL,
  `explnum_docnum_statut` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `explnum_signature` varchar(255) NOT NULL DEFAULT '',
  `explnum_create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `explnum_update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `explnum_file_size` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_id`),
  KEY `explnum_notice` (`explnum_notice`),
  KEY `explnum_bulletin` (`explnum_bulletin`),
  KEY `explnum_repertoire` (`explnum_repertoire`),
  KEY `i_explnum_nomfichier` (`explnum_nomfichier`(30)),
  KEY `i_e_explnum_signature` (`explnum_signature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_custom`
--

CREATE TABLE IF NOT EXISTS `explnum_custom` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_custom_dates`
--

CREATE TABLE IF NOT EXISTS `explnum_custom_dates` (
  `explnum_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_custom_date_type` int(11) DEFAULT NULL,
  `explnum_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `explnum_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `explnum_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`explnum_custom_champ`,`explnum_custom_origine`,`explnum_custom_order`),
  KEY `explnum_custom_champ` (`explnum_custom_champ`),
  KEY `explnum_custom_origine` (`explnum_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_custom_lists`
--

CREATE TABLE IF NOT EXISTS `explnum_custom_lists` (
  `explnum_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_custom_list_value` varchar(255) DEFAULT NULL,
  `explnum_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `explnum_custom_champ` (`explnum_custom_champ`),
  KEY `explnum_champ_list_value` (`explnum_custom_champ`,`explnum_custom_list_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_custom_values`
--

CREATE TABLE IF NOT EXISTS `explnum_custom_values` (
  `explnum_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_custom_small_text` varchar(255) DEFAULT NULL,
  `explnum_custom_text` text DEFAULT NULL,
  `explnum_custom_integer` int(11) DEFAULT NULL,
  `explnum_custom_date` date DEFAULT NULL,
  `explnum_custom_float` float DEFAULT NULL,
  `explnum_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `explnum_custom_champ` (`explnum_custom_champ`),
  KEY `i_encv_st` (`explnum_custom_small_text`),
  KEY `i_encv_t` (`explnum_custom_text`(255)),
  KEY `i_encv_i` (`explnum_custom_integer`),
  KEY `i_encv_d` (`explnum_custom_date`),
  KEY `i_encv_f` (`explnum_custom_float`),
  KEY `explnum_custom_origine` (`explnum_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_doc`
--

CREATE TABLE IF NOT EXISTS `explnum_doc` (
  `id_explnum_doc` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_doc_nomfichier` text NOT NULL,
  `explnum_doc_mimetype` varchar(255) NOT NULL DEFAULT '',
  `explnum_doc_data` mediumblob NOT NULL,
  `explnum_doc_extfichier` varchar(20) NOT NULL DEFAULT '',
  `explnum_doc_url` text NOT NULL,
  PRIMARY KEY (`id_explnum_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_doc_actions`
--

CREATE TABLE IF NOT EXISTS `explnum_doc_actions` (
  `num_explnum_doc` int(10) NOT NULL DEFAULT 0,
  `num_action` int(10) NOT NULL DEFAULT 0,
  `prive` int(1) NOT NULL DEFAULT 0,
  `rapport` int(1) NOT NULL DEFAULT 0,
  `num_explnum` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_explnum_doc`,`num_action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_doc_sugg`
--

CREATE TABLE IF NOT EXISTS `explnum_doc_sugg` (
  `num_explnum_doc` int(10) NOT NULL DEFAULT 0,
  `num_suggestion` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_explnum_doc`,`num_suggestion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_lenders`
--

CREATE TABLE IF NOT EXISTS `explnum_lenders` (
  `explnum_lender_num_explnum` int(11) NOT NULL DEFAULT 0,
  `explnum_lender_num_lender` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_lender_num_explnum`,`explnum_lender_num_lender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_licence`
--

CREATE TABLE IF NOT EXISTS `explnum_licence` (
  `id_explnum_licence` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_licence_label` varchar(255) DEFAULT '',
  `explnum_licence_uri` varchar(255) DEFAULT '',
  PRIMARY KEY (`id_explnum_licence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_licence_profiles`
--

CREATE TABLE IF NOT EXISTS `explnum_licence_profiles` (
  `id_explnum_licence_profile` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_licence_profile_explnum_licence_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_licence_profile_label` varchar(255) DEFAULT '',
  `explnum_licence_profile_uri` varchar(255) DEFAULT '',
  `explnum_licence_profile_logo_url` varchar(255) DEFAULT '',
  `explnum_licence_profile_explanation` text DEFAULT NULL,
  `explnum_licence_profile_quotation_rights` text DEFAULT NULL,
  PRIMARY KEY (`id_explnum_licence_profile`),
  KEY `i_elp_explnum_licence_num` (`explnum_licence_profile_explnum_licence_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_licence_profile_explnums`
--

CREATE TABLE IF NOT EXISTS `explnum_licence_profile_explnums` (
  `explnum_licence_profile_explnums_explnum_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_licence_profile_explnums_profile_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_licence_profile_explnums_explnum_num`,`explnum_licence_profile_explnums_profile_num`),
  KEY `i_elpe_explnum_profile_num` (`explnum_licence_profile_explnums_profile_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_licence_profile_rights`
--

CREATE TABLE IF NOT EXISTS `explnum_licence_profile_rights` (
  `explnum_licence_profile_num` int(11) NOT NULL DEFAULT 0,
  `explnum_licence_right_num` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_licence_profile_num`,`explnum_licence_right_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_licence_rights`
--

CREATE TABLE IF NOT EXISTS `explnum_licence_rights` (
  `id_explnum_licence_right` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_licence_right_explnum_licence_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_licence_right_label` varchar(255) DEFAULT '',
  `explnum_licence_right_type` int(2) DEFAULT 0,
  `explnum_licence_right_logo_url` varchar(255) DEFAULT '',
  `explnum_licence_right_explanation` text DEFAULT NULL,
  PRIMARY KEY (`id_explnum_licence_right`),
  KEY `i_elr_explnum_licence_num` (`explnum_licence_right_explnum_licence_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_location`
--

CREATE TABLE IF NOT EXISTS `explnum_location` (
  `num_explnum` int(10) NOT NULL DEFAULT 0,
  `num_location` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_explnum`,`num_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_segments`
--

CREATE TABLE IF NOT EXISTS `explnum_segments` (
  `explnum_segment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_segment_explnum_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_segment_speaker_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_segment_start` double NOT NULL DEFAULT 0,
  `explnum_segment_duration` double NOT NULL DEFAULT 0,
  `explnum_segment_end` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_segment_id`),
  KEY `i_ensg_explnum_num` (`explnum_segment_explnum_num`),
  KEY `i_ensg_speaker` (`explnum_segment_speaker_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_speakers`
--

CREATE TABLE IF NOT EXISTS `explnum_speakers` (
  `explnum_speaker_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `explnum_speaker_explnum_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_speaker_speaker_num` varchar(10) NOT NULL DEFAULT '',
  `explnum_speaker_gender` varchar(1) DEFAULT '',
  `explnum_speaker_author` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`explnum_speaker_id`),
  KEY `i_ensk_explnum_num` (`explnum_speaker_explnum_num`),
  KEY `i_ensk_author` (`explnum_speaker_author`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `explnum_statut`
--

CREATE TABLE IF NOT EXISTS `explnum_statut` (
  `id_explnum_statut` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gestion_libelle` varchar(255) NOT NULL DEFAULT '',
  `opac_libelle` varchar(255) NOT NULL DEFAULT '',
  `class_html` varchar(255) NOT NULL DEFAULT '',
  `explnum_visible_opac` tinyint(1) NOT NULL DEFAULT 1,
  `explnum_visible_opac_abon` tinyint(1) NOT NULL DEFAULT 0,
  `explnum_consult_opac` tinyint(1) NOT NULL DEFAULT 1,
  `explnum_consult_opac_abon` tinyint(1) NOT NULL DEFAULT 0,
  `explnum_download_opac` tinyint(1) NOT NULL DEFAULT 1,
  `explnum_download_opac_abon` tinyint(1) NOT NULL DEFAULT 0,
  `explnum_thumbnail_visible_opac_override` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_explnum_statut`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `explnum_statut`
--

INSERT INTO `explnum_statut` VALUES
(1, 'Sans statut particulier', '', '', 1, 0, 1, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `expl_custom`
--

CREATE TABLE IF NOT EXISTS `expl_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
-- Structure de la table `expl_custom_dates`
--

CREATE TABLE IF NOT EXISTS `expl_custom_dates` (
  `expl_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_custom_date_type` int(11) DEFAULT NULL,
  `expl_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `expl_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `expl_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`expl_custom_champ`,`expl_custom_origine`,`expl_custom_order`),
  KEY `expl_custom_champ` (`expl_custom_champ`),
  KEY `expl_custom_origine` (`expl_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `expl_custom_lists`
--

CREATE TABLE IF NOT EXISTS `expl_custom_lists` (
  `expl_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_custom_list_value` varchar(255) DEFAULT NULL,
  `expl_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `expl_custom_champ` (`expl_custom_champ`),
  KEY `i_excl_lv` (`expl_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `expl_custom_values`
--

CREATE TABLE IF NOT EXISTS `expl_custom_values` (
  `expl_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expl_custom_small_text` varchar(255) DEFAULT NULL,
  `expl_custom_text` text DEFAULT NULL,
  `expl_custom_integer` int(11) DEFAULT NULL,
  `expl_custom_date` date DEFAULT NULL,
  `expl_custom_float` float DEFAULT NULL,
  `expl_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `expl_custom_champ` (`expl_custom_champ`),
  KEY `expl_custom_origine` (`expl_custom_origine`),
  KEY `i_excv_st` (`expl_custom_small_text`),
  KEY `i_excv_t` (`expl_custom_text`(255)),
  KEY `i_excv_i` (`expl_custom_integer`),
  KEY `i_excv_d` (`expl_custom_date`),
  KEY `i_excv_f` (`expl_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `external_count`
--

CREATE TABLE IF NOT EXISTS `external_count` (
  `rid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `recid` varchar(255) NOT NULL DEFAULT '',
  `source_id` int(11) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `recid` (`recid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facettes`
--

CREATE TABLE IF NOT EXISTS `facettes` (
  `id_facette` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `facette_type` varchar(255) NOT NULL DEFAULT 'notices',
  `facette_name` varchar(255) NOT NULL DEFAULT '',
  `facette_critere` int(5) NOT NULL DEFAULT 0,
  `facette_ss_critere` int(5) NOT NULL DEFAULT 0,
  `facette_nb_result` int(2) NOT NULL DEFAULT 0,
  `facette_visible_gestion` tinyint(1) NOT NULL DEFAULT 0,
  `facette_visible` tinyint(1) NOT NULL DEFAULT 0,
  `facette_type_sort` int(1) NOT NULL DEFAULT 0,
  `facette_order_sort` int(1) NOT NULL DEFAULT 0,
  `facette_datatype_sort` varchar(255) NOT NULL DEFAULT 'alpha',
  `facette_order` int(11) NOT NULL DEFAULT 1,
  `facette_limit_plus` int(11) NOT NULL DEFAULT 0,
  `facette_opac_views_num` text NOT NULL,
  PRIMARY KEY (`id_facette`),
  KEY `i_facette_visible` (`facette_visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facettes_external`
--

CREATE TABLE IF NOT EXISTS `facettes_external` (
  `id_facette` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `facette_type` varchar(255) NOT NULL DEFAULT 'notices',
  `facette_name` varchar(255) NOT NULL DEFAULT '',
  `facette_critere` int(5) NOT NULL DEFAULT 0,
  `facette_ss_critere` int(5) NOT NULL DEFAULT 0,
  `facette_nb_result` int(2) NOT NULL DEFAULT 0,
  `facette_visible_gestion` tinyint(1) NOT NULL DEFAULT 0,
  `facette_visible` tinyint(1) NOT NULL DEFAULT 0,
  `facette_type_sort` int(1) NOT NULL DEFAULT 0,
  `facette_order_sort` int(1) NOT NULL DEFAULT 0,
  `facette_datatype_sort` varchar(255) NOT NULL DEFAULT 'alpha',
  `facette_order` int(11) NOT NULL DEFAULT 1,
  `facette_limit_plus` int(11) NOT NULL DEFAULT 0,
  `facette_opac_views_num` text NOT NULL,
  PRIMARY KEY (`id_facette`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_questions`
--

CREATE TABLE IF NOT EXISTS `faq_questions` (
  `id_faq_question` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faq_question_num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `faq_question_num_theme` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `faq_question_num_demande` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `faq_question_question` text NOT NULL,
  `faq_question_question_userdate` varchar(255) NOT NULL DEFAULT '',
  `faq_question_question_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `faq_question_answer` text NOT NULL,
  `faq_question_answer_userdate` varchar(255) NOT NULL DEFAULT '',
  `faq_question_answer_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `faq_question_statut` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_faq_question`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_questions_categories`
--

CREATE TABLE IF NOT EXISTS `faq_questions_categories` (
  `num_faq_question` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_categ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `categ_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  KEY `i_faq_categ` (`num_faq_question`,`num_categ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_questions_fields_global_index`
--

CREATE TABLE IF NOT EXISTS `faq_questions_fields_global_index` (
  `id_faq_question` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `ordre` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  `pond` int(4) UNSIGNED NOT NULL DEFAULT 100,
  `lang` varchar(10) NOT NULL DEFAULT '',
  `authority_num` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_faq_question`,`code_champ`,`code_ss_champ`,`lang`,`ordre`),
  KEY `i_value` (`value`(300)),
  KEY `i_code_champ_code_ss_champ` (`code_champ`,`code_ss_champ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_questions_words_global_index`
--

CREATE TABLE IF NOT EXISTS `faq_questions_words_global_index` (
  `id_faq_question` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_ss_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_word` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `field_position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_faq_question`,`code_champ`,`code_ss_champ`,`num_word`,`position`,`field_position`),
  KEY `code_champ` (`code_champ`),
  KEY `i_id_mot` (`num_word`,`id_faq_question`),
  KEY `i_code_champ_code_ss_champ_num_word` (`code_champ`,`code_ss_champ`,`num_word`),
  KEY `i_num_word` (`num_word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_themes`
--

CREATE TABLE IF NOT EXISTS `faq_themes` (
  `id_theme` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_theme` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faq_types`
--

CREATE TABLE IF NOT EXISTS `faq_types` (
  `id_type` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fiche`
--

CREATE TABLE IF NOT EXISTS `fiche` (
  `id_fiche` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `infos_global` text NOT NULL,
  `index_infos_global` text NOT NULL,
  PRIMARY KEY (`id_fiche`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frais`
--

CREATE TABLE IF NOT EXISTS `frais` (
  `id_frais` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `condition_frais` text NOT NULL,
  `montant` double(12,2) NOT NULL DEFAULT 0.00,
  `num_cp_compta` varchar(255) NOT NULL DEFAULT '',
  `num_tva_achat` varchar(25) NOT NULL DEFAULT '0',
  `index_libelle` text DEFAULT NULL,
  PRIMARY KEY (`id_frais`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_cadres`
--

CREATE TABLE IF NOT EXISTS `frbr_cadres` (
  `id_cadre` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cadre_name` varchar(255) NOT NULL DEFAULT '',
  `cadre_comment` text NOT NULL,
  `cadre_object` varchar(255) NOT NULL DEFAULT '',
  `cadre_css_class` varchar(255) NOT NULL DEFAULT '',
  `cadre_num_datanode` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cadre_num_page` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cadre_visible_in_graph` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cadre_datanodes_path` varchar(255) DEFAULT NULL,
  `cadre_display_empty_template` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_cadre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_cadres_content`
--

CREATE TABLE IF NOT EXISTS `frbr_cadres_content` (
  `id_cadre_content` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cadre_content_type` varchar(255) NOT NULL DEFAULT '',
  `cadre_content_object` varchar(255) NOT NULL DEFAULT '',
  `cadre_content_num_cadre` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cadre_content_data` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id_cadre_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_cataloging_categories`
--

CREATE TABLE IF NOT EXISTS `frbr_cataloging_categories` (
  `id_cataloging_category` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cataloging_category_title` varchar(255) NOT NULL DEFAULT '',
  `cataloging_category_num_parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_cataloging_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_cataloging_datanodes`
--

CREATE TABLE IF NOT EXISTS `frbr_cataloging_datanodes` (
  `id_cataloging_datanode` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cataloging_datanode_title` varchar(255) NOT NULL DEFAULT '',
  `cataloging_datanode_comment` text NOT NULL,
  `cataloging_datanode_owner` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cataloging_datanode_allowed_users` varchar(255) NOT NULL DEFAULT '',
  `cataloging_datanode_num_category` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_cataloging_datanode`),
  KEY `i_cataloging_datanode_title` (`cataloging_datanode_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_cataloging_items`
--

CREATE TABLE IF NOT EXISTS `frbr_cataloging_items` (
  `num_cataloging_item` int(10) UNSIGNED NOT NULL,
  `type_cataloging_item` varchar(255) NOT NULL DEFAULT '',
  `cataloging_item_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cataloging_item_added_date` datetime DEFAULT NULL,
  `cataloging_item_num_datanode` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_cataloging_item`,`type_cataloging_item`,`cataloging_item_num_datanode`),
  KEY `i_cataloging_item_num_datanode` (`cataloging_item_num_datanode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_datanodes`
--

CREATE TABLE IF NOT EXISTS `frbr_datanodes` (
  `id_datanode` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datanode_name` varchar(255) NOT NULL DEFAULT '',
  `datanode_comment` text NOT NULL,
  `datanode_object` varchar(255) NOT NULL DEFAULT '',
  `datanode_num_page` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datanode_num_parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_datanode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_datanodes_content`
--

CREATE TABLE IF NOT EXISTS `frbr_datanodes_content` (
  `id_datanode_content` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datanode_content_type` varchar(255) NOT NULL DEFAULT '',
  `datanode_content_object` varchar(255) NOT NULL DEFAULT '',
  `datanode_content_num_datanode` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `datanode_content_data` text NOT NULL,
  PRIMARY KEY (`id_datanode_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_managed_entities`
--

CREATE TABLE IF NOT EXISTS `frbr_managed_entities` (
  `managed_entity_name` varchar(255) NOT NULL DEFAULT '',
  `managed_entity_box` text NOT NULL,
  PRIMARY KEY (`managed_entity_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_pages`
--

CREATE TABLE IF NOT EXISTS `frbr_pages` (
  `id_page` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL DEFAULT '',
  `page_comment` text NOT NULL,
  `page_entity` varchar(255) NOT NULL DEFAULT '',
  `page_parameters` text NOT NULL,
  `page_opac_views` varchar(255) NOT NULL DEFAULT '',
  `page_order` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_pages_content`
--

CREATE TABLE IF NOT EXISTS `frbr_pages_content` (
  `id_page_content` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_content_type` varchar(255) NOT NULL DEFAULT '',
  `page_content_object` varchar(255) NOT NULL DEFAULT '',
  `page_content_num_page` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `page_content_data` text NOT NULL,
  PRIMARY KEY (`id_page_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frbr_place`
--

CREATE TABLE IF NOT EXISTS `frbr_place` (
  `place_num_page` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `place_num_cadre` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `place_cadre_type` varchar(255) NOT NULL DEFAULT '',
  `place_visibility` int(1) NOT NULL DEFAULT 0,
  `place_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`place_num_page`,`place_num_cadre`,`place_cadre_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gestfic0_custom`
--

CREATE TABLE IF NOT EXISTS `gestfic0_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
-- Structure de la table `gestfic0_custom_lists`
--

CREATE TABLE IF NOT EXISTS `gestfic0_custom_lists` (
  `gestfic0_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `gestfic0_custom_list_value` varchar(255) DEFAULT NULL,
  `gestfic0_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `gestfic0_custom_champ` (`gestfic0_custom_champ`),
  KEY `gestfic0_champ_list_value` (`gestfic0_custom_champ`,`gestfic0_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gestfic0_custom_values`
--

CREATE TABLE IF NOT EXISTS `gestfic0_custom_values` (
  `gestfic0_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `gestfic0_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `gestfic0_custom_small_text` varchar(255) DEFAULT NULL,
  `gestfic0_custom_text` text DEFAULT NULL,
  `gestfic0_custom_integer` int(11) DEFAULT NULL,
  `gestfic0_custom_date` date DEFAULT NULL,
  `gestfic0_custom_float` float DEFAULT NULL,
  `gestfic0_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `gestfic0_custom_champ` (`gestfic0_custom_champ`),
  KEY `gestfic0_custom_origine` (`gestfic0_custom_origine`),
  KEY `i_gcv_st` (`gestfic0_custom_small_text`),
  KEY `i_gcv_t` (`gestfic0_custom_text`(255)),
  KEY `i_gcv_i` (`gestfic0_custom_integer`),
  KEY `i_gcv_d` (`gestfic0_custom_date`),
  KEY `i_gcv_f` (`gestfic0_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_category`
--

CREATE TABLE IF NOT EXISTS `gradebook_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `course_code` varchar(40) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `weight` smallint(6) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `certif_min_score` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `document_id` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_certificate`
--

CREATE TABLE IF NOT EXISTS `gradebook_certificate` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `score_certificate` float UNSIGNED NOT NULL DEFAULT 0,
  `date_certificate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `path_certificate` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gradebook_certificate_category_id` (`cat_id`),
  KEY `idx_gradebook_certificate_user_id` (`user_id`),
  KEY `idx_gradebook_certificate_category_id_user_id` (`cat_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_evaluation`
--

CREATE TABLE IF NOT EXISTS `gradebook_evaluation` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `course_code` varchar(40) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT 0,
  `weight` smallint(6) NOT NULL,
  `max` float UNSIGNED NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `type` varchar(40) NOT NULL DEFAULT 'evaluation',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_link`
--

CREATE TABLE IF NOT EXISTS `gradebook_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_code` varchar(40) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` int(11) DEFAULT NULL,
  `weight` smallint(6) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_linkeval_log`
--

CREATE TABLE IF NOT EXISTS `gradebook_linkeval_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_linkeval_log` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_log` int(11) DEFAULT NULL,
  `weight` smallint(6) DEFAULT NULL,
  `visible` tinyint(4) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `user_id_log` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_result`
--

CREATE TABLE IF NOT EXISTS `gradebook_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `score` float UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_result_log`
--

CREATE TABLE IF NOT EXISTS `gradebook_result_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_result` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `date_log` datetime DEFAULT '0000-00-00 00:00:00',
  `score` float UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gradebook_score_display`
--

CREATE TABLE IF NOT EXISTS `gradebook_score_display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` float UNSIGNED NOT NULL,
  `display` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `grids_generic`
--

CREATE TABLE IF NOT EXISTS `grids_generic` (
  `grid_generic_type` varchar(32) NOT NULL DEFAULT '',
  `grid_generic_filter` varchar(255) NOT NULL DEFAULT '',
  `grid_generic_data` mediumblob NOT NULL,
  PRIMARY KEY (`grid_generic_type`,`grid_generic_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `grids_generic`
--

INSERT INTO `grids_generic` VALUES
('auteurs', '70', 0x5b7b226e6f64654964223a22656c30222c226c6162656c223a225a6f6e65207061722064753030653966617574222c226973457870616e6461626c65223a66616c73652c2273686f774c6162656c223a66616c73652c2276697369626c65223a747275652c22656c656d656e7473223a5b7b226e6f64654964223a22656c304368696c645f30222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f315f61222c2276697369626c65223a747275652c22636c6173734e616d65223a22636f6c6f6e6e6532227d2c7b226e6f64654964223a22656c304368696c645f315f62222c2276697369626c65223a747275652c22636c6173734e616d65223a22636f6c6f6e6e655f7375697465227d2c7b226e6f64654964223a22656c304368696c645f32222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f33222c2276697369626c65223a66616c73652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f345f61222c2276697369626c65223a66616c73652c22636c6173734e616d65223a22636f6c6f6e6e6532227d2c7b226e6f64654964223a22656c304368696c645f345f62222c2276697369626c65223a66616c73652c22636c6173734e616d65223a22636f6c6f6e6e655f7375697465227d2c7b226e6f64654964223a22656c304368696c645f355f61222c2276697369626c65223a66616c73652c22636c6173734e616d65223a22636f6c6f6e6e6532227d2c7b226e6f64654964223a22656c304368696c645f355f62222c2276697369626c65223a66616c73652c22636c6173734e616d65223a22636f6c6f6e6e655f7375697465227d2c7b226e6f64654964223a22656c304368696c645f36222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f37222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f38222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c364368696c645f33222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c304368696c645f39222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d2c7b226e6f64654964223a22656c374368696c645f30222c2276697369626c65223a747275652c22636c6173734e616d65223a22726f77227d5d7d5d);

-- --------------------------------------------------------

--
-- Structure de la table `grilles`
--

CREATE TABLE IF NOT EXISTS `grilles` (
  `grille_typdoc` char(2) NOT NULL DEFAULT 'a',
  `grille_niveau_biblio` char(1) NOT NULL DEFAULT 'm',
  `grille_localisation` mediumint(8) NOT NULL DEFAULT 0,
  `descr_format` longtext DEFAULT NULL,
  PRIMARY KEY (`grille_typdoc`,`grille_niveau_biblio`,`grille_localisation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `picture_uri` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `visibility` int(11) NOT NULL,
  `updated_on` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupe`
--

CREATE TABLE IF NOT EXISTS `groupe` (
  `id_groupe` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_groupe` varchar(255) NOT NULL,
  `resp_groupe` int(6) UNSIGNED DEFAULT 0,
  `lettre_rappel` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `mail_rappel` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `lettre_rappel_show_nomgroup` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_groupe`),
  UNIQUE KEY `libelle_groupe` (`libelle_groupe`),
  KEY `i_resp_groupe` (`resp_groupe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupexpl`
--

CREATE TABLE IF NOT EXISTS `groupexpl` (
  `id_groupexpl` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupexpl_resp_expl_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupexpl_name` varchar(255) NOT NULL DEFAULT '',
  `groupexpl_comment` varchar(255) NOT NULL DEFAULT '',
  `groupexpl_location` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupexpl_statut_resp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupexpl_statut_others` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_groupexpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groupexpl_expl`
--

CREATE TABLE IF NOT EXISTS `groupexpl_expl` (
  `groupexpl_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupexpl_expl_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `groupexpl_checked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`groupexpl_num`,`groupexpl_expl_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `group_rel_tag`
--

CREATE TABLE IF NOT EXISTS `group_rel_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `group_rel_user`
--

CREATE TABLE IF NOT EXISTS `group_rel_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `relation_type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_field`
--

CREATE TABLE IF NOT EXISTS `harvest_field` (
  `id_harvest_field` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_harvest_profil` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_field_xml_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_field_first_flag` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_field_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_harvest_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_profil`
--

CREATE TABLE IF NOT EXISTS `harvest_profil` (
  `id_harvest_profil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `harvest_profil_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_harvest_profil`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_profil_import`
--

CREATE TABLE IF NOT EXISTS `harvest_profil_import` (
  `id_harvest_profil_import` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `harvest_profil_import_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_harvest_profil_import`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_profil_import_field`
--

CREATE TABLE IF NOT EXISTS `harvest_profil_import_field` (
  `num_harvest_profil_import` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_profil_import_field_xml_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_profil_import_field_flag` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_profil_import_field_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_harvest_profil_import`,`harvest_profil_import_field_xml_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_search_field`
--

CREATE TABLE IF NOT EXISTS `harvest_search_field` (
  `num_harvest_profil` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_source` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_field` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_ss_field` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_harvest_profil`,`num_source`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `harvest_src`
--

CREATE TABLE IF NOT EXISTS `harvest_src` (
  `id_harvest_src` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_harvest_field` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_source` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_src_unimacfield` varchar(255) NOT NULL DEFAULT '',
  `harvest_src_unimacsubfield` varchar(255) NOT NULL DEFAULT '',
  `harvest_src_pmb_unimacfield` varchar(255) NOT NULL DEFAULT '',
  `harvest_src_pmb_unimacsubfield` varchar(255) NOT NULL DEFAULT '',
  `harvest_src_prec_flag` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `harvest_src_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_harvest_src`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `import_marc`
--

CREATE TABLE IF NOT EXISTS `import_marc` (
  `id_import` bigint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notice` longblob NOT NULL,
  `origine` varchar(50) DEFAULT '',
  `no_notice` int(10) UNSIGNED DEFAULT 0,
  `encoding` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_import`),
  KEY `i_nonot_orig` (`no_notice`,`origine`),
  KEY `i_origine` (`origine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indexation_stack`
--

CREATE TABLE IF NOT EXISTS `indexation_stack` (
  `indexation_stack_entity_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `indexation_stack_entity_type` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `indexation_stack_datatype` varchar(255) NOT NULL DEFAULT '',
  `indexation_stack_timestamp` bigint(20) NOT NULL DEFAULT 0,
  `indexation_stack_parent_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `indexation_stack_parent_type` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`indexation_stack_entity_id`,`indexation_stack_entity_type`,`indexation_stack_datatype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indexint`
--

CREATE TABLE IF NOT EXISTS `indexint` (
  `indexint_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `indexint_name` varchar(255) NOT NULL DEFAULT '',
  `indexint_comment` text NOT NULL,
  `index_indexint` text DEFAULT NULL,
  `num_pclass` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`indexint_id`),
  UNIQUE KEY `indexint_name` (`indexint_name`,`num_pclass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indexint_custom`
--

CREATE TABLE IF NOT EXISTS `indexint_custom` (
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
-- Structure de la table `indexint_custom_dates`
--

CREATE TABLE IF NOT EXISTS `indexint_custom_dates` (
  `indexint_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `indexint_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `indexint_custom_date_type` int(11) DEFAULT NULL,
  `indexint_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `indexint_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `indexint_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`indexint_custom_champ`,`indexint_custom_origine`,`indexint_custom_order`),
  KEY `indexint_custom_champ` (`indexint_custom_champ`),
  KEY `indexint_custom_origine` (`indexint_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indexint_custom_lists`
--

CREATE TABLE IF NOT EXISTS `indexint_custom_lists` (
  `indexint_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `indexint_custom_list_value` varchar(255) DEFAULT NULL,
  `indexint_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`indexint_custom_champ`),
  KEY `editorial_champ_list_value` (`indexint_custom_champ`,`indexint_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indexint_custom_values`
--

CREATE TABLE IF NOT EXISTS `indexint_custom_values` (
  `indexint_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `indexint_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `indexint_custom_small_text` varchar(255) DEFAULT NULL,
  `indexint_custom_text` text DEFAULT NULL,
  `indexint_custom_integer` int(11) DEFAULT NULL,
  `indexint_custom_date` date DEFAULT NULL,
  `indexint_custom_float` float DEFAULT NULL,
  `indexint_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`indexint_custom_champ`),
  KEY `editorial_custom_origine` (`indexint_custom_origine`),
  KEY `i_icv_st` (`indexint_custom_small_text`),
  KEY `i_icv_t` (`indexint_custom_text`(255)),
  KEY `i_icv_i` (`indexint_custom_integer`),
  KEY `i_icv_d` (`indexint_custom_date`),
  KEY `i_icv_f` (`indexint_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `index_concept`
--

CREATE TABLE IF NOT EXISTS `index_concept` (
  `num_object` int(10) UNSIGNED NOT NULL,
  `type_object` int(10) UNSIGNED NOT NULL,
  `num_concept` int(10) UNSIGNED NOT NULL,
  `order_concept` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_visible_opac` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_object`,`type_object`,`num_concept`),
  KEY `i_num_concept_type_object` (`num_concept`,`type_object`),
  KEY `i_type_object_num_object` (`type_object`,`num_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `infopages`
--

CREATE TABLE IF NOT EXISTS `infopages` (
  `id_infopage` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_infopage` longblob NOT NULL,
  `title_infopage` varchar(255) NOT NULL DEFAULT '',
  `valid_infopage` tinyint(1) NOT NULL DEFAULT 1,
  `restrict_infopage` int(11) NOT NULL DEFAULT 0,
  `infopage_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_infopage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `original_name` varchar(255) DEFAULT NULL,
  `english_name` varchar(255) DEFAULT NULL,
  `isocode` varchar(10) DEFAULT NULL,
  `dokeos_folder` varchar(250) DEFAULT NULL,
  `available` tinyint(4) NOT NULL DEFAULT 1,
  `parent_id` tinyint(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dokeos_folder` (`dokeos_folder`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `language`
--

INSERT INTO `language` VALUES
(1, 'Arabija', 'arabic', 'ar', 'arabic', 0, NULL),
(2, 'Brazil', 'brazilian', 'pt-BR', 'brazilian', 1, NULL),
(3, 'Balgarski', 'bulgarian', 'bg', 'bulgarian', 0, NULL),
(4, 'Catalan', 'catalan', 'ca', 'catalan', 0, NULL),
(5, 'Hrvatski', 'croatian', 'hr', 'croatian', 0, NULL),
(6, 'Dansk', 'danish', 'da', 'danish', 0, NULL),
(7, 'Nederlands', 'dutch', 'nl', 'dutch', 1, NULL),
(8, 'English', 'english', 'en', 'english', 1, NULL),
(9, 'Suomi', 'finnish', 'fi', 'finnish', 0, NULL),
(10, 'Français', 'french', 'fr', 'french', 1, NULL),
(11, 'Galego', 'galician', 'gl', 'galician', 0, NULL),
(12, 'Deutsch', 'german', 'de', 'german', 1, NULL),
(13, 'Ellinika', 'greek', 'el', 'greek', 0, NULL),
(14, 'Magyar', 'hungarian', 'hu', 'hungarian', 1, NULL),
(15, 'Indonesia', 'indonesian', 'id', 'indonesian', 1, NULL),
(16, 'Italiano', 'italian', 'it', 'italian', 1, NULL),
(17, 'Nihongo', 'japanese', 'ja', 'japanese', 0, NULL),
(18, 'Melayu', 'malay', 'ms', 'malay', 0, NULL),
(19, 'Polski', 'polish', 'pl', 'polish', 0, NULL),
(20, 'Portugais', 'portuguese', 'pt', 'portuguese', 1, NULL),
(21, 'Russkij', 'russian', 'ru', 'russian', 0, NULL),
(22, 'Chinese', 'simpl_chinese', 'zh', 'simpl_chinese', 0, NULL),
(23, 'Slovenscina', 'slovenian', 'sl', 'slovenian', 1, NULL),
(24, 'Espagnol', 'spanish', 'es', 'spanish', 1, NULL),
(25, 'Svenska', 'swedish', 'sv', 'swedish', 0, NULL),
(26, 'Thai', 'thai', 'th', 'thai', 0, NULL),
(27, 'Turque', 'turkce', 'tr', 'turkce', 0, NULL),
(28, 'Vietnam', 'vietnamese', 'vi', 'vietnamese', 0, NULL),
(29, 'Norsk', 'norwegian', 'no', 'norwegian', 0, NULL),
(30, 'Farsi', 'persian', 'fa', 'persian', 0, NULL),
(31, 'Srpski', 'serbian', 'sr', 'serbian', 0, NULL),
(32, 'Bosanski', 'bosnian', NULL, 'bosnian', 1, NULL),
(33, 'Swahili', 'swahili', 'sw', 'swahili', 0, NULL),
(34, 'Esperanto', 'esperanto', 'eo', 'esperanto', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `legal`
--

CREATE TABLE IF NOT EXISTS `legal` (
  `legal_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `date` int(11) NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `type` int(11) NOT NULL,
  `changes` text NOT NULL,
  `version` int(11) DEFAULT NULL,
  PRIMARY KEY (`legal_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lenders`
--

CREATE TABLE IF NOT EXISTS `lenders` (
  `idlender` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lender_libelle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idlender`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `lenders`
--

INSERT INTO `lenders` VALUES
(1, 'BDP'),
(2, 'Fonds propre');

-- --------------------------------------------------------

--
-- Structure de la table `liens_actes`
--

CREATE TABLE IF NOT EXISTS `liens_actes` (
  `num_acte` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_acte_lie` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_acte`,`num_acte_lie`),
  KEY `i_num_acte` (`num_acte`),
  KEY `i_num_acte_lie` (`num_acte_lie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lignes_actes`
--

CREATE TABLE IF NOT EXISTS `lignes_actes` (
  `id_ligne` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_ligne` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_acte` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `lig_ref` int(15) UNSIGNED NOT NULL DEFAULT 0,
  `num_acquisition` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `num_rubrique` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_produit` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_type` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` text NOT NULL,
  `code` varchar(255) NOT NULL DEFAULT '',
  `prix` double(12,2) NOT NULL DEFAULT 0.00,
  `tva` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `nb` int(5) UNSIGNED NOT NULL DEFAULT 1,
  `date_ech` date NOT NULL DEFAULT '0000-00-00',
  `date_cre` date NOT NULL DEFAULT '0000-00-00',
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `remise` float(8,2) NOT NULL DEFAULT 0.00,
  `index_ligne` text NOT NULL,
  `ligne_ordre` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  `debit_tva` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  `commentaires_gestion` text NOT NULL,
  `commentaires_opac` text NOT NULL,
  PRIMARY KEY (`id_ligne`),
  KEY `num_acte` (`num_acte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lignes_actes_applicants`
--

CREATE TABLE IF NOT EXISTS `lignes_actes_applicants` (
  `ligne_acte_num` int(11) NOT NULL DEFAULT 0,
  `empr_num` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ligne_acte_num`,`empr_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lignes_actes_relances`
--

CREATE TABLE IF NOT EXISTS `lignes_actes_relances` (
  `num_ligne` int(10) UNSIGNED NOT NULL,
  `date_relance` date NOT NULL DEFAULT '0000-00-00',
  `type_ligne` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_acte` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `lig_ref` int(15) UNSIGNED NOT NULL DEFAULT 0,
  `num_acquisition` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `num_rubrique` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_produit` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_type` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` text NOT NULL,
  `code` varchar(255) NOT NULL DEFAULT '',
  `prix` float(8,2) NOT NULL DEFAULT 0.00,
  `tva` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `nb` int(5) UNSIGNED NOT NULL DEFAULT 1,
  `date_ech` date NOT NULL DEFAULT '0000-00-00',
  `date_cre` date NOT NULL DEFAULT '0000-00-00',
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `remise` float(8,2) NOT NULL DEFAULT 0.00,
  `index_ligne` text NOT NULL,
  `ligne_ordre` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  `debit_tva` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  `commentaires_gestion` text NOT NULL,
  `commentaires_opac` text NOT NULL,
  PRIMARY KEY (`num_ligne`,`date_relance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lignes_actes_statuts`
--

CREATE TABLE IF NOT EXISTS `lignes_actes_statuts` (
  `id_statut` int(3) NOT NULL AUTO_INCREMENT,
  `libelle` text NOT NULL,
  `relance` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_statut`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `lignes_actes_statuts`
--

INSERT INTO `lignes_actes_statuts` VALUES
(1, 'Traitement normal', 1);

-- --------------------------------------------------------

--
-- Structure de la table `linked_mots`
--

CREATE TABLE IF NOT EXISTS `linked_mots` (
  `num_mot` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_linked_mot` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `type_lien` tinyint(1) NOT NULL DEFAULT 1,
  `ponderation` float NOT NULL DEFAULT 1,
  PRIMARY KEY (`num_mot`,`num_linked_mot`,`type_lien`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lists`
--

CREATE TABLE IF NOT EXISTS `lists` (
  `id_list` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `list_num_user` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `list_objects_type` varchar(255) NOT NULL DEFAULT '',
  `list_label` varchar(255) NOT NULL DEFAULT '',
  `list_selected_columns` text DEFAULT NULL,
  `list_filters` text DEFAULT NULL,
  `list_applied_group` text DEFAULT NULL,
  `list_applied_sort` text DEFAULT NULL,
  `list_pager` text DEFAULT NULL,
  `list_selected_filters` text DEFAULT NULL,
  `list_autorisations` mediumtext DEFAULT NULL,
  `list_default_selected` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `list_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `locked_entities`
--

CREATE TABLE IF NOT EXISTS `locked_entities` (
  `id_entity` int(10) UNSIGNED NOT NULL,
  `type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `parent_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `empr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_entity`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logopac`
--

CREATE TABLE IF NOT EXISTS `logopac` (
  `id_log` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `url_demandee` varchar(255) NOT NULL DEFAULT '',
  `url_referente` varchar(255) NOT NULL DEFAULT '',
  `get_log` blob NOT NULL,
  `post_log` blob NOT NULL,
  `num_session` varchar(255) NOT NULL DEFAULT '',
  `server_log` blob NOT NULL,
  `empr_carac` blob NOT NULL,
  `empr_doc` blob NOT NULL,
  `empr_expl` mediumblob NOT NULL,
  `nb_result` blob NOT NULL,
  `gen_stat` blob NOT NULL,
  PRIMARY KEY (`id_log`),
  KEY `lopac_date_log` (`date_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `log_expl_retard`
--

CREATE TABLE IF NOT EXISTS `log_expl_retard` (
  `id_log` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `titre` varchar(255) NOT NULL DEFAULT '',
  `expl_id` int(11) NOT NULL DEFAULT 0,
  `expl_cb` varchar(255) NOT NULL DEFAULT '',
  `date_pret` date NOT NULL DEFAULT '0000-00-00',
  `date_retour` date NOT NULL DEFAULT '0000-00-00',
  `amende` decimal(16,2) NOT NULL DEFAULT 0.00,
  `num_log_retard` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `log_retard`
--

CREATE TABLE IF NOT EXISTS `log_retard` (
  `id_log` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `niveau_reel` int(1) NOT NULL DEFAULT 0,
  `niveau_suppose` int(1) NOT NULL DEFAULT 0,
  `amende_totale` decimal(16,2) NOT NULL DEFAULT 0.00,
  `frais` decimal(16,2) NOT NULL DEFAULT 0.00,
  `idempr` int(11) NOT NULL DEFAULT 0,
  `log_printed` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_mail` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mails_waiting`
--

CREATE TABLE IF NOT EXISTS `mails_waiting` (
  `id_mail` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mail_waiting_to_name` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_to_mail` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_object` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_content` mediumtext NOT NULL,
  `mail_waiting_from_name` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_from_mail` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_headers` text NOT NULL,
  `mail_waiting_copy_cc` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_copy_bcc` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_do_nl2br` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `mail_waiting_attachments` text NOT NULL,
  `mail_waiting_reply_name` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_reply_mail` varchar(255) NOT NULL DEFAULT '',
  `mail_waiting_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mailtpl`
--

CREATE TABLE IF NOT EXISTS `mailtpl` (
  `id_mailtpl` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailtpl_name` varchar(255) NOT NULL DEFAULT '',
  `mailtpl_objet` varchar(255) NOT NULL DEFAULT '',
  `mailtpl_tpl` mediumtext NOT NULL,
  `mailtpl_users` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_mailtpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `map_echelles`
--

CREATE TABLE IF NOT EXISTS `map_echelles` (
  `map_echelle_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_echelle_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`map_echelle_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `map_emprises`
--

CREATE TABLE IF NOT EXISTS `map_emprises` (
  `map_emprise_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_emprise_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_emprise_obj_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_emprise_data` geometry NOT NULL,
  `map_emprise_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`map_emprise_id`),
  KEY `i_map_emprise_obj_num` (`map_emprise_obj_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `map_hold_areas`
--

CREATE TABLE IF NOT EXISTS `map_hold_areas` (
  `id_obj` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type_obj` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `area` double DEFAULT NULL,
  `bbox_area` double DEFAULT NULL,
  `center` longtext DEFAULT NULL,
  PRIMARY KEY (`id_obj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `map_projections`
--

CREATE TABLE IF NOT EXISTS `map_projections` (
  `map_projection_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_projection_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`map_projection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `map_refs`
--

CREATE TABLE IF NOT EXISTS `map_refs` (
  `map_ref_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_ref_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`map_ref_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_sender_id` int(10) UNSIGNED NOT NULL,
  `user_receiver_id` int(10) UNSIGNED NOT NULL,
  `msg_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `send_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_message_user_sender` (`user_sender_id`),
  KEY `idx_message_user_receiver` (`user_receiver_id`),
  KEY `idx_message_user_sender_user_receiver` (`user_sender_id`,`user_receiver_id`),
  KEY `idx_message_msg_status` (`msg_status`),
  KEY `idx_message_group` (`group_id`),
  KEY `idx_message_parent` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_attachment`
--

CREATE TABLE IF NOT EXISTS `message_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `size` int(11) NOT NULL DEFAULT 0,
  `message_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mots`
--

CREATE TABLE IF NOT EXISTS `mots` (
  `id_mot` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mot` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_mot`),
  UNIQUE KEY `mot` (`mot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `noeuds`
--

CREATE TABLE IF NOT EXISTS `noeuds` (
  `id_noeud` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `autorite` varchar(255) NOT NULL DEFAULT '',
  `num_parent` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_renvoi_voir` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `visible` char(1) NOT NULL DEFAULT '1',
  `num_thesaurus` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `path` text NOT NULL,
  `authority_import_denied` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `not_use_in_indexation` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_noeud`),
  KEY `num_parent` (`num_parent`),
  KEY `num_thesaurus` (`num_thesaurus`),
  KEY `autorite` (`autorite`),
  KEY `key_path` (`path`(333)),
  KEY `i_num_renvoi_voir` (`num_renvoi_voir`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `noeuds`
--

INSERT INTO `noeuds` VALUES
(1, 'TOP', 0, 0, '0', 1, '', 0, 0),
(2, 'ORPHELINS', 1, 0, '0', 1, '', 0, 0),
(3, 'NONCLASSES', 1, 0, '0', 1, '', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_children_records`
--

CREATE TABLE IF NOT EXISTS `nomenclature_children_records` (
  `child_record_num_record` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_formation` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_musicstand` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_instrument` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_effective` varchar(10) NOT NULL DEFAULT '0',
  `child_record_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_other` varchar(255) NOT NULL DEFAULT '',
  `child_record_num_voice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_workshop` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `child_record_num_nomenclature` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`child_record_num_record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_exotic_instruments`
--

CREATE TABLE IF NOT EXISTS `nomenclature_exotic_instruments` (
  `id_exotic_instrument` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exotic_instrument_num_nomenclature` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `exotic_instrument_num_instrument` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `exotic_instrument_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `exotic_instrument_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_exotic_instrument`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_exotic_other_instruments`
--

CREATE TABLE IF NOT EXISTS `nomenclature_exotic_other_instruments` (
  `id_exotic_other_instrument` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exotic_other_instrument_num_exotic_instrument` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `exotic_other_instrument_num_instrument` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `exotic_other_instrument_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_exotic_other_instrument`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_families`
--

CREATE TABLE IF NOT EXISTS `nomenclature_families` (
  `id_family` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `family_name` varchar(255) NOT NULL DEFAULT '',
  `family_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_family`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_formations`
--

CREATE TABLE IF NOT EXISTS `nomenclature_formations` (
  `id_formation` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `formation_name` varchar(255) NOT NULL DEFAULT '',
  `formation_nature` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `formation_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_formation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_instruments`
--

CREATE TABLE IF NOT EXISTS `nomenclature_instruments` (
  `id_instrument` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `instrument_code` varchar(255) NOT NULL DEFAULT '',
  `instrument_name` varchar(255) NOT NULL DEFAULT '',
  `instrument_musicstand_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `instrument_standard` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_instrument`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_musicstands`
--

CREATE TABLE IF NOT EXISTS `nomenclature_musicstands` (
  `id_musicstand` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `musicstand_name` varchar(255) NOT NULL DEFAULT '',
  `musicstand_famille_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `musicstand_division` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `musicstand_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `musicstand_workshop` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_musicstand`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_notices_nomenclatures`
--

CREATE TABLE IF NOT EXISTS `nomenclature_notices_nomenclatures` (
  `id_notice_nomenclature` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notice_nomenclature_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_nomenclature_num_formation` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_nomenclature_num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_nomenclature_label` varchar(255) NOT NULL DEFAULT '',
  `notice_nomenclature_abbreviation` text NOT NULL,
  `notice_nomenclature_notes` text NOT NULL,
  `notice_nomenclature_families_notes` mediumtext NOT NULL,
  `notice_nomenclature_exotic_instruments_note` text NOT NULL,
  `notice_nomenclature_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_notice_nomenclature`),
  KEY `i_notice_nomenclature_num_notice` (`notice_nomenclature_num_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_types`
--

CREATE TABLE IF NOT EXISTS `nomenclature_types` (
  `id_type` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL DEFAULT '',
  `type_formation_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_voices`
--

CREATE TABLE IF NOT EXISTS `nomenclature_voices` (
  `id_voice` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `voice_code` varchar(255) NOT NULL DEFAULT '',
  `voice_name` varchar(255) NOT NULL DEFAULT '',
  `voice_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_voice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_workshops`
--

CREATE TABLE IF NOT EXISTS `nomenclature_workshops` (
  `id_workshop` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workshop_label` varchar(255) NOT NULL DEFAULT '',
  `workshop_num_nomenclature` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `workshop_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `workshop_defined` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_workshop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nomenclature_workshops_instruments`
--

CREATE TABLE IF NOT EXISTS `nomenclature_workshops_instruments` (
  `id_workshop_instrument` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `workshop_instrument_num_workshop` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `workshop_instrument_num_instrument` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `workshop_instrument_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `workshop_instrument_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_workshop_instrument`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices`
--

CREATE TABLE IF NOT EXISTS `notices` (
  `notice_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typdoc` char(2) NOT NULL DEFAULT 'a',
  `tit1` text DEFAULT NULL,
  `tit2` text DEFAULT NULL,
  `tit3` text DEFAULT NULL,
  `tit4` text DEFAULT NULL,
  `tparent_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `tnvol` varchar(100) NOT NULL DEFAULT '',
  `ed1_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `ed2_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `coll_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `subcoll_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `year` varchar(50) DEFAULT NULL,
  `nocoll` varchar(255) DEFAULT NULL,
  `mention_edition` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(50) NOT NULL DEFAULT '',
  `npages` varchar(255) DEFAULT NULL,
  `ill` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `accomp` varchar(255) DEFAULT NULL,
  `n_gen` text NOT NULL,
  `n_contenu` text NOT NULL,
  `n_resume` text NOT NULL,
  `lien` text NOT NULL,
  `eformat` varchar(255) NOT NULL DEFAULT '',
  `index_l` text NOT NULL,
  `indexint` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `index_serie` tinytext DEFAULT NULL,
  `index_matieres` text NOT NULL,
  `niveau_biblio` char(1) NOT NULL DEFAULT 'm',
  `niveau_hierar` char(1) NOT NULL DEFAULT '0',
  `origine_catalogage` int(8) UNSIGNED NOT NULL DEFAULT 1,
  `prix` varchar(255) NOT NULL DEFAULT '',
  `index_n_gen` text DEFAULT NULL,
  `index_n_contenu` text DEFAULT NULL,
  `index_n_resume` text DEFAULT NULL,
  `index_sew` text DEFAULT NULL,
  `index_wew` text DEFAULT NULL,
  `statut` int(5) NOT NULL DEFAULT 1,
  `commentaire_gestion` text NOT NULL,
  `create_date` datetime NOT NULL DEFAULT '2005-01-01 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `signature` varchar(255) NOT NULL DEFAULT '',
  `thumbnail_url` mediumblob NOT NULL,
  `date_parution` date NOT NULL DEFAULT '0000-00-00',
  `opac_visible_bulletinage` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `indexation_lang` varchar(20) NOT NULL DEFAULT '',
  `map_echelle_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_projection_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_ref_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `map_equinoxe` varchar(255) NOT NULL DEFAULT '',
  `notice_is_new` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_date_is_new` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `opac_serialcirc_demande` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `num_notice_usage` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `is_numeric` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`notice_id`),
  KEY `typdoc` (`typdoc`),
  KEY `tparent_id` (`tparent_id`),
  KEY `ed1_id` (`ed1_id`),
  KEY `ed2_id` (`ed2_id`),
  KEY `coll_id` (`coll_id`),
  KEY `subcoll_id` (`subcoll_id`),
  KEY `cb` (`code`),
  KEY `indexint` (`indexint`),
  KEY `sig_index` (`signature`),
  KEY `i_notice_n_biblio` (`niveau_biblio`),
  KEY `i_notice_n_hierar` (`niveau_hierar`),
  KEY `notice_eformat` (`eformat`),
  KEY `i_date_parution` (`date_parution`),
  KEY `i_not_statut` (`statut`),
  KEY `i_map_echelle_num` (`map_echelle_num`),
  KEY `i_map_projection_num` (`map_projection_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_authorities_sources`
--

CREATE TABLE IF NOT EXISTS `notices_authorities_sources` (
  `num_authority_source` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_authority_source`,`num_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_authperso`
--

CREATE TABLE IF NOT EXISTS `notices_authperso` (
  `notice_authperso_notice_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_authperso_authority_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notice_authperso_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`notice_authperso_notice_num`,`notice_authperso_authority_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_categories`
--

CREATE TABLE IF NOT EXISTS `notices_categories` (
  `notcateg_notice` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_noeud` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `num_vedette` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `ordre_vedette` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `ordre_categorie` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`notcateg_notice`,`num_noeud`,`num_vedette`),
  KEY `num_noeud` (`num_noeud`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_custom`
--

CREATE TABLE IF NOT EXISTS `notices_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `opac_sort` int(11) NOT NULL DEFAULT 1,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_custom_dates`
--

CREATE TABLE IF NOT EXISTS `notices_custom_dates` (
  `notices_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notices_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notices_custom_date_type` int(11) DEFAULT NULL,
  `notices_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `notices_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `notices_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`notices_custom_champ`,`notices_custom_origine`,`notices_custom_order`),
  KEY `notices_custom_champ` (`notices_custom_champ`),
  KEY `notices_custom_origine` (`notices_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_custom_lists`
--

CREATE TABLE IF NOT EXISTS `notices_custom_lists` (
  `notices_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notices_custom_list_value` varchar(255) DEFAULT NULL,
  `notices_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `notices_custom_champ` (`notices_custom_champ`),
  KEY `i_ncl_lv` (`notices_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_custom_values`
--

CREATE TABLE IF NOT EXISTS `notices_custom_values` (
  `notices_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notices_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notices_custom_small_text` varchar(255) DEFAULT NULL,
  `notices_custom_text` text DEFAULT NULL,
  `notices_custom_integer` int(11) DEFAULT NULL,
  `notices_custom_date` date DEFAULT NULL,
  `notices_custom_float` float DEFAULT NULL,
  `notices_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `notices_custom_champ` (`notices_custom_champ`),
  KEY `notices_custom_origine` (`notices_custom_origine`),
  KEY `i_ncv_st` (`notices_custom_small_text`),
  KEY `i_ncv_t` (`notices_custom_text`(255)),
  KEY `i_ncv_i` (`notices_custom_integer`),
  KEY `i_ncv_d` (`notices_custom_date`),
  KEY `i_ncv_f` (`notices_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_externes`
--

CREATE TABLE IF NOT EXISTS `notices_externes` (
  `num_notice` int(11) NOT NULL DEFAULT 0,
  `recid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`num_notice`),
  KEY `i_recid` (`recid`),
  KEY `i_notice_recid` (`num_notice`,`recid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_fields_global_index`
--

CREATE TABLE IF NOT EXISTS `notices_fields_global_index` (
  `id_notice` mediumint(8) NOT NULL DEFAULT 0,
  `code_champ` int(3) NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) NOT NULL DEFAULT 0,
  `ordre` int(4) NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  `pond` int(4) NOT NULL DEFAULT 100,
  `lang` varchar(10) NOT NULL DEFAULT '',
  `authority_num` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_notice`,`code_champ`,`code_ss_champ`,`lang`,`ordre`),
  KEY `i_value` (`value`(300)),
  KEY `i_code_champ_code_ss_champ` (`code_champ`,`code_ss_champ`),
  KEY `i_id_notice` (`id_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_global_index`
--

CREATE TABLE IF NOT EXISTS `notices_global_index` (
  `num_notice` mediumint(8) NOT NULL DEFAULT 0,
  `no_index` mediumint(8) NOT NULL DEFAULT 0,
  `infos_global` text NOT NULL,
  `index_infos_global` text NOT NULL,
  PRIMARY KEY (`num_notice`,`no_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_langues`
--

CREATE TABLE IF NOT EXISTS `notices_langues` (
  `num_notice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `type_langue` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `code_langue` char(3) NOT NULL DEFAULT '',
  `ordre_langue` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_notice`,`type_langue`,`code_langue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_mots_global_index`
--

CREATE TABLE IF NOT EXISTS `notices_mots_global_index` (
  `id_notice` mediumint(8) NOT NULL DEFAULT 0,
  `code_champ` int(3) NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) NOT NULL DEFAULT 0,
  `num_word` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(4) NOT NULL DEFAULT 100,
  `position` int(11) NOT NULL DEFAULT 1,
  `field_position` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_notice`,`code_champ`,`code_ss_champ`,`num_word`,`position`,`field_position`),
  KEY `code_champ` (`code_champ`),
  KEY `i_id_mot` (`num_word`,`id_notice`),
  KEY `i_code_champ_code_ss_champ_num_word` (`code_champ`,`code_ss_champ`,`num_word`),
  KEY `i_num_word` (`num_word`),
  KEY `i_id_notice` (`id_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_relations`
--

CREATE TABLE IF NOT EXISTS `notices_relations` (
  `num_notice` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `linked_notice` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `relation_type` char(1) NOT NULL DEFAULT '',
  `rank` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_notice`,`linked_notice`),
  KEY `linked_notice` (`linked_notice`),
  KEY `relation_type` (`relation_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notices_titres_uniformes`
--

CREATE TABLE IF NOT EXISTS `notices_titres_uniformes` (
  `ntu_num_notice` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `ntu_num_tu` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `ntu_titre` varchar(255) NOT NULL DEFAULT '',
  `ntu_date` varchar(255) NOT NULL DEFAULT '',
  `ntu_sous_vedette` varchar(255) NOT NULL DEFAULT '',
  `ntu_langue` varchar(255) NOT NULL DEFAULT '',
  `ntu_version` varchar(255) NOT NULL DEFAULT '',
  `ntu_mention` varchar(255) NOT NULL DEFAULT '',
  `ntu_ordre` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ntu_num_notice`,`ntu_num_tu`),
  KEY `i_ntu_ntu_num_tu` (`ntu_num_tu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notice_onglet`
--

CREATE TABLE IF NOT EXISTS `notice_onglet` (
  `id_onglet` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `onglet_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_onglet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notice_statut`
--

CREATE TABLE IF NOT EXISTS `notice_statut` (
  `id_notice_statut` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gestion_libelle` varchar(255) DEFAULT NULL,
  `opac_libelle` varchar(255) DEFAULT NULL,
  `notice_visible_opac` tinyint(1) NOT NULL DEFAULT 1,
  `notice_visible_gestion` tinyint(1) NOT NULL DEFAULT 1,
  `expl_visible_opac` tinyint(1) NOT NULL DEFAULT 1,
  `class_html` varchar(255) NOT NULL DEFAULT '',
  `notice_visible_opac_abon` tinyint(1) NOT NULL DEFAULT 0,
  `expl_visible_opac_abon` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `explnum_visible_opac` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `explnum_visible_opac_abon` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `notice_scan_request_opac` tinyint(1) NOT NULL DEFAULT 0,
  `notice_scan_request_opac_abon` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_notice_statut`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `notice_statut`
--

INSERT INTO `notice_statut` VALUES
(1, 'Sans statut particulier', '', 1, 1, 1, 'statutnot1', 0, 0, 1, 0, 0, 0),
(2, 'Prêt express', '', 0, 1, 1, 'statutnot2', 1, 0, 1, 0, 0, 0),
(3, 'En commande', 'commandé', 1, 1, 1, 'statutnot4', 0, 0, 1, 0, 0, 0),
(4, 'En cours d\'import / saisie', '', 0, 1, 0, 'statutnot10', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `notice_tpl`
--

CREATE TABLE IF NOT EXISTS `notice_tpl` (
  `notpl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notpl_name` varchar(256) NOT NULL DEFAULT '',
  `notpl_code` text NOT NULL,
  `notpl_comment` varchar(256) NOT NULL DEFAULT '',
  `notpl_id_test` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notpl_show_opac` int(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`notpl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notice_tplcode`
--

CREATE TABLE IF NOT EXISTS `notice_tplcode` (
  `num_notpl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notplcode_localisation` mediumint(8) NOT NULL DEFAULT 0,
  `notplcode_typdoc` char(2) NOT NULL DEFAULT 'a',
  `notplcode_niveau_biblio` char(1) NOT NULL DEFAULT 'm',
  `notplcode_niveau_hierar` char(1) NOT NULL DEFAULT '0',
  `nottplcode_code` text NOT NULL,
  PRIMARY KEY (`num_notpl`,`notplcode_localisation`,`notplcode_typdoc`,`notplcode_niveau_biblio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notice_usage`
--

CREATE TABLE IF NOT EXISTS `notice_usage` (
  `id_usage` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usage_libelle` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_usage`),
  KEY `usage_libelle` (`usage_libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offres_remises`
--

CREATE TABLE IF NOT EXISTS `offres_remises` (
  `num_fournisseur` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_produit` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `remise` float(4,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `condition_remise` text DEFAULT NULL,
  PRIMARY KEY (`num_fournisseur`,`num_produit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ontologies`
--

CREATE TABLE IF NOT EXISTS `ontologies` (
  `id_ontology` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ontology_name` varchar(255) NOT NULL DEFAULT '',
  `ontology_description` text NOT NULL,
  `ontology_creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ontology_storage_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_ontology`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ontology_g2t`
--

CREATE TABLE IF NOT EXISTS `ontology_g2t` (
  `g` mediumint(8) UNSIGNED NOT NULL,
  `t` mediumint(8) UNSIGNED NOT NULL,
  UNIQUE KEY `gt` (`g`,`t`),
  KEY `tg` (`t`,`g`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

--
-- Déchargement des données de la table `ontology_g2t`
--

INSERT INTO `ontology_g2t` VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(1, 80),
(1, 81),
(1, 82),
(1, 83),
(1, 84),
(1, 85),
(1, 86),
(1, 87),
(1, 88),
(1, 89),
(1, 90),
(1, 91),
(1, 92),
(1, 93),
(1, 94),
(1, 95),
(1, 96),
(1, 97),
(1, 98),
(1, 99),
(1, 100),
(1, 101),
(1, 102),
(1, 103),
(1, 104),
(1, 105),
(1, 106),
(1, 107),
(1, 108),
(1, 109),
(1, 110),
(1, 111),
(1, 112),
(1, 113),
(1, 114),
(1, 115),
(1, 116),
(1, 117),
(1, 118),
(1, 119),
(1, 120),
(1, 121),
(1, 122),
(1, 123),
(1, 124),
(1, 125),
(1, 126),
(1, 127),
(1, 128),
(1, 129),
(1, 130),
(1, 131),
(1, 132),
(1, 133),
(1, 134),
(1, 135),
(1, 136),
(1, 137),
(1, 138),
(1, 139),
(1, 140),
(1, 141),
(1, 142),
(1, 143),
(1, 144),
(1, 145),
(1, 146),
(1, 147),
(1, 148),
(1, 149),
(1, 150),
(1, 151),
(1, 152),
(1, 153),
(1, 154),
(1, 155),
(1, 156),
(1, 157),
(1, 158),
(1, 159),
(1, 160),
(1, 161),
(1, 162),
(1, 163),
(1, 164),
(1, 165),
(1, 166),
(1, 167),
(1, 168),
(1, 169),
(1, 170),
(1, 171),
(1, 172),
(1, 173),
(1, 174),
(1, 175),
(1, 176),
(1, 177),
(1, 178),
(1, 179),
(1, 180),
(1, 181),
(1, 182),
(1, 183),
(1, 184),
(1, 185),
(1, 186),
(1, 187),
(1, 188),
(1, 189),
(1, 190),
(1, 191),
(1, 192),
(1, 193),
(1, 194),
(1, 195),
(1, 196),
(1, 197),
(1, 198),
(1, 199),
(1, 200),
(1, 201),
(1, 202),
(1, 203),
(1, 204),
(1, 205),
(1, 206),
(1, 207),
(1, 208),
(1, 209),
(1, 210),
(1, 211),
(1, 212),
(1, 213),
(1, 214),
(1, 215),
(1, 216),
(1, 217),
(1, 218),
(1, 219),
(1, 220),
(1, 221),
(1, 222),
(1, 223),
(1, 224),
(1, 225),
(1, 226),
(1, 227),
(1, 228),
(1, 229),
(1, 230),
(1, 231),
(1, 232),
(1, 233),
(1, 234),
(1, 235),
(1, 236),
(1, 237),
(1, 238),
(1, 239),
(1, 240),
(1, 241),
(1, 242),
(1, 243),
(1, 244),
(1, 245),
(1, 246),
(1, 247),
(1, 248),
(1, 249),
(1, 250),
(1, 251),
(1, 252),
(1, 253),
(1, 254),
(1, 255),
(1, 256),
(1, 257),
(1, 258),
(1, 259),
(1, 260),
(1, 261),
(1, 262),
(1, 263),
(1, 264),
(1, 265),
(1, 266),
(1, 267),
(1, 268),
(1, 269),
(1, 270),
(1, 271),
(1, 272),
(1, 273),
(1, 274),
(1, 275),
(1, 276),
(1, 277),
(1, 278),
(1, 279),
(1, 280),
(1, 281),
(1, 282),
(1, 283),
(1, 284),
(1, 285),
(1, 286),
(1, 287),
(1, 288),
(1, 289),
(1, 290),
(1, 291),
(1, 292),
(1, 293),
(1, 294),
(1, 295),
(1, 296),
(1, 297),
(1, 298),
(1, 299),
(1, 300),
(1, 301),
(1, 302),
(1, 303),
(1, 304),
(1, 305),
(1, 306),
(1, 307),
(1, 308),
(1, 309),
(1, 310),
(1, 311),
(1, 312),
(1, 313),
(1, 314),
(1, 315),
(1, 316),
(1, 317),
(1, 318),
(1, 319),
(1, 320),
(1, 321),
(1, 322),
(1, 323),
(1, 324),
(1, 325),
(1, 326),
(1, 327),
(1, 328),
(1, 329),
(1, 330),
(1, 331),
(1, 332),
(1, 333),
(1, 334),
(1, 335),
(1, 336),
(1, 337),
(1, 338),
(1, 339),
(1, 340),
(1, 341),
(1, 342),
(1, 343),
(1, 344),
(1, 345),
(1, 346),
(1, 347),
(1, 348),
(1, 349),
(1, 350),
(1, 351),
(1, 352),
(1, 353),
(1, 354),
(1, 355),
(1, 356),
(1, 357),
(1, 358),
(1, 359),
(1, 360),
(1, 361),
(1, 362),
(1, 363),
(1, 364),
(1, 365),
(1, 366),
(1, 367),
(1, 368),
(1, 369),
(1, 370),
(1, 371),
(1, 372),
(1, 373),
(1, 374),
(1, 375),
(1, 376),
(1, 377),
(1, 378),
(1, 379),
(1, 380),
(1, 381),
(1, 382),
(1, 383),
(1, 384),
(1, 385),
(1, 386),
(1, 387),
(1, 388),
(1, 389),
(1, 390),
(1, 391),
(1, 392),
(1, 393),
(1, 394),
(1, 395),
(1, 396),
(1, 397),
(1, 398),
(1, 399),
(1, 400),
(1, 401),
(1, 402),
(1, 403),
(1, 404),
(1, 405),
(1, 406),
(1, 407),
(1, 408),
(1, 409),
(1, 410),
(1, 411),
(1, 412),
(1, 413),
(1, 414),
(1, 415),
(1, 416),
(1, 417),
(1, 418),
(1, 419),
(1, 420),
(1, 421),
(1, 422),
(1, 423),
(1, 424),
(1, 425),
(1, 426),
(1, 427),
(1, 428),
(1, 429),
(1, 430),
(1, 431),
(1, 432),
(1, 433),
(1, 434),
(1, 435),
(1, 436),
(1, 437),
(1, 438),
(1, 439),
(1, 440),
(1, 441),
(1, 442),
(1, 443),
(1, 444),
(1, 445),
(1, 446),
(1, 447),
(1, 448),
(1, 449),
(1, 450),
(1, 451),
(1, 452),
(1, 453),
(1, 454),
(1, 455),
(1, 456),
(1, 457),
(1, 458),
(1, 459),
(1, 460),
(1, 461),
(1, 462),
(1, 463),
(1, 464),
(1, 465),
(1, 466),
(1, 467),
(1, 468),
(1, 469),
(1, 470),
(1, 471),
(1, 472),
(1, 473),
(1, 474),
(1, 475),
(1, 476),
(1, 477),
(1, 478),
(1, 479),
(1, 480),
(1, 481),
(1, 482),
(1, 483),
(1, 484),
(1, 485),
(1, 486),
(1, 487),
(1, 488),
(1, 489),
(1, 490),
(1, 491),
(1, 492),
(1, 493),
(1, 494),
(1, 495),
(1, 496),
(1, 497),
(1, 498),
(1, 499),
(1, 500),
(1, 501),
(1, 502),
(1, 503),
(1, 504),
(1, 505),
(1, 506),
(1, 507),
(1, 508),
(1, 509),
(1, 510),
(1, 511),
(1, 512),
(1, 513),
(1, 514),
(1, 515),
(1, 516),
(1, 517),
(1, 518),
(1, 519),
(1, 520),
(1, 521),
(1, 522),
(1, 523),
(1, 524),
(1, 525),
(1, 526),
(1, 527),
(1, 528),
(1, 529),
(1, 530),
(1, 531),
(1, 532),
(1, 533),
(1, 534),
(1, 535),
(1, 536),
(1, 537),
(1, 538),
(1, 539),
(1, 540),
(1, 541),
(1, 542),
(1, 543);

COMMIT;

