<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: extensions.php,v 1.8.8.1 2025/04/01 09:40:16 dgoron Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "EXTENSIONS_AUTH";
$base_title = "\$msg[extensions_menu]";
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

global $class_path, $include_path;

require_once($class_path."/modules/module_extensions.class.php");


module_extensions::get_instance()->proceed_header();

// ATTENTION: la ligne suivante (21) et la ligne 27 (les /DIV correspondants) sont à reproduire dans le fichier inclus "extensions.inc.php"
// print "<div id='conteneur' class='$current_module'><div id='contenu'>";

// 
if (file_exists("$include_path/extensions.inc.php")) {
	include("$include_path/extensions.inc.php");
}
// print "</div></div>";

module_extensions::get_instance()->proceed_footer();

html_builder();

// deconnection MYSql
pmb_mysql_close();
