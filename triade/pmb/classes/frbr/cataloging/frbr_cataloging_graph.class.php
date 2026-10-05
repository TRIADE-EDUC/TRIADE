<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_graph.class.php,v 1.9.16.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/frbr/cataloging/frbr_cataloging_datastore.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

class frbr_cataloging_graph {

	protected $id;
	protected $uri;
	protected $num_datanode;

	/**
	 * liste des noeuds d entite
	 * @var array
	 */
	protected $entity_nodes;

	/**
	 * liste des liens entre entites
	 * @var array
	 */
	protected $entity_links;

	/**
	 * liste des liens entre noeuds dans le graphe
	 * @var array
	 */
	protected $entity_wires;

	public function __construct($num_datanode = 0) {
		$this->num_datanode = $num_datanode;
	}

	protected function fetch_data() {
		$this->entity_nodes = array();
		$this->entity_links = array();
		$this->entity_wires = array();

		$query = "select * where {
			<".$this->get_uri()."> pmb:startNode ?start_node .
			?start_node pmb:entityType ?entity_type .
			?start_node pmb:entityId ?entity_id .
		}";
		frbr_cataloging_datastore::query($query);
		//$this->clean_store();
		if (frbr_cataloging_datastore::num_rows()) {
			$results = frbr_cataloging_datastore::get_result();
			foreach ($results as $result) {
				$node = $this->get_entity_node_info($result->entity_type, $result->entity_id);
				if (is_array($node['links'])) {
					foreach ($node['links'] as $link) {
						$this->entity_links[] = $this->get_entity_link_info($result->entity_type.'_'.$result->entity_id, $link['name']);
						$this->entity_wires[] = array(
								'source' => $result->entity_type.'_'.$result->entity_id,
								'target' => $result->entity_type.'_'.$result->entity_id.'_'.$link['name'],
						);
					}
				}
				$this->entity_nodes[] = $node;
			}
		}
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
			$this->uri = 'http://www.pmbservices.fr/cataloging_graph#'.$this->num_datanode;
			$this->id = onto_common_uri::set_new_uri($this->uri);
		}
		return $this->uri;
	}

	public function clean_store() {
// 		$query = 'select * where {
// 				?subject pmb:in_cataloging_graph <'.$this->get_uri().'>
// 		}';
// 		frbr_cataloging_datastore::query($query);
// 		if (frbr_cataloging_datastore::num_rows()) {
// 			foreach (frbr_cataloging_datastore::get_result() as $result) {
// 				$query = '
// 					delete {
// 						<'.$result->subject.'> ?p ?o
// 					}';
// 				frbr_cataloging_datastore::query($query);
// 				onto_common_uri::delete_uri($result->subject);
// 			}
// 		}
		$query = 'delete {
				<'.$this->get_uri().'> ?p ?o .
				?o ?p2 ?o2
		}';
		frbr_cataloging_datastore::query($query);
	}

	public function save_graph(){
		$this->clean_store();

		/*
		$query = 'insert into <pmb> {';
		foreach($this->entity_nodes as $node) {}
				?subject pmb:in_cataloging_graph <'.$this->get_uri().'>
		}
		frbr_cataloging_datastore::query($query);
		*/
	}

	public function delete() {
		$this->clean_store();
		onto_common_uri::delete_uri($this->get_uri());
	}

	public function get_graph_data() {
		if (!isset($this->entity_nodes) || !isset($this->entity_links) || !isset($this->entity_wires)) {
			$this->fetch_data();
		}
		return array(
				"entity_nodes" => array_values($this->entity_nodes),
				"entity_links" => array_values($this->entity_links),
				"entity_wires" => array_values($this->entity_wires),
		);
	}

	public function set_graph_data($items) {
		if (isset($items->start_node)) {
			$this->init_graphed_entities();
			$start_node = $this->add_node($items->start_node->type, $items->start_node->id,'' ,true);

			$this->add_linked_data($start_node, $items->items);
		}
		return $this;
	}

	/**
	 * fonction recursive pour ajouter des noeuds par rapport a un noeud de reference
	 * @param array $parent_node
	 * @param array $items tableau d'object qui provient du JS
	 * @return boolean
	 */
	protected function add_linked_data($parent_node, $items) {
		if (!is_array($items)) {
			return false;
		}

		$links_from_entity = onto_pmb_entities_mapping::get_links_from_entity($parent_node['type'], $parent_node['eltId']);

		$added_data = false;
		if (!empty($items) && !empty($links_from_entity)) {
			foreach ($items as $i => $item) {
				foreach ($links_from_entity  as $link_name => $link) {
					if ($item->type == $link['link_type']) {
						foreach ($link['linked_entities'] as $entity) {
							if ($entity['id'] == $item->id) {
								$new_link = $this->add_link($parent_node['type'].'_'.$parent_node['eltId'], $link_name, $parent_node['color'], $item->type.'_'.$item->id, $entity['link_type']);
								$new_node = $this->add_node($item->type, $item->id, $new_link['id']);

								//recursif
								//on met a jour la liste des items pour la recursivite
								$new_items = $items;
								unset($new_items[$i]);

								$this->add_linked_data($new_node, $new_items);
								$added_data = true;
							}
						}
					}
				}
			}
		}
		return $added_data;
	}

	public function set_entity_nodes($entity_nodes) {
		$this->entity_nodes = $entity_nodes;
		return $this;
	}

	public function add_node($entity_type, $entity_id, $link_id = '', $start_node = false) {
		if (!isset($this->entity_nodes[$entity_type."_".$entity_id])) {
			$radius = ($start_node ? 35 : 20);
			$node = $this->get_entity_node_info($entity_type, $entity_id, $radius);
			$this->entity_nodes[$entity_type."_".$entity_id] = $node;
		}

		if ($link_id && isset($this->entity_links[$link_id])) {
			$this->add_wire($link_id, $entity_type."_".$entity_id, $this->entity_nodes[$entity_type."_".$entity_id]['color']);
		}
		return $this->entity_nodes[$entity_type."_".$entity_id];
	}

	public function add_link($source_id, $link_name, $color = "0,0,0", $target_id = null, $link_type = '') {

		$color = entity_graph::get_degradate($color);
		$link = $this->get_entity_link_info($source_id, $link_name, $color, $target_id, $link_type);

		$link_id = $link['id'];

		if (!$this->reciprocal_link_exist($source_id, $target_id)) {
			$this->entity_links[$link_id] = $link;
			$this->add_wire($source_id, $link_id, $color);
		}
		return $link;
	}

	public function add_wire($source, $target, $color = '0,0,0') {
		$wire = array(
				'source' => $source,
				'target' => $target,
				'color' => $color
		);
		$this->entity_wires[] = $wire;
		return $wire;
	}

	public function add_start_node($entity_type, $entity_id) {
		global $opac_url_base;
		$uri = $opac_url_base.'#'.$entity_id.'_'.$entity_type.'_'.$this->num_datanode;
		$uri_id = onto_common_uri::get_id($uri);
		if(!$uri_id){
			onto_common_uri::set_new_uri($uri);
			$query = "insert into <pmb> {
				<".$this->get_uri()."> pmb:startNode <".$uri."> .
				<".$uri."> pmb:entityType '".$entity_type."' .
				<".$uri."> pmb:entityId '".$entity_id."' .
			}";
			if (frbr_cataloging_datastore::query($query)) {
				return true;
			}
		}
		return false;
	}

	public function get_entity_node_info($entity_type, $entity_id, $radius = 20){
		$infos = array(
				'id' => $entity_type.'_'.$entity_id,
				'eltId' => $entity_id,
				'type' => $entity_type,
				'url' => '',
				'radius' => $radius,
		);

		switch($entity_type){
		 	case 'records':
		 		$entity = new notice($entity_id);
		 		$infos['color'] = entity_graph::get_color_from_type('record');
		 		$infos['img'] = notice::get_icon($entity_id);
		 		$infos['links'] = frbr_cataloging_entities_links::get_links_from_entity_type('record', $entity_id);
		 		$infos['name'] = $entity->tit1;
		 		break;
		 	case 'authorities':
		 		$entity = new authority($entity_id);
		 		$infos['color'] = entity_graph::get_color_from_type($entity->get_string_type_object());
		 		$infos['img'] = $entity->get_type_icon();
		 		$infos['links'] = frbr_cataloging_entities_links::get_links_from_entity_type($entity->get_string_type_object(), $entity_id);
		 		$infos['name'] = $entity->get_isbd();
		 		break;
		 	default :
		 		$entity = new stdClass();
		 		if (class_exists($entity_type)) {
		 			$entity = new $entity_type($entity_id);
		 		}
		 		$infos['color'] = entity_graph::get_color_from_type($entity_type);
		 		$infos['img'] = $entity->get_type_icon();
		 		$infos['links'] = frbr_cataloging_entities_links::get_links_from_entity_type($entity_type, $entity_id);
		 		$infos['name'] = $entity->get_isbd();
		 		break;
		 }

		 return $infos;
	}

	public function get_entity_link_info($source_id, $link_name, $color = '0,0,0', $target_id = null, $link_type = array()){
		$infos = array(
				'id' => $source_id.'_'.$link_name.(isset($link_type['id']) ? '_'.$link_type['id'] : ''),
				'link_name' => $link_name,
				'name' => (isset($link_type['label']) ? $link_type['label'] : frbr_cataloging_entities_links::get_label("http://www.pmbservices.fr/ontology#".$link_name)),
				'color'=> $color,
				'radius' => 15,
				'source' => $source_id,
				'target' => $target_id,
				'reciprocal' => frbr_cataloging_entities_links::is_reciprocal($link_name),
		);
		return $infos;
	}

	protected function get_type_from_entity_type($entity_type, $entity_id) {
		switch($entity_type){
			case 'records':
				return 'record';
			case 'authorities':
		 		$entity = new authority($entity_id);
		 		switch ($entity->get_string_type_object()) {
		 			case 'titre_uniforme' :
		 				return 'work';
		 			default :
		 				return $entity->get_string_type_object();
		 		}
			default :
				return $entity_type;
		}
	}

	protected function init_graphed_entities() {
		$this->entity_nodes = array();
		$this->entity_links = array();
		$this->entity_wires = array();
	}

	protected function reciprocal_link_exist($source_id, $target_id) {
		if ($source_id && $target_id) {
			foreach ($this->entity_links as $link) {
				if ($link['source'] == $target_id && $link['target'] == $source_id && $link['reciprocal']) {
					return true;
				}
			}
		}
		return false;
	}
}