<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_condition_lvl.class.php,v 1.9 2019-03-21 14:29:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_condition_lvl extends cms_module_common_condition{

	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_lvl",
		);
	}
	
	public static function is_loadable_default(){
		global $cms_build_info;
		if($cms_build_info['lvl'] || $cms_build_info['search_type_asked']|| $cms_build_info['input'] == "empr.php" || $cms_build_info['input'] == "askmdp.php" || $cms_build_info['input'] == "subscribe.php" ){
			return true;
		}
		return false;
	}
	
	public function get_form(){
		//si on est sur une page de type Page en création de cadre, on propose la condition pré-remplie...
		if($this->cms_build_env['lvl'] || $this->cms_build_env['search_type_asked'] || $this->cms_build_env['input'] == "empr.php" || $this->cms_build_env['input'] == "askmdp.php" || $this->cms_build_env['input'] == "subscribe.php" ){
			if(!$this->id){
				$this->parameters['selectors'][] = array(
					'id' => 0,
					'name' => "cms_module_common_selector_lvl"
				);
			}
		}
		return parent::get_form();
	}
	
	public function check_condition(){
		global $lvl;
		global $search_type_asked;
		global $mode;
		
		$selector = $this->get_selected_selector();
		$values = $selector->get_value();
		$test = array("empr","askmdp","subscribe");
		
		//on regarde si on est sur la bonne page...
		if(in_array(basename($_SERVER['SCRIPT_FILENAME'],".php"),$test) && in_array(basename($_SERVER['SCRIPT_FILENAME'],".php"),$values)){
			return true;
		}else if($search_type_asked && is_array($values) && in_array($search_type_asked,$values)){
			return true;
		// Dans le cas qui suit, on veut seulement s'assurer que la variable n'est pas en POST mais bien GET
		}else if(!isset($_GET['search_type_asked']) && is_array($values) && in_array($lvl,$values)){
			//sur la page
			if($lvl == "index" || $lvl == ""){
				if (!$search_type_asked){
					return true;
				}
			}else{
				return true;
			}
		}else if(!isset($_GET['search_type_asked']) && !empty($search_type_asked) && $search_type_asked == "simple_search"){
			if(in_array("simple_search", $values) || (!empty($mode) && in_array("simple_search_mode_".$mode, $values))) {
				return true;	
			}
		}
		//on est encore dans la fonction, donc la condition n'est pas vérifiée!
		return false;
	}
}