<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_authentificated.class.php,v 1.7 2016/09/20 10:25:42 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_authentificated extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_authentificated"
		);
	}
	
	public function check_condition(){
		$selector = $this->get_selected_selector();
		$value = $selector->get_value();
		//si vrai, alors seulement ce qui est authentifié...
		if(($value && ($_SESSION['id_empr_session'])) || (!$value && !$_SESSION['id_empr_session'])){
			return true;
		}else{
			return false;
		}
	}

	//fonction qui détermine si un cadre utilisant cette condition peut être caché!
	public static function use_cache(){
		return false;
	}
}