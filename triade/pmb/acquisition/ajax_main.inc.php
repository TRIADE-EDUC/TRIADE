<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.7 2019/05/28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $plugin, $sub;

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ) {
	case 'ach':
		include("./acquisition/achats/ajax_main.inc.php");
		break;
	case 'sugg':
		include("./acquisition/suggestions/ajax/ajax_sugg.inc.php");
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'rent' :
		include("./acquisition/rent/ajax_main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("acquisition",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
		break;		
}
