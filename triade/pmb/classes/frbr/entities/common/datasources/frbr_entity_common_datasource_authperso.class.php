<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_authperso.class.php,v 1.2 2019/09/26 13:34:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_authperso extends frbr_entity_common_datasource {
    
	public function __construct($id=0){
		$this->entity_type = 'authperso';
		parent::__construct($id); 
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		if(!empty($this->get_parameters()->sub_datasource_choice)) {
			$class_name = $this->get_parameters()->sub_datasource_choice;
			$sub_datasource = new $class_name();
			$sub_datasource->set_parameters($this->parameters);			
			if(isset($this->external_filter) && $this->external_filter) {
				$sub_datasource->set_filter($this->external_filter);
			}
			if(isset($this->external_sort) && $this->external_sort) {
				$sub_datasource->set_sort($this->external_sort);
			}
			return $sub_datasource->get_datas($datas);
		}
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function get_form() {
	    $form = parent::get_form();
	    return $form;
	}
	
	public function save_form(){
	    global $aut_link_type_parameter;
	    if (!empty($aut_link_type_parameter)) {
	        $this->parameters->link_type = $aut_link_type_parameter;
	    }
	    return parent::save_form();
	}
	
	public function set_authperso_id($id) {
	    $this->parameters->authperso_id = intval($id);
	}
	
	public function set_class_name($class_name) {
	    $this->class_name = $class_name;
	}
	
	protected function get_sub_datasource_value($sub_datasource) {
	    if (isset($this->parameters->authperso_id)) {
	        return $sub_datasource."_".$this->parameters->authperso_id;
	    }
	    return $sub_datasource;
	}
}