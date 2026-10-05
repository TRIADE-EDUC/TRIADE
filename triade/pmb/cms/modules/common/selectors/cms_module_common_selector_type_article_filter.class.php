<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_type_article_filter.class.php,v 1.1 2012/12/17 10:35:14 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_type_article_filter extends cms_module_common_selector_type_editorial{	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->cms_module_common_selector_type_editorial_type="article";
	}

	protected function get_sub_selectors(){
		return array();
	}
	
	public function execute_ajax(){
		global $id_type;		
		$response = array();
		$fields = new cms_editorial_parametres_perso($id_type);		
		$select="
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['cms_module_common_selector_type_editorial_fields_label'])."</label>
			</div>
			<div class='colonne-suite'> 
				<select name='".$this->get_form_value_name("select_field")."' >";	
		$select.= $fields->get_selector_options($this->parameters["type_editorial_field"]);
		$select.= "
				</select>
			</div>
		</div>";	
		$response['content'] = $select;
		$response['content-type'] = 'text/html'; 
		return $response;
	}
	
}