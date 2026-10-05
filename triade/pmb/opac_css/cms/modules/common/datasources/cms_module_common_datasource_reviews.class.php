<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_reviews.class.php,v 1.2 2022/08/04 14:13:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once $class_path."/avis.class.php";

class cms_module_common_datasource_reviews extends cms_module_common_datasource_list{
    
    /**
     * type d'objet sur lequel portent les avis, defaut : notice
     */
    protected const OBJECT_TYPE = AVIS_RECORDS;
    
    public function __construct($id=0){
        parent::__construct($id);
        $this->sortable = true;
        $this->limitable = true;
    }
    /*
     * On défini les sélecteurs utilisable pour cette source de donnée
     */
    public function get_available_selectors(){
        return array(
        );
    }
    
    /*
     * On défini les critères de tri utilisable pour cette source de donnée
     */
    protected function get_sort_criterias() {
        return array (
            "dateajout",
            "note",
        );
    }
    
    /*
     * Récupération des données de la source...
     */
    public function get_datas(){
        $ordered_reviews = [];
        $query = "SELECT id_avis FROM avis WHERE type_object = ".static::OBJECT_TYPE;
        if ($this->parameters["sort_by"] != "") {
            $query .= " order by ".$this->parameters["sort_by"];
            if ($this->parameters["sort_order"] != "") {
                $query .= " ".$this->parameters["sort_order"];
            }
        }
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            while($row = pmb_mysql_fetch_assoc($result)){
                $ordered_reviews[] = $row["id_avis"];
            }
        }
        $ordered_reviews = $this->filter_datas("avis",$ordered_reviews);
        if ($this->parameters["nb_max_elements"] > 0) {
            $ordered_reviews = array_slice($ordered_reviews, 0, $this->parameters["nb_max_elements"]);
        }
        return $ordered_reviews;
    }
}