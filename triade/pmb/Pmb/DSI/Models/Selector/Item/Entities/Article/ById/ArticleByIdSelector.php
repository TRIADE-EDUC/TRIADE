<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArticleByIdSelector.php,v 1.5 2024/03/18 13:30:29 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Item\Entities\Article\ById;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;

class ArticleByIdSelector extends SubSelector
{

	protected $articleIds = array();

	public function __construct($selectors = null)
	{
		if (isset($selectors->data->articleId)) {
			$this->articleIds = explode(',', $selectors->data->articleId);
			array_walk($this->articleIds, function (&$value) {
				$value = intval(trim($value));
			});
		}
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
		$results = array();

		$query = "SELECT id_article, article_title FROM cms_articles WHERE id_article IN (" . implode(",", $this->articleIds) . ")";
		$fullQuery = $this->getSelectorQuery($query);
		$resultQuery = pmb_mysql_query($fullQuery);
		while ($row = pmb_mysql_fetch_assoc($resultQuery)) {
			$results[] = intval($row['id_article']);
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
