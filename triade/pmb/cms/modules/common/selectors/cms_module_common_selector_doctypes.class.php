<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_doctypes.class.php,v 1.3.18.1.2.1 2025/04/30 12:52:10 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/marc_table.class.php");

class cms_module_common_selector_doctypes extends cms_module_common_selector{

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_doctypes'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form .= $this->gen_select();
		$form .= "
				</div>
			</div>";
		$form .= parent::get_form();
		return $form;
	}

	protected function gen_select(){
		global $tdoc;

		if (empty($tdoc)) {
		    $tdoc = new marc_list('doctype');
		}

		//si on est en création de cadre
		if(!$this->id){
			$this->parameters = array();
		}

		$select = "
					<select name='".$this->get_form_value_name("doctypes")."[]' multiple='yes'>";
		if (!is_array($tdoc->table) || !count($tdoc->table)) {
			$select .= "
						<option value='0'>".$this->format_text($this->msg['cms_module_common_selector_doctypes_no_doctype'])."</option>";
		}
		foreach ($tdoc->table as $key => $label) {
			$select .= "
						<option value='".$key."' ".(in_array($key, $this->parameters) ? "selected='selected'" : "").">".$this->format_text($label)."</option>";
		}
		$select .= "</select>";
		return $select;
	}

	public function save_form(){
		$this->parameters = array();
		if($this->get_value_from_form("doctypes")){
			$this->parameters = $this->get_value_from_form("doctypes");
		}
		return parent ::save_form();
	}

	/*
	 * Retourne la valeur sélectionnée
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}