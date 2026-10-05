<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.23 2019/06/05 09:04:41 btafforeau Exp $

global $class_path, $base_auth, $base_title, $base_noheader, $use_opac_url_base, $opac_url_base, $prefix_url_image, $no_aff_doc_num_image;
global $fichier_temp_nom, $idcaddie, $mode, $dest;

// définition du minimum nécessaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");  
require_once ("./edition_func.inc.php");  
require_once ($class_path."/caddie.class.php");
require_once ($class_path."/caddie/caddie_controller.class.php");

$use_opac_url_base=1;
$prefix_url_image=$opac_url_base;
$no_aff_doc_num_image=1;

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new caddie($idcaddie);
if (!$myCart->idcaddie) die();
// création de la page
if(empty($mode)) $mode = 'simple';
switch($dest) {
	case "TABLEAU":
		caddie_controller::proceed_edition_tableau($idcaddie, $mode);
		break;
	case "TABLEAUHTML":
		caddie_controller::proceed_edition_tableauhtml($idcaddie, $mode);
		break;
	case "EXPORT_NOTI":
		caddie_controller::proceed_edition_export_noti($idcaddie, $mode);
		break;		
	default:
		caddie_controller::proceed_edition_html($idcaddie, $mode);
		break;
}
pmb_mysql_close();
