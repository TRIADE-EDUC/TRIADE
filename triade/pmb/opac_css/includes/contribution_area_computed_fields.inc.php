<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_computed_fields.inc.php,v 1.2 2019/01/17 08:15:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	die();
}

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/contribution_area/computed_fields/computed_field.class.php');
require_once($class_path.'/rdf_entities_conversion/rdf_entities_converter_controller.class.php');
require_once($class_path.'/contribution_area/contribution_area_store.class.php');

$return = array();

switch ($what) {
	case 'get_entity_data':
		$return = array();
		if (!$entity_id || !$entity_type) {
			break;
		}
		$assertions = rdf_entities_converter_controller::convert($entity_id, $entity_type);
		$contribution_area_store = new contribution_area_store();
		$ontology = $contribution_area_store->get_ontology();
		$pmb_namespace = 'http://www.pmbservices.fr/ontology#';
		foreach ($assertions as $assertion) {
			$property = $ontology->get_property($pmb_namespace.$entity_type, $assertion->get_predicate());
			$value = array(
					'value' => $assertion->get_object(),
					'display_label' => ''
			);
			$object_properties = $assertion->get_object_properties();
			if ($object_properties['type'] == 'uri') {
				$value['display_label'] = $object_properties['display_label'];
			}
			$return[$property->pmb_name] = $value;
		}
		break;
	case 'get_fields':
	default:
		$computed_fields = computed_field::get_area_computed_fields($area_id);
		foreach ($computed_fields as $computed_field) {
			$return[] = $computed_field->get_data();
		}
		break;
}

print encoding_normalize::json_encode($return);