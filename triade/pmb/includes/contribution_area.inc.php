<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.1 2017-09-13 12:38:33 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

require_once($class_path."/contribution_area/contribution_area.class.php");
require_once($class_path."/contribution_area/contribution_area_scenario.class.php");
require_once($class_path."/contribution_area/contribution_area_form.class.php");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

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
$onto_ui->proceed();
