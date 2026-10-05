<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_bannette.class.php,v 1.3 2018/02/12 10:20:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_bannette extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		if (!is_array($this->parameters)) $this->parameters = array();
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_bannette_id_bannette'>".$this->format_text($this->msg['cms_module_common_selector_bannette_id_bannette'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->parameters = $this->get_value_from_form("id_bannette");
		return parent ::save_form();
	}
	
	protected function gen_select(){
		$query= "select id_bannette, nom_bannette from bannettes order by nom_bannette";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("id_bannette")."[]'>";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
						<option value='".$row->id_bannette."' ".(in_array($row->id_bannette, $this->parameters)  ? "selected='selected'" : "").">".$this->format_text($row->nom_bannette)."</option>";
			}
		}else{
			$select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_common_selector_bannette_no_bannette'])."</option>";
		}
		$select.= "
			</select>";
		return $select;
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
}