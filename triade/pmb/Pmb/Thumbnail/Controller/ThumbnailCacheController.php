<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailCacheController.php,v 1.3 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Controller;

use Pmb\Common\Views\VueJsView;
use Pmb\Thumbnail\Models\ThumbnailCache;

class ThumbnailCacheController extends ThumbnailController
{
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Controller\ThumbnailController::defaultAction()
     */
    protected function defaultAction()
    {
        $model = new ThumbnailCache();
        $viewData = $this->getViewBaseData();
        $viewData["parameters"] = $model->getData();
        $newVue = new VueJsView("thumbnail/cache", $viewData);
        print $newVue->render();
    }
}

