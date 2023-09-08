<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_graph.class.php,v 1.6 2018-07-26 15:25:52 tsamson Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );
	
	// require_once($class_path.'/authorities/tabs/authority_tabs.class.php');
	// require_once($class_path.'/authority.class.php');
require_once ($class_path . '/entity_graph.class.php');
require_once ($class_path . '/index_concept.class.php');
require_once ($class_path . '/notice.class.php');
require_once ($class_path . '/marc_table.class.php');
class frbr_entity_graph extends entity_graph {
	
	/**
	 * données provenant des cadres
	 * 
	 * @var array
	 */
	protected static $cadres_data;
	protected static $entity_graph = array ();
	
	/**
	 *
	 * @param unknown $instance        	
	 * @param unknown $type        	
	 * @return entity_graph
	 */
	public static function get_entity_graph($instance, $type) {
		if (! isset ( self::$entity_graph [$type] [$instance->get_id ()] )) {
			self::$entity_graph [$type] [$instance->get_id ()] = new frbr_entity_graph ( $instance, $type );
		}
		return self::$entity_graph [$type] [$instance->get_id ()];
	}
	
	public function get_entities_graphed($is_root = true) {
		if (isset ( $this->entities_graphed )) {
			return $this->entities_graphed;
		}
		$this->entities_graphed = array (
				'nodes' => array (),
				'links' => array () 
		);
		$nb_result = 0;
		
		switch ($this->type) {
			case 'authority' :
				if (! isset ( $this->entities_graphed ['nodes'] ['authorities_' . $this->instance->get_id ()] )) {
					$type = $this->instance->get_string_type_object ();
					if ($type == "authperso" && $this->instance->get_object_instance ()->is_event ()) {
						$type = "event";
					}
					$node = array (
							'id' => 'authorities_' . $this->instance->get_id (),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type ( $type ),
							'name' => $this->instance->isbd,
							'url' => $this->instance->get_permalink () . '&quoi=common_entity_graph',
							'img' => $this->instance->get_type_icon () 
					);
					if ($is_root) {
						$this->entities_graphed ['nodes'] ['authorities_' . $this->instance->get_id ()] = $node;
					}
				}
				$this->root_node_id = 'authorities_' . $this->instance->get_id ();
				break;
			case 'record' :
				if (! isset ( $this->entities_graphed ['nodes'] ['records_' . $this->instance->get_id ()] )) {
					$node = array (
							'id' => 'records_' . $this->instance->get_id (),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type ( 'record' ),
							'name' => notice::get_notice_title ( $this->instance->get_id () ),
							'url' => notice::get_permalink ( $this->instance->get_id () ),
							'img' => notice::get_icon ( $this->instance->get_id () ) 
					);
					if ($is_root) {
						$this->entities_graphed ['nodes'] ['records_' . $this->instance->get_id ()] = $node;
					}
				}
				$this->root_node_id = 'records_' . $this->instance->get_id ();
				break;
		}
		if (count ( self::$cadres_data )) {
			foreach ( self::$cadres_data as $key => $cadre_data ) {
				$entity_type = substr ( $key, 0, strpos ( $key, '_' ) );
				if (isset ( $cadre_data ['parent_node'] ) && $cadre_data ['parent_node']) {
					$this->root_node_id = $cadre_data ['parent_node'] ['id'];
					$this->compute_entities ( array (
							$entity_type => $cadre_data ['node'] 
					), $entity_type, $cadre_data ['parent_node'] );
				} else {
					$this->root_node_id = $cadre_data ['entity_type'] . '_' . $this->instance->get_id ();
					$this->compute_entities ( array (
							$entity_type => $cadre_data 
					), $entity_type, $node );
				}
			}
		}
		
		return $this->entities_graphed;
	}
	
	public static function add_nodes($data, $id, $name, $type, $parent_id = '', $parent_type = '') {
		switch ($type) {
			case 'records' :
				self::add_records_nodes ( $data, $id, $name, $parent_id, $parent_type );
				break;
			default :
				self::add_authorities_nodes ( $data, $id, $name, $type, $parent_id, $parent_type );
				break;
		}
	}
	
	protected static function add_records_nodes($data, $id, $name, $parent_id = '', $parent_type = '') {
		$node = array (
				'id' => 'records_' . $id,
				'type' => 'subroot',
				'radius' => '15',
				'color' => entity_graph::get_color_from_type ( 'records' ),
				'label' => $name,
				'url' => '' 
		);
		if ($parent_id) {
			self::$cadres_data ['records_' . $id] ['parent_node'] = array (
					'id' => $parent_id,
					'color' => entity_graph::get_color_from_type ( $parent_type ) 
			);
			self::$cadres_data ['records_' . $id] ['node'] ['records'] = $node;
			self::$cadres_data ['records_' . $id] ['node'] ['records'] ['elements'] = $data;
		} else {
			self::$cadres_data ['records_' . $id] ['records'] = $node;
			self::$cadres_data ['records_' . $id] ['records'] ['elements'] = $data;
		}
	}
	
	protected static function add_authorities_nodes($data, $id, $name, $type, $parent_id = '', $parent_type = '') {
		$node = array (
				'id' => 'authorities_' . $id,
				'type' => 'subroot',
				'radius' => '15',
				'color' => entity_graph::get_color_from_type ( $type ),
				'label' => $name,
				'url' => '' 
		);
		$cadre_id = explode ( '_', $id );
		$cadre_id = $cadre_id [0];
		if ($parent_id) {
			self::$cadres_data ['authorities_' . $id] ['parent_node'] = array (
					'id' => $parent_id,
					'color' => entity_graph::get_color_from_type ( $parent_type ) 
			);
			self::$cadres_data ['authorities_' . $id] ['node'] [$type] ['authorities_' . $cadre_id] = $node;
			self::$cadres_data ['authorities_' . $id] ['node'] [$type] ['authorities_' . $cadre_id] ['elements'] = $data;
		} else {
			self::$cadres_data ['authorities_' . $id] [$type] ['authorities_' . $cadre_id] = $node;
			self::$cadres_data ['authorities_' . $id] [$type] ['authorities_' . $cadre_id] ['elements'] = $data;
		}
	}
	
	/**
	 * dérivation de la methode pour les graphes des pages frbr. 
	 * on utilise l'id des cadres pour distinguer les differents noeuds de liaison
	 * cela permet de corriger le cas ou tous les noeuds d'un meme type se retrouvent lies a un seul noeud de liaison
	 * @see entity_graph::compute_entities()
	 */
	protected function compute_entities($entities_array, $entities_type, $parent_node) {
		global $msg;
		if (isset ( $entities_array [$entities_type] ) && count ( $entities_array [$entities_type] )) {
			if ($entities_type == 'indexed_entities') {
				if (! isset ( $this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_entities'] )) {
					$node = array (
							'id' => $parent_node ['id'] . '_indexed_entities',
							'type' => 'subroot',
							'radius' => '15',
							'color' => self::get_color_from_type ( 'indexed_entities' ),
							'name' => $msg ['entity_graph_talk_about'],
							'url' => '' 
					);
					$this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_entities'] = $node;
				}
				
				$this->entities_graphed ['links'] [] = array (
						'source' => $parent_node ['id'],
						'target' => $parent_node ['id'] . '_indexed_entities',
						'color' => $parent_node ['color'] 
				);
				foreach ( array_keys ( $entities_array [$entities_type] ) as $entity_type ) {
					$this->compute_entities ( $entities_array [$entities_type], $entity_type, $node );
				}
			}
			
			if ($entities_type == 'indexed_concepts') {
				if (! isset ( $this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_concepts'] )) {
					$node = array (
							'id' => $parent_node ['id'] . '_indexed_concepts',
							'type' => 'subroot',
							'radius' => '15',
							'color' => self::get_color_from_type ( 'indexed_concept' ),
							'name' => $msg ['ontology_skos_menu'],
							'url' => '' 
					);
					$this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_concepts'] = $node;
				}
				$this->entities_graphed ['links'] [] = array (
						'source' => $parent_node ['id'],
						'target' => $parent_node ['id'] . '_indexed_concepts',
						'color' => $parent_node ['color'] 
				);
				foreach ( $entities_array [$entities_type] as $concept_indexed ) {
					$color = self::get_degradate ( $node ['color'] );
					$composed_concept_node = array (
							'id' => 'indexed_concepts_' . $concept_indexed ['id'],
							'type' => 'subroot',
							'radius' => '15',
							'color' => $color,
							'name' => $concept_indexed ['label'],
							'url' => $concept_indexed ['link'] 
					);
					$this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_concepts_' . $concept_indexed ['id']] = $composed_concept_node;
					$this->nb_nodes_graphed ++;
					$this->entities_graphed ['links'] [] = array (
							'source' => $parent_node ['id'] . '_indexed_concepts',
							'target' => 'indexed_concepts_' . $concept_indexed ['id'],
							'color' => $node ['color'] 
					);
					foreach ( $concept_indexed ['elements'] as $entity_type => $concept_entities_array ) {
						/**
						 * Ajouter les noeuds selon leurs type au graph
						 */
						foreach ( $concept_entities_array as $entity_id ) {
							if ($entity_type == 'authorities') {
								$authority = authorities_collection::get_authority('authority', $entity_id);
								if (! isset ( $this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_concepts_' . $concept_indexed ['id'] . '_' . $entity_type . '_' . $entity_id] )) {
									$this->entities_graphed ['nodes'] [$entities_type . '_' . $authority->get_id ()] = array (
											'id' => $entity_type . '_' . $entity_id,
											'type' => 'authorities_' . $authority->get_string_type_object (),
											'name' => $authority->isbd,
											'radius' => 11,
											'img' => $authority->get_type_icon (),
											'color' => self::get_color_from_type ( $authority->get_string_type_object () ),
											'url' => $authority->get_authority_link (),
											'ajaxParams' => array (
													'id' => $authority->get_id (),
													'type' => 'authority' 
											) 
									);
									$this->nb_nodes_graphed ++;
								}
							} else {
								$this->entities_graphed ['nodes'] [$parent_node ['id'] . '_indexed_concepts_' . $concept_indexed ['id'] . '_' . $entity_type . '_' . $entity_id] = array (
										'id' => $entity_type . '_' . $entity_id,
										'type' => 'randomtype',
										'name' => notice::get_notice_title ( $entity_id ),
										'url' => notice::get_permalink ( $entity_id ) . '&quoi=common_entity_graph',
										'img' => notice::get_icon ( $entity_id ),
										'radius' => 10,
										'color' => self::get_color_from_type ( $entity_type ),
										'ajaxParams' => array (
												'id' => $entity_id,
												'type' => 'record' 
										) 
								);
								$this->nb_nodes_graphed ++;
							}
							$this->entities_graphed ['links'] [] = array (
									'source' => 'indexed_concepts_' . $concept_indexed ['id'],
									'target' => $entity_type . '_' . $entity_id,
									'color' => $color 
							);
						}
					}
				}
			}
			if ($entities_type == 'authorities') {
				foreach ( $entities_array [$entities_type] as $entities_pmb_type => $relations ) {
					foreach ( $relations as $relation_type => $data ) {
						if (count ( $data ['elements'] )) {
							$color = self::get_color_from_type ( $entities_pmb_type . '_' . $relation_type );
							if (! $color) {
								if (isset ( $data ['color'] ) && $data ['color']) {
									$color = $data ['color'];
								} else {
									$color = self::get_degradate ( $parent_node ['color'] );
								}
							}
							if (! isset ( $this->entities_graphed ['nodes'] [$this->root_node_id . '_' . $entities_pmb_type . '_' . $relation_type . '_' . $data ['id']] )) {
								$this->entities_graphed ['nodes'] [$this->root_node_id . '_' . $entities_pmb_type . '_' . $relation_type . '_' . $data ['id']] = array (
										'id' => $this->root_node_id . '_' . $entities_pmb_type . '_' . $relation_type . '_' . $data ['id'],
										'type' => 'subroot',
										'radius' => '15',
										'name' => $data ['label'],
										'url' => $data ['link'],
										'color' => $color 
								);
							}
							$this->entities_graphed ['links'] [] = array (
									'source' => $parent_node ['id'],
									'target' => $this->root_node_id . '_' . $entities_pmb_type . '_' . $relation_type . '_' . $data ['id'],
									'color' => $parent_node ['color'] 
							);
						}
						foreach ( $data ['elements'] as $id ) {
							//$authority = new authority ( 0, $id, authority::get_const_type_object ( $entities_pmb_type ) );
							$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $id, 'type_object' => authority::get_const_type_object ( $entities_pmb_type )]);
							//var_dump($authority);
							// Si le noeud principal est une oeuvre (un titre uniforme) et que l'objet que l'on
							// traite est une autorité perso, alors c'est un événement
							$color = self::get_color_from_type ( $entities_pmb_type );
							if ($entities_pmb_type == "authperso" && $this->type == 'authority' && $this->instance->get_string_type_object () == 'titre_uniforme') {
								$color = self::get_color_from_type ( 'event' );
							}
							
							if (! isset ( $this->entities_graphed ['nodes'] [$entities_type . '_' . $authority->get_id ()] )) {
								$this->entities_graphed ['nodes'] [$entities_type . '_' . $authority->get_id ()] = array (
										'id' => $entities_type . '_' . $authority->get_id (),
										'type' => $entities_type . '_' . $relation_type,
										'name' => $authority->isbd,
										'radius' => 11,
										'img' => $authority->get_type_icon (),
										'color' => $color,
										'url' => $authority->get_permalink () . '&quoi=common_entity_graph',
										'ajaxParams' => array (
												'id' => $authority->get_id (),
												'type' => 'authority' 
										) 
								);
								$this->nb_nodes_graphed ++;
							}
							$this->entities_graphed ['links'] [] = array (
									'source' => $this->root_node_id . '_' . $entities_pmb_type . '_' . $relation_type . '_' . $data ['id'],
									'target' => $entities_type . '_' . $authority->get_id (),
									'color' => $color 
							);
						}
					}
				}
			}
			if ($entities_type == "records") {				
				foreach ( $entities_array [$entities_type] as $key => $data ) {
					if (count ( $data ['elements'] )) {
						$color = self::get_color_from_type ( $entities_type . '_' . $key );
						if (! $color) {
							if (isset ( $data ['color'] ) && $data ['color']) {
								$color = $data ['color'];
							} else {
								$color = self::get_degradate ( $parent_node ['color'] );
							}
						}
						if (! isset ( $this->entities_graphed ['nodes'] [$this->root_node_id . '_' . $entities_type . '_' . $key . '_' . $data ['id']] )) {
							$this->entities_graphed ['nodes'] [$this->root_node_id . '_' . $entities_type . '_' . $key . '_' . $data ['id']] = array (
									'id' => $this->root_node_id . '_' . $entities_type . '_' . $key . '_' . $data ['id'],
									'type' => 'subroot',
									'radius' => '15',
									'name' => $data ['label'],
									'url' => $data ['link'],
									'color' => $color 
							);
						}
						$this->entities_graphed ['links'] [] = array (
								'source' => $parent_node ['id'],
								'target' => $this->root_node_id . '_' . $entities_type . '_' . $key . '_' . $data ['id'],
								'color' => $parent_node ['color'] 
						);
					}
					foreach ( $data ['elements'] as $id ) {
						if (! isset ( $this->entities_graphed ['nodes'] [$entities_type . '_' . $id] )) {
							$this->entities_graphed ['nodes'] [$entities_type . '_' . $id] = array (
									'id' => $entities_type . '_' . $id,
									'type' => $entities_type . '_' . $key,
									'name' => notice::get_notice_title ( $id ),
									'url' => notice::get_permalink ( $id ) . '&quoi=common_entity_graph',
									'img' => notice::get_icon ( $id ),
									'radius' => 10,
									'color' => self::get_color_from_type ( $entities_type ),
									'ajaxParams' => array (
											'id' => $id,
											'type' => 'record' 
									) 
							);
							$this->nb_nodes_graphed ++;
						}
						$this->entities_graphed ['links'] [] = array (
								'source' => $this->root_node_id . '_' . $entities_type . '_' . $key . '_' . $data ['id'],
								'target' => $entities_type . '_' . $id,
								'color' => $color 
						);
					}
				}
			}
		}
	}
}