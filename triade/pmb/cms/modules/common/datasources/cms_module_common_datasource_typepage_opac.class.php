<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_typepage_opac.class.php,v 1.15.8.1 2025/03/04 10:45:25 dbellamy Exp $

use Pmb\Common\Helper\Portal;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}
class cms_module_common_datasource_typepage_opac
{

    public static function get_type_page()
    {
        return Portal::getTypePage();
    }

    public static function get_subtype_page()
    {
        return Portal::getSubTypePage();
    }

    public static function get_label($type)
    {
        return Portal::getLabel($type);
    }
}