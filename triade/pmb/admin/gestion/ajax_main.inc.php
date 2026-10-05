<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2024/01/31 13:06:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $object_type, $filters;

$is_external = false;
$temporary_variable_filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
if(!isset($temporary_variable_filters['type'])) $temporary_variable_filters['type'] = 'notices';
if('notices_externes' == $temporary_variable_filters['type']) $is_external = true;
facettes_gestion_controller::set_object_id(0);
facettes_gestion_controller::set_type($temporary_variable_filters['type']);
facettes_gestion_controller::set_is_external($is_external);
facettes_gestion_controller::proceed_ajax($object_type, 'configuration/'.$categ);