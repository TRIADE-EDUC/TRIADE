<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda_selector_calendars.class.php,v 1.5 2016-12-07 08:46:23 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda_selector_calendars extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		if(!$this->parameters) $this->parameters = array();
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_agenda_selector_calendars_calendar'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("calendars");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$calendars = $this->get_calendars_list();
		
		$select = "
					<select name='".$this->get_form_value_name("calendars")."[]' multiple='yes'>";
		foreach($calendars as $key => $name){
			$select.="
						<option value='".$key."' ".(in_array($key,$this->parameters) ? "selected='selected'" : "").">".$this->format_text($name)."</option>";
		}
		$select.= "
					</select>";
		return $select;
	}	
	
	protected function get_calendars_list(){
		$menus = array();
		$query = "select managed_module_box from cms_managed_modules where managed_module_name = '".addslashes($this->module_class_name)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$box = pmb_mysql_result($result,0,0);
			$infos =unserialize($box);
			if (is_array($infos['module']['calendars'])) {
				foreach($infos['module']['calendars'] as $key => $values){
					$menus[$key] = $values['name'];
				}
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