<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_concept.class.php,v 1.46.2.2.2.1 2025/04/03 14:54:25 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/concept.class.php");
require_once($include_path."/templates/index_concept_form.tpl.php");
require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/skos/skos_datastore.class.php");
require_once($class_path."/indexation_stack.class.php");

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
	 * @var concept
	 */
	private $concepts = array();

	/**
	 * tableau des commentaires de l'indexation de l'objet
	 * @var array
	 */
	private $comments = array();


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

	private static $entities_caches = [];

	private static $authorities_caches = [];

	private static $narrowers_labels= [];

	private static $concept_labels = [];

	private static $concept_altlabels = [];

	private static $concept_hiddenlabels = [];

	private static $concept_max_number = 2000;

	const LANG_CODES = array(
	    'fr' => 'fr_FR',
	    'en' => 'en_UK'
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
			$button_add_field = "<input id='add_field_index_concept' type='button' class='bouton' value='+' onClick=\"onto_add('concept',0);\"/>";
			$current_concept_form = str_replace('!!iconcept!!', "0", $index_concept_text_form) ;
			$current_concept_form = str_replace('!!concept_display_label!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_uri!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_type!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_comment!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!concept_comment_visible_opac!!', '', $current_concept_form);
			$current_concept_form = str_replace('!!button_add_field!!', $button_add_field, $current_concept_form);
			$tab_concept_order = "0";
			$concepts_repetables.= $current_concept_form;
		} else {
			foreach ($this->concepts as $i => $concept) {
				$button_add_field = "";
				$current_concept_form = str_replace('!!iconcept!!', $i, $index_concept_text_form) ;

				$current_concept_form = str_replace('!!concept_display_label!!', htmlentities($concept->get_isbd(),ENT_QUOTES, $charset), $current_concept_form);
				$current_concept_form = str_replace('!!concept_uri!!', $concept->get_uri(), $current_concept_form);
				$current_concept_form = str_replace('!!concept_type!!', $concept->get_type(), $current_concept_form);
				$current_concept_form = str_replace('!!concept_comment!!', (isset($this->comments[$i]) ? $this->comments[$i]['value'] : ""), $current_concept_form);
				$current_concept_form = str_replace('!!concept_comment_visible_opac!!', (!empty($this->comments[$i]['visible']) ? "checked" : ""), $current_concept_form);
				if ($i === ($max_concepts - 1)) {
					$button_add_field = "<input id='add_field_index_concept' type='button' class='bouton' value='+' onClick=\"onto_add('concept',0);\"/>";
				}
				$current_concept_form = str_replace('!!button_add_field!!', $button_add_field, $current_concept_form);
				if($tab_concept_order!="") $tab_concept_order.=",";
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

		if (!isset($tab_concept_order) || !is_string($tab_concept_order)) {
			// $tab_concept_order n'est pas defini ou n'est pas une chaine de caracteres.
			return;
		}

		if ($tab_concept_order !== "") {
    		$concept_order = explode(",", $tab_concept_order);
    		foreach ($concept_order as $index) {
    			if (
					isset($concept[$index]) &&
					isset($concept[$index]['value']) &&
					$concept[$index]['value']
				) {
    			    if (is_numeric($concept[$index]['value'])) {
    			        $concept[$index]['value'] = onto_common_uri::get_uri($concept[$index]['value']);
    			    }

					$this->concepts[] = new concept(0, $concept[$index]['value'], $concept[$index]['type'], $concept[$index]['display_label']);
    				$this->comments[] = array(
    				    'value' => $concept[$index]['comment'],
    				    'visible' => (!empty($concept[$index]['comment_visible_opac']) ? 1 : 0)
    				);
    			}
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
		if (count($this->concepts)) {
		    $query = "insert into index_concept (num_object, type_object, num_concept, order_concept, comment, comment_visible_opac) values ";
		    $query_values = "";
    		foreach ($this->concepts as $order => $concept) {
    		    if ($query_values) {
    		        $query_values .= ", ";
    		    }
    		    $query_values .= "(".$this->object_id.",".$this->object_type.",".$concept->get_id().",".$order.", ".(isset($this->comments[$order]) ? "'".$this->comments[$order]['value']."', ".$this->comments[$order]['visible'] : "'', 0").")";
    		}
    		pmb_mysql_query($query.$query_values);
		}
	}

	public function get_concepts() {
		if (!count($this->concepts) && $this->object_id) {
			$this->concepts = array();
			$this->comments = array();
			$query = "select num_concept, order_concept, comment, comment_visible_opac from index_concept where num_object = ".$this->object_id." and type_object = ".$this->object_type." order by order_concept";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)){
					$this->concepts[] = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT, $row->num_concept);//new concept($row->num_concept);
					$this->comments[] = array('value' => $row->comment, 'visible' => $row->comment_visible_opac);
				}
			}
		}
		return $this->concepts;
	}

	public function get_concepts_id() {
		$concepts_id = array();
		if ($this->object_id) {
			$query = "select num_concept, order_concept from index_concept where num_object = ".$this->object_id." and type_object = ".$this->object_type." order by order_concept";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)){
					$concepts_id[] = $row['num_concept'];
				}
			}
		}
		return $concepts_id;
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
				if ($concept->get_scheme()) {
					$scheme = $concept->get_scheme();
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
						$concept_display_label = "[".$scheme."] " . $concept_display_label;
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
				if ($concept->get_scheme()) {
					$scheme = $concept->get_scheme();
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
						indexation_stack::push($row->num_object, TYPE_NOTICE, "concept");
						break;
					case TYPE_AUTHOR :
						auteur::update_index($row->num_object,"concept");
						break;
					case TYPE_CATEGORY:
						categories::update_index($row->num_object,"concept");
						break;
					case TYPE_PUBLISHER:
						editeur::update_index($row->num_object,"concept");
						break;
					case TYPE_COLLECTION:
						collection::update_index($row->num_object,"concept");
						break;
					case TYPE_SUBCOLLECTION:
						subcollection::update_index($row->num_object,"concept");
						break;
					case TYPE_SERIE:
						serie::update_index($row->num_object,"concept");
						break;
					case TYPE_TITRE_UNIFORME:
						titre_uniforme::update_index($row->num_object,"concept");
						break;
					case TYPE_INDEXINT:
						indexint::update_index($row->num_object,"concept");
						break;
					default :
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

	/**
	 * Retourne un tableau des libellés des concepts qui indexent une entité
	 * (Utilisée en callback par l'indexation)
	 */
	public static function get_concepts_labels_from_entity($entity_id, $entity_type, $scheme_id = 0) {
		$concepts_labels = array();
		$concepts = self::get_concepts_form_entity($entity_id, $entity_type, $scheme_id);

		foreach ($concepts as $concept) {
			$concepts_labels[] = self::get_concept_label_from_id($concept['num_concept']);
		}
		return $concepts_labels;
	}


	public static function get_concepts_altlabels_from_entity($entity_id, $entity_type, $scheme_id = 0) {
	    $concepts_labels = array();
	    $concepts = self::get_concepts_form_entity($entity_id, $entity_type, $scheme_id);

	    foreach ($concepts as $concept) {
	        $concepts_labels = array_merge($concepts_labels,self::get_concept_altlabel_from_id($concept['num_concept']));
	    }
	    return $concepts_labels;
	}

	public static function get_concepts_hiddenlabels_from_entity($entity_id, $entity_type, $scheme_id = 0) {
	    $concepts_labels = array();
	    $concepts = self::get_concepts_form_entity($entity_id, $entity_type, $scheme_id);

	    foreach ($concepts as $concept) {
	        $concepts_labels = array_merge($concepts_labels,self::get_concept_hiddenlabel_from_id($concept['num_concept']));
	    }
	    return $concepts_labels;
	}

	public static function get_concepts_property_from_entity($entity_id, $entity_type, $scheme_id = 0, $property='') {
	    switch ($property) {
	        case 'labels':
	            return static::get_concepts_labels_from_entity($entity_id, $entity_type, $scheme_id);
	        case 'altlabels':
	            return static::get_concepts_altlabels_from_entity($entity_id, $entity_type, $scheme_id);
	        case 'hiddenlabels':
	            return static::get_concepts_hiddenlabels_from_entity($entity_id, $entity_type, $scheme_id);
	        case 'generic':
	            return static::get_generic_concepts_labels_from_entity($entity_id, $entity_type);
	        case 'specific':
	            return static::get_specific_concepts_labels_from_entity($entity_id, $entity_type);
	    }
	}

	public static function get_generic_concepts_labels_from_entity($entity_id, $entity_type) {
		global $thesaurus_concepts_autopostage;

		$concepts_broaders_labels = array();
		if ($thesaurus_concepts_autopostage) {
			$concepts = self::get_concepts_form_entity($entity_id, $entity_type);
			foreach ($concepts as $concept) {
				$concept_uri = onto_common_uri::get_uri($concept['num_concept']);
				$query = "SELECT ?broadpath {<".$concept_uri."> pmb:broadPath ?broadpath}";
				skos_datastore::query($query);
				if (skos_datastore::num_rows()) {
		 			foreach (skos_datastore::get_result() as $result) {
						$ids_broders = explode('/', $result->broadpath);
						foreach ($ids_broders as $id_broader) {
							if ($id_broader) {
								$broader_label = self::get_concept_label_from_id($id_broader);
								if (!in_array($broader_label, $concepts_broaders_labels)) {
									$concepts_broaders_labels[] = $broader_label;
								}
							}
						}
		 			}
				}
			}
		}
		return $concepts_broaders_labels;
	}

	public static function get_specific_concepts_labels_from_entity($entity_id, $entity_type) {
		global $thesaurus_concepts_autopostage;

		$concepts_narrowers_labels = array();
		if ($thesaurus_concepts_autopostage) {
			$concepts = self::get_concepts_form_entity($entity_id, $entity_type);
			foreach ($concepts as $concept) {
			    if(empty(self::$narrowers_labels[$concept['num_concept']])) {
			        if (!isset(self::$narrowers_labels[$concept['num_concept']])) {
    			        self::$narrowers_labels[$concept['num_concept']] = array();
			        }
    				$concept_uri = onto_common_uri::get_uri($concept['num_concept']);
    				$query = "SELECT ?narrowpath {<".$concept_uri."> pmb:narrowPath ?narrowpath}";
    				skos_datastore::query($query);
    				if (skos_datastore::num_rows()) {
    		 			foreach (skos_datastore::get_result() as $result) {
    						$ids_narrowers = explode('/', $result->narrowpath);
    						foreach ($ids_narrowers as $id_narrower) {
    							if ($id_narrower) {
    								$narrower_label = self::get_concept_label_from_id($id_narrower);
    								if (!in_array($narrower_label, self::$narrowers_labels[$concept['num_concept']])) {
    								    self::$narrowers_labels[$concept['num_concept']][] = $narrower_label;
    								}
    							}
    						}
    		 			}
    				}
			    }
			    if(is_countable(self::$narrowers_labels[$concept['num_concept']]) && count(self::$narrowers_labels[$concept['num_concept']]) > 0) {
			        for($i=0; $i<count(self::$narrowers_labels[$concept['num_concept']]); $i++){
			            if(!in_array(self::$narrowers_labels[$concept['num_concept']][$i], $concepts_narrowers_labels)) {
			                $concepts_narrowers_labels[] = self::$narrowers_labels[$concept['num_concept']][$i];
			            }
			        }
			    }
			}
		}
		return $concepts_narrowers_labels;
	}

	public static function get_concept_from_id($concept_id, $property) {
		switch ($property) {
			case 'label':
				return self::get_concept_label_from_id($concept_id);
			case 'altlabel':
				return self::get_concept_altlabel_from_id($concept_id);
			case 'hiddenlabel':
				return self::get_concept_hiddenlabel_from_id($concept_id);
		}
	}

	protected static function get_concept_label_from_id($concept_id) {
	    if(count(self::$concept_labels) > self::$concept_max_number){
			self::$concept_labels = [];
		}
	    if(isset(self::$concept_labels[$concept_id])){
	        return self::$concept_labels[$concept_id];
	    }
	    self::$concept_labels[$concept_id] = [];
		$concept_uri = onto_common_uri::get_uri($concept_id);
		$query = 'select ?label where {
					<'.$concept_uri.'> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
				}';
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			foreach (skos_datastore::get_result() as $concept) {
			    $lang = "";
			    if (isset($concept->label_lang)) {
			        $lang = self::LANG_CODES[$concept->label_lang] ?? "";
			    }
		        self::$concept_labels[$concept_id][$lang] = $concept->label;
			}
		}
		return self::$concept_labels[$concept_id];
	}


	protected static function get_concept_altlabel_from_id($concept_id) {
	    if(count(self::$concept_altlabels) > self::$concept_max_number){
	        self::$concept_altlabels = [];
	    }
	    if(isset(self::$concept_altlabels[$concept_id])){
	        return self::$concept_altlabels[$concept_id];
	    }
	    self::$concept_altlabels[$concept_id] = [];
	    $concept_uri = onto_common_uri::get_uri($concept_id);
	    $query = 'select ?label where {
					<'.$concept_uri.'> <http://www.w3.org/2004/02/skos/core#altLabel> ?label
				}';
	    skos_datastore::query($query);
	    if (skos_datastore::num_rows()) {
	        foreach (skos_datastore::get_result() as $concept) {
	            self::$concept_altlabels[$concept_id][]= $concept->label;
	        }
	    }
	    return self::$concept_altlabels[$concept_id];
	}

	protected static function get_concept_hiddenlabel_from_id($concept_id) {
	    if(count(self::$concept_hiddenlabels) > self::$concept_max_number){
	        self::$concept_hiddenlabels = [];
	    }
	    if(isset(self::$concept_hiddenlabels[$concept_id])){
	        return self::$concept_hiddenlabels[$concept_id];
	    }
	    self::$concept_hiddenlabels[$concept_id] = [];
	    $concept_uri = onto_common_uri::get_uri($concept_id);
	    $query = 'select ?label where {
					<'.$concept_uri.'> <http://www.w3.org/2004/02/skos/core#hiddenLabel> ?label
				}';
	    skos_datastore::query($query);
	    if (skos_datastore::num_rows()) {
	        foreach (skos_datastore::get_result() as $concept) {
	            self::$concept_hiddenlabels[$concept_id][]= $concept->label;
	        }
	    }
	    return self::$concept_hiddenlabels[$concept_id];
	}

	protected static function get_concepts_form_entity($entity_id, $entity_type, $scheme_id = 0) {
		$scheme_uri = 0;
		if (!empty($scheme_id)) {
			$scheme_uri = onto_common_uri::get_uri($scheme_id);
		}

		if(isset(self::$entities_caches[$entity_id."_".$entity_type."_".$scheme_id])){
		    return self::$entities_caches[$entity_id."_".$entity_type."_".$scheme_id];
		}

		$concepts = array();
		$query = "SELECT num_concept, order_concept FROM index_concept WHERE type_object = ".$entity_type." AND num_object = ".$entity_id." ORDER BY order_concept";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)){
				if (!empty($scheme_uri)) {
					$concept_uri = onto_common_uri::get_uri($row['num_concept']);
					$query = 'select ?scheme_uri where {
						<'.$concept_uri.'> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme_uri
					}';
					skos_datastore::query($query);
					if (skos_datastore::num_rows()) {
						foreach (skos_datastore::get_result() as $scheme) {
							if ($scheme->scheme_uri == $scheme_uri) {
								$concepts[] = $row;
								break;
							}
						}
					}
					continue;
				}
				$concepts[] = $row;
			}
		}
		if(count(self::$entities_caches) > 2000){
		    self::$entities_caches = [];

		}
		self::$entities_caches[$entity_id."_".$entity_type."_".$scheme_id] = $concepts;
		return $concepts;
	}

	/**
	 * Retourne un tableau des libelles des concepts qui indexent une autorite
	 * (Utilisée en callback par l'indexation)
	 */
	public static function get_concepts_labels_from_linked_authority($entity_id, $authority_type, $authperso_type = 0) {
	    $concepts_labels = array();
	    $concepts = self::get_concepts_from_linked_authority($entity_id, $authority_type, $authperso_type);

	    foreach ($concepts as $concept) {
	        $concepts_labels[] = self::get_concept_label_from_id($concept['num_concept']);
	    }
	    return $concepts_labels;
	}

	/**
	 * recherche des concepts lies a l'autorite
	 * @param int $entity_id
	 * @param int $entity_type
	 * @param number $scheme_id
	 * @return array
	 */
	protected static function get_concepts_from_linked_authority($entity_id, $authority_type, $authperso_type = 0) {
		if (!empty($authperso_type)) {
		    $authority_type = 1000 + intval($authperso_type);
		}
		if(isset(self::$authorities_caches[$entity_id."_".$authority_type])){
		    return self::$authorities_caches[$entity_id."_".$authority_type];
		}
		$concepts = array();
		$query = "SELECT aut_link_to_num AS num_concept, aut_link_rank AS order_concept
                FROM aut_link
                WHERE aut_link_to = ".AUT_TABLE_CONCEPT." AND aut_link_from = ".$authority_type." AND aut_link_from_num = ".$entity_id."
                UNION
                SELECT aut_link_from_num AS num_concept, aut_link_rank AS order_concept
                FROM aut_link
                WHERE aut_link_from = ".AUT_TABLE_CONCEPT." AND aut_link_to = ".$authority_type." AND aut_link_to_num = ".$entity_id."
                ORDER BY order_concept";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)){
				$concepts[] = $row;
			}
		}
		if(count(self::$authorities_caches) > 2000){
		    self::$authorities_caches = [];

		}
		self::$authorities_caches[$entity_id."_".$authority_type] = $concepts;
		return $concepts;
	}

	public static function set_concept_max_number($concept_max_number) {
	    static::$concept_max_number = intval($concept_max_number);
	}
} // fin de définition de la classe index_concept
