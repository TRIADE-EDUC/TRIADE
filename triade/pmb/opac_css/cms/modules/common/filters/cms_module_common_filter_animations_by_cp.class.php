<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_animations_by_cp.class.php,v 1.1.6.1 2025/01/17 10:40:43 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_filter_animations_by_cp extends cms_module_common_filter {

    public function get_filter_from_selectors() {
        return array(
            "cms_module_common_selector_animation_cp"
        );
    }

    public function get_filter_by_selectors() {
        return array(
            "cms_module_common_selector_env_var",
            "cms_module_common_selector_empr_infos",
            "cms_module_common_selector_value",
            "cms_module_common_selector_session_var",
            "cms_module_common_selector_global_var"
        );
    }

    public function filter($datas) {
        $selector_from = $this->get_selected_selector("from");
        $selector_by = $this->get_selected_selector("by");

        $custom_field = $selector_from->get_value();
        $custom_field_value = $selector_by->get_value();

        $custom_field_value = explode(';', $custom_field_value);
        $custom_field_value = array_map('trim', $custom_field_value);
        $index = 0;
        if (is_countable($datas)) {
            $index = count($datas);
        }
        $filtered_datas = array();

        if (! empty($custom_field_value) && ! empty($custom_field) && $index >0) {

            $pperso = new parametres_perso("anim_animation");
            for ($i = 0; $i < $index; $i ++) {
                $pperso->get_values($datas[$i]);
                $values = $pperso->values;
                if (isset($values[$custom_field])) {

                    $found = false;
                    foreach ($custom_field_value as $value) {
                        if (in_array($value, $values[$custom_field])) {
                            $found = true;
                            break;
                        }
                    }

                    if ($found) {
                        $filtered_datas[] = $datas[$i];
                    }
                }
            }
        }
        return $filtered_datas;
    }
}