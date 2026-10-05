<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootUrlBuilder.php,v 1.2 2023/03/15 10:43:44 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class RootUrlBuilder implements UrlBuilder
{

    public function makeUrl(): string
    {
        global $cms_url_base_cms_build, $pmb_opac_url, $opac_url_base;

        if (defined('GESTION')) {
            return $cms_url_base_cms_build ? $cms_url_base_cms_build : $pmb_opac_url;
        } else {
            return $opac_url_base;
        }
    }

    /**
     *
     * @param string|int $type
     * @param string|int $subType
     * @return RootUrlBuilder
     */
    public static function getClassUrlBuilder($type, $subType = ""): RootUrlBuilder
    {
        $type = intval($type);
        $subType = intval($subType);

        $classname = "\\Pmb\\CMS\\Library\\UrlBuilder\\UrlBuilder_{$type}";
        if (! empty($subType)) {
            $classname .= "_{$subType}";
        }

        if (class_exists($classname)) {
            return new $classname();
        }

        return new RootUrlBuilder();
    }
}

