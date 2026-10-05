<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_graph.class.php,v 1.9.6.1.2.1 2025/04/29 12:27:40 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path.'/authorities/tabs/authority_tabs.class.php');
// require_once($class_path.'/authority.class.php');
require_once($class_path.'/index_concept.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/serials.class.php');
require_once($class_path.'/marc_table.class.php');

class entity_graph {
	
	protected $type;
	protected $instance;
	protected $entities;
	protected $entities_graphed;
	protected $root_node_id;
	protected $nb_nodes_graphed = 0;
	protected static $entity_graph = array();
	
	/**
	 * Type du noeud additionnel
	 * @var string
	 */
	public const ADDITIONNAL_TYPE = "additionnal_nodes";
	
	/**
	 * Type du noeud sous-racine
	 * @var string
	 */
	public const NODE_SUBROOT_TYPE = "subroot";
	
	/**
	 * Rayon du noeud sous-racine
	 * @var string
	 */
	public const NODE_SUBROOT_RADUIS = "15";
	
	/**
	 * 
	 * @param stdClass $instance
	 * @param string $type
	 * @return entity_graph
	 */
	public static function get_entity_graph($instance, $type){
		if (!isset(self::$entity_graph[$type][$instance->get_id()])) {
			self::$entity_graph[$type][$instance->get_id()] = new entity_graph($instance, $type);
		}
		return self::$entity_graph[$type][$instance->get_id()];
	}
	
	public function __construct($instance, $type){
		$this->instance = $instance;
		$this->type = $type;
	}
	
	public function get_entities_graphed($is_root = true){
		
		if (isset($this->entities_graphed)) {
			return $this->entities_graphed;
		}
		
		$this->entities_graphed = array('nodes'=>array(), 'links'=>array());
		
		switch($this->type){
			case 'authority':
				if (!isset($this->entities_graphed['nodes']['authorities_'.$this->instance->get_id()])) {
					$type = $this->instance->get_string_type_object();
					
					if($type == "authperso" && $this->instance->get_object_instance()->is_event()){
						$type = "event";
					}
					$node = array(
							'id' => 'authorities_'.$this->instance->get_id(),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type($type),
							'name' => $this->instance->header,
							'url' => $this->instance->get_permalink().'&quoi=common_entity_graph',
							'img' => $this->instance->get_type_icon()
					);
					if ($is_root) {
						$this->entities_graphed['nodes']['authorities_'.$this->instance->get_id()] = $node;
					}
				}
				$this->root_node_id = 'authorities_'.$this->instance->get_id();
				$this->get_entities_graphed_from_authority();
				break;
			case 'record':
				if (!isset($this->entities_graphed['nodes']['records_'.$this->instance->get_id()])) {
					$node = array(
							'id' => 'records_'.$this->instance->get_id(),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type('record'),
							'name' => notice::get_notice_title($this->instance->get_id()),
							'url' => notice::get_permalink($this->instance->get_id()),
							'img' => notice::get_icon($this->instance->get_id())
					);
					if ($is_root) {
						$this->entities_graphed['nodes']['records_'.$this->instance->get_id()] = $node;
					}
				}
				$this->root_node_id = 'records_'.$this->instance->get_id();
				$this->get_entities_graphed_from_record();
				break;
		}
		
		if (!empty($this->entities) && count($this->entities)) {
			if (isset($this->entities['records']) && count($this->entities['records'])) {
			    $this->entities['records'] = $this->elements_limited_of_entities($this->entities, 'records', $node['color']);
				$this->compute_entities($this->entities, 'records', $node);
			}
			if(isset($this->entities['authorities']) && count($this->entities['authorities'])){
			    $this->entities['authorities'] = $this->elements_limited_of_entities($this->entities, 'authorities', $node['color']);
				$this->compute_entities($this->entities, 'authorities', $node);
			}
			if(isset($this->entities['indexed_entities']) && count($this->entities['indexed_entities'])){
			    $this->entities['indexed_entities'] = $this->elements_limited_of_entities($this->entities, 'indexed_entities', $node['color']);
				$this->compute_entities($this->entities, 'indexed_entities', $node);
			}
			if(isset($this->entities['indexed_concepts']) && count($this->entities['indexed_concepts'])){
				$this->compute_entities($this->entities,'indexed_concepts', $node);
			}
		}
		return $this->entities_graphed;
	}
	
	protected function get_entities_graphed_from_authority(){
		switch($this->instance->get_type_object()){
			case AUT_TABLE_AUTHORS :
				return $this->get_entities_graphed_from_author();
			case AUT_TABLE_CATEG :
				return $this->get_entities_graphed_from_categ();
			case AUT_TABLE_PUBLISHERS :
				return $this->get_entities_graphed_from_publisher();
			case AUT_TABLE_COLLECTIONS :
				return $this->get_entities_graphed_from_collection();
			case AUT_TABLE_SUB_COLLECTIONS :
				return $this->get_entities_graphed_from_sub_collection();
			case AUT_TABLE_SERIES :
				return $this->get_entities_graphed_from_serie();
			case AUT_TABLE_TITRES_UNIFORMES :
				return $this->get_entities_graphed_from_work();
			case AUT_TABLE_INDEXINT :
				return $this->get_entities_graphed_from_indexint();
			case AUT_TABLE_CONCEPT :
				return $this->get_entities_graphed_from_concept();
			case AUT_TABLE_AUTHPERSO :
				return $this->get_entities_graphed_from_authperso();
		}
	}
	
	protected function get_entities_graphed_from_author(){
		global $msg;
		if (isset($this->entities)) {
			return $this->entities;
		}
		$this->entities = array();
		
		// Récupération des notices portant cet auteur
		$query = "select responsability_notice as notice_id from responsability where responsability_author = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->entities['records']['author_records'] = array(
				'type' => 'author_records',
				'label' => $msg['entity_graph_records_having_as_authors'],
				'link' => $this->instance->get_permalink().'&quoi=author_records',
				'img' => '',
				'elements' => array()
			);
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['records']['author_records']['elements'][] = $row->notice_id;
			}
		}
		$this->entities['authorities'] = array();
		$this->get_entities_indexed();
		
		// Récupération des oeuvres portant cet auteur
		$query = "select responsability_tu_num as work_id, responsability_tu_type as type from responsability_tu where responsability_tu.responsability_tu_author_num = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($row->type) {
					if (!isset($this->entities['authorities']['titre_uniforme']['performer'])) {
						$this->entities['authorities']['titre_uniforme']['performer'] = array(
								'type' => 'titre_uniforme_performer',
								'label' => $msg['entity_graph_works_having_as_performer'],
								'link' => $this->instance->get_permalink().'&quoi=author_oeuvres',
								'elements' => array()
						);
					}
					$this->entities['authorities']['titre_uniforme']['performer']['elements'][] = $row->work_id;
				} else {
					if (!isset($this->entities['authorities']['titre_uniforme']['author'])) {
						$this->entities['authorities']['titre_uniforme']['author'] = array(
								'type' => 'titre_uniforme_author',
								'label' => $msg['entity_graph_works_having_as_author'],
								'link' => $this->instance->get_permalink().'&quoi=author_oeuvres',
								'elements' => array()
						);
					}
					$this->entities['authorities']['titre_uniforme']['author']['elements'][] = $row->work_id;
				}
			}
		}
		return $this->entities;
	}
	
	protected function get_entities_graphed_from_categ(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_publisher(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_collection(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_sub_collection(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_serie(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_work(){
		global $charset;
		global $msg;
		
		if (isset($this->entities)) {
			return $this->entities;
		}
		$this->entities = array();
		
		// Récupération des notices portant cet oeuvre
		$query = "select ntu_num_notice as notice_id from notices_titres_uniformes where ntu_num_tu = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->entities['records']['oeuvre_records'] = array(
					'type' => 'oeuvre_records',
					'label' => $msg['entity_graph_records_from_work'],
					'link' => $this->instance->get_permalink().'&quoi=oeuvre_records',
					'color' => $this->get_color_from_type('oeuvre_records'),
					'img' => '',
					'elements' => array()
			);
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['records']['oeuvre_records']['elements'][] = $row->notice_id;
			}
		}
		$this->entities['authorities'] = array();
		
		$this->get_entities_indexed();

		$oeuvre_link= marc_list_collection::get_instance('oeuvre_link');
		// Récupération des oeuvres liées
		$query = "select oeuvre_link_to as work_id, oeuvre_link_type as type, oeuvre_link_expression as expression from tu_oeuvres_links where oeuvre_link_from = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if(!isset($this->entities['authorities']['titre_uniforme'][$row->type])){
					$label = '';
					foreach ($oeuvre_link->table as $table) {
						if (isset($table[$row->type])) {
							$label = $table[$row->type];
							break;
						}
					}
					$this->entities['authorities']['titre_uniforme'][$row->type] = array(
							'type' => $row->type,
							'label' => $label,
							'color' => $this->get_color_from_type($row->type),
							'link' => ($row->expression ? '' : $this->instance->get_permalink().'&quoi=oeuvre_other_links'),
							'elements' => array()
					);
				}
				$this->entities['authorities']['titre_uniforme'][$row->type]['elements'][] = $row->work_id;
			}
		}

		// Récupération des oeuvres expression de
		$query = "select oeuvre_link_from as work_id, oeuvre_link_type as type from tu_oeuvres_links where oeuvre_link_expression = 1 and oeuvre_link_to = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$type = $oeuvre_link->inverse_of[$row->type];
				if(!isset($this->entities['authorities']['titre_uniforme'][$type])){
					$label = '';
					foreach ($oeuvre_link->table as $table) {
						if (isset($table[$type])) {
							$label = $table[$type];
							break;
						}
					}
					$this->entities['authorities']['titre_uniforme'][$type] = array(
							'type' => $type,
							'label' => $label,
							'link' => $this->instance->get_permalink().'&quoi=oeuvre_expressions',
							'elements' => array()
					);
				}
				$this->entities['authorities']['titre_uniforme'][$type]['elements'][] = $row->work_id;
			}
		}
		
		// Récupération des auteurs/interprètes de l'oeuvre
		$query = "select responsability_tu_author_num as author_id, responsability_tu_type as type from responsability_tu where responsability_tu.responsability_tu_num = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($row->type) {
					if (!isset($this->entities['authorities']['author']['performer'])) {
						$this->entities['authorities']['author']['performer'] = array(
								'type' => 'author_performer',
								'label' => $msg['tu_interpreter_list'],
								'link' => '',
								'elements' => array()
						);
					}
					$this->entities['authorities']['author']['performer']['elements'][] = $row->author_id;
				} else {
					if (!isset($this->entities['authorities']['author']['author'])) {
						$this->entities['authorities']['author']['author'] = array(
								'type' => 'author_author',
								'label' => $msg['133'],
								'link' => '',
								'elements' => array()
						);
					}
					$this->entities['authorities']['author']['author']['elements'][] = $row->author_id;
				}
			}
		}
		$this->get_entity_concepts();
		$this->get_event_used_by_work();
		return $this->entities;
	}
	
	protected function get_entities_graphed_from_indexint(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_concept(){
		return $this->entities = array();
	}
	
	protected function get_entities_graphed_from_authperso(){
		global $msg;
		
		$this->entities = array();
		$query = 'select oeuvre_event_tu_num from tu_oeuvres_events where oeuvre_event_authperso_authority_num = "'.$this->instance->get_object_instance()->id.'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			//A voir si l'on veux effectuer un traitement différent 
			//en fonction de si c'est une autorité perso ou non
			$this->entities['authorities']['titre_uniforme']['event_works'] = array(
					'type' => 'event_works',
					'label' => $msg['entity_graph_work_which_use_event'],
					'link' => $this->instance->get_permalink().'&quoi=oeuvre_records',
					'elements' => array()
			);		
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['authorities']['titre_uniforme']['event_works']['elements'][] = $row->oeuvre_event_tu_num;
			}
		}
		
		$query = "select notice_authperso_notice_num as notice_id from notices_authperso where notice_authperso_authority_num = ".$this->instance->get_num_object();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->entities['records']['authperso_records'] = array(
					'type' => 'authperso_records',
					'label' => $msg['entity_graph_record_which_use_authperso'],
					'link' => $this->instance->get_permalink().'&quoi=oeuvre_records',
					'img' => '',
					'elements' => array()
			);
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['records']['authperso_records']['elements'][] = $row->notice_id;
			}
		}
	}
	
	protected function get_entities_indexed(){
		global $msg;
		$concepts_ids = $this->instance->get_concepts_ids();
		if (!count($concepts_ids)) {
			return;
		}

		$query = 'select distinct num_object, type_object, id_authperso, authperso_name from index_concept left join authperso_authorities on num_object = id_authperso_authority and type_object = '.TYPE_AUTHPERSO.' left join authperso on id_authperso = authperso_authority_authperso_num where num_concept in ('.implode(',', $concepts_ids).') ';
		$result = pmb_mysql_query($query);
		
		if(pmb_mysql_num_rows($result)){
			if(!isset($this->entities['indexed_entities'])){
				$this->entities['indexed_entities'] = array();
			}
			while($row = pmb_mysql_fetch_object($result)){
				switch($row->type_object){
					case TYPE_AUTHOR:
						if(!isset($this->entities['indexed_entities']['authorities']['author']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['author']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_authors_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['author']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_CATEGORY:
						if(!isset($this->entities['indexed_entities']['authorities']['category']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['category']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_category_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['category']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_PUBLISHER:
						if(!isset($this->entities['indexed_entities']['authorities']['publisher']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['publisher']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_publishers_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['publisher']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_COLLECTION:
						if(!isset($this->entities['indexed_entities']['authorities']['collection']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['collection']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_collections_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['collection']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_SUBCOLLECTION:
						if(!isset($this->entities['indexed_entities']['authorities']['subcollection']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['subcollection']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_subcollections_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['subcollection']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_SERIE:
						if(!isset($this->entities['indexed_entities']['authorities']['serie']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['serie']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_series_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['serie']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_TITRE_UNIFORME:
						if(!isset($this->entities['indexed_entities']['authorities']['titre_uniforme']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['titre_uniforme']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_works_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['titre_uniforme']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_INDEXINT:
						if(!isset($this->entities['indexed_entities']['authorities']['indexint']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['indexint']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_indexint_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['indexint']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_AUTHPERSO:
						if(!isset($this->entities['indexed_entities']['authorities']['authperso']['common_indexed_authorities'])){
							$this->entities['indexed_entities']['authorities']['authperso']['common_indexed_authorities'] = array(
								'type' => 'common_indexed_authorities',
								'label' => $msg['entity_graph_authpersos_indexed_with'],
								'link' => $this->instance->get_permalink().'&quoi=common_indexed_authorities',
								'elements' => array()
							);
						}
						$this->entities['indexed_entities']['authorities']['authperso']['common_indexed_authorities']['elements'][] = $row->num_object;
						break;
					case TYPE_NOTICE: 
						if(!isset($this->entities['indexed_entities']['records']['common_indexed_records'])){
							$this->entities['indexed_entities']['records']['common_indexed_records'] = array(
									'type' => 'common_indexed_records',
									'label' => $msg['entity_graph_records_indexed_with'],
									'img' => notice::get_icon($row->num_object),
									'link' => $this->instance->get_permalink().'&quoi=common_indexed_records',
									'elements' => array()
							);
						}
						$this->entities['indexed_entities']['records']['common_indexed_records']['elements'][] = $row->num_object;
						break;
						
				}
			}
		}
	}
	
	public function get_json_entities_graphed($is_declarative = true) {
		if($is_declarative){
			$nodes = encoding_normalize::json_encode(array_values($this->entities_graphed['nodes']));
			$links = encoding_normalize::json_encode(array_values($this->entities_graphed['links']));
			return array('nodes'=>$nodes, 'links'=>$links);
		}
		$nodes = array_values($this->entities_graphed['nodes']);
		$links = array_values($this->entities_graphed['links']);
		return encoding_normalize::json_encode(array(
				'nodes' => $nodes,
				'links' => $links
		));
	}
	
	protected function get_entities_graphed_from_record(){
		global $msg;
		if (isset($this->entities)) {
			return $this->entities;
		}
		
		$this->entities = array('authorities' => array());
		
		// Récupération des auteurs
		$query = "select responsability_author as author_id from responsability where responsability_notice = ".$this->instance->get_id();
		$result = pmb_mysql_query($query);
		
		if (pmb_mysql_num_rows($result)) {
			$this->entities['authorities']['author']['author'] = array(
					'type' => 'author_records',
					'label' => $msg['entity_graph_authors_of_record'],
					'link' => $this->instance->get_permalink().'&quoi=oeuvre_records',
					'elements' => array()
			);
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['authorities']['author']['author']['elements'][] = $row->author_id;
			}
		}
		
		// Récupération des oeuvres portant cette notice
		$query = "select ntu_num_tu as tu_id from notices_titres_uniformes where ntu_num_notice = ".$this->instance->get_id();
		$result = pmb_mysql_query($query);
		
		if(pmb_mysql_num_rows($result)){
			$this->entities['authorities']['titre_uniforme']['common_linked_work'] = array(
					'type' => 'oeuvre_records',
					'label' => $msg['entity_graph_works_of_record'],
					'link' => $this->instance->get_permalink().'&quoi=oeuvre_records',
					'elements' => array()
			);
			while($row = pmb_mysql_fetch_object($result)){
				$this->entities['authorities']['titre_uniforme']['common_linked_work']['elements'][] = $row->tu_id;
			}
		}
		$this->get_entities_indexed();
		$this->get_entity_concepts();
		$this->get_entity_linked_records();
		$this->get_authperso_used_by_record();
		return $this->entities;
	}
	
	protected function get_entity_linked_records() {
		//Récupération des liens / types
		$links=array();
		$labelsup=marc_list_collection::get_instance("relationtypeup");
		$labelsdown=marc_list_collection::get_instance("relationtypedown");
		
		foreach($this->instance->notice_link as $type=>$typed_links) {
			foreach ($typed_links as $link) {
				$links[$link->get_relation_type()][]=$link->get_linked_notice();
			}
			
			foreach ($links as $relation_type => $notices) {
			    $label = $labelsup->table[$relation_type];
			    if($type == "down") {
			        $label = $labelsdown->table[$relation_type];
			    }
			    
				$this->entities["records"]["link_".$relation_type]= array(
						'type'=>'records',
						'label'=>$label,
						'link'=>'',
						'elements'=>$notices
				);
			}
		}
	}
	
	protected function get_prefix(){
		return ($this->type == 'authority' ? 'authority_' : 'record_') ;
	}
	
	protected function compute_entities($entities_array, $entities_type, $parent_node){
		global $msg;
		if(isset($entities_array[$entities_type]) && count($entities_array[$entities_type])){
			if($entities_type == 'indexed_entities'){
				if (!isset($this->entities_graphed['nodes'][$parent_node['id'].'_indexed_entities'])) {
					$node = array(
							'id' => $parent_node['id'].'_indexed_entities',
							'type' => 'subroot',
							'radius' => '15',
							'color' => self::get_color_from_type('indexed_entities'),
							'name' => $msg['entity_graph_talk_about'],
							'url' => ''
					);
					$this->entities_graphed['nodes'][$parent_node['id'].'_indexed_entities'] = $node;
				}
				
				$this->entities_graphed['links'][] = array(
						'source'=> $parent_node['id'],
						'target' => $parent_node['id'].'_indexed_entities',
						'color' => $parent_node['color']
				);
				foreach(array_keys($entities_array[$entities_type]) as $entity_type){
					$this->compute_entities($entities_array[$entities_type], $entity_type, $node);
				}
			}
			if($entities_type == 'indexed_concepts'){
				if (!isset($this->entities_graphed['nodes'][$parent_node['id'].'_indexed_concepts'])) {
					$node = array(
							'id' => $parent_node['id'].'_indexed_concepts',
							'type' => 'subroot',
							'radius' => '15',
							'color' => self::get_color_from_type('indexed_concept'),
							'name' => $msg['ontology_skos_menu'],
							'url' => ''
					);
					$this->entities_graphed['nodes'][$parent_node['id'].'_indexed_concepts'] = $node;
				}
				$this->entities_graphed['links'][] = array(
						'source'=> $parent_node['id'],
						'target' => $parent_node['id'].'_indexed_concepts',
						'color' => $parent_node['color']
				);
				foreach($entities_array[$entities_type] as $concept_indexed){
					$color = self::get_degradate($node['color']);
					$composed_concept_node = array(
							'id' => 'indexed_concepts_'.$concept_indexed['id'],
							'type' => 'subroot',
							'radius' => '15',
							'color' => $color,
							'name' => $concept_indexed['label'],
							'url' => $concept_indexed['link']
					);
					$this->entities_graphed['nodes'][$parent_node['id'].'_indexed_concepts_'.$concept_indexed['id']] = $composed_concept_node;
					$this->nb_nodes_graphed++;
					$this->entities_graphed['links'][] = array(
							'source'=> $parent_node['id'].'_indexed_concepts',
							'target' => 'indexed_concepts_'.$concept_indexed['id'],
							'color' => $node['color']
					);
					foreach($concept_indexed['elements'] as $entity_type => $concept_entities_array){
						/**
						 * Ajouter les noeuds selon leurs type au graph
						 */
						foreach($concept_entities_array as $entity_id){
							if($entity_type == 'authorities'){
								//$authority = new authority($entity_id);
								$authority = authorities_collection::get_authority('authority', $entity_id);
								if (!isset($this->entities_graphed['nodes'][$parent_node['id'].'_indexed_concepts_'.$concept_indexed['id'].'_'.$entity_type.'_'.$entity_id])) {
									$this->entities_graphed['nodes'][$entities_type.'_'.$authority->get_id()] = array(
											'id' => $entity_type.'_'.$entity_id,
											'type' => 'authorities_'.$authority->get_string_type_object(),
											'name' => $authority->header,
											'radius' => 11,
											'img' => $authority->get_type_icon(),
											'color' => self::get_color_from_type($authority->get_string_type_object()),
											'url' => $authority->get_authority_link(),
											'ajaxParams' => array('id' => $authority->get_id(), 'type' => 'authority')
									);
									$this->nb_nodes_graphed++;
								}
							}else{
								$this->entities_graphed['nodes'][$parent_node['id'].'_indexed_concepts_'.$concept_indexed['id'].'_'.$entity_type.'_'.$entity_id] = array(
										'id' => $entity_type.'_'.$entity_id,
										'type' => 'randomtype',
										'name' => notice::get_notice_title($entity_id),
										'url' => notice::get_permalink($entity_id).'&quoi=common_entity_graph',
										'img' => notice::get_icon($entity_id),
										'radius' => 10,
										'color' => self::get_color_from_type($entity_type),
										'ajaxParams' => array('id' => $entity_id, 'type' => 'record')
								);
								$this->nb_nodes_graphed++;
							}	
							$this->entities_graphed['links'][] = array(
									'source'=> 'indexed_concepts_'.$concept_indexed['id'],
									'target' =>  $entity_type.'_'.$entity_id,
									'color' => $color
							);
							
						}
					}
				}
			}
			if($entities_type == 'authorities'){
				foreach($entities_array[$entities_type] as $entities_pmb_type => $relations){
					foreach($relations as $relation_type => $data){
						if (count($data['elements'])) {
							$color = self::get_color_from_type($entities_pmb_type.'_'.$relation_type);
							if(!$color){
								if (isset($data['color']) && $data['color']) {
									$color = $data['color'];
								} else {
									$color = self::get_degradate($parent_node['color']);
								}
							}
 							if (!isset($this->entities_graphed['nodes'][$this->root_node_id.'_'.$entities_pmb_type.'_'.$relation_type])) {
								$this->entities_graphed['nodes'][$this->root_node_id.'_'.$entities_pmb_type.'_'.$relation_type] = array(
										'id' => $this->root_node_id.'_'.$entities_pmb_type.'_'.$relation_type,
										'type' => 'subroot',
										'radius' => '15',
										'name' => $data['label'],
										'url' => $data['link'],
										'color' => 	$color
								);
 							}
 							$this->entities_graphed['links'][] = array(
								'source'=> $parent_node['id'],
								'target' => $this->root_node_id.'_'.$entities_pmb_type.'_'.$relation_type,
								'color' => $parent_node['color']
							);
						}
						foreach($data['elements'] as $id){
							//$authority = new authority(0,$id,authority::get_const_type_object($entities_pmb_type));
							$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $id, 'type_object' => authority::get_const_type_object($entities_pmb_type)]);
							//Si le noeud principal est une oeuvre (un titre uniforme) et que l'objet que l'on
							//traite est une autorité perso, alors c'est un événement
							$color = self::get_color_from_type($entities_pmb_type);
							if($entities_pmb_type == "authperso" && $this->type == 'authority' && $this->instance->get_string_type_object() == 'titre_uniforme'){ 
								$color = self::get_color_from_type('event');
							}
			
							if (!isset($this->entities_graphed['nodes'][$entities_type.'_'.$authority->get_id()])) {
								$this->entities_graphed['nodes'][$entities_type.'_'.$authority->get_id()] = array(
										'id' => $entities_type.'_'.$authority->get_id(),
										'type' => $entities_type.'_'.$relation_type,
										'name' => $authority->header,
										'radius' => 11,
										'img' => $authority->get_type_icon(),
										'color' => $color,
										'url' => $authority->get_permalink().'&quoi=common_entity_graph',
										'ajaxParams' => array('id' => $authority->get_id(), 'type' => 'authority')
								);
								$this->nb_nodes_graphed++;
							}
							$this->entities_graphed['links'][] = array(
									'source'=> $this->root_node_id.'_'.$entities_pmb_type.'_'.$relation_type,
									'target' => $entities_type.'_'.$authority->get_id(),
									'color' => $color 
							);
						}
					}
				}
			}
			if($entities_type == "records"){
				foreach($entities_array[$entities_type] as $key => $data){
					if (count($data['elements'])) {
						$color = self::get_color_from_type($entities_type.'_'.$key);
						if(!$color){
							if (isset($data['color']) && $data['color']) {
								$color = $data['color'];
							} else {
								$color = self::get_degradate($parent_node['color']);
							}
						}
						if (!isset($this->entities_graphed['nodes'][$this->root_node_id.'_'.$entities_type.'_'.$key])) {
							$this->entities_graphed['nodes'][$this->root_node_id.'_'.$entities_type.'_'.$key] = array(
									'id' => $this->root_node_id.'_'.$entities_type.'_'.$key,
									'type' => 'subroot',
									'radius' => '15',
									'name' => $data['label'],
									'url' => $data['link'],
									'color' => $color
							);
						}
						$this->entities_graphed['links'][] = array(
								'source'=> $parent_node['id'],
								'target' => $this->root_node_id.'_'.$entities_type.'_'.$key,
								'color' => $parent_node['color']
						);
					}
					foreach($data['elements'] as $id){
						if (!isset($this->entities_graphed['nodes'][$entities_type.'_'.$id])) {
							$this->entities_graphed['nodes'][$entities_type.'_'.$id] = array(
									'id' => $entities_type.'_'.$id,
									'type' => $entities_type.'_'.$key,
									'name' => notice::get_notice_title($id),
									'url' => notice::get_permalink($id).'&quoi=common_entity_graph',
									'img' => notice::get_icon($id),
									'radius' => 10,
									'color' => self::get_color_from_type($entities_type),
									'ajaxParams' => array('id' => $id, 'type' => 'record')
							);
							$this->nb_nodes_graphed++;
						}
						$this->entities_graphed['links'][] = array(
								'source'=> $this->root_node_id.'_'.$entities_type.'_'.$key,
								'target' => $entities_type.'_'.$id,
								'color' => $color
						);
					}
				}
			}	
		}
	}
	
	public static function get_color_from_type($type){
		switch($type){
			case 'author':
			case 'authors':
			case 'author_author':
			case 'authorities_author':
			case 'authorities_performer':
				return '136,181,0';
			case 'indexed_entities':
				return '255,197,1';
			case 'record':
			case 'records':
			case 'records_oeuvre_records':
			case 'records_author_records':
			case 'records_authperso_records':
				return '215,23,62';
			case 'oeuvre_records':
				return '242,59,64';
			case 'work':
			case 'works':
			case 'titre_uniforme':
			case 'titre_uniforme_author':
			case 'authorities_common_linked_work':
				return '78,87,142';
			case 'category':
			case 'categories':
				return '92, 249, 249';
			case 'publisher':
			case 'publishers':
				return '92, 249, 249';
			case 'collection':
			case 'collections':
				return '72,106,105';
			case 'subcollection':
			case 'subcollections':
				return '74,156,142';
			case 'serie':
			case 'series':
				return '255,222,3';
			case 'indexint':
				return '248,135,163';
			case 'authperso':
				return '91,191,222';
			case 'authperso_event':
			case 'event':
				return '0,89,0';
			case 'indexed_concept':
			case 'concepts':
			case 'concept':
			    return '65,93,94';
			case 'expl':
			case 'expls':
			    return '225, 153, 76';
			case self::ADDITIONNAL_TYPE:
			default :
				return '';
		}
	}
	
	public static function get_degradate($color){
		$rgb = explode(',', $color);
		$new_color = ''; 
		foreach($rgb as $composant){
			$composant = intval($composant);
			if($new_color){
				$new_color.=',';
			}
			$new_color.= round($composant + ((255 - $composant) /2));
			
		}
		return $new_color;
	}
	
	public function get_recursive_graph($depth, $is_root = true) {
		$depth--;
		$this->get_entities_graphed($is_root);
		if ($depth > 0) {
			foreach ($this->entities as $type => $sub_type) {
				switch ($type) {
					case 'records' :
						foreach ($sub_type as $uses) {
							foreach ($uses['elements'] as $id) {
								$entity_graph = self::get_entity_graph(notice::get_notice($id), 'record');
// 								$this->entities_graphed = array_merge_recursive($this->entities_graphed, $entity_graph->get_recursive_graph($depth));
								$this->entities_graphed = self::entities_merge($this->entities_graphed, $entity_graph->get_recursive_graph($depth, false));
								$this->nb_nodes_graphed+= $entity_graph->get_nb_nodes_graphed();
							}
						}
						break;
					case 'authorities' :
						foreach ($sub_type as $type_object => $authority_type) {
							foreach ($authority_type as $uses) {
								foreach ($uses['elements'] as $id) {
									//$authority = new authority(0,$id,authority::get_const_type_object($type_object));
									$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $id, 'type_object' => authority::get_const_type_object($type_object)]);
									$entity_graph = self::get_entity_graph($authority, 'authority');						
									//$this->entities_graphed = array_merge_recursive($this->entities_graphed, $entity_graph->get_recursive_graph($depth));
									$this->entities_graphed = self::entities_merge($this->entities_graphed, $entity_graph->get_recursive_graph($depth, false));
									$this->nb_nodes_graphed+= $entity_graph->get_nb_nodes_graphed();
								}
							}
						}
						break;
				}
			}
		}
		return $this->entities_graphed;
	}

	public function get_nb_nodes_graphed(){
		return $this->nb_nodes_graphed;
	}
	
	public static function entities_merge($array1, $array2){
		return array('nodes'=>($array1['nodes'] + $array2['nodes']), 'links'=>array_merge($array1['links'], $array2['links']));
	}
	
	protected function get_entity_concepts() {
		if(!isset($this->entities['indexed_concepts'])){
			$this->entities['indexed_concepts'] = array();
		}
		
		if($this->instance->get_entity_type() == 'authority'){
			$index_concept = new index_concept($this->instance->get_num_object(), $this->instance->get_vedette_type());
		}else{
			$index_concept = new index_concept($this->instance->get_id(), TYPE_NOTICE);
		}
		
		$concepts = $index_concept->get_concepts();
		foreach($concepts as $concept){
			//$auhtority_concept = new authority(0, $concept->get_id(), AUT_TABLE_CONCEPT);
			$auhtority_concept = authorities_collection::get_authority('authority', 0, ['num_object' => $concept->get_id(), 'type_object' => AUT_TABLE_CONCEPT]);
			if ($vedette = $concept->get_vedette()) {
				$vedette_elements = $vedette->get_elements();
				if(count($vedette_elements)){
					$composed_concept = array(
							'type' => 'indexed_concept',
							'label' => $vedette->get_label(),
							'id' => $vedette->get_id(),
							'link' => $auhtority_concept->get_authority_link(),
							'elements' => array('records' => array(), 'authorities'=> array())
					);
					foreach ($vedette_elements as $elements) {
						foreach ($elements as $element) {
							//element = instance de vedette_element ; get_entity = instance de la classe liée (notice ou autorité) ;
							if($element->get_entity()->get_entity_type() == 'authority'){
								$composed_concept['elements']['authorities'][] = $element->get_entity()->get_id();
							}else{
								$composed_concept['elements']['records'][] = $element->get_entity()->get_id();
							}
							
						}
					}
					$this->entities['indexed_concepts'][] = $composed_concept;
				}
			}else{
				$composed_concept = array(
						'type' => 'indexed_concept',
						'label' => $concept->get_display_label(),
						'id' => $concept->get_id(),
						'link' => $auhtority_concept->get_authority_link(),
						'elements' => array('records' => array(), 'authority'=> array())
				);
				$this->entities['indexed_concepts'][] = $composed_concept;
			}
		}
	}
	
	protected function get_event_used_by_work(){
		global $msg;
		$query = "select oeuvre_event_authperso_authority_num from tu_oeuvres_events where oeuvre_event_tu_num = '".$this->instance->get_num_object()."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!isset($this->entities['authorities']['authperso']['event'])) {
					$this->entities['authorities']['authperso']['event'] = array(
							'type' => 'work_authperso',
							'label' => $msg['authority_tabs_titre_uniforme_evenements'],
							'link' => '',
							'elements' => array()
					);
				}
				$this->entities['authorities']['authperso']['event']['elements'][] = $row->oeuvre_event_authperso_authority_num;
			}
		}
		
	}
	
	protected function get_authperso_used_by_record(){
		global $msg;
		$query = "select notice_authperso_authority_num from notices_authperso where notice_authperso_notice_num = '".$this->instance->get_id()."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!isset($this->entities['authorities']['authperso']['authperso'])) {
					$this->entities['authorities']['authperso']['authperso'] = array(
							'type' => 'record_authperso',
							'label' => $msg['entity_graph_associated_authpersos'],
							'link' => '',
							'elements' => array()
					);
				}
				$this->entities['authorities']['authperso']['authperso']['elements'][] = $row->notice_authperso_authority_num;
			}
		}
	}
	
	private function elements_limited_of_entities($entities, $type, $parent_color = '')
	{
	    if (empty($entities[$type])) {
	        return array();
	    }
	    
	    if ($type == 'indexed_entities' || $type == 'indexed_concepts') {
	        // Cas spécifique qui contient des autorités et des notices
	        $entities_type = array_keys($entities[$type]);
	        $index = count($entities_type);
	        for ($i = 0; $i < $index; $i++) {
	            $color_link = self::get_color_from_type($type);
	            $entities[$type][$entities_type[$i]] = $this->elements_limited_of_entities($entities[$type], $entities_type[$i], $color_link);
	        }
	    } else {
	        foreach ($entities[$type] as $key => $relations) {
	            switch ($type) {
	                case 'authorities':
	                    $color_link = self::get_color_from_type($key);
	                    if($key == "authperso" && $this->type == 'authority' && $this->instance->get_string_type_object() == 'titre_uniforme'){
	                        $color_link = self::get_color_from_type('event');
	                    }
	                    foreach($relations as $relation_type => $node) {
	                        $entities[$type][$key][$relation_type]['elements'] = $this->get_elements_limited_of_node($node, $type, $color_link, $relation_type, $key);
	                    }
	                    break;
	                case 'records':
	                    // ICI la relations correspond au noeud dans le graphe
	                    $node = $relations;
	                    $color_link = self::get_color_from_type($type.'_'.$key);
	                    if(!$color_link){
	                        $color_link = self::get_degradate($parent_color);
	                    }
	                    $entities[$type][$key]['elements'] = $this->get_elements_limited_of_node($node, $type, $color_link);
	                    break;
	            }
	        }
	    }
	    return $entities[$type];
	}
	
	private function get_elements_limited_of_node($node, $entities_type, $color_link = "", $relation_type = "", $entities_pmb_type = "") {
	    global $opac_entity_graph_limit;
	    if (empty($node['elements'])) {
	        return array();
	    }
	    
	    $nodes_limited = array();
	    if ($opac_entity_graph_limit != 0 && count($node['elements']) > $opac_entity_graph_limit) {
	        $nodes_limited = array_slice($node['elements'], 0, $opac_entity_graph_limit);
	        $additional_nodes = array_slice($node['elements'], $opac_entity_graph_limit, count($node['elements'])-1);
	        $this->add_additionnal_nodes($additional_nodes, $node['type'], $entities_type, $color_link, $relation_type, $entities_pmb_type);
	    } else {
	        $nodes_limited = $node['elements'];
	    }
	    return $nodes_limited;
	}
	
	protected function add_additionnal_nodes($additional_nodes, $node_type, $entities_type, $color_link = "", $relation_type = "",  $entities_pmb_type = "") {
	    global $msg, $opac_entity_graph_limit;
	    
	    // On reconstruit l'identifiant du noeud parent
	    if ($entities_type == "records") {
	        $source = $this->root_node_id . "_" . $entities_type . "_" . $node_type;
	    } elseif ($entities_type == "authorities") {
	        $source = $this->root_node_id . "_" . $entities_pmb_type . "_" . $relation_type;
	    } else {
	        return;
	    }
	    //var_dump($entities_type, $source, $node_type, $entities_type, $relation_type, $entities_pmb_type);
	    
	    // Creation du noeud additionnel
	    $node_id = "additionnals_" . $source;
	    if (!isset($this->entities_graphed['nodes'][$node_id])) {
	        $this->entities_graphed['nodes'][$node_id] = array(
	            'id' => $node_id,
	            'type' => self::ADDITIONNAL_TYPE,
	            'radius' => self::NODE_SUBROOT_RADUIS,
	            'color' => entity_graph::get_color_from_type(self::ADDITIONNAL_TYPE),
	            'name' => sprintf($msg['graph_node_limited'], count($additional_nodes)),
	            'url' => '',
	            'limit' => $opac_entity_graph_limit,
	            'elements' => $additional_nodes,
	            'info' => array( // info pour la requête ajax
	                'elements' => [],
	                'entities_pmb_type' => $entities_pmb_type,
	                'entities_type' => $entities_type,
	                'node_type' => $node_type,
	                'link' =>  array(
	                    'source'=> $source,
	                    'color' => $color_link ?? ""
	                )
	            )
	        );
	    }
	    
	    // Creation du lien
	    $this->entities_graphed['links'][] = array(
	        'source'=> $source,
	        'target' => $node_id,
	        'color' => $color_link ?? ""
	    );
	}
	
	
	/**
	 * @param array $node
	 * @return array
	 */
	public static function make_additionnal_nodes($node) {
	    
	    $entities_graphed = array();
	    $entities_graphed['nodes'] = array();
	    $entities_graphed['links'] = array();
	    
	    // On évite les doublons d'identifiant
	    $node['elements'] = array_unique($node['elements']);
	    $index = count($node['elements']);
	    for ($i = 0; $i < $index; $i++) {
	        
	        $node_id = $name = $url = $img = $radius = "";
	        $ajax_type = $node['entities_type'];
	        $entity_id = $node['elements'][$i];
	        switch ($node['entities_type']) {
	            case 'expls':
	                $node_id = 'expls_' . $entity_id;
	                $name = exemplaire::get_expl_isbd($entity_id);
	                $radius = 10;
	                
	                $record_id = exemplaire::get_expl_notice_from_id($entity_id);
	                if (empty($record_id)) {
	                    $issue_id = exemplaire::get_expl_bulletin_from_id($entity_id);
	                    $url = bulletinage::get_permalink($issue_id);
	                } else {
	                    $url = notice::get_permalink($record_id) . '&quoi=common_entity_graph';
	                }
	                // On met 0 pour l'ajaxParams
                    $entity_id = 0;
	                break;
	            case 'records':
	                $node_id = 'records_' . $entity_id;
	                $name = notice::get_notice_title($entity_id);
	                $url = notice::get_permalink($entity_id) . '&quoi=common_entity_graph';
	                $img = notice::get_icon($entity_id);
	                $radius = 10;
	                $ajax_type = "record";
	                break;
	            case 'authorities':
	                $authority = authorities_collection::get_authority('authority', 0, ['num_object' => $entity_id, 'type_object' => authority::get_const_type_object($node['entities_pmb_type'])]);
	                $node_id = 'authorities_' . $authority->get_id();
	                $name = $authority->isbd;
	                $url = $authority->get_permalink() . '&quoi=common_entity_graph';
	                $img = $authority->get_type_icon();
	                $radius = 11;
	                $ajax_type = "authority";
	                $entity_id = $authority->get_id();
	                break;
	            default:
	                return ['nodes' => [], 'links' => []];
	        }
	        
	        $color = self::get_color_from_type($node['entities_type'] . '_' . $node['entities_pmb_type']);
	        if (!$color) {
	            $color = self::get_color_from_type($node['entities_type']);
	        }
	        if (!$color) {
	            $color = self::get_color_from_type($node['entities_pmb_type']);
	        }
	        if (!$color) {
	            $color = self::get_degradate($node['link']['color']);
	        }
	        
	        $entities_graphed['nodes'][] = array(
	            'id' => $node_id,
	            'type' => $node['entities_type'] . '_'.$node['node_type'],
	            'name' => $name,
	            'url' => $url,
	            'img' => $img,
	            'radius' => $radius,
	            'color' => $color,
	            'ajaxParams' => array('id' => $entity_id, 'type' => $ajax_type)
	        );
	        
	        $entities_graphed['links'][] = array(
	            'source'=> $node['link']['source'],
	            'target' => $node_id,
	            'color' => $node['link']['color']
	        );
	    }
	    
	    return $entities_graphed;
	}
}