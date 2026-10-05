<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailSourcesHandler.php,v 1.30.2.6.2.2 2025/04/04 14:01:59 tsamson Exp $
namespace Pmb\Thumbnail\Models;

use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;
use Pmb\Thumbnail\Models\Pivots\RootPivot;
use Pmb\Thumbnail\Orm\SourcesEntitiesOrm;
use Pmb\Common\Helper\HelperEntities;
use Pmb\Common\Library\Image\Image;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Library\Image\CacheImage;
use Pmb\Common\Library\Image\CacheInfo;

class ThumbnailSourcesHandler
{

    /**
     * liste des sources par entite
     *
     * @var array
     */
    private $sourcesByEntity = [];

    /**
     * liste des entites declarees dans les manifests
     * @var array
     */
    private $entities = [];

    /**
     * Retourne une source (instance) en fonction d'un type d'entité et d'un nom de source
     *
     * @param string $entityType
     * @param string $sourceName
     * @return NULL|RootThumbnailSource
     */
    public function getSourceClass(string $entityType, string $sourceName)
    {
        $path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'Sources'. DIRECTORY_SEPARATOR . 'Entities' .
            DIRECTORY_SEPARATOR . ucfirst($entityType) . DIRECTORY_SEPARATOR . ucfirst($sourceName);

        $sourceManifest = ThumbnailParserDirectory::getInstance()->getManifests($path);
        $sourceManifest = $sourceManifest[0] ?? false;

        if (false === $sourceManifest) {
            return null;
        }

        return $sourceManifest->namespace::getInstance();
    }

    /**
     * Retourne la liste des entités (classe enfantes de RootEntity)
     *
     * @return array
     */
    public function getEntitiesList(): array
    {
        global $msg;

        if (empty($this->entities)) {
            $manifests = ThumbnailParserDirectory::getInstance()->getEntitiesList();
            foreach ($manifests as $manifest) {
                $this->entities[] = [
                    "namespace" => $manifest->namespace,
                    "type" => $manifest->entityType,
                    "label_code" => "admin_thumbnail_entity_{$manifest->entityType}",
                    "label" => $msg["admin_thumbnail_entity_{$manifest->entityType}"] ?? $manifest->entityType
                ];
            }
        }
        return $this->entities;
    }

    /**
     * Retourne la liste des sources pour un type d'entité donné.
     * Si Aucun type donnée retourne toutes les source pour chaque entité
     *
     * @param string|null $entityType
     * @return array
     */
    public function getSourcesByEntity(?string $entityType = null)
    {
        if (empty($this->sourcesByEntity)) {
            $manifests = ThumbnailParserDirectory::getInstance()->getManifests();
            foreach ($manifests as $manifest) {
                if ("source" == $manifest->type) {
                    $messages = $manifest->namespace::getMessages();
                    $this->sourcesByEntity[$manifest->entity][] = [
                        "namespace" => $manifest->namespace,
                        "source" => $manifest->name,
                        "label" => $messages['name'] ?? $manifest->name
                    ];
                }
            }
        }
        return $entityType ? $this->sourcesByEntity[$entityType] : $this->sourcesByEntity;
    }

    /**
     * Retourne la liste des pivots pour un type d'entité donné.
     *
     * @param string $entityType
     * @return array
     */
    public function getPivotsByEntity(string $entityType) : array
    {
        $entityIndex = array_search($entityType, array_column($this->getEntitiesList(), "type"));
        if (false === $entityIndex || ! class_exists($this->entities[$entityIndex]['namespace'])) {
            return [];
        }

        $pivots = [];
        $entity = new $this->entities[$entityIndex]['namespace']();
        foreach ($entity->getPivots() as $manifest) {
            if (class_exists($manifest->namespace)) {
                $pivots[] = $manifest->namespace;
            }
        }
        return $pivots;
    }

    /**
     * Retourne la liste des sources qui match pour un type d'entité donné avec son identifiant
     *
     * @param string $entityType
     * @param int $objectId
     * @return array
     */
    public function getSourcesByObject(string $entityType, int $objectId) : array
    {
        $sources = [];
        $pivots = $this->getPivotsByEntity($entityType);
        if (!empty($pivots)) {
            foreach ($pivots as $pivot) {
                $sources = array_merge($sources, $pivot::getSourcesFromObjectId($objectId));
            }
        }
        return $sources;
    }

    /**
     * Récupère les donnée dans la table "SourcesEntitiesOrm"
     *
     * @param string $entityType
     * @param object $pivot
     * @return array
     */
    public function getSourcesByEntityPivot(string $entityType, object $pivot) : array
    {
        $pivotsOrm = SourcesEntitiesOrm::finds([
            "pivot" => \encoding_normalize::json_encode($pivot),
            "type" => \entities::get_entity_type_from_entity($entityType)
        ], 'ranking');

        $sources = [];
        foreach ($pivotsOrm as $pivotOrm) {
            $sources[$pivotOrm->ranking] = $pivotOrm->source_class;
        }

        return $sources;
    }

    /**
     * Permet de remplir la table "SourcesEntitiesOrm"
     *
     * @param string $entityType
     * @param mixed $pivot
     * @param array $sources
     */
    public function setSourcesByEntityPivot(string $entityType, $pivot, array $sources) : bool
    {
        $success = true;

        $pivotData = clone $pivot;
        unset($pivotData->namespace);

        $pivotsOrm = SourcesEntitiesOrm::finds([
            "pivot_class" => $pivot->namespace,
            "pivot" => \encoding_normalize::json_encode($pivotData),
            "type" => \entities::get_entity_type_from_entity($entityType)
        ]);
        if (!empty($pivotsOrm)) {
            array_walk($pivotsOrm, function ($orm) {
                $orm->delete();
            });
        }

        foreach ($sources as $ranking => $sourceNamespace) {

            /**
             * @var RootPivot $pivotInstance
             */
            $pivotInstance = new $pivot->namespace();
            $pivotInstance->setDataFromForm($pivot);
            $pivotInstance->setRanking($ranking);
            $pivotInstance->setType(\entities::get_entity_type_from_entity($entityType));
            $pivotInstance->setSourceClass($sourceNamespace);
            $success &= $pivotInstance->save();
        }
        return $success;
    }

    /**
     * affiche l'image en fonction de la source ou du cache
     * @param int $type
     * @param int $objectId
     */
    public function printImage(int $type, int $objectId) : void
    {
        $entitiesNamespaces = HelperEntities::get_entities_namespace();

        $filenameCache = CacheImage::generateFilename($entitiesNamespaces[$type], $objectId);
        $cache = CacheImage::fetch($filenameCache);
        if (!empty($cache) && !is_null(Image::print($cache))) {
            exit;
        }

        $maxSize = intval(GlobalContext::get("notice_img_pics_max_size"));
        $sources = $this->getSourcesByObject($entitiesNamespaces[$type], $objectId);

        foreach ($sources as $source) {
            $sourceClass = $source::getInstance();
            if ($sourceClass->isActive()) {
                $img = $sourceClass->getImage($objectId);
                if (empty($img)) {
                    continue;
                }
            	$cacheInfo = new CacheInfo();
            	$cacheInfo->checkSize();
            	$contentType = $sourceClass->getImageHeaders()['content-type'] ?? $sourceClass->getImageHeaders()['Content-Type'] ?? '';
                if ($contentType == 'image/svg+xml') {
                    if (!is_null(Image::printSVG($img))) {
                		exit;
                	}
                } else {
                	$img = Image::format($img, $maxSize, $sourceClass->getWatermark());
                	if ($sourceClass->hasAllowedCache() && !empty(CacheImage::add($filenameCache, $img))) {
                	    $cacheInfo->update($filenameCache);
                	}
                	if (!is_null(Image::print($img))) {
                		exit;
                	}
                }
            }
        }

        // On n'a pas d'image pour cette entite
        // On renvoie 404, pour que le navigateur affiche l'attribut alt
        http_response_code(404);
    }

    /**
     * Permet de vérifier si le type d'entité existe
     *
     * @param string $type
     * @return boolean
     */
    public static function checkType(string $type) : bool
    {
        $entitiesNamespaces = HelperEntities::get_entities_namespace();
        if (empty($entitiesNamespaces[$type])) {
            return false;
        }
        return true;
    }

    /**
     * Genere l'URL d'accès pour le type d'entite et un idenfiant donnee
     *
     * @param int $type
     * @param int $objectId
     * @return string
     */
    public function generateUrl(int $type, int $objectId): string
    {
        $entitiesNamespaces = HelperEntities::get_entities_namespace();
        if (!empty($entitiesNamespaces[$type])) {
            $filenameCache = CacheImage::generateFilename($entitiesNamespaces[$type], $objectId);
            $urlCache = CacheImage::generateUrl($filenameCache);
            if (!empty($urlCache)) {
                return $urlCache;
            }
        }
        $url = GlobalContext::urlBase();
        if (GlobalContext::get("notice_is_pdf")) {
            $url = GlobalContext::get("pmb_url_internal");
        }
        return $url . "thumbnail.php?type={$type}&id={$objectId}";
    }

    /**
     * Genere l'image base64 pour le type d'entite et un idenfiant donnee
     *
     * @param int $type
     * @param int $objectId
     * @return string
     */
    public function generateSrcBase64(int $type, int $objectId): string
    {
        global $use_opac_url_base;

        $binary = null;
        $mime_content_type = null;

        if (CacheImage::enabled()) {
            $entitiesNamespaces = HelperEntities::get_entities_namespace();
            $filename = CacheImage::generateAbsoluteUrl(
                CacheImage::generateFilename($entitiesNamespaces[$type], $objectId)
            );

            // Si le cache est active et qu'on a deja l'image en cache, on evite le cURL
            if (!empty($filename)) {
                $imageData = Image::convertBinaryWebpToPng(file_get_contents($filename));
                $mime_content_type = mime_content_type($filename);
                if (false !== $imageData) {
                    [$binary, $mime_content_type] = $imageData;
                }
            }
        }

        // Aucune image en cache, on va la chercher
        if (empty($binary)) {
            $curl = new \Curl();
            $curl->timeout = RootThumbnailSource::CURL_TIMEOUT;
            $curl->options['CURLOPT_SSL_VERIFYPEER'] = 0;
            $response = $curl->get(GlobalContext::urlBase() . "thumbnail.php?type={$type}&id={$objectId}".($use_opac_url_base ? "&img_cache_type=png" : ""));

            if ($response instanceof \CurlResponse) {
                $imageData = Image::convertBinaryWebpToPng($response->body);
                if (false !== $imageData) {
                    [$binary, $mime_content_type] = $imageData;
                }
            }
        }

        if (
            !empty($binary) &&
            in_array($mime_content_type, Image::MIMETYPE)
        ) {
            return 'data:' . $mime_content_type . ';base64,' . base64_encode($binary);
        }
        return GlobalContext::urlBase() . "thumbnail.php?type={$type}&id={$objectId}";
    }

    /**
     * suppression d'un pivot
     * @param string $entityType
     * @param \stdClass $pivot
     * @return bool
     */
    public function removePivot(string $entityType, \stdClass $pivot) : bool
    {
        $success = true;
        $pivotData = clone $pivot;
        unset($pivotData->namespace);

        $pivotsOrm = SourcesEntitiesOrm::finds([
            "pivot_class" => $pivot->namespace,
            "pivot" => \encoding_normalize::json_encode($pivotData),
            "type" => \entities::get_entity_type_from_entity($entityType)
        ]);
        if (!empty($pivotsOrm)) {
            array_walk($pivotsOrm, function ($orm) {
                $orm->delete();
            });
        }
        return $success;
    }

    /**
     * retourne le nom de la source
     * @param int $type
     * @param int $objectId
     */
    public function getSourceLabel(int $type, int $objectId): string
    {
        $entitiesNamespaces = HelperEntities::get_entities_namespace();
        $sources = $this->getSourcesByObject($entitiesNamespaces[$type], $objectId);

        foreach ($sources as $source) {
            $sourceClass = $source::getInstance();

            if ($sourceClass->isActive()) {
                $img = $sourceClass->getImage($objectId);
                if (!empty($img)) {
                    $sourceLabel = substr($source, strrpos($source, '\\') + 1);
                    return $sourceLabel;
                }
            }
        }
        return "";
    }
}
