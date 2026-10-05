<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_animations_by_concepts.class.php,v 1.1 2021/03/31 08:47:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_animations_by_concepts extends cms_module_common_datasource_list
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = true;
        $this->limitable = true;
    }

    /*
     * On défini les sélecteurs utilisable pour cette source de donnée
     */
    public function get_available_selectors()
    {
        return array(
            "cms_module_common_selector_generic_authorities_concepts"
        );
    }

    /*
     * On défini les critères de tri utilisable pour cette source de donnée
     */
    protected function get_sort_criterias()
    {
        return array(
            "id_animation",
            "name",
        );
    }

    /*
     * Récupération des données de la source...
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if ($selector && $selector->get_value()) {
            
            $raw_ids = $selector->get_authorities_raw_ids();
            if (empty($raw_ids)) {
                return false;
            }
            
            $values = implode(',', $raw_ids);
            if (empty($values)) {
                return false;
            }
            
            $query = "SELECT DISTINCT id_animation FROM anim_animations JOIN index_concept ON id_animation=num_object AND type_object = '" . TYPE_ANIMATION . "'
        		WHERE num_concept IN (" . $values . ")";
            
            // On regarde si on se base sur les concepts d'une animation, auquel cas on ne veut pas de l'animation en question
            $excluded_elements = $selector->get_excluded_elements();
            $excludedElementsList = implode(',', $excluded_elements['animation_ids']);
            if (! empty($excludedElementsList)) {
                $query .= " AND id_animation NOT IN (" . $excludedElementsList . ")";
            }
            
            // On tris les animations
            if ($this->parameters["sort_by"] != "") {
                $query .= " ORDER BY " . $this->parameters["sort_by"];
                if ($this->parameters["sort_order"] != "") {
                    $query .= " " . $this->parameters["sort_order"];
                }
            }
            
            $result = pmb_mysql_query($query);
            
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $animations[] = intval($row['id_animation']);
                }
            }
            
            $animations = $this->filter_datas("animations", $animations);
            if ($this->parameters["nb_max_elements"] > 0) {
                $animations = array_slice($animations, 0, $this->parameters["nb_max_elements"]);
            }
            
            return [
                "animations" => $animations
            ];
        }
        return false;
    }
}