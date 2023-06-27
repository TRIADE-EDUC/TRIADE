<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_build.class.php,v 1.5 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/autoloader.class.php");
if(!isset($autoloader)) {
	$autoloader = new autoloader();
}
$autoloader->add_register("frbr_entities",true);

require_once($class_path."/frbr/frbr_pages.class.php");

class frbr_build {
	
	protected $object_id;
	
	protected $object_type;
	
	protected $page;
	
	protected $cadres;
	
	protected $datanodes_data;
	
	protected $datanodes_tree;
	
	protected static $instances = array();
	
	/**
	 * 
	 * @var array frbr_entity_common_entity_datanode
	 */
	protected $datanodes;
	
	public function __construct($object_id=0, $object_type='') {
	    $this->object_id = (int) $object_id;
		$this->object_type = $object_type;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->cadres = array();
		if($this->object_id && $this->object_type) {
			$num_page = 0;
			$frbr_pages = new frbr_pages($this->object_type);
			foreach ($frbr_pages->get_pages() as $page) {
				$frbr_entity_class_name = 'frbr_entity_'.$page->get_entity().'_page';
				$frbr_entity_instance = new $frbr_entity_class_name($page->get_id());
				if(isset($frbr_entity_instance->get_backbone()['id']) && $frbr_entity_instance->get_backbone()['id']) {
					$frbr_backbone_fields = new frbr_backbone_fields($frbr_entity_instance->informations['indexation']['type'], $frbr_entity_instance->informations['indexation']['path']);
					$indice = 'backbone'.$frbr_entity_instance->get_backbone()['data']->id;
					$to_unformat = $frbr_entity_instance->get_managed_datas()['backbones'][$indice]['fields'];
					$frbr_backbone_fields->unformat_fields($to_unformat);
					$filtered_data = $frbr_backbone_fields->filter_datas(array($this->object_id));
					if(isset($filtered_data[0]) && $filtered_data[0] == $this->object_id) {
						$num_page = $page->get_id();
						break;
					}
				} else {
					$num_page = $page->get_id();
					break;
				}
			}
			$this->page = new frbr_entity_common_entity_page($num_page);
			if($this->page->get_id()) {
				$query = 'SELECT * FROM frbr_place
					LEFT JOIN frbr_cadres ON place_num_cadre = id_cadre 
					LEFT JOIN frbr_cadres_content ON cadre_content_num_cadre = id_cadre
					WHERE place_num_page = "'.$this->page->get_id().'" AND (place_visibility=1 OR cadre_visible_in_graph = 1) ORDER BY place_order';
				$result = pmb_mysql_query($query);
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->cadres[] = array(
							'id' => $row->id_cadre,
							'name' => $row->cadre_name,
							'cadre_object' => $row->cadre_object,
							'cadre_type' => $row->place_cadre_type,
							'id_cadre_content' => $row->id_cadre_content,
							'cadre_content_object' => $row->cadre_content_object,
							'cadre_datanodes_path' => $row->cadre_datanodes_path,
							'place_visibility' => $row->place_visibility,
							'cadre_visible_in_graph' => $row->cadre_visible_in_graph
					);
				}
				
				$query = '
				    SELECT id_datanode 
				    FROM frbr_datanodes
					WHERE datanode_num_page = "'.$this->page->get_id().'"';
				$result = pmb_mysql_query($query);
				while ($row = pmb_mysql_fetch_assoc($result)) {
				    if (!isset($this->datanodes[$row['id_datanode']])) {
				        $this->datanodes[$row['id_datanode']] = frbr_entity_common_entity_datanode::get_instance($row['id_datanode']);
				    }
				}
			}
		}
	}
	
	public function has_page() {
		if(isset($this->page) && $this->page->get_id()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function has_cadres() {
		if(count($this->cadres)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_object_id() {
		return $this->object_id;
	}
	
	public function get_object_type() {
		return $this->object_type;
	}

	public function get_page() {
		return $this->page;
	}
	
	public function get_cadres() {
		return $this->cadres;
	}	

	public function get_datanodes_data() {
		if (isset($this->datanodes_data)) {
			return $this->datanodes_data;
		}
		$this->datanodes_data = array();
		$this->datanodes_tree = array(
				0 => array(
						'children' => array(),
						'cadres' => array(),
						'type' => $this->object_type
				)
		);
		foreach ($this->cadres as $cadre) {
			$parent_data = array($this->object_id);
			if ($cadre['cadre_datanodes_path']) {
				$datanode_ids = explode('/',$cadre['cadre_datanodes_path']);
				for ($i = 0; $i < count($datanode_ids); $i++) {
					if (!isset($this->datanodes_data[$datanode_ids[$i]])) {
						$datanode = frbr_entity_common_entity_datanode::get_instance($datanode_ids[$i]);
						$raw_data = $datanode->get_datanode_datas($parent_data);
						$filter_data = $datanode->filter_data($raw_data);
						if ($datanode->has_children_filter()) {
						    $operator = $datanode->get_children_filter()['data']->children_filter_operator;
						    //$this->filter_by_children_data($datanode_ids[$i], $this->datanodes_data[$datanode_ids[$i]][0]);
						    $children_filter_data = $this->filter_by_children_data($datanode_ids[$i], ($operator == "and" ? $filter_data : $raw_data));
						    $filter_data = $this->merge_datanode_data($filter_data, $children_filter_data, $operator);
						}						
						$this->datanodes_data[$datanode_ids[$i]] = $datanode->sort_data($filter_data);
					}
					if (isset($this->datanodes_data[$datanode_ids[$i]][0])) {
						$parent_data = $this->datanodes_data[$datanode_ids[$i]][0];
					} else {
						$parent_data = array();
					}
					if (!isset($this->datanodes_tree[$datanode_ids[$i]])) {
						$this->datanodes_tree[$datanode_ids[$i]] = array(
								'children' => array(),
								'cadres' => array(),
								'type' => $datanode->get_entity_type()
						);
					}
					if (!in_array($datanode_ids[$i], $this->datanodes_tree[(($i > 0) ? $datanode_ids[$i-1] : 0)]['children'])) {
						$this->datanodes_tree[(($i > 0) ? $datanode_ids[$i-1] : 0)]['children'][] = $datanode_ids[$i];
					}
					if ($i == (count($datanode_ids)-1)) {
						$this->datanodes_tree[$datanode_ids[$i]]['cadres'][] = $cadre;
					}
				}
			}
		}
		$this->set_graph_data();
		return $this->datanodes_data;
	}
	
	protected function set_graph_data($parent_datanode = 0, $parent_type = '', $parent_id = '', $parent_node_id= '') {
		$flag = false;
		if (!$parent_id) {
			$parent_id = $this->object_id;
		}		
		if ($parent_datanode) {
			foreach($this->datanodes_tree[$parent_datanode]['cadres'] as $cadre) {
				$flag = true;				
				if (isset($this->datanodes_data[$parent_datanode][$parent_id])) {
					$children_data = $this->datanodes_data[$parent_datanode][$parent_id];
					if ($cadre['cadre_visible_in_graph']) {												
						$type = $this->get_type_from_class_name($cadre['cadre_object']);
						$cadre_id = $cadre['id'].($parent_id ? '_'.$parent_id : '');
						frbr_entity_graph::add_nodes($children_data, $cadre_id, $cadre['name'], $type, $parent_node_id, $parent_type);
					}
					foreach($this->datanodes_tree[$parent_datanode]['children'] as $child) {
						foreach ($children_data as $child_data) {
							if ($cadre['cadre_visible_in_graph']) {
								switch ($type) {
									case 'records' :
										$parent_node_id = 'records_'.$child_data;
										break;
									default :
										//$authority = new authority(0,$child_data,authority::get_const_type_object($type));
										$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $child_data, 'type_object' => authority::get_const_type_object($type)]);
										$parent_node_id = 'authorities_'.$authority->get_id();
										break;
								}
							}
							$this->set_graph_data($child, ($type ? $type : $parent_type), $child_data, $parent_node_id);
						}
					}
				}
			}
		}
		if (!$flag) {
			$children_data = array($parent_id);
			if (isset($this->datanodes_data[$parent_datanode][$parent_id])) {
				$children_data = $this->datanodes_data[$parent_datanode][$parent_id];
			}
			foreach($this->datanodes_tree[$parent_datanode]['children'] as $child) {
				foreach ($children_data as $child_data) {
					$this->set_graph_data($child, $parent_type, $child_data);
				}
			}
		}
	}
	
	protected function filter_by_children_data($datanode_id, $parent_data) {
	    if (isset($this->datanodes[$datanode_id])) {
	        //$operator = $this->datanodes[$datanode_id]->get_children_filter()['data']->children_filter_operator;
    	    $children_filter = $this->datanodes[$datanode_id]->get_children_filter()['data']->children_filter;
    	    if (!empty($children_filter)) {
    	        foreach ($children_filter as $id => $value) {
    	            if (!isset($this->datanodes[$id])) {
    	                continue;
    	            }
    	            if (!isset($this->datanodes_data[$id])) {
                        $child_raw_data = $this->datanodes[$id]->get_datanode_datas($parent_data[0]);
                        $child_data = $this->datanodes[$id]->filter_data($child_raw_data);
                        if ($this->datanodes[$id]->has_children_filter()) {
                            $operator = $this->datanodes[$id]->get_children_filter()['data']->children_filter_operator;
                            $sub_child_data = $this->filter_by_children_data($id, ($operator == "and" ? $child_data : $child_raw_data));
                            $child_data = $this->merge_datanode_data($child_data, $sub_child_data, $operator);
                        }
                        $this->datanodes_data[$id] = $this->datanodes[$id]->sort_data($child_data);;
    	            } else {
    	                $child_data = $this->datanodes_data[$id];
    	            }
    	            
    	            if ($value == 1) { //ne doit pas etre vide
    	                if (count($child_data) == 0) {
    	                    //le jeu de donnees n'a pas de donnees, donc on reset
    	                    $parent_data = array();
    	                    return $parent_data;
    	                } else {
    	                    foreach ($child_data as $sub_id => $sub_value) {
    	                        if ($sub_id && count($sub_value) == 0) {
    	                            foreach ($parent_data as $key => $tab) {    	                                
    	                                $ind = array_search($sub_id, $tab);
    	                                if ($ind !== false) {
    	                                    unset($parent_data[$key][$ind]);
    	                                }
    	                            }
    	                            unset($child_data[$sub_id]);
    	                        }
    	                    }
    	                }
    	            } elseif ($value == 2) { //doit etre vide
    	                if (count($child_data) > 0) {
    	                    foreach ($child_data as $sub_id => $sub_value) {
    	                        if ($sub_id && count($sub_value) > 0) {
    	                            foreach ($parent_data as $key => $tab) {
    	                                $ind = array_search($sub_id, $tab);
    	                                if ($ind !== false) {
    	                                    unset($parent_data[$key][$ind]);
    	                                }
    	                            }
    	                            unset($child_data[$sub_id]);
    	                        }
    	                    }
    	                }
    	            }
    	        }
    	    }
	    }
	    return $parent_data;
	}
	
	protected function get_type_from_class_name($class_name) {
		
		if ($class_name) {
			$node_type = explode('_', $class_name);
			if (is_array($node_type) && isset($node_type[2]) && $node_type[2]) {
				return $node_type[2];
			}
		}
		return '';
	}
	
	public static function get_instance($object_id=0, $object_type='') {
	    if (!isset(static::$instances[$object_type])) {
	        static::$instances[$object_type] = array();
	    }
	    if (!isset(static::$instances[$object_type][$object_id])) {
	        static::$instances[$object_type][$object_id] = new frbr_build($object_id, $object_type);
	    }
	    return static::$instances[$object_type][$object_id];
	}
	
	protected function merge_datanode_data($data1, $data2, $operator) {
	    $data_merged = array();
	    foreach($data1 as $id => $results) {
	        if ($operator == "or") {
	            if (isset($data2[$id]) && is_array($data2[$id])) {
	                $data_merged[$id] = array_unique(array_merge($data1[$id], $data2[$id]));
	            } else {
	                $data_merged[$id] = $data1[$id];
	            }
	        } elseif ($operator == "and") {
	            if (isset($data2[$id]) && is_array($data2[$id])) {
	                $data_merged[$id] = array_intersect($data1[$id], $data2[$id]);
	            }
	        }
	    }
	    return $data_merged;
	}
}