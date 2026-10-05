<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_property_range.class.php,v 1.1 2015/08/10 23:16:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class onto_ontopmb_property_range extends onto_common_property {
	
	public function get_available_range(){
		$ontopmb_class_list = array();
		$query ="select * where {
				?elem rdf:type <http://www.w3.org/2002/07/owl#Class> .
				?elem <http://www.w3.org/2000/01/rdf-schema#label> ?label
			} order by ?label";
		$this->data_store->query($query);
		if($this->data_store->num_rows()){
			$results = $this->data_store->get_result();
			foreach($results as $result){
				$ontopmb_class_list[$result->elem] = $result->label;
			}
		}
		return $ontopmb_class_list;
	}
}