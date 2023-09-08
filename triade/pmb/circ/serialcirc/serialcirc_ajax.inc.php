<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ajax.inc.php,v 1.10 2017-03-17 14:29:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc.class.php");

if($pmb_lecteurs_localises && (!isset($location_id) || !$location_id) && isset($_SESSION['serialcirc_location'])){
	$location_id = $_SESSION['serialcirc_location'];
} else {
	$location_id = 0;
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
		ajax_http_send_response($serialcirc->copy_accept($copy_id) ); 
	break;	
	case 'copy_none':
		ajax_http_send_response($serialcirc->copy_none($copy_id) ); 
	break;	
	case 'resa_accept':
		ajax_http_send_response($serialcirc->resa_accept($expl_id,$empr_id) ); 
	break;	
	case 'resa_none':
		ajax_http_send_response($serialcirc->resa_none($expl_id,$empr_id) ); 
	break;
	default :
	break;		
	
}



