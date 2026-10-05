<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_article_filter.class.php,v 1.2 2017/07/04 08:57:50 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_article_filter extends cms_module_common_selector{	
	protected $generic_type;
	
	public function __construct($id=0){
		parent::__construct($id);
		$query = 'select id_editorial_type from cms_editorial_types where editorial_type_element = "article_generic"';
		$result = pmb_mysql_query($query);
		$this->generic_type = pmb_mysql_result($result,0,0);
	}

	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div id='type_editorial_fields'>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_selector_field_choise'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='".$this->get_form_value_name("select_field")."' >";
		$fields = new cms_editorial_parametres_perso($this->generic_type);
		$form.= $fields->get_selector_options($this->parameters["type_editorial_field"]);
		$form.= "
						</select>
					</div>
				</div>
			</div>";
		return $form;
	
	}

	public function save_form(){
		$this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
		$this->parameters["type_editorial"] = 0;
		return parent::save_form();
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){		
			$this->value = array(
					'type' => 0,
					'field' =>$this->parameters['type_editorial_field']
			);
		}
		return $this->value;
	}		
}