<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authpersos.class.php,v 1.10.14.1 2024/09/13 14:00:12 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path . "/vedette/vedette_element.class.php");
require_once($class_path . "/authperso.class.php");

class vedette_authpersos extends vedette_element
{

	protected $type = TYPE_AUTHPERSO;

	public function __construct($type, $id, $isbd = "", $params = array())
	{
		$this->entity = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['num_object' => $id, 'type_object' => AUT_TABLE_AUTHPERSO]);
		if (empty($params['id_authority'])) {
			$params['id_authority'] = $this->entity->get_object_instance()->id;
		}

		if (empty($params['label'])) {
			$params['label'] = $this->entity->get_object_instance()->info['authperso']['name'];
		}

		if (empty($params['authperso_name'])) {
			$params['authperso_name'] = $this->entity->get_type_label();
		}
		parent::__construct($type, $id, $isbd, $params);
	}

	public function set_vedette_element_from_database()
	{
		$this->entity = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_AUTHPERSO]);
		$this->isbd = $this->entity->get_object_instance()->get_isbd($this->id);
	}

	public function get_link_see()
	{
		return str_replace("!!type!!", "authperso", $this->get_generic_link());
	}
}
