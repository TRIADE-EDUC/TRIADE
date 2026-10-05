<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter_date.class.php,v 1.7 2023/02/23 09:00:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_filter.class.php");

class editions_state_filter_date extends editions_state_filter {
	
	public function get_from_form(){
		$filter_value = $this->elem['id']."_filter";
		global ${$filter_value};
		if(isset(${$filter_value})){
			$this->value = ${$filter_value};
		}
	}
	
	protected function get_inherited_form(){
		global $msg, $charset;
		
		return "
		<div class='colonne_suite'>
			".$msg['editions_state_filter_date_start']."&nbsp;<input type='text' name='".$this->elem['id']."_filter[start]' value ='".htmlentities(stripslashes($this->value['start']),ENT_QUOTES,$charset)."'/>
				&nbsp;".$msg['editions_state_filter_date_end']."&nbsp;
				<input type='text' name='".$this->elem['id']."_filter[end]' value ='".htmlentities(stripslashes($this->value['end']),ENT_QUOTES,$charset)."'/>
		</div>";
	}
	
	public function get_form($draggable=false){
		$form = parent::get_form($draggable);
		$form .="
			<!--<script type='text/javascript'>
				function filter_date_change_form_".$this->elem['id']."(op){
				document.getElementById('filters_pret_archive_arc_debut').setAttribute('draggable', 'no');
					var div = document.getElementById('filter_date_".$this->elem['id']."');
					if(op == 'between'){
						div.style.display = 'inline';
					}else{
						div.style.display = 'none';
					}
				}
			</script>-->
		";
		return $form;
	}
	
	public function get_sql_filter(){
		$sql_filter = "";
		$start = detectFormatDate($this->value['start']);
		$end = detectFormatDate($this->value['end']);
		if(($start && $start != "0000-00-00") || ($end && $end != "0000-00-00")) {
			if($this->elem['field_join']){
				$champ=$this->elem['field_join'];
			}elseif($this->elem['field_alias']){
				$champ=$this->elem['field_alias'];
			}else{
				$champ=$this->elem['field'];
			}
			
			if($start != "0000-00-00" && $end != "0000-00-00"){
				$sql_filter = $champ." between '".$start." 00:00:00' and '".$end." 23:59:59'";
			}else if($start != "0000-00-00"){
				$sql_filter = $champ." >= '".$start." 00:00:00'";	
			}else{
				$sql_filter = $champ." <= '".$end." 23:59:59'";		
			}
			if($this->elem['authorized_null']){
				$sql_filter="((".$sql_filter.") OR (".$champ." IS NULL))";
			}
		}
		return $sql_filter;
	} 
}