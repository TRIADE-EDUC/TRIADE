<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_ensai_ensae.inc.php,v 1.9 2021/12/09 14:22:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	global $info_956;
	
	$record = new iso2709_record($notice, AUTO_UPDATE);
	$info_956=$record->get_subfield("956","u","3","z");

	global $add_explnum;
	$add_explnum=TRUE;


} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus

	
function import_new_notice_suite() {
	global $notice_id ;
	
	global $index_sujets ;
	global $pmb_keyword_sep ;
	
	global $info_956;
	
	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;
	
	$mots_cles .= import_records::get_mots_cles();
	
	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
	if ($info_956[0]['u']) {
		if (!$info_956[0]['z']) $info_956[0]['z']=$info_956[0]['u']; 
		$rqt_maj = "insert into explnum set explnum_notice='$notice_id', explnum_nom='".addslashes($info_956[0]['z'])."', 
		explnum_mimetype='URL', explnum_url='".addslashes($info_956[0]['u'])."'";
		pmb_mysql_query($rqt_maj);
	}
	
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('ensai_ensae');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}

//TRAITEMENT DES DOCS NUMERIQUES SUR NOTICE EXISTANTE
function ajoute_explnum () {
	global $notice_id;
	global $info_956;

	if ($info_956[0]['u']) {
		if (!$info_956[0]['z']) $info_956[0]['z']=$info_956[0]['u']; 
		
		$q = "select count(*) from explnum where explnum_notice='$notice_id' and explnum_url='".addslashes($info_956[0]['u'])."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r,0,0)==0){
			$rqt_maj = "insert into explnum set explnum_notice='$notice_id', explnum_nom='".addslashes($info_956[0]['z'])."', 
			explnum_mimetype='URL', explnum_url='".addslashes($info_956[0]['u'])."'";
			pmb_mysql_query($rqt_maj);
		}
	}
	
}