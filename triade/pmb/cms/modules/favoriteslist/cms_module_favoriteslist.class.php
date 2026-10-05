<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_favoriteslist.class.php,v 1.4.6.2.2.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_favoriteslist extends cms_module_common_module {

    public function __construct($id=0){
        $this->module_path = str_replace(basename(__FILE__),"",__FILE__);
        parent::__construct($id);
    }

    public static function get_max_item_id($datas){
        $max = 0;
        if(is_countable($datas) && count($datas)){
            foreach	($datas as $key => $val){
                $key = intval(str_replace("favoriteslist_item","",$key));
                if($key>$max) $max = $key;
            }
        }
        return $max;
    }

    protected function get_items(){
        $items = array();
        $this->fetch_managed_datas();
        if(isset($this->managed_datas['module']['favoriteslist_items'])){
            foreach($this->managed_datas['module']['favoriteslist_items'] as $key => $values){
                $items[$key] = $values;
            }
        }
        return $items;
    }

    protected function get_items_from_cadre_parent() {
        $items = [];
        $all_items = $this->get_items();
        foreach($all_items as $key => $values){
            if (isset($values["cadre_parent"]) && $values["cadre_parent"] == $this->id) {
                $items[$key] = $values;
            }
        }
        return $items;
    }

    public function confirm_delete() {
        $items = $this->get_items_from_cadre_parent();
        if (is_countable($items) && count($items)) {
            $message = $this->msg["cms_module_favoriteslist_confirm_delete_item"];
            $others_cadres = [];
            foreach ($items as $item_id => $item) {
                $others_cadres = array_merge($others_cadres, $this->get_others_cadres_use_item($item_id));
            }
            if (is_countable($others_cadres) && count($others_cadres)) {
                $message = sprintf($this->msg["cms_module_favoriteslist_confirm_delete_item_others_cadres"], implode(", ", $others_cadres));
            }
            return json_encode(["message"=>$message]);
        }
        return true;
    }

    private function get_others_cadres_use_item($item_id) {
        $cadres_ids = [];
        $query = "SELECT cadre_content_num_cadre, cadre_content_data FROM cms_cadre_content
                WHERE cadre_content_object = 'cms_module_favoriteslist_datasource'
                AND cadre_content_data LIKE '%$item_id%'
                AND cadre_content_num_cadre != ".$this->id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_fields($result)) {
            while($row = pmb_mysql_fetch_assoc($result)) {
                if (!in_array($row["cadre_content_num_cadre"], $cadres_ids)) {
                    $cadres_ids[] = $row["cadre_content_num_cadre"];
                }
            }
        }
        return $cadres_ids;
    }

    public function delete() {
        if (!isset($this->managed_datas)) {
            $this->fetch_managed_datas();
        }
        if(isset($this->managed_datas['module']['favoriteslist_items'])){
            foreach($this->managed_datas['module']['favoriteslist_items'] as $key => $values){
                if ($this->managed_datas['module']['favoriteslist_items'][$key]["cadre_parent"] == $this->id) {
                    unset($this->managed_datas['module']['favoriteslist_items'][$key]);
                }
            }
        }
        $query = "replace into cms_managed_modules set managed_module_name = '".$this->class_name."', managed_module_box = '".addslashes(serialize($this->managed_datas))."'";
        pmb_mysql_query($query);
        return parent::delete();
    }

    public function get_form($ajax= true,$callback='',$cancel_callback='',$delete_callback='',$action="?action=save"){
        $forbidden_duplication = false;
        if (!empty($this->get_items_from_cadre_parent())) {
            $forbidden_duplication = true;
        }
        return parent::get_form($ajax,$callback,$cancel_callback,$delete_callback,$action, $forbidden_duplication);
    }
}