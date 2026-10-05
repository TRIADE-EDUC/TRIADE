<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_bdp.inc.php,v 1.31 2024/01/08 13:17:23 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	global $info_896;
	$info_896 = array();
	$record = new iso2709_record($notice, AUTO_UPDATE);
	$info_896 = $record->get_subfield_array("896", 'a');
}

function import_new_notice_suite() {
	global $notice_id ;

	global $index_sujets ;
	global $pmb_keyword_sep ;
	global $info_896;

	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;

	$mots_cles .= import_records::get_mots_cles();

	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';
	/* Traitement de la vignette */
	$thumbnail_url = "";
	if(! empty($info_896[0])) {
		$thumbnail_url = $info_896[0];
	}
	$rqt_maj = "UPDATE notices SET index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ', thumbnail_url='".addslashes($thumbnail_url)."' WHERE notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
} // fin import_new_notice_suite

// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('bdp');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}