<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_selector_last_read.class.php,v 1.1 2015-04-16 12:14:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_selector_last_read extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}

	public function save_form(){
		$this->parameters = "tab_result_read";
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$this->value = $_SESSION[$this->parameters];
		}
		return $this->value;
	}
}