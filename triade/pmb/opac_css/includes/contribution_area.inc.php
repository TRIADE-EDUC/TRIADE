<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.29.4.1 2025/03/05 10:20:03 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $opac_contribution_area_activate, $allow_contribution, $msg, $class_path, $gestion_acces_active, $gestion_acces_empr_contribution_area;
global $gestion_acces_empr_contribution_scenario, $sub, $id, $id_empr, $nb_per_page, $iframe, $opac_duration_session_auth;

if (!$opac_contribution_area_activate || !$allow_contribution) {
	print $msg['empr_contribution_area_unauthorized'];
	return false;
}

require_once($class_path."/contribution_area/contribution_area.class.php");
require_once($class_path."/contribution_area/contribution_area_scenario.class.php");
require_once($class_path."/contribution_area/contribution_area_attachment.class.php");
require_once($class_path."/contribution_area/contribution_area_form.class.php");
require_once($class_path."/rdf_entities_conversion/rdf_entities_converter_controller.class.php");
require_once($class_path."/onto/common/onto_common_uri.class.php");

if (($gestion_acces_active == 1) && (($gestion_acces_empr_contribution_area == 1) || ($gestion_acces_empr_contribution_scenario == 1))) {
	$ac = new acces();
	if ($gestion_acces_empr_contribution_area == 1) {
		$dom_4 = $ac->setDomain(4);
	}
	if ($gestion_acces_empr_contribution_scenario == 1) {
		$dom_5 = $ac->setDomain(5);
	}
}

if ($sub == 'area') {
	if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'],$id, 4)) {
		print $msg['empr_contribution_area_unauthorized'];
		return false;
	}
	$contribution = new contribution_area($id);
	$start_scenarios = $contribution->get_start_scenarios();
	if (count($start_scenarios) == 1) {
		// S'il n'y a qu'un seul scénario dans l'espace, on l'affiche directement
		$sub = 'scenario';
		$scenario = $start_scenarios[0]['id'];

		$contribution_url = "./index.php?lvl=contribution_area&sub=".$sub."&id=".$id."&scenario=".$scenario;
		
		print '<script>
					if (typeof(window.history.replaceState) == "function") {
						window.history.replaceState("","","'.$contribution_url.'");
					} else {
						window.location = "'.$contribution_url.'";
					}
			</script>';
	}
}
if ($sub == 'scenario') {
	if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'], $id, 4)) {
		print $msg['empr_contribution_area_unauthorized'];
		return false;
	}
	if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id('http://www.pmbservices.fr/ca/Scenario#'.$scenario), 4)) {
		print $msg['empr_contribution_area_unauthorized'];
		return false;
	}
 	$contribution_area_scenario = new contribution_area_scenario($scenario,$id);
	$scenario_forms = $contribution_area_scenario->get_forms();
	if (count($scenario_forms) == 1) {
		// S'il n'y a qu'un seul formulaire dans le scénario, on l'affiche directement
		$sub = $scenario_forms[0]['entityType'];
		$area_id = $id;
		$form_id = $scenario_forms[0]['formId'];
		$form_uri = $scenario_forms[0]['id'];
		$id = 0;
		
		$contribution_url = "./index.php?lvl=contribution_area&sub=".$sub."&area_id=".$area_id."&scenario=".$scenario."&form_id=".$form_id."&form_uri=".$form_uri."&id=".$id;
		
		print '<script>
					if (typeof(window.history.replaceState) == "function") {
						window.history.replaceState("","","'.$contribution_url.'");
					} else {
						window.location = "'.$contribution_url.'";
					}
			</script>';
	}
}

if ($id_empr) {
	switch ($sub) {
		case 'area' :
			print $contribution->render();
			break;
		case 'scenario' :
			print $contribution_area_scenario->render();
			break;
		case 'attachment' :
		    global $attachment, $area_id, $entity_type;
		    $contribution_area_attachment = new contribution_area_attachment($attachment,$area_id);
		    if (!empty($entity_type)) {
		        $contribution_area_attachment->set_entity_type($entity_type);
		    }
		    $contribution_area_attachment->get_scenarios();
		    print $contribution_area_attachment->render();
			break;
		case 'convert' :
		    print contribution_area_form::get_form_entity_convert();
			break;
		case 'edit_contribution' :
		case 'scenario_child' :
		    global $sub_form, $entity_type, $entity;
		    
		    $edit_entity = false;
		    if (!empty($entity) && $entity) {
		        // on modifie une entité du fonds
		        $edit_entity = true;
		    }
		    
         	$contribution_area_scenario = new contribution_area_scenario($scenario,$area_id);
         	$scenario_forms = $contribution_area_scenario->get_forms($edit_entity);
        	if (count($scenario_forms) > 1 && 'scenario_child' == $sub) {
    		    print $contribution_area_scenario->sub_render();
    			break;
        	}
        	
        	// On a qu'un seul formulaire on passe dans le default.
        	$area_id = $scenario_forms[0]['area_id'];
        	$form_id = $scenario_forms[0]['formId'];
        	$form_uri = $scenario_forms[0]['id'];
        	
        	if ($edit_entity) {
        	    $sub = $entity_type;
        	} else {
        		$sub = $scenario_forms[0]['entityType'];
        	}
        	$sub_form = 1;
        	
		default :
		    global $lvl_redirect, $action, $unauthorized_ids, $id_contrib;
		    $contribution_area_store = new contribution_area_store();
			if(!isset($id_contrib)) {
				$id_contrib = $id;
			}

		    // Si on a aucun id on fait la redirection
		    if (empty($id_contrib) && in_array($action, array('push', 'delete'))) {
		        echo $msg['empr_contribution_area_unauthorized'];
		        echo '<script>
                        window.location = "./empr.php?tab=contribution_area&lvl='.(!empty($lvl_redirect) ? $lvl_redirect : 'contribution_area_list') .'"   
                      </script>';
		        break;
		    }
		    
		    $ids = explode(',', $id_contrib);
		    $count_ids = count($ids);
		    foreach ($ids as $key => $id) {
		        $id = intval($id);
		        
    	        // Si on a un identifiant pmb on interdit la modification de l'entitée
    		    if ($contribution_area_store->get_pmb_identifier_from_uri_id($id) != 0) {
    		        $id = 0;
    		    }
    		    
    		    $uri = onto_common_uri::get_uri($id);
    		    
    		    if ($action == "delete" && empty($uri) && $id != 0) {
    		        if ($count_ids > 1) {
    		            continue;
    		        }else{
    		            print "<script>
                            window.location = './empr.php?tab=contribution_area&lvl=".(!empty($lvl_redirect) ? $lvl_redirect : 'contribution_area_list') ."&check_ids= ".(!empty($unauthorized_ids) ? $unauthorized_ids : '')."'
                           </script>";
    		        }
    		    }
    		    
    		    $infos = $contribution_area_store->get_properties_from_uri($uri);
    			$params = new onto_param(array(
					'base_resource' => 'index.php',
					'lvl' => 'contribution_area',
					'sub' => '',
					'action' => 'edit',
					'page' => '1',
					'nb_per_page' => (isset($nb_per_page) ? $nb_per_page : 20),
					'id' => $id,
					'area_id' => (!empty($infos['area']) ? $infos['area'] : ""),
					'parent_id' => '',
					'form_id' => $form_id ?? $infos['form_id'],
					'form_uri' => '',
    				'item_uri' => '',
    			));
    			
    			if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'], $params->area_id, 4)) {
    				print $msg['empr_contribution_area_unauthorized'];
    				return false;
    			}
    			
    			$form =  contribution_area_form::get_contribution_area_form($params->sub,$params->form_id,$params->area_id,$params->form_uri);		
    			$onto_store = contribution_area_store::get_formstore($form_id ?? $infos['form_id'], $form->get_active_properties());
    			
    			//chargement de l'ontologie dans son store
    			$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
    			onto_parametres_perso::load_in_store($onto_store, $reset);
    			
    			$onto_ui = new onto_ui("", $onto_store, array(), "arc2", contribution_area_store::DATASTORE_CONFIG, contribution_area_store::CONTRIBUTION_NAMESPACE,'http://www.w3.org/2000/01/rdf-schema#label',$params);
    	
    			$last_item = false;
    			if ($key == $count_ids-1) {
        			$last_item = true;
    			}
    			
    			$onto_ui->proceed($last_item);
		    }
			break;
			
	}
} else {
	if ($iframe) {
		print '{ "session_expired" : "'.sprintf($msg['session_expired'], round($opac_duration_session_auth / 60)).'"}';
	} else {
		print sprintf($msg['session_expired'], round($opac_duration_session_auth / 60));
	}						 
}