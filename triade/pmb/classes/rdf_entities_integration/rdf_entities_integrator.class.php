<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator.class.php,v 1.34.4.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/onto/onto_pmb_entities_mapping.class.php");

class rdf_entities_integrator {
	/**
	 * Table de référence de l'entité à intégré
	 * @var string
	 */
	protected $table_name;

	/**
	 * Clé primaire de la table de référence de l'entité à intégrer
	 * @var int $table_key
	 */
	protected $table_key;

	/**
	 * Store RDF associé
	 * @var rdf_entities_store
	 */
	protected $store;

	/**
	 * Tableau associatif champ table SQL / propriété classe RDF
	 * @var array
	 */
	protected $map_fields;

	/**
	 * Tableau associatif des champs de clé étrangère SQL / propriété RDF
	 * @var array
	 */
	protected $foreign_fields;

	/**
	 * Tableau contenant les entités et leurs tables de liaison
	 * @var array
	 */
	protected $linked_entities;

	/**
	 * Association champ / callable
	 * @var array
	 */
	protected $special_fields;

	/**
	 * Tableau des éléments permettant de construire la requête de base (dans la table $this->table)
	 * @var array
	 */
	protected $base_query_elements;

	/**
	 * Identifiant de l'entité en cours d'intégration
	 * @var int
	 */
	protected $entity_id;

	/**
	 * Tableau des éléments permettant de construire les requêtes dans les tables de liaisons
	 * @var array [{{ table }} => ['reference_field_name' => {{ reference_field_name }}, 'external_field_name' => {{ external_field_name }}, 'external_field_values' => [{{ external_field_value_1 }}, {{ external_field_value_2 }}]]]
	 */
	protected $linked_queries_elements;

	/**
	 * Type d'intégration : 1 : INSERTION, 2 : MODIFICATION, 3 : MIGRATION
	 * @var string
	 */
	protected $integration_type;

	public const INTEGRATION_TYPE_INSERT = 1;

	public const INTEGRATION_TYPE_UPDATE = 2;

	public const INTEGRATION_TYPE_MIGRATION = 3;

	/**
	 * Identifiant du contributeur
	 * @var int
	 */
	protected $contributor_id;

	/**
	 * Type de contributeur : 0 : Utilisateur gestion, 1 : Lecteur OPAC
	 * @var int
	 */
	protected $contributor_type;

	/**
	 * Tableau des callables pour les champs spéciaux (responsabilité, notices liées...)
	 * @var array
	 */
	protected $special_callables;

	/**
	 * Prefixe associé aux champs persos de l'entité
	 * @var string
	 */
	protected $ppersos_prefix;

	/**
	 * Tableau des données de l'entité à renvoyer
	 * @var array
	 */
	protected $entity_data;

	/**
	 * Contribtuion issue d'un sous-formulaire
	 * @var boolean
	 */
	protected $subform = false;

	/**
	 * Constructeur
	 * @param rdf_entities_store $store Store rdf à utiliser
	 */
	public function __construct($store) {
		$this->store = $store;
		$this->init_foreign_fields();
		$this->init_linked_entities();
		$this->init_map_fields();
		$this->init_special_fields();
	}

	protected function init_map_fields() {
		$this->map_fields = array();
		return $this->map_fields;
	}

	protected function init_foreign_fields() {
		$this->foreign_fields = array();
		return $this->foreign_fields;
	}

	protected function init_linked_entities() {
		$this->linked_entities = array();
		return $this->linked_entities;
	}

	protected function init_special_fields() {
		$this->special_fields = array();
		$this->special_callables = array();
		return $this->special_fields;
	}

	protected function init_cataloging_entities() {
		$this->cataloging_entities = array();
		return $this->cataloging_entities;
	}

	/**
	 * Initialise le tableau des éléments à insérer dans la table de référence
	 * Permet de définir des valeurs par défaut
	 */
	protected function init_base_query_elements() {
		$this->base_query_elements = array();
		return $this->base_query_elements;
	}

	/**
	 * Intégre une entité dont on ne connait pas le type dans PMB
	 * @param string $uri URI de l'entité dans le store
	 */
	public function integrate_entity($uri, $subform = false) {
		if (!$uri) {
			return array();
		}
		$type_property = $this->store->get_property($uri, 'rdf:type');

		$entity_integrator = $this->get_entity_integrator_from_type_uri($type_property[0]['value']);

		if ($entity_integrator) {
		    if ($subform) {
		        $entity_integrator->subform = true;
		    }
            return $entity_integrator->integrate_itself($uri);
		}
		return array();
	}


	/**
	 * Intégration d'une entité du type de cette classe
	 * @param string $uri URI de l'entité
	 */
	public function integrate_itself($uri) {
		if (!$uri) {
			return array();
		}

		$this->entity_data = array(
			'uri' => $uri,
			'children' => array()
		);
		$this->integration_type = 0;

		$this->get_entity_id($uri);

		if ($this->is_modified($uri)) {
    		$this->get_contributor_id($uri);

    		$this->init_base_query_elements();
    		$this->linked_queries_elements = array();

    		foreach ($this->store->get_properties($uri) as $property_uri => $values) {
    			$this->handle_property($property_uri, $values);
    		}

    		$this->execute_base_query();
    		$this->execute_linked_queries();

    		if ($this->ppersos_prefix) {

    			$onto_parametres_perso = new onto_parametres_perso($this->ppersos_prefix);

    			$integrated_children = $onto_parametres_perso->rec_fields_perso_with_integrator($this, $uri, $this->entity_id);
    			if (count($integrated_children)) {
    				$this->entity_data['children'] = array_merge($this->entity_data['children'], $integrated_children);
    			}
    		}

    		foreach ($this->special_callables as $callable) {
    			call_user_func_array($callable["method"], $callable["arguments"]);
    		}

    		$this->post_create($uri);
		}

		$this->entity_data['id'] = $this->entity_id;
		return $this->entity_data;
	}

	/**
	 * Traite la propriété
	 * @param string $property_uri URI de la propriété
	 * @param array $values Valeurs associées
	 */
	protected function handle_property($property_uri, $values) {
		if (isset($this->map_fields[$property_uri])) {
			$this->base_query_elements[$this->map_fields[$property_uri]] = $values[0]['value'];
		}

		if (isset($this->foreign_fields[$property_uri])) {
		    if ($values[0]['type'] === 'uri') {
				$integrated_entity = $this->integrate_entity($values[0]['value'], true);
				$this->entity_data['children'][] = $integrated_entity;
				if ($integrated_entity['id']) {
					$this->base_query_elements[$this->foreign_fields[$property_uri]] = $integrated_entity['id'];
				}
			}
		}

		if (isset($this->linked_entities[$property_uri])) {
			foreach ($values as $value) {
				$external_field_value = $value['value'];

				if ($value['type'] === 'uri') {
					$integrated_entity = $this->integrate_entity($value['value'], true);
					$this->entity_data['children'][] = $integrated_entity;
					$external_field_value = $integrated_entity['id'];
				}

				if ($value['type'] === 'literal') {
					$external_field_value = $value['value'];
				}

				$this->add_linked_queries_element($this->linked_entities[$property_uri], $external_field_value);
			}
		}

		if (isset($this->special_fields[$property_uri])) {
			$arguments = $this->special_fields[$property_uri]["arguments"];
			$arguments[] = $values;
			$this->special_callables[] = array('method' => $this->special_fields[$property_uri]["method"], 'arguments' => $arguments);
		}
	}

	/**
	 * Execute la requête d'insertion ou de modification dans la table de référence de l'entité
	 * @return int Identifiant de l'entité insérée ou modifiée
	 */
	protected function execute_base_query() {
		if (!count($this->base_query_elements) || !$this->table_name || !$this->table_key) {
			return $this->entity_id;
		}
		$this->integration_type = static::INTEGRATION_TYPE_INSERT;
		$query = 'insert into '.$this->table_name.' set ';
		$query_clause = '';
		if ($this->entity_id) {
		    $this->integration_type = static::INTEGRATION_TYPE_UPDATE;
			$query = 'update '.$this->table_name.' set ';
			$query_clause = ' where '.$this->table_key.' = '.$this->entity_id;
		}
		$first_value = true;


		foreach ($this->base_query_elements as $base_query_field => $base_query_value) {
			if (!$first_value) {
				$query.= ', ';
			}
			$query.= $base_query_field.' = "'.addslashes(encoding_normalize::utf8_decode($base_query_value)).'"';
			$first_value = false;
		}
		pmb_mysql_query($query.$query_clause);
		if (!$this->entity_id) {
			$this->entity_id = pmb_mysql_insert_id();
		}
		return $this->entity_id;
	}

	/**
	 * Retourne la classe d'intégration associé au type d'entité
	 * @param string $uri URI du type d'entité
	 */
	protected function get_entity_integrator_from_type_uri($type_uri) {
		switch ($type_uri) {
			default :
				$is_cms = false;
				$type = substr($type_uri, strpos($type_uri, '#') + 1);
				$type = strtolower($type);

				$integrator_class = 'rdf_entities_integrator_'.$type;
				if (strpos($type, 'article') !== false) {
					$integrator_class = 'rdf_entities_integrator_article';
					$is_cms = true;
				}
				if (strpos($type, 'section') !== false) {
					$integrator_class = 'rdf_entities_integrator_section';
					$is_cms = true;
				}
				if (strpos($type, 'authperso') !== false) {
				    $authperso =  preg_split("#_([\d]+)#", $type, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
					$integrator_class = 'rdf_entities_integrator_authperso';
					$integrator = new $integrator_class($this->store);
					$integrator->set_authperso_num($authperso[1]);
					return $integrator;
				}
				if (class_exists($integrator_class)) {
					$integrator = new $integrator_class($this->store);
					if($is_cms){
						$type_explode = explode('_', $type);
						$num_type = $type_explode[count($type_explode) - 1];
						if (is_numeric($num_type)) {
							$integrator->set_cms_type($num_type);
						}
					}
					return $integrator;

				}
				return null;
		}
	}

	/**
	 * Retourne la classe d'intégration associé au type d'entité
	 * @param string $type type d'entité
	 */
	public static function get_entity_integrator_name_from_type($type) {
		switch ($type) {
			default :
				$integrator_class = 'rdf_entities_integrator_'.$type;
				if (class_exists($integrator_class)) {
					return $integrator_class;
				}
				return null;
		}
	}

	/**
	 * Retourne l'identifiant de l'entité en cours d'intégration
	 */
	protected function get_entity_id($uri) {
	    if (!isset($this->entity_id)) {
		    $this->entity_id = $this->get_id_from_uri($uri);
		}
		return $this->entity_id;
	}

	/**
	 * retourne l'identifiant d'une entité en fonction de son URI
	 * @param string $uri
	 */
	protected function get_id_from_uri($uri) {
	    $identifier_property = $this->store->get_property($uri, 'pmb:identifier');
	    if (!empty($identifier_property[0])) {
	        return  intval($identifier_property[0]['value']);
	    }
	    return 0;
	}

	/**
	 * renseigne l'identifiant de l'entité
	 */
	public function set_entity_id($id) {
	    $this->entity_id = intval($id);
		return $this;
	}

	protected function execute_linked_queries() {
		if (!count($this->linked_queries_elements) || !$this->entity_id) {
			return null;
		}
		foreach ($this->linked_queries_elements as $table => $query_elements) {

			// On insère tout ce qu'il y a
			$query = 'insert into '.$table.' ('.$query_elements['reference_field_name'].', '.$query_elements['external_field_name'];
			$other_values = '';
			if (is_array($query_elements['other_fields'])) {
				foreach ($query_elements['other_fields'] as $other_field => $other_value) {
					$query.= ', '.$other_field;
					$other_values = ', "'.addslashes($other_value).'"';
				}
			}
			$query.= ') values ';
			$first_value = true;
			foreach ($query_elements['external_field_values'] as $value) {
				if (!$first_value) {
					$query.= ', ';
				}
				$query.= '('.$this->entity_id.', "'.addslashes($value).'"'.$other_values.')';
				$first_value = false;
			}
			pmb_mysql_query($query);
		}
	}

	/**
	 * Ajoute un élément pour les requêtes dans les tables de liaison
	 * @param array $linked_query_data Données relatives à la table de liaison et aux champs à utiliser
	 * @param string $external_field_value Valeur à insérer
	 */
	protected function add_linked_queries_element($linked_query_data, $external_field_value) {

	    // On commence par supprimer les anciens enregistrements
	    $query = 'delete from '.$linked_query_data['table'].' where '.$linked_query_data['reference_field_name'].' = '.$this->entity_id;
	    pmb_mysql_query($query);

	    if (!$external_field_value) {
	        // Le champ a ete vide
			return null;
		}

		if (!isset($this->linked_queries_elements[$linked_query_data['table']])) {
			$this->linked_queries_elements[$linked_query_data['table']] = array(
					'reference_field_name' => $linked_query_data['reference_field_name'],
					'external_field_name' => $linked_query_data['external_field_name'],
					'other_fields' => $linked_query_data['other_fields'],
					'external_field_values' => array()
			);
		}

		$this->linked_queries_elements[$linked_query_data['table']]['external_field_values'][] = $external_field_value;
	}

	/**
	 * Action à effectuer après l'insertion en base
	 */
	protected function post_create($uri) {
		// A dériver
	}

	protected function get_contributor_id($uri) {
		$this->contributor_id = 0;
		$this->contributor_type = 0;
		$contributor_property = $this->store->get_property($uri, 'pmb:has_contributor');
		if (!empty($contributor_property[0]['value'])) {
			if ($contributor_property[0]['value']*1) {
			    $this->contributor_id = intval($contributor_property[0]['value']);
				$this->contributor_type = 1;
			}
		}
		return $this->contributor_id;
	}

	public function get_store() {
		return $this->store;
	}

	public function get_map_fields() {
		if (!isset($this->map_fields)) {
			$this->init_map_fields();
		}
		return $this->map_fields;
	}

	public function get_linked_entities() {
		if (!isset($this->linked_entities)) {
			$this->init_linked_entities();
		}
		return $this->linked_entities;
	}

	public function get_foreign_fields() {
		if (!isset($this->foreign_fields)) {
			$this->init_foreign_fields();
		}
		return $this->foreign_fields;
	}

	public function get_special_fields() {
		if (!isset($this->special_fields)) {
			$this->init_special_fields();
		}
		return $this->special_fields;
	}

	//entites liees utilisees pour le catalogage frbr principalement
	//on passe par la pour ne pas utiliser les specials fields
	public function get_cataloging_entities() {
		if (!isset($this->cataloging_entities)) {
			$this->init_cataloging_entities();
		}
		return $this->cataloging_entities;
	}

	public function update_property($property, $value) {
		if (!$this->entity_id) {
			return null;
		}
		$query = 'UPDATE '.$this->table_name.' SET '.$property.' = '.$value.' WHERE '.$this->table_key.' = '.$this->entity_id;
		pmb_mysql_query($query);
		return true;
	}

	public function add_linked_entity($linked_entity, $value) {
		if (!$this->entity_id) {
			return null;
		}
		// On insère tout ce qu'il y a
		$query = 'insert into '.$linked_entity['table'].' ('.$linked_entity['reference_field_name'].', '.$linked_entity['external_field_name'];
		$other_values = '';
		if (isset($linked_entity['other_fields']) && is_array($linked_entity['other_fields'])) {
			foreach ($linked_entity['other_fields'] as $other_field => $other_value) {
				$query.= ', '.$other_field;
				$other_values .= ', "'.addslashes($other_value).'"';
			}
		}
		$query.= ') values ('.$this->entity_id.','.$value.$other_values.')';
		pmb_mysql_query($query);
		return true;
	}

	public function add_special_link($special_field, $value) {
		if (!empty($special_field)) {
			$special_field["arguments"][] = $value;
			call_user_func_array($special_field["method"], $special_field["arguments"]);
		}
	}

	public function insert_item($values) {
	    if (is_array($values)) {
	        $entity_integrator_values = array();
	        foreach($values as $value) {
	            $type_property = $this->store->get_property($value["value"], 'rdf:type');
	            $entity_integrator = $this->get_entity_integrator_from_type_uri($type_property[0]['value']);
	            if ($entity_integrator) {
	                $entity_integrator->{$this->table_key} = $this->entity_id;
	                $entity_integrator->subform = true;
	                $entity_integrator_values[] = $entity_integrator->integrate_itself($value["value"]);
	            }
	        }
	        $this->entity_data["children"] = array_merge($this->entity_data['children'], $entity_integrator_values);
	        return $entity_integrator_values;
	    }
	}

	public function __set($name, $value){
	    if (!empty($name)) {
	        $this->{$name} = $value;
	    }
	}

	public function create_audit_comment($uri) {
	    global $msg, $charset;
	    $comment = $msg['contribution_audit_comment'];

	    $store = $this->get_store();
	    $query = "
            SELECT * WHERE {
                <$uri> <http://www.pmbservices.fr/ontology#contribution_comment> ?commentaire .
            }
        ";
	    // Ici query retourne un tableau avec les resultats
	    $succes = $store->query($query);
	    if (count($succes['result']['rows']) > 0 && !empty($succes['result']['rows'][0]['commentaire'])) {
	        $comment = htmlentities($succes['result']['rows'][0]['commentaire'], ENT_QUOTES, $charset);
	    }

	    return addslashes(json_encode(array(
	        "uri" => $uri,
	        "comment" => $comment,
	        "subform" => $this->subform
	    )));
	}

	public function insert_vedette($values, $id_responsability) {
	    $vedette_composee = new vedette_composee($values->id, $values->grammar);

	    if (!empty($values->apercu_vedette)) {
	        $vedette_composee->set_label($values->apercu_vedette);
	    }

	    $vedette_composee->reset_elements();
	    $vedette_composee_id = 0;
	    $tosave = false;

	    foreach ($values->elements as $subdivision => $elements) {
	        foreach ($elements as $order => $element) {
	            if (!empty($element->id) && !empty($element->label)) {
	                $tosave = true;

	                $type_element = $element->type;

	                if (strpos($type_element, 'vedette_authperso') === 0) {
	                    $type_element = 'vedette_authpersos';
	                }

	                if (strpos($type_element, 'vedette_ontologies') === 0) {
	                    $type_element = 'vedette_ontologies';
	                }

	                $available_field_class_name = $vedette_composee->get_at_available_field_num($element->available_field_num);
	                if (empty($available_field_class_name['params'])) {
	                    $available_field_class_name['params'] = [];
	                }

	                $vedette_element = new $type_element($element->available_field_num, $element->id, $element->label, $available_field_class_name['params']);
	                $vedette_composee->add_element($vedette_element, $subdivision, $order);
	            }
	        }
	    }

	    if (!empty($tosave)) {
	        $vedette_composee_id = $vedette_composee->save();
	        if (!empty($vedette_composee_id) && !empty($vedette_composee) && !empty($id_responsability) && !empty($values->type)) {
	            vedette_link::save_vedette_link($vedette_composee, $id_responsability, $values->type);
	        }
	    }

	    return $vedette_composee_id;
	}

	/**
	 * Permet de savoir si l'entitée à été modifiée/créée
	 * @param string $uri
	 * @return boolean
	 */
	private function is_modified($uri) {

	    if (empty($uri)) {
	        return true;
	    }

	    if (!$this->entity_id || $this->entity_id == 0) {
	        // c'est une création
	        return true;
	    }

	    $contribution_area_store = new contribution_area_store();
	    $datastore = $contribution_area_store->get_datastore();
	    $datastore->query("SELECT * WHERE {
            <".$uri."> ?p ?o .
            filter(?p != pmb:identifier) .
            filter(?p != pmb:displayLabel) .
            filter(?p != rdf:type) .
        }");

	    if($datastore->num_rows()) {
	        return true;
	    }

        return false;

	}
}