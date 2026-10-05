<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_concept.class.php,v 1.54.2.1.2.1 2025/01/29 10:03:21 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/onto/common/onto_common_uri.class.php");
require_once ($class_path . "/onto/onto_store_arc2.class.php");
require_once ($class_path . "/skos/skos_datastore.class.php");
require_once ($class_path . "/notice.class.php");
require_once ($class_path . "/author.class.php");
require_once ($class_path . "/category.class.php");
require_once ($class_path . "/publisher.class.php");
require_once ($class_path . "/collection.class.php");
require_once ($class_path . "/subcollection.class.php");
require_once ($class_path . "/serie.class.php");
require_once ($class_path . "/titre_uniforme.class.php");
require_once ($class_path . "/indexint.class.php");
require_once ($class_path . "/explnum.class.php");
require_once ($class_path . "/authperso_data.class.php");
require_once ($class_path . "/skos/skos_view_concepts.class.php");
require_once ($class_path . "/skos/skos_view_concept.class.php");
require_once ($class_path . "/authority.class.php");

if (!defined('TYPE_NOTICE')) {
	define('TYPE_NOTICE', 1);
}
if (!defined('TYPE_AUTHOR')) {
	define('TYPE_AUTHOR', 2);
}
if (!defined('TYPE_CATEGORY')) {
	define('TYPE_CATEGORY', 3);
}
if (!defined('TYPE_PUBLISHER')) {
	define('TYPE_PUBLISHER', 4);
}
if (!defined('TYPE_COLLECTION')) {
	define('TYPE_COLLECTION', 5);
}
if (!defined('TYPE_SUBCOLLECTION')) {
	define('TYPE_SUBCOLLECTION', 6);
}
if (!defined('TYPE_SERIE')) {
	define('TYPE_SERIE', 7);
}
if (!defined('TYPE_TITRE_UNIFORME')) {
	define('TYPE_TITRE_UNIFORME', 8);
}
if (!defined('TYPE_INDEXINT')) {
	define('TYPE_INDEXINT', 9);
}
if (!defined('TYPE_EXPL')) {
	define('TYPE_EXPL', 10);
}
if (!defined('TYPE_EXPLNUM')) {
	define('TYPE_EXPLNUM', 11);
}
if (!defined('TYPE_AUTHPERSO')) {
	define('TYPE_AUTHPERSO', 12);
}
if (!defined('TYPE_CMS_SECTION')) {
	define('TYPE_CMS_SECTION', 13);
}
if (!defined('TYPE_CMS_ARTICLE')) {
	define('TYPE_CMS_ARTICLE', 14);
}

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
	 * termes équivalents
	 * @var skos_concepts_list
	 */
	private $exactmatch;

	/**
	 *  template des termes équivalents du concept
	 * @var string
	 */
	private $exactmatch_list;

	/**
	 * termes approchants
	 * @var skos_concepts_list
	 */
	private $closematch;

	/**
	 *  template des termes approchants du concept
	 * @var string
	 */
	private $closematch_list;

	/**
	 * termes approchants
	 * @var skos_concepts_list
	 */
	private $mappingrelation;

	/**
	 *  template des termes approchants du concept
	 * @var string
	 */
	private $mappingrelation_list;

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
	 * Tableau des synonymes
	 * @var array
	 */
	private $altlabels = array();

	/**
	 * Note modifiée
	 * @var string
	 */
	private $changenote;

	/**
	 * Note éditoriale
	 * @var string
	 */
	private $editorialnote;

	/**
	 * Note Note d'emploi
	 * @var string
	 */
	private $scopenote;

	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 */
	public function __construct($id = 0, $uri = "")
	{
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
		if (!$this->narrowers) {
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
		}
		return $this->narrowers;
	}

	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_narrowers_list()
	{
		if (!isset($this->narrowers_list)) {
			$this->narrowers_list = skos_view_concepts::get_narrowers_list($this->get_narrowers());
		}
		return $this->narrowers_list;
	}

	/**
	 * Retourne les parents du concept
	 * @return skos_concepts_list Liste des parents du concept
	 */
	public function get_broaders()
	{
		if (!$this->broaders) {
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
		}
		return $this->broaders;
	}

	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_broaders_list()
	{
		if (!isset($this->broaders_list)) {
			$this->broaders_list = skos_view_concepts::get_broaders_list($this->get_broaders());
		}
		return $this->broaders_list;
	}

	/**
	 * Retourne le rendu HTML des relations associatives
	 */
	public function get_related_list()
	{
		if (!isset($this->related_list)) {
			$this->related_list = skos_view_concepts::get_related_list($this->get_related());
		}
		return $this->related_list;
	}

	/**
	 * Retourne le rendu HTML des termes associés
	 */
	public function get_related_match_list()
	{
		if (!isset($this->related_match_list)) {
			$this->related_match_list = skos_view_concepts::get_related_match_list($this->get_related_match());
		}
		return $this->related_match_list;
	}

	/**
	 * Retourne les termes équivalents du concept
	 * @return skos_concepts_list Liste des équivalents du concept
	 */
	public function get_exactmatch()
	{
		if (isset($this->exactmatch)) {
			return $this->exactmatch;
		}
		$this->exactmatch = new skos_concepts_list();

		$query = "select ?exactmatch where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#exactMatch> ?exactmatch
			}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->exactmatch->add_concept(new skos_concept(0, $result->exactmatch));
			}
		}
		return $this->exactmatch;
	}

	/**
	 * Retourne le rendu HTML des termes équivalents
	 */
	public function get_exactmatch_list()
	{
		if (!isset($this->exactmatch_list)) {
			$this->exactmatch_list = skos_view_concepts::get_exactmatch_list($this->get_exactmatch());
		}
		return $this->exactmatch_list;
	}

	/**
	 * Retourne les termes approchants du concept
	 * @return skos_concepts_list Liste des approchants du concept
	 */
	public function get_closematch()
	{
		if (isset($this->closematch)) {
			return $this->closematch;
		}
		$this->closematch = new skos_concepts_list();

		$query = "select ?closematch where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#closeMatch> ?closematch
			}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->closematch->add_concept(new skos_concept(0, $result->closematch));
			}
		}
		return $this->closematch;
	}

	/**
	 * Retourne le rendu HTML des termes approchants
	 */
	public function get_closematch_list()
	{
		if (!isset($this->closematch_list)) {
			$this->closematch_list = skos_view_concepts::get_closematch_list($this->get_closematch());
		}
		return $this->closematch_list;
	}

	/**
	 * Retourne les relations d'équivalence du concept
	 * @return skos_concepts_list Liste des équivalences du concept
	 */
	public function get_mappingrelation()
	{
		if (isset($this->mappingrelation)) {
			return $this->mappingrelation;
		}
		$this->mappingrelation = new skos_concepts_list();

		$query = "select ?mappingrelation where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#mappingRelation> ?mappingrelation
			}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				$this->mappingrelation->add_concept(new skos_concept(0, $result->mappingrelation));
			}
		}
		return $this->mappingrelation;
	}

	/**
	 * Retourne le rendu HTML des relations d'équivalences
	 */
	public function get_mappingrelation_list()
	{
		if (!isset($this->mappingrelation_list)) {
			$this->mappingrelation_list = skos_view_concepts::get_mappingrelation_list($this->get_mappingrelation());
		}
		return $this->closematch_list;
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
			$filter = new filter_results($this->indexed_notices);
			$this->indexed_notices = explode(",", $filter->get_results());
		}
		return $this->indexed_notices;
	}

	/**
	 * Charge les données de carthographie
	 */
	private function fetch_map()
	{
		global $opac_map_activate;

		if ($opac_map_activate) {
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
							$this->indexed_authorities['publisher'][] = new publisher($row->num_object);
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
							$this->indexed_authorities['titre_uniforme'][] = new titre_uniforme($row->num_object);
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
							$this->indexed_authorities['authperso'][] = new authperso_data($row->num_object);
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

			$this->composed_concepts->set_composed_concepts_built_with_element($this->id, "concept");
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
		$query = "select * where {
				<" . $this->uri . "> rdf:type skos:Concept .
				<" . $this->uri . "> skos:prefLabel ?label .
				optional {
					<" . $this->uri . "> skos:altLabel ?altlabel
				} .
				optional {
					<" . $this->uri . "> skos:note ?note
				} .
				optional {
					<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#Note> ?notebnf
				} .
				optional {
					<" . $this->uri . "> skos:related ?related .
					optional {
						?related skos:prefLabel ?relatedlabel
					}
				} .
				optional {
					<" . $this->uri . "> skos:related ?related .
					optional {
						?related skos:prefLabel ?relatedlabel
					}
				} .
				optional {
					<" . $this->uri . "> owl:sameAs ?sameas .
					optional {
						?sameas skos:prefLabel ?sameaslabel
					}
				} .
				optional {
					<" . $this->uri . "> rdfs:seeAlso ?seealso .
					optional {
						?seealso skos:prefLabel ?seealsolabel
					}
				} .
				optional {
					<" . $this->uri . "> skos:exactMatch ?exactmatch .
					optional {
						?exactmatch skos:prefLabel ?exactmatchlabel
					}
				} .
				optional {
					<" . $this->uri . "> skos:closeMatch ?closematch .
					optional {
						?closematch skos:prefLabel ?closematchlabel
					}
				}
			}";

		skos_datastore::query($query);
		if (skos_datastore::num_rows()) {
			$results = skos_datastore::get_result();
			foreach ($results as $result) {
				foreach ($result as $property => $value) {
					switch ($property) {
						//cas des literaux
						case "altlabel":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#altLabel'])) {
								$details['http://www.w3.org/2004/02/skos/core#altLabel'] = array();
							}
							if (isset($result->{$property . "_lang"}) == substr($lang, 0, 2)) {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#altLabel'])) {
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
								break;
							} else {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#altLabel'])) {
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;
						case "hiddenlabel":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])) {
								$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'] = array();
							}
							if (isset($result->hiddenlabel_lang) == substr($lang, 0, 2)) {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#hiddenLabel'])) {
									$details['http://www.w3.org/2004/02/skos/core#hiddenLabel'][] = $value;
								}
								break;
							} else {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#altLabel'])) {
									$details['http://www.w3.org/2004/02/skos/core#altLabel'][] = $value;
								}
							}
							break;
						case "related":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#related'])) {
								$details['http://www.w3.org/2004/02/skos/core#related'] = array();
							}
							if ($result->related_type == "uri") {
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array('uri' => $value);
								if (isset($result->relatedlabel)) {
									$detail['label'] = $result->relatedlabel;
								}
								if ($id) {
									$detail['id'] = $id;
								}
								if (!in_array($detail, $details['http://www.w3.org/2004/02/skos/core#related'])) {
									$details['http://www.w3.org/2004/02/skos/core#related'][] = $detail;
								}
							}
							break;
						case "sameas":
							if (!isset($details['http://www.w3.org/2002/07/owl#sameAs'])) {
								$details['http://www.w3.org/2002/07/owl#sameAs'] = array();
							}
							if ($result->sameas_type == "uri") {
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array('uri' => $value);
								if (isset($result->sameaslabel)) {
									$detail['label'] = $result->sameaslabel;
								}
								if ($id) {
									$detail['id'] = $id;
								}
								if (!in_array($detail, $details['http://www.w3.org/2002/07/owl#sameAs'])) {
									$details['http://www.w3.org/2002/07/owl#sameAs'][] = $detail;
								}
							}
							break;
						case "note":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#note'])) {
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if (isset($result->note_lang) == substr($lang, 0, 2)) {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#note'])) {
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							} else {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#note'])) {
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "notebnf":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#note'])) {
								$details['http://www.w3.org/2004/02/skos/core#note'] = array();
							}
							if (isset($result->notebnf_lang) == substr($lang, 0, 2)) {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#note'])) {
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
								break;
							} else {
								if (!in_array($value, $details['http://www.w3.org/2004/02/skos/core#note'])) {
									$details['http://www.w3.org/2004/02/skos/core#note'][] = $value;
								}
							}
							break;
						case "seealso":
							if (!isset($details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])) {
								$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'] = array();
							}
							if ($result->seealso_type == "uri") {
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array('uri' => $value);
								if (isset($result->seealsolabel)) {
									$detail['label'] = $result->seealsolabel;
								}
								if ($id) {
									$detail['id'] = $id;
								}
								if (!in_array($detail, $details['http://www.w3.org/2000/01/rdf-schema#seeAlso'])) {
									$details['http://www.w3.org/2000/01/rdf-schema#seeAlso'][] = $detail;
								}
							}
							break;
						case "exactmatch":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#exactMatch'])) {
								$details['http://www.w3.org/2004/02/skos/core#exactMatch'] = array();
							}
							if ($result->exactmatch_type == "uri") {
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array('uri' => $value);
								if (isset($result->exactmatchlabel)) {
									$detail['label'] = $result->exactmatchlabel;
								}
								if ($id) {
									$detail['id'] = $id;
								}
								if (!in_array($detail, $details['http://www.w3.org/2004/02/skos/core#exactMatch'])) {
									$details['http://www.w3.org/2004/02/skos/core#exactMatch'][] = $detail;
								}
							}
							break;
						case "closematch":
							if (!isset($details['http://www.w3.org/2004/02/skos/core#closeMatch'])) {
								$details['http://www.w3.org/2004/02/skos/core#closeMatch'] = array();
							}
							if ($result->closematch_type == "uri") {
								//on cherche si l'URI est connu dans notre système
								$id = onto_common_uri::get_id($value);
								$detail = array('uri' => $value);
								if (isset($result->closematchlabel)) {
									$detail['label'] = $result->closematchlabel;
								}
								if ($id) {
									$detail['id'] = $id;
								}
								if (!in_array($detail, $details['http://www.w3.org/2004/02/skos/core#closeMatch'])) {
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

	public function get_details_list()
	{
		return skos_view_concept::get_detail_concept($this);
	}

	public function get_db_id()
	{
		return $this->get_id();
	}

	public function get_isbd()
	{
		return $this->get_display_label();
	}

	public function get_header()
	{
		return $this->get_display_label();
	}

	public function get_permalink()
	{
		global $liens_opac;
		return str_replace('!!id!!', $this->get_id(), $liens_opac['lien_rech_concept']);
	}

	public function get_comment()
	{
		return '';
	}

	public function get_authoritieslist()
	{
		return skos_view_concept::get_authorities_indexed_with_concept($this);
	}

	public function format_datas($antiloop = false)
	{
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
	public function get_p_perso()
	{
		if (!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}

	public function get_authority()
	{
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_CONCEPT]);
	}

	/**
	 * Retourne la note
	 * @return string
	 */
	public function get_note()
	{
		global $lang;

		if (!$this->note) {
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#note> ?note
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if (isset($result->note_lang) && $result->note_lang == substr($lang, 0, 2)) {
						$this->note = $result->note;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->note) {
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
	 * retourne les relations associatives
	 * @return skos_concepts_list
	 */
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
	 * retourne les termes associés
	 * @return skos_concepts_list
	 */
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

	/**
	 * Retourne les altlabels du concept
	 * @return skos_concepts_list Liste des enfants du concept
	 */
	public function get_altlabels()
	{
		if (!$this->altlabels) {
			$query = "select ?altlabel where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#altLabel> ?altlabel .
			}";

			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					if (!empty($result->altlabel)) {
						$label = $result->altlabel;
						if (!empty($result->altlabel_lang)) {
							$label .= " (" . $result->altlabel_lang . ")";
						}
						$this->altlabels[] = $label;
					}
				}
			}
		}
		return $this->altlabels;
	}

	/**
	 * Retourne la note modifiée
	 * @return string
	 */
	public function get_changenote()
	{
		if (!isset($this->changenote)) {
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#changeNote> ?changenote
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					$this->changenote = $result->changenote;
					if (isset($result->changenote_lang)) {
						$this->changenote .= " (" . $result->changenote_lang . ")";
					}
				}
			}
		}
		return $this->changenote;
	}

	/**
	 * Retourne la note éditoriale
	 * @return string
	 */
	public function get_editorialnote()
	{
		if (!isset($this->editorialnote)) {
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#editorialNote> ?editorialnote
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					$this->editorialnote = $result->editorialnote;
					if (isset($result->editorialnote_lang)) {
						$this->editorialnote .= " (" . $result->editorialnote_lang . ")";
					}
				}
			}
		}
		return $this->editorialnote;
	}

	/**
	 * Retourne la note d'emploi
	 * @return string
	 */
	public function get_scopenote()
	{
		if (!isset($this->scopenote)) {
			$query = "select * where {
				<" . $this->uri . "> <http://www.w3.org/2004/02/skos/core#scopeNote> ?scopenote
			}";
			skos_datastore::query($query);
			if (skos_datastore::num_rows()) {
				$results = skos_datastore::get_result();
				foreach ($results as $result) {
					$this->scopenote = $result->scopenote;
					if (isset($result->scopenote_lang)) {
						$this->scopenote .= " (" . $result->scopenote_lang . ")";
					}
				}
			}
		}
		return $this->scopenote;
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
