<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_concept.class.php,v 1.8 2023/05/04 14:15:47 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/skos/skos_concept.class.php");
require_once($include_path."/templates/index_concept_form.tpl.php");

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
 * class index_concept
 * Pour l'indexation des concepts
 */
class index_concept {
	
	/**
	 * Type d'objet à indexer
	 * @var int
	 */
	private $object_type;
	
	/**
	 * Identifiant de l'objet indexé (si il existe)
	 * @var int
	 */
	private $object_id;
	
	/**
	 * Tableau des concepts associés à l'objet
	 * @var skos_concept
	 */
	private $concepts = array();
	
	
	private static $type_table = array(
			TYPE_AUTHOR => AUT_TABLE_AUTHORS,
			TYPE_CATEGORY => AUT_TABLE_CATEG,
			TYPE_PUBLISHER => AUT_TABLE_PUBLISHERS,
			TYPE_COLLECTION => AUT_TABLE_COLLECTIONS,
			TYPE_SUBCOLLECTION => AUT_TABLE_SUB_COLLECTIONS,
			TYPE_SERIE => AUT_TABLE_SERIES,
			TYPE_TITRE_UNIFORME => AUT_TABLE_TITRES_UNIFORMES,
			TYPE_INDEXINT => AUT_TABLE_INDEXINT,
			TYPE_AUTHPERSO => AUT_TABLE_AUTHPERSO
	);
	
	public function __construct($object_id, $object_type) {
		$this->object_id = $object_id;
		$this->object_type = $object_type;
	}
	
	/**
	 * Retourne le formulaire d'indexation des concepts
	 * @param string $caller Nom du formulaire
	 * @return string
	 */
	public function get_form($caller) {
		global $index_concept_form, $index_concept_script, $index_concept_add_button_form, $index_concept_text_form, $charset;
		
		if (!count($this->concepts)) {
			$this->get_concepts();
		}

		$form = $index_concept_form;

		$max_concepts = count($this->concepts) ;
		
		$tab_concept_order="";
		$concepts_repetables = $index_concept_script.$index_concept_add_button_form;
		
		$concepts_repetables = str_replace("!!caller!!", $caller, $concepts_repetables);
		
		if ( count($this->concepts)==0 ) {
			$current_concept_form = str_replace('!!iconcept!!', "0", $index_concept_text_form) ;
			$current_concept_form = str_replace('!!concept_display_label!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_uri!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_type!!', '', $current_concept_form);
			$tab_concept_order = "0";
			$concepts_repetables.= $current_concept_form;
		} else {
			foreach ($this->concepts as $i => $concept) {
				$current_concept_form = str_replace('!!iconcept!!', $i, $index_concept_text_form) ;
				
				$current_concept_form = str_replace('!!concept_display_label!!', htmlentities($concept->get_display_label(),ENT_QUOTES, $charset), $current_concept_form);
				$current_concept_form = str_replace('!!concept_uri!!', $concept->get_uri(), $current_concept_form);
				$current_concept_form = str_replace('!!concept_type!!', $concept->get_type(), $current_concept_form);
				
				if($tab_concept_order!="")$tab_concept_order.=",";
				$tab_concept_order.= $i;
				$concepts_repetables.= $current_concept_form;
			}
		}
		$form = str_replace('!!max_concepts!!', $max_concepts, $form);
		$form = str_replace('!!concepts_repetables!!', $concepts_repetables, $form);
		$form = str_replace('!!tab_concept_order!!', $tab_concept_order, $form);
		
		return $form;
	}
	
	/**
	 * Instancie les concepts d'après les données du formulaire
	 */
	public function get_from_form() {
		global $concept, $tab_concept_order;
		$concept_order = explode(",", $tab_concept_order);
		foreach ($concept_order as $index) {
			if (isset($concept[$index]['value']) && $concept[$index]['value']) {
				$this->concepts[] = new skos_concept(0, $concept[$index]['value']);
			}
		}
	}
	
	public function add_concept($concept){
		if(!in_array($concept,$this->concepts)){
			$this->concepts[] = $concept;
		}
	}
	
	public static function is_concept_in_form() {
		global $concept;
		
		if (count($concept)) {
			foreach ($concept as $object) {
				if (isset($object['value']) && $object['value']) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Sauvegarde
	 */
	public function save($from_form = true) {
		// On commence par supprimer l'existant
		$query = "delete from index_concept where num_object = ".$this->object_id." and type_object = ".$this->object_type;
		pmb_mysql_query($query);
		
		// On sauvegarde les infos transmise par le formulaire
		if($from_form){
			$this->get_from_form();
		}
		foreach ($this->concepts as $order => $concept) {
			$query = "insert into index_concept (num_object, type_object, num_concept, order_concept) values (".$this->object_id.",".$this->object_type.",".$concept->get_id().",".$order.")";
			pmb_mysql_query($query);
		}
	}
	
	public function get_concepts() {
		if (!count($this->concepts) && $this->object_id) {
			$this->concepts = array();
			$query = "select num_concept, order_concept from index_concept where num_object = ".$this->object_id." and type_object = ".$this->object_type." order by order_concept";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)){
					$this->concepts[] = new skos_concept($row->num_concept);
				}
			}
		}
		return $this->concepts;
	}
	
	/**
	 * Retourne la liste des concepts pour l'affichage dans l'aperçu de notice
	 * @return string
	 */
	public function get_isbd_display() {
		global $thesaurus_concepts_affichage_ordre, $thesaurus_concepts_concept_in_line;
		global $index_concept_isbd_display_concept_link;
		global $msg;

		if (!count($this->concepts)) {
			$this->get_concepts();
		}
		
		$isbd_display = "";
		
		if (count($this->concepts)) {
			$concepts_list = "";
			
			// On trie le tableau des concepts selon leurs schemas
			$sorted_concepts = array();
			
			foreach ($this->concepts as $concept) {
				$schemes = $concept->get_schemes();
				if (count($schemes)) {
					$scheme = implode(',', $schemes);
				} else {
					$scheme = $msg['index_concept_label'];
				}
				$sorted_concepts[$scheme][$concept->get_id()] = $concept->get_display_label();
			}
			
			//On génère la liste
			foreach ($sorted_concepts as $scheme => $concepts) {
				$isbd_display .= "<br />";
				// Si affichage en ligne, on affiche le nom du schema qu'une fois
				if ($thesaurus_concepts_concept_in_line == 1) {
					$isbd_display .= "<b>".$scheme."</b><br />";
				}
				
				$concepts_list = "";
				
				// On trie par ordre alphabétique si spécifié en paramètre
				if ($thesaurus_concepts_affichage_ordre != 1) {
					asort($concepts);
				}
				foreach ($concepts as $concept_id => $concept_display_label) {
					$current_concept = "";
					
					// Si affichage les uns en dessous des autres, on affiche le schema à chaque fois
					if ($thesaurus_concepts_concept_in_line != 1) {
						$current_concept = "[".$scheme."] ";
					}
					$current_concept .= $index_concept_isbd_display_concept_link;
					$current_concept = str_replace("!!concept_id!!", $concept_id, $current_concept);
					$current_concept = str_replace("!!concept_display_label!!", $concept_display_label, $current_concept);
					
					if ($concepts_list) {
						// On va chercher le séparateur spécifié dans les paramètres
						if ($thesaurus_concepts_concept_in_line == 1) {
							$concepts_list .= " ; ";
						} else {
							$concepts_list .= "<br />";
						}
					}
					$concepts_list .= $current_concept;
				}
				$isbd_display.= $concepts_list;
			}
		}
		
		return $isbd_display;
	}

	/**
	 * Retourne les données des concepts pour l'affichage dans les template
	 * @return string
	 */
	public function get_data() {
		global $thesaurus_concepts_affichage_ordre;
		global $index_concept_isbd_display_concept_link;
		global $msg;
	
		if (!count($this->concepts)) {
			$this->get_concepts();
		}
		$concepts_list = array();
		if (count($this->concepts)) {							
			// On trie le tableau des concepts selon leurs schemas
			$sorted_concepts = array();				
			foreach ($this->concepts as $concept) {
				$schemes = $concept->get_schemes();
				if (count($schemes)) {
					$scheme = implode(',',$schemes);
				} else {
					$scheme = $msg['index_concept_label'];
				}
				$sorted_concepts[$scheme][$concept->get_id()] = $concept->get_display_label();
			}				
			//On génère la liste
			foreach ($sorted_concepts as $scheme => $concepts) {	
				// On trie par ordre alphabétique si spécifié en paramètre
				if ($thesaurus_concepts_affichage_ordre != 1) {
					asort($concepts);
				}
				foreach ($concepts as $concept_id => $concept_display_label) {
					$concept_data = array();
					$concept_data['sheme']=$scheme;
					$link=str_replace("!!concept_id!!", $concept_id ?? "", $index_concept_isbd_display_concept_link);
					$link=str_replace("!!concept_display_label!!", $concept_display_label ?? "", $link);
					$concept_data['link']=$link;
					$concept_data['id']=$concept_id;
					$concept_data['label']=$concept_display_label;	
					$concepts_list[]=$concept_data;
				}
			}
		}	
		return $concepts_list;
	}
		
	/**
	 * Suppression
	 */
	public function delete() {
		if ($this->object_id) {
			$query = "delete from index_concept where num_object = ".$this->object_id." and type_object = ".$this->object_type;
			pmb_mysql_query($query);
		}
	}
	
	public static function update_linked_elements($num_concept){
		$num_concept = intval($num_concept);
		$query = "select num_object,type_object from index_concept where num_concept = ".$num_concept;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				switch($row->type_object){
					case TYPE_NOTICE :
						notice::majNoticesMotsGlobalIndex($row->num_object,"concept");
						break;
					default : 
						//TODO AR
						break;
				}
			}
		}
	}
	
	public static function get_aut_table_type_from_type($type){
		if(isset(self::$type_table[$type])){
			return self::$type_table[$type];
		}
	}
	
	public function get_object_id() {
		return $this->object_id;
	}
	
	public function set_object_id($object_id) {
		$this->object_id = $object_id;
	}
} // fin de définition de la classe index_concept
