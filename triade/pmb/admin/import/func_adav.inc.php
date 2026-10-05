<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_adav.inc.php,v 1.8 2021/12/09 14:22:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/explnum.class.php");
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	global $n_gen,$lien,$eformat,$info_856,$info_900,$info_902;
	
	$info_856=$info_900=$info_902=array();
	$record = new iso2709_record($notice, AUTO_UPDATE);
	
	$lien=$eformat=array();
	$info_856=$record->get_subfield("856","u");
	$info_900=$record->get_subfield("900","a");
	$info_902=$record->get_subfield("902","a");
	
} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id,$info_856,$info_900,$info_902,$msg;
	
	global $index_sujets ;
	global $pmb_keyword_sep ;
	
	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;
	
	$mots_cles .= import_records::get_mots_cles();
	
	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
	
	if($tmp=trim($info_856[0])){
		$rqt_maj = "update notices set thumbnail_url='".addslashes($tmp)."' where notice_id='".$notice_id."' " ;
		pmb_mysql_query($rqt_maj);
	}

	if($tmp=trim($info_900[0])){
		$explnum = new explnum(0,$notice_id);
		$explnum->explnum_url = $tmp;
		$explnum->explnum_nom = $msg["explnum_associate_docnum"];
		$explnum->explnum_mimetype = "URL";
	 	$explnum->save();
	}
	
	if($tmp=trim($info_902[0])){
		$explnum = new explnum(0,$notice_id);
		$explnum->explnum_url = $tmp;
		$explnum->explnum_nom = $msg["explnum_associate_docnum"];
		$explnum->explnum_mimetype = "URL";
		$explnum->save();
	}
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('adav');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}	