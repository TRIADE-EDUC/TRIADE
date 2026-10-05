<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_gam.inc.php,v 1.7 2021/12/09 14:22:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once ($class_path."/import/import_expl_bdp.class.php");

function recup_noticeunimarc_suite($notice) {
	global $n_gen,$lien,$eformat,$info_856,$info_959;
	
	$info_856=array();
	$info_959=array();
	$record = new iso2709_record($notice, AUTO_UPDATE);
	
	$lien=$eformat=array();
	$info_856=$record->get_subfield("856","u","z");
	$info_959=$record->get_subfield("959","3");
	
	$info_334=array();
	$info_334=$record->get_subfield("334","a","b");
	
	foreach ( $info_334 as $key => $value ) {
       if($tmp=trim($value["a"])){
       		$val="Note sur la récompense : ".$tmp;
       		if($tmp=trim($value["b"])){
       			$val.=", ".$tmp;
       		}
       		$n_gen[]=$val;
       }
	}
	
} // fin recup_noticeunimarc_suite = fin récupération des variables propres BDP : rien de plus
	
function import_new_notice_suite() {
	global $notice_id,$info_856,$info_959 ;
	
	global $index_sujets ;
	global $pmb_keyword_sep ;
	
	if (is_array($index_sujets)) $mots_cles = implode (" $pmb_keyword_sep ",$index_sujets);
		else $mots_cles = $index_sujets;
	
	$mots_cles .= import_records::get_mots_cles();
	
	$mots_cles ? $index_matieres = strip_empty_words($mots_cles) : $index_matieres = '';
	$rqt_maj = "update notices set index_l='".addslashes($mots_cles)."', index_matieres=' ".addslashes($index_matieres)." ' where notice_id='$notice_id' " ;
	pmb_mysql_query($rqt_maj);
	
	//Url image
	if($tmp=trim($info_959[0])){
		$requete="UPDATE notices SET thumbnail_url='".addslashes($tmp)."' WHERE notice_id='".$notice_id."'";
		if(!pmb_mysql_query($requete)){
			affiche_mes_erreurs("requete echoué : ".$requete);
		}
	}
	
	//Doc numérique Gam
	if(count($info_856)){
		foreach ( $info_856 as $value ) {
	       if($tmp=trim($value["u"])){
	       		$libelle=trim($value["z"]);
	       		if(!$libelle){
	       			$libelle=$tmp;
	       		}
				$rqt_maj = "insert into explnum set explnum_notice='$notice_id', explnum_nom='".addslashes($libelle)."', explnum_mimetype='URL', explnum_url='".addslashes($tmp)."'";
				if(!pmb_mysql_query($rqt_maj)){
					affiche_mes_erreurs("requete echoué : ".$rqt_maj);
				}
			}
		}
	}
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function traite_exemplaires () {
	import_expl_bdp::traite_exemplaires('agam');
} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI

// fonction spécifique d'export de la zone 995
function export_traite_exemplaires ($ex=array()) {
	return import_expl::export_traite_exemplaires($ex);
}