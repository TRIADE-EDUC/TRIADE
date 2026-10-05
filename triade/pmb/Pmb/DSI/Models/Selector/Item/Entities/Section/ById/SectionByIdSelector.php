<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SectionByIdSelector.php,v 1.1.4.2 2025/03/26 08:26:03 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Item\Entities\Section\ById;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;

class SectionByIdSelector extends SubSelector
{

	protected $sectionIds = array();

	public function __construct($selectors = null)
	{
		if (isset($selectors->data->sectionIds)) {
			$this->sectionIds = explode(',', $selectors->data->sectionIds);
			array_walk($this->sectionIds, function (&$value) {
				$value = intval(trim($value));
			});
		}
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
		$results = array();

		$query = "SELECT id_section, section_title FROM cms_sections WHERE id_section IN (" . implode(",", $this->sectionIds) . ")";
		$fullQuery = $this->getSelectorQuery($query);
		$resultQuery = pmb_mysql_query($fullQuery);
		while ($row = pmb_mysql_fetch_assoc($resultQuery)) {
			$results[] = intval($row['id_section']);
		}
		return $results;
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
