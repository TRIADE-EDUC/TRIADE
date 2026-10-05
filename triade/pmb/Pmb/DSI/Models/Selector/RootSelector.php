<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootSelector.php,v 1.9 2023/12/01 08:20:26 rtigero Exp $
namespace Pmb\DSI\Models\Selector;

use Pmb\DSI\Models\Root;

class RootSelector extends Root
{

	public const IGNORE_PROPS_TOARRAY = [
		"data"
	];

	/**
	 * Chaine de caractere qui contient la recherche a effectuer.
	 * Utilise la methode getSearchInput()
	 *
	 * @var null|string
	 */
	public $searchInput = null;

	protected $sort;

	public function __construct($selectors = null)
	{
		if (! empty($selectors->sort) && class_exists($selectors->sort->namespace)) {
			$namespace = $selectors->sort->namespace;
			$this->sort = new $namespace($selectors->sort->data);
		}
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes.
	 * Retourne la recherche effectuer pour l'affichage.
	 *
	 * @return string
	 */
	public function getSearchInput(): string
	{
		return "";
	}

	/**
	 * Cette methode doit etre remplacee dans les sous-classes.
	 * Retourne la recherche effectuer pour l'affichage avec la vue en détail de chaque elements.
	 *
	 * @return array
	 */
	public function trySearch()
	{
		return [];
	}

	protected function getSelectorQuery($query, $limit = 0)
	{
		$result = $query;
		if (! empty($this->sort)) {
			$count = 0;
			$join = $this->sort->getJoinClause();
			$result = str_ireplace("where", $join . " WHERE", $result, $count);
			if ($count == 0) {
				$result .= " " . $join;
			}
			$result .= " " . $this->sort->getSortQuery();
		}

		if ($limit && ! empty($this->limit)) {
			$limit = $this->limit <= $limit ? $this->limit : $limit;
		}

		if ($limit != 0) {
			$result .= " LIMIT " . intval($limit);
		}
		return $result;
	}

	protected function sortResults($results = array())
	{
		if (! empty($this->sort)) {
			return $this->sort->getSortOther($results);
		}

		return $results;
	}

	/**
	 * Permet de surcharger la limite dans la requête
	 *
	 * @param int $limit
	 */
	public function setLimit($limit = 0)
	{
		$this->limit = intval($limit);
	}
}