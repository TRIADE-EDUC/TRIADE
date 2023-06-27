<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_integer.class.php,v 1.6 2014-10-28 18:03:42 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_integer extends editions_state_filter {

	public function __construct($elem,$params=array()){
		parent::__construct($elem,$params);
		//on s'assure d'avoir un nombre !
		if($this->value !== ""){
			$this->value+=0;
		}
	}
	
	public function get_form($draggable=false){
		global $msg,$charset;
		$form= "
			<div class='row'>&nbsp;</div>
			<div class='row' >";
		if($draggable){
			$form.= "<div class='colonne3' id='filters_".$this->elem['id']."_drag' draggable='yes' dragtype='editionsstatefilterslist'>";
		}else{
			$form.= "<div class='colonne3' id='filters_".$this->elem['id']."' >";
		}
		$form.= "
					
				<label style='cursor: pointer;'>".htmlentities($this->elem['label'],ENT_QUOTES,$charset)."</label>
				</div>
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
				</div>
			</div>";	
		return $form;
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