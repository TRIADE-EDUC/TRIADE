<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_bretagne.inc.php,v 1.13 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function z_recup_noticeunimarc_suite($notice) {
	global $base_path, $class_path;
	require_once($base_path."/admin/import/func_bretagne.inc.php");
	recup_noticeunimarc_suite($notice);
} // fin recup_noticeunimarc_suite = fin récupération des variables propres à la bretagne
	
function z_import_new_notice_suite() {
	global $base_path, $class_path;
	require_once($base_path."/admin/import/func_bretagne.inc.php");
	import_new_notice_suite();
} // fin import_new_notice_suite
			
// TRAITEMENT DES EXEMPLAIRES ICI
function z_traite_exemplaires () {
	traite_exemplaires();
	} // fin traite_exemplaires	TRAITEMENT DES EXEMPLAIRES JUSQU'ICI
	
// enregistrement de la notices dans les catégories
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {
	z3950_notice::traite_categories_enreg($notice_retour, $categories, $thesaurus_traite);
}

function traite_categories_from_form() {
	return z3950_notice::traite_categories_from_form();
}
	