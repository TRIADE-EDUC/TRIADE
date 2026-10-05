<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authors.inc.php,v 1.8 2022/01/04 09:23:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// récupération des responsabilités d'une notice

// get_notice_authors : retourne un tableau avec les responsabilités d'une notice donnée
function get_notice_authors($notice=0) {
	$responsabilites = array() ;
	$auteurs = array() ;
	
	$res = array();
	$res["responsabilites"] = array() ;
	$res["auteurs"] = array() ;
	
	$rqt = "select author_id, responsability_fonction, responsability_type ";
	$rqt.= "from responsability, authors where responsability_notice='$notice' and responsability_author=author_id order by responsability_type, responsability_ordre " ;

	$res_sql = pmb_mysql_query($rqt);
	while ($notice=pmb_mysql_fetch_object($res_sql)) {
		$responsabilites[] = $notice->responsability_type ;
		$auteurs[] = array( 
				'id' => $notice->author_id,
				'fonction' => $notice->responsability_fonction,
				'responsability' => $notice->responsability_type
				) ;
		}
	$res["responsabilites"] = $responsabilites ;
	$res["auteurs"] = $auteurs ;
	return $res;
}

// constitution du header de responsabilité
function gen_authors_header($responsabilites, $separator=',') {
	global $pmb_notice_author_functions_grouping;

	$author_list = array();
	$as = array_search ("0", $responsabilites["responsabilites"]);
	if ($as!== FALSE && $as!== NULL) {
		$auteur_0 = $responsabilites["auteurs"][$as] ;
		$auteur = new auteur($auteur_0["id"]);
		if ($auteur->get_isbd()){
			$author_list[] = $auteur->get_isbd();
		}
	}else {
		$as = array_keys ($responsabilites["responsabilites"], "1" );
		for ($i = 0 ; $i < count($as) ; $i++) {
			$auteur_1 = $responsabilites["auteurs"][$as[$i]] ;
			$auteur = new auteur($auteur_1["id"]);;
			if ($auteur->get_isbd()){
				$author_list[] = $auteur->get_isbd();
			}
		}
	}

	if($pmb_notice_author_functions_grouping) {
		$author_list = array_unique($author_list);
	}
	return  implode ($separator.' ', $author_list);

}
