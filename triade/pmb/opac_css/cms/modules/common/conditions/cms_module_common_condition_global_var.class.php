<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_global_var.class.php,v 1.1 2013/01/02 11:07:10 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_global_var extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_global_var",
		);
	}
	
	public function check_condition(){
		$selector = $this->get_selected_selector();
		$value = $selector->get_value();
		//on regarde si on est sur la bonne page...
		if($value!== false){
			return true;
		}else{
			return false;
		}
	}
}