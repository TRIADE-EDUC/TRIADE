<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_index.class.php,v 1.3 2019/09/20 09:42:04 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/indexation.class.php");
require_once($class_path."/onto/onto_handler.class.php");

/**
 * class onto_indexation
 * Cette classe permet de mettre à plat un index d'un élément d'une ontologie accessible dans notre schéma relationel
*/
class onto_index extends indexation {
	

    static protected $instances = array();
/**
	 * handler
	 *
	 * @var onto_handler
	 * @access public
	 */
	public $handler;
	
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
	protected $sparql_result;

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
		$ontology = $this->handler->get_ontology();
		$this->classes = $this->handler->get_classes();
		$unions =array();
		$this->properties = $ontology->get_properties();
		
		foreach($this->classes as $class){
			$unions[$class->uri] = array();
			$query = "select * {
				<".$class->uri."> <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?subclass .
				?subclass rdf:type pmb:indexation .
				?subclass owl:onProperty ?property .
				optional {		
					?subclass pmb:use ?use .
				}
				?subclass pmb:pound ?pound .
				?subclass pmb:field ?field .
				?subclass pmb:subfield ?subfield .
				optional {
					?subclass owl:unionOf ?union
				}
			}";
			$this->handler->onto_query($query);
			if($this->handler->onto_num_rows()){
				$results= $this->handler->onto_result();
				foreach($results as $result){
					if(isset($result->use) && $result->use){
						$element = array($result->property => $result->use);
					}else{
						$element = $result->property;
						
					}
					$this->infos[$class->uri][$result->pound][]= $element;
					
					$this->tab_code_champ[$result->field][$this->classes[$class->uri]->pmb_name."_".$this->properties[$result->property]->pmb_name] = array(
						'champ' => $result->field,
						'ss_champ' => $result->subfield,
						'pond' => $result->pound,
						'no_words' => false					
					);
				}
				if(isset($result->union) && $result->union && !in_array($result->union,$unions[$class->uri])){
					$unions[$class->uri][]=$result->union;
				}
			}
		}
		foreach($unions as $class_uri => $bnodes){
			foreach($bnodes as $bnode){
				$this->recurse_analyse_indexation($class_uri,$bnode);
			}
		}
	}
	
	protected function recurse_analyse_indexation($class,$bnode){
		$bnodes  =array();
		$query = "select * {
			<".$bnode."> rdf:type pmb:indexation  .
			<".$bnode."> owl:onProperty ?property .
			optional {		
				<".$bnode."> pmb:useProperty ?use .
			}
			<".$bnode."> pmb:pound ?pound .
			<".$bnode."> pmb:field ?field .
			<".$bnode."> pmb:subfield ?subfield .
			optional {
				<".$bnode."> owl:unionOf ?union
			}
		}";
		$this->handler->onto_query($query);
		if($this->handler->onto_num_rows()){
			$results= $this->handler->onto_result();
			foreach($results as $result){
				if(isset($result->use) && $result->use){
					$element = array($result->property => $result->use);
				}else{
					$element = $result->property;
				}
				$this->infos[$class][$result->pound][]= $element;
				$this->tab_code_champ[$result->field][$this->classes[$class]->pmb_name."_".$this->properties[$result->property]->pmb_name] = array(
					'champ' => $result->field,
					'ss_champ' => $result->subfield,
					'pond' => $result->pound,
					'no_words' => false
				);
			}
			if(isset($result->union) && $result->union && !in_array($result->union,$bnodes)){
				$bnodes[]=$result->union;
			}
		}
		foreach($bnodes as $bnode){
			$this->recurse_analyse_indexation($class,$bnode);
		}
	}
	
	public function get_sparql_result($object_uri){
		$assertions = array();
		$query = "select * {
			<".$object_uri."> rdf:type ?type
 		}";
		$this->sparql_result = array();
		
		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			$result = $this->handler->data_result();
			$type = $result[0]->type;
			if($type){
				if(isset($this->infos[$type]) && is_array($this->infos[$type])){
					foreach($this->infos[$type] as $pound => $elements){
						foreach($elements as $element){
							if(is_string($element)){
								if($element == $this->handler->get_display_label($this->classes[$type]->uri)){
									$assertions[] = "
									<".$object_uri."> <".$element."> ?".$this->classes[$type]->pmb_name."_".$this->properties[$element]->pmb_name;
								}else{
									$assertions[] = "
								optional {
									<".$object_uri."> <".$element."> ?".$this->classes[$type]->pmb_name."_".$this->properties[$element]->pmb_name."
								}";
								}
							}else if(is_array($element)){
								foreach($element as $property => $sub_property){
									$assertions[] = "
								optional {
									<".$object_uri."> <".$property."> ?".$this->properties[$property]->pmb_name." .
									?".$this->properties[$property]->pmb_name." <".$sub_property."> ?".$this->classes[$type]->pmb_name."_".$this->properties[$property]->pmb_name."
								}";
								}
							}
						}
					}
				}
			}
		}
		
		if(count($assertions)){
			$query = "select * {".implode(" . ",$assertions)."}";
			if($this->handler->data_query($query)){
				if($this->handler->data_num_rows()){
					$rows = $this->handler->data_result();
					//on parcours toutes les assertions utilies à l'indexation
					foreach($rows as $row){
						//on parcours la propriété infos pour retrouver les bons éléments
						foreach($this->infos[$type] as $pound => $properties_uris){
							$prefix = $this->classes[$type]->pmb_name."_";
							foreach($properties_uris as $property_uri){
								if(is_string($property_uri)){
									$property_name = $this->properties[$property_uri]->pmb_name;
									$var_name = $prefix.$property_name;
									if(isset($row->{$var_name})){
										if(!isset($this->sparql_result[$var_name][$row->{$var_name."_lang"}])){
											$this->sparql_result[$var_name][$row->{$var_name."_lang"}] = array();
										}
										if(!in_array($row->{$var_name},$this->sparql_result[$var_name][$row->{$var_name."_lang"}])){
											$this->sparql_result[$var_name][$row->{$var_name."_lang"}][] = $row->{$var_name};
										}
									}
								}else if (is_array($property_uri)){
									foreach($property_uri as $property => $sub_property){
										$property_name = $this->properties[$property]->pmb_name;
										$var_name = $prefix.$property_name;
										if(isset($row->{$var_name})){										
											if(!isset($this->sparql_result[$var_name][$row->{$property_name}])){
												$this->sparql_result[$var_name][$row->{$property_name}] = array();
		 									}
		 									if(!isset($this->sparql_result[$var_name][$row->{$property_name}][$row->{$var_name."_lang"}])){
		 										$this->sparql_result[$var_name][$row->{$property_name}][$row->{$var_name."_lang"}] = array();
		 									}
		 									if (!in_array($row->{$var_name},$this->sparql_result[$var_name][$row->{$property_name}][$row->{$var_name."_lang"}])){
												$this->sparql_result[$var_name][$row->{$property_name}][$row->{$var_name."_lang"}][] = $row->{$var_name};
		 									} 
										}							
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
		
		$this->delete_index($object_id,$datatype);
		//on a un tableau de résultat, on peut le travailler...
		foreach($this->tab_code_champ as $field_id => $element) {
			foreach ($element as $column => $infos){
				if(isset($this->sparql_result[$column])){
					$field_order = 1;
					foreach($this->sparql_result[$column] as $key => $values){
						foreach($values as $key2 => $value){
							if(is_string($value)){
								$language = $key;
								//fields (contenu brut)
								$tab_fields_insert[] = "('".$object_id."','".$infos['champ']."','".$infos['ss_champ']."','".$field_order."','".addslashes($value)."','".$language."','".$infos['pond']."','".$autority_num."')";
								
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
	
	/**
	 * Methode qui retourne l'instance de la classe d'indexation correspondant à l'ontologie
	 * @param string $onto_name
	 * @return onto_common_index
	 */
	public static function get_instance($onto_name = "common"){
	    $prefix="onto_";
	    $suffixe = "_index";
	    $instance_name = $prefix."common".$suffixe;
	    if($onto_name && class_exists($prefix.$onto_name.$suffixe)){
	        $instance_name = $prefix.$onto_name.$suffixe;
	    }
	    if (!isset(self::$instances[$instance_name])) {
	        self::$instances[$instance_name] = new $instance_name();
	    }
	    return self::$instances[$instance_name];
	}
}