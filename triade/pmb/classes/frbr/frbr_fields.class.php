<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_fields.class.php,v 1.17.4.1 2025/01/28 15:28:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_fields extends fields
{
	protected function get_id_authperso()
	{
		if (!empty($this->details["authperso_id"])) {
			return $this->details["authperso_id"];
		}
		global $num_page, $object_id, $elem, $element;
		if (isset($object_id)) {
			if (!is_object($element)) {
				$element = new $elem($object_id);
			}
			$object = $element;
			if (method_exists($object, "get_num_datanode")) {
				$object = new frbr_entity_authperso_datanode($element->get_num_datanode());
			}
			if (!empty($object->get_datasource()['data']->authperso_id)) {
				return $object->get_datasource()['data']->authperso_id;
			}
		}
		if (!empty($num_page) && intval($num_page)) {
			$frbr_page = new frbr_page($num_page);
			return $frbr_page->get_parameter_value("authperso");
		}
		return 0;
	}


	protected function get_authority_id_from_data($data, $type)
	{
		$values = array();
		if (is_array($data) && $type) {
			foreach ($data as $id) {
				$values[] = authority::get_authority_id_from_entity($id, $type);
			}
		}
		return $values;
	}
}
