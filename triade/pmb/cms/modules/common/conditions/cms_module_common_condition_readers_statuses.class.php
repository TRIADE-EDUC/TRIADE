<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_readers_statuses.class.php,v 1.2 2019/02/25 14:40:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_readers_statuses extends cms_module_common_condition{
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_readers_statuses",
		);
	}
	
	public function check_condition(){
		global $empr_statut;
	
		$selector = $this->get_selected_selector();
		if(is_object($selector)) {
			$values = $selector->get_value();
			//on regarde si le lecteur est autorisé à accéder aux informations de ce cadre...
			if(is_array($values)){
				foreach($values as $value){
					if($empr_statut == $value){
						return true;
					}
				}
			}
		}
		return false;
	}
}