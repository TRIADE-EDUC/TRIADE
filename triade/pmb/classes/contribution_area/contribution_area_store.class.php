<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_store.class.php,v 1.3 2019-02-22 10:16:53 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/onto/onto_parametres_perso.class.php');
require_once($class_path.'/onto/onto_ontology.class.php');

class contribution_area_store {
	
	protected static $onto;
	protected static $graphstore;
	protected static $datastore;
	
	public function get_ontology(){
		global $class_path;
		global $base_path;
	
		if(!isset(self::$onto)){
			$onto_store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'ontodemo',
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
			
			$onto_store = new onto_store_arc2_extended($onto_store_config);
			$onto_store->set_namespaces($tab_namespaces);
			
 			//chargement de l'ontologie dans son store
			$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
			onto_parametres_perso::load_in_store($onto_store, $reset);
			self::$onto = new onto_ontology($onto_store);
		}
		return self::$onto;
	}
	
	public function get_graphstore(){
		if(!isset(self::$graphstore)){
			$store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'contribution_area_graphstore',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0
			);
			$tab_namespaces = array(
					"dc"	=> "http://purl.org/dc/elements/1.1",
					"dct"	=> "http://purl.org/dc/terms/",
					"owl"	=> "http://www.w3.org/2002/07/owl#",
					"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
					"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
					"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
					"pmb"	=> "http://www.pmbservices.fr/ontology#",
					"ca"	=> "http://www.pmbservices.fr/ca/"
			);
	
			self::$graphstore = new onto_store_arc2($store_config);
			self::$graphstore->set_namespaces($tab_namespaces);
		}
		return self::$graphstore;
	}
	
	public function get_datastore(){
	    if(!isset(self::$datastore)){
	        $store_config = array(
	            /* db */
	            'db_name' => DATA_BASE,
	            'db_user' => USER_NAME,
	            'db_pwd' => USER_PASS,
	            'db_host' => SQL_SERVER,
	            /* store */
	            'store_name' => 'contribution_area_datastore',
	            /* stop after 100 errors */
	            'max_errors' => 100,
	            'store_strip_mb_comp_str' => 0
	        );
	        $tab_namespaces = array(
	            "dc"	=> "http://purl.org/dc/elements/1.1",
	            "dct"	=> "http://purl.org/dc/terms/",
	            "owl"	=> "http://www.w3.org/2002/07/owl#",
	            "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	            "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
	            "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
	            "pmb"	=> "http://www.pmbservices.fr/ontology#",
	            "ca"	=> "http://www.pmbservices.fr/ca/"
	        );
	        
	        self::$datastore = new onto_store_arc2($store_config);
	        self::$datastore->set_namespaces($tab_namespaces);
	    }
	    return self::$datastore;
	}
	
	public function get_attachment($source_uri, $area_uri = ''){
		$attachments = array();
		$this->get_graphstore();
		$query = 'select * where {
			?attachment rdf:type ca:Attachment .';
		if ($area_uri) {
			$query .= '
			?attachment ca:inArea <'.$area_uri.'> .';
		}
		$query .='
			?attachment ca:attachmentSource <'.$source_uri.'> .
			?attachment ca:attachmentDest ?dest .
			?attachment ca:rights ?rights .
			optional {
				?attachment rdf:label ?name .
				?attachment ca:identifier ?identifier .
				?attachment pmb:name ?property_pmb_name
			}
		}';
		
		$result = self::$graphstore->query($query);
		if($result){
			$attachments = self::$graphstore->get_result();
		}
		return $attachments;
	}
	
	/**
	 * Retourne les attaches
	 * @param string $source_uri URI du noeud parent
	 * @param string $area_uri URI de l'espace
	 * @param string $source_id ID du noeud parent
	 * @param string $dest_type Type de destination
	 * @param unknown $depth Nombre de niveaux de profondeur (0 pas de limite)
	 * @return Ambigous <multitype:, multitype:string >
	 */
	public function get_attachment_detail($source_uri, $area_uri = '', $source_id='', $dest_type='', $depth = 0){
		$depth--;
		$details = array();		
		$attachments = $this->get_attachment($source_uri,$area_uri);

		for($i=0 ; $i<count($attachments) ; $i++){
			$detail = $this->get_infos($attachments[$i]->dest);

			if($source_id){
				$detail['parent'] = $source_id;
			}
			if(!empty($attachments[$i]->property_pmb_name)){
				$detail['propertyPmbName'] = $attachments[$i]->property_pmb_name;
			}
			$details[]=$detail;
			
			if ($depth != 0) {
				$details = array_merge($details,$this->get_attachment_detail($attachments[$i]->dest,$area_uri,$detail['id'],'',$depth));				
			}			
		}
		return $details;
	}
	
	public function get_infos($uri){
		$this->get_graphstore();
		$infos = array('uri' => $uri);
		$result = self::$graphstore->query('select * where {
			<'.$uri.'> ?p ?o .
		}');
		if($result){
			$results = self::$graphstore->get_result();
			for($i=0 ; $i<count($results) ; $i++){
				switch($results[$i]->p){
					case 'http://www.pmbservices.fr/ca/eltId' :
						$infos['formId'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ca/identifier' :
						$infos['id'] = $results[$i]->o;
						break;
					case 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' :
						switch($results[$i]->o){
							case "http://www.pmbservices.fr/ca/Form" :
								$infos['type'] = 'form';
								break;
							case "http://www.pmbservices.fr/ca/Scenario" :
								$infos['type'] = 'scenario';
								break;
							default :
								$infos['type'] = $results[$i]->o;
								break;
						}
						break;
					case 'http://www.w3.org/1999/02/22-rdf-syntax-ns#label' :
						$infos['name'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#entity' :
						$infos['entityType'] = $results[$i]->o;
						break;						
					case 'http://www.pmbservices.fr/ontology#startScenario' :
						$infos['startScenario'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#displayed' :
						$infos['displayed'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#parentScenario' :
						$infos['parentScenario'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#question' :
						$infos['question'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#comment' :
						$infos['comment'] = $results[$i]->o;
						break;
				}
			}
		}
		return $infos;
	}
	
	public function get_uri_from_id($id) {
		$this->get_graphstore();
		$result = self::$graphstore->query('select ?uri where {
			?uri <http://www.pmbservices.fr/ca/identifier> "'.$id.'" .
		}');
		if($result){
			$results = self::$graphstore->get_result();
			if (count($results)) {
				return $results[0]->uri;
			}
		}
		return '';
	}
	
	protected function prepare_data($data){
		$assertions = array();
		$scenario_uri = "<http://www.pmbservices.fr/ca/Scenario#!!id!!>";
		$attachment_uri = "<http://www.pmbservices.fr/ca/Attachement#!!id!!>";
		$form_uri = "<http://www.pmbservices.fr/ca/Form#!!id!!>";
		for($i=0 ; $i<count($data) ; $i++){
			$assertions = array_merge($assertions,$this->get_node_assertions($data[$i]));
				
			switch($data[$i]->type){
				case 'startScenario':
					///LES ATTACHMENT
					//assertion pour l'attachement
					$assertions[]  =array(
					'subject' => str_replace('!!id!!','area'.$data[$i]->id,$attachment_uri),
					'predicat' => 'rdf:type',
							'value' => 'ca:Attachment'
					);
									$assertions[]  =array(
											'subject' => str_replace('!!id!!','area'.$data[$i]->id,$attachment_uri),
											'predicat' => 'ca:inArea',
											'value' => $this->get_area_uri()
					);
					$assertions[]  =array(
						'subject' => str_replace('!!id!!','area'.$data[$i]->id,$attachment_uri),
						'predicat' => 'ca:attachmentSource',
									'value' => $this->get_area_uri()
					);
					$assertions[]  =array(
						'subject' =>str_replace('!!id!!','area'.$data[$i]->id,$attachment_uri),
						'predicat' => 'ca:attachmentDest',
									'value' => str_replace('!!id!!',$data[$i]->id,$scenario_uri)
					);
					$assertions[]  =array(
						'subject' => str_replace('!!id!!','area'.$data[$i]->id,$attachment_uri),
						'predicat' => 'ca:rights',
									'value' => '"TBD"'
					);
					break;
					case 'form':
					///LES ATTACHMENT
					//assertion pour l'attachement
					$assertions[]  =array(
					'subject' => str_replace('!!id!!',$data[$i]->parentType.$data[$i]->id,$attachment_uri),
					'predicat' => 'rdf:type',
					'value' => 'ca:Attachment'
					);
							
						$assertions[]  =array(
								'subject' => str_replace('!!id!!',$data[$i]->parentType.$data[$i]->id,$attachment_uri),
								'predicat' => 'ca:inArea',
								'value' => $this->get_area_uri()
						);
						switch($data[$i]->parentType){
							case 'startScenario':
								$attachment_source = str_replace('!!id!!',$data[$i]->parent,$scenario_uri);
								break;
									
						}
						$assertions[]  =array(
								'subject' => str_replace('!!id!!',$data[$i]->parentType.$data[$i]->id,$attachment_uri),
								'predicat' => 'ca:attachmentSource',
								'value' => $attachment_source
						);
						$assertions[]  =array(
								'subject' =>str_replace('!!id!!',$data[$i]->parentType.$data[$i]->id,$attachment_uri),
								'predicat' => 'ca:attachmentDest',
								'value' => str_replace('!!id!!',$data[$i]->id,$form_uri)
						);
						$assertions[]  =array(
								'subject' => str_replace('!!id!!',$data[$i]->parentType.$data[$i]->id,$attachment_uri),
								'predicat' => 'ca:rights',
								'value' => '"TBD"'
						);
						break;
			}
		}
		return $assertions;
	}	
}