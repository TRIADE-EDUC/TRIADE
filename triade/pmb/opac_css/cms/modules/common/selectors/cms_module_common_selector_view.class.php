<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_view.class.php,v 1.5 2021/10/22 13:27:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_view extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
		if(!$this->parameters){
			$this->parameters = array();
		}
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_view_id_view'])."</label>
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
		$this->parameters = $this->get_value_from_form('opac_view_id');
		return parent ::save_form();
	}
	
	protected function gen_select() {
		// Pour le moment, on ne regarde pas le statut de publication
		$query = "select opac_view_id, opac_view_name from opac_views order by opac_view_name asc";
		$result = pmb_mysql_query($query);
		$select = "
					<select name='".$this->get_form_value_name("opac_view_id")."[]' multiple='yes'>";
		if (pmb_mysql_num_rows($result)) {
		    // Permet de faire une condition d'affichage 
    		$select .= "<option value ='0' ".(in_array("0", $this->parameters) ? "selected" : "").">".$this->format_text($this->msg['cms_module_common_selector_view_out_sight'])."</option>";
			while ($row = pmb_mysql_fetch_object($result)) {
				$select .= "
						<option value='$row->opac_view_id' ".(in_array($row->opac_view_id, $this->parameters) ? "selected" : "").">".$this->format_text($row->opac_view_name)."</option>";
			}
		} else {
			$select .= "
						<option value ='0' selected>".$this->format_text($this->msg['cms_module_common_selector_view_no_view'])."</option>";
		}
		$select .= "
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
	
	public function get_human_description_selector(){
		$description = "";
		$query= "select opac_view_id, opac_view_name from opac_views order by opac_view_name asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if(in_array($row->opac_view_id,$this->parameters)){
					if(array_search($row->opac_view_id, $this->parameters) == 0){
						$description .= $this->format_text($row->opac_view_name);
					}else{
						$description .= ", ".$this->format_text($row->opac_view_name);
					}
				}
			}
		}	
		return $description;
	}
}