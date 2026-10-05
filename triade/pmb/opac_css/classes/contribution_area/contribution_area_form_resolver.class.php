<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_form_resolver.class.php,v 1.1 2019/01/07 11:39:09 apetithomme Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($class_path.'/contribution_area/contribution_area_store.class.php');

/**
 * class contribution_area_form_resolver
 * Classe de résolution de formulaire à utiliser en contribution
 */
class contribution_area_form_resolver {
	
	public static function get_contribution_forms_from_entity_type($entity_type) {
		$contribution_area_store = new contribution_area_store();
		$store = $contribution_area_store->get_graphstore();
		$store->query('select * where {
			?form rdf:type ca:Form .
			?form pmb:entity "'.$entity_type.'" .
			?form ca:eltId ?form_id .
			?attachment ca:attachmentDest ?form .
			?attachment ca:inArea ?area .
			?attachment ca:attachmentSource ?scenario .
		} limit 10');
		$results = $store->get_result();
		if (empty($results)) {
			return array();
		}
		$forms = array();
		foreach ($results as $result) {
			$forms[] = array(
					'type' => $entity_type,
					'area_uri' => $result->area,
					'scenario_uri' => $result->scenario,
					'form_uri' => $result->form,
					'form_id' => $result->form_id
			);
		}
		return $forms;
	}
}