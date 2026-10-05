<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_record_doctype.class.php,v 1.1 2016/05/17 10:25:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_record_doctype extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div class='row'>";
		$form.=$this->gen_select();
		$form.="
			</div>";
		return $form;	
		
	}
	
	protected function gen_select() {
		global $charset;
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters['record_doctype'] = array();
		}
		$marc_list = new marc_list('doctype');
		$select = "
		<select name='".$this->get_form_value_name("record_doctype")."[]' multiple='yes'>";
		foreach ($marc_list->table as $code => $doctype) {
			$selected = (in_array($code, $this->parameters['record_doctype']) ? ' selected="selected"' : '');
			$select.= '
					<option value="'.$code.'"'.$selected.'>'.htmlentities($doctype, ENT_QUOTES, $charset).'</option>';
		}
		$select.= "
		</select>";
		return $select;
	}	
	
	public function save_form(){
		$this->parameters["record_doctype"] = $this->get_value_from_form("record_doctype");
		return parent::save_form();
	}
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters['record_doctype'];
		}
		return $this->value;
	}
}