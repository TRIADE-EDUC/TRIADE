<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_concept.class.php,v 1.53.2.1 2024/08/27 08:13:47 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/onto/common/onto_common_uri.class.php");
require_once ($class_path . "/onto/onto_store_arc2.class.php");
require_once ($class_path . "/skos/skos_datastore.class.php");
require_once ($class_path . "/notice.class.php");
require_once ($class_path . "/author.class.php");
require_once ($class_path . "/category.class.php");
require_once ($class_path . "/editor.class.php");
require_once ($class_path . "/collection.class.php");
require_once ($class_path . "/subcollection.class.php");
require_once ($class_path . "/serie.class.php");
require_once ($class_path . "/titre_uniforme.class.php");
require_once ($class_path . "/indexint.class.php");
require_once ($class_path . "/explnum.class.php");
require_once ($class_path . "/authperso_authority.class.php");
require_once ($class_path . "/skos/skos_view_concepts.class.php");
require_once ($class_path . "/skos/skos_view_concept.class.php");
require_once ($class_path . "/concept.class.php");
require_once ($class_path . "/vedette/vedette_composee.class.php");
require_once ($class_path . "/authority.class.php");
require_once ($class_path . "/aut_pperso.class.php");
require_once ($class_path . "/aut_link.class.php");
require_once ($class_path . "/audit.class.php");
require_once $class_path . "/indexation_stack.class.php";

/**
 * class skos_concept
 * Le modèle d'un concept
 */
class skos_concept
{

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
	 * Liste des labels
	 *
	 * @var array
	 */
	private $display_label_list;

	/**
	 * Note du concept
	 * @var string
	 */
	private $note;

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
	 * Parents du concept
	 * @var skos_concepts_list
	 */
	private $broaders;

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
	 * Scope note
	 * @var string $scope_note
	 */
	private $scope_note;

	/**
	 * relations associatives
	 * @var skos_concepts_list $related
	 */
	private $related;

	/**
	 * Termes associés
	 * @var skos_concepts_list $related_match
	 */
	private $related_match;

	/**
	 * Tableau des libellés alternatifs
	 * @var array $altlabel
	 */
	private $altlabel;

	/**
	 * Definition du concept
	 * @var string
	 */
	private $definition;

	private static $handler;

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
	 * Information liée a une contribution
	 * @var array
	 */
	private $infos_from_contribution = array();

	private const ONTO_STORE_CONFIG = array(
	    /* db */
	    'db_name' => DATA_BASE,
	    'db_user' => USER_NAME,
	    'db_pwd' => USER_PASS,
	    'db_host' => SQL_SERVER,
	    /* store */
	    'store_name' => 'ontology',
	    /* stop after 100 errors */
	    'max_errors' => 100,
	    'store_strip_mb_comp_str' => 0
	);

	private const DATA_STORE_CONFIG = array(
	    /* db */
	    'db_name' => DATA_BASE,
	    'db_user' => USER_NAME,
	    'db_pwd' => USER_PASS,
	    'db_host' => SQL_SERVER,
	    /* store */
	    'store_name' => 'rdfstore',
	    /* stop after 100 errors */
	    'max_errors' => 100,
	    'store_strip_mb_comp_str' => 0
	);


	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 */
	public function __construct($id = 0, $uri = "")
	{
		$this->id = 0;
		$this->uri = '';
		$this->display_label = '';
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_display_label();
		} else if ($uri) {
			$this->uri = $uri;
			$this->get_id();
			$this->get_display_label();
		}
	}

	/**
	 * Retourne l'URI du concept
	 */
	public function get_uri()
	{
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_uri($this->id);
		}
		return $this->uri;
	}

	/**
	 * Retourne l'identifiant du concept
	 * @return int
	 */
	public function get_id()
	{
		if (!$this->id) {
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->id;
	}

	/**
	 * Retourne le libellé à afficher
	 * @return string
	 */
	public function get_display_label()
	{
		if (!$this->display_label) {
			global $lang;

			$this->get_display_label_list();
			$this->check_display_label_in_index();
			if (!$this->display_label) {

				$query = "select * where {
					<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
				}";

				skos_datastore::query($query);
				if (skos_datastore::num_rows()) {
					$results = skos_datastore::get_result();
					foreach ($results as $result) {
						if (isset($result->label_lang) && $result->label_lang == substr($lang, 0, 2)) {
							$this->display_label = $result->label;
							break;
						}
					}
					//pas de langue de l'interface trouvée
					if (!$this->display_label) {
						$this->display_label = $result->label;
					}
				}
			}
		}
		return $this->display_label;
	}

	/**
	 * Retourne le libellé à afficher
	 * @return string
	 */
	public function get_display_label_list()
	{
		if (!$this->display_label_list) {
			$this->display_label_list = [];

			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
			}";

			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if (! isset($result->label_lang)) {
						continue;
					}
					$this->display_label_list[$result->label_lang] = $result->label;
				}
			}
		}
		return $this->display_label_list;
	}

	private function check_display_label_in_index()
	{
		global $lang;
		if ($this->id) {
			$query = 'select value from skos_fields_global_index where id_item = ' . $this->id . ' and code_champ = code_ss_champ and code_champ = 1';
			if (!empty($lang)) {
				$andQuery = $query . " AND lang = '" . $lang . "'";
			}
			$result = pmb_mysql_query($andQuery);
			if (pmb_mysql_num_rows($result)) {
				$this->display_label = pmb_mysql_result($result, 0, 0);
			} else {
				$result = pmb_mysql_query($query . " ORDER BY ordre");
				if (pmb_mysql_num_rows($result)) {
					$this->display_label = pmb_mysql_result($result, 0, 0);
				}
			}
		}
	}

	/**
	 * Retourne les schémas du concept
	 * @return string
	 */
	public function get_schemes()
	{
		global $lang;

		if (!isset($this->schemes)) {
			$this->schemes = array();
			$query = "select value, lang, authority_num from skos_fields_global_index where id_item = " . $this->id . " and code_champ = 4 and code_ss_champ = 1";
			$last_values = array();
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->lang == substr($lang, 0, 2)) {
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
			} else {
				$query = "select * where {
					<" . $this->uri . "> skos:inScheme ?scheme .
                    ?scheme skos:prefLabel ?label
				}";

				skos_datastore::query($query);
				if (skos_datastore::num_rows()) {
					$results = skos_datastore::get_result();
					foreach ($results as $result) {
						if (isset($result->label_lang) && $result->label_lang == substr($lang, 0, 2)) {
							$this->schemes[onto_common_uri::get_id($result->scheme)] = $result->label;
							break;
						}
						$last_values[onto_common_uri::get_id($result->scheme)] = $result->label;
					}
					foreach ($last_values as $scheme_id => $last_value) {
						if (!isset($this->schemes[$scheme_id])) {
							$this->schemes[$scheme_id] = $last_value;
						}
					}
				}
			}
		}
		return $this->schemes;
	}


	/**
	 * Retourne la liste des schemas
	 *
	 * @return [
	 *     "uri"    => [
	 *          "default"   => "Nom sans langue",
	 *          "fr"        => "Nom en francais,
	 *          ...
	 *     ]
	 * ]
	 */
	public static function getSchemes()
	{
		$query = "select * where {
            ?elem rdf:type <http://www.w3.org/2004/02/skos/core#ConceptScheme> .
            ?elem skos:prefLabel ?label .
        } order by ?label";

		$scheme_list = [];
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$scheme_list[$result->elem]['default'] = $result->label;
				$scheme_list[$result->elem][$result->label_lang] = $result->label;
			}
		}
		return $scheme_list;
	}


	/**
	 * Retourne la liste des top concepts
	 *
	 * @return array
	 */
	public static function getTopConcepts(string $schemeUri = '')
	{
		$query = "select * where {
            ?elem rdf:type <http://www.w3.org/2004/02/skos/core#Concept> .
            ?elem skos:prefLabel ?label .
            ?elem skos:topConceptOf <" . $schemeUri . ">
        }";
		if ('no_scheme' == $schemeUri) {
			$query = "select * where {
                ?elem rdf:type <http://www.w3.org/2004/02/skos/core#Concept> .
                ?elem skos:prefLabel ?label .
                ?elem pmb:showInTop owl:Nothing
            }";
		}
		$top_concepts_list = [];
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$top_concepts_list[$result->elem]['top'] = $schemeUri;
				$top_concepts_list[$result->elem]['default'] = $result->label;
				$top_concepts_list[$result->elem][$result->label_lang] = $result->label;
			}
		}
		return $top_concepts_list;
	}



	/**
	 * Retourne le rendu HTML des schémas
	 */
	public function get_schemes_list()
	{
		return skos_view_concepts::get_schemes_list($this->get_schemes());
	}


	/**
	 * Retourne la vedette composée associée au concept
	 * @return vedette_composee
	 */
	public function get_vedette()
	{
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
	public function get_narrowers()
	{
		if (isset($this->narrowers)) {
			return $this->narrowers;
		}
		$this->narrowers = new skos_concepts_list();

		$query = "select DISTINCT ?narrower where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower .
                ?narrower skos:prefLabel ?narrower_label .
			}
            order by ?narrower_label";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->narrowers->add_concept(new skos_concept(0, $result->narrower));
			}
		}
		return $this->narrowers;
	}

	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_narrowers_list()
	{
		return skos_view_concepts::get_narrowers_list($this->get_narrowers());
	}

	/**
	 * Retourne les parents du concept
	 * @return skos_concepts_list Liste des parents du concept
	 */
	public function get_broaders()
	{
		if (isset($this->broaders)) {
			return $this->broaders;
		}
		$this->broaders = new skos_concepts_list();

		$query = "select DISTINCT ?broader where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
                ?broader skos:prefLabel ?broader_label .
			}
            order by ?broader_label";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->broaders->add_concept(new skos_concept(0, $result->broader));
			}
		}

		return $this->broaders;
	}

	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_broaders_list()
	{
		return skos_view_concepts::get_broaders_list($this->get_broaders());
	}

	/**
	 * Retourne le rendu HTML des relations associatives
	 */
	public function get_related_list()
	{
		return skos_view_concepts::get_related_list($this->get_related());
	}

	public function get_related()
	{
		if (isset($this->related)) {
			return $this->related;
		}
		$this->related = new skos_concepts_list();

		$query = "select ?related where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#related> ?related
		}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->related->add_concept(new skos_concept(0, $result->related));
			}
		}
		return $this->related;
	}

	/**
	 * Retourne le rendu HTML des termes associés
	 */
	public function get_related_match_list()
	{
		return skos_view_concepts::get_related_match_list($this->get_related_match());
	}

	public function get_related_match()
	{
		if (isset($this->related_match)) {
			return $this->related_match;
		}
		$this->related_match = new skos_concepts_list();

		$query = "select ?related_match where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#relatedMatch> ?related_match
		}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->related_match->add_concept(new skos_concept(0, $result->related_match));
			}
		}
		return $this->related_match;
	}

	/**
	 * Retourne les identifiants des notices indexées par le concept
	 * @return array Tableau des notices indexées par le concept
	 */
	public function get_indexed_notices()
	{
		if (!$this->indexed_notices) {
			$this->indexed_notices = array();

			$query = "select num_object from index_concept where num_concept = " . $this->id . " and type_object = " . TYPE_NOTICE;
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
	 * Charge les données de carthographie
	 */
	private function fetch_map()
	{
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
	public function get_map()
	{
		if (!$this->map) {
			$this->fetch_map();
		}
		return $this->map;
	}

	/**
	 * Retourne les infos de la carte associée
	 * @return map_info
	 */
	public function get_map_info()
	{
		if (!$this->map_info) {
			$this->fetch_map();
		}
		return $this->map_info;
	}

	public function build_header_to_export()
	{
		global $msg;

	    $data = array(
	        'URI',
	        $msg['ontology_skos_conceptscheme'],
	        $msg[67],
	        $msg['ontology_skos_note'],
	        $msg['cms_document_format_data_broaders'],
	        $msg['cms_document_format_data_narrowers'],
	        $msg['onto_common_altlabel'],
	    );
		return $data;
	}

	private function build_data_shemes($elts, $limit = 5)
	{
		$display_elts = '';
		if (count($elts['elements'])) {
			$count_all = 0;
			foreach ($elts['elements'] as $sheme => $concepts) {
				if ($count_all) $display_elts .= '. ';
				$display_elts .= $sheme . ' : ';
				$count_concept = 0;
				foreach ($concepts as $concept) {
					if ($count_concept) $display_elts .= ', ';
					$display_elts .= strip_tags($concept);
					$count_all++;
					$count_concept++;
					if ($count_all > $limit) {
						$display_elts .= '... ';
						break;
					}
					if ($count_all > $limit) break;
				}
			}
		}
		return $display_elts;
	}

	public function build_data_to_export()
	{
		$altlabel_display = '';

		foreach ($this->get_altlabel() as $altlabel) {
			if ($altlabel_display) $altlabel_display .= '; ';
			$altlabel_display .= $altlabel;
		}
	    $formatted_data = array(
	        'uri' => $this->get_uri(),
	        'schemes' => implode(',',$this->get_schemes()),
	        'label' =>$this->get_display_label(),
	        'note' => $this->get_note(),
			'broaders_display' => self::build_data_shemes(skos_view_concepts::get_broaders_data_list($this->get_broaders())),
	        'narrowers_display' => self::build_data_shemes(skos_view_concepts::get_narrowers_data_list($this->get_narrowers())),
	        'altlabel' => $altlabel_display,
	    );
		return $formatted_data;
	}

	/**
	 * Retourne les autorités indexées par le concept
	 * @return array Tableau associatif de tableaux d'autorités indexées par le concept
	 */
	public function get_indexed_authorities()
	{
		if (!$this->indexed_authorities) {
			$this->indexed_authorities = array();

			$query = "select num_object, type_object from index_concept where num_concept = " . $this->id . " and type_object != " . TYPE_NOTICE;
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					switch ($row->type_object) {
						case TYPE_AUTHOR:
							$this->indexed_authorities['author'][] = new auteur($row->num_object);
							break;
						case TYPE_CATEGORY:
							$this->indexed_authorities['category'][] = new category($row->num_object);
							break;
						case TYPE_PUBLISHER:
							$this->indexed_authorities['publisher'][] = new editeur($row->num_object);
							break;
						case TYPE_COLLECTION:
							$this->indexed_authorities['collection'][] = new collection($row->num_object);
							break;
						case TYPE_SUBCOLLECTION:
							$this->indexed_authorities['subcollection'][] = new subcollection($row->num_object);
							break;
						case TYPE_SERIE:
							$this->indexed_authorities['serie'][] = new serie($row->num_object);
							break;
						case TYPE_TITRE_UNIFORME:
							$this->indexed_authorities['titre_uniforme'][] = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $row->num_object);
							break;
						case TYPE_INDEXINT:
							$this->indexed_authorities['indexint'][] = new indexint($row->num_object);
							break;
						case TYPE_EXPL:
							//TODO Quelle classe utiliser ?
							// 							$this->indexed_authorities['expl'][] = new auteur($row->num_object);
							break;
						case TYPE_EXPLNUM:
							$this->indexed_authorities['explnum'][] = new explnum($row->num_object);
							break;
						case TYPE_AUTHPERSO:
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
	public function get_composed_concepts()
	{
		if (!$this->composed_concepts) {
			$this->composed_concepts = new skos_concepts_list();

			$this->composed_concepts->set_composed_concepts_built_with_element($this->id, TYPE_CONCEPT);
		}
		return $this->composed_concepts;
	}

	/**
	 * Retourne le détail d'un concept
	 * @return array Tableau des différentes propriétés du concept
	 */
	public function get_details()
	{
		global $lang;
		$details = array();

		$resource = skos_datastore::get_data_resource();
		$resource->setURI($this->uri);
		$props = $resource->getProps();

		foreach ($props as $prop => $obj) {
			//ces property la, on les gère dans d'autres méthodes
			if(in_array($prop,array(
				'http://www.w3.org/2004/02/skos/core#prefLabel',
				'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
				'http://www.w3.org/2004/02/skos/core#inScheme',
				'http://www.pmbservices.fr/ontology#showInTop',
				'http://www.w3.org/2004/02/skos/core#narrower',
				'http://www.w3.org/2004/02/skos/core#borrower',
				'http://www.w3.org/2004/02/skos/core#hasTopConcept',
				'http://www.w3.org/2004/02/skos/core#topConceptOf',
				'http://www.pmbservices.fr/ontology#broadPath',
			    'http://www.pmbservices.fr/ontology#narrowPath',
			    'http://www.pmbservices.fr/ontology#has_authority_status',
			))){
				continue;
			}
			if (!isset($details[$prop])) {
				$details[$prop] = array();
			}
			for ($i = 0; $i < count($obj); $i++) {
				if ($obj[$i]['type'] == 'literal') {
					$obj[$i]['value'] = encoding_normalize::charset_normalize($obj[$i]['value'], 'utf-8');
					if (isset($obj[$i]['lang']) && $obj[$i]['lang'] == substr($lang, 0, 2)) {
						if (!in_array($obj[$i]['value'], $details[$prop])) {
							$details[$prop][] = $obj[$i]['value'];
						}
						continue;
					} else {
						if (!in_array($obj[$i]['value'], $details[$prop])) {
							$details[$prop][] = $obj[$i]['value'];
						}
					}
				} else {
					$resource->setURI($obj[$i]['value']);
					$subobj = $resource->getProps('skos:prefLabel');
					if ($subobj != '') {
						//on cherche si l'URI est connu dans notre système
						$id = onto_common_uri::get_id($obj[$i]['value']);
						$detail = array('uri' => $obj[$i]['value']);
						for ($j = 0; $j < count($subobj); $j++) {
							$subobj[$j]['value'] = encoding_normalize::charset_normalize($subobj[$j]['value'], 'utf-8');
							if (isset($subobj[$j]['lang']) && $subobj[$j]['lang'] == substr($lang, 0, 2)) {
								$detail['label'] = $subobj[$j]['value'];
							} else if (empty($detail['label'])) {
								$detail['label'] = $subobj[$j]['value'];
							}
						}
						if ($id) {
							$detail['id'] = $id;
						}
						if (!in_array($detail, $details[$prop])) {
							$details[$prop][] = $detail;
						}
					}
				}
			}
			if (count($details[$prop]) === 0) {
				unset($details[$prop]);
			}
		}
		return $details;
	}

	/**
	 * Retourne la note
	 * @return string
	 */
	public function get_note()
	{
		global $lang;

		if (isset($this->note)) {
			return $this->note;
		}
		$this->note = '';
		$query = "select * where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#note> ?note
		}";
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				if (!empty($result->note_lang) && ($result->note_lang == substr($lang, 0, 2))) {
					$this->note = $result->note;
					break;
				}
			}
			//pas de langue de l'interface trouvée
			if (!$this->note) {
				$this->note = $result->note;
			}
		}
		return $this->note;
	}

	/**
	 * Retourne la scopeNote
	 * @return string
	 */
	public function get_scope_note()
	{
		global $lang;

		if (isset($this->scope_note)) {
			return $this->scope_note;
		}
		$this->scope_note = '';
		$query = "select * where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#scopeNote> ?scopeNote
		}";
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				if (!empty($result->scopeNote_lang) && ($result->scopeNote_lang == substr($lang, 0, 2))) {
					$this->scope_note = $result->scopeNote;
					break;
				}
			}
			//pas de langue de l'interface trouvée
			if (!$this->scope_note) {
				$this->scope_note = $result->scopeNote;
			}
		}
		return $this->scope_note;
	}

	public function get_details_list()
	{
		return skos_view_concept::get_detail_concept($this);
	}

	public function get_alter_hidden_list()
	{
		return skos_view_concept::get_alter_hidden_list_concept($this);
	}

	public function get_right()
	{
		return SESSrights & CONCEPTS_AUTH;
	}

	public function get_db_id()
	{
		return $this->get_id();
	}

	public function get_gestion_link()
	{
		return './autorites.php?categ=see&sub=concept&id=' . $this->id;
	}

	public function get_isbd()
	{
		global $msg;
		$this->get_schemes();
		if (count($this->schemes)) {
			$display_label = '[' . implode(' / ', $this->schemes) . '] ';
		} else {
			$display_label = '[' . $msg['skos_view_concept_no_scheme'] . '] ';
		}
		return $display_label . $this->get_display_label();
	}

	public function get_comment()
	{
		return '';
	}

	public function get_authoritieslist()
	{
		return skos_view_concept::get_authorities_indexed_with_concept($this);
	}

	public function get_header()
	{
		return $this->get_isbd();
	}

	public static function get_format_data_structure($antiloop = false)
	{
		global $msg;

		$main_fields = array();
		$main_fields[] = array(
				'var' => "id",
				'desc' => $msg['1601']
		);
		$main_fields[] = array(
				'var' => "uri",
				'desc' => $msg['ontology_object_uri']
		);
		$main_fields[] = array(
				'var' => "permalink",
				'desc' => $msg['notice_permalink_opac']
		);
		$main_fields[] = array(
				'var' => "label",
				'desc' => $msg['cms_concept_format_data_display_label']
		);
		$main_fields[] = array(
				'var' => "note",
				'desc' => $msg['ontology_skos_note']
		);
		$main_fields[] = array(
				'var' => "schemes",
				'desc' => $msg['ontology_skos_conceptscheme']
		);
		$main_fields[] = array(
				'var' => "broaders_list",
				'desc' => $msg['onto_common_broader']
		);
		$main_fields[] = array(
				'var' => "narrowers_list",
				'desc' => $msg['onto_common_narrower']
		);
		// 		$authority = new authority(0, 0, AUT_TABLE_CONCEPT);
		// 		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}

	public function format_datas($antiloop = false)
	{
		$formatted_data = array(
				'id' => $this->get_id(),
				'uri' => $this->get_uri(),
				'permalink' => $this->get_id(),
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
	 * Retourne le chemin des concepts génériques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public static function get_paths($uri, $paths = array(), $path_beginning = '', $type = 'broader')
	{
		if ($uri) {
			if ($type == 'broader') {
				$query = "select ?entity where {
					<" . $uri . "> <http://www.w3.org/2004/02/skos/core#broader> ?entity
				}";
			} else {
				$query = "select ?entity where {
					<" . $uri . "> <http://www.w3.org/2004/02/skos/core#narrower> ?entity
				}";
			}

			skos_datastore::query($query);
			$results = skos_datastore::get_result();

			if (is_array($results) && count($results)) {
				foreach ($results as $result) {
					$entity_id = onto_common_uri::get_id($result->entity);
					if (strpos($path_beginning, $entity_id . '/') === false) {
						$key = array_search($path_beginning, $paths);
						if ($key !== false) {
							$paths[$key] = $path_beginning . $entity_id . '/';
						} else {
							$paths[] = $path_beginning . $entity_id . '/';
						}
						$paths = self::get_paths($result->entity, $paths, $path_beginning . $entity_id . '/', $type);
					}
				}
			}
		}
		return $paths;
	}

	public static function get_broad_paths($uri)
	{
		// 		$paths = self::get_paths($uri);
		$paths = array();
		$query = "select ?broad_path where {
					<" . $uri . "> pmb:broadPath ?broad_path
				}";
		skos_datastore::query($query);
		$results = skos_datastore::get_result();
		if (is_array($results) && count($results)) {
			foreach ($results as $result) {
				$paths[] = $result->broad_path;
			}
		}
		return $paths;
	}

	public static function get_narrow_paths($uri)
	{
		//$paths = self::get_paths($uri, array(), '', 'narrow');
		$paths = array();
		$query = "select ?narrow_path where {
					<" . $uri . "> pmb:narrowPath ?narrow_path
				}";
		skos_datastore::query($query);
		$results = skos_datastore::get_result();
		if (is_array($results) && count($results)) {
			foreach ($results as $result) {
				$paths[] = $result->narrow_path;
			}
		}
		return $paths;
	}

	public static function check_if_exists($data, $scheme_uri, $lang = "")
	{
		$lang = strtolower($lang);
		switch ($lang) {
			case "fr":
			case "fre":
			case "français":
			case "francais":
			case "french":
				$lang = "fr_FR";
				break;
			default:
				$lang = "fr_FR";
				break;
		}

		if ($data['label'] == "") {
			return 0;
		}
		$id = concept::get_concept_id_from_label(addslashes($data['label']), onto_common_uri::get_id($scheme_uri));
		return $id;
	}

	public static function get_informations_from_unimarc($fields, $link = false, $code_field = "250")
	{
		$data = array();
		$data['composition'] = array();
		if (!$link) {
			$data['label'] = $fields[$code_field][0]['a'][0];
			$data['composition']['a'] = $fields[$code_field][0]['a'];
			if (isset($fields[$code_field][0]['j']) && is_array($fields[$code_field][0]['j'])) {
				for ($i = 0; $i < count($fields[$code_field][0]['j']); $i++) {
					$data['label'] .= " -- " . $fields[$code_field][0]['j'][$i];
				}
				$data['composition']['j'] = $fields[$code_field][0]['j'];
			}
			if (isset($fields[$code_field][0]['x']) && is_array($fields[$code_field][0]['x'])) {
				for ($i = 0; $i < count($fields[$code_field][0]['x']); $i++) {
					$data['label'] .= " -- " . $fields[$code_field][0]['x'][$i];
				}
				$data['composition']['x'] = $fields[$code_field][0]['x'];
			}
			if (isset($fields[$code_field][0]['y']) && is_array($fields[$code_field][0]['y'])) {
				for ($i = 0; $i < count($fields[$code_field][0]['y']); $i++) {
					$data['label'] .= " -- " . $fields[$code_field][0]['y'][$i];
				}
				$data['composition']['y'] = $fields[$code_field][0]['y'];
			}
			if (isset($fields[$code_field][0]['z']) && is_array($fields[$code_field][0]['z'])) {
				for ($i = 0; $i < count($fields[$code_field][0]['z']); $i++) {
					$data['label'] .= " -- " . $fields[$code_field][0]['z'][$i];
				}
				$data['composition']['z'] = $fields[$code_field][0]['z'];
			}

			$data['comment'] = '';
			if (isset($fields['300']) && is_array($fields['300'])) {
				for ($i = 0; $i < count($fields['300']); $i++) {
					for ($j = 0; $j < count($fields['300'][$i]['a']); $j++) {
						if ($data['comment'] != "") {
							$data['comment'] .= "\n";
						}
						$data['comment'] .= $fields['300'][$i]['a'][$j];
					}
				}
			}
			$data['note'] = '';
			if (isset($fields['330']) && is_array($fields['330'])) {
				for ($i = 0; $i < count($fields['330']); $i++) {
					for ($j = 0; $j < count($fields['330'][$i]['a']); $j++) {
						if ($data['note'] != "") {
							$data['note'] .= "\n";
						}
						$data['note'] .= $fields['330'][$i]['a'][$j];
					}
				}
			}
			$data['narrowers'] = array();
			$data['broaders'] = array();
			$data['related'] = array();
			if (isset($fields['550']) && is_array($fields['550'])) {
				for ($i = 0; $i < count($fields['550']); $i++) {
					switch ($fields['550'][$i][5][0]) {
						case 'g':
							// Termes génériques
							$data['broaders'][] = skos_concept::get_informations_from_unimarc($fields['550'][$i], true);
							break;
						case 'h':
							// Termes spécifiques
							$data['narrowers'][] = skos_concept::get_informations_from_unimarc($fields['550'][$i], true);
							break;
						case 'z':
							// Termes associés
							$data['related'][] = skos_concept::get_informations_from_unimarc($fields['550'][$i], true);
					}
				}
			}
			// Libellés alternatifs (termes rejetés)
			$data['altlabel'] = array();
			if (isset($fields['450']) && is_array($fields['450'])) {
				for ($i = 0; $i < count($fields['450']); $i++) {
					$data['altlabel'][] = skos_concept::get_informations_from_unimarc($fields['450'][$i], true);
				}
			}
		} else {
			$data['label'] = $fields['a'][0];
			$data['composition']['a'] = $fields['a'];
			if (isset($fields['j']) && is_array($fields['j'])) {
				for ($i = 0; $i < count($fields['j']); $i++) {
					$data['label'] .= " -- " . $fields['j'][$i];
				}
				$data['composition']['j'] = $fields['j'];
			}
			if (isset($fields['x']) && is_array($fields['x'])) {
				for ($i = 0; $i < count($fields['x']); $i++) {
					$data['label'] .= " -- " . $fields['x'][$i];
				}
				$data['composition']['x'] = $fields['x'];
			}
			if (isset($fields['y']) && is_array($fields['y'])) {
				for ($i = 0; $i < count($fields['y']); $i++) {
					$data['label'] .= " -- " . $fields['y'][$i];
				}
				$data['composition']['y'] = $fields['y'];
			}
			if (isset($fields['z']) && is_array($fields['z'])) {
				for ($i = 0; $i < count($fields['z']); $i++) {
					$data['label'] .= " -- " . $fields['z'][$i];
				}
				$data['composition']['z'] = $fields['z'];
			}
			$data['authority_number'] = (isset($fields['3'][0]) ? $fields['3'][0] : '');
		}
		$data['type_authority'] = "concept";
		return $data;
	}

	protected function create_if_not_exists($label, $scheme_uri)
	{
		$concept_id = concept::get_concept_id_from_label($label, onto_common_uri::get_id($scheme_uri));
		if (!$concept_id) {
			$skos_concept = new skos_concept();
			// Schéma
			if ($scheme_uri) {
				$skos_concept->set_schemes(array(onto_common_uri::get_id($scheme_uri) => '')); // Pas besoin du label du schéma pour l'enregistrement
			}
			// Label
			$skos_concept->set_display_label($label);
			$concept_id = $skos_concept->save();
		}
		return $concept_id;
	}

	public function set_vedette_from_composition($composition, $scheme_uri = '')
	{
		if (!empty($composition) && (!empty($composition['j']) || !empty($composition['x']) || !empty($composition['y']) || !empty($composition['z']))) {
			$vedette_elements = $vedette_elements_for_check = array();
			$concept = $this->create_if_not_exists(trim($composition['a'][0]), $scheme_uri);
			$vedette_elements['subdivision_tete'][] = $concept;
			$vedette_elements_for_check[] = array(
					'type' => TYPE_CONCEPT,
					'id' => $concept
			);
			if (!empty($composition['j'])) {
				for ($i = 0; $i < count($composition['j']); $i++) {
					$concept = $this->create_if_not_exists(trim($composition['j'][$i]), $scheme_uri);
					$vedette_elements['subdivision_forme'][] = $concept;
					$vedette_elements_for_check[] = array(
							'type' => TYPE_CONCEPT,
							'id' => $concept
					);
				}
			}
			if (!empty($composition['x'])) {
				for ($i = 0; $i < count($composition['x']); $i++) {
					$concept = $this->create_if_not_exists(trim($composition['x'][$i]), $scheme_uri);
					$vedette_elements['subdivision_sujet'][] = $concept;
					$vedette_elements_for_check[] = array(
							'type' => TYPE_CONCEPT,
							'id' => $concept
					);
				}
			}
			if (!empty($composition['y'])) {
				for ($i = 0; $i < count($composition['y']); $i++) {
					$concept = $this->create_if_not_exists(trim($composition['y'][$i]), $scheme_uri);
					$vedette_elements['subdivision_geo'][] = $concept;
					$vedette_elements_for_check[] = array(
							'type' => TYPE_CONCEPT,
							'id' => $concept
					);
				}
			}
			if (!empty($composition['z'])) {
				for ($i = 0; $i < count($composition['z']); $i++) {
					$concept = $this->create_if_not_exists(trim($composition['z'][$i]), $scheme_uri);
					$vedette_elements['subdivision_chrono'][] = $concept;
					$vedette_elements_for_check[] = array(
							'type' => TYPE_CONCEPT,
							'id' => $concept
					);
				}
			}
			$vedette_build_with_elements = vedette_composee::get_vedettes_built_with_elements($vedette_elements_for_check, 'rameau', true);
			if (count($vedette_build_with_elements)) {
				// La vedette existe déjà, on s'arrête là
				return $this;
			}
			$vedette = new vedette_composee(0, 'rameau');
			$vedette_concept_field = $vedette->get_at_available_field_type('concept');

			foreach ($vedette_elements as $subdiv => $concepts) {
				foreach ($concepts as $pos => $concept_id) {
					$vedette_concept = new vedette_concepts($vedette_concept_field['num'], $concept_id);
					$vedette->add_element($vedette_concept, $subdiv, $pos);
				}
			}
			$vedette->update_label();
			$vedette->save();

			$query = "insert into vedette_link set num_object = " . $this->get_id() . ", num_vedette = " . $vedette->get_id() . ", type_object = " . TYPE_CONCEPT_PREFLABEL;
			pmb_mysql_query($query);
		}
		return $this;
	}

	static public function import($data, $scheme_uri, $num_parent = 0, $lang = "")
	{
		$lang = strtolower($lang);
		switch ($lang) {
			case "fr":
			case "fre":
			case "français":
			case "francais":
			case "french":
				$lang = "fr_FR";
				break;
			default:
				$lang = "fr_FR";
				break;
		}

		if ($data['label'] == "") {
			return 0;
		}
		$skos_concept = new skos_concept();
		// Schéma
		$skos_concept->set_schemes(array(onto_common_uri::get_id($scheme_uri) => '')); // Pas besoin du label du schéma pour l'enregistrement
		// Label
		$skos_concept->set_display_label($data['label']);

		// Broaders
		if (!empty($num_parent) || !empty($data['broaders'])) {
			$broaders = new skos_concepts_list();
			if (!empty($num_parent)) {
				$broaders->add_concept(new skos_concept($num_parent));
			}
			for ($i = 0; $i < count($data['broaders']); $i++) {
				static::add_concept_in_list_from_data($broaders, $data['broaders'][$i], $scheme_uri, $lang);
			}
			$skos_concept->set_broaders($broaders);
		}
		// Note
		if (isset($data['note'])) {
			$skos_concept->set_note($data['note']);
		}
		// Commentaire
		if (isset($data['comment'])) {
			$skos_concept->set_scope_note($data['comment']);
		}

		// Narrowers
		if (!empty($data['narrowers'])) {
			$narrowers = new skos_concepts_list();
			for ($i = 0; $i < count($data['narrowers']); $i++) {
				static::add_concept_in_list_from_data($narrowers, $data['narrowers'][$i], $scheme_uri, $lang);
			}
			$skos_concept->set_narrowers($narrowers);
		}

		// Related
		if (!empty($data['related'])) {
			$related = new skos_concepts_list();
			for ($i = 0; $i < count($data['related']); $i++) {
				static::add_concept_in_list_from_data($related, $data['related'][$i], $scheme_uri, $lang);
			}
			$skos_concept->set_related($related);
		}

		// altlabel
		if (!empty($data['altlabel'])) {
			$altlabel = array();
			for ($i = 0; $i < count($data['altlabel']); $i++) {
				$altlabel[] = $data['altlabel'][$i]['label'];
			}
			$skos_concept->set_altlabel($altlabel);
		}

		$skos_concept->save();
		$skos_concept->set_vedette_from_composition($data['composition'], $scheme_uri);

		return $skos_concept->get_id();
	}

	public function update($data, $scheme_uri, $num_parent, $lang)
	{
		$lang = strtolower($lang);
		switch ($lang) {
			case "fr":
			case "fre":
			case "français":
			case "francais":
			case "french":
				$lang = "fr_FR";
				break;
			default:
				$lang = "fr_FR";
				break;
		}

		// Schéma
		$this->set_schemes(array(onto_common_uri::get_id($scheme_uri) => '')); // Pas besoin du label du schéma pour l'enregistrement

		// Broader
		if (!empty($num_parent)) {
			$broaders = new skos_concepts_list();
			if (!empty($num_parent)) {
				$broaders->add_concept(new skos_concept($num_parent));
			}
			for ($i = 0; $i < count($data['broaders']); $i++) {
				static::add_concept_in_list_from_data($broaders, $data['broaders'][$i], $scheme_uri, $lang);
			}
			$this->set_broaders($broaders);
		}
		// Note
		if (isset($data['note'])) {
			$this->set_note($data['note']);
		}
		// Commentaire
		if (isset($data['comment'])) {
			$this->set_scope_note($data['comment']);
		}

		// Narrowers
		if (!empty($data['narrowers'])) {
			$narrowers = new skos_concepts_list();
			for ($i = 0; $i < count($data['narrowers']); $i++) {
				static::add_concept_in_list_from_data($narrowers, $data['narrowers'][$i], $scheme_uri, $lang);
			}
			$this->set_narrowers($narrowers);
		}

		// Related
		if (!empty($data['related'])) {
			$related = new skos_concepts_list();
			for ($i = 0; $i < count($data['related']); $i++) {
				static::add_concept_in_list_from_data($related, $data['related'][$i], $scheme_uri, $lang);
			}
			$this->set_related($related);
		}

		// altlabel
		if (!empty($data['altlabel'])) {
			$altlabel = array();
			for ($i = 0; $i < count($data['altlabel']); $i++) {
				$altlabel[] = $data['altlabel'][$i]['label'];
			}
			$this->set_altlabel($altlabel);
		}

		$this->save();
		$this->set_vedette_from_composition($data['composition'], $scheme_uri);
	}

	public function save()
	{
		global $base_path;

		$creation = true;
		if (!empty($this->uri)) {
			$creation = false;
		}

		$this->insert_in_store();

		//Ajout de la référence dans la table authorities
		$authority = new authority(0, $this->get_id(), AUT_TABLE_CONCEPT);
		$authority->set_num_statut(1);
		$authority->update();

		if ($creation) {
			audit::insert_creation(AUDIT_CONCEPT, $this->get_id());
		} else {
			audit::insert_modif(AUDIT_CONCEPT, $this->get_id());
		}

		$onto_index = onto_index::get_instance('skos');
		$onto_index->load_handler($base_path . "/classes/rdf/skos_pmb.rdf", "arc2", self::ONTO_STORE_CONFIG, "arc2", self::DATA_STORE_CONFIG, ONTOLOGY_NAMESPACE, 'http://www.w3.org/2004/02/skos/core#prefLabel');
		$onto_index->maj(0, $this->uri);

		return $this->get_id();
	}

	/**
	 * @return number id
	 */
	public function save_from_contribution()
	{
		global $base_path;

		$this->insert_in_store_from_contribution();

		//Ajout de la référence dans la table authorities
		$authority = new authority(0, $this->get_id(), AUT_TABLE_CONCEPT);
		$authority->set_num_statut(1);
		$authority->update();

		$onto_index = onto_index::get_instance('skos');
		$onto_index->load_handler($base_path . "/classes/rdf/skos_pmb.rdf", "arc2", self::ONTO_STORE_CONFIG, "arc2", self::DATA_STORE_CONFIG, ONTOLOGY_NAMESPACE, 'http://www.w3.org/2004/02/skos/core#prefLabel');
		$onto_index->maj(0, $this->uri);

		return $this->get_id();
	}

	public function insert_in_store_from_contribution()
	{
		$reverse_infos = array();
		$broaders_by_schemes = array();

		$update = true;
		if (!$this->uri) {
			$update = false;
			$this->uri = onto_common_uri::get_new_uri($this->get_concept_base_uri());
		}

		$this->infos_from_contribution['rdf:type'] = 'skos:Concept';

		if (!empty($this->broaders) && is_a($this->broaders, "skos_concepts_list")) {
			$broaders_by_schemes = $this->broaders->get_concepts_by_schemes();
		}

		$this->infos_from_contribution['skos:inScheme'] = array();
		$this->infos_from_contribution['pmb:showInTop'] = array();
		foreach ($this->schemes as $scheme_id => $scheme_label) {
			$uri = onto_common_uri::get_uri($scheme_id);
			if (empty($uri)) {
				continue;
			} else {
				$scheme_uri = '<' . $uri . '>';
				$this->infos_from_contribution['skos:inScheme'][] = $scheme_uri;
				if (!in_array($scheme_id, array_keys($broaders_by_schemes))) {
					$this->infos_from_contribution['pmb:showInTop'][] = $scheme_uri;
				}
			}
		}

		if (empty($this->infos_from_contribution['pmb:showInTop'])) {
			$this->infos_from_contribution['pmb:showInTop'] = 'owl:Nothing';
		}

		// Broaders
		$this->infos_from_contribution['skos:broader'] = array();
		$reverse_infos['skos:narrower'] = array();
		foreach ($this->get_broaders()->get_concepts() as $broader) {
			$uri = $broader->get_uri();
			if (!empty($uri)) {
				$this->infos_from_contribution['skos:broader'][] = '<' . $uri . '>';
				$reverse_infos['skos:narrower'][] = '<' . $uri . '>';
			}
		}

		// Narrowers
		$this->infos_from_contribution['skos:narrower'] = array();
		$reverse_infos['skos:broader'] = array();
		foreach ($this->get_narrowers()->get_concepts() as $narrower) {
			$uri = $narrower->get_uri();
			if (!empty($uri)) {
				$this->infos_from_contribution['skos:narrower'][] = '<' . $uri . '>';
				$reverse_infos['skos:broader'][] = '<' . $uri . '>';
			}
		}

		// Related
		$this->infos_from_contribution['skos:related'] = array();
		$reverse_infos['skos:related'] = array();
		foreach ($this->get_related()->get_concepts() as $related) {
			$uri = $related->get_uri();
			if (!empty($uri)) {
				$this->infos_from_contribution['skos:related'][] = '<' . $uri . '>';
				$reverse_infos['skos:related'][] = '<' . $uri . '>';
			}
		}

		// Related Match
		$this->infos_from_contribution['skos:relatedMatch'] = array();
		if (!is_array($reverse_infos['skos:related'])) {
			$reverse_infos['skos:related'] = array();
		}
		foreach ($this->get_related_match()->get_concepts() as $related) {
			$uri = $related->get_uri();
			if (!empty($uri)) {
				$uri = '<' . $uri . '>';
				$this->infos_from_contribution['skos:relatedMatch'][] = $uri;
	            if (!in_array($uri, $reverse_infos['skos:related'])) {
					$reverse_infos['skos:related'][] = $uri;
				}
			}
		}

		$query = 'insert into <pmb> {';
		$delete_query = 'delete {';
		foreach ($this->infos_from_contribution as $predicate => $objects) {
			if (!is_array($objects)) {
				$objects = array($objects);
			}

			foreach ($objects as $object) {
				if (!empty($object)) {
					$query .= '<' . $this->uri . '> ' . $predicate . ' ' . $object . ' .';
					$delete_query .= '<' . $this->uri . '> ' . $predicate . ' ?o .';
				}
			}
		}
		foreach ($reverse_infos as $predicate => $objects) {
			if (!is_array($objects)) {
				$objects = array($objects);
			}

			foreach ($objects as $object) {
				if (!empty($object)) {
					$check_query = 'select * where {
							' . $object . ' ' . $predicate . ' ?o .
							?s ' . $predicate . ' <' . $this->uri . '>
					}';
					skos_datastore::query($check_query);
					if (!skos_datastore::num_rows()) {
						$query .= $object . ' ' . $predicate . ' <' . $this->uri . '> .';
					}
				}
			}
		}
		$query .= '}';
		$delete_query .= '}';
		if ($update) {
			skos_datastore::query($delete_query);
		}
		skos_datastore::query($query);
	}

	public function insert_in_store()
	{
		$infos = array();
		$reverse_infos = array();
		$creation = false;
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_new_uri($this->get_concept_base_uri());
			$creation = true;
		}
		$infos['rdf:type'] = 'skos:Concept';

		$infos['skos:prefLabel'] = [];
		if (!empty($this->get_display_label_list())) {
			foreach ($this->get_display_label_list() as $lang => $label) {
				$infos['skos:prefLabel'][] = '"' . addslashes($label) . '"@' . addslashes($lang);
			}
		} else {
			$infos['skos:prefLabel'][] = '"' . addslashes($this->get_display_label()) . '"@no';
		}

		if ($this->get_note()) {
			$infos['skos:note'] = '"' . addslashes($this->get_note()) . '"';
		}
		if ($this->get_scope_note()) {
			$infos['skos:scopeNote'] = '"' . addslashes($this->get_scope_note()) . '"';
		}

		$broaders_by_schemes = array();
		if (is_object($this->broaders)) {
			$broaders_by_schemes = $this->broaders->get_concepts_by_schemes();
		}
		$infos['skos:inScheme'] = array();
		$infos['pmb:showInTop'] = array();
		foreach ($this->schemes as $scheme_id => $scheme_label) {
			$infos['skos:inScheme'][] = '<' . onto_common_uri::get_uri($scheme_id) . '>';
			if (!in_array($scheme_id, array_keys($broaders_by_schemes))) {
				$infos['pmb:showInTop'][] = '<' . onto_common_uri::get_uri($scheme_id) . '>';
			}
		}
		if (empty($infos['pmb:showInTop'])) {
			$infos['pmb:showInTop'] = 'owl:Nothing';
		}
		// Broaders
		$infos['skos:broader'] = array();
		$reverse_infos['skos:narrower'] = array();
		foreach ($broaders_by_schemes as $scheme) {
			foreach ($scheme['elements'] as $broader) {
				$infos['skos:broader'][] = '<' . $broader->get_uri() . '>';
				$reverse_infos['skos:narrower'][] = '<' . $broader->get_uri() . '>';
			}
		}

		// Narrowers
		$infos['skos:narrower'] = array();
		$reverse_infos['skos:broader'] = array();
		foreach ($this->get_narrowers()->get_concepts() as $narrower) {
			$infos['skos:narrower'][] = '<' . $narrower->get_uri() . '>';
			$reverse_infos['skos:broader'][] = '<' . $narrower->get_uri() . '>';
		}

		// Related
		$infos['skos:related'] = array();
		$reverse_infos['skos:related'] = array();
		foreach ($this->get_related()->get_concepts() as $related) {
			$infos['skos:related'][] = '<' . $related->get_uri() . '>';
			$reverse_infos['skos:related'][] = '<' . $related->get_uri() . '>';
		}

		// Altlabel
		$infos['skos:altlabel'] = array();
		foreach ($this->get_altlabel() as $altlabel) {
			$infos['skos:altlabel'][] = '"' . addslashes($altlabel) . '"';
		}

		$query = 'insert into <pmb> {';
		$delete_query = 'delete {';
		foreach ($infos as $predicate => $objects) {
			if (!is_array($objects)) {
				$objects = array($objects);
			}
			foreach ($objects as $object) {
				if ($object) {
					$query .= '
					<' . $this->uri . '> ' . $predicate . ' ' . $object . ' .';
					$delete_query .= '
					<' . $this->uri . '> ' . $predicate . ' ?o .';
				}
			}
		}
		foreach ($reverse_infos as $predicate => $subjects) {
			if (!is_array($subjects)) {
				$subjects = array($subjects);
			}
			foreach ($subjects as $subject) {
				if ($subject) {
					$check_query = 'select * where {
							' . $subject . ' ' . $predicate . ' ?o .
							?s ' . $predicate . ' <' . $this->uri . '>
					}';
					skos_datastore::query($check_query);
					if (!skos_datastore::num_rows()) {
						$query .= '
						' . $subject . ' ' . $predicate . ' <' . $this->uri . '> .';
					}
				}
			}
		}
		$query .= '
				}';
		$delete_query .= '
				}';
		if (!$creation) {
			skos_datastore::query($delete_query);
		}

		skos_datastore::query($query);
	}

	/**
	 * Suppression des informations d'import d'autorités
	 * @param int $idaut
	 */
	static public function delete_autority_sources($idaut = 0)
	{
		$tabl_id = array();
		if (!$idaut) {
			$requete = "SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN onto_uri ON num_authority=uri_id  WHERE authority_type = 'concept' AND uri_id IS NULL";
			$res = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($res)) {
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[] = $ligne->num_authority;
				}
			}
		} else {
			$tabl_id[] = $idaut;
		}
		foreach ($tabl_id as $value) {
			// suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = " . $value . " and authority_type = 'concept'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = " . $ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = " . $value . " and authority_type = 'concept'";
			pmb_mysql_query($query);
		}
	}

	public function set_schemes($schemes)
	{
		$this->schemes = $schemes;
		return $this;
	}

	public function set_broaders($broaders)
	{
		$this->broaders = $broaders;
		return $this;
	}

	public function set_narrowers($narrowers)
	{
		$this->narrowers = $narrowers;
		return $this;
	}

	public function set_related($related)
	{
		$this->related = $related;
		return $this;
	}

	public function set_note($note)
	{
		$this->note = $note;
		return $this;
	}

	public function set_display_label($display_label)
	{
		$this->display_label = $display_label;
		return $this;
	}

	public function set_scope_note($scope_note)
	{
		$this->scope_note = $scope_note;
		return $this;
	}

	public function get_concept_base_uri()
	{
		global $opac_url_base;
		return $opac_url_base . 'concept#';
	}

	public function set_altlabel($altlabel)
	{
		$this->altlabel = $altlabel;
		return $this;
	}

	public function get_altlabel()
	{
		if (isset($this->altlabel)) {
			return $this->altlabel;
		}
		$this->altlabel = array();

		$query = "select * where {
			<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#altLabel> ?altlabel
		}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->altlabel[] = $result->altlabel;
			}
		}
		return $this->altlabel;
	}

	/**
	 * Ajoute un concept à une liste à partir de ses données, le crée si necessaire
	 * @param skos_concepts_list $skos_concepts_list Liste à remplir
	 * @param array $data Tableau des données du concept
	 * @param string $scheme_uri URI du schéma
	 * @param string $lang Langue
	 */
	static public function add_concept_in_list_from_data(&$skos_concepts_list, $data, $scheme_uri, $lang)
	{
		$concept_id = static::check_if_exists($data, $scheme_uri, $lang);
		if ($concept_id) {
			$concept = new skos_concept($concept_id);
			$concept->update($data, $scheme_uri, 0, $lang);
		} else {
			$concept_id = static::import($data, $scheme_uri, 0, $lang);
			$concept = new skos_concept($concept_id);
		}
		$skos_concepts_list->add_concept($concept);
	}

	/**
	 * Retourne la note
	 * @return string
	 */
	public function get_definition()
	{
		global $lang;

		if (!$this->definition) {
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#definition> ?definition
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if (isset($result->definition_lang) && $result->definition_lang == substr($lang, 0, 2)) {
						$this->definition = $result->definition;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->definition) {
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
	public function get_history_note()
	{
		global $lang;

		if (empty($this->history_note)) {
			$this->history_note = '';
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#historyNote> ?historyNote
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if ($result->historyNote_lang == substr($lang, 0, 2)) {
						$this->history_note = $result->historyNote;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->history_note) {
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
	public function get_example()
	{
		global $lang;

		if (empty($this->example)) {
			$this->example = '';
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#example> ?example
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if (isset($result->example_lang) && $result->example_lang == substr($lang, 0, 2)) {
						$this->example = $result->example;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->example) {
					$this->example = $result->example;
				}
			}
		}
		return $this->example;
	}

	/**
	 * Suppression du concept
	 */
	public function delete($force_delete = false)
	{
		// On déclare un flag pour savoir si on peut continuer la suppression
		$deletion_allowed = true;

		// On regarde si le concdept est utilisé pour indexer d'autres éléments (tbl index_concept)
		$query = "select num_object from index_concept where num_concept = " . $this->id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$deletion_allowed = false;
		}

		// On regarde si l'autorité est utilisée dans des vedettes composées
		$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_CONCEPT);
		if (count($attached_vedettes)) {
			// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
			$deletion_allowed = false;
		}


		if (($usage = aut_pperso::delete_pperso(AUT_TABLE_CONCEPT, $this->uri, $force_delete))) {
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			$deletion_allowed = false;
		}

		// On stockera dans un tableau tous les triplets desquels l'item est l'objet
		$is_object_of = $this->is_object_of();
		if (count($is_object_of)) {
			$deletion_allowed = false;
		}

		if ($force_delete || $deletion_allowed) {

			/**
			 * suppression relationnelle
			 */

			audit::delete_audit(AUDIT_CONCEPT, $this->id);
			// On peut continuer la suppression
			$id_vedette = vedette_link::get_vedette_id_from_object($this->id, TYPE_CONCEPT_PREFLABEL);
			$vedette = new vedette_composee($id_vedette);
			$vedette->delete();
			vedette_composee::delete_element_and_update_vedettes_built_with_element($this->id, TYPE_CONCEPT);

			//suppression des autorités liées... & des statuts des concepts
			// liens entre autorités
			$aut_link = new aut_link(AUT_TABLE_CONCEPT, $this->id);
			$aut_link->delete();

			$map = new map_edition_controler(AUT_TABLE_CONCEPT, $this->id);
			$map->delete();

			$aut_pperso = new aut_pperso("skos", $this->id);
			$aut_pperso->delete();

			skos_concept::delete_autority_sources($this->id);

			$query = "DELETE FROM index_concept where num_concept = $this->id";
			pmb_mysql_query($query);

			$authority = new authority(0, $this->id, AUT_TABLE_CONCEPT);
			$authority->delete();

			$usage = aut_pperso::delete_pperso(AUT_TABLE_CONCEPT, $this->id, 1);

			/**
			 * suppression sémantique
			 */
			if ($force_delete || !count($is_object_of)) {
				$query = "delete {
				    <$this->uri> ?prop ?obj
                }";
				skos_datastore::query($query);
				if (skos_datastore::get_errors() === false) {
					$query = "delete {
					   ?subject ?predicate <$this->uri>
				    }";
					skos_datastore::query($query);
					if (skos_datastore::get_errors() === false) {
						// On met à jour l'index
						$onto_index = onto_index::get_instance("skos");
						$onto_index->set_handler(static::get_handler());
						$onto_index->maj($this->id);

						if (count($is_object_of)) {
							foreach ($is_object_of as $object) {
								$onto_index->maj(0, $object->get_subject());
							}
						}
						$query = "delete from onto_uri where uri = '$this->uri'";
						pmb_mysql_query($query);
					}
				}
			}
		}
		return $force_delete ? $force_delete : $deletion_allowed;
	}

	private function is_object_of()
	{
		$is_object_of = [];
		$query = "select * where {
    			?subject ?predicate <$this->uri>
    		}";
		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $assertion) {
				$is_object_of[] = new onto_assertion($assertion->subject, $assertion->predicate, $this->uri);
			}
		}
		return $is_object_of;
	}

	public function __get($name)
	{
		$parameters = array();
		switch (true) {
			// Si la propriété existe
			case !empty($this->{$name}):
				return $this->{$name};
			// si la méthode existe...
			case method_exists($this, $name):
				return $this->{$name}();
			// Si la méthode get existe
			case method_exists($this, "get_" . $name):
				return call_user_func_array(array($this, "get_" . $name), $parameters);
			// Si la méthode set existe
			case method_exists($this, "is_" . $name):
				return call_user_func_array(array($this, "is_" . $name), $parameters);
			default:
				return null;
		}
	}

	/**
	 * Fonction interne pour l'affiche dans les éditions de listes
	 */
	public function get_edit_narrowers()
	{
		$narrowers_list = [];
		foreach ($this->get_narrowers()->get_concepts() as $narrower) {
			$narrowers_list[] = $narrower->display_label;
		}
		return $narrowers_list;
	}

	/**
	 * Fonction interne pour l'affiche dans les éditions de listes
	 */
	public function get_edit_broaders()
	{
		$broaders_list = [];
		foreach ($this->get_broaders()->get_concepts() as $broaders) {
			$broaders_list[] = $broaders->display_label;
		}
		return $broaders_list;
	}

	public static function get_properties()
	{
		$props = skos_onto::get_properties_labels("http://www.w3.org/2004/02/skos/core#Concept");
	    $properties = [
	        'altlabel',
	        'note',
	        'id',
	        'uri',
	        'display_label',
	        'note',
	        'schemes',
	        'vedette',
	        'narrowers',
	        'broaders',
	        'composed_concepts',
	        'indexed_notices',
	        'indexed_authorities',
			'scope_note',
			//'related',
			//'related_match',
	        'altlabel',
	        'definition',
	        'handler',
	        'history_note',
	        'example',
	        'map',
	        'map_info'
	    ];
		$return = array();
		$return['display_label'] = $props['http://www.w3.org/2004/02/skos/core#prefLabel']['label'];
		$return['edit_narrowers'] = $props['http://www.w3.org/2004/02/skos/core#narrower']['label'];
		$return['edit_broaders'] = $props['http://www.w3.org/2004/02/skos/core#broader']['label'];
		foreach ($props as $value) {
			if (in_array($value['pmb_name'], $properties)) {
				$return[$value['pmb_name']] = $value['label'];
			}
		}

		return $return;
	}

	private static function get_handler()
	{
		if (!isset(static::$handler)) {
			static::$handler = new onto_handler('', skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel');
			static::$handler->get_ontology();
		}
		return static::$handler;
	}

	public function __set($name, $value)
	{
		if (method_exists($this, "set_" . $name)) {
			return $this->{"set_" . $name}($value);
		}

		$this->{$name} = $value;
		return $this;
	}

	public function set_infos($predicate, $object)
	{
		if (isset($this->infos_from_contribution[$predicate])) {
			$this->infos_from_contribution[$predicate] = [$this->infos_from_contribution[$predicate]];
		}

		if (is_array($this->infos_from_contribution[$predicate])) {
			$this->infos_from_contribution[$predicate][] = $object;
		} else {
			$this->infos_from_contribution[$predicate] = $object;
		}
	}

	public static function has_children(string $uri)
	{
		$query = "select DISTINCT ?narrower where {
			<" . $uri . "> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower .
			?narrower skos:prefLabel ?narrower_label .
		} LIMIT 1 ";

		skos_datastore::query($query);
		return skos_datastore::num_rows() > 0;
	}
}
