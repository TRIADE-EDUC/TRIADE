<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParentSectionSelector.php,v 1.11 2023/11/02 15:57:24 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Article\ParentSection;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;

class ParentSectionSelector extends SubSelector
{
    protected $data = [];

    public function __construct($selectors = null)
    {
        if (!empty($selectors->data)) {
            $this->data = $selectors->data;
        }
    }

    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;
        $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

        $results = [];
        $query = "SELECT id_article FROM cms_articles WHERE num_section = " . intval($this->data->sectionId);
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

        $messages = $this->getMessages();

        $this->data->watchId = intval($this->data->sectionId);
        $query = "SELECT section_title FROM cms_sections WHERE id_section = {$this->data->watchId}";
        $result = pmb_mysql_query($query);
        $sectionTitle = "{$this->data->sectionId}";
        if (pmb_mysql_num_rows($result)) {
            $sectionTitle = pmb_mysql_result($result, 0, 0);
        }

        $this->searchInput = sprintf(
            $messages['search_input'],
            htmlentities($sectionTitle, ENT_QUOTES, GlobalContext::charset())
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
