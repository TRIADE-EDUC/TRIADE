<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_search_result.class.php,v 1.1 2012/05/24 14:30:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_search_result extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_search_result_help'>".$this->format_text($this->msg['cms_module_common_selector_search_result_help'])."</label>
				</div>
			</div>";
		return $form;
	}
	
	public function get_value(){
		if(!$this->value){
			global $user_query;
			$this->value = $user_query;
		}
		return $this->value;
	}
}