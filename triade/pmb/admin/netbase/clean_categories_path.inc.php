<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: clean_categories_path.inc.php,v 1.8 2018-07-18 09:10:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/thesaurus.class.php");
require_once("$class_path/noeuds.class.php");
require_once("$class_path/categories.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// initialisation de la borne de départ
if (empty($start)) {
	$start=0;
}

$v_state=urldecode($v_state);

if (!isset($count) || !$count) {
	$categories = pmb_mysql_query("SELECT count(1) FROM categories", $dbh);
	$count = pmb_mysql_result($categories, 0, 0);
}

print "<br /><br /><h2 class='center'>".htmlentities($msg["clean_categories_path"], ENT_QUOTES, $charset)."</h2>";

if(!$start) {
	// Pour tous les thésaurus, on parcours les childs
	$list_thesaurus = thesaurus::getThesaurusList();
	
	foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
		$thes = new thesaurus($id_thesaurus);
		$noeud_rac =  $thes->num_noeud_racine;
		$r = noeuds::listChilds($noeud_rac, 0);
		while(($row = pmb_mysql_fetch_object($r))){
			noeuds::process_categ_path($row->id_noeud);
		}
	}	
}
	
if($thesaurus_auto_postage_search){
	if($count >= $start) {
		print netbase::get_display_progress($start, $count);
		categories::process_categ_index($start, $lot);
		$next = $start + $lot;
		print netbase::get_current_state_form($v_state, $spec, 'CLEAN_CATEGORIES_PATH', $next, $count);
	}
}
if(!$thesaurus_auto_postage_search || (($start + $lot) > $count)) {
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();
	$v_state=urldecode($v_state);
	$v_state.= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["clean_categories_path_end"], ENT_QUOTES, $charset).".";
	$spec = $spec - CLEAN_CATEGORIES_PATH;
	print netbase::get_process_state_form($v_state, $spec);
}