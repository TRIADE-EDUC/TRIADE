<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: responsabilities.class.php,v 1.6.8.1 2025/01/31 09:18:09 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class responsabilities
{

    /**
     * Retourne le template en fonction du type de responsabilité.
     *
     * @param int $type
     * @param int $id
     * @return string
     */
    public function get_form(int $type, int $id)
    {
        switch ($type) {
            case TYPE_AUTHPERSO_RESPONSABILITY:
                return $this->authperso_form($id);
                break;
            default:
                // Aucun type de responsabilité on retourne rien
                return "";
                break;
        }
    }

    /**
     * Template d'Autorité perso
     *
     * @param int $authperso_id
     * @return string
     */
    public function authperso_form(int $authperso_id)
    {
        global $include_path;
        global $pmb_authors_qualification;
        global $value_deflt_fonction;

        $template = "";

        $template_path = $include_path . '/templates/responsabilities/authperso.tpl.html';
        if (file_exists($include_path . '/templates/responsabilities/authperso_subst.tpl.html')) {
            $template_path = $include_path . '/templates/responsabilities/authperso_subst.tpl.html';
        }

        $h2o = H2o_collection::get_instance($template_path);
        $responsabilities = $this->get_responsabilities_authperso($authperso_id);
        $aut_fonctions = marc_list_collection::get_instance('function');
        $template = $h2o->render(array(
            "responsabilities" => $responsabilities,
            "max_aut0" => count($responsabilities),
            "qualification" => $pmb_authors_qualification,
            "default_fonction_name" => $aut_fonctions->table[$value_deflt_fonction] ?? "",
            "default_fonction_id" => $value_deflt_fonction,
            "pmb_escape" => pmb_escape(),
            "icone" => [
                "plusgif" => get_url_icon('plus.gif'),
                "minusgif" => get_url_icon('minus.gif')
            ]
        ));

        return $template;
    }

    /**
     * Retourne la liste des responsabilitié pour une Autorité perso
     *
     * @param int $authperso_id
     * @return array
     *
     */
    public function get_responsabilities_authperso(int $authperso_id)
    {
        global $pmb_authors_qualification, $value_deflt_fonction, $charset, $sub;

        $responsabilities = array();
        $aut_fonctions = marc_list_collection::get_instance('function');
        if (! empty($authperso_id)) {
            $query = "SELECT * FROM responsability_authperso WHERE responsability_authperso_num = $authperso_id";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $i = 0;
                while ($r = pmb_mysql_fetch_assoc($result)) {
                    $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [
                        'num_object' => $r["responsability_authperso_author"],
                        'type_object' => AUT_TABLE_AUTHORS
                    ]);

                    $vedette_author = "";
                    if ($pmb_authors_qualification) {
                        // $vedette_ui = new vedette_ui(new vedette_composee($r["responsability_authperso_author"], 'responsabilities'));
                        $vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($r["id_responsability_authperso"], TYPE_AUTHPERSO_RESPONSABILITY), 'responsabilities'));
                        $vedette_author = $vedette_ui->get_form('role', $i, 'saisie_authperso');
                    }
                    $id_vedette = vedette_composee::get_vedette_id_from_object($r["id_responsability_authperso"], TYPE_AUTHPERSO_RESPONSABILITY);
                    $responsabilities[] = [
                        "id" => $r["responsability_authperso_author"],
                        "isbd" => htmlentities(trim($authority_instance->get_isbd()), ENT_QUOTES, $charset),
                        "fonction_name" => htmlentities($aut_fonctions->table[$r['responsability_authperso_fonction']], ENT_QUOTES, $charset),
                        "fonction_id" => $r['responsability_authperso_fonction'],
                        "qualification" => $id_vedette ? strip_tags(vedette_composee::get_vedettes_display([$id_vedette])) : "",
                        "vedette_author" => $vedette_author
                    ];
                    $i ++;
                }
            }
        }
        // Si on est dans le formulaire de création d'une autorité perso
        if (empty($responsabilities) && $sub == "authperso_form") {
            
            $vedette_author = "";
            if ($pmb_authors_qualification) {
                $vedette_ui = new vedette_ui(new vedette_composee(0, 'responsabilities'));
                $vedette_author = $vedette_ui->get_form('role', 0, 'saisie_authperso');
            }
            $responsabilities[] = [
                "id" => "",
                "isbd" => "",
                "fonction_name" => $aut_fonctions->table[$value_deflt_fonction],
                "fonction_id" => $value_deflt_fonction,
                "qualification" => "",
                "vedette_author" => $vedette_author
            ];
        }

        return $responsabilities;
    }

    /**
     * Sauvegarde des responsabilité pour une Autorité perso
     *
     * @param int $id
     * @param array $responsabilities
     */
    public function save_authperso(int $id, array $responsabilities)
    {
        global $pmb_authors_qualification;

        if (empty($responsabilities)) {
            return false;
        }

        $var_name = 'saisie_authperso_role_composed';
        global ${$var_name};
        $role_composed = ${$var_name};

        $tab_id_responsabilities = array();
        $id_responsability_authperso = 0;

        $query = "SELECT id_responsability_authperso FROM responsability_authperso  WHERE responsability_authperso_num = $id";
        $results = pmb_mysql_query($query);
        while ($r = pmb_mysql_fetch_array($results)) {
            $tab_id_responsabilities[] = $r[0];
        }

        $query = "DELETE FROM responsability_authperso WHERE responsability_authperso_num = $id";
        pmb_mysql_query($query);

        // Clean des vedettes
        $id_vedettes_links_deleted = responsabilities::delete_vedette_links($id);

        foreach ($responsabilities as $key => $responsability) {
            
            $type_aut = 0; // 0 = auteurs
            if (!empty($responsability['authors_id']) && !empty($id) && isset($responsability['fonction_code']) && isset($type_aut)) {
                $query = "INSERT INTO responsability_authperso
                        (responsability_authperso_author,responsability_authperso_num, responsability_authperso_fonction,responsability_authperso_type,responsability_authperso_ordre)
                        VALUES (" . $responsability['authors_id'] . ", " . $id . ", '" . $responsability['fonction_code'] . "', $type_aut, " . $key . ")";
                pmb_mysql_query($query);
                $id_responsability_authperso = pmb_mysql_insert_id();
                
                if ($pmb_authors_qualification) {
                    switch ($type_aut) {
                        // auteurs
                        case 0:
                            $id_vedette = $this->update_vedette(stripslashes_array($role_composed[$key]), $id_responsability_authperso, TYPE_AUTHPERSO_RESPONSABILITY);
                            break;
                            // interpretes
                        case 1:
                            // $id_vedette=$this->update_vedette(stripslashes_array($role_composed_autre[$ordre_aut]),$id_responsability_tu,TYPE_AUTHPERSO_RESPONSABILITY);
                            break;
                    }
                    if ($id_vedette) {
                        $id_vedettes_used[] = $id_vedette;
                    }
                }
                
                foreach ($id_vedettes_links_deleted as $id_vedette) {
                    if (! in_array($id_vedette, $id_vedettes_used)) {
                        $vedette_composee = new vedette_composee($id_vedette);
                        $vedette_composee->delete();
                    }
                }
            }
        }
    }

    /**
     * Met à jour les vedettes
     *
     * @param array $data
     * @param int $id
     * @param int $type
     * @return number|void
     */
    public function update_vedette(array $data, int $id, int $type)
    {
        if (! empty($data["elements"])) {

            $vedette_composee = new vedette_composee($data["id"], "responsabilities");
            if (! empty($data["value"])) {
                $vedette_composee->set_label($data["value"]);
            }

            // On commence par réinitialiser le tableau des éléments de la vedette composée
            $vedette_composee->reset_elements();

            // On remplit le tableau des éléments de la vedette composée
            $vedette_composee_id = 0;
            $tosave = false;

            foreach ($data["elements"] as $subdivision => $elements) {
                if ($elements["elements_order"] !== "") {

                    $elements_order = explode(",", $elements["elements_order"]);

                    foreach ($elements_order as $position => $num_element) {
                        if ($elements[$num_element]["id"] && $elements[$num_element]["label"]) {

                            $tosave = true;
                            $velement = $elements[$num_element]["type"];

                            if (strpos($velement, "vedette_ontologies") === 0) {
                                $velement = "vedette_ontologies";
                            }

                            $available_field_class_name = $vedette_composee->get_at_available_field_num($elements[$num_element]['available_field_num']);
                            if (empty($available_field_class_name['params'])) {
                                $available_field_class_name['params'] = array();
                            }

                            $vedette_element = new $velement($elements[$num_element]['available_field_num'], $elements[$num_element]["id"], $elements[$num_element]["label"], $available_field_class_name['params']);
                            $vedette_composee->add_element($vedette_element, $subdivision, $position);
                        }
                    }
                }
            }
            if ($tosave) {
                $vedette_composee_id = $vedette_composee->save();
            }
        }
        if ($vedette_composee_id) {
            vedette_link::save_vedette_link($vedette_composee, $id, $type);
        }

        return $vedette_composee_id;
    }

    /**
     * Clean des vedettes, retourne la liste des vedettes à supprimer
     *
     * @param int $responsability_authperso_num
     * @return array
     */
    public static function delete_vedette_links(int $responsability_authperso_num)
    {
        $id_vedettes = array();

        $query = 'SELECT id_responsability_authperso, responsability_authperso_type FROM responsability_authperso WHERE responsability_authperso_num="' . $responsability_authperso_num . '" ';
        $responsabilities = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($responsabilities)) {
            while ($r = pmb_mysql_fetch_object($responsabilities)) {

                $object_id = $r->id_responsability_authperso;
                $type_aut = $r->responsability_authperso_type;
                $id_vedette = 0;

                switch ($type_aut) {
                    // auteurs
                    case 0:
                        $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'responsabilities'), $object_id, TYPE_AUTHPERSO_RESPONSABILITY);
                        break;
                    // interpretes
                    case 1:
                        // $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'responsabilities'), $object_id, TYPE_AUTHPERSO_RESPONSABILITY);
                        break;
                }

                if ($id_vedette) {
                    $id_vedettes[] = $id_vedette;
                }
            }
        }

        return $id_vedettes;
    }

    /**
     * Suppresion de responsability et des vedettes et vedettes liées
     *
     * @param int $id
     */
    public function delete_authperso(int $id)
    {
        // Clean des vedettes
        $id_vedettes_links_deleted = responsabilities::delete_vedette_links($id);

        foreach ($id_vedettes_links_deleted as $id_vedette) {
            $vedette_composee = new vedette_composee($id_vedette);
            $vedette_composee->delete();
        }

        $query = "DELETE FROM responsability_authperso WHERE responsability_authperso_num = $id";
        pmb_mysql_query($query);
    }
} 
