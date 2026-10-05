<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_integer.class.php,v 1.8 2023/02/23 09:00:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_integer extends editions_state_filter {

	public function __construct($elem,$params=array()){
		parent::__construct($elem,$params);
		//on s'assure d'avoir un nombre !
		if($this->value !== ""){
			$this->value = intval($this->value);
		}
	}
	
	protected function get_inherited_form(){
		global $charset;
		
		return "
		<div class='colonne3'>
			<select name='".$this->elem['id']."_filter_op'>
				<option value='='".($this->op == "=" ? " selected='selected'" : "").">=</option>
				<option value='>'".($this->op == ">" ? " selected='selected'" : "").">></option>
				<option value='>='".($this->op == ">=" ? " selected='selected'" : "").">>=</option>
				<option value='<'".($this->op == "<" ? " selected='selected'" : "")."><</option>
				<option value='<='".($this->op == "<=" ? " selected='selected'" : "")."><=</option>
			</select>
		</div>
		<div class='colonne_suite'>
			<input type='text' name='".$this->elem['id']."_filter' value ='".htmlentities($this->value,ENT_QUOTES,$charset)."'/>
		</div>";
	}
	
	public function get_sql_filter(){
		$sql_filter = "";
		if($this->op && isset($this->value) && ($this->value !== "")){
			if($this->elem['field_join']){
				$champ=$this->elem['field_join'];
			}elseif($this->elem['field_alias']){
				$champ=$this->elem['field_alias'];
			}else{
				$champ=$this->elem['field'];
			}
			$sql_filter = $champ." ".$this->op." ".$this->value;
			if($this->elem['authorized_null']){
				$sql_filter="((".$sql_filter.") OR (".$champ." IS NULL))";
			}	
		}
		return $sql_filter;
	}
}