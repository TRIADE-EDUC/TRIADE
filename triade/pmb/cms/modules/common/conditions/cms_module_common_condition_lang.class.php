<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_lang.class.php,v 1.1 2017/01/23 16:09:56 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_lang extends cms_module_common_condition {

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_lang",
		);
	}

	public function check_condition(){
		global $lang;
		
		$selector = $this->get_selected_selector();
		$values = $selector->get_value();
		//on regarde si on est sur la bonne page...
		if(in_array($lang, $values)){
			return true;
		}
		return false;
	}
}