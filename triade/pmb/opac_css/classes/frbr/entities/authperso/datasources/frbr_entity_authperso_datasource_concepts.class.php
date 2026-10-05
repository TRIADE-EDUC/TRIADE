<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_concepts.class.php,v 1.5 2021/03/01 14:02:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_concepts extends frbr_entity_common_datasource_concept {
	
    protected $origin_type = TYPE_AUTHPERSO;
    
	public function __construct($id=0){
		$this->entity_type = 'concepts';
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
	
	public function get_sub_datasources() {
	    return array(
	        "frbr_entity_authperso_datasource_concepts_indexing",
	        "frbr_entity_authperso_datasource_concepts_used_in_custom_fields",
	    );
	}
}