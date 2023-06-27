-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: bibli.sql,v 1.75 2016-03-30 09:04:52 mbertin Exp $

-- MySQL dump 10.14  Distrib 5.5.36-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: bibli
-- ------------------------------------------------------
-- Server version	5.5.36-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abo_liste_lecture`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abo_liste_lecture (
  num_empr int(8) unsigned NOT NULL DEFAULT '0',
  num_liste int(8) unsigned NOT NULL DEFAULT '0',
  etat int(1) unsigned NOT NULL DEFAULT '0',
  commentaire text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_empr,num_liste)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_abts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_abts (
  abt_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  abt_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  base_modele_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  base_modele_id int(11) NOT NULL DEFAULT '0',
  num_notice int(11) NOT NULL DEFAULT '0',
  date_debut date NOT NULL DEFAULT '0000-00-00',
  date_fin date NOT NULL DEFAULT '0000-00-00',
  fournisseur int(11) NOT NULL DEFAULT '0',
  destinataire varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cote varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  typdoc_id int(11) NOT NULL DEFAULT '0',
  exemp_auto int(11) NOT NULL DEFAULT '0',
  location_id int(11) NOT NULL DEFAULT '0',
  section_id int(11) NOT NULL DEFAULT '0',
  lender_id int(11) NOT NULL DEFAULT '0',
  statut_id int(11) NOT NULL DEFAULT '0',
  codestat_id int(11) NOT NULL DEFAULT '0',
  type_antivol int(11) NOT NULL DEFAULT '0',
  duree_abonnement int(11) NOT NULL DEFAULT '0',
  abt_numeric int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (abt_id),
  KEY index_num_notice (num_notice),
  KEY i_date_fin (date_fin)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_abts_modeles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_abts_modeles (
  modele_id int(11) NOT NULL DEFAULT '0',
  abt_id int(11) NOT NULL DEFAULT '0',
  num int(11) NOT NULL DEFAULT '0',
  vol int(11) NOT NULL DEFAULT '0',
  tome int(11) NOT NULL DEFAULT '0',
  delais int(11) NOT NULL DEFAULT '0',
  critique int(11) NOT NULL DEFAULT '0',
  num_statut_general smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (modele_id,abt_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_grille_abt`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_grille_abt (
  id_bull int(11) NOT NULL AUTO_INCREMENT,
  num_abt int(10) unsigned NOT NULL DEFAULT '0',
  date_parution date NOT NULL DEFAULT '0000-00-00',
  modele_id int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  nombre int(11) NOT NULL DEFAULT '0',
  numero int(11) NOT NULL DEFAULT '0',
  ordre int(11) NOT NULL DEFAULT '0',
  state int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_bull),
  KEY num_abt (num_abt)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_grille_modele`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_grille_modele (
  num_modele int(10) unsigned NOT NULL DEFAULT '0',
  date_parution date NOT NULL DEFAULT '0000-00-00',
  type_serie int(11) NOT NULL DEFAULT '0',
  numero varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  nombre_recu int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (num_modele,date_parution,type_serie),
  KEY num_modele (num_modele)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_modeles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_modeles (
  modele_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  modele_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_notice int(10) unsigned NOT NULL DEFAULT '0',
  num_periodicite int(10) unsigned NOT NULL DEFAULT '0',
  duree_abonnement int(11) NOT NULL DEFAULT '0',
  date_debut date DEFAULT NULL,
  date_fin date DEFAULT NULL,
  days varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1111111',
  day_month varchar(31) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1111111111111111111111111111111',
  week_month varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111',
  week_year varchar(54) COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111111111111111111111111111111111111111111111111111',
  month_year varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111111111',
  num_cycle int(11) NOT NULL DEFAULT '0',
  num_combien int(11) NOT NULL DEFAULT '0',
  num_increment int(11) NOT NULL DEFAULT '0',
  num_date_unite int(11) NOT NULL DEFAULT '0',
  num_increment_date int(11) NOT NULL DEFAULT '0',
  num_depart int(11) NOT NULL DEFAULT '0',
  vol_actif int(11) NOT NULL DEFAULT '0',
  vol_increment int(11) NOT NULL DEFAULT '0',
  vol_date_unite int(11) NOT NULL DEFAULT '0',
  vol_increment_numero int(11) NOT NULL DEFAULT '0',
  vol_increment_date int(11) NOT NULL DEFAULT '0',
  vol_cycle int(11) NOT NULL DEFAULT '0',
  vol_combien int(11) NOT NULL DEFAULT '0',
  vol_depart int(11) NOT NULL DEFAULT '0',
  tom_actif int(11) NOT NULL DEFAULT '0',
  tom_increment int(11) NOT NULL DEFAULT '0',
  tom_date_unite int(11) NOT NULL DEFAULT '0',
  tom_increment_numero int(11) NOT NULL DEFAULT '0',
  tom_increment_date int(11) NOT NULL DEFAULT '0',
  tom_cycle int(11) NOT NULL DEFAULT '0',
  tom_combien int(11) NOT NULL DEFAULT '0',
  tom_depart int(11) NOT NULL DEFAULT '0',
  format_aff varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  format_periode varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (modele_id),
  KEY num_notice (num_notice),
  KEY num_periodicite (num_periodicite)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abts_periodicites`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE abts_periodicites (
  periodicite_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  duree int(11) NOT NULL DEFAULT '0',
  unite int(11) NOT NULL DEFAULT '0',
  retard_periodicite int(4) DEFAULT '0',
  seuil_periodicite int(4) DEFAULT '0',
  consultation_duration int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (periodicite_id)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acces_profiles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE acces_profiles (
  prf_id int(2) unsigned NOT NULL AUTO_INCREMENT,
  prf_type int(1) unsigned NOT NULL DEFAULT '1',
  prf_name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  prf_rule blob NOT NULL,
  prf_hrule text COLLATE utf8_unicode_ci NOT NULL,
  prf_used int(2) unsigned NOT NULL DEFAULT '0',
  dom_num int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (prf_id),
  KEY prf_type (prf_type),
  KEY prf_name (prf_name),
  KEY dom_num (dom_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acces_rights`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE acces_rights (
  dom_num int(2) unsigned NOT NULL DEFAULT '0',
  usr_prf_num int(2) unsigned NOT NULL DEFAULT '0',
  res_prf_num int(2) unsigned NOT NULL DEFAULT '0',
  dom_rights int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (dom_num,usr_prf_num,res_prf_num),
  KEY dom_num (dom_num),
  KEY usr_prf_num (usr_prf_num),
  KEY res_prf_num (res_prf_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `actes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE actes (
  id_acte int(8) unsigned NOT NULL AUTO_INCREMENT,
  date_acte date NOT NULL DEFAULT '0000-00-00',
  numero varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  type_acte int(3) unsigned NOT NULL DEFAULT '0',
  statut int(3) unsigned NOT NULL DEFAULT '0',
  date_paiement date NOT NULL DEFAULT '0000-00-00',
  num_paiement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_entite int(5) unsigned NOT NULL DEFAULT '0',
  num_fournisseur int(5) unsigned NOT NULL DEFAULT '0',
  num_contact_livr int(8) unsigned NOT NULL DEFAULT '0',
  num_contact_fact int(8) unsigned NOT NULL DEFAULT '0',
  num_exercice int(8) unsigned NOT NULL DEFAULT '0',
  commentaires text COLLATE utf8_unicode_ci NOT NULL,
  reference varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_acte text COLLATE utf8_unicode_ci NOT NULL,
  devise varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaires_i text COLLATE utf8_unicode_ci NOT NULL,
  date_valid date NOT NULL DEFAULT '0000-00-00',
  date_ech date NOT NULL DEFAULT '0000-00-00',
  nom_acte varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_acte),
  KEY num_fournisseur (num_fournisseur),
  KEY `date` (date_acte),
  KEY num_entite (num_entite),
  KEY numero (numero)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_session`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE admin_session (
  userid int(10) unsigned NOT NULL DEFAULT '0',
  `session` mediumblob,
  PRIMARY KEY (userid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analysis`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE analysis (
  analysis_bulletin int(8) unsigned NOT NULL DEFAULT '0',
  analysis_notice int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (analysis_bulletin,analysis_notice),
  KEY analysis_notice (analysis_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arch_emplacement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE arch_emplacement (
  archempla_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  archempla_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (archempla_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arch_statut`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE arch_statut (
  archstatut_id int(8) NOT NULL AUTO_INCREMENT,
  archstatut_gestion_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  archstatut_opac_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  archstatut_visible_opac tinyint(1) unsigned NOT NULL DEFAULT '1',
  archstatut_visible_opac_abon tinyint(1) unsigned NOT NULL DEFAULT '1',
  archstatut_visible_gestion tinyint(1) unsigned NOT NULL DEFAULT '1',
  archstatut_class_html varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (archstatut_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arch_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE arch_type (
  archtype_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  archtype_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (archtype_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE audit (
  type_obj int(1) NOT NULL DEFAULT '0',
  object_id int(10) unsigned NOT NULL DEFAULT '0',
  user_id int(8) unsigned NOT NULL DEFAULT '0',
  user_name varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  type_modif int(1) NOT NULL DEFAULT '1',
  quand timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  type_user int(1) unsigned NOT NULL DEFAULT '0',
  info text COLLATE utf8_unicode_ci NOT NULL,
  KEY type_obj (type_obj),
  KEY object_id (object_id),
  KEY user_id (user_id),
  KEY type_modif (type_modif)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut_link`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE aut_link (
  aut_link_from int(2) NOT NULL DEFAULT '0',
  aut_link_from_num int(11) NOT NULL DEFAULT '0',
  aut_link_to int(2) NOT NULL DEFAULT '0',
  aut_link_to_num int(11) NOT NULL DEFAULT '0',
  aut_link_type varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  aut_link_reciproc int(1) NOT NULL DEFAULT '0',
  aut_link_comment varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (aut_link_from,aut_link_from_num,aut_link_to,aut_link_to_num,aut_link_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `author_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE author_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `author_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE author_custom_lists (
  author_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  author_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  author_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (author_custom_champ),
  KEY editorial_champ_list_value (author_custom_champ,author_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `author_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE author_custom_values (
  author_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  author_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  author_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  author_custom_text text COLLATE utf8_unicode_ci,
  author_custom_integer int(11) DEFAULT NULL,
  author_custom_date date DEFAULT NULL,
  author_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (author_custom_champ),
  KEY editorial_custom_origine (author_custom_origine),
  KEY i_acv_st (author_custom_small_text),
  KEY i_acv_t (author_custom_text(255)),
  KEY i_acv_i (author_custom_integer),
  KEY i_acv_d (author_custom_date),
  KEY i_acv_f (author_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authorities_sources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authorities_sources (
  id_authority_source int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_authority int(10) unsigned NOT NULL DEFAULT '0',
  authority_number varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  authority_type varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_origin_authority int(10) unsigned NOT NULL DEFAULT '0',
  authority_favorite int(10) unsigned NOT NULL DEFAULT '0',
  import_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id_authority_source)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  author_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  author_type enum('70','71','72') COLLATE utf8_unicode_ci NOT NULL DEFAULT '70',
  author_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_rejete varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_date varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_see mediumint(8) unsigned NOT NULL DEFAULT '0',
  author_web varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_author text COLLATE utf8_unicode_ci,
  author_comment text COLLATE utf8_unicode_ci,
  author_lieu varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_ville varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_pays varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_subdivision varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_numero varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  author_import_denied int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (author_id),
  KEY author_see (author_see),
  KEY author_name (author_name),
  KEY author_rejete (author_rejete)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authperso`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authperso (
  id_authperso int(10) unsigned NOT NULL AUTO_INCREMENT,
  authperso_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  authperso_notice_onglet_num int(10) unsigned NOT NULL DEFAULT '0',
  authperso_isbd_script text COLLATE utf8_unicode_ci NOT NULL,
  authperso_view_script text COLLATE utf8_unicode_ci NOT NULL,
  authperso_opac_search int(10) unsigned NOT NULL DEFAULT '0',
  authperso_opac_multi_search int(10) unsigned NOT NULL DEFAULT '0',
  authperso_gestion_search int(10) unsigned NOT NULL DEFAULT '0',
  authperso_gestion_multi_search int(10) unsigned NOT NULL DEFAULT '0',
  authperso_comment text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_authperso)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authperso_authorities`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authperso_authorities (
  id_authperso_authority int(10) unsigned NOT NULL AUTO_INCREMENT,
  authperso_authority_authperso_num int(10) unsigned NOT NULL DEFAULT '0',
  authperso_infos_global text COLLATE utf8_unicode_ci NOT NULL,
  authperso_index_infos_global text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_authperso_authority)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authperso_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authperso_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  custom_prefixe varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authperso_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authperso_custom_lists (
  authperso_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  authperso_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  authperso_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (authperso_custom_champ),
  KEY editorial_champ_list_value (authperso_custom_champ,authperso_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authperso_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE authperso_custom_values (
  authperso_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  authperso_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  authperso_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  authperso_custom_text text COLLATE utf8_unicode_ci,
  authperso_custom_integer int(11) DEFAULT NULL,
  authperso_custom_date date DEFAULT NULL,
  authperso_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (authperso_custom_champ),
  KEY editorial_custom_origine (authperso_custom_origine),
  KEY i_acv_st (authperso_custom_small_text),
  KEY i_acv_t (authperso_custom_text(255)),
  KEY i_acv_i (authperso_custom_integer),
  KEY i_acv_d (authperso_custom_date),
  KEY i_acv_f (authperso_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avis`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE avis (
  id_avis mediumint(8) NOT NULL AUTO_INCREMENT,
  num_empr mediumint(8) NOT NULL DEFAULT '0',
  num_notice mediumint(8) NOT NULL DEFAULT '0',
  note int(3) DEFAULT NULL,
  sujet text COLLATE utf8_unicode_ci,
  commentaire text COLLATE utf8_unicode_ci,
  dateajout timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  valide int(1) unsigned NOT NULL DEFAULT '0',
  avis_rank int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_avis),
  KEY avis_num_notice (num_notice),
  KEY avis_num_empr (num_empr),
  KEY avis_note (note)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_abon`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_abon (
  num_bannette int(9) unsigned NOT NULL DEFAULT '0',
  num_empr int(9) unsigned NOT NULL DEFAULT '0',
  actif int(1) unsigned NOT NULL DEFAULT '0',
  bannette_mail varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (num_bannette,num_empr),
  KEY i_num_empr (num_empr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_contenu`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_contenu (
  num_bannette int(9) unsigned NOT NULL DEFAULT '0',
  num_notice int(9) unsigned NOT NULL DEFAULT '0',
  date_ajout timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (num_bannette,num_notice),
  KEY date_ajout (date_ajout),
  KEY i_num_notice (num_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_equation`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_equation (
  num_bannette int(9) unsigned NOT NULL DEFAULT '0',
  num_equation int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_bannette,num_equation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_exports`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_exports (
  num_bannette int(11) unsigned NOT NULL DEFAULT '0',
  export_format int(3) NOT NULL DEFAULT '0',
  export_data longblob NOT NULL,
  export_nomfichier varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (num_bannette,export_format)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_facettes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_facettes (
  num_ban_facette int(10) unsigned NOT NULL DEFAULT '0',
  ban_facette_critere int(5) NOT NULL DEFAULT '0',
  ban_facette_ss_critere int(5) NOT NULL DEFAULT '0',
  ban_facette_order int(1) NOT NULL DEFAULT '0',
  KEY bannette_facettes_key (num_ban_facette,ban_facette_critere,ban_facette_ss_critere)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannette_tpl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannette_tpl (
  bannettetpl_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  bannettetpl_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  bannettetpl_comment varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  bannettetpl_tpl text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (bannettetpl_id)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannettes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannettes (
  id_bannette int(9) unsigned NOT NULL AUTO_INCREMENT,
  num_classement int(8) unsigned NOT NULL DEFAULT '1',
  nom_bannette varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  comment_gestion text COLLATE utf8_unicode_ci NOT NULL,
  comment_public text COLLATE utf8_unicode_ci NOT NULL,
  entete_mail text COLLATE utf8_unicode_ci NOT NULL,
  date_last_remplissage datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_last_envoi datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  proprio_bannette int(9) unsigned NOT NULL DEFAULT '0',
  bannette_auto int(1) unsigned NOT NULL DEFAULT '0',
  periodicite int(3) unsigned NOT NULL DEFAULT '7',
  diffusion_email int(1) unsigned NOT NULL DEFAULT '0',
  categorie_lecteurs int(8) unsigned NOT NULL DEFAULT '0',
  nb_notices_diff int(4) unsigned NOT NULL DEFAULT '0',
  num_panier int(8) unsigned NOT NULL DEFAULT '0',
  limite_type char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  limite_nombre int(6) NOT NULL DEFAULT '0',
  update_type char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'C',
  typeexport varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  prefixe_fichier varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  param_export blob NOT NULL,
  piedpage_mail text COLLATE utf8_unicode_ci NOT NULL,
  notice_tpl int(10) unsigned NOT NULL DEFAULT '0',
  group_type int(10) unsigned NOT NULL DEFAULT '0',
  group_pperso int(10) unsigned NOT NULL DEFAULT '0',
  display_notice_in_every_group int(10) unsigned NOT NULL DEFAULT '0',
  statut_not_account int(1) unsigned NOT NULL DEFAULT '0',
  archive_number int(10) unsigned NOT NULL DEFAULT '0',
  document_generate int(10) unsigned NOT NULL DEFAULT '0',
  document_notice_tpl int(10) unsigned NOT NULL DEFAULT '0',
  document_insert_docnum int(10) unsigned NOT NULL DEFAULT '0',
  document_group int(10) unsigned NOT NULL DEFAULT '0',
  document_add_summary int(10) unsigned NOT NULL DEFAULT '0',
  groupe_lecteurs int(8) unsigned NOT NULL DEFAULT '0',
  bannette_opac_accueil int(10) unsigned NOT NULL DEFAULT '0',
  bannette_tpl_num int(6) unsigned NOT NULL DEFAULT '0',
  bannette_aff_notice_number int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_bannette)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bannettes_descriptors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bannettes_descriptors (
  num_bannette int(11) NOT NULL DEFAULT '0',
  num_noeud int(11) NOT NULL DEFAULT '0',
  bannette_descriptor_order int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_bannette,num_noeud)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budgets`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE budgets (
  id_budget int(8) unsigned NOT NULL AUTO_INCREMENT,
  num_entite int(5) unsigned NOT NULL DEFAULT '0',
  num_exercice int(8) unsigned NOT NULL DEFAULT '0',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaires text COLLATE utf8_unicode_ci,
  montant_global double(12,2) unsigned NOT NULL DEFAULT '0.00',
  seuil_alerte int(3) unsigned NOT NULL DEFAULT '100',
  statut int(3) unsigned NOT NULL DEFAULT '0',
  type_budget int(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_budget)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bulletins`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE bulletins (
  bulletin_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  bulletin_numero varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  bulletin_notice int(8) NOT NULL DEFAULT '0',
  mention_date varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  date_date date NOT NULL DEFAULT '0000-00-00',
  bulletin_titre text COLLATE utf8_unicode_ci,
  index_titre text COLLATE utf8_unicode_ci,
  bulletin_cb varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  num_notice int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (bulletin_id),
  KEY bulletin_numero (bulletin_numero),
  KEY bulletin_notice (bulletin_notice),
  KEY date_date (date_date),
  KEY i_num_notice (num_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_amendes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cache_amendes (
  id_empr int(10) unsigned NOT NULL DEFAULT '0',
  cache_date date NOT NULL DEFAULT '0000-00-00',
  data_amendes blob NOT NULL,
  KEY id_empr (id_empr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caddie`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE caddie (
  idcaddie int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NOTI',
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  autorisations mediumtext COLLATE utf8_unicode_ci,
  caddie_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  acces_rapide int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idcaddie),
  KEY caddie_type (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caddie_content`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE caddie_content (
  caddie_id int(8) unsigned NOT NULL DEFAULT '0',
  object_id int(10) unsigned NOT NULL DEFAULT '0',
  content varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  blob_type varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  flag varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (caddie_id,object_id,content),
  KEY object_id (object_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caddie_procs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE caddie_procs (
  idproc smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SELECT',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  requete blob NOT NULL,
  `comment` tinytext COLLATE utf8_unicode_ci NOT NULL,
  autorisations mediumtext COLLATE utf8_unicode_ci,
  parameters text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idproc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cashdesk`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cashdesk (
  cashdesk_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  cashdesk_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cashdesk_autorisations varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cashdesk_transactypes varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cashdesk_cashbox int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cashdesk_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cashdesk_locations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cashdesk_locations (
  cashdesk_loc_cashdesk_num int(10) unsigned NOT NULL DEFAULT '0',
  cashdesk_loc_num int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cashdesk_loc_cashdesk_num,cashdesk_loc_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cashdesk_sections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cashdesk_sections (
  cashdesk_section_cashdesk_num int(10) unsigned NOT NULL DEFAULT '0',
  cashdesk_section_num int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cashdesk_section_cashdesk_num,cashdesk_section_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categ_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE categ_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categ_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE categ_custom_lists (
  categ_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  categ_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  categ_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (categ_custom_champ),
  KEY editorial_champ_list_value (categ_custom_champ,categ_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categ_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE categ_custom_values (
  categ_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  categ_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  categ_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  categ_custom_text text COLLATE utf8_unicode_ci,
  categ_custom_integer int(11) DEFAULT NULL,
  categ_custom_date date DEFAULT NULL,
  categ_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (categ_custom_champ),
  KEY editorial_custom_origine (categ_custom_origine),
  KEY i_ccv_st (categ_custom_small_text),
  KEY i_ccv_t (categ_custom_text(255)),
  KEY i_ccv_i (categ_custom_integer),
  KEY i_ccv_d (categ_custom_date),
  KEY i_ccv_f (categ_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE categories (
  num_thesaurus int(3) unsigned NOT NULL DEFAULT '1',
  num_noeud int(9) unsigned NOT NULL DEFAULT '0',
  langue varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR',
  libelle_categorie text COLLATE utf8_unicode_ci NOT NULL,
  note_application text COLLATE utf8_unicode_ci NOT NULL,
  comment_public text COLLATE utf8_unicode_ci NOT NULL,
  comment_voir text COLLATE utf8_unicode_ci NOT NULL,
  index_categorie text COLLATE utf8_unicode_ci NOT NULL,
  path_word_categ text COLLATE utf8_unicode_ci NOT NULL,
  index_path_word_categ text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_noeud,langue),
  KEY categ_langue (langue),
  KEY libelle_categorie (libelle_categorie(5))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE classements (
  id_classement int(8) unsigned NOT NULL AUTO_INCREMENT,
  type_classement char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BAN',
  nom_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_classement)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms (
  id_cms int(10) unsigned NOT NULL AUTO_INCREMENT,
  cms_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cms_comment text COLLATE utf8_unicode_ci NOT NULL,
  cms_opac_default int(10) unsigned NOT NULL DEFAULT '0',
  cms_opac_view_num int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_cms)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_articles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_articles (
  id_article int(10) unsigned NOT NULL AUTO_INCREMENT,
  article_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  article_resume mediumtext COLLATE utf8_unicode_ci NOT NULL,
  article_contenu mediumtext COLLATE utf8_unicode_ci NOT NULL,
  article_logo mediumblob NOT NULL,
  article_publication_state varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  article_start_date datetime DEFAULT NULL,
  article_end_date datetime DEFAULT NULL,
  num_section int(11) NOT NULL DEFAULT '0',
  article_num_type int(10) unsigned NOT NULL DEFAULT '0',
  article_creation_date date DEFAULT NULL,
  article_order int(10) unsigned DEFAULT '0',
  article_update_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_article),
  KEY i_cms_article_title (article_title),
  KEY i_cms_article_publication_state (article_publication_state),
  KEY i_cms_article_num_parent (num_section)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_articles_descriptors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_articles_descriptors (
  num_article int(11) NOT NULL DEFAULT '0',
  num_noeud int(11) NOT NULL DEFAULT '0',
  article_descriptor_order int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_article,num_noeud)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_build`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_build (
  id_build int(10) unsigned NOT NULL AUTO_INCREMENT,
  build_version_num int(11) NOT NULL DEFAULT '0',
  build_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'cadre',
  build_fixed int(11) NOT NULL DEFAULT '0',
  build_obj varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  build_page int(11) NOT NULL DEFAULT '0',
  build_parent varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  build_child_before varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  build_child_after varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  build_css text COLLATE utf8_unicode_ci NOT NULL,
  build_div int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_build),
  KEY cms_build_index (build_version_num,build_obj),
  KEY i_build_parent_build_version_num (build_parent,build_version_num),
  KEY i_build_obj_build_version_num (build_obj,build_version_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_cache_cadres`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_cache_cadres (
  cache_cadre_hash varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  cache_cadre_type_content varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  cache_cadre_create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cache_cadre_content mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (cache_cadre_hash,cache_cadre_type_content)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_cadre_content`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_cadre_content (
  id_cadre_content int(10) unsigned NOT NULL AUTO_INCREMENT,
  cadre_content_hash varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_content_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_content_object varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_content_num_cadre int(10) unsigned NOT NULL DEFAULT '0',
  cadre_content_data text COLLATE utf8_unicode_ci NOT NULL,
  cadre_content_num_cadre_content int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_cadre_content)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_cadres`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_cadres (
  id_cadre int(10) unsigned NOT NULL AUTO_INCREMENT,
  cadre_hash varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_object varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_fixed int(11) NOT NULL DEFAULT '0',
  cadre_styles text COLLATE utf8_unicode_ci NOT NULL,
  cadre_dom_parent varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_dom_after varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_url text COLLATE utf8_unicode_ci NOT NULL,
  cadre_memo_url int(11) NOT NULL DEFAULT '0',
  cadre_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cadre_modcache varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'get_post_view',
  PRIMARY KEY (id_cadre)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_collections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_collections (
  id_collection int(10) unsigned NOT NULL AUTO_INCREMENT,
  collection_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collection_description text COLLATE utf8_unicode_ci NOT NULL,
  collection_num_parent int(11) NOT NULL DEFAULT '0',
  collection_num_storage int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_collection),
  KEY i_cms_collection_title (collection_title)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_documents`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_documents (
  id_document int(10) unsigned NOT NULL AUTO_INCREMENT,
  document_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_description text COLLATE utf8_unicode_ci NOT NULL,
  document_filename varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_mimetype varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_filesize int(11) NOT NULL DEFAULT '0',
  document_vignette mediumblob NOT NULL,
  document_url text COLLATE utf8_unicode_ci NOT NULL,
  document_path varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_create_date date NOT NULL DEFAULT '0000-00-00',
  document_num_storage int(11) NOT NULL DEFAULT '0',
  document_type_object varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_num_object int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_document),
  KEY i_cms_document_title (document_title)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_documents_links`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_documents_links (
  document_link_type_object varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  document_link_num_object int(11) NOT NULL DEFAULT '0',
  document_link_num_document int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (document_link_type_object,document_link_num_object,document_link_num_document)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp),
  KEY i_num_type (num_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_custom_lists (
  cms_editorial_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  cms_editorial_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  cms_editorial_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (cms_editorial_custom_champ),
  KEY editorial_champ_list_value (cms_editorial_custom_champ,cms_editorial_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_custom_values (
  cms_editorial_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  cms_editorial_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  cms_editorial_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  cms_editorial_custom_text text COLLATE utf8_unicode_ci,
  cms_editorial_custom_integer int(11) DEFAULT NULL,
  cms_editorial_custom_date date DEFAULT NULL,
  cms_editorial_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (cms_editorial_custom_champ),
  KEY editorial_custom_origine (cms_editorial_custom_origine),
  KEY i_ccv_st (cms_editorial_custom_small_text),
  KEY i_ccv_t (cms_editorial_custom_text(255)),
  KEY i_ccv_i (cms_editorial_custom_integer),
  KEY i_ccv_d (cms_editorial_custom_date),
  KEY i_ccv_f (cms_editorial_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_fields_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_fields_global_index (
  num_obj int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  code_champ int(3) NOT NULL DEFAULT '0',
  code_ss_champ int(3) NOT NULL DEFAULT '0',
  ordre int(4) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  pond int(4) NOT NULL DEFAULT '100',
  lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (num_obj,`type`,code_champ,code_ss_champ,ordre),
  KEY i_value (`value`(300))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_publications_states`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_publications_states (
  id_publication_state int(10) unsigned NOT NULL AUTO_INCREMENT,
  editorial_publication_state_label varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  editorial_publication_state_opac_show int(1) NOT NULL DEFAULT '0',
  editorial_publication_state_auth_opac_show int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_publication_state)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_types`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_types (
  id_editorial_type int(10) unsigned NOT NULL AUTO_INCREMENT,
  editorial_type_element varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  editorial_type_label varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  editorial_type_comment text COLLATE utf8_unicode_ci NOT NULL,
  editorial_type_extension text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_editorial_type),
  KEY i_editorial_type_element (editorial_type_element)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_editorial_words_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_editorial_words_global_index (
  num_obj int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  code_champ int(11) NOT NULL DEFAULT '0',
  code_ss_champ int(11) NOT NULL DEFAULT '0',
  num_word int(11) NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  position int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (num_obj,`type`,code_champ,code_ss_champ,num_word,position)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_hash`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_hash (
  `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_managed_modules`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_managed_modules (
  managed_module_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  managed_module_box text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (managed_module_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_modules_extensions_datas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_modules_extensions_datas (
  id_extension_datas int(10) NOT NULL AUTO_INCREMENT,
  extension_datas_module varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  extension_datas_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  extension_datas_type_element varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  extension_datas_num_element int(10) NOT NULL DEFAULT '0',
  extension_datas_datas blob,
  PRIMARY KEY (id_extension_datas)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_pages`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_pages (
  id_page int(10) unsigned NOT NULL AUTO_INCREMENT,
  page_hash varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  page_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  page_description text COLLATE utf8_unicode_ci NOT NULL,
  page_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_page)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_pages_env`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_pages_env (
  page_env_num_page int(10) unsigned NOT NULL AUTO_INCREMENT,
  page_env_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  page_env_id_selector varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (page_env_num_page)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_sections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_sections (
  id_section int(10) unsigned NOT NULL AUTO_INCREMENT,
  section_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  section_resume mediumtext COLLATE utf8_unicode_ci NOT NULL,
  section_logo mediumblob NOT NULL,
  section_publication_state varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  section_start_date datetime DEFAULT NULL,
  section_end_date datetime DEFAULT NULL,
  section_num_parent int(11) NOT NULL DEFAULT '0',
  section_num_type int(10) unsigned NOT NULL DEFAULT '0',
  section_creation_date date DEFAULT NULL,
  section_order int(10) unsigned DEFAULT '0',
  section_update_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_section),
  KEY i_cms_section_title (section_title),
  KEY i_cms_section_publication_state (section_publication_state),
  KEY i_cms_section_num_parent (section_num_parent)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_sections_descriptors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_sections_descriptors (
  num_section int(11) NOT NULL DEFAULT '0',
  num_noeud int(11) NOT NULL DEFAULT '0',
  section_descriptor_order int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_section,num_noeud)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_vars`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_vars (
  id_var int(10) unsigned NOT NULL AUTO_INCREMENT,
  var_num_page int(10) unsigned NOT NULL DEFAULT '0',
  var_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  var_comment varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_var)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cms_version`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE cms_version (
  id_version int(10) unsigned NOT NULL AUTO_INCREMENT,
  version_cms_num int(10) unsigned NOT NULL DEFAULT '0',
  version_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  version_comment text COLLATE utf8_unicode_ci NOT NULL,
  version_public int(10) unsigned NOT NULL DEFAULT '0',
  version_user int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_version)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collection_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collection_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collection_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collection_custom_lists (
  collection_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  collection_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  collection_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (collection_custom_champ),
  KEY editorial_champ_list_value (collection_custom_champ,collection_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collection_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collection_custom_values (
  collection_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  collection_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  collection_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  collection_custom_text text COLLATE utf8_unicode_ci,
  collection_custom_integer int(11) DEFAULT NULL,
  collection_custom_date date DEFAULT NULL,
  collection_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (collection_custom_champ),
  KEY editorial_custom_origine (collection_custom_origine),
  KEY i_ccv_st (collection_custom_small_text),
  KEY i_ccv_t (collection_custom_text(255)),
  KEY i_ccv_i (collection_custom_integer),
  KEY i_ccv_d (collection_custom_date),
  KEY i_ccv_f (collection_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collections (
  collection_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  collection_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collection_parent mediumint(8) unsigned NOT NULL DEFAULT '0',
  collection_issn varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_coll text COLLATE utf8_unicode_ci,
  collection_web text COLLATE utf8_unicode_ci NOT NULL,
  collection_comment text COLLATE utf8_unicode_ci NOT NULL,
  authority_import_denied int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (collection_id),
  KEY collection_name (collection_name),
  KEY collection_parent (collection_parent)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collections_state`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collections_state (
  collstate_id int(8) NOT NULL AUTO_INCREMENT,
  id_serial mediumint(8) unsigned NOT NULL DEFAULT '0',
  location_id smallint(5) unsigned NOT NULL DEFAULT '0',
  state_collections text COLLATE utf8_unicode_ci NOT NULL,
  collstate_emplacement int(8) unsigned NOT NULL DEFAULT '0',
  collstate_type int(8) unsigned NOT NULL DEFAULT '0',
  collstate_origine varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collstate_cote varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collstate_archive varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collstate_statut int(8) unsigned NOT NULL DEFAULT '0',
  collstate_lacune text COLLATE utf8_unicode_ci NOT NULL,
  collstate_note text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (collstate_id),
  KEY i_colls_arc (collstate_archive),
  KEY i_colls_empl (collstate_emplacement),
  KEY i_colls_type (collstate_type),
  KEY i_colls_orig (collstate_origine),
  KEY i_colls_cote (collstate_cote),
  KEY i_colls_stat (collstate_statut),
  KEY i_colls_serial (id_serial),
  KEY i_colls_loc (location_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collstate_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collstate_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) NOT NULL DEFAULT '0',
  search int(11) NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collstate_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collstate_custom_lists (
  collstate_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  collstate_custom_list_value varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  collstate_custom_list_lib varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ordre int(11) NOT NULL DEFAULT '0',
  KEY collstate_custom_champ (collstate_custom_champ),
  KEY i_ccl_lv (collstate_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collstate_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE collstate_custom_values (
  collstate_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  collstate_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  collstate_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  collstate_custom_text text COLLATE utf8_unicode_ci,
  collstate_custom_integer int(11) DEFAULT NULL,
  collstate_custom_date date DEFAULT NULL,
  collstate_custom_float float DEFAULT NULL,
  KEY collstate_custom_champ (collstate_custom_champ),
  KEY collstate_custom_origine (collstate_custom_origine),
  KEY i_ccv_st (collstate_custom_small_text),
  KEY i_ccv_t (collstate_custom_text(255)),
  KEY i_ccv_i (collstate_custom_integer),
  KEY i_ccv_d (collstate_custom_date),
  KEY i_ccv_f (collstate_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comptes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE comptes (
  id_compte int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  type_compte_id int(10) unsigned NOT NULL DEFAULT '0',
  solde decimal(16,2) DEFAULT '0.00',
  prepay_mnt decimal(16,2) NOT NULL DEFAULT '0.00',
  proprio_id int(10) unsigned NOT NULL DEFAULT '0',
  droits text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_compte),
  KEY i_cpt_proprio_id (proprio_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors (
  connector_id varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  parameters text COLLATE utf8_unicode_ci NOT NULL,
  repository int(11) NOT NULL DEFAULT '0',
  timeout int(11) NOT NULL DEFAULT '5',
  retry int(11) NOT NULL DEFAULT '3',
  ttl int(11) NOT NULL DEFAULT '1440',
  PRIMARY KEY (connector_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_categ`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_categ (
  connectors_categ_id smallint(5) NOT NULL AUTO_INCREMENT,
  connectors_categ_name varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  opac_expanded smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (connectors_categ_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_categ_sources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_categ_sources (
  num_categ smallint(6) NOT NULL DEFAULT '0',
  num_source smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_categ,num_source),
  KEY i_num_source (num_source)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out (
  connectors_out_id int(11) NOT NULL AUTO_INCREMENT,
  connectors_out_config longblob NOT NULL,
  PRIMARY KEY (connectors_out_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_oai_deleted_records`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_oai_deleted_records (
  num_set int(11) unsigned NOT NULL DEFAULT '0',
  num_notice int(11) unsigned NOT NULL DEFAULT '0',
  deletion_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (num_set,num_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_oai_tokens`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_oai_tokens (
  connectors_out_oai_token_token varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  connectors_out_oai_token_environnement text COLLATE utf8_unicode_ci NOT NULL,
  connectors_out_oai_token_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (connectors_out_oai_token_token)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_setcache_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_setcache_values (
  connectors_out_setcache_values_cachenum int(11) NOT NULL DEFAULT '0',
  connectors_out_setcache_values_value int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (connectors_out_setcache_values_cachenum,connectors_out_setcache_values_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_setcaches`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_setcaches (
  connectors_out_setcache_id int(11) NOT NULL AUTO_INCREMENT,
  connectors_out_setcache_setnum int(11) NOT NULL DEFAULT '0',
  connectors_out_setcache_lifeduration int(4) NOT NULL DEFAULT '0',
  connectors_out_setcache_lifeduration_unit enum('seconds','minutes','hours','days','weeks','months') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'seconds',
  connectors_out_setcache_lastupdatedate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (connectors_out_setcache_id),
  UNIQUE KEY connectors_out_setcache_setnum (connectors_out_setcache_setnum)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_setcateg_sets`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_setcateg_sets (
  connectors_out_setcategset_setnum int(11) NOT NULL,
  connectors_out_setcategset_categnum int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (connectors_out_setcategset_setnum,connectors_out_setcategset_categnum)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_setcategs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_setcategs (
  connectors_out_setcateg_id int(11) NOT NULL AUTO_INCREMENT,
  connectors_out_setcateg_name varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (connectors_out_setcateg_id),
  UNIQUE KEY connectors_out_setcateg_name (connectors_out_setcateg_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_sets`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_sets (
  connector_out_set_id int(11) NOT NULL AUTO_INCREMENT,
  connector_out_set_caption varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  connector_out_set_type int(4) NOT NULL DEFAULT '0',
  connector_out_set_config longblob NOT NULL,
  being_refreshed int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (connector_out_set_id),
  UNIQUE KEY connector_out_set_caption (connector_out_set_caption)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_sources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_sources (
  connectors_out_source_id int(11) NOT NULL AUTO_INCREMENT,
  connectors_out_sources_connectornum int(11) NOT NULL DEFAULT '0',
  connectors_out_source_name varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  connectors_out_source_comment varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  connectors_out_source_config longblob NOT NULL,
  PRIMARY KEY (connectors_out_source_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_out_sources_esgroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_out_sources_esgroups (
  connectors_out_source_esgroup_sourcenum int(11) NOT NULL DEFAULT '0',
  connectors_out_source_esgroup_esgroupnum int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (connectors_out_source_esgroup_sourcenum,connectors_out_source_esgroup_esgroupnum)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connectors_sources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE connectors_sources (
  source_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  id_connector varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  parameters mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  repository int(11) NOT NULL DEFAULT '0',
  timeout int(11) NOT NULL DEFAULT '5',
  retry int(11) NOT NULL DEFAULT '3',
  ttl int(11) NOT NULL DEFAULT '1440',
  opac_allowed int(3) unsigned NOT NULL DEFAULT '0',
  rep_upload int(11) NOT NULL DEFAULT '0',
  upload_doc_num int(11) NOT NULL DEFAULT '1',
  enrichment int(11) NOT NULL DEFAULT '0',
  opac_affiliate_search int(11) NOT NULL DEFAULT '0',
  opac_selected int(3) unsigned NOT NULL DEFAULT '0',
  type_enrichment_allowed text COLLATE utf8_unicode_ci NOT NULL,
  ico_notice varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  last_sync_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (source_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coordonnees`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE coordonnees (
  id_contact int(8) unsigned NOT NULL AUTO_INCREMENT,
  type_coord int(1) unsigned NOT NULL DEFAULT '0',
  num_entite int(5) unsigned NOT NULL DEFAULT '0',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  contact varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  adr1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  adr2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cp varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ville varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  etat varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  pays varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tel1 varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tel2 varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  fax varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  email varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaires text COLLATE utf8_unicode_ci,
  PRIMARY KEY (id_contact),
  KEY i_num_entite (num_entite)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes (
  id_demande int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_demandeur int(10) unsigned NOT NULL DEFAULT '0',
  theme_demande int(3) NOT NULL DEFAULT '0',
  type_demande int(3) NOT NULL DEFAULT '0',
  etat_demande int(3) NOT NULL DEFAULT '0',
  date_demande date NOT NULL DEFAULT '0000-00-00',
  date_prevue date NOT NULL DEFAULT '0000-00-00',
  deadline_demande date NOT NULL DEFAULT '0000-00-00',
  titre_demande varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  sujet_demande text COLLATE utf8_unicode_ci NOT NULL,
  progression mediumint(3) NOT NULL DEFAULT '0',
  num_user_cloture mediumint(3) NOT NULL DEFAULT '0',
  num_notice int(10) NOT NULL DEFAULT '0',
  dmde_read_gestion int(1) unsigned NOT NULL DEFAULT '0',
  reponse_finale text COLLATE utf8_unicode_ci,
  dmde_read_opac int(1) unsigned NOT NULL DEFAULT '0',
  demande_note_num int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_demande),
  KEY i_num_demandeur (num_demandeur),
  KEY i_date_demande (date_demande),
  KEY i_deadline_demande (deadline_demande)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_actions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_actions (
  id_action int(10) unsigned NOT NULL AUTO_INCREMENT,
  type_action int(3) NOT NULL DEFAULT '0',
  statut_action int(3) NOT NULL DEFAULT '0',
  sujet_action varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  detail_action text COLLATE utf8_unicode_ci NOT NULL,
  date_action date NOT NULL DEFAULT '0000-00-00',
  deadline_action date NOT NULL DEFAULT '0000-00-00',
  temps_passe float DEFAULT NULL,
  cout mediumint(3) NOT NULL DEFAULT '0',
  progression_action mediumint(3) NOT NULL DEFAULT '0',
  prive_action int(1) NOT NULL DEFAULT '0',
  num_demande int(10) NOT NULL DEFAULT '0',
  actions_num_user int(10) unsigned NOT NULL DEFAULT '0',
  actions_type_user tinyint(4) unsigned NOT NULL DEFAULT '0',
  actions_read_opac int(11) NOT NULL DEFAULT '0',
  actions_read_gestion int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_action),
  KEY i_date_action (date_action),
  KEY i_deadline_action (deadline_action),
  KEY i_num_demande (num_demande),
  KEY i_actions_user (actions_num_user,actions_type_user)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_custom_lists (
  demandes_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  demandes_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  demandes_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY i_demandes_custom_champ (demandes_custom_champ),
  KEY i_demandes_champ_list_value (demandes_custom_champ,demandes_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_custom_values (
  demandes_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  demandes_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  demandes_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  demandes_custom_text text COLLATE utf8_unicode_ci,
  demandes_custom_integer int(11) DEFAULT NULL,
  demandes_custom_date date DEFAULT NULL,
  demandes_custom_float float DEFAULT NULL,
  KEY i_demandes_custom_champ (demandes_custom_champ),
  KEY i_demandes_custom_origine (demandes_custom_origine)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_notes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_notes (
  id_note int(10) unsigned NOT NULL AUTO_INCREMENT,
  prive int(1) NOT NULL DEFAULT '0',
  rapport int(1) NOT NULL DEFAULT '0',
  contenu text COLLATE utf8_unicode_ci NOT NULL,
  date_note datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  num_action int(10) NOT NULL DEFAULT '0',
  num_note_parent int(10) NOT NULL DEFAULT '0',
  notes_num_user int(10) unsigned NOT NULL DEFAULT '0',
  notes_type_user tinyint(4) unsigned NOT NULL DEFAULT '0',
  notes_read_gestion int(1) unsigned NOT NULL DEFAULT '0',
  notes_read_opac int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_note),
  KEY i_date_note (date_note),
  KEY i_num_action (num_action),
  KEY i_num_note_parent (num_note_parent),
  KEY i_notes_user (notes_num_user,notes_type_user)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_theme`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_theme (
  id_theme int(10) unsigned NOT NULL AUTO_INCREMENT,
  libelle_theme varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_theme)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_type (
  id_type int(10) unsigned NOT NULL AUTO_INCREMENT,
  libelle_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  allowed_actions text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandes_users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE demandes_users (
  num_user int(10) NOT NULL DEFAULT '0',
  num_demande int(10) NOT NULL DEFAULT '0',
  date_creation date NOT NULL DEFAULT '0000-00-00',
  users_statut int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_user,num_demande)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs_codestat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docs_codestat (
  idcode smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  codestat_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  statisdoc_codage_import char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  statisdoc_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (idcode),
  KEY statisdoc_owner (statisdoc_owner)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs_location`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docs_location (
  idlocation smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  location_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  locdoc_codage_import varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  locdoc_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  location_pic varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  location_visible_opac tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  adr1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  adr2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  cp varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  town varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  state varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  country varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  phone varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  email varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  website varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  logo varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaire text COLLATE utf8_unicode_ci NOT NULL,
  transfert_ordre smallint(2) unsigned NOT NULL DEFAULT '9999',
  transfert_statut_defaut smallint(5) unsigned NOT NULL DEFAULT '0',
  num_infopage int(6) unsigned NOT NULL DEFAULT '0',
  css_style varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_num int(11) NOT NULL DEFAULT '0',
  surloc_used tinyint(1) NOT NULL DEFAULT '0',
  show_a2z int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (idlocation),
  KEY locdoc_owner (locdoc_owner)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs_section`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docs_section (
  idsection smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  section_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sdoc_codage_import varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  sdoc_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  section_pic varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  section_visible_opac tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (idsection),
  KEY sdoc_owner (sdoc_owner)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs_statut`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docs_statut (
  idstatut smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  statut_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  statut_libelle_opac varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  pret_flag tinyint(4) NOT NULL DEFAULT '1',
  statusdoc_codage_import char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  statusdoc_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  transfert_flag tinyint(4) unsigned NOT NULL DEFAULT '1',
  statut_visible_opac tinyint(1) unsigned NOT NULL DEFAULT '1',
  statut_allow_resa int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (idstatut),
  KEY statusdoc_owner (statusdoc_owner)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docs_type (
  idtyp_doc int(5) unsigned NOT NULL AUTO_INCREMENT,
  tdoc_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  duree_pret smallint(6) NOT NULL DEFAULT '31',
  duree_resa int(6) unsigned NOT NULL DEFAULT '15',
  tdoc_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  tdoc_codage_import varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tarif_pret decimal(16,2) NOT NULL DEFAULT '0.00',
  short_loan_duration int(6) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (idtyp_doc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docsloc_section`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docsloc_section (
  num_section int(5) unsigned NOT NULL DEFAULT '0',
  num_location int(5) unsigned NOT NULL DEFAULT '0',
  num_pclass int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_section,num_location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_categories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_categories (
  id_category int(10) unsigned NOT NULL AUTO_INCREMENT,
  category_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  category_num_parent int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_category)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_datasources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_datasources (
  id_datasource int(10) unsigned NOT NULL AUTO_INCREMENT,
  datasource_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  datasource_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  datasource_ttl int(10) unsigned NOT NULL DEFAULT '0',
  datasource_last_date datetime DEFAULT NULL,
  datasource_parameters mediumtext COLLATE utf8_unicode_ci NOT NULL,
  datasource_num_category int(10) unsigned NOT NULL DEFAULT '0',
  datasource_default_interesting int(10) unsigned NOT NULL DEFAULT '0',
  datasource_clean_html int(10) unsigned NOT NULL DEFAULT '1',
  datasource_num_watch int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_datasource),
  KEY i_docwatch_datasource_title (datasource_title)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_items`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_items (
  id_item int(10) unsigned NOT NULL AUTO_INCREMENT,
  item_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  item_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  item_summary mediumtext COLLATE utf8_unicode_ci NOT NULL,
  item_content mediumtext COLLATE utf8_unicode_ci NOT NULL,
  item_added_date datetime DEFAULT NULL,
  item_publication_date datetime DEFAULT NULL,
  item_hash varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  item_url varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  item_logo_url varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  item_status int(10) unsigned NOT NULL DEFAULT '0',
  item_interesting int(10) unsigned NOT NULL DEFAULT '0',
  item_num_article int(10) unsigned NOT NULL DEFAULT '0',
  item_num_section int(10) unsigned NOT NULL DEFAULT '0',
  item_num_notice int(10) unsigned NOT NULL DEFAULT '0',
  item_num_datasource int(10) unsigned NOT NULL DEFAULT '0',
  item_num_watch int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_item),
  KEY i_docwatch_item_type (item_type),
  KEY i_docwatch_item_title (item_title),
  KEY i_docwatch_item_num_article (item_num_article),
  KEY i_docwatch_item_num_section (item_num_section),
  KEY i_docwatch_item_num_notice (item_num_notice),
  KEY i_docwatch_item_num_watch (item_num_watch)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_items_descriptors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_items_descriptors (
  num_item int(10) unsigned NOT NULL DEFAULT '0',
  num_noeud int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_item,num_noeud)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_items_tags`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_items_tags (
  num_item int(10) unsigned NOT NULL DEFAULT '0',
  num_tag int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_item,num_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_selectors`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_selectors (
  id_selector int(10) unsigned NOT NULL AUTO_INCREMENT,
  selector_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  selector_num_datasource int(10) unsigned NOT NULL DEFAULT '0',
  selector_parameters mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_selector)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_tags`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_tags (
  id_tag int(10) unsigned NOT NULL AUTO_INCREMENT,
  tag_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docwatch_watches`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE docwatch_watches (
  id_watch int(10) unsigned NOT NULL AUTO_INCREMENT,
  watch_title varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  watch_owner int(10) unsigned NOT NULL DEFAULT '0',
  watch_allowed_users varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  watch_num_category int(10) unsigned NOT NULL DEFAULT '0',
  watch_last_date datetime DEFAULT NULL,
  watch_ttl int(10) unsigned NOT NULL DEFAULT '0',
  watch_desc text COLLATE utf8_unicode_ci NOT NULL,
  watch_logo_url varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  watch_record_default_type char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  watch_record_default_status int(10) unsigned NOT NULL DEFAULT '0',
  watch_article_default_parent int(10) unsigned NOT NULL DEFAULT '0',
  watch_article_default_content_type int(10) unsigned NOT NULL DEFAULT '0',
  watch_article_default_publication_status int(10) unsigned NOT NULL DEFAULT '0',
  watch_section_default_parent int(10) unsigned NOT NULL DEFAULT '0',
  watch_section_default_content_type int(10) unsigned NOT NULL DEFAULT '0',
  watch_section_default_publication_status int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_watch),
  KEY i_docwatch_watch_title (watch_title)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dsi_archive`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE dsi_archive (
  num_banette_arc int(10) unsigned NOT NULL DEFAULT '0',
  num_notice_arc int(10) unsigned NOT NULL DEFAULT '0',
  date_diff_arc date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (num_banette_arc,num_notice_arc,date_diff_arc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `editions_states`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE editions_states (
  id_editions_state int(10) unsigned NOT NULL AUTO_INCREMENT,
  editions_state_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  editions_state_num_classement int(11) NOT NULL DEFAULT '0',
  editions_state_used_datasource varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  editions_state_comment text COLLATE utf8_unicode_ci NOT NULL,
  editions_state_fieldslist text COLLATE utf8_unicode_ci NOT NULL,
  editions_state_fieldsparams text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_editions_state)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr (
  id_empr int(10) unsigned NOT NULL AUTO_INCREMENT,
  empr_cb varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  empr_nom varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_prenom varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_adr1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_adr2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_cp varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_ville varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_pays varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_mail varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_tel1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_tel2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_prof varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_year int(4) unsigned NOT NULL DEFAULT '0',
  empr_categ smallint(5) unsigned NOT NULL DEFAULT '0',
  empr_codestat smallint(5) unsigned NOT NULL DEFAULT '0',
  empr_creation datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  empr_modif date NOT NULL DEFAULT '0000-00-00',
  empr_sexe tinyint(3) unsigned NOT NULL DEFAULT '0',
  empr_login varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_password varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_password_is_encrypted int(1) NOT NULL DEFAULT '0',
  empr_digest varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_date_adhesion date DEFAULT NULL,
  empr_date_expiration date DEFAULT NULL,
  empr_msg text COLLATE utf8_unicode_ci,
  empr_lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR',
  empr_ldap tinyint(1) unsigned DEFAULT '0',
  type_abt int(1) NOT NULL DEFAULT '0',
  last_loan_date date DEFAULT NULL,
  empr_location int(6) unsigned NOT NULL DEFAULT '1',
  date_fin_blocage date NOT NULL DEFAULT '0000-00-00',
  total_loans bigint(20) unsigned NOT NULL DEFAULT '0',
  empr_statut bigint(20) unsigned NOT NULL DEFAULT '1',
  cle_validation varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  empr_sms int(1) unsigned NOT NULL DEFAULT '0',
  empr_subscription_action text COLLATE utf8_unicode_ci,
  PRIMARY KEY (id_empr),
  UNIQUE KEY empr_cb (empr_cb),
  KEY empr_nom (empr_nom),
  KEY empr_date_adhesion (empr_date_adhesion),
  KEY empr_date_expiration (empr_date_expiration),
  KEY i_empr_categ (empr_categ),
  KEY i_empr_codestat (empr_codestat),
  KEY i_empr_location (empr_location),
  KEY i_empr_statut (empr_statut),
  KEY i_empr_typabt (type_abt)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_caddie`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_caddie (
  idemprcaddie int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  autorisations mediumtext COLLATE utf8_unicode_ci,
  empr_caddie_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (idemprcaddie)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_caddie_content`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_caddie_content (
  empr_caddie_id int(8) unsigned NOT NULL DEFAULT '0',
  object_id int(10) unsigned NOT NULL DEFAULT '0',
  flag varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (empr_caddie_id,object_id),
  KEY object_id (object_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_caddie_procs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_caddie_procs (
  idproc smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SELECT',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  requete blob NOT NULL,
  `comment` tinytext COLLATE utf8_unicode_ci NOT NULL,
  autorisations mediumtext COLLATE utf8_unicode_ci,
  parameters text COLLATE utf8_unicode_ci,
  PRIMARY KEY (idproc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_categ`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_categ (
  id_categ_empr smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  duree_adhesion int(10) unsigned DEFAULT '365',
  tarif_abt decimal(16,2) NOT NULL DEFAULT '0.00',
  age_min int(3) unsigned NOT NULL DEFAULT '0',
  age_max int(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_categ_empr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_codestat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_codestat (
  idcode smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT',
  PRIMARY KEY (idcode)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_custom_lists (
  empr_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  empr_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  empr_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY empr_custom_champ (empr_custom_champ),
  KEY i_ecl_lv (empr_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_custom_values (
  empr_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  empr_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  empr_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  empr_custom_text text COLLATE utf8_unicode_ci,
  empr_custom_integer int(11) DEFAULT NULL,
  empr_custom_date date DEFAULT NULL,
  empr_custom_float float DEFAULT NULL,
  KEY empr_custom_champ (empr_custom_champ),
  KEY empr_custom_origine (empr_custom_origine),
  KEY i_ecv_st (empr_custom_small_text),
  KEY i_ecv_t (empr_custom_text(255)),
  KEY i_ecv_i (empr_custom_integer),
  KEY i_ecv_d (empr_custom_date),
  KEY i_ecv_f (empr_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_grilles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_grilles (
  empr_grille_categ int(5) NOT NULL DEFAULT '0',
  empr_grille_location int(5) NOT NULL DEFAULT '0',
  empr_grille_format longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (empr_grille_categ,empr_grille_location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_groupe`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_groupe (
  empr_id int(6) unsigned NOT NULL DEFAULT '0',
  groupe_id int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (empr_id,groupe_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empr_statut`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empr_statut (
  idstatut smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  statut_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  allow_loan tinyint(4) NOT NULL DEFAULT '1',
  allow_loan_hist tinyint(4) unsigned NOT NULL DEFAULT '0',
  allow_book tinyint(4) NOT NULL DEFAULT '1',
  allow_opac tinyint(4) NOT NULL DEFAULT '1',
  allow_dsi tinyint(4) NOT NULL DEFAULT '1',
  allow_dsi_priv tinyint(4) NOT NULL DEFAULT '1',
  allow_sugg tinyint(4) NOT NULL DEFAULT '1',
  allow_dema tinyint(4) unsigned NOT NULL DEFAULT '1',
  allow_prol tinyint(4) NOT NULL DEFAULT '1',
  allow_avis tinyint(4) unsigned NOT NULL DEFAULT '1',
  allow_tag tinyint(4) unsigned NOT NULL DEFAULT '1',
  allow_pwd tinyint(4) unsigned NOT NULL DEFAULT '1',
  allow_liste_lecture tinyint(4) unsigned NOT NULL DEFAULT '0',
  allow_self_checkout tinyint(4) unsigned NOT NULL DEFAULT '0',
  allow_self_checkin tinyint(4) unsigned NOT NULL DEFAULT '0',
  allow_serialcirc int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (idstatut)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empty_words_calculs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE empty_words_calculs (
  id_calcul int(9) unsigned NOT NULL AUTO_INCREMENT,
  date_calcul date NOT NULL DEFAULT '0000-00-00',
  php_empty_words text COLLATE utf8_unicode_ci NOT NULL,
  nb_notices_calcul mediumint(8) unsigned NOT NULL DEFAULT '0',
  archive_calcul tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_calcul)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entites`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE entites (
  id_entite int(5) unsigned NOT NULL AUTO_INCREMENT,
  type_entite int(3) unsigned NOT NULL DEFAULT '0',
  num_bibli int(5) unsigned NOT NULL DEFAULT '0',
  raison_sociale varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaires text COLLATE utf8_unicode_ci,
  siret varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  naf varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  rcs varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tva varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_cp_client varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_cp_compta varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  site_web varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  logo varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  autorisations mediumtext COLLATE utf8_unicode_ci NOT NULL,
  num_frais int(8) unsigned NOT NULL DEFAULT '0',
  num_paiement int(8) unsigned NOT NULL DEFAULT '0',
  index_entite text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_entite),
  KEY raison_sociale (raison_sociale)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entrepots_localisations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE entrepots_localisations (
  loc_id int(11) NOT NULL AUTO_INCREMENT,
  loc_code varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  loc_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  loc_visible tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (loc_id),
  UNIQUE KEY loc_code (loc_code)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE equations (
  id_equation int(9) unsigned NOT NULL AUTO_INCREMENT,
  num_classement int(8) unsigned NOT NULL DEFAULT '1',
  nom_equation text COLLATE utf8_unicode_ci NOT NULL,
  comment_equation varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  requete blob NOT NULL,
  proprio_equation int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_equation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `error_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE error_log (
  error_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  error_origin varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  error_text text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_cache`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_cache (
  escache_groupname varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  escache_unique_id varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  escache_value int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (escache_groupname,escache_unique_id,escache_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_cache_blob`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_cache_blob (
  es_cache_objectref varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_objecttype int(11) NOT NULL DEFAULT '0',
  es_cache_objectformat varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_owner varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_creationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  es_cache_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  es_cache_content mediumblob NOT NULL,
  PRIMARY KEY (es_cache_objectref,es_cache_objecttype,es_cache_objectformat,es_cache_owner),
  KEY cache_index (es_cache_owner,es_cache_objectformat,es_cache_objecttype)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_cache_int`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_cache_int (
  es_cache_objectref varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_objecttype int(11) NOT NULL DEFAULT '0',
  es_cache_objectformat varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_owner varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_cache_creationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  es_cache_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  es_cache_content int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (es_cache_objectref,es_cache_objecttype,es_cache_objectformat,es_cache_owner),
  KEY cache_index (es_cache_owner,es_cache_objectformat,es_cache_objecttype)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_converted_cache`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_converted_cache (
  es_converted_cache_objecttype int(11) NOT NULL DEFAULT '0',
  es_converted_cache_objectref int(11) NOT NULL DEFAULT '0',
  es_converted_cache_format varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_converted_cache_value text COLLATE utf8_unicode_ci NOT NULL,
  es_converted_cache_bestbefore datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (es_converted_cache_objecttype,es_converted_cache_objectref,es_converted_cache_format)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_esgroup_esusers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_esgroup_esusers (
  esgroupuser_groupnum int(11) NOT NULL DEFAULT '0',
  esgroupuser_usertype int(4) NOT NULL DEFAULT '0',
  esgroupuser_usernum int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (esgroupuser_usernum,esgroupuser_groupnum,esgroupuser_usertype)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_esgroups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_esgroups (
  esgroup_id int(11) NOT NULL AUTO_INCREMENT,
  esgroup_name varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  esgroup_fullname varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  esgroup_pmbusernum int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (esgroup_id),
  UNIQUE KEY esgroup_name (esgroup_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_esusers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_esusers (
  esuser_id int(11) NOT NULL AUTO_INCREMENT,
  esuser_username varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  esuser_password varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  esuser_fullname varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  esuser_groupnum int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (esuser_id),
  UNIQUE KEY esuser_username (esuser_username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_methods`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_methods (
  id_method int(10) unsigned NOT NULL AUTO_INCREMENT,
  groupe varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  method varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  available smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_method)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_methods_users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_methods_users (
  num_method int(10) unsigned NOT NULL DEFAULT '0',
  num_user int(10) unsigned NOT NULL DEFAULT '0',
  anonymous smallint(6) DEFAULT '0',
  PRIMARY KEY (num_method,num_user)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_searchcache`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_searchcache (
  es_searchcache_searchid varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_searchcache_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  es_searchcache_serializedsearch text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (es_searchcache_searchid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `es_searchsessions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE es_searchsessions (
  es_searchsession_id varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_searchsession_searchnum varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_searchsession_searchrealm varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  es_searchsession_pmbuserid int(11) NOT NULL DEFAULT '-1',
  es_searchsession_opacemprid int(11) NOT NULL DEFAULT '-1',
  es_searchsession_lastseendate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (es_searchsession_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etagere`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE etagere (
  idetagere int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` blob NOT NULL,
  validite int(1) unsigned NOT NULL DEFAULT '0',
  validite_date_deb date NOT NULL DEFAULT '0000-00-00',
  validite_date_fin date NOT NULL DEFAULT '0000-00-00',
  visible_accueil int(1) unsigned NOT NULL DEFAULT '1',
  autorisations mediumtext COLLATE utf8_unicode_ci,
  id_tri int(11) NOT NULL,
  thumbnail_url mediumblob NOT NULL,
  etagere_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (idetagere),
  KEY i_id_tri (id_tri)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etagere_caddie`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE etagere_caddie (
  etagere_id int(8) unsigned NOT NULL DEFAULT '0',
  caddie_id int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (etagere_id,caddie_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exemplaires`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE exemplaires (
  expl_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  expl_cb varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  expl_notice int(10) unsigned NOT NULL DEFAULT '0',
  expl_bulletin int(10) unsigned NOT NULL DEFAULT '0',
  expl_typdoc int(5) unsigned NOT NULL DEFAULT '0',
  expl_cote varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  expl_section smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_statut smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_location smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_codestat smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_date_depot date NOT NULL DEFAULT '0000-00-00',
  expl_date_retour date NOT NULL DEFAULT '0000-00-00',
  expl_note tinytext COLLATE utf8_unicode_ci NOT NULL,
  expl_prix varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  expl_owner mediumint(8) unsigned NOT NULL DEFAULT '0',
  expl_lastempr int(10) unsigned NOT NULL DEFAULT '0',
  last_loan_date date DEFAULT NULL,
  create_date datetime NOT NULL DEFAULT '2005-01-01 00:00:00',
  update_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  type_antivol int(1) unsigned NOT NULL DEFAULT '0',
  transfert_location_origine smallint(5) unsigned NOT NULL DEFAULT '0',
  transfert_statut_origine smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_comment text COLLATE utf8_unicode_ci,
  expl_nbparts int(8) unsigned NOT NULL DEFAULT '1',
  expl_retloc smallint(5) unsigned NOT NULL DEFAULT '0',
  expl_abt_num int(10) unsigned NOT NULL DEFAULT '0',
  transfert_section_origine smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (expl_id),
  UNIQUE KEY expl_cb (expl_cb),
  KEY expl_typdoc (expl_typdoc),
  KEY expl_cote (expl_cote),
  KEY expl_notice (expl_notice),
  KEY expl_codestat (expl_codestat),
  KEY expl_owner (expl_owner),
  KEY expl_bulletin (expl_bulletin),
  KEY i_expl_location (expl_location),
  KEY i_expl_section (expl_section),
  KEY i_expl_statut (expl_statut),
  KEY i_expl_lastempr (expl_lastempr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exemplaires_temp`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE exemplaires_temp (
  cb varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  sess varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  UNIQUE KEY cb (cb)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exercices`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE exercices (
  id_exercice int(8) unsigned NOT NULL AUTO_INCREMENT,
  num_entite int(5) unsigned NOT NULL DEFAULT '0',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  date_debut date NOT NULL DEFAULT '2006-01-01',
  date_fin date NOT NULL DEFAULT '2006-01-01',
  statut int(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_exercice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expl_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE expl_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expl_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE expl_custom_lists (
  expl_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  expl_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  expl_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY expl_custom_champ (expl_custom_champ),
  KEY i_excl_lv (expl_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expl_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE expl_custom_values (
  expl_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  expl_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  expl_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  expl_custom_text text COLLATE utf8_unicode_ci,
  expl_custom_integer int(11) DEFAULT NULL,
  expl_custom_date date DEFAULT NULL,
  expl_custom_float float DEFAULT NULL,
  KEY expl_custom_champ (expl_custom_champ),
  KEY expl_custom_origine (expl_custom_origine),
  KEY i_excv_st (expl_custom_small_text),
  KEY i_excv_t (expl_custom_text(255)),
  KEY i_excv_i (expl_custom_integer),
  KEY i_excv_d (expl_custom_date),
  KEY i_excv_f (expl_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum (
  explnum_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  explnum_notice mediumint(8) unsigned NOT NULL DEFAULT '0',
  explnum_bulletin int(8) unsigned NOT NULL DEFAULT '0',
  explnum_nom varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_mimetype varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_url text COLLATE utf8_unicode_ci NOT NULL,
  explnum_data mediumblob,
  explnum_vignette mediumblob,
  explnum_extfichier varchar(20) COLLATE utf8_unicode_ci DEFAULT '',
  explnum_nomfichier text COLLATE utf8_unicode_ci,
  explnum_statut int(5) unsigned NOT NULL DEFAULT '0',
  explnum_index_sew mediumtext COLLATE utf8_unicode_ci NOT NULL,
  explnum_index_wew mediumtext COLLATE utf8_unicode_ci NOT NULL,
  explnum_repertoire int(8) NOT NULL DEFAULT '0',
  explnum_path text COLLATE utf8_unicode_ci NOT NULL,
  explnum_docnum_statut smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (explnum_id),
  KEY explnum_notice (explnum_notice),
  KEY explnum_bulletin (explnum_bulletin),
  KEY explnum_repertoire (explnum_repertoire),
  KEY i_explnum_nomfichier (explnum_nomfichier(30)),
  FULLTEXT KEY i_f_explnumwew (explnum_index_wew)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_doc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_doc (
  id_explnum_doc int(8) unsigned NOT NULL AUTO_INCREMENT,
  explnum_doc_nomfichier text COLLATE utf8_unicode_ci NOT NULL,
  explnum_doc_mimetype varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_doc_data mediumblob NOT NULL,
  explnum_doc_extfichier varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_doc_url text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_explnum_doc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_doc_actions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_doc_actions (
  num_explnum_doc int(10) NOT NULL DEFAULT '0',
  num_action int(10) NOT NULL DEFAULT '0',
  prive int(1) NOT NULL DEFAULT '0',
  rapport int(1) NOT NULL DEFAULT '0',
  num_explnum int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_explnum_doc,num_action)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_doc_sugg`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_doc_sugg (
  num_explnum_doc int(10) NOT NULL DEFAULT '0',
  num_suggestion int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_explnum_doc,num_suggestion)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_location`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_location (
  num_explnum int(10) NOT NULL DEFAULT '0',
  num_location int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_explnum,num_location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_segments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_segments (
  explnum_segment_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  explnum_segment_explnum_num int(10) unsigned NOT NULL DEFAULT '0',
  explnum_segment_speaker_num int(10) unsigned NOT NULL DEFAULT '0',
  explnum_segment_start double NOT NULL DEFAULT '0',
  explnum_segment_duration double NOT NULL DEFAULT '0',
  explnum_segment_end double NOT NULL DEFAULT '0',
  PRIMARY KEY (explnum_segment_id),
  KEY i_ensg_explnum_num (explnum_segment_explnum_num),
  KEY i_ensg_speaker (explnum_segment_speaker_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_speakers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_speakers (
  explnum_speaker_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  explnum_speaker_explnum_num int(10) unsigned NOT NULL DEFAULT '0',
  explnum_speaker_speaker_num varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_speaker_gender varchar(1) COLLATE utf8_unicode_ci DEFAULT '',
  explnum_speaker_author int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (explnum_speaker_id),
  KEY i_ensk_explnum_num (explnum_speaker_explnum_num),
  KEY i_ensk_author (explnum_speaker_author)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `explnum_statut`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE explnum_statut (
  id_explnum_statut smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  gestion_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  opac_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  class_html varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  explnum_visible_opac tinyint(1) NOT NULL DEFAULT '1',
  explnum_visible_opac_abon tinyint(1) NOT NULL DEFAULT '0',
  explnum_consult_opac tinyint(1) NOT NULL DEFAULT '1',
  explnum_consult_opac_abon tinyint(1) NOT NULL DEFAULT '0',
  explnum_download_opac tinyint(1) NOT NULL DEFAULT '1',
  explnum_download_opac_abon tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_explnum_statut)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `external_count`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE external_count (
  rid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  recid varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  source_id int(11) NOT NULL,
  PRIMARY KEY (rid),
  KEY recid (recid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facettes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE facettes (
  id_facette int(10) unsigned NOT NULL AUTO_INCREMENT,
  facette_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  facette_critere int(5) NOT NULL DEFAULT '0',
  facette_ss_critere int(5) NOT NULL DEFAULT '0',
  facette_nb_result int(2) NOT NULL DEFAULT '0',
  facette_visible tinyint(1) NOT NULL DEFAULT '0',
  facette_type_sort int(1) NOT NULL DEFAULT '0',
  facette_order_sort int(1) NOT NULL DEFAULT '0',
  facette_order int(11) NOT NULL DEFAULT '1',
  facette_limit_plus int(11) NOT NULL DEFAULT '0',
  facette_opac_views_num text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_facette)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_questions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_questions (
  id_faq_question int(10) unsigned NOT NULL AUTO_INCREMENT,
  faq_question_num_type int(10) unsigned NOT NULL DEFAULT '0',
  faq_question_num_theme int(10) unsigned NOT NULL DEFAULT '0',
  faq_question_num_demande int(10) unsigned NOT NULL DEFAULT '0',
  faq_question_question text COLLATE utf8_unicode_ci NOT NULL,
  faq_question_question_userdate varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  faq_question_question_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  faq_question_answer text COLLATE utf8_unicode_ci NOT NULL,
  faq_question_answer_userdate varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  faq_question_answer_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  faq_question_statut int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_faq_question)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_questions_categories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_questions_categories (
  num_faq_question int(10) unsigned NOT NULL DEFAULT '0',
  num_categ int(10) unsigned NOT NULL DEFAULT '0',
  categ_order int(10) unsigned NOT NULL DEFAULT '0',
  KEY i_faq_categ (num_faq_question,num_categ)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_questions_fields_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_questions_fields_global_index (
  id_faq_question int(10) unsigned NOT NULL DEFAULT '0',
  code_champ int(3) unsigned NOT NULL DEFAULT '0',
  code_ss_champ int(3) unsigned NOT NULL DEFAULT '0',
  ordre int(4) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  pond int(4) unsigned NOT NULL DEFAULT '100',
  lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  authority_num varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (id_faq_question,code_champ,code_ss_champ,lang,ordre),
  KEY i_value (`value`(300)),
  KEY i_code_champ_code_ss_champ (code_champ,code_ss_champ)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_questions_words_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_questions_words_global_index (
  id_faq_question int(10) unsigned NOT NULL DEFAULT '0',
  code_champ int(10) unsigned NOT NULL DEFAULT '0',
  code_ss_champ int(10) unsigned NOT NULL DEFAULT '0',
  num_word int(10) unsigned NOT NULL DEFAULT '0',
  pond int(10) unsigned NOT NULL DEFAULT '100',
  position int(10) unsigned NOT NULL DEFAULT '1',
  field_position int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_faq_question,code_champ,num_word,position,code_ss_champ),
  KEY code_champ (code_champ),
  KEY i_id_mot (num_word,id_faq_question),
  KEY i_code_champ_code_ss_champ_num_word (code_champ,code_ss_champ,num_word)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_themes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_themes (
  id_theme int(10) unsigned NOT NULL AUTO_INCREMENT,
  libelle_theme varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_theme)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_types`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE faq_types (
  id_type int(10) unsigned NOT NULL AUTO_INCREMENT,
  libelle_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fiche`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE fiche (
  id_fiche int(10) unsigned NOT NULL AUTO_INCREMENT,
  infos_global text COLLATE utf8_unicode_ci NOT NULL,
  index_infos_global text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_fiche)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frais`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE frais (
  id_frais int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  condition_frais text COLLATE utf8_unicode_ci NOT NULL,
  montant double(12,2) unsigned NOT NULL DEFAULT '0.00',
  num_cp_compta varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_tva_achat varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  index_libelle text COLLATE utf8_unicode_ci,
  PRIMARY KEY (id_frais)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gestfic0_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE gestfic0_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gestfic0_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE gestfic0_custom_lists (
  gestfic0_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  gestfic0_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  gestfic0_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY gestfic0_custom_champ (gestfic0_custom_champ),
  KEY gestfic0_champ_list_value (gestfic0_custom_champ,gestfic0_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gestfic0_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE gestfic0_custom_values (
  gestfic0_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  gestfic0_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  gestfic0_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  gestfic0_custom_text text COLLATE utf8_unicode_ci,
  gestfic0_custom_integer int(11) DEFAULT NULL,
  gestfic0_custom_date date DEFAULT NULL,
  gestfic0_custom_float float DEFAULT NULL,
  KEY gestfic0_custom_champ (gestfic0_custom_champ),
  KEY gestfic0_custom_origine (gestfic0_custom_origine),
  KEY i_gcv_st (gestfic0_custom_small_text),
  KEY i_gcv_t (gestfic0_custom_text(255)),
  KEY i_gcv_i (gestfic0_custom_integer),
  KEY i_gcv_d (gestfic0_custom_date),
  KEY i_gcv_f (gestfic0_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grilles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE grilles (
  grille_typdoc char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  grille_niveau_biblio char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  grille_localisation mediumint(8) NOT NULL DEFAULT '0',
  descr_format longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (grille_typdoc,grille_niveau_biblio,grille_localisation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groupe`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE groupe (
  id_groupe int(6) unsigned NOT NULL AUTO_INCREMENT,
  libelle_groupe varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  resp_groupe int(6) unsigned DEFAULT '0',
  lettre_rappel int(1) unsigned NOT NULL DEFAULT '0',
  mail_rappel int(1) unsigned NOT NULL DEFAULT '0',
  lettre_rappel_show_nomgroup int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_groupe),
  UNIQUE KEY libelle_groupe (libelle_groupe)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groupexpl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE groupexpl (
  id_groupexpl int(10) unsigned NOT NULL AUTO_INCREMENT,
  groupexpl_resp_expl_num int(10) unsigned NOT NULL DEFAULT '0',
  groupexpl_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  groupexpl_comment varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  groupexpl_location int(10) unsigned NOT NULL DEFAULT '0',
  groupexpl_statut_resp int(10) unsigned NOT NULL DEFAULT '0',
  groupexpl_statut_others int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_groupexpl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groupexpl_expl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE groupexpl_expl (
  groupexpl_num int(10) unsigned NOT NULL DEFAULT '0',
  groupexpl_expl_num int(10) unsigned NOT NULL DEFAULT '0',
  groupexpl_checked int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (groupexpl_num,groupexpl_expl_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_field`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_field (
  id_harvest_field int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_harvest_profil int(10) unsigned NOT NULL DEFAULT '0',
  harvest_field_xml_id int(10) unsigned NOT NULL DEFAULT '0',
  harvest_field_first_flag int(10) unsigned NOT NULL DEFAULT '0',
  harvest_field_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_harvest_field)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_profil`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_profil (
  id_harvest_profil int(10) unsigned NOT NULL AUTO_INCREMENT,
  harvest_profil_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_harvest_profil)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_profil_import`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_profil_import (
  id_harvest_profil_import int(10) unsigned NOT NULL AUTO_INCREMENT,
  harvest_profil_import_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_harvest_profil_import)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_profil_import_field`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_profil_import_field (
  num_harvest_profil_import int(10) unsigned NOT NULL DEFAULT '0',
  harvest_profil_import_field_xml_id int(10) unsigned NOT NULL DEFAULT '0',
  harvest_profil_import_field_flag int(10) unsigned NOT NULL DEFAULT '0',
  harvest_profil_import_field_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_harvest_profil_import,harvest_profil_import_field_xml_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_search_field`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_search_field (
  num_harvest_profil int(10) unsigned NOT NULL DEFAULT '0',
  num_source int(10) unsigned NOT NULL DEFAULT '0',
  num_field int(10) unsigned NOT NULL DEFAULT '0',
  num_ss_field int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_harvest_profil,num_source)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `harvest_src`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE harvest_src (
  id_harvest_src int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_harvest_field int(10) unsigned NOT NULL DEFAULT '0',
  num_source int(10) unsigned NOT NULL DEFAULT '0',
  harvest_src_unimacfield varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  harvest_src_unimacsubfield varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  harvest_src_pmb_unimacfield varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  harvest_src_pmb_unimacsubfield varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  harvest_src_prec_flag int(10) unsigned NOT NULL DEFAULT '0',
  harvest_src_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_harvest_src)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `import_marc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE import_marc (
  id_import bigint(5) unsigned NOT NULL AUTO_INCREMENT,
  notice longblob NOT NULL,
  origine varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  no_notice int(10) unsigned DEFAULT '0',
  encoding varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_import),
  KEY i_nonot_orig (no_notice,origine)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `index_concept`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE index_concept (
  num_object int(10) unsigned NOT NULL,
  type_object int(10) unsigned NOT NULL,
  num_concept int(10) unsigned NOT NULL,
  order_concept int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_object,type_object,num_concept)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indexint`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE indexint (
  indexint_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  indexint_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  indexint_comment text COLLATE utf8_unicode_ci NOT NULL,
  index_indexint text COLLATE utf8_unicode_ci,
  num_pclass int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (indexint_id),
  UNIQUE KEY indexint_name (indexint_name,num_pclass)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indexint_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE indexint_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indexint_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE indexint_custom_lists (
  indexint_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  indexint_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  indexint_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (indexint_custom_champ),
  KEY editorial_champ_list_value (indexint_custom_champ,indexint_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indexint_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE indexint_custom_values (
  indexint_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  indexint_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  indexint_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  indexint_custom_text text COLLATE utf8_unicode_ci,
  indexint_custom_integer int(11) DEFAULT NULL,
  indexint_custom_date date DEFAULT NULL,
  indexint_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (indexint_custom_champ),
  KEY editorial_custom_origine (indexint_custom_origine),
  KEY i_icv_st (indexint_custom_small_text),
  KEY i_icv_t (indexint_custom_text(255)),
  KEY i_icv_i (indexint_custom_integer),
  KEY i_icv_d (indexint_custom_date),
  KEY i_icv_f (indexint_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `infopages`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE infopages (
  id_infopage int(10) unsigned NOT NULL AUTO_INCREMENT,
  content_infopage longblob NOT NULL,
  title_infopage varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  valid_infopage tinyint(1) NOT NULL DEFAULT '1',
  restrict_infopage int(11) NOT NULL DEFAULT '0',
  infopage_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_infopage)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lenders`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE lenders (
  idlender smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  lender_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (idlender)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liens_actes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE liens_actes (
  num_acte int(8) unsigned NOT NULL DEFAULT '0',
  num_acte_lie int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_acte,num_acte_lie),
  KEY i_num_acte (num_acte),
  KEY i_num_acte_lie (num_acte_lie)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lignes_actes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE lignes_actes (
  id_ligne int(15) unsigned NOT NULL AUTO_INCREMENT,
  type_ligne int(3) unsigned NOT NULL DEFAULT '0',
  num_acte int(8) unsigned NOT NULL DEFAULT '0',
  lig_ref int(15) unsigned NOT NULL DEFAULT '0',
  num_acquisition int(12) unsigned NOT NULL DEFAULT '0',
  num_rubrique int(8) unsigned NOT NULL DEFAULT '0',
  num_produit int(8) unsigned NOT NULL DEFAULT '0',
  num_type int(8) unsigned NOT NULL DEFAULT '0',
  libelle text COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  prix double(12,2) unsigned NOT NULL DEFAULT '0.00',
  tva float(8,2) unsigned NOT NULL DEFAULT '0.00',
  nb int(5) unsigned NOT NULL DEFAULT '1',
  date_ech date NOT NULL DEFAULT '0000-00-00',
  date_cre date NOT NULL DEFAULT '0000-00-00',
  statut int(3) unsigned NOT NULL DEFAULT '0',
  remise float(8,2) NOT NULL DEFAULT '0.00',
  index_ligne text COLLATE utf8_unicode_ci NOT NULL,
  ligne_ordre smallint(2) unsigned NOT NULL DEFAULT '0',
  debit_tva smallint(2) unsigned NOT NULL DEFAULT '0',
  commentaires_gestion text COLLATE utf8_unicode_ci NOT NULL,
  commentaires_opac text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_ligne),
  KEY num_acte (num_acte)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lignes_actes_relances`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE lignes_actes_relances (
  num_ligne int(10) unsigned NOT NULL,
  date_relance date NOT NULL DEFAULT '0000-00-00',
  type_ligne int(3) unsigned NOT NULL DEFAULT '0',
  num_acte int(8) unsigned NOT NULL DEFAULT '0',
  lig_ref int(15) unsigned NOT NULL DEFAULT '0',
  num_acquisition int(12) unsigned NOT NULL DEFAULT '0',
  num_rubrique int(8) unsigned NOT NULL DEFAULT '0',
  num_produit int(8) unsigned NOT NULL DEFAULT '0',
  num_type int(8) unsigned NOT NULL DEFAULT '0',
  libelle text COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  prix float(8,2) NOT NULL DEFAULT '0.00',
  tva float(8,2) unsigned NOT NULL DEFAULT '0.00',
  nb int(5) unsigned NOT NULL DEFAULT '1',
  date_ech date NOT NULL DEFAULT '0000-00-00',
  date_cre date NOT NULL DEFAULT '0000-00-00',
  statut int(3) unsigned NOT NULL DEFAULT '1',
  remise float(8,2) NOT NULL DEFAULT '0.00',
  index_ligne text COLLATE utf8_unicode_ci NOT NULL,
  ligne_ordre smallint(2) unsigned NOT NULL DEFAULT '0',
  debit_tva smallint(2) unsigned NOT NULL DEFAULT '0',
  commentaires_gestion text COLLATE utf8_unicode_ci NOT NULL,
  commentaires_opac text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_ligne,date_relance)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lignes_actes_statuts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE lignes_actes_statuts (
  id_statut int(3) NOT NULL AUTO_INCREMENT,
  libelle text COLLATE utf8_unicode_ci NOT NULL,
  relance int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_statut)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `linked_mots`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE linked_mots (
  num_mot mediumint(8) unsigned NOT NULL DEFAULT '0',
  num_linked_mot mediumint(8) unsigned NOT NULL DEFAULT '0',
  type_lien tinyint(1) NOT NULL DEFAULT '1',
  ponderation float NOT NULL DEFAULT '1',
  PRIMARY KEY (num_mot,num_linked_mot,type_lien)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_expl_retard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE log_expl_retard (
  id_log int(11) unsigned NOT NULL AUTO_INCREMENT,
  date_log timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  titre varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  expl_id int(11) NOT NULL DEFAULT '0',
  expl_cb varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  date_pret date NOT NULL DEFAULT '0000-00-00',
  date_retour date NOT NULL DEFAULT '0000-00-00',
  amende decimal(16,2) NOT NULL DEFAULT '0.00',
  num_log_retard int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_log)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_retard`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE log_retard (
  id_log int(11) unsigned NOT NULL AUTO_INCREMENT,
  date_log timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  niveau_reel int(1) NOT NULL DEFAULT '0',
  niveau_suppose int(1) NOT NULL DEFAULT '0',
  amende_totale decimal(16,2) NOT NULL DEFAULT '0.00',
  frais decimal(16,2) NOT NULL DEFAULT '0.00',
  idempr int(11) NOT NULL DEFAULT '0',
  log_printed int(1) unsigned NOT NULL DEFAULT '0',
  log_mail int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_log)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logopac`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE logopac (
  id_log int(8) unsigned NOT NULL AUTO_INCREMENT,
  date_log timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  url_demandee varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  url_referente varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  get_log blob NOT NULL,
  post_log blob NOT NULL,
  num_session varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  server_log blob NOT NULL,
  empr_carac blob NOT NULL,
  empr_doc blob NOT NULL,
  empr_expl mediumblob NOT NULL,
  nb_result blob NOT NULL,
  gen_stat blob NOT NULL,
  PRIMARY KEY (id_log),
  KEY lopac_date_log (date_log)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mailtpl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE mailtpl (
  id_mailtpl int(10) unsigned NOT NULL AUTO_INCREMENT,
  mailtpl_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  mailtpl_objet varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  mailtpl_tpl mediumtext COLLATE utf8_unicode_ci NOT NULL,
  mailtpl_users varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_mailtpl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `map_echelles`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE map_echelles (
  map_echelle_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  map_echelle_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (map_echelle_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `map_emprises`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE map_emprises (
  map_emprise_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  map_emprise_type int(10) unsigned NOT NULL DEFAULT '0',
  map_emprise_obj_num int(10) unsigned NOT NULL DEFAULT '0',
  map_emprise_data geometry NOT NULL,
  map_emprise_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (map_emprise_id),
  KEY i_map_emprise_obj_num (map_emprise_obj_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `map_hold_areas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE map_hold_areas (
  id_obj int(10) unsigned NOT NULL DEFAULT '0',
  type_obj int(10) unsigned NOT NULL DEFAULT '0',
  area double DEFAULT NULL,
  bbox_area double DEFAULT NULL,
  center longtext CHARACTER SET latin1,
  PRIMARY KEY (id_obj)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `map_projections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE map_projections (
  map_projection_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  map_projection_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (map_projection_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `map_refs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE map_refs (
  map_ref_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  map_ref_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (map_ref_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mots`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE mots (
  id_mot mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  mot varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_mot),
  UNIQUE KEY mot (mot)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `noeuds`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE noeuds (
  id_noeud int(9) unsigned NOT NULL AUTO_INCREMENT,
  autorite varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_parent int(9) unsigned NOT NULL DEFAULT '0',
  num_renvoi_voir int(9) unsigned NOT NULL DEFAULT '0',
  visible char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  num_thesaurus int(3) unsigned NOT NULL DEFAULT '0',
  path text COLLATE utf8_unicode_ci NOT NULL,
  authority_import_denied int(10) unsigned NOT NULL DEFAULT '0',
  not_use_in_indexation int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_noeud),
  KEY num_parent (num_parent),
  KEY num_thesaurus (num_thesaurus),
  KEY autorite (autorite),
  KEY key_path (path(333)),
  KEY i_num_renvoi_voir (num_renvoi_voir)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_children_records`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_children_records (
  child_record_num_record int(10) unsigned NOT NULL DEFAULT '0',
  child_record_num_formation int(10) unsigned NOT NULL DEFAULT '0',
  child_record_num_type int(10) unsigned NOT NULL DEFAULT '0',
  child_record_num_musicstand int(10) unsigned NOT NULL DEFAULT '0',
  child_record_num_instrument int(10) unsigned NOT NULL DEFAULT '0',
  child_record_effective int(10) unsigned NOT NULL DEFAULT '0',
  child_record_order int(10) unsigned NOT NULL DEFAULT '0',
  child_record_other varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  child_record_num_voice int(10) unsigned NOT NULL DEFAULT '0',
  child_record_num_workshop int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (child_record_num_record)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_exotic_instruments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_exotic_instruments (
  id_exotic_instrument int(10) unsigned NOT NULL AUTO_INCREMENT,
  exotic_instrument_num_nomenclature int(10) unsigned NOT NULL DEFAULT '0',
  exotic_instrument_num_instrument int(10) unsigned NOT NULL DEFAULT '0',
  exotic_instrument_number int(10) unsigned NOT NULL DEFAULT '0',
  exotic_instrument_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_exotic_instrument)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_exotic_other_instruments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_exotic_other_instruments (
  id_exotic_other_instrument int(10) unsigned NOT NULL AUTO_INCREMENT,
  exotic_other_instrument_num_exotic_instrument int(10) unsigned NOT NULL DEFAULT '0',
  exotic_other_instrument_num_instrument int(10) unsigned NOT NULL DEFAULT '0',
  exotic_other_instrument_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_exotic_other_instrument)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_families`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_families (
  id_family int(10) unsigned NOT NULL AUTO_INCREMENT,
  family_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  family_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_family)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_formations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_formations (
  id_formation int(10) unsigned NOT NULL AUTO_INCREMENT,
  formation_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  formation_nature int(10) unsigned NOT NULL DEFAULT '0',
  formation_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_formation)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_instruments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_instruments (
  id_instrument int(10) unsigned NOT NULL AUTO_INCREMENT,
  instrument_code varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  instrument_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  instrument_musicstand_num int(10) unsigned NOT NULL DEFAULT '0',
  instrument_standard int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_instrument)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_musicstands`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_musicstands (
  id_musicstand int(10) unsigned NOT NULL AUTO_INCREMENT,
  musicstand_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  musicstand_famille_num int(10) unsigned NOT NULL DEFAULT '0',
  musicstand_division int(10) unsigned NOT NULL DEFAULT '0',
  musicstand_order int(10) unsigned NOT NULL DEFAULT '0',
  musicstand_workshop int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_musicstand)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_notices_nomenclatures`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_notices_nomenclatures (
  id_notice_nomenclature int(10) unsigned NOT NULL AUTO_INCREMENT,
  notice_nomenclature_num_notice int(10) unsigned NOT NULL DEFAULT '0',
  notice_nomenclature_num_formation int(10) unsigned NOT NULL DEFAULT '0',
  notice_nomenclature_num_type int(10) unsigned NOT NULL DEFAULT '0',
  notice_nomenclature_label varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notice_nomenclature_abbreviation text COLLATE utf8_unicode_ci NOT NULL,
  notice_nomenclature_notes text COLLATE utf8_unicode_ci NOT NULL,
  notice_nomenclature_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_notice_nomenclature)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_types`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_types (
  id_type int(10) unsigned NOT NULL AUTO_INCREMENT,
  type_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  type_formation_num int(10) unsigned NOT NULL DEFAULT '0',
  type_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_voices`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_voices (
  id_voice int(10) unsigned NOT NULL AUTO_INCREMENT,
  voice_code varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  voice_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  voice_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_voice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_workshops`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_workshops (
  id_workshop int(10) unsigned NOT NULL AUTO_INCREMENT,
  workshop_label varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  workshop_num_nomenclature int(10) unsigned NOT NULL DEFAULT '0',
  workshop_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_workshop)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nomenclature_workshops_instruments`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nomenclature_workshops_instruments (
  id_workshop_instrument int(10) unsigned NOT NULL AUTO_INCREMENT,
  workshop_instrument_num_workshop int(10) unsigned NOT NULL DEFAULT '0',
  workshop_instrument_num_instrument int(10) unsigned NOT NULL DEFAULT '0',
  workshop_instrument_number int(10) unsigned NOT NULL DEFAULT '0',
  workshop_instrument_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_workshop_instrument)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notice_onglet`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notice_onglet (
  id_onglet int(10) unsigned NOT NULL AUTO_INCREMENT,
  onglet_name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id_onglet)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notice_statut`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notice_statut (
  id_notice_statut smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  gestion_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  opac_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  notice_visible_opac tinyint(1) NOT NULL DEFAULT '1',
  notice_visible_gestion tinyint(1) NOT NULL DEFAULT '1',
  expl_visible_opac tinyint(1) NOT NULL DEFAULT '1',
  class_html varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notice_visible_opac_abon tinyint(1) NOT NULL DEFAULT '0',
  expl_visible_opac_abon int(10) unsigned NOT NULL DEFAULT '0',
  explnum_visible_opac int(1) unsigned NOT NULL DEFAULT '1',
  explnum_visible_opac_abon int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_notice_statut)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notice_tpl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notice_tpl (
  notpl_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  notpl_name varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notpl_code text COLLATE utf8_unicode_ci NOT NULL,
  notpl_comment varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notpl_id_test int(10) unsigned NOT NULL DEFAULT '0',
  notpl_show_opac int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (notpl_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notice_tplcode`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notice_tplcode (
  num_notpl int(10) unsigned NOT NULL DEFAULT '0',
  notplcode_localisation mediumint(8) NOT NULL DEFAULT '0',
  notplcode_typdoc char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  notplcode_niveau_biblio char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  notplcode_niveau_hierar char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  nottplcode_code text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_notpl,notplcode_localisation,notplcode_typdoc,notplcode_niveau_biblio)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices (
  notice_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  typdoc char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  tit1 text COLLATE utf8_unicode_ci,
  tit2 text COLLATE utf8_unicode_ci,
  tit3 text COLLATE utf8_unicode_ci,
  tit4 text COLLATE utf8_unicode_ci,
  tparent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  tnvol varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed1_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  ed2_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  coll_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  subcoll_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  `year` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  nocoll varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  mention_edition varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  npages varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ill varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  size varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  accomp varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  n_gen text COLLATE utf8_unicode_ci NOT NULL,
  n_contenu text COLLATE utf8_unicode_ci NOT NULL,
  n_resume text COLLATE utf8_unicode_ci NOT NULL,
  lien text COLLATE utf8_unicode_ci NOT NULL,
  eformat varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_l text COLLATE utf8_unicode_ci NOT NULL,
  indexint int(8) unsigned NOT NULL DEFAULT '0',
  index_serie tinytext COLLATE utf8_unicode_ci,
  index_matieres text COLLATE utf8_unicode_ci NOT NULL,
  niveau_biblio char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm',
  niveau_hierar char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  origine_catalogage int(8) unsigned NOT NULL DEFAULT '1',
  prix varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_n_gen text COLLATE utf8_unicode_ci,
  index_n_contenu text COLLATE utf8_unicode_ci,
  index_n_resume text COLLATE utf8_unicode_ci,
  index_sew text COLLATE utf8_unicode_ci,
  index_wew text COLLATE utf8_unicode_ci,
  statut int(5) NOT NULL DEFAULT '1',
  commentaire_gestion text COLLATE utf8_unicode_ci NOT NULL,
  create_date datetime NOT NULL DEFAULT '2005-01-01 00:00:00',
  update_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  signature varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  thumbnail_url mediumblob NOT NULL,
  date_parution date NOT NULL DEFAULT '0000-00-00',
  opac_visible_bulletinage tinyint(3) unsigned NOT NULL DEFAULT '1',
  indexation_lang varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  map_echelle_num int(10) unsigned NOT NULL DEFAULT '0',
  map_projection_num int(10) unsigned NOT NULL DEFAULT '0',
  map_ref_num int(10) unsigned NOT NULL DEFAULT '0',
  map_equinoxe varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notice_is_new int(10) unsigned NOT NULL DEFAULT '0',
  notice_date_is_new datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  opac_serialcirc_demande tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (notice_id),
  KEY typdoc (typdoc),
  KEY tparent_id (tparent_id),
  KEY ed1_id (ed1_id),
  KEY ed2_id (ed2_id),
  KEY coll_id (coll_id),
  KEY subcoll_id (subcoll_id),
  KEY cb (`code`),
  KEY indexint (indexint),
  KEY sig_index (signature),
  KEY i_notice_n_biblio (niveau_biblio),
  KEY i_notice_n_hierar (niveau_hierar),
  KEY notice_eformat (eformat),
  KEY i_date_parution (date_parution),
  KEY i_not_statut (statut)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_authorities_sources`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_authorities_sources (
  num_authority_source int(10) unsigned NOT NULL DEFAULT '0',
  num_notice int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_authority_source,num_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_authperso`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_authperso (
  notice_authperso_notice_num int(10) unsigned NOT NULL DEFAULT '0',
  notice_authperso_authority_num int(10) unsigned NOT NULL DEFAULT '0',
  notice_authperso_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (notice_authperso_notice_num,notice_authperso_authority_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_categories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_categories (
  notcateg_notice int(9) unsigned NOT NULL DEFAULT '0',
  num_noeud int(9) unsigned NOT NULL DEFAULT '0',
  num_vedette int(3) unsigned NOT NULL DEFAULT '0',
  ordre_vedette int(3) unsigned NOT NULL DEFAULT '1',
  ordre_categorie smallint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (notcateg_notice,num_noeud,num_vedette),
  KEY num_noeud (num_noeud)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_custom_lists (
  notices_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  notices_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  notices_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY notices_custom_champ (notices_custom_champ),
  KEY i_ncl_lv (notices_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_custom_values (
  notices_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  notices_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  notices_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  notices_custom_text text COLLATE utf8_unicode_ci,
  notices_custom_integer int(11) DEFAULT NULL,
  notices_custom_date date DEFAULT NULL,
  notices_custom_float float DEFAULT NULL,
  KEY notices_custom_champ (notices_custom_champ),
  KEY notices_custom_origine (notices_custom_origine),
  KEY i_ncv_st (notices_custom_small_text),
  KEY i_ncv_t (notices_custom_text(255)),
  KEY i_ncv_i (notices_custom_integer),
  KEY i_ncv_d (notices_custom_date),
  KEY i_ncv_f (notices_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_externes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_externes (
  num_notice int(11) NOT NULL DEFAULT '0',
  recid varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (num_notice),
  KEY i_recid (recid),
  KEY i_notice_recid (num_notice,recid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_fields_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_fields_global_index (
  id_notice mediumint(8) NOT NULL DEFAULT '0',
  code_champ int(3) NOT NULL DEFAULT '0',
  code_ss_champ int(3) NOT NULL DEFAULT '0',
  ordre int(4) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  pond int(4) NOT NULL DEFAULT '100',
  lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  authority_num varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (id_notice,code_champ,code_ss_champ,lang,ordre),
  KEY i_value (`value`(300)),
  KEY i_code_champ_code_ss_champ (code_champ,code_ss_champ)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (code_champ,code_ss_champ)
PARTITIONS 50 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_global_index (
  num_notice mediumint(8) NOT NULL DEFAULT '0',
  no_index mediumint(8) NOT NULL DEFAULT '0',
  infos_global text COLLATE utf8_unicode_ci NOT NULL,
  index_infos_global text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_notice,no_index)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_langues`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_langues (
  num_notice int(8) unsigned NOT NULL DEFAULT '0',
  type_langue int(1) unsigned NOT NULL DEFAULT '0',
  code_langue char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ordre_langue smallint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_notice,type_langue,code_langue)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_mots_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_mots_global_index (
  id_notice mediumint(8) NOT NULL DEFAULT '0',
  code_champ int(3) NOT NULL DEFAULT '0',
  code_ss_champ int(3) NOT NULL DEFAULT '0',
  num_word int(10) unsigned NOT NULL DEFAULT '0',
  pond int(4) NOT NULL DEFAULT '100',
  position int(11) NOT NULL DEFAULT '1',
  field_position int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (id_notice,code_champ,code_ss_champ,num_word,position,field_position),
  KEY code_champ (code_champ),
  KEY i_id_mot (num_word,id_notice),
  KEY i_code_champ_code_ss_champ_num_word (code_champ,code_ss_champ,num_word)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
/*!50100 PARTITION BY KEY (code_champ,code_ss_champ)
PARTITIONS 50 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_relations`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_relations (
  num_notice bigint(20) unsigned NOT NULL DEFAULT '0',
  linked_notice bigint(20) unsigned NOT NULL DEFAULT '0',
  relation_type char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  rank int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (num_notice,linked_notice),
  KEY linked_notice (linked_notice),
  KEY relation_type (relation_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notices_titres_uniformes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE notices_titres_uniformes (
  ntu_num_notice int(9) unsigned NOT NULL DEFAULT '0',
  ntu_num_tu int(9) unsigned NOT NULL DEFAULT '0',
  ntu_titre varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_date varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_sous_vedette varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_langue varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_version varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_mention varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntu_ordre smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ntu_num_notice,ntu_num_tu)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offres_remises`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE offres_remises (
  num_fournisseur int(5) unsigned NOT NULL DEFAULT '0',
  num_produit int(8) unsigned NOT NULL DEFAULT '0',
  remise float(4,2) unsigned NOT NULL DEFAULT '0.00',
  condition_remise text COLLATE utf8_unicode_ci,
  PRIMARY KEY (num_fournisseur,num_produit)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `onto_uri`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE onto_uri (
  uri_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  uri varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (uri_id),
  UNIQUE KEY uri (uri)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opac_filters`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE opac_filters (
  opac_filter_view_num int(10) unsigned NOT NULL DEFAULT '0',
  opac_filter_path varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  opac_filter_param text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (opac_filter_view_num,opac_filter_path)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opac_liste_lecture`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE opac_liste_lecture (
  id_liste int(8) unsigned NOT NULL AUTO_INCREMENT,
  nom_liste varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  description text COLLATE utf8_unicode_ci,
  notices_associees blob NOT NULL,
  public int(1) NOT NULL DEFAULT '0',
  num_empr int(8) unsigned NOT NULL DEFAULT '0',
  `read_only` int(1) NOT NULL DEFAULT '0',
  confidential int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_liste)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opac_sessions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE opac_sessions (
  empr_id int(10) unsigned NOT NULL DEFAULT '0',
  `session` mediumblob,
  date_rec timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (empr_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opac_views`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE opac_views (
  opac_view_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  opac_view_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  opac_view_query text COLLATE utf8_unicode_ci NOT NULL,
  opac_view_human_query text COLLATE utf8_unicode_ci NOT NULL,
  opac_view_param text COLLATE utf8_unicode_ci NOT NULL,
  opac_view_visible int(1) unsigned NOT NULL DEFAULT '0',
  opac_view_comment text COLLATE utf8_unicode_ci NOT NULL,
  opac_view_last_gen datetime DEFAULT NULL,
  opac_view_ttl int(11) NOT NULL DEFAULT '86400',
  PRIMARY KEY (opac_view_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opac_views_empr`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE opac_views_empr (
  emprview_view_num int(10) unsigned NOT NULL DEFAULT '0',
  emprview_empr_num int(10) unsigned NOT NULL DEFAULT '0',
  emprview_default int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (emprview_view_num,emprview_empr_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `origin_authorities`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE origin_authorities (
  id_origin_authorities int(10) unsigned NOT NULL AUTO_INCREMENT,
  origin_authorities_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  origin_authorities_country varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  origin_authorities_diffusible int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_origin_authorities)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `origine_notice`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE origine_notice (
  orinot_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  orinot_nom varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  orinot_pays varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FR',
  orinot_diffusion int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (orinot_id),
  KEY orinot_nom (orinot_nom)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ouvertures`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE ouvertures (
  date_ouverture date NOT NULL DEFAULT '0000-00-00',
  ouvert int(1) NOT NULL DEFAULT '1',
  commentaire varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_location int(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (date_ouverture,num_location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paiements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE paiements (
  id_paiement int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaire text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_paiement)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `param_subst`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE param_subst (
  subst_module_param varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  subst_module_num int(2) unsigned NOT NULL DEFAULT '0',
  subst_type_param varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  subst_sstype_param varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  subst_valeur_param text COLLATE utf8_unicode_ci NOT NULL,
  subst_comment_param longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (subst_module_param,subst_module_num,subst_type_param,subst_sstype_param)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parametres`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE parametres (
  id_param int(6) unsigned NOT NULL AUTO_INCREMENT,
  type_param varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  sstype_param varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  valeur_param text COLLATE utf8_unicode_ci,
  comment_param longtext COLLATE utf8_unicode_ci,
  section_param varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  gestion int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_param),
  UNIQUE KEY typ_sstyp (type_param,sstype_param)
) ENGINE=MyISAM AUTO_INCREMENT=951 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pclassement`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pclassement (
  id_pclass int(10) unsigned NOT NULL AUTO_INCREMENT,
  name_pclass varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  typedoc varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_pclass)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `perio_relance`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE perio_relance (
  rel_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  rel_abt_num int(10) unsigned NOT NULL DEFAULT '0',
  rel_date_parution date NOT NULL DEFAULT '0000-00-00',
  rel_libelle_numero varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  rel_comment_gestion text COLLATE utf8_unicode_ci NOT NULL,
  rel_comment_opac text COLLATE utf8_unicode_ci NOT NULL,
  rel_nb int(10) unsigned NOT NULL DEFAULT '0',
  rel_date date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (rel_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `planificateur`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE planificateur (
  id_planificateur int(11) unsigned NOT NULL AUTO_INCREMENT,
  num_type_tache int(11) NOT NULL,
  libelle_tache varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  desc_tache varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  num_user int(11) NOT NULL,
  param text COLLATE utf8_unicode_ci,
  statut tinyint(1) unsigned DEFAULT '0',
  rep_upload int(8) DEFAULT NULL,
  path_upload text COLLATE utf8_unicode_ci,
  perio_heure varchar(28) COLLATE utf8_unicode_ci DEFAULT NULL,
  perio_minute varchar(28) COLLATE utf8_unicode_ci DEFAULT '01',
  perio_jour_mois varchar(128) COLLATE utf8_unicode_ci DEFAULT '*',
  perio_jour varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  perio_mois varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  calc_next_heure_deb varchar(28) COLLATE utf8_unicode_ci DEFAULT NULL,
  calc_next_date_deb date DEFAULT NULL,
  PRIMARY KEY (id_planificateur)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pret (
  pret_idempr int(10) unsigned NOT NULL DEFAULT '0',
  pret_idexpl int(10) unsigned NOT NULL DEFAULT '0',
  pret_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  pret_retour date DEFAULT NULL,
  pret_arc_id int(10) unsigned NOT NULL DEFAULT '0',
  niveau_relance int(1) NOT NULL DEFAULT '0',
  date_relance date DEFAULT '0000-00-00',
  printed int(1) NOT NULL DEFAULT '0',
  retour_initial date DEFAULT '0000-00-00',
  cpt_prolongation int(1) NOT NULL DEFAULT '0',
  pret_temp varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  short_loan_flag int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (pret_idexpl),
  KEY i_pret_idempr (pret_idempr),
  KEY i_pret_arc_id (pret_arc_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret_archive`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pret_archive (
  arc_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  arc_debut datetime DEFAULT '0000-00-00 00:00:00',
  arc_fin datetime DEFAULT NULL,
  arc_id_empr int(10) unsigned NOT NULL DEFAULT '0',
  arc_empr_cp varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arc_empr_ville varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arc_empr_prof varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arc_empr_year int(4) unsigned DEFAULT '0',
  arc_empr_categ smallint(5) unsigned DEFAULT '0',
  arc_empr_codestat smallint(5) unsigned DEFAULT '0',
  arc_empr_sexe tinyint(3) unsigned DEFAULT '0',
  arc_empr_statut int(10) unsigned NOT NULL DEFAULT '1',
  arc_empr_location int(6) unsigned NOT NULL DEFAULT '0',
  arc_type_abt int(6) unsigned NOT NULL DEFAULT '0',
  arc_expl_typdoc int(5) unsigned DEFAULT '0',
  arc_expl_cote varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arc_expl_statut smallint(5) unsigned DEFAULT '0',
  arc_expl_location smallint(5) unsigned DEFAULT '0',
  arc_expl_location_origine int(10) unsigned NOT NULL DEFAULT '0',
  arc_expl_location_retour int(10) unsigned NOT NULL DEFAULT '0',
  arc_expl_codestat smallint(5) unsigned DEFAULT '0',
  arc_expl_owner mediumint(8) unsigned DEFAULT '0',
  arc_expl_section int(5) unsigned NOT NULL DEFAULT '0',
  arc_expl_id int(10) unsigned NOT NULL DEFAULT '0',
  arc_expl_notice int(10) unsigned NOT NULL DEFAULT '0',
  arc_expl_bulletin int(10) unsigned NOT NULL DEFAULT '0',
  arc_groupe varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  arc_niveau_relance int(1) unsigned DEFAULT '0',
  arc_date_relance date NOT NULL DEFAULT '0000-00-00',
  arc_printed int(1) unsigned DEFAULT '0',
  arc_cpt_prolongation int(1) unsigned DEFAULT '0',
  arc_short_loan_flag int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (arc_id),
  KEY i_pa_expl_id (arc_expl_id),
  KEY i_pa_idempr (arc_id_empr),
  KEY i_pa_expl_notice (arc_expl_notice),
  KEY i_pa_expl_bulletin (arc_expl_bulletin),
  KEY i_pa_arc_fin (arc_fin),
  KEY i_pa_arc_empr_categ (arc_empr_categ),
  KEY i_pa_arc_expl_location (arc_expl_location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pret_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  filters int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pret_custom_lists (
  pret_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  pret_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  pret_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY i_pret_custom_champ (pret_custom_champ),
  KEY i_pret_champ_list_value (pret_custom_champ,pret_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pret_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pret_custom_values (
  pret_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  pret_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  pret_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  pret_custom_text text COLLATE utf8_unicode_ci,
  pret_custom_integer int(11) DEFAULT NULL,
  pret_custom_date date DEFAULT NULL,
  pret_custom_float float DEFAULT NULL,
  KEY i_pret_custom_champ (pret_custom_champ),
  KEY i_pret_custom_origine (pret_custom_origine)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `procs`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE procs (
  idproc smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  requete blob NOT NULL,
  `comment` tinytext COLLATE utf8_unicode_ci NOT NULL,
  autorisations mediumtext COLLATE utf8_unicode_ci,
  parameters text COLLATE utf8_unicode_ci,
  num_classement int(5) unsigned NOT NULL DEFAULT '0',
  proc_notice_tpl int(2) unsigned NOT NULL DEFAULT '0',
  proc_notice_tpl_field varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (idproc),
  KEY idproc (idproc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `procs_classements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE procs_classements (
  idproc_classement smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  libproc_classement varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (idproc_classement)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publisher_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE publisher_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publisher_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE publisher_custom_lists (
  publisher_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  publisher_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  publisher_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (publisher_custom_champ),
  KEY editorial_champ_list_value (publisher_custom_champ,publisher_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publisher_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE publisher_custom_values (
  publisher_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  publisher_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  publisher_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  publisher_custom_text text COLLATE utf8_unicode_ci,
  publisher_custom_integer int(11) DEFAULT NULL,
  publisher_custom_date date DEFAULT NULL,
  publisher_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (publisher_custom_champ),
  KEY editorial_custom_origine (publisher_custom_origine),
  KEY i_pcv_st (publisher_custom_small_text),
  KEY i_pcv_t (publisher_custom_text(255)),
  KEY i_pcv_i (publisher_custom_integer),
  KEY i_pcv_d (publisher_custom_date),
  KEY i_pcv_f (publisher_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publishers`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE publishers (
  ed_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  ed_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_adr1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_adr2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_cp varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_ville varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_pays varchar(96) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ed_web varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_publisher text COLLATE utf8_unicode_ci,
  ed_comment text COLLATE utf8_unicode_ci,
  PRIMARY KEY (ed_id),
  KEY ed_name (ed_name),
  KEY ed_ville (ed_ville)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotas`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE quotas (
  quota_type int(10) unsigned NOT NULL DEFAULT '0',
  constraint_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  elements int(10) unsigned NOT NULL DEFAULT '0',
  `value` float DEFAULT NULL,
  PRIMARY KEY (quota_type,constraint_type,elements)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotas_finance`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE quotas_finance (
  quota_type int(10) unsigned NOT NULL DEFAULT '0',
  constraint_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  elements int(10) unsigned NOT NULL DEFAULT '0',
  `value` float DEFAULT NULL,
  PRIMARY KEY (quota_type,constraint_type,elements)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotas_opac_views`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE quotas_opac_views (
  quota_type int(10) unsigned NOT NULL DEFAULT '0',
  constraint_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  elements int(10) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (quota_type,constraint_type,elements)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rapport_demandes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rapport_demandes (
  id_item int(10) unsigned NOT NULL AUTO_INCREMENT,
  contenu text COLLATE utf8_unicode_ci NOT NULL,
  num_note int(10) NOT NULL DEFAULT '0',
  num_demande int(10) NOT NULL DEFAULT '0',
  ordre mediumint(3) NOT NULL DEFAULT '0',
  `type` mediumint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_item)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_g2t`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_g2t (
  g mediumint(8) unsigned NOT NULL,
  t mediumint(8) unsigned NOT NULL,
  UNIQUE KEY gt (g,t),
  KEY tg (t,g)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_id2val`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_id2val (
  id mediumint(8) unsigned NOT NULL,
  misc tinyint(1) NOT NULL DEFAULT '0',
  val text COLLATE utf8_unicode_ci NOT NULL,
  val_type tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY id (id,val_type),
  KEY v (val(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_index (
  num_triple int(10) unsigned NOT NULL DEFAULT '0',
  subject_uri text CHARACTER SET utf8 NOT NULL,
  subject_type text COLLATE utf8_unicode_ci NOT NULL,
  predicat_uri text CHARACTER SET utf8 NOT NULL,
  num_object int(10) unsigned NOT NULL DEFAULT '0',
  object_val text CHARACTER SET utf8 NOT NULL,
  object_index text CHARACTER SET utf8 NOT NULL,
  object_lang char(5) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (num_object)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_o2val`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_o2val (
  id mediumint(8) unsigned NOT NULL,
  misc tinyint(1) NOT NULL DEFAULT '0',
  val_hash char(32) COLLATE utf8_unicode_ci NOT NULL,
  val text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY id (id),
  KEY vh (val_hash),
  KEY v (val(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_s2val`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_s2val (
  id mediumint(8) unsigned NOT NULL,
  misc tinyint(1) NOT NULL DEFAULT '0',
  val_hash char(32) COLLATE utf8_unicode_ci NOT NULL,
  val text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY id (id),
  KEY vh (val_hash)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_setting`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_setting (
  k char(32) COLLATE utf8_unicode_ci NOT NULL,
  val text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY k (k)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdfstore_triple`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rdfstore_triple (
  t mediumint(8) unsigned NOT NULL,
  s mediumint(8) unsigned NOT NULL,
  p mediumint(8) unsigned NOT NULL,
  o mediumint(8) unsigned NOT NULL,
  o_lang_dt mediumint(8) unsigned NOT NULL,
  o_comp char(35) COLLATE utf8_unicode_ci NOT NULL,
  s_type tinyint(1) NOT NULL DEFAULT '0',
  o_type tinyint(1) NOT NULL DEFAULT '0',
  misc tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY t (t),
  KEY sp (s,p),
  KEY os (o,s),
  KEY po (p,o),
  KEY misc (misc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recouvrements`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE recouvrements (
  recouvr_id int(16) unsigned NOT NULL AUTO_INCREMENT,
  empr_id int(10) unsigned NOT NULL DEFAULT '0',
  id_expl int(10) unsigned NOT NULL DEFAULT '0',
  date_rec date NOT NULL DEFAULT '0000-00-00',
  libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  montant decimal(16,2) DEFAULT '0.00',
  recouvr_type int(2) unsigned NOT NULL DEFAULT '0',
  date_pret datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_relance1 datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_relance2 datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_relance3 datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (recouvr_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resa`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE resa (
  id_resa mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  resa_idempr int(10) unsigned NOT NULL DEFAULT '0',
  resa_idnotice mediumint(8) unsigned NOT NULL DEFAULT '0',
  resa_idbulletin int(8) unsigned NOT NULL DEFAULT '0',
  resa_date datetime DEFAULT NULL,
  resa_date_debut date NOT NULL DEFAULT '0000-00-00',
  resa_date_fin date NOT NULL DEFAULT '0000-00-00',
  resa_cb varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resa_confirmee int(1) unsigned NOT NULL DEFAULT '0',
  resa_loc_retrait smallint(5) unsigned NOT NULL DEFAULT '0',
  resa_arc int(10) unsigned NOT NULL DEFAULT '0',
  resa_planning_id_resa int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_resa),
  KEY resa_date_fin (resa_date_fin),
  KEY resa_date (resa_date),
  KEY resa_cb (resa_cb),
  KEY i_idbulletin (resa_idbulletin),
  KEY i_idnotice (resa_idnotice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resa_archive`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE resa_archive (
  resarc_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  resarc_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  resarc_debut date NOT NULL DEFAULT '0000-00-00',
  resarc_fin date NOT NULL DEFAULT '0000-00-00',
  resarc_idnotice int(10) unsigned NOT NULL DEFAULT '0',
  resarc_idbulletin int(10) unsigned NOT NULL DEFAULT '0',
  resarc_confirmee int(1) unsigned DEFAULT '0',
  resarc_cb varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resarc_loc_retrait smallint(5) unsigned DEFAULT '0',
  resarc_from_opac int(1) unsigned DEFAULT '0',
  resarc_anulee int(1) unsigned DEFAULT '0',
  resarc_pretee int(1) unsigned DEFAULT '0',
  resarc_arcpretid int(10) unsigned NOT NULL DEFAULT '0',
  resarc_id_empr int(10) unsigned NOT NULL DEFAULT '0',
  resarc_empr_cp varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resarc_empr_ville varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resarc_empr_prof varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resarc_empr_year int(4) unsigned DEFAULT '0',
  resarc_empr_categ smallint(5) unsigned DEFAULT '0',
  resarc_empr_codestat smallint(5) unsigned DEFAULT '0',
  resarc_empr_sexe tinyint(3) unsigned DEFAULT '0',
  resarc_empr_location int(6) unsigned NOT NULL DEFAULT '1',
  resarc_expl_nb int(5) unsigned DEFAULT '0',
  resarc_expl_typdoc int(5) unsigned DEFAULT '0',
  resarc_expl_cote varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  resarc_expl_statut smallint(5) unsigned DEFAULT '0',
  resarc_expl_location smallint(5) unsigned DEFAULT '0',
  resarc_expl_codestat smallint(5) unsigned DEFAULT '0',
  resarc_expl_owner mediumint(8) unsigned DEFAULT '0',
  resarc_expl_section int(5) unsigned NOT NULL DEFAULT '0',
  resarc_resa_planning_id_resa int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (resarc_id),
  KEY i_pa_idempr (resarc_id_empr),
  KEY i_pa_notice (resarc_idnotice),
  KEY i_pa_bulletin (resarc_idbulletin),
  KEY i_pa_resarc_date (resarc_date)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resa_loc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE resa_loc (
  resa_loc int(8) unsigned NOT NULL DEFAULT '0',
  resa_emprloc int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (resa_loc,resa_emprloc),
  KEY i_resa_emprloc (resa_emprloc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resa_planning`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE resa_planning (
  id_resa mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  resa_idempr mediumint(8) unsigned NOT NULL DEFAULT '0',
  resa_idnotice mediumint(8) unsigned NOT NULL DEFAULT '0',
  resa_idbulletin int(8) unsigned NOT NULL DEFAULT '0',
  resa_date datetime DEFAULT NULL,
  resa_date_debut date NOT NULL DEFAULT '0000-00-00',
  resa_date_fin date NOT NULL DEFAULT '0000-00-00',
  resa_validee int(1) unsigned NOT NULL DEFAULT '0',
  resa_confirmee int(1) unsigned NOT NULL DEFAULT '0',
  resa_loc_retrait int(5) unsigned NOT NULL DEFAULT '0',
  resa_qty int(5) unsigned NOT NULL DEFAULT '1',
  resa_remaining_qty int(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_resa),
  KEY resa_date_fin (resa_date_fin),
  KEY resa_date (resa_date)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resa_ranger`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE resa_ranger (
  resa_cb varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (resa_cb)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responsability`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE responsability (
  responsability_author mediumint(8) unsigned NOT NULL DEFAULT '0',
  responsability_notice mediumint(8) unsigned NOT NULL DEFAULT '0',
  responsability_fonction varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  responsability_type mediumint(1) unsigned NOT NULL DEFAULT '0',
  responsability_ordre smallint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (responsability_author,responsability_notice,responsability_fonction),
  KEY responsability_notice (responsability_notice)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `responsability_tu`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE responsability_tu (
  responsability_tu_author_num int(10) unsigned NOT NULL DEFAULT '0',
  responsability_tu_num int(10) unsigned NOT NULL DEFAULT '0',
  responsability_tu_fonction char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  responsability_tu_type int(10) unsigned NOT NULL DEFAULT '0',
  responsability_tu_ordre smallint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (responsability_tu_author_num,responsability_tu_num,responsability_tu_fonction),
  KEY responsability_tu_author (responsability_tu_author_num),
  KEY responsability_tu_num (responsability_tu_num)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rss_content`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rss_content (
  rss_id int(10) unsigned NOT NULL DEFAULT '0',
  rss_content longblob NOT NULL,
  rss_content_parse longblob NOT NULL,
  rss_last timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (rss_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rss_flux`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rss_flux (
  id_rss_flux int(9) unsigned NOT NULL AUTO_INCREMENT,
  nom_rss_flux varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  link_rss_flux blob NOT NULL,
  descr_rss_flux blob NOT NULL,
  lang_rss_flux varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr',
  copy_rss_flux blob NOT NULL,
  editor_rss_flux varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  webmaster_rss_flux varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ttl_rss_flux int(9) unsigned NOT NULL DEFAULT '60',
  img_url_rss_flux blob NOT NULL,
  img_title_rss_flux blob NOT NULL,
  img_link_rss_flux blob NOT NULL,
  format_flux blob NOT NULL,
  rss_flux_content longblob NOT NULL,
  rss_flux_last timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  export_court_flux tinyint(1) unsigned NOT NULL DEFAULT '0',
  tpl_rss_flux int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_rss_flux)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rss_flux_content`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rss_flux_content (
  num_rss_flux int(9) unsigned NOT NULL DEFAULT '0',
  type_contenant char(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BAN',
  num_contenant int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (num_rss_flux,type_contenant,num_contenant)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rubriques`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE rubriques (
  id_rubrique int(8) unsigned NOT NULL AUTO_INCREMENT,
  num_budget int(8) unsigned NOT NULL DEFAULT '0',
  num_parent int(8) unsigned NOT NULL DEFAULT '0',
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  commentaires text COLLATE utf8_unicode_ci NOT NULL,
  montant float(8,2) unsigned NOT NULL DEFAULT '0.00',
  num_cp_compta varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  autorisations mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_rubrique)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sauv_lieux`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sauv_lieux (
  sauv_lieu_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sauv_lieu_nom varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_lieu_url varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_lieu_protocol varchar(10) COLLATE utf8_unicode_ci DEFAULT 'file',
  sauv_lieu_host varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_lieu_login varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_lieu_password varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (sauv_lieu_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sauv_log`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sauv_log (
  sauv_log_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sauv_log_start_date date DEFAULT NULL,
  sauv_log_file varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_log_succeed int(11) DEFAULT '0',
  sauv_log_messages mediumtext COLLATE utf8_unicode_ci,
  sauv_log_userid int(11) DEFAULT NULL,
  PRIMARY KEY (sauv_log_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sauv_sauvegardes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sauv_sauvegardes (
  sauv_sauvegarde_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sauv_sauvegarde_nom varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_sauvegarde_file_prefix varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_sauvegarde_tables mediumtext COLLATE utf8_unicode_ci,
  sauv_sauvegarde_lieux mediumtext COLLATE utf8_unicode_ci,
  sauv_sauvegarde_users mediumtext COLLATE utf8_unicode_ci,
  sauv_sauvegarde_compress int(11) DEFAULT '0',
  sauv_sauvegarde_compress_command mediumtext COLLATE utf8_unicode_ci,
  sauv_sauvegarde_crypt int(11) DEFAULT '0',
  sauv_sauvegarde_key1 varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_sauvegarde_key2 varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (sauv_sauvegarde_id)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sauv_tables`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sauv_tables (
  sauv_table_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  sauv_table_nom varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  sauv_table_tables text COLLATE utf8_unicode_ci,
  PRIMARY KEY (sauv_table_id),
  UNIQUE KEY sauv_table_nom (sauv_table_nom)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_cache`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE search_cache (
  object_id varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  delete_on_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `value` mediumblob NOT NULL,
  PRIMARY KEY (object_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_perso`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE search_perso (
  search_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  num_user int(8) unsigned NOT NULL DEFAULT '0',
  search_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  search_shortname varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  search_query text COLLATE utf8_unicode_ci NOT NULL,
  search_human text COLLATE utf8_unicode_ci NOT NULL,
  search_directlink tinyint(1) unsigned NOT NULL DEFAULT '0',
  autorisations mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (search_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_persopac`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE search_persopac (
  search_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  num_empr int(8) unsigned NOT NULL DEFAULT '0',
  search_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  search_shortname varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  search_query text COLLATE utf8_unicode_ci NOT NULL,
  search_human text COLLATE utf8_unicode_ci NOT NULL,
  search_directlink tinyint(1) unsigned NOT NULL DEFAULT '0',
  search_limitsearch tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (search_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_persopac_empr_categ`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE search_persopac_empr_categ (
  id_categ_empr int(11) NOT NULL DEFAULT '0',
  id_search_persopac int(11) NOT NULL DEFAULT '0',
  KEY i_id_s_persopac (id_search_persopac),
  KEY i_id_categ_empr (id_categ_empr)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc (
  id_serialcirc int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_abt int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_type int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_virtual int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_duration int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_checked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_retard_mode int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_allow_resa int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_allow_copy int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_allow_send_ask int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_allow_subscription int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_duration_before_send int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_statut_circ int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_statut_circ_after int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_state int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_tpl text COLLATE utf8_unicode_ci NOT NULL,
  serialcirc_piedpage text COLLATE utf8_unicode_ci NOT NULL,
  serialcirc_no_ret int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_sort_diff text COLLATE utf8_unicode_ci NOT NULL,
  serialcirc_simple int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_serialcirc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_ask`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_ask (
  id_serialcirc_ask int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_ask_perio int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_ask_serialcirc int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_ask_empr int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_ask_type int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_ask_statut int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_ask_date date NOT NULL DEFAULT '0000-00-00',
  serialcirc_ask_comment text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_serialcirc_ask)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_circ`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_circ (
  id_serialcirc_circ int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_circ_diff int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_circ_expl int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_circ_empr int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_circ_serialcirc int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_order int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_subscription int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_hold_asked int(11) NOT NULL DEFAULT '0',
  serialcirc_circ_ret_asked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_trans_asked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_trans_doc_asked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_circ_expected_date datetime DEFAULT NULL,
  serialcirc_circ_pointed_date datetime DEFAULT NULL,
  PRIMARY KEY (id_serialcirc_circ)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_copy`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_copy (
  id_serialcirc_copy int(11) NOT NULL AUTO_INCREMENT,
  num_serialcirc_copy_empr int(11) NOT NULL DEFAULT '0',
  num_serialcirc_copy_bulletin int(11) NOT NULL DEFAULT '0',
  serialcirc_copy_analysis text COLLATE utf8_unicode_ci,
  serialcirc_copy_date date NOT NULL DEFAULT '0000-00-00',
  serialcirc_copy_state int(11) NOT NULL DEFAULT '0',
  serialcirc_copy_comment text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_serialcirc_copy)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_diff`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_diff (
  id_serialcirc_diff int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_diff_serialcirc int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_diff_empr_type int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_diff_type_diff int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_diff_empr int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_diff_group_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  serialcirc_diff_duration int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_diff_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_serialcirc_diff)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_expl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_expl (
  id_serialcirc_expl int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_expl_id int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_expl_serialcirc int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_bulletine_date date NOT NULL DEFAULT '0000-00-00',
  serialcirc_expl_state_circ int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_expl_serialcirc_diff int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_ret_asked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_trans_asked int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_trans_doc_asked int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_expl_current_empr int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_expl_start_date date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (id_serialcirc_expl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_group`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_group (
  id_serialcirc_group int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_serialcirc_group_diff int(10) unsigned NOT NULL DEFAULT '0',
  num_serialcirc_group_empr int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_group_responsable int(10) unsigned NOT NULL DEFAULT '0',
  serialcirc_group_order int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_serialcirc_group)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serialcirc_tpl`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serialcirc_tpl (
  serialcirctpl_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  serialcirctpl_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  serialcirctpl_comment varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  serialcirctpl_tpl text COLLATE utf8_unicode_ci NOT NULL,
  serialcirctpl_piedpage text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (serialcirctpl_id)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serie_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serie_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serie_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serie_custom_lists (
  serie_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  serie_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  serie_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (serie_custom_champ),
  KEY editorial_champ_list_value (serie_custom_champ,serie_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serie_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE serie_custom_values (
  serie_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  serie_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  serie_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  serie_custom_text text COLLATE utf8_unicode_ci,
  serie_custom_integer int(11) DEFAULT NULL,
  serie_custom_date date DEFAULT NULL,
  serie_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (serie_custom_champ),
  KEY editorial_custom_origine (serie_custom_origine),
  KEY i_scv_st (serie_custom_small_text),
  KEY i_scv_t (serie_custom_text(255)),
  KEY i_scv_i (serie_custom_integer),
  KEY i_scv_d (serie_custom_date),
  KEY i_scv_f (serie_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `series`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE series (
  serie_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  serie_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  serie_index text COLLATE utf8_unicode_ci,
  PRIMARY KEY (serie_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sessions (
  SESSID varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  login varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  IP varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  SESSstart varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  LastOn varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  SESSNAME varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notifications text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shorturls`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE shorturls (
  id_shorturl int(10) unsigned NOT NULL AUTO_INCREMENT,
  shorturl_hash varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  shorturl_last_access datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  shorturl_context text COLLATE utf8_unicode_ci NOT NULL,
  shorturl_type varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  shorturl_action varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_shorturl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skos_fields_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE skos_fields_global_index (
  id_item int(10) unsigned NOT NULL DEFAULT '0',
  code_champ int(3) unsigned NOT NULL DEFAULT '0',
  code_ss_champ int(3) unsigned NOT NULL DEFAULT '0',
  ordre int(4) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  pond int(4) unsigned NOT NULL DEFAULT '100',
  lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  authority_num varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (id_item,code_champ,code_ss_champ,lang,ordre),
  KEY i_value (`value`(300)),
  KEY i_code_champ_code_ss_champ (code_champ,code_ss_champ)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skos_words_global_index`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE skos_words_global_index (
  id_item int(10) unsigned NOT NULL DEFAULT '0',
  code_champ int(10) unsigned NOT NULL DEFAULT '0',
  code_ss_champ int(10) unsigned NOT NULL DEFAULT '0',
  num_word int(10) unsigned NOT NULL DEFAULT '0',
  pond int(10) unsigned NOT NULL DEFAULT '100',
  position int(10) unsigned NOT NULL DEFAULT '1',
  field_position int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id_item,code_champ,num_word,position,code_ss_champ),
  KEY code_champ (code_champ),
  KEY i_id_mot (num_word,id_item),
  KEY i_code_champ_code_ss_champ_num_word (code_champ,code_ss_champ,num_word)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_sync`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE source_sync (
  source_id int(10) unsigned NOT NULL DEFAULT '0',
  nrecu varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ntotal varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  message varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  date_sync datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  percent int(10) unsigned NOT NULL DEFAULT '0',
  env text COLLATE utf8_unicode_ci NOT NULL,
  cancel int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (source_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sources_enrichment`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sources_enrichment (
  source_enrichment_num int(11) NOT NULL DEFAULT '0',
  source_enrichment_typnotice varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  source_enrichment_typdoc varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  source_enrichment_params text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (source_enrichment_num,source_enrichment_typnotice,source_enrichment_typdoc),
  KEY i_s_enrichment_typnoti (source_enrichment_typnotice),
  KEY i_s_enrichment_typdoc (source_enrichment_typdoc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statopac`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE statopac (
  id_log int(8) unsigned NOT NULL AUTO_INCREMENT,
  date_log timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  url_demandee varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  url_referente varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  get_log blob NOT NULL,
  post_log blob NOT NULL,
  num_session varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  server_log blob NOT NULL,
  empr_carac blob NOT NULL,
  empr_doc blob NOT NULL,
  empr_expl mediumblob NOT NULL,
  nb_result blob NOT NULL,
  gen_stat blob NOT NULL,
  PRIMARY KEY (id_log),
  KEY sopac_date_log (date_log)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statopac_request`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE statopac_request (
  idproc int(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  requete blob NOT NULL,
  `comment` tinytext COLLATE utf8_unicode_ci NOT NULL,
  parameters text COLLATE utf8_unicode_ci NOT NULL,
  num_vue mediumint(8) NOT NULL DEFAULT '0',
  autorisations mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (idproc)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statopac_vues`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE statopac_vues (
  id_vue int(8) unsigned NOT NULL AUTO_INCREMENT,
  date_consolidation datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  nom_vue varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` tinytext COLLATE utf8_unicode_ci NOT NULL,
  date_debut_log datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_fin_log datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id_vue)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statopac_vues_col`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE statopac_vues_col (
  id_col int(8) unsigned NOT NULL AUTO_INCREMENT,
  nom_col varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  expression varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_vue mediumint(8) NOT NULL DEFAULT '0',
  ordre mediumint(8) NOT NULL DEFAULT '0',
  filtre varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  maj_flag int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id_col)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `storages`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE storages (
  id_storage int(10) unsigned NOT NULL AUTO_INCREMENT,
  storage_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  storage_class varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  storage_params text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_storage),
  KEY i_storage_class (storage_class)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_collections`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sub_collections (
  sub_coll_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  sub_coll_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  sub_coll_parent mediumint(9) unsigned NOT NULL DEFAULT '0',
  sub_coll_issn varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  index_sub_coll text COLLATE utf8_unicode_ci,
  subcollection_web text COLLATE utf8_unicode_ci NOT NULL,
  subcollection_comment text COLLATE utf8_unicode_ci NOT NULL,
  authority_import_denied int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (sub_coll_id),
  KEY sub_coll_name (sub_coll_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subcollection_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE subcollection_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subcollection_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE subcollection_custom_lists (
  subcollection_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  subcollection_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  subcollection_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (subcollection_custom_champ),
  KEY editorial_champ_list_value (subcollection_custom_champ,subcollection_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subcollection_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE subcollection_custom_values (
  subcollection_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  subcollection_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  subcollection_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  subcollection_custom_text text COLLATE utf8_unicode_ci,
  subcollection_custom_integer int(11) DEFAULT NULL,
  subcollection_custom_date date DEFAULT NULL,
  subcollection_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (subcollection_custom_champ),
  KEY editorial_custom_origine (subcollection_custom_origine),
  KEY i_scv_st (subcollection_custom_small_text),
  KEY i_scv_t (subcollection_custom_text(255)),
  KEY i_scv_i (subcollection_custom_integer),
  KEY i_scv_d (subcollection_custom_date),
  KEY i_scv_f (subcollection_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE suggestions (
  id_suggestion int(12) unsigned NOT NULL AUTO_INCREMENT,
  titre tinytext COLLATE utf8_unicode_ci NOT NULL,
  editeur varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  auteur varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  prix float(8,2) unsigned NOT NULL DEFAULT '0.00',
  commentaires text COLLATE utf8_unicode_ci,
  commentaires_gestion text COLLATE utf8_unicode_ci,
  statut int(3) unsigned NOT NULL DEFAULT '0',
  num_produit int(8) NOT NULL DEFAULT '0',
  num_entite int(5) NOT NULL DEFAULT '0',
  index_suggestion text COLLATE utf8_unicode_ci NOT NULL,
  nb int(5) unsigned NOT NULL DEFAULT '1',
  date_creation date NOT NULL DEFAULT '0000-00-00',
  date_decision date NOT NULL DEFAULT '0000-00-00',
  num_rubrique int(8) unsigned NOT NULL DEFAULT '0',
  num_fournisseur int(5) unsigned NOT NULL DEFAULT '0',
  num_notice int(8) unsigned NOT NULL DEFAULT '0',
  url_suggestion varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_categ int(12) NOT NULL DEFAULT '1',
  sugg_location smallint(5) unsigned NOT NULL DEFAULT '0',
  sugg_source int(8) NOT NULL DEFAULT '0',
  date_publication varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  notice_unimarc blob NOT NULL,
  PRIMARY KEY (id_suggestion)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions_categ`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE suggestions_categ (
  id_categ int(12) NOT NULL AUTO_INCREMENT,
  libelle_categ varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_categ)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions_origine`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE suggestions_origine (
  origine varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_suggestion int(12) unsigned NOT NULL DEFAULT '0',
  type_origine int(3) unsigned NOT NULL DEFAULT '0',
  date_suggestion date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (origine,num_suggestion,type_origine),
  KEY i_origine (origine,type_origine)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions_source`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE suggestions_source (
  id_source int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle_source varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_source)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sur_location`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE sur_location (
  surloc_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  surloc_libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_pic varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_visible_opac tinyint(1) unsigned NOT NULL DEFAULT '1',
  surloc_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_adr1 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_adr2 varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_cp varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_town varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_state varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_country varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_phone varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_email varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_website varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_logo varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  surloc_comment text COLLATE utf8_unicode_ci NOT NULL,
  surloc_num_infopage int(6) unsigned NOT NULL DEFAULT '0',
  surloc_css_style varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (surloc_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taches`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE taches (
  id_tache int(11) unsigned NOT NULL AUTO_INCREMENT,
  num_planificateur int(11) DEFAULT NULL,
  start_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  end_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  msg_statut blob,
  commande int(8) NOT NULL DEFAULT '0',
  next_state int(8) NOT NULL DEFAULT '0',
  msg_commande blob,
  indicat_progress int(3) DEFAULT NULL,
  rapport text COLLATE utf8_unicode_ci,
  id_process int(8) DEFAULT NULL,
  PRIMARY KEY (id_tache)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taches_docnum`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE taches_docnum (
  id_tache_docnum int(11) unsigned NOT NULL AUTO_INCREMENT,
  tache_docnum_nomfichier varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  tache_docnum_mimetype varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  tache_docnum_data mediumblob NOT NULL,
  tache_docnum_extfichier varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  tache_docnum_repertoire int(8) DEFAULT NULL,
  tache_docnum_path text COLLATE utf8_unicode_ci NOT NULL,
  num_tache int(11) NOT NULL,
  PRIMARY KEY (id_tache_docnum)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taches_type`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE taches_type (
  id_type_tache int(11) unsigned NOT NULL,
  parameters text COLLATE utf8_unicode_ci NOT NULL,
  timeout int(11) NOT NULL DEFAULT '5',
  histo_day int(11) NOT NULL DEFAULT '7',
  histo_number int(11) NOT NULL DEFAULT '3',
  restart_on_failure int(1) unsigned NOT NULL DEFAULT '0',
  alert_mail_on_failure varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (id_type_tache)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tags (
  id_tag mediumint(8) NOT NULL AUTO_INCREMENT,
  libelle varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_notice mediumint(8) NOT NULL DEFAULT '0',
  user_code varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  dateajout timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tag)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `thesaurus`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE thesaurus (
  id_thesaurus int(3) unsigned NOT NULL AUTO_INCREMENT,
  libelle_thesaurus varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  langue_defaut varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR',
  active char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  opac_active char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  num_noeud_racine int(9) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_thesaurus),
  UNIQUE KEY libelle_thesaurus (libelle_thesaurus)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `titres_uniformes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE titres_uniformes (
  tu_id int(9) unsigned NOT NULL AUTO_INCREMENT,
  tu_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_tonalite varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_comment text COLLATE utf8_unicode_ci NOT NULL,
  index_tu text COLLATE utf8_unicode_ci NOT NULL,
  tu_import_denied int(10) unsigned NOT NULL DEFAULT '0',
  tu_num_author bigint(11) unsigned NOT NULL DEFAULT '0',
  tu_forme varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_date varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_date_date date NOT NULL DEFAULT '0000-00-00',
  tu_sujet text COLLATE utf8_unicode_ci NOT NULL,
  tu_lieu varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_histoire text COLLATE utf8_unicode_ci,
  tu_caracteristique text COLLATE utf8_unicode_ci,
  tu_public varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_contexte text COLLATE utf8_unicode_ci,
  tu_coordonnees varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_equinoxe varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_completude int(2) unsigned NOT NULL DEFAULT '0',
  tu_tonalite_marclist varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tu_forme_marclist varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (tu_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transacash`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transacash (
  transacash_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  transacash_empr_num int(10) unsigned NOT NULL DEFAULT '0',
  transacash_desk_num int(10) unsigned NOT NULL DEFAULT '0',
  transacash_user_num int(10) unsigned NOT NULL DEFAULT '0',
  transacash_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  transacash_sold float NOT NULL DEFAULT '0',
  transacash_collected float NOT NULL DEFAULT '0',
  transacash_rendering float NOT NULL DEFAULT '0',
  PRIMARY KEY (transacash_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transactions (
  id_transaction int(10) unsigned NOT NULL AUTO_INCREMENT,
  compte_id int(8) unsigned NOT NULL DEFAULT '0',
  user_id int(10) unsigned NOT NULL DEFAULT '0',
  user_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  machine varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  date_enrgt datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  date_prevue date DEFAULT NULL,
  date_effective date DEFAULT NULL,
  montant decimal(16,2) NOT NULL DEFAULT '0.00',
  sens int(1) NOT NULL DEFAULT '0',
  realisee int(1) NOT NULL DEFAULT '0',
  commentaire text COLLATE utf8_unicode_ci,
  encaissement int(1) NOT NULL DEFAULT '0',
  transactype_num int(10) unsigned NOT NULL DEFAULT '0',
  cashdesk_num int(10) unsigned NOT NULL DEFAULT '0',
  transacash_num int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_transaction)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactype`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transactype (
  transactype_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  transactype_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  transactype_quick_allowed int(10) unsigned NOT NULL DEFAULT '0',
  transactype_unit_price float NOT NULL DEFAULT '0',
  PRIMARY KEY (transactype_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transferts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transferts (
  id_transfert int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_notice int(10) unsigned NOT NULL DEFAULT '0',
  num_bulletin int(10) unsigned NOT NULL DEFAULT '0',
  date_creation date NOT NULL,
  type_transfert int(5) unsigned NOT NULL DEFAULT '0',
  etat_transfert tinyint(3) unsigned NOT NULL DEFAULT '0',
  origine int(5) unsigned NOT NULL DEFAULT '0',
  origine_comp varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `source` smallint(5) unsigned DEFAULT NULL,
  destinations varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  date_retour date DEFAULT NULL,
  motif varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_transfert),
  KEY etat_transfert (etat_transfert)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transferts_demande`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transferts_demande (
  id_transfert_demande int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_transfert int(10) unsigned NOT NULL DEFAULT '0',
  date_creation date NOT NULL,
  sens_transfert tinyint(3) unsigned NOT NULL DEFAULT '0',
  num_location_source smallint(5) unsigned NOT NULL DEFAULT '0',
  num_location_dest smallint(5) unsigned NOT NULL DEFAULT '0',
  num_expl int(10) unsigned NOT NULL DEFAULT '0',
  etat_demande tinyint(3) unsigned NOT NULL DEFAULT '0',
  date_visualisee date DEFAULT NULL,
  date_envoyee date DEFAULT NULL,
  date_reception date DEFAULT NULL,
  motif_refus varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  statut_origine int(10) unsigned NOT NULL DEFAULT '0',
  section_origine int(10) unsigned NOT NULL DEFAULT '0',
  resa_trans int(8) unsigned NOT NULL DEFAULT '0',
  resa_arc_trans int(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_transfert_demande),
  KEY num_transfert (num_transfert),
  KEY num_location_source (num_location_source),
  KEY num_location_dest (num_location_dest),
  KEY num_expl (num_expl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transferts_source`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE transferts_source (
  trans_source_numexpl int(10) unsigned NOT NULL DEFAULT '0',
  trans_source_numloc int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (trans_source_numexpl)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translation`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE translation (
  trans_table varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  trans_field varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  trans_lang varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  trans_num int(8) unsigned NOT NULL DEFAULT '0',
  trans_text varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (trans_table,trans_field,trans_lang,trans_num),
  KEY i_lang (trans_lang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tris`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tris (
  id_tri int(4) NOT NULL AUTO_INCREMENT,
  tri_par varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  nom_tri varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  tri_reference varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notices',
  PRIMARY KEY (id_tri)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_custom`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_custom (
  idchamp int(10) unsigned NOT NULL AUTO_INCREMENT,
  num_type int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  titre varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  datatype varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `options` text COLLATE utf8_unicode_ci,
  multiple int(11) NOT NULL DEFAULT '0',
  obligatoire int(11) NOT NULL DEFAULT '0',
  ordre int(11) DEFAULT NULL,
  search int(1) unsigned NOT NULL DEFAULT '0',
  export int(1) unsigned NOT NULL DEFAULT '0',
  exclusion_obligatoire int(1) unsigned NOT NULL DEFAULT '0',
  pond int(11) NOT NULL DEFAULT '100',
  opac_sort int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (idchamp)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_custom_lists`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_custom_lists (
  tu_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  tu_custom_list_value varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  tu_custom_list_lib varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  ordre int(11) DEFAULT NULL,
  KEY editorial_custom_champ (tu_custom_champ),
  KEY editorial_champ_list_value (tu_custom_champ,tu_custom_list_value)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_custom_values`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_custom_values (
  tu_custom_champ int(10) unsigned NOT NULL DEFAULT '0',
  tu_custom_origine int(10) unsigned NOT NULL DEFAULT '0',
  tu_custom_small_text varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  tu_custom_text text COLLATE utf8_unicode_ci,
  tu_custom_integer int(11) DEFAULT NULL,
  tu_custom_date date DEFAULT NULL,
  tu_custom_float float DEFAULT NULL,
  KEY editorial_custom_champ (tu_custom_champ),
  KEY editorial_custom_origine (tu_custom_origine),
  KEY i_tcv_st (tu_custom_small_text),
  KEY i_tcv_t (tu_custom_text(255)),
  KEY i_tcv_i (tu_custom_integer),
  KEY i_tcv_d (tu_custom_date),
  KEY i_tcv_f (tu_custom_float)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_distrib`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_distrib (
  distrib_num_tu int(9) unsigned NOT NULL DEFAULT '0',
  distrib_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  distrib_ordre smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (distrib_num_tu,distrib_ordre)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_ref`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_ref (
  ref_num_tu int(9) unsigned NOT NULL DEFAULT '0',
  ref_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  ref_ordre smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ref_num_tu,ref_ordre)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tu_subdiv`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tu_subdiv (
  subdiv_num_tu int(9) unsigned NOT NULL DEFAULT '0',
  subdiv_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  subdiv_ordre smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (subdiv_num_tu,subdiv_ordre)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tva_achats`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tva_achats (
  id_tva int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  taux_tva float(4,2) unsigned NOT NULL DEFAULT '0.00',
  num_cp_compta varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (id_tva)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `type_abts`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE type_abts (
  id_type_abt int(5) unsigned NOT NULL AUTO_INCREMENT,
  type_abt_libelle varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  prepay int(1) unsigned NOT NULL DEFAULT '0',
  prepay_deflt_mnt decimal(16,2) NOT NULL DEFAULT '0.00',
  tarif decimal(16,2) NOT NULL DEFAULT '0.00',
  commentaire text COLLATE utf8_unicode_ci NOT NULL,
  caution decimal(16,2) NOT NULL DEFAULT '0.00',
  localisations varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_type_abt)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `type_comptes`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE type_comptes (
  id_type_compte int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  type_acces int(8) unsigned NOT NULL DEFAULT '0',
  acces_id text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_type_compte)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `types_produits`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE types_produits (
  id_produit int(8) unsigned NOT NULL AUTO_INCREMENT,
  libelle varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  num_cp_compta varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  num_tva_achat varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (id_produit),
  KEY libelle (libelle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `upload_repertoire`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE upload_repertoire (
  repertoire_id int(8) unsigned NOT NULL AUTO_INCREMENT,
  repertoire_nom varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  repertoire_url text COLLATE utf8_unicode_ci NOT NULL,
  repertoire_path text COLLATE utf8_unicode_ci NOT NULL,
  repertoire_navigation int(1) NOT NULL DEFAULT '0',
  repertoire_hachage int(1) NOT NULL DEFAULT '0',
  repertoire_subfolder int(8) NOT NULL DEFAULT '0',
  repertoire_utf8 int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (repertoire_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE users (
  userid int(5) NOT NULL AUTO_INCREMENT,
  create_dt date NOT NULL DEFAULT '0000-00-00',
  last_updated_dt date NOT NULL DEFAULT '0000-00-00',
  username varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  pwd varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  user_digest varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  nom varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  prenom varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  rights int(8) unsigned NOT NULL DEFAULT '0',
  user_lang varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR',
  nb_per_page_search int(10) unsigned NOT NULL DEFAULT '4',
  nb_per_page_select int(10) unsigned NOT NULL DEFAULT '10',
  nb_per_page_gestion int(10) unsigned NOT NULL DEFAULT '20',
  param_popup_ticket smallint(1) unsigned NOT NULL DEFAULT '0',
  param_sounds smallint(1) unsigned NOT NULL DEFAULT '1',
  param_rfid_activate int(1) NOT NULL DEFAULT '1',
  param_licence int(1) unsigned NOT NULL DEFAULT '0',
  deflt_notice_statut int(6) unsigned NOT NULL DEFAULT '1',
  deflt_integration_notice_statut int(6) NOT NULL DEFAULT '1',
  xmlta_indexation_lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  deflt_docs_type int(6) unsigned NOT NULL DEFAULT '1',
  deflt_lenders int(6) unsigned NOT NULL DEFAULT '0',
  deflt_styles varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  deflt_docs_statut int(6) unsigned DEFAULT '0',
  deflt_docs_codestat int(6) unsigned DEFAULT '0',
  value_deflt_lang varchar(20) COLLATE utf8_unicode_ci DEFAULT 'fre',
  value_deflt_fonction varchar(20) COLLATE utf8_unicode_ci DEFAULT '070',
  value_deflt_relation varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  value_deflt_relation_serial varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  value_deflt_relation_bulletin varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  value_deflt_relation_analysis varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  deflt_docs_location int(6) unsigned DEFAULT '0',
  deflt_collstate_location int(6) unsigned DEFAULT '0',
  deflt_bulletinage_location int(6) unsigned NOT NULL DEFAULT '0',
  deflt_resas_location int(6) unsigned DEFAULT '0',
  deflt_docs_section int(6) unsigned DEFAULT '0',
  value_deflt_module varchar(30) COLLATE utf8_unicode_ci DEFAULT 'circu',
  user_email varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  user_alert_resamail int(1) unsigned NOT NULL DEFAULT '0',
  user_alert_demandesmail int(1) unsigned NOT NULL DEFAULT '0',
  user_alert_subscribemail int(1) unsigned NOT NULL DEFAULT '0',
  deflt2docs_location int(6) unsigned NOT NULL DEFAULT '0',
  deflt_empr_statut bigint(20) unsigned NOT NULL DEFAULT '1',
  deflt_thesaurus int(3) unsigned NOT NULL DEFAULT '1',
  deflt_concept_scheme int(3) NOT NULL DEFAULT '-1',
  deflt_import_thesaurus int(11) NOT NULL DEFAULT '1',
  value_prefix_cote tinyblob NOT NULL,
  xmlta_doctype char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a',
  xmlta_doctype_serial varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  xmlta_doctype_bulletin varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  xmlta_doctype_analysis varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  speci_coordonnees_etab mediumtext COLLATE utf8_unicode_ci NOT NULL,
  value_email_bcc varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  value_deflt_antivol varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  explr_invisible text COLLATE utf8_unicode_ci,
  explr_visible_mod text COLLATE utf8_unicode_ci,
  explr_visible_unmod text COLLATE utf8_unicode_ci,
  deflt3bibli int(5) unsigned NOT NULL DEFAULT '0',
  deflt3exercice int(8) unsigned NOT NULL DEFAULT '0',
  deflt3rubrique int(8) unsigned NOT NULL DEFAULT '0',
  deflt3dev_statut int(3) NOT NULL DEFAULT '-1',
  deflt3cde_statut int(3) NOT NULL DEFAULT '-1',
  deflt3liv_statut int(3) NOT NULL DEFAULT '-1',
  deflt3fac_statut int(3) NOT NULL DEFAULT '-1',
  deflt3sug_statut int(3) NOT NULL DEFAULT '-1',
  environnement mediumblob NOT NULL,
  param_allloc int(1) unsigned NOT NULL DEFAULT '0',
  grp_num int(10) unsigned DEFAULT '0',
  deflt_arch_statut int(6) unsigned NOT NULL DEFAULT '0',
  deflt_arch_emplacement int(6) unsigned NOT NULL DEFAULT '0',
  deflt_arch_type int(6) unsigned NOT NULL DEFAULT '0',
  deflt_upload_repertoire int(8) NOT NULL DEFAULT '0',
  deflt3lgstatdev int(3) NOT NULL DEFAULT '1',
  deflt3lgstatcde int(3) NOT NULL DEFAULT '1',
  deflt3receptsugstat int(3) NOT NULL DEFAULT '32',
  deflt_short_loan_activate int(1) unsigned NOT NULL DEFAULT '0',
  deflt_cashdesk int(11) NOT NULL DEFAULT '0',
  deflt_explnum_statut int(6) unsigned NOT NULL DEFAULT '1',
  user_alert_suggmail int(1) unsigned NOT NULL DEFAULT '0',
  deflt_notice_replace_keep_categories int(1) NOT NULL DEFAULT '0',
  deflt_notice_is_new int(1) unsigned NOT NULL DEFAULT '0',
  deflt_agnostic_warehouse int(6) unsigned NOT NULL DEFAULT '0',
  deflt_cms_article_statut int(6) unsigned NOT NULL DEFAULT '0',
  deflt_cms_article_type int(6) unsigned NOT NULL DEFAULT '0',
  deflt_cms_section_type int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (userid)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_groups`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE users_groups (
  grp_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  grp_name varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (grp_id),
  KEY i_users_groups_grp_name (grp_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vedette`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE vedette (
  id_vedette int(11) unsigned NOT NULL AUTO_INCREMENT,
  label varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  grammar varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'rameau',
  PRIMARY KEY (id_vedette)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vedette_link`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE vedette_link (
  num_vedette int(10) unsigned NOT NULL,
  num_object int(10) unsigned NOT NULL,
  type_object int(10) unsigned NOT NULL,
  PRIMARY KEY (num_vedette,num_object,type_object)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vedette_object`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE vedette_object (
  object_type int(3) unsigned NOT NULL DEFAULT '0',
  object_id int(11) unsigned NOT NULL DEFAULT '0',
  num_vedette int(11) unsigned NOT NULL DEFAULT '0',
  subdivision varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  position int(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (object_type,object_id,num_vedette,subdivision,position),
  KEY i_vedette_object_object (object_type,object_id),
  KEY i_vedette_object_vedette (num_vedette)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visionneuse_params`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE visionneuse_params (
  visionneuse_params_id int(11) NOT NULL AUTO_INCREMENT,
  visionneuse_params_class varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  visionneuse_params_parameters text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (visionneuse_params_id),
  UNIQUE KEY visionneuse_params_class (visionneuse_params_class)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voir_aussi`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE voir_aussi (
  num_noeud_orig int(9) unsigned NOT NULL DEFAULT '0',
  num_noeud_dest int(9) unsigned NOT NULL DEFAULT '0',
  langue varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  comment_voir_aussi text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (num_noeud_orig,num_noeud_dest,langue),
  KEY num_noeud_dest (num_noeud_dest)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE words (
  id_word int(10) unsigned NOT NULL AUTO_INCREMENT,
  word varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  double_metaphone varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  stem varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_word),
  UNIQUE KEY i_word_lang (word,lang),
  KEY i_stem_lang (stem,lang)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_attr`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE z_attr (
  attr_bib_id int(6) unsigned NOT NULL DEFAULT '0',
  attr_libelle varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  attr_attr varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (attr_bib_id,attr_libelle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_bib`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE z_bib (
  bib_id int(6) unsigned NOT NULL AUTO_INCREMENT,
  bib_nom varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  search_type varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  url varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  base varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  format varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  auth_user varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  auth_pass varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  sutrs_lang varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  fichier_func varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (bib_id)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_notices`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE z_notices (
  znotices_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  znotices_query_id int(11) DEFAULT NULL,
  znotices_bib_id int(6) unsigned DEFAULT '0',
  isbd text COLLATE utf8_unicode_ci,
  isbn varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  titre varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  auteur varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  z_marc longblob NOT NULL,
  PRIMARY KEY (znotices_id),
  KEY idx_z_notices_idq (znotices_query_id),
  KEY idx_z_notices_isbn (isbn),
  KEY idx_z_notices_titre (titre),
  KEY idx_z_notices_auteur (auteur)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_query`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE z_query (
  zquery_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  search_attr varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  zquery_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (zquery_id),
  KEY zquery_date (zquery_date)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-29 11:56:47
