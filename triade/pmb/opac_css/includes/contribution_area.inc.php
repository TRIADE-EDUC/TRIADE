<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.13 2018-12-28 16:19:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	print $msg['empr_contribution_area_unauthorized'];
	return false;
}

require_once($class_path."/contribution_area/contribution_area.class.php");
require_once($class_path."/contribution_area/contribution_area_scenario.class.php");
require_once($class_path."/contribution_area/contribution_area_form.class.php");
require_once($class_path."/rdf_entities_conversion/rdf_entities_converter_controller.class.php");
require_once($class_path."/onto/common/onto_common_uri.class.php");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

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
		
		print '<script type="text/javascript">
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
		
		print '<script type="text/javascript">
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
		default :
			$params = new onto_param(array(
					'base_resource' => 'index.php',
					'lvl' => 'contribution_area',
					'sub' => '',
					'action' => 'edit',
					'page' => '1',
					'nb_per_page' => (isset($nb_per_page) ? $nb_per_page : 20),
					'id' => $id,
					'area_id' => '',
					'parent_id' => '',
					'form_id' => '',
					'form_uri' => '',
					'item_uri' => '',
			));
			if (isset($dom_4) && !$dom_4->getRights($_SESSION['id_empr_session'], $params->area_id, 4)) {
				print $msg['empr_contribution_area_unauthorized'];
				return false;
			}
			
			$form =  contribution_area_form::get_contribution_area_form($params->sub,$params->form_id,$params->area_id,$params->form_uri);		
			
			$onto_store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'onto_contribution_form_' . $form_id,
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0,
					'params' => $form->get_active_properties()
			);
			$data_store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'contribution_area_datastore',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0
			);
			
			$tab_namespaces = array(
					"skos"	=> "http://www.w3.org/2004/02/skos/core#",
					"dc"	=> "http://purl.org/dc/elements/1.1",
					"dct"	=> "http://purl.org/dc/terms/",
					"owl"	=> "http://www.w3.org/2002/07/owl#",
					"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
					"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
					"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
					"pmb"	=> "http://www.pmbservices.fr/ontology#"
			);
			

			$onto_store = new onto_store_arc2_extended($onto_store_config);
			$onto_store->set_namespaces($tab_namespaces);
				
			//chargement de l'ontologie dans son store
			$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
			onto_parametres_perso::load_in_store($onto_store, $reset);
			
			$onto_ui = new onto_ui("", $onto_store, array(), "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2000/01/rdf-schema#label',$params);
	// 		$this->get_linked_forms();
			$onto_ui->proceed();
			break;
			
	}
} else {
	if ($iframe) {
		print '{ "session_expired" : "'.sprintf($msg['session_expired'], round($opac_duration_session_auth / 60)).'"}';
	} else {
		print sprintf($msg['session_expired'], round($opac_duration_session_auth / 60));
	}						 
}