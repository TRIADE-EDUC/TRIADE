<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_concept.class.php,v 1.40 2019-05-22 08:03:41 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/skos/skos_datastore.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/category.class.php");
require_once($class_path."/publisher.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/explnum.class.php");
require_once($class_path."/authperso_authority.class.php");
require_once($class_path."/skos/skos_view_concepts.class.php");
require_once($class_path."/skos/skos_view_concept.class.php");
require_once($class_path."/authority.class.php");

if(!defined('TYPE_NOTICE')){
	define('TYPE_NOTICE',1);
}
if(!defined('TYPE_AUTHOR')){
	define('TYPE_AUTHOR',2);
}
if(!defined('TYPE_CATEGORY')){
	define('TYPE_CATEGORY',3);
}
if(!defined('TYPE_PUBLISHER')){
	define('TYPE_PUBLISHER',4);
}
if(!defined('TYPE_COLLECTION')){
	define('TYPE_COLLECTION',5);
}
if(!defined('TYPE_SUBCOLLECTION')){
	define('TYPE_SUBCOLLECTION',6);
}
if(!defined('TYPE_SERIE')){
	define('TYPE_SERIE',7);
}
if(!defined('TYPE_TITRE_UNIFORME')){
	define('TYPE_TITRE_UNIFORME',8);
}
if(!defined('TYPE_INDEXINT')){
	define('TYPE_INDEXINT',9);
}
if(!defined('TYPE_EXPL')){
	define('TYPE_EXPL',10);
}
if(!defined('TYPE_EXPLNUM')){
	define('TYPE_EXPLNUM',11);
}
if(!defined('TYPE_AUTHPERSO')){
	define('TYPE_AUTHPERSO',12);
}
if(!defined('TYPE_CMS_SECTION')){
	define('TYPE_CMS_SECTION',13);
}
if(!defined('TYPE_CMS_ARTICLE')){
	define('TYPE_CMS_ARTICLE',14);
}

/**
 * class skos_concept
 * Le modèle d'un concept
*/
class skos_concept {
	
	/**
	 * Identifiant du concept
	 * @var int
	 */
	private $id;
	
	/**
	 * URI du concept
	 * @var string
	 */
	private $uri;
	
	/**
	 * Label du concept
	 * @var string
	 */
	private $display_label;
	
	/**
	 * Tableau des schemas du concept
	 * @var string
	 */
	private $schemes;
	
	/**
	 * Vedette composée associée si concept composé
	 * @var vedette_composee
	 */
	private $vedette = null;
	
	/**
	 * Enfants du concept
	 * @var skos_concepts_list
	 */
	private $narrowers;
	
	/**
	 * template des enfants du concept
	 * @var string
	 */
	private $narrowers_list;
	
	/**
	 * Parents du concept
	 * @var skos_concepts_list
	 */
	private $broaders;
	
	/**
	 * template des parents du concept
	 * @var string
	 */
	private $broaders_list;
	
	/**
	 * Concepts composés qui utilisent ce concept
	 * @var skos_concepts_list
	 */
	private $composed_concepts;
	
	/**
	 * Tableau des identifiants de notices indexées par le concept
	 * @var array
	 */
	private $indexed_notices;
	
	/**
	 * Tableau associatif de tableaux d'autorités indexées par le concept
	 * @var array
	 */
	private $indexed_authorities;

	/**
	 * Tableau des champs perso
	 * @var array
	 */	
	private $p_perso;
	
	/**
	 * Note du concept
	 * @var string
	 */
	private $note;
	/**
	 * Definition du concept
	 * @var string
	 */
	private $definition;
	
	/**
	 * Relations associées
	 * @var skos_concepts_list $related
	 */
	private $related;
	
	/**
	 * template des relations associées du concept
	 * @var string
	 */
	private $related_list;
	
	/**
	 * termes associés
	 * @var skos_concepts_list $related
	 */
	private $related_match;
	
	/**
	 * template des termes associés du concept
	 * @var string
	 */
	private $related_match_list;
	
	/**
	 * Note historique
	 * @var string
	 */
	private $history_note;
	
	/**
	 * Exemple
	 * @var string
	 */
	private $example;
	
	/**
	 * Carte associée
	 * @var map_objects_controler
	 */
	private $map = null;
	
	/**
	 * Info de la carte associée
	 * @var map_info
	 */
	private $map_info = null;
	
	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 */
	public function __construct($id = 0, $uri = "") {
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_display_label();
		} else {
			$this->uri = $uri;
			$this->get_id();
			$this->get_display_label();
		}
	}
	
	/**
	 * Retourne l'URI du concept
	 */
	public function get_uri() {
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_uri($this->id);
		}
		return $this->uri;
	}
	
	/**
	 * Retourne l'identifiant du concept
	 * @return int
	 */
	public function get_id() {
		if (!$this->id) {
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->id;
	}
	
	/**
	 * Retourne le libellé à afficher
	 * @return string
	 */
	public function get_display_label() {
		if (!$this->display_label) {
			global $lang;
				
			$this->check_display_label_in_index();
			if(!$this->display_label){
	
				$query = "select * where {
					<".$this->uri."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
				}";
				
				skos_datastore::query($query);
				if(skos_datastore::num_rows()){
					$results = skos_datastore::get_result();
					foreach($results as $key=>$result){
						if(isset($result->label_lang) && $result->label_lang==substr($lang,0,2)){
							$this->display_label = $result->label;
							break;
						}
					}
					//pas de langue de l'interface trouvée
					if (!$this->display_label){
						$this->display_label = $result->label;
					}
				}
			}
		}
		return $this->display_label;
	}
	
	private function check_display_label_in_index(){
		$query = 'select value from skos_fields_global_index where id_item = '.$this->id.' and code_champ = code_ss_champ and code_champ = 1';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->display_label = pmb_mysql_result($result, 0, 0);
		}
	}
	
	/**
	 * Retourne les schémas du concept
	 * @return string
	 */
	public function get_schemes() {
		global $dbh, $lang;
		
		if (!isset($this->schemes)) {
			$this->schemes = array();
			$query = "select value, lang, authority_num from skos_fields_global_index where id_item = ".$this->id." and code_champ = 4 and code_ss_champ = 1";
			$last_values = array();
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->lang == substr($lang,0,2)) {
						$this->schemes[$row->authority_num] = $row->value;
						break;
					}
					$last_values[$row->authority_num] = $row->value;
				}
				//pas de langue de l'interface trouvée
				foreach ($last_values as $scheme_id => $last_value) {
					if (!isset($this->schemes[$scheme_id])) {
						$this->schemes[$scheme_id] = $last_value;
					}
				}
			}
		}
		return $this->schemes;
	}
	
	/**
	 * Retourne la vedette composée associée au concept
	 * @return vedette_composee
	 */
	public function get_vedette() {
		if (!$this->vedette) {
			if ($vedette_id = vedette_link::get_vedette_id_from_object($this->id, TYPE_CONCEPT_PREFLABEL)) {
				$this->vedette = new vedette_composee($vedette_id);
			}
		}
		return $this->vedette;
	}
	
	/**
	 * Retourne les enfants du concept
	 * @return skos_concepts_list Liste des enfants du concept
	 */
	public function get_narrowers() {
		if (!$this->narrowers) {
			$this->narrowers = new skos_concepts_list();
	
			$query = "select ?narrower where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower .
                ?narrower skos:prefLabel ?narrower_label . 
			}
            order by ?narrower_label";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->narrowers->add_concept(new skos_concept(0, $result->narrower));
				}
			}
		}
		return $this->narrowers;
	}
	
	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_narrowers_list() {
	    if (!isset($this->narrowers_list)) {
	        $this->narrowers_list = skos_view_concepts::get_narrowers_list($this->get_narrowers());
	    }
	    return $this->narrowers_list;
	}
	
	/**
	 * Retourne les parents du concept
	 * @return skos_concepts_list Liste des parents du concept
	 */
	public function get_broaders() {
		if (!$this->broaders) {
			$this->broaders = new skos_concepts_list();
	
			$query = "select ?broader where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
                ?broader skos:prefLabel ?broader_label . 
			}
            order by ?broader_label";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->broaders->add_concept(new skos_concept(0, $result->broader));
				}
			}
		}
		return $this->broaders;
	}
	
	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_broaders_list() {
	    if (!isset($this->broaders_list)) {
	        $this->broaders_list = skos_view_concepts::get_broaders_list($this->get_broaders());
	    }
	    return $this->broaders_list;
	}
	
	/**
	 * Retourne le rendu HTML des relations associatives
	 */
	public function get_related_list() {	    
	    if (!isset($this->related_list)) {
	        $this->related_list = skos_view_concepts::get_related_list($this->get_related());
	    }
	    return $this->related_list;
	}
	
	/**
	 * Retourne le rendu HTML des termes associés
	 */
	public function get_related_match_list() {	    
	    if (!isset($this->related_match_list)) {
	        $this->related_match_list = skos_view_concepts::get_related_match_list($this->get_related_match());
	    }
	    return $this->related_match_list;
	}
	
	/**
	 * Retourne les identifiants des notices indexées par le concept
	 * @return array Tableau des notices indexées par le concept
	 */
	public function get_indexed_notices() {
		global $dbh;
		
		if (!$this->indexed_notices) {
			$this->indexed_notices = array();
			
			$query = "select num_object from index_concept where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->indexed_notices[] = $row->num_object;
				}
			}
			$filter = new filter_results($this->indexed_notices);
			$this->indexed_notices = explode(",",$filter->get_results());
		}
		return $this->indexed_notices;
	}
	
	/**
	 * Charge les données de carthographie
	 */
	private function fetch_map() {
	    global $pmb_map_activate;
	    
	    if ($pmb_map_activate) {
	        $this->map = new map_objects_controler(AUT_TABLE_CONCEPT, array($this->id));
	        $this->map_info = new map_info($this->id);
	    }
	}
	
	/**
	 * Retourne la carte associée
	 * @return map_objects_controler
	 */
	public function get_map() {
	    if (!$this->map) {
	        $this->fetch_map();
	    }
	    return $this->map;
	}
	
	/**
	 * Retourne les infos de la carte associée
	 * @return map_info
	 */
	public function get_map_info() {
	    if (!$this->map_info) {
	        $this->fetch_map();
	    }
	    return $this->map_info;
	}
	
	/**
	 * Retourne les autorités indexées par le concept
	 * @return array Tableau associatif de tableaux d'autorités indexées par le concept
	 */
	public function get_indexed_authorities() {
		global $dbh;
		
		if (!$this->indexed_authorities) {
			$this->indexed_authorities = array();
			
			$query = "select num_object, type_object from index_concept where num_concept = ".$this->id." and type_object != ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					switch ($row->type_object) {
						case TYPE_AUTHOR :
							$this->indexed_authorities['author'][] = new auteur($row->num_object);
							break;
						case TYPE_CATEGORY :
							$this->indexed_authorities['category'][] = new category($row->num_object);
							break;
						case TYPE_PUBLISHER :
							$this->indexed_authorities['publisher'][] = new publisher($row->num_object);
							break;
						case TYPE_COLLECTION :
							$this->indexed_authorities['collection'][] = new collection($row->num_object);
							break;
						case TYPE_SUBCOLLECTION :
							$this->indexed_authorities['subcollection'][] = new subcollection($row->num_object);
							break;
						case TYPE_SERIE :
							$this->indexed_authorities['serie'][] = new serie($row->num_object);
							break;
						case TYPE_TITRE_UNIFORME :
							$this->indexed_authorities['titre_uniforme'][] = new titre_uniforme($row->num_object);
							break;
						case TYPE_INDEXINT :
							$this->indexed_authorities['indexint'][] = new indexint($row->num_object);
							break;
						case TYPE_EXPL :
							//TODO Quelle classe utiliser ?
// 							$this->indexed_authorities['expl'][] = new auteur($row->num_object);
							break;
						case TYPE_EXPLNUM :
							$this->indexed_authorities['explnum'][] = new explnum($row->num_object);
							break;
						case TYPE_AUTHPERSO :
							$this->indexed_authorities['authperso'][] = new authperso_authority($row->num_object);
							break;
						default:
							break;
					}
				}
			}
		}
		return $this->indexed_authorities;
	}
	
	/**
	 * Retourne les concepts composés qui utilisent le concept
	 * @return skos_concepts_list Liste des concepts composés qui utilisent le concept
	 */
	public function get_composed_concepts() {
		if (!$this->composed_concepts) {
			$this->composed_concepts = new skos_concepts_list();
			
			$this->composed_concepts->set_composed_concepts_built_with_element($this->id, "concept");
		}
		return $this->composed_concepts;
	}

	/**
	 * Retourne le détail d'un concept
	 * @return array Tableau des différentes propriétés du concept
	 */
	public function get_details() {
		global $lang;
		$details = array();
		$query = "select * where {
				<".$this->uri."> rdf:type skos:Concept .
				<".$this->uri."> skos:prefLabel ?label .		
				optional {
					<".$this->uri."> skos:altLabel ?altlabel
				} . 
				optional {
					<".$this->uri."> skos:note ?note
				} .
				optional {
					<".$this->uri."> <http://www.w3.org/2004/02/skos/core#Note> ?notebnf
				} .			
				optional {
					<".$this->uri."> skos:related ?related .
					optional {		
						?related skos:prefLabel ?relatedlabel	
					}
				} .
				optional {
					<".$this->uri."> skos:related ?related .
					optional {		
						?related skos:prefLabel ?relatedlabel	
					}
				} .
				optional {
					<".$this->uri."> owl:sameAs ?sameas .
					optional {		
						?sameas skos:prefLabel ?sameaslabel	
					}
				} .
				optional {
					<".$this->uri."> rdfs:seeAlso ?seealso .
					optional {		
						?seealso skos:prefLabel ?seealsolabel	
					}
				} .
				optional {
					<".$this->uri."> skos:exactMatch ?exactmatch .
					optional {		
						?exactmatch skos:prefLabel ?exactmatchlabel	
					}
				} .
				optional {
					<".$this->uri."> skos:closeMatch ?closematch .
					optional {		
						?closematch skos:prefLabel ?closematchlabel	
					}
				}
			}";
			
		skos_datastore::query($query);
		if(skos_datastore::num_rows()){
			$results = skos_datastore::get_result();
			foreach($results as $result){
				foreach($result as $property => $value){
					switch($property){
						//cas des literaux
						case "altlabel" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#altLabel'])){
								$details['http://www.w3.org/2004/02/skos/core#altLabel'] = array();
							}
							if(isset($result->{$propery."_lang"}) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;
						case "hiddenlabel" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])){
								$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'] = array();
							}
							if(isset($result->hiddenlabel_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#altLabel'])){
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;							
						case "related" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#related'])){
								$details['http://www.w3.org/2004/02/skos/core#related'] = array();
							}
							if($result->related_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->relatedlabel)){
									$detail['label'] = $result->relatedlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#related'])){
									$details['http://www.w3.org/2004/02/skos/core#related'][] = $detail;
								}
							}
							break;
						case "sameas" :
							if(!isset($details['http://www.w3.org/2002/07/owl#sameAs'])){
								$details['http://www.w3.org/2002/07/owl#sameAs'] = array();
							}
							if($result->sameas_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->sameaslabel)){
									$detail['label'] = $result->sameaslabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2002/07/owl#sameAs'])){
									$details['http://www.w3.org/2002/07/owl#sameAs'][] = $detail;
								}
							}
							break;
						case "note" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#note'])){
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if(isset($result->note_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "notebnf" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#note'])){
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if(isset($result->notebnf_lang) == substr($lang,0,2)){
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							}else{
								if(!in_array($value,$details['http://www.w3.org/2004/02/skos/core#note'])){
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "seealso" :
							if(!isset($details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])){
								$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'] = array();
							}
							if($result->seealso_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->seealsolabel)){
									$detail['label'] = $result->seealsolabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])){
									$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'][] = $detail;
								}
							}
							break;
						case "exactmatch" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#exactMatch'])){
								$details['http://www.w3.org/2004/02/skos/core#exactMatch'] = array();
							}
							if($result->exactmatch_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->exactmatchlabel)){
									$detail['label'] = $result->exactmatchlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#exactMatch'])){
									$details['http://www.w3.org/2004/02/skos/core#exactMatch'][] = $detail;
								}
							}
							break;
						case "closematch" :
							if(!isset($details['http://www.w3.org/2004/02/skos/core#closeMatch'])){
								$details['http://www.w3.org/2004/02/skos/core#closeMatch'] = array();
							}
							if($result->closematch_type == "uri"){
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array(
									'uri' => $value
								);
								if(isset($result->closematchlabel)){
									$detail['label'] = $result->closematchlabel;
								}
								if($id){
									$detail['id'] = $id;
								}
								if(!in_array($detail,$details['http://www.w3.org/2004/02/skos/core#closeMatch'])){
									$details['http://www.w3.org/2004/02/skos/core#closeMatch'][] = $detail;
								}
							}
							break;
					}
				}			
			}
		}
		return $details;
	}
	
	public function get_details_list() {
		return skos_view_concept::get_detail_concept($this);
	}
	
	public function get_db_id() {
		return $this->get_id();
	}
	
	public function get_isbd() {
		return $this->get_display_label();
	}
	
	public function get_header() {
		return $this->get_display_label();
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->get_id(), $liens_opac['lien_rech_concept']);
	}
	
	public function get_comment() {
		return '';
	}
	
	public function get_authoritieslist() {
		return skos_view_concept::get_authorities_indexed_with_concept($this);
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'id' => $this->get_id(),
				'uri' => $this->get_uri(),
				'permalink' => $this->get_permalink(),
				'label' => $this->get_isbd(),
				'note' => $this->get_note(),
				'schemes' => $this->get_schemes(),
				'broaders_list' => $this->get_broaders_list(),
				'narrowers_list' => $this->get_narrowers_list()
		);
// 		$authority = new authority(0, $this->id, AUT_TABLE_CONCEPT);
// 		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	/**
	 * Retourne les champs perso du concept
	 */
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_CONCEPT]);
	}
	
	/**
	 * Retourne la note
	 * @return string
	 */
	public function get_note() {
		global $lang;
			
		if (!$this->note) {
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#note> ?note
			}";
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $key=>$result){
					if($result->note_lang==substr($lang,0,2)){
						$this->note = $result->note;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->note){
					$this->note = $result->note;
				}
			}
		}
		return $this->note;
	}
	
	/**
	 * Retourne la definition
	 * @return string
	 */
	public function get_definition() {
	    global $lang;
	    
	    if (!$this->definition) {
	        $query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#definition> ?definition
			}";
	        skos_datastore::query($query);
	        if(skos_datastore::num_rows()){
	            $results = skos_datastore::get_result();
	            foreach($results as $key=>$result){
	                if($result->definition_lang==substr($lang,0,2)){
	                    $this->definition = $result->definition;
	                    break;
	                }
	            }
	            //pas de langue de l'interface trouvée
	            if (!$this->definition){
	                $this->definition = $result->definition;
	            }
	        }
	    }
	    return $this->definition;
	}
	
	/**
	 * Retourne la note historique
	 * @return string
	 */
	public function get_history_note() {
		global $lang;
		
		if (empty($this->history_note)) {
			$this->history_note = '';
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#historyNote> ?historyNote
			}";
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $key=>$result){
					if($result->historyNote_lang==substr($lang,0,2)){
						$this->history_note = $result->historyNote;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->history_note){
					$this->history_note = $result->historyNote;
				}
			}
		}
		return $this->history_note;
	}
	
	/**
	 * Retourne l'exemple
	 * @return string
	 */
	public function get_example() {
		global $lang;
		
		if (empty($this->example)) {
			$this->example = '';
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#example> ?example
			}";
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $key=>$result){
					if($result->example_lang==substr($lang,0,2)){
						$this->example = $result->example;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->example){
					$this->example = $result->example;
				}
			}
		}
		return $this->example;
	}
	
	/**
	 * retourne les relations associatives
	 * @return skos_concepts_list
	 */
	public function get_related() {
	    if (isset($this->related)) {
	        return $this->related;
	    }
	    $this->related = new skos_concepts_list();
	    
	    $query = "select ?related where {
			<".$this->uri."> <http://www.w3.org/2004/02/skos/core#related> ?related
		}";
	    
	    skos_datastore::query($query);
	    if(skos_datastore::num_rows()){
	        $results = skos_datastore::get_result();
	        foreach($results as $result){
	            $this->related->add_concept(new skos_concept(0, $result->related));
	        }
	    }
	    return $this->related;
	}
	
	/**
	 * retourne les termes associés
	 * @return skos_concepts_list
	 */
	public function get_related_match() {
	    if (isset($this->related_match)) {
	        return $this->related_match;
	    }
	    $this->related_match = new skos_concepts_list();
	    
	    $query = "select ?related_match where {
			<".$this->uri."> <http://www.w3.org/2004/02/skos/core#relatedMatch> ?related_match
		}";
	    
	    skos_datastore::query($query);
	    if(skos_datastore::num_rows()){
	        $results = skos_datastore::get_result();
	        foreach($results as $result){
	            $this->related_match->add_concept(new skos_concept(0, $result->related_match));
	        }
	    }
	    return $this->related_match;
	}
}