<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_date_flot.class.php,v 1.2 2021/04/06 10:20:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $include_path;
require_once($class_path.'/searcher/searcher_generic.class.php');
require_once "$include_path/search_queries/dynamics/dynamic_search_date_flot.class.php";


//un jour ca sera utile
class searcher_date_flot {
    private $entity_type;
    private $field_id;
    private $user_query;
    private $details = [];
    
    /**
     * 
     * @var searcher de l'entite
     */
    private $searcher = null;
    
    /**
     * 
     * @search instance de la classe search pour la dynamic_search
     */
    private $search = null;
    
    
    public function __construct($user_query){
        $this->user_query = $user_query;
    }
    
    protected function _get_user_query(){
        return $this->user_query["value"]."|||".$this->user_query["date_begin"]."|||".$this->user_query["date_end"];
    }
    
    protected function _get_search_query(){
        $this->init_details();
        $this->init_searcher_instance();
        $this->init_search_instance();
        
        $query = "select 1 as $this->object_table_key";
        if(is_array($this->user_query) && !empty($this->user_query["date_begin"])){            
            $dymanic = new dynamic_search_date_flot($this->field_id, $this->entity_type, 0, [], $this->search);
            $query = $dymanic->get_query($this->user_query["date_begin"], $this->user_query["date_end"], $this->user_query["value"]);
        }
        $this->searcher->set_query($query);
    }
    
    public function get_raw_query() {
        $this->_get_search_query();
        return $this->searcher->get_raw_query();
    }
    
    public function get_entity_type() {
        return $this->entity_type;
    }
    
    public function get_field_id() {
        return $this->field_id;
    }
    
    public function set_entity_type($entity_type) {
        $this->entity_type = $entity_type;
    }
    
    public function set_field_id($field_id) {
        $this->field_id = $field_id;
    }
    
    public function set_details(array $details) {
        $this->details = $details;
    }
    
    private function init_details() {
        if (!empty($this->details)) {
            $this->entity_type = $this->entity_type ?? $this->details["ENTITYTYPE"] ?? "";
            $this->field_id = $this->field_id ?? $this->details["FIELDID"] ?? "";
        }
    }
    
    private function init_searcher_instance() {
        if (isset($this->searcher)) {
            return $this->searcher;
        }
        if ($this->entity_type) {
            $this->searcher = searcher_factory::get_searcher($this->entity_type, "query", "");
        }
        return $this->searcher;
    }
    
    private function init_search_instance() {
        if (isset($this->search)) {
            return $this->search;
        }
        if ($this->entity_type) {
            switch ($this->entity_type) {
                case "records" :
                    $this->search = new search();
                    break;
                default :
                    $this->search = new search_authorities(false, "search_fields_authorities");
                    break;
            }
        }
        return $this->search;
    }
    
    public function __get($name) {
        $return = $this->look_for_attribute_in_class($this, $name);
        if (!$return) {
            $return = $this->look_for_attribute_in_class($this->searcher, $name);
        }
        return $return;
    }
    
    public function __call($name, $arguments) {
        $return = $this->look_for_attribute_in_class($this, $name, $arguments);
        if (!$return) {
            $return = $this->look_for_attribute_in_class($this->searcher, $name, $arguments);
        }
        return $return;
    }
    
    private function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
        if (is_object($class) && isset($class->{$attribute})) {
            return $class->{$attribute};
        } else if (method_exists($class, $attribute)) {
            return call_user_func_array(array($class, $attribute), $parameters);
        } else if (method_exists($class, "get_".$attribute)) {
            return call_user_func_array(array($class, "get_".$attribute), $parameters);
        } else if (method_exists($class, "is_".$attribute)) {
            return call_user_func_array(array($class, "is_".$attribute), $parameters);
        }
        return null;
    }
}