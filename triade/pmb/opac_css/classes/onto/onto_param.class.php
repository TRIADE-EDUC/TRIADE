<?php

/**
 * @author abacarisse
 *
 */
class onto_param extends stdClass{

	private $tab_param=array();
	
	public function __construct($tab_param=array()){
		if(empty($tab_param)){
			$this->tab_param=array('categ'=>'','sub'=>'','action'=>'','page'=>'1','nb_per_page'=>'20');
		}else{
			$this->tab_param=$tab_param;
		}
		$this->assign_globals();
	}
	
	private function assign_globals(){
		foreach($this->tab_param as $param_name=>$param_default){
			global ${$param_name};
			$this->{$param_name}=${$param_name};
			if (!isset($this->{$param_name}) || ($this->{$param_name} === "")) {
				$this->{$param_name}=$param_default;
			}
		}
	}
}
