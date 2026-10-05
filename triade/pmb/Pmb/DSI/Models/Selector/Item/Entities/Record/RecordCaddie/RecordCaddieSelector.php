<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordCaddieSelector.php,v 1.1 2023/11/07 16:10:00 rtigero Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Record\RecordCaddie;

use Pmb\DSI\Models\Selector\SubSelector;
use notice;
use caddie;

class RecordCaddieSelector extends SubSelector
{
    public $selector = null;

    public $data = [];

    public function __construct($selectors = null)
    {
        if (!empty($selectors)) {
            $this->data = $selectors->data ?? [];
        }
        parent::__construct($selectors);
    }

    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;

        //Ce sélecteur ne devrait pas être utilisé à l'OPAC mais on sait jamais
        if (!class_exists("caddie")) {
            return array();
        }
        $data = array();
        $result = array();

        if (!empty($this->data->caddieId)) {
            $caddie = new caddie($this->data->caddieId);
            $data = $caddie->get_cart("ALL");
        }

        if (count($data) > $dsi_private_bannette_nb_notices) {
            array_splice($data, $dsi_private_bannette_nb_notices);
        }

        foreach ($data as $id) {
            $notice = new notice($id);
            $content = gen_plus($id, notice::get_notice_title($id), $notice->get_detail());
            $result[$id] = $content;
        }

        return $result;
    }

    public function getData(): array
    {
        if (empty($this->data) && !empty($this->selector)) {
            return $this->selector->getData();
        }

        return $this->sortResults($this->getResults());
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
        if (!empty($this->data->caddieId) && class_exists("caddie")) {
            $caddie = new caddie($this->data->caddieId);
            $this->searchInput = $caddie->name;
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
        return $this->getResults();
    }
}
