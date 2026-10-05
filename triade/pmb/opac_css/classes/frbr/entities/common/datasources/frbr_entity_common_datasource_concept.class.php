<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_concept.class.php,v 1.6 2021/03/08 15:39:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_concept extends frbr_entity_common_datasource {
	
    protected $origin_type = 0;
    
	public function __construct($id = 0) {
		parent::__construct($id);
		if (!isset($this->parameters->scheme_choice)) $this->parameters->scheme_choice = array();
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
	
	public function save_form() {
	    global $scheme_choice;
	    
        $this->parameters->scheme_choice = $scheme_choice;
		return parent::save_form();
	}
	
	/**
	 * @return onto_store_arc2
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
	
	public function get_datas($datas = array()){
	    
	    $query = "select distinct index_concept.num_concept as id, index_concept.num_object as parent FROM index_concept
			WHERE index_concept.type_object = ".$this->origin_type." AND index_concept.num_object IN (".implode(',', $datas).")";
	    $datas = $this->get_datas_from_query($query);
	    /**
	     * Filtre sur le schema si présent
	     */
	    if (is_string($this->parameters->scheme_choice)) {
	        $this->parameters->scheme_choice = array($this->parameters->scheme_choice);
	    }
	    if ($this->parameters->scheme_choice[0] !== "-1" && (!empty($datas[0]))) {
	        $results = array();
	        $filtered_ids = array();
	        
	        $store = $this->get_store();
	        foreach ($datas as $id => $data) {
	            if ($id) {
	                $uri_filter = "";
	                foreach ($data as $data) {
	                    if ($uri_filter) {
	                        $uri_filter .= ' ||';
	                    }
	                    $uri = onto_common_uri::get_uri($data);
	                    $uri_filter .= " ?uri = <$uri>";
	                }
	                foreach ($this->parameters->scheme_choice as $scheme) {
	                    $query = "select * where{
        					?uri rdf:type skos:Concept .
        			 		?uri skos:inScheme <$scheme> .
        				 	".($uri_filter ? " filter (".$uri_filter.")" : "")."
        				}";
	                    $store->query($query);
	                    $results[] = $store->get_result();
	                }
	                if (!empty($results)) {
	                    $result_ids = array();
	                    foreach ($results as $result) {
	                        foreach ($result as $concept) {
	                            $result_ids[] = onto_common_uri::get_id($concept->uri); //Récupération des id de concept filtrés
	                        }
	                    }
	                    //maj des id enfants par id parent
	                    $datas[$id] = $result_ids;
	                    $filtered_ids = array_merge($filtered_ids, $result_ids);
	                }
	            }
	        }
	        $datas[0] = $filtered_ids;
	    }
	    $datas = $this->get_datas_with_schemes($datas);
	    return parent::get_datas($datas);
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
	    if (!empty($result)) {
	        if (is_string($this->parameters->scheme_choice)) {
	            $this->parameters->scheme_choice = array($this->parameters->scheme_choice);
	        }
	        $selector = "
				<select name='scheme_choice[]' id='scheme_choice' multiple>
	                <option value='-1'".((empty($this->parameters->scheme_choice) || isset($this->parameters->scheme_choice[0]) && $this->parameters->scheme_choice[0] == "-1") ? 'selected' : '').">".$this->msg["frbr_entity_common_datasource_concept_all_scheme"]."</option>
	                <option value='0'".((isset($this->parameters->scheme_choice[0]) && $this->parameters->scheme_choice[0] == "0" || isset($this->parameters->scheme_choice[1]) && $this->parameters->scheme_choice[1] == "0") ? 'selected' : '').">".$this->msg["frbr_entity_common_datasource_concept_without_scheme"]."</option>
			";
	        foreach ($result as $row) {
	            $selected = "";
	            foreach ($this->parameters->scheme_choice as $scheme) {
	                if ($scheme == $row->uri) {
	                    $selected = "selected='selected'";
	                    break;
	                }
	            }
	            $selector .= "<option value='".$row->uri."' $selected>".htmlentities($row->name,ENT_QUOTES,$charset)."</option>";
	        }
	        $selector .= "</select>";
	        return $selector;
	    }
	    return '';
	}
	
	private function get_datas_with_schemes($datas) {
	    if (!empty($datas[0])) {
    	    foreach($datas[0] as $concept_id) {
    	        $concept_uri = onto_common_uri::get_uri($concept_id);
    	        $query = "SELECT * WHERE {
    					?uri ?p skos:ConceptScheme .
    					?uri skos:prefLabel ?name .
                        <$concept_uri>  skos:inScheme ?uri
    				}";
    	        $store = $this->get_store();
    	        $store->query($query);
    	        $scheme_uri = "";
    	        $label = $this->msg["frbr_entity_common_datasource_concepts_without_scheme"];
    	        if ($store->num_rows()) {
        	        $schemes = $store->get_result();
        	        $label = $schemes[0]->name ?? "";
        	        $scheme_uri = $schemes[0]->uri;
    	        }
        	    if (!isset($datas["group"])) {
        	        $datas["group"] = [];
        	    }
        	    if (!isset($datas["group"][$scheme_uri])) {
        	        $datas["group"][$scheme_uri] = [];
        	    }
        	    if (!isset($datas["group"][$scheme_uri]["label"])) {
        	        $datas["group"][$scheme_uri]["label"] = $label;
        	    }
        	    $datas["group"][$scheme_uri]["values"][] = $concept_id;
    	    }
	    }
	    return $datas;
	}
}