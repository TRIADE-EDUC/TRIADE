<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_extensions.class.php,v 1.1.4.2 2025/04/01 09:40:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/modules/module.class.php");

class module_extensions extends module{
	
    public function get_display_tabs() {
        $list_tabs_ui_class_name = "list_tabs_".$this->name."_ui";
        if (!class_exists($list_tabs_ui_class_name)) {
            return '';
        }
        return parent::get_display_tabs();
    }
    
    public function get_display_subtabs() {
        $list_subtabs_ui_class_name = "list_subtabs_".$this->name."_ui";
        if (!class_exists($list_subtabs_ui_class_name)) {
            return '';
        }
        return parent::get_display_subtabs();
    }
}