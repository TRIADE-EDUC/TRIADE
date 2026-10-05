<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.php,v 1.17 2021/04/28 06:52:35 dgoron Exp $

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $class_path, $msg;
global $categ, $charset, $plugin, $sub, $lang;

// définition du minimum nécéssaire 
$base_path = ".";                            
$base_auth = "DEMANDES_AUTH";  
$base_title = "\$msg[demandes_menu_title]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");  

// modules propres à demandes.php ou à ses sous-modules
require_once($class_path."/modules/module_demandes.class.php");
require_once($class_path.'/interface/demandes/interface_demandes_form.class.php');
require("$include_path/templates/demandes.tpl.php");
require("$include_path/templates/demandes_actions.tpl.php");
require("$include_path/templates/demandes_notes.tpl.php");
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/demandes_types.class.php");

module_demandes::get_instance()->proceed_header();

$error_msg = '';
if($categ == 'faq') {
	$nb_themes = faq_themes::get_qty();
	$nb_types = faq_types::get_qty();
	if(!$nb_themes) {
		//Pas de themes
		$error_msg .= htmlentities($msg["faq_no_theme_available"], ENT_QUOTES, $charset)."<div class='row'></div>";
	}
	if(!$nb_types) {
		//Pas de themes
		$error_msg .= htmlentities($msg["faq_no_type_available"], ENT_QUOTES, $charset)."<div class='row'></div>";
	}
	if(!$nb_themes || !$nb_types) {
		//Pas de themes ou de types définis
		error_message($msg[321], $error_msg.htmlentities($msg["faq_err_par"], ENT_QUOTES, $charset), '1', './admin.php?categ=faq');
	}
} else {
	$nb_themes = demandes_themes::get_qty();
	$nb_types = demandes_types::get_qty();
	if(!$nb_themes || !$nb_types) {
		//Pas de themes ou de types définis
		$error_msg .= htmlentities($msg["demandes_err_theme_type"], ENT_QUOTES, $charset)."<div class='row'></div>";
		error_message($msg[321], $error_msg.htmlentities($msg["demandes_err_par"], ENT_QUOTES, $charset), '1', './admin.php?categ=demandes');
	}
}
if(!$error_msg) {
	switch($categ){
		case 'gestion':
			include("./demandes/demandes.inc.php");
			break;
		case 'list' :
			include("./demandes/demandes_liste.inc.php");
			break;
		case 'action' :
			include("./demandes/demandes_actions.inc.php");
			break;
		case 'notes' :
			include("./demandes/demandes_notes.inc.php");
			break;
		case "faq" :
			include("./demandes/faq/main.inc.php");
			break;
		case 'plugin' :
			$plugins = plugins::get_instance();
			$file = $plugins->proceed("demandes", $plugin, $sub);
			if($file){
				include $file;
			}
			break;
		default :		
			include("$include_path/messages/help/$lang/demandes.txt");	
		break;
	}
}

module_demandes::get_instance()->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
?>