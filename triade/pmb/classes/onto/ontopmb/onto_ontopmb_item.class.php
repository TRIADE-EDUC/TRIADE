<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_item.class.php,v 1.3 2022/12/02 09:42:34 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/ontopmb/onto_ontopmb_item.tpl.php');

class onto_ontopmb_item extends onto_common_item {
	
	
	public function replace_temp_uri(){
	    if(onto_ontopmb_uri::is_temp_uri($this->get_uri())){
		    foreach($this->get_assertions() as $assertion){
		        if('http://www.pmbservices.fr/ontology#name' === $assertion->get_predicate()){
		            $pmbname = $assertion->get_object();
		            break;
		        }
		    }
		    $this->uri = onto_ontopmb_uri::replace_temp_uri($this->get_uri(),$this->onto_class->uri,$this->onto_class->get_base_uri()."#".$pmbname);
		}
	}	
	
	
	public function check_values() {
	    global $ontology_id;
	    global $msg;
	    
	    $this->checking_errors = array();
	    $valid = parent::check_values();
	    global $ontology_id;
	    $ontology_id = intval($ontology_id);
	    if($ontology_id>0){
	        $pmbname_datatype = $this->datatypes['http://www.pmbservices.fr/ontology#name'][0];
	        $ontology = new ontology($ontology_id);
	        $query = 'select ?pmbname where {
                ?s pmb:name ?pmbname .
                filter (regex(?pmbname, "^'.$pmbname_datatype->get_value().'$")) .
                filter (?s != <'.$this->get_uri().'>)
            }';
	        $result = $ontology->exec_onto_query($query);
	        if(is_array($result) && count($result)>0){
	            $this->checking_errors['http://www.pmbservices.fr/ontology#name']['type'] = "pmbname exists";
	            $this->checking_errors['http://www.pmbservices.fr/ontology#name']['error'] = get_class($pmbname_datatype);
	            $this->checking_errors['http://www.pmbservices.fr/ontology#name']['message'] = $msg['onto_ontopmb_pmbname_exists'];
	            
	             $valid =false;
	        }
	    }
	    return $valid;
	}  
}