<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2021/02/09 07:25:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// inclusions principales

if(!$section && $elements){
	$section = "affect";
}
switch($section) {
	case "affect" :
		include($base_path."/admin/opac/opac_view/affect.inc.php");
		break;
	case "list":
	default :
		// affichage de la liste des recherches en opac
		include("./admin/opac/opac_view/list.inc.php");
	break;
}


