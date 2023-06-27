<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_records_by_cp.class.php,v 1.2 2018-02-26 10:45:34 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_records_by_cp extends cms_module_common_filter{
    
    public function get_filter_from_selectors(){
        return array(
            "cms_module_common_selector_record_cp"
        );
    }
    
    public function get_filter_by_selectors(){
        return array(
            "cms_module_common_selector_env_var",
            "cms_module_common_selector_global_var",
            "cms_module_common_selector_empr_infos",
            "cms_module_common_selector_value"
        );
    }
    
    public function filter($datas){
        $filtered_datas= $filter = array();
        $selector_from = $this->get_selected_selector("from");
        $selector_by = $this->get_selected_selector("by");
        $field_by = $selector_by->get_value();
        $field_from = $selector_from->get_value();
        if(count($field_by) && $field_from && count($datas)){
            $selector_by->get_value();
            $pperso = new parametres_perso("notices");
            for($i=0 ; $i<count($datas) ; $i++){
                $pperso->get_values($datas[$i]);
                $values = $pperso->values;
                if(isset($values[$field_from]) && in_array($field_by,$values[$field_from])){
                    $filtered_datas[] = $datas[$i];
                }
            }
        }
        return $filtered_datas;
    }
}