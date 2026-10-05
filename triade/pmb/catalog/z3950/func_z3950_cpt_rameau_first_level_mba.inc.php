<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_z3950_cpt_rameau_first_level_mba.inc.php,v 1.12 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
global $thesaurus_defaut, $thes;

// enregistrement de la notices dans les catégories
require_once "$include_path/misc.inc.php" ;
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");

//Attention, dans le multithesaurus, le thesaurus dans lequel on importe est le thesaurus par defaut
$thes = new thesaurus($thesaurus_defaut);
 
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}

function traite_categories_for_form($tableau_600 = array(), $tableau_601 = array(), $tableau_602 = array(), $tableau_605 = array(), $tableau_606 = array(), $tableau_607 = array(), $tableau_608 = array()) {
	global $charset, $rameau,$pmb_keyword_sep;
	$champ_rameau="";
	if(!$pmb_keyword_sep){
		$pmb_keyword_sep = " ; ";
	}
	
	$info_600_a = $tableau_600["info_600_a"] ;
	$info_600_3 = $tableau_600["info_600_3"] ;	
	$info_600_b = $tableau_600["info_600_b"] ;
	$info_600_f = $tableau_600["info_600_f"] ;
	$info_600_j = $tableau_600["info_600_j"] ;
	$info_600_x = $tableau_600["info_600_x"] ;
	$info_600_y = $tableau_600["info_600_y"] ;
	$info_600_z = $tableau_600["info_600_z"] ;
	for ($a=0; $a<count($info_600_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_600_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_600_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_600_j[$a][$j]) ;
		}
		if($info_600_b[$a][0]) $libelle_final.=" ".trim($info_600_b[$a][0]);
		if($info_600_f[$a][0]) $libelle_final.=" (".trim($info_600_f[$a][0]).")";
		
		if (!$libelle_j) $libelle_final = trim($info_600_a[$a][0]).$libelle_final ; else $libelle_final = trim($info_600_a[$a][0]).$libelle_final." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_600_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_600_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_600_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_600_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_600_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_600_z[$a][$j]) ;
		}
		//if($info_600_3[$a][0])$libelle_final.=" @@3 ".trim($info_600_3[$a][0]);		
		if ($champ_rameau) $champ_rameau.=$pmb_keyword_sep; 
		$champ_rameau.=$libelle_final;
	} 		
	
	$info_601_a = $tableau_601["info_601_a"] ;
	$info_601_3 = $tableau_601["info_601_3"] ;	
	$info_601_j = $tableau_601["info_601_j"] ;
	$info_601_x = $tableau_601["info_601_x"] ;
	$info_601_y = $tableau_601["info_601_y"] ;
	$info_601_z = $tableau_601["info_601_z"] ;
	for ($a=0; $a<count($info_601_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_601_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_601_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_601_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_601_a[$a][0]) ; else $libelle_final = trim($info_601_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_601_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_601_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_601_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_601_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_601_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_601_z[$a][$j]) ;
		}
		//if($info_601_3[$a][0])$libelle_final.=" @@3 ".trim($info_601_3[$a][0]);
		if ($champ_rameau) $champ_rameau.=$pmb_keyword_sep;
		$champ_rameau.=$libelle_final;
	} 		
	
	$info_606_a = $tableau_606["info_606_a"] ;
	$info_606_3 = $tableau_606["info_606_3"] ;	
	$info_606_j = $tableau_606["info_606_j"] ;
	$info_606_x = $tableau_606["info_606_x"] ;
	$info_606_y = $tableau_606["info_606_y"] ;
	$info_606_z = $tableau_606["info_606_z"] ;
	for ($a=0; $a<count($info_606_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_606_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_606_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_606_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_606_a[$a][0]) ; else $libelle_final = trim($info_606_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_606_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_606_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_606_z[$a][$j]) ;
		}
		//if($info_606_3[$a][0])$libelle_final.=" @@3 ".trim($info_606_3[$a][0]);
		if ($champ_rameau) $champ_rameau.=$pmb_keyword_sep;
		$champ_rameau.=$libelle_final;
	} 

	$info_607_a = $tableau_607["info_607_a"] ;
	$info_607_3 = $tableau_607["info_607_3"] ;
	$info_607_j = $tableau_607["info_607_j"] ;
	$info_607_x = $tableau_607["info_607_x"] ;
	$info_607_y = $tableau_607["info_607_y"] ;
	$info_607_z = $tableau_607["info_607_z"] ;
	for ($a=0; $a<count($info_607_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_607_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_607_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_607_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_607_a[$a][0]) ; else $libelle_final = trim($info_607_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_607_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_607_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_607_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_607_z[$a][$j]) ;
		}
		//if($info_607_3[$a][0])$libelle_final.=" @@3 ".trim($info_607_3[$a][0]);
		if ($champ_rameau) $champ_rameau.=$pmb_keyword_sep;
		$champ_rameau.=$libelle_final;
	} 
	
	$info_608_a = $tableau_608["info_608_a"] ;
	$info_608_3 = $tableau_608["info_608_3"] ;
	$info_608_j = $tableau_608["info_608_j"] ;
	$info_608_x = $tableau_608["info_608_x"] ;
	$info_608_y = $tableau_608["info_608_y"] ;
	$info_608_z = $tableau_608["info_608_z"] ;
	for ($a=0; $a<count($info_608_a); $a++) {
		$libelle_final="";
		$libelle_j="";
		for ($j=0; $j<count($info_608_j[$a]); $j++) {
			if (!$libelle_j) $libelle_j .= trim($info_608_j[$a][$j]) ;
				else $libelle_j .= " ** ".trim($info_608_j[$a][$j]) ;
		}
		if (!$libelle_j) $libelle_final = trim($info_608_a[$a][0]) ; else $libelle_final = trim($info_608_a[$a][0])." ** ".$libelle_j ;
		if (!$libelle_final) break ;
		for ($j=0; $j<count($info_608_x[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_608_x[$a][$j]) ;
		}
		for ($j=0; $j<count($info_608_y[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_608_y[$a][$j]) ;
		}
		for ($j=0; $j<count($info_608_z[$a]); $j++) {
			$libelle_final .= " -- ".trim($info_608_z[$a][$j]) ;
		}
	//	if($info_608_3[$a][0])$libelle_final.=" @@3 ".trim($info_608_3[$a][0]);
		if ($champ_rameau) $champ_rameau.=$pmb_keyword_sep;
		$champ_rameau.=$libelle_final;
	} 	
	
	// $rameau est la variable traitée par la fonction traite_categories_from_form, 
	// $rameau est normalement POSTée, afin de pouvoir être traitée en lot, donc hors 
	// formulaire, il faut l'affecter.
	$rameau = addslashes($champ_rameau) ;

	return array(
		"form" => "<input type='hidden' name='rameau' value='".htmlentities($champ_rameau,ENT_QUOTES,$charset)."' />",
		"message" => "<br />Rameau sera int&eacute;gr&eacute; en zone d'indexation libre (Mots-cl&eacute;s) : ".htmlentities($champ_rameau,ENT_QUOTES,$charset)
	);
}

function traite_categories_from_form() {
	global $rameau ;
	global $f_free_index ;
	global $pmb_keyword_sep ;
	if (!$pmb_keyword_sep) $pmb_keyword_sep=" ; ";
	if(trim($rameau)){
		if (trim($f_free_index)) {
			$f_free_index=$f_free_index.$pmb_keyword_sep.$rameau;
		} else {
			$f_free_index=$rameau;
		}
	}
	
	return z3950_notice::traite_categories_from_form();
}


function create_categ_z3950($num_parent,$autorite, $libelle, $index) {
	
	global $thes;
	$n = new noeuds();
	$n->num_thesaurus = $thes->id_thesaurus;
	$n->autorite = $autorite;
	$n->num_parent = $num_parent;
	$n->save();
	
	$c = new categories($n->id_noeud, 'fr_FR');
	$c->libelle_categorie = $libelle;
	$c->index_categorie = $index;
	$c->save();
	return $n->id_noeud;
}

function param_perso_form(&$p_perso) {
	global $tableau_503;

	$tab_non_rep=array();
	if(count($tableau_503)){
		for($i=0;$i<count($tableau_503["info_503"]);$i++){
			
			if($tableau_503["info_503"][$i]["a"] && !($tab_non_rep["titre"])){
				$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_titre'";
				$res = pmb_mysql_query($rqt);
				if (pmb_mysql_num_rows($res)>0){
					$id_champ=pmb_mysql_result($res,0);
					//$p_perso->values[$id_champ][$i]=trim($tableau_503["info_503"][$i]["a"]);
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes(trim($tableau_503["info_503"][$i]["a"]))."' and notices_custom_champ='".$id_champ."'";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ='".$id_champ."'";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values('".$id_champ."',$n,'".addslashes(trim($tableau_503["info_503"][$i]["a"]))."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$tab_non_rep["titre"]=1;	
					$p_perso->values[$id_champ][$i]=$value;
				}
			}
			
			if($tableau_503["info_503"][$i]["e"]  && !($tab_non_rep["nom"])){
				$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_nom'";
				$res = pmb_mysql_query($rqt);
				if (pmb_mysql_num_rows($res)>0){
					$id_champ=pmb_mysql_result($res,0);
					$p_perso->values[$id_champ][$i]=trim($tableau_503["info_503"][$i]["e"]);
					$tab_non_rep["nom"]=1; 
				}
			}
			
			if($tableau_503["info_503"][$i]["f"] && !($tab_non_rep["prenom"])){
				$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_prenom'";
				$res = pmb_mysql_query($rqt);
				if (pmb_mysql_num_rows($res)>0){
					$id_champ=pmb_mysql_result($res,0);
					$p_perso->values[$id_champ][$i]=trim($tableau_503["info_503"][$i]["f"]);
					$tab_non_rep["prenom"]=1;  
				}
			}
			
			if($tableau_503["info_503"][$i]["h"] && !($tab_non_rep["qual"])){
				$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_qualificatif'";
				$res = pmb_mysql_query($rqt);
				if (pmb_mysql_num_rows($res)>0){
					$id_champ=pmb_mysql_result($res,0);
					$p_perso->values[$id_champ][$i]=trim($tableau_503["info_503"][$i]["h"]);
					$tab_non_rep["qual"]=1;  
				}
			}
			
			if(count($tableau_503["info_503_j"][$i])  && !($tab_non_rep["date"])){
				//Si j'ai une date
				
				$date="";
				for($p=0;$p<count($tableau_503["info_503_j"][$i]);$p++){
					if(trim($tableau_503["info_503_j"][$i][$p])){
						$jm=trim($tableau_503["info_503_d"][$i][$p]);
						if($jm){
							//Si j'ai aussi le mois et le jour
							if(strlen($jm) == 4){
								if($date){
									$date.=" - ".substr($jm,2,2)."/".substr($jm,0,2)."/".trim($tableau_503["info_503_j"][$i][$p]);
								}else{
									$date=substr($jm,2,2)."/".substr($jm,0,2)."/".trim($tableau_503["info_503_j"][$i][$p]);
								}
								
							}else{
								if($date){
									$date.=" - ".$jm." ".trim($tableau_503["info_503_j"][$i][$p]);
								}else{
									$date=$jm." ".trim($tableau_503["info_503_j"][$i][$p]);
								}	
							}
						}else{
							if($date){
								$date.=" - ".trim($tableau_503["info_503_j"][$i][$p]);	
							}else{
								$date=trim($tableau_503["info_503_j"][$i][$p]);
							}
						}
					}
				}
				
				if($date){
					$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_date'";
					$res = pmb_mysql_query($rqt);
					if (pmb_mysql_num_rows($res)>0){
						$id_champ=pmb_mysql_result($res,0);
						$p_perso->values[$id_champ][$i]=$date;
						$tab_non_rep["date"]=1; 
					}
				}
			}
			
			$lieu_etabl="";
			if(trim($tableau_503["info_503"][$i]["m"]) and trim($tableau_503["info_503"][$i]["n"])){
				$lieu_etabl=trim($tableau_503["info_503"][$i]["m"]).", ".trim($tableau_503["info_503"][$i]["n"]);
			}elseif(trim($tableau_503["info_503"][$i]["m"])){
				$lieu_etabl=trim($tableau_503["info_503"][$i]["m"]);
			}elseif(trim($tableau_503["info_503"][$i]["m"]) and trim($tableau_503["info_503"][$i]["n"])){
				$lieu_etabl=trim($tableau_503["info_503"][$i]["n"]);
			}
			if($lieu_etabl){
				$rqt = "SELECT idchamp FROM notices_custom WHERE name='t_d_f_lieu_etabl'";
				$res = pmb_mysql_query($rqt);
				if (pmb_mysql_num_rows($res)>0){
					$id_champ=pmb_mysql_result($res,0);
					$p_perso->values[$id_champ][$i]=$lieu_etabl; 
				}
			}
		}
	}
	/*echo "<pre>";
	print_r($p_perso);
	echo "</pre>";*/
}

function traite_info_subst(&$mes_info){
	//Je supprime les informations de date pour les auteurs personnes
	for($i=0; $i< count($mes_info->aut_array);$i++){
		if($mes_info->aut_array[$i]["type_auteur"] == "70"){
			$mes_info->aut_array[$i]["date"]="";
		}
	}
	
}	
