<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_selector_items_by_tags.class.php,v 1.1 2015/02/26 16:02:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_selector_items_by_tags extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	public function get_sub_selectors(){
		return array(
				"cms_module_itemslist_selector_itemstags"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if($this->parameters['sub_selector']){
			$sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
			return $sub_selector->get_value();
		} else {
			return array();
		}
	}
}