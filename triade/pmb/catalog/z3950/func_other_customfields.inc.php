<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_other_customfields.inc.php,v 1.7 2017-12-04 10:33:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

include_once $base_path.'/admin/import/lib_func_customfields.inc.php';

function z_recup_noticeunimarc_suite($notice) {
	func_customfields_recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	func_customfields_import_new_notice_suite();
}

// Permet de mémoriser la valeur d'un import extern pour ensuite l'intégré dans un champ perso de la notice avec param_perso_form
function param_perso_prepare($record) {
	global $param_perso_900;
	
	$param_perso_900=$record->get_subfield("900","a","l","n");
	
}

function param_perso_form(&$p_perso) {
	global $dbh;
	global $param_perso_900;

	for($i=0;$i<count($param_perso_900);$i++){
	
		$req = " select idchamp, type, datatype from notices_custom where name='".$param_perso_900[$i]['n']."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$perso = pmb_mysql_fetch_object($res);

			if($perso->idchamp){
				if($perso->type == 'list'){
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($param_perso_900[$i]['a'])."' and notices_custom_champ=$perso->idchamp";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=$perso->idchamp";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($param_perso_900[$i]['a'])."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$p_perso->values[$perso->idchamp][]=$value;
				} elseif($perso->type == 'date_box'){
					$p_perso->values[$perso->idchamp][]=dateFrToMysql($param_perso_900[$i]['a']);
				} else {
					$p_perso->values[$perso->idchamp][]=$param_perso_900[$i]['a'];
				}
			}
		}
	}
}

function dateFrToMysql($value){
	if(preg_match('`^(\d{2})\/(\d{2})\/(\d{4})$`',$value,$out)){
		return $out[3]."-".$out[2]."-".$out[1];
	}else{
		return $value;
	}
}

// enregistrement de la notices dans les catégories
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {

	global $dbh;
	
	// si $thesaurus_traite fourni, on ne delete que les catégories de ce thesaurus, sinon on efface toutes
	//  les indexations de la notice sans distinction de thesaurus
	if (!$thesaurus_traite) $rqt_del = "delete from notices_categories where notcateg_notice='$notice_retour' ";
	else $rqt_del = "delete from notices_categories where notcateg_notice='$notice_retour' and num_noeud in (select id_noeud from noeuds where num_thesaurus='$thesaurus_traite' and id_noeud=notices_categories.num_noeud) ";
	$res_del = @pmb_mysql_query($rqt_del, $dbh);
	
	$rqt_ins = "insert into notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
	
	for($i=0 ; $i< sizeof($categories) ; $i++) {
		$id_categ=$categories[$i]['categ_id'];
		if ($id_categ) {
			$rqt = $rqt_ins . " ('$notice_retour','$id_categ', $i) " ; 
			$res_ins = @pmb_mysql_query($rqt, $dbh);
		}
	}
}

function traite_categories_for_form($tableau_600="",$tableau_601="",$tableau_602="",$tableau_605="",$tableau_606="",$tableau_607="",$tableau_608="") {
	global $charset, $msg, $pmb_keyword_sep, $rameau;
	$rameau = "" ;
	$info_606_a = $tableau_606["info_606_a"] ;
	$info_606_j = $tableau_606["info_606_j"] ;
	$info_606_x = $tableau_606["info_606_x"] ;
	$info_606_y = $tableau_606["info_606_y"] ;
	$info_606_z = $tableau_606["info_606_z"] ;
	
	$champ_rameau="";
	for ($a=0; $a<sizeof($info_606_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<sizeof($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<sizeof($info_606_x[$a]); $j++) {
			$libelle_final .= " : ".trim($info_606_x[$a][$j]) ;
		}
		for ($j=0; $j<sizeof($info_606_y[$a]); $j++) {
			$libelle_final .= " : ".trim($info_606_y[$a][$j]) ;
		}
		for ($j=0; $j<sizeof($info_606_z[$a]); $j++) {
			$libelle_final .= " : ".trim($info_606_z[$a][$j]) ;
		}
		if ($champ_rameau) $champ_rameau.=" @@@ ";
		$champ_rameau.=$libelle_final;
	} 
	
	return array(
		"form" => "",
		"message" => htmlentities($msg['traite_categ_ignore'].$champ_rameau,ENT_QUOTES,$charset)
	);
}


function traite_categories_from_form() {
	global $max_categ ;
	$categories = array () ;
	for ($i=0; $i< $max_categ ; $i++) {
		$var_categ = "f_categ_id$i" ;
		global ${$var_categ} ;
		if (${$var_categ}) 
			$categories[] = array('categ_id' => ${$var_categ} );
		}
	return $categories ;
}

function traite_concepts_for_form($tableau_606 = array()) {
	global $charset, $msg, $pmb_keyword_sep, $rameau;
	$rameau = "" ;
	$info_606_a = $tableau_606["info_606_a"] ;
	$info_606_j = $tableau_606["info_606_j"] ;
	$info_606_x = $tableau_606["info_606_x"] ;
	$info_606_y = $tableau_606["info_606_y"] ;
	$info_606_z = $tableau_606["info_606_z"] ;

	$champ_rameau="";
	for ($a=0; $a<sizeof($info_606_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<sizeof($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
			else $libelle_j .= " -- ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." -- ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<sizeof($info_606_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_x[$a][$j]) ;
		}
		for ($j=0; $j<sizeof($info_606_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_y[$a][$j]) ;
		}
		for ($j=0; $j<sizeof($info_606_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_z[$a][$j]) ;
		}
		if ($champ_rameau) $champ_rameau.=" @@@ ";
		$champ_rameau.=$libelle_final;
	}

	return array(
			"form" => "",
			"message" => htmlentities($champ_rameau,ENT_QUOTES,$charset)
	);
}
