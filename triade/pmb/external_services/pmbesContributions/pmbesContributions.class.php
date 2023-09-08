<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesContributions.class.php,v 1.4 2017-06-27 12:50:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_caches.class.php");
require_once($class_path."/encoding_normalize.class.php");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("rdf_entities_integration", true);

if (!isset($msg)) {
	//Allons chercher les messages
	include_once($class_path."/XMLlist.class.php");
	$messages = new XMLlist($include_path."/messages/".$lang.".xml", 0);
	$messages->analyser();
	$msg = $messages->table;
}

class pmbesContributions extends external_services_api_class{
	public $error=false;		//Y-a-t-il eu une erreur
	public $error_message="";	//Message correspondant à l'erreur
	
	function form_general_config() {
		return false;
	}
	
	public function integrate_entity($uri) {
		$config = array(
				'store_name' => 'contribution_area_datastore'
		);
		$rdf_entities_integrator = new rdf_entities_integrator(new rdf_entities_store_arc2($config));
		$result = $rdf_entities_integrator->integrate_entity($uri);

		return encoding_normalize::utf8_normalize($result);
	}
}
