<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WatchSelector.php,v 1.8 2023/11/02 15:57:24 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\ItemWatch\Watch;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;

class WatchSelector extends SubSelector
{
    public $data = [];

    public $selector = null;

    public function __construct($selectors = null)
    {
        if (!empty($selectors->data)) {
            $this->data = $selectors->data;
        }

        parent::__construct($selectors);
    }

    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;
        $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

        $results = [];
        $idWatch = intval($this->data->watchId ?? 0);

        $query = "SELECT id_item FROM docwatch_items WHERE item_num_watch = '{$idWatch}'";
        $fullQuery = $this->getSelectorQuery($query, $dsi_private_bannette_nb_notices);
        $result = pmb_mysql_query($fullQuery);
        if (pmb_mysql_num_rows($result) > 0) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $results[] = $row['id_item'];
            }
        }
        return $results;
    }

    public function getData(): array
    {
        $itemsWatch = [];
        foreach ($this->getResults() as $id) {
            $itemsWatch[$id] = (new \docwatch_item($id))->get_normalized_item();
        }
        return $this->sortResults($itemsWatch);
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

        $this->data->watchId = intval($this->data->watchId);
        $query = "SELECT watch_title FROM docwatch_watches WHERE id_watch = {$this->data->watchId}";
        $result = pmb_mysql_query($query);
        $watchTitle = "{$this->data->watchId}";
        if (pmb_mysql_num_rows($result)) {
            $watchTitle = pmb_mysql_result($result, 0, 0);
        }

        $this->searchInput = sprintf(
            $messages['search_input'],
            htmlentities($watchTitle, ENT_QUOTES, GlobalContext::charset())
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
            $item = gen_plus($key, $item['title'], $item['detail']);
        });
        return $data;
    }
}
