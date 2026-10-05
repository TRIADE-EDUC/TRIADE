<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7 2021/05/25 07:04:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $quelle, $mode, $msg;
global $idemprcaddie, $idemprcaddie_origine;
$idemprcaddie_origine = intval($idemprcaddie_origine);

switch ($quelle) {
	case 'transfert':
		empr_caddie_controller::proceed_transfert($idemprcaddie, $idemprcaddie_origine);
		break;
	case 'export':
		require_once("$include_path/parser.inc.php");
		empr_caddie_controller::proceed_export($idemprcaddie);
		break;
	case 'supprpanier':
		empr_caddie_controller::proceed_supprpanier($idemprcaddie);
		break;
	case 'supprbase':
		empr_caddie_controller::proceed_supprbase($idemprcaddie);
		break;
	case 'edition':
		if(empty($mode)) $mode = 'simple';
		empr_caddie_controller::proceed_edition($idemprcaddie, $mode);
		break;
	case 'selection':
		require_once ($class_path."/empr_caddie_procs.class.php");
		empr_caddie_controller::proceed_selection($idemprcaddie, 'action', 'selection');
		break;
	case 'mailing':
		include ("./circ/caddie/action/mailing.inc.php");
		break;
	case 'carte':
		empr_caddie_controller::proceed_carte($idemprcaddie);
		break;
	default:
		print "<br /><br /><b>".$msg["caddie_select_action"]."</b>" ;
		break;
	}
