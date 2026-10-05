<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_datasource_records_reviews.class.php,v 1.5.4.1 2025/01/17 10:40:46 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_reviewslist_datasource_records_reviews extends cms_module_common_datasource_reviews{

    protected const OBJECT_TYPE = AVIS_RECORDS;

    /*
     * On d�fini les s�lecteurs utilisable pour cette source de donn�e
     */
    public function get_available_selectors(){
        return array(
            "cms_module_reviewslist_selector_records_by_segment",
            "cms_module_reviewslist_selector_reviews_by_empr",
        );
    }

    /*
     * R�cup�ration des donn�es de la source...
     */
    public function get_datas(){
        $ordered_reviews = [];
        $query = "SELECT id_avis FROM avis WHERE type_object = ".static::OBJECT_TYPE;
        if ($this->parameters["sort_by"] != "") {
            $query .= " ORDER BY ".$this->parameters["sort_by"];
            if ($this->parameters["sort_order"] != "") {
                $query .= " ".$this->parameters["sort_order"];
            }
        }
        //on commence par r�cup�rer l'identifiant retourn� par le s�lecteur...
        if ($this->parameters['selector'] != "") {
            if (is_countable($this->selectors)) {
                for($i=0 ; $i<count($this->selectors) ; $i++){
                    if($this->selectors[$i]['name'] == $this->parameters['selector']){
                        $selector = new $this->parameters['selector']($this->selectors[$i]['id']);
                        break;
                    }
                }
            }

            if (!isset($selector)) {
                return false;
            }

            $selector_values = $selector->get_value();
        }
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            while($row = pmb_mysql_fetch_assoc($result)){
                if (isset($selector_values)) {
                    if (in_array($row["id_avis"], $selector_values)) {
                        $ordered_reviews[] = $row["id_avis"];
                    }
                } else {
                    $ordered_reviews[] = $row["id_avis"];
                }
            }
        }
        $ordered_reviews = $this->filter_datas("avis",$ordered_reviews);
        if ($this->parameters["nb_max_elements"] > 0) {
            $ordered_reviews = array_slice($ordered_reviews, 0, $this->parameters["nb_max_elements"]);
        }
        return $ordered_reviews;
    }
}