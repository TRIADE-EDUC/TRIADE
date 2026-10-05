<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.22 2022/03/08 13:45:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quelle, $mode, $msg, $idcaddie, $idcaddie_origine;
switch ($quelle) {
	case 'changebloc':
		break;
	case 'transfert':
		$idcaddie_origine = intval($idcaddie_origine);
		caddie_controller::proceed_transfert($idcaddie, $idcaddie_origine);
		break;
	case 'export':
		include ("./catalog/caddie/action/export.inc.php");
		break;
	case 'supprpanier':
		caddie_controller::proceed_supprpanier($idcaddie);
		break;
	case 'supprbase':
		caddie_controller::proceed_supprbase($idcaddie);
		break;
	case 'edition':
		require_once("./classes/notice_tpl_gen.class.php");
		if(empty($mode)) $mode = 'simple';
		caddie_controller::proceed_edition($idcaddie, $mode);
		break;
	case 'selection':
		require_once ($class_path."/caddie_procs.class.php");
		caddie_controller::proceed_selection($idcaddie, 'action', 'selection');
		break;
	case 'impr_cote':
		include ("./catalog/caddie/action/impr_cote.inc.php");
		break;
	case 'docnum':
		caddie_controller::proceed_docnum($idcaddie);
		break;
	case 'reindex':
		caddie_controller::proceed_reindex($idcaddie);
		break;
	case 'access_rights':
		caddie_controller::proceed_access_rights($idcaddie);
		break;
	case 'scan_request':
		include ("./catalog/caddie/action/scan_request.inc.php");
		break;
	case 'transfert_to_location':
		include ("./catalog/caddie/action/transfert_to_location.inc.php");
		break;
	case 'print_barcode':
		caddie_controller::proceed_print_barcode($idcaddie);
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
		break;
	}
