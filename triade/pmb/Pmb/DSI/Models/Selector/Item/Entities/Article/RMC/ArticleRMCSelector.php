<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleRMCSelector.php,v 1.1.4.2 2025/03/26 08:26:02 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Item\Entities\Article\RMC;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;
use search;

class ArticleRMCSelector extends SubSelector
{

	protected const ARTICLE_TYPE = "article";

	protected $articleIds = array();
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
		$articles = [];
		foreach ($this->getResults() as $id) {
			$id = intval($id);
			$query = "SELECT article_title FROM cms_articles WHERE id_article = '{$id}'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$articles[$id] = pmb_mysql_result($result, 0);
			}
		}
		return $this->sortResults($articles);
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
		$query = "SELECT id_article FROM (
			SELECT SUBSTRING(num_object, INSTR(num_object, '_') + 1) AS cms_type, SUBSTRING(num_object, 1, INSTR(num_object, '_') - 1) AS cms_id FROM " . $tempTable;
		$query .= ") as sub_query JOIN cms_articles ON cms_articles.id_article = sub_query.cms_id WHERE sub_query.cms_type = '" . static::ARTICLE_TYPE . "'";
		$fullQuery = $this->getSelectorQuery($query, 0);

		$result = pmb_mysql_query($fullQuery);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$this->results[] = $row["id_article"];
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
		if (count($this->articleIds)) {
			$ids = implode(",", $this->articleIds);
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
			$article = new \cms_article($key);
			$item = gen_plus($key, $article->title, $article->get_detail());
		});
		return $data;
	}
}
