<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_entities_link.class.php,v 1.4 2018/03/21 09:46:49 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/marc_table.class.php');
require_once($class_path.'/onto/onto_pmb_entities_store.class.php');

class frbr_cataloging_entities_link {
	
	protected $uri;
	
	protected $name;
	
	/**
	 * 
	 * @var frbr_cataloging_entity
	 */
	protected $sources;

	/**
	 *
	 * @var frbr_cataloging_entity
	 */
	protected $destinations;
	
	protected $type;
	
	public function __construct($uri, $name, $sources) {
		$this->uri = $uri;
		$this->name = $name;
		$this->sources = $sources;
	}
	
	public function get_sources() {
		return $this->sources;
	}
	
	public function get_destinations() {
		return $this->destinations;
	}
	
	public function add_destination($destination_uri, $destination_name) {	
		$this->destinations = array();
		$label_uri = $destination_uri;
		// On gère les cas particuliers
		switch ($destination_name) {
			case 'linked_record' :
				$destination_name = 'record';
				$label_uri = 'http://www.pmbservices.fr/ontology#record';
				break;
			case 'responsability' :
				$destination_name = 'author';
				$label_uri = 'http://www.pmbservices.fr/ontology#author';
				break;
			case 'linked_work' :
				$destination_name = 'work';
				$label_uri = 'http://www.pmbservices.fr/ontology#work';
				break;
			case 'linked_authority' :
				$destination_name = 'authority';
				$label_uri = 'http://www.pmbservices.fr/ontology#authority';
				break;
		}
		$this->destinations[] = array(
				'name' => $destination_name,
				'label' => frbr_cataloging_entities::get_label($label_uri)
		);
		$link_type = $this->get_type_from_destination_uri($destination_uri);
	}
	
	protected function get_type_from_destination_uri($destination_uri) {
		if (!empty($this->type)) {
			return $this->type;
		}
		$this->type = array();
		$query = '
			select * where {
				 ?type rdfs:domain <'.$destination_uri.'> ;
					pmb:name ?type_name ;
					pmb:datatype ?type_datatype ;
					rdfs:subClassOf pmb:entity_link_type .
			}
		';
		onto_pmb_entities_store::query($query);
		if (onto_pmb_entities_store::num_rows() == 1) {
			$results = onto_pmb_entities_store::get_result();
			$this->type = array(
					'type_uri' => $results[0]->type,
					'type_name' => $results[0]->type_name,
					'type_datatype' => $results[0]->type_datatype
			);
			$this->type['type_kinds'] = $this->get_type_kinds();
		}
		return $this->type;
	}
	
	public function get_type() {
		if (!isset($this->type)) {
			$this->type = array();
		}
		return $this->type;
	}
	
	/**
	 * Tableau des types de liens
	 * 
	 */
	protected function get_type_kinds() {
		switch ($this->type['type_datatype']) {
			case 'http://www.pmbservices.fr/ontology#marclist' :
				$query = '
					select ?o where {
						<'.$this->type['type_uri'].'> <http://www.pmbservices.fr/ontology#marclist_type> ?o
					}
				';
				onto_pmb_entities_store::query($query);
				if (onto_pmb_entities_store::num_rows() == 1) {						
					$result = onto_pmb_entities_store::get_result();
					return $this->get_marclist_table($result[0]->o, $this->type['type_uri']);
				}
				return array();
				break;
			default :
				return array();
				break;
		}
	}
	
	public function get_label() {
		$label = frbr_cataloging_entities_links::get_label($this->uri);
		return $label;
	}
	
	protected function get_marclist_table($marclist_type, $type_uri) {
		global $msg;
		
		switch ($marclist_type) {
			case 'relationtypeup' :
				$relations_table = array();
				$relations_type = array(
						'up' => $msg['notice_lien_montant'],
						'down' => $msg['notice_lien_descendant'],
						'both' => $msg['notice_lien_symetrique']
				);
				foreach ($relations_type as $direction => $direction_label) {
					foreach (notice_relations::get_liste_type_relation_by_direction($direction)->table as $key => $value) {
						if (!isset($relations_table[$direction_label])) {
							$relations_table[$direction_label] = array();
						}
						$relations_table[$direction_label][$key.'-'.$direction] = $value;
					}
				}
				return $relations_table;
			case 'oeuvre_link' :
				$marc_list = marc_list_collection::get_instance($marclist_type);
				switch ($this->uri) {
					case 'http://www.pmbservices.fr/ontology#expression_of' :
						$type = 'expression_of';
						break;
					case 'http://www.pmbservices.fr/ontology#has_expression' :
						$type = 'have_expression';
						break;
					case 'http://www.pmbservices.fr/ontology#has_other_link' :
						$type = 'other_link';
						break;
				}
				$optgroup_list = array();
				foreach($marc_list->table as $group => $types) {
					$options = array();
					foreach($types as $code => $label){
						if ($marc_list->attributes[$code]['GROUP'] == $type) {
							$options[$code] = $label;
						}
						if(count($options)) {
							$optgroup_list[$group] = $options;
						}
					}
				}
				return $optgroup_list;
			default :
				$marc_list = marc_list_collection::get_instance($marclist_type);
				return $marc_list->table;
		}		
	}
	
	public function get_uri() {
		return $this->uri;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function is_reciprocal() {
		if (!empty($this->uri)) {
			$query = '
					select ?reciprocal where {
						<'.$this->uri.'> pmb:reciprocalLink ?reciprocal
					}
				';
			onto_pmb_entities_store::query($query);
			if (onto_pmb_entities_store::num_rows() == 1) {
				$result = onto_pmb_entities_store::get_result();
				if ($result->reciprocal) {
					return true;
				}
			}
		}
		return false;		
	}
}