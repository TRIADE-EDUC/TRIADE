<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_bdp_pcdm3.inc.php,v 1.9 2021/11/09 13:58:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id ;
	
	global $index_sujets ;
	global $pmb_keyword_sep ;
	
	global $info_686;
	global $dewey;

	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;
	
	$mots_cles .= import_records::get_mots_cles();

	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';

	$pcdm3 = "";
	if (!$dewey[0] && $info_686[0]) $pcdm3 = indexint::import(clean_string('M '.$info_686[0]["a"]));

	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' ";
	if ($pcdm3) $rqt_maj.= ", indexint='".$pcdm3."' ";
	$rqt_maj.= "where notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('bdp_pcdm3');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}	