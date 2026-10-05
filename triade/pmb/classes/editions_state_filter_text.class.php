<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_text.class.php,v 1.8 2023/02/23 09:00:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_text extends editions_state_filter {
		
	protected function get_inherited_form(){
		global $charset;
		
		return "
		<div class='colonne3'>
			<select name='".$this->elem['id']."_filter_op'>
				<option value='like'".($this->op == "like" ? " selected='selected'" : "").">=</option>
				<option value='content'".($this->op == "content" ? " selected='selected'" : "").">Contient</option>
				<option value='start'".($this->op == "start" ? " selected='selected'" : "").">Commence par</option>
				<option value='finish'".($this->op == "finish" ? " selected='selected'" : "").">Finit par</option>
				<option value='empty'".($this->op == "empty" ? " selected='selected'" : "").">Est vide</option>
				<option value='not_empty'".($this->op == "not_empty" ? " selected='selected'" : "").">N'est pas vide</option>
			</select>
		</div>
		<div class='colonne_suite'>
			<input type='text' name='".$this->elem['id']."_filter' value ='".htmlentities($this->value,ENT_QUOTES,$charset)."'/>
		</div>";
	}
	
	public function get_sql_filter(){
		$sql_filter = "";
		switch($this->op){
			case 'like' :
				$op = "like '!!val!!'";
				break;
			case 'content' :
				$op = "like '%!!val!!%'";
				break;
			case 'start' :
				$op = "like '!!val!!%'";
				break;
			case 'finish' :
				$op = "like '%!!val!!'";
				break;
			case 'empty' :
				$op = "like ''";
				break;
			case 'not_empty' :
			default :
				$op = "not like ''";
				break;			
		}
		if($this->op && ($this->value!= "" || $this->op == "empty" ||  $this->op == "not_empty")){
			if(!empty($this->elem['field_join'])){
				$champ=$this->elem['field_join'];
			}elseif(!empty($this->elem['field_alias'])){
				$champ=$this->elem['field_alias'];
			}else{
				$champ=$this->elem['field'];
			}
			$sql_filter = $champ." ".str_replace("!!val!!",addslashes($this->value),$op);
			if(!empty($this->elem['authorized_null'])){
				$sql_filter="((".$sql_filter.") OR (".$champ." IS NULL))";
			}
		}
		return $sql_filter;
	} 	
}