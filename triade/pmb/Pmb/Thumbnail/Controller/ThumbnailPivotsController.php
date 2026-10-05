<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailPivotsController.php,v 1.8 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class ThumbnailPivotsController extends ThumbnailController
{
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Controller\ThumbnailController::defaultAction()
     */
    protected function defaultAction()
    {
        global $msg;
        
        $pivots = [];
        $handler = new ThumbnailSourcesHandler();
        foreach ($handler->getPivotsByEntity($this->data->type) as $pivotClass) {
            $pivots[] = $pivotClass::getViewData();
        }
        
        $viewData = $this->getViewBaseData();
        $viewData["label"] = $msg["admin_thumbnail_entity_{$this->data->type}"] ?? $this->data->type;
        $viewData["type"] = $this->data->type ?? "";
        $viewData["pivots"] = $pivots ?? [];
        $viewData["sources"] = $handler->getSourcesByEntity($this->data->type) ?? [];
        
        $newVue = new VueJsView("thumbnail/pivots", $viewData);
        print $newVue->render();
    }
}

