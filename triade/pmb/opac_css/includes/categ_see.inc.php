<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_see.inc.php,v 1.114 2021/12/28 10:28:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $opac_auto_postage_etendre_recherche, $opac_auto_postage_nb_montant, $opac_auto_postage_nb_descendant;

require_once ($class_path . "/categories.class.php");

require_once($class_path."/authorities/page/authority_page_category.class.php");

if (!isset($id)) $id = 0;
$id = intval($id);
if (!isset($main)) $main = 0;
$main = intval($main);
if ($id) {
	//recuperation du thesaurus session
	if (!isset($id_thes) || !$id_thes) {
		$id_thes = thesaurus::getSessionThesaurusId();
	} else {
		thesaurus::setSessionThesaurusId($id_thes);
	}
	
	//LISTE DES NOTICES ASSOCIEES
	//Lire le champ path du noeud pour étendre la recherche éventuellement au fils et aux père de la catégorie
	// lien Etendre auto_postage
	if (!isset($nb_level_enfants)) {
		// non defini, prise des valeurs par défaut
		if (isset($_SESSION["nb_level_enfants"]) && $opac_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
		else $nb_level_descendant=$opac_auto_postage_nb_descendant;
	} else {
		$nb_level_descendant=$nb_level_enfants;
	}
	
	// lien Etendre auto_postage
	if(!isset($nb_level_parents)) {
		// non defini, prise des valeurs par défaut
		if(isset($_SESSION["nb_level_parents"]) && $opac_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
		else $nb_level_montant=$opac_auto_postage_nb_montant;
	} else {
		$nb_level_montant=$nb_level_parents;
	}
	
	$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
	$_SESSION["nb_level_parents"]=	$nb_level_montant;
	
	$q = "select path from noeuds where id_noeud = '".$id."' ";
	$r = pmb_mysql_query($q);
	if($r && pmb_mysql_num_rows($r)){
		$path=pmb_mysql_result($r, 0, 0);
		$nb_pere=substr_count($path,'/');
	}else{
		$path="";
		$nb_pere=0;
	}
	
	if ($path) {
		if ($opac_auto_postage_etendre_recherche == 1 || ($opac_auto_postage_etendre_recherche == 2 && !$nb_pere)) {
			$input_txt="<input name='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_enfants='+this.value\">";
			$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_enfants"]);
		} elseif ($opac_auto_postage_etendre_recherche == 2 && $nb_pere) {
			$input_txt="<input name='nb_level_enfants' id='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_enfants='+this.value+'&nb_level_parents='+document.getElementById('nb_level_parents').value;\">";
			$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_parents_enfants"]);
	
			$input_txt="<input name='nb_level_parents' id='nb_level_parents' type='text' size='2' value='$nb_level_montant'
			onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_parents='+this.value+'&nb_level_enfants='+document.getElementById('nb_level_enfants').value;\">";
			$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$auto_postage_form);
	
		} elseif ($opac_auto_postage_etendre_recherche == 3 ) {
			if($nb_pere) {
				$input_txt="<input name='nb_level_parents' type='text' size='2' value='$nb_level_montant'
				onchange=\"document.location='$base_path/index.php?lvl=categ_see&id=$id&main=$main&nb_level_parents='+this.value\">";
				$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$msg["categories_autopostage_parents"]);
			}
		}
	}
	
	$authority_page = new authority_page_category($id);
	$authority_page->proceed('categories');
} else {
	$ourCateg = new categorie(0);
	print pmb_bidi($ourCateg->child_list(get_url_icon('folder.gif'),$css));
}