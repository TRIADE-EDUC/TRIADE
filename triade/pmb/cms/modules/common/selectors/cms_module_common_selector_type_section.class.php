<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_type_section.class.php,v 1.3.16.1 2025/04/30 07:28:37 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_type_section extends cms_module_common_selector_type_editorial{

	public function __construct($id=0){
		parent::__construct($id);
		$this->cms_module_common_selector_type_editorial_type="section";
	}

	protected function get_sub_selectors(){
		$sub_selectors= parent::get_sub_selectors();
		$sub_selectors[]='cms_module_common_selector_global_var';
		$sub_selectors[]='cms_module_common_selector_section';
		return $sub_selectors;
	}


	public function execute_ajax(){
		global $id_type;
		$response = array();
		$fields = new cms_editorial_parametres_perso($id_type);

		$select ="
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['cms_module_common_selector_type_editorial_fields_label'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='".$this->get_form_value_name("select_field")."' >";
		$select.= $fields->get_selector_options($this->parameters["type_editorial_field"] ?? 0);
		$select.= "
				</select>
			</div>
		</div>";
		$response['content'] = $select;
		$response['content-type'] = 'text/html';
		return $response;
	}
}