<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link.class.php,v 1.7 2019-04-19 12:23:43 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_sub_datasources(){
		return array(
				"frbr_entity_common_datasource_aut_link_authors",
				"frbr_entity_common_datasource_aut_link_categories",
				"frbr_entity_common_datasource_aut_link_concepts",
				"frbr_entity_common_datasource_aut_link_publishers",
				"frbr_entity_common_datasource_aut_link_collections",
				"frbr_entity_common_datasource_aut_link_subcollections",
				"frbr_entity_common_datasource_aut_link_series",
				"frbr_entity_common_datasource_aut_link_works",
				"frbr_entity_common_datasource_aut_link_indexint"
		);
	}
	

	public function get_form(){
		$form = parent::get_form();
		$selector = new marc_select("aut_link", "aut_link_type_parameter", (isset($this->parameters->link_type) ? $this->parameters->link_type : ''), '', 0, $this->msg["frbr_entity_common_datasource_aut_link_all_links"]);
		$selector->get_selector();
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for='aut_link_type_parameter'>".$this->format_text($this->msg['frbr_entity_common_datasource_aut_link_type'])."</label>
					</div>
					<div class='colonne-suite'>
						".$selector->display."
					</div>
				</div>";
		return $form;
	}
	
	public function save_form(){
		global $aut_link_type_parameter;
		$this->parameters->link_type = $aut_link_type_parameter;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		if($this->get_parameters()->sub_datasource_choice) {
			$class_name = $this->get_parameters()->sub_datasource_choice;
			$sub_datasource = new $class_name();
			if (isset($this->parameters->link_type)) {
				$sub_datasource->set_link_type($this->parameters->link_type); 
			}
			if(isset($this->external_filter) && $this->external_filter) {
				$sub_datasource->set_filter($this->external_filter);
			}
			if(isset($this->external_sort) && $this->external_sort) {
				$sub_datasource->set_sort($this->external_sort);
			}
			return $sub_datasource->get_datas($datas);
		}
	}
}