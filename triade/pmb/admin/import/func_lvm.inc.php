<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_lvm.inc.php,v 1.4 2022/04/25 14:43:37 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".inc.php" ))
	die ( "no access" );
	
	// require_once("$class_path/categories.class.php");
function recup_noticeunimarc_suite($notice) {
	global $info_606;
	$info_606 = array ();
	$record = new iso2709_record ( $notice, AUTO_UPDATE );
	$info_606 = $record->get_subfield ( "606", "a", "z" );
} // fin recup_noticeunimarc_suite

function import_new_notice_suite() {
	global $notice_id;
	global $info_606;
	
	$ordre_categ = 0;
	foreach ( $info_606 as $categorie ) {
		$libelle = $categorie["a"];
		$lang = $categorie["z"];
		// Recherche du terme
		// dans le thesaurus par defaut et dans la langue de l'interface
		$libelle = addslashes ( $libelle );
		$categ_id = categories::searchLibelle ( $libelle , 0, $lang);
		if ($categ_id) {
			$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) values($notice_id,$categ_id,$ordre_categ)";
			pmb_mysql_query($requete);
			$ordre_categ ++;
		}
	}
}	