<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: includes_etagere.inc.php,v 1.24 2022/05/05 06:44:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path;

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path."/includes/templates/common.tpl.php");

// classe de gestion des catégories
require_once($base_path."/classes/categorie.class.php");
require_once($base_path."/classes/notice.class.php");
require_once($base_path."/classes/notice_display.class.php");

// classe indexation interne
require_once($base_path."/classes/indexint.class.php");

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/common.tpl.php");
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");

require_once($base_path."/includes/notice_affichage.inc.php");

// pour les étagères et les nouveaux affichages
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/classes/notice_affichage.class.php");
require_once($base_path."/includes/etagere_func.inc.php");
require_once($base_path."/includes/templates/etagere.tpl.php");

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");

// print $etageres_header;

// pour affichage de liens sur les éléments affichés :
/*
$liens_opac['lien_rech_notice'] 		= "./index.php?lvl=notice_display&id=!!id!!";
$liens_opac['lien_rech_auteur'] 		= "./index.php?lvl=author_see&id=!!id!!";
$liens_opac['lien_rech_editeur'] 		= "./index.php?lvl=publisher_see&id=!!id!!";
$liens_opac['lien_rech_serie'] 			= "./index.php?lvl=serie_see&id=!!id!!";
$liens_opac['lien_rech_collection'] 	= "./index.php?lvl=coll_see&id=!!id!!";
$liens_opac['lien_rech_subcollection'] 	= "./index.php?lvl=subcoll_see&id=!!id!!";
$liens_opac['lien_rech_indexint'] 		= "./index.php?lvl=indexint_see&id=!!id!!";
$liens_opac['lien_rech_motcle'] 		= "./index.php?lvl=more_results&mode=keyword&user_query=!!mot!!";
$liens_opac['lien_rech_categ'] 			= "./index.php?lvl=categ_see&id=!!id!!";
$liens_opac['lien_rech_perio'] 			= "./index.php?lvl=notice_display&id=!!id!!";
$liens_opac['lien_rech_bulletin'] 		= "./index.php?lvl=bulletin_display&id=!!id!!";
$liens_opac['lien_rech_concept'] 		= "./index.php?lvl=concept_see&id=!!id!!";
$liens_opac['lien_rech_authperso'] 		= "./index.php?lvl=authperso_see&id=!!id!!";
*/


// paramètres :
//	$accueil : filtres les étagères de l'accueil uniquement si 1
//	$etageres : les numéros des étagères séparés par les ',' toutes si vide
//	$aff_notices_nb : nombres de notices affichées : toutes = 0 
//	$mode_aff_notice : mode d'affichage des notices, REDUIT (titre+auteur principal) ou ISBD ou PMB ou les deux : dans ce cas : (titre + auteur) en entête du truc, à faire dans notice_display.class.php
//	$depliable : affichage des notices une par ligne avec le bouton de dépliable
//	$link_to_etagere : lien pour afficher le contenu de l'étagère
//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction
//	$liens_opac : tableau contenant les url destinatrices des liens si voulu 
// function affiche_etagere($accueil=0, $etageres="", $aff_commentaire=0, $aff_notices_nb=0, $mode_aff_notice=AFF_ETA_NOTICES_BOTH, $depliable=AFF_ETA_NOTICES_DEPLIABLES_OUI, $link_to_etagere="", $htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="", $liens_opac=array() ) {

// print affiche_etagere (1, "$id", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./fonction_etagere.php?lvl=etagere_see&id=!!id!!" , $liens_opac) ;
 
// print $etageres_footer;
	
//pmb_mysql_close();

