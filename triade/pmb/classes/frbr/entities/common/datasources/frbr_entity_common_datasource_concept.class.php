<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_concept.class.php,v 1.1 2017-09-15 10:06:13 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class frbr_entity_common_datasource_concept extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){		
		$form = parent::get_form();		
	
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for='aut_link_type_parameter'>".$this->format_text($this->msg['frbr_entity_common_datasource_concept_scheme'])."</label>
					</div>
					<div class='colonne-suite'>
						".$this->get_scheme_selector()."
					</div>
				</div>";
		return $form;
	}
	
	public function save_form(){
		global $scheme_choice;
		$this->parameters->scheme_choice = $scheme_choice;
		return parent::save_form();
	}
	
	/**
	 * return onto_store_arc2
	 */
	private function get_store() {
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		
		$tab_namespaces = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		
		$store = new onto_store_arc2($data_store_config);
		$store->set_namespaces($tab_namespaces);
		return $store;
	}
	
	private function get_scheme_selector() {
		global $charset;
		$query = "SELECT * WHERE {
					?uri ?p skos:ConceptScheme .
					?uri skos:prefLabel ?name
				}";
		$store = $this->get_store();
		$store->query($query);
		$result = $store->get_result();
		$selector = "";
		$selected = "";
		if (!empty($result)) {
			$selector = "
				<select name='scheme_choice' id='scheme_choice'>
					<option value='-1' ".(isset($this->parameters->scheme_choice) && $this->parameters->scheme_choice == -1 ? "selected='selected'" :"").">".$this->msg["frbr_entity_common_datasource_concept_all_scheme"]."</option>
					<option value='0' ".(isset($this->parameters->scheme_choice) && $this->parameters->scheme_choice == 0 ? "selected='selected'" :"").">".$this->msg["frbr_entity_common_datasource_concept_without_scheme"]."</option>
			";
			foreach($result as $row) {
				$selected = (isset($this->parameters->scheme_choice) && $this->parameters->scheme_choice == $row->uri ? "selected='selected'" :"");
				$selector .= "<option value='".$row->uri."' ".$selected.">".htmlentities($row->name,ENT_QUOTES,$charset)."</option>";
			}
			$selector .= "
				</select>
			";
		}
		return $selector;
	}
}