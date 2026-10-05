<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authors.inc.php,v 1.18 2022/01/03 10:35:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// récupération des responsabilités d'une notice
global $class_path;
require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");

global $fonction_auteur;
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// get_notice_authors : retourne un tableau avec les responsabilités d'une notice donnée
function get_notice_authors($notice=0) {
	$responsabilites = array();
	$auteurs = array();
	
	$res = array();
	$res['responsabilites'] = array();
	$res['auteurs'] = array();
	
	$rqt = 'select id_responsability, author_id, responsability_fonction, responsability_type, responsability_ordre from responsability, authors where responsability_notice="'.$notice.'" and responsability_author=author_id order by responsability_type, responsability_ordre ' ;

	$res_sql = pmb_mysql_query($rqt);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
		$responsabilites[] = $notice->responsability_type ;
		$auteurs[] = array( 
			'id' => $notice->author_id,
			'fonction' => $notice->responsability_fonction,
			'responsability' => $notice->responsability_type,
			'id_responsability' => $notice->id_responsability,
			'order' => $notice->responsability_ordre
			) ;
		}
	$res['responsabilites'] = $responsabilites;
	$res['auteurs'] = $auteurs;
	return $res;
}

// constitution du header de responsabilité
function gen_authors_header($responsabilites, $separator=',') {
	global $pmb_notice_author_functions_grouping;

	$author_list = array();
	$as = array_search ("0", $responsabilites["responsabilites"]);
	if ($as!== FALSE && $as!== NULL) {
		$auteur_0 = $responsabilites["auteurs"][$as] ;
		$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$auteur_0["id"]);
		if ($auteur->get_isbd()){
			$author_list[] = $auteur->get_isbd();
		}
	}else {
		//On ne prend que le premier
		$as = array_keys ($responsabilites["responsabilites"], "1" );
		if(count($as)){
			$auteur_1 = $responsabilites["auteurs"][$as[0]] ;
			$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$auteur_1["id"]);
			if ($auteur->isbd_entry){
				$author_list[] = $auteur->isbd_entry;
			}	
		}
	}
	
	if($pmb_notice_author_functions_grouping) {
		$author_list = array_unique($author_list);
	} 
	return  implode ($separator.' ', $author_list);
	
}

// constitution de la mention de responsabilité
function gen_authors_isbd($responsabilites, $print_mode=0) {
    global $fonction_auteur, $pmb_notice_author_functions_grouping;
    global $pmb_authors_qualification;

	$libelle_mention_resp = '';
	$mention_resp = array() ;
	$author_list_functions = array();
	$as = array_search ("0", $responsabilites["responsabilites"]);
	if ($as!== FALSE && $as!== NULL) {
		$auteur_0 = $responsabilites["auteurs"][$as];
		$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$auteur_0["id"]);
		$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_0["id"], 'type_object' => AUT_TABLE_AUTHORS]);
		
		if ($print_mode) {
		    $resp_lib = $authority_instance->get_isbd();
		}else {
		    $resp_lib = $authority_instance->build_isbd_entry_lien_gestion();
			if($auteur->author_web_link) {
				$resp_lib.= ' '.$auteur->author_web_link;
			}
		}
		$qualification = '';
		if ($pmb_authors_qualification) {		    
		    $qualif_id = vedette_composee::get_vedette_id_from_object($auteur_0["id_responsability"], TYPE_NOTICE_RESPONSABILITY_PRINCIPAL);
		    if($qualif_id){
		        $qualif = new vedette_composee($qualif_id);
		        $qualification = ' (' . $qualif->get_label() .')';
		    }
		}
		if ($auteur_0["fonction"] && !empty($fonction_auteur[$auteur_0["fonction"]])) {
		    $author_list_functions[$resp_lib][] = $fonction_auteur[$auteur_0["fonction"]] . $qualification;
			$mention_resp[] = $resp_lib.", ".$fonction_auteur[$auteur_0["fonction"]] . $qualification;
		}else {
			$author_list_functions[$resp_lib][] = '';
			$mention_resp[] = $resp_lib . $qualification;
		}
	}
	$as = array_keys ($responsabilites["responsabilites"], "1");
	for ($i = 0 ; $i < count($as) ; $i++) {
		$auteur_1 = $responsabilites["auteurs"][$as[$i]];
		$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$auteur_1["id"]);
		$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_1["id"], 'type_object' => AUT_TABLE_AUTHORS]);
		
		if ($print_mode) {
		    $resp_lib = $authority_instance->get_isbd();
		}else {
		    $resp_lib = $authority_instance->build_isbd_entry_lien_gestion();
			if($auteur->author_web_link) {
				$resp_lib.= ' '.$auteur->author_web_link;
			}
		}
		$qualification = '';
		if ($pmb_authors_qualification) {
		    $qualif_id = vedette_composee::get_vedette_id_from_object($auteur_1["id_responsability"], TYPE_NOTICE_RESPONSABILITY_AUTRE);
		    if($qualif_id){
		        $qualif = new vedette_composee($qualif_id);
		        $qualification = ' (' . $qualif->get_label() .')';
		    }
		}
		if ($auteur_1["fonction"] && !empty($fonction_auteur[$auteur_1["fonction"]])) {
		    $author_list_functions[$resp_lib][] = $fonction_auteur[$auteur_1["fonction"]] . $qualification;
		    $mention_resp[] = $resp_lib.", ".$fonction_auteur[$auteur_1["fonction"]] . $qualification;
		}else {
			$author_list_functions[$resp_lib][] = '';
			$mention_resp[] = $resp_lib . $qualification;
		}
	}
	$as = array_keys ($responsabilites["responsabilites"], "2");
	for ($i = 0 ; $i < count($as) ; $i++) {
		$auteur_2 = $responsabilites["auteurs"][$as[$i]];
		$auteur = authorities_collection::get_authority(AUT_TABLE_AUTHORS,$auteur_2["id"]);		
		$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_2["id"], 'type_object' => AUT_TABLE_AUTHORS]);
		
		if ($print_mode) {
		    $resp_lib = $authority_instance->get_isbd();
		}else {
		    $resp_lib = $authority_instance->build_isbd_entry_lien_gestion();
			if($auteur->author_web_link) {
				$resp_lib.= ' '.$auteur->author_web_link;
			}
		}
		$qualification = '';
		if ($pmb_authors_qualification) {
		    $qualif_id = vedette_composee::get_vedette_id_from_object($auteur_2["id_responsability"], TYPE_NOTICE_RESPONSABILITY_SECONDAIRE);
		    if($qualif_id){
		        $qualif = new vedette_composee($qualif_id);
		        $qualification = ' (' . $qualif->get_label() .')';
		    }
		}
		if ($auteur_2["fonction"] && !empty($fonction_auteur[$auteur_2["fonction"]])) {
		    $author_list_functions[$resp_lib][] = $fonction_auteur[$auteur_2["fonction"]] . $qualification;
			$mention_resp[] = $resp_lib.", ".$fonction_auteur[$auteur_2["fonction"]] . $qualification;
		}else {
			$author_list_functions[$resp_lib][] = '';
			$mention_resp[] = $resp_lib . $qualification;
		}
	}
	
	if($pmb_notice_author_functions_grouping) {
		foreach ($author_list_functions as $isbd => $function_list) {
			if($libelle_mention_resp)$libelle_mention_resp.= '; ';
			$libelle_mention_resp.= $isbd;
			if(count($function_list)) {
				foreach ($function_list as $function) {
					if($function) {
						$libelle_mention_resp.= ', '.$function;
					}
				}
			}
		}
		return $libelle_mention_resp;
	}
    return implode ("; ",$mention_resp);
}