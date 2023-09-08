<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_filter.class.php,v 1.5 2017-07-12 15:15:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class editions_state_filter {
	public $elem;
	public $value;
	public $op;
	
	public function __construct($elem,$params = array()){
		$this->elem = $elem;
		if(!$params){
			$this->get_from_form();
		}else $this->set_params($params);
	}
	
	public function get_from_form(){
		$filter_value = $this->elem['id']."_filter";
		$filter_op = $this->elem['id']."_filter_op";
		global ${$filter_value};
		global ${$filter_op};
		if(isset(${$filter_value})){
			$this->value = stripslashes(${$filter_value});
		}
		if(isset(${$filter_op})){
			$this->op =${$filter_op};
		}
	}
	
	public function get_form(){
	}
	
	public function get_params(){
		if(isset($this->value) || isset($this->op)){
			return array(
					'op' => $this->op,
					'value' => $this->value
			);
		}else{
			return false;
		}
	}
	
	protected function set_params($params){
		$this->op = $params['op'];
		$this->value = $params['value'];
	}
	public function get_sql_filter(){
		$sql_filter = "";
		if($this->op && $this->value){
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