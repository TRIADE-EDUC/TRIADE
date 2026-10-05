<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_used_in_qualification.class.php,v 1.2 2021/01/28 14:41:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_used_in_qualification extends frbr_entity_common_datasource {
    
    protected $vedette_type = [];
    
    public function __construct($id=0){
        parent::__construct($id);
    }
    
    public function get_sub_datasources(){
        if(static::class != 'frbr_entity_common_datasource_used_in_qualification') {
            return array();
        }
        return array(
            "frbr_entity_common_datasource_used_in_work_qualification",
            "frbr_entity_common_datasource_used_in_record_qualification",
        );
    }
    public function get_datas($datas = array()) {
        if ($this->get_parameters()->sub_datasource_choice) {
            $class_name = $this->get_parameters()->sub_datasource_choice;
            $sub_datasource = new $class_name();
            $sub_datasource->set_parent_type($this->get_parent_type());
            if (isset($this->parameters->link_type)) {
                $sub_datasource->set_link_type($this->parameters->link_type);
            }
            if (isset($this->external_filter) && $this->external_filter) {
                $sub_datasource->set_filter($this->external_filter);
            }
            if (isset($this->external_sort) && $this->external_sort) {
                $sub_datasource->set_sort($this->external_sort);
            }
            return $sub_datasource->get_datas($datas);
        }
    }
}