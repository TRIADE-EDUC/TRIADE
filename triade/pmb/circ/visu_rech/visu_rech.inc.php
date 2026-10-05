<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visu_rech.inc.php,v 1.15 2021/03/19 14:49:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice

require_once("$include_path/templates/catalog.tpl.php");
require_once("$include_path/isbn.inc.php");
require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once("$class_path/marc_table.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/notice.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/expl.class.php");
require_once("$class_path/explnum.class.php");

// inclusions principales
require_once("$include_path/templates/resa.tpl.php");
require_once("$class_path/searcher.class.php");

// gestion des liens en rech resa ou pas 

//Lien pour l'affichage
if (SESSrights & CATALOGAGE_AUTH){
	$link = notice::get_pattern_link();
	$link_analysis = analysis::get_pattern_link();
	$link_serial = serial::get_pattern_link();
	$link_bulletin=bulletinage::get_pattern_link();
	$link_explnum_serial="";
	$link_expl = exemplaire::get_pattern_link();		
} else {
	$link = "";	
	$link_analysis = "";
	$link_serial = "";
	$link_bulletin = "";
	$link_explnum_serial = "";
	$link_expl = "";	
}
$base_url = "./circ.php?categ=visu_rech";

print str_replace("!!mode_recherche!!", $msg[354], $menu_search_visu_rech);
switch($mode) {
	case 'view_serial'://Ce cas n'est plus possible depuis le 19/05/2010
		// affichage de la liste des éléments bulletinés pour un périodique
		include('./circ/resa/view_serial.inc.php');
		break;
	default :
		if (!empty($ex_query)) {
			$back_to_visu=1;
			include('./circ/visu_ex.inc.php');
		} else{
			$sh=new searcher_title($base_url);
		}
		break;
}
