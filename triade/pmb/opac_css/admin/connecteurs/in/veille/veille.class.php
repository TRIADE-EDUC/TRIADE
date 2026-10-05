<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: veille.class.php,v 1.15.4.1 2025/04/16 12:16:53 dbellamy Exp $
global $class_path;
require_once ($class_path . "/curl.class.php");

class veille extends connector
{

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "veille";
    }

    // Formulaire des propriétés générales
    public function source_get_property_form($source_id)
    {
        $params = $this->get_source_params($source_id);
        // Affichage du formulaire en fonction de $this->parameters
        $sources_watches = '';
        $items_interesting = '';
        if ($params["PARAMETERS"]) {
            $vars = unserialize($params["PARAMETERS"]);
            $sources_watches = $vars['sources_watches'];
            $items_interesting = $vars['items_interesting'];
        }

        $form = '';

        $docwatch_watches = new docwatch_watches(0);
        if (! empty($docwatch_watches) && is_countable($docwatch_watches->watches) && count($docwatch_watches->watches)) {
            $form .=
            "<div class='row'>
                <div class='colonne3'>
                    <label for='sources_watches'>" . $this->msg['sources_watches_label'] . "</label>
                </div>
                <div class='colonne_suite'>
                        <select multiple name='sources_watches[]' id='sources_watches'>";
            $form .= self::get_selector($docwatch_watches, $sources_watches);
            $form .= "</select>";
            $form .= "</div>";
        }

        // Options tous /interressant
        $form .= "
        <div class='row'>
                <div class='colonne3'>
                        <label for='items_interesting'>" . $this->msg["items_interesting_label"] . "</label>
                </div>
                <div class='colonne_suite'>
                        <input type='radio' " . ($items_interesting == 1 ? 'checked' : '') . " name='items_interesting' id='items_interesting_all' class='saisie-60em' value='1'/>
                        <label for='items_interesting_all'>" . $this->msg["items_interesting_all"] . "</label>
                </div>
                <div class='colonne_suite'>
                        <input type='radio' " . ($items_interesting == 2 ? 'checked' : '') . " name='items_interesting' id='items_interesting_only' class='saisie-60em' value='2'/>
                        <label for='items_interesting_only'>" . $this->msg["items_interesting_only"] . "</label>
                </div>
                <div class='colonne_suite'>
                        <input type='radio' " . ($items_interesting == 3 ? 'checked' : '') . " name='items_interesting' id='items_not_interesting_only' class='saisie-60em' value='3'/>
                        <label for='items_not_interesting_only'>" . $this->msg["items_not_interesting_only"] . "</label>
                </div>
        </div>";

        return $form;
    }

    public static function get_selector($docwatch_watches, $sources_watches)
    {
        global $msg, $charset;

        $form = self::compute_rubrique($docwatch_watches, $sources_watches);

        // Affichage des noeuds racines
        $form .= "<optgroup label='" . htmlentities($msg['root'], ENT_QUOTES, $charset) . "'>";
        foreach ($docwatch_watches->watches as $fluxRacine) {
            $form .= "<option " . (!empty($sources_watches) && in_array($fluxRacine->id, $sources_watches) ? 'selected=\'selected\'' : '') . " value='" . $fluxRacine->id . "'>" . $fluxRacine->title . "</option>";
        }
        $form .= "</optgroup>";
        return $form;
    }

    public static function compute_rubrique($docwatch_watches, $sources_watches)
    {
        global $charset;

        $form = '';
        foreach ($docwatch_watches->children as $rubrique) {
            $form .= "<optgroup label='" . htmlentities($rubrique->title, ENT_QUOTES, $charset) . "'>";

            if (count($rubrique->children)) {
                $form .= self::compute_rubrique($rubrique, $sources_watches);
            }
            foreach ($rubrique->watches as $flux) {
                $form .= "<option " . (!empty($sources_watches) && in_array($flux->id, $sources_watches) ? 'selected=\'selected\'' : '') . " value='" . $flux->id . "'>" . $flux->title . "</option>";
            }
            $form .= "</optgroup>";
        }
        return $form;
    }

    public function make_serialized_source_properties($source_id)
    {
        global $sources_watches, $items_interesting;
        $this->del_notices($source_id);
        $this->sources[$source_id]["PARAMETERS"] = serialize([
            'sources_watches' => $sources_watches,
            'items_interesting' => $items_interesting
        ]);
    }

    // Récupération des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
    public function fetch_default_global_values()
    {
        parent::fetch_default_global_values();
        $this->repository = 2;
    }

    private function checkArray($value)
    {
        if (is_array($value))
            $val = $value[0];
        else
            $val = $value;
        if (is_object($val)) {
            if ($val->{"$"}) {
                $val = $val->{"$"};
            } else {
                $val = "?";
            }
        }
        return $val;
    }

    public function rec_record($record, $source_id, $source_label)
    {
        // On supprime les entrées de l'entrepot pour le record
        $q = "DELETE FROM entrepot_source_" . $source_id . " WHERE ref ='" . $record["id_item"] . "'";
        pmb_mysql_query($q);
        // On supprime de external_count
        $q = "delete from external_count where recid='" . addslashes($this->get_id() . " " . $source_id . " " . $record["id_item"]) . "' and source_id = " . $source_id;
        pmb_mysql_query($q);

        /**
         * On ne veut garder que les items non-lu, lu ou restauré.
         *
         * les satuts :
         * 0 -> UREAD
         * 1 -> READ OR RESTORE
         * 2 -> DELETE
         * 3 -> PURGED
         */

        if ($record['item_status'] == '2' || $record['item_status'] == '3') {
            return;
        }

        // On recupère le type de document
        $q = "SELECT watch_record_default_type from docwatch_watches where id_watch=" . $record['item_num_watch'];
        $r = pmb_mysql_query($q);
        $dt = pmb_mysql_fetch_array($r)['watch_record_default_type'];

        $date_import = date("Y-m-d H:i:s", time());
        // Insertion de l'entï¿½te
        $n_header = array();
        $n_header["rs"] = "*";
        $n_header["ru"] = "*";
        $n_header["el"] = "*";
        $n_header["bl"] = "m";
        $n_header["hl"] = "0";
        $n_header["dt"] = $dt ?? "a";

        // Récupération d'un ID
        $recid = $this->insert_into_external_count($source_id, $record['id_item']);

        foreach ($n_header as $hc => $code) {
            $this->insert_header_into_entrepot($source_id, $record['id_item'], $date_import, $hc, $code, $recid);
        }

        $field_order = 0;
        foreach ($record as $key => $value) {
            switch ($key) {
                case "id_item":
                    $ufield = "001";
                    $usubfield = "";
                    $val = $this->checkArray($value);
                    break;
                case "item_title":
                    $ufield = "200";
                    $usubfield = "a";
                    $val = $this->checkArray($value);
                    break;
                case "item_publication_date":
                    $ufield = "214";
                    $usubfield = "z";
                    $date = new DateTime($value);
                    $val = $this->checkArray($date->format("U"));
                    $this->insert_content_into_entrepot($source_id, $record['id_item'], $date_import, $ufield, $usubfield, $field_order, 0, $val, $recid);
                    $usubfield = "d";
                    $val = $this->checkArray($date->format("d/m/Y"));
                    break;
                case "item_url":
                    $ufield = "856";
                    $usubfield = "u";
                    $val = $this->checkArray($value);
                    break;
                case "item_summary":
                case "item_content":
                    $ufield = "330";
                    $usubfield = "a";
                    $val = $this->checkArray($value);
                    break;
                case "item_logo_url":
                    $ufield = "896";
                    $usubfield = "a";
                    $val = $this->checkArray($value);
                    break;
                default:
                    break;
            }
            $this->insert_content_into_entrepot($source_id, $record['id_item'], $date_import, $ufield, $usubfield, $field_order, 0, $val, $recid);
        }
        // Ajout du label de la source
        $this->insert_content_into_entrepot($source_id, $record['id_item'], $date_import, 801, 'c', $field_order, 0, $source_label, $recid);

        $this->insert_tags_into_entrepot($source_id, $record, $date_import, $recid);
        $this->insert_categories_into_entrepot($source_id, $record, $date_import, $recid);

        $this->n_recu ++;
    }

    /**
     *
     * Fonction de recherche
     *
     * @param int $source_id
     * @param $query
     * @param int $search_id
     *
     * @see connector::search()
     *
     */
    public function search($source_id, $query, $search_id)
    {
        $params = $this->get_source_params($source_id);
        $params_source = unserialize($params["PARAMETERS"]);

        $list_ids = implode(',', $params_source['sources_watches']);
        $search_query = "SELECT * FROM docwatch_items WHERE item_num_watch IN (" . $list_ids . ") ";
        if (2 == $params_source["items_interesting"]) {
            $search_query .= " AND item_interesting = 1";
        } else if (3 == $params_source["items_interesting"]) {
            $search_query .= " AND item_interesting = 0";
        }

        /**
         * On ne veut garder que les items non-lu, lu ou restauré.
         *
         * les satuts :
         * 0 -> UREAD
         * 1 -> READ OR RESTORE
         * 2 -> DELETE
         * 3 -> PURGED
         */
        $search_query .= " AND item_status IN (0, 1)";

        foreach ($query as $amterm) {
            switch ($amterm->ufield) {
                case '330':
                    $search_query .= " AND item_summary LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    $search_query .= " OR item_content LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    break;
                case '896':
                    $search_query .= " AND item_logo_url LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    break;
                case '214$d':
                case '210$d':
                case '210$d214$d':
                    $search_query .= " AND item_publication_date LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    break;
                case '200$a':
                    $search_query .= " AND item_title LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    break;
                case '610':
                    $ids = array();
                    $ids_item = array();
                    $querySupp = "SELECT id_tag FROM docwatch_tags WHERE tag_title LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    $result = pmb_mysql_query($querySupp);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $ids[] = $row['id_tag'];
                        }
                    }
                    if (! count($ids));
                    break;

                    $querySupp = "SELECT num_item FROM docwatch_items_tags WHERE num_tag IN (" . implode(',', $ids) . ") ";
                    $result = pmb_mysql_query($querySupp);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $ids_item[] = $row['num_item'];
                        }
                    }
                    if (count($ids_item)) {
                        $search_query .= " AND id_item IN (" . implode(',', $ids_item) . ") ";
                    }
                    break;
                case '60X':
                    $ids_categ = array();
                    $ids_item = array();
                    $ids_to_save = array();

                    // On recupere les categories lies aux items
                    $querySupp = "SELECT num_noeud FROM categories WHERE index_categorie LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    $result = pmb_mysql_query($querySupp);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $ids_categ[] = $row['num_noeud'];
                        }
                    }

                    if (count($ids_categ)) {
                        $querySupp = "
                        SELECT num_item FROM docwatch_items_descriptors
                        JOIN docwatch_items on id_item = num_item
                        WHERE num_noeud IN (" . implode(',', $ids_categ) . ")
                        AND item_num_watch IN (" . $list_ids . ") ";
                        $result = pmb_mysql_query($querySupp);
                        if (pmb_mysql_num_rows($result)) {
                            while ($row = pmb_mysql_fetch_assoc($result)) {
                                if (! in_array($row['num_item'], $ids_to_save)) {
                                    $ids_to_save[] = $row['num_item'];
                                }
                            }
                        }
                    }

                    if (! count($ids_to_save))
                        break;

                    $search_query .= " AND id_item IN (" . implode(',', $ids_to_save) . ") ";

                    break;
                case 'XXX':
                    if ($amterm->values[0] == "*")
                        break;
                default:
                    $search_query .= " AND item_index_sew LIKE '%" . rawurlencode($amterm->values[0]) . "%'";
                    break;
            }
        }
        $result = pmb_mysql_query($search_query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->rec_record($row, $source_id, $params["NAME"]);
            }
        }
    }

    private function insert_tags_into_entrepot($source_id, $record, $date_import, $recid)
    {
        $num_tag = array();
        $field_order = 0;
        $q = 'SELECT num_tag FROM docwatch_items_tags WHERE num_item = ' . $record['id_item'];
        $result = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $num_tag[] = $row['num_tag'];
            }
        }
        if (count($num_tag)) {
            $ufield = "610";
            $usubfield = "a";
            $q = 'SELECT tag_title FROM docwatch_tags WHERE id_tag IN (' . implode(',', $num_tag) . ')';
            $result = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $value = $row['tag_title'];
                    $val = $this->checkArray($value);
                    $this->insert_content_into_entrepot($source_id, $record['id_item'], $date_import, $ufield, $usubfield, $field_order, 0, $val, $recid);
                    $field_order ++;
                }
            }
        }
    }

    private function insert_categories_into_entrepot($source_id, $record, $date_import, $recid)
    {
        $num_noeud = array();
        $field_order = 0;
        $q = 'SELECT num_noeud FROM docwatch_items_descriptors WHERE num_item = ' . $record['id_item'];
        $result = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $num_noeud[] = $row['num_noeud'];
            }
        }
        if (count($num_noeud)) {
            $ufield = "606";
            $usubfield = "a";
            $q = 'SELECT index_categorie FROM categories WHERE num_noeud IN (' . implode(',', $num_noeud) . ')';
            $result = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $value = $row['index_categorie'];
                    $val = $this->checkArray($value);
                    $this->insert_content_into_entrepot($source_id, $record['id_item'], $date_import, $ufield, $usubfield, $field_order, 0, $val, $recid);
                    $field_order ++;
                }
            }
        }
    }
}
