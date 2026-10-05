<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.3 2019/06/03 07:04:57 btafforeau Exp $

global $class_path, $fichier_temp_nom, $mode, $dest, $idcaddie;

// définition du minimum nécessaire 
$base_path="../../..";                            
$base_auth = "AUTORITES_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");  
require_once ($class_path."/caddie/authorities_caddie_controller.class.php");

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

// création de la page
if(empty($mode)) $mode = 'simple';
switch($dest) {
	case "TABLEAU":
		authorities_caddie_controller::proceed_edition_tableau($idcaddie, $mode);
		break;
	case "TABLEAUHTML":
		authorities_caddie_controller::proceed_edition_tableauhtml($idcaddie, $mode);
		break;
	default:
		authorities_caddie_controller::proceed_edition_html($idcaddie, $mode);
		break;
}
	
pmb_mysql_close();
