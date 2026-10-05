<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_view.class.php,v 1.3 2013/09/25 07:08:36 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_view extends cms_module_common_condition {

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_view",
		);
	}
	
	public static function is_loadable_default(){
		if($_SESSION['opac_view']){
			return true;
		}
		return false;
	}
	
	public function check_condition(){
		$selector = $this->get_selected_selector();
		$values = $selector->get_value();
		//on regarde si on est sur la bonne page...
		if(in_array($_SESSION['opac_view'],$values)){
			return true;
		}
		//on est encore dans la fonction, donc la condition n'est pas vérifiée!
		return false;
	}
}