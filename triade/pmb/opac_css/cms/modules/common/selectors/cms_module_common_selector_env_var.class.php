<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_env_var.class.php,v 1.10 2017-07-12 15:15:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_env_var extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_env_var'>".$this->format_text($this->msg['cms_module_common_selector_env_var'])."</label>
				</div>
				<div class='colonne-suite'>";
		if(isset($this->cms_build_env['page']) && is_array($this->cms_build_env['page'])){
			$form.="
					<select name='".$this->get_form_value_name("env_var")."'>";
			foreach($this->cms_build_env['page'] as $var){
				$form.=	"
						<option value='".$var['name']."' ".($var['name'] == $this->parameters ? "selected='selected'": "").">".$this->format_text(($var['comment']!=""? $var['comment'] : $var['name']))."</option>";
			}
			$form.="
					</select>";
		}else{
			$form.=$this->format_text($this->msg['cms_module_common_selector_env_var_no_vars']);
		}
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("env_var");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$var = $this->parameters;
			global ${$var};
			$this->value = ${$var};
		}
		return $this->value;
	}
}