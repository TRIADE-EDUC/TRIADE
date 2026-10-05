<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_category.class.php,v 1.3 2023/10/26 07:49:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_category extends cms_module_common_selector{
	
	public function __construct($id=0)
	{
		parent::__construct($id);
	}
	
	public function get_form()
	{
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_category_id_category'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='".$this->get_form_value_name("id_category")."' value='".$this->format_text($this->parameters)."'/>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form()
	{
		$this->parameters = $this->get_value_from_form("id_category");
		return parent ::save_form();
	}
	
	/*
	 * Retourne la valeur selectionnee
	 */
	public function get_value()
	{
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}