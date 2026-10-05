<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.12 2022/07/07 14:49:19 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $pmb_contribution_area_activate, $class_path, $action, $params, $id, $form_id, $type, $include_path;

// functions particulières à ce module
if (!$pmb_contribution_area_activate) {
	die();
}


require_once($class_path . '/contribution_area/contribution_area_form.class.php');

switch($action) {
	case "save" :
	case "push" :
	case "delete" :
	case "edit" :
		$params = new onto_param(array(
				'base_resource' => 'index.php',
				'lvl' => 'contribution_area',
				'sub' => '',
				'action' => 'edit',
				'page' => '1',
				'nb_per_page' => 0,
				'id' => $id,
				'area_id' => '',
				'parent_id' => '',
				'form_id' => '',
				'form_uri' => '',
				'item_uri' => '',
		));
		
		$form =  contribution_area_form::get_contribution_area_form($params->sub,$params->form_id,$params->area_id,$params->form_uri);
		$onto_store = contribution_area_store::get_formstore($form_id, $form->get_active_properties());
		
		//chargement de l'ontologie dans son store
		$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
		onto_parametres_perso::load_in_store($onto_store, $reset);
			
		$onto_ui = new onto_ui(
			"", 
			$onto_store, 
			array(), 
			"arc2", 
			contribution_area_store::DATASTORE_CONFIG,
			contribution_area_store::CONTRIBUTION_NAMESPACE,
			'http://www.w3.org/2000/01/rdf-schema#label',
			$params
		);
		return $onto_ui->proceed();
		break;
	case "list" :
	default:
        if(!isset($applied_sort)){
            $applied_sort = array();
        }
        $contributions_ui = new list_contributions_ui(array(), array(), $applied_sort);
        $contributions_ui->set_applied_sort_from_form();
	    
	    switch($dest) {
	        case "TABLEAU":
	            $contributions_ui->get_display_spreadsheet_list();
	            break;
	        case "TABLEAUHTML":
	            print $contributions_ui->get_display_html_list();
	            break;
	        default:
	            print $contributions_ui->get_display_list();
	            break;
	    }
		break;
}


