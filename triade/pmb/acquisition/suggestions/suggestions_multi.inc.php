<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_multi.inc.php,v 1.4 2019-05-28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $act, $class_path, $base_path, $acquisition_sugg_display;

if(!isset($act)) $act = '';

require_once($class_path.'/suggestion_multi.class.php');
require_once($class_path.'/suggestions_map.class.php');
require_once($base_path.'/acquisition/suggestions/func_suggestions.inc.php');
if ($acquisition_sugg_display) {
	require_once($acquisition_sugg_display);
} else {
	require_once('suggestions_display.inc.php');
}

$sug_map = new suggestions_map();
$sug = new suggestion_multi();
switch($act){	
	case 'save_multi_sugg':
		$sug->save();
		show_list_sug();
	break;
	case 'import':
		$sug->create_table_from_uni();
		print $sug->display_form();
		break;
	default:
		print $sug->display_form();	
	break;
}

?>