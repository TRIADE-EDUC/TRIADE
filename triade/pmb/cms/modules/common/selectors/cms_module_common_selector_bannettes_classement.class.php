<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_bannettes_classement.class.php,v 1.2 2021/02/12 15:50:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_bannettes_classement extends cms_module_common_selector {
	
	public function get_form() {
	    
		$form = parent::get_form();
		$form .= "<div class='row'>";
		$form .= $this->gen_select();
		$form .= "</div>";
		
		return $form;	
		
	}
	
	protected function gen_select() {
		global $charset;
		
		//si on est en création de cadre
		if(!$this->id) {
			$this->parameters['classement'] = array();
		}
		
		$select = "<select name='".$this->get_form_value_name("bannettes")."[]' multiple='yes'>";	
		
		$query = "SELECT id_classement, nom_classement FROM classements";
		$result = pmb_mysql_query($query);
		
		if(pmb_mysql_num_rows($result)){
		    while($row = pmb_mysql_fetch_object($result)){
    			$select .= "
    			<option value='".$row->id_classement."' ".(in_array($row->id_classement, $this->parameters['classement']) ? " selected='selected'" : "").">".
                    htmlentities($row->nom_classement, ENT_QUOTES, $charset).
                "</option>";
		    }
		}
		$select .= "</select>";
		
		return $select;
	}	
	
	public function save_form(){
		$this->parameters["classement"] = $this->get_value_from_form("bannettes");
		return parent::save_form();
	}
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters['classement'];
		}
		return $this->value;
	}
}