<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationSubSelector.php,v 1.1.4.2 2025/05/21 08:03:12 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Animation;

use Pmb\Animations\Models\AnimationModel;
use Pmb\Common\Models\SearchModel;
use Pmb\DSI\Models\Selector\SubSelector;

class AnimationSubSelector extends SubSelector
{
    public $data = null;

    // Ne pas enlever le CONSTRUCTEUR !
    public function __construct($selectors = null)
    {
        $this->data = $selectors->data;
        parent::__construct($selectors);
    }

    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;
        $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

        $results = [];
        if (! $this->checkData()) {
            return $results;
        }

        $searchModel = new SearchModel();
        $table = "";
        $searchGlobals = $this->getGlobalsSearch();

        $searchModel->makeSearch(AnimationModel::getGlobalsSearch($searchGlobals), 'id_animation', 'search_fields_animations', $table);
        $query = "SELECT * FROM $table JOIN anim_animations ON $table.id_animation = anim_animations.id_animation JOIN anim_events ON num_event = id_event";

        $fullQuery = $this->getSelectorQuery($query, $dsi_private_bannette_nb_notices);

        $result = pmb_mysql_query($fullQuery);
        if (pmb_mysql_num_rows($result) > 0) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $results[] = intval($row['id_animation']);
            }
        }
        return $results;
    }

    public function getData(): array
    {
        $animations = [];
        foreach ($this->getResults() as $id) {
            $id = intval($id);
            $query = "SELECT name FROM anim_animations WHERE id_animation = '{$id}'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $animations[$id] = pmb_mysql_result($result, 0);
            }
        }
        return $this->sortResults($animations);
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
        if (! empty($messages['search_input'])) {
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
            $animation = AnimationModel::getAnimation($key);
            $item = gen_plus($key, $animation->name, $animation->description);
        });
        return $data;
    }

    protected function getGlobalsSearch()
    {
        return new \stdClass();
    }

    protected function checkData()
    {
        return true;
    }
}
