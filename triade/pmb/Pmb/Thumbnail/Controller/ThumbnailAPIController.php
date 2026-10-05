<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailAPIController.php,v 1.6.2.1 2024/07/26 09:13:38 jparis Exp $
namespace Pmb\Thumbnail\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;
use Pmb\Thumbnail\Models\ThumbnailCache;
use Pmb\Common\Library\Image\CacheImage;

class ThumbnailAPIController extends Controller
{
    /**
     * enregistrement d'une source
     * @param string $entityType
     * @param string $sourceName
     */
    public function saveSource(string $entityType, string $sourceName) : void
    {
        global $msg;

        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        $source = $thumbnailSourcesHandler->getSourceClass($entityType, $sourceName);
        $source->setParameters($this->data->values);
        $succes = $source->save();

        if ($succes) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }

    /**
     * recuperation des donnees d'une source
     * @param string $entityType
     * @param string $sourceName
     */
    public function getData(string $entityType, string $sourceName) : void
    {
        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        $source = $thumbnailSourcesHandler->getSourceClass($entityType, $sourceName);
        $this->ajaxJsonResponse([
            'messages' => $source::getMessages(),
            'parameters' => $source->getParameters()
        ]);
    }

    /**
     * enregistrement d'un pivot
     * @param string $entityType
     */
    public function savePivot(string $entityType) : void
    {
        global $msg;

        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        $succes = $thumbnailSourcesHandler->setSourcesByEntityPivot($entityType, $this->data->pivot, $this->data->sources);
        if ($succes) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }

    /**
     * recuperation des sources associees a un type
     * @param string $entityType
     */
    public function getSourcesByEntityPivot(string $entityType) : void
    {
        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        $sources = $thumbnailSourcesHandler->getSourcesByEntityPivot($entityType, $this->data->pivot);
        $this->ajaxJsonResponse([
            'sources' => $sources
        ]);
    }

    /**
     * enregistrement des parametres associes au cache
     */
    public function saveCache() : void
    {
        global $msg;

        $cacheModel= new ThumbnailCache();
        $success = $cacheModel->save($this->data->values);
        if ($success) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }

    /**
     * nettoyage du cache
     */
    public function cleanCache() : void
    {
        global $msg;

        $success = CacheImage::clearCache();
        if ($success) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }

    /**
     * suppresion d'un pivot
     * @param string $entityType
     */
    public function removePivot(string $entityType) : void
    {
        global $msg;

        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        $succes = $thumbnailSourcesHandler->removePivot($entityType, $this->data->pivot);
        if ($succes) {
            $this->ajaxJsonResponse([
                'succes' => true
            ]);
        }
        $this->ajaxError($msg['common_failed_save']);
    }
}