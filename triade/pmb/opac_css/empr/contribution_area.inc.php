<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.23 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_contribution_area_activate, $allow_contribution;
global $msg, $class_path, $include_path, $check_ids, $id_empr;
global $last_id, $lvl;

if (!$opac_contribution_area_activate || !$allow_contribution) {
	print $msg['empr_contribution_area_unauthorized'];
	return false;
}

require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');

if (!empty($check_ids)){
    $check_ids = explode(',',$check_ids);
} else {
    $check_ids = array();
}

switch ($lvl) {
	case 'contribution_area_new' :	
	    $areas = contribution_area::get_list();
		if (count($areas) == 1) {
		    // S'il n'y a qu'un seul espace, on affiche directement son contenu
		    $id = $areas[0]['id'];
		    
		    $contribution_url = "./index.php?lvl=contribution_area&sub=area&id=".$id;
		    
		    print '<script>
						window.location = "'.$contribution_url.'";
			</script>';
		} else {
		    $h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas.tpl.html');
		    print $h2o->render(array('areas' => $areas));
		}    	
		break;
	case 'contribution_area_list' :
		if ($id_empr) {
		    $template_path = $include_path .'/templates/contribution_area/contribution_areas_list.tpl.html';
		    if (file_exists($include_path .'/templates/contribution_area/contribution_areas_list_subst.tpl.html')) {
		        $template_path = $include_path .'/templates/contribution_area/contribution_areas_list_subst.tpl.html';
		    }
		    $h2o = H2o_collection::get_instance($template_path);
			print $h2o->render(array(
			    'forms' => contribution_area_forms_controller::get_empr_forms($id_empr, false, (!empty($last_id) ? $last_id : 0), false),
			    'contribution_area_done' => false,
			    'is_draft' => false,
			    'redirect' => $lvl,
			    'check_ids' => $check_ids,
			    'contribution_done' => (!empty($last_id) ? 1 : 0)
			));
		}
		break;
	case 'contribution_area_list_draft' :
		if ($id_empr) {
		    $template_path = $include_path .'/templates/contribution_area/contribution_areas_list.tpl.html';
		    if (file_exists($include_path .'/templates/contribution_area/contribution_areas_list_subst.tpl.html')) {
		        $template_path = $include_path .'/templates/contribution_area/contribution_areas_list_subst.tpl.html';
		    }
		    $h2o = H2o_collection::get_instance($template_path);print $h2o->render(array(
			    'forms' => contribution_area_forms_controller::get_empr_forms($id_empr, false, (!empty($last_id) ? $last_id : 0), true),
			    'contribution_area_done' => false,
			    'is_draft' => true,
			    'redirect' => $lvl,
			    'check_ids' => $check_ids,
		        'contribution_done' => (!empty($last_id) ? 1 : 0)
			));
		}
		break;
	case 'contribution_area_done' :
		if ($id_empr) {
		    $template_path = $include_path .'/templates/contribution_area/contribution_areas_list_done.tpl.html';
		    if (file_exists($include_path .'/templates/contribution_area/contribution_areas_list_done_subst.tpl.html')) {
		        $template_path = $include_path .'/templates/contribution_area/contribution_areas_list_done_subst.tpl.html';
		    }
		    $h2o = H2o_collection::get_instance($template_path);
			print $h2o->render(array(
			    'forms' => contribution_area_forms_controller::get_empr_forms_done($id_empr, (!empty($last_id) ? $last_id : 0)),
			    'redirect' => $lvl,
			    'contribution_done' => (!empty($last_id) ? 1 : 0)
			));
		}
		break;
	case 'contribution_area_moderation' :
	    global $gestion_acces_active, $gestion_acces_contribution_moderator_empr;
	    
	    if ($id_empr && ($gestion_acces_active == 1) && ($gestion_acces_contribution_moderator_empr == 1)) {
			$h2o = H2o_collection::get_instance($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			print $h2o->render(array(
			    'forms' => contribution_area_forms_controller::get_moderation_forms($id_empr),
			    'contribution_area_done' => false,
			    'is_draft' => false,
			    'redirect' => $lvl,
			    'check_ids' => $check_ids
			));
	    } else {
	        print $msg['empr_contribution_area_unauthorized'];
	    }
		break;
}
?>