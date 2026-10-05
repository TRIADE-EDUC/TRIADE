<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontology.class.php,v 1.35 2024/03/22 14:54:51 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/onto/onto_store.class.php");
require_once($class_path."/onto/common/onto_common_class.class.php");
require_once($class_path."/onto/common/onto_common_property.class.php");
require_once($class_path."/onto/onto_class.class.php");
require_once($class_path."/onto/onto_property.class.php");


/**
 * class onto_ontology
 * 
 */
class onto_ontology {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Tableau des URI & infos annexes des classes
	 * @access protected
	 */
	protected $classes_uris = array();

	/**
	 * Tableau des instances des classes déjà  lues
	 * @access private
	 */
	private $classes;

	/**
	 * Tableau des URI & infos annexes (domaine, range) des propriétés de l'ontologie
	 * @access private
	 */
	private $properties_uri;
	
	/**
	 * Tableau des instances des propriétés déjà  lues
	 * @access private
	 */
	private $properties;

	/**
	 * nom de l'ontologie (pmb:name)
	 * @access public
	 */
	public $name = "";
	
	/**
	 *
	 * @access public
	 */
	public $title;

	public $subfield;

	/**
	 * Store de l'ontologie
	 * @var onto_store
	 * @access private
	 */
	private $store;
	
	/**
	 * Store de données
	 * @var onto_store
	 * @access private
	 */
	private $data_store;	
	/**
	 * Tableau des URI des propriétés inverse
	 * @access private
	 */
	private $inverse_of;
	
	/**
	 * Tableau des URI des sous classes
	 * @access private
	 */
	private $sub_class_of;
	
	/**
	 * URI de base de l'ontologie
	 * @access private
	 */
	private $uri;
	
	protected $class_properties;
	
	/**
	 * 
	 *
	 * @param onto_store store 

	 * @return void
	 * @access public
	 */
	public function __construct( $store ) {
		$this->store = $store;
		$this->get_name();
		$this->get_classes();
		$this->get_properties();
		$this->get_inverse_of_properties();
	} // end of member function __construct

	/**
	 * Récupère le nom informatique de l'ontologie
	 *
	 * @return void
	 * @access private
	 */	
	private function get_name(){
		$query = "select ?onto ?name ?title where {
			?onto rdf:type <http://www.w3.org/2002/07/owl#Ontology> .
			?onto pmb:name ?name . 
			optional {		
				?onto dct:title ?title
			}
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$result = $this->store->get_result();
				$this->uri = $result[0]->onto;
				$this->name = $result[0]->name;
				$this->title = $result[0]->title;
			}
		}else{
			highlight_string(print_r($this->store->get_errors(),true));
		}
	}
	
	/**
	 * Récupère la liste des URI des classes de l'ontologie (propriété classes_uri)
	 *
	 * @return void
	 * @access public
	 */
	public function get_classes( ) {
		$query  = "select * where {
			?class rdf:type <http://www.w3.org/2002/07/owl#Class> .
			?class rdfs:label ?label .
    			?class pmb:name ?pmb_name .
			?class pmb:name ?name .
			optional {
            	?class pmb:flag ?flag .
			}.
			optional {
                	?class pmb:field ?field .
    			}.
    			optional {
				?class rdfs:subClassOf ?sub_class_of .
				optional {
					?sub_class_of rdfs:subClassOf pmb:Class .
					?sub_class_of rdf:type owl:Class .
				}
			}
		}";
		if ($this->store->query($query)) {
			if ($this->store->num_rows()) {
				$result = $this->store->get_result();
				foreach ($result as $elem){
    				    if (isset($elem->flag) && $elem->flag === "internal"){
    				        // On utilise ce flag pour masquer de l'interface certains éléments
    				        continue;
    				    }
                    if (!isset($this->classes_uris[$elem->class])) {
						$class = new onto_class();
						$class->uri = $elem->class;
						$class->name = $elem->label;
    					$class->pmb_name = $elem->pmb_name;
    					$class->label = $this->get_label($elem);
						$this->classes_uris[$elem->class] = $class;
                    }
                    if (isset($elem->field)) {
                        $this->classes_uris[$elem->class]->field = $elem->field;
                    }
                    if (isset($elem->sub_class_of)) {
                    	$this->classes_uris[$elem->class]->add_sub_class_of($elem->sub_class_of);
                    }

                    if (isset($elem->flag) && $elem->flag){
                    	if (!isset($this->classes_uris[$elem->class]->flags)) {
                        	$this->classes_uris[$elem->class]->flags = array();
						}
						if (!in_array($elem->flag, $this->classes_uris[$elem->class]->flags)) {
                        	$this->classes_uris[$elem->class]->flags[] = $elem->flag;
						}
					}
				}
			}
		} else {
			highlight_string(print_r($this->store->get_errors(),true));
		}

		return $this->classes_uris;
	} // end of member function get_classes

	/**
	 * Retourne une instance de la classe correspondante à l'URI
	 *
	 * @param string uri_class 

	 * @return onto_common_class
	 * @access public
	 */
	public function get_class( $uri_class ) {
		if(!isset($this->classes[$uri_class])){
			$elements = array(
				'class_uri' => $uri_class
			);

			$class_name = $this->get_class_name("class", $elements);
			$this->classes[$uri_class] = new $class_name($uri_class, $this);

			if (isset($this->classes_uris[$uri_class]->pmb_name)) {
				$this->classes[$uri_class]->set_pmb_name($this->classes_uris[$uri_class]->pmb_name);
			}
			if (isset($this->classes_uris[$uri_class]->field)) {
				$this->classes[$uri_class]->set_field($this->classes_uris[$uri_class]->field);
			}
			if (isset($this->name)) {
				$this->classes[$uri_class]->set_onto_name($this->name);
			}
			if (isset($this->classes_uris[$uri_class]->field)) {
				$this->classes[$uri_class]->set_data_store($this->classes_uris[$uri_class]->field);
			}
		}
		return $this->classes[$uri_class];		
	} // end of member function get_class

	/**
	 * Récupère les cardinalités entre une classe et une propriété
	 *
	 * @param string uri_class 

	 * @param string uri_property 

	 * @return onto_restriction
	 * @access public
	 */
	public function get_restriction($uri_class,$uri_property) {
		$restriction = new onto_restriction();
		$query = "select ?max ?min where {
			<".$uri_class."> rdf:type <http://www.w3.org/2002/07/owl#Class> .
			<".$uri_class."> rdfs:subClassOf ?restrict .	
			?restrict rdf:type <http://www.w3.org/2002/07/owl#Restriction> .
			?restrict owl:onProperty <".$uri_property."> .		
			optional {
    			<".$uri_property."> pmb:distinctWith ?distinct
			} .
			optional {
				?restrict owl:maxCardinality ?max
			} .
			optional {
				?restrict owl:minCardinality ?min
			}
		}";
		if($this->store->query($query)){
			if($this->store->num_rows()){
				$results = $this->store->get_result();
				foreach ($results as $result){
				    if(!empty($result->min)){
						$restriction->set_min($result->min);
					}
					if(!empty($result->max)){
						$restriction->set_max($result->max);
					}
					if(!empty($result->distinct)){
    					$restriction->set_new_distinct($this->get_property($uri_class, $result->distinct));
					}
				}
			} else {
			    if (isset($this->classes_uris[$uri_class]->sub_class_of[0])) {
			        return $this->get_restriction($this->classes_uris[$uri_class]->sub_class_of[0],$uri_property);
			    }
			}
		}else{
			var_dump($this->store->get_errors());
		}
		return $restriction;
	} // end of member function get_card

	/**
	 * Retourne la liste des URI des propriétés avec les domain et range (propriété
	 * properties_uri)
	 *
	 * @return void
	 * @access public
	 */
	public function get_properties( ) {
		if (!$this->properties_uri) {
		    
		    $success  = $this->store->query("select * where {
				?property rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .
                optional {
                	?property rdfs:label ?label .
    			}.
				optional {
                	?property pmb:subfield ?subfield .
    			}.	
				optional {
					?property rdfs:domain ?domain
				} .
                optional {
                    ?property rdfs:range ?range
                } .
                optional {
                    ?property pmb:flag ?flag
                } .
				optional {
					?property pmb:defaultValueType ?default_value .
					?property pmb:defaultValue ?default_value_name .
				} .
				optional {
					?property pmb:list_item ?list_item .
					optional {
						?list_item rdfs:label ?list_item_value .
						?list_item pmb:identifier ?list_item_id .
						?list_item pmb:msg_code ?list_item_msg_code .
    					optional {
    						?list_item pmb:order ?list_item_order .
    	   				}
	   				}
				} .
                optional {
                    ?property pmb:extended ?extended .
                } .
                optional {
                    ?property pmb:marclist_type ?marclist_type
                } .
                optional {
                    ?property pmb:formOrder ?form_order
                }
			}
            ORDER BY ?form_order");
		    if($success && $this->store->num_rows()){
				$result = $this->store->get_result();
				foreach ($result as $elem){
					if(!isset($this->properties_uri[$elem->property])){
					    $this->properties_uri[$elem->property] = new onto_property();
					    $this->properties_uri[$elem->property]->uri = $elem->property;
					    $this->properties_uri[$elem->property]->label = $elem->label;
					    $this->properties_uri[$elem->property]->get_properties($this->store);
					}
					
                    if(isset($elem->subfield)) {
                        $this->properties_uri[$elem->property]->subfield = $elem->subfield;
                    }
					if (!empty($elem->domain)) {
					    if(!isset($this->properties_uri[$elem->property]->domain) || !is_array($this->properties_uri[$elem->property]->domain)) {
					        $this->properties_uri[$elem->property]->domain = array();
					    }
					    if(!isset($this->properties_uri[$elem->property]->domain[$elem->domain])) {
					        $this->properties_uri[$elem->property]->domain[$elem->domain] = $elem->domain;
					    }
					}
					
					if(isset($elem->list_item) && $elem->list_item){
					    if (!isset($this->properties_uri[$elem->property]->pmb_list_item)  || !is_array($this->properties_uri[$elem->property]->pmb_list_item)) {
							$this->properties_uri[$elem->property]->pmb_list_item = array();
						}
						if (!isset($this->properties_uri[$elem->property]->pmb_list_item[$elem->list_item_id])) {
							$this->properties_uri[$elem->property]->pmb_list_item[$elem->list_item_id] = array(
							        'value' => $this->get_label_from_msg($elem->list_item_msg_code, $elem->list_item_value),
									'id' => $elem->list_item_id,
						            'order' => $elem->list_item_order ?? 0
							);
						}
					}
					
					if(!empty($elem->range)){
					    if(!isset($this->properties_uri[$elem->property]->range) || !is_array($this->properties_uri[$elem->property]->range)) {
							$this->properties_uri[$elem->property]->range = array();
						}
						//il faut gérer le cas du noeud blanc
						if($elem->range_type == "bnode"){
							$this->properties_uri[$elem->property]->range = array_merge($this->properties_uri[$elem->property]->range,$this->get_recursive_blank_range($elem->range));
						}else{
							if(!in_array($elem->range,$this->properties_uri[$elem->property]->range)){
								$this->properties_uri[$elem->property]->range[] = $elem->range;
							}
						}
					}
					if(!empty($elem->default_value) && empty($this->properties_uri[$elem->property]->default_value)){
						$this->properties_uri[$elem->property]->default_value = array(
							'value' => $elem->default_value_name,
							'type' => $elem->default_value
						);
					}
					if(!empty($elem->flag)){
					    if(!isset($this->properties_uri[$elem->property]->flags)) {
							$this->properties_uri[$elem->property]->flags = array();
						}
						if (!isset($this->properties_uri[$elem->property]->flags[$elem->flag])) {
							$this->properties_uri[$elem->property]->flags[$elem->flag] = $elem->flag;
						}
					}
					if (!empty($elem->extended)){
					    $this->properties_uri[$elem->property]->pmb_extended = $this->get_pmb_extended_from_property($elem->extended);
					}
				}
			} else {
			    $errors = $this->store->get_errors();
			    if (!empty($errors)) {
			        highlight_string(__FILE__.'  ('.__LINE__.')');
			        highlight_string(print_r($errors, true));
			    }
			}
		}
		
		//on vérifie, si aucun domaine précisé, on peut mettre la propriété partout
		if (!empty($this->properties_uri) && is_array($this->properties_uri)) {
		    foreach($this->properties_uri as $property_uri => $property){
		        if(!is_array($property->domain) || !count($property->domain)){
		            foreach($this->classes_uris as $class_uri => $class){
	                	if(!isset($this->properties_uri[$property_uri]->domain) || !is_array($this->properties_uri[$property_uri]->domain)) {
	                		$this->properties_uri[$property_uri]->domain = array();
	                	}
		                $this->properties_uri[$property_uri]->domain[] = $class_uri;
		            }
		        }
		    }
		}
		
		return $this->properties_uri;
	} // end of member function get_properties

	
	protected function get_recursive_blank_range($brange){
		$range = array();
		$query = "select * where {
			<".$brange."> ?prop ?value .
		}";
		$this->store->query($query);
		$range_results = $this->store->get_result();
		foreach($range_results as $range_result){
			switch($range_result->prop){
				case "http://www.w3.org/2002/07/owl#unionOf" :
					$range = array_merge($range,$this->get_recursive_blank_range($range_result->value));
					break;
				case "http://www.w3.org/1999/02/22-rdf-syntax-ns#first":
					$range[] = $range_result->value;
					break;
				case "http://www.w3.org/1999/02/22-rdf-syntax-ns#rest" :
					if($range_result->value_type == "bnode"){
						$range = array_merge($range,$this->get_recursive_blank_range($range_result->value));
					}else if ($range_result->value != "http://www.w3.org/1999/02/22-rdf-syntax-ns#nil"){
						$range[] = $range_result->value;  
					}
					break;
			}
		}
		return $range;
	}
	
	public function get_class_properties($class_uri) {
		if (isset($this->class_properties[$class_uri])) {
			return $this->class_properties[$class_uri];
		}
		$this->class_properties[$class_uri] = array();
		if (is_array($this->properties_uri)) {
    		foreach ($this->properties_uri as $property_uri => $property) {
    		    // C'est une property associée au domain et sans le flag internal
    			if (
					in_array($class_uri ?? "", $property->domain ?? []) &&
					!in_array("internal",$property->flags ?? [])
				) {
   			        $this->class_properties[$class_uri][] = $property_uri;
    			}
    		}
		}
		if (!empty($this->classes_uris[$class_uri]) && is_array($this->classes_uris[$class_uri]->sub_class_of)) {
		    foreach ($this->classes_uris[$class_uri]->sub_class_of as $parent_uri) {
		        $properties = $this->get_class_properties($parent_uri);
		        if (!empty($properties) && is_array($properties)) {
		            for ($i=0 ; $i< count($properties); $i++) {
		                if (!is_array($properties[$i])) {
		                    $property = $this->get_property($parent_uri, $properties[$i]);
		                    if (!$property->is_undisplayed()) {
		                        $this->class_properties[$class_uri][] = $properties[$i];
		                    }
		                }
		            }
		        }
		    }
		}
		return $this->class_properties[$class_uri];
	}
	
	
    /**
     *
     *
     * @param string uri_property

     * @return onto_common_property
     * @access public
     */
    public function get_property($uri_class, $uri_property) {

        if (!isset($this->properties[$uri_property])) {
            $this->properties[$uri_property] = [];
        }

		if (!isset($this->properties[$uri_property][$uri_class])) {
            $elements = [
                    'class_uri' => $uri_class,
                    'property_uri' => $uri_property,
            ];
            $class_name = $this->get_class_name("property", $elements);
            $this->properties[$uri_property][$uri_class] = new $class_name($uri_property, $this);

            if (isset($this->properties_uri[$uri_property]->domain)) {
                $this->properties[$uri_property][$uri_class]->set_domain($this->properties_uri[$uri_property]->domain);
            }
            if (isset($this->properties_uri[$uri_property]->range)) {
                $this->properties[$uri_property][$uri_class]->set_range($this->properties_uri[$uri_property]->range);
            }
            if (isset($this->properties_uri[$uri_property]->pmb_name)) {
                $this->properties[$uri_property][$uri_class]->set_pmb_name($this->properties_uri[$uri_property]->pmb_name);
            }
            if (isset($this->properties_uri[$uri_property]->subfield)) {
                $this->properties[$uri_property][$uri_class]->set_subfield($this->properties_uri[$uri_property]->subfield);
            }
            if (isset($this->properties_uri[$uri_property]->label)) {
                $this->properties[$uri_property][$uri_class]->set_label($this->properties_uri[$uri_property]->label);
            }
            if (isset($this->properties_uri[$uri_property]->pmb_marclist_type)) {
                $this->properties[$uri_property][$uri_class]->set_pmb_marclist_type($this->properties_uri[$uri_property]->pmb_marclist_type);
            }
            if (isset($this->properties_uri[$uri_property]->pmb_list_item)) {
                $this->properties[$uri_property][$uri_class]->set_pmb_list_item($this->properties_uri[$uri_property]->pmb_list_item);
            }
            if (isset($this->properties_uri[$uri_property]->pmb_list_query)) {
                $this->properties[$uri_property][$uri_class]->set_pmb_list_query($this->properties_uri[$uri_property]->pmb_list_query);
            }
            if (isset($this->properties_uri[$uri_property]->pmb_extended)) {
                $this->properties[$uri_property][$uri_class]->set_pmb_extended($this->properties_uri[$uri_property]->pmb_extended);
            }
            if (isset($this->inverse_of[$uri_property])) {
                $this->properties[$uri_property][$uri_class]->set_inverse_of($this->get_property($uri_class, $this->inverse_of[$uri_property]));
            }
            if (isset($this->properties_uri[$uri_property]->undisplayed)) {
                $this->properties[$uri_property][$uri_class]->set_undisplayed($this->properties_uri[$uri_property]->undisplayed);
            }
            if (isset($this->properties_uri[$uri_property]->form_uri)) {
                $this->properties[$uri_property][$uri_class]->set_form_uri($this->properties_uri[$uri_property]->form_uri);
            }
            if (isset($this->properties_uri[$uri_property]->form_id)) {
                $this->properties[$uri_property][$uri_class]->set_form_id($this->properties_uri[$uri_property]->form_id);
            }
            if (isset($this->properties_uri[$uri_property]->no_search)) {
                $this->properties[$uri_property][$uri_class]->set_no_search($this->properties_uri[$uri_property]->no_search);
            }
            if (isset($this->properties_uri[$uri_property]->cp_options)) {
                $this->properties[$uri_property][$uri_class]->set_cp_options($this->properties_uri[$uri_property]->cp_options);
            }
            $this->properties[$uri_property][$uri_class]->set_onto_name($this->name);
            $this->properties[$uri_property][$uri_class]->set_data_store($this->data_store);
        }
        return $this->properties[$uri_property][$uri_class];
    } // end of member function get_property

	/**
	 * 
	 *
	 * @param string class_prefix 

	 * @param Array() elements Tableau des noms pour déterminer le nom de la classe. Associatif ?

	 * @return string
	 * @access public
	 */
	public function get_class_name( $class_prefix,  $elements ) {
		return $this->search_class_name($class_prefix, $this->name, $elements);
	} // end of member function get_class_name

	protected function search_class_name($class_prefix,$name,$elements){
		switch ($class_prefix){
			case "class" :
				/*
				 * $elements['class_uri'] => URI de la classe
				 */
			    //ex : onto_skos_class_concept
			    $class_name = (isset($this->classes_uris[$elements['class_uri']]) ? "onto_".$name."_".$class_prefix."_".$this->classes_uris[$elements['class_uri']]->pmb_name : '');	
				if(!class_exists($class_name)){
					//ex : onto_skos_class
					$class_name = "onto_".$name."_".$class_prefix;
					if(!class_exists($class_name)){
						$class_name = $this->search_class_name($class_prefix,"common",$elements);
					}
				}
				break;
			case "property" :
				/*
				 * $elements['class_uri'] => URI de la classe
				 * $elements['property_uri'] => URI de la classe
				 */
				//ex : onto_skos_class_concept_property_inscheme
				$class_pmb_name = (isset($this->classes_uris[$elements['class_uri']]) ? $this->classes_uris[$elements['class_uri']]->pmb_name : '');				
				$property_pmb_name = (isset($this->properties_uri[$elements['property_uri']]) ? $this->properties_uri[$elements['property_uri']]->pmb_name : '');
								
				$class_name = "onto_".$name."_class_".$class_pmb_name."_".$class_prefix."_".$property_pmb_name;
				if(!class_exists($class_name)){
					//ex : onto_skos_property_inscheme
					$class_name = "onto_".$name."_".$class_prefix."_".$property_pmb_name;
					if(!class_exists($class_name)){
						//ex : onto_skos_property
						$class_name = "onto_".$name."_".$class_prefix;
						if(!class_exists($class_name)){
							$class_name = $this->search_class_name($class_prefix, "common", $elements);
						}
					}
				}
				break;
		}
		return $class_name;
	}
	
	public function get_label($object){
		global $msg;
		if(!$this->name){
			$this->get_name();
		}

		if(isset($msg['onto_'.$this->name.'_'.$object->pmb_name])){
			//le message PMB spécifique pour l'ontologie courante
			$label = $msg['onto_'.$this->name.'_'.$object->pmb_name];
		}else if (isset($msg['onto_common_'.$object->pmb_name])){
			//le message PMB générique
			$label = $msg['onto_common_'.$object->pmb_name];
		}else if (isset($msg[$object->pmb_name])){
			$label = $msg[$object->pmb_name];
		}else {
			$label = $object->name;
    		if (substr($label,0,4) == "msg:") {
    		    if (isset($msg[substr($label,4)])) {
    		        $label = $msg[substr($label,4)];
    		    } else {
    		        // si on trouve pas le message on met juste le code dans le label
    		        $label = substr($label,4);
    		    }
    		}
		}
		return $label;
	}
	
	public function get_label_from_msg($code, $label = ""){
	    global $msg;
	    if(!$this->name){
	        $this->get_name();
	    }
	    if(isset($msg['onto_'.$this->name.'_'.$code])){
	        return $msg['onto_'.$this->name.'_'.$code];
	    }
	    if (isset($msg['onto_common_'.$code])){
	        return $msg['onto_common_'.$code];
	    }
	    if (isset($msg[$code])){
	        return $msg[$code];
	    }
	    return $label;
	}

	public function get_class_label($uri_class){
	    if (isset($this->classes_uris[$uri_class])) {
    		return $this->get_label($this->classes_uris[$uri_class]);
	    }
        return "";
	}
	
	public function get_property_label($uri_property){
	    if (isset($this->classes_uris[$uri_property])) {
	        return $this->get_label($this->properties_uri[$uri_property]);
	    }
	    return "";
	}
	
	public function get_property_pmb_datatype($uri_property){
		if (empty($this->properties_uri[$uri_property]) || !isset($this->properties_uri[$uri_property]->pmb_datatype)) {
			return "";
		}
		return $this->properties_uri[$uri_property]->pmb_datatype;
	}
	
	public function get_property_default_value($uri_property){
		return $this->properties_uri[$uri_property]->default_value;
	}
	
	public function get_classes_uri(){
		return $this->classes_uris;
	}
	
	public function get_inverse_of_properties(){
		if(!is_array($this->inverse_of) || (!count($this->inverse_of))){
			$query = "select * {
				?property owl:inverseOf ?inverse
			}";
			$this->store->query($query);
			$inverse_results = $this->store->get_result();
			foreach($inverse_results as $inverse_of){
				$this->inverse_of[$inverse_of->property] = $inverse_of->inverse;
			}
		}
		return $this->inverse_of;
	}
	
	public function get_flags($uri_class="",$uri_property=""){
		$flags = array();
		if($uri_class && isset($this->classes_uri[$uri_class]->flags)){
			$flags = $this->classes_uri[$uri_class]->flags;
		}else if (isset($this->properties_uri[$uri_property]->flags)){
			$flags = $this->properties_uri[$uri_property]->flags;
		}
		return $flags;
	}
	
	public function get_multilingue($uri_class="", $uri_property="") {
	    $multilingue = false;
	    
	    if($uri_class && isset($this->classes_uri[$uri_class]->multilingue)){
	        $multilingue = true;
	    } else if (isset($this->properties_uri[$uri_property]->multilingue)){
	        $multilingue = true;
	    }
	    
	    return $multilingue;
	}
	
	public function is_use_lang_concept($uri_class="", $uri_property="") {
	    $langConcept = false;
	    if($uri_class && isset($this->classes_uri[$uri_class]->useLangConcept)){
	        $langConcept = true;
	    } else if (isset($this->properties_uri[$uri_property]->useLangConcept)){
	        $langConcept = true;
	    }
	    return $langConcept;
	}
	
	public function is_cp($uri_class="", $uri_property="") {
	    $is_cp = false;
	    
	    if($uri_class && isset($this->classes_uri[$uri_class]->is_cp)){
	        $is_cp = true;
	    } else if (isset($this->properties_uri[$uri_property]->is_cp)){
	        $is_cp = true;
	    }
	    
	    return $is_cp;
	}
	
	public function set_data_store($data_store){
		$this->data_store = $data_store;
	}
	
	public function get_sub_class_of($uri){
		$sub_class_of = array();
		$query = "
			select ?sub_class_of where {
				<".$uri."> rdfs:subClassOf ?sub_class_of .
				?sub_class_of rdfs:subClassOf pmb:Class .
				?sub_class_of rdf:type owl:Class .
			}
		";
		$this->store->query($query);
		if ($this->store->num_rows()) {
			$results = $this->store->get_result();
			foreach($results as $result){
				$sub_class_of[] = $result->sub_class_of;
			}
		}
		return $sub_class_of;
	}
	
	public function get_sub_class_from_uri($uri) {
	    if (isset($this->sub_class_of[$uri])) {
	        return $this->sub_class_of[$uri];
	    }
	    $this->sub_class_of[$uri] = array();
	    $query = "
			select ?sub_class where {
				?sub_class rdfs:subClassOf <".$uri."> .
				?sub_class rdf:type owl:Class .
			}
		";
	    $this->store->query($query);
	    if ($this->store->num_rows()) {
	        $results = $this->store->get_result();
	        foreach($results as $result){
	            $this->sub_class_of[$uri][] = $result->sub_class;
	        }
	    }
	    return $this->sub_class_of[$uri];
	}
	
	protected function get_sub_assertions($blanknode){
	    $sub_assertions = array();
	    $query = "select * where {
			<".$blanknode."> pmb:assertions ?assertions .
            ?assertions ?prop ?obj .
		}";
	    
	    $this->store->query($query);
	    $results = $this->store->get_result();
	    
	    foreach($results as $result){
	        if ($result->obj_type == 'bnode'){
	            $sub_assertions["assertions"] = $this->get_sub_assertions($result->obj);
	        } else {
	            $sub_assertions[$result->prop] = $result->obj;
	        }
	    }
	    return $sub_assertions;
	    
	}
	
	protected function get_pmb_extended_from_property($uri) : array{
	    $return = [];
	    $query = "select * where {
			'$uri' ?prop ?obj .
        }";
	    
	    $this->store->query($query);
	    if ($this->store->num_rows()) {
	        $results = $this->store->get_result();
	        foreach($results as $result){
	            $prop_name = explode('#', $result->prop);
	            if ($result->obj_type == "bnode"){
	                $return[$prop_name[1]] = $this->get_pmb_extended_from_property($result->obj);
	            } else {
	                $return[$prop_name[1]] = $result->obj;
	            }
	        }
	    }
	    return $return;
	}
} // end of onto_ontology