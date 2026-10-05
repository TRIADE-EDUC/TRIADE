<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsHistoryController.php,v 1.7.2.1 2024/09/05 08:44:06 gneveu Exp $

namespace Pmb\DSI\Controller;

use Pmb\Common\Helper\HelperEntities;
use Pmb\DSI\Helper\Filters;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\ContentHistory;
use Pmb\DSI\Models\Product;
use Pmb\DSI\Models\Tag;
use Pmb\DSI\Models\{DSIParserDirectory, DiffusionHistory};
use Pmb\DSI\Orm\DiffusionHistoryOrm;

class DiffusionsHistoryController extends CommonController
{
    protected const VUE_NAME = "dsi/diffusionsHistory";

    /**
     *
     * {@inheritDoc}
     * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
     */
    protected function getBreadcrumb()
    {
        global $msg;
        return "{$msg['dsi_menu']} {$msg['menu_separator']} {$msg['dsi_history_dashboard']}";
    }

    protected function defaultAction()
    {
        $history = new DiffusionHistory();
        $product = new Product();
        print $this->render([
            "list" => $history->getFilteredList([
                "state" => DiffusionHistory::SENT,
            ]),
            "entities" => Filters::getEntityOptions(),
            "filters" => Filters::getFilters(),
            "products" => $product->getList()
        ]);
    }

    /**
     * Permet de retourner le liste des type de contenue d'historique
     *
     * @return string
     */
    public function getContentHistoryTypes()
    {
        return $this->ajaxJsonResponse(ContentHistory::CONTENT_TYPES);
    }

    /**
     * Permet de retourner le rendu d'un historique
     *
     * @param integer $idDiffusionHistory
     * @return void
     */
    public function previewView(int $idDiffusionHistory)
    {
        if (!DiffusionHistoryOrm::exist($idDiffusionHistory)) {
            $this->notFound("Diffusion history not found");
        }
        $history = new DiffusionHistory($idDiffusionHistory);
        $this->ajaxResponse($history->previewView());
    }

    /**
     * Permet de supprimer en lot ou non d'une diffusion history
     *
     * @return void
     */
    public function delete()
    {
        $ids = explode(",", $this->data->id);
        $ids = array_map("intval", $ids);

        $idDeleteCount = 0;
        foreach ($ids as $id) {
            if (!DiffusionHistoryOrm::exist($id)) {
                continue;
            }

            $history = new DiffusionHistory($id);
            $history->delete();

            if (!DiffusionHistoryOrm::exist($id)) {
                $idDeleteCount++;
            }
        }

        return $this->ajaxJsonResponse([
            "success" => count($ids) == $idDeleteCount ? true : false
        ]);
    }
}
