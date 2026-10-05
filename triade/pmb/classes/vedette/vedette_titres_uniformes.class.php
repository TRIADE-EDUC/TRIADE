<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_titres_uniformes.class.php,v 1.7 2019/02/22 15:30:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/titre_uniforme.class.php");

class vedette_titres_uniformes extends vedette_element{
	
	protected $type = TYPE_TITRE_UNIFORME;
	
	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_TITRES_UNIFORMES);
		$this->isbd = $this->entity->get_isbd();
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "titre_uniforme",$this->get_generic_link());
	}
}
