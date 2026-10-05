<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2019/06/03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $name, $property_name, $instance_name;

require_once ($class_path.'/vedette/vedette_controller.class.php');

$params = array(
    'action' => $action,
    'name' => $name,
    'property_name' => $property_name,
    'instance_name' => $instance_name
);

$vedette_controller = new vedette_controller($params);
$vedette_controller->proceed_ajax();

