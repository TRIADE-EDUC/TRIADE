<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.7 2021/12/09 09:00:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $sub, $object_type;
global $num_bulletin, $num_record, $num_request;

//Point d'entrée d'upload, de suppression et d'ajout de documents numériques sur une demande
/**
 * Id de demande nécessaire pour tout les traitements
 * Id de notice ? (a voir si la valorisation ne se fait pas plutôt au post du formulaire)
 */
require_once($class_path.'/scan_request/scan_request.class.php');

$num_bulletin = intval($num_bulletin);
$num_record = intval($num_record);
$num_request = intval($num_request);

switch($action) {
    case "list":
        lists_controller::proceed_ajax($object_type, 'scan_requests');
        break;
}

if($num_request == 0 || ($num_bulletin && $num_record)) return;
if($num_request>0){
	$scan_request = new scan_request($num_request);	
	switch($sub){	
		case 'upload':			
			$scan_request->add_explnum();
			break;
		case 'edit':    
			print  $scan_request->get_ajax_form();
			break;
		case 'save':		
			print $scan_request->save_ajax_form();
			break;
		default:
			
			break;
	}
}
