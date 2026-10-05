<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_piwik_selector_piwik_server.class.php,v 1.3 2016/09/20 10:25:42 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_piwik_selector_piwik_server extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_piwik_selector_piwik_server'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("menu");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$menus = $this->get_server_list();
		
		$select = "
					<select name='".$this->get_form_value_name("menu")."'>";
		foreach($menus as $key => $name){
			$select.="
						<option value='".$key."' ".($this->parameters == $key ? "selected='selected'" : "").">".$this->format_text($name)."</option>";
		}
		$select.= "
					</select>";
		return $select;
	}	
	
	protected function get_server_list(){
		$menus = array();
		$query = "select managed_module_box from cms_managed_modules where managed_module_name = '".addslashes($this->module_class_name)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$box = pmb_mysql_result($result,0,0);
			$infos =unserialize($box);
			foreach($infos['module']['servers'] as $key => $values){
				$menus[$key] = $values['name'];
			}
		}
		return $menus;
	}
	
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}