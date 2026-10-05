<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_resources_ui.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_acces_profiles_resources_ui extends list_acces_profiles_ui {
	
	protected $profile_type = 'res';
	
	protected function _get_query_base() {
		return $this->get_dom()->loadResourceProfiles();;
	}
	
	protected function save_object($object, $property, $value) {
		switch ($property) {
			case 'prf_use':
				$this->get_dom()->saveResourceProfile($object->prf_id, $object->prf_name, $object->prf_rule, $object->prf_hrule, $value);
				break;
			default:
				parent::save_object($object, $property, $value);
				break;
		}
	}
}