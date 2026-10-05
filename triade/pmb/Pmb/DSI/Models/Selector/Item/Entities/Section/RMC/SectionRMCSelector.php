<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionRMCSelector.php,v 1.1.4.2 2025/03/26 08:26:03 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Item\Entities\Section\RMC;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;
use search;

class SectionRMCSelector extends SubSelector
{

	protected const SECTION_TYPE = "section";

	protected $sectionIds = array();
	protected $data = null;

	public function __construct($selectors = null)
	{
		if (isset($selectors->data->search_serialize)) {
			$this->data = $selectors->data;
		}
		parent::__construct($selectors);
	}

	public function getData(): array
	{
		$sections = [];
		foreach ($this->getResults() as $id) {
			$id = intval($id);
			$query = "SELECT section_title FROM cms_sections WHERE id_section = '{$id}'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$sections[$id] = pmb_mysql_result($result, 0);
			}
		}
		return $this->sortResults($sections);
	}

	public function getResults()
	{
		// global $dsi_private_bannette_nb_notices;
		// $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

		if (empty($this->data->search_serialize)) {
			return [];
		}

		if (isset($this->results)) {
			return $this->results;
		}

		$search = new search(false, "search_fields_cms_editorial");
		$search->unserialize_search($this->data->search_serialize);
		$tempTable = $search->make_search();
		//La table tempo renvoie un num_object du format : identifiant_type
		$query = "SELECT id_section FROM (
			SELECT SUBSTRING(num_object, INSTR(num_object, '_') + 1) AS cms_type, SUBSTRING(num_object, 1, INSTR(num_object, '_') - 1) AS cms_id FROM " . $tempTable;
		$query .= ") as sub_query JOIN cms_sections ON cms_sections.id_section = sub_query.cms_id WHERE sub_query.cms_type = '" . static::SECTION_TYPE . "'";
		$fullQuery = $this->getSelectorQuery($query, 0);

		$result = pmb_mysql_query($fullQuery);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$this->results[] = $row["id_section"];
			}
			pmb_mysql_free_result($result);
		}

		// Souci de table tempo pas encore supprimee -> on force donc la suppression
		if (! empty($tempTable)) {
			$query = "DROP TABLE IF EXISTS " . $tempTable;
			pmb_mysql_query($query);
		}

		return $this->results;
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage.
	 *
	 * @return string
	 */
	public function getSearchInput(): string
	{
		$ids = "";
		if (count($this->sectionIds)) {
			$ids = implode(",", $this->sectionIds);
		}
		$messages = $this->getMessages();

		$this->searchInput = sprintf(
			$messages['search_input'],
			htmlentities($ids, ENT_QUOTES, GlobalContext::charset())
		);
		return $this->searchInput;
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage avec la vue en détail de chaque elements.
	 *
	 * @return array
	 */
	public function trySearch()
	{
		$data = $this->getData();
		array_walk($data, function (&$item, $key) {
			$section = new \cms_section($key);
			$item = gen_plus($key, $section->title, $section->get_detail());
		});
		return $data;
	}
}
