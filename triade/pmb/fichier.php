<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fichier.php,v 1.13 2021/04/28 06:52:35 dgoron Exp $


// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "FICHES_AUTH";  
$base_title = "\$msg[onglet_fichier]";
$base_use_dojo = 1;
$prefix = "gestfic0";

if ((isset($_POST["dest"])) && ($_POST["dest"]=="TABLEAU")) {
	$base_noheader=1;
}

require_once ("$base_path/includes/init.inc.php");  
// modules propres à fichier.php ou à ses sous-modules
require_once($class_path."/modules/module_fichier.class.php");
require("$include_path/templates/fichier.tpl.php");

// création de la page
module_fichier::get_instance()->proceed_header();
global $dest;
switch($dest) {
	case "TABLEAU":
		
		break;
	case "TABLEAUHTML":
		echo "<h1>".htmlentities($msg['onglet_fichier'].$msg[1003].$msg[1001],ENT_QUOTES,$charset)."</h1>";
		break;
	default:
		break;
}

switch($categ){
	case 'consult':
		include("$base_path/fichier/fichier_consult.inc.php");
		break;
	case 'saisie':
		include("$base_path/fichier/fichier_saisie.inc.php");
		break;
	case 'panier':
		include("$base_path/fichier/fichier_panier.inc.php");
		break;
	case 'gerer':
		include("$base_path/fichier/fichier_gestion.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("fichier",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
		include("$include_path/messages/help/$lang/module_fichier.txt");	
		break;
}

module_fichier::get_instance()->proceed_footer();
switch($dest) {
	case "TABLEAU":
		break;
	case "TABLEAUHTML":
		print "</body>" ;
		break;
	default:
		print "</body>" ;
		break;
}

// deconnection MYSql
pmb_mysql_close();