<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_authority.class.php,v 1.12 2020/12/10 13:42:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/i_2709.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/category.class.php");
require_once($class_path."/notice_authority_generic.class.php");
require_once($class_path."/origins.class.php");
require_once($class_path.'/skos/skos_concept.class.php');


/*
 * Classe pour les autorité classiques...
 */
class notice_authority extends iso2709_authorities implements notice_authority_generic{
	public $type;
	public $common_data;
	public $specifics_data;
	public $rejected_forms;
	public $associated_forms;
	public $parallel_forms;
	public $use_rejected;
	public $use_associated;
	public $use_parallel;
	protected $concept_or_category;
	
	static protected $scheme_bnf_labels = array();
	
	public function __construct($data="",$type="UNI",$file_charset="iso-8859-1"){
		if($file_charset == "utf-8"){
			$this->is_utf8 = true;
		}
		parent::__construct($data,$type);
		if($this->error){
			$this->try_autocorrect();
		}
		$this->get_type();
	}
	
	public function get_type(){
		switch($this->guide_infos['et']){
			// nom de personne
			case "a" :
				$this->type = "author"; 
				break;
			// nom de collectivité
			case "b" :
				$this->type = "author";
				break;
			// famille
			case "e" :	
				$this->type = "author";
				break;
			// marque
			case "d" :
				$this->type = "author";
				break;
			// titre uniforme
			case "f" :
				$this->type = "uniform_title";
				break;
			// nom de territoire ou nom géographique	
			case "c" :	
			// matière nom commun
			case "j" :
			// forme, genre ou caractéristiques physiques	
			case "l" :	
				$this->type = $this->get_concept_or_category();
				break;
			/*certaines autorités ne sont pas traitables par PMB*/
			// rubrique de classement
			case "g" :
			// lieu d'édition
			case "k" :
			// auteur / titre
			case "h" :
			// auteur / rubrique de classement
			case "i" :	
			default :
				$this->type = "";
				break;
		}			
	}
	
	/*
	 * Pour avoir le numéro d'autorité 
	 */
	public function format_authority_number($authority_number){
		global $pmb_import_modele_authorities;
		//appel à la méthode statique de la classe d'import...
		if($pmb_import_modele_authorities!= ""){
			return call_user_func(array($pmb_import_modele_authorities,"format_authority_number"),$authority_number);
		}else{
			return $authority_number;
		}
	}	
	
	
	public function get_informations($use_rejected = true, $use_associated = true, $use_parallel = false){
		$this->use_rejected = $use_rejected;
		$this->use_associated = $use_associated;
		$this->use_parallel = $use_parallel;
		if(!$this->error){
			$this->get_common_informations();
			$this->get_specifics_informations();
			$this->get_rejected_forms();
			$this->get_associated_forms();
			$this->get_parallel_forms();
		}
	}
	
	public function get_common_informations(){
		$this->common_data = array();
		$this->common_data['authority_number'] = $this->format_authority_number($this->fields['001'][0]['value']);
		$this->common_data['lang'] = (isset($this->fields[101][0]['a'][0]) ? $this->fields[101][0]['a'][0] : '');
		$this->common_data['source']=array(
			'country' => $this->fields[801][0]['a'][0],
			'origin' => $this->fields[801][0]['b'][0],
			'date' => $this->fields[801][0]['c'][0]
		);
	}
	
	public function get_specifics_informations(){
		switch($this->guide_infos['et']){
			// nom de personne
			case "a" :
				$this->specifics_data = auteur::get_informations_from_unimarc($this->fields,"2",70);
				break;
			// nom de collectivité
			case "b" :
				$this->specifics_data = auteur::get_informations_from_unimarc($this->fields,"2",71);				
				break;
			// famille
			case "e" :	
				$this->specifics_data = auteur::get_informations_from_unimarc($this->fields,"2",71,"220");
				break;
			// marque
			case "d" :
				$this->specifics_data = auteur::get_informations_from_unimarc($this->fields,"2",71,"216");
				break;
			// titre uniforme
			case "f" :
				$this->specifics_data = titre_uniforme::get_informations_from_unimarc($this->fields,"2");
				break;
			case "j" :	
			// matière nom commun
				if ($this->get_concept_or_category() == 'category') {
					$this->specifics_data = category::get_informations_from_unimarc($this->fields,false,"250");
				} else {
					$this->specifics_data = skos_concept::get_informations_from_unimarc($this->fields,false,"250");
				}
				break;
			// nom de territoire ou nom géographique	
			case "c" :
				if ($this->get_concept_or_category() == 'category') {
					$this->specifics_data = category::get_informations_from_unimarc($this->fields,false,"215");
				} else {
					$this->specifics_data = skos_concept::get_informations_from_unimarc($this->fields,false,"215");
				}
				break;
			// forme, genre ou caractéristiques physiques	
			case "l" :
				if ($this->get_concept_or_category() == 'category') {
					$this->specifics_data = category::get_informations_from_unimarc($this->fields,false,"280");
				} else {
					$this->specifics_data = skos_concept::get_informations_from_unimarc($this->fields,false,"280");
				}
				break;		
			// rubrique de classement
			case "g" :
			/*certaines autorités ne sont pas traitables par PMB*/
			// lieu d'édition
			case "k" :
			// auteur / titre
			case "h" :
			// auteur / rubrique de classement
			case "i" :		
			default :
				break;
		}		
	}
	
	public function get_rejected_forms(){
		if($this->use_rejected)
			$this->rejected_forms = $this->get_linked_authorities("4");
	}
	
	public function get_associated_forms(){
		if($this->use_associated)
			$this->associated_forms = $this->get_linked_authorities("5");		
	}
		
	public function get_parallel_forms(){
		if($this->use_parallel)
			$this->parallel_forms = $this->get_linked_authorities("7");
	}

	public function get_linked_authorities($zone){
		$data = array();
		foreach($this->fields as $key => $field){
			switch($key){
				// Forme associée - Nom de Personne
				case $zone."00" :
					for($i=0 ; $i<count($field) ; $i++){
						$infos = array();
						$infos = auteur::get_informations_from_unimarc($field[$i],$zone,70);
						$infos['link_code'] = $field[$i]['5'][0];
						$infos['comment'] = (isset($field[$i]['0'][0]) ? $field[$i]['0'][0] : '');
						$data[] = $infos;
					}
					break;
				// Forme associée - Nom de Collectivité
				case $zone."10" :
				// Forme associée - Marque
				case $zone."16" :	
				// Forme associée - Famille
				case $zone."20" :
					for($i=0 ; $i<count($field) ; $i++){
						$infos = array();
						$infos = auteur::get_informations_from_unimarc($field[$i],$zone,71);
						$infos['link_code'] = $field[$i]['5'][0];
						$infos['comment'] = (isset($field[$i]['0'][0]) ? $field[$i]['0'][0] : '');
						$data[] = $infos;
					}
					break;
				// Forme associée - Titre Uniforme
				case $zone."30" :
					for($i=0 ; $i<count($field) ; $i++){
						$infos = titre_uniforme::get_informations_from_unimarc($field[$i],$zone);
						$infos['link_code'] = $field[$i]['5'][0];
						$infos['comment'] = (isset($field[$i]['0'][0]) ? $field[$i]['0'][0] : '');
						$data[] = $infos;
					}
					break;
				// Forme rejetée - Rubrique de Classement
				case $zone."35" :
					
					break;
				// Forme associée - Nom de territoire ou nom géographique 
				case $zone."15" :
				// Forme associée - Auteur / Titre 
				case $zone."40" :
				// Forme associée - Auteur / Rubrique de Classement
				case $zone."45" :
				// Forme associée - Forme, genre ou caractéristiques physiques
				case $zone."80" :	
				// Forme associée - Matière nom commun
				case $zone."50" :
					if ($this->type == 'concept') {
						// si on est sur un concept, on gère les liens avec les autres concepts à part 
						break;
					}
					for($i=0 ; $i<count($field) ; $i++){
						if ($this->get_concept_or_category() == 'category') {
							$infos = category::get_informations_from_unimarc($field[$i],true);
						} else {
							$infos = skos_concept::get_informations_from_unimarc($field[$i],true);
						}
						$infos['link_code'] = (isset($field[$i]['5'][0]) ? $field[$i]['5'][0] : '');
						if($this->type != $this->get_concept_or_category() || ($this->type == $this->get_concept_or_category() && $infos['link_code']!="z")){
							$infos['comment'] = (isset($field[$i]['0'][0]) ? $field[$i]['0'][0] : '');
						}
						$data[] = $infos;
					}
					break;
				// Forme associée - Lieu d'édition	
				case $zone."60" :
				default :
					break;
			}
		}
		return $data;				
	}
	
	
	public function check_if_exists($data,$id_thesaurus = 0){
		switch($data['type_authority']){
			// Forme associée - Nom de Personne
			case "author" :
				$id = auteur::check_if_exists($data);
				break;
			// Forme associée - Titre Uniforme	
			case "uniform_title" :
				$id = titre_uniforme::import_tu_exist($data);
				break;
			case "category" :
				$id = category::check_if_exists($data,$id_thesaurus,0,$this->common_data['lang']);
				break;
			case "concept" :
				$id = skos_concept::check_if_exists($data,$id_thesaurus,$this->common_data['lang']);
				break;
			default :
				$id=0;
				break;
		}
		return $id; 
	}
	
	protected function try_autocorrect(){
		$this->error = false;
		foreach($this->directory_table as $field){
			if(substr($field['LABEL'],0,1) == "2"){
				switch($field['LABEL']){
					case "200" :
						$this->guide_infos["et"] = "a";
						break;
					case "210" :
					case "214" :
						$this->guide_infos["et"] = "b";
						break;
					case "215" :
						$this->guide_infos["et"] = "c";
						break;
					case "216" :
						$this->guide_infos["et"] = "d";
						break;
					case "220" :
						$this->guide_infos["et"] = "e";
						break;
					case "230" :
						$this->guide_infos["et"] = "f";
						break;
					case "235" :
						$this->guide_infos["et"] = "g";
						break;
					case "240" :
						$this->guide_infos["et"] = "h";
						break;
					case "245" :
						$this->guide_infos["et"] = "i";
						break;
					case "250" :
						$this->guide_infos["et"] = "j";
						break;
					case "260" :
						$this->guide_infos["et"] = "k";
						break;
					case "280" :
						$this->guide_infos["et"] = "l";
						break;
				}
				break;
			}else{
				continue;
			}
		}
		if ($this->check_guide_infos()) {
			$this->read_fields();
		}
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function set_concept_or_category($concept_or_category) {
		$this->concept_or_category = $concept_or_category;
		return $this;
	}
	
	public function get_concept_or_category() {
		if (empty($this->concept_or_category)) {
			$this->concept_or_category = 'category';
		}
		return $this->concept_or_category;
	}
	
	public function get_scheme_uri() {
		$scheme_uri = '';
		switch($this->guide_infos['et']){
			// nom de personne
			case "a" :
				break;
			// nom de collectivité
			case "b" :
				break;
			// famille
			case "e" :
				break;
			// marque
			case "d" :
				break;
			// titre uniforme
			case "f" :
				break;
			// matière nom commun
			case "j" :
				$scheme_uri = 'http://data.bnf.fr/vocabulary/scheme/r166';
				break;
			// nom de territoire ou nom géographique
			case "c" :
				$scheme_uri = 'http://data.bnf.fr/vocabulary/scheme/r167';
				break;
			// forme, genre ou caractéristiques physiques
			case "l" :
				break;
			// rubrique de classement
			case "g" :
			/*certaines autorités ne sont pas traitables par PMB*/
			// lieu d'édition
			case "k" :
			// auteur / titre
			case "h" :
			// auteur / rubrique de classement
			case "i" :
			default :
				break;
		}
		if(!$scheme_uri) {
			return '';
		}
		$label = $this->get_scheme_bnf_label($scheme_uri);
		if (!$label) {
			return '';
		}
		$this->create_schema($scheme_uri, $label);
		return $scheme_uri;
	}
	
	public function get_scheme_bnf_label($scheme_uri) {
		if (!isset(self::$scheme_bnf_labels[$scheme_uri])) {
			self::$scheme_bnf_labels[$scheme_uri] = '';
			$storebnf = ARC2::getRemoteStore(array(
					'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
			));
			$result = $storebnf->query('PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
				SELECT ?label
				WHERE {
					<'.$scheme_uri.'> rdfs:label ?label
				}', 'row');
			if(!empty($result)) {
				self::$scheme_bnf_labels[$scheme_uri] = $result['label'];
			}	
		}
		return self::$scheme_bnf_labels[$scheme_uri];
	}
	
	protected function create_schema($uri, $label) {
		$scheme_id = onto_common_uri::get_id($uri);
		if(!$scheme_id) {
			$query = 'insert into <pmb> {
					<'.$uri.'> rdf:type skos:ConceptScheme ;
							skos:prefLabel "'.addslashes($label).'" ;
							pmb:showInTop owl:Nothing
				}';
			skos_datastore::query($query);
			$onto_index = onto_index::get_instance('skos');
			$onto_index->maj(0, $uri);
			$scheme_id = onto_common_uri::set_new_uri($uri);
		}
		return $scheme_id;
	}
}