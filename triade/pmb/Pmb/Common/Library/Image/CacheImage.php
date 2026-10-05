<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CacheImage.php,v 1.10.2.1.2.2 2025/03/27 14:40:52 dbellamy Exp $
namespace Pmb\Common\Library\Image;

use Pmb\Common\Helper\GlobalContext;

abstract class CacheImage
{

    /**
     * Permet de supprimer une image du repertoire de cache
     * Sans tenir compte de l'extension
     *
     * @param string $filename
     * @return bool
     */
    public static function deleteWithoutExtension(string $filename): bool
    {
        if (!self::enabled()) {
            return false;
        }

        $success = true;
        $filname = substr($filename, 0, strrpos($filename, '.'));
        $resources = glob(GlobalContext::get("pmb_img_cache_folder") . $filname . ".*");
        if (!empty($resources)) {
            foreach ($resources as $resource) {
                if (is_file($resource)) {
                    $success &= unlink($resource);
                }
            }
        }

        $resources = glob(GlobalContext::get("opac_img_cache_folder") . $filname . ".*");
        if (!empty($resources)) {
            foreach ($resources as $resource) {
                if (is_file($resource)) {
                    $success &= unlink($resource);
                }
            }
        }

        $cacheInfo = new CacheInfo();
        $cacheInfo->checkSize();

        return $success;
    }

    /**
     * Permet de supprimer une image du repertoire de cache
     *
     * @param string $filename
     * @return bool
     */
    public static function delete(string $filename): bool
    {
        if (!self::enabled()) {
            return false;
        }

        if (is_file(GlobalContext::get("pmb_img_cache_folder") . $filename)) {
            unlink(GlobalContext::get("pmb_img_cache_folder") . $filename);
        }

        if (is_file(GlobalContext::get("opac_img_cache_folder") . $filename)) {
            unlink(GlobalContext::get("opac_img_cache_folder") . $filename);
        }

        $cacheInfo = new CacheInfo();
        $cacheInfo->checkSize();

        return true;
    }

    /**
     * Permet de recuperer une image du repertoire de cache
     *
     * @param string $filename
     * @param bool $needResoure
     * @return NULL|string|resource
     */
    public static function fetch(string $filename, bool $needResoure = true)
    {
        if (self::exists($filename)) {
            $img = file_get_contents(GlobalContext::get("img_cache_folder") . $filename);
            if ($needResoure) {
                $img = imagecreatefromstring($img);
                if(false === $img) {
                    return null;
                }
                if (GlobalContext::get("img_cache_type") == "png") {
                    // On active la transparence
                    imagesavealpha($img, true);
                    imagealphablending($img, false);
                }
            }
            return $img;
        }
        return null;
    }

    /**
     * Permet de savoir si une image est existante dans le repertoire de cache
     *
     * @param string $filename
     * @return bool
     */
    public static function exists(string $filename): bool
    {
        return self::enabled() ? is_file(GlobalContext::get("img_cache_folder") . $filename) : false;
    }

    /**
     * Permet de recuperer la date de modification du fichier
     *
     * @param string $filename
     * @return int|false
     */
    public static function filemtime(string $filename)
    {
        if (self::exists($filename)) {
            return filemtime(GlobalContext::get("img_cache_folder") . $filename);
        }
        return false;
    }

    /**
     * Permet de savoir si le repertoire de cache est configure
     *
     * @return bool
     */
    public static function enabled(): bool
    {
        return ! empty(GlobalContext::get("img_cache_folder")) && is_dir(GlobalContext::get("img_cache_folder"));
    }

    /**
     * Genere l'URL d'accès pour une image en cache si le parametre img_cache_url est configure
     * @param string $filename
     * @return string
     */
    public static function generateUrl(string $filename) : string
    {
        if (self::exists($filename) && ! empty(GlobalContext::imgCacheUrl())) {
            return GlobalContext::imgCacheUrl() . $filename;
        }
        return "";
    }

	/**
     * Genere l'URL absolue pour une image en cache si le parametre img_cache_folder est configure
     *
     * @return string|null
     */
    public static function generateAbsoluteUrl(string $filename)
    {
        if (self::exists($filename)) {
            return GlobalContext::get("img_cache_folder") . $filename;
        }
        return null;
    }

    /**
     * Permet de ajoute une image dans le repertoire de cache
     *
     * @param string $filename
     * @param resource|\GdImage $image
     * @return bool
     */
    public static function add(string $filename, $image) : bool
    {
    	global $use_opac_url_base;

        if (self::enabled() && !empty($image)) {
        	switch (GlobalContext::get("img_cache_type")) {
                case "png" :
                    return imagepng(
                    	$image,
                    	GlobalContext::get("img_cache_folder") . $filename,
                    	9,
                    	defined('PNG_ALL_FILTERS') ? PNG_ALL_FILTERS : null
                    ) === true;
                default :
                    // On force le PNG dans un contexte extérieur
                    if ($use_opac_url_base) {
                    	return imagepng(
                    		$image,
                    		GlobalContext::get("img_cache_folder") . $filename,
                    		9,
                    		defined('PNG_ALL_FILTERS') ? PNG_ALL_FILTERS : null
                    	) === true;
                    }
                    return imagewebp($image, GlobalContext::get("img_cache_folder") . $filename) === true;
            }
        }
        return false;
    }

    /**
     * Permet de supprimer les toutes images du repertoire de cache
     *
     * @return bool
     */
    public static function clearCache(): bool
    {
        if (! self::enabled()) {
            return false;
        }
        $resources = glob(GlobalContext::get("img_cache_folder") . LOCATION."*");
        $success = true;
        foreach ($resources as $resource) {
            if (is_dir($resource)) {
                continue;
            }
            $success &= unlink($resource);
        }
        return $success;
    }

    /**
     * Genere le nom du fichier en cache
     *
     * @param string $entityType
     * @param int $objectId
     * @return string
     */
    public static function generateFilename(string $entityType, int $objectId) : string
    {
        switch (GlobalContext::get("img_cache_type")) {
            case "png" :
                $extension = "png";
                break;

            case "webp" :
            default :
                $extension = "webp";
                break;
        }
        return LOCATION ."_". $entityType ."_". $objectId . ".".$extension;
    }
}