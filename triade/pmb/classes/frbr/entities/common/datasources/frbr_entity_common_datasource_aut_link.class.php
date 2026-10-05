<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link.class.php,v 1.12 2019/09/04 13:20:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link extends frbr_entity_common_datasource {
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_sub_datasources() {
		return array(
			"frbr_entity_common_datasource_aut_link_authors",
			"frbr_entity_common_datasource_aut_link_categories",
			"frbr_entity_common_datasource_aut_link_concepts",
			"frbr_entity_common_datasource_aut_link_publishers",
			"frbr_entity_common_datasource_aut_link_collections",
			"frbr_entity_common_datasource_aut_link_subcollections",
			"frbr_entity_common_datasource_aut_link_series",
			"frbr_entity_common_datasource_aut_link_works",
            "frbr_entity_common_datasource_aut_link_indexint",
            "frbr_entity_common_datasource_aut_link_authpersos"
		);
	}
	
	public function save_form() {
		global $aut_link_type_parameter;
		$this->parameters->link_type = $aut_link_type_parameter;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
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