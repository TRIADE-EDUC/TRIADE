<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AllArticleSelector.php,v 1.12 2023/11/02 15:57:24 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Article\All;

use Pmb\DSI\Models\Selector\SubSelector;

class AllArticleSelector extends SubSelector
{
    // Ne pas enlever le CONSTRUCTEUR !
    public function __construct($selectors = null) {
        parent::__construct($selectors);
    }
    
    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;
        $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

        $results = [];
        $query = "SELECT id_article FROM cms_articles";
        $fullQuery = $this->getSelectorQuery($query, $dsi_private_bannette_nb_notices);

        $result = pmb_mysql_query($fullQuery);
        if (pmb_mysql_num_rows($result) > 0) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $results[] = intval($row['id_article']);
            }
        }
        return $results;
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
        $this->searchInput = "";
        $messages = $this->getMessages();
        if(! empty($messages['search_input'])) {
            $this->searchInput = $messages['search_input'];
        }
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
