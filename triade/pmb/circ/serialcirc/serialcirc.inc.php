<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.inc.php,v 1.4 2017-01-25 16:43:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc.class.php");

if(!isset($location_id)) $location_id = 0;
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
	
	default :
		print $serialcirc->gen_circ_form(); 
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



