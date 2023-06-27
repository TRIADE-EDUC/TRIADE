<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_concept.class.php,v 1.1 2017-09-15 10:06:14 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_concept extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){		
		$form = parent::get_form();
		return $form;
	}
	
	public function save_form(){
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
	
	public function get_datas($datas){
		/**
		 * Filtre sur le schema si présent
		 */
		if($this->parameters->scheme_choice !== "-1" && (isset($datas) && !empty($datas[0]))){
			$filtered_ids = array();
			$uri_filter = "";
			foreach($datas[0] as $data){
				if($uri_filter){
					$uri_filter.= ' ||';
				}
				$uri = onto_common_uri::get_uri($data);
				$uri_filter.= " ?uri = <".$uri.">";
			}
			if($this->parameters->scheme_choice === "0"){ //Tous les chémas
				$query = "select * where{
					?uri rdf:type skos:Concept .	
					optional{		
				 		?uri skos:inScheme ?scheme .
					}  
				 	filter(!bound(?scheme) ".($uri_filter ? " && (".$uri_filter.")" : "")." )
				 	}"; 
			}else{
				$query = "select * where{
					?uri rdf:type skos:Concept .
			 		?uri skos:inScheme <".$this->parameters->scheme_choice."> .
				 	".($uri_filter ? " filter (".$uri_filter.")" : "")." 
				 	}";
			}
			$store = $this->get_store();
			$store->query($query);
			
 			$result = $store->get_result();
 			if(count($result)){
 				$keys = array_keys($datas); //Récupération de la structure originale du tableau datas
 				$result_ids = array();
 				foreach($result as $concept){
 					$result_ids[] = onto_common_uri::get_id($concept->uri); //Récupération des id de concept filtrés
 				}
 				foreach($keys as $key){
 					$filtered_ids[$key] = $result_ids; //Recomposition de la structure originale
 				}
 			}
 			return parent::get_datas($filtered_ids);
		}
		return parent::get_datas($datas);
	}
}