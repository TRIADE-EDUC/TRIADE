<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_auth.class.php,v 1.1 2023/02/24 10:40:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_auth extends editions_state_filter {

	public function __construct($elem,$params=array()){
		parent::__construct($elem,$params);
	}
	
	public function get_from_form(){
		$filter_value = $this->elem['id']."_filter";
		$filter_op = $this->elem['id']."_filter_op";
		global ${$filter_value};
		global ${$filter_op};
		if(!empty(${$filter_value})){
			$this->value = array();
			foreach (${$filter_value} as $value) {
				$this->value[] = $value['id'];
			}
		}
		if(isset(${$filter_op})){
			$this->op =${$filter_op};
		}
	}
	
	protected function get_inherited_form(){
		$field = $this->elem['value_object'];
		$authority_type = $field["OPTIONS"][0]['DATA_TYPE'][0]['value'];
		switch($field["OPTIONS"][0]["DATA_TYPE"]["0"]["value"]) {
			case 2:
				//Pour n'appeler que le thésaurus choisi en champ perso
				if(isset($field["OPTIONS"][0]["ID_THES"]["0"]["value"])){
					$id_thes_unique=$field["OPTIONS"][0]["ID_THES"]["0"]["value"];
					$att_id_filter = $field["OPTIONS"][0]["ID_THES"]["0"]["value"];
				} else {
					$id_thes_unique = 0;
					$att_id_filter = '';
				}
				templates::init_completion_attributes(array(
						array('name' => 'att_id_filter', 'value' => $att_id_filter),
				));
				templates::init_selection_attributes(array(
						array('name' => 'id_thes_unique', 'value' => $id_thes_unique),
						
				));
				break;
			case 9:
				$concept_schemes = [];
				if(isset($field['OPTIONS'][0]['ID_SCHEME_CONCEP']))
					for($i=0 ; $i<count($field['OPTIONS'][0]['ID_SCHEME_CONCEP']) ; $i++){
						$concept_schemes[] = $field['OPTIONS'][0]['ID_SCHEME_CONCEP'][$i]['value'];
				}
				
				//autocomplétion
				if(isset($concept_schemes[0]) && $concept_schemes[0] != -1){
					$param1 = implode(',',$concept_schemes);
				} else {
					$param1 = '';
				}
				templates::init_completion_attributes(array(
						array('name' => 'att_id_filter', 'value' => "http://www.w3.org/2004/02/skos/core#Concept"),
						array('name' => 'param1', 'value' => $param1),
				));
				
				//sélecteurs
				if(isset($concept_schemes[0]) && $concept_schemes[0] != -1){
					$return_concept_id = '1';
					$unique_scheme = '1';
					$concept_scheme = implode(',',$concept_schemes);
				} else {
					$return_concept_id = '';
					$unique_scheme = '';
					$concept_scheme = '';
				}
				templates::init_selection_attributes(array(
						array('name' => 'element', 'value' => 'concept'),
						array('name' => 'param1', 'value' => $field["NAME"]),
						array('name' => 'param2', 'value' => "f_".$field["NAME"]),
						array('name' => 'return_concept_id', 'value' => $return_concept_id),
						array('name' => 'unique_scheme', 'value' => $unique_scheme),
						array('name' => 'concept_scheme', 'value' => $concept_scheme),
				));
				break;
		}
		$elements = array();
		if(is_array($this->value)){
			foreach ($this->value as $value) {
				$elements[] = array(
						'id' => $value,
						'name' => get_authority_isbd_from_field($this->elem['value_object'], $value)
				);
			}
		}
		$selection_parameters = get_authority_selection_parameters($authority_type);
		$completion = $selection_parameters['completion'];
		return "
		<div class='colonne3'>
			<select name='".$this->elem['id']."_filter_op'>
				<option value='='".($this->op == "=" ? " selected='selected'" : "").">=</option>
			</select>
		</div>
		<div class='colonne_suite'>
			".templates::get_display_elements_completion_field($elements, 'editions_state_form_show', $this->elem['id']."_filter", $this->elem['id']."_filter_id", $completion)."
		</div>";
	}
	
	public function get_form($draggable=false){
		global $base_path;
		$form = parent::get_form($draggable);
		$form .="
			<script type='text/javascript' src='".$base_path."/javascript/ajax.js'></script>
			<script type='text/javascript'>
				ajax_parse_dom();
			</script>
		";
		return $form;
	}
	
	public static function int_caster(&$value){
		return intval($value);
	}
	
	public function get_sql_filter(){
		$sql_filter = "";
		if($this->op && is_array($this->value) && count($this->value)){
			if($this->elem['field_join']){
				$champ=$this->elem['field_join'];
			}elseif($this->elem['field_alias']){
				$champ=$this->elem['field_alias'];
			}else{
				$champ=$this->elem['field'];
			}
			if($this->elem['type'] == "text"){
				$liste_filter="('".implode("','",$this->value)."')";
			}elseif($this->elem['type'] == "integer"){
				array_walk($this->value, 'static::int_caster');
				$liste_filter="(".implode(",",$this->value).")";
			}else{
				$liste_filter="(".implode(",",$this->value).")";
			}
			$sql_filter = $champ." ".$this->op." ".$liste_filter;
			if($this->elem['authorized_null']){
				$sql_filter="((".$sql_filter.") OR (".$champ." IS NULL))";
			}
		}
		return $sql_filter;
	} 
}