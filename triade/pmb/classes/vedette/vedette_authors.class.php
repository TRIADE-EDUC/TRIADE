<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authors.class.php,v 1.8 2018/12/04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/author.class.php");

class vedette_authors extends vedette_element{
	
	protected $type = TYPE_AUTHOR;
	
	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_AUTHORS);
		$this->isbd = $this->entity->get_isbd();
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "author",$this->get_generic_link());
	}
}
