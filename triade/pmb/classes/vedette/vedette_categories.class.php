<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_categories.class.php,v 1.6 2018-12-04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/category.class.php");

class vedette_categories extends vedette_element{
	
	protected $type = TYPE_CATEGORY;
	
	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_CATEG);
		$this->isbd = $this->entity->get_object_instance()->libelle;
	}
	public function get_link_see(){
		return str_replace("!!type!!", "category",$this->get_generic_link());
	}
}
