<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2018/01/12 13:52:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants
require_once($class_path.'/modules/module_frbr.class.php');
switch($categ):
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("frbr",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'cataloging':
			$module_frbr = new module_frbr();
			$module_frbr->proceed_ajax_cataloging();
		break;
	default:
		break;		
endswitch;
