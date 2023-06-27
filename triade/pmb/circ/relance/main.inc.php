<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2015-02-18 09:46:00 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($sub) {
	case "todo":
		echo window_title($database_window_title.$msg["5"]." : ".$msg["relance_menu"]." ".$msg["relance_to_do"]);
		require_once("./circ/relance/relance.inc.php");
		break;
	case "recouvr":
		echo window_title($database_window_title.$msg["5"]." : ".$msg["relance_menu"]." ".$msg["relance_recouvrement"]);
		require_once("./circ/relance/recouvr.inc.php");
		break;
}

?>