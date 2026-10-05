<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_scheme.class.php,v 1.5 2022/01/21 08:37:14 dgoron Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/frbr/cataloging/frbr_cataloging_entities_links.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_entities.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_datastore.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

class frbr_cataloging_scheme {
	/**
	 * 
	 * @var integer identifiant du scheme dans onto_uri
	 */
	protected $id;
	
	/**
	 * 
	 * @var string uri du scheme dans le store
	 */
	protected $uri;
	
	/**
	 * 
	 * @var string nom du scheme
	 */
	protected $name;
	
	/**
	 * Tableau indicé des entités du scénario dans l'ordre
	 * @var array
	 */
	protected $entities;
	
	/**
	 * Tableau indicé des liens du scénario dans l'ordre
	 * @var array
	 */
	protected $links;
	
	/**
	 * Tableau indicé des types de liens
	 * @var array
	 */
	protected $links_types;
	
	/**
	 * URI de l'entité de départ
	 * @var string
	 */
	protected $start_entity_uri;
	
	/**
	 * URI du type de l'entité de départ
	 * @var string
	 */
	protected $start_entity_type_uri;
	
	
	/**
	 * Constructeur
	 * @param number $id
	 */
	public function __construct($id = 0, $uri = '') {
		$this->id = intval($id);
		$this->uri = $uri;
	}
	
	protected function fetch_data() {
		$this->entities = array();
		$query = '
			select * where {
				<'.$this->get_uri().'> rdfs:label ?label ;
					pmb:scheme_start_entity ?start_entity .
				?start_entity pmb:entity_name ?start_entity_name .
				?start_entity pmb:entity_type_uri ?start_entity_type_uri
			}
		';
		frbr_cataloging_datastore::query($query);
		if (frbr_cataloging_datastore::num_rows()) {
			$results = frbr_cataloging_datastore::get_result();
			$this->name = $results[0]->label;
			$this->start_entity_uri = $results[0]->start_entity;
			$this->start_entity_type_uri = $results[0]->start_entity_type_uri;			
			$this->entities[] = $results[0]->start_entity_name;
		}
		return $this->name;
	}
	
	protected function get_linked_entities($entity) {
		if (!isset($this->links)) {
			$this->links = array();
		}
		if (!isset($this->links_types)) {
			$this->links_types = array();
		}
		$query = '
			select * where {
				?link rdfs:domain <'.$entity.'> ;
					pmb:entity_link_name ?link_name ;
					rdfs:range ?range .
				?range pmb:entity_name ?range_name
				optional {
					?link pmb:entity_link_type_value ?link_type_value
				}
			}';
		frbr_cataloging_datastore::query($query);
		if (frbr_cataloging_datastore::num_rows()) {
			$results = frbr_cataloging_datastore::get_result();
			$this->entities[] = $results[0]->range_name;
			$this->links[] = $results[0]->link_name;
			if (isset($results[0]->link_type_value)) {
				$this->links_types[count($this->links)-1] = $results[0]->link_type_value;
			}
			$this->get_linked_entities($results[0]->range);
		}
	}
	
	public function get_entities() {
		if (isset($this->entities) && (count($this->entities) > 1)) {
			return $this->entities;
		}
		if (!isset($this->start_entity_uri)) {
			$this->fetch_data();
		}
		$this->get_linked_entities($this->start_entity_uri);
		return $this->entities;
	}
	
	public function get_links() {
		if (isset($this->links)) {
			return $this->links;
		}
		if (!isset($this->start_entity_uri)) {
			$this->fetch_data();
		}
		$this->get_linked_entities($this->start_entity_uri);
		return $this->links;
	}
	
	public function get_links_types() {
		if (isset($this->links_types)) {
			return $this->links_types;
		}
		if (!isset($this->start_entity_uri)) {
			$this->fetch_data();
		}
		$this->get_linked_entities($this->start_entity_uri);
		return $this->links_types;
	}
	
	/**
	 * 
	 * @param string $context
	 */
	public function get_form(){
		$template_path =  "./includes/templates/frbr/cataloging/frbr_cataloging_scheme.html";
		if(file_exists("./includes/templates/frbr/cataloging/frbr_cataloging_scheme_subst.html")){
			$template_path =  "./includes/templates/frbr/cataloging/frbr_cataloging_scheme_subst.html";
		}
		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path);
			return $h2o->render(array(
					'scheme' => $this,
					'entities' => frbr_cataloging_entities::get_json_entities(),
					'entities_links' => frbr_cataloging_entities_links::get_json_links()
			));
		}
		return '';
	}
	
	public function get_id() {
		if ($this->id) {
			return $this->id;
		}
		$this->id = 0;
		if ($this->uri) {
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->id;
	}
	
	public function get_uri() {
		if ($this->uri) {
			return $this->uri;
		}
		if ($this->id) {
			$this->uri = onto_common_uri::get_uri($this->id);
		}
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_new_uri('http://www.pmbservices.fr/cataloging_scheme#');
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->uri;
	}
	
	public function get_name() {
		if (isset($this->name)) {
			return $this->name;
		}
		$query = 'select ?label where {
				<'.$this->get_uri().'> rdfs:label ?label .
		}';
		frbr_cataloging_datastore::query($query);
		if (frbr_cataloging_datastore::num_rows()) {
			$results = frbr_cataloging_datastore::get_result();
			$this->name = $results[0]->label;
		}
		return $this->name;
	}
	
	public function get_start_entity_uri() {
		if (isset($this->start_entity_uri)) {
			return $this->start_entity_uri;
		}
		$this->fetch_data();
		return $this->start_entity_uri;
	}
	
	public function get_start_entity_type_uri() {
		if (isset($this->start_entity_type_uri)) {
			return $this->start_entity_type_uri;
		}
		$this->fetch_data();
		return $this->start_entity_type_uri;
	}
	
	public function set_values_from_form() {
		global $scheme_name, $entities, $links, $links_types;
		
		$this->name = (isset($scheme_name) ? $scheme_name : '');
		$this->entities = (isset($entities) ? $entities : array());
		$this->links = (isset($links) ? $links : array());
		$this->links_types = (isset($links_types) ? $links_types : array());
	}
	
	public function save() {
		if ($this->get_id()) {
			$this->clean_store();
		}
		
		$query = 'insert into <pmb> {
				<'.$this->get_uri().'> rdfs:label "'.addslashes($this->name).'" .
				<'.$this->get_uri().'> rdfs:type pmb:cataloging_scheme .
		';
		
		if (!empty($this->entities[0])) {
			$start_entity = onto_common_uri::get_new_uri('http://www.pmbservices.fr/cataloging_scheme_entity#');
			$query.= '
					<'.$this->get_uri().'> pmb:scheme_start_entity <'.$start_entity.'> .
					<'.$start_entity.'> pmb:entity_name "'.$this->entities[0].'" .
					<'.$start_entity.'> pmb:entity_type_uri pmb:'.$this->entities[0].' .
					<'.$start_entity.'> pmb:in_cataloging_scheme <'.$this->get_uri().'> .
			';
				
			if (!empty($this->links)) {
				$parent_entity = $start_entity;
				foreach ($this->links as $i => $link) {
					$link_uri = onto_common_uri::get_new_uri('http://www.pmbservices.fr/cataloging_scheme_entity_link#');
					$query.= '
						<'.$link_uri.'> rdfs:domain <'.$parent_entity.'> .
						<'.$link_uri.'> pmb:in_cataloging_scheme <'.$this->uri.'> .
						<'.$link_uri.'> pmb:entity_link_name "'.$link.'" .
					';
					
					if (isset($this->links_types[$i])) {
						$query.= '
							<'.$link_uri.'> pmb:entity_link_type_value "'.$this->links_types[$i].'" .
						';
					}
					$parent_entity = onto_common_uri::get_new_uri('http://www.pmbservices.fr/cataloging_scheme_entity#');
					$query.= '
						<'.$parent_entity.'> pmb:entity_name "'.$this->entities[$i+1].'" .
						<'.$parent_entity.'> pmb:in_cataloging_scheme <'.$this->get_uri().'> .
						<'.$link_uri.'> rdfs:range <'.$parent_entity.'> .
					';
				}
			}
		}
		
		$query.= '
		}';
		frbr_cataloging_datastore::query($query);
	}
	
	public function clean_store() {
		$query = 'select * where {
				?subject pmb:in_cataloging_scheme <'.$this->get_uri().'>
		}';
		frbr_cataloging_datastore::query($query);
		if (frbr_cataloging_datastore::num_rows()) {
			foreach (frbr_cataloging_datastore::get_result() as $result) {
				$query = 'delete {
						<'.$result->subject.'> ?p ?o
				}';
				frbr_cataloging_datastore::query($query);
				onto_common_uri::delete_uri($result->subject);
			}
		}
		$query = 'delete {
				<'.$this->get_uri().'> ?p ?o
		}';
		frbr_cataloging_datastore::query($query);
	}
	
	public function delete() {
		$this->clean_store();
		onto_common_uri::delete_uri($this->get_uri());
	}
	
	public function get_json_elements() {		
		return encoding_normalize::json_encode($this->get_linked_elements());
	}
	
	/**
	 * retourne les elements lies dans le schema de catalogage
	 * @return array
	 */	
	public function get_linked_elements() {
		$this->get_entities();
		$linked_elements = array();
		for ($i = 0 ; $i < count($this->entities) ; $i++) {
			$linked_elements[] = array(
					'entity' => $this->entities[$i],
					'link' => (isset($this->links[$i]) ? $this->links[$i] : ''),
					'link_type' => (isset($this->links_types[$i]) ? $this->links_types[$i] : '')
			);
		}
		return $linked_elements;
	}
	
	/**
	 * Retourne le lien de catalogage
	 * @return string
	 */
	public function get_cataloging_link() {
		$this->get_entities();
		return $this->get_link_form($this->entities[0]);
	}
	
	/**
	 * Retourne le lien du formulaire d'une entite
	 * @param string $entity
	 * @return string
	 */
	protected function get_link_form($entity) {
		global $pmb_url_base;
		switch ($entity) {
			case "record" :
				return $pmb_url_base."catalog.php?categ=notice_form&cataloging_scheme_id=".$this->get_id();
			case "author" :
				return $pmb_url_base."autorites.php?categ=auteurs&sub=author_form&type_autorite=&cataloging_scheme_id=".$this->get_id();
			case "category" :
				return $pmb_url_base."autorites.php?categ=categories&sub=categ_form&parent=0&id=0&cataloging_scheme_id=".$this->get_id();
			case "publisher" :
				return $pmb_url_base."autorites.php?categ=editeurs&sub=editeur_form&cataloging_scheme_id=".$this->get_id();
			case "collection" :
				return $pmb_url_base."autorites.php?categ=collections&sub=collection_form&cataloging_scheme_id=".$this->get_id();
			case "subcollection" :
				return $pmb_url_base."autorites.php?categ=souscollections&sub=collection_form&cataloging_scheme_id=".$this->get_id();
			case "serie" :
				return $pmb_url_base."autorites.php?categ=series&sub=serie_form&cataloging_scheme_id=".$this->get_id();
			case "work" :
				return $pmb_url_base."autorites.php?categ=titres_uniformes&sub=titre_uniforme_form&cataloging_scheme_id=".$this->get_id();
			case "indexint" :
				return $pmb_url_base."autorites.php?categ=indexint&sub=indexint_form&id_pclass=0&cataloging_scheme_id=".$this->get_id();
			case "concept" :
				return $pmb_url_base."autorites.php?categ=concepts&sub=concept&id=&action=edit&concept_scheme=0&parent_id=&cataloging_scheme_id=".$this->get_id();
			default :
				return $pmb_url_base;
		}
	}
}