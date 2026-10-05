<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesContributions.class.php,v 1.8 2023/03/16 10:49:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $lang, $msg;
require_once($class_path."/external_services.class.php");
require_once($class_path."/external_services_caches.class.php");
require_once($class_path."/encoding_normalize.class.php");

if (!isset($msg)) {
	//Allons chercher les messages
	include_once($class_path."/XMLlist.class.php");
	$messages = new XMLlist($include_path."/messages/".$lang.".xml", 0);
	$messages->analyser();
	$msg = $messages->table;
}

class pmbesContributions extends external_services_api_class{
	
	public function integrate_entity($uri) {
		$config = array(
				'store_name' => 'contribution_area_datastore'
		);
		$store = new rdf_entities_store_arc2($config);
		$rdf_entities_integrator = new rdf_entities_integrator($store);
		$result = $rdf_entities_integrator->integrate_entity($uri);
		return encoding_normalize::utf8_normalize($result);
	}
}
