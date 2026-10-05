<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7 2021/02/08 10:30:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'rep':
		include("./admin/upload/folders.inc.php");		
		break;
	case "storages" :
		require_once($class_path."/storages/storages.class.php");
		$storages = new storages();
		$storages->process($action,$id);
		break;
	case "statut" :
		include("./admin/upload/statut.inc.php");
		break;
	case 'perso':
		include("./admin/upload/perso.inc.php");
		break;
	case "licence":
		include("./admin/upload/licence.inc.php");
		break;
	default:
		break;
}
?>