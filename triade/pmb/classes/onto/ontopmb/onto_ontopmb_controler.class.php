<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_controler.class.php,v 1.2 2017-06-12 09:22:27 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/onto_files.class.php');

class onto_ontopmb_controler extends onto_common_controler {
	
	public function get_base_resource($with_params=true){
		return $this->params->base_resource."?ontology_id=".$this->params->ontology_id.($with_params? "&" : "");
	}
	
	protected function proceed_save($list=true){
		global $class_path;
		
		$this->item->get_values_from_form();
		
		$result = $this->handler->save($this->item);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else{
			//on a besoin pour notre ontologie de revoir certaine property...
			$query = "select * where{
				<".$this->item->get_uri()."> rdf:type ?type .
				<".$this->item->get_uri()."> rdfs:label ?label .
				optional {
					<".$this->item->get_uri()."> pmb_onto:indexWith ?indexWith .
				} .
				optional {
					<".$this->item->get_uri()."> pmb_onto:restrictWith ?restrictWith .
				} 
			}";
			$useInConcept = false;
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$query = "
					insert into <pmb> {";
 				switch($results[0]->type){
 					case "http://www.w3.org/2002/07/owl#Class" :
						//nothing to do for now!
 						break;
 					case "http://www.w3.org/2002/07/owl#ObjectProperty" :
 						$query.= "
						<".$this->item->get_uri()."> rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .";
						
						break;
 				}
				
				$labels = $inverseOf = $indexWith = $restrictWith = array();
				foreach($results as $result){
					
					$labels[] = "\"".$result->label."\"".(isset($result->label_lang) ? "@".$result->label_lang : "");
					if($result->indexWith){
						$indexWith[] = "<".$result->indexWith.">";
					}
					if($result->restrictWith){
						$restrictWith[] = "<".$result->restrictWith.">";
					}
				}
				$labels = array_unique($labels);
				$indexWith = array_unique($indexWith);
				$restrictWith = array_unique($restrictWith);
				
				foreach($indexWith as $elem){
					$query.= "
						<".$this->item->get_uri()."> rdfs:subClassOf ".$elem." .";
				}
				foreach($restrictWith as $elem){
					$query.= "
						<".$this->item->get_uri()."> rdfs:subClassOf ".$elem." .";
				}
				$query.= "
					}";
				
				$this->handler->data_query($query);
			}
		}
		if($list){
			$this->proceed_list();
		}
	}
}