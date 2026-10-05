<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AllDiffusionSelector.php,v 1.7 2023/06/15 09:06:15 qvarin Exp $

namespace Pmb\DSI\Models\Selector\Item\Entities\Diffusion\All;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Selector\SubSelector;
use Pmb\DSI\Orm\DiffusionOrm;

class AllDiffusionSelector extends SubSelector
{
    protected $data = [];

    public function __construct($selector = null)
    {
        if (!empty($selector->data)) {
            $this->data = $selector->data;
        }
    }

    public function getResults(): array
    {
        global $dsi_private_bannette_nb_notices;
        $dsi_private_bannette_nb_notices = intval($dsi_private_bannette_nb_notices);

        $ids = array_slice($this->data->ids, 0, $dsi_private_bannette_nb_notices, true);
        return array_filter($ids, function ($id) {
            $id = intval($id);
            return DiffusionOrm::exist($id);
        });
    }

    public function getData(): array
    {
        $data = [];
        if (!empty($this->data)) {
            foreach ($this->getResults() as $id) {
                $diffusion = new Diffusion($id);
                $data[$id] = [
                    'content' => $diffusion,
                    'render' => $diffusion->renderView(),
                ];
            }
        }
        return $data;
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

        if (!empty($this->data)) {
            $diffusionList = [];
            foreach ($this->data->ids as $id) {
                $diffusion = new Diffusion($id);
                $diffusionList[] = $diffusion->name;
            }
            $diffusionList = implode(', ', $diffusionList);
        } else {
            $diffusionList = "";
        }

        $messages = $this->getMessages();
        $this->searchInput = sprintf(
            $messages['search_input'],
            htmlentities($diffusionList, ENT_QUOTES, GlobalContext::charset())
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
        $result =[];
        if (!empty($this->data)) {
            foreach ($this->data->ids as $id) {
                $diffusion = new Diffusion($id);
                $result[$id] = gen_plus($id, $diffusion->name, $diffusion->getDetail());
            }
        }
        return $result;
    }
}
