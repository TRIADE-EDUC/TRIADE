<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_index.class.php,v 1.13.4.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexation.class.php");
require_once($class_path."/onto/onto_handler.class.php");
require_once($class_path."/sphinx/sphinx_concepts_indexer.class.php");

/**
 * class onto_indexation
 * Cette classe permet de mettre à plat un index d'un élément d'une ontologie accessible dans notre schéma relationel
*/
class onto_common_index extends indexation {
	/**
	 * handler
	 *
	 * @var onto_handler
	 * @access public
	 */
	public $handler;

	public $classes;

	/**
	 * properties
	 *
	 * @var Array()
	 * @access protected
	 */
	protected $properties;

	/**
	 * infos
	 *
	 * @var array
	 * @access public
	 */
	public $infos;

	/**
	 * sparql_result
	 *
	 * @var array
	 * @access protected
	 */
	protected static $sphinx_indexer;

	/**
	 * en nettoyage de base ou non
	 * @var bool
	 */
	protected $netbase = false;

	protected $sparql_result;

	protected $lang_codes = array(
		'fr' => 'fr_FR',
		'en' => 'en_UK',
		'nl' => 'nl_NL',
		'ar' => 'ar',
		'ca' => 'ca_ES',
		'es' => 'es_ES',
		'hu' => 'hu_HU',
		'it' => 'it_IT',
		'pt' => 'pt_PT',
		'ro' => 'ro_RO'
	);

	public function __construct(){

	}

	public function load_handler($ontology_filepath, $onto_store_type, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label){
		$this->handler = new onto_handler($ontology_filepath, $onto_store_type, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label);
	}

	public function set_handler($handler){
		$this->handler = $handler;
	}

	public function init(){
		$this->handler->get_ontology();
		$this->table_prefix = $this->handler->get_onto_name();
		$this->reference_key = "id_item";
		$this->analyse_indexation();
	}

	protected function analyse_indexation(){
	    if(empty($this->infos) || count($this->infos) == 0){
	        $cache = cache_factory::getCache();
	        $ontology = $this->handler->get_ontology();
	        $this->classes = $this->handler->get_classes();
	        $this->properties = $ontology->get_properties();
	        if(is_object($cache)){
	            $infos = $cache->getFromCache('onto_'.$ontology->name.'_index_infos');
	            if(is_array($infos) && count($infos)){
	                $tab_code_champ = $cache->getFromCache('onto_'.$ontology->name.'_index_tab_code_champ');
	                if(is_array($tab_code_champ) && count($tab_code_champ)){
	                    $this->infos = $infos;
	                    $this->tab_code_champ = $tab_code_champ;
	                    return;
	                }

	            }
	        }

    		if (is_array($this->classes)) {
        		foreach($this->classes as $class){
        			$query = "select * where {
        				<".$class->uri."> <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?subclass .
        				?subclass rdf:type pmb:indexation .
        			}";
        			$this->handler->onto_query($query);
        			if($this->handler->onto_num_rows()){
        				$results= $this->handler->onto_result();
        				foreach($results as $result){
        				    $this->recurse_analyse_indexation($class->uri,$result->subclass);
        				}
        			}
        		}
    		}
    		if(is_object($cache)){
    		    $cache->setInCache('onto_'.$ontology->name.'_index_tab_code_champ',$this->tab_code_champ);
    		    $cache->setInCache('onto_'.$ontology->name.'_index_infos',$this->infos);
    		}
	    }
	}

	protected function recurse_analyse_indexation($class_uri,$indexnode){
		$unions  =array();
		$query = "select * where {
			<".$indexnode."> rdf:type pmb:indexation .
			<".$indexnode."> owl:onProperty ?property .
			<".$indexnode."> pmb:pound ?pound .
			<".$indexnode."> pmb:field ?field .
			<".$indexnode."> pmb:subfield ?subfield .
            optional {
                <".$indexnode."> owl:unionOf ?union
            } .
            optional {
				<".$indexnode."> pmb:useProperty ?use .
			} .
            optional {
                <".$indexnode."> pmb:onRange ?on_range .
			} .
		}";
		$this->handler->onto_query($query);
		if($this->handler->onto_num_rows()){
		    $results= $this->handler->onto_result();
		    foreach($results as $result){
		        $element = [
		            'property' => $result->property,
		        ];
		        if(!empty($result->use)){
		            $element['use'] = $result->use;
		        }
		        if(!empty($result->on_range)){
		            $element['on_range'] = $result->on_range;
		        }
		        $this->infos[$class_uri][$result->pound][]= $element;
		        $name = $this->classes[$class_uri]->pmb_name."_".$this->properties[$result->property]->pmb_name;
		        if(!empty($result->on_range)){
		            $name.= '_'.$this->classes[$result->on_range]->pmb_name.'_'.$this->properties[$result->use]->pmb_name;
		        } else if(!empty($result->use)){
		            $name.= '_'.$this->properties[$result->use]->pmb_name;
		        }
		        $this->tab_code_champ[$result->field][$name] = array(
		            'champ' => $result->field,
		            'ss_champ' => $result->subfield,
		            'pond' => $result->pound,
		            'no_words' => false
		        );
		    }
		    if(isset($result->union) && $result->union && !in_array($result->union,$unions)){
		        $unions[]=$result->union;
		    }
		}
		foreach($unions as $union){
		    $this->recurse_analyse_indexation($class_uri,$union);
		}
	}

	public function get_sparql_result($object_uri) {

	    $assertions = array();
		$query = "SELECT * WHERE {
			<".$object_uri."> rdf:type ?type
 		}";
		$this->sparql_result = array();

		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			$result = $this->handler->data_result();
			$type = $result[0]->type;
			if($type){
				if(isset($this->infos[$type]) && is_array($this->infos[$type])){
					foreach($this->infos[$type] as $elements){
						foreach($elements as $element){
				            $name = $this->classes[$type]->pmb_name."_".$this->properties[$element['property']]->pmb_name;
				            $assertions[] = 'optional { '.PHP_EOL.'  <'.$object_uri.'> <'.$element['property'].'> ?'.$name . ' . '.PHP_EOL.'}';
						}
					}
				}
			}
		}
		// On peut avoir des doublons en cas de range multiples !
		$assertions=array_unique($assertions);
		if(count($assertions)){
		    // Une query ne peut pas être composer que d'optional
		    $query = "SELECT * WHERE {".PHP_EOL;
		    $query .= "<".$object_uri."> rdf:type ?type .".PHP_EOL;
		    $query .= implode(" . ".PHP_EOL,$assertions).PHP_EOL."}";

			if($this->handler->data_query($query)){
				if($this->handler->data_num_rows()){
					$rows = $this->handler->data_result();
					//on parcours toutes les assertions utilies à l'indexation
					foreach($rows as $row){
						//on parcours la propriété infos pour retrouver les bons éléments
						foreach($this->infos[$type] as $elements){
							$prefix = $this->classes[$type]->pmb_name."_";
							foreach($elements as $element){
							    $var_name = $prefix.$this->properties[$element['property']]->pmb_name;
							    if(isset($row->{$var_name})){
							        switch(true){
							            case !empty($element['on_range']) :
							                $query = "select * where {
												<".$row->{$var_name}."> <".$element['use']."> ?sub_property .
                                                <".$row->{$var_name}."> rdf:type <".$element['on_range']."> .
											}";
							                $this->handler->data_query($query);
							                if($this->handler->data_num_rows()){
							                    $result = $this->handler->data_result();
							                    $lang = '';
							                    $subrows = $this->handler->data_result();
							                    $subname = $var_name.'_'.$this->classes[$element['on_range']]->pmb_name.'_'.$this->properties[$element['use']]->pmb_name;
							                    foreach($subrows as $subrow){
							                        if (isset($subrow->sub_property_lang) && isset($this->lang_codes[$subrow->sub_property_lang])) {
							                            $lang = $this->lang_codes[$subrow->sub_property_lang];
    							                    }
    							                    if(!isset($this->sparql_result[$subname][$row->{$var_name}])){
    							                        $this->sparql_result[$subname][$row->{$var_name}] = array();
    							                    }
    							                    if(!isset($this->sparql_result[$subname][$row->{$var_name}][$lang])){
    							                        $this->sparql_result[$subname][$row->{$var_name}][$lang] = array();
    							                    }
    							                    if (!in_array($subrow->sub_property,$this->sparql_result[$subname][$row->{$var_name}][$lang])){
    							                        $this->sparql_result[$subname][$row->{$var_name}][$lang][] = $subrow->sub_property;
    							                    }
							                    }
							                }
							                break;
							            case !empty($element['use']) :
							                $query = "select * where {
												<".$row->{$var_name}."> <".$element['use']."> ?sub_property .
											}";
						                    $this->handler->data_query($query);
						                    if($this->handler->data_num_rows()){
						                        $lang = '';
						                        $subrows = $this->handler->data_result();
						                        $subname = $var_name.'_'.$this->properties[$element['use']]->pmb_name;
						                        foreach($subrows as $subrow){
						                            if (isset($subrow->sub_property_lang) && isset($this->lang_codes[$subrow->sub_property_lang])) {
						                                $lang = $this->lang_codes[$subrow->sub_property_lang];
						                            }
						                            if(!isset($this->sparql_result[$subname][$row->{$var_name}])){
						                                $this->sparql_result[$subname][$row->{$var_name}] = array();
						                            }
						                            if(!isset($this->sparql_result[$subname][$row->{$var_name}][$lang])){
						                                $this->sparql_result[$subname][$row->{$var_name}][$lang] = array();
						                            }
						                            if (!in_array($subrow->sub_property,$this->sparql_result[$subname][$row->{$var_name}][$lang])){
						                                $this->sparql_result[$subname][$row->{$var_name}][$lang][] = $subrow->sub_property;
						                            }
						                        }
						                    }
						                    break;
							            default :
											$lang = "";
							                if (isset($row->{$var_name."_lang"}) && isset($this->lang_codes[$row->{$var_name."_lang"}])) {
							                    $lang = $this->lang_codes[$row->{$var_name."_lang"}];
											}
											if(!isset($this->sparql_result[$var_name][$lang])){
												$this->sparql_result[$var_name][$lang] = array();
											}
											if(!in_array($row->{$var_name},$this->sparql_result[$var_name][$lang])){
												$this->sparql_result[$var_name][$lang][] = $row->{$var_name};
											}
							                break;
							        }
							    }
							}
						}
					}
				}
			}
		}
	}

	public function maj($object_id,$object_uri="",$datatype="all"){
		if($object_id == 0 && $object_uri != ""){
			$object_id = onto_common_uri::get_id($object_uri);
		}
		if($object_id != 0 && !$object_uri){
			$object_uri = onto_common_uri::get_uri($object_id);
		}

		if(!count($this->tab_code_champ)){
			$this->init();
		}

		$tab_words_insert = $tab_fields_insert = array();

		$this->get_sparql_result($object_uri);

		if(!$this->deleted_index) {
			$this->delete_index($object_id,$datatype);
		}
		//on a un tableau de résultat, on peut le travailler...
		foreach($this->tab_code_champ as $element) {
			foreach ($element as $column => $infos){
				if(isset($this->sparql_result[$column])){
					$field_order = 1;
					foreach($this->sparql_result[$column] as $key => $values){
						foreach($values as $key2 => $value){
							if(is_string($value)){
								$language = $key;
								//fields (contenu brut)
								$tab_fields_insert[] = "('".$object_id."','".$infos['champ']."','".$infos['ss_champ']."','".$field_order."','".addslashes($value)."','".$language."','".$infos['pond']."','')";

								//words (contenu éclaté)
								$tab_tmp=explode(' ',strip_empty_words($value));
								$word_position = 1;
								foreach($tab_tmp as $word){
									$num_word = indexation::add_word($word, $language);
									$tab_words_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$field_order,$word_position)";
									$word_position++;
								}
							}else {
								$language = $key2;
								$autority_num = onto_common_uri::get_id($key);

								foreach($value as $val){
									//fields (contenu brut)
									$tab_fields_insert[] = "('".$object_id."','".$infos['champ']."','".$infos['ss_champ']."','".$field_order."','".addslashes($val)."','".$language."','".$infos['pond']."','".$autority_num."')";

									//words (contenu éclaté)
									$tab_tmp=explode(' ',strip_empty_words($val));
									$word_position = 1;
									foreach($tab_tmp as $word){
										$num_word = indexation::add_word($word, $language);
										$tab_words_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$field_order,$word_position)";
										$word_position++;
									}
								}
							}
							$field_order++;
						}
					}
				}else{
					continue;
				}
			}
		}
		$this->save_elements($tab_words_insert,$tab_fields_insert);
		return true;
	}

	public function set_netbase($netbase) {
	    $this->netbase = $netbase;
	}
}