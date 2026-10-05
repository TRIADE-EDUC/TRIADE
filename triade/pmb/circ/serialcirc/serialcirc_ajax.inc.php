<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ajax.inc.php,v 1.14 2023/01/05 11:11:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $location_id, $expl_id, $start_diff_id, $cb, $list, $empr_id, $copy_id;
global $pmb_lecteurs_localises, $pmb_serialcirc_subst;

require_once("$class_path/serialcirc.class.php");
require_once("$class_path/serialcirc/serialcirc_copy.class.php");
require_once("$class_path/expl.class.php");

$expl_id = intval($expl_id);
if($pmb_lecteurs_localises && (!isset($location_id) || !$location_id) && isset($_SESSION['serialcirc_location'])){
	$location_id = $_SESSION['serialcirc_location'];
} else {
	if($expl_id) {
		$location_id = exemplaire::get_expl_location_from_id($expl_id);
	} else {
		$location_id = 0;
	}
}

if ($pmb_serialcirc_subst) {
	require_once("$class_path/".$pmb_serialcirc_subst);	
	$serialcirc=new serialcirc_subst($location_id);
}else {
	$serialcirc=new serialcirc($location_id);
}

switch($sub){	
	// Zone de pointage
	case 'cb_enter':
		ajax_http_send_response($serialcirc->gen_circ_cb($cb)); 
		break;		
	case 'send_alert':
		ajax_http_send_response($serialcirc->send_alert($expl_id) ); 
		break;	
	case 'print_diffusion':		
		// retourne le pdf, donc pas de ajax_http_send_response
		print $serialcirc->print_diffusion($expl_id, $start_diff_id) ; 
		break;	
	case 'print_sel_diffusion':		
		// retourne le pdf des fiches de circulation sélectionnées, donc pas de ajax_http_send_response		
		print $serialcirc->print_sel_diffusion(unserialize(stripslashes($list))) ; 
		break;		
	case 'print_cote':		
		// retourne le pdf, donc pas de ajax_http_send_response
		print $serialcirc->print_cote($expl_id, $start_diff_id) ; 
		break;	
	case 'print_sel_cote':		
		// retourne le pdf des fiches de circulation sélectionnées, donc pas de ajax_http_send_response		
		print $serialcirc->print_sel_cote(unserialize(stripslashes($list))) ; 
		break;	
	case 'call_expl':
		ajax_http_send_response($serialcirc->call_expl($expl_id) ); 
		break;	
	case 'call_insist':
		ajax_http_send_response($serialcirc->call_insist($expl_id) ); 
		break;		
	case 'do_trans':
		ajax_http_send_response($serialcirc->do_trans($expl_id) ); 
		break;
	case 'return_expl':
		ajax_http_send_response($serialcirc->return_expl($expl_id) ); 
		break;	
	case 'delete_diffusion':
		ajax_http_send_response($serialcirc->delete_diffusion($expl_id) ); 
		break;		
	case 'copy_accept':
		$serialcirc_copy = new serialcirc_copy($copy_id);
		ajax_http_send_response($serialcirc_copy->copy_accept()); 
		break;	
	case 'copy_none':
		$serialcirc_copy = new serialcirc_copy($copy_id);
		ajax_http_send_response($serialcirc_copy->copy_none()); 
		break;	
	case 'resa_accept':
		ajax_http_send_response($serialcirc->resa_accept($expl_id,$empr_id) ); 
		break;	
	case 'resa_none':
		ajax_http_send_response($serialcirc->resa_none($expl_id,$empr_id) ); 
		break;
	case 'repair_diffusion':
		ajax_http_send_response($serialcirc->repair_diffusion($expl_id));
		break;
	default :
	break;		
	
}



