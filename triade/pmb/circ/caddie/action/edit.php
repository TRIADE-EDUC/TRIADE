<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.9 2022/03/11 09:06:36 dgoron Exp $

// définition du minimum nécéssaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");

global $class_path, $mode, $dest, $idemprcaddie;

require_once ("./edition_func.inc.php");  
require_once ($class_path."/empr_caddie.class.php");
require_once ($class_path."/caddie/empr_caddie_controller.class.php");

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new empr_caddie($idemprcaddie);
if (!$myCart->idemprcaddie) die();
// création de la page
if(empty($mode)) $mode = 'simple';
switch($dest) {
	case "TABLEAU":
		empr_caddie_controller::proceed_edition_tableau($idemprcaddie, $mode);
		break;
	case "TABLEAUHTML":
		empr_caddie_controller::proceed_edition_tableauhtml($idemprcaddie, $mode);
		break;
	default:
		empr_caddie_controller::proceed_edition_html($idemprcaddie, $mode);
		break;
}
	
pmb_mysql_close();
