<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.inc.php,v 1.3 2017-06-06 15:26:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($location_id)) $location_id = 0;
if(!isset($type_filter)) $type_filter = 0;
if(!isset($statut_filter)) $statut_filter = 0;

require_once("$class_path/serialcirc_ask.class.php");

switch($sub){		
	case 'circ_ask':		
		switch($action){	
			case 'accept':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->accept();
				}				
			break;		
			case 'refus':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->refus();
				}				
			break;		
			case 'delete':		
				foreach($asklist_id as $id){
					$ask= new serialcirc_ask($id);
					$ask->delete();
				}				
			break;				
		}			
		$asklist=new serialcirc_asklist($location_id,$type_filter,$statut_filter);
		print $asklist->get_form_list();
	break;		
	default :			
		$asklist=new serialcirc_asklist($location_id,$type_filter,$statut_filter);
		print $asklist->get_form_list();
	break;		
	
}



