<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_animations_by_cp_val.class.php,v 1.2.2.1 2024/08/06 07:38:33 pmallambic Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_datasource_animations_by_cp_val extends cms_module_common_datasource_animations_list
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->limitable = true;
        $this->paging = true;
    }

    /*
     * On défini les sélecteurs utilisable pour cette source de donnée
     */
    public function get_available_selectors()
    {
        return array(
            "cms_module_common_selector_animation_cp_val"
        );
    }

    /*
     * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
     */
    public function save_form()
    {
        global $selector_choice;

        $this->parameters = array();
        $this->parameters['selector'] = $selector_choice;
        return parent::save_form();
    }

    /*
     * Récupération des données de la source...
     */
    public function get_datas()
    {
        // on commence par récupérer l'identifiant retourné par le sélecteur
        $selector = $this->get_selected_selector();
        if (empty($selector) && !$selector->get_value()) {
            return false;
        }

        $data = array(
            "title" => "",
            "animations" => array()
        );
        $animations = [];
        $parameters = $selector->get_value();
        $parameters['cp'] = intval($parameters['cp']);

        $pperso = new parametres_perso("anim_animation");

        $query = "SELECT id_animation FROM anim_animations
                    JOIN anim_animation_custom_values on anim_animation_custom_origine=id_animation
                    JOIN anim_events ON id_event = num_event
                    AND anim_animation_custom_champ={$parameters['cp']}
                    WHERE to_days(start_date)>=to_days(now())
                    OR to_days(end_date)>=to_days(now())";

        $sort = "";
        if ("" != $this->parameters["sort_by"]) {
            $sort = " ORDER BY " . $this->parameters["sort_by"];

            if ("" != $this->parameters["sort_order"]) {
                $sort .= " " . $this->parameters["sort_order"];
            }
        }

        $result = pmb_mysql_query($query.$sort);

        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $id_animation = intval($row["id_animation"]);
                $pperso->get_values($id_animation);
                $values = $pperso->values;

                $found = false;
                foreach ($parameters['cp_val'] as $value) {
                    if (in_array($value, $values[$parameters['cp']])) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $animations[] = $id_animation;
                }
            }
        }

        $data['animations'] = $this->filter_datas('animations', $animations);

        // Pagination
        if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
            $data['paging'] = $this->inject_paginator($data['animations']);
            $data['animations'] = $this->cut_paging_list($data['animations'], $data['paging']);
        } elseif ($this->parameters["nb_max_elements"] > 0) {
            $data['animations'] = array_slice($data['animations'], 0, $this->parameters["nb_max_elements"]);
        }

        return $data;
    }
}