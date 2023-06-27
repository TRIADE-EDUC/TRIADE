<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.12 2019-05-23 12:31:03 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	print $msg['empr_contribution_area_unauthorized'];
	return false;
}

require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');

switch ($lvl) {
	case 'contribution_area_new' :	
	    $areas = contribution_area::get_list();
		if (count($areas) == 1) {
		    // S'il n'y a qu'un seul espace, on affiche directement son contenu
		    $id = $areas[0]['id'];
		    
		    $contribution_url = "./index.php?lvl=contribution_area&sub=area&id=".$id;
		    
		    print '<script type="text/javascript">
						window.location = "'.$contribution_url.'";
			</script>';
		} else {
		    $h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas.tpl.html');
		    print $h2o->render(array('areas' => $areas));
		}    	
		break;
	case 'contribution_area_list' :
		if ($id_empr) {
			$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			print $h2o->render(array('forms' => contribution_area_forms_controller::get_empr_forms($id_empr)));
		}
		break;
	case 'contribution_area_done' :
		if ($id_empr) {
			$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			print $h2o->render(array('forms' => contribution_area_forms_controller::get_empr_forms($id_empr, true, (!empty($last_id) ? $last_id : 0))));
		}
		break;
	case 'contribution_area_moderation' :
		if ($id_empr) {
			$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			print $h2o->render(array('forms' => contribution_area_forms_controller::get_moderation_forms($id_empr)));
		}
		break;
}
?>