<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_list.class.php,v 1.9 2023/02/23 09:00:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_list extends editions_state_filter {
	
	public function get_from_form(){
		$filter_value = $this->elem['id']."_filter";
		$filter_op = $this->elem['id']."_filter_op";
		global ${$filter_value};
		global ${$filter_op};
		if(isset(${$filter_value})){
			$this->value = ${$filter_value};
		}
		if(isset(${$filter_op})){
			$this->op =${$filter_op};
		}
	}
	
	protected function get_inherited_form(){
		global $msg, $charset;
		
		$form = "
		<div class='colonne3'>
			<select name='".$this->elem['id']."_filter_op'>
				<option value='in'".($this->op == "in" ? " selected='selected'" : "").">=</option>
				<option value='not in'".($this->op == "not in" ? " selected='selected'" : "").">".$msg['editions_state_filter_different']."</option>
			</select>
		</div>
		<div class='colonne3'>
			<select name='".$this->elem['id']."_filter[]' multiple='yes'>";
		foreach($this->elem['value'] as $value => $label){
			$selected="";
			if(is_array($this->value) && (in_array(addslashes($value),$this->value))){
				$selected=" selected='selected' ";
			}
			$form.= "
				<option value='".htmlentities($value,ENT_QUOTES,$charset)."' $selected >".htmlentities($label,ENT_QUOTES,$charset)."</option>";
		}
		$form.= "
			</select>
		</div>";
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
			if($this->elem['type'] == "text_like"){
				if($this->op == "in"){
					$liste_filter="(".$champ." LIKE '%".implode("%' AND ".$champ." LIKE '%",$this->value)."%')";
				}else/*if($this->op == "not in")*/{
					$liste_filter="(".$champ." NOT LIKE '%".implode("%' AND ".$champ." NOT LIKE '%",$this->value)."%')";
				}
				return $liste_filter;
			}elseif($this->elem['type'] == "text"){
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