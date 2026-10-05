<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_datasource_articles_reviews.class.php,v 1.3.6.1 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_reviewslist_datasource_articles_reviews extends cms_module_common_datasource_reviews{

    protected const OBJECT_TYPE = AVIS_ARTICLES;

    /*
     * On défini les sélecteurs utilisable pour cette source de donnée
     */
    public function get_available_selectors(){
        return array(
            "cms_module_reviewslist_selector_articles_by_empr_cp",
        );
    }

    /*
     * Récupération des données de la source...
     */
    public function get_datas(){
        $ordered_reviews = [];
        $query = "SELECT id_avis FROM avis WHERE type_object = ".static::OBJECT_TYPE;
        //on commence par récupérer l'identifiant retourné par le sélecteur...
        if($this->parameters['selector'] != "") {
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

            $selector_value = $selector->get_value();
            if(!empty($selector_value["cp_val"])){
                $query .= " AND num_empr = ".$selector_value["cp_val"];
            }
        }
        if ($this->parameters["sort_by"] != "") {
            $query .= " ORDER BY ".$this->parameters["sort_by"];
            if ($this->parameters["sort_order"] != "") {
                $query .= " ".$this->parameters["sort_order"];
            }
        }

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)){
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