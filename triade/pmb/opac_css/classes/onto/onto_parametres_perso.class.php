<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_parametres_perso.class.php,v 1.21 2024/01/19 07:42:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/encoding_normalize.class.php");

class onto_parametres_perso extends parametres_perso {
	
	/**
	 * déclaration des uri liées aux préfixes
	 * 
	 * @var array
	 */
	public static $entities_uri = array(
		'notices' => 'http://www.pmbservices.fr/ontology#record',
		'author' => 'http://www.pmbservices.fr/ontology#author',
		'categ' => 'http://www.pmbservices.fr/ontology#category',
		'publisher' => 'http://www.pmbservices.fr/ontology#publisher',		
		'collection' => 'http://www.pmbservices.fr/ontology#collection',		
		'subcollection' => 'http://www.pmbservices.fr/ontology#sub_collection',			
		'serie' => 'http://www.pmbservices.fr/ontology#serie',		
		'tu' => 'http://www.pmbservices.fr/ontology#work',		
		'indexint' => 'http://www.pmbservices.fr/ontology#indexint',
		'skos' => 'http://www.w3.org/2004/02/skos/core#Concept',
		'explnum' => 'http://www.pmbservices.fr/ontology#docnum',
		'expl' => 'http://www.pmbservices.fr/ontology#expl',
	);
	
	/**
	 * Nom du fichier où est enregistrée l'ontologie des champs persos
	 * @var string
	 */
	protected static $filename = "./temp/ontologies_pmb_entities_ppersos.rdf";
	
	/**
	 * URI du rdf:description
	 * 
	 * @var string
	 */
	protected $uri_description;
	
	/**
	 * URI du rdfs:range
	 * 
	 * @var string
	 */
	protected $uri_range;
	
	/**
	 * URI du pmb:datatype
	 * 
	 * @var string
	 */
	protected $uri_datatype;
	
	/**
	 * Portion de propriétés optionnelle en fonction du type de champ perso
	 * 
	 * @var string
	 */
	protected $optional_properties;
	
	/**
	 * Noeuds blancs nécessaires
	 * @var string
	 */
	protected $blank_nodes;
	
	/**
	 * @var string
	 */
	protected $rdf_nodeId;
	
	/**
	 * Triplets qui définissent les sous-classes de l'entité parente
	 * @var string
	 */
	protected $parent_subclasses;
	
	public function init_attributes () {
		$this->uri_description = "";
		$this->uri_range = 'http://www.w3.org/2000/01/rdf-schema#Literal';
		$this->optional_properties = "";
		$this->blank_nodes = "";
	}	
	
	public function build_onto () {
		$onto = "
	<!-- Champs perso ".$this->prefix." PMB -->";
		
		foreach ($this->t_fields as $id => $t_field) {
			$this->init_attributes();
			$this->set_uri_description($t_field["NAME"]);
			$this->set_datatype_from_field($id,$t_field);
			$this->set_restrictions($t_field);
			
			$onto.= "
	            <rdf:Description rdf:about='http://www.pmbservices.fr/ontology#" . $this->uri_description. "'>
        		<rdfs:label>" . htmlspecialchars(encoding_normalize::utf8_normalize($t_field["TITRE"]), ENT_QUOTES, 'utf-8') . "</rdfs:label>
        		<rdfs:comment>" . htmlspecialchars(encoding_normalize::utf8_normalize($t_field["COMMENT"]), ENT_QUOTES, 'utf-8') . "</rdfs:comment>
        		<rdfs:isDefinedBy rdf:resource='http://www.pmbservices.fr/ontology#'/>
               	<rdf:type rdf:resource='http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'/>
        		<rdfs:domain rdf:resource='" . self::$entities_uri[$this->prefix] . "'/>
        		<rdfs:range rdf:resource='$this->uri_range'/>
        		<pmb:datatype rdf:resource='$this->uri_datatype'/>";
            $onto.= $this->optional_properties;
		
		    $onto.= "
                <pmb:is_cp>1</pmb:is_cp>
        		<pmb:cp_options>".htmlspecialchars(encoding_normalize::json_encode($t_field["OPTIONS"][0]))."</pmb:cp_options>
        		<pmb:name>$this->uri_description</pmb:name>";
		    
		    $type = $t_field["TYPE"] ?? ($t_field["type"] ?? "");
		    if (strpos($type, 'i18n') !== false) {
		        $onto .= "<pmb:multilingue>1</pmb:multilingue>";
		    }
        			
            if (isset($t_field["OPTIONS"][0]["DATA_TYPE"][0]["value"])){
                $onto .= "<pmb:flag>".$this->get_authority_type_from_query_auth($t_field["OPTIONS"][0]["DATA_TYPE"][0]["value"])."</pmb:flag>";
            }
            $onto .= "</rdf:Description>";
    		// On n'oublie pas les noeuds blancs
    		$onto.= $this->blank_nodes;
		}
		
		// On ajoute les sous-classes au parent
		if ($this->parent_subclasses) {
			$onto.= "
	<rdf:Description rdf:about='" . self::$entities_uri[$this->prefix] . "'>
		".$this->parent_subclasses."
    </rdf:Description>
";
		}
		
		return $onto;
	}
		
	public function set_datatype_from_field ($id ,$t_field)	{
	    $type = $t_field["TYPE"] ?? ($t_field["type"] ?? "");
	    switch ($type) {
			case "list" :
			case "query_list" :
				$this->get_items_from_options($id, $t_field["OPTIONS"][0]);
				$this->uri_datatype = 'http://www.pmbservices.fr/ontology#list';
				break;
				
			case "query_auth" :										
				$this->set_uri_range($this->get_authority_from_query_auth($t_field["OPTIONS"][0]["DATA_TYPE"][0]["value"]));				
				$this->uri_datatype = 'http://www.pmbservices.fr/ontology#resource_selector';	
				break;
							
			case "date_box" :
				$this->uri_datatype = 'http://www.pmbservices.fr/ontology#date';
				break;
				
			case "url" :
				$this->uri_datatype = 'http://www.pmbservices.fr/ontology#url';
				break;
				
			case "resolve" :
				$this->uri_datatype = 'http://www.pmbservices.fr/ontology#resolve';
				break;
				
			case "marclist" :
			    $this->uri_datatype = 'http://www.pmbservices.fr/ontology#marclist';
			    $this->optional_properties.= "
					<pmb:marclist_type>".$t_field["OPTIONS"][0]["DATA_TYPE"][0]["value"]."</pmb:marclist_type>";
				break;
			case "date_flot":
			    $this->uri_datatype = 'http://www.pmbservices.fr/ontology#floating_date';
			    break;
			case "q_txt_i18n" :
			    // Texte multilingue qualifié
			    $this->uri_datatype = 'http://www.pmbservices.fr/ontology#multilingual_qualified';
			    break;
			    
			case "text" :
			case "text_i18n" : // Texte multilingue
			case "comment" :
			case "html" :
			    $datatype = $t_field["DATATYPE"] ?? ($t_field["datatype"] ?? "");
			    if (!empty($datatype) && $datatype == "text") { // Texte large
			        $this->uri_datatype = 'http://www.pmbservices.fr/ontology#text';
			        break;
			    }
			case "external":
			default:
			    $this->uri_datatype = 'http://www.pmbservices.fr/ontology#small_text';
			    break;
		}
	}
	
	public function get_authority_from_query_auth ($choice) {
		switch ($choice){
			case 1:
				return 'http://www.pmbservices.fr/ontology#author';
			case 2:
				return 'http://www.pmbservices.fr/ontology#category';
			case 3:
				return 'http://www.pmbservices.fr/ontology#publisher';
			case 4:
				return 'http://www.pmbservices.fr/ontology#collection';
			case 5:
				return 'http://www.pmbservices.fr/ontology#sub_collection';
			case 6:
				return 'http://www.pmbservices.fr/ontology#serie';
			case 7:
				return 'http://www.pmbservices.fr/ontology#indexint';
			case 8:
				return 'http://www.pmbservices.fr/ontology#work';
			case 9:
			default:
			    if($choice >=1000){
			        return 'http://www.pmbservices.fr/ontology#authperso_'.intval($choice-1000);
			    }
				return "http://www.w3.org/2004/02/skos/core#Concept";
		}
	}
	
	public function get_uri_range() {
		return $this->uri_range;
	}
	
	public function set_uri_range ($uri_range) {
		$this->uri_range = $uri_range;
	}
	
	protected function get_items_from_options($id,$options) {
		$query = '';
		$list_items = array();
		if(!empty($options['FOR'])) {
    		switch ($options['FOR']) {
    			case 'list':
    				$query = "SELECT ". $this->prefix . "_custom_list_value as id, ". $this->prefix . "_custom_list_lib as libelle, ordre FROM " . $this->prefix ."_custom_lists WHERE " . $this->prefix . "_custom_champ = " . $id . " ORDER BY ordre";
    				$result = pmb_mysql_query($query);
    				if (pmb_mysql_num_rows($result)) {
    					while ($row = pmb_mysql_fetch_object($result)) {
    						$this->optional_properties.= "
            					<pmb:list_item rdf:nodeID='list_item_".$this->uri_description."_".htmlspecialchars(encoding_normalize::utf8_normalize($row->id), ENT_QUOTES, 'utf-8')."'/>";
    						$this->blank_nodes.= "
    							<rdf:Description rdf:nodeID='list_item_".$this->uri_description."_".htmlspecialchars(encoding_normalize::utf8_normalize($row->id), ENT_QUOTES, 'utf-8')."'>
    								<rdfs:label xml:lang='fr'>".htmlspecialchars(encoding_normalize::utf8_normalize($row->libelle), ENT_QUOTES, 'utf-8')."</rdfs:label>
    								<pmb:identifier>".htmlspecialchars(encoding_normalize::utf8_normalize($row->id), ENT_QUOTES, 'utf-8')."</pmb:identifier>
                                    <pmb:msg_code></pmb:msg_code>
                                    <pmb:order>".htmlspecialchars(encoding_normalize::utf8_normalize(isset($row->ordre) ? $row->ordre : 0), ENT_QUOTES, 'utf-8')."</pmb:order>
    							</rdf:Description>";
    					}
    				}
    				break;
    			case 'query_list':
    				$this->optional_properties.= "
    					<pmb:list_query>".htmlspecialchars($options['QUERY'][0]['value'], ENT_QUOTES, 'utf-8')."</pmb:list_query>";
    				break;
    		}
		}
		return $list_items;
	}
	
	public function get_uri_description () {
		return $this->uri_description;
	}
	
	public function set_uri_description ($name) {
		$this->uri_description = $this->prefix . "_" . $name;
	}
	
	public function rec_fields_perso_with_integrator($integrator, $uri, $id) {
		//Enregistrement des champs personalisés
		$integrated_entities = array();
		$query = "delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_origine=".$id;
		pmb_mysql_query($query);
		foreach ($this->t_fields as $key => $val) {
			$query = '';
			$this->set_uri_description($val["NAME"]);
			$property = $integrator->get_store()->get_property($uri, 'pmb:'.$this->uri_description);
			if (count($property))  {
				for ($j = 0; $j < count($property); $j++) {
					if ($property[$j]['type'] === 'uri') {
						$integrated_entity = $integrator->integrate_entity($property[$j]['value']);
						$integrated_entities[] = $integrated_entity;
						if ($integrated_entity['id']) {
							$property[$j]['value'] = $integrated_entity['id'];
						}
					}
					if ($query) {
						$query.= ',';
					}
					
					$value = $property[$j]['value'];
					
					// Multilingue
					$type = $val['TYPE'] ?? ($val['type'] ?? "");
					if (strpos($type, 'i18n') !== FALSE) {
					    $value .= '|||'.$property[$j]['lang'];
					}
					
					$query.= '('.$key.','.$id.',"'.addslashes($value).'",'.$j.')';
				}
				if ($query) {
					$query = 'insert into '.$this->prefix.'_custom_values ('.$this->prefix.'_custom_champ,'.$this->prefix.'_custom_origine,'.$this->prefix.'_custom_'.$val["DATATYPE"].','.$this->prefix.'_custom_order) values '.$query;
					pmb_mysql_query($query);
				}
			}
		}
		return $integrated_entities;
	}
	
	/**
	 * Les champs persos ont-ils été modifiés récemment ?
	 * @return boolean
	 */
	public static function is_modified() {
		global $thesaurus_ontology_filemtime;
		
		if (!file_exists(self::$filename)) {
			return true;
		}
		
		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		if (!isset($tab_file_rdf[self::$filename])) {
			$tab_file_rdf[self::$filename] = 0;
		}
		if(filemtime(self::$filename) > $tab_file_rdf[self::$filename]){
			return true;
		}
		return false;
	}
	
	public static function load_in_store($store, $force = false) {
		global $thesaurus_ontology_filemtime;
		
		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		if (!isset($tab_file_rdf[self::$filename])) {
			$tab_file_rdf[self::$filename] = 0;
		}

		if ($force || (filemtime(self::$filename) > $tab_file_rdf[self::$filename])) {
			$ontology_pperso = '';
			foreach (self::$entities_uri as $prefix => $uri) {
				$onto_parametre_perso = new onto_parametres_perso($prefix);
				$ontology_pperso.= $onto_parametre_perso->build_onto();
			}
			
			//cms parametres perso
			$onto_cms_parametre_perso = new onto_cms_parametres_perso();
			$ontology_pperso_cms = $onto_cms_parametre_perso->build_onto();
			$ontology_pperso.= $ontology_pperso_cms;
			
			//autorite perso
			$onto_auth_perso = new onto_auth_perso();
			$ontology_pperso.= $onto_auth_perso->build_onto();
			
			self::remove_file();
			file_put_contents(self::$filename, "<?xml version='1.0' encoding='UTF-8'?>
	<rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
		xmlns:dct='http://purl.org/dc/terms/'
		xmlns:pmb='http://www.pmbservices.fr/ontology#'
		xmlns:owl='http://www.w3.org/2002/07/owl#'
		xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
		".$ontology_pperso."
	</rdf:RDF>");
			
			$res = $store->query('LOAD <file:///'.realpath(self::$filename).'>');
			
			$tab_file_rdf[self::$filename] = filemtime(self::$filename);
			
			if($res){
				$thesaurus_ontology_filemtime = serialize($tab_file_rdf);
				$query='UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
				pmb_mysql_query($query);
				return true;
			}else{
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Supprime le fichier
	 */
	public static function reinitialize() {
		global $thesaurus_ontology_filemtime;
		
		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		$tab_file_rdf[self::$filename] = 0;

		$thesaurus_ontology_filemtime = serialize($tab_file_rdf);
		$query='UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
		pmb_mysql_query($query);
	}
	
	protected function set_restrictions($field_params) {
		$min = '0';
		$max = 'n';
		$restrict = "";
		if ((isset($field_params['MANDATORY']) && $field_params['MANDATORY']) || (isset($field_params['obligatoire']) && $field_params['obligatoire'])) {
			$min = '1';
			$restrict.= "
		<owl:minCardinality rdf:datatype='http://www.w3.org/2001/XMLSchema#nonNegativeInteger'>1</owl:minCardinality>";
		}
		if ((!isset($field_params['OPTIONS'][0]['MULTIPLE'][0]['value']) && isset($field_params['OPTIONS'][0]['REPEATABLE'][0]['value']) && ($field_params['OPTIONS'][0]['REPEATABLE'][0]['value'] == '0'))
				|| (isset($field_params['OPTIONS'][0]['MULTIPLE'][0]['value']) && ($field_params['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'no'))) {
			$max = '1';
			$restrict.= "
		<owl:maxCardinality rdf:datatype='http://www.w3.org/2001/XMLSchema#nonNegativeInteger'>1</owl:maxCardinality>";
		}
		
		if ($restrict) {
			$this->blank_nodes.= "
	<rdf:Description rdf:nodeID='".$this->uri_description."_".$min."-".$max."'>
		<rdf:type rdf:resource='http://www.w3.org/2002/07/owl#Restriction'/>
		<owl:onProperty rdf:resource='http://www.pmbservices.fr/ontology#" . $this->uri_description. "'/>	
		".$restrict."
	</rdf:Description>";
			
			$this->rdf_nodeId = $this->uri_description."_".$min."-".$max;
			$this->parent_subclasses.= "
		<rdfs:subClassOf rdf:nodeID='".$this->rdf_nodeId."'/>";
		}
	}
	
	public function get_assertions_for_rdf($id, $subject) {
	    $assertions = array();
	    $this->get_values($id);
	    foreach ($this->t_fields as $key => $val) {
	        if (isset($this->values[$key])) {
	            $this->set_uri_description($val["NAME"]);
	            $property = 'http://www.pmbservices.fr/ontology#'. $this->uri_description;
	            foreach ($this->values[$key] as $value) {
	                $object_properties = $this->get_object_properties($value,$val);
	                $object_type = 'http://www.w3.org/2000/01/rdf-schema#Literal';
	                if (isset($val['OPTIONS'][0]['DATA_TYPE'][0]['value'])) {
	                    $object_type = $this->get_authority_from_query_auth($val['OPTIONS'][0]['DATA_TYPE'][0]['value']);
	                }
	                $assertions[] = new onto_assertion($subject, $property, $value, $object_type, $object_properties);
	            }
	        }
	    }
	    return $assertions;
	}
	
	protected function get_object_properties($value, $val) {
	    switch ($val['TYPE']) {
	        case 'query_auth' :
	            $object_type = $this->get_authority_type_from_query_auth($val['OPTIONS'][0]['DATA_TYPE'][0]['value']);
	            return array(
	                'type' => 'uri',
	                'display_label' => rdf_entities_converter::get_entity_isbd($value, $object_type)
	            );
	            break;
	        default :
	            return array('type' => 'literal');
	            break;
	    }
	}
		
	public function get_authority_type_from_query_auth ($choice) {
		$choice = intval($choice);
	    switch ($choice){
	        case 1:
	            return 'author';
	        case 2:
	            return 'category';
	        case 3:
	            return 'publisher';
	        case 4:
	            return 'collection';
	        case 5:
	            return 'sub_collection';
	        case 6:
	            return 'serie';
	        case 7:
	            return 'indexint';
	        case 8:
	            return 'work';
	        case 9:
	        default:
	            if($choice >=1000){
	                return 'authperso_'.intval($choice-1000);
	            }
	            return "concept";
	    }
	}
	
	/**
	 * On supprime le fichier temporaire
	 * @return boolean
	 */
	public static function remove_file() {
	    if (is_file(self::$filename)) {
	        return unlink(self::$filename);
	    }
	    return false;
	}
}