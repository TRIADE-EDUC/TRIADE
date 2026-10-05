<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CMS.php,v 1.2 2022/04/15 12:16:06 dbellamy Exp $
namespace Pmb\Common\Helper;

class CMS
{
    private static function init() 
    {
        global $class_path;
        
        if (!class_exists("cms_modules_parser")) {            
            require_once $class_path . "/cms/cms_modules_parser.class.php";
        }
    }
    
    /**
     * 
     * @param int $id
     * @return mixed
     */
    public static function getCadreById(int $id) 
    {
        static::init();
        return \cms_modules_parser::get_module_class_by_id($id);
    }
    
    public static function getCadreByIdTag(string $idTag)
    {
        static::init();
        $id = explode("_", $idTag);
        $id = intval($id[count($id)-1]);
        return \cms_modules_parser::get_module_class_by_id($id);
    }
}