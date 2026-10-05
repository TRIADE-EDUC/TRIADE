<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_store.class.php,v 1.13.8.2 2025/04/29 09:51:00 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/onto/onto_parametres_perso.class.php');
require_once($class_path.'/onto/onto_ontology.class.php');

class contribution_area_store {

	protected static $onto;
	protected static $ontostore;
	protected static $graphstore;
	protected static $datastore;

	protected static $uri_from_pmb_name;

	public const GRAPHSTORE = 1;
	public const DATASTORE = 2;
	public const ONTOSTORE = 3;

	/**
	 * tableau des namespaces
	 * @var array
	 */
	public const ONTOLOGY_NAMESPACE = array(
	    "skos"	=> "http://www.w3.org/2004/02/skos/core#",
	    "dc"	=> "http://purl.org/dc/elements/1.1",
	    "dct"	=> "http://purl.org/dc/terms/",
	    "owl"	=> "http://www.w3.org/2002/07/owl#",
	    "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	    "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
	    "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
	    "pmb"	=> "http://www.pmbservices.fr/ontology#"
	);

	/**
	 * tableau des namespaces
	 * @var array
	 */
	public const CONTRIBUTION_NAMESPACE = array(
	    "dc"	=> "http://purl.org/dc/elements/1.1",
	    "dct"	=> "http://purl.org/dc/terms/",
	    "owl"	=> "http://www.w3.org/2002/07/owl#",
	    "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	    "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
	    "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
	    "pmb"	=> "http://www.pmbservices.fr/ontology#",
	    "ca"	=> "http://www.pmbservices.fr/ca/"
	);

	/**
	 * Config du graphstore
	 * @var array
	 */
	public const GRAPHSTORE_CONFIG = array(
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

	/**
	 * Config du ontostore
	 * @var array
	 */
	public const ONTOSTORE_CONFIG = array(
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

	/**
	 * Config du datastore
	 * @var array
	 */
	public const DATASTORE_CONFIG = array(
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

	/**
	 * @return onto_ontology
	 */
	public function get_ontology() {
		global $class_path;

		if(!isset(self::$onto)){
			$onto_store = $this->get_ontostore();
 			//chargement de l'ontologie dans son store
			$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
			onto_parametres_perso::load_in_store($onto_store, $reset);
			self::$onto = new onto_ontology($onto_store);
		}
		return self::$onto;
	}

	/**
	 * @return onto_store_arc2_extended
	 */
	public function get_ontostore() {
		if(!isset(self::$ontostore)){
		    self::$ontostore = new onto_store_arc2_extended(self::ONTOSTORE_CONFIG);
		    self::$ontostore->set_namespaces(self::ONTOLOGY_NAMESPACE);
		}
		return self::$ontostore;
	}

	/**
	 * @return onto_store_arc2
	 */
	public function get_graphstore() {
		if(!isset(self::$graphstore)){
			self::$graphstore = new onto_store_arc2(self::GRAPHSTORE_CONFIG);
			self::$graphstore->set_namespaces(self::CONTRIBUTION_NAMESPACE);
		}
		return self::$graphstore;
	}

	/**
	 * @return onto_store_arc2
	 */
	public function get_datastore() {
	    if(!isset(self::$datastore)) {
	        self::$datastore = new onto_store_arc2(self::DATASTORE_CONFIG);
	        self::$datastore->set_namespaces(self::CONTRIBUTION_NAMESPACE);
	    }
	    return self::$datastore;
	}

	/**
	 * @param int|string $form_id
	 * @param array $params
	 * @return onto_store_arc2
	 */
	public static function get_formstore ($form_id, $params = array()) {

		if (empty($form_id)) {
			throw new \Exception("form_id is empty");
		}

	    $store_config = array(
	        /* db */
	        'db_name' => DATA_BASE,
	        'db_user' => USER_NAME,
	        'db_pwd' => USER_PASS,
	        'db_host' => SQL_SERVER,
	        /* store */
	        'store_name' => 'onto_contribution_form_' . $form_id,
	        /* stop after 100 errors */
	        'max_errors' => 100,
	        'store_strip_mb_comp_str' => 0
	    );
	    if (!empty($params)) {
	        $store_config['params'] = $params;
	    }
	    $formstore = new onto_store_arc2_extended($store_config);
        $formstore->set_namespaces(contribution_area_store::CONTRIBUTION_NAMESPACE);
        return $formstore;
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
	 * @param string|int $depth Nombre de niveaux de profondeur (0 pas de limite)
	 * @return array <multitype:, multitype:string >
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
					case 'http://www.pmbservices.fr/ontology#response' :
						$infos['response'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#orderResponse' :
					    $infos['orderResponse'] = $results[$i]->o;
					    break;
					case 'http://www.pmbservices.fr/ontology#equation' :
						$infos['equation'] = $results[$i]->o;
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

	public function get_uri_from_pmb_name($pmb_name) {
	    if (isset(self::$uri_from_pmb_name[$pmb_name])) {
	        return self::$uri_from_pmb_name[$pmb_name];
	    }

	    $this->get_ontostore();
		$result = self::$ontostore->query('select ?uri where {
			?uri pmb:name "'.$pmb_name.'" .
		}');
		if($result){
		    $results = self::$ontostore->get_result();
			if (count($results)) {
			    self::$uri_from_pmb_name[$pmb_name] = $results[0]->uri;
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

	/**
	 *
	 * @param int $store Utiliser les constantes de la classe
	 * @param boolean $force
	 * @return boolean
	 */
	public function reset_store($store, $force = false, $delete_file_opac = false ) {
        switch ($store) {
	        case self::DATASTORE:
	            // On supprime les fichiers temporaire de l'OPAC
	            onto_parametres_perso::remove_file_opac();

	            $this->delete_all_champ_perso($this->get_datastore());

	            // On supprime le fichier de gestion
	            return onto_parametres_perso::load_in_store($this->get_datastore(), $force);
	        case self::ONTOSTORE:
	            return onto_parametres_perso::load_in_store($this->get_ontostore(), $force);
	    }
        return false;
	}

	/**
	 * Suppresion des champs perso
	 * (penser a faire un load_in_store après cette methode)
	 *
	 * @param onto_store_arc2_extended|onto_store_arc2 $store
	 * @return []
	 */
	public function delete_all_champ_perso ($store) {

	    // On récupère tout les champs persos
	    $succes = $store->query('SELECT ?s WHERE {
                ?s pmb:is_cp ?is_cp .
            } GROUP BY ?s');

        $failed = array();
	    if ($succes && $store->num_rows() > 0) {
	        $results = $store->get_result();

	        $length = count($results);

	        // On supprime les datatypes du champs
	        for ($i = 0; $i < $length; $i++) {
        	    $succes = $store->query('DELETE {
                    <'.$results[$i]->s.'> ?p ?o .
                }');
        	    if (!$succes) {
        	        $failed[] = $results[$i];
        	    }
	        }
	    }

	    return $failed;
	}

	/**
	 * On vérifie les propriétés des formulaire, quand on update un champ perso
	 * @param string $table_name
	 * @param int $id_champ
	 * @param boolean $champ_deleted
	 */
	public function check_properties_form($table_name, $id_champ = 0,$champ_deleted = false) {
	    if (!empty($table_name)) {

	        // On récupère les infos des champs
	        $requete = "SELECT idchamp, name, type, datatype FROM ".$table_name;
	        $result = pmb_mysql_query($requete);
	        if (pmb_mysql_num_rows($result)) {
	            while ($champ = pmb_mysql_fetch_object($result)) {

        	        // On récupère la liste des formulaires
    	            $forms = contribution_area_forms_controller::get_all_forms();
    	            $length = count($forms);

    	            // Liste des id de formulaire à mettre à jour
    	            $update_forms = array();

    	            for ($i = 0; $i < $length; $i++) {
    	                $form = $forms[$i];
    	                foreach ($form->form_parameters as $property_name => $value) {
    	                    // Le formulaire utilise la propriété modifier/supprimer
    	                    if (strpos($property_name, $champ->name) !== false) {
    	                        $update_forms[] = [
    	                            "id_form" => $form->id_form,
    	                            "form_type" => $form->form_type,
    	                            "property_name" => $property_name,
    	                            "deleted" => ($champ->idchamp == $id_champ) ? $champ_deleted : false
    	                        ];
    	                    }
    	                }
    	            }


                    // On met à jour les formulaires
    	            $length = count($update_forms);
    	            for ($i = 0; $i < $length; $i++) {
    	                if (!empty($update_forms[$i]['deleted']) && $update_forms[$i]['deleted']) {
    	                    $this->delete_properties($update_forms[$i]);
    	                } else {
    	                    $this->update_properties($update_forms[$i]);
    	                }
    	            }
	            }
	        }
	    }
	}

	/**
	 * On supprime une propriété du store
	 * @param array $form
	 * @return boolean
	 */
	private function delete_properties ($form) {
	    $datastore = $this->get_datastore();
	    $formstore = $this->get_formstore($form['id_form']);

	    $succes_datastore = false;
	    $succes_datastore_1 = false;
	    $succes_formstore = false;


	    $succes_datastore = $datastore->query('DELETE {
                        pmb:'.$form['property_name'].' ?p ?o .
                    }');
	    if ($succes_datastore) {
	        $succes_datastore_1 = $datastore->query('DELETE {
                            ?s pmb:'.$form['property_name'].' ?o .
                        }');

	        $succes_formstore = $formstore->query('DELETE {
                            pmb:'.$form['property_name'].' ?p ?o .
                        }');

            $contribution_area_form = new contribution_area_form($form['form_type'], $form['id_form']);
            $contribution_area_form->remove_property($form['property_name']);
	    }


	    return ($succes_datastore && $succes_datastore_1 && $succes_formstore);
	}

	/**
	 * On met a jour la propriété dans un formulaire
	 * @param array $form
	 * @return boolean
	 */
	private function update_properties ($form) {
	    $datastore = $this->get_datastore();
	    $formstore = $this->get_formstore($form['id_form']);

	    $succes_delete = false;
	    $succes_insert = false;

	    $succes_datastore = $datastore->query('SELECT ?datatype WHERE {
                        pmb:'.$form['property_name'].' pmb:datatype ?datatype .
                    }');
	    $succes_formstore = $formstore->query('SELECT ?datatype WHERE {
                        pmb:'.$form['property_name'].' pmb:datatype ?datatype .
                    }');

	    if (($succes_datastore && $datastore->num_rows() > 0 ) && ( $succes_formstore && $formstore->num_rows() > 0)) {

	        $formResults = $formstore->get_result();
	        $dataResults = $datastore->get_result();

	        // Le datatype ne correspond pas
	        if ($formResults[0]->datatype != $dataResults[0]->datatype) {
	            $succes_delete = $formstore->query('DELETE {
                                pmb:'.$form['property_name'].' pmb:datatype ?datatype .
                            }');
	            if ($succes_delete) {
	                $succes_insert = $formstore->query('insert into <pmb> {
                                pmb:'.$form['property_name'].' pmb:datatype <'.$dataResults[0]->datatype.'> .
                            }');
	            }
	        }
	    }

	    return ($succes_delete && $succes_insert);
	}

	public function empty_store() {
	    $data_store = $this->get_datastore();
	    $result = $data_store->query('
    	    SELECT ?uri WHERE {
    	        ?uri pmb:identifier ?o .
    	    }
		');
	    $rows = $data_store->get_result();
	    foreach ($rows as $row){
    	    $query = "DELETE {
                <$row->uri> ?p ?o .
            } WHERE {
                <$row->uri> ?p ?o .
                <$row->uri> pmb:identifier ?identifier .
                FILTER(?p != pmb:identifier && ?p != rdf:type && ?p != pmb:displayLabel)
            }";
    	    $data_store->query($query);
	    }
	}

	public function clear_drafts() {
	    $data_store = $this->get_datastore();
		$result = $data_store->query('
			SELECT ?uri WHERE {
				?uri pmb:is_draft ?o .
			}
		');
		$rows = $data_store->get_result();
		foreach ($rows as $row){
			$query = "DELETE {
				<$row->uri> ?p ?o .
			} WHERE {
				<$row->uri> ?p ?o .
				<$row->uri> pmb:is_draft ?o .
			}";
			$data_store->query($query);
		}
	}

	public function get_properties_from_uri($uri) {
	    $properties = array (
	        'uri' => $uri
	    );

	    if (!empty($uri)) {
	        $query = "SELECT * WHERE {
                    <$uri> ?p ?o
                }";
	        $this->get_datastore()->query($query);
	        if ($this->get_datastore()->num_rows()) {
	            $results = $this->get_datastore()->get_result();
	            foreach ($results as $result){
	                $properties[$result->p] = $result->o;
	                $prop = str_replace("http://www.pmbservices.fr/ontology#", "", $result->p);
	                $properties[$prop] = $result->o;
	            }
	        }
	    }

	    return $properties;
	}

	private function get_node_assertions($data)
	{
		return [];
	}

	public function get_area_uri()
	{
        return '';
	}

}