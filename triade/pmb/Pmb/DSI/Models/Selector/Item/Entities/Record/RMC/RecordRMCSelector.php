<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordRMCSelector.php,v 1.15.2.2.2.1 2025/03/21 08:32:08 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Record\RMC;

use Pmb\DSI\Models\Selector\SubSelector;
use search;
use notice;

class RecordRMCSelector extends SubSelector
{
	public const CONTENT_NOTICE_TITLE = 0;

	public const CONTENT_NOTICE_DETAIL = 1;

	public $selector = null;

	public $data = [];

	private $results = null;

	// protected static $tempTable = "";

	public function __construct($selectors = null)
	{
		if (!empty($selectors)) {
			$this->data = $selectors->data ?? [];
		}

		parent::__construct($selectors);
	}

	public function getResults(): array
	{
		// global $dsi_private_bannette_nb_notices;
		// $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

		if (empty($this->data->search_serialize)) {
			return [];
		}

		if (isset($this->results)) {
			return $this->results;
		}

		$results = [];
		$search = new search();
		$search->unserialize_search($this->data->search_serialize);
		$tempTable = $search->make_search();

		$query = "SELECT * FROM " . $tempTable . " JOIN notices ON $tempTable.notice_id = notices.notice_id";

		// TODO Revoir la limite de notices renvoyé qui bloque certaine fonctionnalités (diffusion dans un panier / export)
		//$fullQuery = $this->getSelectorQuery($query, $dsi_private_bannette_nb_notices);
		$fullQuery = $this->getSelectorQuery($query, 0);

		$result = pmb_mysql_query($fullQuery);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$results[] = $row['notice_id'];
			}
			pmb_mysql_free_result($result);
		}

		// Souci de table tempo pas encore supprimee -> on force donc la suppression
		if (! empty($tempTable)) {
			$query = "DROP TABLE IF EXISTS " . $tempTable;
			pmb_mysql_query($query);
		}

		$this->results = $results;
		return $this->results;
	}

	public function getData(): array
	{
		if (empty($this->data) && !empty($this->selector)) {
			return $this->selector->getData();
		}

		return $this->sortResults($this->getRMCResult());
	}

	protected function getRMCResult(int $contentType = RecordRMCSelector::CONTENT_NOTICE_TITLE): array
	{
		$records = [];
		if (!isset($this->data->search_serialize)) {
			return $records;
		}

		$results = $this->getResults();

		switch ($contentType) {
			case RecordRMCSelector::CONTENT_NOTICE_TITLE:
				foreach ($results as $id) {
					$records[$id] = notice::get_notice_title($id);
				}
				break;
			case RecordRMCSelector::CONTENT_NOTICE_DETAIL:
				foreach ($results as $id) {
					$notice = new notice($id);
					$records[$id] = gen_plus($id, notice::get_notice_title($id), $notice->get_detail());
				}
				break;
		}
		return $records;
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage.
	 *
	 * @return string
	 */
	public function getSearchInput(): string
	{
		if (isset($this->searchInput)) {
			return $this->searchInput;
		}

		$this->searchInput = $this->data->human_query ?? "";
		return $this->searchInput;
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage avec la vue en détail de chaque elements.
	 *
	 * @return array
	 */
	public function trySearch()
	{
		return $this->getRMCResult(RecordRMCSelector::CONTENT_NOTICE_DETAIL);
	}
}
