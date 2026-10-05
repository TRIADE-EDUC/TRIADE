<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_category_auto.inc.php,v 1.7 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// enregistrement de la notices dans les catégories
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/categories.class.php");
global $thesaurus_defaut;
/*
//Attention, dans le multithesaurus, le thesaurus dans lequel on importe est le thesaurus par defaut
$thes = new thesaurus($thesaurus_defaut);
$rac = $thes->num_noeud_racine;*/

function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}

function traite_categories_for_form($tableau_600="",$tableau_601="",$tableau_602="",$tableau_605="",$tableau_606="",$tableau_607="",$tableau_608="") {
	
	global $charset, $rameau,$lang,$msg;
	
	$tabl_categ_lib=array();
	category_auto::save_info_categ($tabl_categ_lib);
	
	
	$list_rameau=array();
	$pile=array();
	$tabl_build=array();
	$incr=0;
	$id_parent=$id_thes=0;
	foreach ( $tabl_categ_lib as $key => $value ) {
		if(array_key_exists("link",$value)){
			if($value["id_thes"] && !$value["link"] && !$value["word_parent"]){
				$id_parent=$value["id_parent"];
				$id_thes=$value["id_thes"];
				$thes_temp = new thesaurus($value["id_thes"]);
				$lib="[".$thes_temp->libelle_thesaurus."]";
				$pile=array();
				if(($value["id_parent"]) && ($lib_hierar=trim(categories::listAncestorNames($value["id_parent"], $lang)))){
					$pile[]=$lib." ".$lib_hierar.":";
				}else{
					$pile[]=$lib." ";
				}
				$pile[]=$value["wording"];
			}else{
				if($value["link"] == 1){
					$lib_temp=implode("",$pile);
					
					$list_rameau[$lib_temp]=htmlentities($lib_temp,ENT_QUOTES,$charset);
					if(!$value["create_node"]){
						$list_rameau[$lib_temp].="</b>".htmlentities($msg["func_category_auto_reprise"],ENT_QUOTES,$charset)."<b>";
					}
										
					$tabl_build[$incr]["wording"]=$pile;
					$tabl_build[$incr]["id_thes"]=$id_thes;
					$tabl_build[$incr]["id_parent"]=$id_parent;
					$tabl_build[$incr]["create_node"]=$value["create_node"];
					$incr++;
				}else{
					while(($pile[count($pile)-1] != $value["word_parent"]) && (count($pile))){
						array_pop($pile);
					}
					$pile[] = ":";
					$pile[] = $value["wording"];
				}
			}
		}else{
			if($value["id_thes"] && $value["wording"]){
				$thes_temp = new thesaurus($value["id_thes"]);
				$lib="[".$thes_temp->libelle_thesaurus."]";
				$lib_hierar="";
				if($value["id_parent"]){
					$lib_hierar=" ".categories::listAncestorNames($value["id_parent"], $lang);
				}
				if(trim($lib_hierar)){
					$lib.=$lib_hierar.":".$value["wording"];
				}else{
					$lib.=" ".$value["wording"];
				}
				
				$list_rameau[$lib]=htmlentities($lib,ENT_QUOTES,$charset);
				if(!$value["id_parent"]){
					$list_rameau[$lib].="</b>".htmlentities($msg["func_category_auto_reprise"],ENT_QUOTES,$charset)."<b>";
				}
								
				$tabl_build[$incr]["wording"]=$value["wording"];
				$tabl_build[$incr]["id_thes"]=$value["id_thes"];
				$tabl_build[$incr]["id_parent"]=$value["id_parent"];
				$incr++;
			}
		}
	}
	
	$champ_rameau="";
	$champ_rameau.=implode("<br/>",$list_rameau);
	//$champ_rameau.="<pre>".print_r($tabl_categ_lib,true)."</pre>";
	$rameau_form = serialize($tabl_build) ;
	
	// $rameau est la variable traitée par la fonction traite_categories_from_form, 
	// $rameau est normalement POSTée, afin de pouvoir être traitée en lot, donc hors 
	// formulaire, il faut l'affecter.
	$rameau = addslashes(serialize($tabl_build)) ;

	return array(
		"form" => "<input type='hidden' name='rameau' value='".htmlentities($rameau_form,ENT_QUOTES,$charset)."' />",
		"message" => htmlentities($msg["func_category_auto_reprise_suivante"],ENT_QUOTES,$charset)."<br/><b>".$champ_rameau."</b>"
	);
}


function traite_categories_from_form() {
	global $rameau ;
	
	$tabl_build = unserialize(stripslashes($rameau)) ;
	
	$categ_retour=array();
	
	foreach ( $tabl_build as $value ) {
       if(is_array($value["wording"])){
       		$id_parent=$value["id_parent"];
       		$id_thes=$value["id_thes"];
       		foreach ( $value["wording"] as $key => $lib ) {
       			if($key && $lib !=":"){
       				$id_parent=create_categ_z3950($lib,$id_thes,$id_parent,$value["create_node"]);
       			}
			}
			if($id_parent){
       			$categ_retour[]['categ_id'] = $id_parent ;
       		}
			
       }else{
       		$id_noeud=create_categ_z3950($value["wording"],$value["id_thes"],$value["id_parent"]);
       		if($id_noeud){
       			$categ_retour[]['categ_id'] = $id_noeud ;
       		}
       }
	}
// DEBUG echo "<pre>"; print_r($categ_retour) ; echo "</pre>"; exit ;
	return $categ_retour ;
}


function create_categ_z3950($tab_categ,$id_thes,$id_parent,$create_node=true){
	global $lang;
	if(trim($tab_categ)){
		$resultat = categories::searchLibelle(addslashes($tab_categ), $id_thes, $lang,$id_parent);				
		if (!$resultat && $id_parent && $create_node){
			// création de la catégorie
			$n=new noeuds();
			$n->num_parent=$id_parent;
			$n->num_thesaurus=$id_thes;
			$n->save();
			$resultat=$id_n=$n->id_noeud;
			$c=new categories($id_n, $lang);
			$c->libelle_categorie=$tab_categ;
			$c->save();
		}
		return $resultat;
	}
	return 0;
}
