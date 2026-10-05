<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_category_permalink.class.php,v 1.3 2017/03/17 09:33:57 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_category_permalink extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}	

	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_category_permalink_help'>".$this->format_text($this->msg['cms_module_common_selector_category_permalink_help'])."</label>
				</div>
			</div>";
		return $form;
	}

	public function get_value(){
		global $lvl;
		
		if($lvl != 'categ_see') return 0;
		
		if(!$this->value){
			global $id;
			$this->value = $id;
		}
		return $this->value;
	}
}