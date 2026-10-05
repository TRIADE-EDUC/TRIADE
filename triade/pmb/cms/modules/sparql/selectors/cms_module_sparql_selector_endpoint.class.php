<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sparql_selector_endpoint.class.php,v 1.1 2013/09/26 10:15:57 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sparql_selector_endpoint extends cms_module_common_selector {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form = parent::get_form();
		$form.= "
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("server_url")."'>".$this->format_text($this->msg['cms_module_sparql_datasource_server_url'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='".$this->get_form_value_name("server_url")."' value='".$this->format_text($this->parameters['server_url'])."' />
			</div>
		</div>";
		return $form;		
	}
	
	public function save_form(){
		$this->parameters['server_url'] = $this->get_value_from_form("server_url");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters['server_url'];
		}
		return $this->value;
	}
}