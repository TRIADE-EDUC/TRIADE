<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_main.inc.php,v 1.13 2023/09/06 06:55:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg, $serial_header;
global $serial_id, $bul_id, $analysis_id, $explnum_id, $f_explnum_id;

require_once($class_path."/entities/entities_analysis_controller.class.php");
require_once($class_path."/entities/entities_analysis_explnum_controller.class.php");

function abort() {
	echo "<script type=\"text/javascript\">
		alert(\"PMB navigation error. Please contact devel team...\");
		document.location=\"./catalog.php?categ=serials\";
		</script>";
}

switch($action) {
	case 'analysis_form':
		$entities_analysis_controller = new entities_analysis_controller($analysis_id);
		$entities_analysis_controller->set_bulletin_id($bul_id);
		$entities_analysis_controller->set_action('form');
		$entities_analysis_controller->proceed();
		break;
	case 'analysis_duplicate':
		$entities_analysis_controller = new entities_analysis_controller($analysis_id);
		$entities_analysis_controller->set_bulletin_id($bul_id);
		$entities_analysis_controller->set_action('duplicate');
		$entities_analysis_controller->proceed();
		break;
	case 'update':
		include('./catalog/serials/analysis/analysis_update.inc.php');
		break;
	case 'delete':
		$entities_analysis_controller = new entities_analysis_controller($analysis_id);
		$entities_analysis_controller->set_bulletin_id($bul_id);
		$entities_analysis_controller->set_serial_id($serial_id);
		$entities_analysis_controller->set_action('delete');
		$entities_analysis_controller->proceed();
		break;
	case 'explnum_delete':
		// suppression d'un exemplaire de bulletinage
		echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);
		
		$entities_analysis_explnum_controller = new entities_analysis_explnum_controller($explnum_id);
		$entities_analysis_explnum_controller->set_bulletin_id($bul_id);
		$entities_analysis_explnum_controller->set_action('explnum_delete');
		$entities_analysis_explnum_controller->proceed();
		break;
	case 'explnum_update':
		echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);
		
		$entities_analysis_explnum_controller = new entities_analysis_explnum_controller($f_explnum_id);
		$entities_analysis_explnum_controller->set_bulletin_id($bul_id);
		$entities_analysis_explnum_controller->set_action('explnum_update');
		$entities_analysis_explnum_controller->proceed();
		break;	
	case 'explnum_form':
		echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);
		
		$entities_analysis_explnum_controller = new entities_analysis_explnum_controller($explnum_id);
		$entities_analysis_explnum_controller->set_bulletin_id($bul_id);
		$entities_analysis_explnum_controller->set_analysis_id($analysis_id);
		$entities_analysis_explnum_controller->set_action('explnum_form');
		$entities_analysis_explnum_controller->proceed();
		
		break;
	case 'analysis_move':
		include('./catalog/serials/analysis/analysis_move.inc.php');
		break;
	case 'analysis_orphan_form':
	    $entities_analysis_controller = new entities_analysis_controller($analysis_id);
	    $entities_analysis_controller->set_action('orphan_form');
	    $entities_analysis_controller->proceed();
	    break;
	default:
		abort();
		break;
}
?>