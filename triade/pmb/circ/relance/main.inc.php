<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2021/04/21 20:49:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;
switch ($sub) {
	case "todo":
		require_once("./circ/relance/relance.inc.php");
		break;
	case "recouvr":
		require_once("./circ/relance/recouvr.inc.php");
		break;
}

?>