<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_page.class.php,v 1.6 2016/03/21 16:11:36 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_page extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_page",
		);
	}
	
	public static function is_loadable_default(){
		global $cms_build_info;
		if($cms_build_info['lvl'] == "cmspage"){
			return true;
		}
		return false;
	}
	
	public function get_form(){
		//si on est sur une page de type Page en création de cadre, on propose la condition pré-remplie...
		if($this->cms_build_env['lvl'] == "cmspage"){
			if(!$this->id){
				$this->parameters['selectors'][] = array(
					'id' => 0,
					'name' => "cms_module_common_selector_page"
				);
			}
		}
		return parent::get_form();
	}
	
	public function check_condition(){
		global $lvl,$pageid;
		
		$selector = $this->get_selected_selector();
		$values = $selector->get_value();
		//on regarde si on est sur la bonne page...
		if(is_array($values)){
			foreach($values as $value){
				if($lvl == "cmspage" && $pageid == $value){
					return true;
				}
			}	
		}
		return false;
		
	}
}