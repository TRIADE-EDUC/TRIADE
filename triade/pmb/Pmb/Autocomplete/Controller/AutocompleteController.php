<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AutocompleteController.php,v 1.6 2024/01/04 08:58:14 jparis Exp $
namespace Pmb\Autocomplete\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Helper\UrlEntities;

class AutocompleteController extends Controller
{

	/**
	 * Contient la liste des résultats
	 */
	private $autocomplete_results = [];

	private $entities_list = null;

	private $perts = array();

	public const AUTOCOMPLETE_LIMIT = 3;

	public const AUTOCOMPLETE_LIST_LIMIT = 10;

	private const CMS_TYPES = [
		TYPE_CMS_ARTICLE,
		TYPE_CMS_SECTION
	];

	/**
	 * Retourne la liste des entités de PMB
	 */
	public function getEntitiesList()
	{
	    session_write_close();
	    
		if (isset($this->entities_list)) {
			print \encoding_normalize::json_encode($this->entities_list);
			return;
		}

		global $opac_modules_search_author, $opac_modules_search_category, $opac_modules_search_collection;
		global $opac_modules_search_concept, $opac_modules_search_indexint, $opac_modules_search_publisher;
		global $opac_modules_search_subcollection, $opac_modules_search_titre_uniforme;

		$entities = \entities::get_entities_labels();
		$this->entities_list = array();

		foreach ($entities as $entity_type => $entity_label) {
			if (empty($entity_label)) {
				continue;
			}

			switch ($entity_type) {
				case TYPE_NOTICE:
					$checked = "1";
					break;
				case TYPE_AUTHOR:
					if (!$opac_modules_search_author) {
						continue 2;
					}
					$checked = $opac_modules_search_author == "2" ? "1" : "";
					break;
				case TYPE_CATEGORY:
					if (!$opac_modules_search_category) {
						continue 2;
					}
					$checked = $opac_modules_search_category == "2" ? "1" : "";
					break;
				case TYPE_COLLECTION:
					if (!$opac_modules_search_collection) {
						continue 2;
					}
					$checked = $opac_modules_search_collection == "2" ? "1" : "";
					break;
				case TYPE_CONCEPT:
					if (!$opac_modules_search_concept) {
						continue 2;
					}
					$checked = $opac_modules_search_concept == "2" ? "1" : "";
					break;
				case TYPE_INDEXINT:
					if (!$opac_modules_search_indexint) {
						continue 2;
					}
					$checked = $opac_modules_search_indexint == "2" ? "1" : "";
					break;
				case TYPE_PUBLISHER:
					if (!$opac_modules_search_publisher) {
						continue 2;
					}
					$checked = $opac_modules_search_publisher == "2" ? "1" : "";
					break;
				case TYPE_SUBCOLLECTION:
					if (!$opac_modules_search_subcollection) {
						continue 2;
					}
					$checked = $opac_modules_search_subcollection == "2" ? "1" : "";
					break;
				case TYPE_TITRE_UNIFORME:
					if (!$opac_modules_search_titre_uniforme) {
						continue 2;
					}
					$checked = $opac_modules_search_titre_uniforme == "2" ? "1" : "";
					break;
				default:
					continue 2;
			}
			$this->entities_list[$entity_type] = [
				"checked" => $checked,
				"value" => $entity_label
			];
		}
		print \encoding_normalize::json_encode($this->entities_list);
	}

	/**
	 * Autocompletion dans les univers
	 *
	 * @param int $id
	 */
	public function getUniverseAutocomplete(int $id)
	{
		global $user_query;
		$user_query = $this->data->user_query;
		new \search_universes_controller($id);
		$search_universe = new \search_universe($id);
		$segments = $search_universe->get_segments();

		foreach ($segments as $segment) {
			global $es;
			if (is_object($es)) {
				$es->destroy_global_env();
			}
			$temp_table = $segment->get_search_result_table();
			$query = "SELECT * FROM $temp_table ORDER BY pert DESC LIMIT " . self::AUTOCOMPLETE_LIMIT;

			$result = pmb_mysql_fetch_all(pmb_mysql_query($query));
			$result_array = array();

			array_walk($result, function ($element, $key) use (&$result_array, $segment) {
				$result_array[] = $element[0];
				if (!is_numeric($element[1])) {
					$this->perts[$segment->get_type()][$element[0]] = 0;
				} else {
					$this->perts[$segment->get_type()][$element[0]] = floatval($element[1]);
				}
			});
			if (!empty($result_array)) {
				$this->formatResult($result_array, $segment->get_formated_type(), $segment->get_label(), $segment->get_type());
			}
		}
		$this->sortAutocomplete();
		print \encoding_normalize::json_encode($this->autocomplete_results);
	}

	/**
	 * Autocompletion dans les segments
	 *
	 * @param int $id
	 */
	public function getSegmentAutocomplete(int $id)
	{
		$search_segment = \search_segment::get_instance($id);
		$num_universe = $search_segment->get_num_universe();
		$this->getUniverseAutocomplete($num_universe);
	}

	/**
	 * Autocompletion dans la recherche simple
	 */
	public function getSimpleSearchAutocomplete()
	{
		session_write_close();

		if (empty($this->data->entities_types)) {
			print \encoding_normalize::json_encode([
				"error" => true,
				"errorMessage" => "pas de types selectionnes"
			]);
			return;
		}

		foreach ($this->data->entities_types as $type) {
			$search_instance = null;
			$search_json = \combine_search::simple_search_to_mc($this->data->user_query, true, $type, $search_instance);
			$search_instance->json_decode_search($search_json);
			$temp_table = $search_instance->make_search();
			$query = "SELECT * FROM $temp_table ORDER BY pert DESC LIMIT " . self::AUTOCOMPLETE_LIMIT;
			$result = pmb_mysql_fetch_all(pmb_mysql_query($query));
			$result_array = array();

			array_walk($result, function ($element, $key) use (&$result_array, $type) {
				$result_array[] = $element[0];
				if (!is_numeric($element[1])) {
					$this->perts[$type][$element[0]] = 0;
				} else {
					$this->perts[$type][$element[0]] = $element[1];
				}
			});
			if (!empty($result_array)) {
				$prefix = "";
				if (array_key_exists($type, \entities::get_entities_labels())) {
					$prefix = \entities::get_entities_labels()[$type];
				}
				if (get_class($search_instance) == "search_authorities") {
					$type = TYPE_AUTHORITY;
				}
				$this->formatResult($result_array, $type, $prefix);
			}
			$search_instance->destroy_global_env();
		}
		$this->sortAutocomplete();
		print \encoding_normalize::json_encode($this->autocomplete_results);
	}

	/**
	 * formatage des résultats de recherche
	 *
	 * @param array $ids
	 * @param int $formatedType
	 * @param string $prefix
	 * @param int $type
	 */
	private function formatResult($ids, $formatedType, $prefix = "", $type = "")
	{
		if (empty($prefix) && array_key_exists($formatedType, $ids)) {
			$prefix = \entities::get_entities_labels()[$formatedType];
		}
		if (empty($type)) {
			$type = $formatedType;
		}
		foreach ($ids as $id) {
			$entityLabel = $this->getLabelFromType($id, $formatedType);
			$entityLink = $this->getLinkFromType($id, $formatedType);
			if (empty($entityLabel)) {
				continue;
			}
			$this->autocomplete_results[] = [
				"value" => $entityLabel,
				"label" => "[$prefix] $entityLabel",
				"link" => $entityLink,
				"id" => $id,
				"type" => $type
			];
		}
	}

	/**
	 * Retourne l'isbd d'une entité
	 *
	 * @param int $id
	 * @param int $type
	 */
	private function getLabelFromType($id, $type)
	{
		switch ($type) {
			case TYPE_NOTICE:
				$notice = new \notice($id);
				return $notice->tit1;
			case TYPE_EXTERNAL:
				return "";
			case TYPE_CMS_ARTICLE:
				$article = new \cms_editorial_data($id, "article");
				return $article->get_title();
			case TYPE_CMS_SECTION:
				$section = new \cms_editorial_data($id, "section");
				return $section->get_title();
			case TYPE_ANIMATION:
				return "";
			default:
				$type_authority = \entities::get_aut_table_from_type($type);
				$aut = \authorities_collection::get_authority($type_authority, $id);
				return (isset($aut) ? $aut->get_title() : "");
		}
	}

	/**
	 * Retourne le lien vers un entité
	 *
	 * @param int $id
	 * @param int $type
	 */
	private function getLinkFromType($id, $type)
	{
		global $opac_url_base;

		switch ($type) {
			case TYPE_NOTICE:
				return UrlEntities::getOpacRealPermalink(TYPE_NOTICE, $id);
			case TYPE_EXTERNAL:
				return "";
			case TYPE_CMS_ARTICLE:
				$editorial = new \cms_editorial_data($id, "article");
				return $editorial->get_permalink();
			case TYPE_CMS_SECTION:
				$editorial = new \cms_editorial_data($id, "section");
				return $editorial->get_permalink();
			case TYPE_ANIMATION:
				return "";
			default:
				$type_authority = \entities::get_aut_table_from_type($type);
				$aut = \authorities_collection::get_authority($type_authority, $id);
				$type_const = \authority::aut_const_to_type_const($aut->get_type_object());
				return (isset($aut) ? UrlEntities::getOpacRealPermalink($type_const, $aut->get_num_object()) : $opac_url_base);
		}
	}

	public function getCmsAutocomplete(int $id = 0)
	{
		foreach (self::CMS_TYPES as $type) {
			$search_instance = null;
			$search_json = \combine_search::simple_search_to_mc($this->data->user_query, true, $type, $search_instance);
			$search_instance->json_decode_search($search_json);
			$temp_table = $search_instance->make_search();
			$query = "SELECT * FROM $temp_table ORDER BY pert DESC LIMIT " . self::AUTOCOMPLETE_LIMIT;
			$result = pmb_mysql_fetch_all(pmb_mysql_query($query));
			$result_array = array();

			array_walk($result, function ($element, $key) use (&$result_array) {
				$result_array[] = $element[0];
			});
			if (!empty($result_array)) {
				$prefix = \entities::get_entities_labels()[$type];
				$this->formatResult($result_array, $type, $prefix);
			}
			$search_instance->destroy_global_env();
		}
		if (count($this->autocomplete_results) > self::AUTOCOMPLETE_LIST_LIMIT) {
			array_splice($this->autocomplete_results, self::AUTOCOMPLETE_LIST_LIMIT);
		}
		print \encoding_normalize::json_encode($this->autocomplete_results);
	}

	/**
	 * Normalise les differentes ponderations
	 * @return void
	 */
	private function normalizePert()
	{
		foreach ($this->perts as $typeId => $type) {
			$max = !empty($type) ? max($type) : 0;
			if ($max == 0) {
				$this->perts[$typeId] = $type;
				continue;
			}
			foreach ($type as $id => $value) {
				$this->perts[$typeId][$id] = floatval($value) / floatval($max);
			}
		}
	}
	/**
	 * Trie et limite les resultats
	 */
	private function sortAutocomplete()
	{
		$this->normalizePert();
		usort($this->autocomplete_results, function ($a, $b) {
			if(isset($this->perts[$a["type"]][$a["id"]]) && isset($this->perts[$b["type"]][$b["id"]])) {
				if ($this->perts[$a["type"]][$a["id"]] == $this->perts[$b["type"]][$b["id"]]) {
					return 0;
				}
				return $this->perts[$a["type"]][$a["id"]] > $this->perts[$b["type"]][$b["id"]] ? -1 : 1;
			}
			return 0;
		});
		if (count($this->autocomplete_results) > self::AUTOCOMPLETE_LIST_LIMIT) {
			array_splice($this->autocomplete_results, self::AUTOCOMPLETE_LIST_LIMIT);
		}
	}
}