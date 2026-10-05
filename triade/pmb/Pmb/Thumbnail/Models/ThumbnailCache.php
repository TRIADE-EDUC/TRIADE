<?php
namespace Pmb\Thumbnail\Models;

use Pmb\Common\Helper\GlobalContext;

class ThumbnailCache
{
    /**
     * donnees stockees dans les parametres de cache
     * @return array
     */
    public function getData() : array
    {
        return [
            "pmb" => [
                "img_cache_folder" => GlobalContext::get("pmb_img_cache_folder"),
                "img_cache_url" => GlobalContext::get("pmb_img_cache_url"),
                "img_cache_size" => GlobalContext::get("pmb_img_cache_size") ?? 100,
                "img_cache_clean_size" => GlobalContext::get("pmb_img_cache_clean_size") ?? 20,
            ],
            "opac" => [
                "img_cache_folder" => GlobalContext::get("opac_img_cache_folder"),
                "img_cache_url" => GlobalContext::get("opac_img_cache_url"),
                "img_cache_size" => GlobalContext::get("opac_img_cache_size") ?? 100,
                "img_cache_clean_size" => GlobalContext::get("opac_img_cache_clean_size") ?? 20,
                "img_cache_type" => GlobalContext::get("opac_img_cache_type"),
            ],
        ];
        
    }
    
    /**
     * mise a jour des parametres de cache
     * @param \stdClass $parameters
     * @return bool
     */
    public function save(\stdClass $parameters) : bool
    {
        foreach ($parameters as $type => $params) {
            foreach ($params as $label => $value) {
                \parameter::update($type, $label, $value);
            }
        }
        return true;
    }
}

