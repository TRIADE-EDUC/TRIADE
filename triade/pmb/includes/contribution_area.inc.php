<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.3 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $pmb_contribution_area_activate;
if (!$pmb_contribution_area_activate) {
	die();
}

global $class_path, $form_id, $nb_per_page_gestion;
require_once($class_path."/contribution_area/contribution_area.class.php");
require_once($class_path."/contribution_area/contribution_area_scenario.class.php");
require_once($class_path."/contribution_area/contribution_area_form.class.php");

$params = new onto_param(array(
		'base_resource' => 'index.php',
		'lvl' => 'contribution_area',
		'sub' => '',
		'action' => 'edit',
		'page' => '1',
		'nb_per_page' => $nb_per_page_gestion,
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

$onto_ui = new onto_ui("", $onto_store, array(), "arc2", contribution_area_store::DATASTORE_CONFIG, contribution_area_store::ONTOLOGY_NAMESPACE,'http://www.w3.org/2000/01/rdf-schema#label',$params);
$onto_ui->proceed();
