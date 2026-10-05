<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ParentSectionSelector.php,v 1.1.4.2 2025/03/26 08:25:59 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Section\ParentSection;

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
        $query = "SELECT id_section FROM cms_sections WHERE section_num_parent = " . intval($this->data->sectionId);
        $fullQuery = $this->getSelectorQuery($query, $dsi_private_bannette_nb_notices);

        $result = pmb_mysql_query($fullQuery);
        if (pmb_mysql_num_rows($result) > 0) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $results[] = intval($row['id_section']);
            }
        }
        return $results;
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

        $this->data->sectionId = intval($this->data->sectionId);
        $query = "SELECT section_title FROM cms_sections WHERE id_section = {$this->data->sectionId}";
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
            $section = new \cms_section($key);
            $item = gen_plus($key, $section->title, $section->get_detail());
        });
        return $data;
    }
}
