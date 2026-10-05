<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MenuSource.php,v 1.2.2.1 2024/10/18 12:44:34 pmallambic Exp $

namespace Pmb\Dashboard\Models\Widget\Menu;

use Pmb\Dashboard\Models\WidgetModel;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class MenuSource
{
    public function getConfiguration()
    {
        global $msg;

        $instance = \list_modules_ui::get_instance();
        $modules = $instance->get_objects();
        $menu = [];

        foreach ($modules as $module) {

            $className = "list_tabs_" . $module->get_name() . "_ui";

            if (class_exists($className)) {

                $className::set_module_name($module->get_name());
                $tabInstance = $className::get_instance();

                $sections = [];
                foreach ($tabInstance->get_objects() as $object) {
                    if(isset($msg[$object->get_section()])){
                        $sections[$object->get_section()]["label"] = $msg[$object->get_section()];
                    }
                    $sections[$object->get_section()]["tabs"][] = [
                        "hash" => md5($object->get_id() . $object->get_label() . $object->get_module() . $module->get_title()),
                        "name" => $object->get_id(),
                        "label" => $object->get_label(),
                        "link" => $object->get_destination_link(),
                    ];
                }

                $menu[] = [
                    "hash" => md5($module->get_name() . $module->get_label() . $module->get_title()),
                    "name" => $module->get_name(),
                    "label" => $module->get_label(),
                    "link" => $instance->get_module_destination_link($module->get_name()),
                    "sections" => $sections
                ];


            }
        }

        return $menu;
    }
}