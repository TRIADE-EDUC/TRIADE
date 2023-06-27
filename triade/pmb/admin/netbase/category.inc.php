<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.inc.php,v 1.21 2017-11-22 11:07:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/thesaurus.class.php");
require_once("$class_path/noeuds.class.php");
require_once("$class_path/categories.class.php");


//function process_categ($id_noeud) {
//	global $dbh;
//	
//	global $deleted;
//	global $lot;
//	
//	$res = noeuds::listChilds($id_noeud, 0);
//	$total = pmb_mysql_num_rows($res);
//	if ($total) {
//		while ($row = pmb_mysql_fetch_object($res)) {
//			// la categorie a des filles qu'on va traiter
//			process_categ ($row->id_noeud);
//		}
//		
//		// après ménage de ses filles, reste-t-il des filles ?
//		$total_filles = noeuds::hasChild($id_noeud);
//		
//		// categ utilisée en renvoi voir ?
//		$total_see = noeuds::isTarget($id_noeud);
//		
//		// est-elle utilisée ?
//		$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
//		
//		if(!$iuse && !$total_filles && !$total_see) {
//			$deleted++ ;
//			noeuds::delete($id_noeud);
//		}
//		
//	} else { // la catégorie n'a pas de fille on va la supprimer si possible
//			// regarder si categ utilisée
//			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
//			if(!$iuse) {
//				$deleted++ ;
//				noeuds::delete($id_noeud);
//			}
//	}
//			
//}

$v_state=urldecode($v_state);

if ($deleted=="") $deleted=0 ;

print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_suppr_categories"], ENT_QUOTES, $charset)."</h2>";

$list_thesaurus = thesaurus::getThesaurusList();
foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
	$thes = new thesaurus($id_thesaurus);
	$noeud_rac =  $thes->num_noeud_racine;
	$r = noeuds::listChilds($noeud_rac, 0);
	while($row = pmb_mysql_fetch_object($r)){
		noeuds::process_categ($row->id_noeud);
	}
}	

//Nettoyage des informations d'autorités pour les sous collections
noeuds::delete_autority_sources();

$spec = $spec - CLEAN_CATEGORIES;
//TODO non repris >> Utilité ???
//	$delete = pmb_mysql_query("delete from categories where categ_libelle='#deleted#'");

$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_suppr_categories"], ENT_QUOTES, $charset)." : ";
$v_state .= $deleted." ".htmlentities($msg["nettoyage_res_suppr_categories"], ENT_QUOTES, $charset);

$optn = noeuds::optimize();
$optc = categories::optimize();

print netbase::get_process_state_form($v_state, $spec);
