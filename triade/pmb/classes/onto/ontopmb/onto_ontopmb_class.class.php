<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_class.class.php,v 1.1 2015/08/10 23:16:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_class.class.php';


/**
 * class onto_common_class
 * 
 */
class onto_ontopmb_class extends onto_common_class {
	protected $base_uri = "";
	
	public function get_base_uri(){
		if(!$this->base_uri){
			$this->data_store->query("select ?uri where {
				?uri rdf:type owl:Ontology
			}");
			$results = $this->data_store->get_result();
			$this->base_uri = $results[0]->uri;
		}
		return $this->base_uri;
	}
} // end of onto_common_class