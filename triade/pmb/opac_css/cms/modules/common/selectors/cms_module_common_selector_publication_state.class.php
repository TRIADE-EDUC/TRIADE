<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_publication_state.class.php,v 1.2 2021/02/18 16:31:56 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_publication_state extends cms_module_common_selector {
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_form() {
		$form = parent::get_form();
		$form .= "
			<div class='row'>
                <div class='colonne3'>
                    &nbsp;
                </div>
                <div class='colonne-suite'>
                    " . $this->gen_select() . "
                 </div>
            </div>";
		
		return $form;
	}
	
	protected function gen_select() {
		global $charset;
		//si on est en création de cadre
		if(!$this->id){
			$this->parameters['publication_states'] = array();
		}
		$select = "
		<select name='".$this->get_form_value_name("section")."[]' multiple='yes'>";	
		$publications_states = new cms_editorial_publications_states();
		$publications_states->get_publications_states();
		for($i=0 ; $i<count($publications_states->publications_states) ; $i++){
			$select .= "<option value='".$publications_states->publications_states[$i]['id']."'".(in_array($publications_states->publications_states[$i]['id'],$this->parameters['publication_states']) ? "selected='selected'" : "").">".htmlentities($publications_states->publications_states[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		$select.= "
		</select>";
		return $select;
	}	
	
	public function save_form() {
		$this->parameters["publication_states"] = $this->get_value_from_form("section");
		
		return parent::save_form();
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value() {
		if (empty($this->value)) {
			$this->value = $this->parameters['publication_states'];
		}
		
		return $this->value;
	}
}