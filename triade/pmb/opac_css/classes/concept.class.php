<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept.class.php,v 1.2 2022/01/05 15:25:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/onto/onto_index.class.php");
require_once($class_path."/skos/skos_onto.class.php");
require_once($class_path."/skos/skos_datastore.class.php");


/**
 * class concept
 * Un concept
 */
class concept {
	
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
	 * URI de la classe de l'ontologie
	 * @var string
	 */
	private $type;
	
	/**
	 * Label du concept
	 * @var string
	 */
	private $display_label;
	
	/**
	 * Nom du schema du concept
	 * @var string
	 */
	private $scheme;
	
	/**
	 * Tableau des identifiants de notices indexées par le concept
	 * @var array
	 */
	private $indexed_notices;
	
	/**
	 * Vedette composée associée au concept
	 * @var vedette_composee
	 */
	private $vedette;
	
	private static $onto_index;
	
	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 * @param string $type [optional] URI de la classe de l'ontologie
	 * @param string $display_label [optional] Label du concept
	 */
	public function __construct($id, $uri = "", $type = "", $display_label = "") {
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_type();
			$this->get_display_label();
		} else {
			$this->uri = $uri;
			$this->get_id();
			$this->type = $type;
			$this->display_label = $display_label;
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
	 * Retourne le type du concept
	 * @return string
	 */
	public function get_type() {
		if (!$this->type) {
			$this->type = "http://www.w3.org/2004/02/skos/core#Concept";
		}
		return $this->type;
	}
	
	public function get_display_label() {
		if (!$this->display_label) {
			global $lang;
			$this->check_display_label_in_index();
			if(!$this->display_label){
				$query = "select ?label where {
					<".$this->uri."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
				}";
				skos_datastore::query($query);
				if(skos_datastore::num_rows()){
					$results = skos_datastore::get_result();
					foreach($results as $result){
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
		global $lang;
		$query = 'select value from skos_fields_global_index where id_item = '.$this->id.' and code_champ = code_ss_champ and code_champ = 1 and lang in (""'.($lang ? ',"'.$lang.'"' : '').') order by lang desc';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->display_label = pmb_mysql_result($result, 0, 0);
		}
	}
	
	public function update_display_label($label) {
		// On commence par supprimer le label existant
		$query = "delete {
				<".$this->get_uri()."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?obj
				}";
		skos_datastore::query($query);
		
		// On insert le nouveau label
		$query = "insert into <pmb> {
				<".$this->get_uri()."> <http://www.w3.org/2004/02/skos/core#prefLabel> '".$label."'
				}";
		
		$this->display_label = $label;
		
		skos_datastore::query($query);
		indexation_stack::push($this->get_id(), TYPE_CONCEPT);
		
		index_concept::update_linked_elements($this->get_id());
	}
	
	public function get_scheme() {
		global $lang;
		
		if (!$this->scheme) {
			$query = "select value, lang from skos_fields_global_index where id_item = ".$this->id." and code_champ = 4 and code_ss_champ = 1";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->scheme = $row->value;
					if ($row->lang == substr($lang,0,2)) {
						break;
					}
				}
			}
		}
		return $this->scheme;
	}

	/**
	 * Retourne les identifiants des notices indexées par le concept
	 * @return array Tableau des notices indexées par le concept
	 */
	public function get_indexed_notices() {
		if (!$this->indexed_notices) {
			$this->indexed_notices = array();
			
			$query = "select num_object from index_concept where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE;
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->indexed_notices[] = $row->num_object;
				}
			}
		}
		return $this->indexed_notices;
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
	
	public static function get_concept_id_from_label($label, $id_scheme = 0) {
		$query = 'SELECT S1.id_item FROM skos_fields_global_index S1 ';
		if($id_scheme){
			$query.=' JOIN skos_fields_global_index S2 ON S1.id_item = S2.id_item WHERE S2.authority_num = "'.$id_scheme.'" AND S2.code_champ = 4 AND S2.code_ss_champ = 1 AND ';
		} else {
			$query.=' WHERE ';
		}
		$query .= 'S1.value = "'.$label.'" AND S1.code_champ = 1 AND S1.code_ss_champ = 1';
		
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 0);
		}
		
		$query = "select ?uri where {
					?uri <http://www.w3.org/2004/02/skos/core#prefLabel> '".addslashes($label)."'";
		if ($id_scheme) {
			$uri_scheme = onto_common_uri::get_uri($id_scheme);
			$query .= "	 .
					?uri <http://www.w3.org/2004/02/skos/core#inScheme> <".addslashes($uri_scheme)."> ";	
		}
		$query.= "}";
		skos_datastore::query($query);
		if(skos_datastore::num_rows()){
			$results = skos_datastore::get_result();
			$uri = $results[0]->uri;
			return onto_common_uri::get_id($uri);
		}
		
		return 0;
	}
	
	public static function get_schema_id_from_label($label){
		$query = 'select id_item from skos_fields_global_index where value = "'.$label.'" and code_champ = 100 and code_ss_champ = 1';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 0);
		}
		return 0;
	}
	
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=concept&id='.$this->id;
	}
	
	/**
	 * Récupération de l'identifiant du concept composé
	 * Soit on récupère celui existant (si il y'en a un) soit on le crée
	 */
	public static function get_concepts_composed_with_entity($object_id, $object_type, $grammar, $subdivision_type){
		
		//Filtre sur la subdivision dans le cas ou elle est passée en paramètre
		$sub_request_subdivision = '';
		$end_condition = '';
		$concept_id = array();
		
		if($subdivision_type){
			$sub_request_subdivision = " and vedette_object.subdivision = '".$subdivision_type."' ";
			$end_condition = " having count(*) = 1 ";
		}
		
		$query = "select id_vedette, id_authority, vedette_link.num_object from vedette join vedette_object
				on vedette_object.num_vedette = vedette.id_vedette
				 ".$sub_request_subdivision." 
				and vedette_object.object_type = '".$object_type."'
				and vedette_object.object_id = ".$object_id."
				and vedette.grammar = '".$grammar."'
				join vedette_link
				on vedette_link.num_vedette = vedette_object.num_vedette
				and vedette_link.type_object = 1
				join authorities
				on vedette_link.num_object = authorities.num_object
				and authorities.type_object = '".AUT_TABLE_CONCEPT."'
				group by id_vedette
				".$end_condition;
		$result = pmb_mysql_query($query);

		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$concept_id[] = $row->id_authority;
			}
		}
		return $concept_id;
	}
	
	public function get_isbd() {
		global $msg;
		$this->get_scheme();
		if(!empty($this->scheme)){
			$display_label = '['.$this->scheme.'] ';
		}else{
			$display_label = '['.$msg['skos_view_concept_no_scheme'].'] ';
		}
		return $display_label.$this->get_display_label();
	
	}
} // end of concept