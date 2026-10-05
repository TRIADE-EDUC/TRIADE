<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link.class.php,v 1.10 2019/07/31 12:35:48 arenou Exp $

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
	

	public function get_form() {
		$form = parent::get_form();
		$form .= "<div class='row'>
					<div class='colonne3'>
						<label for='aut_link_type_parameter'>".$this->format_text($this->msg['frbr_entity_common_datasource_aut_link_type'])."</label>
					</div>
					<div class='colonne-suite'>
						".$this->get_link_selector()."
					</div>
				</div>";
		return $form;
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
	
	private function get_link_selector() {
	    global $charset;
	    
	    if (!isset($this->parameters->link_type)) $this->parameters->link_type = array();
	    $display = "<select name='aut_link_type_parameter[]' id='aut_link_type_parameter' multiple>";
	    $display .= "<option value='0'".(empty($this->parameters->link_type) || $this->parameters->link_type[0] == "0" ? 'selected' : '').">".$this->msg['frbr_entity_common_datasource_aut_link_all_links']."</option>";
	    $source = marc_list_collection::get_instance('aut_link');
	    $aut_link = $source->table;
	    foreach ($aut_link as $value => $libelle) {
	        if (is_array($libelle)) {
	            $display .= "<optgroup label='".htmlentities($value, ENT_QUOTES, $charset)."'>";
	            foreach ($libelle as $key => $val) {
	                $selected = "";
	                if (is_string($this->parameters->link_type)) {
	                    // Il peut rester des link_type sous forme de chaine en base tant que l'utilisateur n'a pas ré-enregistré sa page FRBR
	                    $this->parameters->link_type = array($this->parameters->link_type);
	                }
	                foreach ($this->parameters->link_type as $link) {
	                    if ($key == $link) {
	                        $selected = "selected='selected'";
	                        break;
	                    }
	                }
	                $display .= "<option value='$key' $selected>".htmlentities($val, ENT_QUOTES, $charset)."</option>";
	            }
	            $display .= "</optgroup>";
	        } else {
	            $selected = "";
	            foreach ($this->parameters->link_type as $link) {
	                if ($key == $link) {
	                    $selected = "selected='selected'";
	                    break;
	                }
	            }
	            $display .= "<option value='$key' $selected>".htmlentities($val, ENT_QUOTES, $charset)."</option>";
	        }
	    }
	    $display .= "</select>";
	    return $display;
	}
}