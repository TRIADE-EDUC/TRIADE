<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_empr_infos.class.php,v 1.3 2023/02/16 13:50:55 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_empr_infos extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){

	    if (!isset($this->parameters['val_choice'])) {
	        $this->parameters['val_choice'] = "0";
	    }

		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("val_choice")."'>".$this->format_text($this->msg['cms_module_common_selector_empr_infos_val_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='".$this->get_form_value_name("val_choice")."' onchange='cms_".$this->get_form_value_name("get_values")."(this.value)'>
						<option value='0'>".$this->format_text($this->msg['cms_module_common_selector_empr_infos_val'])."</option>
						<option value='statut' ".($this->parameters['val_choice'] == "statut" ? "selected='selected'": "").">".$this->format_text($this->msg['cms_module_common_selector_empr_infos_statut'])."</option>
						<option value='categ'".($this->parameters['val_choice'] == "categ" ? "selected='selected'": "").">".$this->format_text($this->msg['cms_module_common_selector_empr_infos_categ'])."</option>
						<option value='codestat'".($this->parameters['val_choice'] == "codestat" ? "selected='selected'": "").">".$this->format_text($this->msg['cms_module_common_selector_empr_infos_codestat'])."</option>
					</select>
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	

	public function save_form(){
		$this->parameters['val_choice'] = $this->get_value_from_form("val_choice");
		return parent::save_form();
	}
	
	public function get_value(){
		if(!$this->value){
			$this->value = "empty";
			if($_SESSION['id_empr_session']){
				switch($this->parameters['val_choice']){
					case "statut" :
						$query = "select empr_statut as val from empr where id_empr = ".$_SESSION['id_empr_session'];
						break;
					case "categ" :
						$query = "select empr_codestat as val from empr where id_empr = ".$_SESSION['id_empr_session'];
						break;
					case "codestat" :
						$query = "select empr_categ as val from empr where id_empr = ".$_SESSION['id_empr_session'];
						break;
				}
				if($query){
					$result = pmb_mysql_query($query);
					$this->value = pmb_mysql_result($result,0,0);
				}
			}
		}
		return $this->value;
	}
}