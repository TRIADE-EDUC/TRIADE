<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities.inc.php,v 1.4 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id, $action, $from;

require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/entities.class.php");
require_once($class_path."/authorities_collection.class.php");

if(!isset($id)) $id = 0; else $id = intval($id);

$entity_id = 0;
$entity_label = '';

$get_type = str_replace('get_', '', $action);
$query = entities::get_query_from_entity_linked($id, $get_type, $from);
if($query) {
	$result = pmb_mysql_query($query);
	if($result) {
		$entity_id = pmb_mysql_result($result, 0, 0);
		switch($action){
			case 'get_publisher':
				$authority = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $entity_id);
				$entity_label = $authority->get_isbd();
				break;
			case 'get_collection':
				$authority = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $entity_id);
				$entity_label = $authority->get_isbd();
				break;
			case 'get_sub_collection':
				$authority = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $entity_id);
				$entity_label = $authority->get_isbd();
				break;
		}
	}
}
print encoding_normalize::json_encode(array('entity_id' => $entity_id, 'entity_label' => $entity_label));