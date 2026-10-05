<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.inc.php,v 1.8 2023/01/09 14:44:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $location_id, $serialcirc_expl_ui_location, $cb, $cb_list, $id;
global $serialcirc_circ_form;

require_once("$class_path/serialcirc.class.php");

//Faut-il traiter la localisation passée en filtre "serialcirc_expl_ui_location" ??


if(!isset($location_id)) {
	if(!empty($serialcirc_expl_ui_location)) {
		$location_id = $serialcirc_expl_ui_location;
	} else {
		$location_id = 0;
	}
}
$serialcirc=new serialcirc($location_id);

switch($sub){	
	// Zone de pointage
	case 'cb_enter':
		print $serialcirc->gen_circ_cb($cb); 
		break;	
	case 'print_diff':
		$cb_list[]=$cb;
		print $serialcirc->print_diff_list($cb_list); 
		break;
	case 'del_circ':
	
		break;		
	
	// Zone de liste
	case 'print_diff_list':
		print $serialcirc->print_diff_list($cb_list); 
		break;		
	case 'list_diff':
		print list_serialcirc_diff_ui::get_instance()->get_display_list();
		break;
	default :
		require_once($class_path.'/serialcirc/serialcirc_expl_controller.class.php');
		
		print str_replace("!!message!!", "", $serialcirc_circ_form);
		
		serialcirc_expl_controller::proceed($id);
		if($cb){
			print "
			<script type='text/javascript'>
				serialcirc_circ_get_info_cb('".$cb."','serialcirc_pointage_zone');
				document.forms['saisie_cb_ex'].elements['form_cb_expl'].value='';
				document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus(); 
			</script>";
		}
	break;		
	
}



