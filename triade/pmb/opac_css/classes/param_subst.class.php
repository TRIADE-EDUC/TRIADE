<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_subst.class.php,v 1.8 2021/12/28 13:30:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class param_subst {
	public $values = array();
	public $subst_param = array();
	public $type;	
	public $module;
	public $module_num;
	
	public function __construct($type, $module, $module_num) {
		$this->type = $type;// opac, acquisition...
		$this->module = $module;// opac_view
		$this->module_num = $module_num;// pour évolution...
		$this->fetch_data();
	}

	public function fetch_data() {
		$this->subst_param=array();
		$myQuery = pmb_mysql_query("SELECT * FROM param_subst where subst_type_param= '".$this->type."' and  subst_module_param= '".$this->module."' and subst_module_num= '".$this->module_num."' ");
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_assoc($myQuery))) {
				$this->subst_param[]=$r;
			}
		}
	}

	public function set_parameters() {
		foreach($this->subst_param as $param){
			$subst_param_name = $param["subst_type_param"]."_".$param["subst_sstype_param"];
			global ${$subst_param_name};
			${$subst_param_name}=$param["subst_valeur_param"];
			
		}
	}
	
	public function get_parameter_value($type_param, $sstype_param) {
		$parameter_value = '';
		foreach($this->subst_param as $param){
			if($param["subst_type_param"] == $type_param && $param["subst_sstype_param"] == $sstype_param) {
				$parameter_value = $param["subst_valeur_param"];
				break;
			}
		}
		return $parameter_value;
	}
}
?>