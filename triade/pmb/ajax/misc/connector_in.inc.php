<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connector_in.inc.php,v 1.2 2020/11/09 08:37:10 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path;
global $source_id, $method;

require_once $class_path."/connecteurs.class.php";

if( empty($source_id) || empty($method)) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Missing parameter",
			]);
	return;
}
//on recupere le nom du connecteur
$connector_name = connecteurs::get_class_name($source_id);
if(!$connector_name) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Invoke has failed",
			]);
	return;
}

//puis l'id du connecteur
$connectors = connecteurs::get_instance();
$connector_id = 0;
$connector_path = '';
foreach ($connectors->catalog as $k=> $connector) {
	if ($connector['NAME'] == $connector_name) {
		$connector_id = $k;
		$connector_path = $connector['PATH'];
		break;
	}
}
if( !$connector_id) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Invoke has failed",
			]);
	return;
}

//on instancie le connecteur
require_once $base_path."/admin/connecteurs/in/".$connector_path."/".$connector_name.".class.php";
$connector = new $connector_name($base_path."/admin/connecteurs/in/".$connector_path);

//on verifie que la methode est autorisee
if( !in_array($method, $connector->get_ajax_allowed_methods()) ) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Method not allowed",
			]);
	return;
}
//et qu'elle existe
if( !method_exists($connector, $method)) {
	
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Method not implemented",
			]);
	
	return;
}

//on recupere les parametres de la methode par reflection
$method = new ReflectionMethod($connector, $method);
$method_parameters = $method->getParameters();

$expected_parameters = [];
if( !empty($method_parameters) ) {
	foreach($method_parameters as $method_parameter) {
		$tmp = [];
		$tmp['position'] = $method_parameter->getPosition();
		$tmp['name'] = $method_parameter->getName();
		$tmp['type'] = 'string';
		if($method_parameter->hasType()) {
			$tmp['type'] = $method_parameter->getType()->getName();
		}
		$tmp['value'] = NULL;
		if($method_parameter->isDefaultValueAvailable()) {
			$tmp['value'] = $method_parameter->getDefaultValue();
 		}
 		$expected_parameters[] = $tmp;
	}
}
unset($method_parameters);

//on verifie que tous les parametres necessaires sont bien passes et sont du bon type
//il faudra peut-être limiter sur les valeurs GET et POST
$done = true;
$call_parameters = [];
if(!empty($expected_parameters)) {
	foreach ($expected_parameters as $k=>$expected_parameter) {
		
		if( !isset(${$expected_parameter['name']}) && is_null($expected_parameter['value'])) {
			$done =false;
			continue;
		}
		if( isset(${$expected_parameter['name']}) ) {
			$check_value = settype(${$expected_parameter['name']}, $expected_parameter['type']);
			if(!$check_value) {
				$done = false;
				continue;
			} 
			$call_parameters[] = ${$expected_parameter['name']};
			continue;
		} 
		$call_parameters[] = $expected_parameter['value'];
	}
}
unset($expected_parameters);

if( !$done ) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Missing parameter",
			]);
	return;
}

//On appelle la fonction 
try {
	
	$result = $method->invokeArgs($connector, $call_parameters);
	ajax_http_send_response($result);
	
} catch(Exception $e) {
	ajax_http_send_response(
			[
					'error' => 1,
					'error_msg' => "Invoke has failed",
			]);
}


