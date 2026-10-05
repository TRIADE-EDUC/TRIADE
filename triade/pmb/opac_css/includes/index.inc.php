<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index.inc.php,v 1.53 2023/08/03 10:32:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $search_type, $opac_show_infopages_id_top, $opac_show_search_title, $opac_show_infopages_id;
global $opac_show_public_bannettes, $opac_bannette_nb_liste;
global $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables;

// affichage recherche
require_once ($base_path.'/includes/simple_search.inc.php');
require_once ($class_path.'/search_view.class.php');

print common::format_hidden_title();

if ($search_type == "simple_search" && $opac_show_infopages_id_top) {
	// affichage des infopages demandés juste AVANT le formulaire de recherche simple et si !$user_query
	require_once ($base_path.'/includes/show_infopages.inc.php');
	print "<div id='infopages_top'>".show_infopages($opac_show_infopages_id_top)."</div>";
}

if ($opac_show_search_title) {
    print "<div id='search_block'>".common::format_title($msg['search_block_title']);
}
search_view::set_search_type($search_type);
search_view::set_user_query($user_query);
search_view::set_url_base($base_path.'/index.php?');
$display_search_tabs_form=search_view::get_display_search_tabs_form($user_query, $css);
$display_search_tabs_form=str_replace("!!surligne!!","",$display_search_tabs_form);
print $display_search_tabs_form;
if ($opac_show_search_title) print "</div>";

if ($search_type == "simple_search") {
	// affichage des infopages demandés juste après le formulaire de recherche simple et si !$user_query
	if ($opac_show_infopages_id) {
		require_once ($base_path.'/includes/show_infopages.inc.php');
		print "<div id='infopages'>".show_infopages($opac_show_infopages_id)."</div>";
	}
	// affichage des du navigateur de périodiques
	if ($opac_show_perio_browser) {
		require_once($base_path."/classes/perio_a2z.class.php");	
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);		
		print $perio_a2z=$a2z->get_form();		
	}	
	// affichage catégories
	if ($opac_show_categ_browser) {
		$opac_show_categ_browser_tab=explode(" ",$opac_show_categ_browser);
		if (!empty($opac_show_categ_browser_tab[1])) 
			$opac_show_categ_browser_home_id_thes=$opac_show_categ_browser_tab[1];
		require_once ($base_path.'/classes/categorie.class.php');
		require_once ($base_path.'/includes/templates/categories.tpl.php');
		require_once ($base_path.'/categ/categories.inc.php');
	}
	// affichage des bannettes auxquelles le lecteur est abonné
	if ($opac_show_subscribed_bannettes) {
		require_once($base_path."/includes/bannette_func.inc.php");		
		$affiche_bannette_tpl="
		<div class='bannette' id='banette_!!id_bannette!!'>
			!!diffusion!!
		</div>
		";
		$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!","bannettes_private-container_2", ""));
		if($aff){
			$bannettes= "<div id='bannettes_subscribed'>\n";
			$bannettes.= common::format_title($msg['accueil_bannette_privee']);
			$bannettes.= "<div id='bannettes_private-container'>";
			$bannettes.= $aff;
			$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
			$bannettes.= "</div><!-- fermeture #bannettes -->\n";
			print $bannettes;
		} 		
	}
	
	//affichage des bannettes publiques sélectionnées (et restantes si $opac_show_subscribed_bannettes est activé) pour la page d'accueil	
 	if ($opac_show_public_bannettes) {
 		require_once($base_path."/includes/bannette_func.inc.php");
 		$affiche_bannette_tpl="
		<div class='bannette' id='banette_!!id_bannette!!'>
 			!!diffusion!!
 		</div>";
 		$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./index.php?lvl=bannette_see&id_bannette=!!id_bannette!!","bannettes-public-container_2", "",true));
 		if($aff){
 			$bannettes= "<div id='bannettes_public'>\n";
 			$bannettes.= common::format_title($msg['accueil_bannette_public']);
 			$bannettes.= "<div id='bannettes_public-container'>";
 			$bannettes.= $aff;
 			$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
			$bannettes.= "</div><!-- fermeture #bannettes -->\n";
 			print $bannettes;
 		}
 	}

	if ($opac_show_section_browser==1) {
		if ($opac_sur_location_activate==1) require_once($base_path."/includes/enter_sur_location.inc.php");
		else require_once($base_path."/includes/enter_localisation.inc.php");
	}
	// affichage marguerite des couleurs
	if ($opac_show_marguerite_browser) {
	    require_once ($base_path.'/indexint/marguerite_browser.inc.php');
	}

	// affichage tableau des 100 cases du savoir
	if ($opac_show_100cases_browser) {
	    require_once ($base_path.'/indexint/100cases_browser.inc.php');
	}

	// affichage derniers ouvrages saisis
	if ($opac_show_dernieresnotices) {
		require_once ($base_path.'/includes/last_records.inc.php');
	}

	// affichage des étagères de l'accueil
	if ($opac_show_etageresaccueil) {
		require_once ($base_path.'/includes/templates/etagere.tpl.php');
		$aff_etagere = affiche_etagere(1, "", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./index.php?lvl=etagere_see&id=!!id!!", $liens_opac);
		if ($aff_etagere) {
		    print "<div id='etageres'>".common::format_title($msg['accueil_etageres_virtuelles']);
			print $aff_etagere ;
			print "</div>";
		}
	}

	// affichage des flux rss
	if ($opac_show_rss_browser) {
	    require_once ($base_path.'/includes/rss.inc.php');
	}

	//define( 'AFF_ETA_NOTICES_NON', 0 );
	//define( 'AFF_ETA_NOTICES_ISBD', 1 );
	//define( 'AFF_ETA_NOTICES_PMB', 2 );
	//define( 'AFF_ETA_NOTICES_BOTH', 4 );
	//define( 'AFF_ETA_NOTICES_REDUIT', 8 );
	//define( 'AFF_ETA_NOTICES_DEPLIABLES_NON', 0 );
	//define( 'AFF_ETA_NOTICES_DEPLIABLES_OUI', 1 );
	// paramètres :
	//	$accueil : filtres les étagères de l'accueil uniquement si 1
	//	$etageres : les numéros des étagères séparés par les ',' toutes si vides
	//	$aff_commentaire : affichage du commentaire associé à l'étagère
	//	$aff_notices_nb : nombres de notices affichées : toutes = 0 
	//	$mode_aff_notice : mode d'affichage des notices, REDUIT (titre+auteur principal) ou ISBD ou PMB ou les deux : dans ce cas : (titre + auteur) en entête du truc, à faire dans notice_display.class.php
	//	$depliable : affichage des notices une par ligne avec le bouton de dépliable
	//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction 
	//function affiche_etagere($accueil=0, $etageres="", $aff_commentaire=0, $aff_notices_nb=0, $mode_aff_notice=AFF_ETA_NOTICES_BOTH, $depliable=AFF_ETA_NOTICES_DEPLIABLES_OUI, $htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" ) {

}
