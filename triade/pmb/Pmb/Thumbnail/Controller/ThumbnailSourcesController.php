<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailSourcesController.php,v 1.9 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class ThumbnailSourcesController extends ThumbnailController
{
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Controller\ThumbnailController::defaultAction()
     */
    protected function defaultAction()
    {
        $handler = new ThumbnailSourcesHandler();
        $viewData = $this->getViewBaseData();
        $viewData["sources"] = $handler->getSourcesByEntity();
        $newVue = new VueJsView("thumbnail/sources", $viewData);
        print $newVue->render();
    }
}

