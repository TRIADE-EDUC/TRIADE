<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_autoposting.class.php,v 1.2 2018/06/29 12:50:41 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class onto_skos_autoposting {
    
    /**
     * permet de stocker les anciens chemins génériques avant la sauvegarde pour les comparer aux nouveaux
     * @var array
     */
    protected $old_broad_paths;
    
    /**
     * permet de stocker les anciens chemins spécifiques avant la sauvegarde pour les comparer aux nouveaux
     * @var array
     */
    protected $old_narrow_paths;
	
	/**
	 * @var onto_handler handler
	 */
	protected $handler;	
	
	/**
	 * 
	 * @var onto_index
	 */
	protected static $onto_index;
	
	/**
	 * uri du concept sur lequel on travaille
	 * @var string
	 */
	protected $uri;
	
	/**
	 * 
	 * @var array liste des preflabels des termes génériques
	 */
	protected $broaders_preflabels;
	
	/**
	 * 
	 * @var array liste des preflabels des termes spécifiques
	 */
	protected $narrowers_preflabels;
	
	public function __construct($handler, $uri = ''){
	    $this->handler=$handler;
	    $this->uri = $uri;
	}
	
	public function save_autoposting() {
	    $list_of_concepts = array();
	    
        $new_broad_paths = $this->get_paths();
        $this->get_old_broad_paths();
	    sort($new_broad_paths);
	    sort($this->old_broad_paths);
        $update_broad_paths = true;
        if ($new_broad_paths === $this->old_broad_paths) {
            $update_broad_paths = false;
        }
	    
	    if ($update_broad_paths) {
	        $list_of_concepts = array_merge($list_of_concepts, $this->get_ids_from_paths($new_broad_paths));
            $list_of_concepts = array_merge($list_of_concepts, $this->get_ids_from_paths($this->old_broad_paths));	        
	    }
	    
        $new_narrow_paths = $this->get_paths(true);
        $this->get_old_narrow_paths();
        sort($new_narrow_paths);
        sort($this->old_narrow_paths);
        $update_narrow_paths = true;
        if ($new_narrow_paths === $this->old_narrow_paths) {
            $update_narrow_paths = false;
        }
	    
	    if ($update_narrow_paths) {
	        $list_of_concepts = array_merge($list_of_concepts, $this->get_ids_from_paths($new_narrow_paths));
            $list_of_concepts = array_merge($list_of_concepts, $this->get_ids_from_paths($this->old_narrow_paths));	        
	    }
	    
	    $list_of_concepts = array_unique($list_of_concepts);
	    if (count($list_of_concepts)) {
	        $list_of_concepts[] = onto_common_uri::get_id($this->get_uri());
    	    foreach ($list_of_concepts as $concept_id) {
    	        $this->index_autoposted_concept($concept_id);
    	    }
	    }
	}
	
	/**
	 * indexation du concept autoposté
	 * @param int $concept_id
	 */
	public function index_autoposted_concept($concept_id) {	
        $this->save_paths(onto_common_uri::get_uri($concept_id));
	    //réindexation des notices indexés avec le concepts
        index_concept::update_linked_elements($concept_id);
	}
	
	/**
	 * 
	 * @param uri $uri
	 * @param boolean $narrow
	 * @return array
	 */
	public function get_paths($narrow = false) {
	    global $thesaurus_concepts_autopostage_specific_levels_nb;
	    global $thesaurus_concepts_autopostage_generic_levels_nb;
	    
	    if ($narrow) {
	       $nb_levels = $thesaurus_concepts_autopostage_specific_levels_nb;
	    } else {
	        $nb_levels = $thesaurus_concepts_autopostage_generic_levels_nb;
	       
	    }
	    if (!is_numeric($nb_levels)) {
	        $nb_levels = -1;
	    }
	    
	    if ($narrow) {
	        return $this->get_narrow_paths($this->get_uri(), array(), "", $nb_levels);
	    }
	    return $this->get_broad_paths($this->get_uri(), array(), "", $nb_levels);
	}
	
	/**
	 * renvoie les concepts génériques d'un noeud
	 * 
	 * @param string $class_uri
	 * @return array
	 */
	public function get_broaders_uri($uri){
	    if ($uri) {
    		$query .= "select ?broader where {
    			<".$uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
    		}";
    		$this->handler->data_query($query);
    		$results=$this->handler->data_result();
    		
    		if(is_array($results)){
    			$return=array();
    			foreach ($results as $key=>$result){
    				$return[] = $result->broader;
    			}
    			return $return;
    		}
	    }
		return array();
 	}
 	
	/**
	 * renvoie les concepts spécifiques d'un noeud
	 * 
	 * @param string $class_uri
	 * @return array
	 */
	public function get_narrowers_uri($uri){
	    if ($uri) {
    		$query .= "select ?narrower where {
    			<".$uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower .
    		}";
    		$this->handler->data_query($query);
    		$results=$this->handler->data_result();
    		
    		if(is_array($results)){
    			$return=array();
    			foreach ($results as $key=>$result){
    				$return[] = $result->narrower;
    			}
    			return $return;
    		}
	    }
		return array();
 	}
 	
	
	/**
	 * Retourne le chemin des concepts génériques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public function get_broad_paths($uri, $paths = array(), $path_beginning = '', $nb_levels = -1) {
		if ($uri) {
			if ($nb_levels != -1 && substr_count($path_beginning, '/') == $nb_levels) {
				return $paths;
			}
			$query = "select ?broader where {
				<".$uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader			
			}";
			
			$this->handler->data_query($query);
			$results = $this->handler->data_result();
			
			if(is_array($results) && count($results)){
				foreach ($results as $result) {
					$broader_id = onto_common_uri::get_id($result->broader);
					if (strpos($path_beginning, $broader_id.'/') === false) {
						$index = array_search($path_beginning, $paths);
						if ($index !== false) {
							$paths[$index] = $path_beginning.$broader_id.'/';
						} else {
							$paths[] = $path_beginning.$broader_id.'/';
						}
						$paths = $this->get_broad_paths($result->broader, $paths, $path_beginning.$broader_id.'/', $nb_levels);
					}					
				}
			} 
		}
		return $paths;
	}
	
	/**
	 * Retourne le chemin des concepts spécifiques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public function get_narrow_paths($uri, $paths = array(), $path_beginning = '', $nb_levels = -1) {
		if ($uri) {
			if ($nb_levels != -1 && substr_count($path_beginning, '/') == $nb_levels) {
				return $paths;
			}
			$query = "select ?narrower where {
				<".$uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower			
			}";
			
			$this->handler->data_query($query);
			$results = $this->handler->data_result();
			
			if(is_array($results) && count($results)){
				foreach ($results as $result) {
					$narrower_id = onto_common_uri::get_id($result->narrower);
					if (strpos($path_beginning, $narrower_id.'/') === false) {
						$index = array_search($path_beginning, $paths);
						if ($index !== false) {
							$paths[$index] = $path_beginning.$narrower_id.'/';
						} else {
							$paths[] = $path_beginning.$narrower_id.'/';
						}
						$paths = $this->get_narrow_paths($result->narrower, $paths, $path_beginning.$narrower_id.'/', $nb_levels);
					}					
				}
			} 
		}
		return $paths;
	}
	
	/**
	 * Enregistrement des chemins des concepts spécifiques et génériques pour l'autopostage
	 * @param string $uri
	 */
	public function save_paths($uri = "") {
	    if (empty($uri)) {
	        if ($this->get_uri() == "") {
	            return null;
	        }
	        $uri = $this->uri;	        
	    }
		$broad_paths = $this->save_broad_paths($uri);
		$narrow_paths = $this->save_narrow_paths($uri);

		$this->broaders_preflabels = $this->get_paths_preflabels($broad_paths);
		$this->narrowers_preflabels = $this->get_paths_preflabels($narrow_paths);
	}
	
	/**
	 * Enregistrement des chemins des concepts génériques
	 * @param string $uri
	 * @return array
	 */
	protected function save_broad_paths($uri) {
		global $thesaurus_concepts_autopostage_generic_levels_nb;
		
		$nb_levels = $thesaurus_concepts_autopostage_generic_levels_nb;
		if (!is_numeric($nb_levels)) {
			$nb_levels = -1;
		}		
		//on commence par supprimer les anciens chemins avant de les remettre à jour
		$query = "DELETE {<".$uri."> pmb:broadPath ?broadpath}";
		$this->handler->data_query($query);		
		
		$broad_paths = array();
		$broad_paths = $this->get_broad_paths($uri, array(), "", $nb_levels);
		if (count($broad_paths)) {
			foreach ($broad_paths as $broad_path) {
				$formated_path = $this->format_path($broad_path, $nb_levels);
				$query = "INSERT INTO <pmb> {<".$uri."> pmb:broadPath '".$formated_path."'}";
				$this->handler->data_query($query);
			}
		}
		return $broad_paths;
	}
	
	/**
	 * Enregistrement des chemins des concepts specifiques
	 * @param string $uri
	 * @return array
	 */
	protected function save_narrow_paths($uri) {
		global $thesaurus_concepts_autopostage_specific_levels_nb;
		
		$nb_levels = $thesaurus_concepts_autopostage_specific_levels_nb;
		if (!is_numeric($nb_levels)) {
			$nb_levels = -1;
		}		
		//on commence par supprimer les anciens chemins avant de les remettre à jour
		$query = "DELETE {<".$uri."> pmb:narrowPath ?narrowpath}";
		$this->handler->data_query($query);
		
		$narrow_paths = array();
		$narrow_paths = $this->get_narrow_paths($uri, array(), "", $nb_levels);
		if (count($narrow_paths)) {
			foreach ($narrow_paths as $narrow_path) {
				$formated_path = $this->format_path($narrow_path, $nb_levels);
				$query = "INSERT INTO <pmb> {<".$uri."> pmb:narrowPath '".$formated_path."'}";
				$this->handler->data_query($query);
			}
		}
		return $narrow_paths;
	}
		
	protected function get_paths_preflabels($paths) {
		if (is_array($paths) && count($paths)) {
			for ($i = 0 ; $i < count($paths) ; $i++) {
				$ids = explode('/', $paths[$i]);
				if (count($ids)) {
					for ( $j = 0 ; $j < count($ids) ; $j++) {
						$ids[$j] = $this->get_preflabel_from_id($ids[$j]);
					}
				}
				$paths[$i] = $ids;
			}
		}
		return $paths;
	}
	
	public function get_ids_from_paths($paths) {
	    $ids = array();
	    if (is_array($paths) && count($paths)) {
			foreach ($paths as $path) {
				$tab = explode('/', $path);
				if (count($tab)) {
					for ( $j = 0 ; $j < count($tab) ; $j++) {
					    if ($tab[$j] && !in_array($tab[$j], $ids)) {
					        $ids[] = $tab[$j];
					    }
					}
				}
			}	        
	    }
	    return $ids;
	}
	
	protected function get_preflabel_from_id($id) {
		if ($id) {
			$uri = onto_common_uri::get_uri($id);
			$query = "
				SELECT ?preflabel
				WHERE {
					<".$uri."> skos:prefLabel ?preflabel
				}";
			if ($this->handler->data_query($query)) {
				$results = $this->handler->data_result();
				$lang = '';
				if (!empty($results[0]->preflabel_lang)) {
					$lang = $results[0]->preflabel_lang;
				}
				return array(
						'id' => $id,
						'preflabel' => $results[0]->preflabel,
						'lang' => $lang
				);
			}
		}
		return array();
	}
	
	/**
	 * formate le chemin 
	 * @param array $path
	 * @param int $nb_levels
	 * @return string
	 */
	protected function format_path($path, $nb_levels = -1) {
		if ($nb_levels > -1) {
			$formated_path = '';
			$list_path = explode('/', $path);
			for ($i = 0; $i < $nb_levels; $i++) {
				if (!empty($list_path[$i])) {
					$formated_path .= $list_path[$i].'/';
				}
			}
			$path = $formated_path;
		}
		return $path;
	}
	
	/**
	 * retourne l'instance de l'onto_index
	 */
	protected function get_onto_index() {
	    if (empty(static::$onto_index)) {
	        static::$onto_index = onto_index::get_instance('skos');
	        static::$onto_index->set_handler($this->handler);
	        static::$onto_index->init();
	    }
	    return static::$onto_index;
	}
	
	/**
	 * 
	 * @return onto_skos_autoposting
	 */
	public function init_old_broad_paths() {
	    $this->old_broad_paths = $this->get_paths();
	    return $this;
	}
	
	/**
	 * 
	 * @return onto_skos_autoposting
	 */
	public function init_old_narrow_paths() {
	    $this->old_narrow_paths = $this->get_paths(true);
	    return $this;
	}
	
	/**
	 * 
	 * @param array $old_broad_paths
	 * @return onto_skos_autoposting
	 */
	public function set_old_broad_paths($old_broad_paths) {
	    $this->old_broad_paths = $old_broad_paths;
	    return $this;
	}
	
	/**
	 * 
	 * @param array $old_narrowers
	 * @return onto_skos_autoposting
	 */
	public function set_old_narrow_paths($old_narrow_paths) {
	    $this->old_narrow_paths = $old_narrow_paths;
	    return $this;
	}
	
	/**
	 * 
	 * @return array:
	 */
	public function get_old_broad_paths() {
	    if (!isset($this->old_broad_paths)) {
	        $this->old_broad_paths = array();
	    }
	    return $this->old_broad_paths;
	}
	
	/**
	 * 
	 * @return array:
	 */
	public function get_old_narrow_paths() {
	    if (!isset($this->old_narrow_paths)) {
	        $this->old_narrow_paths = array();
	    }
	    return $this->old_narrow_paths;
	}
	
	public function get_uri() {
	    if (!isset($this->uri)) {
	        $this->uri = '';
	    }
	    return $this->uri;
	}
	
	public function set_uri($uri) {
	    if ($uri) {
	        $this->uri = $uri;
	    }
	}
	
	public function get_broaders_preflabels() {
	    if (!isset($this->broaders_preflabels)) {
	        $this->broaders_preflabels = array();
	    }
	    return $this->broaders_preflabels;
	}
	
	public function get_narrowers_preflabels() {
	    if (!isset($this->narrowers_preflabels)) {
	        $this->narrowers_preflabels = array();
	    }
	    return $this->narrowers_preflabels;
	}
}